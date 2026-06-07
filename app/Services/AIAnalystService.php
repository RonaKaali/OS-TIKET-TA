<?php

namespace App\Services;

use Illuminate\Support\Str;

class AIAnalystService
{
    /**
     * Analisis & solusi praktis untuk pelapor.
     */
    public function getAnalysis(string $message): array
    {
        $input = Str::lower($message);

        $scenarios = [
            [
                'triggers' => ['phishing', 'penipuan', 'email mencurigakan', 'link palsu', 'sms curiga', 'spam email', 'kena phishing'],
                'text' => "🎣 **Insiden Phishing — Langkah Segera**\n\n**Jangan panik. Lakukan ini:**\n1. **Jangan klik** link/lampiran lagi\n2. **Jangan isi** password atau OTP\n3. **Screenshot** email/pesan sebagai bukti\n4. **Ubah password** akun yang mungkin terpapar\n5. **Laporkan** via portal dengan kategori Phishing\n\n**Jika sudah mengisi password:**\n- Segera ganti password di layanan terkait\n- Aktifkan MFA jika belum\n- Laporkan sebagai insiden **akses tidak sah**\n\nTim CSIRT dapat membantu analisis link dan domain mencurigakan.",
                'suggestions' => ['Akun diretas', 'Cara lapor insiden', 'Lampiran bukti', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['malware', 'virus', 'ransomware', 'trojan', 'worm', 'file aneh', 'encrypt', 'malicious'],
                'text' => "🦠 **Insiden Malware — Langkah Segera**\n\n1. **Putuskan internet** perangkat terdampak (Wi‑Fi/LAN off)\n2. **Jangan** bayar tebusan ransomware\n3. **Jangan** hapus file sebelum dilaporkan (bukti forensik)\n4. Catat pesan error / nama file mencurigakan\n5. **Laporkan** dengan kategori Malware + lampiran screenshot\n\n**Pencegahan:**\n- Update antivirus & sistem operasi\n- Jangan buka attachment dari email tidak dikenal\n- Backup data rutin\n\nTim CSIRT dapat membantu identifikasi dan mitigasi lanjutan.",
                'suggestions' => ['Phishing', 'Cara lapor insiden', 'Lampiran bukti', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['diretas', 'retas', 'hack', 'bobol', 'login aneh', 'password bocor', 'akun tidak bisa', 'akses tidak sah'],
                'text' => "🔐 **Akun / Sistem Kemungkinan Diretas**\n\n**Langkah darurat:**\n1. **Logout** semua sesi & **ganti password** segera\n2. **Aktifkan MFA** jika belum\n3. Cek email recovery — pastikan tidak diubah pihak lain\n4. **Laporkan** insiden dengan kategori Akses Tidak Sah\n5. Cantumkan: waktu kejadian, gejala, akun terdampak\n\n**Jangan:**\n- Gunakan password yang sama di layanan lain\n- Abaikan notifikasi login dari lokasi asing\n\nTim CSIRT akan bantu investigasi jejak akses.",
                'suggestions' => ['Phishing', 'Cara lapor insiden', 'MFA', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['deface', 'website diubah', 'halaman berubah', 'situs dihack', 'web defacement', 'tampilan berubah'],
                'text' => "🌐 **Web Defacement — Website Diubah Tanpa Izin**\n\n1. **Screenshot** tampilan yang berubah (bukti)\n2. **Jangan** edit/hapus file server sendiri jika tidak yakin\n3. **Isolasi** akses admin (ganti password panel hosting)\n4. **Laporkan** dengan kategori Web Defacement\n5. Sertakan URL, waktu kejadian, perubahan yang terlihat\n\n**Prioritas tinggi** jika website resmi pemerintah/dinas.\n\nTim CSIRT koordinasi penanganan & pemulihan.",
                'suggestions' => ['Cara lapor insiden', 'Lampiran bukti', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['ddos', 'situs down', 'situs lambat', 'tidak bisa akses', 'server down', 'layanan mati'],
                'text' => "⚡ **Gangguan Ketersediaan Layanan (DDoS / Down)**\n\n1. Pastikan gangguan bukan hanya di jaringan lokal Anda\n2. Catat **waktu mulai** & gejala (timeout, error 503, dll.)\n3. **Laporkan** kategori DDoS / Availability\n4. Sertakan URL layanan & dampak (publik/internal)\n\n**Sementara:**\n- Koordinasi dengan tim IT/hosting\n- Siapkan bukti log server jika ada\n\nTim CSIRT bantu analisis pola serangan.",
                'suggestions' => ['Cara lapor insiden', 'Kontak CSIRT', 'Jenis insiden'],
            ],
            [
                'triggers' => ['bocor', 'leak', 'data pribadi', 'data bocor', 'informasi rahasia', 'kebocoran data'],
                'text' => "⚠️ **Kebocoran Data (Data Leak)**\n\n1. **Identifikasi** data apa yang bocor (NIK, email, dokumen, dll.)\n2. **Batasi** akses ke data tersebut segera\n3. **Dokumentasi** sumber kebocoran jika diketahui\n4. **Laporkan** kategori Data Leak — **prioritas tinggi**\n5. **Jangan** sebar data bocor lebih luas\n\n**Wajib lapor** jika melibatkan data masyarakat/pegawai.\n\nTim CSIRT bantu containment & rekomendasi notifikasi.",
                'suggestions' => ['Cara lapor insiden', 'Akun diretas', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['serangan', 'attack', 'hacker', 'ancaman', 'insiden siber', 'diserang'],
                'text' => "🛡️ **Insiden Siber — Respons Awal**\n\n**Langkah umum (NIST Respond):**\n1. **Deteksi** — catat apa yang terjadi & kapan\n2. **Isolasi** — batasi sistem terdampak dari jaringan\n3. **Laporkan** — buat tiket di portal CSIRT\n4. **Dokumentasi** — kumpulkan bukti (log, screenshot)\n5. **Jangan** musnahkan bukti sebelum analisis\n\nCeritakan lebih spesifik (phishing, malware, deface, dll.) agar saya beri panduan detail.",
                'suggestions' => ['Phishing', 'Malware', 'Cara lapor insiden', 'Jenis insiden'],
            ],
            [
                'triggers' => ['zero trust', 'cia', 'nist', 'framework', 'keamanan sistem', 'fitur keamanan'],
                'text' => "🛡️ **Keamanan Portal CSIRT**\n\nPortal ini menerapkan:\n- **Verifikasi identitas** (login + MFA)\n- **Pemantauan akses** (Zero Trust)\n- **Enkripsi data** laporan\n\nSebagai pelapor, yang penting:\n1. Gunakan password kuat + MFA\n2. Laporkan insiden secepatnya\n3. Jangan bagikan kredensial\n\nButuh bantuan praktis? Tanya **cara lapor** atau jenis insiden spesifik.",
                'suggestions' => ['Cara lapor insiden', 'MFA', 'Phishing', 'Malware'],
            ],
        ];

        foreach ($scenarios as $scenario) {
            foreach ($scenario['triggers'] as $trigger) {
                if (Str::contains($input, $trigger)) {
                    return [
                        'text' => $scenario['text'],
                        'suggestions' => $scenario['suggestions'],
                        'actions' => [],
                    ];
                }
            }
        }

        return [
            'text' => "🤖 Saya belum yakin maksud pertanyaan Anda, tapi saya siap bantu.\n\n**Coba jelaskan:**\n- Apa yang terjadi? (email aneh, virus, website berubah, dll.)\n- Kapan kejadiannya?\n- Sistem/akun apa yang terdampak?\n\n**Atau pilih topik populer:**\n- Phishing / penipuan email\n- Malware / virus\n- Akun diretas\n- Cara buat laporan\n- Cek status laporan\n\nSemakin spesifik, semakin tepat solusi yang saya berikan.",
            'suggestions' => config('chatbot.default_suggestions', []),
            'actions' => [],
        ];
    }
}
