<?php

return [
    // Auth messages
    'login_success'              => 'Login successful',
    'login_failed'               => 'Invalid email or password',
    'logout_success'             => 'Logged out successfully',
    'register_success'           => 'Registration successful',
    'password_reset_link_sent'   => 'Password reset link has been sent to your email',
    'password_reset_success'     => 'Password has been reset successfully',
    'password_reset_failed'      => 'Failed to reset password',
    'invalid_reset_token'        => 'Invalid or expired password reset token',
    'profile_update_success'     => 'Profile updated successfully',
    'password_change_success'    => 'Password changed successfully',
    'current_password_incorrect' => 'Current password is incorrect',
    'unauthenticated'            => 'Unauthenticated',
    'unauthorized'               => 'Unauthorized',

    // CRUD messages
    'created'                    => ':resource created successfully',
    'updated'                    => ':resource updated successfully',
    'deleted'                    => ':resource deleted successfully',
    'fetched'                    => ':resource fetched successfully',
    'not_found'                  => ':resource not found',
    'create_failed'              => 'Failed to create :resource',
    'update_failed'              => 'Failed to update :resource',
    'delete_failed'              => 'Failed to delete :resource',

    // Resource names
    'resources'                  => [
        'user'         => 'User',
        'task'         => 'Task',
        'department'   => 'Department',
        'position'     => 'Position',
        'comment'      => 'Comment',
        'attachment'   => 'Attachment',
        'role'         => 'Role',
        'permission'   => 'Permission',
        'notification' => 'Notification',
        'routine'      => 'Routine Template',
        'time_entry'   => 'Time Entry',
    ],

    // Task messages
    'task_started'               => 'Task started successfully',
    'task_completed'             => 'Task completed successfully',
    'task_already_started'       => 'Task is already in progress',
    'task_already_completed'     => 'Task is already completed',
    'task_cannot_start'          => 'Cannot start this task',
    'task_cannot_complete'       => 'Cannot complete this task',

    // Comment messages
    'comment_created'            => 'Comment added successfully',
    'comment_updated'            => 'Comment updated successfully',
    'comment_deleted'            => 'Comment deleted successfully',

    // Time entry messages
    'time_entry_started'         => 'Timer started',
    'time_entry_stopped'         => 'Timer stopped',
    'no_active_timer'            => 'No active timer found',
    'timer_already_running'      => 'A timer is already running for this task',

    // Notification messages
    'notification_marked_read'   => 'Notification marked as read',
    'notifications_marked_read'  => 'All notifications marked as read',
    'notification_deleted'       => 'Notification deleted',
    'preferences_updated'        => 'Notification preferences updated',

    // Routine template messages
    'routine_created'            => 'Routine template created successfully',
    'routine_updated'            => 'Routine template updated successfully',
    'routine_deleted'            => 'Routine template deleted successfully',
    'routine_activated'          => 'Routine template activated',
    'routine_deactivated'        => 'Routine template deactivated',
    'routine_triggered'          => 'Routine executed - task created',
    'routine_inactive'           => 'Cannot trigger inactive routine',

    // Validation messages
    'validation_failed'          => 'Validation failed',
    'email_already_exists'       => 'This email is already registered',
    'invalid_email'              => 'Please provide a valid email address',
    'password_min_length'        => 'Password must be at least :count characters',
    'passwords_must_match'       => 'Password confirmation does not match',

    // General messages
    'success'                    => 'Operation completed successfully',
    'error'                      => 'An error occurred',
    'server_error'               => 'Internal server error',
    'forbidden'                  => 'You do not have permission to perform this action',
    'too_many_requests'          => 'Too many requests. Please try again later',
];
