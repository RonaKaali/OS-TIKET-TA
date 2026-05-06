# Panduan Demo Sidang & Pengujian Zero Trust (OS-Tiket)

Dokumen ini membantu Anda **mendemokan** seluruh lapisan keamanan Zero Trust di OS-Tiket saat sidang tugas akhir, sekaligus menjadi **kerangka** untuk bab laporan tentang **perbedaan sebelum–sesudah** dan **metode pengujian**.

**Referensi di repositori**

| Dokumen | Isi |
|--------|-----|
| `ZERO_TRUST_DEFENSES.md` | Penjelasan tiap lapisan pertahanan + diagram alur (Mermaid) |
| `LAPORAN_PERBEDAAN_ZERO_TRUST_SEBELUM_SESUDAH.md` | Tabel perbandingan fitur, checklist screenshot, langkah uji per fitur |
| `config/zero_trust.php` | Saklar dan parameter (ambang skor, GeoIP, interval session, dll.) |

---

## 1. Prinsip demo sidang (yang ingin ditunjukkan ke penguji)

Zero Trust di OS-Tiket mengikuti gagasan **“Never Trust, Always Verify”** melalui lima area:

1. **Identitas & MFA** — pemastian pemilik akun (TOTP + backup codes).
2. **Perangkat & konteks** — fingerprint, trust score, jam kerja, GeoIP, GPS opsional, **risk score** → step-up MFA atau pemblokiran.
3. **Proteksi data** — lampiran terenkripsi di penyimpanan; secret MFA tidak plaintext di database.
4. **Verifikasi berkelanjutan** — middleware `ZeroTrustVerification` + validasi session berkala.
5. **Audit** — `SecurityEventLogService`, file `storage/logs/security-*.log`, tabel `security_events`.

**Satu kalimat pembuka yang kuat:** “Setelah login, setiap request tidak lagi hanya mengandalkan cookie session; sistem menilai ulang perangkat, konteks, dan risiko, serta mencatat jejak keamanan.”

---

## 2. Persiapan sebelum demo

### 2.1 Lingkungan

- Gunakan **development** atau **staging**, bukan produksi langsung.
- Pastikan aplikasi jalan (`php artisan serve` atau stack Anda), database terisi user uji dengan MFA siap pakai, dan peran/permission sesuai seeder (misalnya agen dengan `admin.panel`).

### 2.2 Setelah mengubah `.env`

```bash
php artisan config:clear
```

Jika mengubah JavaScript (`resources/js`), jalankan `npm run build` atau `npm run dev`.

### 2.3 Saklar utama: sebelum vs sesudah

| Kondisi | Variabel | Efek untuk demo |
|--------|----------|-----------------|
| **Sebelum (baseline)** | `ZERO_TRUST_ENABLED=false` | Inti logika Zero Trust (fingerprint, risk-based step-up, dll.) tidak aktif seperti saat ZT menyala. |
| **Sesudah** | `ZERO_TRUST_ENABLED=true` | Alur lengkap: device + konteks + validasi berkala + logging terkait ZT. |

Detail variabel tambahan ada di `LAPORAN_PERBEDAAN_ZERO_TRUST_SEBELUM_SESUDAH.md` bagian 1–2.

### 2.4 Materi yang disiapkan di layar

- Browser (Chrome/Edge) dengan **DevTools** siap (tab Network untuk GPS).
- Editor/teks untuk menunjukkan `config/zero_trust.php` atau cuplikan `.env` (tanpa rahasia produksi).
- Akses ke `storage/logs/security-YYYY-MM-DD.log` atau tool DB untuk `security_events`.
- (Opsional) File GeoLite2 City `.mmdb` jika mendemo GeoIP.

---

## 3. Urutan demo sidang (alur presentasi ±15–25 menit)

Sesuaikan durasi; urutan di bawah dari **konsep** ke **bukti teknis**.

