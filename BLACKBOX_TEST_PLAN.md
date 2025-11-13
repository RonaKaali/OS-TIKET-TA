# 📋 Blackbox Test Plan - OS-Tiket CSIRT Kalselprov

## 🎯 Tujuan Testing

Blackbox testing dilakukan untuk memastikan semua fitur aplikasi berfungsi dengan benar dari perspektif pengguna akhir, tanpa melihat kode internal. Testing ini mencakup:

-   ✅ Fungsi aplikasi sesuai requirement
-   ✅ Alur bisnis berjalan dengan benar
-   ✅ Validasi input bekerja dengan baik
-   ✅ Keamanan akses sesuai role/permission
-   ✅ Notifikasi email terkirim dengan benar
-   ✅ UI/UX responsif dan user-friendly

---

## 📦 Test Environment Setup

### Prasyarat

-   ✅ Database sudah di-migrate dan di-seed
-   ✅ Mail configuration sudah benar (untuk testing notifikasi)
-   ✅ Storage link sudah dibuat: `php artisan storage:link`
-   ✅ Server berjalan: `php artisan serve`
-   ✅ Asset sudah di-build: `npm run dev` atau `npm run build`

### Test Accounts (dari Seeder)

```
Super Admin:
- Email: admin@csirt.kalselprov.go.id
- Password: password

Agent:
- Email: agent@csirt.kalselprov.go.id
- Password: password

User (untuk registrasi baru):
- Buat akun baru via form registrasi
```

---

## 🧪 Test Cases

### **MODUL 1: AUTHENTICATION & AUTHORIZATION**

#### TC-001: Registrasi Pengguna Baru

**Tujuan**: Memastikan user dapat mendaftar akun baru

**Precondition**:

-   User belum login
-   Database sudah di-setup

**Test Steps**:

1. Buka halaman `/register`
2. Isi form dengan data valid:
    - Nama: "Test User"
    - Email: "testuser@example.com"
    - Password: "password123"
    - Confirm Password: "password123"
3. Klik tombol "Register"
4. Verifikasi redirect ke halaman welcome/home
5. Cek email inbox (jika email verification aktif)

**Expected Result**:

-   ✅ User berhasil terdaftar
-   ✅ Redirect ke halaman home/welcome
-   ✅ Email verifikasi terkirim (jika aktif)
-   ✅ User dapat login dengan kredensial baru

**Test Data**:

```
Nama: Test User
Email: testuser@example.com
Password: password123
```

---

#### TC-002: Login dengan Kredensial Valid

**Tujuan**: Memastikan user dapat login dengan kredensial yang benar

**Test Steps**:

1. Buka halaman `/login`
2. Masukkan email: `agent@csirt.kalselprov.go.id`
3. Masukkan password: `password`
4. Klik tombol "Log in"

**Expected Result**:

-   ✅ Login berhasil
-   ✅ Redirect sesuai role:
    -   Super Admin → bisa akses Admin Panel
    -   Agent → redirect ke Agent Dashboard
    -   User → redirect ke Welcome/Portal

---

#### TC-003: Login dengan Kredensial Invalid

**Tujuan**: Memastikan sistem menolak login dengan kredensial salah

**Test Steps**:

1. Buka halaman `/login`
2. Masukkan email: `wrong@example.com`
3. Masukkan password: `wrongpassword`
4. Klik tombol "Log in"

**Expected Result**:

-   ✅ Login gagal
-   ✅ Error message muncul: "These credentials do not match our records"
-   ✅ User tetap di halaman login

---

#### TC-004: Logout

**Tujuan**: Memastikan user dapat logout dengan benar

**Test Steps**:

1. Login sebagai user (misal: Agent)
2. Klik tombol/logout di menu
3. Konfirmasi logout

**Expected Result**:

-   ✅ Session dihapus
-   ✅ Redirect ke halaman login atau home
-   ✅ User tidak bisa akses halaman yang memerlukan auth

---

#### TC-005: Akses Halaman Terproteksi Tanpa Login

**Tujuan**: Memastikan middleware auth bekerja

**Test Steps**:

1. Pastikan user belum login (clear session/cookies)
2. Akses langsung URL: `/agent` atau `/admin`

**Expected Result**:

-   ✅ Redirect ke halaman login
-   ✅ Flash message muncul (jika ada)

---

### **MODUL 2: PORTAL PELAPOR**

#### TC-006: Membuat Tiket Baru (User Login)

**Tujuan**: Memastikan user yang login dapat membuat tiket baru

**Precondition**:

-   User sudah login
-   Master data sudah di-seed (departemen, topik bantuan, dll)

**Test Steps**:

1. Login sebagai user biasa
2. Buka `/portal/ticket/new`
3. Isi form:
    - Pilih Departemen: "Keamanan Siber"
    - Pilih Topik Bantuan: (pilih dari dropdown)
    - Subjek: "Test Laporan Insiden"
    - Pesan: "Ini adalah test laporan insiden siber"
    - Prioritas: (opsional)
    - Upload lampiran: (opsional, file PDF/Image)
4. Klik tombol "Submit" atau "Kirim Laporan"

**Expected Result**:

