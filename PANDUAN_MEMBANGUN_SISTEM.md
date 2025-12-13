# 📘 Panduan Membangun Sistem OS-Tiket dari Awal

## 📋 Daftar Isi

1. [Overview Sistem](#overview-sistem)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Langkah-langkah Instalasi](#langkah-langkah-instalasi)
4. [Konfigurasi Aplikasi](#konfigurasi-aplikasi)
5. [Setup Database](#setup-database)
6. [Setup Notifikasi](#setup-notifikasi)
7. [Setup Telegram Bot](#setup-telegram-bot)
8. [Menjalankan Aplikasi](#menjalankan-aplikasi)
9. [Arsitektur Sistem](#arsitektur-sistem)
10. [Fitur Utama](#fitur-utama)
11. [Struktur Proyek](#struktur-proyek)
12. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview Sistem

**OS-Tiket** adalah sistem ticketing berbasis web untuk **CSIRT (Computer Security Incident Response Team) Kalselprov** yang digunakan untuk:

- **Menerima dan mengelola laporan insiden siber** dari masyarakat dan instansi
- **Tracking dan monitoring** status penanganan insiden
- **Notifikasi real-time** melalui Email dan Telegram Bot
- **Manajemen tiket** untuk agent/admin dengan dashboard yang lengkap
- **Chatbot otomatis** untuk membantu user mendapatkan informasi cepat

### Tujuan Sistem

1. Memudahkan masyarakat melaporkan insiden siber
2. Mempercepat respon tim CSIRT terhadap insiden
3. Meningkatkan transparansi dan akuntabilitas penanganan insiden
4. Dokumentasi yang terstruktur untuk analisis dan pelaporan
5. Integrasi dengan Telegram untuk komunikasi real-time

---

## 💻 Persyaratan Sistem

### Software yang Diperlukan

1. **PHP 8.2 atau lebih tinggi**
   - Extension: `pdo`, `pdo_mysql`, `mbstring`, `xml`, `openssl`, `json`, `fileinfo`, `zip`
   - Cek versi: `php -v`

2. **Composer** (PHP Package Manager)
   - Versi 2.x atau lebih tinggi
   - Cek versi: `composer --version`
   - Download: https://getcomposer.org/

3. **Node.js & NPM**
   - Versi 18.x atau lebih tinggi
   - Cek versi: `node -v` dan `npm -v`
   - Download: https://nodejs.org/

4. **Database Server**
   - MySQL 8.0+ atau MariaDB 10.3+
   - Atau SQLite untuk development (sudah disertakan)

5. **Web Server** (Opsional untuk production)
   - Apache atau Nginx
   - Untuk development, Laravel sudah menyediakan built-in server

### Akun & Layanan Eksternal

1. **Email SMTP** (untuk notifikasi email)
   - Gmail, Outlook, atau SMTP server lainnya
   - App Password untuk Gmail (jika menggunakan 2FA)

2. **Telegram Bot** (opsional, untuk notifikasi Telegram)
   - Bot Token dari @BotFather
   - Webhook URL untuk menerima pesan dari user

---

## 🚀 Langkah-langkah Instalasi

### 1. Persiapan Environment

#### Windows

```bash
# Install XAMPP atau Laragon (sudah termasuk PHP, MySQL, Apache)
# Atau install secara terpisah:
# - PHP: https://windows.php.net/download/
# - MySQL: https://dev.mysql.com/downloads/installer/
# - Composer: https://getcomposer.org/Composer-Setup.exe
# - Node.js: https://nodejs.org/
```

#### Linux (Ubuntu/Debian)

```bash
# Install PHP dan extension
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL
sudo apt install mysql-server
```

#### macOS

```bash
# Install Homebrew (jika belum ada)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php@8.2

# Install Composer
brew install composer

# Install Node.js
brew install node

# Install MySQL
brew install mysql
```

### 2. Clone atau Download Proyek

```bash
# Jika menggunakan Git
git clone <repository-url> os-tiket
cd os-tiket

# Atau extract file ZIP ke folder os-tiket
```

### 3. Install Dependencies PHP

```bash
# Install semua package PHP yang diperlukan
composer install
```

**Penjelasan**: 
- Composer akan membaca `composer.json` dan menginstall semua package yang diperlukan
- Package utama: Laravel Framework, Spatie Permission, Laravel Sanctum, dll
- File akan diinstall ke folder `vendor/`

### 4. Install Dependencies JavaScript

```bash
# Install semua package JavaScript yang diperlukan
npm install
```

**Penjelasan**:
- NPM akan membaca `package.json` dan menginstall semua package yang diperlukan
- Package utama: Vite, Tailwind CSS, Alpine.js, dll
- File akan diinstall ke folder `node_modules/`

### 5. Setup Environment File

```bash
# Copy file .env.example menjadi .env
cp .env.example .env

# Atau di Windows:
copy .env.example .env
```

**Penjelasan**:
- File `.env` berisi konfigurasi aplikasi (database, email, dll)
- File ini tidak di-commit ke Git untuk keamanan

### 6. Generate Application Key

```bash
php artisan key:generate
```

**Penjelasan**:
- Menghasilkan encryption key untuk aplikasi Laravel
- Key ini digunakan untuk encrypt session, cookie, dll
- Key akan otomatis ditambahkan ke file `.env`

---

## ⚙️ Konfigurasi Aplikasi

### 1. Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
# Untuk MySQL/MariaDB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=os_tiket
DB_USERNAME=root
DB_PASSWORD=

# Atau untuk SQLite (development)
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
# Untuk SQLite, file database sudah ada di database/database.sqlite
```

**Langkah Setup Database MySQL**:

```bash
# Login ke MySQL
mysql -u root -p

# Buat database baru
CREATE DATABASE os_tiket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Keluar dari MySQL
exit;
```

### 2. Konfigurasi Email (SMTP)

Sesuaikan konfigurasi email di file `.env`:

```env
# Contoh untuk Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=csirt@kalselprov.go.id
MAIL_FROM_NAME="CSIRT Kalselprov"

# Catatan: Untuk Gmail, gunakan App Password, bukan password biasa
# Cara membuat App Password:
# 1. Buka Google Account Settings
# 2. Security > 2-Step Verification > App Passwords
# 3. Generate password baru untuk "Mail"
```

### 3. Konfigurasi Telegram Bot (Opsional)

```env
# Dapatkan Bot Token dari @BotFather di Telegram
TELEGRAM_BOT_TOKEN=your-bot-token-here

# Webhook URL (akan di-setup setelah aplikasi running)
# Format: https://your-domain.com/telegram/webhook
```

**Cara Mendapatkan Bot Token**:
1. Buka Telegram, cari @BotFather
2. Kirim perintah `/newbot`
3. Ikuti instruksi untuk membuat bot baru
4. Copy Bot Token yang diberikan
5. Paste ke file `.env`

### 4. Konfigurasi Aplikasi Lainnya

```env
# Nama aplikasi
APP_NAME="OS-Tiket CSIRT Kalselprov"

# Environment (local, staging, production)
APP_ENV=local

# Debug mode (true untuk development, false untuk production)
APP_DEBUG=true

# URL aplikasi
APP_URL=http://localhost:8000

# Timezone
APP_TIMEZONE=Asia/Makassar
```

---

## 🗄️ Setup Database

### 1. Jalankan Migrasi Database

```bash
# Jalankan semua migration untuk membuat tabel-tabel database
php artisan migrate
```

**Penjelasan**:
- Migration adalah file PHP yang mendefinisikan struktur database
- File migration ada di folder `database/migrations/`
- Migration akan membuat semua tabel yang diperlukan:
  - `users` - Data pengguna
  - `tickets` - Data tiket/laporan
  - `ticket_threads` - Thread/balasan tiket
  - `attachments` - File lampiran
  - `departments`, `statuses`, `priorities`, dll - Master data
  - `roles`, `permissions` - Role & Permission (Spatie)

### 2. Jalankan Seeder (Data Awal)

```bash
# Jalankan semua seeder untuk mengisi data awal
php artisan db:seed
```

**Penjelasan**:
- Seeder adalah file PHP yang mengisi database dengan data awal
- File seeder ada di folder `database/seeders/`
- Seeder akan membuat:
  - **Master Data**: Status, Prioritas, Departemen, Help Topics, SLA Plans, dll
  - **Roles & Permissions**: Super Admin, Admin, Agent, Support Agent, User
  - **User Contoh**:
    - Super Admin: `admin@csirt.kalselprov.go.id` / `password`
    - Agent: `agent@csirt.kalselprov.go.id` / `password`
  - **Chatbot Responses**: Data contoh untuk chatbot

### 3. Buat Storage Link (untuk File Upload)

```bash
# Membuat symbolic link untuk akses file upload via web
php artisan storage:link
```

**Penjelasan**:
- File yang di-upload disimpan di `storage/app/public/attachments/`
- Symbolic link membuat file bisa diakses via URL: `http://localhost:8000/storage/attachments/...`
- Tanpa link ini, file tidak bisa diakses dari browser

---

## 📧 Setup Notifikasi

### 1. Setup Email Notification

Email notification sudah dikonfigurasi otomatis setelah setup SMTP di `.env`.

**Notifikasi yang Tersedia**:
- `NewTicketSubmitted` - Notifikasi ke pelapor saat tiket dibuat
- `NewTicketCreated` - Notifikasi ke admin/agent saat tiket baru masuk
- `TicketReplyFromAgent` - Notifikasi ke pelapor saat agent membalas
- `TicketReplyFromRequester` - Notifikasi ke agent saat pelapor membalas
- `TicketAssigned` - Notifikasi ke agent saat tiket ditugaskan
- `TicketStatusChanged` - Notifikasi ke pelapor saat status berubah
- `UserRegistered` - Notifikasi ke user setelah registrasi

**Testing Email**:
```bash
# Test kirim email (jika ada route test)
# Atau buat tiket baru melalui portal untuk trigger email
```

### 2. Setup Queue (Opsional, untuk Production)

Untuk production, gunakan queue agar email tidak blocking:

```env
# Di .env, ubah:
QUEUE_CONNECTION=database
# atau
QUEUE_CONNECTION=redis
```

Jalankan queue worker:
```bash
php artisan queue:work
```

---

## 🤖 Setup Telegram Bot

### 1. Setup Webhook

Setelah aplikasi running, setup webhook untuk menerima pesan dari Telegram:

```bash
# Ganti YOUR_BOT_TOKEN dan YOUR_DOMAIN
curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://YOUR_DOMAIN.com/telegram/webhook"
```

**Atau via Browser**:
```
https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://YOUR_DOMAIN.com/telegram/webhook
```

**Untuk Development (menggunakan ngrok)**:
```bash
# Install ngrok: https://ngrok.com/
ngrok http 8000

# Copy URL ngrok (contoh: https://abc123.ngrok.io)
# Setup webhook:
curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://abc123.ngrok.io/telegram/webhook"
```

### 2. Test Telegram Bot

1. Buka Telegram, cari bot Anda (nama bot sesuai yang dibuat di @BotFather)
2. Kirim pesan `/start`
3. Bot akan merespons dan menyimpan chat_id otomatis

### 3. Konfigurasi Chatbot Responses

1. Login sebagai Super Admin
2. Akses Admin Panel > Chatbot Responses
3. Tambah response baru dengan keyword dan jawaban
4. Bot akan otomatis merespons sesuai keyword yang dikonfigurasi

**Detail lengkap**: Lihat file `CHATBOT_RESPONSES_GUIDE.md`

---

## ▶️ Menjalankan Aplikasi

### 1. Build Assets (CSS & JavaScript)

```bash
# Development mode (dengan hot reload)
npm run dev

# Production mode (optimized)
npm run build
```

**Penjelasan**:
- `npm run dev` - Menjalankan Vite dev server untuk development
- `npm run build` - Compile dan minify assets untuk production
- Assets akan di-compile dari `resources/css/` dan `resources/js/` ke `public/build/`

### 2. Jalankan Laravel Development Server

```bash
# Jalankan server di http://127.0.0.1:8000
php artisan serve

# Atau dengan port custom
php artisan serve --port=8080
```

### 3. Akses Aplikasi

Buka browser dan akses:
- **Homepage**: http://127.0.0.1:8000
- **Login**: http://127.0.0.1:8000/login
- **Portal**: http://127.0.0.1:8000/portal/ticket/new
- **Agent Dashboard**: http://127.0.0.1:8000/agent (harus login sebagai agent)
- **Admin Panel**: http://127.0.0.1:8000/admin (harus login sebagai super admin)

### 4. Login dengan Akun Contoh

**Super Admin**:
- Email: `admin@csirt.kalselprov.go.id`
- Password: `password`

**Agent**:
- Email: `agent@csirt.kalselprov.go.id`
- Password: `password`

### 5. Menjalankan Semua Bersamaan (Development)

```bash
# Menjalankan server, queue, logs, dan vite secara bersamaan
composer run dev
```

**Penjelasan**:
- Script ini akan menjalankan 4 proses secara bersamaan:
  1. Laravel server (`php artisan serve`)
  2. Queue worker (`php artisan queue:listen`)
  3. Log viewer (`php artisan pail`)
  4. Vite dev server (`npm run dev`)

---

## 🏗️ Arsitektur Sistem

### 1. Arsitektur Umum

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                      │
└───────────────────────┬─────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────┐
│              LARAVEL APPLICATION LAYER                   │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Routes (web.php)                                │  │
│  │  - Portal Routes                                  │  │
│  │  - Agent/Admin Routes                             │  │
│  │  - Telegram Webhook Route                         │  │
│  └──────────────────────────────────────────────────┘  │
│                        │                                 │
│                        ▼                                 │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Controllers                                      │  │
│  │  - Portal/TicketController                       │  │
│  │  - Agent/TicketController                         │  │
│  │  - Admin/*Controller                              │  │
│  │  - TelegramWebhookController                     │  │
│  └──────────────────────────────────────────────────┘  │
│                        │                                 │
│                        ▼                                 │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Models (Eloquent ORM)                            │  │
│  │  - User, Ticket, TicketThread, etc.               │  │
│  └──────────────────────────────────────────────────┘  │
│                        │                                 │
│                        ▼                                 │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Notifications                                    │  │
│  │  - Email Channel                                  │  │
│  │  - Telegram Channel                               │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL)                      │
└─────────────────────────────────────────────────────────┘
```

### 2. Flow Pembuatan Tiket

```
User (Portal)
    │
    ├─> Isi Form Laporan
    │
    ▼
Portal/TicketController@store
    │
    ├─> Validasi Input
    │
    ├─> Generate Nomor Tiket (CSIRT-XXXXX)
    │
    ├─> Simpan ke Database
    │   ├─> Tabel: tickets
    │   ├─> Tabel: ticket_threads (pesan pertama)
    │   └─> Tabel: attachments (jika ada)
    │
    ├─> Hitung Due Date dari SLA Plan
    │
    ├─> Notifikasi ke Pelapor
    │   ├─> Email ✅
    │   └─> Telegram ✅ (jika ada telegram_username)
    │
    └─> Notifikasi ke Admin/Agent
        ├─> Email ✅
        └─> Telegram ✅ (jika ada telegram_username)
```

### 3. Flow Penanganan Tiket oleh Agent

```
Agent Login
    │
    ├─> Dashboard Agent
    │   └─> Lihat Daftar Tiket
    │
    ├─> Klik Tiket
    │   └─> Detail Tiket
    │
    ├─> Assign Tiket (opsional)
    │   └─> Notifikasi ke Agent yang ditugaskan
    │
    ├─> Balas Tiket
    │   ├─> Simpan Balasan ke ticket_threads
    │   ├─> Update Status Tiket
    │   └─> Notifikasi ke Pelapor
    │
    └─> Ubah Status
        ├─> Update Status di Database
        └─> Notifikasi ke Pelapor (jika status penting)
```

### 4. Role & Permission System

```
User Login
    │
    ├─> Check Authentication
    │
    ├─> Check Role (Spatie Permission)
    │   ├─> Super Admin → Full Access
    │   ├─> Admin → Admin Panel
    │   ├─> Agent → Agent Panel
    │   └─> User → Portal Only
    │
    └─> Check Permission
        ├─> admin.panel → Dashboard access
        ├─> tickets.* → Ticket management
        └─> master.* → Master data management
```

---

## ✨ Fitur Utama

### 1. Portal Pelaporan (Public)

**Fitur**:
- ✅ Form pelaporan insiden siber
- ✅ Upload file/lampiran (maks 10MB)
- ✅ Cek status laporan (dengan verifikasi email)
- ✅ Balasan dan komunikasi dengan agent
- ✅ View detail tiket dengan thread

**Akses**: 
- Public (untuk cek status)
- Login required (untuk membuat tiket baru)

### 2. Dashboard Agent/Admin

**Fitur**:
- ✅ Manajemen tiket (view, assign, update status)
- ✅ Balasan tiket dengan attachment
- ✅ Canned responses (template balasan)
- ✅ Filter dan pencarian tiket
- ✅ Statistik dan laporan
- ✅ Internal notes (catatan untuk agent lain)

**Akses**: 
- User dengan role Agent, Admin, atau Super Admin

### 3. Admin Panel

**Fitur**:
- ✅ Manajemen Master Data:
  - Departments (Departemen)
  - Help Topics (Kategori insiden)
  - Priorities (Prioritas)
  - Statuses (Status tiket)
  - SLA Plans (Rencana SLA)
  - Teams (Tim agent)
  - Organizations (Organisasi)
  - Canned Responses (Template balasan)
  - Chatbot Responses (Respons bot Telegram)
- ✅ Manajemen User & Role
- ✅ Assign Role & Permission

**Akses**: 
- Hanya Super Admin

### 4. Sistem Notifikasi

**Channel**:
- ✅ **Email Notification** - Notifikasi via email SMTP
- ✅ **Telegram Notification** - Notifikasi via Telegram Bot

**Event yang Mencetuskan Notifikasi**:
- Tiket baru dibuat (ke pelapor & admin/agent)
- Agent membalas tiket (ke pelapor)
- Pelapor membalas tiket (ke agent)
- Tiket ditugaskan ke agent (ke agent)
- Status tiket berubah (ke pelapor)
- User baru registrasi (ke user)

### 5. Telegram Bot & Chatbot

**Fitur**:
- ✅ Notifikasi otomatis via Telegram
- ✅ Chatbot dengan keyword matching
- ✅ Auto-save chat_id saat user mengirim /start
- ✅ Webhook untuk menerima pesan dari user

**Konfigurasi**:
- Keyword-based responses (contains, exact, starts_with)
- Priority system untuk multiple matches
- Active/inactive status per response

---

## 📁 Struktur Proyek

### Direktori Penting

```
os-tiket/
│
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Portal/           # Controller untuk portal pelapor
│   │   │   ├── Agent/            # Controller untuk agent dashboard
│   │   │   ├── Admin/            # Controller untuk admin panel
│   │   │   └── ProfileController.php
│   │   ├── Middleware/           # Custom middleware
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # Eloquent models
│   │   ├── User.php
│   │   ├── Ticket.php
│   │   ├── TicketThread.php
│   │   └── ...
│   ├── Notifications/            # Notification classes
│   │   ├── Channels/
│   │   │   └── TelegramChannel.php
│   │   ├── NewTicketCreated.php
│   │   └── ...
│   ├── Services/                 # Service classes
│   │   ├── TelegramService.php
│   │   └── ChatbotService.php
│   └── Providers/                # Service providers
│
├── config/                       # Konfigurasi aplikasi
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   ├── services.php              # Telegram config
│   └── permission.php            # Spatie Permission config
│
├── database/
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   │   ├── DatabaseSeeder.php
│   │   ├── MasterDataSeeder.php
│   │   ├── RolePermissionSeeder.php
│   │   └── ...
│   └── database.sqlite           # SQLite database (dev)
│
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/              # Layout templates
│   │   ├── portal/               # Portal views
│   │   ├── agent/                # Agent dashboard views
│   │   ├── admin/                # Admin panel views
│   │   └── components/           # Reusable components
│   ├── css/                      # CSS source files
│   └── js/                       # JavaScript source files
│
├── routes/
│   ├── web.php                   # Web routes
│   └── auth.php                  # Auth routes (Breeze)
│
├── public/                       # Public assets
│   ├── index.php                 # Entry point
│   ├── storage/                   # Symlink ke storage
│   └── build/                    # Compiled assets
│
├── storage/                      # Storage files
│   ├── app/
│   │   └── public/
│   │       └── attachments/      # Uploaded files
│   └── logs/                     # Log files
│
├── .env                          # Environment configuration
├── .env.example                  # Environment template
├── composer.json                 # PHP dependencies
├── package.json                  # JavaScript dependencies
└── README.md                     # Dokumentasi utama
```

### File Konfigurasi Penting

1. **`.env`** - Semua konfigurasi aplikasi (database, email, telegram, dll)
2. **`config/app.php`** - Konfigurasi aplikasi Laravel
3. **`config/database.php`** - Konfigurasi database
4. **`config/mail.php`** - Konfigurasi email
5. **`config/services.php`** - Konfigurasi Telegram bot
6. **`config/permission.php`** - Konfigurasi Spatie Permission

### Model Utama

1. **`User`** - Model pengguna (extends Spatie HasRoles)
2. **`Ticket`** - Model tiket/laporan
3. **`TicketThread`** - Model thread/balasan tiket
4. **`Attachment`** - Model file lampiran
5. **`Department`**, **`Status`**, **`Priority`**, dll - Master data models

---

## 🔧 Troubleshooting

### 1. Error: "Class not found" atau "Autoload error"

**Solusi**:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### 2. Error: "Permission denied" untuk storage

**Solusi**:
```bash
# Linux/macOS
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (jika menggunakan WSL)
# Biasanya tidak perlu, tapi pastikan folder tidak read-only
```

### 3. Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solusi**:
- Pastikan MySQL server running
- Cek konfigurasi database di `.env`
- Pastikan database sudah dibuat
- Cek username dan password

### 4. Email tidak terkirim

**Solusi**:
- Cek konfigurasi SMTP di `.env`
- Untuk Gmail, pastikan menggunakan App Password
- Cek log: `storage/logs/laravel.log`
- Test dengan queue: `php artisan queue:work`

### 5. Telegram Bot tidak merespons

**Solusi**:
- Pastikan Bot Token benar di `.env`
- Pastikan webhook sudah di-setup
- Cek log: `storage/logs/laravel.log`
- Test webhook: kirim pesan ke bot dan cek log

### 6. Assets (CSS/JS) tidak muncul

**Solusi**:
```bash
# Rebuild assets
npm run build

# Atau jalankan dev server
npm run dev
```

### 7. Error: "Storage link already exists"

**Solusi**:
```bash
# Hapus link yang ada
rm public/storage

# Buat link baru
php artisan storage:link
```

### 8. Permission tidak bekerja

**Solusi**:
```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 9. Migration Error

**Solusi**:
```bash
# Reset database (HATI-HATI: akan menghapus semua data)
php artisan migrate:fresh --seed

# Atau rollback dan migrate ulang
php artisan migrate:rollback
php artisan migrate
```

### 10. Port 8000 sudah digunakan

**Solusi**:
```bash
# Gunakan port lain
php artisan serve --port=8080
```

---

## 📚 Dokumentasi Tambahan

File dokumentasi lainnya yang tersedia:

1. **`README.md`** - Dokumentasi utama proyek
2. **`ARCHITECTURE_DIAGRAM.md`** - Diagram arsitektur sistem
3. **`DATABASE_DOCUMENTATION.md`** - Dokumentasi database lengkap
4. **`USE_CASE_DIAGRAM.md`** - Use case diagram untuk setiap role
5. **`ROLE_PERMISSION.md`** - Dokumentasi role & permission
6. **`CHATBOT_RESPONSES_GUIDE.md`** - Panduan mengisi chatbot responses
7. **`TELEGRAM_SETUP.md`** - Panduan setup Telegram bot
8. **`EMAIL_SETUP.md`** - Panduan setup email
9. **`PRESENTATION_GUIDE.md`** - Panduan presentasi proyek

---

## 🎓 Poin Penting untuk Presentasi ke Dosen Penguji

### 1. **Teknologi yang Digunakan**
- Laravel 12 (Framework PHP modern)
- MySQL (Database relational)
- Tailwind CSS + Alpine.js (Frontend modern)
- Spatie Permission (RBAC system)
- Laravel Notifications (Multi-channel: Email & Telegram)
- Telegram Bot API (Integrasi chatbot)

### 2. **Arsitektur Sistem**
- MVC Pattern (Model-View-Controller)
- Service Layer untuk business logic kompleks
- Notification System dengan multiple channels
- Role-Based Access Control (RBAC)
- RESTful API untuk Telegram webhook

### 3. **Fitur Unik**
- Integrasi Telegram Bot untuk notifikasi real-time
- Chatbot dengan keyword matching system
- Auto-save Telegram chat_id untuk notifikasi
- Multi-channel notification (Email + Telegram)
- Portal publik dengan verifikasi email untuk cek status

### 4. **Keamanan**
- Authentication & Authorization (Laravel Breeze + Spatie)
- Input validation (Form Request)
- File upload validation (type & size)
- SQL injection protection (Eloquent ORM)
- XSS protection (Blade templating)

### 5. **Best Practices yang Diterapkan**
- Separation of Concerns (Controller, Model, Service)
- DRY Principle (Don't Repeat Yourself)
- Database Migration & Seeding
- Environment Configuration (.env)
- Logging untuk debugging

### 6. **Alur Kerja Sistem**
1. User membuat tiket via portal
2. Sistem generate nomor tiket otomatis
3. Notifikasi ke pelapor & admin/agent
4. Agent meninjau dan menangani tiket
5. Komunikasi dua arah (pelapor ↔ agent)
6. Tracking status dari awal hingga selesai
7. Notifikasi real-time untuk setiap event penting

---

## ✅ Checklist Setup Lengkap

Gunakan checklist ini untuk memastikan semua sudah terinstall dengan benar:

- [ ] PHP 8.2+ terinstall
- [ ] Composer terinstall
- [ ] Node.js & NPM terinstall
- [ ] MySQL/SQLite terinstall dan running
- [ ] Dependencies PHP terinstall (`composer install`)
- [ ] Dependencies JavaScript terinstall (`npm install`)
- [ ] File `.env` sudah dibuat dan dikonfigurasi
- [ ] Application key sudah di-generate
- [ ] Database sudah dibuat (jika MySQL)
- [ ] Migration sudah dijalankan
- [ ] Seeder sudah dijalankan
- [ ] Storage link sudah dibuat
- [ ] Email SMTP sudah dikonfigurasi (opsional)
- [ ] Telegram Bot sudah dibuat dan dikonfigurasi (opsional)
- [ ] Assets sudah di-build
- [ ] Server sudah running
- [ ] Bisa login dengan akun contoh
- [ ] Bisa membuat tiket baru
- [ ] Notifikasi email terkirim (jika dikonfigurasi)
- [ ] Telegram bot merespons (jika dikonfigurasi)

---

## 📞 Bantuan & Support

Jika mengalami masalah:

1. **Cek Log**: `storage/logs/laravel.log`
2. **Cek Dokumentasi**: Baca file `.md` yang relevan
3. **Cek Konfigurasi**: Pastikan semua setting di `.env` benar
4. **Cek Dependencies**: Pastikan semua package terinstall

---

**Versi**: 1.0  
**Dibuat untuk**: Sistem OS-Tiket CSIRT Kalselprov  
**Update Terakhir**: 2024

---

## 🎯 Kesimpulan

Dokumen ini menjelaskan langkah-langkah lengkap untuk membangun sistem OS-Tiket dari awal. Dengan mengikuti panduan ini, Anda akan:

1. ✅ Memahami struktur dan arsitektur sistem
2. ✅ Mampu menginstall dan mengkonfigurasi sistem
3. ✅ Memahami alur kerja dan fitur-fitur utama
4. ✅ Siap menjelaskan proyek secara jelas kepada dosen penguji

**Selamat mengerjakan proyek!** 🚀

