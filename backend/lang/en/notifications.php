<?php

return [
    // Notification types
    'task_assigned'     => 'New task assigned to you',
    'task_completed'    => 'Task completed',
    'task_comment'      => 'New comment on task',
    'task_due_soon'     => 'Task deadline approaching',
    'task_overdue'      => 'Task is overdue',
    'approval_request'  => 'Approval request received',
    'approval_approved' => 'Your request has been approved',
    'approval_rejected' => 'Your request has been rejected',
    'mention'           => 'You were mentioned in a comment',
    'system'            => 'System notification',
    'new_comment'       => 'New comment',

    // Notification channels
    'channels'          => [
        'web'      => 'Web Notifications',
        'email'    => 'Email Notifications',
        'sms'      => 'SMS Notifications',
        'telegram' => 'Telegram Notifications',
        'push'     => 'Push Notifications',
    ],

    // Preferences
    'preferences'       => [
        'email_enabled'    => 'Enable email notifications',
        'push_enabled'     => 'Enable push notifications',
        'sms_enabled'      => 'Enable SMS notifications',
        'digest_frequency' => 'Digest frequency',
        'quiet_hours'      => 'Quiet hours',
    ],

    // Digest frequencies
    'digest'            => [
        'instant' => 'Instant',
        'hourly'  => 'Hourly',
        'daily'   => 'Daily',
        'weekly'  => 'Weekly',
    ],
];