-   ✅ Tiket berhasil dibuat
-   ✅ Nomor tiket otomatis ter-generate (format: CSIRT-000001)
-   ✅ Status default: "Terbuka" (open)
-   ✅ Redirect ke halaman detail tiket atau konfirmasi
-   ✅ Email notifikasi terkirim ke pelapor
-   ✅ Email notifikasi terkirim ke admin/agent

**Test Data**:

```
Departemen: Keamanan Siber
Topik: Serangan Malware
Subjek: Test Laporan Insiden Siber
Pesan: Ini adalah test laporan untuk memverifikasi sistem ticketing
Prioritas: Normal
Lampiran: test-file.pdf (opsional)
```

---

#### TC-007: Membuat Tiket dengan Lampiran

**Tujuan**: Memastikan upload lampiran berfungsi

**Test Steps**:

1. Login sebagai user
2. Buka `/portal/ticket/new`
3. Isi form tiket
4. Upload file lampiran:
    - File valid: PDF, JPG, PNG, DOC, DOCX
    - Ukuran maks: 10MB
5. Submit form

**Expected Result**:

-   ✅ File berhasil di-upload
-   ✅ File tersimpan di storage
-   ✅ File muncul di detail tiket
-   ✅ File dapat di-download

**Test Cases untuk Lampiran**:

-   ✅ File PDF (valid)
-   ✅ File Image JPG/PNG (valid)
-   ✅ File DOC/DOCX (valid)
-   ✅ File > 10MB (harus ditolak)
-   ✅ File dengan ekstensi tidak valid (harus ditolak)

---

#### TC-008: Cek Status Tiket (Guest/Non-Login)

**Tujuan**: Memastikan guest dapat cek status tiket tanpa login

**Precondition**:

-   Ada tiket yang sudah dibuat sebelumnya
-   Catat nomor tiket dan email pelapor

**Test Steps**:

1. Buka `/portal/ticket/status` (tanpa login)
2. Masukkan nomor tiket: `CSIRT-000001`
3. Masukkan email pelapor: `testuser@example.com`
4. Klik "Cek Status"

**Expected Result**:

-   ✅ Jika nomor tiket dan email cocok:
    -   Redirect ke halaman detail tiket
    -   Session verification tersimpan
    -   User dapat melihat detail tiket
-   ✅ Jika tidak cocok:
    -   Error message muncul
    -   Tetap di halaman status check

---

#### TC-009: Melihat Detail Tiket (Guest)

**Tujuan**: Memastikan guest dapat melihat detail tiket setelah verifikasi

**Test Steps**:

1. Lakukan TC-008 (cek status) dengan data valid
2. Setelah redirect ke detail tiket, verifikasi informasi yang ditampilkan

**Expected Result**:

-   ✅ Nomor tiket ditampilkan
-   ✅ Subjek ditampilkan
-   ✅ Status ditampilkan
-   ✅ Prioritas ditampilkan
-   ✅ Thread/pesan ditampilkan
-   ✅ Lampiran ditampilkan (jika ada)
-   ✅ Tombol "Balas" tersedia

---

#### TC-010: Balasan dari Pelapor (Guest)

**Tujuan**: Memastikan pelapor dapat membalas tiket

**Precondition**:

-   Sudah verifikasi via TC-008
-   Ada tiket dengan status "Menunggu Pelapor" atau "Terbuka"

**Test Steps**:

1. Setelah verifikasi, buka detail tiket
2. Scroll ke form balasan
3. Isi pesan balasan: "Terima kasih atas responsnya"
4. Upload lampiran (opsional)
5. Klik "Kirim Balasan"

**Expected Result**:

-   ✅ Balasan berhasil dikirim
-   ✅ Status tiket berubah menjadi "Menunggu Pelapor" (answered)
-   ✅ Thread baru muncul di detail tiket
-   ✅ Email notifikasi terkirim ke agent/admin
-   ✅ Lampiran tersimpan (jika ada)

---

#### TC-011: Balasan dari Pelapor (User Login)

**Tujuan**: Memastikan user yang login dapat membalas tiket miliknya

**Test Steps**:

1. Login sebagai user yang memiliki tiket
2. Buka `/portal/ticket/{nomor_tiket}`
3. Isi form balasan
4. Submit

**Expected Result**:

-   ✅ Sama seperti TC-010
-   ✅ Tidak perlu verifikasi email (karena sudah login)

---

### **MODUL 3: AGENT PANEL**

#### TC-012: Akses Agent Dashboard

**Tujuan**: Memastikan agent dapat mengakses dashboard

**Precondition**:

-   Login sebagai Agent atau Super Admin

**Test Steps**:

1. Login sebagai `agent@csirt.kalselprov.go.id`
2. Akses `/agent` atau `/agent/dashboard`

**Expected Result**:

-   ✅ Dashboard terbuka
-   ✅ Statistik tiket ditampilkan:
    -   Total tiket
    -   Tiket terbuka
    -   Tiket ditugaskan ke saya
    -   Tiket overdue
-   ✅ Chart/grafik ditampilkan (jika ada)

---

#### TC-013: Melihat Daftar Tiket (Agent)

**Tujuan**: Memastikan agent dapat melihat daftar semua tiket

**Test Steps**:

1. Login sebagai Agent
2. Buka `/agent/tickets`
3. Verifikasi daftar tiket yang ditampilkan

**Expected Result**:

