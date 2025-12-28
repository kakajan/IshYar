<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        // User's task statistics
        $myTasks = [
            'total'       => Task::where('assignee_id', $user->id)->count(),
            'pending'     => Task::where('assignee_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'in_progress' => Task::where('assignee_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed'   => Task::where('assignee_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'overdue'     => Task::where('assignee_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Tasks due today
        $todayTasks = Task::where('assignee_id', $user->id)
            ->whereDate('due_date', today())
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('creator')
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get();

        // Recent tasks
        $recentTasks = Task::where('assignee_id', $user->id)
            ->with(['creator'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Tasks created by user pending approval
        $pendingApproval = Task::where('creator_id', $user->id)
            ->where('requires_approval', true)
            ->where('approval_status', 'pending')
            ->count();

        // Tasks awaiting my approval (if manager)
        $awaitingMyApproval = 0;
        if ($user->hasRole(['manager', 'admin', 'super-admin'])) {
            $awaitingMyApproval = Task::where('approver_id', $user->id)
                ->where('requires_approval', true)
                ->where('approval_status', 'pending')
                ->count();
        }

        // Weekly progress
        $weeklyProgress = $this->getWeeklyProgress($user);

        return response()->json([
            'data' => [
                'my_tasks'             => $myTasks,
                'today_tasks'          => $todayTasks,
                'recent_tasks'         => $recentTasks,
                'pending_approval'     => $pendingApproval,
                'awaiting_my_approval' => $awaitingMyApproval,
                'weekly_progress'      => $weeklyProgress,
            ],
        ]);
    }

    /**
     * Get team dashboard statistics (for managers)
     */
    public function team(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user->hasRole(['manager', 'admin', 'super-admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get team members
        $teamMemberIds = $user->directReports()->pluck('id');

        $teamStats = [
            'total_members' => $teamMemberIds->count(),
            'tasks'         => [
                'total'       => Task::whereIn('assignee_id', $teamMemberIds)->count(),
                'pending'     => Task::whereIn('assignee_id', $teamMemberIds)
                    ->where('status', 'pending')
                    ->count(),
                'in_progress' => Task::whereIn('assignee_id', $teamMemberIds)
                    ->where('status', 'in_progress')
                    ->count(),
                'completed'   => Task::whereIn('assignee_id', $teamMemberIds)
                    ->where('status', 'completed')
                    ->count(),
                'overdue'     => Task::whereIn('assignee_id', $teamMemberIds)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->where('due_date', '<', now())
                    ->count(),
            ],
        ];

        // Team member performance
        $teamPerformance = DB::table('tasks')
            ->select('assignee_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completed'))
            ->whereIn('assignee_id', $teamMemberIds)
            ->groupBy('assignee_id')
            ->get();

        return response()->json([
            'data' => [
                'team_stats'       => $teamStats,
                'team_performance' => $teamPerformance,
            ],
        ]);
    }

    /**
     * Get weekly progress data
     */
    private function getWeeklyProgress($user): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek   = now()->endOfWeek();

        $completedByDay = DB::table('tasks')
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('assignee_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DATE(completed_at)'))
            ->get()
            ->keyBy('date');

        $progress = [];
        for ($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
            $dateStr    = $date->format('Y-m-d');
            $progress[] = [
                'date'      => $dateStr,
                'day'       => $date->format('D'),
                'completed' => $completedByDay[$dateStr]->count ?? 0,
            ];
        }

        return $progress;
    }
}
