<?php

namespace Database\Seeders;

use App\Models\{
    Department,
    HelpTopic,
    Status,
    Priority,
    SlaPlan,
    Team,
    Organization,
    CannedResponse
};
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating master data...');

        // 1. Organizations
        $organizations = [
            ['name' => 'Dinas Komunikasi dan Informatika'],
            ['name' => 'Dinas Kesehatan'],
            ['name' => 'Dinas Pendidikan'],
            ['name' => 'Dinas Sosial'],
        ];

        foreach ($organizations as $org) {
            Organization::firstOrCreate(['name' => $org['name']], $org);
        }
        $this->command->info('✓ Organizations created');

        // 2. Departments
        $departments = [
            ['name' => 'Keamanan Siber', 'email' => 'security@kalselprov.go.id', 'is_public' => true],
            ['name' => 'Infrastruktur IT', 'email' => 'infra@kalselprov.go.id', 'is_public' => true],
            ['name' => 'Aplikasi & Sistem', 'email' => 'apps@kalselprov.go.id', 'is_public' => true],
            ['name' => 'Network & Security', 'email' => 'network@kalselprov.go.id', 'is_public' => true],
            ['name' => 'Support Teknis', 'email' => 'support@kalselprov.go.id', 'is_public' => true],
        ];

        $deptIds = [];
        foreach ($departments as $dept) {
            $d = Department::firstOrCreate(['name' => $dept['name']], $dept);
            $deptIds[] = $d->id;
        }
        $this->command->info('✓ Departments created');

        // 3. Help Topics
        $helpTopics = [
            ['name' => 'Serangan Malware', 'department_id' => $deptIds[0]],
            ['name' => 'Ransomware', 'department_id' => $deptIds[0]],
            ['name' => 'Phishing Attack', 'department_id' => $deptIds[0]],
            ['name' => 'Data Breach', 'department_id' => $deptIds[0]],
            ['name' => 'Vulnerability Report', 'department_id' => $deptIds[0]],
            ['name' => 'Server Down', 'department_id' => $deptIds[1]],
            ['name' => 'Network Issue', 'department_id' => $deptIds[1]],
            ['name' => 'Database Error', 'department_id' => $deptIds[2]],
            ['name' => 'Aplikasi Error', 'department_id' => $deptIds[2]],
            ['name' => 'Login Issue', 'department_id' => $deptIds[2]],
            ['name' => 'Firewall Issue', 'department_id' => $deptIds[3]],
            ['name' => 'VPN Problem', 'department_id' => $deptIds[3]],
            ['name' => 'Email Issue', 'department_id' => $deptIds[4]],
            ['name' => 'Printer Issue', 'department_id' => $deptIds[4]],
        ];

        $topicIds = [];
        foreach ($helpTopics as $topic) {
            $t = HelpTopic::firstOrCreate(
                ['name' => $topic['name'], 'department_id' => $topic['department_id']],
                $topic
            );
            $topicIds[] = $t->id;
        }
        $this->command->info('✓ Help Topics created');

        // 4. Status
        $statuses = [
            ['name' => 'Terbuka', 'slug' => 'open', 'is_closed' => false],
            ['name' => 'Menunggu Pelapor', 'slug' => 'answered', 'is_closed' => false],
            ['name' => 'Ditugaskan', 'slug' => 'assigned', 'is_closed' => false],
            ['name' => 'Dalam Proses', 'slug' => 'in_progress', 'is_closed' => false],
            ['name' => 'Tertutup', 'slug' => 'closed', 'is_closed' => true],
            ['name' => 'Dibatalkan', 'slug' => 'cancelled', 'is_closed' => true],
        ];

        $statusIds = [];
        foreach ($statuses as $status) {
            $s = Status::firstOrCreate(['slug' => $status['slug']], $status);
            $statusIds[] = $s->id;
        }
        $this->command->info('✓ Statuses created');

        // 5. Priorities
        $priorities = [
            ['name' => 'Sangat Rendah', 'weight' => 1],
            ['name' => 'Rendah', 'weight' => 2],
            ['name' => 'Normal', 'weight' => 3],
            ['name' => 'Tinggi', 'weight' => 4],
            ['name' => 'Sangat Tinggi', 'weight' => 5],
            ['name' => 'Kritis', 'weight' => 6],
        ];

        $priorityIds = [];
        foreach ($priorities as $priority) {
            $p = Priority::firstOrCreate(['name' => $priority['name']], $priority);
            $priorityIds[] = $p->id;
        }
        $this->command->info('✓ Priorities created');

        // 6. SLA Plans
        $slaPlans = [
            ['name' => 'Standard (48 Jam)', 'grace_hours' => 48],
            ['name' => 'Cepat (24 Jam)', 'grace_hours' => 24],
            ['name' => 'Sangat Cepat (12 Jam)', 'grace_hours' => 12],
            ['name' => 'Kritis (4 Jam)', 'grace_hours' => 4],
            ['name' => 'Normal (72 Jam)', 'grace_hours' => 72],
        ];

        $slaIds = [];
        foreach ($slaPlans as $sla) {
            $s = SlaPlan::firstOrCreate(['name' => $sla['name']], $sla);
            $slaIds[] = $s->id;
        }
        $this->command->info('✓ SLA Plans created');

        // 7. Teams
        $teams = [
            ['name' => 'Tim Keamanan Siber'],
            ['name' => 'Tim Infrastruktur'],
            ['name' => 'Tim Aplikasi'],
            ['name' => 'Tim Support'],
            ['name' => 'Tim Network'],
        ];

        foreach ($teams as $team) {
            Team::firstOrCreate(['name' => $team['name']], $team);
        }
        $this->command->info('✓ Teams created');

        // 8. Canned Responses
        $cannedResponses = [
            [
                'title' => 'Laporan Diterima',
                'body' => 'Terima kasih telah melaporkan insiden keamanan siber. Tim CSIRT akan segera meninjau laporan Anda dan memberikan respons sesuai dengan SLA yang berlaku.'
            ],
            [
                'title' => 'Masalah Sedang Ditangani',
                'body' => 'Laporan Anda sedang ditangani oleh tim teknis kami. Kami akan memberikan update segera setelah ada perkembangan.'
            ],
            [
                'title' => 'Memerlukan Informasi Tambahan',
                'body' => 'Untuk menangani laporan Anda dengan lebih baik, kami memerlukan informasi tambahan. Mohon kirimkan detail berikut:'
            ],
            [
                'title' => 'Laporan Diselesaikan',
                'body' => 'Laporan insiden siber Anda telah diselesaikan. Jika Anda memiliki pertanyaan lebih lanjut, silakan balas email ini atau buat laporan baru.'
            ],
            [
                'title' => 'Penanganan Insiden Ransomware',
                'body' => 'Terima kasih telah melaporkan insiden ransomware. Tim keamanan siber kami telah mengisolasi sistem yang terinfeksi. Langkah-langkah pencegahan telah diterapkan untuk mencegah penyebaran lebih lanjut.'
            ],
            [
                'title' => 'Penanganan Phishing',
                'body' => 'Terima kasih telah melaporkan email phishing. Email tersebut telah diblokir dan semua pengguna telah diberi peringatan. Mohon untuk tidak membuka link atau attachment dari email yang mencurigakan.'
            ],
        ];

        foreach ($cannedResponses as $canned) {
            CannedResponse::firstOrCreate(['title' => $canned['title']], $canned);
        }
        $this->command->info('✓ Canned Responses created');

        $this->command->info('');
        $this->command->info('Master data created successfully!');
        $this->command->info('Summary:');
        $this->command->info('  - Organizations: ' . Organization::count());
        $this->command->info('  - Departments: ' . Department::count());
        $this->command->info('  - Help Topics: ' . HelpTopic::count());
        $this->command->info('  - Statuses: ' . Status::count());
        $this->command->info('  - Priorities: ' . Priority::count());
        $this->command->info('  - SLA Plans: ' . SlaPlan::count());
        $this->command->info('  - Teams: ' . Team::count());
        $this->command->info('  - Canned Responses: ' . CannedResponse::count());
    }
}
