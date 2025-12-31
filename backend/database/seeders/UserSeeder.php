<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::first();

        if (!$org) {
            return;
        }

        // Create Super Admin
        $superAdmin = User::create([
            'organization_id' => $org->id,
            'name' => 'Super Admin',
            'email' => 'admin@ishyar.local',
            'password' => 'password',
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super-admin');

        // Create Department Head / Manager
        $engineering = Department::where('slug', 'engineering')->first();
        $managerPosition = Position::where('slug', 'engineering-manager')->first();

        $manager = User::create([
            'organization_id' => $org->id,
            'department_id' => $engineering?->id,
            'position_id' => $managerPosition?->id,
            'name' => 'John Manager',
            'email' => 'manager@ishyar.local',
            'password' => 'password',
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Update department head
        if ($engineering) {
            $engineering->update(['head_id' => $manager->id]);
        }

        // Create Regular Employees
        $backend = Department::where('slug', 'backend')->first();
        $seniorPosition = Position::where('slug', 'senior-engineer')->first();
        $engineerPosition = Position::where('slug', 'software-engineer')->first();

        $employee1 = User::create([
            'organization_id' => $org->id,
            'department_id' => $backend?->id,
            'position_id' => $seniorPosition?->id,
            'manager_id' => $manager->id,
            'name' => 'Alice Developer',
            'email' => 'alice@ishyar.local',
            'password' => 'password',
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'is_active' => true,
        ]);
        $employee1->assignRole('employee');

        $employee2 = User::create([
            'organization_id' => $org->id,
            'department_id' => $backend?->id,
            'position_id' => $engineerPosition?->id,
            'manager_id' => $manager->id,
            'name' => 'Bob Developer',
            'email' => 'bob@ishyar.local',
            'password' => 'password',
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'is_active' => true,
        ]);
        $employee2->assignRole('employee');

        // Frontend developer
        $frontend = Department::where('slug', 'frontend')->first();

        $employee3 = User::create([
            'organization_id' => $org->id,
            'department_id' => $frontend?->id,
            'position_id' => $engineerPosition?->id,
            'manager_id' => $manager->id,
            'name' => 'Carol Frontend',
            'email' => 'carol@ishyar.local',
            'password' => 'password',
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'is_active' => true,
        ]);
        $employee3->assignRole('employee');
    }
}
