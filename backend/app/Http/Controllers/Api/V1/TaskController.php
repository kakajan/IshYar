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

        $query = Task::with(['creator', 'assignee', 'department'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->priority, fn($q, $priority) => $q->where('priority', $priority))
            ->when($request->assignee_id, fn($q, $id) => $q->where('assignee_id', $id))
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
            'status'          => Task::STATUS_PENDING,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task created successfully',
            'data'    => $task->load(['creator', 'assignee', 'department']),
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
                'creator', 'assignee', 'department', 'subtasks',
                'parent', 'comments.user', 'timeEntries',
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

        return response()->json([
            'status'  => 'success',
            'message' => 'Task updated successfully',
            'data'    => $task->fresh()->load(['creator', 'assignee', 'department']),
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
}
