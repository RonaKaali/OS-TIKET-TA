# Perbedaan Proyek OS-Tiket: Sebelum vs Sesudah Zero Trust Security

Dokumen ini merangkum **bagian mana saja** yang berubah setelah penerapan Zero Trust, agar memudahkan penyusunan laporan dan pengambilan **screenshot perbandingan** (sebelum/sesudah).

> **Catatan penting:** Middleware Zero Trust tetap terdaftar di aplikasi, tetapi **perilaku “sesudah”** baru aktif jika `ZERO_TRUST_ENABLED=true` di `.env`. Untuk screenshot “sebelum”, set `ZERO_TRUST_ENABLED=false` (atau hapus variabel tersebut agar memakai default `false` dari `config/zero_trust.php`).

---

## 1. Cara membandingkan secara teknis (untuk demo & screenshot)

| Kondisi | Yang perlu di-set | Efek yang terlihat |
|--------|-------------------|-------------------|
| **Sebelum (tanpa Zero Trust aktif)** | `ZERO_TRUST_ENABLED=false` | Tidak ada pemeriksaan risiko berkala, tidak ada redirect step-up MFA karena skor risiko, device fingerprint tidak memengaruhi alur (middleware tetap jalan ringan / skip logika inti). |
| **Sesudah (Zero Trust aktif)** | `ZERO_TRUST_ENABLED=true` | Setelah login, setiap request web (yang tidak di-skip) melewati device fingerprint, analisis konteks, validasi session berkala, logging akses; risiko tinggi dapat memicu MFA ulang atau penolakan akses. |

Variabel `.env` lain yang relevan untuk menunjukkan perbedaan perilaku (opsional untuk laporan):

- `ZERO_TRUST_DEVICE_FINGERPRINTING`, `ZERO_TRUST_CONTEXT_AWARE`, `GEO_LOCATION_ENABLED`, `GEOIP_DB_PATH`, `ALLOWED_COUNTRIES`, `BLOCKED_IPS`, `RISK_SCORE_THRESHOLD_HIGH`, `RISK_SCORE_THRESHOLD_CRITICAL`, `SESSION_VALIDATION_INTERVAL`

File konfigurasi pusat: `config/zero_trust.php`.

---

## 2. Cara mencoba / menguji fitur Zero Trust berfungsi

