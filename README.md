# CSIRT Kalselprov – Pengaduan Insiden Siber (Ticketing System)

Sistem pelaporan dan penanganan insiden siber untuk Pemerintah Provinsi Kalimantan Selatan (CSIRT Kalselprov). Aplikasi ini menyediakan portal pelapor publik, dashboard agen, dan panel admin untuk mengelola master data, pengguna, serta alur penanganan insiden dari awal hingga selesai.

## Fitur Utama

-   Portal pelapor untuk membuat tiket dan memantau status via nomor tiket/email
-   Upload lampiran pada laporan dan balasan
-   Penomoran tiket otomatis (prefix `CSIRT` + counter)
-   Dashboard Agent untuk meninjau, membalas, mengubah status, dan meng-assign tiket
-   Panel Admin untuk manajemen master data: Departments, Help Topics, Status, Priority, SLA Plans, Teams, Canned Responses, Organizations, Users
-   Role & Permission berbasis Spatie (Super Admin, Admin, Agent, Support Agent, User)
-   Notifikasi email otomatis untuk event penting (tiket baru, balasan agen/pelapor, perubahan status tertentu, assignment)
-   Desain modern responsif (Tailwind + Alpine) dengan branding CSIRT Kalselprov

## Teknologi

-   Laravel 10+
-   PHP 8.2+
-   MySQL/MariaDB
-   Tailwind CSS, Vite, Alpine.js
-   Spatie Laravel Permission v6 (Role & Permission)
-   Laravel Notifications (Mail)

---

## Arsitektur & Modul

-   Portal (Guest/User): membuat laporan, melihat detail, membalas
-   Agent Panel: melihat daftar tiket, detail, membalas, ubah status, assign
-   Admin Panel: CRUD master data + manajemen pengguna/role
-   Notifications: email ke pelapor/agent/admin sesuai event

Struktur direktori penting:

-   `app/Models/*` – Model Eloquent
-   `app/Http/Controllers/Portal/*` – Portal pelapor
-   `app/Http/Controllers/Agent/*` – Panel agent
-   `app/Http/Controllers/Admin/*` – Panel admin
-   `app/Notifications/*` – Notifikasi email
-   `resources/views/*` – Blade views (layouts agent/admin/guest, portal, admin, agent)
-   `routes/web.php` – Definisi route web
-   `database/seeders/*` – Seeder master data, role/permission, data contoh

---

## Skema Database (intisari)

Tabel utama (nama kolom kunci saja):

-   `tiket`
    -   id, uuid, nomor_tiket, subjek
    -   email_pelapor, nama_pelapor, id_pengguna (nullable)
    -   id_departemen, id_topik_bantuan (nullable), id_prioritas (nullable)
    -   id_status, id_rencana_sla (nullable)
    -   jatuh_tempo_pada, ditutup_pada
    -   ditugaskan_ke (nullable), dikunci_oleh/dikunci_sampai (opsional), bidang_kustom (json)
-   `utas_tiket`
    -   id, id_tiket, tipe (pesan/balasan/catatan), id_pengguna (nullable), isi
-   `lampiran`
    -   id, id_utas_tiket, nama_file, mime, ukuran, path
-   `status`
    -   id, nama (Terbuka, Menunggu Pelapor, Ditugaskan, Dalam Proses, Tertutup, Dibatalkan), slug (`open`, `answered`, `assigned`, `in_progress`, `closed`, `cancelled`), menutup
-   `prioritas`, `departemen`, `topik_bantuan`, `rencana_sla`, `tim`, `organisasi`, `respons_template`
-   Tabel Spatie Permission: `roles`, `permissions`, `model_has_roles`, `role_has_permissions`, `model_has_permissions`

Relasi penting:

-   Tiket hasMany UtasTiket; UtasTiket hasMany Lampiran
-   Tiket belongsTo Status/Prioritas/Departemen/TopikBantuan/RencanaSla
-   Tiket belongsTo assignee `pengguna` via `ditugaskan_ke`
-   Pengguna HasRoles (Spatie)

---

## Status Tiket & Alur

