<?php

namespace App\Providers;

use App\Contracts\DomainDocumentStore;
use App\Persistence\Json\JsonDomainDocumentStore;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use LogicException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DomainDocumentStore::class, function ($app): DomainDocumentStore {
            $driver = config('armaghan.domain_store.driver');

            if ($driver !== 'json') {
                throw new LogicException("Unsupported Armaghan domain-store driver [{$driver}].");
            }

            return new JsonDomainDocumentStore(
                files: $app->make(Filesystem::class),
                rootPath: (string) config('armaghan.domain_store.json.path'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
