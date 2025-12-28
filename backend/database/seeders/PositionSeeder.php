<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::first();

        if (! $org) {
            return;
        }

        $positions = [
            // Executive positions
            [
                'name'            => ['en' => 'Chief Executive Officer', 'fa' => 'مدیرعامل'],
                'slug'            => 'ceo',
                'level'           => 10,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],
            [
                'name'            => ['en' => 'Chief Technology Officer', 'fa' => 'مدیر ارشد فناوری'],
                'slug'            => 'cto',
                'level'           => 9,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],
            [
                'name'            => ['en' => 'Chief Product Officer', 'fa' => 'مدیر ارشد محصول'],
                'slug'            => 'cpo',
                'level'           => 9,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],

            // Engineering positions
            [
                'name'            => ['en' => 'Engineering Manager', 'fa' => 'مدیر مهندسی'],
                'slug'            => 'engineering-manager',
                'level'           => 7,
                'is_manager'      => true,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => ['en' => 'Senior Software Engineer', 'fa' => 'مهندس نرم افزار ارشد'],
                'slug'            => 'senior-engineer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => ['en' => 'Software Engineer', 'fa' => 'مهندس نرم افزار'],
                'slug'            => 'software-engineer',
                'level'           => 4,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => ['en' => 'Junior Software Engineer', 'fa' => 'مهندس نرم افزار جونیور'],
                'slug'            => 'junior-engineer',
                'level'           => 3,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => ['en' => 'DevOps Engineer', 'fa' => 'مهندس دوآپز'],
                'slug'            => 'devops-engineer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'devops',
            ],

            // Product positions
            [
                'name'            => ['en' => 'Product Manager', 'fa' => 'مدیر محصول'],
                'slug'            => 'product-manager',
                'level'           => 6,
                'is_manager'      => true,
                'department_slug' => 'product',
            ],
            [
                'name'            => ['en' => 'Senior UX Designer', 'fa' => 'طراح تجربه کاربری ارشد'],
                'slug'            => 'senior-ux-designer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'design',
            ],
            [
                'name'            => ['en' => 'UX Designer', 'fa' => 'طراح تجربه کاربری'],
                'slug'            => 'ux-designer',
                'level'           => 4,
                'is_manager'      => false,
                'department_slug' => 'design',
            ],

            // HR positions
            [
                'name'            => ['en' => 'HR Manager', 'fa' => 'مدیر منابع انسانی'],
                'slug'            => 'hr-manager',
                'level'           => 6,
                'is_manager'      => true,
                'department_slug' => 'hr',
            ],
            [
                'name'            => ['en' => 'HR Specialist', 'fa' => 'کارشناس منابع انسانی'],
                'slug'            => 'hr-specialist',
                'level'           => 4,
                'is_manager'      => false,
                'department_slug' => 'hr',
            ],
        ];

        foreach ($positions as $positionData) {
            $department = Department::where('slug', $positionData['department_slug'])->first();

            Position::create([
                'organization_id' => $org->id,
                'department_id'   => $department?->id,
                'name'            => $positionData['name'],
                'slug'            => $positionData['slug'],
                'level'           => $positionData['level'],
                'is_manager'      => $positionData['is_manager'],
                'is_active'       => true,
            ]);
        }
    }
}
