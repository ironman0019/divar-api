<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementStatusController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementPromotionController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\Category\CategoryAttributeController;
use App\Http\Controllers\Admin\Category\CategoryValueController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('login', [AuthController::class, 'login'])->middleware('guest')->name('login');
Route::post('login/auth', [AuthController::class, 'auth'])->middleware('guest')->name('login.auth');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function() {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Menu Management Routes
    Route::resource('menus', MenuController::class);
    Route::patch('menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');

    // Settings Management Routes
    Route::get('settings', [SettingsController::class, 'general'])->name('settings.general');
    Route::put('settings', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');

    // Advertisement Management Routes
    Route::prefix('advertisements')->name('advertisements.')->group(function() {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::get('/{advertisement}', [AdvertisementController::class, 'show'])->name('show');
        Route::get('/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('edit');
        Route::put('/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
        Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])->name('destroy');
        
        // Status Management
        Route::get('/status/pending', [AdvertisementStatusController::class, 'pending'])->name('pending');
        Route::patch('/{advertisement}/approve', [AdvertisementStatusController::class, 'approve'])->name('approve');
        Route::patch('/{advertisement}/reject', [AdvertisementStatusController::class, 'reject'])->name('reject');
        Route::patch('/{advertisement}/toggle-status', [AdvertisementStatusController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{advertisement}/set-expired', [AdvertisementStatusController::class, 'setExpired'])->name('set-expired');
        Route::post('/bulk-approve', [AdvertisementStatusController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [AdvertisementStatusController::class, 'bulkReject'])->name('bulk-reject');
        
        // Promotion Management
        Route::get('/featured/list', [AdvertisementPromotionController::class, 'featured'])->name('featured');
        Route::get('/{advertisement}/promote', [AdvertisementPromotionController::class, 'promoteForm'])->name('promote-form');
        Route::post('/{advertisement}/promote', [AdvertisementPromotionController::class, 'promote'])->name('promote');
        Route::delete('/featured/{featured}', [AdvertisementPromotionController::class, 'removeFeatured'])->name('remove-featured');
        Route::patch('/featured/{featured}/toggle', [AdvertisementPromotionController::class, 'toggleFeaturedStatus'])->name('toggle-featured');
        Route::patch('/featured/{featured}/extend', [AdvertisementPromotionController::class, 'extend'])->name('extend-featured');
    });

    // Category Management Routes
    Route::prefix('categories')->name('categories.')->group(function() {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        // Category Attributes Routes (must come before {category} routes)
        Route::prefix('attributes')->name('attributes.')->group(function() {
            Route::get('/', [CategoryAttributeController::class, 'index'])->name('index');
            Route::get('/create', [CategoryAttributeController::class, 'create'])->name('create');
            Route::post('/', [CategoryAttributeController::class, 'store'])->name('store');
            Route::get('/{attribute}', [CategoryAttributeController::class, 'show'])->name('show');
            Route::get('/{attribute}/edit', [CategoryAttributeController::class, 'edit'])->name('edit');
            Route::put('/{attribute}', [CategoryAttributeController::class, 'update'])->name('update');
            Route::delete('/{attribute}', [CategoryAttributeController::class, 'destroy'])->name('destroy');
            Route::patch('/{attribute}/toggle-status', [CategoryAttributeController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Category Values Routes
        Route::prefix('values')->name('values.')->group(function() {
            Route::get('/', [CategoryValueController::class, 'index'])->name('index');
            Route::get('/create', [CategoryValueController::class, 'create'])->name('create');
            Route::post('/', [CategoryValueController::class, 'store'])->name('store');
            Route::get('/{value}', [CategoryValueController::class, 'show'])->name('show');
            Route::get('/{value}/edit', [CategoryValueController::class, 'edit'])->name('edit');
            Route::put('/{value}', [CategoryValueController::class, 'update'])->name('update');
            Route::delete('/{value}', [CategoryValueController::class, 'destroy'])->name('destroy');
            Route::patch('/{value}/toggle-status', [CategoryValueController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Category routes with {category} parameter (must come after specific routes)
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

});
