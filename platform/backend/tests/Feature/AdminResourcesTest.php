<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_open_core_resource_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        foreach ([
            '/admin/categories',
            '/admin/subcategories',
            '/admin/products',
            '/admin/customers',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_customer_user_cannot_open_admin_resources(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/admin/products')
            ->assertForbidden();
    }
}
