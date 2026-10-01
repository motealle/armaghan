<?php

use App\Http\Controllers\Admin\StyleProfileController as AdminStyleProfileController;
use App\Http\Controllers\PublicStyleProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/csrf-token', function () {
    return response()->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store');
});

Route::get('/api/style-profile/{channel?}', [PublicStyleProfileController::class, 'show'])
    ->where('channel', 'staging|production');

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
