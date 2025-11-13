# Setup Telegram Bot untuk Notifikasi CSIRT Kalselprov

## Overview

Sistem sekarang mendukung notifikasi melalui Telegram Bot selain email. Notifikasi Telegram akan otomatis dikirim ke admin dan pengguna yang memiliki `telegram_username` di profil mereka.

## Langkah-langkah Setup

### 1. Buat Telegram Bot

1. Buka Telegram dan cari **@BotFather**
2. Kirim perintah `/newbot`
3. Ikuti instruksi untuk memberikan nama dan username bot
4. Setelah bot dibuat, BotFather akan memberikan **Bot Token**
5. Simpan token ini untuk digunakan di konfigurasi

### 2. Konfigurasi Bot Token

Tambahkan token bot ke file `.env`:

```env
TELEGRAM_BOT_TOKEN=your-bot-token-here
```

Contoh:

```env
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
```

### 3. Jalankan Migration

Jalankan migration untuk menambahkan kolom `telegram_username` ke tabel users:

```bash
php artisan migrate
```

### 4. Setup Username Telegram untuk User

Ada dua cara untuk setup username Telegram:

#### Cara 1: Melalui Database/Seeder

Update user di database atau seeder:

```php
$user->telegram_username = 'username_telegram'; // tanpa @
$user->save();
```

#### Cara 2: Melalui Form Profil (jika sudah dibuat)

Tambahkan field `telegram_username` di form edit profil user.

### 5. Aktifkan Bot untuk User

**PENTING**: User harus memulai chat dengan bot terlebih dahulu agar bot dapat mengirim pesan.

1. User mencari bot di Telegram menggunakan username bot (contoh: `@csirt_kalselprov_bot`)
2. User mengirim perintah `/start` ke bot
3. Sistem akan otomatis menyimpan chat_id dari interaksi ini

**Catatan**: Bot hanya bisa mengirim pesan ke user yang sudah pernah memulai chat dengan bot.

## Cara Kerja

1. Sistem akan mengirim notifikasi melalui **email** (seperti biasa)
2. Jika user memiliki `telegram_username` yang terisi, sistem juga akan mengirim notifikasi melalui **Telegram**
3. Notifikasi Telegram akan dikirim ke username yang terdaftar

## Notifikasi yang Didukung

Semua notifikasi berikut mendukung Telegram:

-   ✅ **NewTicketCreated** - Notifikasi ke admin/agent saat tiket baru dibuat
-   ✅ **NewTicketSubmitted** - Notifikasi ke pelapor saat tiket berhasil dibuat
-   ✅ **TicketAssigned** - Notifikasi ke agent saat tiket ditugaskan
-   ✅ **TicketStatusChanged** - Notifikasi ke pelapor saat status tiket berubah
-   ✅ **TicketReplyFromAgent** - Notifikasi ke pelapor saat agent membalas
-   ✅ **TicketReplyFromRequester** - Notifikasi ke agent saat pelapor membalas

## Testing

Untuk test mengirim notifikasi Telegram, gunakan command:

```bash
php artisan telegram:test email@example.com
```

Atau dengan pesan custom:

```bash
php artisan telegram:test email@example.com --message="Pesan test Anda"
```

Command ini akan:

-   Mencari user berdasarkan email
-   Mengirim pesan test ke Telegram user
-   Menyimpan chat_id ke database jika ditemukan
-   Menampilkan status sukses/gagal

## Troubleshooting

### Bot tidak mengirim pesan

1. **Cek Bot Token**: Pastikan `TELEGRAM_BOT_TOKEN` di `.env` sudah benar

    ```bash
    php artisan tinker --execute="echo config('services.telegram.bot_token') ? 'Token ada' : 'Token tidak ada';"
    ```

2. **Cek Username**: Pastikan user sudah mengisi `telegram_username` di database

    ```bash
    php artisan tinker --execute="echo \App\Models\User::where('email', 'email@example.com')->first()->telegram_username ?? 'Username tidak ada';"
    ```

3. **User harus memulai chat**: User harus sudah mengirim `/start` ke bot terlebih dahulu

    - User mencari bot di Telegram (gunakan username bot)
    - User mengirim perintah `/start`
    - Sistem akan otomatis menyimpan chat_id ke database

4. **Cek Log**: Lihat `storage/logs/laravel.log` untuk error detail

    ```bash
    tail -f storage/logs/laravel.log | grep -i telegram
    ```

5. **Test dengan command**: Gunakan command test untuk debugging
    ```bash
    php artisan telegram:test email@example.com
    ```

### Error: "Chat not found" atau "Tidak dapat menemukan chat_id"

Ini berarti user belum pernah memulai chat dengan bot. Solusi:

1. **Minta user untuk memulai chat dengan bot**:

    - User mencari bot di Telegram (gunakan username bot dari BotFather)
    - User mengirim perintah `/start`
    - Sistem akan otomatis menyimpan chat_id ke database

2. **Setelah user mengirim /start, test lagi**:

    ```bash
    php artisan telegram:test email@example.com
    ```

3. **Cek chat_id di database**:
    ```bash
    php artisan tinker --execute="echo \App\Models\User::where('email', 'email@example.com')->first()->telegram_chat_id ?? 'Chat ID belum ada';"
    ```

### Error: "Bot token tidak dikonfigurasi"

Pastikan `TELEGRAM_BOT_TOKEN` sudah ditambahkan di `.env`:

```env
TELEGRAM_BOT_TOKEN=your-bot-token-here
```

Lalu clear config cache:

```bash
php artisan config:clear
```

### Notifikasi email masuk tapi Telegram tidak

1. **Cek apakah user memiliki telegram_username**:
    - Jika tidak ada, user perlu mengisi di halaman Profile
2. **Cek apakah user sudah memulai chat dengan bot**:
    - User harus mengirim `/start` ke bot terlebih dahulu
3. **Cek log untuk detail error**:

    ```bash
    tail -n 100 storage/logs/laravel.log | grep -i telegram
    ```

4. **Test manual dengan command**:
    ```bash
    php artisan telegram:test email@example.com
    ```

## Catatan Penting

-   **Username vs Chat ID**: Sistem menggunakan username untuk kemudahan, tetapi Telegram memerlukan chat_id untuk mengirim pesan. Sistem akan otomatis mencari chat_id dari username saat mengirim pesan.

-   **Privasi**: Pastikan bot token disimpan dengan aman dan tidak di-commit ke repository publik.

-   **Rate Limiting**: Telegram memiliki rate limit. Jika mengirim banyak notifikasi sekaligus, pertimbangkan menggunakan queue.

## Testing

Untuk testing, pastikan:

1. Bot token sudah dikonfigurasi
2. User memiliki `telegram_username` di database
3. User sudah memulai chat dengan bot (`/start`)
4. Buat tiket baru atau trigger notifikasi lainnya
5. Cek log untuk melihat apakah pesan berhasil dikirim

## Perbaikan di Masa Depan

Untuk production yang lebih baik, pertimbangkan:

1. **Menyimpan chat_id di database**: Saat user pertama kali berinteraksi dengan bot, simpan chat_id mereka di database untuk performa yang lebih baik
2. **Webhook**: Setup webhook untuk menerima update dari Telegram secara real-time
3. **Command Handler**: Tambahkan command handler di bot untuk fitur tambahan (contoh: `/status`, `/help`)
