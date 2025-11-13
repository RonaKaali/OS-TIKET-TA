<?php

namespace Database\Seeders;

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
        // Pastikan RolePermissionSeeder sudah dijalankan terlebih dahulu
        $this->command->info('Creating default users...');

        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@csirt.kalselprov.go.id'],
            [
                'nama' => 'Super Admin CSIRT',
                'password' => Hash::make('password'),
                'email_terverifikasi_pada' => now(),
            ]
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
            $this->command->info('✓ Super Admin created and assigned role');
        } else {
            $this->command->info('✓ Super Admin already exists');
        }

        // 2. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin1@csirt.kalselprov.go.id'],
            [
                'nama' => 'Administrator CSIRT',
                'password' => Hash::make('password'),
                'email_terverifikasi_pada' => now(),
            ]
        );

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
            $this->command->info('✓ Admin created and assigned role');
        } else {
            $this->command->info('✓ Admin already exists');
        }

        // 3. Agent
        $agent = User::firstOrCreate(
            ['email' => 'agent@csirt.kalselprov.go.id'],
            [
                'nama' => 'Agent CSIRT',
                'password' => Hash::make('password'),
                'email_terverifikasi_pada' => now(),
            ]
        );

        if (!$agent->hasRole('Agent')) {
            $agent->assignRole('Agent');
            $this->command->info('✓ Agent created and assigned role');
        } else {
            $this->command->info('✓ Agent already exists');
        }

        // 4. Support Agent
        $supportAgent = User::firstOrCreate(
            ['email' => 'support@csirt.kalselprov.go.id'],
            [
                'nama' => 'Support Agent CSIRT',
                'password' => Hash::make('password'),
                'email_terverifikasi_pada' => now(),
            ]
        );

        if (!$supportAgent->hasRole('Support Agent')) {
            $supportAgent->assignRole('Support Agent');
            $this->command->info('✓ Support Agent created and assigned role');
        } else {
            $this->command->info('✓ Support Agent already exists');
        }

        // 5. Agent Tambahan (opsional)
        $agent2 = User::firstOrCreate(
            ['email' => 'agent2@csirt.kalselprov.go.id'],
            [
                'nama' => 'Agent CSIRT 2',
                'password' => Hash::make('password'),
                'email_terverifikasi_pada' => now(),
            ]
        );

        if (!$agent2->hasRole('Agent')) {
            $agent2->assignRole('Agent');
            $this->command->info('✓ Agent 2 created and assigned role');
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Default Users Created:');
        $this->command->info('========================================');
        $this->command->info('Super Admin:');
        $this->command->info('  Email: admin@csirt.kalselprov.go.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Admin:');
        $this->command->info('  Email: admin1@csirt.kalselprov.go.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Agent:');
        $this->command->info('  Email: agent@csirt.kalselprov.go.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Support Agent:');
        $this->command->info('  Email: support@csirt.kalselprov.go.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Agent 2:');
        $this->command->info('  Email: agent2@csirt.kalselprov.go.id');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Change default passwords after first login!');
        $this->command->info('========================================');
    }
}
