<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfflineCustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); Cache::flush(); }

    private function fields(Customer $customer, array $extra = []): array
    {
        return array_merge(['name' => 'Offline Buyer', 'email' => 'offline@example.test', 'role' => 'customer',
            'active' => true, 'password' => 'CustomerPassword2026', 'password_confirmation' => 'CustomerPassword2026',
            'customer_id' => $customer->id, 'customer_revision' => app(CustomerController::class)->revision($customer->fresh())], $extra);
    }

    public function test_new_login_uses_existing_customer_and_preserves_orders_and_private_profile(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $customer = Customer::create(['company_name' => 'Existing company', 'active' => true, 'whatsapp' => '123456789',
            'notes' => 'Private staff note', 'priority' => 7, 'country_code' => 'IQ', 'direct_link_enabled' => true]);
        $order = $this->postJson('/api/admin/orders', ['customer_id' => $customer->id, 'request_path' => 'custom',
            'description' => 'Existing order before account creation'])->assertCreated()->json('order');
        $payload = $this->fields($customer); unset($payload['role'], $payload['active'], $payload['customer_id']);
        $response = $this->postJson('/api/admin/customers/'.$customer->id.'/account', $payload)->assertCreated();
        $user = User::findOrFail($response->json('user.id'));
        $this->assertTrue(Hash::check('CustomerPassword2026', $user->password));
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'user_id' => $user->id, 'company_name' => 'Existing company',
            'notes' => 'Private staff note', 'priority' => 7, 'country_code' => 'IQ', 'whatsapp' => '123456789', 'direct_link_enabled' => true]);
        $this->assertDatabaseHas('tracked_orders', ['id' => $order['id'], 'customer_id' => $customer->id]);
        $this->getJson('/api/admin/customers')->assertJsonPath('customers.0.has_account', true);
        $log = ActivityLog::where('action', 'admin.customer.account.created')->sole();
        $this->assertSame($customer->id, $log->customer_id);
        $this->assertStringNotContainsString('CustomerPassword2026', json_encode($log->toArray()));
        $this->postJson('/api/admin/logout')->assertOk();
        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'CustomerPassword2026'])->assertOk();
        $this->getJson('/api/customer/session')->assertJsonPath('customer.id', $customer->id)->assertJsonMissingPath('customer.notes');
        $this->getJson('/api/customer/orders')->assertOk()->assertJsonCount(1, 'orders')->assertJsonPath('orders.0.id', $order['id']);
    }

    public function test_stale_inactive_missing_and_linked_targets_do_not_create_an_account(): void
    {
        $actor = User::factory()->admin()->create();
        $this->actingAs($actor);
        $customer = Customer::create(['active' => true, 'company_name' => 'Keep']);
        $stale = $this->fields($customer);
        $customer->update(['notes' => 'Changed']);
        $this->postJson('/api/admin/users', $stale)->assertConflict();
        $customer->update(['active' => false]);
        $this->postJson('/api/admin/users', $this->fields($customer))->assertUnprocessable();
        $customer->update(['active' => true]);
        $this->postJson('/api/admin/users', $this->fields($customer, ['customer_id' => 999999]))->assertNotFound();
        $customer->update(['user_id' => $actor->id]);
        $this->postJson('/api/admin/users', $this->fields($customer))->assertConflict();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('activity_log', 0);
        $this->assertSame($actor->id, $customer->fresh()->user_id);
    }

    public function test_reserved_mailboxes_admin_roles_and_invalid_payloads_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create(['email' => 'motealle@gmail.com', 'email_verified_at' => now()]));
        $customer = Customer::create(['active' => true]);
        foreach (config('owner-access.google_admin_emails') as $email) {
            $this->postJson('/api/admin/users', $this->fields($customer, ['email' => $email]))->assertUnprocessable();
        }
        foreach ([['role' => 'admin'], ['active' => false], ['customer_revision' => 'invalid'],
            ['password' => 'short', 'password_confirmation' => 'short']] as $extra) {
            $this->postJson('/api/admin/users', $this->fields($customer, $extra))->assertUnprocessable();
        }
        $payload = $this->fields($customer); unset($payload['customer_revision']);
        $this->postJson('/api/admin/users', $payload)->assertUnprocessable();
        User::factory()->create(['email' => 'offline@example.test']);
        $this->postJson('/api/admin/users', $this->fields($customer))->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('customers', 1);
        $this->assertNull($customer->fresh()->user_id);
        $this->assertDatabaseCount('activity_log', 0);
    }
    public function test_dedicated_route_rejects_unauthorized_and_forged_account_fields(): void
    {
        $customer = Customer::create(['active' => true]);
        $payload = $this->fields($customer); unset($payload['role'], $payload['active'], $payload['customer_id']);
        $url = '/api/admin/customers/'.$customer->id.'/account';
        $this->postJson($url, $payload)->assertUnauthorized();
        $this->actingAs(User::factory()->create())->postJson($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->admin()->inactive()->create())->postJson($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->admin()->create());
        foreach (['role' => 'admin', 'active' => false, 'customer_id' => 999] as $field => $value) {
            $this->postJson($url, array_merge($payload, [$field => $value]))->assertUnprocessable();
        }
        $this->assertNull($customer->fresh()->user_id);
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('activity_log', 0);
    }

}
