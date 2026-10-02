<?php

use App\Http\Controllers\Admin\CustomerMagicLinkController as AdminCustomerMagicLinkController;
use App\Http\Controllers\Admin\StyleProfileController as AdminStyleProfileController;
use App\Http\Controllers\CustomerMagicLinkController;
use App\Http\Controllers\CustomerSessionController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\PublicStyleProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/csrf-token', function () {
    return response()->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store');
});

Route::post('/api/customer/magic-link/consume', [CustomerMagicLinkController::class, 'consume'])
    ->middleware('throttle:20,1')
    ->name('customer.magic.consume');

Route::prefix('api/customer')
    ->middleware(['customer.session', 'throttle:120,1'])
    ->group(function (): void {
        Route::get('/session', [CustomerSessionController::class, 'show']);
        Route::patch('/session', [CustomerSessionController::class, 'update'])
            ->middleware('throttle:30,1');
        Route::post('/logout', [CustomerSessionController::class, 'logout'])
            ->middleware('throttle:30,1');
    });

Route::get('/api/style-profile/{channel?}', [PublicStyleProfileController::class, 'show'])
    ->where('channel', 'staging|production');

Route::prefix('api/catalog')
    ->middleware('throttle:120,1')
    ->group(function (): void {
        Route::get('/categories', [PublicCatalogController::class, 'categories']);
        Route::get('/products', [PublicCatalogController::class, 'products']);
    });

Route::prefix('api/admin/style-profile')
    ->middleware('active.admin')
    ->group(function (): void {
        Route::get('/', [AdminStyleProfileController::class, 'show']);
        Route::put('/draft', [AdminStyleProfileController::class, 'updateDraft']);
        Route::post('/publish/{channel}', [AdminStyleProfileController::class, 'publish'])
            ->where('channel', 'staging|production');
        Route::post('/versions/{styleProfileVersion}/restore/{channel}', [AdminStyleProfileController::class, 'restore'])
            ->where('channel', 'staging|production');
    });


Route::prefix('api/admin/customers')
    ->middleware(['active.admin', 'throttle:60,1'])
    ->group(function (): void {
        Route::post('/{customer}/magic-link', [AdminCustomerMagicLinkController::class, 'store']);
        Route::delete('/{customer}/magic-link', [AdminCustomerMagicLinkController::class, 'destroy']);
    });
