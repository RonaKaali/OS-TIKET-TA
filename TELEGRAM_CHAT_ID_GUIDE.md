# Cara Mendapatkan Chat ID Telegram

Karena Telegram Bot API tidak bisa langsung mendapatkan chat_id dari username, ada beberapa cara untuk mendapatkan chat_id:

## Cara 1: Menggunakan Bot @userinfobot (Paling Mudah)

1. Buka Telegram dan cari bot `@userinfobot`
2. Kirim pesan apapun ke bot tersebut
3. Bot akan membalas dengan informasi user Anda, termasuk **Chat ID**
4. Salin Chat ID yang diberikan (biasanya berupa angka, contoh: `123456789`)
5. Set chat_id menggunakan command:
    ```bash
    php artisan telegram:set-chat-id email@example.com 123456789
    ```

## Cara 2: Menggunakan Bot @getidsbot

1. Buka Telegram dan cari bot `@getidsbot`
2. Kirim pesan apapun ke bot tersebut
3. Bot akan membalas dengan **Your user ID** (ini adalah chat_id Anda)
4. Salin ID yang diberikan
5. Set chat_id menggunakan command:
    ```bash
    php artisan telegram:set-chat-id email@example.com YOUR_CHAT_ID
    ```

## Cara 3: Menggunakan Web Browser (Untuk Group Chat)

Jika Anda ingin mendapatkan chat_id untuk group:

1. Tambahkan bot ke group
2. Kirim pesan di group
3. Buka browser dan akses: `https://api.telegram.org/bot<BOT_TOKEN>/getUpdates`
4. Cari chat_id di response JSON

## Cara 4: Setelah User Mengirim /start ke Bot CSIRT

Jika user sudah mengirim `/start` ke bot CSIRT:

1. Jalankan command untuk mendapatkan chat_id:
    ```bash
    php artisan telegram:get-chat-id email@example.com
    ```
2. Jika berhasil, chat_id akan otomatis disimpan

## Set Chat ID Secara Manual

Jika Anda sudah mendapatkan chat_id dengan cara apapun:

```bash
php artisan telegram:set-chat-id email@example.com CHAT_ID
```

Contoh:

```bash
php artisan telegram:set-chat-id m.abrarridhani@gmail.com 123456789
```

## Verifikasi

Setelah set chat_id, test dengan:

```bash
php artisan telegram:test email@example.com
```

Jika berhasil, notifikasi Telegram akan terkirim!
