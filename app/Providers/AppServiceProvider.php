<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            $mobile = $request->input('mobile');

            if ($mobile) {
                // Limit by mobile
                return Limit::perMinutes(1, 3)->by($mobile);
            }

            // Fallback: limit by IP
            return Limit::perMinutes(1, 3)->by($request->ip());
        });

        // Paginator::useBootstrap();
    }
}
