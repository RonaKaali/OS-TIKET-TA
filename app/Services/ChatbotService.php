<?php

namespace App\Services;

use App\Models\ChatbotResponse;
use Illuminate\Support\Str;

class ChatbotService
{
    protected AIAnalystService $aiService;

    public function __construct(AIAnalystService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Mencari response yang cocok berdasarkan pesan user
     *
     * @param string $message Pesan dari user
     * @return ChatbotResponse|null Response yang cocok atau null jika tidak ada
     */
    public function findResponse(string $message): ?ChatbotResponse
    {
        // Normalisasi pesan (lowercase, trim)
        $normalizedMessage = Str::lower(trim($message));

        // Ambil semua response aktif, diurutkan berdasarkan priority
        /** @var \Illuminate\Database\Eloquent\Collection<int, ChatbotResponse> $responses */
        $responses = ChatbotResponse::active()
            ->ordered()
            ->get();

        // Loop melalui semua response untuk mencari yang cocok
        foreach ($responses as $response) {
            $keyword = Str::lower(trim($response->keyword));

            // Cocokkan berdasarkan match_type
            $isMatch = match ($response->match_type) {
                'exact' => $normalizedMessage === $keyword,
                'starts_with' => Str::startsWith($normalizedMessage, $keyword),
                'contains' => Str::contains($normalizedMessage, $keyword),
                default => Str::contains($normalizedMessage, $keyword),
            };

            if ($isMatch) {
                return $response;
            }
        }

        // Jika tidak ada yang cocok, kembalikan null
        return null;
    }

    /**
     * Mendapatkan response untuk pesan user
     * Jika tidak ada yang cocok, kembalikan response dari AI Agent
     *
     * @param string $message Pesan dari user
     * @return string Response text
     */
    public function getResponse(string $message): string
    {
        $normalizedMessage = Str::lower(trim($message));
        
        // 1. Cek database terlebih dahulu (Customizable responses)
        $response = $this->findResponse($message);
        if ($response) {
            return $response->response;
        }

        // 2. Hardcoded FAQ untuk Cyber Security Reporting (Sidang Akhir Optimization)
        $faqs = [
            'halo' => "Halo! Saya **Assistant Kamu**. 🛡️ Ada yang bisa saya bantu terkait keamanan siber atau pelaporan insiden hari ini?",
            'pagi' => "Selamat pagi! **Assistant Kamu** di sini. Siap menjaga integritas data Anda. Apa ada aktivitas mencurigakan yang ingin dilaporkan?",
            'siang' => "Selamat siang! Tetap waspada. Saya **Assistant Kamu**, siap membantu proses pelaporan insiden Anda.",
            'sore' => "Selamat sore! Pastikan sesi Anda aman. Ada yang bisa **Assistant Kamu** bantu?",
            'malam' => "Selamat malam! Pemantauan keamanan tetap berjalan 24/7. Ada masalah teknis yang perlu saya analisis?",
            
            'cara buat laporan' => "🛡️ **Panduan Pelaporan Insiden Siber:**\n\n1. Login ke akun Anda.\n2. Klik menu **'Buat Tiket Baru'**.\n3. Pilih kategori insiden (misal: Malware, Phishing, Web Defacement).\n4. Isi judul dan deskripsi kronologi kejadian secara mendetail.\n5. Lampirkan bukti (screenshot/log) jika ada.\n6. Klik **'Kirim Laporan'**.\n\nTim CSIRT Kalsel akan segera melakukan investigasi menggunakan framework **NIST Respond**.",
            
            'lupa password' => "🔐 **Bantuan Akses (Identity Protection):**\n\nJika Anda lupa kata sandi, silakan gunakan fitur **'Lupa Password'** di halaman login atau hubungi administrator sistem melalui email di `admin-csirt@kalselprov.go.id` untuk verifikasi identitas manual.",
            
            'jenis insiden' => "📊 **Kategori Insiden (Taxonomy):**\n\n- **Web Defacement:** Perubahan visual situs tanpa izin.\n- **Malware:** Infeksi kode berbahaya (Ransomware, Trojan, dll).\n- **Phishing:** Upaya penipuan untuk mencuri kredensial.\n- **DDoS:** Serangan yang mengganggu aspek *Availability* layanan.\n- **Data Leak:** Kebocoran data yang melanggar aspek *Confidentiality*.",
            
            'apa itu zero trust' => "🛡️ **Arsitektur Zero Trust (ZTA):**\n\nPlatform ini menerapkan prinsip **Zero Trust**, yaitu 'Never Trust, Always Verify'. Kami memverifikasi setiap akses berdasarkan:\n- **Identity:** Siapa yang mengakses?\n- **Context:** Dari mana dan kapan akses dilakukan?\n- **Device:** Apakah perangkat aman?\n\nIni memastikan laporan insiden Anda tidak dapat diakses oleh pihak yang tidak berwenang.",
            
            'kontak' => "📞 **Kontak Darurat CSIRT Kalsel:**\n\n- **Email:** csirt@kalselprov.go.id\n- **Telepon:** (0511) 1234567\n- **Pusat Komando:** Banjarbaru, Kalimantan Selatan.\n\nKami siap membantu 24/7 untuk insiden keamanan siber kritis.",
            
            'status laporan' => "🔍 **Monitoring Insiden (NIST Detect):**\n\nAnda dapat mengecek status laporan di menu **'Lihat Tiket'**. Kami menggunakan tahapan status:\n- **Open:** Laporan diterima.\n- **In Progress:** Sedang dianalisis oleh tim CSIRT.\n- **Resolved:** Masalah telah ditangani.\n- **Closed:** Kasus ditutup.",
            
            'help' => "🤖 **Command Center Assistant Kamu:**\n\nCoba ketik kata kunci berikut:\n- `cara buat laporan`\n- `jenis insiden`\n- `zero trust`\n- `status laporan`\n- `kontak`\n- `nist` (untuk info framework)",
            
            'terima kasih' => "Sama-sama! 🙏 Senang bisa membantu. Tetaplah menjadi bagian dari pertahanan siber yang kuat! 🛡️"
        ];

        foreach ($faqs as $key => $reply) {
            if (Str::contains($normalizedMessage, $key)) {
                return $reply;
            }
        }

        // 3. Gunakan AI Analyst Service sebagai fallback cerdas (Agent Skills)
        return $this->aiService->getAnalysis($message);
    }
}

