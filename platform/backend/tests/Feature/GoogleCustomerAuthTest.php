<?php
namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\CustomerGoogleIdentity;
use App\Models\User;
use App\Services\GoogleCustomerIdentityService;
use App\Support\CustomerSession;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class GoogleCustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    private function configureGoogle(): void
    {
        config(['services.google.enabled'=>true, 'services.google.client_id'=>'test-client',
            'services.google.client_secret'=>'test-secret',
            'services.google.redirect'=>'https://armaghantrading.com/backend/auth/google/callback']);
    }
    private function identity(string $subject='subject-1', string $email='buyer@example.test', bool $verified=true): GoogleUser
    {
        return (new GoogleUser())->setRaw(['email_verified'=>$verified])
            ->map(['id'=>$subject, 'email'=>$email, 'name'=>'Buyer']);
    }
    public function test_unconfigured_provider_is_truthful_and_exposes_no_credentials(): void
    {
        config(['services.google.enabled'=>false]);
        $this->getJson('/api/auth/google/status')->assertOk()->assertExactJson(['enabled'=>false]);
        $this->get('/auth/google/redirect')->assertStatus(503);
        $this->assertDatabaseCount('customer_google_identities',0);
    }
    public function test_redirect_requires_allowed_return_path_and_stateful_provider(): void
    {
        $this->configureGoogle();
        $this->get('/auth/google/redirect?return_path=https://evil.example/')->assertStatus(422);
        $provider=Mockery::mock();
        $provider->shouldReceive('scopes')->once()->with(['openid','email','profile'])->andReturnSelf();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/test'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->get('/auth/google/redirect?return_path=%2Ft%2F29%2F')->assertRedirect('https://accounts.google.com/test');
        $this->assertSame('/t/29/',session('armaghan.google.return_path'));
    }
    public function test_signup_and_repeat_login_use_stable_google_subject(): void
    {
        $service=app(GoogleCustomerIdentityService::class);
        $first=$service->resolve($this->identity());
        $second=$service->resolve($this->identity('subject-1','changed@example.test'));
        $this->assertSame($first->id,$second->id);
        $this->assertSame(UserRole::Customer,$first->user->role);
        $this->assertDatabaseCount('users',1);
        $this->assertDatabaseCount('customers',1);
        $this->assertDatabaseCount('customer_google_identities',1);
    }
    public function test_existing_email_is_not_automatically_linked(): void
    {
        User::factory()->create(['email'=>'buyer@example.test']);
        $this->expectException(DomainException::class);
        app(GoogleCustomerIdentityService::class)->resolve($this->identity());
    }
    public function test_admin_email_cannot_become_customer_login(): void
    {
        User::factory()->admin()->create(['email'=>'buyer@example.test']);
        try {
            app(GoogleCustomerIdentityService::class)->resolve($this->identity());
            $this->fail('Admin email was accepted');
        } catch (DomainException) {
            $this->assertDatabaseCount('customer_google_identities',0);
            $this->assertDatabaseCount('customers',0);
        }
    }
    public function test_authenticated_customer_may_link_their_matching_account(): void
    {
        $user=User::factory()->create(['email'=>'buyer@example.test']);
        $customer=Customer::create(['user_id'=>$user->id,'active'=>true]);
        $result=app(GoogleCustomerIdentityService::class)->resolve($this->identity(),$customer);
        $this->assertSame($customer->id,$result->id);
        $this->assertDatabaseCount('customers',1);
    }
    public function test_unverified_google_email_never_creates_records(): void
    {
        try {
            app(GoogleCustomerIdentityService::class)->resolve($this->identity(verified:false));
            $this->fail('Unverified email was accepted');
        } catch (DomainException) {
            $this->assertDatabaseCount('users',0);
            $this->assertDatabaseCount('customer_google_identities',0);
        }
    }
    public function test_inactive_customer_cannot_log_in_via_linked_identity(): void
    {
        $service=app(GoogleCustomerIdentityService::class);
        $customer=$service->resolve($this->identity());
        $customer->update(['active'=>false]);
        $this->expectException(DomainException::class);
        $service->resolve($this->identity());
    }
    public function test_callback_preserves_admin_auth_and_starts_separate_customer_session(): void
    {
        $this->configureGoogle();
        $admin=User::factory()->admin()->create();
        $provider=Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn($this->identity());
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->actingAs($admin)->withSession(['armaghan.google.return_path'=>'/t/29/'])
            ->get('/auth/google/callback?code=test')->assertRedirect('https://armaghantrading.com/t/29/#/tracking');
        $this->assertAuthenticatedAs($admin);
        $this->assertSame(Customer::query()->sole()->id,session(CustomerSession::KEY));
    }
    public function test_invalid_state_fails_without_customer_session(): void
    {
        $this->configureGoogle();
        $provider=Mockery::mock();
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->get('/auth/google/callback?code=test')->assertRedirect('https://armaghantrading.com/#/tracking?auth_error=google&auth_reason=expired');
        $this->assertNull(session(CustomerSession::KEY));
        $this->assertDatabaseCount('customers',0);
    }
    public function test_cancel_discards_state_and_blocks_external_return_path(): void
    {
        $this->configureGoogle();
        $this->withSession(['state'=>'old','armaghan.google.return_path'=>'https://evil.example/'])
            ->get('/auth/google/callback?error=access_denied')->assertRedirect('https://armaghantrading.com/#/tracking?auth_error=google');
        $this->assertNull(session('state'));
        $this->assertNull(session(CustomerSession::KEY));
    }

    public function test_success_returns_to_frontend_root_when_backend_url_has_subdirectory(): void
    {
        $this->configureGoogle();
        URL::forceRootUrl('https://armaghantrading.com/backend');
        try {
            $provider=Mockery::mock();
            $provider->shouldReceive('user')->once()->andReturn($this->identity());
            Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
            $this->get('http://localhost/auth/google/callback?code=test')
                ->assertRedirect('https://armaghantrading.com/#/tracking');
            $this->assertSame(Customer::query()->sole()->id,session(CustomerSession::KEY));
        } finally {
            URL::forceRootUrl(null);
        }
    }

    public function test_cancel_returns_to_allowed_frontend_paths_when_backend_url_has_subdirectory(): void
    {
        $this->configureGoogle();
        URL::forceRootUrl('https://armaghantrading.com/backend');
        try {
            foreach (['/' => '/', '/t/29/' => '/t/29/', '//evil.example/' => '/'] as $input => $expected) {
                $this->withSession(['armaghan.google.return_path'=>$input])
                    ->get('http://localhost/auth/google/callback?error=access_denied')
                    ->assertRedirect('https://armaghantrading.com'.$expected.'#/tracking?auth_error=google');
                $this->assertNull(session(CustomerSession::KEY));
            }
        } finally {
            URL::forceRootUrl(null);
        }
    }
    public function test_customer_session_exposes_only_own_readonly_identity(): void
    {
        $this->configureGoogle();
        $provider=Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn($this->identity());
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->get('/auth/google/callback?code=test')->assertRedirect('https://armaghantrading.com/#/tracking');
        $this->getJson('/api/customer/session')->assertOk()
            ->assertJsonPath('customer.name','Buyer')
            ->assertJsonPath('customer.email','buyer@example.test')
            ->assertJsonMissingPath('customer.user_id')
            ->assertJsonMissingPath('customer.notes')
            ->assertJsonMissingPath('customer.role');
        $this->patchJson('/api/customer/session',['name'=>'Forged','email'=>'other@example.test','company_name'=>'Shop'])
            ->assertOk()->assertJsonPath('customer.name','Buyer')
            ->assertJsonPath('customer.email','buyer@example.test');
        $this->assertSame('buyer@example.test',Customer::query()->sole()->user->email);
    }

    public function test_only_verified_owner_google_identities_provision_admin_and_preserve_password_on_repeat(): void
    {
        $this->configureGoogle();
        foreach (config('owner-access.google_admin_emails') as $index => $email) {
            $provider=Mockery::mock();
            $provider->shouldReceive('user')->once()->andReturn($this->identity('owner-'.$index,$email));
            Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
            $this->get('/auth/google/callback?code=test')->assertRedirect('https://armaghantrading.com/backend/account/security');
            $user=User::where('email',$email)->firstOrFail();
            $this->assertTrue($user->isActiveAdmin());
            $this->assertNotNull($user->email_verified_at);
            $this->assertAuthenticatedAs($user);
            $this->assertNull(session(CustomerSession::KEY));
            $this->get('/account/security')->assertOk()->assertSee('ورود به مدیریت');
        }
        $this->assertDatabaseCount('customers',0);
        $service=app(\App\Services\GoogleAdminIdentityService::class);
        $user=User::where('email','motealle@gmail.com')->firstOrFail();
        $hash=$user->password;
        $this->assertSame($user->id,$service->resolve($this->identity('owner-0','motealle@gmail.com'))->id);
        $this->assertSame($hash,$user->fresh()->password);
    }
    public function test_unverified_owner_and_disabled_owner_cannot_be_elevated(): void
    {
        $service=app(\App\Services\GoogleAdminIdentityService::class);
        try { $service->resolve($this->identity('owner','motealle@gmail.com',false)); $this->fail('Unverified owner accepted'); }
        catch (DomainException) { $this->assertDatabaseCount('users',0); }
        $user=User::factory()->create(['email'=>'motealle@gmail.com','active'=>false]);
        try { $service->resolve($this->identity('owner','motealle@gmail.com')); $this->fail('Disabled owner accepted'); }
        catch (DomainException) { $this->assertFalse($user->fresh()->isActiveAdmin()); }
        $this->assertNull($service->resolve($this->identity('other','other@gmail.com')));
    }
    public function test_owner_promotion_revokes_prior_customer_session_and_old_password(): void
    {
        $user=User::factory()->create(['email'=>'motealle@gmail.com','password'=>'OldCustomerPassword2026']);
        $customer=Customer::create(['user_id'=>$user->id,'active'=>true]);
        app(\App\Services\GoogleAdminIdentityService::class)->resolve($this->identity('owner','motealle@gmail.com'));
        $this->assertTrue($user->fresh()->isActiveAdmin());
        $this->assertFalse($customer->fresh()->active);
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check('OldCustomerPassword2026',$user->fresh()->password));
        $this->withSession([CustomerSession::KEY=>$customer->id])->getJson('/api/customer/session')->assertUnauthorized();
    }

}