-   ✅ Daftar tiket ditampilkan dalam tabel
-   ✅ Kolom yang ditampilkan:
    -   Nomor Tiket
    -   Subjek
    -   Pelapor
    -   Departemen
    -   Status
    -   Prioritas
    -   Tanggal Dibuat
-   ✅ Filter/Search berfungsi (jika ada)
-   ✅ Pagination berfungsi (jika ada)

---

#### TC-014: Filter Tiket (Agent)

**Tujuan**: Memastikan filter tiket berfungsi

**Test Steps**:

1. Login sebagai Agent
2. Buka `/agent/tickets`
3. Gunakan filter:
    - Filter by Status: "Terbuka"
    - Filter by Departemen: "Keamanan Siber"
    - Filter by Assigned: "Ditugaskan ke Saya"
    - Search: "CSIRT-000001"

**Expected Result**:

-   ✅ Filter bekerja dengan benar
-   ✅ Hasil sesuai dengan filter yang dipilih
-   ✅ Search menemukan tiket yang sesuai

---

#### TC-015: Melihat Detail Tiket (Agent)

**Tujuan**: Memastikan agent dapat melihat detail lengkap tiket

**Test Steps**:

1. Login sebagai Agent
2. Buka `/agent/tickets`
3. Klik salah satu tiket untuk melihat detail
4. Verifikasi informasi yang ditampilkan

**Expected Result**:

-   ✅ Detail tiket lengkap ditampilkan:
    -   Nomor tiket, UUID
    -   Subjek, Deskripsi
    -   Pelapor (nama, email)
    -   Departemen, Topik Bantuan
    -   Status, Prioritas
    -   SLA, Due Date
    -   Assigned To (jika ada)
-   ✅ Thread/pesan ditampilkan dengan benar
-   ✅ Lampiran dapat di-download
-   ✅ Form balasan tersedia
-   ✅ Tombol ubah status tersedia
-   ✅ Tombol assign tersedia (jika punya permission)

---

#### TC-016: Membalas Tiket (Agent)

**Tujuan**: Memastikan agent dapat membalas tiket

**Precondition**:

-   Ada tiket dengan status "Terbuka" atau "Menunggu Pelapor"

**Test Steps**:

1. Login sebagai Agent
2. Buka detail tiket
3. Scroll ke form balasan
4. Isi pesan: "Terima kasih telah melaporkan. Kami sedang menangani laporan Anda."
5. Upload lampiran (opsional)
6. Klik "Kirim Balasan"

**Expected Result**:

-   ✅ Balasan berhasil dikirim
-   ✅ Status tiket berubah menjadi "Terbuka" (open)
-   ✅ Thread baru muncul
-   ✅ Email notifikasi terkirim ke pelapor
-   ✅ Lampiran tersimpan (jika ada)

---

#### TC-017: Mengubah Status Tiket (Agent)

**Tujuan**: Memastikan agent dapat mengubah status tiket

**Test Steps**:

1. Login sebagai Agent
2. Buka detail tiket
3. Klik tombol "Ubah Status" atau dropdown status
4. Pilih status baru: "Dalam Proses" (in_progress)
5. Submit

**Expected Result**:

-   ✅ Status berhasil diubah
-   ✅ Jika status = "Tertutup" (closed):
    -   Field `ditutup_pada` terisi
    -   Email notifikasi terkirim ke pelapor
-   ✅ Jika status = "Ditugaskan" (assigned) atau "Dalam Proses" (in_progress):
    -   Email notifikasi terkirim ke pelapor
-   ✅ Status baru ditampilkan di detail tiket

**Test Cases untuk Status**:

-   ✅ Terbuka → Menunggu Pelapor
-   ✅ Terbuka → Ditugaskan
-   ✅ Terbuka → Dalam Proses
-   ✅ Dalam Proses → Tertutup
-   ✅ Tertutup → (tidak bisa diubah lagi, atau bisa dibuka kembali?)

---

#### TC-018: Assign Tiket ke Agent (Agent dengan Permission)

**Tujuan**: Memastikan agent dengan permission dapat assign tiket

**Precondition**:

-   Login sebagai Agent dengan permission `tickets.assign`
-   Ada tiket yang belum di-assign

**Test Steps**:

1. Login sebagai Agent (Super Admin atau Agent dengan permission)
2. Buka detail tiket
3. Klik tombol "Assign" atau dropdown "Assign To"
4. Pilih agent dari dropdown
5. Submit

**Expected Result**:

-   ✅ Tiket berhasil di-assign ke agent
-   ✅ Status otomatis berubah menjadi "Ditugaskan" (assigned)
-   ✅ Email notifikasi terkirim ke agent yang di-assign
-   ✅ Field `ditugaskan_ke` terisi dengan ID agent

---

#### TC-019: Membuat Catatan Internal (Agent)

**Tujuan**: Memastikan agent dapat membuat catatan internal

**Test Steps**:

1. Login sebagai Agent
2. Buka detail tiket
3. Klik "Tambah Catatan" atau "Internal Note"
4. Isi catatan: "Catatan internal untuk tim"
5. Submit

**Expected Result**:

-   ✅ Catatan berhasil dibuat
-   ✅ Catatan hanya terlihat oleh agent/admin (tipe: "catatan")
-   ✅ Catatan tidak terlihat oleh pelapor
-   ✅ Catatan muncul di thread dengan indikator "Internal"

