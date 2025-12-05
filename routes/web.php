<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandUserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionFormController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Brand Management - Admin Only
    Route::middleware('admin')->group(function () {
        Route::resource('brands', BrandController::class)->except(['show']);
    });

    // Brand Access - For All Users
    Route::get('/brands/user', [BrandController::class, 'userBrands'])->name('brands.user');
    Route::post('/brands/{brand}/switch', [BrandController::class, 'switch'])->name('brands.switch');
    
    // Brand-specific routes - Require brand access
    Route::middleware('brand.access')->prefix('brands/{brand}')->name('brands.')->group(function () {
        Route::get('/', [BrandController::class, 'show'])->name('show');
        
        // Brand Users Management
        Route::get('/users', [BrandUserController::class, 'index'])->name('users.index');
        Route::post('/users', [BrandUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [BrandUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [BrandUserController::class, 'destroy'])->name('users.destroy');
        
        // API Keys Management
        Route::resource('api-keys', ApiKeyController::class)->except(['show']);
        Route::post('/api-keys/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate');
        
        // Email Lists Management
        Route::resource('lists', ListController::class);
        
        // Subscriber Management (nested under lists)
        Route::prefix('lists/{list}')->name('lists.')->group(function () {
            // Import Management
            Route::get('/imports', [\App\Http\Controllers\ImportController::class, 'index'])->name('imports.index');
            Route::get('/imports/{import}', [\App\Http\Controllers\ImportController::class, 'show'])->name('imports.show');
            
            // API endpoint for import status polling
            Route::get('/imports/status', [\App\Http\Controllers\Api\ImportStatusController::class, 'index'])
                ->name('imports.status');
            
            // Define specific routes BEFORE resource routes to avoid conflicts
            Route::get('/subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
            Route::post('/subscribers/import', [SubscriberController::class, 'processImport'])->name('subscribers.process-import');
            Route::get('/subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
            Route::post('/subscribers/bulk-delete', [SubscriberController::class, 'bulkDelete'])->name('subscribers.bulk-delete');
            
            // Resource routes
            Route::resource('subscribers', SubscriberController::class);
            
            // Custom Fields Management
            Route::resource('custom-fields', CustomFieldController::class);
            Route::post('/custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');
            
            // Subscription Forms Management
            Route::resource('subscription-forms', SubscriptionFormController::class)->names('subscription-forms');
        });
        
        // Campaign Management
        Route::resource('campaigns', CampaignController::class);
        Route::post('/campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
        Route::post('/campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
        Route::post('/campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
        Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
    });
});

// Public subscription management routes (no auth required)
Route::get('/confirm/{token}', [App\Http\Controllers\SubscriptionController::class, 'confirm'])->name('subscription.confirm');
Route::get('/unsubscribe/{token}', [App\Http\Controllers\SubscriptionController::class, 'showUnsubscribe'])->name('subscription.unsubscribe');

// Public subscription form routes
Route::prefix('forms')->name('forms.')->group(function () {
    Route::get('/{identifier}', [App\Http\Controllers\PublicFormController::class, 'show'])->name('show');
    Route::post('/{identifier}', [App\Http\Controllers\PublicFormController::class, 'submit'])->name('submit');
    Route::get('/{identifier}/embed.js', [App\Http\Controllers\PublicFormController::class, 'embedJs'])->name('embed-js');
    Route::get('/confirm/{token}', [App\Http\Controllers\PublicFormController::class, 'confirm'])->name('confirm');
});
Route::post('/unsubscribe/{token}', [App\Http\Controllers\SubscriptionController::class, 'unsubscribe'])->name('subscription.do-unsubscribe');

require __DIR__.'/auth.php';