| Urutan | Topik | Yang ditunjukkan | Bukti singkat |
|--------|--------|------------------|---------------|
| A | **Konfigurasi** | File `config/zero_trust.php` atau `.env` dengan `ZERO_TRUST_ENABLED=true` | “Semua ambang dan fitur diatur terpusat.” |
| B | **Login & MFA** | Login → halaman MFA → kode TOTP | Alur di `ZERO_TRUST_DEFENSES.md` bagian 1 |
| C | **Zero Trust aktif** | Browsing ke `/agent` atau dashboard setelah login | Baris baru di `security-*.log` (akses / konteks) |
| D | **Device & risiko** | Jelaskan fingerprint + risk score (slide atau diagram dari `ZERO_TRUST_DEFENSES.md`) | Log dengan metadata perangkat / anomaly jika ada |
| E | **Step-up MFA** | Setelah turunkan sementara `RISK_SCORE_THRESHOLD_HIGH`, picu skor tinggi (mis. luar jam kerja + akhir pekan) | Redirect ke `/mfa/verify`, lalu sukses dengan TOTP |
| F | **Pemblokiran kritis** | (Opsional, hanya jika waktu cukup) turunkan sementara `RISK_SCORE_THRESHOLD_CRITICAL` untuk uji 403 | Halaman 403 atau respons JSON sesuai middleware |
| G | **Jam kerja** | Akun agen **tanpa** `admin.after_hours_access` mengakses panel di luar jam kerja | 403 + anomaly `after_hours_access_attempt` di log |
| H | **GPS** | Izinkan lokasi di browser → tab Network | Request `POST /zero-trust/gps` status 200, body `{"status":"ok"}` |
| I | **GeoIP** | (Jika `.mmdb` terpasang) jelaskan `ALLOWED_COUNTRIES` | Skor risiko naik jika negara di luar daftar |
| J | **Lampiran** | Upload lampiran → tunjukkan file `.enc` di storage → unduh sebagai user berhak | File terbuka normal; di disk tetap terenkripsi |
| K | **Secret MFA di DB** | Satu baris penjelasan: kolom `users.mfa_secret` berbentuk ciphertext Laravel | Screenshot query (sensor sebagian jika perlu) |
| L | **Audit** | Scroll `security-*.log` atau tabel `security_events` | Event `auth_*`, `access`, `high_risk`, unduhan lampiran |

**Tips bicara:** untuk setiap huruf di atas, hubungkan ke **prinsip Zero Trust** (verifikasi berkelanjutan, least privilege, asumsi jaringan tidak tepercaya).

---

## 4. Panduan demo per fitur (langkah praktis)

Bagian ini merangkum langkah; detail angka ambang dan skenario ada di `LAPORAN_PERBEDAAN_ZERO_TRUST_SEBELUM_SESUDAH.md` bagian 2.

### 4.1 MFA (login + backup codes)

1. Login dengan akun yang MFA-nya aktif.
2. Masukkan kode dari aplikasi authenticator; opsional uji satu **backup code** sekali pakai.
3. **Laporan:** jelaskan bahwa password saja tidak cukup; cocokkan dengan flowchart di `ZERO_TRUST_DEFENSES.md`.

### 4.2 Zero Trust middleware & logging

1. `ZERO_TRUST_ENABLED=true`, `php artisan config:clear`.
2. Login dan buka beberapa halaman yang membutuhkan auth.
3. Buka `storage/logs/security-YYYY-MM-DD.log` — tunjukkan jejak aktivitas/akses.

### 4.3 Device fingerprint & trust score

1. Pastikan `ZERO_TRUST_DEVICE_FINGERPRINTING=true`.
2. Jelaskan bahwa setiap request membangun fingerprint (user-agent, IP, dll.).
3. Bukti: entri log / metadata yang menyebut fingerprint atau event perangkat.  
   *Catatan:* jika trust rendah dan route verifikasi perangkat belum tersedia, perilaku bisa *fail open* dengan pencatatan anomaly — jujurkan di sidang sesuai implementasi saat ini.

### 4.4 Risk score, step-up MFA, 403 kritis

**Letak menurunkan ambang (hanya untuk demo/uji di lingkungan non-produksi):**

- **File `.env`** di root proyek OS-Tiket — set variabel lingkungan berikut (tanpa spasi di sekitar `=`):
  - `RISK_SCORE_THRESHOLD_HIGH` — misalnya `30` agar step-up MFA mudah terpicu (default jika variabel tidak ada: **70**).
  - `RISK_SCORE_THRESHOLD_CRITICAL` — misalnya `40` hanya saat ingin mendemo respons 403 kritis (default jika tidak ada: **85**).
- **Pemetaan di kode:** nilai tersebut dibaca di `config/zero_trust.php` pada kunci `risk_score_threshold_high` dan `risk_score_threshold_critical`.
- Setelah mengubah `.env`, jalankan `php artisan config:clear` supaya Laravel memuat nilai baru.

