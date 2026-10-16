<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\CustomerLoginIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerPhoneRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function form(array $replace = []): array
    {
        return array_merge([
            'username' => 'BaghdadShop',
            'phone' => '+964 770 123 4567',
            'password' => 'Shop2026secure',
            'password_confirmation' => 'Shop2026secure',
        ], $replace);
    }

    public function test_register_login_logout_with_username_or_phone_without_sms(): void
    {
        $this->postJson('/api/auth/register', $this->form([
            'role' => 'admin',
            'active' => false,
            'email_verified_at' => now(),
        ]))->assertCreated()->assertExactJson(['ok'=>true]);

        $user = User::query()->sole();
        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertTrue($user->active);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Shop2026secure', $user->password));
        $this->assertStringEndsWith('@accounts.armaghan.invalid', $user->email);
        $this->assertDatabaseHas('customer_login_identities', [
            'user_id' => $user->id, 'username' => 'baghdadshop', 'phone_e164' => '+9647701234567',
        ]);
        $this->getJson('/api/customer/session')->assertOk()
            ->assertJsonPath('customer.name', 'baghdadshop')
            ->assertJsonPath('customer.phone', '+9647701234567')
            ->assertJsonPath('customer.email', null)
            ->assertJsonMissingPath('customer.password');
        $this->postJson('/api/customer/logout')->assertOk();
        $this->getJson('/api/customer/session')->assertUnauthorized();

        $this->postJson('/api/auth/login', ['identifier'=>'BAGHDADSHOP','password'=>'Shop2026secure'])->assertOk();
        $this->postJson('/api/customer/logout')->assertOk();
        $this->postJson('/api/auth/login', ['identifier'=>'٠٠٩٦٤ ٧٧٠ ١٢٣ ٤٥٦٧','password'=>'Shop2026secure'])->assertOk();
        $this->getJson('/api/customer/session')->assertOk()->assertJsonPath('customer.name','baghdadshop');
        $this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
    }

    public function test_duplicates_reserved_names_and_bad_phones_do_not_create_accounts(): void
    {
        $this->postJson('/api/auth/register', $this->form(['username'=>'mot']))->assertUnprocessable();
        $this->postJson('/api/auth/register', $this->form(['phone'=>'07701234567']))->assertUnprocessable();
        $this->postJson('/api/auth/register', $this->form(['password'=>'abc123','password_confirmation'=>'abc123']))->assertUnprocessable();
        $this->assertDatabaseCount('users',0);

        $this->postJson('/api/auth/register', $this->form())->assertCreated();
        $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.101'])->postJson('/api/auth/register', $this->form(['username'=>'BAGHDADSHOP', 'phone'=>'+9647809876543']))->assertUnprocessable();
        $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.102'])->postJson('/api/auth/register', $this->form(['username'=>'shopother', 'phone'=>'009647701234567']))->assertUnprocessable();
        $this->assertDatabaseCount('users',1);
        $this->assertDatabaseCount('customer_login_identities',1);
        $this->assertDatabaseCount('customers',1);
    }

    public function test_wrong_password_inactive_customer_and_admin_identity_are_not_compromised(): void
    {
        $this->postJson('/api/auth/register', $this->form())->assertCreated();
        $user = User::query()->sole();
        $this->postJson('/api/customer/logout')->assertOk();
        $this->postJson('/api/auth/login',['identifier'=>'baghdadshop','password'=>'badpassword'])->assertUnprocessable();
        $this->postJson('/api/auth/login',['identifier'=>'+9647701234567','password'=>'badpassword'])->assertUnprocessable();
        $user->update(['active'=>false]);
        $this->postJson('/api/auth/login',['identifier'=>'baghdadshop','password'=>'Shop2026secure'])->assertUnprocessable();
        $this->assertNull(session(\App\Support\CustomerSession::KEY));

        $this->postJson('/api/auth/register', $this->form(['username'=>'admin','phone'=>'+989121234567']))->assertUnprocessable();
        $this->assertSame(UserRole::Customer,$user->fresh()->role);
    }

    public function test_existing_email_registration_and_login_still_work_and_reserved_email_cannot_be_claimed(): void
    {
        $this->postJson('/api/auth/register',[
            'name'=>'Email Buyer','email'=>'emailbuyer@example.test',
            'password'=>'EmailBuyer2026','password_confirmation'=>'EmailBuyer2026',
        ])->assertCreated();
        $this->postJson('/api/customer/logout')->assertOk();
        $this->postJson('/api/auth/login',['identifier'=>'emailbuyer@example.test','password'=>'EmailBuyer2026'])->assertOk();
        $this->postJson('/api/customer/logout')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.110'])->postJson('/api/auth/register',[
            'name'=>'Spoofer','email'=>'phone-guess@accounts.armaghan.invalid',
            'password'=>'EmailBuyer2026','password_confirmation'=>'EmailBuyer2026',
        ])->assertUnprocessable();
        $this->assertDatabaseCount('customer_login_identities',0);
    }
    public function test_customer_pin_supports_local_digits_and_rejects_predictable_codes(): void
    {
        $this->postJson('/api/auth/register', $this->form([
            'password'=>'۵۸۳۹۲۱', 'password_confirmation'=>'٥٨٣٩٢١',
        ]))->assertCreated();
        $this->assertTrue(Hash::check('583921', User::query()->sole()->password));
        $this->postJson('/api/customer/logout')->assertOk();
        foreach (['baghdadshop', '+9647701234567'] as $identifier) {
            $this->postJson('/api/auth/login', ['identifier'=>$identifier, 'password'=>'٥٨٣٩٢١'])->assertOk();
            $this->postJson('/api/customer/logout')->assertOk();
        }
        foreach (['123456', '000000', '121212'] as $index=>$pin) {
            $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.'.(150+$index)])
                ->postJson('/api/auth/register', $this->form([
                    'username'=>'otherbuyer', 'phone'=>'+9647809876543',
                    'password'=>$pin, 'password_confirmation'=>$pin,
                ]))->assertUnprocessable();
        }
        $this->assertDatabaseCount('users', 1);
    }

    public function test_failed_attempt_budget_is_shared_across_aliases_and_expires(): void
    {
        $this->postJson('/api/auth/register', $this->form([
            'password'=>'583921', 'password_confirmation'=>'583921',
        ]))->assertCreated();
        $this->postJson('/api/customer/logout')->assertOk();
        for ($i=0; $i<10; $i++) {
            $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.'.(170+$i)])
                ->postJson('/api/auth/login', [
                    'identifier'=>$i%2 ? 'baghdadshop' : '+9647701234567',
                    'password'=>'wrong',
                ])->assertUnprocessable();
            $this->travel(13)->seconds();
        }
        $this->postJson('/api/auth/login', ['identifier'=>'baghdadshop', 'password'=>'583921'])->assertUnprocessable();
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->travel(901)->seconds();
        $this->postJson('/api/auth/login', ['identifier'=>'+9647701234567', 'password'=>'583921'])->assertOk();
    }

    public function test_successful_login_clears_failure_budget_and_keeps_legacy_password_bytes(): void
    {
        $this->postJson('/api/auth/register', $this->form())->assertCreated();
        $user=User::query()->sole();
        $user->update(['password'=>'۵۸۳۹۲۱']);
        $this->postJson('/api/customer/logout')->assertOk();
        $key='customer-login-account:'.$user->id;
        \Illuminate\Support\Facades\RateLimiter::hit($key, 900);
        $this->postJson('/api/auth/login', ['identifier'=>'baghdadshop', 'password'=>'۵۸۳۹۲۱'])->assertOk();
        $this->assertSame(0, \Illuminate\Support\Facades\RateLimiter::attempts($key));
    }

    public function test_per_identifier_minute_limit_still_applies_to_pin_accounts(): void
    {
        $this->postJson('/api/auth/register', $this->form())->assertCreated();
        $this->postJson('/api/customer/logout')->assertOk();
        for ($i=0; $i<5; $i++) {
            $this->postJson('/api/auth/login', ['identifier'=>'baghdadshop', 'password'=>'wrong'])->assertUnprocessable();
        }
        $this->postJson('/api/auth/login', ['identifier'=>'baghdadshop', 'password'=>'Shop2026secure'])->assertStatus(429);
    }

}
