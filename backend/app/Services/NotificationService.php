<?php
namespace App\Services;

use App\Jobs\SendSmsNotification;
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
        $this->dispatchToChannels($notification, $user, $channels);

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

        // Add SMS channel if user has verified phone and SMS is enabled
        if (($preferences['sms'] ?? false) && $user->phone && $user->phone_verified_at) {
            $channels[] = Notification::CHANNEL_SMS;
        }

        return $channels;
    }

    /**
     * Dispatch notification to channels.
     */
    protected function dispatchToChannels(Notification $notification, User $user, array $channels): void
    {
        foreach ($channels as $channel) {
            $status           = $notification->delivery_status ?? [];
            $status[$channel] = [
                'sent_at' => now()->toIso8601String(),
                'status'  => 'pending',
            ];
            $notification->update(['delivery_status' => $status]);

            // Dispatch to appropriate channel handler
            match ($channel) {
                Notification::CHANNEL_SMS => $this->dispatchSms($notification, $user),
                Notification::CHANNEL_WEB => $this->markChannelSent($notification, $channel),
                // Future: Add email, push handlers here
                default => $this->markChannelSent($notification, $channel),
            };
        }
    }

    /**
     * Dispatch SMS notification job.
     */
    protected function dispatchSms(Notification $notification, User $user): void
    {
        if (! $user->phone) {
            $this->markChannelStatus($notification, Notification::CHANNEL_SMS, 'skipped', 'No phone number');
            return;
        }

        // Map notification type to SMS pattern type
        $smsType = $this->mapNotificationTypeToSmsType($notification->type);

        // Build params for SMS pattern
        $params = $this->buildSmsParams($notification);

        SendSmsNotification::dispatch(
            $notification,
            $user->phone,
            $smsType,
            $params
        );
    }

    /**
     * Map notification type to SMS pattern type.
     */
    protected function mapNotificationTypeToSmsType(string $notificationType): string
    {
        return match ($notificationType) {
            Notification::TYPE_TASK_ASSIGNED => 'task_assigned',
            Notification::TYPE_TASK_COMPLETED => 'task_completed',
            Notification::TYPE_TASK_COMMENT => 'task_comment',
            Notification::TYPE_APPROVAL_REQUEST => 'approval_request',
            default => 'general',
        };
    }

    /**
     * Build SMS params from notification data.
     */
    protected function buildSmsParams(Notification $notification): array
    {
        $data = $notification->data ?? [];

        return [
            'title' => $notification->title,
            'body' => $notification->body ?? '',
            'task_title' => $data['task_title'] ?? '',
            'creator' => $data['creator'] ?? '',
            'completed_by' => $data['completed_by'] ?? '',
            'commenter' => $data['commenter'] ?? '',
            'requester' => $data['requester'] ?? '',
        ];
    }

    /**
     * Mark channel as sent.
     */
    protected function markChannelSent(Notification $notification, string $channel): void
    {
        $this->markChannelStatus($notification, $channel, 'sent');
    }

    /**
     * Update channel delivery status.
     */
    protected function markChannelStatus(Notification $notification, string $channel, string $status, ?string $error = null): void
    {
        $deliveryStatus = $notification->delivery_status ?? [];
        $deliveryStatus[$channel] = [
            'sent_at' => now()->toIso8601String(),
            'status'  => $status,
            'error'   => $error,
        ];
        $notification->update(['delivery_status' => $deliveryStatus]);
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

