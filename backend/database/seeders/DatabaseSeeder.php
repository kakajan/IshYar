<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            TaskSeeder::class,
            IranianHolidaysSeeder::class,
        ]);
    }
}
