<?php

namespace Tests\Feature;

use App\Contracts\DomainDocumentStore;
use App\Persistence\Json\JsonDomainDocumentStore;
use Tests\TestCase;

class DomainStoreBindingTest extends TestCase
{
    public function test_json_is_the_default_domain_store_driver(): void
    {
        $this->assertSame('json', config('armaghan.domain_store.driver'));
        $this->assertInstanceOf(
            JsonDomainDocumentStore::class,
            app(DomainDocumentStore::class),
        );
    }
}
