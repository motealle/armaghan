<?php

namespace Tests\Feature;

use App\Enums\ProductAvailability;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_categories_return_only_active_taxonomy_with_managed_metadata(): void
    {
        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
            'name_en' => 'Baby',
            'sort_order' => 10,
        ]);

        Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
            'active' => true,
            'sort_order' => 10,
        ]);

        Subcategory::create([
            'category_id' => $category->id,
            'code' => '12',
            'name_fa' => 'پتوی نوزادی',
            'active' => false,
            'sort_order' => 20,
        ]);

        Category::create([
            'code' => '9',
            'name_fa' => 'دسته غیرفعال',
            'active' => false,
        ]);

        $response = $this->getJson('/api/catalog/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', '1')
            ->assertJsonPath('data.0.names.fa', 'نوزادی')
            ->assertJsonCount(1, 'data.0.subcategories')
            ->assertJsonPath('data.0.subcategories.0.code', '11');

        $this->assertContains('1', $response->json('catalog.managed_category_codes'));
        $this->assertContains('9', $response->json('catalog.managed_category_codes'));
        $this->assertContains('11', $response->json('catalog.managed_subcategory_codes'));
        $this->assertContains('12', $response->json('catalog.managed_subcategory_codes'));
    }

    public function test_public_products_are_paginated_filterable_and_exclude_inactive_taxonomy(): void
    {
        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
        ]);

        $hiddenSubcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '12',
            'name_fa' => 'پتوی نوزادی',
            'active' => false,
        ]);

        Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11001',
            'name_fa' => 'محصول موجود',
            'name_en' => 'Available product',
            'availability' => ProductAvailability::Available,
            'sort_order' => 10,
        ]);

        Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11002',
            'name_fa' => 'محصول سفارشی',
            'availability' => ProductAvailability::MadeToOrder,
            'sort_order' => 20,
        ]);

        Product::create([
            'subcategory_id' => $subcategory->id,
            'code' => '11003',
            'name_fa' => 'محصول غیرفعال',
            'active' => false,
        ]);

        Product::create([
            'subcategory_id' => $hiddenSubcategory->id,
            'code' => '12001',
            'name_fa' => 'محصول زیردسته غیرفعال',
            'active' => true,
        ]);

        $page = $this->getJson('/api/catalog/products?per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.code', '11001')
            ->assertJsonPath('data.0.category.code', '1')
            ->assertJsonPath('data.0.subcategory.code', '11');

        foreach (['11001', '11002', '11003', '12001'] as $code) {
            $this->assertContains($code, $page->json('catalog.managed_codes'));
        }

        $this->getJson('/api/catalog/products?availability=made_to_order&q=%D8%B3%D9%81%D8%A7%D8%B1%D8%B4%DB%8C')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', '11002')
            ->assertJsonPath('data.0.availability', 'made_to_order');

        $this->getJson('/api/catalog/products?subcategory=12')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_public_product_query_validation_is_bounded(): void
    {
        $this->getJson('/api/catalog/products?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);

        $this->getJson('/api/catalog/products?availability=unknown')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['availability']);
    }
}
