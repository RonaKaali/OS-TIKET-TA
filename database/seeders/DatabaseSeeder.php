<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Seed default users (Super Admin, Admin, Agent, Support Agent)
        $this->call(UserSeeder::class);

        // Seed master data (Departments, Help Topics, Status, Priority, SLA, Teams, etc.)
        $this->call(MasterDataSeeder::class);

        // Seed sample tickets with threads
        $this->call(TicketSeeder::class);

        // Seed chatbot responses
        $this->call(ChatbotResponseSeeder::class);
    }
}
