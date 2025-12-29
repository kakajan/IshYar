<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Rules\TranslatableValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $query = Task::with(['creator', 'assignees', 'department'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->priority, fn($q, $priority) => $q->where('priority', $priority))
            ->when($request->assignee_id, fn($q, $id) => $q->whereHas('assignees', fn($q) => $q->where('users.id', $id)))
            ->when($request->department_id, fn($q, $id) => $q->where('department_id', $id))
            ->when($request->search, fn($q, $search) => $q->where("title->{$locale}", 'like', "%{$search}%"))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        $tasks = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data'   => $tasks,
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'           => ['required', new TranslatableValue(max: 255)],
            'description'     => ['nullable', new TranslatableValue()],
            'type'            => ['required', 'in:routine,situational'],
            'priority'        => ['nullable', 'in:low,medium,high,urgent'],
            'assignee_id'     => ['nullable', 'uuid', 'exists:users,id'],
            'department_id'   => ['nullable', 'uuid', 'exists:departments,id'],
            'due_date'        => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'checklist'       => ['nullable', 'array'],
            'parent_id'       => ['nullable', 'uuid', 'exists:tasks,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $task = Task::create([
             ...$request->only([
                'title', 'description', 'type', 'priority', 'assignee_id',
                'department_id', 'due_date', 'estimated_hours', 'checklist', 'parent_id',
            ]),
            'organization_id' => auth()->user()->organization_id,
            'creator_id'      => auth()->id(),
            'status'          => $request->status ?? Task::STATUS_PENDING,
        ]);

        if ($request->has('assignee_ids')) {
            $task->assignees()->sync($request->assignee_ids);
        } elseif ($request->has('assignee_id')) {
             $task->assignees()->sync([$request->assignee_id]);
        }

        if ($request->has('label_ids')) {
            $task->labels()->sync($request->label_ids);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Task created successfully',
            'data'    => $task->load(['creator', 'assignees', 'department', 'labels']),
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $task->load([
                'creator', 'assignees', 'department', 'subtasks',
                'parent', 'comments.user', 'timeEntries', 'labels'
            ]),
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'           => ['sometimes', new TranslatableValue(max: 255)],
            'description'     => ['nullable', new TranslatableValue()],
            'status'          => ['sometimes', 'in:pending,in_progress,completed,on_hold,cancelled'],
            'priority'        => ['sometimes', 'in:low,medium,high,urgent'],
            'assignee_id'     => ['nullable', 'uuid', 'exists:users,id'],
            'department_id'   => ['nullable', 'uuid', 'exists:departments,id'],
            'due_date'        => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'progress'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'checklist'       => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $task->update($request->only([
            'title', 'description', 'status', 'priority', 'assignee_id',
            'department_id', 'due_date', 'estimated_hours', 'progress', 'checklist',
        ]));

        if ($request->has('assignee_ids')) {
            $task->assignees()->sync($request->assignee_ids);
        }

        if ($request->has('label_ids')) {
            $task->labels()->sync($request->label_ids);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Task updated successfully',
            'data'    => $task->fresh()->load(['creator', 'assignees', 'department', 'labels']),
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Mark task as complete.
     */
    public function complete(Task $task): JsonResponse
    {
        $task->markAsCompleted();

        return response()->json([
            'status'  => 'success',
            'message' => 'Task completed successfully',
            'data'    => $task->fresh(),
        ]);
    }

    /**
     * Start working on a task.
     */
    public function start(Task $task): JsonResponse
    {
        $task->update([
            'status'     => Task::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task started',
            'data'    => $task->fresh(),
        ]);
    }

    /**
     * Submit task for approval.
     *
     * POST /api/v1/tasks/{task}/submit-for-approval
     */
    public function submitForApproval(Task $task): JsonResponse
    {
        // Only assignee can submit their task for approval
        if ($task->assignee_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Only the task assignee can submit for approval',
            ], 403);
        }

        // Task must be in progress
        if ($task->status !== Task::STATUS_IN_PROGRESS) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Task must be in progress to submit for approval',
            ], 422);
        }

        $task->submitForApproval();

        // Notify the approver
        $approver = $task->getApprover();
        if ($approver) {
            app(\App\Services\NotificationService::class)->approvalRequest($approver, $task);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Task submitted for approval',
            'data'    => $task->fresh()->load(['creator', 'assignee', 'approvedBy']),
        ]);
    }

    /**
     * Approve task completion.
     *
     * POST /api/v1/tasks/{task}/approve
     */
    public function approve(Task $task): JsonResponse
    {
        // Only manager or task creator can approve
        $approver = $task->getApprover();
        if (!$approver || $approver->id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not authorized to approve this task',
            ], 403);
        }

        // Task must be pending review
        if (!$task->isPendingReview()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Task is not pending review',
            ], 422);
        }

        $task->approve(auth()->id());

        // Notify the assignee
        if ($task->assignee) {
            app(\App\Services\NotificationService::class)->send(
                user: $task->assignee,
                type: \App\Models\Notification::TYPE_APPROVAL_RESULT,
                title: __('notifications.task_approved'),
                body: $task->title,
                data: [
                    'task_id'    => $task->id,
                    'task_title' => $task->title,
                    'approved_by' => auth()->user()->name,
                ],
                actionUrl: "/tasks/{$task->id}"
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Task approved successfully',
            'data'    => $task->fresh()->load(['creator', 'assignee', 'approvedBy']),
        ]);
    }

    /**
     * Request revision on task.
     *
     * POST /api/v1/tasks/{task}/request-revision
     */
    public function requestRevision(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason'            => ['required', 'string', 'max:1000'],
            'revision_deadline' => ['nullable', 'date', 'after:now'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Only manager or task creator can request revision
        $approver = $task->getApprover();
        if (!$approver || $approver->id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not authorized to request revision on this task',
            ], 403);
        }

        // Task must be pending review
        if (!$task->isPendingReview()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Task is not pending review',
            ], 422);
        }

        $deadline = $request->revision_deadline
            ? new \DateTime($request->revision_deadline)
            : null;

        $task->requestRevision($request->reason, $deadline);

        // Notify the assignee
        if ($task->assignee) {
            app(\App\Services\NotificationService::class)->send(
                user: $task->assignee,
                type: \App\Models\Notification::TYPE_APPROVAL_RESULT,
                title: __('notifications.revision_requested'),
                body: $request->reason,
                data: [
                    'task_id'    => $task->id,
                    'task_title' => $task->title,
                    'reason'     => $request->reason,
                    'requested_by' => auth()->user()->name,
                ],
                actionUrl: "/tasks/{$task->id}"
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Revision requested',
            'data'    => $task->fresh()->load(['creator', 'assignee']),
        ]);
    }

    /**
     * Get tasks pending approval for current user.
     *
     * GET /api/v1/tasks/pending-approvals
     */
    public function pendingApprovals(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Get tasks where user is the approver (manager of assignee or task creator)
        $tasks = Task::with(['assignee', 'department', 'creator'])
            ->where('status', Task::STATUS_PENDING_REVIEW)
            ->where(function ($query) use ($user) {
                // Tasks created by user
                $query->where('creator_id', $user->id)
                    // Or tasks assigned to users who report to this user
                    ->orWhereHas('assignee', function ($q) use ($user) {
                        $q->where('manager_id', $user->id);
                    });
            })
            ->orderBy('submitted_at', 'asc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data'   => $tasks,
        ]);
    }

    /**
     * Get current user's tasks.
     */
    public function myTasks(Request $request): JsonResponse
    {
        $tasks = Task::with(['department', 'creator'])
            ->where('assignee_id', auth()->id())
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data'   => $tasks,
        ]);
    }

    /**
     * Get today's tasks for current user.
     */
    public function todayTasks(): JsonResponse
    {
        $tasks = Task::with(['department'])
            ->where('assignee_id', auth()->id())
            ->whereDate('due_date', today())
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $tasks,
        ]);
    }

    /**
     * Get task comments.
     */
    public function comments(Task $task): JsonResponse
    {
        $comments = $task->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->with('replies.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $comments,
        ]);
    }

    /**
     * Add comment to task.
     */
    public function addComment(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content'   => ['required', 'string'],
            'parent_id' => ['nullable', 'uuid', 'exists:task_comments,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment = $task->comments()->create([
            'user_id'   => auth()->id(),
            'content'   => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Comment added',
            'data'    => $comment->load('user'),
        ], 201);
    }

    /**
     * Get tasks optimized for Kanban board view.
     *
     * GET /api/v1/tasks/kanban
     */
    public function kanban(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Task::with(['assignees', 'creator', 'department', 'labels'])
            ->where('organization_id', $user->organization_id)
            ->when($request->project_id, fn($q, $id) => $q->where('parent_id', $id))
            ->when($request->assignee_id, fn($q, $id) => $q->whereHas('assignees', fn($q) => $q->where('users.id', $id)))
            ->when($request->department_id, fn($q, $id) => $q->where('department_id', $id))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->when($request->subject, fn($q, $s) => $q->whereJsonContains('metadata->tags', $s));

        // Exclude completed tasks older than 7 days unless requested
        if (!$request->boolean('include_all_completed')) {
            $query->where(function ($q) {
                $q->where('status', '!=', Task::STATUS_COMPLETED)
                  ->orWhere('completed_at', '>=', now()->subDays(7));
            });
        }

        $tasks = $query->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        // Group tasks by status for columns
        $columns = [
            [
                'id' => 'pending',
                'title' => __('tasks.status.pending'),
                'color' => '#6366f1',
                'tasks' => $tasks->where('status', Task::STATUS_PENDING)->values(),
                'count' => $tasks->where('status', Task::STATUS_PENDING)->count(),
            ],
            [
                'id' => 'in_progress',
                'title' => __('tasks.status.in_progress'),
                'color' => '#3b82f6',
                'tasks' => $tasks->where('status', Task::STATUS_IN_PROGRESS)->values(),
                'count' => $tasks->where('status', Task::STATUS_IN_PROGRESS)->count(),
                'wip_limit' => 10,
            ],
            [
                'id' => 'pending_review',
                'title' => __('tasks.status.pending_review'),
                'color' => '#f59e0b',
                'tasks' => $tasks->where('status', Task::STATUS_PENDING_REVIEW)->values(),
                'count' => $tasks->where('status', Task::STATUS_PENDING_REVIEW)->count(),
            ],
            [
                'id' => 'completed',
                'title' => __('tasks.status.completed'),
                'color' => '#10b981',
                'tasks' => $tasks->where('status', Task::STATUS_COMPLETED)->values(),
                'count' => $tasks->where('status', Task::STATUS_COMPLETED)->count(),
            ],
        ];

        // Optional swimlane grouping
        $swimlanes = null;
        if ($request->swimlane) {
            $swimlanes = $this->buildSwimlanes($tasks, $request->swimlane);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'columns' => $columns,
                'swimlanes' => $swimlanes,
            ],
            'meta' => [
                'total' => $tasks->count(),
                'filters_applied' => $request->only(['project_id', 'assignee_id', 'department_id', 'priority', 'subject']),
            ],
        ]);
    }

    /**
     * Build swimlanes for Kanban board.
     */
    protected function buildSwimlanes($tasks, string $groupBy): array
    {
        return match ($groupBy) {
            'assignee' => $tasks->groupBy('assignee_id')->map(function ($group, $assigneeId) {
                $assignee = $group->first()->assignee;
                return [
                    'id' => $assigneeId ?? 'unassigned',
                    'title' => $assignee?->name ?? __('tasks.unassigned'),
                    'avatar' => $assignee?->avatar,
                    'tasks' => $group->values(),
                    'count' => $group->count(),
                ];
            })->values()->all(),
            'priority' => $tasks->groupBy('priority')->map(function ($group, $priority) {
                return [
                    'id' => $priority,
                    'title' => __("tasks.priority.{$priority}"),
                    'tasks' => $group->values(),
                    'count' => $group->count(),
                ];
            })->values()->all(),
            'department' => $tasks->groupBy('department_id')->map(function ($group, $deptId) {
                $dept = $group->first()->department;
                return [
                    'id' => $deptId ?? 'none',
                    'title' => $dept?->name ?? __('tasks.no_department'),
                    'tasks' => $group->values(),
                    'count' => $group->count(),
                ];
            })->values()->all(),
            default => [],
        };
    }

    /**
     * Move task to different status/position (Kanban drag-drop).
     *
     * PATCH /api/v1/tasks/{task}/move
     */
    public function move(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status'   => ['required', 'in:pending,in_progress,pending_review,completed,on_hold,cancelled'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldStatus = $task->status;
        $newStatus = $request->status;

        // Handle status-specific logic
        if ($newStatus === Task::STATUS_COMPLETED && $oldStatus !== Task::STATUS_COMPLETED) {
            $task->update([
                'status' => $newStatus,
                'completed_at' => now(),
                'progress' => 100,
            ]);
        } elseif ($newStatus === Task::STATUS_IN_PROGRESS && !$task->started_at) {
            $task->update([
                'status' => $newStatus,
                'started_at' => now(),
            ]);
        } else {
            $task->update(['status' => $newStatus]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Task moved successfully',
            'data'    => $task->fresh()->load(['assignee', 'creator', 'department']),
        ]);
    }

    /**
     * Update task progress.
     *
     * POST /api/v1/tasks/{task}/progress
     */
    public function updateProgress(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'note'     => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $task->update(['progress' => $request->progress]);

        // Log progress update as activity
        if ($request->note) {
            $task->comments()->create([
                'user_id' => auth()->id(),
                'content' => "📊 Progress: {$request->progress}% - {$request->note}",
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Progress updated',
            'data'    => $task->fresh(),
        ]);
    }

    /**
     * Change task status with validation.
     *
     * POST /api/v1/tasks/{task}/status
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status'  => ['required', 'in:pending,in_progress,pending_review,completed,on_hold,cancelled'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldStatus = $task->status;
        $newStatus = $request->status;

        // Update status with appropriate timestamps
        $updateData = ['status' => $newStatus];

        if ($newStatus === Task::STATUS_IN_PROGRESS && !$task->started_at) {
            $updateData['started_at'] = now();
        } elseif ($newStatus === Task::STATUS_COMPLETED) {
            $updateData['completed_at'] = now();
            $updateData['progress'] = 100;
        }

        $task->update($updateData);

        // Log status change as comment
        if ($request->comment) {
            $task->comments()->create([
                'user_id' => auth()->id(),
                'content' => "🔄 Status: {$oldStatus} → {$newStatus}\n{$request->comment}",
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Status updated',
            'data'    => $task->fresh()->load(['assignee', 'creator']),
        ]);
    }

    /**
     * Get task subtasks.
     *
     * GET /api/v1/tasks/{task}/subtasks
     */
    public function subtasks(Task $task): JsonResponse
    {
        $subtasks = $task->subtasks()
            ->with(['assignee', 'creator'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $subtasks,
        ]);
    }

    /**
     * Create subtask.
     *
     * POST /api/v1/tasks/{task}/subtasks
     */
    public function createSubtask(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'       => ['required', new TranslatableValue(max: 255)],
            'description' => ['nullable', new TranslatableValue()],
            'assignee_id' => ['nullable', 'uuid', 'exists:users,id'],
            'priority'    => ['nullable', 'in:low,medium,high,urgent'],
            'due_date'    => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $subtask = Task::create([
            ...$request->only(['title', 'description', 'assignee_id', 'priority', 'due_date']),
            'organization_id' => $task->organization_id,
            'parent_id'       => $task->id,
            'creator_id'      => auth()->id(),
            'department_id'   => $task->department_id,
            'type'            => $task->type,
            'status'          => Task::STATUS_PENDING,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Subtask created',
            'data'    => $subtask->load(['assignee', 'creator']),
        ], 201);
    }

    /**
     * Get task dependencies.
     *
     * GET /api/v1/tasks/{task}/dependencies
     */
    public function dependencies(Task $task): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'blocks' => $task->dependents()->with(['assignee'])->get(),
                'blocked_by' => $task->dependencies()->with(['assignee'])->get(),
            ],
        ]);
    }

    /**
     * Add task dependency.
     *
     * POST /api/v1/tasks/{task}/dependencies
     */
    public function addDependency(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'depends_on_id'   => ['required', 'uuid', 'exists:tasks,id', 'different:task'],
            'dependency_type' => ['nullable', 'in:finish_to_start,start_to_start,finish_to_finish,start_to_finish'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check for circular dependency
        if ($this->wouldCreateCircularDependency($task, $request->depends_on_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This would create a circular dependency',
            ], 422);
        }

        $task->dependencies()->attach($request->depends_on_id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'dependency_type' => $request->dependency_type ?? 'finish_to_start',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dependency added',
        ], 201);
    }

    /**
     * Remove task dependency.
     *
     * DELETE /api/v1/tasks/{task}/dependencies/{dependsOnId}
     */
    public function removeDependency(Task $task, string $dependsOnId): JsonResponse
    {
        $task->dependencies()->detach($dependsOnId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dependency removed',
        ]);
    }

    /**
     * Check if adding dependency would create circular reference.
     */
    protected function wouldCreateCircularDependency(Task $task, string $dependsOnId): bool
    {
        $visited = [];
        $queue = [$dependsOnId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);

            if ($currentId === $task->id) {
                return true;
            }

            if (in_array($currentId, $visited)) {
                continue;
            }

            $visited[] = $currentId;
            $current = Task::find($currentId);

            if ($current) {
                foreach ($current->dependencies as $dep) {
                    $queue[] = $dep->id;
                }
            }
        }

        return false;
    }

    /**
     * Get unique subjects/tags for filtering.
     *
     * GET /api/v1/tasks/subjects
     */
    public function subjects(): JsonResponse
    {
        $user = auth()->user();

        // Extract unique tags from task metadata
        $tags = Task::where('organization_id', $user->organization_id)
            ->whereNotNull('metadata->tags')
            ->pluck('metadata')
            ->flatMap(fn($m) => $m['tags'] ?? [])
            ->unique()
            ->values();

        return response()->json([
            'status' => 'success',
            'data'   => $tags,
        ]);
    }
}
