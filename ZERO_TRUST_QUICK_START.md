# Zero Trust Security - Quick Start Guide

## 🚀 Quick Start (5 Menit)

### 1. Install Package yang Diperlukan

```bash
composer require pragmarx/google2fa
```

### 2. Update .env

Tambahkan minimal konfigurasi ini:

```env
ZERO_TRUST_ENABLED=true
ZERO_TRUST_DEVICE_FINGERPRINTING=true
ZERO_TRUST_MFA_ENABLED=true
```

### 3. Clear Cache

```bash
php artisan config:clear
```

### 4. Test

Login ke aplikasi dan cek:

-   Device fingerprinting bekerja (cek `storage/logs/security.log`)
-   Session validation bekerja

## 📋 Konfigurasi Lengkap

Untuk konfigurasi lengkap, lihat file `.env.example` atau dokumentasi di `ZERO_TRUST_INSTALLATION.md`.

## 🎯 Fitur yang Tersedia

### ✅ Sudah Diimplementasikan

1. **Device Fingerprinting**

    - Generate device fingerprint dari request
    - Device registration dan tracking
    - Device trust scoring

2. **Continuous Verification**

    - Session validation per request
    - Token rotation
    - Activity monitoring

3. **Context-Aware Access Control**

    - Time-based access control
    - Location-based access control (jika GeoIP enabled)
    - IP whitelist/blacklist
    - Behavioral pattern analysis

4. **Security Event Logging**

    - Authentication events
    - Authorization events
    - Device events
    - Anomaly detection

5. **Multi-Factor Authentication (MFA)**
    - TOTP support (Google Authenticator, Authy, dll)
    - Backup codes
    - MFA verification per action

## 🔧 Cara Menggunakan

### Device Fingerprinting

Device fingerprinting otomatis aktif saat user login. Tidak perlu konfigurasi tambahan.

### MFA Setup

1. Buat route untuk MFA setup (contoh di bawah)
2. User setup MFA melalui interface
3. MFA akan diminta saat login atau akses sensitif

**Contoh Controller untuk MFA Setup:**

```php
<?php

namespace App\Http\Controllers;

use App\Services\MfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    public function __construct(
        protected MfaService $mfaService
    ) {}

    public function showSetup()
    {
        $user = Auth::user();
        $secret = $this->mfaService->generateSecret($user);
        $qrUrl = $this->mfaService->getQrCodeUrl($user, $secret);

        return view('mfa.setup', [
            'secret' => $secret,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = cache()->get("mfa_secret_temp:{$user->id}");

        if (!$secret) {
            return back()->withErrors(['code' => 'Secret tidak ditemukan. Silakan setup ulang.']);
        }

        if ($this->mfaService->enableMfa($user, $secret, $request->code)) {
            return redirect()->route('profile')->with('status', 'MFA berhasil diaktifkan!');
        }

        return back()->withErrors(['code' => 'Kode verifikasi tidak valid.']);
    }
}
```

### Security Event Monitoring

Akses security events melalui service:

```php
$logService = app(\App\Services\SecurityEventLogService::class);
$events = $logService->getRecentEvents(50);
```

## ⚠️ Catatan Penting

1. **Performance**: Zero Trust menambahkan overhead. Monitor performance setelah implementasi.

2. **Cache**: Pastikan cache driver sudah dikonfigurasi (Redis recommended).

3. **Logging**: Security logs akan bertambah. Setup log rotation.

4. **Testing**: Test secara menyeluruh sebelum production.

## 📚 Dokumentasi Lengkap

-   [ZERO_TRUST_SECURITY_GUIDE.md](./ZERO_TRUST_SECURITY_GUIDE.md) - Panduan lengkap
-   [ZERO_TRUST_INSTALLATION.md](./ZERO_TRUST_INSTALLATION.md) - Instalasi detail

---

**Version**: 1.0
