<?php

use App\Http\Controllers\Admin\CustomerMagicLinkController as AdminCustomerMagicLinkController;
use App\Http\Controllers\Admin\StyleProfileController as AdminStyleProfileController;
use App\Http\Controllers\CustomerMagicLinkController;
use App\Http\Controllers\CustomerSessionController;
use App\Http\Controllers\FavoriteShareController;
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
        Route::delete('/favorite-shares/{favoriteShare}', [FavoriteShareController::class, 'destroy'])
            ->middleware('throttle:30,1');
    });

Route::prefix('api/favorite-shares')->group(function (): void {
    Route::post('/', [FavoriteShareController::class, 'store'])
        ->middleware('throttle:15,1');
    Route::post('/resolve', [FavoriteShareController::class, 'resolve'])
        ->middleware('throttle:60,1');
});

// Public read-only media/catalog never needs a session or anti-forgery cookie.
// Keep route bindings; authenticated/mutating routes retain the complete web stack.
$publicReadWithoutSession = [
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
];

Route::get('/api/catalog/media/{media}/{variant}', [\App\Http\Controllers\PublicProductMediaController::class, 'show'])
    ->whereNumber('media')
    ->whereIn('variant', ['thumb', 'card', 'detail'])
    ->withoutMiddleware($publicReadWithoutSession)
    ->middleware('throttle:public-media')
    ->name('catalog.product-media');

Route::get('/api/home-media/file/{media}/{variant?}', [\App\Http\Controllers\Admin\HomeMediaController::class, 'publicFile'])->whereNumber('media')->withoutMiddleware($publicReadWithoutSession)->middleware('throttle:public-media')->name('home-media.file');
Route::get('/api/home-media/{channel}', [\App\Http\Controllers\Admin\HomeMediaController::class, 'publicIndex'])->where('channel','staging|production')->withoutMiddleware($publicReadWithoutSession)->middleware('throttle:public-catalog');

Route::get('/api/style-profile/{channel?}', [PublicStyleProfileController::class, 'show'])
    ->where('channel', 'staging|production');

Route::prefix('api/catalog')
    ->withoutMiddleware($publicReadWithoutSession)
    ->middleware('throttle:public-catalog')
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


Route::post('/api/auth/login', [\App\Http\Controllers\PasswordAuthController::class, 'login'])->middleware('throttle:password-login');
Route::post('/api/auth/register', [\App\Http\Controllers\PasswordAuthController::class, 'register'])->middleware('throttle:5,1');
Route::get('/account/security', [\App\Http\Controllers\PasswordAuthController::class, 'security'])->middleware('throttle:60,1');
Route::post('/account/password', [\App\Http\Controllers\PasswordAuthController::class, 'password'])->middleware('throttle:10,1');

Route::get('/api/auth/google/status', [\App\Http\Controllers\GoogleCustomerAuthController::class, 'status'])->middleware('throttle:60,1');
Route::get('/auth/google/redirect', [\App\Http\Controllers\GoogleCustomerAuthController::class, 'redirect'])->middleware('throttle:15,1')->name('auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleCustomerAuthController::class, 'callback'])->middleware('throttle:30,1')->name('auth.google.callback');


Route::prefix('api/admin')->middleware(['active.admin', 'throttle:60,1'])->group(function (): void {
    Route::get('/account-archives/{resource}', [\App\Http\Controllers\Admin\AccountArchiveController::class, 'index'])->whereIn('resource', ['users', 'customers']);
    Route::post('/account-archives/import', [\App\Http\Controllers\Admin\AccountArchiveController::class, 'import']);
    Route::post('/account-archives/{resource}/{id}/backup', [\App\Http\Controllers\Admin\AccountArchiveController::class, 'prepare'])->whereIn('resource', ['users', 'customers'])->whereNumber('id');
    Route::delete('/account-archives/{archive}', [\App\Http\Controllers\Admin\AccountArchiveController::class, 'destroy'])->whereUuid('archive');
    Route::post('/account-archives/{archive}/restore', [\App\Http\Controllers\Admin\AccountArchiveController::class, 'restore'])->whereUuid('archive');
    Route::get('/home-media', [\App\Http\Controllers\Admin\HomeMediaController::class, 'index']);
    Route::get('/home-media/file/{media}/{variant?}', [\App\Http\Controllers\Admin\HomeMediaController::class, 'adminFile'])->whereNumber('media')->name('admin.home-media.file');
    Route::post('/home-media', [\App\Http\Controllers\Admin\HomeMediaController::class, 'upload'])->middleware('throttle:admin-product-uploads');
    Route::post('/home-media/publish/{channel}', [\App\Http\Controllers\Admin\HomeMediaController::class, 'publish'])->where('channel','staging|production');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'index']);
    Route::post('/orders', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'store']);
    Route::put('/orders/{order}/quote', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'commercial']);
    Route::post('/orders/{order}/documents', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'uploadDocument'])->middleware('throttle:admin-product-uploads');
    Route::get('/orders/{order}/documents/{document}', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'downloadDocument'])->whereNumber('document');
    Route::patch('/orders/{order}', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'update']);
    Route::post('/bulk-tags/{resource}', [\App\Http\Controllers\Admin\BulkTagsController::class, 'update'])->whereIn('resource', ['products', 'customers', 'users']);
    Route::post('/bulk-status/{resource}', [\App\Http\Controllers\Admin\BulkStatusController::class, 'update'])->whereIn('resource', ['products', 'customers', 'users']);
    Route::post('/advanced-access', [\App\Http\Controllers\Admin\AdvancedAccessController::class, 'store']);
    Route::delete('/advanced-access', [\App\Http\Controllers\Admin\AdvancedAccessController::class, 'destroy']);
    Route::put('/product-taxonomy/{subcategory}/specifications', [\App\Http\Controllers\Admin\ProductController::class, 'saveSchema']);
    Route::get('/product-taxonomy', [\App\Http\Controllers\Admin\ProductController::class, 'taxonomy']);
    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index']);
    Route::post('/products', [\App\Http\Controllers\Admin\ProductController::class, 'store']);
    Route::get('/products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->whereNumber('product');
    Route::patch('/products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update']);
    Route::post('/products/{product}/images', [\App\Http\Controllers\Admin\ProductController::class, 'upload'])->middleware('throttle:admin-product-uploads');
    Route::put('/products/{product}/images/order', [\App\Http\Controllers\Admin\ProductController::class, 'order']);
    Route::delete('/products/{product}/images', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImages']);
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
    Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
    Route::get('/session', [\App\Http\Controllers\Admin\AdminSessionController::class, 'show']);
    Route::post('/logout', [\App\Http\Controllers\Admin\AdminSessionController::class, 'logout']);
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index']);
    Route::post('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'store']);
    Route::post('/customers/{customer}/account', [\App\Http\Controllers\Admin\UserController::class, 'storeForCustomer'])->middleware('throttle:10,1');
    Route::patch('/customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'update']);
});

Route::prefix('api/customer/orders')->middleware(['customer.session', 'throttle:30,1'])->group(function (): void {
    Route::get('/{order}/documents/{document}', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'downloadDocument'])->whereNumber('document');
    Route::get('/', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Admin\OrderTrackingController::class, 'store']);
});
