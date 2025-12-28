<?php
namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org     = Organization::first();
        $manager = User::where('email', 'manager@ishyar.local')->first();
        $alice   = User::where('email', 'alice@ishyar.local')->first();
        $bob     = User::where('email', 'bob@ishyar.local')->first();
        $carol   = User::where('email', 'carol@ishyar.local')->first();

        if (! $org || ! $manager) {
            return;
        }

        // Sample tasks
        $tasks = [
            [
                'title'        => 'Set up development environment',
                'description'  => 'Configure local development environment with Docker and necessary tools',
                'type'         => 'situational',
                'status'       => 'completed',
                'priority'     => 'high',
                'creator_id'   => $manager->id,
                'assignee_id'  => $alice?->id,
                'progress'     => 100,
                'due_date'     => now()->subDays(5),
                'completed_at' => now()->subDays(3),
            ],
            [
                'title'        => 'Implement user authentication API',
                'description'  => 'Create login, register, and JWT token refresh endpoints',
                'type'         => 'situational',
                'status'       => 'completed',
                'priority'     => 'high',
                'creator_id'   => $manager->id,
                'assignee_id'  => $alice?->id,
                'progress'     => 100,
                'due_date'     => now()->subDays(2),
                'completed_at' => now()->subDays(1),
            ],
            [
                'title'       => 'Design dashboard UI mockups',
                'description' => 'Create high-fidelity mockups for the main dashboard interface',
                'type'        => 'situational',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'creator_id'  => $manager->id,
                'assignee_id' => $carol?->id,
                'progress'    => 60,
                'due_date'    => now()->addDays(2),
            ],
            [
                'title'       => 'Implement task management API',
                'description' => 'Create CRUD endpoints for tasks with filtering and pagination',
                'type'        => 'situational',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'creator_id'  => $manager->id,
                'assignee_id' => $bob?->id,
                'progress'    => 40,
                'due_date'    => now()->addDays(3),
            ],
            [
                'title'       => 'Write API documentation',
                'description' => 'Document all API endpoints using OpenAPI/Swagger specification',
                'type'        => 'situational',
                'status'      => 'pending',
                'priority'    => 'medium',
                'creator_id'  => $manager->id,
                'assignee_id' => $alice?->id,
                'progress'    => 0,
                'due_date'    => now()->addDays(7),
            ],
            [
                'title'       => 'Set up CI/CD pipeline',
                'description' => 'Configure GitHub Actions for automated testing and deployment',
                'type'        => 'situational',
                'status'      => 'pending',
                'priority'    => 'medium',
                'creator_id'  => $manager->id,
                'assignee_id' => $bob?->id,
                'progress'    => 0,
                'due_date'    => now()->addDays(10),
            ],
            [
                'title'           => 'Code review - Authentication module',
                'description'     => 'Review pull request for authentication implementation',
                'type'            => 'situational',
                'status'          => 'pending',
                'priority'        => 'high',
                'creator_id'      => $alice?->id,
                'assignee_id'     => $manager->id,
                'progress'        => 0,
                'due_date'        => now()->addDays(1),
                'approval_status' => 'pending',
            ],
            [
                'title'           => 'Daily standup meeting',
                'description'     => 'Team synchronization and progress updates',
                'type'            => 'routine',
                'status'          => 'pending',
                'priority'        => 'low',
                'creator_id'      => $manager->id,
                'assignee_id'     => $manager->id,
                'is_recurring'    => true,
                'recurrence_rule' => [
                    'frequency' => 'daily',
                    'interval'  => 1,
                    'days'      => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    'time'      => '09:00',
                ],
                'due_date'        => now()->addDays(1)->setTime(9, 0),
            ],
            [
                'title'           => 'Weekly code review session',
                'description'     => 'Review and discuss code quality improvements',
                'type'            => 'routine',
                'status'          => 'pending',
                'priority'        => 'medium',
                'creator_id'      => $manager->id,
                'assignee_id'     => $manager->id,
                'is_recurring'    => true,
                'recurrence_rule' => [
                    'frequency' => 'weekly',
                    'interval'  => 1,
                    'day'       => 'friday',
                    'time'      => '14:00',
                ],
                'due_date'        => now()->next('Friday')->setTime(14, 0),
            ],
            [
                'title'        => 'Implement frontend login page',
                'description'  => 'Create responsive login page with form validation',
                'type'         => 'situational',
                'status'       => 'completed',
                'priority'     => 'high',
                'creator_id'   => $manager->id,
                'assignee_id'  => $carol?->id,
                'progress'     => 100,
                'due_date'     => now()->subDays(1),
                'completed_at' => now()->subHours(12),
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                'organization_id' => $org->id,
                ...$taskData,
            ]);
        }
    }
}
