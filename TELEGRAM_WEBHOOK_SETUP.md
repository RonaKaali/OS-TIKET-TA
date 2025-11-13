# Setup Telegram Webhook untuk Auto Chat ID

## Overview

Dengan webhook, chat_id akan otomatis tersimpan saat user pertama kali mengirim pesan ke bot (misalnya `/start`). Ini menghilangkan kebutuhan untuk memasukkan chat_id secara manual.

## Cara Kerja

1. User mengisi `telegram_username` saat register atau update profil
2. Sistem mencoba mendapatkan chat_id otomatis dari getUpdates
3. Jika user sudah pernah mengirim pesan ke bot, chat_id akan otomatis tersimpan
4. Jika belum, user perlu mengirim `/start` ke bot
5. Webhook akan otomatis menyimpan chat_id saat user mengirim pesan ke bot

## Setup Webhook

### 1. Pastikan Bot Token Sudah Dikonfigurasi

Di file `.env`:

```env
TELEGRAM_BOT_TOKEN=your-bot-token-here
```

### 2. Setup Webhook URL

Jalankan command berikut untuk set webhook:

```bash
php artisan telegram:set-webhook
```

Atau secara manual, akses URL berikut di browser (ganti `YOUR_BOT_TOKEN` dan `YOUR_DOMAIN`):

```
https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://YOUR_DOMAIN/telegram/webhook
```

Contoh:

```
https://api.telegram.org/bot123456789:ABCdefGHIjklMNOpqrsTUVwxyz/setWebhook?url=https://csirt.kalselprov.go.id/telegram/webhook
```

### 3. Verifikasi Webhook

Cek status webhook:

```bash
php artisan telegram:get-webhook-info
```

Atau akses di browser:

```
https://api.telegram.org/botYOUR_BOT_TOKEN/getWebhookInfo
```

## Fitur Webhook

### Auto Save Chat ID

Saat user mengirim pesan ke bot (termasuk `/start`), webhook akan:

1. Mencari user berdasarkan `telegram_username`
2. Menyimpan `telegram_chat_id` ke database
3. Mengirim pesan sambutan ke user

### Command yang Tersedia

User bisa menggunakan command berikut di bot:

-   `/start` - Memulai bot dan mendaftarkan chat ID
-   `/help` - Menampilkan bantuan
-   `/status` - Cek status registrasi

## Auto Get Chat ID saat Register/Update Profil

Sistem akan otomatis mencoba mendapatkan chat_id saat:

1. **Registrasi**: Jika user mengisi `telegram_username`, sistem akan mencari chat_id dari getUpdates
2. **Update Profil**: Jika user mengubah `telegram_username` atau belum punya chat_id, sistem akan mencoba mendapatkan chat_id

## Alur Lengkap

### Skenario 1: User sudah pernah chat dengan bot

1. User register dengan `telegram_username`
2. Sistem otomatis mendapatkan chat_id dari getUpdates
3. Chat_id tersimpan ke database
4. Notifikasi langsung bisa terkirim ✅

### Skenario 2: User belum pernah chat dengan bot

1. User register dengan `telegram_username`
2. Sistem tidak menemukan chat_id (karena belum ada update)
3. User mengirim `/start` ke bot
4. Webhook menerima update dan menyimpan chat_id
5. Notifikasi bisa terkirim ✅

### Skenario 3: User update profil

1. User mengisi/ubah `telegram_username` di profil
2. Sistem mencoba mendapatkan chat_id
3. Jika ditemukan, langsung tersimpan
4. Jika tidak, user perlu mengirim `/start` ke bot

## Troubleshooting

### Webhook tidak menerima update

1. **Cek webhook URL**: Pastikan URL benar dan bisa diakses dari internet
2. **Cek SSL**: Webhook memerlukan HTTPS (kecuali localhost)
3. **Cek route**: Pastikan route `/telegram/webhook` bisa diakses
4. **Cek log**: Lihat `storage/logs/laravel.log` untuk error

### Chat ID tidak otomatis tersimpan

1. **Pastikan user sudah mengirim pesan ke bot**: Minimal `/start`
2. **Cek webhook**: Pastikan webhook sudah di-set dan aktif
3. **Cek username**: Pastikan `telegram_username` di database sesuai dengan username Telegram user
4. **Cek log**: Lihat log untuk detail error

## Catatan Penting

-   **HTTPS Required**: Webhook memerlukan HTTPS (kecuali development dengan ngrok)
-   **CSRF Protection**: Route webhook tidak menggunakan CSRF protection (karena dari Telegram)
-   **Rate Limiting**: Telegram memiliki rate limit, jangan spam request

## Development dengan ngrok

Untuk development lokal, gunakan ngrok:

```bash
ngrok http 8000
```

Kemudian set webhook dengan URL ngrok:

```
https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://YOUR_NGROK_URL/telegram/webhook
```
