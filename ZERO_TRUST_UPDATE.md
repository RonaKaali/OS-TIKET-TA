# Zero Trust Security - Update & Migration Guide

## 📋 Update yang Telah Dilakukan

### 1. Database Migrations

Tiga migration baru telah dibuat:

1. **`2025_12_10_000001_create_device_fingerprints_table.php`**
   - Tabel untuk menyimpan device fingerprints
   - Menyimpan informasi device, trust score, dan metadata

2. **`2025_12_10_000002_create_security_events_table.php`**
   - Tabel untuk menyimpan security events
   - Menyimpan semua event keamanan untuk audit trail

3. **`2025_12_10_000003_add_zero_trust_fields_to_users_table.php`**
   - Menambahkan field Zero Trust ke tabel `pengguna`
   - Field MFA, device trust, dan security settings

### 2. Model Baru

1. **`DeviceFingerprint` Model**
   - Model untuk device fingerprints
   - Relasi ke User
   - Helper methods untuk update trust score dan verification

2. **`SecurityEvent` Model**
   - Model untuk security events
   - Relasi ke User
   - Query scopes untuk filtering

### 3. Update User Model

User model telah diupdate dengan:
- Field Zero Trust di `$fillable`
- Casting untuk field baru
- Relasi ke `DeviceFingerprint` dan `SecurityEvent`
- Helper methods:
  - `hasMfaEnabled()` - Cek apakah MFA aktif
  - `requiresDeviceVerification()` - Cek apakah perlu verifikasi device
  - `isIpAllowed($ip)` - Cek apakah IP diizinkan
  - `addIpToWhitelist($ip)` - Tambah IP ke whitelist
  - `removeIpFromWhitelist($ip)` - Hapus IP dari whitelist
  - `updateLastSecurityEvent()` - Update timestamp security event

### 4. Service Updates

- **SecurityEventLogService**: Sekarang menyimpan ke database jika model tersedia
- **DeviceFingerprintService**: Sekarang menyimpan ke database jika model tersedia

## 🚀 Cara Menjalankan Migration

### Step 1: Jalankan Migration

```bash
php artisan migrate
```

Ini akan membuat:
- Tabel `device_fingerprints`
- Tabel `security_events`
- Menambahkan field Zero Trust ke tabel `pengguna`

### Step 2: Verifikasi Migration

```bash
php artisan migrate:status
```

Pastikan semua migration berstatus "Ran".

### Step 3: Test Database Models

```bash
php artisan tinker
```

```php
// Test DeviceFingerprint model
$user = App\Models\User::first();
$device = App\Models\DeviceFingerprint::create([
    'user_id' => $user->id,
    'fingerprint' => 'test_fingerprint_123',
    'trust_score' => 75,
    'registered_at' => now(),
]);
echo "Device created: " . $device->id;

// Test SecurityEvent model
$event = App\Models\SecurityEvent::create([
    'user_id' => $user->id,
    'event_type' => 'test_event',
    'severity' => 'low',
    'message' => 'Test security event',
    'created_at' => now(),
]);
echo "Event created: " . $event->id;

// Test User helper methods
$user->hasMfaEnabled(); // false
$user->isIpAllowed('127.0.0.1'); // true (jika tidak ada whitelist)
```

## 📊 Struktur Database

### Tabel `device_fingerprints`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke pengguna |
| fingerprint | string(64) | SHA256 hash fingerprint |
| user_agent | string | Browser user agent |
| ip_address | string(45) | IP address |
| screen_resolution | string | Screen resolution |
| timezone | string | Timezone |
| trust_score | integer | Trust score (0-100) |
| registered_at | timestamp | Waktu registrasi |
| last_seen_at | timestamp | Waktu terakhir terlihat |
| is_verified | boolean | Apakah device sudah diverifikasi |
| metadata | json | Metadata tambahan |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

