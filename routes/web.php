<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandDashboardController;
use App\Http\Controllers\ListController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('brands.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Brand management routes (admin only)
    Route::resource('brands', BrandController::class);
    
    // Brand dashboard
    Route::get('/brands/{brand}/dashboard', [BrandDashboardController::class, 'index'])->name('brands.dashboard');
    
    // User management routes within brand context
    Route::resource('brands.users', UserController::class)->except(['show']);
    
    // List management routes within brand context
    Route::resource('brands.lists', ListController::class)->except(['show', 'create']);
    
    // Select brand route
    Route::post('/select-brand/{brand}', function ($brandId) {
        session(['selected_brand_id' => $brandId]);
        return back();
    })->name('select-brand');

    // Settings routes (admin only)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