Status default (Seeder `MasterDataSeeder`):

-   Terbuka (`open`) – tiket baru dibuat atau menunggu respons agent
-   Menunggu Pelapor (`answered`) – menunggu balasan pelapor setelah agent meminta info/menjawab
-   Ditugaskan (`assigned`) – tiket telah dipasangkan ke agent tertentu
-   Dalam Proses (`in_progress`) – tiket sedang dikerjakan agent
-   Tertutup (`closed`) – tiket selesai; `ditutup_pada` terisi
-   Dibatalkan (`cancelled`) – tiket dibatalkan

Perubahan status otomatis di kode:

-   Portal balasan pelapor → set `answered` (menunggu pelapor) di `Portal/TicketController@reply`
-   Agent balasan ke pelapor → set ke `open` (menunggu pelapor membalas) di `Agent/TicketController@reply`
-   Assignment oleh agent → set ke `assigned` (jika ada) di `Agent/AssignmentController`
-   Ubah status manual oleh agent → `Agent/TicketController@setStatus`

Notifikasi status ke pelapor dikirim ketika status berubah ke salah satu: `assigned`, `in_progress`, `closed`.

**Catatan**: Semua nama tabel dan kolom database menggunakan bahasa Indonesia. Lihat `DATABASE_DOCUMENTATION.md` untuk detail lengkap.

---

## Roles & Permissions

Dikelola oleh Spatie Permission.

Roles bawaan:

-   Super Admin – akses penuh (termasuk Admin Panel)
-   Admin – akses terbatas (bisa disesuaikan)
-   Agent – menangani tiket (akses Agent Panel)
-   Support Agent – subset akses agent
-   User – default saat registrasi (hanya portal)

Kebijakan akses:

-   Admin Panel: HANYA `role:Super Admin`
-   Agent Panel: pengguna dengan permission/role terkait (di middleware controller Agent)
-   Dropdown “Admin Panel” hanya tampil jika `@role('Super Admin')`

Seeder menyediakan:

-   Permissions inti: `admin.panel`, `tickets.*`, `users.*`, `departments.*`, dsb.
-   Roles + mapping permissions
-   Akun contoh:
    -   Super Admin: `admin@csirt.kalselprov.go.id` / `password`
    -   Agent: `agent@csirt.kalselprov.go.id` / `password`

---

## Notifikasi Email

Notifikasi yang tersedia (`app/Notifications`):

-   `NewTicketSubmitted` – ke pelapor saat tiket dibuat (detail tiket lengkap)
-   `NewTicketCreated` – ke admin/agent saat tiket baru masuk
-   `TicketReplyFromAgent` – ke pelapor saat agent membalas
-   `TicketReplyFromRequester` – ke admin/agent saat pelapor membalas
-   `TicketAssigned` – ke agent yang ditugaskan menangani tiket
-   `TicketStatusChanged` – ke pelapor saat status berubah (assigned/in_progress/closed)
-   `UserRegistered` – ke user setelah registrasi

Catatan pengiriman:

-   Beberapa notif disetel synchronous (tanpa queue) demi kemudahan dev/testing
-   Produksi direkomendasikan memakai queue worker (`QUEUE_CONNECTION=database/redis`)

---

## Instalasi & Menjalankan

### Prasyarat

-   PHP 8.2+, Composer
-   MySQL/MariaDB
-   Node.js 18+ (untuk Vite)

### Langkah Setup

1. Clone & install dependency

```bash
composer install
npm install
```

2. Buat file env & kunci aplikasi

```bash
cp .env.example .env
php artisan key:generate
```

3. Konfigurasi database di `.env`

```env
DB_DATABASE=csirt
DB_USERNAME=root
DB_PASSWORD=secret
```

4. Konfigurasi Mail (SMTP) di `.env` (contoh)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=you@example.com
MAIL_PASSWORD=app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=csirt@kalselprov.go.id
MAIL_FROM_NAME="CSIRT Kalselprov"
```

5. Migrasi & seeder

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat master data, roles/permissions, dan akun contoh (lihat bagian Roles).

6. Build asset & jalankan server

```bash
npm run dev        # atau: npm run build (produksi)
php artisan serve  # http://127.0.0.1:8000
```

7. Opsi: reset cache permission saat mengubah role/permission

```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Rute Utama

