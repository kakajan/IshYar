<?php

return [
    // Auth messages
    'login_success' => 'Login successful',
    'login_failed' => 'Invalid email or password',
    'logout_success' => 'Logged out successfully',
    'register_success' => 'Registration successful',
    'password_reset_link_sent' => 'Password reset link has been sent to your email',
    'password_reset_success' => 'Password has been reset successfully',
    'password_reset_failed' => 'Failed to reset password',
    'invalid_reset_token' => 'Invalid or expired password reset token',
    'profile_update_success' => 'Profile updated successfully',
    'password_change_success' => 'Password changed successfully',
    'current_password_incorrect' => 'Current password is incorrect',
    'unauthenticated' => 'Unauthenticated',
    'unauthorized' => 'Unauthorized',

    // CRUD messages
    'created' => ':resource created successfully',
    'updated' => ':resource updated successfully',
    'deleted' => ':resource deleted successfully',
    'fetched' => ':resource fetched successfully',
    'not_found' => ':resource not found',
    'create_failed' => 'Failed to create :resource',
    'update_failed' => 'Failed to update :resource',
    'delete_failed' => 'Failed to delete :resource',

    // Resource names
    'resources' => [
        'user' => 'User',
        'task' => 'Task',
        'department' => 'Department',
        'position' => 'Position',
        'comment' => 'Comment',
        'attachment' => 'Attachment',
        'role' => 'Role',
        'permission' => 'Permission',
    ],

    // Task messages
    'task_started' => 'Task started successfully',
    'task_completed' => 'Task completed successfully',
    'task_already_started' => 'Task is already in progress',
    'task_already_completed' => 'Task is already completed',
    'task_cannot_start' => 'Cannot start this task',
    'task_cannot_complete' => 'Cannot complete this task',

    // Validation messages
    'validation_failed' => 'Validation failed',
    'email_already_exists' => 'This email is already registered',
    'invalid_email' => 'Please provide a valid email address',
    'password_min_length' => 'Password must be at least :count characters',
    'passwords_must_match' => 'Password confirmation does not match',

    // General messages
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'server_error' => 'Internal server error',
    'forbidden' => 'You do not have permission to perform this action',
    'too_many_requests' => 'Too many requests. Please try again later',
];
