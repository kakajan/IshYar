<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Rules\TranslatableValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Get the current user's organization
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();

        $organization = Organization::with(['departments', 'positions'])
            ->find($user->organization_id);

        if (! $organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        return response()->json([
            'data' => $organization,
        ]);
    }

    /**
     * Update organization settings
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('organizations.update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $organization = Organization::find($user->organization_id);

        if (! $organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        $validated = $request->validate([
            'name'             => ['sometimes', new TranslatableValue(max: 255)],
            'description'      => ['nullable', new TranslatableValue(max: 1000)],
            'timezone'         => 'sometimes|string|timezone',
            'default_locale'   => 'sometimes|string|in:en,fa',
            'default_currency' => 'sometimes|string|max:3',
            'settings'         => 'nullable|array',
        ]);

        $organization->update($validated);

        return response()->json([
            'message' => 'Organization updated successfully',
            'data'    => $organization,
        ]);
    }

    /**
     * Get organization statistics
     */
    public function statistics(): JsonResponse
    {
        $user         = auth()->user();
        $organization = Organization::find($user->organization_id);

        if (! $organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        $stats = [
            'total_users'       => $organization->users()->count(),
            'active_users'      => $organization->users()->where('is_active', true)->count(),
            'departments_count' => $organization->departments()->count(),
            'positions_count'   => $organization->positions()->count(),
            'tasks'             => [
                'total'       => $organization->tasks()->count(),
                'pending'     => $organization->tasks()->where('status', 'pending')->count(),
                'in_progress' => $organization->tasks()->where('status', 'in_progress')->count(),
                'completed'   => $organization->tasks()->where('status', 'completed')->count(),
            ],
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}