1. Untuk demo step-up: **sementara** turunkan `RISK_SCORE_THRESHOLD_HIGH` di `.env` (mis. `RISK_SCORE_THRESHOLD_HIGH=30`), pertahankan `RISK_SCORE_THRESHOLD_CRITICAL` lebih tinggi dari skor yang Anda picu; picu skor dengan akses di luar jam kerja / akhir pekan / perubahan IP.
2. Tunjukkan redirect ke verifikasi MFA, lalu akses lanjut setelah TOTP benar.
3. Untuk 403 kritis: hanya di lingkungan uji, turunkan sementara `RISK_SCORE_THRESHOLD_CRITICAL` di `.env` (mis. `RISK_SCORE_THRESHOLD_CRITICAL=40`), lalu **kembalikan ke default** (atau hapus barisnya agar memakai default dari `config/zero_trust.php`) setelah demo, dan jangan lupa `php artisan config:clear`.

### 4.5 Konteks jam kerja (agen tanpa izin after-hours)

1. Gunakan akun dengan `admin.panel` tanpa permission akses luar jam kerja.
2. Akses `/agent` di luar rentang jam kerja (sesuai `APP_TIMEZONE`).
3. Tunjukkan penolakan akses dan entri log terkait.

### 4.6 GeoIP (opsional)

1. Pasang `GeoLite2-City.mmdb`, set `GEO_LOCATION_ENABLED=true`, `GEOIP_DB_PATH`, `ALLOWED_COUNTRIES`.
2. Jelaskan penyesuaian skor risiko untuk IP di luar negara yang diizinkan.

### 4.7 GPS browser (`POST /zero-trust/gps`)

1. Build aset frontend; login; izinkan lokasi.
2. DevTools → Network → cari `zero-trust` atau `gps`.
3. Pastikan respons sukses; session menyimpan `zero_trust_gps` untuk analisis konteks.

### 4.8 Enkripsi lampiran

1. Unggah lampiran melalui alur tiket.
2. Di storage: berkas `.enc` dan metadata `is_encrypted`.
3. Unduh dari `GET /attachments/{id}/download` — file terbuka untuk user berhak.
4. (Opsional) Tunjukkan penolakan unduhan tanpa hak + log keamanan.

### 4.9 Secret MFA terenkripsi

1. Buka data `users.mfa_secret` untuk user yang sudah setup MFA.
2. Nilai bukan secret TOTP plaintext — konsisten dengan `encrypt()` di `MfaService`.

---

## 5. Membantu bab laporan: sebelum vs sesudah

### 5.1 Tabel ringkas (isi bab analisis / pembahasan)

Gunakan struktur berikut lalu perkaya dengan data Anda:

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| Verifikasi setelah login | Utama berdasarkan session & role | Ditambah evaluasi perangkat, konteks, risk score berkelanjutan |
| MFA | Fokus saat login | Ditambah **step-up** saat risiko tinggi |
| Data lampiran | Risiko plaintext di disk (pol lama) | Penyimpanan terenkripsi (`.enc`), dekripsi saat unduh |
| Secret MFA di DB | Risiko penyimpanan tidak aman | Secret dienkripsi aplikasi |
| Lokasi / negara | Tidak terintegrasi risiko | GeoIP + GPS opsional memengaruhi skor |
| Audit | Terbatas | Kanal `security` + `security_events` lebih kaya konteks |

Tabel lengkap dengan nomor baris referensi ada di `LAPORAN_PERBEDAAN_ZERO_TRUST_SEBELUM_SESUDAH.md` bagian 3.

### 5.2 Bukti visual untuk lampiran

- Screenshot `.env` atau config: `ZERO_TRUST_ENABLED=false` vs `true` (sensor rahasia).
- Screenshot log `security-*.log` sebelum/sesudah (volume atau jenis event berbeda).
- Screenshot Network untuk `POST /zero-trust/gps`.
- Screenshot storage berisi `.enc` + UI unduhan sukses.
- Cuplikan kode (middleware, service) dari peta file di `LAPORAN_PERBEDAAN_ZERO_TRUST_SEBELUM_SESUDAH.md` bagian 5.

### 5.3 Menjawab pertanyaan umum penguji

- **“Apa bedanya Zero Trust dan MFA saja?”** — MFA memastikan identitas di awal; Zero Trust **meneruskan penilaian** (perangkat, konteks, risiko) pada request berikutnya dan bisa meminta MFA lagi.
- **“Apa jika `APP_KEY` bocor?”** — Lampiran dan secret MFA bergantung pada kunci aplikasi; jelaskan bahwa ini mitigasi **kebocoran storage/DB**, bukan pengganti pengelolaan kunci server yang baik.
- **“Kenapa threshold bisa diubah?”** — Untuk menyesuaikan toleransi false positive vs keamanan; demo menggunakan nilai sementara hanya di lingkungan uji.

---

## 6. Metodologi pengujian (untuk bab metode / pengujian)

