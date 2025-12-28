<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Rules\TranslatableValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Get list of departments
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Department::query()
            ->with(['parent', 'head'])
            ->where('organization_id', $user->organization_id);

        // Filter by parent (for tree structure)
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Get only root departments
        if ($request->boolean('root_only')) {
            $query->whereNull('parent_id');
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $departments = $query->orderBy('sort_order')->get();

        return response()->json([
            'data' => $departments,
        ]);
    }

    /**
     * Get department tree structure
     */
    public function tree(): JsonResponse
    {
        $user = auth()->user();

        $departments = Department::where('organization_id', $user->organization_id)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with('children')->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $departments,
        ]);
    }

    /**
     * Get a specific department
     */
    public function show(Department $department): JsonResponse
    {
        $user = auth()->user();

        if ($department->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $department->load(['parent', 'children', 'head', 'positions', 'users']);

        return response()->json([
            'data' => $department,
        ]);
    }

    /**
     * Create a new department
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('departments.create')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'        => ['required', new TranslatableValue(max: 255)],
            'slug'        => 'required|string|max:255|unique:departments,slug',
            'description' => ['nullable', new TranslatableValue(max: 1000)],
            'parent_id'   => 'nullable|uuid|exists:departments,id',
            'head_id'     => 'nullable|uuid|exists:users,id',
            'code'        => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $department = Department::create([
             ...$validated,
            'organization_id' => $user->organization_id,
        ]);

        return response()->json([
            'message' => 'Department created successfully',
            'data'    => $department,
        ], 201);
    }

    /**
     * Update a department
     */
    public function update(Request $request, Department $department): JsonResponse
    {
        $user = auth()->user();

        if ($department->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $user->hasPermissionTo('departments.update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'        => ['sometimes', new TranslatableValue(max: 255)],
            'slug'        => 'sometimes|string|max:255|unique:departments,slug,' . $department->id,
            'description' => ['nullable', new TranslatableValue(max: 1000)],
            'parent_id'   => 'nullable|uuid|exists:departments,id',
            'head_id'     => 'nullable|uuid|exists:users,id',
            'code'        => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $department->update($validated);

        return response()->json([
            'message' => 'Department updated successfully',
            'data'    => $department,
        ]);
    }

    /**
     * Delete a department
     */
    public function destroy(Department $department): JsonResponse
    {
        $user = auth()->user();

        if ($department->organization_id !== $user->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $user->hasPermissionTo('departments.delete')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check for child departments
        if ($department->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with sub-departments',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully',
        ]);
    }
}
