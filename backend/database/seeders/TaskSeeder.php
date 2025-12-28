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
                'title'        => ['en' => 'Set up development environment', 'fa' => 'راه اندازی محیط توسعه'],
                'description'  => ['en' => 'Configure local development environment with Docker and necessary tools', 'fa' => 'پیکربندی محیط توسعه محلی با Docker و ابزارهای موردنیاز'],
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
                'title'        => ['en' => 'Implement user authentication API', 'fa' => 'پیاده سازی API احراز هویت کاربر'],
                'description'  => ['en' => 'Create login, register, and JWT token refresh endpoints', 'fa' => 'ایجاد endpointهای ورود، ثبت نام و نوسازی توکن JWT'],
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
                'title'       => ['en' => 'Design dashboard UI mockups', 'fa' => 'طراحی ماکاپ های UI داشبورد'],
                'description' => ['en' => 'Create high-fidelity mockups for the main dashboard interface', 'fa' => 'ایجاد ماکاپ های با جزئیات بالا برای رابط داشبورد اصلی'],
                'type'        => 'situational',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'creator_id'  => $manager->id,
                'assignee_id' => $carol?->id,
                'progress'    => 60,
                'due_date'    => now()->addDays(2),
            ],
            [
                'title'       => ['en' => 'Implement task management API', 'fa' => 'پیاده سازی API مدیریت تسک ها'],
                'description' => ['en' => 'Create CRUD endpoints for tasks with filtering and pagination', 'fa' => 'ایجاد endpointهای CRUD برای تسک ها با فیلتر و صفحه بندی'],
                'type'        => 'situational',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'creator_id'  => $manager->id,
                'assignee_id' => $bob?->id,
                'progress'    => 40,
                'due_date'    => now()->addDays(3),
            ],
            [
                'title'       => ['en' => 'Write API documentation', 'fa' => 'نوشتن مستندات API'],
                'description' => ['en' => 'Document all API endpoints using OpenAPI/Swagger specification', 'fa' => 'مستندسازی تمام endpointهای API با OpenAPI/Swagger'],
                'type'        => 'situational',
                'status'      => 'pending',
                'priority'    => 'medium',
                'creator_id'  => $manager->id,
                'assignee_id' => $alice?->id,
                'progress'    => 0,
                'due_date'    => now()->addDays(7),
            ],
            [
                'title'       => ['en' => 'Set up CI/CD pipeline', 'fa' => 'راه اندازی پایپ لاین CI/CD'],
                'description' => ['en' => 'Configure GitHub Actions for automated testing and deployment', 'fa' => 'پیکربندی GitHub Actions برای تست و استقرار خودکار'],
                'type'        => 'situational',
                'status'      => 'pending',
                'priority'    => 'medium',
                'creator_id'  => $manager->id,
                'assignee_id' => $bob?->id,
                'progress'    => 0,
                'due_date'    => now()->addDays(10),
            ],
            [
                'title'           => ['en' => 'Code review - Authentication module', 'fa' => 'بازبینی کد - ماژول احراز هویت'],
                'description'     => ['en' => 'Review pull request for authentication implementation', 'fa' => 'بازبینی Pull Request مربوط به پیاده سازی احراز هویت'],
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
                'title'           => ['en' => 'Daily standup meeting', 'fa' => 'جلسه روزانه استندآپ'],
                'description'     => ['en' => 'Team synchronization and progress updates', 'fa' => 'هماهنگی تیم و به روزرسانی پیشرفت ها'],
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
                'title'           => ['en' => 'Weekly code review session', 'fa' => 'جلسه هفتگی بازبینی کد'],
                'description'     => ['en' => 'Review and discuss code quality improvements', 'fa' => 'بازبینی و گفتگو درباره بهبود کیفیت کد'],
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
                'title'        => ['en' => 'Implement frontend login page', 'fa' => 'پیاده سازی صفحه ورود فرانت اند'],
                'description'  => ['en' => 'Create responsive login page with form validation', 'fa' => 'ایجاد صفحه ورود واکنش گرا با اعتبارسنجی فرم'],
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
