# Checklist Presentasi OS-Tiket

## ✅ Pre-Presentasi Checklist

### 1. Setup Aplikasi

-   [ ] Aplikasi sudah running (`php artisan serve`)
-   [ ] Database sudah di-migrate dan di-seed
-   [ ] Assets sudah di-build (`npm run build`)
-   [ ] Default users sudah tersedia untuk demo

### 2. Konfigurasi Email

-   [ ] Email sudah dikonfigurasi di `.env`
-   [ ] Test email sudah berhasil terkirim
-   [ ] Email masuk ke inbox test

### 3. Konfigurasi Telegram

-   [ ] Bot token sudah dikonfigurasi di `.env`
-   [ ] Bot sudah dibuat di @BotFather
-   [ ] Test notifikasi Telegram sudah berhasil
-   [ ] Webhook sudah di-set (opsional)

### 4. Data Demo

-   [ ] Ada beberapa tiket sample untuk demo
-   [ ] Ada user dengan telegram_username untuk demo notifikasi
-   [ ] Chat_id sudah terdaftar untuk user demo

### 5. Dokumentasi

-   [ ] PRESENTATION_GUIDE.md sudah dibaca
-   [ ] PRESENTATION_QUICK_REFERENCE.md sudah dibaca
-   [ ] ARCHITECTURE_DIAGRAM.md sudah dipahami

---

## 🎯 Poin Presentasi (15 menit)

### Opening (2 menit)

-   [ ] Perkenalkan proyek
-   [ ] Jelaskan tujuan dan manfaat
-   [ ] Sebutkan teknologi yang digunakan

### Fitur Utama (5 menit)

-   [ ] Portal pelaporan insiden
-   [ ] Dashboard agent/admin
-   [ ] Sistem notifikasi (Email + Telegram)
-   [ ] Manajemen master data

### Demo (5 menit)

-   [ ] Demo portal pelaporan
-   [ ] Demo dashboard agent
-   [ ] Demo notifikasi (email + Telegram)
-   [ ] Demo profile management

### Teknologi & Arsitektur (2 menit)

-   [ ] Teknologi stack
-   [ ] Arsitektur sistem
-   [ ] Flow notifikasi

### Closing (1 menit)

-   [ ] Kesimpulan
-   [ ] Q&A

---

## 📁 File yang Harus Diketahui Lokasinya

### Konfigurasi

-   [ ] `.env` - Environment configuration
-   [ ] `config/services.php` - Telegram config
-   [ ] `config/mail.php` - Email config
-   [ ] `config/database.php` - Database config

### Controllers

-   [ ] `app/Http/Controllers/Portal/TicketController.php`
-   [ ] `app/Http/Controllers/Agent/TicketController.php`
-   [ ] `app/Http/Controllers/ProfileController.php`
-   [ ] `app/Http/Controllers/TelegramWebhookController.php`

### Services & Notifications

-   [ ] `app/Services/TelegramService.php`
-   [ ] `app/Notifications/Channels/TelegramChannel.php`
-   [ ] `app/Notifications/NewTicketCreated.php`
-   [ ] `app/Notifications/NewTicketSubmitted.php`

### Routes

-   [ ] `routes/web.php`

### Views

-   [ ] `resources/views/welcome.blade.php`
-   [ ] `resources/views/profile/partials/update-profile-information-form.blade.php`

---

## 🔧 Command yang Harus Diketahui

-   [ ] `php artisan migrate:fresh --seed` - Reset database
-   [ ] `php artisan telegram:test email@example.com` - Test Telegram
-   [ ] `php artisan telegram:set-webhook URL` - Set webhook
-   [ ] `php artisan serve` - Run server

---

## 📝 Catatan Penting

### Jika Ditanya tentang Konfigurasi:

1. **Email**: Edit `.env` (MAIL\_\*) atau `config/mail.php`
2. **Telegram**: Edit `.env` (TELEGRAM_BOT_TOKEN) atau `config/services.php`
3. **Database**: Edit `.env` (DB\_\*) atau `config/database.php`

### Jika Ditanya tentang Notifikasi:

1. **Email**: Sudah terintegrasi dengan Laravel Mail
2. **Telegram**: Custom channel di `app/Notifications/Channels/TelegramChannel.php`
3. **Service**: `app/Services/TelegramService.php` untuk API calls

### Jika Ditanya tentang Auto Chat ID:

1. Sistem otomatis mencari chat_id saat register/update profil
2. Webhook otomatis menyimpan chat_id saat user mengirim `/start` ke bot
3. File: `app/Http/Controllers/TelegramWebhookController.php`

---

## 🎤 Tips Presentasi

1. **Jangan Panik**: Jika ada error, cek log di `storage/logs/laravel.log`
2. **Backup Plan**: Siapkan screenshot jika demo gagal
3. **Highlight**: Tekankan fitur notifikasi Telegram yang unik
4. **Flow**: Jelaskan alur dari pelaporan sampai penanganan
5. **Q&A**: Siapkan jawaban untuk pertanyaan umum

---

## ❓ Pertanyaan yang Mungkin Muncul

### Q: Bagaimana cara setup?

**A**:

1. Clone project
2. `composer install && npm install`
3. Setup `.env`
4. `php artisan migrate:fresh --seed`
5. `php artisan serve`

### Q: Dimana file konfigurasi?

**A**:

-   Environment: `.env` (root project)
-   Services: `config/services.php`
-   Mail: `config/mail.php`
-   Routes: `routes/web.php`

### Q: Bagaimana notifikasi bekerja?

**A**:

-   Notification class menentukan channel (mail/telegram)
-   TelegramChannel menggunakan TelegramService
-   TelegramService mengirim via Bot API
-   Chat_id disimpan di database untuk performa

### Q: Bagaimana auto chat_id?

**A**:

-   Saat register/update profil, sistem mencari chat_id dari getUpdates
-   Webhook otomatis menyimpan chat_id saat user kirim `/start`
-   File: `TelegramWebhookController.php`

---

**Good Luck dengan Presentasi! 🚀**
