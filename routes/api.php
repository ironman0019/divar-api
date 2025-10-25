<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\ProfileController;
use App\Http\Controllers\V1\Auth\OTPLoginController;
use App\Http\Controllers\V1\CityController;

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


    // authenticated routes
    Route::middleware('auth:sanctum')->group(function() {

        //

    });

});
