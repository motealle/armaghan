<?php

namespace Tests\Feature;

use App\Enums\ProductAvailability;
use App\Enums\UserRole;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentCommerceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_open_product_and_customer_resource_pages(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'active' => true,
        ]);

        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
        ]);

        $product = Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11001',
            'name_fa' => 'نام داخلی تست',
            'availability' => ProductAvailability::Available,
        ]);

        $customer = Customer::create([
            'company_name' => 'خریدار تست',
            'whatsapp' => '+9647000000000',
            'country_code' => 'IQ',
            'priority' => 2,
        ]);

        $this->actingAs($admin);

        $this->get(ProductResource::getUrl('index'))->assertOk();
        $this->get(ProductResource::getUrl('create'))->assertOk();
        $this->get(ProductResource::getUrl('edit', ['record' => $product]))->assertOk();

        $this->get(CustomerResource::getUrl('index'))->assertOk();
        $this->get(CustomerResource::getUrl('create'))->assertOk();
        $this->get(CustomerResource::getUrl('edit', ['record' => $customer]))->assertOk();
    }

    public function test_non_admin_cannot_open_commerce_resources(): void
    {
        $customerUser = User::factory()->create([
            'role' => UserRole::Customer,
            'active' => true,
        ]);

        $this->actingAs($customerUser)
            ->get(ProductResource::getUrl('index'))
            ->assertForbidden();

        $this->get(CustomerResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_commerce_resources_do_not_expose_destructive_delete_or_user_link_fields(): void
    {
        $productTable = file_get_contents(app_path('Filament/Resources/Products/Tables/ProductsTable.php'));
        $productForm = file_get_contents(app_path('Filament/Resources/Products/Schemas/ProductForm.php'));
        $customerTable = file_get_contents(app_path('Filament/Resources/Customers/Tables/CustomersTable.php'));
        $customerForm = file_get_contents(app_path('Filament/Resources/Customers/Schemas/CustomerForm.php'));

        $this->assertStringContainsString('SpatieMediaLibraryFileUpload', $productForm);
        $this->assertStringContainsString("collection(\\App\\Models\\Product::MEDIA_COLLECTION)", $productForm);
        $this->assertStringContainsString('maxFiles(6)', $productForm);
        $this->assertStringContainsString('reorderable()', $productForm);
        $this->assertStringContainsString('SpatieMediaLibraryImageColumn', $productTable);

        foreach ([$productTable, $customerTable] as $table) {
            $this->assertStringNotContainsString('DeleteAction', $table);
            $this->assertStringNotContainsString('DeleteBulkAction', $table);
        }

        $this->assertStringNotContainsString("make('user_id')", $customerForm);
        $this->assertStringContainsString("Action::make('magic_link')", $customerTable);
        $this->assertStringContainsString("Action::make('revoke_magic_link')", $customerTable);
        $this->assertStringContainsString('CustomerMagicLinkService::class', $customerTable);
    }
}
