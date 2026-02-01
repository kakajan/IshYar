<?php

use App\Models\Task;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('private-user.{userId}', function ($user, string $userId) {
    return $user->id === $userId;
});

Broadcast::channel('private-organization.{organizationId}', function ($user, string $organizationId) {
    return (string) $user->organization_id === $organizationId;
});

Broadcast::channel('private-department.{departmentId}', function ($user, string $departmentId) {
    return (string) $user->department_id === $departmentId;
});

Broadcast::channel('private-task.{taskId}', function ($user, string $taskId) {
    $task = Task::query()->select(['id', 'organization_id'])->find($taskId);

    if (!$task) {
        return false;
    }

    return (string) $user->organization_id === (string) $task->organization_id;
});
