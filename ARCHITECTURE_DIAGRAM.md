# Diagram Arsitektur Sistem OS-Tiket

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│                  LARAVEL APPLICATION                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Routes (web.php)                                     │  │
│  │  - Portal Routes                                      │  │
│  │  - Agent/Admin Routes                                 │  │
│  │  - Profile Routes                                      │  │
│  │  - Telegram Webhook Route                             │  │
│  └──────────────────────────────────────────────────────┘  │
│                        │                                     │
│                        ▼                                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Controllers                                          │  │
│  │  - Portal/TicketController                           │  │
│  │  - Agent/TicketController                             │  │
│  │  - Admin/*Controller                                  │  │
│  │  - ProfileController                                  │  │
│  │  - TelegramWebhookController                         │  │
│  └──────────────────────────────────────────────────────┘  │
│                        │                                     │
│                        ▼                                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Models (Eloquent)                                    │  │
│  │  - User, Ticket, TicketThread, etc.                  │  │
│  └──────────────────────────────────────────────────────┘  │
│                        │                                     │
│                        ▼                                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Notifications                                       │  │
│  │  - NewTicketCreated                                  │  │
│  │  - NewTicketSubmitted                                │  │
│  │  - TicketAssigned, etc.                              │  │
│  └──────────────────────────────────────────────────────┘  │
│         │                    │                              │
│         ▼                    ▼                              │
│  ┌─────────────┐    ┌──────────────────┐                   │
│  │ Mail Channel│    │ Telegram Channel │                   │
│  └─────────────┘    └──────────────────┘                   │
│         │                    │                              │
└─────────┼────────────────────┼──────────────────────────────┘
          │                    │
          ▼                    ▼
┌─────────────────┐  ┌──────────────────────┐
│   SMTP Server   │  │  Telegram Bot API     │
│   (Email)       │  │  (TelegramService)    │
└─────────────────┘  └──────────────────────┘
          │                    │
          ▼                    ▼
┌─────────────────┐  ┌──────────────────────┐
│  User Email     │  │  Telegram Bot        │
│  Inbox          │  │  (Chat dengan User)   │
└─────────────────┘  └──────────────────────┘
```

---

## 📊 Flow Notifikasi

### Flow: Tiket Baru Dibuat

```
User (Portal)
    │
    ├─> Buat Tiket
    │
    ▼
Portal/TicketController
    │
    ├─> Simpan ke Database
    │
    ├─> Notifikasi ke Pelapor
    │   ├─> Email ✅
    │   └─> Telegram ✅ (jika ada telegram_username)
    │
    └─> Notifikasi ke Admin/Agent
        ├─> Email ✅
        └─> Telegram ✅ (jika ada telegram_username)
```

### Flow: Telegram Notification

```
Notification Class
    │
    ├─> via() method
    │   └─> ['mail', 'telegram']
    │
    ├─> toMail() → Email Channel
    │
    └─> toTelegram() → Telegram Channel
            │
            ▼
        TelegramChannel
            │
            ├─> Cek telegram_username
            │
            ├─> TelegramService.sendMessage()
            │
            ├─> Cek telegram_chat_id di database
            │   ├─> Ada → Kirim langsung
            │   └─> Tidak ada → Cari dari getUpdates
            │
            └─> Kirim via Telegram Bot API
```

---

## 🔄 Flow User Registration dengan Telegram

```
User Register
    │
    ├─> Isi Form (termasuk telegram_username)
    │
    ├─> RegisteredUserController
    │
    ├─> Simpan ke Database
    │
    ├─> Coba Auto-Get Chat ID
    │   ├─> Dari getUpdates
    │   └─> Jika ditemukan → Simpan chat_id
    │
    └─> Jika tidak ditemukan
        │
        └─> User kirim /start ke bot
            │
            └─> Webhook menerima update
                │
                └─> Auto-save chat_id ke database
```

---

## 📁 Struktur File Notifikasi

```
app/Notifications/
├── Channels/
│   └── TelegramChannel.php          # Custom channel untuk Telegram
│
├── NewTicketCreated.php             # Notif tiket baru (ke admin/agent)
│   ├── via() → ['mail', 'telegram']
│   ├── toMail()
│   └── toTelegram()
│
├── NewTicketSubmitted.php           # Notif tiket dibuat (ke pelapor)
├── TicketAssigned.php               # Notif assignment (ke agent)
├── TicketStatusChanged.php          # Notif perubahan status (ke pelapor)
├── TicketReplyFromAgent.php         # Notif balasan agent (ke pelapor)
└── TicketReplyFromRequester.php     # Notif balasan pelapor (ke agent)
```

---

## 🔧 Konfigurasi File Mapping

```
┌─────────────────────────────────────────────────────────┐
│  Konfigurasi Email                                       │
├─────────────────────────────────────────────────────────┤
│  .env                    → MAIL_* variables              │
│  config/mail.php        → Mail configuration            │
│  app/Notifications/*    → Notification classes          │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Konfigurasi Telegram                                    │
├─────────────────────────────────────────────────────────┤
│  .env                    → TELEGRAM_BOT_TOKEN           │
│  config/services.php     → Telegram config              │
│  app/Services/           → TelegramService            │
│    TelegramService.php                                    │
│  app/Notifications/      → TelegramChannel              │
│    Channels/                                              │
│    TelegramChannel.php                                    │
│  app/Http/Controllers/   → TelegramWebhookController     │
│    TelegramWebhookController.php                          │
│  routes/web.php          → /telegram/webhook route       │
└─────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema (Simplified)

```
users
├── id
├── name
├── email
├── phone
├── telegram_username      ← Username Telegram
├── telegram_chat_id       ← Chat ID (auto-saved)
└── ...

tickets
├── id
├── ticket_number
├── subject
├── user_id                ← Link ke users
├── assigned_to            ← Link ke users (agent)
├── status_id
├── priority_id
├── department_id
└── ...

ticket_threads
├── id
├── ticket_id              ← Link ke tickets
├── user_id                ← Link ke users
├── body
└── ...
```

---

## 🎯 Role & Permission Flow

```
User Login
    │
    ├─> Check Authentication
    │
    ├─> Check Role
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

## 📍 File Location Quick Map

```
os-tiket/
│
├── .env                          ← SEMUA KONFIGURASI DI SINI
│
├── config/
│   ├── services.php              ← Telegram bot config
│   ├── mail.php                  ← Email config
│   ├── database.php              ← Database config
│   └── permission.php            ← RBAC config
│
├── app/
│   ├── Http/Controllers/
│   │   ├── Portal/TicketController.php      ← Portal logic
│   │   ├── Agent/TicketController.php       ← Agent logic
│   │   ├── Admin/*Controller.php             ← Admin logic
│   │   ├── ProfileController.php            ← Profile logic
│   │   └── TelegramWebhookController.php    ← Webhook handler
│   │
│   ├── Services/
│   │   └── TelegramService.php               ← Telegram API service
│   │
│   ├── Notifications/
│   │   ├── Channels/
│   │   │   └── TelegramChannel.php          ← Telegram channel
│   │   └── *.php                            ← Notification classes
│   │
│   └── Providers/
│       └── AppServiceProvider.php           ← Register Telegram channel
│
├── routes/
│   └── web.php                              ← All routes
│
└── database/
    ├── migrations/                          ← Database schema
    └── seeders/                             ← Default data
```

---

## 🎬 Demo Script untuk Presentasi

### 1. Demo Portal (2 menit)

```
1. Buka http://127.0.0.1:8000
2. Login sebagai user
3. Klik "Laporkan Insiden Siber"
4. Isi form dan submit
5. Tunjukkan notifikasi email + Telegram terkirim
```

### 2. Demo Agent Dashboard (2 menit)

```
1. Login sebagai agent
2. Tunjukkan dashboard dengan tiket baru
3. Klik tiket → Assign ke agent lain
4. Balas tiket
5. Tunjukkan notifikasi terkirim ke pelapor
```

### 3. Demo Profile & Telegram (1 menit)

```
1. Buka halaman Profile
2. Tunjukkan field Telegram username
3. Tunjukkan status chat_id (aktif/belum aktif)
4. Edit dan save
```

---

**Gunakan diagram ini untuk menjelaskan arsitektur sistem saat presentasi!**
