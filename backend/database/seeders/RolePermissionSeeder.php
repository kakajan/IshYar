<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User permissions
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Organization permissions
            'organizations.view',
            'organizations.update',
            'organizations.settings',

            // Department permissions
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',

            // Position permissions
            'positions.view',
            'positions.create',
            'positions.update',
            'positions.delete',

            // Task permissions
            'tasks.view',
            'tasks.view-all',
            'tasks.create',
            'tasks.update',
            'tasks.delete',
            'tasks.assign',
            'tasks.approve',

            // Report permissions
            'reports.view',
            'reports.view-all',
            'reports.export',

            // Settings permissions
            'settings.view',
            'settings.update',

            // Module permissions
            'modules.view',
            'modules.install',
            'modules.configure',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles with permissions
        $roles = [
            'super-admin' => $permissions, // All permissions

            'admin'       => [
                'users.view', 'users.create', 'users.update',
                'organizations.view', 'organizations.update',
                'departments.view', 'departments.create', 'departments.update', 'departments.delete',
                'positions.view', 'positions.create', 'positions.update', 'positions.delete',
                'tasks.view', 'tasks.view-all', 'tasks.create', 'tasks.update', 'tasks.delete', 'tasks.assign', 'tasks.approve',
                'reports.view', 'reports.view-all', 'reports.export',
                'settings.view', 'settings.update',
            ],

            'manager'     => [
                'users.view',
                'departments.view',
                'positions.view',
                'tasks.view', 'tasks.view-all', 'tasks.create', 'tasks.update', 'tasks.assign', 'tasks.approve',
                'reports.view', 'reports.view-all', 'reports.export',
            ],

            'employee'    => [
                'users.view',
                'departments.view',
                'positions.view',
                'tasks.view', 'tasks.create', 'tasks.update',
                'reports.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
