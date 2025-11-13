# Quick Reference - Presentasi OS-Tiket

## 🎯 Poin Utama Presentasi

### 1. Apa itu OS-Tiket?

Sistem ticketing berbasis web untuk CSIRT Kalselprov untuk menerima dan mengelola laporan insiden siber.

### 2. Fitur Utama

-   ✅ Portal pelaporan insiden siber
-   ✅ Dashboard agent/admin
-   ✅ Notifikasi Email + Telegram
-   ✅ Manajemen tiket lengkap
-   ✅ Master data management

### 3. Teknologi

-   Laravel 12.x
-   MySQL
-   Tailwind CSS
-   Telegram Bot API

---

## 📁 File Konfigurasi Penting

### Environment (`.env`)

**Lokasi**: Root project

```env
# Database
DB_DATABASE=os_tiket
DB_USERNAME=root
DB_PASSWORD=

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=...
MAIL_PASSWORD=...

# Telegram
TELEGRAM_BOT_TOKEN=...
```

### Services Config

**Lokasi**: `config/services.php`

-   Konfigurasi Telegram bot token

### Mail Config

**Lokasi**: `config/mail.php`

-   Konfigurasi email notification

### Routes

**Lokasi**: `routes/web.php`

-   Semua route aplikasi
-   Route webhook Telegram (line 86)

---

## 🔧 File Utama

### Controllers

-   `app/Http/Controllers/Portal/TicketController.php` - Portal pelaporan
-   `app/Http/Controllers/Agent/TicketController.php` - Agent panel
-   `app/Http/Controllers/Admin/` - Admin panel
-   `app/Http/Controllers/ProfileController.php` - Profile management
-   `app/Http/Controllers/TelegramWebhookController.php` - Telegram webhook

### Services

-   `app/Services/TelegramService.php` - Service Telegram Bot API

### Notifications

-   `app/Notifications/Channels/TelegramChannel.php` - Telegram channel
-   `app/Notifications/NewTicketCreated.php` - Notif tiket baru
-   `app/Notifications/NewTicketSubmitted.php` - Notif tiket dibuat
-   `app/Notifications/TicketAssigned.php` - Notif assignment
-   `app/Notifications/TicketStatusChanged.php` - Notif perubahan status
-   `app/Notifications/TicketReplyFromAgent.php` - Notif balasan agent
-   `app/Notifications/TicketReplyFromRequester.php` - Notif balasan pelapor

### Views

-   `resources/views/welcome.blade.php` - Landing page
-   `resources/views/portal/` - Portal public
-   `resources/views/agent/` - Agent dashboard
-   `resources/views/admin/` - Admin panel
-   `resources/views/profile/` - Profile management

---

## 🚀 Setup Cepat

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env

# 4. Run migration & seeder
php artisan migrate:fresh --seed

# 5. Konfigurasi email & Telegram di .env

# 6. Build assets
npm run build

# 7. Run server
php artisan serve
```

---

## 📧 Notifikasi

### Email

-   **Config**: `.env` (MAIL\_\*)
-   **Status**: ✅ Aktif

### Telegram

-   **Config**: `.env` (TELEGRAM_BOT_TOKEN)
-   **Service**: `app/Services/TelegramService.php`
-   **Channel**: `app/Notifications/Channels/TelegramChannel.php`
-   **Webhook**: `app/Http/Controllers/TelegramWebhookController.php`
-   **Status**: ✅ Aktif

### Notifikasi yang Tersedia

1. NewTicketSubmitted
2. NewTicketCreated
3. TicketAssigned
4. TicketStatusChanged
5. TicketReplyFromAgent
6. TicketReplyFromRequester

---

## 👥 Default Users

Setelah seeder:

-   **Super Admin**: admin@csirt.kalselprov.go.id / password
-   **Admin**: admin1@csirt.kalselprov.go.id / password
-   **Agent**: agent@csirt.kalselprov.go.id / password

---

## 🎬 Demo Flow

1. **Portal** → User login → Buat tiket → Notifikasi terkirim
2. **Agent Dashboard** → Lihat tiket → Assign → Balas → Notifikasi terkirim
3. **Profile** → Edit profil → Set Telegram username → Auto chat_id

---

## 📚 Dokumentasi

-   `PRESENTATION_GUIDE.md` - Panduan lengkap presentasi
-   `EMAIL_SETUP.md` - Setup email
-   `TELEGRAM_SETUP.md` - Setup Telegram
-   `TELEGRAM_WEBHOOK_SETUP.md` - Setup webhook
-   `TELEGRAM_CHAT_ID_GUIDE.md` - Panduan chat_id