---

#### TC-020: Akses Agent Panel Tanpa Permission

**Tujuan**: Memastikan user tanpa permission tidak bisa akses agent panel

**Test Steps**:

1. Login sebagai user biasa (tanpa role Agent/Admin)
2. Coba akses `/agent` atau `/agent/tickets`

**Expected Result**:

-   ✅ Redirect atau error 403 Forbidden
-   ✅ Flash message muncul (jika ada)

---

### **MODUL 4: ADMIN PANEL**

#### TC-021: Akses Admin Panel (Super Admin)

**Tujuan**: Memastikan Super Admin dapat akses admin panel

**Test Steps**:

1. Login sebagai Super Admin (`admin@csirt.kalselprov.go.id`)
2. Akses `/admin`

**Expected Result**:

-   ✅ Redirect ke `/admin/users` (default)
-   ✅ Menu Admin Panel tersedia di navigasi
-   ✅ Semua menu CRUD tersedia:
    -   Users
    -   Departments
    -   Help Topics
    -   SLA Plans
    -   Priorities
    -   Statuses
    -   Teams
    -   Canned Responses
    -   Organizations

---

#### TC-022: Akses Admin Panel (Non-Super Admin)

**Tujuan**: Memastikan user selain Super Admin tidak bisa akses

**Test Steps**:

1. Login sebagai Agent atau User biasa
2. Coba akses `/admin` atau `/admin/users`

**Expected Result**:

-   ✅ Redirect atau error 403 Forbidden
-   ✅ Menu "Admin Panel" tidak muncul di navigasi

---

#### TC-023: CRUD Users (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola users

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/users`
3. Klik "Tambah User" atau "Create"
4. Isi form:
    - Nama: "New Agent"
    - Email: "newagent@example.com"
    - Password: "password123"
    - Phone: "081234567890"
    - Role: Pilih "Agent"
5. Submit

**Expected Result**:

-   ✅ User berhasil dibuat
-   ✅ Role berhasil di-assign
-   ✅ Redirect ke list users
-   ✅ User baru muncul di list

**READ**:

1. Buka `/admin/users`
2. Verifikasi list users ditampilkan
3. Klik salah satu user untuk melihat detail (jika ada)

**Expected Result**:

-   ✅ List users ditampilkan dengan pagination
-   ✅ Search berfungsi (jika ada)
-   ✅ Detail user ditampilkan (jika ada halaman detail)

**UPDATE**:

1. Buka `/admin/users`
2. Klik "Edit" pada salah satu user
3. Ubah nama: "Updated Name"
4. Submit

**Expected Result**:

-   ✅ User berhasil di-update
-   ✅ Perubahan tersimpan
-   ✅ Redirect ke list users

**DELETE**:

1. Buka `/admin/users`
2. Klik "Delete" pada salah satu user
3. Konfirmasi penghapusan

**Expected Result**:

-   ✅ User berhasil dihapus
-   ✅ User tidak muncul lagi di list
-   ✅ Relasi dengan tiket tetap aman (soft delete atau cascade sesuai kebutuhan)

---

#### TC-024: CRUD Departments (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola departments

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/departments`
3. Klik "Tambah Departemen"
4. Isi form:
    - Nama: "Departemen Test"
    - Email: "dept@example.com"
    - Publik: Centang (true)
5. Submit

**Expected Result**:

-   ✅ Departemen berhasil dibuat
-   ✅ Muncul di list
-   ✅ Dapat dipilih saat membuat tiket (jika publik = true)

**READ/UPDATE/DELETE**: (sama seperti TC-023)

---

#### TC-025: CRUD Help Topics (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola help topics

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/help-topics`
3. Klik "Tambah Topik Bantuan"
4. Isi form:
    - Nama: "Topik Test"
    - Departemen: Pilih dari dropdown
    - Form Schema: (opsional, JSON)
5. Submit

**Expected Result**:

-   ✅ Topik bantuan berhasil dibuat
-   ✅ Muncul di list
-   ✅ Dapat dipilih saat membuat tiket di portal

---

#### TC-026: CRUD SLA Plans (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola SLA plans

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/sla`
3. Klik "Tambah SLA Plan"
4. Isi form:
    - Nama: "SLA Test (24 Jam)"
    - Jam Grace: 24
5. Submit

**Expected Result**:

-   ✅ SLA Plan berhasil dibuat
-   ✅ Dapat dipilih saat membuat tiket
-   ✅ Due date otomatis dihitung berdasarkan jam grace

---

#### TC-027: CRUD Priorities (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola priorities

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/priorities`
3. Klik "Tambah Prioritas"
4. Isi form:
    - Nama: "Sangat Tinggi"
    - Bobot: 10
5. Submit

**Expected Result**:

-   ✅ Prioritas berhasil dibuat
-   ✅ Dapat dipilih saat membuat tiket
-   ✅ Bobot digunakan untuk sorting/filtering

---

#### TC-028: CRUD Statuses (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola statuses

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/statuses`
3. Klik "Tambah Status"
4. Isi form:
    - Nama: "Status Test"
    - Slug: "test-status"
    - Menutup: Centang (true) jika status closing
5. Submit

**Expected Result**:

