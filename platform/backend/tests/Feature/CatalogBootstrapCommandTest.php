<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogBootstrapCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_bootstrap_is_dry_run_by_default(): void
    {
        $this->artisan('armaghan:bootstrap-catalog')
            ->expectsOutputToContain('Dry run PASS')
            ->assertSuccessful();

        $this->assertDatabaseCount('categories', 0);
        $this->assertDatabaseCount('subcategories', 0);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_catalog_bootstrap_imports_canonical_mvp_catalog_once(): void
    {
        $this->artisan('armaghan:bootstrap-catalog', ['--apply' => true])
            ->expectsOutputToContain('Catalog bootstrap PASS')
            ->assertSuccessful();

        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('subcategories', 6);
        $this->assertDatabaseCount('products', 18);

        $this->assertDatabaseHas('categories', [
            'code' => '3',
            'name_fa' => 'زنانه',
            'active' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'code' => '22001',
            'availability' => 'unavailable',
            'active' => true,
        ]);

        $this->artisan('armaghan:bootstrap-catalog', ['--apply' => true])
            ->expectsOutputToContain('Catalog bootstrap refused')
            ->assertFailed();

        $this->assertDatabaseCount('products', 18);
    }

    public function test_catalog_bootstrap_refuses_partially_populated_catalog(): void
    {
        Category::create([
            'code' => '1',
            'name_fa' => 'Existing',
        ]);

        $this->artisan('armaghan:bootstrap-catalog', ['--apply' => true])
            ->expectsOutputToContain('Catalog bootstrap refused')
            ->assertFailed();

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('subcategories', 0);
        $this->assertDatabaseCount('products', 0);
    }
}
