<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Models\AccountArchive;
use App\Models\Customer;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountArchiveTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): array
    {
        $user = User::factory()->create();
        $customer = Customer::create(['user_id' => $user->id, 'company_name' => 'Keep business', 'active' => true, 'direct_link_enabled' => true]);
        return [$user, $customer];
    }

    private function backup(User $actor, string $resource, $row): array
    {
        $controller = $resource === 'users' ? UserController::class : CustomerController::class;
        return $this->actingAs($actor)->postJson('/api/admin/account-archives/'.$resource.'/'.$row->id.'/backup',
            ['revision' => app($controller)->revision($row->fresh())])->assertOk()->json();
    }

    private function remove(array $backup): void
    {
        $this->deleteJson('/api/admin/account-archives/'.$backup['archive_id'],
            ['receipt' => $backup['receipt'], 'backup_sha256' => $backup['backup_sha256'], 'backup_downloaded' => true])->assertOk();
    }

    public function test_backup_first_delete_and_database_undo_preserve_records_and_revoke_sessions(): void
    {
        [$user, $customer] = $this->buyer();
        $actor = User::factory()->admin()->create();
        $password = $user->password;
        $customer->businessProfile()->create(['contact_name' => 'Business contact', 'city' => 'Tehran', 'pinned' => true]);
        \App\Models\TrackedOrder::create(['customer_id' => $customer->id, 'reference' => 'AT-ARCHIVE-TEST',
            'request_path' => 'simple', 'description' => 'Preserved order', 'stage' => 'inquiry']);
        $magic = \App\Models\MagicLink::create(['customer_id' => $customer->id,
            'token_hash' => hash('sha256', 'test-secret'), 'scope' => 'customer-portal',
            'enabled' => true, 'expires_at' => now()->addHour()]);
        DB::table('sessions')->insert(['id' => 'old-session', 'user_id' => $user->id, 'payload' => 'x', 'last_activity' => now()->timestamp]);
        $this->withSession([CustomerSession::KEY => $customer->id]);
        $backup = $this->backup($actor, 'users', $user);
        $this->assertStringNotContainsString($password, $backup['backup']);
        $this->assertSame('Tehran', json_decode($backup['backup'], true)['customer_business']['city']);
        $this->assertStringNotContainsString($password, DB::table('account_archives')->value('snapshot'));
        $this->deleteJson('/api/admin/account-archives/'.$backup['archive_id'], ['receipt' => $backup['receipt']])->assertUnprocessable();
        $this->remove($backup);
        $this->getJson('/api/admin/users')->assertJsonCount(0, 'users');
        $this->getJson('/api/admin/customers')->assertJsonCount(0, 'customers');
        $this->getJson('/api/admin/orders')->assertOk()->assertJsonPath('orders.0.customer_name', 'Keep business');
        $this->assertNotNull($magic->fresh()->revoked_at);
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->assertDatabaseMissing('sessions', ['id' => 'old-session']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'password' => $password, 'active' => false]);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'user_id' => $user->id]);
        $this->postJson('/api/admin/account-archives/'.$backup['archive_id'].'/restore')->assertOk();
        $this->assertTrue(User::findOrFail($user->id)->active);
        $this->assertTrue(Customer::findOrFail($customer->id)->active);
        $profile = Customer::findOrFail($customer->id)->businessProfile;
        $this->assertSame('Business contact', $profile->contact_name);
        $this->assertTrue($profile->pinned);
        // Undo must not revive the pre-deletion customer session.
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->postJson('/api/admin/account-archives/'.$backup['archive_id'].'/restore')->assertConflict();
    }

    public function test_downloaded_json_can_restore_but_modified_backup_cannot(): void
    {
        [$user, $customer] = $this->buyer();
        $actor = User::factory()->admin()->create();
        $backup = $this->backup($actor, 'customers', $customer);
        $this->remove($backup);
        $this->postJson('/api/admin/account-archives/import', ['backup' => str_replace('Keep business', 'Forged business', $backup['backup'])])->assertUnprocessable();
        $this->assertNull(Customer::find($customer->id));
        $this->postJson('/api/admin/account-archives/import', ['backup' => $backup['backup']])->assertOk();
        $this->assertSame('Keep business', Customer::findOrFail($customer->id)->company_name);
        $this->assertSame($user->password, User::findOrFail($user->id)->password);
    }

    public function test_guest_customer_and_disabled_admin_cannot_download_delete_restore_or_import(): void
    {
        [$user] = $this->buyer();
        foreach ([null, User::factory()->create(), User::factory()->admin()->inactive()->create()] as $actor) {
            if ($actor) $this->actingAs($actor);
            $status = $actor ? 403 : 401;
            $this->postJson('/api/admin/account-archives/users/'.$user->id.'/backup', ['revision' => app(UserController::class)->revision($user)])->assertStatus($status);
            $this->getJson('/api/admin/account-archives/users')->assertStatus($status);
            $this->deleteJson('/api/admin/account-archives/00000000-0000-4000-8000-000000000000')->assertStatus($status);
            $this->postJson('/api/admin/account-archives/00000000-0000-4000-8000-000000000000/restore')->assertStatus($status);
            $this->postJson('/api/admin/account-archives/import', ['backup' => '{}'])->assertStatus($status);
        }
    }

    public function test_self_owner_and_other_admin_are_protected_from_business_admin(): void
    {
        $actor = User::factory()->admin()->create();
        $owner = User::factory()->admin()->create(['email' => config('owner-access.primary_owner_email'), 'email_verified_at' => now()]);
        foreach ([$actor, $owner, User::factory()->admin()->create()] as $target) {
            $this->actingAs($actor)->postJson('/api/admin/account-archives/users/'.$target->id.'/backup', ['revision' => app(UserController::class)->revision($target)])->assertForbidden();
        }
        $this->actingAs($owner)->postJson('/api/admin/account-archives/users/'.$owner->id.'/backup', ['revision' => app(UserController::class)->revision($owner)])->assertForbidden();
    }

    public function test_owner_can_delete_other_admin_but_business_admin_cannot_restore_it(): void
    {
        $owner = User::factory()->admin()->create(['email' => config('owner-access.primary_owner_email'), 'email_verified_at' => now()]);
        $target = User::factory()->admin()->create();
        $backup = $this->backup($owner, 'users', $target);
        $this->remove($backup);
        $this->actingAs(User::factory()->admin()->create())->getJson('/api/admin/account-archives/users')->assertJsonCount(0, 'archives');
        $this->postJson('/api/admin/account-archives/'.$backup['archive_id'].'/restore')->assertForbidden();
        $this->postJson('/api/admin/account-archives/import', ['backup' => $backup['backup']])->assertForbidden();
        $this->actingAs($owner)->postJson('/api/admin/account-archives/'.$backup['archive_id'].'/restore')->assertOk();
    }

    public function test_receipt_is_actor_bound_expiring_single_use_and_linked_changes_require_new_backup(): void
    {
        [$user, $customer] = $this->buyer();
        $actor = User::factory()->admin()->create();
        $backup = $this->backup($actor, 'users', $user);
        $body = ['receipt' => $backup['receipt'], 'backup_sha256' => $backup['backup_sha256'], 'backup_downloaded' => true];
        $this->actingAs(User::factory()->admin()->create())->deleteJson('/api/admin/account-archives/'.$backup['archive_id'], $body)->assertConflict();
        $customer->update(['notes' => 'New edit']);
        $this->actingAs($actor)->deleteJson('/api/admin/account-archives/'.$backup['archive_id'], $body)->assertConflict();
        $backup = $this->backup($actor, 'users', $user->fresh());
        $this->travel(16)->minutes();
        $this->deleteJson('/api/admin/account-archives/'.$backup['archive_id'], ['receipt' => $backup['receipt'], 'backup_sha256' => $backup['backup_sha256'], 'backup_downloaded' => true])->assertConflict();
        $backup = $this->backup($actor, 'users', $user->fresh());
        $this->remove($backup);
        $this->deleteJson('/api/admin/account-archives/'.$backup['archive_id'], ['receipt' => $backup['receipt'], 'backup_sha256' => $backup['backup_sha256'], 'backup_downloaded' => true])->assertConflict();
    }

    public function test_customer_without_login_round_trip_and_stale_revision(): void
    {
        $actor = User::factory()->admin()->create();
        $customer = Customer::create(['company_name' => 'CRM only', 'active' => false]);
        $revision = app(CustomerController::class)->revision($customer);
        $customer->update(['company_name' => 'Changed']);
        $this->actingAs($actor)->postJson('/api/admin/account-archives/customers/'.$customer->id.'/backup', ['revision' => $revision])->assertConflict();
        $backup = $this->backup($actor, 'customers', $customer->fresh());
        $this->remove($backup);
        $this->postJson('/api/admin/account-archives/'.$backup['archive_id'].'/restore')->assertOk();
        $this->assertFalse(Customer::findOrFail($customer->id)->active);
    }
}
