<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandUserController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriberController;
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
            Route::resource('subscribers', SubscriberController::class);
            Route::get('/subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
            Route::post('/subscribers/import', [SubscriberController::class, 'processImport'])->name('subscribers.process-import');
            Route::get('/subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
            
            // Custom Fields Management
            Route::resource('custom-fields', CustomFieldController::class);
            Route::post('/custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');
        });
    });
});

require __DIR__.'/auth.php';
