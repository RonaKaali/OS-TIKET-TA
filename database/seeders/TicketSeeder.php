<?php

namespace Database\Seeders;

use App\Models\{
    Ticket,
    TicketThread,
    Department,
    HelpTopic,
    Status,
    Priority,
    SlaPlan,
    User
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample tickets...');

        // Get master data
        $departments = Department::all();
        $topics = HelpTopic::all();
        $statuses = Status::all()->keyBy('slug');
        $priorities = Priority::all();
        $slaPlans = SlaPlan::all();

        // Get agents - users with Admin or Agent role
        $agents = User::whereHas('roles', function ($q) {
            $q->whereIn('roles.name', ['Super Admin', 'Admin', 'Agent']);
        })->get();

        // Fallback: if no agents found, get all users
        if ($agents->isEmpty()) {
            $agents = User::all();
        }

        if ($departments->isEmpty() || $topics->isEmpty() || $statuses->isEmpty()) {
            $this->command->warn('⚠️  Master data not found. Please run MasterDataSeeder first!');
            return;
        }

        if ($agents->isEmpty()) {
            $this->command->warn('⚠️  No agents found. Please run UserSeeder first!');
            return;
        }

        // Sample ticket subjects
        $subjects = [
            'Serangan Ransomware pada Server Utama',
            'Phishing Email Masuk ke Semua User',
            'Kebocoran Data Pribadi Pegawai',
            'Vulnerability pada Sistem Aplikasi',
            'Server Database Down',
            'Network Slow di Gedung A',
            'Aplikasi Login Error',
            'Firewall Blocking Legitimate Traffic',
            'Email Server Tidak Bisa Kirim',
            'VPN Connection Problem',
            'Malware Detected pada PC Staf',
            'Suspicious Activity di Network',
            'Data Backup Failed',
            'Aplikasi E-Government Error',
            'Printer Network Tidak Bisa Diakses',
        ];

        // Sample messages
        $messages = [
            'Saya menemukan serangan ransomware pada server utama. File-file penting sudah terenkripsi. Mohon segera ditangani.',
            'Ada email phishing yang masuk ke semua user di organisasi. Email tersebut meminta login credentials.',
            'Terjadi kebocoran data pribadi pegawai. Data seperti nama, email, dan nomor telepon terlihat di publik.',
            'Saya menemukan vulnerability pada sistem aplikasi yang bisa dieksploitasi oleh attacker.',
            'Server database tiba-tiba down dan tidak bisa diakses. Aplikasi tidak bisa berfungsi normal.',
            'Network di gedung A sangat lambat. Banyak user yang mengeluhkan koneksi internet.',
            'Aplikasi login error, tidak bisa masuk meskipun password sudah benar.',
            'Firewall memblokir traffic yang legitimate. Beberapa layanan tidak bisa diakses.',
            'Email server tidak bisa mengirim email. Semua email yang dikirim gagal.',
            'VPN connection sering putus. User kesulitan untuk remote access.',
            'Malware terdeteksi pada PC staf. Antivirus sudah menghapus tapi perlu pengecekan lebih lanjut.',
            'Ada aktivitas mencurigakan di network. Traffic aneh dari beberapa IP address.',
            'Data backup gagal. Backup otomatis tidak berjalan dengan baik.',
            'Aplikasi E-Government error. User tidak bisa mengakses beberapa fitur.',
            'Printer network tidak bisa diakses. Semua PC tidak bisa print ke printer tersebut.',
        ];

        // Sample replies
        $replies = [
            'Terima kasih atas laporannya. Tim keamanan siber kami sedang menangani kasus ini. Sistem yang terinfeksi telah diisolasi.',
            'Email phishing telah diblokir dan semua user telah diberi peringatan. Kami juga telah mengupdate firewall rules.',
            'Kebocoran data sedang ditangani. Kami telah mengubah password dan mengaktifkan 2FA untuk semua akun yang terpengaruh.',
            'Vulnerability telah di-patch. Aplikasi akan di-update dalam waktu dekat.',
            'Server database telah direstart dan kembali normal. Monitoring akan dilakukan untuk memastikan stabilitas.',
            'Network issue telah diperbaiki. Router telah di-restart dan koneksi sudah kembali normal.',
            'Masalah login telah diperbaiki. Silakan coba login lagi.',
            'Firewall rules telah diperbarui. Traffic legitimate sekarang bisa lewat dengan normal.',
            'Email server telah diperbaiki. Masalah terjadi pada konfigurasi SMTP yang sudah diperbaiki.',
            'VPN connection issue telah diselesaikan. Server VPN telah di-restart.',
            'PC yang terinfeksi telah diisolasi dan di-clean. Scan mendalam telah dilakukan.',
            'Aktivitas mencurigakan sedang diselidiki. IP yang mencurigakan telah diblokir.',
            'Backup system telah diperbaiki. Backup otomatis sekarang berjalan normal.',
            'Aplikasi E-Government telah di-update. Error sudah diperbaiki.',
            'Printer network telah di-reset. Sekarang sudah bisa diakses dari semua PC.',
        ];

        $ticketsCreated = 0;
        $openStatus = $statuses['open'];
        $answeredStatus = $statuses['answered'];
        $closedStatus = $statuses['closed'];
        $inProgressStatus = $statuses['in_progress'] ?? $openStatus;

        // Create tickets with different statuses
        for ($i = 0; $i < count($subjects); $i++) {
            $department = $departments->random();
            $topic = $topics->where('id_departemen', $department->id)->first() ?? $topics->random();
            $priority = $priorities->random();
            $sla = $slaPlans->random();
            $agent = $agents->random();

            // Determine status based on index
            if ($i < 5) {
                $status = $openStatus; // First 5: Open
            } elseif ($i < 8) {
                $status = $answeredStatus; // Next 3: Answered
            } elseif ($i < 12) {
                $status = $inProgressStatus; // Next 4: In Progress
            } else {
                $status = $closedStatus; // Last 3: Closed
            }

            // Assign agent for non-open tickets
            $assignedTo = ($status->slug !== 'open') ? $agent->id : null;

            // Generate ticket number manually
            $prefix = env('TICKET_NUMBER_PREFIX', 'CSIRT');
            $length = 6;
            $next = str_pad((string) (Ticket::max('id') + 1 ?? 1), $length, '0', STR_PAD_LEFT);
            $ticketNumber = "{$prefix}-{$next}";

            // Create ticket
            $ticket = Ticket::create([
                'uuid' => (string) Str::uuid(),
                'ticket_number' => $ticketNumber,
                'subject' => $subjects[$i],
                'reporter_email' => 'user' . ($i + 1) . '@example.com',
                'reporter_name' => 'User ' . ($i + 1),
                'department_id' => $department->id,
                'help_topic_id' => $topic->id,
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'sla_plan_id' => $sla->id,
                'assigned_to' => $assignedTo,
                'due_at' => now()->addHours($sla->grace_hours),
                'closed_at' => ($status->slug === 'closed') ? now()->subDays(rand(1, 30)) : null,
            ]);

            // Create initial thread (message from requester)
            $initialThread = TicketThread::create([
                'ticket_id' => $ticket->id,
                'type' => 'message',
                'user_id' => null,
                'body' => $messages[$i],
            ]);

            // Create reply threads for answered/in-progress/closed tickets
            if ($status->slug !== 'open') {
                // Reply from agent
                TicketThread::create([
                    'ticket_id' => $ticket->id,
                    'type' => 'reply',
                    'user_id' => $agent->id,
                    'body' => $replies[$i],
                ]);

                // Add more threads for closed tickets
                if ($status->slug === 'closed') {
                    // Final reply from agent
                    TicketThread::create([
                        'ticket_id' => $ticket->id,
                        'type' => 'reply',
                        'user_id' => $agent->id,
                        'body' => 'Laporan insiden siber telah diselesaikan. Jika ada pertanyaan lebih lanjut, silakan buat laporan baru.',
                    ]);
                }
            }

            $ticketsCreated++;
        }

        $this->command->info('');
        $this->command->info('✓ Sample tickets created: ' . $ticketsCreated);
        $this->command->info('');
        $this->command->info('Ticket Status Summary:');
        $this->command->info('  - Open: ' . Ticket::where('status_id', $openStatus->id)->count());
        $this->command->info('  - Answered: ' . Ticket::where('status_id', $answeredStatus->id)->count());
        $this->command->info('  - In Progress: ' . Ticket::where('status_id', $inProgressStatus->id)->count());
        $this->command->info('  - Closed: ' . Ticket::where('status_id', $closedStatus->id)->count());
        $this->command->info('');
        $this->command->info('Total Threads: ' . TicketThread::count());
    }
}