Lakukan pengujian di lingkungan **development** atau **staging**, bukan produksi langsung. Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
```

Agar perubahan variabel lingkungan terbaca. Bangun ulang aset frontend jika Anda mengubah JS (`npm run build` atau `npm run dev`).

### 2.1 Memastikan Zero Trust benar-benar aktif

1. Set `ZERO_TRUST_ENABLED=true` di `.env`, lalu `php artisan config:clear`.
2. Login sebagai pengguna yang sudah lolos MFA (jika MFA wajib).
3. Buka halaman yang membutuhkan auth (misalnya dashboard agen `/agent` atau profil).
4. **Bukti berfungsi:** cek file log keamanan harian, misalnya `storage/logs/security-YYYY-MM-DD.log`. Setelah beberapa request, seharusnya muncul event bertipe akses (misalnya pesan mengandung path atau `event_type` terkait `access`) beserta konteks seperti `risk_score` atau metadata perangkat — sesuai implementasi `SecurityEventLogService`.
5. Bandingkan dengan `ZERO_TRUST_ENABLED=false`: log dari middleware Zero Trust untuk pemeriksaan konteks/fingerprint tidak lagi dijalankan (volume log berbeda; perilaku halaman tetap normal selama MFA middleware lain tetap aktif).

### 2.2 Device fingerprint & trust score

1. Pastikan `ZERO_TRUST_DEVICE_FINGERPRINTING=true` (default di config).
2. Login dari browser biasa, lalu navigasi beberapa halaman setelah login.
3. **Bukti berfungsi:** di log keamanan atau event device, cari jejak pendaftaran/akses perangkat (metadata fingerprint, IP, user agent). Di kode, perangkat baru juga memicu logging lewat `logDeviceEvent` saat registrasi.
4. **Trust score rendah:** nilai trust di bawah ambang `DEVICE_TRUST_SCORE_THRESHOLD` (default 70) membuat `requiresVerification` bernilai true. Rute `device.verify` **belum** didefinisikan di proyek ini; jika trust rendah, middleware mencatat anomaly `device_verification_route_missing` alih-alih memblokir (fail open). Untuk uji “peringatan”, Anda bisa sementara menaikkan threshold ke misalnya `95` agar lebih mudah memicu kondisi “perlu verifikasi” lalu periksa log.

### 2.3 Skor risiko, step-up MFA, dan pemblokiran risiko kritis

Skor risiko dihitung di `ContextAwareAccessService::calculateRiskScore()` (faktor antara lain: negara GeoIP vs daftar diizinkan, jam di luar 08:00–17:00, akhir pekan, perubahan IP dari pola sebelumnya, selisih besar GPS vs GeoIP). Ambang step-up dan blokir diatur oleh `RISK_SCORE_THRESHOLD_HIGH` (default 70) dan `RISK_SCORE_THRESHOLD_CRITICAL` (default 85).

**Menguji step-up MFA (redirect ke `/mfa/verify`):**

- Cara praktis untuk demo: **turunkan sementara** ambang di `.env`, misalnya `RISK_SCORE_THRESHOLD_HIGH=30` dan pastikan `RISK_SCORE_THRESHOLD_CRITICAL` tetap lebih tinggi (misalnya `85`), lalu `config:clear`.
- Akses aplikasi saat skor risiko mudah terkumpul, misalnya **di luar jam kerja** (+10) dan/atau **akhir pekan** (+5), atau setelah mengganti jaringan sehingga **IP berbeda** dari pola di cache (+15). Gabungan beberapa faktor + GeoIP/GPS (jika diaktifkan) dapat menaikkan skor dengan cepat.
- **Bukti berfungsi:** browser dialihkan ke halaman verifikasi MFA; setelah kode TOTP benar, Anda kembali ke URL yang diminta (`url.intended`). Di session terkait step-up, alur ini terhubung dengan `mfa_step_up_action` dan `mfa_verified_high_risk` di `MfaVerificationController` / `MfaService`.

**Menguji pemblokiran risiko sangat tinggi (403):**

- Dengan ambang kritis default 85, skor harus sangat tinggi. Untuk uji terkontrol, Anda bisa sementara set `RISK_SCORE_THRESHOLD_CRITICAL=40` sambil mempertahankan skenario risiko tinggi di atas, lalu akses halaman yang dilindungi — **harap kembalikan nilai ke default setelah uji.**
- Respons JSON untuk request yang mengharapkan JSON berisi pesan error terkait high risk score (lihat middleware).

### 2.4 Konteks jam kerja & permission (403 berbasis konteks)

1. Gunakan akun yang punya permission `admin.panel` (agen/admin) **tanpa** permission `admin.after_hours_access` (sesuai seeder/role Anda).
2. Akses panel (misalnya `/agent`) **di luar jam 08:00–17:00** (sesuai timezone `APP_TIMEZONE`).
3. **Bukti berfungsi:** akses ditolak (403) dengan pesan konteks keamanan; anomaly `after_hours_access_attempt` dapat tercatat.

*Catatan:* ini adalah **context-aware access** terpisah dari murni “risk score”; keduanya bisa berlaku bergantung route dan permission.

### 2.5 GeoIP (MaxMind) & negara yang diizinkan

1. Unduh database **GeoLite2 City** (format `.mmdb`) dari MaxMind, letakkan misalnya di `storage/app/geoip/GeoLite2-City.mmdb`.
2. Set `GEO_LOCATION_ENABLED=true`, `GEOIP_DB_PATH` mengarah ke file tersebut, dan `ALLOWED_COUNTRIES` sesuai uji (misalnya hanya `ID`).
3. Uji dari IP publik yang terdeteksi negara di luar daftar (atau sesuaikan daftar untuk memaksa mismatch di lingkungan uji).
4. **Bukti berfungsi:** skor risiko naik (+30 untuk negara tidak diizinkan); kombinasi dengan faktor lain dapat memicu step-up atau (jika skor sangat tinggi) pemblokiran.

### 2.6 GPS browser → endpoint Zero Trust

1. Pastikan aset frontend ter-build dan halaman memuat `app.js` (yang mengimpor `geo-location.js`).
2. Login, lalu **izinkan akses lokasi** di browser.
3. Buka DevTools → tab **Network**, filter `zero-trust` atau `gps`.
4. **Bukti berfungsi:** request `POST /zero-trust/gps` dengan status 200 dan body respons JSON `{"status":"ok"}`; session menyimpan `zero_trust_gps` untuk dipakai analisis konteks.
5. Jika GeoIP aktif dan koordinat jauh dari perkiraan kota IP, skor risiko dapat naik karena selisih jarak (mismatch besar +20, sedang +10) — berguna untuk demo “konsistensi lokasi”.

### 2.7 Enkripsi lampiran & unduhan

1. Unggah lampiran baru melalui alur tiket yang memakai `FileEncryptionService::storeEncrypted()` (sesuai implementasi upload proyek).
2. **Bukti berfungsi:** di disk `local`, berkas tersimpan dengan nama berakhiran `.enc`; pada basis data, flag/metadata `is_encrypted` sesuai.
3. Unduh lampiran dari URL `attachments/{id}/download` sebagai pengguna yang berhak.
4. File terbuka normal; isi di disk tetap terenkripsi. Coba akses unduhan tanpa hak — harus ditolak (403) dan tercatat di log keamanan.

### 2.8 Secret MFA terenkripsi di database

1. Aktifkan MFA untuk satu pengguna uji lewat alur `mfa/setup`.
2. Lihat kolom `mfa_secret` di tabel `users` (phpMyAdmin, Artisan tinker, atau tool DB).
3. **Bukti berfungsi:** nilai tidak berupa secret TOTP plaintext yang panjang saja, melainkan data terenkripsi Laravel (biasanya diawali `eyJ` jika berbentuk JSON payload terenkripsi), konsisten dengan `encrypt()` di `MfaService`.

### 2.9 Checklist singkat (untuk lampiran laporan)

| Fitur | Langkah utama | Bukti disarankan |
|-------|----------------|-------------------|
| ZT aktif | `ZERO_TRUST_ENABLED=true`, browsing setelah login | Cuplikan log `security-*.log` dengan event akses |
| Step-up MFA | Turunkan sementara `RISK_SCORE_THRESHOLD_HIGH`, picu skor risiko | Screenshot redirect `/mfa/verify` + sukses setelah TOTP |
| Jam kerja | Akses panel admin di luar jam kerja tanpa izin after-hours | Screenshot 403 |
| GPS | Izin lokasi, lihat Network | Screenshot `POST .../zero-trust/gps` 200 |
| Lampiran | Upload → cek `.enc` → unduh | Screenshot storage + unduhan sukses |
| GeoIP | File `.mmdb` + `GEO_LOCATION_ENABLED=true` | Screenshot konfigurasi + log/country di konteks |

---

## 3. Ringkasan perbedaan per area

| No | Area | Sebelum (tanpa ZT aktif / pola lama) | Sesudah (ZT aktif / penambahan) |
|----|------|--------------------------------------|----------------------------------|
| 1 | **Alur HTTP setelah login** | Request langsung ke controller setelah auth + MFA wajib (middleware lain). | Ditambah lapisan **ZeroTrustVerification**: fingerprint perangkat, skor risiko konteks, validasi session periodik, log akses. |
| 2 | **MFA** | Verifikasi TOTP saat login; secret bisa disimpan lebih aman dengan enkripsi. | Ditambah **step-up MFA** jika `risk_score` tinggi (`mfa_step_up_action`, session `mfa_verified_high_risk`); alur di `MfaVerificationController` + `MfaService`. |
| 3 | **Perangkat (device)** | Tidak ada penilaian “perangkat dikenal” secara sistematis per request. | **DeviceFingerprintService**: fingerprint, trust score, registrasi/update last seen, opsi alur verifikasi perangkat jika route tersedia. |
| 4 | **Konteks akses (IP, waktu, lokasi)** | Akses utama berdasarkan role/permission saja. | **ContextAwareAccessService**: jam kerja untuk admin panel, GeoIP (jika DB & opsi aktif), blok IP, kombinasi ke **risk score**; opsional **GPS** dari browser. |
| 5 | **Data lampiran** | File bisa disimpan plaintext di storage publik (pol lama). | **FileEncryptionService** + upload: file disimpan terenkripsi (`.enc`); **AttachmentController** mendekripsi saat unduh + logging keamanan. |
| 6 | **Secret MFA di database** | Risiko penyimpanan plaintext (tergantung implementasi lama). | Secret disimpan dengan **`encrypt()`** Laravel sebelum ke kolom `mfa_secret` (lihat `MfaService`). |
| 7 | **Frontend** | Tidak ada pengiriman lokasi ke server untuk keamanan. | **`resources/js/geo-location.js`** (diimpor dari `app.js`): kirim koordinat ke `POST /zero-trust/gps` jika user mengizinkan. |
| 8 | **Routing** | Tidak ada endpoint khusus Zero Trust / GPS. | Route `zero_trust.gps.update` di `routes/web.php`; route download lampiran dengan dekripsi. |
| 9 | **Dependency** | Stack Laravel standar + MFA (Google2FA), permission. | Ditambah **`geoip2/geoip2`** untuk MaxMind GeoIP (lihat `composer.json`). |
| 10 | **Bootstrap middleware** | Hanya middleware umum (aktivitas, MFA required). | **`ZeroTrustVerification`** didaftarkan di `bootstrap/app.php` pada grup `web`. |

---

## 4. Detail per komponen (untuk teks laporan)

### 4.1 Middleware & bootstrap aplikasi

- **Sebelum:** Pipeline `web` tidak menyertakan verifikasi Zero Trust berkelanjutan.
- **Sesudah:** `bootstrap/app.php` menambahkan `\App\Http\Middleware\ZeroTrustVerification::class` setelah middleware terkait auth/MFA.
- **File:** `bootstrap/app.php`, `app/Http/Middleware/ZeroTrustVerification.php`

**Saran screenshot**

1. Cuplikan `bootstrap/app.php` yang memuat daftar middleware `web` (tunjukkan baris Zero Trust).
2. (Opsional) Log Laravel atau debug bar menunjukkan middleware yang dijalankan untuk satu request (jika alat Anda mendukung).

---

### 4.2 Konfigurasi Zero Trust

- **Sebelum:** Tidak ada file `config/zero_trust.php` atau tidak dipakai.
- **Sesudah:** Satu file konfigurasi mengatur enable flag, fingerprint, MFA, konteks, rate limit, interval validasi session, ambang risk score.
- **File:** `config/zero_trust.php`

**Saran screenshot**

1. Isi `config/zero_trust.php` (atau panel `.env` yang menunjuk `ZERO_TRUST_ENABLED=true` vs `false`).

---

### 4.3 MFA — login vs step-up (high risk)

- **Sebelum:** Halaman MFA terutama untuk login pertama kali setelah kredensial benar.
- **Sesudah:** Setelah login, jika konteks dinilai berisiko tinggi, middleware dapat mengarahkan ke `mfa.verify` dengan `mfa_step_up_action = high_risk` dan menyimpan URL tujuan (`url.intended`). Controller MFA menandai verifikasi step-up di session.
- **File:** `app/Http/Middleware/ZeroTrustVerification.php`, `app/Http/Controllers/Auth/MfaVerificationController.php`, `app/Services/MfaService.php`

**Saran screenshot**

1. **Sesudah:** Simulasikan skor risiko tinggi (sesuaikan threshold atau konteks uji) — tampilan redirect ke halaman verifikasi MFA dengan pesan/step-up.
2. **Sebelum:** `ZERO_TRUST_ENABLED=false` — tidak ada paksaan MFA tambahan setelah sesi login normal untuk skenario risiko yang sama.

---

### 4.4 Device fingerprint & trust score

- **Sebelum:** Tidak ada perhitungan fingerprint/trust score pada setiap request.
- **Sesudah:** `DeviceFingerprintService` menghasilkan fingerprint, menghitung trust score, mendaftarkan perangkat, memperbarui last seen; data dilampirkan ke request dan di-log.
- **File:** `app/Services/DeviceFingerprintService.php` (dipanggil dari `ZeroTrustVerification`)

**Saran screenshot**

1. Log aplikasi atau entri `security_events` / file `storage/logs/security-*.log` yang memuat `device_fingerprint` atau event `device` / `registered` (jika tersedia di lingkungan Anda).

---

### 4.5 Context-aware access & GeoIP / GPS

- **Sebelum:** Pembatasan jam kerja / negara / IP tidak terintegrasi dalam satu pipeline “risk score” global untuk setiap request.
- **Sesudah:** `ContextAwareAccessService` menggabungkan IP, waktu, GeoIP (opsional), GPS dari session (`zero_trust_gps`), dan evaluasi untuk permission tertentu; skor risiko memengaruhi step-up atau pemblokiran.
- **File:** `app/Services/ContextAwareAccessService.php`, route GPS di `routes/web.php`

**Saran screenshot**

1. Browser: izin lokasi → Network tab menunjukkan `POST /zero-trust/gps` dengan respons `{"status":"ok"}`.
2. **Sesudah:** Halaman error 403 jika skor risiko kritis atau akses konteks ditolak (sesuai skenario uji).
3. Konsol browser: log `[ZeroTrust] geo-location.js loaded` (hanya untuk membuktikan skrip aktif — opsional).

---

### 4.6 Proteksi lampiran (enkripsi at rest)

- **Sebelum:** Unduhan lampiran dari path storage tanpa lapisan enkripsi server-side standar di alur ini.
- **Sesudah:** Upload memakai `FileEncryptionService::storeEncrypted()`; unduhan memakai dekripsi stream; metadata `is_encrypted` pada model; logging `attachment_download_*`.
- **File:** `app/Services/FileEncryptionService.php`, `app/Http/Controllers/AttachmentController.php`, route `attachments.download`

**Saran screenshot**

1. Storage/disk: file lampiran dengan ekstensi `.enc` di direktori lokal (bukan plaintext).
2. UI unduhan berhasil untuk user yang berhak.
3. Cuplikan kode `FileEncryptionService` atau `AttachmentController` yang menunjukkan cabang `is_encrypted`.

---

### 4.7 Logging & audit keamanan

- **Sebelum:** Log aplikasi umum; tidak selalu ada jejak terpusat per akses dengan risk score + fingerprint.
- **Sesudah:** `SecurityEventLogService` dipanggil dari middleware dan aksi sensitif (contoh: unduhan lampiran, anomaly, auth MFA gagal).
- **File:** referensi pemanggilan di `ZeroTrustVerification`, `AttachmentController`, `MfaVerificationController`

**Saran screenshot**

1. Isi file `storage/logs/security-YYYY-MM-DD.log` atau tabel `security_events` dengan event `access`, `high_risk_access`, `attachment_download_attempt`, dll.

---

### 4.8 Frontend (JavaScript)

- **Sebelum:** `resources/js/app.js` hanya bootstrap + Alpine + session monitor.
- **Sesudah:** Impor `./geo-location` untuk mendukung pengiriman GPS opsional ke backend.
- **File:** `resources/js/app.js`, `resources/js/geo-location.js`

**Saran screenshot**

1. Perbandingan diff atau dua versi `app.js` (baris import geo-location).
2. Tab Network dengan request ke `/zero-trust/gps`.

---

## 5. Peta file utama (referensi cepat)

| File | Peran dalam Zero Trust |
|------|-------------------------|
| `config/zero_trust.php` | Saklar & parameter |
| `bootstrap/app.php` | Registrasi middleware |
| `app/Http/Middleware/ZeroTrustVerification.php` | Inti verifikasi berkelanjutan |
| `app/Services/DeviceFingerprintService.php` | Fingerprint & trust |
| `app/Services/ContextAwareAccessService.php` | Konteks, GeoIP, risk |
| `app/Services/FileEncryptionService.php` | Enkripsi/dekripsi lampiran |
| `app/Services/MfaService.php` | MFA + enkripsi secret |
| `app/Http/Controllers/Auth/MfaVerificationController.php` | MFA login + step-up |
| `app/Http/Controllers/AttachmentController.php` | Unduhan aman + log |
| `routes/web.php` | `POST /zero-trust/gps`, download lampiran |
| `resources/js/app.js` | Impor geo-location |
| `resources/js/geo-location.js` | Kirim GPS ke server |
| `composer.json` | Dependensi `geoip2/geoip2` |

---

## 6. Dokumen teknis tambahan di repositori

- **`ZERO_TRUST_DEFENSES.md`** — penjelasan lapisan pertahanan (MFA, device, enkripsi, continuous verification, logging) beserta diagram alur.
- **`integrasi-keamanan-scope.md`** — batas cakupan (Google2FA & Spatie Permission; tanpa SSO/AD).

Gunakan dokumen ini sebagai **checklist screenshot**: untuk setiap baris di tabel ringkasan (bagian 3), ambil minimal satu bukti visual sebelum (`ZERO_TRUST_ENABLED=false`) dan sesudah (`true`) jika perilakunya dapat diamankan di lingkungan pengujian. Panduan uji fungsional ada di **bagian 2**.

---

*Dokumen dibantu untuk keperluan laporan magang / tugas akhir; sesuaikan nama environment dan tangkapan layar dengan lingkungan pengujian Anda.*
