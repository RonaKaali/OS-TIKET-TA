# Panduan Setup Google2FA untuk Login

## 📋 Overview

Sistem sekarang mendukung **Two-Factor Authentication (2FA)** menggunakan **Google Authenticator** atau aplikasi authenticator lainnya (Authy, Microsoft Authenticator, dll).

## 🚀 Instalasi Package

### 1. Install Google2FA Package

```bash
composer require pragmarx/google2fa
```

### 2. Install QR Code Generator (Opsional)

Untuk menampilkan QR code di halaman setup:

```bash
composer require simplesoftwareio/simple-qrcode
```

## 📝 Cara Menggunakan

### Untuk User: Setup MFA

1. **Login ke aplikasi**

2. **Akses halaman setup MFA**

    - Buka: `http://localhost:8000/mfa/setup`
    - Atau tambahkan link di profile page

3. **Scan QR Code**

    - Buka aplikasi authenticator di smartphone (Google Authenticator, Authy, dll)
    - Scan QR code yang ditampilkan
    - Atau masukkan secret key secara manual

4. **Verifikasi Setup**

    - Masukkan kode 6 digit dari aplikasi authenticator
    - Klik "Aktifkan MFA"

5. **Simpan Backup Codes**
    - Setelah MFA aktif, backup codes akan ditampilkan
    - **PENTING**: Simpan backup codes di tempat yang aman
    - Backup codes bisa digunakan jika kehilangan akses ke aplikasi authenticator

### Untuk User: Login dengan MFA

1. **Login seperti biasa**

    - Masukkan email dan password
    - Klik "Masuk"

2. **Verifikasi MFA**

    - Setelah login berhasil, jika MFA aktif, akan diarahkan ke halaman verifikasi MFA
    - Buka aplikasi authenticator
    - Masukkan kode 6 digit
    - Klik "Verifikasi"

3. **Akses Berhasil**
    - Setelah verifikasi MFA berhasil, akan diarahkan ke dashboard

### Untuk User: Nonaktifkan MFA

1. **Akses Profile Settings**

    - Buka halaman profile
    - Cari opsi "Nonaktifkan MFA"

2. **Konfirmasi Password**

    - Masukkan password saat ini untuk konfirmasi

3. **MFA Dinonaktifkan**
    - MFA akan dinonaktifkan
    - Login tidak lagi memerlukan kode MFA

## 🔧 Untuk Developer: Implementasi

### Flow Login dengan MFA

```
1. User submit login form
   ↓
2. AuthenticatedSessionController->store()
   - Validasi email & password
   - Cek apakah user punya MFA enabled
   ↓
3. Jika MFA enabled:
   - Redirect ke /mfa/verify
   - Jangan complete session setup dulu
   ↓
4. User masukkan kode MFA
   ↓
5. MfaVerificationController->store()
   - Verifikasi TOTP code
   - Jika valid, complete session setup
   - Redirect ke dashboard
```

### Routes yang Tersedia

```php
// MFA Verification (setelah login)
GET  /mfa/verify          - Tampilkan form verifikasi MFA
POST /mfa/verify          - Proses verifikasi MFA

// MFA Management (harus login)
GET  /mfa/setup           - Setup MFA (generate QR code)
POST /mfa/enable          - Aktifkan MFA
GET  /mfa/backup-codes    - Tampilkan backup codes
POST /mfa/disable         - Nonaktifkan MFA
```

### Controller yang Dibuat

1. **MfaVerificationController** (`app/Http/Controllers/Auth/MfaVerificationController.php`)

    - Handle verifikasi MFA setelah login

2. **MfaController** (`app/Http/Controllers/MfaController.php`)
    - Handle setup, enable, disable MFA

### Service yang Digunakan

-   **MfaService** (`app/Services/MfaService.php`)
    -   Generate secret
    -   Generate QR code URL
    -   Verify TOTP code
    -   Manage backup codes

## 📱 Aplikasi Authenticator yang Didukung

-   ✅ Google Authenticator
-   ✅ Authy
-   ✅ Microsoft Authenticator
-   ✅ 1Password
-   ✅ LastPass Authenticator
-   ✅ Aplikasi authenticator lainnya yang mendukung TOTP

## 🔐 Backup Codes

Backup codes adalah kode cadangan yang bisa digunakan jika:

-   Kehilangan akses ke aplikasi authenticator
-   Smartphone rusak/hilang
-   Tidak bisa mengakses aplikasi authenticator

**PENTING**:

-   Simpan backup codes di tempat yang aman
-   Jangan share backup codes dengan siapapun
-   Setiap backup code hanya bisa digunakan sekali

## ⚠️ Troubleshooting

### Masalah: "Kode verifikasi tidak valid"

**Solusi**:

1. Pastikan waktu di smartphone sudah benar (sinkronisasi waktu)
2. Pastikan menggunakan kode terbaru (kode berubah setiap 30 detik)
3. Pastikan secret key sudah benar di aplikasi authenticator

### Masalah: "Secret tidak ditemukan"

**Solusi**:

1. Setup MFA ulang dari awal
2. Pastikan tidak melewati batas waktu (10 menit untuk setup)

### Masalah: Tidak bisa login setelah enable MFA

**Solusi**:

1. Gunakan backup code jika tersedia
2. Hubungi admin untuk reset MFA
3. Atau nonaktifkan MFA melalui database (jika punya akses)

## 🔒 Security Best Practices

1. **Enable MFA untuk semua admin dan agent**
2. **Simpan backup codes dengan aman**
3. **Jangan share QR code atau secret key**
4. **Gunakan aplikasi authenticator yang terpercaya**
5. **Nonaktifkan MFA jika tidak digunakan lagi**

## 📚 Referensi

-   [Google Authenticator](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2)
-   [Authy](https://authy.com/)
-   [PragmaRX Google2FA Documentation](https://github.com/antonioribeiro/google2fa)

---

**Version**: 1.0
