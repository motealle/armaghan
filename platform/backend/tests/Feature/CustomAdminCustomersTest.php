<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomAdminCustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_customer_and_disabled_admin_cannot_read_or_mutate_records(): void
    {
        $row = Customer::query()->create(['company_name' => 'Private company', 'notes' => 'Private note']);
        foreach ([null, User::factory()->create(), User::factory()->admin()->inactive()->create()] as $actor) {
            if ($actor) { $this->actingAs($actor); }
            $status = $actor ? 403 : 401;
            $this->getJson('/api/admin/session')->assertStatus($status);
            $this->getJson('/api/admin/customers')->assertStatus($status)->assertJsonMissing(['notes' => 'Private note']);
            $this->postJson('/api/admin/customers', ['company_name' => 'Bad'])->assertStatus($status);
            $this->patchJson('/api/admin/customers/'.$row->id, ['active' => false])->assertStatus($status);
        }
        $this->assertDatabaseCount('customers', 1);
    }

    public function test_admin_create_edit_reload_archive_and_audit_round_trip(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $created = $this->postJson('/api/admin/customers', ['company_name' => 'Real business', 'whatsapp' => '+96412345678',
            'country_code' => 'IQ', 'country_name' => 'Iraq', 'notes' => 'Internal only', 'priority' => 4,
            'active' => true, 'direct_link_enabled' => false])->assertCreated()->assertHeader('Cache-Control', 'no-store, private')->json('customer');
        $this->assertNull($created['email']);
        $this->assertNull(Customer::query()->sole()->user_id);
        $this->patchJson('/api/admin/customers/'.$created['id'], ['revision' => $created['revision'], 'company_name' => 'Updated', 'priority' => 8])
            ->assertOk()->assertJsonPath('customer.priority', 8);
        $current = $this->getJson('/api/admin/customers')->assertOk()->assertJsonPath('customers.0.company_name', 'Updated')->json('customers.0');
        $this->patchJson('/api/admin/customers/'.$created['id'], ['revision' => $current['revision'], 'active' => false])->assertOk()->assertJsonPath('customer.active', false);
        $this->assertDatabaseCount('customers', 1);
        $logs = ActivityLog::query()->get();
        $this->assertCount(3, $logs);
        $this->assertStringNotContainsString('Internal only', $logs->toJson());
    }

    public function test_stale_revision_cannot_overwrite_a_change_in_the_same_second(): void
    {
        $this->freezeTime();
        $this->actingAs(User::factory()->admin()->create());
        $row = $this->postJson('/api/admin/customers', ['company_name' => 'Initial'])->assertCreated()->json('customer');
        $this->patchJson('/api/admin/customers/'.$row['id'], ['revision' => $row['revision'], 'notes' => 'First edit'])->assertOk();
        $this->patchJson('/api/admin/customers/'.$row['id'], ['revision' => $row['revision'], 'notes' => 'Stale edit'])->assertConflict();
        $this->assertSame('First edit', Customer::query()->sole()->notes);
    }

    public function test_account_fields_cannot_be_reassigned_or_credentials_changed(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $customer = Customer::query()->create(['company_name' => 'Existing', 'user_id' => $owner->id]);
        $password = $owner->password;
        $this->actingAs($admin);
        foreach (['user_id' => $admin->id, 'role' => 'admin', 'password' => 'Anything123456', 'email' => 'other@example.test', 'name' => 'Other'] as $field => $value) {
            $this->postJson('/api/admin/customers', ['company_name' => 'Bad', $field => $value])->assertUnprocessable();
        }
        $revision = $this->getJson('/api/admin/customers')->assertOk()->json('customers.0.revision');
        $this->patchJson('/api/admin/customers/'.$customer->id, ['revision' => $revision, 'user_id' => $admin->id])->assertUnprocessable();
        $this->assertSame($owner->id, $customer->fresh()->user_id);
        $this->assertSame($password, $owner->fresh()->password);
    }

    public function test_validation_pagination_search_and_safe_account_identity(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->postJson('/api/admin/customers', ['company_name' => 'Test', 'priority' => 256, 'country_code' => 'IRAQ'])->assertUnprocessable();
        for ($i = 0; $i < 51; $i++) { Customer::query()->create(['company_name' => 'Business '.$i]); }
        $this->getJson('/api/admin/customers')->assertOk()->assertJsonCount(50, 'customers')->assertJsonPath('total', 51)->assertJsonPath('last_page', 2);
        $this->getJson('/api/admin/customers?page=2')->assertJsonCount(1, 'customers');
        $this->getJson('/api/admin/customers?search=Business%2050')->assertJsonCount(1, 'customers');
        $owner = User::factory()->create(['name' => 'Account owner', 'email' => 'owner@example.test']);
        Customer::query()->create(['user_id' => $owner->id, 'company_name' => 'Company']);
        $this->getJson('/api/admin/customers?search=owner%40example.test')->assertOk()->assertJsonPath('customers.0.name', 'Account owner')
            ->assertJsonMissingPath('customers.0.password')->assertJsonMissingPath('customers.0.user_id');
    }

    public function test_admin_logout_revokes_admin_access_without_clobbering_customer_session(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = Customer::query()->create(['company_name' => 'Independent', 'active' => true]);
        $this->actingAs($admin)->withSession([CustomerSession::KEY => $customer->id]);
        $this->getJson('/api/admin/session')->assertOk()->assertExactJson(['admin' => ['name' => $admin->name, 'email' => $admin->email, 'is_owner' => false]]);
        $this->postJson('/api/admin/logout')->assertOk();
        $this->getJson('/api/admin/session')->assertUnauthorized();
        $this->getJson('/api/customer/session')->assertOk()->assertJsonPath('customer.id', $customer->id);
    }
    public function test_deactivation_denies_customer_portal_but_preserves_record_and_account(): void
    {
        $owner = User::factory()->create();
        $row = Customer::query()->create(['user_id' => $owner->id, 'company_name' => 'Keep records', 'active' => true]);
        $this->withSession([CustomerSession::KEY => $row->id]);
        $this->getJson('/api/customer/session')->assertOk();
        $this->actingAs(User::factory()->admin()->create());
        $revision = $this->getJson('/api/admin/customers')->json('customers.0.revision');
        $this->patchJson('/api/admin/customers/'.$row->id, ['revision' => $revision, 'active' => false])->assertOk();
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->assertDatabaseHas('customers', ['id' => $row->id, 'user_id' => $owner->id]);
        $this->assertDatabaseHas('users', ['id' => $owner->id]);
    }

}
