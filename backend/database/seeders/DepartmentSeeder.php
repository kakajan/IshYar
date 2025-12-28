<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
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

        // Executive
        $executive = Department::create([
            'organization_id' => $org->id,
            'name'            => 'Executive',
            'slug'            => 'executive',
            'description'     => 'Executive leadership team',
            'code'            => 'EXEC',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        // Engineering
        $engineering = Department::create([
            'organization_id' => $org->id,
            'name'            => 'Engineering',
            'slug'            => 'engineering',
            'description'     => 'Software development and engineering',
            'code'            => 'ENG',
            'sort_order'      => 2,
            'is_active'       => true,
        ]);

        // Engineering sub-departments
        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => 'Backend',
            'slug'            => 'backend',
            'description'     => 'Backend development team',
            'code'            => 'ENG-BE',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => 'Frontend',
            'slug'            => 'frontend',
            'description'     => 'Frontend development team',
            'code'            => 'ENG-FE',
            'sort_order'      => 2,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => 'DevOps',
            'slug'            => 'devops',
            'description'     => 'DevOps and infrastructure team',
            'code'            => 'ENG-DO',
            'sort_order'      => 3,
            'is_active'       => true,
        ]);

        // Product
        $product = Department::create([
            'organization_id' => $org->id,
            'name'            => 'Product',
            'slug'            => 'product',
            'description'     => 'Product management and design',
            'code'            => 'PROD',
            'sort_order'      => 3,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $product->id,
            'name'            => 'Design',
            'slug'            => 'design',
            'description'     => 'UI/UX design team',
            'code'            => 'PROD-DES',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        // Operations
        Department::create([
            'organization_id' => $org->id,
            'name'            => 'Operations',
            'slug'            => 'operations',
            'description'     => 'Business operations',
            'code'            => 'OPS',
            'sort_order'      => 4,
            'is_active'       => true,
        ]);

        // Human Resources
        Department::create([
            'organization_id' => $org->id,
            'name'            => 'Human Resources',
            'slug'            => 'hr',
            'description'     => 'HR and talent management',
            'code'            => 'HR',
            'sort_order'      => 5,
            'is_active'       => true,
        ]);
    }
}
