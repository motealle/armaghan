<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BulkAdminTagsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); Cache::flush();
    }

    private function product(string $code): Product
    {
        $category = Category::firstOrCreate(['code' => '1'], ['name_fa' => 'نوزادی']);
        $sub = Subcategory::firstOrCreate(['code' => '11'], ['category_id' => $category->id, 'name_fa' => 'لباس']);
        return Product::create(['subcategory_id' => $sub->id, 'code' => $code, 'name_fa' => 'لباس', 'availability' => 'available']);
    }

    private function item($row, string $resource): array
    {
        $controller = match ($resource) {
            'products' => \App\Http\Controllers\Admin\ProductController::class,
            'customers' => \App\Http\Controllers\Admin\CustomerController::class,
            'users' => \App\Http\Controllers\Admin\UserController::class,
        };
        return ['id' => $row->id, 'revision' => app($controller)->revision($row->fresh())];
    }

    public function test_persistent_tags_add_remove_replace_and_admin_rows_without_public_leak(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        foreach (['products' => $this->product('11001'), 'customers' => Customer::create(['company_name' => 'Business']), 'users' => User::factory()->create()] as $resource => $row) {
            $url = '/api/admin/bulk-tags/'.$resource;
            $this->postJson($url, ['mode' => 'add', 'tags' => ['VIP', 'vip', 'پیگیری'], 'items' => [$this->item($row, $resource)]])->assertOk()->assertJsonPath('updated', 1);
            $this->assertSame(['VIP', 'پیگیری'], $row->fresh()->adminTags());
            $this->postJson($url, ['mode' => 'remove', 'tags' => ['vIp'], 'items' => [$this->item($row, $resource)]])->assertOk();
            $this->assertSame(['پیگیری'], $row->fresh()->adminTags());
            $this->getJson('/api/admin/'.$resource)->assertOk()->assertJsonFragment(['tags' => ['پیگیری']]);
            $this->postJson($url, ['mode' => 'replace', 'tags' => [], 'items' => [$this->item($row, $resource)]])->assertOk();
            $this->assertSame([], $row->fresh()->adminTags());
        }
        $this->getJson('/api/catalog/products')->assertOk()->assertJsonMissingPath('data.0.tags');
        $this->assertStringNotContainsString('پیگیری', ActivityLog::all()->toJson());
    }

    public function test_atomic_conflicts_missing_targets_and_bounded_validation(): void
    {
        $this->actingAs(User::factory()->admin()->create()); $a = $this->product('11001'); $b = $this->product('11002');
        $items = [$this->item($a, 'products'), $this->item($b, 'products')];
        $b->update(['name_fa' => 'Changed']);
        $url = '/api/admin/bulk-tags/products';
        $this->postJson($url, ['mode' => 'add', 'tags' => ['پیگیری'], 'items' => $items])->assertConflict();
        $this->assertDatabaseCount('admin_record_tags', 0); $this->assertDatabaseCount('activity_log', 0);
        $items = [$this->item($a, 'products')];
        foreach ([['<script>'], [str_repeat('x', 41)], array_map(fn ($i) => 'tag'.$i, range(1, 11)), []] as $tags) {
            $this->postJson($url, ['mode' => 'add', 'tags' => $tags, 'items' => $items])->assertUnprocessable();
        }
        $this->postJson($url, ['mode' => 'add', 'tags' => ['A'], 'items' => array_merge($items, $items)])->assertUnprocessable();
        $this->postJson($url, ['mode' => 'add', 'tags' => ['A'], 'items' => [['id' => 99999, 'revision' => str_repeat('a', 64)]]])->assertNotFound();
        $this->postJson($url, ['mode' => 'add', 'tags' => ['A'], 'items' => $items])->assertOk();
        $this->postJson($url, ['mode' => 'add', 'tags' => ['B'], 'items' => $items])->assertConflict();
        $this->postJson('/api/admin/bulk-status/products', ['active' => false, 'items' => $items])->assertConflict();
        $this->assertTrue($a->fresh()->active);
    }

    public function test_owner_actor_reserved_and_owner_customer_permissions_roll_back_all(): void
    {
        $owner = User::factory()->admin()->create(['email' => config('owner-access.primary_owner_email'), 'email_verified_at' => now()]);
        $admin = User::factory()->admin()->create(); $customer = User::factory()->create();
        $ownerCustomer = Customer::create(['user_id' => $owner->id, 'company_name' => 'Owner']);
        $ordinary = Customer::create(['company_name' => 'Ordinary']);
        $this->actingAs($admin);
        $this->postJson('/api/admin/bulk-tags/customers', ['mode' => 'add', 'tags' => ['A'], 'items' => [$this->item($ordinary, 'customers'), $this->item($ownerCustomer, 'customers')]])->assertForbidden();
        foreach ([$admin, $owner] as $target) {
            $this->postJson('/api/admin/bulk-tags/users', ['mode' => 'add', 'tags' => ['A'], 'items' => [$this->item($customer, 'users'), $this->item($target, 'users')]])->assertForbidden();
        }
        $this->assertDatabaseCount('admin_record_tags', 0);
        $this->actingAs($owner);
        $this->postJson('/api/admin/bulk-tags/users', ['mode' => 'add', 'tags' => ['A'], 'items' => [$this->item($admin, 'users')]])->assertOk();
        $this->postJson('/api/admin/bulk-tags/users', ['mode' => 'replace', 'tags' => [], 'items' => [$this->item($owner, 'users')]])->assertForbidden();
    }

    public function test_guest_customer_and_inactive_admin_cannot_tag(): void
    {
        $row = $this->product('11001');
        $body = ['mode' => 'add', 'tags' => ['A'], 'items' => [$this->item($row, 'products')]];
        $this->postJson('/api/admin/bulk-tags/products', $body)->assertUnauthorized();
        $this->actingAs(User::factory()->create()); $this->postJson('/api/admin/bulk-tags/products', $body)->assertForbidden();
        $this->actingAs(User::factory()->admin()->inactive()->create()); $this->postJson('/api/admin/bulk-tags/products', $body)->assertForbidden();
        $this->assertDatabaseCount('admin_record_tags', 0);
    }
}
