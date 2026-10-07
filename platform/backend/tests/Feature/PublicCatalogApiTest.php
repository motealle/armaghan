<?php

namespace Tests\Feature;

use App\Enums\ProductAvailability;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_cold_product_read_finds_the_uploaded_path_before_product_boot(): void
    {
        Storage::fake('public');
        $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media;
        $media->forceFill(['id' => 42, 'model_type' => Product::class, 'model_id' => 1,
            'collection_name' => Product::MEDIA_COLLECTION, 'disk' => 'public', 'file_name' => 'photo.jpg']);
        $this->assertSame(Storage::disk('public')->path('media/products/42/photo.jpg'), $media->getPath());
    }

    public function test_image_browsing_has_an_independent_limit_and_does_not_start_sessions(): void
    {
        // Exercise the real route middleware, without hundreds of HTTP requests.
        $limiter = \Illuminate\Support\Facades\RateLimiter::limiter('public-media');
        $request = \Illuminate\Http\Request::create('/api/catalog/media/1/thumb');
        $limit = $limiter($request);
        $this->assertSame(1200, $limit->maxAttempts);
        // Laravel prefixes a named limiter's keys internally.
        \Illuminate\Support\Facades\RateLimiter::hit(md5('public-media'.$limit->key), 60);
        $response = $this->getJson('/api/catalog/categories')->assertOk();
        $this->assertSame([], $response->baseResponse->headers->getCookies());
        $this->assertSame(119, (int) $response->headers->get('X-RateLimit-Remaining'));
        $this->assertDatabaseCount('sessions', 0);
        $this->get('/api/catalog/media/999999/thumb')->assertNotFound();
        $this->getJson('/api/admin/products')->assertUnauthorized();
    }

    public function test_missing_legacy_variants_are_repaired_without_serving_the_large_original(): void
    {
        Storage::fake('public');
        $category = Category::create(['code' => '1', 'name_fa' => 'Baby']);
        $subcategory = Subcategory::create(['category_id' => $category->id, 'code' => '11', 'name_fa' => 'Clothes']);
        $product = Product::create(['subcategory_id' => $subcategory->id, 'code' => '11098', 'name_fa' => 'Photo', 'availability' => ProductAvailability::Available]);
        $media = $product->addMedia(UploadedFile::fake()->image('landscape.jpg', 1920, 1080))->toMediaCollection(Product::MEDIA_COLLECTION);
        $originalHash = hash_file('sha256', $media->getPath());
        [$masterWidth, $masterHeight] = getimagesize($media->getPath());
        foreach (['thumb', 'card', 'detail'] as $variant) {
            @unlink($media->getPath($variant));
        }
        $media->forceFill(['generated_conversions' => []])->save();
        foreach (['thumb' => 320, 'card' => 800, 'detail' => 1600] as $variant => $width) {
            $response = $this->get('/api/catalog/media/'.$media->id.'/'.$variant)->assertOk()->assertHeader('Content-Type', 'image/webp');
            $path = $response->baseResponse->getFile()->getPathname();
            $expectedWidth = min($width, $masterWidth);
            $this->assertSame($expectedWidth, getimagesize($path)[0]);
            $this->assertSame((int) round($masterHeight * $expectedWidth / $masterWidth), getimagesize($path)[1]);
            $this->assertNotSame($media->getPath(), $path);
            $this->assertFalse($media->fresh()->hasGeneratedConversion($variant));
            $this->assertSame([], $response->baseResponse->headers->getCookies());
        }
        $this->assertSame($originalHash, hash_file('sha256', $media->getPath()));
        $this->get('/api/catalog/media/'.$media->id.'/original')->assertNotFound();
        $this->getJson('/api/catalog/products?q=11098')->assertJsonPath('data.0.media.0.width', $masterWidth)->assertJsonPath('data.0.media.0.height', $masterHeight);
    }

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

        $availableProduct = Product::create([
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

        Storage::fake('public');
        $availableProduct
            ->addMedia(UploadedFile::fake()->image('front.jpg', 640, 960))
            ->toMediaCollection(Product::MEDIA_COLLECTION);

        $media = $availableProduct->getFirstMedia(Product::MEDIA_COLLECTION);
        $this->assertNotNull($media);
        $this->assertTrue($media->hasGeneratedConversion('card'));
        $this->assertTrue($media->hasGeneratedConversion('thumb'));

        $page = $this->getJson('/api/catalog/products?per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.code', '11001')
            ->assertJsonPath('data.0.category.code', '1')
            ->assertJsonPath('data.0.subcategory.code', '11')
            ->assertJsonCount(1, 'data.0.media')
            ->assertJsonPath('data.0.media.0.id', (string) ($media->uuid ?: $media->id));

        $this->assertStringContainsString('/api/catalog/media/'.$media->id.'/card', $page->json('data.0.media.0.url'));
        $this->assertStringContainsString('/api/catalog/media/'.$media->id.'/thumb', $page->json('data.0.media.0.thumb_url'));
        $this->assertStringContainsString('/api/catalog/media/'.$media->id.'/detail', $page->json('data.0.media.0.detail_url'));

        foreach (['11001', '11002', '11003', '12001'] as $code) {
            $this->assertContains($code, $page->json('catalog.managed_codes'));
        }

        $this->getJson('/api/catalog/products?availability=made_to_order&q=%D8%B3%D9%81%D8%A7%D8%B1%D8%B4%DB%8C')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', '11002')
            ->assertJsonPath('data.0.availability', 'made_to_order');

        // Shared selections must return exactly the requested active products.
        $this->getJson('/api/catalog/products?codes[]=11002&codes[]=11003&per_page=100')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.code', '11002');
        $this->getJson('/api/catalog/products?codes[]=12001&per_page=100')
            ->assertOk()->assertJsonCount(0, 'data');

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
