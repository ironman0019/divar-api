<?php

namespace App\Services\Admin;

use App\Models\AdminNotificationRead;
use App\Models\Advertisement\Advertisement;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminNotificationService
{
    public function pendingAdvertisementsCount(): int
    {
        return Advertisement::where('status', 3)->count();
    }

    public function getNotifications(int $adminUserId, int $limit = 15): Collection
    {
        $readKeys = AdminNotificationRead::where('user_id', $adminUserId)
            ->pluck('notification_key')
            ->flip();

        $notifications = collect()
            ->merge($this->pendingAdvertisementNotifications())
            ->merge($this->newUserNotifications())
            ->merge($this->recentPaymentNotifications())
            ->merge($this->failedPaymentNotifications())
            ->sortByDesc(fn (array $notification) => $notification['created_at']->timestamp)
            ->take($limit)
            ->map(function (array $notification) use ($readKeys) {
                $notification['read'] = $readKeys->has($notification['id']);

                return $notification;
            })
            ->values();

        return $notifications;
    }

    public function unreadCount(int $adminUserId): int
    {
        return $this->getNotifications($adminUserId)->where('read', false)->count();
    }

    public function markAsRead(int $adminUserId, string $notificationKey): void
    {
        AdminNotificationRead::updateOrCreate(
            [
                'user_id' => $adminUserId,
                'notification_key' => $notificationKey,
            ],
            ['read_at' => now()]
        );
    }

    public function markAllAsRead(int $adminUserId): void
    {
        $now = now();

        $this->getNotifications($adminUserId, 50)
            ->where('read', false)
            ->each(function (array $notification) use ($adminUserId, $now) {
                AdminNotificationRead::updateOrCreate(
                    [
                        'user_id' => $adminUserId,
                        'notification_key' => $notification['id'],
                    ],
                    ['read_at' => $now]
                );
            });
    }

    private function pendingAdvertisementNotifications(): Collection
    {
        return Advertisement::with('user')
            ->where('status', 3)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (Advertisement $advertisement) {
                return [
                    'id' => 'pending_ad_' . $advertisement->id,
                    'type' => 'pending_ad',
                    'icon' => 'fa-clock',
                    'icon_class' => 'text-yellow-400 bg-yellow-500/20',
                    'title' => 'آگهی در انتظار تایید',
                    'message' => 'آگهی «' . Str::limit($advertisement->title, 40) . '» منتظر بررسی است',
                    'url' => route('admin.advertisements.show', $advertisement),
                    'created_at' => $advertisement->created_at ?? now(),
                ];
            });
    }

    private function newUserNotifications(): Collection
    {
        return User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => 'new_user_' . $user->id,
                    'type' => 'new_user',
                    'icon' => 'fa-user-plus',
                    'icon_class' => 'text-green-400 bg-green-500/20',
                    'title' => 'کاربر جدید',
                    'message' => ($user->name ?? $user->mobile) . ' به سیستم پیوست',
                    'url' => route('admin.users.show', $user),
                    'created_at' => $user->created_at ?? now(),
                ];
            });
    }

    private function recentPaymentNotifications(): Collection
    {
        return Payment::with(['user', 'advertisement'])
            ->where('status', Payment::STATUS_PAID)
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function (Payment $payment) {
                return [
                    'id' => 'payment_paid_' . $payment->id,
                    'type' => 'payment_paid',
                    'icon' => 'fa-coins',
                    'icon_class' => 'text-yellow-primary bg-yellow-primary/20',
                    'title' => 'پرداخت موفق',
                    'message' => number_format((float) $payment->amount) . ' تومان از '
                        . ($payment->user->name ?? $payment->user->mobile ?? 'کاربر'),
                    'url' => route('admin.payment.transactions.show', $payment),
                    'created_at' => $payment->created_at ?? now(),
                ];
            });
    }

    private function failedPaymentNotifications(): Collection
    {
        return Payment::with(['user'])
            ->where('status', Payment::STATUS_FAILED)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function (Payment $payment) {
                return [
                    'id' => 'payment_failed_' . $payment->id,
                    'type' => 'payment_failed',
                    'icon' => 'fa-exclamation-triangle',
                    'icon_class' => 'text-red-400 bg-red-500/20',
                    'title' => 'پرداخت ناموفق',
                    'message' => number_format((float) $payment->amount) . ' تومان - '
                        . ($payment->user->name ?? $payment->user->mobile ?? 'کاربر'),
                    'url' => route('admin.payment.transactions.show', $payment),
                    'created_at' => $payment->created_at ?? now(),
                ];
            });
    }
}