-   ✅ Status berhasil dibuat
-   ✅ Dapat dipilih saat mengubah status tiket
-   ✅ Jika "Menutup" = true, tiket akan otomatis terisi `ditutup_pada`

---

#### TC-029: CRUD Teams (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola teams

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/teams`
3. Klik "Tambah Tim"
4. Isi form:
    - Nama: "Tim Test"
5. Submit

**Expected Result**:

-   ✅ Tim berhasil dibuat
-   ✅ Dapat di-assign ke users (via pivot table)

---

#### TC-030: CRUD Canned Responses (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola canned responses

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/canned`
3. Klik "Tambah Template"
4. Isi form:
    - Judul: "Template Test"
    - Isi: "Ini adalah template respons standar"
5. Submit

**Expected Result**:

-   ✅ Template berhasil dibuat
-   ✅ Dapat digunakan oleh agent saat membalas tiket (jika fitur ini ada)

---

#### TC-031: CRUD Organizations (Admin)

**Tujuan**: Memastikan Super Admin dapat mengelola organizations

**Test Steps**:

**CREATE**:

1. Login sebagai Super Admin
2. Buka `/admin/organizations`
3. Klik "Tambah Organisasi"
4. Isi form:
    - Nama: "Organisasi Test"
5. Submit

**Expected Result**:

-   ✅ Organisasi berhasil dibuat
-   ✅ Dapat di-assign ke users

---

### **MODUL 5: VALIDASI & ERROR HANDLING**

#### TC-032: Validasi Form Tiket (Portal)

**Tujuan**: Memastikan validasi form bekerja dengan benar

**Test Steps**:

1. Login sebagai user
2. Buka `/portal/ticket/new`
3. Submit form tanpa mengisi field wajib:
    - Kosongkan subjek
    - Kosongkan pesan
    - Tidak pilih departemen
4. Submit form

**Expected Result**:

-   ✅ Error validation muncul:
    -   "Subjek wajib diisi"
    -   "Pesan wajib diisi"
    -   "Departemen wajib dipilih"
-   ✅ Form tidak submit
-   ✅ Field yang error ditandai (red border atau error message)

---

#### TC-033: Validasi Upload File

**Tujuan**: Memastikan validasi file upload bekerja

**Test Steps**:

1. Login sebagai user
2. Buka form tiket
3. Coba upload file dengan:
    - Ukuran > 10MB
    - Ekstensi tidak valid (.exe, .bat, dll)
4. Submit

**Expected Result**:

-   ✅ Error message muncul:
    -   "File terlalu besar (maks 10MB)"
    -   "Tipe file tidak diizinkan"
-   ✅ File tidak ter-upload

---

#### TC-034: Validasi Email

**Tujuan**: Memastikan validasi email bekerja

**Test Steps**:

1. Buka form registrasi atau form tiket
2. Masukkan email tidak valid: "invalid-email"
3. Submit

**Expected Result**:

-   ✅ Error message: "Format email tidak valid"
-   ✅ Form tidak submit

---

#### TC-035: Handling Error 404

**Tujuan**: Memastikan error 404 ditangani dengan baik

**Test Steps**:

1. Akses URL yang tidak ada: `/portal/ticket/INVALID-12345`
2. Atau `/agent/tickets/99999`

**Expected Result**:

-   ✅ Halaman 404 ditampilkan
-   ✅ Pesan error user-friendly
-   ✅ Link kembali ke home tersedia

---

#### TC-036: Handling Error 403

**Tujuan**: Memastikan error 403 ditangani dengan baik

**Test Steps**:

1. Login sebagai user biasa
2. Coba akses `/admin/users`

**Expected Result**:

-   ✅ Error 403 ditampilkan
-   ✅ Pesan: "Anda tidak memiliki akses ke halaman ini"
-   ✅ Link kembali tersedia

---

### **MODUL 6: NOTIFIKASI EMAIL**

#### TC-037: Notifikasi Tiket Baru ke Pelapor

**Tujuan**: Memastikan email notifikasi terkirim saat tiket dibuat

**Test Steps**:

1. Buat tiket baru via portal (TC-006)
2. Cek email inbox pelapor

**Expected Result**:

-   ✅ Email terkirim ke email pelapor
-   ✅ Subject: "Tiket Baru: [Nomor Tiket]"
-   ✅ Isi email berisi:
    -   Nomor tiket
    -   Subjek
    -   Link ke detail tiket
    -   Informasi tiket lengkap

---

#### TC-038: Notifikasi Balasan Agent ke Pelapor

**Tujuan**: Memastikan email notifikasi terkirim saat agent membalas

**Test Steps**:

1. Agent membalas tiket (TC-016)
2. Cek email inbox pelapor

**Expected Result**:

-   ✅ Email terkirim ke email pelapor
-   ✅ Subject: "Balasan untuk Tiket: [Nomor Tiket]"
-   ✅ Isi email berisi:
    -   Balasan dari agent
    -   Link ke detail tiket

---

#### TC-039: Notifikasi Perubahan Status ke Pelapor

**Tujuan**: Memastikan email notifikasi terkirim saat status berubah

**Test Steps**:

1. Agent mengubah status tiket menjadi "Ditugaskan" atau "Dalam Proses" atau "Tertutup" (TC-017)
2. Cek email inbox pelapor

**Expected Result**:

