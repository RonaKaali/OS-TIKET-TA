# 🛠️ Test Execution Helper - Quick Reference

## 📋 Pre-Testing Checklist

Sebelum memulai testing, pastikan:

-   [ ] Database sudah di-migrate: `php artisan migrate:fresh --seed`
-   [ ] Storage link sudah dibuat: `php artisan storage:link`
-   [ ] Server berjalan: `php artisan serve`
-   [ ] Asset sudah di-build: `npm run dev` atau `npm run build`
-   [ ] Mail configuration sudah benar (untuk test notifikasi)
-   [ ] Browser cache sudah di-clear
-   [ ] Multiple browser sudah siap (Chrome, Firefox, Safari)

---

## 🔑 Test Accounts

### Default Accounts (dari Seeder)

```
Super Admin:
Email: admin@csirt.kalselprov.go.id
Password: password

Agent:
Email: agent@csirt.kalselprov.go.id
Password: password

Support Agent:
Email: support@csirt.kalselprov.go.id
Password: password

Agent 2:
Email: agent2@csirt.kalselprov.go.id
Password: password
```

### Create New Test User

1. Buka `/register`
2. Isi form:
    - Nama: "Test User [Timestamp]"
    - Email: "testuser[timestamp]@example.com"
    - Password: "password123"
3. Register

---

## 🧪 Test Data Templates

### Template Data untuk Membuat Tiket

```json
{
    "departemen": "Keamanan Siber",
    "topik_bantuan": "Serangan Malware",
    "subjek": "Test Laporan Insiden Siber - [TIMESTAMP]",
    "pesan": "Ini adalah test laporan untuk memverifikasi sistem ticketing. Mohon ditangani dengan baik.",
    "prioritas": "Normal",
    "lampiran": "test-file.pdf (opsional)"
}
```

### Template Data untuk Balasan

```json
{
    "pesan": "Terima kasih telah melaporkan insiden. Kami sedang menangani laporan Anda dan akan memberikan update segera.",
    "lampiran": "response-file.pdf (opsional)"
}
```

---

## 🎯 Quick Test Scenarios

### Scenario 1: Happy Path - Tiket Selesai

1. **User membuat tiket**

    - Login sebagai user baru
    - Buat tiket dengan data lengkap
    - ✅ Verifikasi: Tiket dibuat, nomor tiket ter-generate

2. **Agent melihat tiket**

    - Login sebagai agent
    - Buka `/agent/tickets`
    - ✅ Verifikasi: Tiket muncul di list

3. **Agent membalas**

    - Buka detail tiket
    - Kirim balasan
    - ✅ Verifikasi: Status = "Terbuka", email terkirim

4. **User membalas**

    - Login sebagai user
    - Buka detail tiket
    - Kirim balasan
    - ✅ Verifikasi: Status = "Menunggu Pelapor"

5. **Agent menutup tiket**
    - Login sebagai agent
    - Ubah status menjadi "Tertutup"
    - ✅ Verifikasi: Status = "Tertutup", `ditutup_pada` terisi, email terkirim

**Expected Result**: ✅ Alur lengkap berjalan tanpa error

---

### Scenario 2: Guest User Flow

1. **Guest membuat tiket** (jika fitur ini ada)
    - Atau: User membuat tiket, lalu logout
2. **Guest cek status**
    - Buka `/portal/ticket/status`
    - Masukkan nomor tiket + email
    - ✅ Verifikasi: Redirect ke detail tiket
3. **Guest membalas**
    - Kirim balasan
    - ✅ Verifikasi: Balasan terkirim, status berubah

**Expected Result**: ✅ Guest dapat berinteraksi dengan tiket tanpa login

---

### Scenario 3: Assignment Flow

1. **Agent A melihat tiket baru**
2. **Agent A assign ke Agent B**
    - ✅ Verifikasi: Assignment berhasil, status = "Ditugaskan"
3. **Agent B menerima notifikasi**
    - ✅ Verifikasi: Email terkirim ke Agent B
4. **Agent B membalas tiket**
    - ✅ Verifikasi: Agent B dapat akses dan membalas

**Expected Result**: ✅ Assignment berfungsi dengan benar

---

### Scenario 4: Multiple Threads

1. **Buat tiket**
2. **Agent membalas** (Thread 1)
3. **User membalas** (Thread 2)
4. **Agent membalas lagi** (Thread 3)
5. **Agent membuat catatan internal** (Thread 4 - catatan)
6. **User membalas lagi** (Thread 5)

**Expected Result**:

-   ✅ Semua thread ditampilkan dalam urutan kronologis
-   ✅ Thread dapat dibedakan (pesan/balasan/catatan)
-   ✅ Catatan internal tidak terlihat oleh user

---

### Scenario 5: Admin CRUD Flow

1. **Login sebagai Super Admin**
2. **CRUD setiap master data**:
    - Users
    - Departments
    - Help Topics
    - SLA Plans
    - Priorities
    - Statuses
    - Teams
    - Canned Responses
    - Organizations

**Expected Result**:

-   ✅ Semua CRUD berfungsi
-   ✅ Validasi bekerja
-   ✅ Data tersimpan dengan benar

---

## 🔍 Common Issues & Solutions

### Issue 1: Email Notifikasi Tidak Terkirim

**Checklist**:

-   [ ] Cek konfigurasi MAIL di `.env`
-   [ ] Cek log: `storage/logs/laravel.log`
-   [ ] Test dengan Mailtrap atau MailHog
-   [ ] Pastikan queue worker berjalan (jika menggunakan queue)