### 6.1 Jenis pengujian

| Jenis | Deskripsi untuk laporan |
|-------|-------------------------|
| **Fungsional / black-box** | Skenario pengguna: login, MFA, navigasi, upload/unduh lampiran, akses panel sesuai role. |
| **Keamanan / konfigurasi** | Mengubah `ZERO_TRUST_ENABLED`, ambang risk score, dan memverifikasi perubahan perilaku (redirect MFA, 403, log). |
| **Audit trail** | Memverifikasi bahwa event penting tertulis ke file log dan/atau database. |
| **Regresi** | Memastikan fitur bisnis utama (tiket, portal) tetap jalan saat ZT aktif. |

### 6.2 Matriks skenario (contoh untuk tabel di laporan)

| ID | Skenario | Langkah utama | Hasil yang diharapkan |
|----|----------|---------------|------------------------|
| T1 | Login dengan MFA benar | Email + password + TOTP | Masuk aplikasi; event auth sukses di log |
| T2 | Login dengan TOTP salah | Kode salah beberapa kali | Penolakan; event gagal MFA |
| T3 | ZT aktif, browsing biasa | `ZERO_TRUST_ENABLED=true`, navigasi | Log akses / konteks tanpa error aplikasi |
| T4 | Step-up MFA | Threshold high diturunkan, picu risiko | Redirect `/mfa/verify`, lalu lanjut setelah verifikasi |
| T5 | Jam kerja | Akses panel di luar jam tanpa izin | 403 sesuai kebijakan |
| T6 | GPS | Izin lokasi, cek Network | `POST /zero-trust/gps` 200 |
| T7 | Lampiran | Upload → cek `.enc` → unduh | Unduhan valid; file di disk tetap terenkripsi |
| T8 | Unduhan tanpa hak | User lain / tamu | Akses ditolak; tercatat di log |

Sesuaikan ID dan baris dengan pengujian yang benar-benar Anda jalankan.

### 6.3 Kriteria keberhasilan (contoh)

- Semua skenario **kritis** (T1, T3, T7) lulus.
- Tidak ada error 500 pada alur utama saat ZT aktif.
- Log keamanan menunjukkan jejak untuk setidaknya: login sukses, satu akses setelah login, satu aksi sensitif (mis. unduhan lampiran atau step-up).

### 6.4 Batasan yang layak disebut di laporan

- GeoIP membutuhkan database MaxMind dan pembaruan berkala.
- GPS membutuhkan izin pengguna dan tidak selalu akurat.
- Threshold risiko perlu penyetelan agar tidak mengganggu pengguna sah.
- Ketergantungan pada `APP_KEY` dan keamanan server secara keseluruhan.

---

## 7. Checklist hari-H sidang

- [ ] `.env` demo sudah `ZERO_TRUST_ENABLED=true` (atau skenario “sebelum” dipersiapkan di clip video cadangan).
- [ ] `php artisan config:clear` sudah dijalankan setelah perubahan terakhir.
- [ ] Akun uji: MFA siap, password diketahui, role sesuai skenario jam kerja.
- [ ] Browser: cache/cookie bersih atau sesi uji terpisah untuk menghindari kebingungan.
- [ ] File log terbaru dibuka di tab lain untuk scroll cepat.
- [ ] (Opsional) Rekaman layar cadangan jika jaringan bermasalah.
- [ ] Setelah demo threshold yang diubah sementara, **kembalikan nilai default** di `.env`.

---

## 8. Peta file kode (ringkas)

| File | Peran |
|------|--------|
| `bootstrap/app.php` | Pendaftaran middleware `ZeroTrustVerification` |
| `app/Http/Middleware/ZeroTrustVerification.php` | Inti verifikasi berkelanjutan |
| `app/Services/DeviceFingerprintService.php` | Fingerprint & trust |
| `app/Services/ContextAwareAccessService.php` | Konteks, GeoIP, risk score |
| `app/Services/FileEncryptionService.php` | Enkripsi/dekripsi lampiran |
| `app/Services/MfaService.php` | MFA & enkripsi secret |
| `app/Http/Controllers/Auth/MfaVerificationController.php` | MFA login & step-up |
| `app/Http/Controllers/AttachmentController.php` | Unduhan aman |
| `routes/web.php` | `POST /zero-trust/gps`, `attachments/{attachment}/download` |
| `resources/js/geo-location.js` | Pengiriman koordinat ke server |

---

*Dokumen ini disusun untuk mendukung demo sidang dan penulisan laporan tugas akhir; sesuaikan nama lingkungan, tangkapan layar, dan hasil pengujian aktual Anda.*
