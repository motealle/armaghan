<?php
namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\AdminAuthState;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PasswordAuthTest extends TestCase
{
    use RefreshDatabase;
    private function registration(array $extra = []): array
    {
        return array_merge(['name'=>'Real Buyer', 'email'=>'buyer@example.test', 'password'=>'BuyerPassword2026',
            'password_confirmation'=>'BuyerPassword2026'], $extra);
    }
    public function test_registration_is_real_hashed_customer_only_and_login_logout_round_trip(): void
    {
        $this->postJson('/api/auth/register', $this->registration(['role'=>'admin', 'active'=>false, 'email_verified_at'=>now()]))
            ->assertCreated()->assertExactJson(['ok'=>true]);
        $user=User::query()->sole();
        $this->assertSame(UserRole::Customer,$user->role);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->active);
        $this->assertNotSame('BuyerPassword2026',$user->password);
        $this->assertTrue(Hash::check('BuyerPassword2026',$user->password));
        $this->assertGuest('web');
        $this->getJson('/api/customer/session')->assertOk()->assertJsonPath('customer.email','buyer@example.test')->assertJsonMissingPath('customer.password');
        $this->postJson('/api/customer/logout')->assertOk();
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->postJson('/api/auth/login',['email'=>'BUYER@example.test','password'=>'BuyerPassword2026'])->assertOk();
        $this->getJson('/api/customer/session')->assertOk()->assertJsonPath('customer.name','Real Buyer');
        $this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
    }
    public function test_registration_cannot_claim_reserved_owner_emails_or_duplicate_accounts(): void
    {
        foreach(config('owner-access.google_admin_emails') as $email){
            $this->postJson('/api/auth/register',$this->registration(['email'=>strtoupper($email)]))->assertUnprocessable();
        }
        $this->assertDatabaseCount('users',0);
        $this->postJson('/api/auth/register',$this->registration())->assertCreated();
        $this->postJson('/api/auth/register',$this->registration(['email'=>'BUYER@example.test']))->assertUnprocessable();
        $this->assertDatabaseCount('users',1);
    }
    public function test_weak_and_mismatched_passwords_create_no_records(): void
    {
        $this->postJson('/api/auth/register',$this->registration(['password'=>'short','password_confirmation'=>'short']))->assertUnprocessable();
        $this->postJson('/api/auth/register',$this->registration(['password_confirmation'=>'different']))->assertUnprocessable();
        $this->assertDatabaseCount('users',0);
        $this->assertDatabaseCount('customers',0);
    }
    public function test_time_boxed_admin_alias_creates_or_resolves_real_admin_and_is_remembered(): void
    {
        config(['temporary-admin-access.aliases.testadmin'=>[
            'email'=>'amirmashti1378@gmail.com',
            'verifier_bcrypt'=>Hash::make('test-pass'),
            'expires_at'=>now()->addDay()->toIso8601String(),
        ]]);
        $response=$this->postJson('/api/auth/login',['identifier'=>'testadmin','password'=>'test-pass'])
            ->assertOk()->assertExactJson(['redirect'=>'/backend/admin']);
        $user=User::where('email','amirmashti1378@gmail.com')->firstOrFail();
        $this->assertTrue($user->isActiveAdmin());
        $this->assertNotNull(AdminAuthState::where('user_id',$user->id)->value('password_configured_at'));
        $response->assertCookie(Auth::guard('web')->getRecallerName());

        $this->postJson('/api/admin/logout')->assertOk();
        config(['temporary-admin-access.aliases.testadmin.expires_at'=>now()->subSecond()->toIso8601String()]);
        $this->postJson('/api/auth/login',['identifier'=>'testadmin','password'=>'test-pass'])->assertUnprocessable();
    }

    public function test_password_login_denies_wrong_missing_and_inactive_accounts_generically(): void
    {
        $user=User::factory()->create(['email'=>'buyer@example.test','password'=>'BuyerPassword2026']);
        Customer::create(['user_id'=>$user->id,'active'=>true]);
        foreach([['buyer@example.test','wrong'],['missing@example.test','wrong']] as [$email,$password]){
            $this->postJson('/api/auth/login',compact('email','password'))->assertUnprocessable()->assertJsonPath('message','Authentication failed.');
        }
        $user->update(['active'=>false]);
        $this->postJson('/api/auth/login',['email'=>'buyer@example.test','password'=>'BuyerPassword2026'])->assertUnprocessable();
        $this->assertNull(session(CustomerSession::KEY));
    }
    public function test_real_admin_password_login_uses_web_guard_and_clears_customer_identity(): void
    {
        $admin=User::factory()->admin()->create(['password'=>'AdminPassword2026','remember_token'=>null]);
        $response=$this->withSession([CustomerSession::KEY=>123])->postJson('/api/auth/login',['email'=>$admin->email,'password'=>'AdminPassword2026'])
            ->assertOk()->assertExactJson(['redirect'=>'/backend/admin']);
        $response->assertCookie(Auth::guard('web')->getRecallerName());
        $this->assertNotNull($admin->fresh()->remember_token);
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session(CustomerSession::KEY));
        $this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
        $this->get('/admin/profile')->assertRedirect('https://armaghantrading.com/#/admin');
    }
    public function test_password_login_rate_limit_is_account_bound_across_ips(): void
    {
        for($i=0;$i<5;$i++){
            $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.'.($i+1)])->postJson('/api/auth/login',['identifier'=>'limited@example.test','password'=>'wrong'])->assertUnprocessable();
        }
        $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.20'])->postJson('/api/auth/login',['identifier'=>'LIMITED@example.test','password'=>'wrong'])->assertStatus(429);
    }
    public function test_password_change_requires_own_current_password_or_fresh_google_proof(): void
    {
        $this->get('/account/security')->assertUnauthorized();
        $this->post('/account/password',[])->assertUnauthorized();
        $this->postJson('/api/auth/register',$this->registration())->assertCreated();
        $user=User::query()->sole();
        $data=['password'=>'NewBuyerPassword2026','password_confirmation'=>'NewBuyerPassword2026'];
        $this->postJson('/account/password',$data)->assertUnprocessable();
        $this->assertTrue(Hash::check('BuyerPassword2026',$user->fresh()->password));
        $this->post('/account/password',$data+['current_password'=>'BuyerPassword2026'])->assertRedirect('https://armaghantrading.com/backend/account/security');
        $this->assertTrue(Hash::check('NewBuyerPassword2026',$user->fresh()->password));
    }
    public function test_admin_may_choose_simple_eight_character_password_once_and_it_is_marked_configured(): void
    {
        $admin=User::factory()->admin()->create(['password'=>'GeneratedPassword2026']);
        $data=['password'=>'x','password_confirmation'=>'x'];
        $this->actingAs($admin)->withSession([
            'armaghan.password_setup_user_id'=>$admin->id,
            'armaghan.password_setup_until'=>now()->addMinutes(10)->timestamp,
        ])->post('/account/password',$data)->assertRedirect('https://armaghantrading.com/backend/account/security');
        $admin->refresh();
        $this->assertTrue(Hash::check('x',$admin->password));
        $this->assertNotNull(AdminAuthState::where('user_id',$admin->id)->value('password_configured_at'));
    }

    public function test_google_password_setup_is_owner_scoped_expiring_and_one_use(): void
    {
        $this->postJson('/api/auth/register',$this->registration())->assertCreated();
        $user=User::query()->sole();
        $data=['password'=>'FreshPassword2026','password_confirmation'=>'FreshPassword2026'];
        $this->withSession(['armaghan.password_setup_user_id'=>$user->id+1,'armaghan.password_setup_until'=>now()->addMinutes(10)->timestamp])->postJson('/account/password',$data)->assertUnprocessable();
        $this->withSession(['armaghan.password_setup_user_id'=>$user->id,'armaghan.password_setup_until'=>now()->subSecond()->timestamp])->postJson('/account/password',$data)->assertUnprocessable();
        $this->withSession(['armaghan.password_setup_user_id'=>$user->id,'armaghan.password_setup_until'=>now()->addMinutes(10)->timestamp])->post('/account/password',$data)->assertRedirect();
        $this->assertNull(session('armaghan.password_setup_user_id'));
        $this->assertTrue(Hash::check('FreshPassword2026',$user->fresh()->password));
        $this->postJson('/account/password',$data)->assertUnprocessable();
    }
}
