<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomAdminProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // The suite reuses isolated user IDs; do not carry throttle counters between test cases.
        \Illuminate\Support\Facades\Cache::flush();
    }

    private function fields(string $code = '11099'): array
    {
        $category = Category::firstOrCreate(['code' => '1'], ['name_fa' => 'نوزادی']);
        $sub = Subcategory::firstOrCreate(['code' => '11'], ['category_id' => $category->id, 'name_fa' => 'لباس']);
        return ['code' => $code, 'subcategory_id' => $sub->id, 'name_fa' => 'محصول واقعی', 'name_en' => 'Real product',
            'name_ar' => null, 'name_ku' => null, 'availability' => 'available', 'active' => true, 'sort_order' => 0];
    }

    public function test_specification_values_are_taxonomy_scoped_atomic_preserved_and_public(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $fields = $this->fields(); $product = Product::create($fields);
        $definition = \App\Models\SpecDefinition::create(['subcategory_id' => $product->subcategory_id,
            'key' => 'fabric', 'label_fa' => 'جنس', 'label_en' => 'Fabric', 'locked' => true]);
        $otherSub = Subcategory::create(['category_id' => $product->subcategory->category_id, 'code' => '12', 'name_fa' => 'دیگر']);
        $foreign = \App\Models\SpecDefinition::create(['subcategory_id' => $otherSub->id, 'key' => 'size', 'label_fa' => 'سایز']);
        $row = $this->row($product);
        $this->getJson('/api/admin/product-taxonomy')->assertJsonPath('subcategories.0.specifications.0.labels.en', 'Fabric');
        $url = '/api/admin/products/'.$product->id;
        foreach ([
            [['definition_id' => $foreign->id, 'value_text' => 'wrong']],
            [['definition_id' => $definition->id, 'value_text' => 'one'], ['definition_id' => $definition->id, 'value_text' => 'two']],
            [['definition_id' => $definition->id, 'value_text' => '<script>bad</script>']],
            [['definition_id' => $definition->id, 'value_text' => str_repeat('x', 1001)]],
            [['definition_id' => $definition->id, 'value_text' => 'valid'], ['definition_id' => $foreign->id, 'value_text' => 'invalid']],
        ] as $values) {
            $this->patchJson($url, array_merge($fields, ['name_fa' => 'must rollback', 'revision' => $row['revision'], 'specifications' => $values]))->assertUnprocessable();
            $this->assertSame($fields['name_fa'], $product->fresh()->name_fa);
            $this->assertDatabaseCount('product_spec_values', 0);
            $this->assertDatabaseCount('activity_log', 0);
        }
        $updated = $this->patchJson($url, $fields + ['revision' => $row['revision'], 'specifications' => [['definition_id' => $definition->id, 'value_text' => ' پنبه ']]])
            ->assertOk()->assertJsonPath('product.specifications.0.value_text', 'پنبه')->json('product');
        $this->patchJson($url, $fields + ['revision' => $row['revision']])->assertConflict();
        $this->getJson('/api/catalog/products')->assertJsonPath('data.0.specifications.0.value_text', 'پنبه')->assertJsonPath('data.0.specifications.0.locked', true);
        $preserved = $this->patchJson($url, $fields + ['revision' => $updated['revision']])->assertOk()->assertJsonPath('product.specifications.0.value_text', 'پنبه')->json('product');
        $this->patchJson($url, array_merge($fields, ['subcategory_id' => $otherSub->id, 'revision' => $preserved['revision']]))->assertUnprocessable();
        $definition->update(['locked' => false]);
        $this->patchJson($url, $fields + ['revision' => $preserved['revision']])->assertConflict();
        $fresh = $this->row($product);
        $this->patchJson($url, $fields + ['revision' => $fresh['revision'], 'specifications' => [['definition_id' => $definition->id, 'value_text' => null]]])->assertOk()->assertJsonPath('product.specifications.0.value_text', null);
        $this->assertStringNotContainsString('پنبه', ActivityLog::all()->toJson());
    }

    public function test_create_with_specifications_and_move_empty_product_use_selected_taxonomy(): void
    {
        $this->actingAs(User::factory()->admin()->create()); $fields = $this->fields();
        $definition = \App\Models\SpecDefinition::create(['subcategory_id' => $fields['subcategory_id'], 'key' => 'fabric', 'label_fa' => 'جنس']);
        $this->postJson('/api/admin/products', $fields + ['specifications' => [['definition_id' => $definition->id, 'value_text' => 'پنبه']]])->assertCreated()->assertJsonPath('product.specifications.0.value_text', 'پنبه');
        $empty = Product::create($this->fields('11098')); $row = $this->row($empty);
        $newSub = Subcategory::create(['category_id' => $empty->subcategory->category_id, 'code' => '12', 'name_fa' => 'دیگر']);
        $newDefinition = \App\Models\SpecDefinition::create(['subcategory_id' => $newSub->id, 'key' => 'size', 'label_fa' => 'سایز']);
        $this->patchJson('/api/admin/products/'.$empty->id, array_merge($this->fields('11098'), ['subcategory_id' => $newSub->id,
            'revision' => $row['revision'], 'specifications' => [['definition_id' => $newDefinition->id, 'value_text' => 'XL']]]))->assertOk()->assertJsonPath('product.specifications.0.value_text', 'XL');
    }

    public function test_schema_management_requires_admin_acknowledgement_revision_and_retains_values(): void
    {
        $fields = $this->fields(); $product = Product::create($fields);
        $url = '/api/admin/product-taxonomy/'.$product->subcategory_id.'/specifications';
        $this->putJson($url, [])->assertUnauthorized();
        $this->actingAs(User::factory()->create()); $this->putJson($url, [])->assertForbidden();
        $this->actingAs(User::factory()->admin()->create());
        $schema = $this->getJson('/api/admin/product-taxonomy')->json('subcategories.0');
        $definition = ['id' => null, 'key' => 'fabric', 'labels' => ['fa' => 'جنس', 'en' => 'Fabric', 'ar' => null, 'ku' => null], 'locked' => true];
        $body = ['revision' => $schema['schema_revision'], 'definitions' => [$definition]];
        $this->putJson($url, $body)->assertUnprocessable();
        $result = $this->putJson($url, $body + ['acknowledged' => true])->assertOk()->json('subcategories.0');
        $this->putJson($url, $body + ['acknowledged' => true])->assertConflict();
        $id = $result['specifications'][0]['id'];
        $product->specValues()->create(['spec_definition_id' => $id, 'value_text' => 'پنبه']);
        $row = $this->row($product);
        $definition['id'] = $id; $definition['locked'] = false; $definition['labels']['en'] = 'Material';
        foreach ([[], [$definition, $definition], [array_merge($definition, ['id' => 999])], [array_merge($definition, ['key' => 'renamed'])]] as $invalid) {
            $this->putJson($url, ['revision' => $result['schema_revision'], 'acknowledged' => true, 'definitions' => $invalid])->assertUnprocessable();
        }
        $this->putJson($url, ['revision' => $result['schema_revision'], 'acknowledged' => true, 'definitions' => [$definition]])->assertOk()->assertJsonPath('subcategories.0.specifications.0.locked', false);
        $this->assertDatabaseHas('product_spec_values', ['product_id' => $product->id, 'value_text' => 'پنبه']);
        $this->patchJson('/api/admin/products/'.$product->id, $fields + ['revision' => $row['revision']])->assertConflict();
        $this->getJson('/api/catalog/products')->assertJsonPath('data.0.specifications.0.labels.en', 'Material')->assertJsonPath('data.0.specifications.0.value_text', 'پنبه');
    }

    public function test_upload_budget_is_separate_from_admin_reads_and_remains_rate_limited(): void
    {
        $this->actingAs(User::factory()->admin()->create()); $product = Product::create($this->fields());
        for ($i = 0; $i < 15; $i++) $this->getJson('/api/admin/products')->assertOk();
        for ($i = 0; $i < 10; $i++) $this->postJson('/api/admin/products/'.$product->id.'/images', [])->assertUnprocessable();
        $this->postJson('/api/admin/products/'.$product->id.'/images', [])->assertTooManyRequests();
        $this->getJson('/api/admin/products')->assertOk();
    }

    private function row(Product $product): array
    {
        return $this->getJson('/api/admin/products?search='.$product->code)->assertOk()->json('products.0');
    }

    public function test_only_active_administrators_can_access_every_product_operation(): void
    {
        $product = Product::create($this->fields());
        foreach ([null, User::factory()->create(), User::factory()->admin()->inactive()->create()] as $actor) {
            if ($actor) $this->actingAs($actor);
            $status = $actor ? 403 : 401;
            $this->getJson('/api/admin/products')->assertStatus($status)->assertJsonMissing(['name_en' => 'Real product']);
            $this->getJson('/api/admin/product-taxonomy')->assertStatus($status);
            $this->postJson('/api/admin/products', $this->fields('11098'))->assertStatus($status);
            $this->patchJson('/api/admin/products/'.$product->id, [])->assertStatus($status);
            $this->postJson('/api/admin/products/'.$product->id.'/images', [])->assertStatus($status);
            $this->putJson('/api/admin/products/'.$product->id.'/images/order', [])->assertStatus($status);
        }
        $this->assertDatabaseCount('products', 1);
    }

    public function test_create_edit_reload_public_visibility_and_archive_preserve_records(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $fields = $this->fields();
        $created = $this->postJson('/api/admin/products', $fields)->assertCreated()->assertHeader('Cache-Control', 'no-store, private')->json('product');
        $this->assertSame('11', $created['subcategory_code']);
        $fields['name_en'] = 'Changed'; $fields['availability'] = 'made_to_order';
        $updated = $this->patchJson('/api/admin/products/'.$created['id'], $fields + ['revision' => $created['revision']])->assertOk()->json('product');
        $this->getJson('/api/admin/products?search=Changed')->assertOk()->assertJsonPath('products.0.name_en', 'Changed')->assertJsonPath('total', 1);
        $this->getJson('/api/catalog/products')->assertOk()->assertJsonPath('data.0.names.en', 'Changed');
        $fields['active'] = false;
        $this->patchJson('/api/admin/products/'.$created['id'], $fields + ['revision' => $updated['revision']])->assertOk();
        $this->getJson('/api/catalog/products')->assertOk()->assertJsonCount(0, 'data');
        $this->assertDatabaseCount('products', 1);
        $this->assertSame(3, ActivityLog::count());
        $this->assertStringNotContainsString('Changed', ActivityLog::all()->toJson());
    }

    public function test_stale_product_and_gallery_revisions_cannot_overwrite_same_second_changes(): void
    {
        $this->freezeTime(); Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());
        $fields = $this->fields(); $product = Product::create($fields); $row = $this->row($product);
        $fields['name_fa'] = 'جدید';
        $this->patchJson('/api/admin/products/'.$product->id, $fields + ['revision' => $row['revision']])->assertOk();
        $this->patchJson('/api/admin/products/'.$product->id, $fields + ['revision' => $row['revision']])->assertConflict();
        $this->postJson('/api/admin/products/'.$product->id.'/images', ['revision' => $row['revision'], 'image' => UploadedFile::fake()->image('x.jpg')])->assertConflict();
        $this->assertCount(0, $product->fresh()->getMedia(Product::MEDIA_COLLECTION));
    }

    public function test_validation_unique_code_taxonomy_and_bounded_queries(): void
    {
        $this->actingAs(User::factory()->admin()->create()); $fields = $this->fields(); Product::create($fields);
        $this->postJson('/api/admin/products', $fields)->assertUnprocessable()->assertJsonValidationErrors('code');
        $fields['code'] = '11098'; $fields['subcategory_id'] = 9999;
        $this->postJson('/api/admin/products', $fields)->assertUnprocessable()->assertJsonValidationErrors('subcategory_id');
        $fields = $this->fields('11097'); $fields['availability'] = 'unknown';
        $this->postJson('/api/admin/products', $fields)->assertUnprocessable()->assertJsonValidationErrors('availability');
        $this->getJson('/api/admin/products?per_page=999')->assertUnprocessable();
        $this->getJson('/api/admin/product-taxonomy')->assertOk()->assertJsonPath('subcategories.0.code', '11');
        for ($i = 0; $i < 25; $i++) Product::create($this->fields('110'.($i + 100)));
        $this->getJson('/api/admin/products')->assertOk()->assertJsonCount(25, 'products')->assertJsonPath('last_page', 2);
        $this->getJson('/api/admin/products?page=2')->assertJsonCount(1, 'products');
    }

    public function test_real_upload_safe_filename_metadata_removal_derivatives_and_public_gallery(): void
    {
        Storage::fake('public'); $this->actingAs(User::factory()->admin()->create());
        $product = Product::create($this->fields()); $row = $this->row($product);
        $source = UploadedFile::fake()->image('source.jpg', 24, 36);
        $file = new UploadedFile($source->getRealPath(), 'original-label.jpg', 'image/jpeg', null, true);
        $original = file_get_contents($file->getRealPath());
        // Embed a JPEG comment which must not survive re-encoding.
        $comment = 'private-metadata-marker';
        $offset = 4 + unpack('n', substr($original, 4, 2))[1];
        file_put_contents($file->getRealPath(), substr($original, 0, $offset)."\xff\xfe".pack('n', strlen($comment) + 2).$comment.substr($original, $offset));
        $this->assertSame('image/jpeg', $file->getMimeType());
        $response = $this->postJson('/api/admin/products/'.$product->id.'/images', ['revision' => $row['revision'], 'image' => $file])
            ->assertCreated()->assertJsonCount(1, 'product.media');
        $media = $product->fresh()->getFirstMedia(Product::MEDIA_COLLECTION);
        $this->assertNotNull($media); $this->assertStringEndsWith('.jpg', $media->file_name);
        $this->assertStringNotContainsString('original-label', $media->file_name);
        $this->assertStringNotContainsString($comment, file_get_contents($media->getPath()));
        $this->assertTrue($media->hasGeneratedConversion('card')); $this->assertTrue($media->hasGeneratedConversion('thumb')); $this->assertTrue($media->hasGeneratedConversion('detail'));
        $this->assertSame('webp', pathinfo($media->getPath('thumb'), PATHINFO_EXTENSION));
        $this->assertSame('webp', pathinfo($media->getPath('card'), PATHINFO_EXTENSION));
        $this->assertSame('webp', pathinfo($media->getPath('detail'), PATHINFO_EXTENSION));
        $this->assertSame(24, getimagesize($media->getPath('card'))[0]);
        $this->assertNotSame($row['revision'], $response->json('product.revision'));
        $this->getJson('/api/catalog/products')->assertJsonCount(1, 'data.0.media')->assertJsonPath('data.0.media.0.detail_url', fn ($value) => is_string($value) && str_contains($value, '-detail.webp'));
    }

    public function test_upload_rejects_disguised_corrupt_oversized_and_excess_gallery(): void
    {
        Storage::fake('public'); $this->actingAs(User::factory()->admin()->create());
        $product = Product::create($this->fields()); $row = $this->row($product);
        $source = UploadedFile::fake()->image('source.jpg', 10, 10);
        $riskyName = new UploadedFile($source->getRealPath(), 'unsafe.php', 'image/jpeg', null, true);
        foreach ([$riskyName, UploadedFile::fake()->createWithContent('bad.jpg', '<?php echo 1;'),
            UploadedFile::fake()->create('large.jpg', 8193, 'image/jpeg'), UploadedFile::fake()->image('wide.jpg', 5001, 1)] as $file) {
            $this->postJson('/api/admin/products/'.$product->id.'/images', ['revision' => $row['revision'], 'image' => $file])->assertUnprocessable();
        }
        $this->assertCount(0, $product->fresh()->getMedia(Product::MEDIA_COLLECTION));
        for ($i = 0; $i < 6; $i++) $product->addMedia(UploadedFile::fake()->image('x.jpg', 10, 10))->toMediaCollection(Product::MEDIA_COLLECTION);
        $row = $this->row($product);
        $this->postJson('/api/admin/products/'.$product->id.'/images', ['revision' => $row['revision'], 'image' => UploadedFile::fake()->image('extra.jpg')])->assertUnprocessable();
        $this->assertCount(6, $product->fresh()->getMedia(Product::MEDIA_COLLECTION));
    }

    public function test_reorder_is_exact_owner_scoped_and_visible_in_public_api(): void
    {
        Storage::fake('public'); $this->actingAs(User::factory()->admin()->create());
        $product = Product::create($this->fields()); $other = Product::create($this->fields('11098'));
        $a = $product->addMedia(UploadedFile::fake()->image('a.jpg', 10, 10))->toMediaCollection(Product::MEDIA_COLLECTION);
        $b = $product->addMedia(UploadedFile::fake()->image('b.jpg', 10, 10))->toMediaCollection(Product::MEDIA_COLLECTION);
        $foreign = $other->addMedia(UploadedFile::fake()->image('c.jpg', 10, 10))->toMediaCollection(Product::MEDIA_COLLECTION);
        $foreignOrder = $foreign->order_column;
        $row = $this->row($product); $url = '/api/admin/products/'.$product->id.'/images/order';
        foreach ([[$a->id], [$a->id, $a->id], [$foreign->id, $b->id]] as $ids) {
            $this->putJson($url, ['revision' => $row['revision'], 'media_ids' => $ids])->assertUnprocessable();
        }
        $this->putJson($url, ['revision' => $row['revision'], 'media_ids' => [$b->id, $a->id]])->assertOk()->assertJsonPath('product.media.0.id', $b->id);
        $this->putJson($url, ['revision' => $row['revision'], 'media_ids' => [$a->id, $b->id]])->assertConflict();
        $this->getJson('/api/catalog/products?q=11099')->assertOk()->assertJsonPath('data.0.media.0.id', (string) $b->uuid);
        $this->assertSame($foreignOrder, $foreign->fresh()->order_column);
    }

    public function test_failed_processing_removes_only_new_files_and_rolls_back_audit(): void
    {
        Storage::fake('public'); $this->actingAs(User::factory()->admin()->create());
        $product = Product::create($this->fields());
        $existing = $product->addMedia(UploadedFile::fake()->image('keep.jpg', 10, 10))->toMediaCollection(Product::MEDIA_COLLECTION);
        $before = Storage::disk('public')->allFiles(); $row = $this->row($product);
        \Illuminate\Support\Facades\Event::listen(\Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent::class, function (): void {
            throw new \RuntimeException('Simulated processing failure');
        });
        $this->postJson('/api/admin/products/'.$product->id.'/images', ['revision' => $row['revision'], 'image' => UploadedFile::fake()->image('fail.jpg', 10, 10)])->assertStatus(500);
        $this->assertSame($before, Storage::disk('public')->allFiles());
        $this->assertCount(1, $product->fresh()->getMedia(Product::MEDIA_COLLECTION));
        $this->assertDatabaseHas('media', ['id' => $existing->id]);
        $this->assertDatabaseCount('activity_log', 0);
    }
}
