<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Get list of positions
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Position::query()
            ->with(['department'])
            ->where('organization_id', $user->organization_id);

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by manager level
        if ($request->has('is_manager')) {
            $query->where('is_manager', $request->boolean('is_manager'));
        }

        $positions = $query->orderBy('level', 'desc')->get();

        return response()->json([
            'data' => $positions,
        ]);
    }

    /**
     * Get a specific position
     */
    public function show(Position $position): JsonResponse
    {
        $user = auth()->user();

        if ($position->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $position->load(['department', 'users']);

        return response()->json([
            'data' => $position,
        ]);
    }

    /**
     * Create a new position
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('positions.create')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'required|string|max:255|unique:positions,slug',
            'description'      => 'nullable|string|max:1000',
            'department_id'    => 'nullable|uuid|exists:departments,id',
            'level'            => 'required|integer|min:1|max:10',
            'is_manager'       => 'boolean',
            'responsibilities' => 'nullable|array',
            'requirements'     => 'nullable|array',
            'is_active'        => 'boolean',
        ]);

        $position = Position::create([
             ...$validated,
            'organization_id' => $user->organization_id,
        ]);

        return response()->json([
            'message' => 'Position created successfully',
            'data'    => $position,
        ], 201);
    }

    /**
     * Update a position
     */
    public function update(Request $request, Position $position): JsonResponse
    {
        $user = auth()->user();

        if ($position->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $user->hasPermissionTo('positions.update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'slug'             => 'sometimes|string|max:255|unique:positions,slug,' . $position->id,
            'description'      => 'nullable|string|max:1000',
            'department_id'    => 'nullable|uuid|exists:departments,id',
            'level'            => 'sometimes|integer|min:1|max:10',
            'is_manager'       => 'boolean',
            'responsibilities' => 'nullable|array',
            'requirements'     => 'nullable|array',
            'is_active'        => 'boolean',
        ]);

        $position->update($validated);

        return response()->json([
            'message' => 'Position updated successfully',
            'data'    => $position,
        ]);
    }

    /**
     * Delete a position
     */
    public function destroy(Position $position): JsonResponse
    {
        $user = auth()->user();

        if ($position->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $user->hasPermissionTo('positions.delete')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if any users have this position
        if ($position->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete position with assigned users',
            ], 422);
        }

        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully',
        ]);
    }
}
