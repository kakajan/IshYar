<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeEntryController extends Controller
{
    /**
     * Get current user's time entries.
     */
    public function index(Request $request): JsonResponse
    {
        $entries = TimeEntry::with(['task'])
            ->where('user_id', auth()->id())
            ->when($request->date, fn($q, $date) => $q->whereDate('started_at', $date))
            ->when($request->task_id, fn($q, $id) => $q->where('task_id', $id))
            ->orderBy('started_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'success',
            'data'   => $entries,
        ]);
    }

    /**
     * Get time entries for a specific task.
     */
    public function forTask(Task $task): JsonResponse
    {
        $entries = $task->timeEntries()
            ->with('user')
            ->orderBy('started_at', 'desc')
            ->get();

        $totalMinutes = $entries->sum('duration_minutes');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'entries'       => $entries,
                'total_minutes' => $totalMinutes,
                'total_hours'   => round($totalMinutes / 60, 2),
            ],
        ]);
    }

    /**
     * Get active time entry (running timer).
     */
    public function active(): JsonResponse
    {
        $entry = TimeEntry::with('task')
            ->where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'data'   => $entry,
        ]);
    }

    /**
     * Start a new time entry (timer).
     */
    public function store(Request $request, Task $task): JsonResponse
    {
        // Check if there's already an active timer
        $activeEntry = TimeEntry::where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        if ($activeEntry) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You already have an active time entry. Stop it first.',
                'data'    => $activeEntry->load('task'),
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'description' => ['nullable', 'string', 'max:500'],
            'is_billable' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $entry = TimeEntry::create([
            'task_id'     => $task->id,
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'started_at'  => now(),
            'is_billable' => $request->is_billable ?? false,
        ]);

        // Start the task if it's pending
        if ($task->status === Task::STATUS_PENDING) {
            $task->update([
                'status'     => Task::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Timer started',
            'data'    => $entry->load('task'),
        ], 201);
    }

    /**
     * Stop a time entry.
     */
    public function stop(TimeEntry $timeEntry): JsonResponse
    {
        if ($timeEntry->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($timeEntry->ended_at) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This time entry has already been stopped.',
            ], 422);
        }

        $timeEntry->stop();

        // Update task actual_hours
        $task               = $timeEntry->task;
        $totalMinutes       = $task->timeEntries()->sum('duration_minutes');
        $task->actual_hours = round($totalMinutes / 60, 2);
        $task->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Timer stopped',
            'data'    => $timeEntry->fresh()->load('task'),
        ]);
    }

    /**
     * Delete a time entry.
     */
    public function destroy(TimeEntry $timeEntry): JsonResponse
    {
        if ($timeEntry->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $timeEntry->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Time entry deleted',
        ]);
    }
}
