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
        $this->postJson('/api/auth/register', $this->form(['password'=>'short123','password_confirmation'=>'short123']))->assertUnprocessable();
        $this->assertDatabaseCount('users',0);

        $this->postJson('/api/auth/register', $this->form())->assertCreated();
        $this->postJson('/api/auth/register', $this->form(['username'=>'BAGHDADSHOP', 'phone'=>'+9647809876543']))->assertUnprocessable();
        $this->postJson('/api/auth/register', $this->form(['username'=>'shopother', 'phone'=>'009647701234567']))->assertUnprocessable();
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
        $this->postJson('/api/auth/register',[
            'name'=>'Spoofer','email'=>'phone-guess@accounts.armaghan.invalid',
            'password'=>'EmailBuyer2026','password_confirmation'=>'EmailBuyer2026',
        ])->assertUnprocessable();
        $this->assertDatabaseCount('customer_login_identities',0);
    }
}
