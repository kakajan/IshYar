<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get list of users
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = User::query()
            ->with(['organization', 'department', 'position', 'roles'])
            ->where('organization_id', $user->organization_id);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by position
        if ($request->has('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = auth()->user();

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'organization_id' => $user->organization_id,
            'department_id' => $validated['department_id'] ?? null,
            'position_id' => $validated['position_id'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($validated['roles'])) {
            $newUser->syncRoles($validated['roles']);
        }

        return response()->json(['data' => $newUser], 201);
    }

    /**
     * Get a specific user
     */
    public function show(User $user): JsonResponse
    {
        $currentUser = auth()->user();

        // Users can only view users in their organization
        if ($user->organization_id !== $currentUser->organization_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $user->load(['organization', 'department', 'position', 'manager', 'roles']);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Get team members (direct reports)
     */
    public function team(Request $request): JsonResponse
    {
        $user = auth()->user();

        $team = User::where('manager_id', $user->id)
            ->with(['department', 'position'])
            ->get();

        return response()->json([
            'data' => $team,
        ]);
    }

    /**
     * Get colleagues in same department
     */
    public function colleagues(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->department_id) {
            return response()->json(['data' => []]);
        }

        $colleagues = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->with(['position'])
            ->get();

        return response()->json([
            'data' => $colleagues,
        ]);
    }
}
