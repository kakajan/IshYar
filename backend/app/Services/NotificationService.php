<?php
namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create and send a notification.
     */
    public function send(
        User $user,
        string $type,
        string $title,
        ?string $body = null,
        ?array $data = null,
        ?string $actionUrl = null,
        ?array $channels = null
    ): Notification {
        $channels = $channels ?? $this->getDefaultChannels($user, $type);

        $notification = Notification::create([
            'user_id'         => $user->id,
            'type'            => $type,
            'title'           => $title,
            'body'            => $body,
            'data'            => $data,
            'action_url'      => $actionUrl,
            'channels'        => $channels,
            'delivery_status' => [],
        ]);

        // Queue channel delivery
        $this->dispatchToChannels($notification, $channels);

        return $notification;
    }

    /**
     * Send task assigned notification.
     */
    public function taskAssigned(User $user, $task): Notification
    {
        return $this->send(
            user: $user,
            type: Notification::TYPE_TASK_ASSIGNED,
            title: __('notifications.task_assigned'),
            body: $task->title,
            data: [
                'task_id'    => $task->id,
                'task_title' => $task->title,
                'creator'    => $task->creator?->name,
            ],
            actionUrl: "/tasks/{$task->id}"
        );
    }

    /**
     * Send task completed notification.
     */
    public function taskCompleted(User $user, $task): Notification
    {
        return $this->send(
            user: $user,
            type: Notification::TYPE_TASK_COMPLETED,
            title: __('notifications.task_completed'),
            body: $task->title,
            data: [
                'task_id'      => $task->id,
                'task_title'   => $task->title,
                'completed_by' => $task->assignee?->name,
            ],
            actionUrl: "/tasks/{$task->id}"
        );
    }

    /**
     * Send task comment notification.
     */
    public function taskComment(User $user, $task, $comment): Notification
    {
        return $this->send(
            user: $user,
            type: Notification::TYPE_TASK_COMMENT,
            title: __('notifications.new_comment'),
            body: \Str::limit($comment->content, 100),
            data: [
                'task_id'    => $task->id,
                'task_title' => $task->title,
                'comment_id' => $comment->id,
                'commenter'  => $comment->user?->name,
            ],
            actionUrl: "/tasks/{$task->id}"
        );
    }

    /**
     * Send task due soon reminder.
     */
    public function taskDueSoon(User $user, $task): Notification
    {
        return $this->send(
            user: $user,
            type: Notification::TYPE_TASK_DUE_SOON,
            title: __('notifications.task_due_soon'),
            body: $task->title,
            data: [
                'task_id'  => $task->id,
                'due_date' => $task->due_date->toIso8601String(),
            ],
            actionUrl: "/tasks/{$task->id}"
        );
    }

    /**
     * Send approval request notification.
     */
    public function approvalRequest(User $approver, $task): Notification
    {
        return $this->send(
            user: $approver,
            type: Notification::TYPE_APPROVAL_REQUEST,
            title: __('notifications.approval_request'),
            body: $task->title,
            data: [
                'task_id'    => $task->id,
                'task_title' => $task->title,
                'requester'  => $task->assignee?->name,
            ],
            actionUrl: "/tasks/{$task->id}"
        );
    }

    /**
     * Get default channels based on user preferences.
     */
    protected function getDefaultChannels(User $user, string $type): array
    {
        $preferences = $user->notification_preferences ?? [];
        $channels    = [Notification::CHANNEL_WEB]; // Always include web

        if ($preferences['email'] ?? true) {
            $channels[] = Notification::CHANNEL_EMAIL;
        }

        if ($preferences['push'] ?? true) {
            $channels[] = Notification::CHANNEL_PUSH;
        }

        return $channels;
    }

    /**
     * Dispatch notification to channels.
     */
    protected function dispatchToChannels(Notification $notification, array $channels): void
    {
        foreach ($channels as $channel) {
            // In a production app, this would dispatch jobs to handle each channel
            // For now, we'll just update the delivery status
            $status           = $notification->delivery_status ?? [];
            $status[$channel] = [
                'sent_at' => now()->toIso8601String(),
                'status'  => 'sent',
            ];
            $notification->update(['delivery_status' => $status]);
        }
    }

    /**
     * Notify multiple users.
     */
    public function sendToMany(
        array $users,
        string $type,
        string $title,
        ?string $body = null,
        ?array $data = null,
        ?string $actionUrl = null
    ): array {
        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = $this->send(
                user: $user,
                type: $type,
                title: $title,
                body: $body,
                data: $data,
                actionUrl: $actionUrl
            );
        }

        return $notifications;
    }
}
