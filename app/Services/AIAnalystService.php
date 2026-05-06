<?php

namespace App\Services;

use Illuminate\Support\Str;

class AIAnalystService
{
    /**
     * Skill: cybersecurity-analyst
     * Menyediakan respons berbasis framework keamanan (CIA, STRIDE, Zero Trust)
     */
    protected $persona = [
        'name' => 'Assistant Kamu',
        'role' => 'Lead Cybersecurity Analyst & AI Guard',
        'principles' => [
            'Defense in Depth',
            'Assume Breach',
            'Least Privilege',
            'Zero Trust Architecture (ZTA)',
            'CIA Triad (Confidentiality, Integrity, Availability)'
        ],
        'frameworks' => [
            'NIST Cybersecurity Framework (CSF)',
            'MITRE ATT&CK',
            'STRIDE Threat Modeling',
            'ISO/IEC 27001'
        ]
    ];

    /**
     * Mendapatkan respons cerdas berdasarkan pesan user
     */
    public function getAnalysis(string $message): string
    {
        $input = Str::lower($message);

        // Analisis ancaman (Simulasi Agent Thinking)
        if (Str::contains($input, ['serangan', 'attack', 'hacker', 'retas', 'ancaman'])) {
            return $this->formatResponse(
                "🔍 **Analisis Ancaman Terdeteksi (Agent Skill: Threat Intel)**\n\n" .
                "Berdasarkan input Anda, saya menganalisis vektor serangan menggunakan kerangka **MITRE ATT&CK**. Potensi ancaman ini berada pada fase *Initial Access* atau *Execution*.\n\n" .
                "🛡️ **Rekomendasi Strategis (NIST Respond):**\n" .
                "1. **Isolasi Segera:** Putuskan koneksi jaringan pada aset yang dicurigai terkompromi.\n" .
                "2. **Preservasi Bukti:** Jangan melakukan reboot atau perubahan konfigurasi drastis untuk menjaga integritas artefak forensik.\n" .
                "3. **Verifikasi Identitas:** Terapkan **MFA** dan tinjau log akses untuk mendeteksi *Lateral Movement*.\n\n" .
                "Saya siap memandu Anda melakukan isolasi teknis jika diperlukan."
            );
        }

        if (Str::contains($input, ['data', 'bocor', 'leak', 'pribadi', 'ekspos'])) {
            return $this->formatResponse(
                "⚠️ **Pelanggaran Aspek Confidentiality (CIA Triad)**\n\n" .
                "Terdeteksi potensi kebocoran informasi sensitif. Dalam arsitektur **Zero Trust**, insiden ini menuntut verifikasi ulang terhadap seluruh *trust boundary*.\n\n" .
                "🕵️ **Langkah Mitigasi:**\n" .
                "- **Data Classification:** Identifikasi apakah data yang terekspos adalah PII (Personally Identifiable Information) atau kredensial sistem.\n" .
                "- **Credential Rotation:** Lakukan rotasi massal pada semua token, kunci API, dan kata sandi yang terkait.\n" .
                "- **Blast Radius Reduction:** Batasi hak akses (Least Privilege) untuk meminimalkan dampak.\n\n" .
                "Segera buat laporan resmi di menu **'Buat Tiket'** agar tim analis kami dapat melakukan investigasi mendalam."
            );
        }

        if (Str::contains($input, ['keamanan', 'aman', 'proteksi', 'lindungi', 'fitur'])) {
            return $this->formatResponse(
                "🛡️ **Postur Pertahanan Sistem (Defense-in-Depth)**\n\n" .
                "Portal CSIRT ini dirancang dengan standar keamanan tingkat tinggi menggunakan beberapa lapisan:\n\n" .
                "1. **Zero Trust Architecture:** Setiap permintaan akses melalui validasi identitas, perangkat, dan konteks risiko.\n" .
                "2. **Data-at-Rest Encryption:** Seluruh laporan Anda dienkripsi menggunakan standar militer **AES-256**.\n" .
                "3. **Continuous Monitoring:** Sistem melakukan pemantauan real-time terhadap anomali perilaku pengguna.\n" .
                "4. **Geo-Fencing:** Perlindungan perimeter berbasis lokasi untuk mencegah akses dari region berisiko tinggi.\n\n" .
                "Anda dapat merasa aman karena data Anda terlindungi oleh protokol keamanan modern."
            );
        }

        if (Str::contains($input, ['nist', 'framework', 'standar'])) {
            return $this->formatResponse(
                "📋 **Framework Keamanan NIST CSF**\n\n" .
                "Kami mengadopsi 5 pilar utama **NIST Cybersecurity Framework**:\n" .
                "- **Identify:** Mengelola risiko keamanan pada sistem dan aset.\n" .
                "- **Protect:** Memasang pagar pengaman untuk memastikan pengiriman layanan kritis.\n" .
                "- **Detect:** Mengidentifikasi terjadinya peristiwa keamanan siber secara cepat.\n" .
                "- **Respond:** Mengambil tindakan terhadap insiden keamanan siber yang terdeteksi.\n" .
                "- **Recover:** Memelihara rencana ketahanan dan memulihkan kapabilitas yang terdampak.\n\n" .
                "Sistem ini difokuskan pada fungsi **Detect** dan **Respond** untuk membantu Anda."
            );
        }

        // Default Intelligent Fallback
        return $this->formatResponse(
            "🤖 **CSIRT Intelligent Assistant (Assistant Kamu)**\n\n" .
            "Saya telah memproses pesan Anda melalui mesin analisis keamanan saya. \n\n" .
            "Meskipun saya tidak menemukan perintah spesifik, saya dapat memberikan wawasan teknis mengenai:\n" .
            "- 🛡️ Prosedur Penanganan Insiden (Sesuai ISO 27001).\n" .
            "- 🔐 Implementasi **Least Privilege** dan **Zero Trust**.\n" .
            "- 🔍 Analisis dasar terhadap aktivitas mencurigakan.\n\n" .
            "Silakan ajukan pertanyaan yang lebih spesifik atau ketik `help` untuk daftar bantuan."
        );
    }

    protected function formatResponse(string $text): string
    {
        return $text;
    }
}
