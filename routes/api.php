<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\ProfileController;
use App\Http\Controllers\V1\Auth\OTPLoginController;
use App\Http\Controllers\V1\CityController;
use App\Http\Controllers\V1\AdvertisementController;
use App\Http\Controllers\V1\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::prefix('V1')->group(function() {

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register/send-otp', [AuthController::class, 'registerSendOtp'])->name('registerSendOtp')->middleware(['guest', 'throttle:otp']);
        Route::post('register/verify-otp', [AuthController::class, 'registerVerifyOtp'])->name('registerVerifyOtp')->middleware('guest');
        Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('guest');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
        Route::post('reset-password/send-otp', [AuthController::class, 'resetPasswordSendOtp'])->name('resetPasswordSendOtp')->middleware(['guest', 'throttle:otp']);
        Route::post('reset-password/verify-otp', [AuthController::class, 'resetPasswordVerifyOtp'])->name('resetPasswordVerifyOtp')->middleware('guest');
        Route::post('send-otp', [OTPLoginController::class, 'sendOtp'])->name('send-otp')->middleware(['guest', 'throttle:otp']);
        Route::post('verify-otp', [OTPLoginController::class, 'verifyOtp'])->name('verify-otp')->middleware('guest');
        Route::post('check-cooldown', [OTPLoginController::class, 'checkCooldown'])->name('check-cooldown');

        // Profile routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('profile', [ProfileController::class, 'getProfile'])->name('profile.get');
            Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        });

    });

    // public routes
    Route::get('cities', [CityController::class, 'index'])->name('city.index.public');

    // Advertisement routes (public)
    Route::prefix('advertisements')->name('advertisements.')->group(function () {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::get('/search', [AdvertisementController::class, 'search'])->name('search');
        Route::get('/filters', [AdvertisementController::class, 'filters'])->name('filters');
        Route::get('/category/{categoryId}', [AdvertisementController::class, 'category'])->name('category');
        Route::get('/{slug}', [AdvertisementController::class, 'show'])->name('show');
    });

    // Category routes (public)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/hierarchy', [CategoryController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{id}/attributes', [CategoryController::class, 'attributes'])->name('attributes');
        Route::get('/{parentId}/children', [CategoryController::class, 'children'])->name('children');
    });


    // authenticated routes
    Route::middleware('auth:sanctum')->group(function() {

        //

    });

});
