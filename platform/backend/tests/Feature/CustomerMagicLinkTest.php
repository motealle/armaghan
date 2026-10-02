<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\MagicLink;
use App\Models\User;
use App\Services\CustomerMagicLinkService;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_issue_fragment_link_and_customer_session_survives_alongside_admin_auth(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'active' => true,
        ]);
        $customer = Customer::create([
            'company_name' => 'Baghdad Buyer',
            'whatsapp' => '+9647000000000',
            'country_code' => 'IQ',
            'country_name' => 'Iraq',
            'active' => true,
            'direct_link_enabled' => true,
        ]);

        $response = $this->actingAs($admin)
            ->postJson("/api/admin/customers/{$customer->id}/magic-link", [
                'expires_in_hours' => 24,
            ])
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonStructure(['url', 'expires_at']);

        $url = (string) $response->json('url');
        $this->assertSame('/t/27/', parse_url($url, PHP_URL_PATH));
        $this->assertNull(parse_url($url, PHP_URL_QUERY));

        $fragment = (string) parse_url($url, PHP_URL_FRAGMENT);
        $this->assertStringStartsWith('/magic/', $fragment);
        $token = basename($fragment);

        $magicLink = MagicLink::query()->sole();
        $this->assertSame(64, strlen($token));
        $this->assertNotSame($token, $magicLink->token_hash);
        $this->assertSame(hash('sha256', $token), $magicLink->token_hash);
        $this->assertTrue($magicLink->enabled);
        $this->assertSame(CustomerMagicLinkService::SCOPE, $magicLink->scope);
        $this->assertDatabaseHas('activity_log', [
            'actor_user_id' => $admin->id,
            'customer_id' => $customer->id,
            'action' => 'customer.magic_link.issued',
        ]);

        $this->postJson('/api/customer/magic-link/consume', ['token' => $token])
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('customer.id', $customer->id)
            ->assertJsonPath('customer.company_name', 'Baghdad Buyer')
            ->assertJsonMissingPath('customer.notes')
            ->assertJsonMissingPath('customer.user_id');

        $this->assertAuthenticatedAs($admin);
        $this->assertSame($customer->id, session(CustomerSession::KEY));

        $this->patchJson('/api/customer/session', [
            'company_name' => 'Baghdad Buyer Updated',
            'whatsapp' => '+9647111111111',
            'notes' => 'must not be customer-writable',
        ])
            ->assertOk()
            ->assertJsonPath('customer.company_name', 'Baghdad Buyer Updated');

        $customer->refresh();
        $this->assertSame('Baghdad Buyer Updated', $customer->company_name);
        $this->assertSame('+9647111111111', $customer->whatsapp);
        $this->assertNull($customer->notes);

        $this->postJson('/api/customer/magic-link/consume', ['token' => $token])
            ->assertGone()
            ->assertJsonPath('message', 'Magic link is unavailable.');

        $magicLink->refresh();
        $this->assertFalse($magicLink->enabled);
        $this->assertNotNull($magicLink->last_used_at);
        $this->assertSame(
            1,
            ActivityLog::query()->where('action', 'customer.magic_link.consumed')->count(),
        );

        $this->postJson('/api/customer/logout')
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session(CustomerSession::KEY));
        $this->getJson('/api/customer/session')->assertUnauthorized();
    }

    public function test_new_link_revokes_previous_and_expired_or_disabled_links_cannot_login(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'active' => true,
        ]);
        $customer = Customer::create([
            'company_name' => 'Dubai Trade',
            'active' => true,
            'direct_link_enabled' => true,
        ]);

        $firstUrl = $this->actingAs($admin)
            ->postJson("/api/admin/customers/{$customer->id}/magic-link")
            ->assertOk()
            ->json('url');

        $secondUrl = $this->postJson("/api/admin/customers/{$customer->id}/magic-link", [
            'expires_in_hours' => 72,
        ])
            ->assertOk()
            ->json('url');

        $links = MagicLink::query()->orderBy('id')->get();
        $this->assertCount(2, $links);
        $this->assertFalse($links[0]->enabled);
        $this->assertNotNull($links[0]->revoked_at);
        $this->assertTrue($links[1]->enabled);

        $firstToken = basename((string) parse_url((string) $firstUrl, PHP_URL_FRAGMENT));
        $secondToken = basename((string) parse_url((string) $secondUrl, PHP_URL_FRAGMENT));

        $this->postJson('/api/customer/magic-link/consume', ['token' => $firstToken])
            ->assertGone();

        $links[1]->forceFill(['expires_at' => now()->subMinute()])->save();

        $this->postJson('/api/customer/magic-link/consume', ['token' => $secondToken])
            ->assertGone();

        $this->assertNull(session(CustomerSession::KEY));

        $customer->forceFill(['direct_link_enabled' => false])->save();

        $this->postJson("/api/admin/customers/{$customer->id}/magic-link")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['customer']);
    }

    public function test_admin_can_revoke_active_links_and_non_admin_cannot_issue_them(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'active' => true,
        ]);
        $nonAdmin = User::factory()->create([
            'role' => UserRole::Customer,
            'active' => true,
        ]);
        $customer = Customer::create([
            'company_name' => 'Istanbul Store',
            'active' => true,
            'direct_link_enabled' => true,
        ]);

        $this->actingAs($nonAdmin)
            ->postJson("/api/admin/customers/{$customer->id}/magic-link")
            ->assertForbidden();

        $issued = $this->actingAs($admin)
            ->postJson("/api/admin/customers/{$customer->id}/magic-link")
            ->assertOk()
            ->json('url');

        $token = basename((string) parse_url((string) $issued, PHP_URL_FRAGMENT));

        $this->deleteJson("/api/admin/customers/{$customer->id}/magic-link")
            ->assertOk()
            ->assertJsonPath('revoked_count', 1);

        $this->postJson('/api/customer/magic-link/consume', ['token' => $token])
            ->assertGone();

        $this->assertDatabaseHas('activity_log', [
            'actor_user_id' => $admin->id,
            'customer_id' => $customer->id,
            'action' => 'customer.magic_link.revoked',
        ]);
    }

    public function test_magic_consume_route_is_post_only_and_guest_session_is_private(): void
    {
        $this->get('/api/customer/magic-link/consume')->assertMethodNotAllowed();

        $this->getJson('/api/customer/session')
            ->assertUnauthorized()
            ->assertHeader('Cache-Control', 'no-store, private');
    }
}
