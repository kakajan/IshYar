<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
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
        });

        // Users
        Route::get('users/team', [UserController::class, 'team']);
        Route::get('users/colleagues', [UserController::class, 'colleagues']);
        Route::apiResource('users', UserController::class)->only(['index', 'show']);

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
        Route::apiResource('tasks', TaskController::class);
        Route::prefix('tasks/{task}')->group(function () {
            Route::post('complete', [TaskController::class, 'complete']);
            Route::post('start', [TaskController::class, 'start']);
            Route::get('comments', [TaskController::class, 'comments']);
            Route::post('comments', [TaskController::class, 'addComment']);
        });

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/team', [DashboardController::class, 'team']);
    });
});
