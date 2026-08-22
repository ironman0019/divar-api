<?php

namespace App\Providers;

use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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

        View::composer(['admin.layouts.partials.sidebar', 'admin.layouts.partials.header'], function ($view) {
            $user = auth()->user();
            $view->with('user', $user);
        });

        View::composer('admin.layouts.partials.header', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $notificationService = app(AdminNotificationService::class);
            $notifications = $notificationService->getNotifications(auth()->id());

            $view->with([
                'adminNotifications' => $notifications,
                'adminUnreadCount' => $notifications->where('read', false)->count(),
                'adminPendingAdsCount' => $notificationService->pendingAdvertisementsCount(),
            ]);
        });

        // Paginator::useBootstrap();
    }
}
