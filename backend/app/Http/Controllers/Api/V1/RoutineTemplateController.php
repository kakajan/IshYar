<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RoutineTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoutineTemplateController extends Controller
{
    /**
     * List all routine templates for the user's organization.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $routines = RoutineTemplate::query()
            ->where('organization_id', $user->organization_id)
            ->with(['department:id,name', 'assignee:id,name,email'])
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('next_run_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($routines);
    }

    /**
     * Show a specific routine template.
     */
    public function show(RoutineTemplate $routine): JsonResponse
    {
        $routine->load(['department:id,name', 'assignee:id,name,email', 'tasks' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json($routine);
    }

    /**
     * Create a new routine template.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'department_id'  => ['nullable', 'uuid', 'exists:departments,id'],
            'assignee_id'    => ['nullable', 'uuid', 'exists:users,id'],
            'priority'       => ['required', 'in:low,medium,high,critical'],
            'estimated_time' => ['nullable', 'integer', 'min:1'],
            'frequency'      => ['required', 'in:daily,weekly,monthly,custom'],
            'frequency_settings' => ['nullable', 'array'],
            'next_run_at'    => ['required', 'date', 'after:now'],
            'is_active'      => ['boolean'],
            'settings'       => ['nullable', 'array'],
        ]);

        $routine = RoutineTemplate::create([
            'organization_id'    => $request->user()->organization_id,
            'created_by'         => $request->user()->id,
            'title'              => $validated['title'],
            'description'        => $validated['description'] ?? null,
            'department_id'      => $validated['department_id'] ?? null,
            'assignee_id'        => $validated['assignee_id'] ?? null,
            'priority'           => $validated['priority'],
            'estimated_time'     => $validated['estimated_time'] ?? null,
            'frequency'          => $validated['frequency'],
            'frequency_settings' => $validated['frequency_settings'] ?? [],
            'next_run_at'        => $validated['next_run_at'],
            'is_active'          => $validated['is_active'] ?? true,
            'settings'           => $validated['settings'] ?? [],
        ]);

        return response()->json([
            'message' => __('messages.routine_created'),
            'data'    => $routine,
        ], 201);
    }

    /**
     * Update a routine template.
     */
    public function update(Request $request, RoutineTemplate $routine): JsonResponse
    {
        $validated = $request->validate([
            'title'          => ['sometimes', 'required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'department_id'  => ['nullable', 'uuid', 'exists:departments,id'],
            'assignee_id'    => ['nullable', 'uuid', 'exists:users,id'],
            'priority'       => ['sometimes', 'required', 'in:low,medium,high,critical'],
            'estimated_time' => ['nullable', 'integer', 'min:1'],
            'frequency'      => ['sometimes', 'required', 'in:daily,weekly,monthly,custom'],
            'frequency_settings' => ['nullable', 'array'],
            'next_run_at'    => ['nullable', 'date'],
            'is_active'      => ['boolean'],
            'settings'       => ['nullable', 'array'],
        ]);

        $routine->update($validated);

        return response()->json([
            'message' => __('messages.routine_updated'),
            'data'    => $routine->fresh(),
        ]);
    }

    /**
     * Delete a routine template.
     */
    public function destroy(RoutineTemplate $routine): JsonResponse
    {
        $routine->delete();

        return response()->json([
            'message' => __('messages.routine_deleted'),
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggle(RoutineTemplate $routine): JsonResponse
    {
        $routine->update(['is_active' => !$routine->is_active]);

        return response()->json([
            'message' => $routine->is_active 
                ? __('messages.routine_activated') 
                : __('messages.routine_deactivated'),
            'data' => $routine->fresh(),
        ]);
    }

    /**
     * Manually trigger a routine (create a task from it).
     */
    public function trigger(RoutineTemplate $routine): JsonResponse
    {
        if (!$routine->is_active) {
            return response()->json([
                'message' => __('messages.routine_inactive'),
            ], 400);
        }

        $task = $routine->generateTask();

        // Update next run
        $routine->calculateNextRunAt();

        return response()->json([
            'message' => __('messages.routine_triggered'),
            'data'    => [
                'task'    => $task,
                'routine' => $routine->fresh(),
            ],
        ]);
    }
}
