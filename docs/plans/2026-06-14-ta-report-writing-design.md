# Rancangan Penyusunan Laporan Tugas Akhir

## 1. Konteks Proyek
Judul: RANCANG BANGUN SISTEM INFORMASI PELAPORAN INSIDEN SIBER BERBASIS WEB DENGAN INTEGRASI ZERO TRUST SECURITY PADA DISKOMINFO PROVINSI KALIMANTAN SELATAN
Penulis: Muhammad Abrar Ridhani & Ahmad Rona Fatahilah

Fitur Utama Sistem yang Dibangun (Fokus TA):
1. **Zero Trust Security Architecture**:
   - *Device Fingerprinting*: Deteksi perangkat yang digunakan untuk login.
   - *Context-Aware Access*: Memverifikasi IP Address, User Agent, dan lokasi akses.
   - *Anomaly Detection*: Mendeteksi login dari IP/perangkat baru atau mencurigakan (Security Event Log).
2. **Zero Trust Dashboard (Super Admin)**:
   - Visualisasi log keamanan (Live Feed) dan metrik keamanan.
3. **Manajemen Insiden Siber & Surat Tugas**:
   - Penerbitan surat tugas penanganan insiden otomatis.
   - Fitur "Print to PDF" Surat Tugas Resmi.

## 2. Pendekatan "Panduan per Bab"
Kolaborasi penulisan akan dilakukan secara bertahap:
- **Langkah 1**: Pengguna memberikan rincian Bab yang sedang dikerjakan.
- **Langkah 2**: Asisten AI menyusun draft konten teknis (seperti deskripsi arsitektur, algoritma *Zero Trust*, dsb).
- **Langkah 3**: Pengguna me-review, menyalin (copy-paste) ke file Word, dan merapikan formatnya.

## 3. Fokus Utama Laporan (Area Teknis)
- **Bab 3 (Analisis dan Perancangan)**:
  - Arsitektur Sistem Zero Trust (Flowchart akses login).
  - Skema Database (tabel `security_event_logs`, `device_fingerprints`, `tickets`, dll).
- **Bab 4 (Implementasi dan Pengujian)**:
  - Implementasi Middleware Zero Trust di Laravel.
  - Tampilan (UI) Zero Trust Dashboard dan Surat Tugas.
  - Pengujian Deteksi Anomali (Login dari perangkat/IP berbeda).
