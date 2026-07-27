<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get paginated notifications for the authenticated user.
     * GET /notifications
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('notifiable_id', $request->user()->id)
            ->where('notifiable_type', 'App\Models\User')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 10));

        $items = $notifications->map(function (Notification $n) {
            return [
                'id'         => $n->id,
                'type'       => $n->type,
                'type_label' => $n->typeLabel(),
                'icon_class' => $n->iconClass(),
                'data'       => $n->data,
                'message'    => $n->data['message'] ?? '',
                'read_at'    => $n->read_at?->toISOString(),
                'is_read'    => ! is_null($n->read_at),
                'link_url'   => $n->linkUrl(),
                'created_at' => $n->created_at->toISOString(),
                'time_ago'   => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get unread notification count for badge display.
     * GET /notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('notifiable_id', $request->user()->id)
            ->where('notifiable_type', 'App\Models\User')
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     * POST /notifications/{notification}/mark-read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('notifiable_id', $request->user()->id)
            ->where('notifiable_type', 'App\Models\User')
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     * POST /notifications/mark-all-read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('notifiable_id', $request->user()->id)
            ->where('notifiable_type', 'App\Models\User')
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
