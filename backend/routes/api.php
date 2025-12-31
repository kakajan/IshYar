<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\JalaliController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\PhoneVerificationController;
use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\RoutineTemplateController;
use App\Http\Controllers\Api\V1\TaskCommentController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TimeEntryController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('debug-test', [App\Http\Controllers\Api\V1\DebugController::class, 'index']);

    // Public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);

        // Phone verification (can be used without auth for registration)
        Route::post('phone/send-otp', [PhoneVerificationController::class, 'sendOtp']);
        Route::post('phone/verify', [PhoneVerificationController::class, 'verify']);
    });

    // Protected routes
    Route::middleware('auth:api')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::put('password', [AuthController::class, 'changePassword']);

            // Phone verification status (authenticated)
            Route::get('phone/status', [PhoneVerificationController::class, 'status']);
        });

        // Users
        Route::get('users/team', [UserController::class, 'team']);
        Route::get('users/colleagues', [UserController::class, 'colleagues']);
        Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']);

        // Organization (current user's organization)
        Route::get('organization', [OrganizationController::class, 'show']);
        Route::put('organization', [OrganizationController::class, 'update']);
        Route::get('organization/statistics', [OrganizationController::class, 'statistics']);

        // Departments
        Route::get('departments/tree', [DepartmentController::class, 'tree']);
        Route::apiResource('departments', DepartmentController::class);

        // Positions
        Route::apiResource('positions', PositionController::class);

        // Tasks
        Route::get('my/tasks', [TaskController::class, 'myTasks']);
        Route::get('my/tasks/today', [TaskController::class, 'todayTasks']);
        Route::get('tasks/pending-approvals', [TaskController::class, 'pendingApprovals']);
        Route::get('tasks/kanban', [TaskController::class, 'kanban']);
        Route::get('tasks/subjects', [TaskController::class, 'subjects']);
        Route::apiResource('tasks', TaskController::class);

        // Labels
        Route::apiResource('labels', \App\Http\Controllers\Api\V1\LabelController::class);

        Route::prefix('tasks/{task}')->group(function () {
            // Task Labels
            Route::post('labels', [\App\Http\Controllers\Api\V1\LabelController::class, 'attachToTask']);
            Route::put('labels', [\App\Http\Controllers\Api\V1\LabelController::class, 'syncTaskLabels']);
            Route::delete('labels/{label}', [\App\Http\Controllers\Api\V1\LabelController::class, 'detachFromTask']);

            Route::post('complete', [TaskController::class, 'complete']);
            Route::post('start', [TaskController::class, 'start']);
            Route::patch('move', [TaskController::class, 'move']);
            Route::post('progress', [TaskController::class, 'updateProgress']);
            Route::post('status', [TaskController::class, 'updateStatus']);

            // Approval workflow
            Route::post('submit-for-approval', [TaskController::class, 'submitForApproval']);
            Route::post('approve', [TaskController::class, 'approve']);
            Route::post('request-revision', [TaskController::class, 'requestRevision']);

            // Subtasks
            Route::get('subtasks', [TaskController::class, 'subtasks']);
            Route::post('subtasks', [TaskController::class, 'createSubtask']);

            // Dependencies
            Route::get('dependencies', [TaskController::class, 'dependencies']);
            Route::post('dependencies', [TaskController::class, 'addDependency']);
            Route::delete('dependencies/{dependsOnId}', [TaskController::class, 'removeDependency']);

            // Task Comments
            Route::get('comments', [TaskCommentController::class, 'index']);
            Route::post('comments', [TaskCommentController::class, 'store']);
            Route::put('comments/{comment}', [TaskCommentController::class, 'update']);
            Route::delete('comments/{comment}', [TaskCommentController::class, 'destroy']);

            // Time entries for task
            Route::get('time-entries', [TimeEntryController::class, 'forTask']);
            Route::post('time-entries', [TimeEntryController::class, 'store']);
        });

        // Routine Templates (Scheduled/Recurring Tasks)
        Route::prefix('routines')->group(function () {
            Route::get('/', [RoutineTemplateController::class, 'index']);
            Route::post('/', [RoutineTemplateController::class, 'store']);
            Route::get('{routine}', [RoutineTemplateController::class, 'show']);
            Route::put('{routine}', [RoutineTemplateController::class, 'update']);
            Route::delete('{routine}', [RoutineTemplateController::class, 'destroy']);
            Route::post('{routine}/toggle', [RoutineTemplateController::class, 'toggle']);
            Route::post('{routine}/trigger', [RoutineTemplateController::class, 'trigger']);
        });

        // Time entries
        Route::prefix('time-entries')->group(function () {
            Route::get('/', [TimeEntryController::class, 'index']);
            Route::get('active', [TimeEntryController::class, 'active']);
            Route::post('{timeEntry}/stop', [TimeEntryController::class, 'stop']);
            Route::delete('{timeEntry}', [TimeEntryController::class, 'destroy']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('{notification}', [NotificationController::class, 'destroy']);
            Route::get('preferences', [NotificationController::class, 'preferences']);
            Route::put('preferences', [NotificationController::class, 'updatePreferences']);
        });

        // Jalali Calendar
        Route::prefix('jalali')->group(function () {
            Route::get('settings', [JalaliController::class, 'settings']);
            Route::patch('settings', [JalaliController::class, 'updateSettings']);
            Route::get('convert', [JalaliController::class, 'convert']);
            Route::get('holidays', [JalaliController::class, 'holidays']);
            Route::get('format', [JalaliController::class, 'format']);
            Route::get('today', [JalaliController::class, 'today']);
            Route::get('calendar', [JalaliController::class, 'calendar']);
        });

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/team', [DashboardController::class, 'team']);
    });
});
