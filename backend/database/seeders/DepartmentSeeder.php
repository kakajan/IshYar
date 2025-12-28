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
            'name'            => ['en' => 'Executive', 'fa' => 'مدیریت ارشد'],
            'slug'            => 'executive',
            'description'     => ['en' => 'Executive leadership team', 'fa' => 'تیم رهبری و مدیریت ارشد'],
            'code'            => 'EXEC',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        // Engineering
        $engineering = Department::create([
            'organization_id' => $org->id,
            'name'            => ['en' => 'Engineering', 'fa' => 'مهندسی'],
            'slug'            => 'engineering',
            'description'     => ['en' => 'Software development and engineering', 'fa' => 'توسعه و مهندسی نرم‌افزار'],
            'code'            => 'ENG',
            'sort_order'      => 2,
            'is_active'       => true,
        ]);

        // Engineering sub-departments
        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => ['en' => 'Backend', 'fa' => 'بک‌اند'],
            'slug'            => 'backend',
            'description'     => ['en' => 'Backend development team', 'fa' => 'تیم توسعه بک‌اند'],
            'code'            => 'ENG-BE',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => ['en' => 'Frontend', 'fa' => 'فرانت‌اند'],
            'slug'            => 'frontend',
            'description'     => ['en' => 'Frontend development team', 'fa' => 'تیم توسعه فرانت‌اند'],
            'code'            => 'ENG-FE',
            'sort_order'      => 2,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $engineering->id,
            'name'            => ['en' => 'DevOps', 'fa' => 'دوآپز'],
            'slug'            => 'devops',
            'description'     => ['en' => 'DevOps and infrastructure team', 'fa' => 'تیم دوآپز و زیرساخت'],
            'code'            => 'ENG-DO',
            'sort_order'      => 3,
            'is_active'       => true,
        ]);

        // Product
        $product = Department::create([
            'organization_id' => $org->id,
            'name'            => ['en' => 'Product', 'fa' => 'محصول'],
            'slug'            => 'product',
            'description'     => ['en' => 'Product management and design', 'fa' => 'مدیریت و طراحی محصول'],
            'code'            => 'PROD',
            'sort_order'      => 3,
            'is_active'       => true,
        ]);

        Department::create([
            'organization_id' => $org->id,
            'parent_id'       => $product->id,
            'name'            => ['en' => 'Design', 'fa' => 'طراحی'],
            'slug'            => 'design',
            'description'     => ['en' => 'UI/UX design team', 'fa' => 'تیم طراحی UI/UX'],
            'code'            => 'PROD-DES',
            'sort_order'      => 1,
            'is_active'       => true,
        ]);

        // Operations
        Department::create([
            'organization_id' => $org->id,
            'name'            => ['en' => 'Operations', 'fa' => 'عملیات'],
            'slug'            => 'operations',
            'description'     => ['en' => 'Business operations', 'fa' => 'عملیات کسب‌وکار'],
            'code'            => 'OPS',
            'sort_order'      => 4,
            'is_active'       => true,
        ]);

        // Human Resources
        Department::create([
            'organization_id' => $org->id,
            'name'            => ['en' => 'Human Resources', 'fa' => 'منابع انسانی'],
            'slug'            => 'hr',
            'description'     => ['en' => 'HR and talent management', 'fa' => 'مدیریت منابع انسانی و استعدادها'],
            'code'            => 'HR',
            'sort_order'      => 5,
            'is_active'       => true,
        ]);
    }
}
