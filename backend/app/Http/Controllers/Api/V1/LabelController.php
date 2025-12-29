<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LabelController extends Controller
{
    /**
     * Display a listing of labels.
     *
     * GET /api/v1/labels
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $labels = Label::where('organization_id', $user->organization_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $labels,
        ]);
    }

    /**
     * Store a newly created label.
     *
     * POST /api/v1/labels
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'color'       => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Check for duplicate name in organization
        $exists = Label::where('organization_id', $user->organization_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A label with this name already exists',
            ], 422);
        }

        // Get max sort_order
        $maxOrder = Label::where('organization_id', $user->organization_id)->max('sort_order') ?? 0;

        $label = Label::create([
            'organization_id' => $user->organization_id,
            'name'            => $request->name,
            'color'           => $request->color ?? Label::COLORS[array_rand(Label::COLORS)],
            'description'     => $request->description,
            'sort_order'      => $maxOrder + 1,
        ]);

        return response()->json([
            'message' => 'Label created successfully',
            'data'    => $label,
        ], 201);
    }

    /**
     * Display the specified label.
     *
     * GET /api/v1/labels/{label}
     */
    public function show(Label $label): JsonResponse
    {
        return response()->json([
            'data' => $label->load('tasks'),
        ]);
    }

    /**
     * Update the specified label.
     *
     * PUT /api/v1/labels/{label}
     */
    public function update(Request $request, Label $label): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:100',
            'color'       => 'sometimes|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check for duplicate name if name is being changed
        if ($request->has('name') && $request->name !== $label->name) {
            $exists = Label::where('organization_id', $label->organization_id)
                ->where('name', $request->name)
                ->where('id', '!=', $label->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'A label with this name already exists',
                ], 422);
            }
        }

        $label->update($request->only(['name', 'color', 'description', 'sort_order']));

        return response()->json([
            'message' => 'Label updated successfully',
            'data'    => $label,
        ]);
    }

    /**
     * Remove the specified label.
     *
     * DELETE /api/v1/labels/{label}
     */
    public function destroy(Label $label): JsonResponse
    {
        $label->delete();

        return response()->json([
            'message' => 'Label deleted successfully',
        ]);
    }

    /**
     * Attach labels to a task.
     *
     * POST /api/v1/tasks/{task}/labels
     */
    public function attachToTask(Request $request, string $taskId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label_ids'   => 'required|array',
            'label_ids.*' => 'uuid|exists:labels,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $task = \App\Models\Task::findOrFail($taskId);
        $task->labels()->syncWithoutDetaching($request->label_ids);

        return response()->json([
            'message' => 'Labels attached successfully',
            'data'    => $task->load('labels'),
        ]);
    }

    /**
     * Detach a label from a task.
     *
     * DELETE /api/v1/tasks/{task}/labels/{label}
     */
    public function detachFromTask(string $taskId, string $labelId): JsonResponse
    {
        $task = \App\Models\Task::findOrFail($taskId);
        $task->labels()->detach($labelId);

        return response()->json([
            'message' => 'Label detached successfully',
            'data'    => $task->load('labels'),
        ]);
    }

    /**
     * Sync labels for a task (replace all).
     *
     * PUT /api/v1/tasks/{task}/labels
     */
    public function syncTaskLabels(Request $request, string $taskId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label_ids'   => 'present|array',
            'label_ids.*' => 'uuid|exists:labels,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $task = \App\Models\Task::findOrFail($taskId);
        $task->labels()->sync($request->label_ids ?? []);

        return response()->json([
            'message' => 'Labels synced successfully',
            'data'    => $task->load('labels'),
        ]);
    }
}
