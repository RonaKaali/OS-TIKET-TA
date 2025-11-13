# 🎬 Detailed Test Scenarios - Step by Step Guide

File ini berisi panduan step-by-step yang sangat detail untuk testing alur-alur penting dalam aplikasi.

---

## 🎯 SCENARIO 1: Complete Ticket Lifecycle (Happy Path)

### Objective

Test alur lengkap dari pembuatan tiket hingga penutupan tiket.

### Precondition

-   Database sudah di-seed
-   Server berjalan
-   Email configuration sudah benar

### Step-by-Step

#### Step 1: User Registration

1. Buka browser (gunakan Incognito/Private mode)
2. Navigate ke: `http://127.0.0.1:8000/register`
3. **Verify**: Form registrasi muncul
4. Isi form:
    ```
    Nama: Test User 001
    Email: testuser001@example.com
    Password: password123
    Confirm Password: password123
    ```
5. Klik tombol "Register"
6. **Verify**:
    - ✅ Redirect ke halaman home/welcome
    - ✅ Tidak ada error message
    - ✅ User sudah login (cek di navbar ada nama user)

#### Step 2: Create New Ticket

1. Klik menu "Buat Laporan" atau navigate ke: `/portal/ticket/new`
2. **Verify**: Form pembuatan tiket muncul
3. Isi form:
    ```
    Departemen: Pilih "Keamanan Siber" dari dropdown
    Topik Bantuan: Pilih topik yang tersedia (misal: "Serangan Malware")
    Subjek: "Test Laporan Insiden Siber - [TIMESTAMP]"
    Pesan: "Saya menemukan aktivitas mencurigakan di sistem.
            Terdapat file aneh yang muncul di server.
            Mohon segera ditangani."
    Prioritas: Pilih "Normal" (opsional)
    ```
4. Upload file lampiran (opsional):
    - Klik "Choose File" atau drag & drop
    - Pilih file: `test-document.pdf` (pastikan file < 10MB)
    - **Verify**: Nama file muncul di form
5. Klik tombol "Kirim Laporan" atau "Submit"
6. **Verify**:
    - ✅ Redirect ke halaman detail tiket atau konfirmasi
    - ✅ Nomor tiket ter-generate (format: CSIRT-000XXX)
    - ✅ Status tiket: "Terbuka" (Open)
    - ✅ Semua informasi tiket ditampilkan dengan benar
    - ✅ Lampiran muncul (jika di-upload)
7. **Catat**: Nomor tiket yang ter-generate: `CSIRT-000XXX`

#### Step 3: Check Email Notification (User)

1. Buka email inbox: `testuser001@example.com`
2. **Verify**:
    - ✅ Email notifikasi terkirim
    - ✅ Subject: "Tiket Baru: CSIRT-000XXX" (atau sesuai format)
    - ✅ Isi email berisi:
        - Nomor tiket
        - Subjek tiket
        - Link ke detail tiket
        - Informasi lengkap tiket
3. Klik link di email (jika ada)
4. **Verify**: Redirect ke halaman detail tiket

#### Step 4: Agent Login & View Ticket

1. Buka browser baru (atau logout dari user sebelumnya)
2. Navigate ke: `http://127.0.0.1:8000/login`
3. Login sebagai Agent:
    ```
    Email: agent@csirt.kalselprov.go.id
    Password: password
    ```
4. **Verify**: Redirect ke Agent Dashboard (`/agent`)
5. **Verify Dashboard**:
    - ✅ Statistik tiket ditampilkan:
        - Total tiket
        - Tiket terbuka
        - Tiket ditugaskan ke saya
        - Tiket overdue
    - ✅ Chart/grafik ditampilkan (jika ada)
6. Klik menu "Daftar Tiket" atau navigate ke: `/agent/tickets`
7. **Verify**:
    - ✅ Daftar tiket ditampilkan dalam tabel
    - ✅ Tiket yang baru dibuat muncul di list
    - ✅ Kolom yang ditampilkan:
        - Nomor Tiket
        - Subjek
        - Pelapor
        - Departemen
        - Status
        - Prioritas
        - Tanggal Dibuat
