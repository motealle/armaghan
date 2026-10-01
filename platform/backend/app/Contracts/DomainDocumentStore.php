<?php

namespace App\Contracts;

use App\Persistence\DomainDocument;

interface DomainDocumentStore
{
    public function read(string $collection): DomainDocument;

    /**
     * @param  array<int, array<string, mixed>>  $records
     */
    public function replace(
        string $collection,
        array $records,
        ?int $expectedRevision = null,
    ): DomainDocument;
}