-   ✅ Email terkirim (hanya untuk status: assigned, in_progress, closed)
-   ✅ Subject: "Status Tiket Diubah: [Nomor Tiket]"
-   ✅ Isi email berisi:
    -   Status baru
    -   Informasi tiket

---

#### TC-040: Notifikasi Assignment ke Agent

**Tujuan**: Memastikan email notifikasi terkirim saat tiket di-assign

**Test Steps**:

1. Assign tiket ke agent (TC-018)
2. Cek email inbox agent yang di-assign

**Expected Result**:

-   ✅ Email terkirim ke agent
-   ✅ Subject: "Tiket Ditugaskan: [Nomor Tiket]"
-   ✅ Isi email berisi:
    -   Informasi tiket
    -   Link ke detail tiket

---

#### TC-041: Notifikasi Balasan Pelapor ke Agent

**Tujuan**: Memastikan email notifikasi terkirim saat pelapor membalas

**Test Steps**:

1. Pelapor membalas tiket (TC-010)
2. Cek email inbox agent/admin

**Expected Result**:

-   ✅ Email terkirim ke agent/admin
-   ✅ Subject: "Balasan dari Pelapor: [Nomor Tiket]"
-   ✅ Isi email berisi:
    -   Balasan dari pelapor
    -   Link ke detail tiket

---

### **MODUL 7: UI/UX & RESPONSIVENESS**

#### TC-042: Responsive Design (Mobile)

**Tujuan**: Memastikan aplikasi responsive di mobile

**Test Steps**:

1. Buka aplikasi di browser mobile atau resize browser ke ukuran mobile
2. Test navigasi di berbagai halaman:
    - Home
    - Portal
    - Agent Dashboard
    - Admin Panel

**Expected Result**:

-   ✅ Layout menyesuaikan ukuran layar
-   ✅ Menu navigasi dapat diakses (hamburger menu)
-   ✅ Form dapat diisi dengan mudah
-   ✅ Tabel dapat di-scroll horizontal (jika perlu)

---

#### TC-043: Loading State & Feedback

**Tujuan**: Memastikan loading state dan feedback user jelas

**Test Steps**:

1. Submit form tiket
2. Submit form balasan
3. Upload file

**Expected Result**:

-   ✅ Loading indicator muncul saat proses
-   ✅ Success message muncul setelah berhasil
-   ✅ Error message jelas dan informatif

---

#### TC-044: Navigation & Breadcrumb

**Tujuan**: Memastikan navigasi mudah digunakan

**Test Steps**:

1. Navigasi ke berbagai halaman
2. Verifikasi menu navigasi
3. Verifikasi breadcrumb (jika ada)

**Expected Result**:

-   ✅ Menu navigasi jelas dan mudah diakses
-   ✅ Breadcrumb menunjukkan posisi user
-   ✅ Link kembali berfungsi

---

### **MODUL 8: INTEGRASI & ALUR BISNIS**

#### TC-045: Alur Lengkap: Pelapor → Agent → Selesai

**Tujuan**: Memastikan alur bisnis lengkap berjalan dengan benar

**Test Steps**:

1. **Pelapor membuat tiket** (TC-006)
    - Verifikasi: Tiket dibuat, status = "Terbuka"
2. **Agent melihat tiket baru** (TC-013)
    - Verifikasi: Tiket muncul di list agent
3. **Agent membalas tiket** (TC-016)
    - Verifikasi: Status berubah menjadi "Terbuka", email terkirim
4. **Pelapor membalas** (TC-010)
    - Verifikasi: Status berubah menjadi "Menunggu Pelapor"
5. **Agent mengubah status menjadi "Dalam Proses"** (TC-017)
    - Verifikasi: Status berubah, email terkirim
6. **Agent menutup tiket** (TC-017, status = "Tertutup")
    - Verifikasi: Status = "Tertutup", `ditutup_pada` terisi, email terkirim

**Expected Result**:

-   ✅ Semua langkah berjalan dengan benar
-   ✅ Status berubah sesuai alur
-   ✅ Notifikasi email terkirim di setiap langkah penting
-   ✅ Data tersimpan dengan benar di database

---

#### TC-046: Alur dengan Assignment

**Tujuan**: Memastikan alur dengan assignment berjalan dengan benar

**Test Steps**:

1. Pelapor membuat tiket
2. Agent A melihat tiket
3. Agent A assign tiket ke Agent B (TC-018)
4. Agent B menerima notifikasi
5. Agent B membalas tiket
6. Agent B menutup tiket

**Expected Result**:

-   ✅ Assignment berhasil
-   ✅ Status otomatis menjadi "Ditugaskan"
-   ✅ Agent B menerima notifikasi
-   ✅ Agent B dapat mengakses tiket
-   ✅ Alur selanjutnya berjalan normal

---

#### TC-047: Multiple Thread/Conversation

**Tujuan**: Memastikan multiple thread dalam satu tiket berjalan dengan baik

**Test Steps**:

1. Buat tiket
2. Agent membalas (thread 1)
3. Pelapor membalas (thread 2)
4. Agent membalas lagi (thread 3)
5. Verifikasi semua thread ditampilkan dengan benar

**Expected Result**:

-   ✅ Semua thread ditampilkan dalam urutan kronologis
-   ✅ Thread dapat dibedakan (pesan/balasan/catatan)
-   ✅ Lampiran di setiap thread dapat diakses

