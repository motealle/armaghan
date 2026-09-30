<?php

namespace Tests\Feature;

use App\Enums\ProductAvailability;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FavoriteShare;
use App\Models\MagicLink;
use App\Models\Product;
use App\Models\ProductSpecValue;
use App\Models\SpecDefinition;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DomainFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_tables_and_relationships_are_available(): void
    {
        foreach ([
            'customers',
            'categories',
            'subcategories',
            'products',
            'spec_definitions',
            'product_spec_values',
            'favorite_shares',
            'favorite_share_product',
            'magic_links',
            'activity_log',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }

        $user = User::factory()->create();
        $customer = Customer::create([
            'user_id' => $user->id,
            'name' => 'Demo Buyer Contact',
            'email' => 'buyer@example.test',
            'country_code' => 'IQ',
            'whatsapp' => '+9647000000000',
            'company_name' => 'Demo Buyer',
            'priority' => 3,
        ]);

        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
            'sort_order' => 10,
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
        ]);

        $product = Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11001',
            'name_fa' => 'ست نوزادی',
            'availability' => ProductAvailability::MadeToOrder,
        ]);

        $definition = SpecDefinition::create([
            'subcategory_id' => $subcategory->id,
            'key' => 'material',
            'label_fa' => 'جنس',
            'locked' => false,
        ]);

        ProductSpecValue::create([
            'product_id' => $product->id,
            'spec_definition_id' => $definition->id,
            'value_text' => 'پنبه',
        ]);

        $share = FavoriteShare::create([
            'customer_id' => $customer->id,
            'token_hash' => hash('sha256', 'share-token'),
        ]);
        $share->products()->attach($product->id, ['sort_order' => 1]);

        $magic = MagicLink::create([
            'customer_id' => $customer->id,
            'token_hash' => hash('sha256', 'magic-token'),
            'expires_at' => now()->addHour(),
        ]);

        $this->assertSame($user->id, $customer->user->id);
        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertSame(ProductAvailability::MadeToOrder, $product->fresh()->availability);
        $this->assertSame('1', $product->subcategory->category->code);
        $this->assertSame('material', $product->specValues->first()->definition->key);
        $this->assertSame('11001', $share->products->first()->code);
        $this->assertTrue($magic->fresh()->enabled);
        $this->assertNotNull($magic->expires_at);
    }
}
