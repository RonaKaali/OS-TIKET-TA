<?php

namespace App\Services;

use App\Models\ChatbotResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotService
{
    public function __construct(
        protected AIAnalystService $aiService
    ) {}

    /**
     * Respons terstruktur untuk widget (teks + saran + aksi).
     */
    public function respond(string $message): array
    {
        $normalized = Str::lower(trim($message));

        $dbResponse = $this->findResponse($message);
        if ($dbResponse) {
            return $this->buildResult(
                $dbResponse->response,
                $this->suggestionsFor($normalized),
                $this->defaultActions()
            );
        }

        foreach ($this->pelaporGuides() as $guide) {
            if ($this->matchesGuide($normalized, $guide['triggers'])) {
                return $this->buildResult(
                    $guide['text'],
                    $guide['suggestions'] ?? $this->suggestionsFor($normalized),
                    array_merge($this->defaultActions(), $guide['actions'] ?? [])
                );
            }
        }

        $ai = $this->aiService->getAnalysis($message);

        return $this->buildResult(
            $ai['text'],
            $ai['suggestions'] ?? $this->suggestionsFor($normalized),
            array_merge($this->defaultActions(), $ai['actions'] ?? [])
        );
    }

    /**
     * @deprecated Gunakan respond() — dipertahankan untuk kompatibilitas.
     */
    public function getResponse(string $message): string
    {
        return $this->respond($message)['response'];
    }

    public function findResponse(string $message): ?ChatbotResponse
    {
        $normalizedMessage = Str::lower(trim($message));

        $responses = ChatbotResponse::active()
            ->ordered()
            ->get();

        foreach ($responses as $response) {
            $keyword = Str::lower(trim($response->keyword));

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

        return null;
    }

    protected function matchesGuide(string $message, array $triggers): bool
    {
        foreach ($triggers as $trigger) {
            if (Str::contains($message, Str::lower($trigger))) {
                return true;
            }
        }

        return false;
    }

    protected function buildResult(string $text, array $suggestions, array $actions): array
    {
        return [
            'response' => $text,
            'suggestions' => array_values(array_unique(array_slice($suggestions, 0, 5))),
            'actions' => $this->uniqueActions($actions),
        ];
    }

    protected function uniqueActions(array $actions): array
    {
        $seen = [];
        $result = [];

        foreach ($actions as $action) {
            $key = ($action['label'] ?? '') . '|' . ($action['url'] ?? '');
            if (!isset($seen[$key]) && !empty($action['label']) && !empty($action['url'])) {
                $seen[$key] = true;
                $result[] = $action;
            }
        }

        return array_slice($result, 0, 3);
    }

    protected function defaultActions(): array
    {
        $actions = [];

        if (Auth::check()) {
            $actions[] = [
                'label' => 'Buat Laporan Baru',
                'url' => route('portal.ticket.create'),
                'style' => 'primary',
            ];
        } else {
            $actions[] = [
                'label' => 'Login untuk Melapor',
                'url' => route('login'),
                'style' => 'primary',
            ];
        }

        $actions[] = [
            'label' => 'Lacak Status Laporan',
            'url' => route('portal.ticket.status.form'),
            'style' => 'secondary',
        ];

        return $actions;
    }

    protected function suggestionsFor(string $message): array
    {
        if (Str::contains($message, ['phishing', 'email', 'link', 'penipuan'])) {
            return ['Malware', 'Akun diretas', 'Cara lapor', 'Kontak CSIRT'];
        }

        if (Str::contains($message, ['virus', 'malware', 'ransomware'])) {
            return ['Phishing', 'Cara lapor', 'Lampiran bukti', 'Kontak CSIRT'];
        }

        if (Str::contains($message, ['status', 'lacak', 'tiket', 'laporan'])) {
            return ['Cara lapor', 'Jenis insiden', 'Phishing', 'Kontak CSIRT'];
        }

        return config('chatbot.default_suggestions', []);
    }

    /**
     * Panduan utama untuk pelapor (Bahasa Indonesia, langkah praktis).
     */
    protected function pelaporGuides(): array
    {
        return [
            [
                'triggers' => ['halo', 'hai', 'hi', 'pagi', 'siang', 'sore', 'malam', 'selamat'],
                'text' => "Halo! Saya **Asisten CSIRT Kalselprov**. 👋\n\nSaya bantu Anda:\n- **Melaporkan** insiden siber\n- **Memahami** langkah penanganan awal\n- **Melacak** status laporan\n\nPilih topik di bawah atau ketik pertanyaan Anda (contoh: *\"kena phishing\"* atau *\"cara buat laporan\"*).",
                'suggestions' => ['Cara lapor insiden', 'Cek status laporan', 'Phishing', 'Malware', 'Kontak CSIRT'],
                'actions' => [],
            ],
            [
                'triggers' => ['help', 'bantuan', 'menu', 'panduan', 'tolong'],
                'text' => "📋 **Menu Bantuan Pelapor**\n\n**Laporan & Status**\n- `cara buat laporan` — panduan lengkap melapor\n- `status laporan` — cara lacak tiket\n\n**Solusi Insiden Umum**\n- `phishing` / `email mencurigakan`\n- `malware` / `virus` / `ransomware`\n- `akun diretas` / `password bocor`\n- `website diubah` / `deface`\n- `situs lambat` / `ddos`\n\n**Lainnya**\n- `jenis insiden` — kategori yang bisa dilaporkan\n- `lampiran bukti` — cara upload screenshot/log\n- `kontak` — hubungi tim CSIRT\n\nKetik topik di atas atau gunakan tombol cepat.",
                'suggestions' => ['Cara lapor insiden', 'Phishing', 'Malware', 'Akun diretas', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['cara buat laporan', 'cara lapor', 'cara buat tiket', 'buat laporan', 'melaporkan', 'lapor insiden'],
                'text' => "📝 **Cara Melaporkan Insiden Siber**\n\n**Langkah singkat:**\n1. **Login** ke akun pelapor Anda\n2. Buka menu **Buat Laporan Baru**\n3. Pilih **kategori insiden** (Phishing, Malware, Deface, dll.)\n4. Tulis **judul** yang jelas (contoh: \"Email phishing palsu bank\")\n5. Isi **deskripsi kronologi**: kapan, apa yang terjadi, dampaknya\n6. **Lampirkan bukti** (screenshot, email, log) jika ada\n7. Klik **Kirim Laporan**\n\n**Tips agar cepat ditangani:**\n- Jangan hapus bukti sebelum dilaporkan\n- Cantumkan URL/link mencurigakan jika ada\n- Sebutkan apakah ada data pribadi terpapar\n\nTim CSIRT akan menindaklanjuti sesuai prioritas insiden.",
                'suggestions' => ['Jenis insiden', 'Lampiran bukti', 'Cek status laporan', 'Phishing'],
            ],
            [
                'triggers' => ['status laporan', 'cek status', 'lacak laporan', 'nomor tiket', 'progress laporan'],
                'text' => "🔍 **Cara Cek Status Laporan**\n\n1. Buka menu **Lacak Laporan** / Cek Status\n2. Masukkan **nomor tiket** yang Anda terima saat submit\n3. Verifikasi identitas jika diminta\n4. Lihat update status:\n   - **Open** — laporan diterima\n   - **In Progress** — sedang ditangani analis\n   - **Resolved** — penanganan selesai\n   - **Closed** — kasus ditutup\n\nJika lupa nomor tiket, login ke akun Anda dan buka riwayat laporan.",
                'suggestions' => ['Cara lapor insiden', 'Kontak CSIRT', 'Phishing'],
            ],
            [
                'triggers' => ['jenis insiden', 'kategori insiden', 'apa saja insiden', 'contoh insiden'],
                'text' => "📊 **Jenis Insiden yang Bisa Dilaporkan**\n\n| Kategori | Contoh Kejadian |\n|----------|------------------|\n| **Phishing** | Email/SMS/link penipuan minta password |\n| **Malware** | Virus, ransomware, file mencurigakan |\n| **Web Defacement** | Tampilan website berubah tanpa izin |\n| **DDoS** | Layanan website/layanan tidak bisa diakses |\n| **Data Leak** | Data pribadi/rahasia bocor |\n| **Akses tidak sah** | Login aneh, akun diretas |\n| **Spam/Scam** | Email massal mencurigakan |\n\nTidak yakin kategorinya? Laporkan saja — tim CSIRT akan mengklasifikasikan.",
                'suggestions' => ['Cara lapor insiden', 'Phishing', 'Malware', 'Akun diretas'],
            ],
            [
                'triggers' => ['lampiran', 'bukti', 'screenshot', 'upload', 'file bukti'],
                'text' => "📎 **Cara Melampirkan Bukti**\n\nSaat membuat laporan:\n1. Scroll ke bagian **Lampiran / Attachment**\n2. Upload file: screenshot, PDF, log, atau email (.eml)\n3. Maksimal ukuran mengikuti batas di form\n\n**Bukti yang membantu analis:**\n- Screenshot halaman phishing\n- Header email asli\n- Log error / antivirus\n- Foto layar pesan ransomware\n\n**Jangan** upload password atau data rahasia yang tidak perlu.",
                'suggestions' => ['Cara lapor insiden', 'Phishing', 'Malware'],
            ],
            [
                'triggers' => ['kontak', 'hubungi', 'telepon csirt', 'email csirt', 'darurat'],
                'text' => "📞 **Kontak CSIRT Kalselprov**\n\n- **Email:** csirt@kalselprov.go.id\n- **Portal:** laporkan insiden melalui menu Buat Laporan\n- **Darurat siber:** laporkan segera via portal + sertakan bukti\n\n**Untuk insiden kritis** (ransomware, kebocoran data masif, deface website resmi):\n1. Laporkan via portal **segera**\n2. Isolasi sistem terdampak (putus internet jika perlu)\n3. Jangan hapus bukti\n\nTim CSIRT siap membantu 24/7.",
                'suggestions' => ['Cara lapor insiden', 'Phishing', 'Malware', 'Akun diretas'],
            ],
            [
                'triggers' => ['lupa password', 'reset password', 'ganti password'],
                'text' => "🔐 **Lupa Password Akun Pelapor**\n\n1. Di halaman login, klik **Lupa Password**\n2. Masukkan email terdaftar\n3. Cek inbox/spam untuk link reset\n4. Buat password baru yang kuat\n\n**Jika email tidak diterima:**\n- Pastikan email yang didaftarkan benar\n- Hubungi admin: admin-csirt@kalselprov.go.id\n\n**Jika curiga akun diretas**, segera laporkan sebagai insiden akses tidak sah.",
                'suggestions' => ['Akun diretas', 'Cara lapor insiden', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['mfa', 'otp', 'authenticator', '2fa', 'verifikasi dua'],
                'text' => "🔑 **Bantuan MFA (Verifikasi 2 Langkah)**\n\n**Setup MFA:**\n1. Login → buka **Profil / Pengaturan Keamanan**\n2. Pilih **Aktifkan MFA**\n3. Scan QR code dengan Google Authenticator\n4. Simpan **backup codes** di tempat aman\n\n**Kode tidak diterima?**\n- Pastikan waktu HP/laptop sinkron (auto timezone)\n- Coba **backup code** jika TOTP gagal\n- Hubungi admin jika secret hilang\n\nMFA melindungi laporan Anda dari akses tidak sah.",
                'suggestions' => ['Akun diretas', 'Cara lapor insiden', 'Kontak CSIRT'],
            ],
            [
                'triggers' => ['terima kasih', 'makasih', 'thanks', 'thx'],
                'text' => "Sama-sama! 🙏\n\nTetap waspada dan laporkan insiden secepatnya. Anda membantu menjaga keamanan siber Kalsel.\n\nButuh bantuan lagi? Ketik **help** atau pilih topik di bawah.",
                'suggestions' => ['Cara lapor insiden', 'Phishing', 'Cek status laporan'],
                'actions' => [],
            ],
        ];
    }
}
