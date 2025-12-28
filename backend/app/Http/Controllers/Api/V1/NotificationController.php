<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get current user's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->when($request->unread_only, fn($q) => $q->unread())
            ->when($request->type, fn($q, $type) => $q->ofType($type))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'success',
            'data'   => $notifications,
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->unread()
            ->count();

        return response()->json([
            'status' => 'success',
            'data'   => ['count' => $count],
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get user's notification preferences.
     */
    public function preferences(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'email_notifications' => $user->notification_preferences['email'] ?? true,
                'push_notifications'  => $user->notification_preferences['push'] ?? true,
                'sms_notifications'   => $user->notification_preferences['sms'] ?? false,
                'task_reminders'      => $user->notification_preferences['task_reminders'] ?? true,
                'weekly_digest'       => $user->notification_preferences['weekly_digest'] ?? true,
            ],
        ]);
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'email_notifications' => ['sometimes', 'boolean'],
            'push_notifications'  => ['sometimes', 'boolean'],
            'sms_notifications'   => ['sometimes', 'boolean'],
            'task_reminders'      => ['sometimes', 'boolean'],
            'weekly_digest'       => ['sometimes', 'boolean'],
        ]);

        $user = auth()->user();

        $preferences = $user->notification_preferences ?? [];
        $preferences = array_merge($preferences, [
            'email'          => $request->email_notifications ?? $preferences['email'] ?? true,
            'push'           => $request->push_notifications ?? $preferences['push'] ?? true,
            'sms'            => $request->sms_notifications ?? $preferences['sms'] ?? false,
            'task_reminders' => $request->task_reminders ?? $preferences['task_reminders'] ?? true,
            'weekly_digest'  => $request->weekly_digest ?? $preferences['weekly_digest'] ?? true,
        ]);

        $user->update(['notification_preferences' => $preferences]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Preferences updated',
            'data'    => $preferences,
        ]);
    }
}
