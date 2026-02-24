# Panduan Instalasi Zero Trust Security

## 📋 Prerequisites

Sebelum mengimplementasikan Zero Trust Security, pastikan:

-   ✅ Laravel 12.x
-   ✅ PHP 8.2+
-   ✅ Composer terinstall
-   ✅ Redis atau Cache driver yang mendukung (untuk device fingerprinting dan MFA)

## 🔧 Instalasi Package

### 1. Install Google2FA untuk MFA

```bash
composer require pragmarx/google2fa
```

### 2. Install QR Code Generator (opsional, untuk QR code MFA)

```bash
composer require simplesoftwareio/simple-qrcode
```

## ⚙️ Konfigurasi

### 1. Update Environment Variables

Tambahkan ke file `.env`:

```env
# Zero Trust Configuration
ZERO_TRUST_ENABLED=true
ZERO_TRUST_DEVICE_FINGERPRINTING=true
ZERO_TRUST_MFA_ENABLED=true
ZERO_TRUST_CONTEXT_AWARE=true

# Device Trust
DEVICE_TRUST_SCORE_THRESHOLD=70
DEVICE_TRUST_SESSION_DURATION=1440

# MFA Configuration
MFA_TOTP_ENABLED=true
MFA_EMAIL_ENABLED=true
MFA_BACKUP_CODES_COUNT=10

# Rate Limiting
RATE_LIMIT_REQUESTS_PER_MINUTE=60
RATE_LIMIT_REQUESTS_PER_HOUR=1000
RATE_LIMIT_ENABLE_ADAPTIVE=true

# Geolocation
GEO_LOCATION_ENABLED=false
ALLOWED_COUNTRIES=ID
BLOCKED_IPS=

# Session Security
SESSION_VALIDATION_INTERVAL=30
TOKEN_ROTATION_INTERVAL=300

# Risk Scoring
RISK_SCORE_THRESHOLD_HIGH=70
RISK_SCORE_THRESHOLD_CRITICAL=85

# Security Logging
LOG_SECURITY_DAYS=90
```

### 2. Clear Config Cache

```bash
php artisan config:clear
php artisan config:cache
```

### 3. Setup Cache Driver

Pastikan cache driver menggunakan Redis atau database:

```env
CACHE_DRIVER=redis
# atau
CACHE_DRIVER=database
```

Jika menggunakan database, jalankan:

```bash
php artisan cache:table
php artisan migrate
```

## 🗄️ Database Migrations (Opsional)

Jika ingin menyimpan security events ke database, buat migration:

```bash
php artisan make:migration create_security_events_table
```

Contoh migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('pengguna')->onDelete('set null');
            $table->string('event_type');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
```

Jalankan migration:

```bash
php artisan migrate
```

## 🧪 Testing

### 1. Test Device Fingerprinting

```bash
php artisan tinker
```

```php
$user = App\Models\User::first();
$request = request();
$service = app(App\Services\DeviceFingerprintService::class);
$fingerprint = $service->generateFingerprint($request);
echo $fingerprint;
```

### 2. Test MFA

```php
$user = App\Models\User::first();
$mfaService = app(App\Services\MfaService::class);
$secret = $mfaService->generateSecret($user);
$qrUrl = $mfaService->getQrCodeUrl($user, $secret);
echo $qrUrl;
```

### 3. Test Security Event Logging

```php
$logService = app(App\Services\SecurityEventLogService::class);
$logService->logEvent([
    'user_id' => 1,
    'event_type' => 'test_event',
    'severity' => 'low',
    'message' => 'Test security event',
]);
```

## 🚀 Aktivasi Zero Trust

### Step 1: Enable Zero Trust

Set `ZERO_TRUST_ENABLED=true` di `.env`

### Step 2: Test dengan User

1. Login dengan user biasa
2. Cek apakah device fingerprinting bekerja
3. Cek log di `storage/logs/security.log`

### Step 3: Enable MFA untuk Admin

1. Buat route untuk MFA setup (lihat contoh di dokumentasi)
2. Admin harus setup MFA
3. Test login dengan MFA

## 📝 Catatan Penting

1. **Performance**: Zero Trust middleware akan menambahkan overhead pada setiap request. Monitor performance setelah implementasi.

2. **Cache**: Pastikan cache driver sudah dikonfigurasi dengan benar. Device fingerprints dan MFA secrets disimpan di cache.

3. **Logging**: Security logs akan bertambah. Pastikan storage cukup dan setup log rotation.

4. **Testing**: Test secara menyeluruh sebelum production deployment.

5. **Gradual Rollout**: Aktifkan Zero Trust secara bertahap:
    - Phase 1: Device fingerprinting saja
    - Phase 2: Tambahkan context-aware access
    - Phase 3: Tambahkan MFA untuk admin
    - Phase 4: Full Zero Trust untuk semua user

## 🔍 Troubleshooting

### Issue: Device fingerprinting tidak bekerja

**Solusi**:

-   Pastikan cache driver sudah dikonfigurasi
-   Cek apakah `ZERO_TRUST_DEVICE_FINGERPRINTING=true`
-   Cek log untuk error

### Issue: MFA tidak bekerja

**Solusi**:

-   Pastikan package `pragmarx/google2fa` sudah terinstall
-   Cek apakah secret sudah di-generate
-   Pastikan timezone server sudah benar (TOTP memerlukan waktu yang akurat)

### Issue: Middleware error

**Solusi**:

-   Pastikan semua service sudah di-register di service container
-   Cek apakah config file `zero_trust.php` sudah ada
-   Clear config cache: `php artisan config:clear`

## 📚 Referensi

-   [ZERO_TRUST_SECURITY_GUIDE.md](./ZERO_TRUST_SECURITY_GUIDE.md) - Panduan lengkap Zero Trust
-   [Laravel Cache Documentation](https://laravel.com/docs/cache)
-   [Google2FA Documentation](https://github.com/antonioribeiro/google2fa)

---

**Dibuat**: [Tanggal]  
**Version**: 1.0
