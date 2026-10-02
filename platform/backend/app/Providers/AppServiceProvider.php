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
        Event::listen(MediaHasBeenAddedEvent::class, SanitizeProductMedia::class);
        RateLimiter::for('password-login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));
            return [Limit::perMinute(30)->by('login-ip:'.$request->ip()),
                Limit::perMinute(5)->by('login-account:'.hash('sha256', $email))];
        });
    }
}
