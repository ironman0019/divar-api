<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private AdminNotificationService $notificationService
    ) {}

    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $notifications = $this->notificationService->getNotifications($userId);

        return response()->json([
            'notifications' => $notifications->map(fn (array $notification) => [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'icon' => $notification['icon'],
                'icon_class' => $notification['icon_class'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'url' => $notification['url'],
                'read' => $notification['read'],
                'created_at' => $notification['created_at']->diffForHumans(),
            ]),
            'unread_count' => $notifications->where('read', false)->count(),
        ]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|string|max:100',
        ]);

        $this->notificationService->markAsRead(
            auth()->id(),
            $request->input('notification_id')
        );

        return response()->json([
            'success' => true,
            'unread_count' => $this->notificationService->unreadCount(auth()->id()),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