8. Cari tiket yang baru dibuat (gunakan search atau scroll)
9. Klik nomor tiket atau subjek untuk melihat detail

#### Step 5: Agent View Ticket Detail

1. **Verify Detail Page**:
    - ✅ Nomor tiket: `CSIRT-000XXX`
    - ✅ Subjek: Sesuai yang di-input
    - ✅ Pelapor: Nama dan email user
    - ✅ Departemen: "Keamanan Siber"
    - ✅ Topik Bantuan: Sesuai yang dipilih
    - ✅ Status: "Terbuka"
    - ✅ Prioritas: Sesuai yang dipilih
    - ✅ Thread/pesan awal ditampilkan
    - ✅ Lampiran dapat di-download (jika ada)
    - ✅ Form balasan tersedia
    - ✅ Tombol "Ubah Status" tersedia
    - ✅ Tombol "Assign" tersedia (jika punya permission)

#### Step 6: Agent Reply to Ticket

1. Scroll ke form balasan
2. Isi form balasan:
    ```
    Pesan: "Terima kasih telah melaporkan insiden.
            Kami telah menerima laporan Anda dan sedang menangani.
            Kami akan memberikan update segera setelah ada perkembangan."
    ```
3. Upload lampiran (opsional):
    - Upload file: `response-document.pdf`
4. Klik tombol "Kirim Balasan" atau "Reply"
5. **Verify**:
    - ✅ Balasan berhasil dikirim
    - ✅ Thread baru muncul di detail tiket
    - ✅ Status tiket berubah menjadi "Terbuka" (open)
    - ✅ Lampiran muncul di thread (jika di-upload)
    - ✅ Timestamp balasan ditampilkan dengan benar

#### Step 7: Check Email Notification (User - Agent Reply)

1. Buka email inbox: `testuser001@example.com`
2. **Verify**:
    - ✅ Email notifikasi terkirim
    - ✅ Subject: "Balasan untuk Tiket: CSIRT-000XXX"
    - ✅ Isi email berisi:
        - Balasan dari agent
        - Link ke detail tiket
3. Klik link di email
4. **Verify**: Redirect ke halaman detail tiket (mungkin perlu verifikasi email lagi jika guest)

#### Step 8: User Reply to Agent

1. Login sebagai user: `testuser001@example.com`
2. Navigate ke detail tiket: `/portal/ticket/CSIRT-000XXX`
3. Scroll ke form balasan
4. Isi form:
    ```
    Pesan: "Terima kasih atas responsnya.
            Saya akan menunggu update selanjutnya."
    ```
5. Klik "Kirim Balasan"
6. **Verify**:
    - ✅ Balasan berhasil dikirim
    - ✅ Status tiket berubah menjadi "Menunggu Pelapor" (answered)
    - ✅ Thread baru muncul

#### Step 9: Agent Change Status to "In Progress"

1. Login sebagai Agent
2. Buka detail tiket: `/agent/tickets/CSIRT-000XXX`
3. Klik tombol "Ubah Status" atau dropdown status
4. Pilih status: "Dalam Proses" (In Progress)
5. Klik "Simpan" atau "Update"
6. **Verify**:
    - ✅ Status berhasil diubah
    - ✅ Status baru ditampilkan: "Dalam Proses"
    - ✅ Timestamp perubahan status ditampilkan (jika ada)

#### Step 10: Check Email Notification (User - Status Change)

1. Buka email inbox: `testuser001@example.com`
2. **Verify**:
    - ✅ Email notifikasi terkirim
    - ✅ Subject: "Status Tiket Diubah: CSIRT-000XXX"
    - ✅ Isi email berisi:
        - Status baru: "Dalam Proses"
        - Informasi tiket

#### Step 11: Agent Close Ticket

