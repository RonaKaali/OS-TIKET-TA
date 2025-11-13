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
            ['nama' => 'Dinas Komunikasi dan Informatika'],
            ['nama' => 'Dinas Kesehatan'],
            ['nama' => 'Dinas Pendidikan'],
            ['nama' => 'Dinas Sosial'],
        ];

        foreach ($organizations as $org) {
            Organization::firstOrCreate(['nama' => $org['nama']], $org);
        }
        $this->command->info('✓ Organizations created');

        // 2. Departments
        $departments = [
            ['nama' => 'Keamanan Siber', 'email' => 'security@kalselprov.go.id', 'publik' => true],
            ['nama' => 'Infrastruktur IT', 'email' => 'infra@kalselprov.go.id', 'publik' => true],
            ['nama' => 'Aplikasi & Sistem', 'email' => 'apps@kalselprov.go.id', 'publik' => true],
            ['nama' => 'Network & Security', 'email' => 'network@kalselprov.go.id', 'publik' => true],
            ['nama' => 'Support Teknis', 'email' => 'support@kalselprov.go.id', 'publik' => true],
        ];

        $deptIds = [];
        foreach ($departments as $dept) {
            $d = Department::firstOrCreate(['nama' => $dept['nama']], $dept);
            $deptIds[] = $d->id;
        }
        $this->command->info('✓ Departments created');

        // 3. Help Topics
        $helpTopics = [
            ['nama' => 'Serangan Malware', 'id_departemen' => $deptIds[0]],
            ['nama' => 'Ransomware', 'id_departemen' => $deptIds[0]],
            ['nama' => 'Phishing Attack', 'id_departemen' => $deptIds[0]],
            ['nama' => 'Data Breach', 'id_departemen' => $deptIds[0]],
            ['nama' => 'Vulnerability Report', 'id_departemen' => $deptIds[0]],
            ['nama' => 'Server Down', 'id_departemen' => $deptIds[1]],
            ['nama' => 'Network Issue', 'id_departemen' => $deptIds[1]],
            ['nama' => 'Database Error', 'id_departemen' => $deptIds[2]],
            ['nama' => 'Aplikasi Error', 'id_departemen' => $deptIds[2]],
            ['nama' => 'Login Issue', 'id_departemen' => $deptIds[2]],
            ['nama' => 'Firewall Issue', 'id_departemen' => $deptIds[3]],
            ['nama' => 'VPN Problem', 'id_departemen' => $deptIds[3]],
            ['nama' => 'Email Issue', 'id_departemen' => $deptIds[4]],
            ['nama' => 'Printer Issue', 'id_departemen' => $deptIds[4]],
        ];

        $topicIds = [];
        foreach ($helpTopics as $topic) {
            $t = HelpTopic::firstOrCreate(
                ['nama' => $topic['nama'], 'id_departemen' => $topic['id_departemen']],
                $topic
            );
            $topicIds[] = $t->id;
        }
        $this->command->info('✓ Help Topics created');

        // 4. Status
        $statuses = [
            ['nama' => 'Terbuka', 'slug' => 'open', 'menutup' => false],
            ['nama' => 'Menunggu Pelapor', 'slug' => 'answered', 'menutup' => false],
            ['nama' => 'Ditugaskan', 'slug' => 'assigned', 'menutup' => false],
            ['nama' => 'Dalam Proses', 'slug' => 'in_progress', 'menutup' => false],
            ['nama' => 'Tertutup', 'slug' => 'closed', 'menutup' => true],
            ['nama' => 'Dibatalkan', 'slug' => 'cancelled', 'menutup' => true],
        ];

        $statusIds = [];
        foreach ($statuses as $status) {
            $s = Status::firstOrCreate(['slug' => $status['slug']], $status);
            $statusIds[] = $s->id;
        }
        $this->command->info('✓ Statuses created');

        // 5. Priorities
        $priorities = [
            ['nama' => 'Sangat Rendah', 'bobot' => 1],
            ['nama' => 'Rendah', 'bobot' => 2],
            ['nama' => 'Normal', 'bobot' => 3],
            ['nama' => 'Tinggi', 'bobot' => 4],
            ['nama' => 'Sangat Tinggi', 'bobot' => 5],
            ['nama' => 'Kritis', 'bobot' => 6],
        ];

        $priorityIds = [];
        foreach ($priorities as $priority) {
            $p = Priority::firstOrCreate(['nama' => $priority['nama']], $priority);
            $priorityIds[] = $p->id;
        }
        $this->command->info('✓ Priorities created');

        // 6. SLA Plans
        $slaPlans = [
            ['nama' => 'Standard (48 Jam)', 'jam_grace' => 48],
            ['nama' => 'Cepat (24 Jam)', 'jam_grace' => 24],
            ['nama' => 'Sangat Cepat (12 Jam)', 'jam_grace' => 12],
            ['nama' => 'Kritis (4 Jam)', 'jam_grace' => 4],
            ['nama' => 'Normal (72 Jam)', 'jam_grace' => 72],
        ];

        $slaIds = [];
        foreach ($slaPlans as $sla) {
            $s = SlaPlan::firstOrCreate(['nama' => $sla['nama']], $sla);
            $slaIds[] = $s->id;
        }
        $this->command->info('✓ SLA Plans created');

        // 7. Teams
        $teams = [
            ['nama' => 'Tim Keamanan Siber'],
            ['nama' => 'Tim Infrastruktur'],
            ['nama' => 'Tim Aplikasi'],
            ['nama' => 'Tim Support'],
            ['nama' => 'Tim Network'],
        ];

        foreach ($teams as $team) {
            Team::firstOrCreate(['nama' => $team['nama']], $team);
        }
        $this->command->info('✓ Teams created');

        // 8. Canned Responses
        $cannedResponses = [
            [
                'judul' => 'Laporan Diterima',
                'isi' => 'Terima kasih telah melaporkan insiden keamanan siber. Tim CSIRT akan segera meninjau laporan Anda dan memberikan respons sesuai dengan SLA yang berlaku.'
            ],
            [
                'judul' => 'Masalah Sedang Ditangani',
                'isi' => 'Laporan Anda sedang ditangani oleh tim teknis kami. Kami akan memberikan update segera setelah ada perkembangan.'
            ],
            [
                'judul' => 'Memerlukan Informasi Tambahan',
                'isi' => 'Untuk menangani laporan Anda dengan lebih baik, kami memerlukan informasi tambahan. Mohon kirimkan detail berikut:'
            ],
            [
                'judul' => 'Laporan Diselesaikan',
                'isi' => 'Laporan insiden siber Anda telah diselesaikan. Jika Anda memiliki pertanyaan lebih lanjut, silakan balas email ini atau buat laporan baru.'
            ],
            [
                'judul' => 'Penanganan Insiden Ransomware',
                'isi' => 'Terima kasih telah melaporkan insiden ransomware. Tim keamanan siber kami telah mengisolasi sistem yang terinfeksi. Langkah-langkah pencegahan telah diterapkan untuk mencegah penyebaran lebih lanjut.'
            ],
            [
                'judul' => 'Penanganan Phishing',
                'isi' => 'Terima kasih telah melaporkan email phishing. Email tersebut telah diblokir dan semua pengguna telah diberi peringatan. Mohon untuk tidak membuka link atau attachment dari email yang mencurigakan.'
            ],
        ];

        foreach ($cannedResponses as $canned) {
            CannedResponse::firstOrCreate(['judul' => $canned['judul']], $canned);
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
