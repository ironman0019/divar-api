<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementStatusController;
use App\Http\Controllers\Admin\Advertisement\AdvertisementPromotionController;

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

});