---

### **MODUL 9: KEAMANAN**

#### TC-048: SQL Injection Prevention

**Tujuan**: Memastikan aplikasi aman dari SQL injection

**Test Steps**:

1. Di form search atau filter, masukkan input:
    - `' OR '1'='1`
    - `'; DROP TABLE users; --`
2. Submit form

**Expected Result**:

-   ✅ Input di-escape dengan benar
-   ✅ Tidak ada error SQL
-   ✅ Query aman (menggunakan parameter binding)

---

#### TC-049: XSS Prevention

**Tujuan**: Memastikan aplikasi aman dari XSS

**Test Steps**:

1. Di form tiket atau balasan, masukkan script:
    - `<script>alert('XSS')</script>`
    - `<img src=x onerror=alert('XSS')>`
2. Submit form

**Expected Result**:

-   ✅ Script tidak dieksekusi
-   ✅ Input di-escape/di-sanitize
-   ✅ Output aman (Blade auto-escape)

---

#### TC-050: CSRF Protection

**Tujuan**: Memastikan CSRF protection aktif

**Test Steps**:

1. Buka form tiket
2. Inspect element, hapus CSRF token
3. Submit form via POST request tanpa token

**Expected Result**:

-   ✅ Error 419 atau CSRF error
-   ✅ Form tidak submit
-   ✅ Pesan error jelas

---

#### TC-051: Authorization Check

**Tujuan**: Memastikan authorization bekerja di semua endpoint

**Test Steps**:

1. Login sebagai user biasa
2. Coba akses langsung URL agent/admin via browser atau tool:
    - `/agent/tickets`
    - `/admin/users`
    - `/agent/tickets/{id}/assign`

**Expected Result**:

-   ✅ Redirect ke login atau error 403
-   ✅ Tidak ada data sensitif yang ter-expose

---

### **MODUL 10: PERFORMANCE & EDGE CASES**

#### TC-052: Pagination (Jika Ada)

**Tujuan**: Memastikan pagination bekerja dengan benar

**Test Steps**:

1. Buat banyak tiket (atau gunakan seeder)
2. Buka halaman list tiket
3. Navigasi ke halaman berikutnya

**Expected Result**:

-   ✅ Pagination berfungsi
-   ✅ Data ditampilkan dengan benar
-   ✅ URL parameter page bekerja

---

#### TC-053: Search Functionality

**Tujuan**: Memastikan search bekerja dengan benar

**Test Steps**:

1. Buka halaman list tiket
2. Masukkan keyword di search box:
    - Nomor tiket: "CSIRT-000001"
    - Subjek: "Test"
    - Email: "test@example.com"
3. Submit search

**Expected Result**:

-   ✅ Hasil search sesuai
-   ✅ Search case-insensitive (jika diimplementasikan)
-   ✅ Search di multiple field (jika diimplementasikan)

---

#### TC-054: Concurrent Access

**Tujuan**: Memastikan aplikasi dapat handle concurrent access

**Test Steps**:

1. Buka aplikasi di 2 browser berbeda
2. Login dengan user berbeda
3. Lakukan operasi bersamaan:
    - User 1: Buat tiket
    - User 2: Lihat list tiket

**Expected Result**:

-   ✅ Tidak ada conflict
-   ✅ Data konsisten
-   ✅ Session terpisah

---

#### TC-055: Large File Upload

**Tujuan**: Memastikan handling file besar

**Test Steps**:

1. Coba upload file mendekati batas (9.9MB)
2. Coba upload file tepat di batas (10MB)
3. Coba upload file melebihi batas (10.1MB)

**Expected Result**:

-   ✅ File 9.9MB: Berhasil
-   ✅ File 10MB: Berhasil (jika batas inklusif) atau ditolak
-   ✅ File 10.1MB: Ditolak dengan error message

---

## 📊 Test Execution Checklist

Gunakan checklist ini untuk menandai test case yang sudah dilakukan:

### Authentication & Authorization

-   [ ] TC-001: Registrasi Pengguna Baru
-   [ ] TC-002: Login dengan Kredensial Valid
-   [ ] TC-003: Login dengan Kredensial Invalid
-   [ ] TC-004: Logout
-   [ ] TC-005: Akses Halaman Terproteksi Tanpa Login

### Portal Pelapor

-   [ ] TC-006: Membuat Tiket Baru (User Login)
-   [ ] TC-007: Membuat Tiket dengan Lampiran
-   [ ] TC-008: Cek Status Tiket (Guest/Non-Login)
-   [ ] TC-009: Melihat Detail Tiket (Guest)
-   [ ] TC-010: Balasan dari Pelapor (Guest)
-   [ ] TC-011: Balasan dari Pelapor (User Login)

### Agent Panel

-   [ ] TC-012: Akses Agent Dashboard
-   [ ] TC-013: Melihat Daftar Tiket (Agent)
-   [ ] TC-014: Filter Tiket (Agent)
-   [ ] TC-015: Melihat Detail Tiket (Agent)
-   [ ] TC-016: Membalas Tiket (Agent)
-   [ ] TC-017: Mengubah Status Tiket (Agent)
-   [ ] TC-018: Assign Tiket ke Agent
-   [ ] TC-019: Membuat Catatan Internal (Agent)
-   [ ] TC-020: Akses Agent Panel Tanpa Permission

