<?php

namespace App\Persistence;

final readonly class DomainDocument
{
    /**
     * @param  array<int, array<string, mixed>>  $records
     */
    public function __construct(
        public int $schema,
        public string $collection,
        public int $revision,
        public string $updatedAt,
        public array $records,
        public string $checksum,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'schema' => $this->schema,
            'collection' => $this->collection,
            'revision' => $this->revision,
            'updated_at' => $this->updatedAt,
            'records' => $this->records,
            'checksum' => $this->checksum,
        ];
    }
}
