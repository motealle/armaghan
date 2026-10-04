<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FavoriteShare;
use App\Models\Product;
use App\Models\Subcategory;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_issue_and_resolve_ordered_hash_only_share(): void
    {
        [$first, $second] = $this->catalogProducts();

        $response = $this->postJson('/api/favorite-shares', [
            'product_codes' => [$second->code, $first->code],
        ])
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('share.owned', false)
            ->assertJsonStructure(['share' => ['id', 'url', 'expires_at', 'owned']]);

        $url = (string) $response->json('share.url');
        $this->assertSame('/', parse_url($url, PHP_URL_PATH));
        $this->assertNull(parse_url($url, PHP_URL_QUERY));

        $fragment = (string) parse_url($url, PHP_URL_FRAGMENT);
        $this->assertStringStartsWith('/s/', $fragment);
        $token = basename($fragment);
        $this->assertSame(22, strlen($token));

        $share = FavoriteShare::query()->sole();
        $this->assertNull($share->customer_id);
        $this->assertNotSame($token, $share->token_hash);
        $this->assertSame(hash('sha256', $token), $share->token_hash);
        $this->assertTrue($share->expires_at->between(now()->addDays(6), now()->addDays(8)));

        $pivot = $share->products()
            ->orderBy('favorite_share_product.sort_order')
            ->get()
            ->map(fn (Product $product): array => [
                'code' => $product->code,
                'sort_order' => $product->pivot->sort_order,
            ])
            ->values()
            ->all();

        $this->assertSame([
            ['code' => $second->code, 'sort_order' => 0],
            ['code' => $first->code, 'sort_order' => 1],
        ], $pivot);

        $this->postJson('/api/favorite-shares/resolve', ['token' => $token])
            ->assertOk()
            ->assertJsonPath('share.id', $share->id)
            ->assertJsonPath('share.product_codes.0', $second->code)
            ->assertJsonPath('share.product_codes.1', $first->code);

        $activity = ActivityLog::query()->where('action', 'favorite_share.issued')->sole();
        $this->assertSame('guest', $activity->metadata['owner']);
        $this->assertSame(2, $activity->metadata['product_count']);
        $this->assertStringNotContainsString($token, json_encode($activity->metadata, JSON_THROW_ON_ERROR));
    }

    public function test_stale_host_fragment_configuration_cannot_send_shares_to_frozen_test27(): void
    {
        config(['armaghan.favorite_share.fragment_path'=>'/t/27/#/favorites/share/']);
        [$first] = $this->catalogProducts();
        $url=$this->postJson('/api/favorite-shares',['product_codes'=>[$first->code]])->assertOk()->json('share.url');
        $this->assertSame('/',parse_url($url,PHP_URL_PATH));
        $this->assertStringStartsWith('/s/', (string) parse_url($url, PHP_URL_FRAGMENT));
        $token=basename(parse_url($url,PHP_URL_FRAGMENT));
        $this->postJson('/api/favorite-shares/resolve',['token'=>$token])->assertOk()->assertJsonPath('share.product_codes.0',$first->code);
    }

    public function test_legacy_64_character_share_tokens_still_resolve(): void
    {
        [$first] = $this->catalogProducts();
        $legacyToken = str_repeat('L', 64);
        $share = FavoriteShare::create([
            'token_hash' => hash('sha256', $legacyToken),
            'expires_at' => now()->addDay(),
        ]);
        $share->products()->attach($first->id, ['sort_order' => 0]);

        $this->postJson('/api/favorite-shares/resolve', ['token' => $legacyToken])
            ->assertOk()
            ->assertJsonPath('share.product_codes.0', $first->code);
    }

    public function test_customer_owned_share_can_only_be_revoked_by_its_owner(): void
    {
        [$first, $second] = $this->catalogProducts();
        $owner = Customer::create([
            'company_name' => 'Owner',
            'active' => true,
            'direct_link_enabled' => true,
        ]);
        $other = Customer::create([
            'company_name' => 'Other',
            'active' => true,
            'direct_link_enabled' => true,
        ]);

        $response = $this
            ->withSession([CustomerSession::KEY => $owner->id])
            ->postJson('/api/favorite-shares', [
                'product_codes' => [$first->code, $second->code],
            ])
            ->assertOk()
            ->assertJsonPath('share.owned', true);

        $share = FavoriteShare::query()->sole();
        $this->assertSame($owner->id, $share->customer_id);
        $this->assertTrue($share->expires_at->between(now()->addDays(29), now()->addDays(31)));

        $token = basename((string) parse_url((string) $response->json('share.url'), PHP_URL_FRAGMENT));

        $this->withSession([CustomerSession::KEY => $other->id])
            ->deleteJson("/api/customer/favorite-shares/{$share->id}")
            ->assertNotFound();

        $this->withSession([CustomerSession::KEY => $owner->id])
            ->deleteJson("/api/customer/favorite-shares/{$share->id}")
            ->assertOk()
            ->assertJson(['ok' => true]);

        $share->refresh();
        $this->assertNotNull($share->revoked_at);

        $this->postJson('/api/favorite-shares/resolve', ['token' => $token])
            ->assertGone()
            ->assertJsonPath('message', 'Favorite share is unavailable.');

        $this->assertDatabaseHas('activity_log', [
            'customer_id' => $owner->id,
            'action' => 'favorite_share.revoked',
            'subject_id' => $share->id,
        ]);
    }

    public function test_expired_share_and_inactive_products_are_rejected(): void
    {
        [$first, $second] = $this->catalogProducts();

        $issued = $this->postJson('/api/favorite-shares', [
            'product_codes' => [$first->code],
        ])->assertOk();

        $token = basename((string) parse_url((string) $issued->json('share.url'), PHP_URL_FRAGMENT));
        FavoriteShare::query()->sole()->forceFill(['expires_at' => now()->subMinute()])->save();

        $this->postJson('/api/favorite-shares/resolve', ['token' => $token])
            ->assertGone();

        $second->forceFill(['active' => false])->save();

        $this->postJson('/api/favorite-shares', [
            'product_codes' => [$first->code, $second->code],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_codes']);
    }

    public function test_resolve_is_post_only_and_customer_revoke_requires_session(): void
    {
        [$first] = $this->catalogProducts();

        $this->get('/api/favorite-shares/resolve')->assertMethodNotAllowed();

        $share = FavoriteShare::create([
            'token_hash' => hash('sha256', str_repeat('A', 64)),
            'expires_at' => now()->addDay(),
        ]);
        $share->products()->attach($first->id, ['sort_order' => 0]);

        $this->deleteJson("/api/customer/favorite-shares/{$share->id}")
            ->assertUnauthorized();
    }

    /**
     * @return array{0: Product, 1: Product}
     */
    private function catalogProducts(): array
    {
        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
            'active' => true,
        ]);
        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
            'active' => true,
        ]);

        $first = Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11001',
            'name_fa' => 'اول',
            'active' => true,
        ]);
        $second = Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11002',
            'name_fa' => 'دوم',
            'active' => true,
        ]);

        return [$first, $second];
    }
}
