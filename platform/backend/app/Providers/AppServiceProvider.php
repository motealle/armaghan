<?php

namespace App\Providers;

use App\Listeners\SanitizeProductMedia;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Standalone media requests resolve paths before StyleProfile is instantiated.
        // Register at application startup so cold public/admin file reads use the upload path.
        \Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory::setCustomPathGenerators(
            \App\Models\StyleProfile::class, \App\Media\HomePathGenerator::class);
        Event::listen(MediaHasBeenAddedEvent::class, SanitizeProductMedia::class);
        RateLimiter::for('admin-product-uploads', fn (Request $request) =>
            Limit::perMinute(30)->by('product-upload:'.($request->user()?->id ?? $request->ip())));
        RateLimiter::for('password-login', function (Request $request) {
            $identifier = strtolower(trim((string) ($request->input('identifier') ?? $request->input('email') ?? '')));
            return [Limit::perMinute(30)->by('login-ip:'.$request->ip()),
                Limit::perMinute(5)->by('login-account:'.hash('sha256', $identifier))];
        });
    }
}
