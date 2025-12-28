<?php

return [
    // Auth messages
    'login_success'              => 'ورود موفقیت‌آمیز بود',
    'login_failed'               => 'ایمیل یا رمز عبور نادرست است',
    'logout_success'             => 'با موفقیت خارج شدید',
    'register_success'           => 'ثبت‌نام با موفقیت انجام شد',
    'password_reset_link_sent'   => 'لینک بازنشانی رمز عبور به ایمیل شما ارسال شد',
    'password_reset_success'     => 'رمز عبور با موفقیت بازنشانی شد',
    'password_reset_failed'      => 'بازنشانی رمز عبور ناموفق بود',
    'invalid_reset_token'        => 'توکن بازنشانی نامعتبر یا منقضی شده است',
    'profile_update_success'     => 'پروفایل با موفقیت به‌روزرسانی شد',
    'password_change_success'    => 'رمز عبور با موفقیت تغییر کرد',
    'current_password_incorrect' => 'رمز عبور فعلی نادرست است',
    'unauthenticated'            => 'احراز هویت نشده',
    'unauthorized'               => 'غیرمجاز',

    // CRUD messages
    'created'                    => ':resource با موفقیت ایجاد شد',
    'updated'                    => ':resource با موفقیت به‌روزرسانی شد',
    'deleted'                    => ':resource با موفقیت حذف شد',
    'fetched'                    => ':resource با موفقیت دریافت شد',
    'not_found'                  => ':resource یافت نشد',
    'create_failed'              => 'ایجاد :resource ناموفق بود',
    'update_failed'              => 'به‌روزرسانی :resource ناموفق بود',
    'delete_failed'              => 'حذف :resource ناموفق بود',

    // Resource names
    'resources'                  => [
        'user'         => 'کاربر',
        'task'         => 'وظیفه',
        'department'   => 'دپارتمان',
        'position'     => 'سمت',
        'comment'      => 'نظر',
        'attachment'   => 'پیوست',
        'role'         => 'نقش',
        'permission'   => 'مجوز',
        'notification' => 'اعلان',
        'routine'      => 'قالب دوره‌ای',
        'time_entry'   => 'ورودی زمان',
    ],

    // Task messages
    'task_started'               => 'وظیفه با موفقیت شروع شد',
    'task_completed'             => 'وظیفه با موفقیت تکمیل شد',
    'task_already_started'       => 'وظیفه در حال انجام است',
    'task_already_completed'     => 'وظیفه قبلاً تکمیل شده است',
    'task_cannot_start'          => 'امکان شروع این وظیفه وجود ندارد',
    'task_cannot_complete'       => 'امکان تکمیل این وظیفه وجود ندارد',

    // Comment messages
    'comment_created'            => 'نظر با موفقیت اضافه شد',
    'comment_updated'            => 'نظر با موفقیت به‌روزرسانی شد',
    'comment_deleted'            => 'نظر با موفقیت حذف شد',

    // Time entry messages
    'time_entry_started'         => 'تایمر شروع شد',
    'time_entry_stopped'         => 'تایمر متوقف شد',
    'no_active_timer'            => 'تایمر فعالی یافت نشد',
    'timer_already_running'      => 'تایمری برای این وظیفه در حال اجرا است',

    // Notification messages
    'notification_marked_read'   => 'اعلان خوانده شد',
    'notifications_marked_read'  => 'همه اعلان‌ها خوانده شدند',
    'notification_deleted'       => 'اعلان حذف شد',
    'preferences_updated'        => 'تنظیمات اعلان به‌روزرسانی شد',

    // Routine template messages
    'routine_created'            => 'قالب دوره‌ای با موفقیت ایجاد شد',
    'routine_updated'            => 'قالب دوره‌ای با موفقیت به‌روزرسانی شد',
    'routine_deleted'            => 'قالب دوره‌ای با موفقیت حذف شد',
    'routine_activated'          => 'قالب دوره‌ای فعال شد',
    'routine_deactivated'        => 'قالب دوره‌ای غیرفعال شد',
    'routine_triggered'          => 'قالب اجرا شد - وظیفه ایجاد شد',
    'routine_inactive'           => 'امکان اجرای قالب غیرفعال وجود ندارد',

    // Validation messages
    'validation_failed'          => 'اعتبارسنجی ناموفق بود',
    'email_already_exists'       => 'این ایمیل قبلاً ثبت شده است',
    'invalid_email'              => 'لطفاً یک آدرس ایمیل معتبر وارد کنید',
    'password_min_length'        => 'رمز عبور باید حداقل :count کاراکتر باشد',
    'passwords_must_match'       => 'تکرار رمز عبور مطابقت ندارد',

    // General messages
    'success'                    => 'عملیات با موفقیت انجام شد',
    'error'                      => 'خطایی رخ داد',
    'server_error'               => 'خطای داخلی سرور',
    'forbidden'                  => 'شما مجوز انجام این عملیات را ندارید',
    'too_many_requests'          => 'درخواست‌های بیش از حد. لطفاً بعداً تلاش کنید',
];
