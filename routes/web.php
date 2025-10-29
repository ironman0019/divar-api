<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;

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

});
