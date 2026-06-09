<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Admin Panel
            'admin.panel',

            // Ticket Permissions
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.delete',
            'tickets.assign',
            'tickets.close',

            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Department Management
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $roles = [
            'Super Admin' => [
                'admin.panel',
                'tickets.view',
                'tickets.create',
                'tickets.update',
                'tickets.delete',
                'tickets.assign',
                'tickets.close',
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'departments.view',
                'departments.create',
                'departments.update',
                'departments.delete',
            ],
            'Admin' => [
                'admin.panel',
                'tickets.view',
                'tickets.create',
                'tickets.update',
                'tickets.assign',
                'tickets.close',
                'users.view',
                'users.create',
                'users.update',
                'departments.view',
                'departments.create',
                'departments.update',
            ],
            'Agent' => [
                'admin.panel',
                'tickets.view',
                'tickets.update',
            ],
            'Agent 1' => [
                'admin.panel',
                'tickets.view',
                'tickets.update',
            ],
            'Agent 2' => [
                'admin.panel',
                'tickets.view',
                'tickets.update',
            ],
            'Support Agent' => [
                'admin.panel',
                'tickets.view',
                'tickets.update',
            ],
            'User' => [
                // User biasa hanya bisa melihat dan membuat tiket sendiri (tidak ada permission khusus)
                // Tidak memiliki akses ke admin panel
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Assign Super Admin role to first user (if exists)
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole('Super Admin');
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles created: ' . implode(', ', array_keys($roles)));
    }
}
