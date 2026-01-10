# Blackbox Testing - OS-Tiket (CSIRT Kalselprov)

## 📋 Daftar Isi

1. [Authentication Testing](#1-authentication-testing)
2. [Guest (Publik) Features Testing](#2-guest-publik-features-testing)
3. [Portal User (Authenticated) Testing](#3-portal-user-authenticated-testing)
4. [Agent Features Testing](#4-agent-features-testing)
5. [Admin/Super Admin - User Management Testing](#5-adminsuper-admin---user-management-testing)
6. [Admin/Super Admin - Master Data Management Testing](#6-adminsuper-admin---master-data-management-testing)
7. [Profile Management Testing](#7-profile-management-testing)
8. [Dashboard & Statistics Testing](#8-dashboard--statistics-testing)
9. [Chatbot Testing](#9-chatbot-testing)
10. [Notification Testing](#10-notification-testing)
11. [Security & Authorization Testing](#11-security--authorization-testing)
12. [Edge Cases & Error Handling Testing](#12-edge-cases--error-handling-testing)
13. [Summary Testing](#-summary-testing)

---

## 1. Authentication Testing

### 1.1 Register

| Kode Tes | Test Case                                        | Output yang Diharapkan                                                     | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------ | -------------------------------------------------------------------------- | ---------------------- | ----- |
| AUTH-001 | Register dengan data valid                       | User berhasil dibuat, redirect ke verifikasi email/dashboard, pesan sukses |                        | ⬜    |
| AUTH-002 | Register dengan email yang sudah terdaftar       | Validasi error: "Email sudah digunakan"                                    |                        | ⬜    |
| AUTH-003 | Register dengan format email tidak valid         | Validasi error: "Format email tidak valid"                                 |                        | ⬜    |
| AUTH-004 | Register dengan password kurang dari 8 karakter  | Validasi error: "Password minimal 8 karakter"                              |                        | ⬜    |
| AUTH-005 | Register dengan password confirmation tidak sama | Validasi error: "Password konfirmasi tidak cocok"                          |                        | ⬜    |
| AUTH-006 | Register dengan field wajib kosong               | Validasi error untuk setiap field yang kosong                              |                        | ⬜    |

### 1.2 Login

| Kode Tes | Test Case                                   | Output yang Diharapkan                            | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------- | ------------------------------------------------- | ---------------------- | ----- |
| AUTH-007 | Login dengan kredensial valid (User biasa)  | Login berhasil, redirect ke welcome/portal        |                        | ⬜    |
| AUTH-008 | Login dengan kredensial valid (Agent/Admin) | Login berhasil, redirect ke dashboard sesuai role |                        | ⬜    |
| AUTH-009 | Login dengan kredensial salah               | Validasi error: "Kredensial tidak valid"          |                        | ⬜    |
| AUTH-010 | Login dengan akun belum verifikasi email    | Pesan: "Email perlu diverifikasi terlebih dahulu" |                        | ⬜    |
| AUTH-011 | Login dengan field kosong                   | Validasi error untuk email dan password           |                        | ⬜    |

### 1.3 Logout

| Kode Tes | Test Case                               | Output yang Diharapkan                                              | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------------- | ------------------------------------------------------------------- | ---------------------- | ----- |
| AUTH-012 | Logout dari sistem                      | Session dihapus, redirect ke halaman welcome, pesan logout berhasil |                        | ⬜    |
| AUTH-013 | Logout saat masih ada tiket yang dibuka | Session dihapus, redirect ke welcome                                |                        | ⬜    |

### 1.4 Forgot/Reset Password

| Kode Tes | Test Case                                            | Output yang Diharapkan                                                               | Output yang Sebenarnya | Hasil |
| -------- | ---------------------------------------------------- | ------------------------------------------------------------------------------------ | ---------------------- | ----- |
| AUTH-014 | Request reset password dengan email valid            | Email reset password dikirim, pesan sukses ditampilkan                               |                        | ⬜    |
| AUTH-015 | Request reset password dengan email tidak terdaftar  | Pesan sukses (security) atau error "Email tidak ditemukan"                           |                        | ⬜    |
| AUTH-016 | Mengakses link reset password yang valid             | Form reset password ditampilkan (email, password, password confirmation)             |                        | ⬜    |
| AUTH-017 | Mengakses link reset password yang expired/invalid   | Pesan error: "Link reset password tidak valid atau sudah kedaluwarsa"                |                        | ⬜    |
| AUTH-018 | Reset password dengan data valid                     | Password berhasil diubah, redirect ke login, pesan sukses                            |                        | ⬜    |
| AUTH-019 | Reset password dengan validasi password tidak sesuai | Validasi error: "Password minimal 8 karakter" atau "Password konfirmasi tidak cocok" |                        | ⬜    |

### 1.5 Email Verification

| Kode Tes | Test Case                                       | Output yang Diharapkan                                                | Output yang Sebenarnya | Hasil |
| -------- | ----------------------------------------------- | --------------------------------------------------------------------- | ---------------------- | ----- |
| AUTH-020 | Klik link verifikasi email yang valid           | Email berhasil diverifikasi, redirect ke dashboard/welcome            |                        | ⬜    |
| AUTH-021 | Klik link verifikasi email yang expired/invalid | Pesan error: "Link verifikasi tidak valid"                            |                        | ⬜    |
| AUTH-022 | Request resend email verifikasi                 | Email verifikasi dikirim ulang, pesan sukses                          |                        | ⬜    |
| AUTH-023 | User baru belum verifikasi email                | Pesan verifikasi email ditampilkan, tombol kirim ulang email tersedia |                        | ⬜    |

---

## 2. Guest (Publik) Features Testing

### 2.1 Landing Page

| Kode Tes  | Test Case                                                    | Output yang Diharapkan                                          | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------------------------------ | --------------------------------------------------------------- | ---------------------- | ----- |
| GUEST-001 | Mengakses halaman utama (/)                                  | Halaman welcome/landing page ditampilkan dengan informasi CSIRT |                        | ⬜    |
| GUEST-002 | Melihat informasi tentang CSIRT                              | Informasi CSIRT ditampilkan dengan jelas                        |                        | ⬜    |
| GUEST-003 | Melihat link menu navigasi (About, Help Topics, Departments) | Menu navigasi ditampilkan dan dapat diklik                      |                        | ⬜    |
| GUEST-004 | Mengakses landing page saat sudah login                      | Redirect sesuai role (user → welcome, agent → dashboard)        |                        | ⬜    |

### 2.2 View Help Topics / Kategori Insiden

| Kode Tes  | Test Case                                | Output yang Diharapkan                                           | Output yang Sebenarnya | Hasil |
| --------- | ---------------------------------------- | ---------------------------------------------------------------- | ---------------------- | ----- |
| GUEST-005 | Melihat daftar help topics               | Daftar help topics ditampilkan dengan nama dan deskripsi         |                        | ⬜    |
| GUEST-006 | Hanya help topics aktif yang ditampilkan | Semua help topics aktif ditampilkan, yang non-aktif tidak muncul |                        | ⬜    |

### 2.3 View Departments

| Kode Tes  | Test Case                               | Output yang Diharapkan                                          | Output yang Sebenarnya | Hasil |
| --------- | --------------------------------------- | --------------------------------------------------------------- | ---------------------- | ----- |
| GUEST-007 | Melihat daftar departemen               | Daftar departemen ditampilkan dengan nama dan informasi         |                        | ⬜    |
| GUEST-008 | Hanya departemen aktif yang ditampilkan | Semua departemen aktif ditampilkan, yang non-aktif tidak muncul |                        | ⬜    |

### 2.4 Cek Status Tiket (Public)

| Kode Tes  | Test Case                                                  | Output yang Diharapkan                                                                             | Output yang Sebenarnya | Hasil |
| --------- | ---------------------------------------------------------- | -------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| GUEST-009 | Cek status dengan nomor tiket dan email valid              | Detail tiket ditampilkan (terbatas, tanpa internal thread), status dan informasi dasar ditampilkan |                        | ⬜    |
| GUEST-010 | Cek status dengan nomor tiket valid tapi email tidak cocok | Pesan error: "Email tidak cocok dengan tiket" atau "Tiket tidak ditemukan"                         |                        | ⬜    |
| GUEST-011 | Cek status dengan nomor tiket tidak valid                  | Pesan error: "Tiket tidak ditemukan"                                                               |                        | ⬜    |
| GUEST-012 | Cek status dengan field kosong                             | Validasi error untuk field yang kosong                                                             |                        | ⬜    |
| GUEST-013 | Melihat detail tiket public (setelah verifikasi)           | Informasi dasar tiket ditampilkan, tanpa thread/balasan internal                                   |                        | ⬜    |

---

## 3. Portal User (Authenticated) Testing

### 3.1 Create Ticket

| Kode Tes   | Test Case                                                                                             | Output yang Diharapkan                                                                                                              | Output yang Sebenarnya | Hasil |
| ---------- | ----------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| PORTAL-001 | Membuat tiket dengan data valid lengkap (subject, help topic, priority opsional, message, attachment) | Tiket berhasil dibuat, nomor tiket di-generate (format: OST-XXXXXX), redirect ke detail tiket, notifikasi email & telegram terkirim |                        | ⬜    |
| PORTAL-002 | Membuat tiket tanpa priority (optional)                                                               | Tiket berhasil dibuat dengan priority null/default                                                                                  |                        | ⬜    |
| PORTAL-003 | Membuat tiket dengan attachment valid (jpg, png, pdf, doc, docx, max 10MB)                            | Tiket berhasil dibuat, attachment tersimpan di storage, dapat diunduh                                                               |                        | ⬜    |
| PORTAL-004 | Membuat tiket dengan attachment lebih dari 10MB atau tipe tidak valid                                 | Validasi error: "Ukuran file maksimal 10MB" atau "Tipe file tidak diperbolehkan"                                                    |                        | ⬜    |
| PORTAL-005 | Membuat tiket dengan field wajib kosong                                                               | Validasi error untuk subject, help_topic, message                                                                                   |                        | ⬜    |
| PORTAL-006 | Status tiket baru adalah "open" dan SLA & due date ter-set otomatis                                   | Status = "open", due_at dihitung berdasarkan SLA plan (grace_hours)                                                                 |                        | ⬜    |

### 3.2 My Tickets

| Kode Tes   | Test Case                                  | Output yang Diharapkan                                                                           | Output yang Sebenarnya | Hasil |
| ---------- | ------------------------------------------ | ------------------------------------------------------------------------------------------------ | ---------------------- | ----- |
| PORTAL-007 | Melihat daftar tiket milik sendiri         | Daftar tiket yang dibuat oleh user ditampilkan dengan informasi: nomor, subject, status, tanggal |                        | ⬜    |
| PORTAL-008 | Filter/pagination/search pada daftar tiket | Daftar tiket ditampilkan dengan pagination atau filter/search                                    |                        | ⬜    |
| PORTAL-009 | User tidak bisa melihat tiket user lain    | Hanya tiket milik sendiri yang ditampilkan                                                       |                        | ⬜    |
| PORTAL-010 | Melihat daftar tiket saat belum ada tiket  | Pesan: "Belum ada tiket" atau tabel kosong                                                       |                        | ⬜    |

### 3.3 Ticket Detail

| Kode Tes   | Test Case                                    | Output yang Diharapkan                                                                               | Output yang Sebenarnya | Hasil |
| ---------- | -------------------------------------------- | ---------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| PORTAL-011 | Melihat detail tiket sendiri                 | Detail lengkap tiket ditampilkan: nomor, subject, status, priority, semua thread/balasan, attachment |                        | ⬜    |
| PORTAL-012 | Melihat detail tiket dengan multiple threads | Semua thread/balasan ditampilkan dalam urutan kronologis                                             |                        | ⬜    |
| PORTAL-013 | Melihat attachment di detail tiket           | Attachment dapat diunduh dan ditampilkan                                                             |                        | ⬜    |
| PORTAL-014 | Mencoba akses tiket milik user lain          | Error 403 atau redirect, pesan: "Tidak memiliki akses"                                               |                        | ⬜    |

### 3.4 Reply Ticket

| Kode Tes   | Test Case                                   | Output yang Diharapkan                                                                                         | Output yang Sebenarnya | Hasil |
| ---------- | ------------------------------------------- | -------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| PORTAL-015 | Membalas tiket sendiri dengan message valid | Balasan berhasil ditambahkan sebagai thread, status berubah sesuai business rule, notifikasi ke assigned agent |                        | ⬜    |
| PORTAL-016 | Membalas tiket dengan attachment            | Attachment berhasil diupload dan tersimpan                                                                     |                        | ⬜    |
| PORTAL-017 | Membalas tiket yang sudah closed            | Pesan error: "Tidak dapat membalas tiket yang sudah ditutup" atau tetap bisa reply (sesuai business rule)      |                        | ⬜    |
| PORTAL-018 | Membalas tiket dengan message kosong        | Validasi error: "Message wajib diisi"                                                                          |                        | ⬜    |

---

## 4. Agent Features Testing

### 4.1 Dashboard Agent

| Kode Tes  | Test Case                                             | Output yang Diharapkan                                                                                | Output yang Sebenarnya | Hasil |
| --------- | ----------------------------------------------------- | ----------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| AGENT-001 | Mengakses dashboard agent (role Agent/Admin)          | Dashboard agent ditampilkan dengan statistik: total tiket, open, assigned, dll dan list tiket terbaru |                        | ⬜    |
| AGENT-002 | Melihat statistik tiket di dashboard                  | Statistik akurat sesuai data di database                                                              |                        | ⬜    |
| AGENT-003 | User tanpa permission admin.panel mengakses dashboard | Error 403 atau redirect ke welcome                                                                    |                        | ⬜    |

### 4.2 Manajemen Tiket (View, Filter, Search, Pagination)

| Kode Tes  | Test Case                                    | Output yang Diharapkan                                             | Output yang Sebenarnya | Hasil |
| --------- | -------------------------------------------- | ------------------------------------------------------------------ | ---------------------- | ----- |
| AGENT-004 | Melihat daftar semua tiket (Agent)           | Semua tiket ditampilkan dengan filter: all, open, assigned, closed |                        | ⬜    |
| AGENT-005 | Melihat daftar tiket yang di-assign ke agent | Hanya tiket yang assigned_to = agent_id ditampilkan                |                        | ⬜    |
| AGENT-006 | Filter tiket berdasarkan status dan priority | Daftar tiket terfilter sesuai status/priority yang dipilih         |                        | ⬜    |
| AGENT-007 | Search tiket berdasarkan nomor atau subject  | Hasil pencarian ditampilkan sesuai keyword                         |                        | ⬜    |
| AGENT-008 | Pagination pada daftar tiket                 | Daftar tiket dipaginate dengan benar                               |                        | ⬜    |

### 4.3 Ticket Detail Agent

| Kode Tes  | Test Case                                            | Output yang Diharapkan                                                                              | Output yang Sebenarnya | Hasil |
| --------- | ---------------------------------------------------- | --------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| AGENT-009 | Melihat detail tiket (Agent)                         | Detail lengkap tiket ditampilkan termasuk semua thread (reply + note internal), history, attachment |                        | ⬜    |
| AGENT-010 | Melihat semua thread dengan pembedaan reply dan note | Thread ditampilkan dengan pembedaan reply (public) dan note (internal)                              |                        | ⬜    |
| AGENT-011 | Melihat attachment di detail tiket                   | Semua attachment dapat diunduh                                                                      |                        | ⬜    |
| AGENT-012 | Melihat informasi assignee dan history               | Informasi agent yang di-assign dan history perubahan ditampilkan                                    |                        | ⬜    |

### 4.4 Reply Agent

| Kode Tes  | Test Case                                         | Output yang Diharapkan                                                                               | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------------------- | ---------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| AGENT-013 | Membalas tiket sebagai agent dengan message valid | Balasan berhasil ditambahkan sebagai thread type "reply", notifikasi ke requester (email & telegram) |                        | ⬜    |
| AGENT-014 | Membalas tiket menggunakan canned response        | Template canned response di-load ke form, dapat diedit sebelum submit                                |                        | ⬜    |
| AGENT-015 | Membalas tiket dengan attachment                  | Attachment berhasil diupload                                                                         |                        | ⬜    |
| AGENT-016 | Status tiket berubah setelah reply agent          | Status berubah sesuai business rule (mis: open → in-progress)                                        |                        | ⬜    |
| AGENT-017 | Support Agent tidak bisa reply tiket              | Error 403 atau tombol reply tidak muncul                                                             |                        | ⬜    |

### 4.5 Assign Ticket

| Kode Tes  | Test Case                                              | Output yang Diharapkan                                                                                            | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------------------------ | ----------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| AGENT-018 | Assign tiket ke agent lain (permission tickets.assign) | Tiket berhasil di-assign, assigned_to ter-update, notifikasi ke agent yang di-assign, history assignment tercatat |                        | ⬜    |
| AGENT-019 | Assign tiket ke diri sendiri                           | Tiket berhasil di-assign ke agent sendiri                                                                         |                        | ⬜    |
| AGENT-020 | Agent tanpa permission tickets.assign mencoba assign   | Error 403 atau tombol assign tidak muncul                                                                         |                        | ⬜    |
| AGENT-021 | Assign tiket yang sudah closed                         | Pesan error: "Tidak dapat assign tiket yang sudah ditutup" atau tetap bisa assign (sesuai business rule)          |                        | ⬜    |

### 4.6 Update Status

| Kode Tes  | Test Case                              | Output yang Diharapkan                                                                                           | Output yang Sebenarnya | Hasil |
| --------- | -------------------------------------- | ---------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| AGENT-022 | Update status tiket (Agent)            | Status tiket ter-update, closed_at ter-set jika status adalah closing, notifikasi ke requester, history tercatat |                        | ⬜    |
| AGENT-023 | Update status ke "closed"              | Status closed, closed_at ter-set, tiket tidak bisa diupdate lagi (atau sesuai business rule)                     |                        | ⬜    |
| AGENT-024 | Support Agent update status (terbatas) | Support Agent hanya bisa update ke status tertentu sesuai permission                                             |                        | ⬜    |

### 4.7 Add Internal Note

| Kode Tes  | Test Case                            | Output yang Diharapkan                                                                                                   | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------ | ------------------------------------------------------------------------------------------------------------------------ | ---------------------- | ----- |
| AGENT-025 | Menambahkan internal note pada tiket | Note berhasil ditambahkan sebagai thread type "note", hanya visible untuk agent/admin, tidak ada notifikasi ke requester |                        | ⬜    |
| AGENT-026 | Note tidak terlihat oleh requester   | Requester tidak melihat note saat melihat detail tiket                                                                   |                        | ⬜    |
| AGENT-027 | Support Agent tidak bisa add note    | Error 403 atau tombol add note tidak muncul                                                                              |                        | ⬜    |

### 4.8 Update Priority

| Kode Tes  | Test Case                                | Output yang Diharapkan                        | Output yang Sebenarnya | Hasil |
| --------- | ---------------------------------------- | --------------------------------------------- | ---------------------- | ----- |
| AGENT-028 | Update priority tiket                    | Priority tiket ter-update, perubahan tercatat |                        | ⬜    |
| AGENT-029 | Update priority dengan nilai tidak valid | Validasi error: "Priority tidak valid"        |                        | ⬜    |

---

## 5. Admin/Super Admin - User Management Testing

### 5.1 View Users

| Kode Tes  | Test Case                                     | Output yang Diharapkan                                                    | Output yang Sebenarnya | Hasil |
| --------- | --------------------------------------------- | ------------------------------------------------------------------------- | ---------------------- | ----- |
| ADMIN-001 | Melihat daftar semua user (Super Admin/Admin) | Daftar user ditampilkan dengan informasi: name, email, role, organization |                        | ⬜    |
| ADMIN-002 | Search user berdasarkan name/email            | Hasil pencarian ditampilkan sesuai keyword                                |                        | ⬜    |
| ADMIN-003 | Filter user berdasarkan role                  | Daftar user terfilter sesuai role                                         |                        | ⬜    |
| ADMIN-004 | Pagination pada daftar user                   | Daftar user dipaginate                                                    |                        | ⬜    |
| ADMIN-005 | Agent mencoba akses user management           | Error 403 atau halaman tidak ditemukan                                    |                        | ⬜    |

### 5.2 Create User

| Kode Tes  | Test Case                                                                                               | Output yang Diharapkan                                                       | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------- | ---------------------- | ----- |
| ADMIN-006 | Membuat user baru dengan data valid (role assignment, email unik, password policy, organisasi opsional) | User berhasil dibuat, role ter-assign, redirect ke index dengan pesan sukses |                        | ⬜    |
| ADMIN-007 | Membuat user dengan email yang sudah ada                                                                | Validasi error: "Email sudah digunakan"                                      |                        | ⬜    |
| ADMIN-008 | Membuat user dengan password kurang dari 8 karakter                                                     | Validasi error: "Password minimal 8 karakter"                                |                        | ⬜    |
| ADMIN-009 | Membuat user dengan role tidak valid                                                                    | Validasi error: "Role tidak valid"                                           |                        | ⬜    |
| ADMIN-010 | Membuat user dengan organization (optional)                                                             | User berhasil dibuat dengan organization ter-assign                          |                        | ⬜    |
| ADMIN-011 | Admin bisa create user                                                                                  | Admin berhasil membuat user baru                                             |                        | ⬜    |

### 5.3 Update User

| Kode Tes  | Test Case                                                     | Output yang Diharapkan                             | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------------------------------------- | -------------------------------------------------- | ---------------------- | ----- |
| ADMIN-012 | Update data user (Super Admin/Admin)                          | Data user ter-update, redirect dengan pesan sukses |                        | ⬜    |
| ADMIN-013 | Update email user dengan email yang sudah digunakan user lain | Validasi error: "Email sudah digunakan"            |                        | ⬜    |
| ADMIN-014 | Update role user                                              | Role user ter-update                               |                        | ⬜    |
| ADMIN-015 | Update password user (optional)                               | Password ter-update jika diisi, tetap jika kosong  |                        | ⬜    |

### 5.4 Delete User (Super Admin Only)

| Kode Tes  | Test Case                       | Output yang Diharapkan                                                        | Output yang Sebenarnya | Hasil |
| --------- | ------------------------------- | ----------------------------------------------------------------------------- | ---------------------- | ----- |
| ADMIN-016 | Delete user (Super Admin)       | User berhasil dihapus, redirect dengan pesan sukses                           |                        | ⬜    |
| ADMIN-017 | Delete user sendiri             | Error: "Tidak dapat menghapus akun sendiri"                                   |                        | ⬜    |
| ADMIN-018 | Admin mencoba delete user       | Error 403 atau tombol delete tidak muncul                                     |                        | ⬜    |
| ADMIN-019 | Delete user yang memiliki tiket | User dihapus, tiket tetap ada (user_id menjadi null atau sesuai cascade rule) |                        | ⬜    |

---

## 6. Admin/Super Admin - Master Data Management Testing

### 6.1 Departments

| Kode Tes | Test Case                              | Output yang Diharapkan                                                      | Output yang Sebenarnya | Hasil |
| -------- | -------------------------------------- | --------------------------------------------------------------------------- | ---------------------- | ----- |
| MD-001   | CRUD Departments dengan data valid     | Department berhasil dibuat/diupdate/dihapus, redirect dengan pesan sukses   |                        | ⬜    |
| MD-002   | Delete department dengan tiket terkait | Error cascade atau department tetap ada (sesuai business rule)              |                        | ⬜    |
| MD-003   | Admin create/update department         | Admin bisa create dan update (terbatas), hanya Super Admin yang bisa delete |                        | ⬜    |

### 6.2 Help Topics

| Kode Tes | Test Case                                 | Output yang Diharapkan                                                   | Output yang Sebenarnya | Hasil |
| -------- | ----------------------------------------- | ------------------------------------------------------------------------ | ---------------------- | ----- |
| MD-004   | CRUD Help Topics dengan relasi department | Help topic berhasil dibuat/diupdate/dihapus dengan department ter-assign |                        | ⬜    |
| MD-005   | Help topic dengan custom form schema      | Custom fields tersimpan dengan benar                                     |                        | ⬜    |
| MD-006   | Delete help topic dengan tiket terkait    | Error cascade atau help topic tetap ada                                  |                        | ⬜    |

### 6.3 Statuses

| Kode Tes | Test Case                                                                 | Output yang Diharapkan                                     | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------------------------------- | ---------------------------------------------------------- | ---------------------- | ----- |
| MD-007   | CRUD Statuses dengan slug unique                                          | Status berhasil dibuat/diupdate/dihapus, slug harus unique |                        | ⬜    |
| MD-008   | Status dengan type (opening, closing, answered, dll) untuk business logic | Type status tersimpan dan digunakan untuk business logic   |                        | ⬜    |
| MD-009   | Delete status dengan tiket terkait                                        | Error cascade atau status default digunakan                |                        | ⬜    |

### 6.4 Priorities

| Kode Tes | Test Case                                | Output yang Diharapkan                                                   | Output yang Sebenarnya | Hasil |
| -------- | ---------------------------------------- | ------------------------------------------------------------------------ | ---------------------- | ----- |
| MD-010   | CRUD Priorities dengan level valid       | Priority berhasil dibuat/diupdate/dihapus dengan level (1-5 atau sesuai) |                        | ⬜    |
| MD-011   | Create priority dengan level tidak valid | Validasi error untuk level                                               |                        | ⬜    |
| MD-012   | Delete priority dengan tiket terkait     | Error cascade atau priority null pada tiket                              |                        | ⬜    |

### 6.5 SLA Plans

| Kode Tes | Test Case                                                | Output yang Diharapkan                                | Output yang Sebenarnya | Hasil |
| -------- | -------------------------------------------------------- | ----------------------------------------------------- | ---------------------- | ----- |
| MD-013   | CRUD SLA Plans dengan grace_hours valid                  | SLA plan berhasil dibuat/diupdate/dihapus             |                        | ⬜    |
| MD-014   | SLA plan digunakan saat create tiket untuk hitung due_at | Due_at dihitung berdasarkan grace_hours dari SLA plan |                        | ⬜    |
| MD-015   | Create SLA plan dengan grace_hours negatif               | Validasi error: "Grace hours harus positif"           |                        | ⬜    |
| MD-016   | Delete SLA plan dengan tiket terkait                     | Error cascade atau SLA default digunakan              |                        | ⬜    |

### 6.6 Teams

| Kode Tes | Test Case                         | Output yang Diharapkan                | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------- | ------------------------------------- | ---------------------- | ----- |
| MD-017   | CRUD Teams                        | Team berhasil dibuat/diupdate/dihapus |                        | ⬜    |
| MD-018   | Delete team dengan relasi terkait | Error cascade atau team tetap ada     |                        | ⬜    |

### 6.7 Organizations

| Kode Tes | Test Case                               | Output yang Diharapkan                         | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------------- | ---------------------------------------------- | ---------------------- | ----- |
| MD-019   | CRUD Organizations                      | Organization berhasil dibuat/diupdate/dihapus  |                        | ⬜    |
| MD-020   | Delete organization dengan user terkait | Error cascade atau organization null pada user |                        | ⬜    |
| MD-021   | Assign organization ke user             | User memiliki organization                     |                        | ⬜    |

### 6.8 Canned Responses

| Kode Tes | Test Case                                         | Output yang Diharapkan                                                                                        | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| MD-022   | CRUD Canned Responses dengan placeholder variable | Canned response berhasil dibuat/diupdate/dihapus, template dengan {ticket_number}, {requester_name} tersimpan |                        | ⬜    |
| MD-023   | Agent menggunakan canned response                 | Template di-load dengan variable ter-replace                                                                  |                        | ⬜    |

### 6.9 Chatbot Responses (Super Admin)

| Kode Tes | Test Case                                               | Output yang Diharapkan                                                        | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------------- | ----------------------------------------------------------------------------- | ---------------------- | ----- |
| MD-024   | CRUD Chatbot Responses dengan keyword-response matching | Chatbot response berhasil dibuat/diupdate/dihapus dengan keyword dan response |                        | ⬜    |
| MD-025   | Chatbot menggunakan response saat user chat             | Response sesuai dengan keyword yang dimatch                                   |                        | ⬜    |
| MD-026   | Agent/Admin mencoba akses chatbot responses management  | Error 403 atau halaman tidak ditemukan (hanya Super Admin)                    |                        | ⬜    |

---

## 7. Profile Management Testing

### 7.1 View/Update Profile

| Kode Tes    | Test Case                                                                           | Output yang Diharapkan                                                         | Output yang Sebenarnya | Hasil |
| ----------- | ----------------------------------------------------------------------------------- | ------------------------------------------------------------------------------ | ---------------------- | ----- |
| PROFILE-001 | Melihat profil sendiri                                                              | Detail profil ditampilkan: name, email, phone, telegram_username, organization |                        | ⬜    |
| PROFILE-002 | Update profil dengan data valid (nama, email, phone, telegram username, organisasi) | Profil ter-update, redirect dengan pesan sukses                                |                        | ⬜    |
| PROFILE-003 | Update email dengan email yang sudah digunakan user lain                            | Validasi error: "Email sudah digunakan"                                        |                        | ⬜    |
| PROFILE-004 | Update email dengan email baru valid                                                | Email ter-update, mungkin perlu verifikasi ulang                               |                        | ⬜    |

### 7.2 Change Password

| Kode Tes    | Test Case                                                   | Output yang Diharapkan                                       | Output yang Sebenarnya | Hasil |
| ----------- | ----------------------------------------------------------- | ------------------------------------------------------------ | ---------------------- | ----- |
| PROFILE-005 | Change password dengan current password benar               | Password berhasil diubah, perlu login ulang atau tetap login |                        | ⬜    |
| PROFILE-006 | Change password dengan current password salah               | Validasi error: "Password saat ini salah"                    |                        | ⬜    |
| PROFILE-007 | Change password dengan password baru kurang dari 8 karakter | Validasi error: "Password minimal 8 karakter"                |                        | ⬜    |
| PROFILE-008 | Change password dengan password confirmation tidak cocok    | Validasi error: "Password konfirmasi tidak cocok"            |                        | ⬜    |

### 7.3 Telegram Integration

| Kode Tes    | Test Case                                                      | Output yang Diharapkan                                        | Output yang Sebenarnya | Hasil |
| ----------- | -------------------------------------------------------------- | ------------------------------------------------------------- | ---------------------- | ----- |
| PROFILE-009 | Set telegram username dengan format valid                      | Telegram username tersimpan (format: @username atau username) |                        | ⬜    |
| PROFILE-010 | Telegram chat_id ter-update otomatis saat user chat dengan bot | Chat ID tersimpan saat user memulai chat dengan bot           |                        | ⬜    |
| PROFILE-011 | Set telegram username dengan format tidak valid                | Validasi error atau auto-format (sesuai implementasi)         |                        | ⬜    |

### 7.4 Delete Account (User Only)

| Kode Tes    | Test Case                                              | Output yang Diharapkan                                                                                         | Output yang Sebenarnya | Hasil |
| ----------- | ------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------- | ---------------------- | ----- |
| PROFILE-012 | User menghapus akun sendiri dengan konfirmasi password | Password dikonfirmasi terlebih dahulu, akun terhapus, redirect ke welcome, tiket tetap ada dengan user_id null |                        | ⬜    |
| PROFILE-013 | Agent/Admin menghapus akun sendiri                     | Tidak bisa atau perlu akses khusus (sesuai business rule)                                                      |                        | ⬜    |

---

## 8. Dashboard & Statistics Testing

### 8.1 Statistik Tiket (Agent Dashboard)

| Kode Tes | Test Case                                           | Output yang Diharapkan                             | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------------------------- | -------------------------------------------------- | ---------------------- | ----- |
| DASH-001 | Statistik total tiket, open, assigned, closed       | Statistik akurat sesuai database                   |                        | ⬜    |
| DASH-002 | Statistik tiket berdasarkan priority dan department | Breakdown tiket per priority dan department akurat |                        | ⬜    |
| DASH-003 | Statistik tiket overdue (melewati due_at)           | Jumlah tiket overdue akurat                        |                        | ⬜    |
| DASH-004 | Chart/graph statistik tiket (jika ada)              | Chart ditampilkan dengan benar                     |                        | ⬜    |

### 8.2 Admin Dashboard Statistics

| Kode Tes | Test Case                                              | Output yang Diharapkan                                   | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------------ | -------------------------------------------------------- | ---------------------- | ----- |
| DASH-005 | Admin melihat statistik global (semua tiket)           | Statistik semua tiket ditampilkan (bukan hanya assigned) |                        | ⬜    |
| DASH-006 | Statistik per agent (performa agent)                   | Breakdown performa per agent ditampilkan                 |                        | ⬜    |
| DASH-007 | Statistik response time dan resolution time (jika ada) | Rata-rata response time dan resolution time ditampilkan  |                        | ⬜    |

---

## 9. Chatbot Testing

| Kode Tes | Test Case                                                    | Output yang Diharapkan                            | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------------------------------ | ------------------------------------------------- | ---------------------- | ----- |
| CHAT-001 | Mengirim pesan ke chatbot (public) yang match dengan keyword | Chatbot merespons sesuai keyword yang match       |                        | ⬜    |
| CHAT-002 | Mengirim pesan yang tidak match                              | Response default atau "Maaf, saya tidak mengerti" |                        | ⬜    |
| CHAT-003 | Case-insensitive keyword matching                            | Keyword matching tidak case-sensitive             |                        | ⬜    |
| CHAT-004 | Chatbot response dengan placeholder variable                 | Variable ter-replace dengan nilai yang sesuai     |                        | ⬜    |
| CHAT-005 | Multiple keyword untuk satu response                         | Semua keyword mengarah ke response yang sama      |                        | ⬜    |
| CHAT-006 | Chatbot accessible tanpa autentikasi                         | Chatbot dapat diakses oleh guest/user tanpa login |                        | ⬜    |

---

## 10. Notification Testing

### 10.1 Email Notification

| Kode Tes  | Test Case                                                 | Output yang Diharapkan                                      | Output yang Sebenarnya | Hasil |
| --------- | --------------------------------------------------------- | ----------------------------------------------------------- | ---------------------- | ----- |
| NOTIF-001 | Email notifikasi saat tiket baru dibuat (requester)       | Email "NewTicketSubmitted" terkirim ke requester            |                        | ⬜    |
| NOTIF-002 | Email notifikasi saat tiket baru dibuat (admin/agent)     | Email "NewTicketCreated" terkirim ke admin/agent            |                        | ⬜    |
| NOTIF-003 | Email notifikasi saat tiket di-assign                     | Email "TicketAssigned" terkirim ke agent yang di-assign     |                        | ⬜    |
| NOTIF-004 | Email notifikasi saat agent reply                         | Email "TicketReplyFromAgent" terkirim ke requester          |                        | ⬜    |
| NOTIF-005 | Email notifikasi saat requester reply                     | Email "TicketReplyFromRequester" terkirim ke assigned agent |                        | ⬜    |
| NOTIF-006 | Email notifikasi saat status tiket berubah                | Email "TicketStatusChanged" terkirim ke requester           |                        | ⬜    |
| NOTIF-007 | Email notifikasi saat user register/verifikasi            | Email "UserRegistered" atau verifikasi email terkirim       |                        | ⬜    |
| NOTIF-008 | Format email notification sesuai template dengan variable | Email menggunakan template yang benar, variable ter-replace |                        | ⬜    |
| NOTIF-009 | Email dengan attachment info                              | Informasi attachment ditampilkan di email                   |                        | ⬜    |

### 10.2 Telegram Notification

| Kode Tes  | Test Case                                                                                      | Output yang Diharapkan                                           | Output yang Sebenarnya | Hasil |
| --------- | ---------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- | ---------------------- | ----- |
| NOTIF-010 | Telegram notifikasi saat tiket baru/assign/reply/status berubah (user dengan telegram_chat_id) | Notifikasi Telegram terkirim ke user yang punya telegram_chat_id |                        | ⬜    |
| NOTIF-011 | User tanpa telegram_chat_id tidak menerima notifikasi Telegram                                 | Tidak ada error, hanya email yang terkirim                       |                        | ⬜    |
| NOTIF-012 | Format notifikasi Telegram sesuai template                                                     | Pesan Telegram menggunakan format yang benar                     |                        | ⬜    |
| NOTIF-013 | Telegram bot webhook menerima update                                                           | Webhook berfungsi, update dari Telegram diproses                 |                        | ⬜    |
| NOTIF-014 | User chat dengan bot untuk set chat_id                                                         | Chat ID tersimpan otomatis saat user memulai chat                |                        | ⬜    |

---

## 11. Security & Authorization Testing

### 11.1 Role-Based Access Control (RBAC)

| Kode Tes | Test Case                            | Output yang Diharapkan                   | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------ | ---------------------------------------- | ---------------------- | ----- |
| SEC-001  | User biasa mengakses agent dashboard | Error 403 atau redirect ke welcome       |                        | ⬜    |
| SEC-002  | Agent mengakses admin panel          | Error 403 atau redirect                  |                        | ⬜    |
| SEC-003  | Admin mengakses Super Admin features | Error 403 atau fitur tidak tersedia      |                        | ⬜    |
| SEC-004  | Super Admin mengakses semua fitur    | Semua fitur dapat diakses                |                        | ⬜    |
| SEC-005  | Support Agent akses fitur terbatas   | Hanya fitur yang diizinkan dapat diakses |                        | ⬜    |

### 11.2 Permission-Based Access Control

| Kode Tes | Test Case                                             | Output yang Diharapkan             | Output yang Sebenarnya | Hasil |
| -------- | ----------------------------------------------------- | ---------------------------------- | ---------------------- | ----- |
| SEC-006  | Agent tanpa permission tickets.assign mencoba assign  | Error 403 atau tombol tidak muncul |                        | ⬜    |
| SEC-007  | Agent dengan permission tickets.assign bisa assign    | Assign tiket berhasil              |                        | ⬜    |
| SEC-008  | User tanpa admin.panel permission mengakses dashboard | Error 403                          |                        | ⬜    |

### 11.3 Data Ownership

| Kode Tes | Test Case                            | Output yang Diharapkan                  | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------ | --------------------------------------- | ---------------------- | ----- |
| SEC-009  | User mengakses tiket milik user lain | Error 403 atau redirect                 |                        | ⬜    |
| SEC-010  | User hanya melihat tiket sendiri     | Hanya tiket milik user yang ditampilkan |                        | ⬜    |
| SEC-011  | Agent melihat semua tiket            | Agent dapat melihat semua tiket         |                        | ⬜    |

### 11.4 Session Management

| Kode Tes | Test Case                            | Output yang Diharapkan                                          | Output yang Sebenarnya | Hasil |
| -------- | ------------------------------------ | --------------------------------------------------------------- | ---------------------- | ----- |
| SEC-012  | Session timeout                      | User logout otomatis setelah session expired                    |                        | ⬜    |
| SEC-013  | Multiple session handling (jika ada) | Sesi terbaru aktif, sesi lama invalid (atau sesuai konfigurasi) |                        | ⬜    |
| SEC-014  | Session check endpoint               | Endpoint /session/check mengembalikan status session            |                        | ⬜    |

### 11.5 Input Security (XSS, SQLi, CSRF, File Upload)

| Kode Tes | Test Case                         | Output yang Diharapkan                                   | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------- | -------------------------------------------------------- | ---------------------- | ----- |
| SEC-015  | XSS attack pada input form        | Input di-sanitize, script tidak dieksekusi               |                        | ⬜    |
| SEC-016  | SQL injection pada input          | Query parameterized, tidak ada SQL injection             |                        | ⬜    |
| SEC-017  | CSRF protection pada form         | Form memiliki CSRF token, request tanpa token ditolak    |                        | ⬜    |
| SEC-018  | File upload dengan malicious file | File yang tidak valid ditolak, hanya tipe yang diizinkan |                        | ⬜    |

---

## 12. Edge Cases & Error Handling Testing

### 12.1 Edge Cases

| Kode Tes | Test Case                                                | Output yang Diharapkan                                                             | Output yang Sebenarnya | Hasil |
| -------- | -------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------- | ----- |
| EDGE-001 | Create tiket saat tidak ada help topic                   | Error atau pesan: "Tidak ada help topic tersedia"                                  |                        | ⬜    |
| EDGE-002 | Create tiket saat tidak ada SLA plan                     | Error atau menggunakan default SLA                                                 |                        | ⬜    |
| EDGE-003 | Assign tiket ke agent yang sudah dihapus                 | Validasi error: "Agent tidak ditemukan"                                            |                        | ⬜    |
| EDGE-004 | Reply tiket yang sudah closed                            | Pesan error atau tetap bisa reply (sesuai business rule)                           |                        | ⬜    |
| EDGE-005 | Upload banyak file (>10 files)                           | Validasi error atau batasan jumlah file                                            |                        | ⬜    |
| EDGE-006 | Upload file dengan ukuran besar atau nama sangat panjang | File tetap tersimpan atau error ditangani dengan baik, nama di-truncate jika perlu |                        | ⬜    |
| EDGE-007 | Search dengan karakter khusus                            | Search tetap berfungsi atau error ditangani dengan baik                            |                        | ⬜    |

### 12.2 Error Handling (404, 500, DB Down, Service Down, Storage Full)

| Kode Tes | Test Case                               | Output yang Diharapkan                                            | Output yang Sebenarnya | Hasil |
| -------- | --------------------------------------- | ----------------------------------------------------------------- | ---------------------- | ----- |
| ERR-001  | Mengakses route yang tidak ada          | Error 404 dengan halaman not found yang user-friendly             |                        | ⬜    |
| ERR-002  | Mengakses resource yang tidak ada (404) | Error 404 dengan pesan yang jelas                                 |                        | ⬜    |
| ERR-003  | Server error (500)                      | Error 500 dengan pesan umum, detail error di log                  |                        | ⬜    |
| ERR-004  | Database connection error               | Error ditangani dengan baik, pesan user-friendly, error di-log    |                        | ⬜    |
| ERR-005  | Email service down                      | Notifikasi email gagal, error di-log, aplikasi tetap berfungsi    |                        | ⬜    |
| ERR-006  | Telegram bot API error                  | Notifikasi Telegram gagal, error di-log, aplikasi tetap berfungsi |                        | ⬜    |
| ERR-007  | File upload error (disk full)           | Error ditangani, pesan user-friendly, error di-log                |                        | ⬜    |

---

## 📊 Summary Testing

| Kategori                    | Total Test Cases | Passed | Failed | Not Tested |
| --------------------------- | ---------------- | ------ | ------ | ---------- |
| Authentication              | 23               |        |        | 23         |
| Guest (Publik) Features     | 13               |        |        | 13         |
| Portal User (Authenticated) | 18               |        |        | 18         |
| Agent Features              | 29               |        |        | 29         |
| User Management             | 19               |        |        | 19         |
| Master Data Management      | 26               |        |        | 26         |
| Profile Management          | 13               |        |        | 13         |
| Dashboard & Statistics      | 7                |        |        | 7          |
| Chatbot                     | 6                |        |        | 6          |
| Notifications               | 14               |        |        | 14         |
| Security & Authorization    | 18               |        |        | 18         |
| Edge Cases & Error Handling | 14               |        |        | 14         |
| **TOTAL**                   | **200**          | **0**  | **0**  | **200**    |

---

## 📝 Catatan Testing

### Environment Testing

-   **Database**: SQLite/MySQL/PostgreSQL (sesuai konfigurasi)
-   **PHP Version**:
-   **Laravel Version**:
-   **Browser**: Chrome, Firefox, Edge (untuk UI testing)

### Pre-requisite Testing

1. Database sudah di-migrate dan di-seed
2. Storage link sudah dibuat (`php artisan storage:link`)
3. Email configuration sudah di-setup
4. Telegram bot sudah dikonfigurasi
5. User test sudah dibuat dengan berbagai role

### Test Data yang Diperlukan

-   User dengan role: Super Admin, Admin, Agent, Support Agent, User
-   Master data: Departments, Help Topics, Statuses, Priorities, SLA Plans, Teams, Organizations
-   Sample tickets dengan berbagai status dan priority
-   Canned responses dan chatbot responses

### Cara Menggunakan Dokumen Ini

1. **Kode Tes**: Gunakan untuk tracking dan referensi saat testing
2. **Test Case**: Deskripsi lengkap apa yang akan ditest
3. **Output yang Diharapkan**: Hasil yang seharusnya terjadi
4. **Output yang Sebenarnya**: Tulis hasil aktual saat testing (isi manual)
5. **Hasil**:
    - ✅ Pass (jika sesuai expected)
    - ❌ Fail (jika tidak sesuai expected)
    - ⬜ Not Tested (belum ditest)

### Tips Testing

-   Test satu fitur secara lengkap sebelum pindah ke fitur lain
-   Dokumentasikan semua bug/issue yang ditemukan
-   Screenshot untuk bug yang ditemukan
-   Test dengan berbagai role dan permission
-   Test edge cases dan error scenarios
-   Verifikasi notifikasi (email & Telegram) benar-benar terkirim

---

**Dibuat**: [Tanggal]  
**Tester**: [Nama Tester]  
**Version**: 2.0