### Admin Panel

-   [ ] TC-021: Akses Admin Panel (Super Admin)
-   [ ] TC-022: Akses Admin Panel (Non-Super Admin)
-   [ ] TC-023: CRUD Users (Admin)
-   [ ] TC-024: CRUD Departments (Admin)
-   [ ] TC-025: CRUD Help Topics (Admin)
-   [ ] TC-026: CRUD SLA Plans (Admin)
-   [ ] TC-027: CRUD Priorities (Admin)
-   [ ] TC-028: CRUD Statuses (Admin)
-   [ ] TC-029: CRUD Teams (Admin)
-   [ ] TC-030: CRUD Canned Responses (Admin)
-   [ ] TC-031: CRUD Organizations (Admin)

### Validasi & Error Handling

-   [ ] TC-032: Validasi Form Tiket (Portal)
-   [ ] TC-033: Validasi Upload File
-   [ ] TC-034: Validasi Email
-   [ ] TC-035: Handling Error 404
-   [ ] TC-036: Handling Error 403

### Notifikasi Email

-   [ ] TC-037: Notifikasi Tiket Baru ke Pelapor
-   [ ] TC-038: Notifikasi Balasan Agent ke Pelapor
-   [ ] TC-039: Notifikasi Perubahan Status ke Pelapor
-   [ ] TC-040: Notifikasi Assignment ke Agent
-   [ ] TC-041: Notifikasi Balasan Pelapor ke Agent

### UI/UX & Responsiveness

-   [ ] TC-042: Responsive Design (Mobile)
-   [ ] TC-043: Loading State & Feedback
-   [ ] TC-044: Navigation & Breadcrumb

### Integrasi & Alur Bisnis

-   [ ] TC-045: Alur Lengkap: Pelapor → Agent → Selesai
-   [ ] TC-046: Alur dengan Assignment
-   [ ] TC-047: Multiple Thread/Conversation

### Keamanan

-   [ ] TC-048: SQL Injection Prevention
-   [ ] TC-049: XSS Prevention
-   [ ] TC-050: CSRF Protection
-   [ ] TC-051: Authorization Check

### Performance & Edge Cases

-   [ ] TC-052: Pagination (Jika Ada)
-   [ ] TC-053: Search Functionality
-   [ ] TC-054: Concurrent Access
-   [ ] TC-055: Large File Upload

---

## 📝 Test Report Template

Setelah melakukan testing, dokumentasikan hasilnya:

### Test Report

**Tanggal Testing**: **\*\***\_\_\_**\*\***
**Tester**: **\*\***\_\_\_**\*\***
**Environment**: **\*\***\_\_\_**\*\***

#### Summary

-   Total Test Cases: 55
-   Passed: \_\_\_
-   Failed: \_\_\_
-   Blocked: \_\_\_
-   Pass Rate: \_\_\_%

#### Failed Test Cases

| TC ID | Test Case | Error Description | Severity | Status |
| ----- | --------- | ----------------- | -------- | ------ |
|       |           |                   |          |        |

#### Blocked Test Cases

| TC ID | Test Case | Reason |
| ----- | --------- | ------ |
|       |           |        |

#### Notes

-
-
-   ***

## 🚀 Quick Start Testing

### Langkah Cepat untuk Memulai Testing:

1. **Setup Environment**

    ```bash
    php artisan migrate:fresh --seed
    php artisan storage:link
    php artisan serve
    ```

2. **Test Accounts**

    - Super Admin: `admin@csirt.kalselprov.go.id` / `password`
    - Agent: `agent@csirt.kalselprov.go.id` / `password`

3. **Prioritas Testing** (Recommended Order):

    - ✅ Authentication (TC-001 s/d TC-005)
    - ✅ Portal Pelapor (TC-006 s/d TC-011)
    - ✅ Agent Panel (TC-012 s/d TC-020)
    - ✅ Admin Panel (TC-021 s/d TC-031)
    - ✅ Alur Lengkap (TC-045 s/d TC-047)
    - ✅ Notifikasi (TC-037 s/d TC-041)
    - ✅ Validasi & Security (TC-032 s/d TC-051)

4. **Tools yang Bisa Digunakan**:
    - Browser DevTools (untuk inspect network, console)
    - Email testing: Mailtrap, MailHog, atau Gmail test account
    - Postman/Insomnia (untuk test API jika ada)
    - Browser Extension untuk test responsive

---

## 📌 Tips Testing

1. **Gunakan Browser Incognito/Private Mode** untuk test session yang bersih
2. **Clear Cache & Cookies** sebelum test baru
3. **Gunakan Multiple Browser** untuk test compatibility
4. **Test dengan Data Real** (jangan hanya test data dummy)
5. **Document Screenshots** untuk bug yang ditemukan
6. **Test di Different Screen Sizes** (mobile, tablet, desktop)
7. **Monitor Network Tab** untuk melihat request/response
8. **Check Database** langsung untuk verifikasi data tersimpan dengan benar

---

**Selamat Testing! 🎉**

Jika menemukan bug atau issue, dokumentasikan dengan detail:

-   Steps to reproduce
-   Expected vs Actual result
-   Screenshot (jika ada)
-   Environment details
-   Error message/log (jika ada)
