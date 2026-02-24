# Zero Trust Security - Timezone Fix

## 🔧 Masalah: Timezone Tidak Sesuai

### Gejala

-   Waktu lokal user: 11:00
-   Waktu di log: 03:00
-   Selisih: 8 jam (UTC+8)

### Penyebab

Aplikasi menggunakan timezone UTC, sedangkan user berada di Indonesia (WITA/WIB/WIT).

## ✅ Solusi yang Diterapkan

### 1. Update Config Timezone

File `config/app.php` telah diupdate untuk menggunakan environment variable:

```php
'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
```

### 2. Set Timezone di .env

Tambahkan ke file `.env`:

```env
# Timezone Indonesia
# Pilih sesuai lokasi:
# - Asia/Jakarta (WIB - UTC+7) untuk Jakarta, Bandung, dll
# - Asia/Makassar (WITA - UTC+8) untuk Makassar, Balikpapan, dll
# - Asia/Jayapura (WIT - UTC+9) untuk Jayapura, dll

APP_TIMEZONE=Asia/Jakarta
```

### 3. Clear Config Cache

Setelah update `.env`, jalankan:

```bash
php artisan config:clear
php artisan config:cache
```

## 📋 Timezone Indonesia

| Timezone        | Lokasi                                 | UTC Offset   |
| --------------- | -------------------------------------- | ------------ |
| `Asia/Jakarta`  | Jakarta, Bandung, Yogyakarta, Surabaya | UTC+7 (WIB)  |
| `Asia/Makassar` | Makassar, Balikpapan, Banjarmasin      | UTC+8 (WITA) |
| `Asia/Jayapura` | Jayapura, Sorong                       | UTC+9 (WIT)  |

## 🔍 Verifikasi

### 1. Cek Timezone Aplikasi

```bash
php artisan tinker
```

```php
// Cek timezone
config('app.timezone');

// Cek waktu sekarang
now()->toDateTimeString();
now()->format('H:i');
```

### 2. Cek Log Security

Setelah update timezone, cek log security:

```bash
tail -f storage/logs/security-*.log
```

Waktu di log seharusnya sudah sesuai dengan waktu lokal.

### 3. Test Time-Based Access

Jika time-based access aktif, test dengan waktu yang berbeda:

```php
// Test di tinker
$user = App\Models\User::find(1);
$service = app(App\Services\ContextAwareAccessService::class);
$request = request();
$context = $service->analyzeContext($request, $user);

// Cek waktu di context
$context['time_of_day'];
$context['timezone'];
```

## ⚠️ Catatan Penting

1. **Database**: Waktu di database tetap disimpan dalam format UTC (best practice)
2. **Display**: Laravel otomatis convert ke timezone aplikasi saat display
3. **Log**: Waktu di log menggunakan timezone aplikasi
4. **Context**: Context-aware access menggunakan timezone aplikasi

## 🚀 Quick Fix

Jika ingin cepat fix tanpa edit `.env`:

```bash
# Edit langsung config/app.php
# Ganti 'timezone' => 'UTC' menjadi:
'timezone' => 'Asia/Jakarta',

# Clear cache
php artisan config:clear
```

## 📚 Referensi

-   [PHP Timezone List](https://www.php.net/manual/en/timezones.asia.php)
-   [Laravel Date/Time Documentation](https://laravel.com/docs/date-time)

---

**Version**: 1.0
