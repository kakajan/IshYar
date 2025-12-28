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
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json($routines);
    }

    /**
     * Show a specific routine template.
     */
    public function show(RoutineTemplate $routine): JsonResponse
    {
        $routine->load(['tasks' => function ($query) {
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
            'name'                    => ['required', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'frequency'               => ['required', 'in:daily,weekly,monthly,yearly,custom'],
            'recurrence_rule'         => ['required', 'array'],
            'default_checklist'       => ['nullable', 'array'],
            'default_estimated_hours' => ['nullable', 'integer', 'min:0'],
            'default_priority'        => ['required', 'in:low,medium,high,urgent'],
            'is_active'               => ['boolean'],
        ]);

        $routine = RoutineTemplate::create([
            'organization_id'         => $request->user()->organization_id,
            'creator_id'              => $request->user()->id,
            'name'                    => $validated['name'],
            'description'             => $validated['description'] ?? null,
            'frequency'               => $validated['frequency'],
            'recurrence_rule'         => $validated['recurrence_rule'],
            'default_checklist'       => $validated['default_checklist'] ?? null,
            'default_estimated_hours' => $validated['default_estimated_hours'] ?? null,
            'default_priority'        => $validated['default_priority'],
            'is_active'               => $validated['is_active'] ?? true,
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
            'name'                    => ['sometimes', 'required', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'frequency'               => ['sometimes', 'required', 'in:daily,weekly,monthly,yearly,custom'],
            'recurrence_rule'         => ['sometimes', 'required', 'array'],
            'default_checklist'       => ['nullable', 'array'],
            'default_estimated_hours' => ['nullable', 'integer', 'min:0'],
            'default_priority'        => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'is_active'               => ['boolean'],
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
        $routine->update(['is_active' => ! $routine->is_active]);

        return response()->json([
            'message' => $routine->is_active
                ? __('messages.routine_activated')
                : __('messages.routine_deactivated'),
            'data'    => $routine->fresh(),
        ]);
    }

    /**
     * Manually trigger a routine (create a task from it).
     */
    public function trigger(RoutineTemplate $routine): JsonResponse
    {
        if (! $routine->is_active) {
            return response()->json([
                'message' => __('messages.routine_inactive'),
            ], 400);
        }

        $task = $routine->generateTask();

        return response()->json([
            'message' => __('messages.routine_triggered'),
            'data'    => [
                'task'    => $task,
                'routine' => $routine->fresh(),
            ],
        ]);
    }
}
