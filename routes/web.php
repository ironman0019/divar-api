<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->middleware([''])->name('admin.')->group(function() {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');


});
