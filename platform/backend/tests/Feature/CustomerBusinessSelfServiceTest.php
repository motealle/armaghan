<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\CustomerBusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBusinessSelfServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_customer_can_edit_all_own_business_details_without_admin_flags(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Buyer A',
            'email' => 'buyer-a@example.test',
            'password' => 'SecureBuyer2026',
            'password_confirmation' => 'SecureBuyer2026',
        ])->assertCreated();

        $own = Customer::query()->sole();
        $otherUser = User::factory()->create(['role' => UserRole::Customer]);
        $other = Customer::create(['user_id' => $otherUser->id, 'active' => true]);
        $other->businessProfile()->create(['contact_name' => 'Other Buyer', 'city' => 'Basra']);

        $fields = [
            'company_name' => 'Baghdad Wholesale',
            'whatsapp' => '+9647700000000',
            'contact_name' => 'Buyer A',
            'phone' => '+9647800000000',
            'contact_email' => 'shop@example.test',
            'preferred_language' => 'ar',
            'city' => 'Baghdad',
            'district' => 'Karrada',
            'shop_number' => '31',
            'sales_product_group' => 'Kids',
            'purchase_volume' => '500 / month',
            'cooperation_type' => 'Wholesale',
            'sales_type' => 'Retail',
            'pinned' => true,
            'priority' => 255,
            'user_id' => $otherUser->id,
        ];
        $this->patchJson('/api/customer/session', $fields)->assertOk()
            ->assertJsonPath('customer.city', 'Baghdad')
            ->assertJsonPath('customer.sales_type', 'Retail')
            ->assertJsonPath('customer.company_name', 'Baghdad Wholesale')
            ->assertJsonMissingPath('customer.pinned')
            ->assertJsonMissingPath('customer.user_id');

        $this->assertSame($own->id, Customer::query()->where('company_name', 'Baghdad Wholesale')->value('id'));
        $this->assertDatabaseHas('customer_business_profiles', [
            'customer_id' => $own->id, 'city' => 'Baghdad', 'shop_number' => '31',
            'cooperation_type' => 'Wholesale', 'pinned' => false,
        ]);
        $this->assertDatabaseHas('customer_business_profiles', [
            'customer_id' => $other->id, 'contact_name' => 'Other Buyer', 'city' => 'Basra',
        ]);
        $this->assertSame(0, (int) $own->fresh()->priority);
        $this->assertSame($own->user_id, $own->fresh()->user_id);

        $this->getJson('/api/customer/session')->assertOk()
            ->assertJsonPath('customer.purchase_volume', '500 / month');
    }

    public function test_guest_cannot_save_or_read_business_profile(): void
    {
        $this->getJson('/api/customer/session')->assertUnauthorized();
        $this->patchJson('/api/customer/session', ['city' => 'Baghdad'])->assertUnauthorized();
    }
}
