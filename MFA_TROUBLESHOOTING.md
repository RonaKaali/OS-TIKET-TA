# MFA Troubleshooting Guide

## 🔧 Masalah: Kode MFA Tidak Valid Meskipun Sudah Benar

### Gejala

-   Kode di Google Authenticator sudah sesuai
-   Saat diinput, muncul error "Kode verifikasi tidak valid"
-   Setup MFA gagal

### Penyebab Umum

1. **Sinkronisasi Waktu**

    - Waktu di server dan smartphone tidak sinkron
    - TOTP sangat sensitif terhadap waktu

2. **Window Verification Terlalu Kecil**

    - Window verification default mungkin terlalu ketat

3. **Secret Key Tidak Cocok**

    - Secret key di aplikasi authenticator berbeda dengan yang di server

4. **Format Code**
    - Code mungkin ada spasi atau karakter non-numeric

## ✅ Solusi yang Diterapkan

### 1. Perbesar Window Verification

Window verification diperbesar dari 1 menjadi 2:

-   **Window 1**: ±30 detik (total 90 detik)
-   **Window 2**: ±60 detik (total 180 detik) ← **Sekarang digunakan**

Ini memberikan toleransi lebih besar untuk perbedaan waktu.

### 2. Validasi Code yang Lebih Ketat

```php
// Pastikan code adalah 6 digit numeric
if (!preg_match('/^\d{6}$/', $code)) {
    return back()->withErrors(['code' => 'Kode harus berupa 6 digit angka.']);
}
```

### 3. Logging untuk Debugging

Logging ditambahkan untuk membantu debugging:

-   Code yang diinput
-   Panjang secret
-   Preview secret (untuk verifikasi)

## 🔍 Cara Debugging

### 1. Cek Log

```bash
tail -f storage/logs/laravel.log
```

Cari log dengan keyword "MFA setup verification failed" untuk melihat detail error.

### 2. Verifikasi Waktu Server

```bash
php artisan tinker
```

```php
// Cek waktu server
now()->toDateTimeString();
date('Y-m-d H:i:s');

// Cek timezone
config('app.timezone');
```

### 3. Test Manual Verification

```php
$user = App\Models\User::find(1);
$mfaService = app(App\Services\MfaService::class);
$secret = cache()->get("mfa_secret_temp:{$user->id}");

// Test dengan kode tertentu
$code = '123456'; // Ganti dengan kode dari authenticator
$google2fa = new \PragmaRX\Google2FA\Google2FA();
$valid = $google2fa->verifyKey($secret, $code, 2);
var_dump($valid);
```

## 🛠️ Troubleshooting Steps

### Step 1: Cek Sinkronisasi Waktu

**Di Smartphone:**

1. Buka Settings → Date & Time
2. Pastikan "Automatic date & time" aktif
3. Atau set waktu manual sesuai dengan waktu server

**Di Server:**

```bash
# Cek waktu server
date

# Sync waktu (jika perlu)
sudo ntpdate -s time.nist.gov
```

### Step 2: Verifikasi Secret Key

1. Buka halaman setup MFA
2. Copy secret key manual (bukan scan QR)
3. Pastikan secret key yang dimasukkan di aplikasi authenticator sama persis

### Step 3: Coba Kode Baru

1. Tunggu kode berubah (setiap 30 detik)
2. Masukkan kode yang baru muncul
3. Jangan gunakan kode yang sudah lama

### Step 4: Reset Setup

Jika masih gagal, reset setup MFA:

```php
// Di tinker
$user = App\Models\User::find(1);
cache()->forget("mfa_secret_temp:{$user->id}");
```

Kemudian setup ulang dari awal.

## 📋 Checklist Troubleshooting

-   [ ] Waktu di smartphone sudah benar (sinkronisasi)
-   [ ] Menggunakan kode terbaru (tidak lebih dari 30 detik)
-   [ ] Secret key sudah benar di aplikasi authenticator
-   [ ] Code adalah 6 digit numeric (tidak ada spasi)
-   [ ] Window verification sudah diperbesar (window 2)
-   [ ] Cek log untuk detail error

## 🔐 Best Practices

1. **Sinkronisasi Waktu Otomatis**

    - Aktifkan automatic time sync di smartphone
    - Pastikan server menggunakan NTP

2. **Gunakan Kode Segera**

    - Masukkan kode segera setelah muncul
    - Jangan tunggu sampai kode hampir berubah

3. **Verifikasi Secret Key**

    - Jika scan QR gagal, gunakan manual entry
    - Pastikan secret key sama persis

4. **Test Sebelum Production**
    - Test MFA di development environment
    - Pastikan waktu server dan smartphone sinkron

## 📚 Referensi

-   [Google Authenticator Troubleshooting](https://support.google.com/accounts/answer/1066447)
-   [TOTP RFC 6238](https://tools.ietf.org/html/rfc6238)
-   [PragmaRX Google2FA Documentation](https://github.com/antonioribeiro/google2fa)

---

**Version**: 1.0
