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
                'name'            => 'Chief Executive Officer',
                'slug'            => 'ceo',
                'level'           => 10,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],
            [
                'name'            => 'Chief Technology Officer',
                'slug'            => 'cto',
                'level'           => 9,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],
            [
                'name'            => 'Chief Product Officer',
                'slug'            => 'cpo',
                'level'           => 9,
                'is_manager'      => true,
                'department_slug' => 'executive',
            ],

            // Engineering positions
            [
                'name'            => 'Engineering Manager',
                'slug'            => 'engineering-manager',
                'level'           => 7,
                'is_manager'      => true,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => 'Senior Software Engineer',
                'slug'            => 'senior-engineer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => 'Software Engineer',
                'slug'            => 'software-engineer',
                'level'           => 4,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => 'Junior Software Engineer',
                'slug'            => 'junior-engineer',
                'level'           => 3,
                'is_manager'      => false,
                'department_slug' => 'engineering',
            ],
            [
                'name'            => 'DevOps Engineer',
                'slug'            => 'devops-engineer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'devops',
            ],

            // Product positions
            [
                'name'            => 'Product Manager',
                'slug'            => 'product-manager',
                'level'           => 6,
                'is_manager'      => true,
                'department_slug' => 'product',
            ],
            [
                'name'            => 'Senior UX Designer',
                'slug'            => 'senior-ux-designer',
                'level'           => 5,
                'is_manager'      => false,
                'department_slug' => 'design',
            ],
            [
                'name'            => 'UX Designer',
                'slug'            => 'ux-designer',
                'level'           => 4,
                'is_manager'      => false,
                'department_slug' => 'design',
            ],

            // HR positions
            [
                'name'            => 'HR Manager',
                'slug'            => 'hr-manager',
                'level'           => 6,
                'is_manager'      => true,
                'department_slug' => 'hr',
            ],
            [
                'name'            => 'HR Specialist',
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
