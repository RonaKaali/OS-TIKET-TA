# Proposal Tugas Akhir

## Judul

Implementasi Zero Trust Security

## 1. Pendahuluan

### 1.1 Latar Belakang

Perkembangan ancaman siber di Indonesia semakin meningkat, termasuk terhadap sistem pemerintahan yang menangani data sensitif. Sistem ticketing OS-Tiket yang telah dikembangkan untuk mendukung operasional CSIRT Provinsi Kalimantan Selatan menyediakan portal pelaporan insiden siber, dashboard agen, dan panel administrasi. Meski sistem ini sudah memenuhi kebutuhan fungsional dasar, keamanan aplikasi masih berfokus pada mekanisme konvensional seperti autentikasi username-password dan role-based access control (RBAC) sederhana.

Model keamanan Zero Trust Security menjadi relevan karena prinsipnya “Never Trust, Always Verify”. Dalam model ini, setiap akses diverifikasi secara terus menerus tanpa mengandalkan lokasi jaringan atau perimeter tradisional. Untuk sistem OS-Tiket yang memproses laporan insiden, data pelapor, lampiran, dan komunikasi internal CSIRT, penerapan Zero Trust dapat meningkatkan proteksi terhadap ancaman seperti session hijacking, brute force, credential stuffing, dan insider threat.

### 1.2 Permasalahan

Sistem OS-Tiket yang sudah ada memiliki beberapa keterbatasan keamanan, yaitu:

- Autentikasi dan otorisasi masih menggunakan mekanisme konvensional tanpa verifikasi berkelanjutan.
- Belum ada kontrol akses berbasis konteks (misalnya perangkat, lokasi, waktu, dan perilaku pengguna).
- Belum diterapkan Multi-Factor Authentication (MFA) secara menyeluruh.
- Kurangnya pemantauan dan pencatatan aktivitas keamanan secara komprehensif.
- Sistem belum sepenuhnya mengadopsi paradigma Zero Trust yang dapat mencegah akses tidak sah secara proaktif.

### 1.3 Tujuan

Tujuan dari tugas akhir ini adalah:

1. Menganalisis kondisi keamanan sistem OS-Tiket yang sudah ada.
2. Merancang arsitektur Zero Trust Security yang terintegrasi dengan sistem OS-Tiket.
3. Mengimplementasikan komponen Zero Trust Security, seperti MFA, device fingerprinting, context-aware access control, continuous session verification, dan security event logging.
4. Menguji efektivitas implementasi Zero Trust dalam meningkatkan keamanan sistem.
5. Menyusun dokumentasi dan evaluasi hasil penerapan Zero Trust pada sistem OS-Tiket.

### 1.4 Manfaat

Manfaat dari tugas akhir ini meliputi:

- Meningkatkan keamanan sistem OS-Tiket sehingga data insiden siber dan informasi pelapor lebih terlindungi.
- Menambah kemampuan CSIRT Kalselprov dalam mengelola akses pengguna dan mendeteksi aktivitas mencurigakan.
- Memberikan bukti implementasi Zero Trust pada sistem existing, sehingga menjadi referensi bagi pengembangan keamanan aplikasi pemerintahan.
- Mengurangi risiko serangan melalui akses tidak sah dengan mekanisme verifikasi berkelanjutan.

### 1.5 Keunikan Proyek

Proyek ini bukan membangun sistem baru. Fokus utama adalah **mengimplementasikan Zero Trust Security pada sistem yang sudah terbangun**, yaitu OS-Tiket. Hal ini membedakan proyek ini dari pembangunan aplikasi baru karena menitikberatkan pada integrasi keamanan dan hardening terhadap sistem existing.

## 2. Tinjauan Pustaka

### 2.1 Zero Trust Security

Zero Trust Security adalah paradigma keamanan yang menolak asumsi bahwa segala sesuatu di dalam perimeter jaringan dapat dipercaya. Standar NIST SP 800-207 mendefinisikan Zero Trust sebagai praktik untuk memverifikasi identitas, perangkat, dan konteks sebelum memberikan akses.

### 2.2 Multi-Factor Authentication (MFA)

MFA menambahkan lapisan autentikasi di luar username-password, misalnya TOTP, kode email, atau perangkat fisik. MFA membantu mencegah akses tidak sah meski kredensial pengguna telah bocor.

### 2.3 Device Fingerprinting dan Context-Aware Access

