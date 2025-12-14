# Panduan Presentasi Proyek OS-Tiket (CSIRT Kalselprov)

## 📋 Daftar Isi

1. [Overview Proyek](#overview-proyek)
2. [Fitur Utama](#fitur-utama)
3. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
4. [Struktur Proyek](#struktur-proyek)
5. [File Konfigurasi Penting](#file-konfigurasi-penting)
6. [Fitur Notifikasi](#fitur-notifikasi)
7. [Cara Setup dan Instalasi](#cara-setup-dan-instalasi)
8. [Demo Fitur](#demo-fitur)
9. [Chatbot Otomatis](#6-chatbot-otomatis)

---

## 🎯 Overview Proyek

**OS-Tiket** adalah sistem ticketing berbasis web untuk **CSIRT (Computer Security Incident Response Team) Kalselprov** yang digunakan untuk:

-   **Menerima dan mengelola laporan insiden siber** dari masyarakat dan instansi
-   **Tracking dan monitoring** status penanganan insiden
-   **Notifikasi real-time** melalui Email dan Telegram
-   **Manajemen tiket** untuk agent/admin
-   **Chatbot otomatis** untuk membantu user mendapatkan informasi cepat

### Tujuan Proyek

1. Memudahkan masyarakat melaporkan insiden siber
2. Mempercepat respon tim CSIRT terhadap insiden
3. Meningkatkan transparansi dan akuntabilitas penanganan insiden
4. Dokumentasi yang terstruktur untuk analisis dan pelaporan

---

## ✨ Fitur Utama

### 1. Portal Pelaporan (Public)

-   ✅ Form pelaporan insiden siber
-   ✅ Upload file/lampiran
-   ✅ Cek status laporan (dengan verifikasi email)
-   ✅ Balasan dan komunikasi dengan agent

### 2. Dashboard Agent/Admin

-   ✅ Manajemen tiket (view, assign, update status)
-   ✅ Balasan tiket dengan attachment
-   ✅ Canned responses (template balasan)
-   ✅ Filter dan pencarian tiket
-   ✅ Statistik dan laporan

### 3. Manajemen Master Data

-   ✅ Departemen
-   ✅ Help Topics (kategori insiden)
-   ✅ Prioritas
-   ✅ Status
-   ✅ SLA Plans
-   ✅ Teams
-   ✅ Organizations
-   ✅ Users & Roles

### 4. Sistem Notifikasi

-   ✅ **Email Notification** - Notifikasi via email
-   ✅ **Telegram Notification** - Notifikasi via Telegram Bot
-   ✅ Notifikasi untuk semua event penting (tiket baru, balasan, perubahan status, dll)

### 5. Sistem Autentikasi & Authorization

-   ✅ Login/Register
-   ✅ Role-Based Access Control (RBAC)
-   ✅ Permission management
-   ✅ Profile management

### 6. Chatbot Otomatis

-   ✅ **Chatbot berbasis keyword** - Respon otomatis berdasarkan kata kunci
-   ✅ **Multiple match types** - Contains, Exact, Starts With
-   ✅ **Priority system** - Prioritas untuk multiple matches
-   ✅ **Aktif/Nonaktif** - Kontrol per response
-   ✅ **Manajemen via Admin Panel** - CRUD chatbot responses
-   ✅ **Widget Chatbot** - Chatbot widget di website (opsional)
-   ✅ **Integrasi Telegram Bot** - Chatbot juga berfungsi di Telegram

---

## 🛠 Teknologi yang Digunakan

### Backend

-   **Framework**: Laravel 12.x
-   **Database**: MySQL/SQLite
-   **PHP**: 8.2+

### Frontend

-   **CSS Framework**: Tailwind CSS
-   **JavaScript**: Alpine.js (via Laravel Breeze)
-   **Build Tool**: Vite

### Package/Plugin

-   **Spatie Laravel Permission** - Untuk RBAC
-   **Laravel Breeze** - Autentikasi
-   **Ramsey UUID** - Generate unique ID

### Notifikasi

-   **Email**: Laravel Mail (SMTP)
-   **Telegram**: Custom Telegram Bot API Integration

---

## 📁 Struktur Proyek

```
os-tiket/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controller untuk admin panel
│   │   │   ├── Agent/          # Controller untuk agent panel
│   │   │   ├── Portal/         # Controller untuk portal public
│   │   │   ├── Auth/           # Controller autentikasi
│   │   │   ├── ProfileController.php
│   │   │   ├── ChatbotController.php  # Controller untuk chatbot API
│   │   │   └── TelegramWebhookController.php
│   │   └── Requests/           # Form Request validation
│   ├── Models/                 # Eloquent Models
│   ├── Notifications/          # Notification classes
│   │   ├── Channels/
│   │   │   └── TelegramChannel.php
│   │   ├── NewTicketCreated.php
│   │   ├── NewTicketSubmitted.php
│   │   ├── TicketAssigned.php
│   │   ├── TicketStatusChanged.php
│   │   ├── TicketReplyFromAgent.php
│   │   └── TicketReplyFromRequester.php
│   ├── Services/
│   │   ├── TelegramService.php # Service untuk Telegram Bot API
│   │   └── ChatbotService.php  # Service untuk chatbot logic
│   └── Providers/
│       └── AppServiceProvider.php
├── config/                     # File konfigurasi
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── mail.php
│   ├── services.php            # Konfigurasi Telegram bot
│   └── permission.php
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   │   ├── admin/            # View untuk admin
│   │   ├── agent/            # View untuk agent
│   │   ├── portal/           # View untuk portal public
│   │   ├── profile/          # View untuk profil user
│   │   └── welcome.blade.php # Landing page
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php               # Web routes
│   └── auth.php              # Auth routes
└── .env                       # Environment configuration
```

---

## ⚙️ File Konfigurasi Penting

### 1. Environment Configuration (`.env`)

**Lokasi**: Root project (`.env`)

File ini berisi semua konfigurasi penting:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=os_tiket
DB_USERNAME=root
DB_PASSWORD=

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@csirt.kalselprov.go.id
MAIL_FROM_NAME="CSIRT Kalselprov"

# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=your-bot-token-here

# Queue (untuk notifikasi)
QUEUE_CONNECTION=sync  # atau 'database' untuk production
```

**Cara Edit**: Edit langsung file `.env` di root project

---

### 2. Services Configuration (`config/services.php`)

**Lokasi**: `config/services.php`

File ini untuk konfigurasi third-party services:

```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
],
```

**Cara Edit**: Edit file `config/services.php` atau set di `.env`

---

### 3. Mail Configuration (`config/mail.php`)

**Lokasi**: `config/mail.php`

Konfigurasi untuk email notification:

```php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'noreply@csirt.kalselprov.go.id'),
    'name' => env('MAIL_FROM_NAME', 'CSIRT Kalselprov'),
],
```

**Cara Edit**: Edit file `config/mail.php` atau set di `.env`

---

### 4. Database Configuration (`config/database.php`)

**Lokasi**: `config/database.php`

Konfigurasi koneksi database.

**Cara Edit**: Edit file `config/database.php` atau set di `.env`

---

### 5. Permission Configuration (`config/permission.php`)

**Lokasi**: `config/permission.php`

Konfigurasi untuk Spatie Laravel Permission (RBAC).

**Cara Edit**: Edit file `config/permission.php`

---

### 6. Routes (`routes/web.php`)

**Lokasi**: `routes/web.php`

File ini berisi semua route aplikasi:

-   Route portal public
-   Route agent/admin panel
-   Route profile
-   Route Telegram webhook
-   Route chatbot API

**Cara Edit**: Edit file `routes/web.php`

---

### 7. App Service Provider (`app/Providers/AppServiceProvider.php`)

**Lokasi**: `app/Providers/AppServiceProvider.php`

File ini untuk:

-   Register Telegram notification channel
-   Bootstrap custom services

**Cara Edit**: Edit file `app/Providers/AppServiceProvider.php`

---

## 📧 Fitur Notifikasi

### Notifikasi yang Tersedia

1. **NewTicketSubmitted** - Notifikasi ke pelapor saat tiket dibuat
2. **NewTicketCreated** - Notifikasi ke admin/agent saat tiket baru
3. **TicketAssigned** - Notifikasi ke agent saat tiket ditugaskan
4. **TicketStatusChanged** - Notifikasi ke pelapor saat status berubah
5. **TicketReplyFromAgent** - Notifikasi ke pelapor saat agent membalas
6. **TicketReplyFromRequester** - Notifikasi ke agent saat pelapor membalas

### Channel Notifikasi

#### Email

-   **Konfigurasi**: `.env` (MAIL\_\*)
-   **File**: `config/mail.php`
-   **Status**: ✅ Berfungsi

#### Telegram

-   **Konfigurasi**: `.env` (TELEGRAM_BOT_TOKEN)
-   **File**: `config/services.php`
-   **Service**: `app/Services/TelegramService.php`
-   **Channel**: `app/Notifications/Channels/TelegramChannel.php`
-   **Webhook**: `app/Http/Controllers/TelegramWebhookController.php`
-   **Route**: `routes/web.php` (line 86)
-   **Status**: ✅ Berfungsi

---

## 🚀 Cara Setup dan Instalasi

### 1. Clone/Download Project

```bash
cd os-tiket
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=os_tiket
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Konfigurasi Email

Edit file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@csirt.kalselprov.go.id
MAIL_FROM_NAME="CSIRT Kalselprov"
```

### 7. Konfigurasi Telegram Bot

1. Buat bot di @BotFather di Telegram
2. Dapatkan bot token
3. Edit file `.env`:

```env
TELEGRAM_BOT_TOKEN=your-bot-token-here
```

4. Set webhook (opsional, untuk auto chat_id):

```bash
php artisan telegram:set-webhook https://yourdomain.com/telegram/webhook
```

### 8. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 9. Run Server

```bash
php artisan serve
```

Akses: `http://127.0.0.1:8000`

---

## 🎬 Demo Fitur

### 1. Portal Pelaporan (Public)

**URL**: `http://127.0.0.1:8000/`

**Fitur**:

-   Landing page dengan informasi CSIRT
-   Form pelaporan insiden (perlu login)
-   Cek status laporan (tanpa login)

**File**:

-   View: `resources/views/welcome.blade.php`
-   Controller: `app/Http/Controllers/Portal/TicketController.php`
-   Route: `routes/web.php` (line 26-38)

### 2. Dashboard Agent/Admin

**URL**: `http://127.0.0.1:8000/agent/dashboard`

**Fitur**:

-   Overview tiket
-   Manajemen tiket
-   Assignment tiket
-   Balasan tiket

**File**:

-   View: `resources/views/agent/`
-   Controller: `app/Http/Controllers/Agent/`
-   Route: `routes/web.php` (line 49-72)

### 3. Manajemen Master Data

**URL**: `http://127.0.0.1:8000/admin/`

**Fitur**:

-   CRUD untuk semua master data
-   User management
-   Role & Permission management
-   **Chatbot Responses Management** - Kelola response chatbot

**File**:

-   View: `resources/views/admin/`
-   Controller: `app/Http/Controllers/Admin/`
-   Route: `routes/web.php` (line 60-76)

### 4. Profile Management

**URL**: `http://127.0.0.1:8000/profile`

**Fitur**:

-   Edit profil
-   Set username Telegram
-   Auto-get chat_id
-   Ubah password

**File**:

-   View: `resources/views/profile/`
-   Controller: `app/Http/Controllers/ProfileController.php`
-   Route: `routes/web.php` (line 79-83)

### 5. Notifikasi

**Cara Test**:

1. Buat tiket baru → Notifikasi terkirim ke pelapor & admin
2. Balas tiket → Notifikasi terkirim
3. Ubah status → Notifikasi terkirim

**File**:

-   Notifications: `app/Notifications/`
-   Telegram Service: `app/Services/TelegramService.php`
-   Telegram Channel: `app/Notifications/Channels/TelegramChannel.php`

### 6. Chatbot Otomatis

**URL**:

-   API: `http://127.0.0.1:8000/chatbot/message` (POST)
-   Admin Panel: `http://127.0.0.1:8000/admin/chatbot-responses`

**Fitur**:

-   **Keyword-based responses** - Bot merespons berdasarkan kata kunci
-   **Multiple match types**:
    -   `contains` - Pesan mengandung keyword
    -   `exact` - Pesan persis sama dengan keyword
    -   `starts_with` - Pesan dimulai dengan keyword
-   **Priority system** - Jika ada multiple matches, yang priority lebih tinggi dipilih
-   **Aktif/Nonaktif** - Kontrol per response
-   **Manajemen via Admin Panel** - Super Admin dapat CRUD chatbot responses
-   **Widget Chatbot** - Chatbot widget tersedia di website (opsional)
-   **Integrasi Telegram** - Chatbot juga berfungsi di Telegram Bot

**Cara Test**:

1. **Via Website Widget** (jika ada):

    - Buka website
    - Klik widget chatbot
    - Ketik pesan seperti "halo", "bantuan", "cara buat tiket"
    - Bot akan merespons sesuai keyword

2. **Via Telegram Bot**:

    - Kirim pesan ke bot Telegram
    - Bot akan merespons sesuai keyword yang dikonfigurasi

3. **Via Admin Panel**:
    - Login sebagai Super Admin
    - Akses Admin Panel > Chatbot Responses
    - Tambah/Edit/Hapus response

**File**:

-   Controller: `app/Http/Controllers/ChatbotController.php`
-   Admin Controller: `app/Http/Controllers/Admin/ChatbotResponseController.php`
-   Service: `app/Services/ChatbotService.php`
-   Model: `app/Models/ChatbotResponse.php`
-   Route: `routes/web.php` (line 28)
-   View: `resources/views/admin/chatbot-responses/`
-   Widget: `resources/views/components/chatbot-widget.blade.php` (jika ada)

---

## 📊 Database Schema

### Tabel Utama

1. **users** - Data user (admin, agent, user)
2. **tickets** - Data tiket/laporan
3. **ticket_threads** - Thread/balasan tiket
4. **attachments** - File lampiran
5. **departments** - Departemen
6. **statuses** - Status tiket
7. **priorities** - Prioritas tiket
8. **organizations** - Organisasi
9. **teams** - Tim agent
10. **help_topics** - Kategori insiden
11. **chatbot_responses** - Data response chatbot

**File Migration**: `database/migrations/`

---

## 🔐 Role & Permission

### Roles yang Tersedia

1. **Super Admin** - Full access
2. **Admin** - Admin panel access
3. **Agent** - Agent panel access
4. **Support Agent** - Support access
5. **User** - Portal access only

### Permission Utama

-   `admin.panel` - Akses ke admin/agent panel
-   `tickets.*` - Manajemen tiket
-   `tickets.assign` - Assign tiket
-   `master.*` - Manajemen master data

**File**:

-   Seeder: `database/seeders/RolePermissionSeeder.php`
-   Config: `config/permission.php`

---

## 📝 Default Users (Setelah Seeder)

Setelah menjalankan seeder, user default:

1. **Super Admin**

    - Email: `admin@csirt.kalselprov.go.id`
    - Password: `password`

2. **Admin**

    - Email: `admin1@csirt.kalselprov.go.id`
    - Password: `password`

3. **Agent**
    - Email: `agent@csirt.kalselprov.go.id`
    - Password: `password`

**File**: `database/seeders/UserSeeder.php`

---

## 🔧 Command yang Tersedia

### Telegram Commands

```bash
# Test mengirim notifikasi Telegram
php artisan telegram:test email@example.com

# Dapatkan chat_id dari username
php artisan telegram:get-chat-id email@example.com

# Set chat_id secara manual
php artisan telegram:set-chat-id email@example.com CHAT_ID

# Set webhook URL
php artisan telegram:set-webhook https://yourdomain.com/telegram/webhook

# Cek info webhook
php artisan telegram:get-webhook-info
```

### Database Commands

```bash
# Fresh migration dengan seeder
php artisan migrate:fresh --seed

# Run migration
php artisan migrate

# Run seeder
php artisan db:seed
```

---

## 📚 Dokumentasi Tambahan

1. **EMAIL_SETUP.md** - Panduan setup email
2. **TELEGRAM_SETUP.md** - Panduan setup Telegram bot
3. **TELEGRAM_WEBHOOK_SETUP.md** - Panduan setup webhook
4. **TELEGRAM_CHAT_ID_GUIDE.md** - Panduan mendapatkan chat_id
5. **CHATBOT_RESPONSES_GUIDE.md** - Panduan mengisi chatbot responses
6. **ROLE_PERMISSION.md** - Dokumentasi role & permission

---

## 🎯 Poin Presentasi

### 1. Opening (2 menit)

-   Perkenalkan proyek: Sistem Ticketing untuk CSIRT Kalselprov
-   Tujuan: Memudahkan pelaporan dan penanganan insiden siber
-   Teknologi: Laravel 12, MySQL, Tailwind CSS

### 2. Fitur Utama (5 menit)

-   Portal pelaporan untuk masyarakat
-   Dashboard agent/admin untuk manajemen tiket
-   Sistem notifikasi (Email + Telegram)
-   **Chatbot otomatis** untuk membantu user
-   Manajemen master data

### 3. Demo (5 menit)

-   Tampilkan portal pelaporan
-   Tampilkan dashboard agent
-   Demo notifikasi (email + Telegram)
-   **Demo chatbot** (via website widget atau Telegram)
-   Tampilkan fitur profile management

### 4. Teknologi & Arsitektur (3 menit)

-   Teknologi yang digunakan
-   Struktur database
-   Sistem notifikasi (email + Telegram)

### 5. Setup & Deployment (2 menit)

-   Cara setup project
-   Konfigurasi yang diperlukan
-   File-file konfigurasi penting

### 6. Closing (1 menit)

-   Kesimpulan
-   Q&A

---

## 📍 Lokasi File Konfigurasi (Quick Reference)

| Konfigurasi              | File                                             | Lokasi                        |
| ------------------------ | ------------------------------------------------ | ----------------------------- |
| **Environment**          | `.env`                                           | Root project                  |
| **Database**             | `config/database.php`                            | `config/`                     |
| **Email**                | `config/mail.php`                                | `config/`                     |
| **Telegram Bot**         | `config/services.php`                            | `config/`                     |
| **Routes**               | `routes/web.php`                                 | `routes/`                     |
| **Telegram Service**     | `app/Services/TelegramService.php`               | `app/Services/`               |
| **Chatbot Service**      | `app/Services/ChatbotService.php`                | `app/Services/`               |
| **Telegram Channel**     | `app/Notifications/Channels/TelegramChannel.php` | `app/Notifications/Channels/` |
| **App Service Provider** | `app/Providers/AppServiceProvider.php`           | `app/Providers/`              |
| **Migrations**           | `database/migrations/`                           | `database/migrations/`        |
| **Seeders**              | `database/seeders/`                              | `database/seeders/`           |

---

## 💡 Tips Presentasi

1. **Siapkan Demo**: Pastikan aplikasi sudah running dan data sudah ada
2. **Test Notifikasi**: Pastikan email dan Telegram sudah dikonfigurasi
3. **Test Chatbot**: Pastikan chatbot responses sudah dikonfigurasi dan berfungsi
4. **Siapkan Screenshot**: Ambil screenshot fitur-fitur penting
5. **Jelaskan Flow**: Jelaskan alur dari pelaporan sampai penanganan
6. **Highlight Fitur Unik**: Tekankan fitur notifikasi Telegram dan chatbot yang unik

---

## ❓ FAQ (Frequently Asked Questions)

### Q: Bagaimana cara setup Telegram bot?

A: Lihat file `TELEGRAM_SETUP.md` untuk panduan lengkap.

### Q: Bagaimana cara mendapatkan chat_id?

A: Lihat file `TELEGRAM_CHAT_ID_GUIDE.md` atau gunakan command `php artisan telegram:get-chat-id`.

### Q: Notifikasi tidak terkirim?

A:

1. Cek log: `storage/logs/laravel.log`
2. Pastikan bot token sudah dikonfigurasi
3. Pastikan user sudah mengirim `/start` ke bot
4. Test dengan command: `php artisan telegram:test email@example.com`

### Q: Bagaimana cara mengatur chatbot responses?

A: Lihat file `CHATBOT_RESPONSES_GUIDE.md` untuk panduan lengkap. Atau akses Admin Panel > Chatbot Responses untuk mengelola response.

### Q: Chatbot tidak merespons?

A:

1. Pastikan response sudah aktif (`is_active = true`)
2. Cek match type (contains, exact, starts_with)
3. Cek priority - mungkin ada response lain dengan priority lebih tinggi
4. Test dengan keyword yang berbeda

### Q: Bagaimana cara deploy ke production?

A:

1. Set environment production di `.env`
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Setup webhook untuk Telegram
5. Setup queue worker untuk notifikasi
6. Pastikan chatbot responses sudah dikonfigurasi

---

**Selamat Presentasi! 🎉**