-   Portal (guest/pelapor)
    -   `GET /portal/ticket/new` – Form laporan baru
    -   `POST /portal/ticket` – Simpan laporan
    -   `GET /portal/ticket/status` – Form cek status
    -   `POST /portal/ticket/status` – Cek status tiket
    -   `GET /portal/ticket/{ticket_number}` – Detail tiket (guest)
    -   `POST /portal/ticket/{ticket_number}/reply` – Balasan pelapor
-   Auth (Breeze)
    -   `GET /login`, `POST /login`, `GET /register`, `POST /register`
-   Agent Panel (hanya user dengan akses agent)
    -   `GET /agent` – Dashboard
    -   `GET /agent/tickets` – Daftar tiket
    -   `GET /agent/tickets/{ticket}` – Detail tiket
    -   `POST /agent/tickets/{ticket}/reply` – Balasan agent
    -   `POST /agent/tickets/{ticket}/status` – Ubah status
    -   `POST /agent/tickets/{ticket}/assign` – Assign agent (permission `tickets.assign`)
-   Admin Panel (khusus `role:Super Admin`)
    -   `GET /admin` – redirect ke `admin.users.index`
    -   Resource CRUD: departments, help-topics, sla, priorities, statuses, teams, canned, organizations, users

Redirect setelah login:

-   User biasa → `welcome` (portal)
-   User dengan `admin.panel` sebelumnya → `agent.dashboard` (namun Admin Panel tetap dikunci hanya Super Admin)

---

## Storage & Lampiran

-   Upload lampiran: disimpan di disk `public` (folder `storage/app/public/attachments`), symlink ke `public/storage` bila diperlukan
-   Pastikan jalankan `php artisan storage:link` jika belum

Validasi lampiran (portal/agent): ukuran maks 10MB, tipe: `jpg,jpeg,png,gif,pdf,doc,docx`.

---

## Branding & UI

-   Logo instansi: letakkan file gambar di `public/images/logo-kalselprov.png` (PNG direkomendasikan). Ukuran ditangani via kelas utility Tailwind.
-   Layout modern untuk Guest (auth), Agent, dan Admin dengan tema biru–indigo.

---

## Alur Penggunaan

1. Pelapor membuat tiket di portal (opsional mengisi nama, wajib email). Dapat lampiran.
2. Sistem membuat `nomor_tiket`, menetapkan `status=open`, SLA, dan due date dari SLA plan.
3. Notifikasi ke pelapor dan ke agent/admin terkirim.
4. Agent meninjau tiket, membalas, mengubah status, dan/atau meng-assign ke agent tertentu.
5. Balasan akan mengubah status sesuai aturan (lihat Status Tiket & Alur).
6. Setelah selesai ditangani, agent menutup tiket (`closed`). Pelapor menerima email konfirmasi penutupan.

---

## Keamanan & Best Practices

-   Gunakan permission untuk pengecekan akses bisnis; gunakan role untuk grouping
-   Clear cache permission setelah mengubah role/permission di produksi
-   Simpan kredensial SMTP & DB di environment variable
-   Gunakan queue untuk skala produksi agar pengiriman email non-blocking

---

## Troubleshooting

-   Permission tidak bekerja:
    ```bash
    php artisan permission:cache-reset
    php artisan config:clear && php artisan cache:clear && php artisan view:clear
    ```
-   Email tidak terkirim:
    -   Cek konfigurasi MAIL di `.env`
    -   Cek log: `storage/logs/laravel.log`
    -   Untuk dev, nonaktifkan queue atau jalankan worker: `php artisan queue:work`
-   Error migrasi/Seeder:
    ```bash
    php artisan migrate:fresh --seed
    ```

---

## Lisensi

Internal project CSIRT Kalselprov. Hak cipta Pemerintah Provinsi Kalimantan Selatan.