1. Login sebagai Agent
2. Buka detail tiket: `/agent/tickets/CSIRT-000XXX`
3. Klik tombol "Ubah Status"
4. Pilih status: "Tertutup" (Closed)
5. Klik "Simpan"
6. **Verify**:
    - ✅ Status berhasil diubah menjadi "Tertutup"
    - ✅ Field `ditutup_pada` terisi dengan timestamp
    - ✅ Tiket tidak bisa diubah lagi (atau bisa dibuka kembali, sesuai requirement)

#### Step 12: Check Email Notification (User - Ticket Closed)

1. Buka email inbox: `testuser001@example.com`
2. **Verify**:
    - ✅ Email notifikasi terkirim
    - ✅ Subject: "Status Tiket Diubah: CSIRT-000XXX" atau "Tiket Ditutup"
    - ✅ Isi email berisi:
        - Status: "Tertutup"
        - Pesan penutupan tiket

### Expected Final Result

-   ✅ Alur lengkap berjalan tanpa error
-   ✅ Semua status berubah sesuai alur
-   ✅ Semua notifikasi email terkirim
-   ✅ Data tersimpan dengan benar di database
-   ✅ User dan Agent dapat berkomunikasi dengan baik

---

## 🎯 SCENARIO 2: Guest User Flow (Without Login)

### Objective

Test alur untuk user yang tidak login (guest) dapat membuat tiket dan mengecek status.

### Precondition

-   Ada tiket yang sudah dibuat sebelumnya (dari Scenario 1)
-   Catat nomor tiket dan email pelapor

### Step-by-Step

#### Step 1: Guest Check Ticket Status

1. Buka browser baru (Incognito/Private mode)
2. Navigate ke: `http://127.0.0.1:8000/portal/ticket/status`
3. **Verify**: Form cek status muncul
4. Isi form:
    ```
    Nomor Tiket: CSIRT-000XXX (dari Scenario 1)
    Email: testuser001@example.com
    ```
5. Klik tombol "Cek Status" atau "Check"
6. **Verify**:
    - ✅ Jika nomor tiket dan email cocok:
        - Redirect ke halaman detail tiket
        - Session verification tersimpan
        - Detail tiket ditampilkan dengan lengkap
    - ✅ Jika tidak cocok:
        - Error message muncul: "Tiket tidak ditemukan atau email tidak sesuai"
        - Tetap di halaman status check

#### Step 2: Guest View Ticket Detail

1. Setelah verifikasi berhasil, **Verify Detail Page**:
    - ✅ Nomor tiket ditampilkan
    - ✅ Subjek ditampilkan
    - ✅ Status ditampilkan
    - ✅ Prioritas ditampilkan
    - ✅ Thread/pesan ditampilkan
    - ✅ Lampiran dapat di-download
    - ✅ Form balasan tersedia
    - ✅ Tombol "Balas" tersedia

#### Step 3: Guest Reply to Ticket

1. Scroll ke form balasan
2. Isi form:
    ```
    Pesan: "Terima kasih atas penanganannya.
            Saya akan menunggu update selanjutnya."
    ```
3. Klik "Kirim Balasan"
4. **Verify**:
    - ✅ Balasan berhasil dikirim
    - ✅ Status tiket berubah menjadi "Menunggu Pelapor"
    - ✅ Thread baru muncul
    - ✅ Email notifikasi terkirim ke agent

#### Step 4: Guest Access After Session Expired

1. Tutup browser atau clear session
2. Coba akses langsung: `/portal/ticket/CSIRT-000XXX`
3. **Verify**:
    - ✅ Redirect ke halaman status check
    - ✅ Atau meminta verifikasi email lagi

### Expected Final Result

-   ✅ Guest dapat cek status tiket tanpa login
-   ✅ Guest dapat melihat detail tiket setelah verifikasi
-   ✅ Guest dapat membalas tiket
-   ✅ Security tetap terjaga (verifikasi email diperlukan)

---

## 🎯 SCENARIO 3: Assignment Flow

### Objective

Test alur assignment tiket dari satu agent ke agent lain.

### Precondition

-   Ada 2 agent account: Agent A dan Agent B
-   Ada tiket yang belum di-assign

