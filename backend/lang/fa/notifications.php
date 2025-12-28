<?php

return [
    // Notification types
    'task_assigned'      => 'وظیفه جدیدی به شما اختصاص داده شد',
    'task_completed'     => 'وظیفه تکمیل شد',
    'task_comment'       => 'نظر جدید روی وظیفه',
    'task_due_soon'      => 'مهلت وظیفه نزدیک است',
    'task_overdue'       => 'وظیفه عقب افتاده است',
    'approval_request'   => 'درخواست تأیید دریافت شد',
    'approval_approved'  => 'درخواست شما تأیید شد',
    'approval_rejected'  => 'درخواست شما رد شد',
    'mention'            => 'در یک نظر به شما اشاره شد',
    'system'             => 'اعلان سیستم',
    'new_comment'        => 'نظر جدید',

    // Notification channels
    'channels'           => [
        'web'      => 'اعلان‌های وب',
        'email'    => 'اعلان‌های ایمیل',
        'sms'      => 'اعلان‌های پیامک',
        'telegram' => 'اعلان‌های تلگرام',
        'push'     => 'اعلان‌های فشاری',
    ],

    // Preferences
    'preferences'        => [
        'email_enabled'     => 'فعال‌سازی اعلان‌های ایمیل',
        'push_enabled'      => 'فعال‌سازی اعلان‌های فشاری',
        'sms_enabled'       => 'فعال‌سازی اعلان‌های پیامک',
        'digest_frequency'  => 'فرکانس خلاصه',
        'quiet_hours'       => 'ساعات آرام',
    ],

    // Digest frequencies
    'digest'             => [
        'instant' => 'فوری',
        'hourly'  => 'ساعتی',
        'daily'   => 'روزانه',
        'weekly'  => 'هفتگی',
    ],
];
