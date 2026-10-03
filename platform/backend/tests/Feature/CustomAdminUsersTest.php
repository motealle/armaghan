<?php
namespace Tests\Feature;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use App\Services\GoogleAdminIdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;
class CustomAdminUsersTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); Cache::flush(); }
    private function owner(): User { return User::factory()->admin()->create(['email'=>'motealle@gmail.com','email_verified_at'=>now()]); }
    private function fields(array $extra=[]): array { return array_merge(['name'=>'New Buyer','email'=>'new@example.test','role'=>'customer','active'=>true,'password'=>'NewPassword2026','password_confirmation'=>'NewPassword2026'],$extra); }
    public function test_verified_google_provisions_new_admin_but_only_motealle_has_owner_authority(): void
    {
        foreach(config('owner-access.google_admin_emails') as $i=>$email){
            $identity=(new GoogleUser)->setRaw(['email_verified'=>true])->map(['id'=>'google-'.$i,'email'=>strtoupper($email),'name'=>'Admin']);
            $user=app(GoogleAdminIdentityService::class)->resolve($identity);
            $this->assertTrue($user->isActiveAdmin());$this->assertSame($email==='motealle@gmail.com',$user->isPrimaryOwner());
            $this->actingAs($user)->getJson('/api/admin/session')->assertJsonPath('admin.is_owner',$email==='motealle@gmail.com');
        }
        $this->assertDatabaseHas('users',['email'=>'amirmashti1378@gmail.com','role'=>'admin']);
        $owner=User::where('email','motealle@gmail.com')->firstOrFail();$owner->forceFill(['email_verified_at'=>null])->save();$this->assertFalse($owner->isPrimaryOwner());
    }
    public function test_guest_customer_and_disabled_admin_cannot_manage_accounts(): void
    {
        $this->getJson('/api/admin/users')->assertUnauthorized();
        foreach([User::factory()->create(),User::factory()->admin()->inactive()->create()] as $user){
            $this->actingAs($user)->getJson('/api/admin/users')->assertForbidden();$this->postJson('/api/admin/users',$this->fields())->assertForbidden();
        }
    }
    public function test_business_admin_creates_hashed_customer_account_and_real_password_login(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $row=$this->postJson('/api/admin/users',$this->fields())->assertCreated()->assertHeader('Cache-Control','no-store, private')->json('user');
        $user=User::findOrFail($row['id']);$this->assertTrue(Hash::check('NewPassword2026',$user->password));$this->assertNotNull($user->customer);$this->assertFalse($row['is_owner']);$this->assertArrayNotHasKey('password',$row);
        $this->postJson('/api/admin/users',$this->fields(['email'=>'bad@example.test','role'=>'admin']))->assertForbidden();
        $this->assertStringNotContainsString('NewPassword2026',ActivityLog::all()->toJson());
        $this->postJson('/api/admin/logout')->assertOk();$this->postJson('/api/auth/login',['email'=>$user->email,'password'=>'NewPassword2026'])->assertOk();
        $this->getJson('/api/customer/session')->assertOk()->assertJsonPath('customer.email',$user->email);
    }
    public function test_only_owner_creates_admin_and_reserved_google_identities_cannot_be_claimed(): void
    {
        $this->actingAs($this->owner());$this->postJson('/api/admin/users',$this->fields(['role'=>'admin']))->assertCreated()->assertJsonPath('user.role','admin');$this->assertDatabaseCount('customers',0);
        foreach(config('owner-access.google_admin_emails') as $email){$this->postJson('/api/admin/users',$this->fields(['email'=>$email]))->assertUnprocessable();}
        $this->postJson('/api/admin/users',$this->fields(['email'=>'forged@example.test','is_owner'=>true]))->assertUnprocessable();
        $this->postJson('/api/admin/users',$this->fields(['email'=>'weak@example.test','password'=>'short']))->assertUnprocessable();$this->assertDatabaseCount('users',2);
    }
    public function test_business_admin_cannot_list_edit_disable_or_escalate_admin_accounts(): void
    {
        $owner=$this->owner();$actor=User::factory()->admin()->create(['email'=>'amirmashti1378@gmail.com']);$target=User::factory()->admin()->create();$customer=User::factory()->create();Customer::create(['user_id'=>$customer->id,'active'=>true]);
        $this->actingAs($owner);$rows=collect($this->getJson('/api/admin/users')->json('users'))->keyBy('id');
        $this->actingAs($actor)->getJson('/api/admin/users')->assertJsonCount(1,'users')->assertJsonPath('users.0.id',$customer->id);
        foreach([$owner,$target] as $user){$this->patchJson('/api/admin/users/'.$user->id,['name'=>'Bad','role'=>'admin','active'=>false,'revision'=>$rows[$user->id]['revision']])->assertForbidden();}
        $this->patchJson('/api/admin/users/'.$customer->id,['name'=>'Bad','role'=>'admin','active'=>true,'revision'=>$rows[$customer->id]['revision']])->assertForbidden();$this->assertTrue($owner->fresh()->active);$this->assertTrue($target->fresh()->active);
    }
    public function test_owner_is_protected_and_can_disable_other_admin_without_deleting_data(): void
    {
        $owner=$this->owner();$target=User::factory()->admin()->create(['email'=>'amirmashti1378@gmail.com']);$this->actingAs($owner);$rows=collect($this->getJson('/api/admin/users')->json('users'))->keyBy('id');
        $this->patchJson('/api/admin/users/'.$owner->id,['name'=>'Bad','role'=>'admin','active'=>false,'revision'=>$rows[$owner->id]['revision']])->assertForbidden();
        $this->patchJson('/api/admin/users/'.$target->id,['name'=>$target->name,'role'=>'admin','active'=>false,'revision'=>$rows[$target->id]['revision']])->assertOk();
        $this->actingAs($target->fresh())->getJson('/api/admin/products')->assertForbidden();$this->assertDatabaseCount('users',2);
    }
    public function test_customer_deactivation_blocks_password_login_and_stale_edit_fails(): void
    {
        $this->freezeTime();$this->actingAs(User::factory()->admin()->create());$row=$this->postJson('/api/admin/users',$this->fields())->json('user');
        $this->patchJson('/api/admin/users/'.$row['id'],['name'=>'Changed','role'=>'customer','active'=>false,'revision'=>$row['revision']])->assertOk();
        $this->patchJson('/api/admin/users/'.$row['id'],['name'=>'Stale','role'=>'customer','active'=>true,'revision'=>$row['revision']])->assertConflict();$this->assertFalse(Customer::query()->sole()->active);
        // A CRM-only reactivation cannot bypass the disabled linked account.
        $customer=Customer::query()->sole();$customer->update(['active'=>true]);
        $this->withSession([\App\Support\CustomerSession::KEY=>$customer->id])->getJson('/api/customer/session')->assertUnauthorized();
        $this->postJson('/api/auth/login',['email'=>$row['email'],'password'=>'NewPassword2026'])->assertUnprocessable();$this->assertDatabaseHas('users',['id'=>$row['id'],'name'=>'Changed','active'=>false]);
    }
    public function test_owner_can_reactivate_reserved_inactive_customer_before_verified_google_promotion(): void
    {
        $owner=$this->owner();$target=User::factory()->inactive()->create(['email'=>'amirmashti1378@gmail.com']);
        Customer::create(['user_id'=>$target->id,'active'=>false]);$this->actingAs($owner);
        $rows=collect($this->getJson('/api/admin/users')->json('users'))->keyBy('id');
        $this->patchJson('/api/admin/users/'.$target->id,['name'=>$target->name,'role'=>'customer','active'=>true,'revision'=>$rows[$target->id]['revision']])->assertOk();
        $identity=(new GoogleUser)->setRaw(['email_verified'=>true])->map(['id'=>'google-amir','email'=>$target->email,'name'=>'Amir']);
        $this->assertTrue(app(GoogleAdminIdentityService::class)->resolve($identity)->isActiveAdmin());
        $this->assertFalse($target->fresh()->isPrimaryOwner());
    }
    public function test_admin_profile_cannot_claim_owner_mailbox_even_before_owner_exists(): void
    {
        $user=User::factory()->admin()->create(['email'=>'amirmashti1378@gmail.com']);
        try { $user->update(['email'=>'motealle@gmail.com']);$this->fail('Profile identity escalation accepted'); }
        catch (\Illuminate\Validation\ValidationException) { $this->assertSame('amirmashti1378@gmail.com',$user->fresh()->email); }
        $this->assertFalse($user->fresh()->isPrimaryOwner());
    }
    public function test_existing_email_role_password_and_verification_cannot_be_reassigned(): void
    {
        $this->actingAs($this->owner());$row=$this->postJson('/api/admin/users',$this->fields())->json('user');
        foreach(['email'=>'different@example.test','password'=>'Different2026','email_verified_at'=>'2026-10-03','is_owner'=>true] as $key=>$value){$this->patchJson('/api/admin/users/'.$row['id'],['name'=>'Keep','role'=>'customer','active'=>true,'revision'=>$row['revision'],$key=>$value])->assertUnprocessable();}
        $this->patchJson('/api/admin/users/'.$row['id'],['name'=>'Keep','role'=>'admin','active'=>true,'revision'=>$row['revision']])->assertUnprocessable();$this->assertDatabaseHas('users',['id'=>$row['id'],'role'=>'customer','email'=>$row['email']]);
    }
    public function test_only_owner_can_edit_own_name_but_nobody_can_delete_disable_or_demote_owner(): void
    {
        $owner=$this->owner();$this->actingAs($owner);
        $row=collect($this->getJson('/api/admin/users')->json('users'))->firstWhere('id',$owner->id);
        $this->patchJson('/api/admin/users/'.$owner->id,['name'=>'Updated owner','role'=>'admin','active'=>true,'revision'=>$row['revision']])->assertOk();
        $this->assertSame('Updated owner',$owner->fresh()->name);
        $actor=User::factory()->admin()->create();$this->actingAs($actor);
        $this->patchJson('/api/admin/users/'.$owner->id,['name'=>'Forged','role'=>'admin','active'=>true,'revision'=>$row['revision']])->assertForbidden();
        foreach (['delete','disable','demote'] as $operation) {
            try {
                $target=$owner->fresh();
                match($operation) {'delete'=>$target->delete(),'disable'=>$target->update(['active'=>false]),'demote'=>$target->update(['role'=>\App\Enums\UserRole::Customer])};
                $this->fail('Primary owner protection bypassed');
            } catch (\Illuminate\Validation\ValidationException) {
                $this->assertTrue($owner->fresh()->isPrimaryOwner());
            }
        }
    }

}
