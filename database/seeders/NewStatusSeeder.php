<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class NewStatusSeeder extends Seeder
{
    public function run()
    {
        $status = [
            'name' => 'Menunggu Verifikasi Kepala Bidang',
            'slug' => 'menunggu_verifikasi_kepala_bidang',
            'is_closed' => false,
        ];

        Status::firstOrCreate(['slug' => $status['slug']], $status);
        
        $this->command->info('New status "Menunggu Verifikasi Kepala Bidang" created successfully.');
    }
}