### Step-by-Step

#### Step 1: Agent A View Unassigned Ticket

1. Login sebagai Agent A: `agent@csirt.kalselprov.go.id`
2. Buka daftar tiket: `/agent/tickets`
3. Pilih tiket yang belum di-assign (status: "Terbuka")
4. Buka detail tiket

#### Step 2: Agent A Assign Ticket to Agent B

1. Di detail tiket, cari tombol "Assign" atau dropdown "Assign To"
2. **Verify**: Dropdown menampilkan list agent yang tersedia
3. Pilih Agent B dari dropdown
4. Klik "Assign" atau "Simpan"
5. **Verify**:
    - ✅ Assignment berhasil
    - ✅ Status tiket otomatis berubah menjadi "Ditugaskan" (assigned)
    - ✅ Field "Ditugaskan ke" terisi dengan nama Agent B
    - ✅ Success message muncul

#### Step 3: Check Email Notification (Agent B)

1. Buka email inbox Agent B
2. **Verify**:
    - ✅ Email notifikasi terkirim
    - ✅ Subject: "Tiket Ditugaskan: CSIRT-000XXX"
    - ✅ Isi email berisi:
        - Informasi tiket
        - Link ke detail tiket
        - Pesan bahwa tiket ditugaskan ke Agent B

#### Step 4: Agent B View Assigned Ticket

1. Login sebagai Agent B: `agent2@csirt.kalselprov.go.id` (atau agent lain)
2. Buka Agent Dashboard: `/agent`
3. **Verify**:
    - ✅ Statistik "Tiket Ditugaskan ke Saya" bertambah
4. Buka daftar tiket: `/agent/tickets`
5. Filter atau cari tiket yang di-assign
6. **Verify**:
    - ✅ Tiket muncul di list
    - ✅ Kolom "Ditugaskan ke" menampilkan nama Agent B
7. Buka detail tiket

#### Step 5: Agent B Reply to Assigned Ticket

1. Di detail tiket, isi form balasan
2. Kirim balasan
3. **Verify**:
    - ✅ Balasan berhasil dikirim
    - ✅ Agent B dapat mengakses dan membalas tiket

### Expected Final Result

-   ✅ Assignment berfungsi dengan benar
-   ✅ Status otomatis berubah menjadi "Ditugaskan"
-   ✅ Agent B menerima notifikasi
-   ✅ Agent B dapat mengakses tiket yang di-assign

---

## 🎯 SCENARIO 4: Admin CRUD Operations

### Objective

Test semua operasi CRUD di Admin Panel.

### Precondition

-   Login sebagai Super Admin

### Step-by-Step: CRUD Departments

#### CREATE

1. Login sebagai Super Admin: `admin@csirt.kalselprov.go.id`
2. Navigate ke: `/admin/departments`
3. **Verify**: List departments ditampilkan
4. Klik tombol "Tambah Departemen" atau "Create"
5. **Verify**: Form create muncul
6. Isi form:
    ```
    Nama: Departemen Test 001
    Email: dept001@example.com
    Publik: Centang (true)
    ```
7. Klik "Simpan" atau "Create"
8. **Verify**:
    - ✅ Departemen berhasil dibuat
    - ✅ Redirect ke list departments
    - ✅ Departemen baru muncul di list
    - ✅ Success message muncul

#### READ

1. Di list departments, **Verify**:
    - ✅ Semua departments ditampilkan
    - ✅ Kolom: Nama, Email, Publik
    - ✅ Pagination berfungsi (jika ada)
    - ✅ Search berfungsi (jika ada)

#### UPDATE

1. Klik "Edit" pada salah satu department
2. **Verify**: Form edit muncul dengan data terisi
3. Ubah nama: "Departemen Test 001 - Updated"
4. Klik "Simpan" atau "Update"
5. **Verify**:
    - ✅ Department berhasil di-update
    - ✅ Perubahan tersimpan
    - ✅ Redirect ke list

#### DELETE

