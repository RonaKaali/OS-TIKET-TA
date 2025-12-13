<?php

namespace Database\Seeders;

use App\Models\ChatbotResponse;
use Illuminate\Database\Seeder;

class ChatbotResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini berisi contoh data chatbot responses.
     * Anda bisa menyesuaikan atau menambah data sesuai kebutuhan.
     */
    public function run(): void
    {
        $responses = [
            // === SAPAAN UMUM ===
            [
                'keyword' => 'halo',
                'response' => "Halo! Selamat datang di CSIRT Kalselprov. 😊\n\nSaya siap membantu Anda dengan:\n- Membuat tiket baru\n- Mengecek status tiket\n- Memberikan informasi\n\nKetik /help untuk bantuan lebih lanjut.",
                'match_type' => 'contains',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'keyword' => 'hi',
                'response' => "Hi! Selamat datang di CSIRT Kalselprov. 😊\n\nSaya siap membantu Anda. Ketik /help untuk melihat daftar perintah.",
                'match_type' => 'contains',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'keyword' => 'selamat pagi',
                'response' => "Selamat pagi! 🌅\n\nTerima kasih telah menghubungi CSIRT Kalselprov. Ada yang bisa saya bantu hari ini?\n\nKetik /help untuk melihat daftar perintah.",
                'match_type' => 'contains',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'keyword' => 'selamat siang',
                'response' => "Selamat siang! ☀️\n\nTerima kasih telah menghubungi CSIRT Kalselprov. Ada yang bisa saya bantu?\n\nKetik /help untuk melihat daftar perintah.",
                'match_type' => 'contains',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'keyword' => 'selamat malam',
                'response' => "Selamat malam! 🌙\n\nTerima kasih telah menghubungi CSIRT Kalselprov. Ada yang bisa saya bantu?\n\nKetik /help untuk melihat daftar perintah.",
                'match_type' => 'contains',
                'priority' => 50,
                'is_active' => true,
            ],

            // === PERINTAH BOT ===
            [
                'keyword' => '/start',
                'response' => "👋 *Selamat datang di CSIRT Kalselprov Bot!*\n\nSaya siap membantu Anda dengan:\n✅ Membuat tiket baru\n✅ Mengecek status tiket\n✅ Memberikan informasi\n\nKetik /help untuk melihat daftar perintah lengkap.\n\nSelamat menggunakan! 😊",
                'match_type' => 'exact',
                'priority' => 100,
                'is_active' => true,
            ],
            [
                'keyword' => '/help',
                'response' => "📋 *Daftar Perintah:*\n\n/start - Memulai bot\n/help - Menampilkan bantuan ini\n/tiket - Membuat tiket baru\n/status - Cek status tiket\n/info - Informasi tentang CSIRT\n\n*Atau langsung ketik pertanyaan Anda!*\n\nContoh:\n- \"cara buat tiket\"\n- \"info CSIRT\"\n- \"bantuan\"",
                'match_type' => 'exact',
                'priority' => 100,
                'is_active' => true,
            ],
            [
                'keyword' => '/info',
                'response' => "ℹ️ *Informasi CSIRT Kalselprov*\n\nCSIRT (Computer Security Incident Response Team) Kalselprov adalah tim yang bertanggung jawab untuk menangani insiden keamanan siber di lingkungan pemerintah Kalimantan Selatan.\n\n*Layanan kami:*\n- Penanganan insiden keamanan siber\n- Laporan dan pelacakan tiket\n- Konsultasi keamanan informasi\n\nUntuk informasi lebih lanjut, kunjungi portal kami atau hubungi admin.",
                'match_type' => 'exact',
                'priority' => 100,
                'is_active' => true,
            ],

            // === CARA BUAT TIKET ===
            [
                'keyword' => 'cara buat tiket',
                'response' => "📝 *Cara Membuat Tiket:*\n\n1. Login ke portal CSIRT Kalselprov\n2. Klik menu \"Buat Tiket Baru\"\n3. Isi form dengan lengkap:\n   - Subject/Pokok permasalahan\n   - Deskripsi masalah secara detail\n   - Priority (Low, Medium, High, Critical)\n   - Kategori/Departemen\n4. Klik Submit untuk mengirim tiket\n\nAtau hubungi admin untuk bantuan lebih lanjut.\n\nKetik /help untuk perintah lainnya.",
                'match_type' => 'contains',
                'priority' => 80,
                'is_active' => true,
            ],
            [
                'keyword' => 'buat tiket',
                'response' => "Untuk membuat tiket baru:\n\n1. Login ke portal CSIRT\n2. Klik \"Buat Tiket Baru\"\n3. Isi form dengan lengkap\n4. Submit tiket\n\nAtau ketik \"cara buat tiket\" untuk panduan lengkap.",
                'match_type' => 'contains',
                'priority' => 70,
                'is_active' => true,
            ],
            [
                'keyword' => 'tiket baru',
                'response' => "Untuk membuat tiket baru, silakan login ke portal CSIRT dan klik menu \"Buat Tiket Baru\".\n\nKetik \"cara buat tiket\" untuk panduan lengkap.",
                'match_type' => 'contains',
                'priority' => 70,
                'is_active' => true,
            ],

            // === CEK STATUS TIKET ===
            [
                'keyword' => 'status tiket',
                'response' => "🔍 *Cek Status Tiket*\n\nUntuk mengecek status tiket Anda:\n\n1. Login ke portal CSIRT\n2. Masukkan nomor tiket Anda\n3. Lihat detail status dan update terbaru\n\nAtau hubungi admin jika mengalami kendala.\n\nKetik /help untuk perintah lainnya.",
                'match_type' => 'contains',
                'priority' => 80,
                'is_active' => true,
            ],
            [
                'keyword' => 'cek status',
                'response' => "Untuk mengecek status tiket, silakan login ke portal CSIRT dan masukkan nomor tiket Anda.\n\nKetik \"status tiket\" untuk informasi lebih lanjut.",
                'match_type' => 'contains',
                'priority' => 70,
                'is_active' => true,
            ],

            // === BANTUAN ===
            [
                'keyword' => 'bantuan',
                'response' => "💬 Saya siap membantu Anda!\n\nAnda bisa menanyakan:\n- Cara membuat tiket\n- Status tiket Anda\n- Informasi tentang CSIRT\n- Dan lainnya\n\nKetik /help untuk melihat daftar perintah lengkap.\n\nAtau langsung ketik pertanyaan Anda!",
                'match_type' => 'contains',
                'priority' => 60,
                'is_active' => true,
            ],
            [
                'keyword' => 'help',
                'response' => "Saya siap membantu! Ketik /help untuk melihat daftar perintah lengkap.\n\nAtau ketik pertanyaan Anda langsung, seperti:\n- \"cara buat tiket\"\n- \"status tiket\"\n- \"info\"",
                'match_type' => 'contains',
                'priority' => 60,
                'is_active' => true,
            ],

            // === INFORMASI ===
            [
                'keyword' => 'info',
                'response' => "ℹ️ Informasi CSIRT Kalselprov:\n\nCSIRT adalah tim yang menangani insiden keamanan siber di lingkungan pemerintah Kalimantan Selatan.\n\n*Layanan:*\n- Penanganan insiden keamanan siber\n- Laporan dan pelacakan tiket\n- Konsultasi keamanan\n\nKetik /info untuk informasi lebih lengkap.",
                'match_type' => 'contains',
                'priority' => 60,
                'is_active' => true,
            ],

            // === RESPONSE FALLBACK ===
            [
                'keyword' => 'tidak tahu',
                'response' => "Maaf, saya tidak mengerti pertanyaan Anda. 😔\n\nSilakan coba salah satu dari:\n- Ketik /help untuk melihat daftar perintah\n- Ketik \"cara buat tiket\" untuk panduan\n- Ketik \"bantuan\" untuk bantuan\n- Hubungi admin untuk bantuan lebih lanjut\n\nTerima kasih!",
                'match_type' => 'contains',
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'keyword' => 'tidak mengerti',
                'response' => "Maaf, saya belum memahami pertanyaan Anda. 😔\n\nSilakan ketik /help untuk melihat daftar perintah yang tersedia.\n\nAtau coba format pertanyaan Anda dengan lebih spesifik, seperti:\n- \"cara buat tiket\"\n- \"info CSIRT\"\n- \"status tiket\"",
                'match_type' => 'contains',
                'priority' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($responses as $response) {
            ChatbotResponse::create($response);
        }

        $this->command->info('✅ Chatbot responses berhasil di-seed!');
        $this->command->info('📝 Total ' . count($responses) . ' responses telah ditambahkan.');
    }
}

