<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * List comments for a task.
     */
    public function index(Request $request, Task $task): JsonResponse
    {
        $comments = $task->comments()
            ->with(['user:id,name,email,avatar', 'replies.user:id,name,email,avatar'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'content'     => ['required', 'string', 'max:10000'],
            'parent_id'   => ['nullable', 'uuid', 'exists:task_comments,id'],
            'attachments' => ['nullable', 'array'],
        ]);

        $comment = $task->comments()->create([
            'user_id'     => $request->user()->id,
            'content'     => $validated['content'],
            'parent_id'   => $validated['parent_id'] ?? null,
            'attachments' => $validated['attachments'] ?? [],
        ]);

        $comment->load('user:id,name,email,avatar');

        // Notify task assignee and creator about new comment
        $usersToNotify = collect([$task->assignee, $task->creator])
            ->filter()
            ->reject(fn($user) => $user->id === $request->user()->id)
            ->unique('id');

        foreach ($usersToNotify as $user) {
            $this->notificationService->taskComment($user, $task, $comment);
        }

        return response()->json([
            'message' => __('messages.comment_created'),
            'data'    => $comment,
        ], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, Task $task, TaskComment $comment): JsonResponse
    {
        // Ensure comment belongs to task and user owns the comment
        if ($comment->task_id !== $task->id) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content'     => ['required', 'string', 'max:10000'],
            'attachments' => ['nullable', 'array'],
        ]);

        $comment->update([
            'content'     => $validated['content'],
            'attachments' => $validated['attachments'] ?? $comment->attachments,
        ]);

        $comment->load('user:id,name,email,avatar');

        return response()->json([
            'message' => __('messages.comment_updated'),
            'data'    => $comment,
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Request $request, Task $task, TaskComment $comment): JsonResponse
    {
        // Ensure comment belongs to task
        if ($comment->task_id !== $task->id) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Only owner or admin can delete
        if ($comment->user_id !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete replies first
        $comment->replies()->delete();
        $comment->delete();

        return response()->json([
            'message' => __('messages.comment_deleted'),
        ]);
    }
}