### Tabel `security_events`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke pengguna (nullable) |
| event_type | string(100) | Tipe event |
| severity | enum | low, medium, high, critical |
| ip_address | string(45) | IP address |
| user_agent | text | Browser user agent |
| device_fingerprint | string(64) | Device fingerprint |
| context | json | Context data |
| message | text | Event message |
| metadata | json | Metadata tambahan |
| risk_score | integer | Risk score (0-100) |
| created_at | timestamp | Event timestamp |

### Field Baru di Tabel `pengguna`

| Column | Type | Description |
|--------|------|-------------|
| mfa_enabled | boolean | Apakah MFA aktif |
| mfa_secret | text | Encrypted TOTP secret |
| mfa_enabled_at | timestamp | Waktu MFA diaktifkan |
| device_trust_threshold | integer | Threshold trust score untuk user |
| require_device_verification | boolean | Apakah perlu verifikasi device |
| ip_whitelist | json | Daftar IP yang diizinkan |
| allow_after_hours_access | boolean | Apakah boleh akses di luar jam kerja |
| last_security_event_at | timestamp | Waktu security event terakhir |

## 🔄 Rollback Migration (Jika Diperlukan)

Jika perlu rollback migration:

```bash
php artisan migrate:rollback --step=3
```

Ini akan:
1. Menghapus field Zero Trust dari tabel `pengguna`
2. Menghapus tabel `security_events`
3. Menghapus tabel `device_fingerprints`

## 💡 Penggunaan Model

### Device Fingerprint

```php
use App\Models\DeviceFingerprint;

// Dapatkan semua device untuk user
$devices = $user->deviceFingerprints;

// Dapatkan device dengan trust score tinggi
$trustedDevices = DeviceFingerprint::where('user_id', $user->id)
    ->where('trust_score', '>=', 70)
    ->get();

// Update trust score
$device->updateTrustScore(85);

// Mark as verified
$device->markAsVerified();
```

### Security Events

```php
use App\Models\SecurityEvent;

// Dapatkan semua security events untuk user
$events = $user->securityEvents;

// Dapatkan high severity events
$highSeverityEvents = SecurityEvent::bySeverity('high')
    ->recent(24) // Last 24 hours
    ->get();

// Dapatkan high risk events
$highRiskEvents = SecurityEvent::highRisk(70)
    ->recent(7) // Last 7 days
    ->get();

// Dapatkan events berdasarkan type
$authEvents = SecurityEvent::byEventType('auth_login')
    ->recent(1)
    ->get();
```

### User Helper Methods

```php
// Cek MFA
if ($user->hasMfaEnabled()) {
    // MFA aktif
}

// Cek IP whitelist
if ($user->isIpAllowed($request->ip())) {
    // IP diizinkan
}

// Tambah IP ke whitelist
$user->addIpToWhitelist('192.168.1.100');

// Hapus IP dari whitelist
$user->removeIpFromWhitelist('192.168.1.100');
```

## ⚠️ Catatan Penting

1. **Data Migration**: Data yang sudah ada di cache tidak akan otomatis pindah ke database. Jika perlu, buat script untuk migrate data dari cache ke database.

2. **Performance**: Query ke database akan lebih lambat dibanding cache. Pertimbangkan untuk tetap menggunakan cache untuk data yang sering diakses.

3. **Backup**: Selalu backup database sebelum menjalankan migration.

4. **Testing**: Test migration di environment development terlebih dahulu.

## 📚 Referensi

- [ZERO_TRUST_SECURITY_GUIDE.md](./ZERO_TRUST_SECURITY_GUIDE.md) - Panduan lengkap
- [ZERO_TRUST_INSTALLATION.md](./ZERO_TRUST_INSTALLATION.md) - Instalasi
- [ZERO_TRUST_QUICK_START.md](./ZERO_TRUST_QUICK_START.md) - Quick start

---

**Version**: 1.0  
**Last Updated**: [Tanggal]

