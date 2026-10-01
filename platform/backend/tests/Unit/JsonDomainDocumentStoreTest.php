<?php

namespace Tests\Unit;

use App\Exceptions\DomainStoreConflict;
use App\Persistence\Json\JsonDomainDocumentStore;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class JsonDomainDocumentStoreTest extends TestCase
{
    private Filesystem $files;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'armaghan-json-store-'.Str::uuid();
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_missing_collection_reads_as_empty_revision_zero(): void
    {
        $document = $this->store()->read('products');

        $this->assertSame(1, $document->schema);
        $this->assertSame('products', $document->collection);
        $this->assertSame(0, $document->revision);
        $this->assertSame([], $document->records);
        $this->assertSame(64, strlen($document->checksum));
    }

    public function test_replace_writes_unicode_json_atomically_and_increments_revision(): void
    {
        $store = $this->store();

        $first = $store->replace('products', [
            ['code' => '11001', 'name_fa' => 'ست نوزادی'],
        ], expectedRevision: 0);

        $this->assertSame(1, $first->revision);
        $this->assertSame('ست نوزادی', $first->records[0]['name_fa']);

        $raw = $this->files->get($this->root.DIRECTORY_SEPARATOR.'products.json');

        $this->assertStringContainsString('ست نوزادی', $raw);
        $this->assertStringNotContainsString('\\u0633', $raw);

        $second = $store->replace('products', [
            ['code' => '11001', 'name_fa' => 'ست نوزادی'],
            ['code' => '11002', 'name_fa' => 'لباس کودک'],
        ], expectedRevision: 1);

        $this->assertSame(2, $second->revision);
        $this->assertCount(2, $store->read('products')->records);
    }

    public function test_stale_revision_is_rejected_without_overwriting_newer_file(): void
    {
        $store = $this->store();

        $store->replace('customers', [
            ['id' => 1, 'company_name' => 'Buyer A'],
        ], expectedRevision: 0);

        $store->replace('customers', [
            ['id' => 1, 'company_name' => 'Buyer B'],
        ], expectedRevision: 1);

        try {
            $store->replace('customers', [
                ['id' => 1, 'company_name' => 'Stale write'],
            ], expectedRevision: 1);

            $this->fail('Expected a domain-store conflict.');
        } catch (DomainStoreConflict) {
            $document = $store->read('customers');

            $this->assertSame(2, $document->revision);
            $this->assertSame('Buyer B', $document->records[0]['company_name']);
        }
    }

    public function test_invalid_collection_names_and_corrupt_checksums_are_rejected(): void
    {
        $store = $this->store();

        $this->expectException(InvalidArgumentException::class);
        $store->read('../products');
    }

    public function test_tampered_document_fails_checksum_validation(): void
    {
        $store = $this->store();
        $store->replace('products', [
            ['code' => '11001'],
        ], expectedRevision: 0);

        $path = $this->root.DIRECTORY_SEPARATOR.'products.json';
        $decoded = json_decode($this->files->get($path), true, flags: JSON_THROW_ON_ERROR);
        $decoded['records'][0]['code'] = 'tampered';
        $this->files->put($path, json_encode($decoded, JSON_THROW_ON_ERROR));

        $this->expectException(RuntimeException::class);
        $store->read('products');
    }

    private function store(): JsonDomainDocumentStore
    {
        return new JsonDomainDocumentStore($this->files, $this->root);
    }
}