**Solution**:

```bash
# Untuk development, set di .env:
QUEUE_CONNECTION=sync

# Atau jalankan queue worker:
php artisan queue:work
```

---

### Issue 2: File Upload Gagal

**Checklist**:

-   [ ] Storage link sudah dibuat: `php artisan storage:link`
-   [ ] Folder `storage/app/public/attachments` ada dan writable
-   [ ] File size tidak melebihi 10MB
-   [ ] File extension valid (jpg, jpeg, png, gif, pdf, doc, docx)

**Solution**:

```bash
# Buat storage link
php artisan storage:link

# Set permission (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### Issue 3: Permission Error (403)

**Checklist**:

-   [ ] User memiliki role/permission yang tepat
-   [ ] Cache permission sudah di-reset
-   [ ] Middleware sudah benar di route

**Solution**:

```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
```

---

### Issue 4: Session Error

**Checklist**:

-   [ ] Tabel `sesi` sudah dibuat
-   [ ] Konfigurasi session di `config/session.php` benar
-   [ ] Storage session writable

**Solution**:

```bash
# Clear session
php artisan session:clear

# Atau set session driver ke file (untuk testing):
# Di .env: SESSION_DRIVER=file
```

---

## 📊 Test Execution Log Template

Gunakan template ini untuk mencatat hasil testing:

```
Date: _______________
Tester: _______________
Environment: _______________

=== Test Session 1 ===
Time: _______________
Module: _______________
Test Cases: _______________

Results:
- TC-XXX: ✅ PASS / ❌ FAIL / ⚠️ BLOCKED
  Notes: _______________
- TC-XXX: ✅ PASS / ❌ FAIL / ⚠️ BLOCKED
  Notes: _______________

Issues Found:
1. _______________
2. _______________

=== Test Session 2 ===
...
```

---

## 🎨 Browser Testing Checklist

### Chrome

-   [ ] Test semua fitur utama
-   [ ] Test responsive design
-   [ ] Check console untuk error JavaScript
-   [ ] Check Network tab untuk request/response

### Firefox

-   [ ] Test semua fitur utama
-   [ ] Test responsive design
-   [ ] Check console untuk error

### Safari (jika ada)

-   [ ] Test semua fitur utama
-   [ ] Test responsive design

### Mobile Browser

-   [ ] Test di Chrome Mobile
-   [ ] Test di Safari Mobile
-   [ ] Test responsive design
-   [ ] Test touch interactions

---

## 📱 Mobile Testing Checklist

-   [ ] Login/Logout berfungsi
-   [ ] Form dapat diisi dengan mudah
-   [ ] Button dapat diklik dengan mudah
-   [ ] Menu navigasi dapat diakses
-   [ ] Tabel dapat di-scroll horizontal
-   [ ] File upload berfungsi
-   [ ] Notifikasi muncul dengan benar

---

## 🔐 Security Testing Quick Checklist

-   [ ] SQL Injection: Test dengan input `' OR '1'='1`
-   [ ] XSS: Test dengan input `<script>alert('XSS')</script>`
-   [ ] CSRF: Test dengan menghapus CSRF token
-   [ ] Authorization: Test akses tanpa permission
-   [ ] Session: Test dengan session expired
-   [ ] File Upload: Test dengan file berbahaya (.exe, .php)

---

## 📧 Email Testing Setup

### Option 1: Mailtrap (Recommended untuk Testing)

1. Daftar di https://mailtrap.io
2. Buat inbox baru
3. Copy SMTP credentials
4. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Option 2: MailHog (Local)

1. Install MailHog
2. Jalankan MailHog
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Option 3: Gmail (Production-like)

1. Buat Gmail test account
2. Generate App Password
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

---

## 🚨 Critical Paths to Test

### Must Test Before Release:

1. ✅ **User Registration & Login**
2. ✅ **Create Ticket (Portal)**
3. ✅ **Agent View & Reply Ticket**
4. ✅ **Status Change (especially to Closed)**
5. ✅ **Email Notifications**
6. ✅ **File Upload**
7. ✅ **Admin CRUD (at least one module)**
8. ✅ **Authorization (403 errors)**
9. ✅ **Guest Check Status**

---

## 📝 Bug Report Template

Jika menemukan bug, gunakan template ini:

```
**Bug ID**: BUG-XXX
**Date**: _______________
**Reporter**: _______________
**Severity**: Critical / High / Medium / Low
**Module**: _______________

**Description**:
_______________

**Steps to Reproduce**:
1. _______________
2. _______________
3. _______________

**Expected Result**:
_______________

**Actual Result**:
_______________

**Screenshot**:
[Attach screenshot if available]

**Environment**:
- Browser: _______________
- OS: _______________
- PHP Version: _______________
- Laravel Version: _______________

**Additional Notes**:
_______________
```

---

## ✅ Final Checklist Before Release

-   [ ] Semua test case critical paths sudah di-test
-   [ ] Tidak ada bug critical/high yang outstanding
-   [ ] Email notifications berfungsi
-   [ ] File upload berfungsi
-   [ ] Authorization bekerja dengan benar
-   [ ] Responsive design sudah di-test di multiple devices
-   [ ] Performance acceptable (tidak ada lag yang signifikan)
-   [ ] Error handling sudah baik (404, 403, 500)
-   [ ] Security testing sudah dilakukan
-   [ ] Documentation sudah lengkap

---

**Happy Testing! 🎉**