1. Klik "Delete" pada salah satu department
2. **Verify**: Konfirmasi dialog muncul
3. Konfirmasi penghapusan
4. **Verify**:
    - ✅ Department berhasil dihapus
    - ✅ Tidak muncul lagi di list
    - ✅ Success message muncul

### Repeat untuk:

-   Help Topics
-   SLA Plans
-   Priorities
-   Statuses
-   Teams
-   Canned Responses
-   Organizations
-   Users

### Expected Final Result

-   ✅ Semua CRUD operations berfungsi
-   ✅ Validasi bekerja dengan benar
-   ✅ Data tersimpan dengan benar
-   ✅ Error handling baik

---

## 🎯 SCENARIO 5: File Upload Testing

### Objective

Test berbagai skenario file upload.

### Step-by-Step

#### Test 1: Valid File Upload

1. Buat tiket baru atau balasan
2. Upload file valid:
    - File PDF: `test.pdf` (< 10MB)
    - File Image: `test.jpg` (< 10MB)
    - File Document: `test.docx` (< 10MB)
3. **Verify**:
    - ✅ File berhasil di-upload
    - ✅ File muncul di detail tiket
    - ✅ File dapat di-download
    - ✅ File tersimpan di storage

#### Test 2: File Size Validation

1. Coba upload file > 10MB
2. **Verify**:
    - ✅ Error message: "File terlalu besar (maks 10MB)"
    - ✅ File tidak ter-upload

#### Test 3: File Type Validation

1. Coba upload file dengan ekstensi tidak valid:
    - `.exe`
    - `.bat`
    - `.php`
    - `.js`
2. **Verify**:
    - ✅ Error message: "Tipe file tidak diizinkan"
    - ✅ File tidak ter-upload

#### Test 4: Multiple Files Upload

1. Coba upload multiple files (jika fitur ini ada)
2. **Verify**:
    - ✅ Semua file berhasil di-upload
    - ✅ Semua file muncul di detail tiket

### Expected Final Result

-   ✅ Validasi file upload bekerja dengan benar
-   ✅ File yang valid berhasil di-upload
-   ✅ File yang tidak valid ditolak dengan error message yang jelas

---

## 🎯 SCENARIO 6: Security Testing

### Objective

Test keamanan aplikasi dari berbagai serangan umum.

### Step-by-Step

#### Test 1: SQL Injection

1. Di form search atau filter, masukkan:
    ```
    ' OR '1'='1
    ```
2. Submit form
3. **Verify**:
    - ✅ Tidak ada error SQL
    - ✅ Query aman (menggunakan parameter binding)
    - ✅ Tidak ada data yang ter-expose

#### Test 2: XSS (Cross-Site Scripting)

1. Di form tiket atau balasan, masukkan:
    ```
    <script>alert('XSS')</script>
    ```
2. Submit form
3. **Verify**:
    - ✅ Script tidak dieksekusi
    - ✅ Input di-escape/di-sanitize
    - ✅ Output aman (tidak ada script yang berjalan)

#### Test 3: CSRF Protection

1. Buka form tiket
2. Inspect element, hapus CSRF token dari form
3. Submit form via POST request
4. **Verify**:
    - ✅ Error 419 atau CSRF error
    - ✅ Form tidak submit
    - ✅ Pesan error jelas

#### Test 4: Authorization

1. Login sebagai user biasa (tanpa role Agent/Admin)
2. Coba akses langsung URL:
    - `/agent/tickets`
    - `/admin/users`
3. **Verify**:
    - ✅ Redirect ke login atau error 403
    - ✅ Tidak ada data sensitif yang ter-expose

### Expected Final Result

-   ✅ Aplikasi aman dari serangan umum
-   ✅ Input validation bekerja dengan baik
-   ✅ Authorization bekerja dengan benar

---

## 📝 Notes

-   Gunakan browser DevTools untuk inspect network requests
-   Monitor console untuk JavaScript errors
-   Check database langsung untuk verifikasi data
-   Screenshot bug yang ditemukan
-   Document semua findings

---

**Happy Testing! 🚀**