Device fingerprinting memungkinkan sistem mengidentifikasi perangkat yang digunakan pengguna. Context-aware access control memeriksa informasi seperti lokasi, waktu akses, dan pola perilaku untuk menilai risiko sebelum memberikan akses.

### 2.4 Continuous Verification dan Anomaly Detection

Dalam Zero Trust, verifikasi tidak berhenti setelah login. Setiap request dapat diperiksa ulang untuk mendeteksi anomali, menjaga validitas sesi, dan menanggapi aktivitas mencurigakan secara cepat.

## 3. Metode Pelaksanaan

### 3.1 Analisis Sistem

Melakukan studi atas arsitektur OS-Tiket yang sudah ada, termasuk autentikasi, otorisasi, alur login, dan model data pengguna. Identifikasi titik lemah keamanan yang perlu ditangani.

### 3.2 Perancangan Arsitektur Zero Trust

Merancang integrasi Zero Trust dengan sistem existing, termasuk komponen berikut:

- Middleware verifikasi Zero Trust
- Multi-Factor Authentication (MFA) berbasis TOTP
- Device fingerprinting dan device trust scoring
- Context-aware access control
- Logging event keamanan dan audit trail
- Validasi sesi berkelanjutan

### 3.3 Implementasi

Mengembangkan fitur keamanan pada aplikasi Laravel OS-Tiket dengan komponen Zero Trust yang telah dirancang. Menyesuaikan konfigurasi sistem tanpa mengubah fungsi operasional utama OS-Tiket.

### 3.4 Pengujian

Melakukan pengujian pada lingkungan development/testing untuk memastikan:

- MFA bekerja pada alur login dan akses sensitif.
- Device fingerprinting dapat membedakan perangkat baru dan lama.
- Context-aware access control menolak akses berdasarkan kondisi risiko.
- Sesi diverifikasi ulang secara berkala.
- Event keamanan tercatat dengan baik.

### 3.5 Evaluasi

Mengevaluasi hasil implementasi melalui pengukuran keamanan, perbandingan kondisi sebelum dan sesudah, serta dokumentasi bukti fungsionalitas Zero Trust.

## 4. Batasan Masalah

Untuk menjaga fokus dan kelayakan pekerjaan, batasan masalah dalam tugas akhir ini adalah sebagai berikut:

- Implementasi hanya dilakukan pada sistem OS-Tiket yang sudah ada, bukan membangun platform ticketing baru.
- Fokus pada keamanan aplikasi web, bukan pada keamanan infrastruktur fisik atau jaringan eksternal.
- Pengujian menggunakan data uji dan skenario terbatas, tanpa data produksi langsung.
- Integrasi dengan sistem eksternal seperti SSO enterprise atau Active Directory tidak termasuk.
- Fitur operasional OS-Tiket yang sudah ada tetap dipertahankan, sedangkan pengembangan fungsional baru dilimitasi pada komponen keamanan.

## 5. Jadwal Pelaksanaan

| Minggu | Kegiatan                                                          |
| ------ | ----------------------------------------------------------------- |
| 1-2    | Studi literatur dan analisis sistem OS-Tiket                      |
| 3-4    | Perancangan arsitektur Zero Trust dan komponen keamanan           |
| 5-6    | Implementasi MFA, device fingerprinting, dan context-aware access |
| 7-8    | Implementasi continuous session verification dan security logging |
| 9      | Pengujian fungsional dan keamanan                                 |
| 10     | Evaluasi hasil dan dokumentasi                                    |
| 11     | Penyusunan laporan tugas akhir                                    |
| 12     | Persiapan presentasi dan demo                                     |

## 6. Sistematika Penulisan

1. Pendahuluan
2. Tinjauan Pustaka
3. Metode Penelitian
4. Hasil dan Pembahasan
5. Kesimpulan dan Saran

## 7. Kesimpulan

Proyek tugas akhir ini berfokus pada peningkatan keamanan sistem OS-Tiket yang sudah berjalan dengan menerapkan prinsip Zero Trust Security. Kegiatan tidak membangun sistem baru, tetapi menguatkan dan mengamankan sistem existing melalui implementasi MFA, device fingerprinting, context-aware access, continuous verification, dan logging. Dengan demikian, sistem OS-Tiket diharapkan mampu menyajikan proteksi yang lebih kuat terhadap ancaman siber dan menjaga keandalan operasional CSIRT Kalselprov.

---

_Catatan: Silakan menyesuaikan bagian identitas penulis, pembimbing, dan tahun akademik sesuai kebutuhan._
