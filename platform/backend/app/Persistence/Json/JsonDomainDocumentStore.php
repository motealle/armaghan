<?php

namespace App\Persistence\Json;

use App\Contracts\DomainDocumentStore;
use App\Exceptions\DomainStoreConflict;
use App\Persistence\DomainDocument;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use RuntimeException;

final class JsonDomainDocumentStore implements DomainDocumentStore
{
    public const SCHEMA_VERSION = 1;

    public function __construct(
        private readonly Filesystem $files,
        private readonly string $rootPath,
    ) {}

    public function read(string $collection): DomainDocument
    {
        $collection = $this->validatedCollection($collection);
        $path = $this->dataPath($collection);

        if (! $this->files->exists($path)) {
            return $this->emptyDocument($collection);
        }

        $decoded = json_decode(
            $this->files->get($path),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new RuntimeException("JSON domain collection [{$collection}] is not an object.");
        }

        return $this->hydrate($collection, $decoded);
    }

    public function replace(
        string $collection,
        array $records,
        ?int $expectedRevision = null,
    ): DomainDocument {
        $collection = $this->validatedCollection($collection);
        $this->assertRecords($records);
        $this->ensureDirectories();

        $lock = fopen($this->lockPath($collection), 'c+');

        if ($lock === false) {
            throw new RuntimeException("Unable to open lock for JSON domain collection [{$collection}].");
        }

        try {
            if (! flock($lock, LOCK_EX)) {
                throw new RuntimeException("Unable to lock JSON domain collection [{$collection}].");
            }

            $current = $this->read($collection);

            if ($expectedRevision !== null && $expectedRevision !== $current->revision) {
                throw new DomainStoreConflict(sprintf(
                    'JSON domain collection [%s] changed from revision %d to %d.',
                    $collection,
                    $expectedRevision,
                    $current->revision,
                ));
            }

            $updatedAt = now()->toISOString();
            $revision = $current->revision + 1;
            $checksum = $this->checksum(
                collection: $collection,
                revision: $revision,
                records: $records,
            );

            $document = new DomainDocument(
                schema: self::SCHEMA_VERSION,
                collection: $collection,
                revision: $revision,
                updatedAt: $updatedAt,
                records: array_values($records),
                checksum: $checksum,
            );

            $json = json_encode(
                $document->toArray(),
                JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR,
            );

            $this->files->replace($this->dataPath($collection), $json.PHP_EOL);

            return $document;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function emptyDocument(string $collection): DomainDocument
    {
        return new DomainDocument(
            schema: self::SCHEMA_VERSION,
            collection: $collection,
            revision: 0,
            updatedAt: '',
            records: [],
            checksum: $this->checksum($collection, 0, []),
        );
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    private function hydrate(string $collection, array $decoded): DomainDocument
    {
        $schema = $decoded['schema'] ?? null;
        $storedCollection = $decoded['collection'] ?? null;
        $revision = $decoded['revision'] ?? null;
        $updatedAt = $decoded['updated_at'] ?? null;
        $records = $decoded['records'] ?? null;
        $checksum = $decoded['checksum'] ?? null;

        if ($schema !== self::SCHEMA_VERSION) {
            throw new RuntimeException("Unsupported JSON domain schema for [{$collection}].");
        }

        if ($storedCollection !== $collection) {
            throw new RuntimeException("JSON domain collection identity mismatch for [{$collection}].");
        }

        if (! is_int($revision) || $revision < 0) {
            throw new RuntimeException("Invalid JSON domain revision for [{$collection}].");
        }

        if (! is_string($updatedAt) || ! is_array($records) || ! is_string($checksum)) {
            throw new RuntimeException("Malformed JSON domain document for [{$collection}].");
        }

        $this->assertRecords($records);

        $expectedChecksum = $this->checksum($collection, $revision, $records);

        if (! hash_equals($expectedChecksum, $checksum)) {
            throw new RuntimeException("JSON domain checksum mismatch for [{$collection}].");
        }

        return new DomainDocument(
            schema: $schema,
            collection: $collection,
            revision: $revision,
            updatedAt: $updatedAt,
            records: array_values($records),
            checksum: $checksum,
        );
    }

    /**
     * @param  array<int, mixed>  $records
     */
    private function assertRecords(array $records): void
    {
        foreach ($records as $index => $record) {
            if (! is_array($record)) {
                throw new InvalidArgumentException("JSON domain record [{$index}] must be an object-like array.");
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     */
    private function checksum(string $collection, int $revision, array $records): string
    {
        return hash('sha256', json_encode([
            'schema' => self::SCHEMA_VERSION,
            'collection' => $collection,
            'revision' => $revision,
            'records' => array_values($records),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    private function validatedCollection(string $collection): string
    {
        if (! preg_match('/^[a-z0-9][a-z0-9._-]{0,63}$/', $collection)) {
            throw new InvalidArgumentException('Invalid JSON domain collection name.');
        }

        return $collection;
    }

    private function ensureDirectories(): void
    {
        $this->files->ensureDirectoryExists($this->rootPath);
        $this->files->ensureDirectoryExists($this->rootPath.DIRECTORY_SEPARATOR.'.locks');
    }

    private function dataPath(string $collection): string
    {
        return $this->rootPath.DIRECTORY_SEPARATOR.$collection.'.json';
    }

    private function lockPath(string $collection): string
    {
        return $this->rootPath.DIRECTORY_SEPARATOR.'.locks'.DIRECTORY_SEPARATOR.$collection.'.lock';
    }
}
