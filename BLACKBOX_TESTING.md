# Blackbox Testing - OS-Tiket (CSIRT Kalselprov)

## 📋 Daftar Isi

1. [Authentication Testing](#1-authentication-testing)
2. [Guest Features Testing](#2-guest-features-testing)
3. [Portal - User Features Testing](#3-portal---user-features-testing)
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

### 1.1 Register (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AUTH-001 | Mengakses halaman register sebagai guest | Halaman register ditampilkan dengan form (name, email, password, password confirmation) | | ⬜ |
| AUTH-002 | Register dengan data valid | User berhasil dibuat, redirect ke halaman verifikasi email atau dashboard, pesan sukses ditampilkan | | ⬜ |
| AUTH-003 | Register dengan email yang sudah terdaftar | Validasi error: "Email sudah digunakan", form tidak submit | | ⬜ |
| AUTH-004 | Register dengan password kurang dari 8 karakter | Validasi error: "Password minimal 8 karakter" | | ⬜ |
| AUTH-005 | Register dengan password confirmation tidak sama | Validasi error: "Password konfirmasi tidak cocok" | | ⬜ |
| AUTH-006 | Register dengan email tidak valid | Validasi error: "Format email tidak valid" | | ⬜ |
| AUTH-007 | Register dengan field wajib kosong | Validasi error untuk setiap field yang kosong | | ⬜ |

### 1.2 Login (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AUTH-008 | Mengakses halaman login sebagai guest | Halaman login ditampilkan dengan form (email, password) | | ⬜ |
| AUTH-009 | Login dengan kredensial valid (User biasa) | Login berhasil, redirect ke halaman welcome/portal, session terbuat | | ⬜ |
| AUTH-010 | Login dengan kredensial valid (Agent/Admin) | Login berhasil, redirect ke dashboard agent/admin sesuai role | | ⬜ |
| AUTH-011 | Login dengan email tidak terdaftar | Validasi error: "Kredensial tidak valid" atau "Email tidak ditemukan" | | ⬜ |
| AUTH-012 | Login dengan password salah | Validasi error: "Kredensial tidak valid" atau "Password salah" | | ⬜ |
| AUTH-013 | Login dengan field kosong | Validasi error untuk email dan password | | ⬜ |
| AUTH-014 | Login dengan akun yang belum verifikasi email | Pesan bahwa email perlu diverifikasi terlebih dahulu | | ⬜ |

### 1.3 Logout (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AUTH-015 | Logout dari sistem (User, Agent, Admin) | Session dihapus, redirect ke halaman welcome, pesan logout berhasil | | ⬜ |
| AUTH-016 | Logout saat masih ada tiket yang dibuka | Session dihapus, redirect ke welcome | | ⬜ |

### 1.4 Forgot Password (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AUTH-017 | Mengakses halaman forgot password | Form reset password ditampilkan (field email) | | ⬜ |
| AUTH-018 | Request reset password dengan email valid | Email reset password dikirim, pesan sukses ditampilkan | | ⬜ |
| AUTH-019 | Request reset password dengan email tidak terdaftar | Pesan: "Email tidak ditemukan" atau tetap menampilkan pesan sukses (security) | | ⬜ |
| AUTH-020 | Request reset password dengan email tidak valid | Validasi error: "Format email tidak valid" | | ⬜ |
| AUTH-021 | Mengakses link reset password yang valid | Form reset password dengan field (email, password, password confirmation) ditampilkan | | ⬜ |
| AUTH-022 | Mengakses link reset password yang expired/invalid | Pesan error: "Link reset password tidak valid atau sudah kedaluwarsa" | | ⬜ |
| AUTH-023 | Reset password dengan data valid | Password berhasil diubah, redirect ke login, pesan sukses | | ⬜ |
| AUTH-024 | Reset password dengan password kurang dari 8 karakter | Validasi error: "Password minimal 8 karakter" | | ⬜ |
| AUTH-025 | Reset password dengan password confirmation tidak cocok | Validasi error: "Password konfirmasi tidak cocok" | | ⬜ |

### 1.5 Verify Email (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AUTH-026 | User baru belum verifikasi email | Pesan verifikasi email ditampilkan, tombol kirim ulang email tersedia | | ⬜ |
| AUTH-027 | Klik link verifikasi email yang valid | Email berhasil diverifikasi, redirect ke dashboard/welcome | | ⬜ |
| AUTH-028 | Klik link verifikasi email yang expired/invalid | Pesan error: "Link verifikasi tidak valid" | | ⬜ |
| AUTH-029 | Request kirim ulang email verifikasi | Email verifikasi dikirim ulang, pesan sukses | | ⬜ |

---

## 2. Guest Features Testing

### 2.1 Landing Page (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| GUEST-001 | Mengakses halaman utama (/) | Halaman welcome/landing page ditampilkan dengan informasi CSIRT | | ⬜ |
| GUEST-002 | Mengakses landing page saat sudah login | Redirect sesuai role (user → welcome, agent → dashboard) | | ⬜ |
| GUEST-003 | Melihat informasi tentang CSIRT di landing page | Informasi CSIRT ditampilkan dengan jelas | | ⬜ |
| GUEST-004 | Melihat link menu navigasi (About, Help Topics, Departments) | Menu navigasi ditampilkan dan dapat diklik | | ⬜ |

### 2.2 View Help Topics (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| GUEST-005 | Melihat daftar help topics/kategori insiden | Daftar help topics ditampilkan dengan nama dan deskripsi | | ⬜ |
| GUEST-006 | Melihat help topics yang sudah dibuat | Semua help topics aktif ditampilkan | | ⬜ |

### 2.3 View Departments (Guest)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| GUEST-007 | Melihat daftar departemen | Daftar departemen ditampilkan dengan nama dan informasi | | ⬜ |
| GUEST-008 | Melihat departemen yang sudah dibuat | Semua departemen aktif ditampilkan | | ⬜ |

### 2.4 Check Ticket Status (Guest - Public)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| GUEST-009 | Mengakses halaman check ticket status | Form dengan field ticket number dan email ditampilkan | | ⬜ |
| GUEST-010 | Cek status dengan nomor tiket dan email valid | Detail tiket ditampilkan (terbatas, tanpa balasan internal), status dan informasi dasar ditampilkan | | ⬜ |
| GUEST-011 | Cek status dengan nomor tiket valid tapi email tidak cocok | Pesan error: "Email tidak cocok dengan tiket" atau "Tiket tidak ditemukan" | | ⬜ |
| GUEST-012 | Cek status dengan nomor tiket tidak valid | Pesan error: "Tiket tidak ditemukan" | | ⬜ |
| GUEST-013 | Cek status dengan field kosong | Validasi error untuk field yang kosong | | ⬜ |
| GUEST-014 | Melihat detail tiket public (setelah verifikasi) | Informasi dasar tiket ditampilkan, tanpa thread/balasan internal | | ⬜ |

---

## 3. Portal - User Features Testing

### 3.1 Create Ticket (User - Authenticated)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PORTAL-001 | Mengakses halaman create ticket saat sudah login | Form create ticket ditampilkan dengan field: subject, help_topic, priority, message, attachments | | ⬜ |
| PORTAL-002 | Membuat tiket dengan data valid lengkap | Tiket berhasil dibuat, nomor tiket di-generate (format: OST-XXXXXX), redirect ke detail tiket, notifikasi email & telegram terkirim | | ⬜ |
| PORTAL-003 | Membuat tiket tanpa priority (optional) | Tiket berhasil dibuat dengan priority null/default | | ⬜ |
| PORTAL-004 | Membuat tiket dengan attachment valid (jpg, png, pdf, doc, docx, max 10MB) | Tiket berhasil dibuat, attachment tersimpan di storage, dapat diunduh | | ⬜ |
| PORTAL-005 | Membuat tiket dengan attachment lebih dari 10MB | Validasi error: "Ukuran file maksimal 10MB" | | ⬜ |
| PORTAL-006 | Membuat tiket dengan attachment tipe tidak valid | Validasi error: "Tipe file tidak diperbolehkan" | | ⬜ |
| PORTAL-007 | Membuat tiket dengan subject kosong | Validasi error: "Subject wajib diisi" | | ⬜ |
| PORTAL-008 | Membuat tiket dengan subject lebih dari 255 karakter | Validasi error: "Subject maksimal 255 karakter" | | ⬜ |
| PORTAL-009 | Membuat tiket tanpa help_topic | Validasi error: "Help topic wajib dipilih" | | ⬜ |
| PORTAL-010 | Membuat tiket dengan message kosong | Validasi error: "Message wajib diisi" | | ⬜ |
| PORTAL-011 | Membuat tiket dengan message lebih dari 20000 karakter | Validasi error: "Message maksimal 20000 karakter" | | ⬜ |
| PORTAL-012 | Membuat tiket multiple attachment | Semua attachment berhasil diupload dan tersimpan | | ⬜ |
| PORTAL-013 | Status tiket baru adalah "open" | Status tiket yang baru dibuat adalah "open" | | ⬜ |
| PORTAL-014 | SLA Plan dan due_at ter-set otomatis | Due date dihitung berdasarkan SLA plan (grace_hours) | | ⬜ |

### 3.2 View My Tickets (User - Authenticated)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PORTAL-015 | Melihat daftar tiket sendiri | Daftar tiket yang dibuat oleh user ditampilkan dengan informasi: nomor, subject, status, tanggal | | ⬜ |
| PORTAL-016 | Melihat daftar tiket saat belum ada tiket | Pesan: "Belum ada tiket" atau tabel kosong | | ⬜ |
| PORTAL-017 | Melihat daftar tiket dengan filter/pagination | Daftar tiket ditampilkan dengan pagination atau filter | | ⬜ |
| PORTAL-018 | User tidak bisa melihat tiket user lain | Hanya tiket milik sendiri yang ditampilkan | | ⬜ |

### 3.3 View Ticket Detail (User - Own Ticket)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PORTAL-019 | Melihat detail tiket sendiri | Detail lengkap tiket ditampilkan: nomor, subject, status, priority, semua thread/balasan, attachment | | ⬜ |
| PORTAL-020 | Melihat detail tiket dengan multiple threads | Semua thread/balasan ditampilkan dalam urutan kronologis | | ⬜ |
| PORTAL-021 | Melihat attachment di detail tiket | Attachment dapat diunduh dan ditampilkan | | ⬜ |
| PORTAL-022 | Mencoba akses tiket milik user lain | Error 403 atau redirect, pesan: "Tidak memiliki akses" | | ⬜ |
| PORTAL-023 | Melihat status update di timeline | Status changes ditampilkan di timeline/thread | | ⬜ |

### 3.4 Reply Ticket (User - Own Ticket)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PORTAL-024 | Membalas tiket sendiri dengan message valid | Balasan berhasil ditambahkan sebagai thread, status berubah ke "answered" (jika belum), notifikasi ke assigned agent | | ⬜ |
| PORTAL-025 | Membalas tiket dengan attachment | Attachment berhasil diupload dan tersimpan | | ⬜ |
| PORTAL-026 | Membalas tiket dengan message kosong | Validasi error: "Message wajib diisi" | | ⬜ |
| PORTAL-027 | Membalas tiket yang sudah closed | Pesan error: "Tidak dapat membalas tiket yang sudah ditutup" atau tetap bisa reply (sesuai business rule) | | ⬜ |
| PORTAL-028 | Status tiket berubah ke "answered" setelah reply | Status tiket berubah sesuai business rule | | ⬜ |
| PORTAL-029 | Mencoba membalas tiket milik user lain | Error 403 atau redirect | | ⬜ |

---

## 4. Agent Features Testing

### 4.1 Agent Dashboard

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-001 | Mengakses dashboard agent (role Agent/Admin) | Dashboard agent ditampilkan dengan statistik: total tiket, open, assigned, dll | | ⬜ |
| AGENT-002 | Melihat statistik tiket di dashboard | Statistik akurat sesuai data di database | | ⬜ |
| AGENT-003 | User tanpa permission admin.panel mengakses dashboard | Error 403 atau redirect ke welcome | | ⬜ |
| AGENT-004 | Dashboard menampilkan tiket terbaru | List tiket terbaru ditampilkan di dashboard | | ⬜ |

### 4.2 View Tickets (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-005 | Melihat daftar semua tiket (Agent) | Semua tiket ditampilkan dengan filter: all, open, assigned, closed | | ⬜ |
| AGENT-006 | Melihat daftar tiket yang di-assign ke agent | Hanya tiket yang assigned_to = agent_id ditampilkan | | ⬜ |
| AGENT-007 | Filter tiket berdasarkan status | Daftar tiket terfilter sesuai status yang dipilih | | ⬜ |
| AGENT-008 | Filter tiket berdasarkan priority | Daftar tiket terfilter sesuai priority yang dipilih | | ⬜ |
| AGENT-009 | Search tiket berdasarkan nomor atau subject | Hasil pencarian ditampilkan sesuai keyword | | ⬜ |
| AGENT-010 | Pagination pada daftar tiket | Daftar tiket dipaginate dengan benar | | ⬜ |

### 4.3 View Ticket Detail (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-011 | Melihat detail tiket (Agent) | Detail lengkap tiket ditampilkan termasuk semua thread, notes, attachment, history | | ⬜ |
| AGENT-012 | Melihat semua thread (reply dan note) | Thread ditampilkan dengan pembedaan reply (public) dan note (internal) | | ⬜ |
| AGENT-013 | Melihat attachment di detail tiket | Semua attachment dapat diunduh | | ⬜ |
| AGENT-014 | Melihat informasi assignee di detail tiket | Informasi agent yang di-assign ditampilkan | | ⬜ |

### 4.4 Reply Ticket (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-015 | Membalas tiket sebagai agent dengan message valid | Balasan berhasil ditambahkan sebagai thread type "reply", notifikasi ke requester (email & telegram) | | ⬜ |
| AGENT-016 | Membalas tiket menggunakan canned response | Template canned response di-load ke form, dapat diedit sebelum submit | | ⬜ |
| AGENT-017 | Membalas tiket dengan attachment | Attachment berhasil diupload | | ⬜ |
| AGENT-018 | Membalas tiket dengan message kosong | Validasi error: "Message wajib diisi" | | ⬜ |
| AGENT-019 | Status tiket berubah setelah reply agent | Status berubah sesuai business rule (mis: open → in-progress) | | ⬜ |
| AGENT-020 | Support Agent tidak bisa reply tiket | Error 403 atau tombol reply tidak muncul | | ⬜ |

### 4.5 Assign Ticket (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-021 | Assign tiket ke agent lain (permission tickets.assign) | Tiket berhasil di-assign, assigned_to ter-update, notifikasi ke agent yang di-assign | | ⬜ |
| AGENT-022 | Assign tiket ke diri sendiri | Tiket berhasil di-assign ke agent sendiri | | ⬜ |
| AGENT-023 | Agent tanpa permission tickets.assign mencoba assign | Error 403 atau tombol assign tidak muncul | | ⬜ |
| AGENT-024 | Assign tiket dengan agent yang dipilih valid | Assignment berhasil, history assignment tercatat | | ⬜ |
| AGENT-025 | Assign tiket yang sudah closed | Pesan error: "Tidak dapat assign tiket yang sudah ditutup" atau tetap bisa assign (sesuai business rule) | | ⬜ |

### 4.6 Update Status (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-026 | Update status tiket (Agent) | Status tiket ter-update, closed_at ter-set jika status adalah closing, notifikasi ke requester | | ⬜ |
| AGENT-027 | Update status ke "closed" | Status closed, closed_at ter-set, tiket tidak bisa diupdate lagi (atau sesuai business rule) | | ⬜ |
| AGENT-028 | Update status ke status tidak valid | Validasi error: "Status tidak valid" | | ⬜ |
| AGENT-029 | Support Agent update status (terbatas) | Support Agent hanya bisa update ke status tertentu sesuai permission | | ⬜ |
| AGENT-030 | Status update tercatat di history | Perubahan status tercatat di timeline/history | | ⬜ |

### 4.7 Add Note (Agent - Internal)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-031 | Menambahkan internal note pada tiket | Note berhasil ditambahkan sebagai thread type "note", hanya visible untuk agent/admin, tidak ada notifikasi ke requester | | ⬜ |
| AGENT-032 | Menambahkan note dengan message kosong | Validasi error: "Note wajib diisi" | | ⬜ |
| AGENT-033 | Note tidak terlihat oleh requester | Requester tidak melihat note saat melihat detail tiket | | ⬜ |
| AGENT-034 | Support Agent tidak bisa add note | Error 403 atau tombol add note tidak muncul | | ⬜ |

### 4.8 Update Priority (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-035 | Update priority tiket | Priority tiket ter-update, perubahan tercatat | | ⬜ |
| AGENT-036 | Update priority dengan nilai tidak valid | Validasi error: "Priority tidak valid" | | ⬜ |

### 4.9 Use Canned Response (Agent)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| AGENT-037 | Melihat daftar canned responses | Daftar canned responses ditampilkan | | ⬜ |
| AGENT-038 | Menggunakan canned response saat reply | Template di-load ke form reply, dapat diedit | | ⬜ |
| AGENT-039 | Canned response dengan placeholder variable | Variable di-replace dengan nilai yang sesuai | | ⬜ |

---

## 5. Admin/Super Admin - User Management Testing

### 5.1 View Users (Admin/Super Admin)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| ADMIN-001 | Melihat daftar semua user (Super Admin) | Daftar user ditampilkan dengan informasi: name, email, role, organization | | ⬜ |
| ADMIN-002 | Melihat daftar user (Admin) | Daftar user ditampilkan (Admin bisa view) | | ⬜ |
| ADMIN-003 | Agent mencoba akses user management | Error 403 atau halaman tidak ditemukan | | ⬜ |
| ADMIN-004 | Search user berdasarkan name/email | Hasil pencarian ditampilkan sesuai keyword | | ⬜ |
| ADMIN-005 | Filter user berdasarkan role | Daftar user terfilter sesuai role | | ⬜ |
| ADMIN-006 | Pagination pada daftar user | Daftar user dipaginate | | ⬜ |

### 5.2 Create User (Admin/Super Admin)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| ADMIN-007 | Membuat user baru dengan data valid | User berhasil dibuat, role ter-assign, redirect ke index dengan pesan sukses | | ⬜ |
| ADMIN-008 | Membuat user dengan email yang sudah ada | Validasi error: "Email sudah digunakan" | | ⬜ |
| ADMIN-009 | Membuat user dengan password kurang dari 8 karakter | Validasi error: "Password minimal 8 karakter" | | ⬜ |
| ADMIN-010 | Membuat user dengan role tidak valid | Validasi error: "Role tidak valid" | | ⬜ |
| ADMIN-011 | Membuat user dengan organization (optional) | User berhasil dibuat dengan organization ter-assign | | ⬜ |
| ADMIN-012 | Membuat user dengan field wajib kosong | Validasi error untuk setiap field wajib | | ⬜ |
| ADMIN-013 | Admin bisa create user | Admin berhasil membuat user baru | | ⬜ |

### 5.3 Update User (Admin/Super Admin)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| ADMIN-014 | Update data user (Super Admin) | Data user ter-update, redirect dengan pesan sukses | | ⬜ |
| ADMIN-015 | Update data user (Admin) | Admin bisa update user | | ⬜ |
| ADMIN-016 | Update email user dengan email yang sudah digunakan user lain | Validasi error: "Email sudah digunakan" | | ⬜ |
| ADMIN-017 | Update password user (optional) | Password ter-update jika diisi, tetap jika kosong | | ⬜ |
| ADMIN-018 | Update role user | Role user ter-update | | ⬜ |
| ADMIN-019 | Update user dengan data valid | Semua field ter-update dengan benar | | ⬜ |

### 5.4 Delete User (Super Admin Only)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| ADMIN-020 | Delete user (Super Admin) | User berhasil dihapus, redirect dengan pesan sukses | | ⬜ |
| ADMIN-021 | Delete user sendiri | Error: "Tidak dapat menghapus akun sendiri" | | ⬜ |
| ADMIN-022 | Admin mencoba delete user | Error 403 atau tombol delete tidak muncul | | ⬜ |
| ADMIN-023 | Delete user yang memiliki tiket | User dihapus, tiket tetap ada (user_id menjadi null atau sesuai cascade rule) | | ⬜ |

---

## 6. Admin/Super Admin - Master Data Management Testing

### 6.1 Departments Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-001 | Melihat daftar departments | Daftar departments ditampilkan dengan CRUD actions | | ⬜ |
| MD-002 | Create department dengan data valid | Department berhasil dibuat, redirect dengan pesan sukses | | ⬜ |
| MD-003 | Create department dengan nama duplikat | Validasi error: "Nama department sudah ada" atau bisa duplikat (sesuai business rule) | | ⬜ |
| MD-004 | Update department | Department ter-update dengan benar | | ⬜ |
| MD-005 | Delete department (Super Admin only) | Department terhapus, atau error jika ada tiket terkait (cascade) | | ⬜ |
| MD-006 | Admin create/update department | Admin bisa create dan update (terbatas) | | ⬜ |
| MD-007 | Delete department dengan tiket terkait | Error cascade atau tiket tetap ada dengan department null | | ⬜ |

### 6.2 Help Topics Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-008 | Melihat daftar help topics | Daftar help topics ditampilkan | | ⬜ |
| MD-009 | Create help topic dengan department valid | Help topic berhasil dibuat dengan department ter-assign | | ⬜ |
| MD-010 | Create help topic tanpa department | Validasi error atau department nullable (sesuai business rule) | | ⬜ |
| MD-011 | Update help topic | Help topic ter-update | | ⬜ |
| MD-012 | Delete help topic | Help topic terhapus atau error cascade | | ⬜ |
| MD-013 | Help topic dengan custom form schema | Custom fields tersimpan dengan benar | | ⬜ |

### 6.3 Statuses Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-014 | Melihat daftar statuses | Daftar status ditampilkan dengan slug, name, color | | ⬜ |
| MD-015 | Create status dengan slug unique | Status berhasil dibuat | | ⬜ |
| MD-016 | Create status dengan slug duplikat | Validasi error: "Slug sudah digunakan" | | ⬜ |
| MD-017 | Update status | Status ter-update | | ⬜ |
| MD-018 | Delete status dengan tiket terkait | Error cascade atau status default digunakan | | ⬜ |
| MD-019 | Status dengan type (opening, closing, answered, dll) | Type status tersimpan dan digunakan untuk business logic | | ⬜ |

### 6.4 Priorities Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-020 | Melihat daftar priorities | Daftar priorities ditampilkan | | ⬜ |
| MD-021 | Create priority dengan level valid | Priority berhasil dibuat dengan level (1-5 atau sesuai) | | ⬜ |
| MD-022 | Create priority dengan level tidak valid | Validasi error untuk level | | ⬜ |
| MD-023 | Update priority | Priority ter-update | | ⬜ |
| MD-024 | Delete priority dengan tiket terkait | Error cascade atau priority null pada tiket | | ⬜ |

### 6.5 SLA Plans Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-025 | Melihat daftar SLA plans | Daftar SLA plans ditampilkan | | ⬜ |
| MD-026 | Create SLA plan dengan grace_hours valid | SLA plan berhasil dibuat | | ⬜ |
| MD-027 | Create SLA plan dengan grace_hours negatif | Validasi error: "Grace hours harus positif" | | ⬜ |
| MD-028 | Update SLA plan | SLA plan ter-update | | ⬜ |
| MD-029 | Delete SLA plan dengan tiket terkait | Error cascade atau SLA default digunakan | | ⬜ |
| MD-030 | SLA plan digunakan saat create tiket | Due_at dihitung berdasarkan grace_hours dari SLA plan | | ⬜ |

### 6.6 Teams Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-031 | Melihat daftar teams | Daftar teams ditampilkan | | ⬜ |
| MD-032 | Create team dengan data valid | Team berhasil dibuat | | ⬜ |
| MD-033 | Update team | Team ter-update | | ⬜ |
| MD-034 | Delete team | Team terhapus atau error cascade | | ⬜ |

### 6.7 Organizations Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-035 | Melihat daftar organizations | Daftar organizations ditampilkan | | ⬜ |
| MD-036 | Create organization dengan data valid | Organization berhasil dibuat | | ⬜ |
| MD-037 | Update organization | Organization ter-update | | ⬜ |
| MD-038 | Delete organization dengan user terkait | Error cascade atau organization null pada user | | ⬜ |
| MD-039 | Assign organization ke user | User memiliki organization | | ⬜ |

### 6.8 Canned Responses Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-040 | Melihat daftar canned responses | Daftar canned responses ditampilkan dengan title dan preview | | ⬜ |
| MD-041 | Create canned response dengan template valid | Canned response berhasil dibuat | | ⬜ |
| MD-042 | Create canned response dengan placeholder variables | Template dengan {ticket_number}, {requester_name} tersimpan | | ⬜ |
| MD-043 | Update canned response | Canned response ter-update | | ⬜ |
| MD-044 | Delete canned response | Canned response terhapus | | ⬜ |
| MD-045 | Agent menggunakan canned response | Template di-load dengan variable ter-replace | | ⬜ |

### 6.9 Chatbot Responses Management (Super Admin)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| MD-046 | Melihat daftar chatbot responses | Daftar chatbot responses ditampilkan dengan keyword dan response | | ⬜ |
| MD-047 | Create chatbot response dengan keyword dan response | Chatbot response berhasil dibuat | | ⬜ |
| MD-048 | Update chatbot response | Chatbot response ter-update | | ⬜ |
| MD-049 | Delete chatbot response | Chatbot response terhapus | | ⬜ |
| MD-050 | Chatbot menggunakan response saat user chat | Response sesuai dengan keyword yang dimatch | | ⬜ |

---

## 7. Profile Management Testing

### 7.1 View Profile (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PROFILE-001 | Melihat profil sendiri | Detail profil ditampilkan: name, email, phone, telegram_username, organization | | ⬜ |
| PROFILE-002 | User melihat profil sendiri | Informasi akurat sesuai data di database | | ⬜ |

### 7.2 Update Profile (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PROFILE-003 | Update profil dengan data valid | Profil ter-update, redirect dengan pesan sukses | | ⬜ |
| PROFILE-004 | Update name | Name ter-update | | ⬜ |
| PROFILE-005 | Update email dengan email baru valid | Email ter-update, mungkin perlu verifikasi ulang | | ⬜ |
| PROFILE-006 | Update email dengan email yang sudah digunakan user lain | Validasi error: "Email sudah digunakan" | | ⬜ |
| PROFILE-007 | Update phone | Phone ter-update | | ⬜ |
| PROFILE-008 | Update telegram_username | Telegram username ter-update | | ⬜ |
| PROFILE-009 | Update organization (jika bisa) | Organization ter-update atau tetap (sesuai business rule) | | ⬜ |

### 7.3 Change Password (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PROFILE-010 | Change password dengan current password benar | Password berhasil diubah, perlu login ulang atau tetap login | | ⬜ |
| PROFILE-011 | Change password dengan current password salah | Validasi error: "Password saat ini salah" | | ⬜ |
| PROFILE-012 | Change password dengan password baru kurang dari 8 karakter | Validasi error: "Password minimal 8 karakter" | | ⬜ |
| PROFILE-013 | Change password dengan password confirmation tidak cocok | Validasi error: "Password konfirmasi tidak cocok" | | ⬜ |

### 7.4 Set Telegram Username (Authenticated User)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PROFILE-014 | Set telegram username dengan format valid | Telegram username tersimpan (format: @username atau username) | | ⬜ |
| PROFILE-015 | Set telegram username dengan format tidak valid | Validasi error atau auto-format (sesuai implementasi) | | ⬜ |
| PROFILE-016 | Telegram chat_id ter-update otomatis saat user chat dengan bot | Chat ID tersimpan saat user memulai chat dengan bot | | ⬜ |

### 7.5 Delete Account (User Only)

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| PROFILE-017 | User menghapus akun sendiri | Akun terhapus, redirect ke welcome, tiket tetap ada dengan user_id null | | ⬜ |
| PROFILE-018 | User menghapus akun dengan konfirmasi password | Password dikonfirmasi terlebih dahulu sebelum hapus | | ⬜ |

---

## 8. Dashboard & Statistics Testing

### 8.1 Agent Dashboard Statistics

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| DASH-001 | Statistik total tiket | Total tiket akurat sesuai database | | ⬜ |
| DASH-002 | Statistik tiket open | Jumlah tiket dengan status open akurat | | ⬜ |
| DASH-003 | Statistik tiket assigned ke agent | Jumlah tiket assigned akurat per agent | | ⬜ |
| DASH-004 | Statistik tiket closed | Jumlah tiket closed akurat | | ⬜ |
| DASH-005 | Statistik tiket berdasarkan priority | Breakdown tiket per priority akurat | | ⬜ |
| DASH-006 | Statistik tiket berdasarkan department | Breakdown tiket per department akurat | | ⬜ |
| DASH-007 | Statistik tiket overdue (melewati due_at) | Jumlah tiket overdue akurat | | ⬜ |
| DASH-008 | Chart/graph statistik tiket | Chart ditampilkan dengan benar (jika ada) | | ⬜ |

### 8.2 Admin Dashboard Statistics

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| DASH-009 | Admin melihat statistik global | Statistik semua tiket ditampilkan (bukan hanya assigned) | | ⬜ |
| DASH-010 | Statistik per agent | Breakdown performa per agent ditampilkan | | ⬜ |
| DASH-011 | Statistik response time | Rata-rata response time ditampilkan | | ⬜ |
| DASH-012 | Statistik resolution time | Rata-rata resolution time ditampilkan | | ⬜ |

---

## 9. Chatbot Testing

### 9.1 Chatbot Public Access

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| CHAT-001 | Mengirim pesan ke chatbot (public) | Chatbot merespons sesuai keyword yang match | | ⬜ |
| CHAT-002 | Mengirim pesan yang match dengan keyword | Response sesuai dengan chatbot response yang terkait | | ⬜ |
| CHAT-003 | Mengirim pesan yang tidak match | Response default atau "Maaf, saya tidak mengerti" | | ⬜ |
| CHAT-004 | Chatbot response dengan placeholder variable | Variable ter-replace dengan nilai yang sesuai | | ⬜ |
| CHAT-005 | Multiple keyword untuk satu response | Semua keyword mengarah ke response yang sama | | ⬜ |
| CHAT-006 | Case-insensitive keyword matching | Keyword matching tidak case-sensitive | | ⬜ |

---

## 10. Notification Testing

### 10.1 Email Notification

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| NOTIF-001 | Email notifikasi saat tiket baru dibuat (requester) | Email "NewTicketSubmitted" terkirim ke requester | | ⬜ |
| NOTIF-002 | Email notifikasi saat tiket baru dibuat (admin/agent) | Email "NewTicketCreated" terkirim ke admin/agent | | ⬜ |
| NOTIF-003 | Email notifikasi saat tiket di-assign | Email "TicketAssigned" terkirim ke agent yang di-assign | | ⬜ |
| NOTIF-004 | Email notifikasi saat agent reply | Email "TicketReplyFromAgent" terkirim ke requester | | ⬜ |
| NOTIF-005 | Email notifikasi saat requester reply | Email "TicketReplyFromRequester" terkirim ke assigned agent | | ⬜ |
| NOTIF-006 | Email notifikasi saat status tiket berubah | Email "TicketStatusChanged" terkirim ke requester | | ⬜ |
| NOTIF-007 | Email notifikasi saat user register | Email "UserRegistered" atau verifikasi email terkirim | | ⬜ |
| NOTIF-008 | Format email notification sesuai template | Email menggunakan template yang benar, variable ter-replace | | ⬜ |
| NOTIF-009 | Email dengan attachment info | Informasi attachment ditampilkan di email | | ⬜ |

### 10.2 Telegram Notification

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| NOTIF-010 | Telegram notifikasi saat tiket baru dibuat (requester dengan telegram_chat_id) | Notifikasi Telegram terkirim ke user yang punya telegram_chat_id | | ⬜ |
| NOTIF-011 | Telegram notifikasi saat tiket di-assign (agent dengan telegram_chat_id) | Notifikasi Telegram terkirim ke agent | | ⬜ |
| NOTIF-012 | Telegram notifikasi saat agent reply | Notifikasi Telegram terkirim ke requester | | ⬜ |
| NOTIF-013 | Telegram notifikasi saat requester reply | Notifikasi Telegram terkirim ke agent | | ⬜ |
| NOTIF-014 | Telegram notifikasi saat status berubah | Notifikasi Telegram terkirim ke requester | | ⬜ |
| NOTIF-015 | User tanpa telegram_chat_id tidak menerima notifikasi Telegram | Tidak ada error, hanya email yang terkirim | | ⬜ |
| NOTIF-016 | Format notifikasi Telegram sesuai template | Pesan Telegram menggunakan format yang benar | | ⬜ |
| NOTIF-017 | Telegram bot webhook menerima update | Webhook berfungsi, update dari Telegram diproses | | ⬜ |
| NOTIF-018 | User chat dengan bot untuk set chat_id | Chat ID tersimpan otomatis saat user memulai chat | | ⬜ |

---

## 11. Security & Authorization Testing

### 11.1 Role-Based Access Control

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| SEC-001 | User biasa mengakses agent dashboard | Error 403 atau redirect ke welcome | | ⬜ |
| SEC-002 | Agent mengakses admin panel | Error 403 atau redirect | | ⬜ |
| SEC-003 | Admin mengakses Super Admin features | Error 403 atau fitur tidak tersedia | | ⬜ |
| SEC-004 | Super Admin mengakses semua fitur | Semua fitur dapat diakses | | ⬜ |
| SEC-005 | Support Agent akses fitur terbatas | Hanya fitur yang diizinkan dapat diakses | | ⬜ |

### 11.2 Permission-Based Access Control

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| SEC-006 | Agent tanpa permission tickets.assign mencoba assign | Error 403 atau tombol tidak muncul | | ⬜ |
| SEC-007 | Agent dengan permission tickets.assign bisa assign | Assign tiket berhasil | | ⬜ |
| SEC-008 | User tanpa admin.panel permission mengakses dashboard | Error 403 | | ⬜ |

### 11.3 Data Ownership

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| SEC-009 | User mengakses tiket milik user lain | Error 403 atau redirect | | ⬜ |
| SEC-010 | User hanya melihat tiket sendiri | Hanya tiket milik user yang ditampilkan | | ⬜ |
| SEC-011 | Agent melihat semua tiket | Agent dapat melihat semua tiket | | ⬜ |

### 11.4 Session Management

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| SEC-012 | Session timeout | User logout otomatis setelah session expired | | ⬜ |
| SEC-013 | Multiple session handling | Sesi terbaru aktif, sesi lama invalid (atau sesuai konfigurasi) | | ⬜ |
| SEC-014 | Session check endpoint | Endpoint /session/check mengembalikan status session | | ⬜ |

### 11.5 Input Validation & Sanitization

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| SEC-015 | XSS attack pada input form | Input di-sanitize, script tidak dieksekusi | | ⬜ |
| SEC-016 | SQL injection pada input | Query parameterized, tidak ada SQL injection | | ⬜ |
| SEC-017 | CSRF protection pada form | Form memiliki CSRF token, request tanpa token ditolak | | ⬜ |
| SEC-018 | File upload dengan malicious file | File yang tidak valid ditolak, hanya tipe yang diizinkan | | ⬜ |

---

## 12. Edge Cases & Error Handling Testing

### 12.1 Edge Cases

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| EDGE-001 | Create tiket saat tidak ada help topic | Error atau pesan: "Tidak ada help topic tersedia" | | ⬜ |
| EDGE-002 | Create tiket saat tidak ada SLA plan | Error atau menggunakan default SLA | | ⬜ |
| EDGE-003 | Assign tiket ke agent yang sudah dihapus | Validasi error: "Agent tidak ditemukan" | | ⬜ |
| EDGE-004 | Update status tiket yang sudah deleted | Error 404: "Tiket tidak ditemukan" | | ⬜ |
| EDGE-005 | Reply tiket yang sudah closed | Pesan error atau tetap bisa reply (sesuai business rule) | | ⬜ |
| EDGE-006 | Delete department yang masih digunakan tiket | Error cascade atau department tetap ada | | ⬜ |
| EDGE-007 | Multiple agent assign ke tiket yang sama | Hanya assign terakhir yang berlaku (atau sesuai business rule) | | ⬜ |
| EDGE-008 | Attachment dengan nama file sangat panjang | File tetap tersimpan atau nama di-truncate | | ⬜ |
| EDGE-009 | Create tiket dengan banyak attachment (>10 files) | Validasi error atau batasan jumlah file | | ⬜ |
| EDGE-010 | Search dengan karakter khusus | Search tetap berfungsi atau error ditangani dengan baik | | ⬜ |

### 12.2 Error Handling

| Kode Tes | Test Case | Output yang Diharapkan | Output yang Sebenarnya | Hasil |
|----------|-----------|------------------------|------------------------|-------|
| ERR-001 | Mengakses route yang tidak ada | Error 404 dengan halaman not found yang user-friendly | | ⬜ |
| ERR-002 | Mengakses resource yang tidak ada (404) | Error 404 dengan pesan yang jelas | | ⬜ |
| ERR-003 | Server error (500) | Error 500 dengan pesan umum, detail error di log | | ⬜ |
| ERR-004 | Database connection error | Error ditangani dengan baik, pesan user-friendly | | ⬜ |
| ERR-005 | Email service down | Notifikasi email gagal, error di-log, aplikasi tetap berfungsi | | ⬜ |
| ERR-006 | Telegram bot API error | Notifikasi Telegram gagal, error di-log, aplikasi tetap berfungsi | | ⬜ |
| ERR-007 | File upload error (disk full) | Error ditangani, pesan user-friendly | | ⬜ |

---

## 📊 Summary Testing

| Kategori | Total Test Cases | Passed | Failed | Not Tested |
|----------|-----------------|--------|--------|------------|
| Authentication | 29 | | | 29 |
| Guest Features | 14 | | | 14 |
| Portal - User | 29 | | | 29 |
| Agent Features | 39 | | | 39 |
| User Management | 23 | | | 23 |
| Master Data | 50 | | | 50 |
| Profile Management | 18 | | | 18 |
| Dashboard & Statistics | 12 | | | 12 |
| Chatbot | 6 | | | 6 |
| Notifications | 18 | | | 18 |
| Security & Authorization | 18 | | | 18 |
| Edge Cases & Error Handling | 17 | | | 17 |
| **TOTAL** | **273** | **0** | **0** | **273** |

---

## 📝 Catatan Testing

### Environment Testing
- **Database**: SQLite/MySQL/PostgreSQL (sesuai konfigurasi)
- **PHP Version**: 
- **Laravel Version**: 
- **Browser**: Chrome, Firefox, Edge (untuk UI testing)

### Pre-requisite Testing
1. Database sudah di-migrate dan di-seed
2. Storage link sudah dibuat (`php artisan storage:link`)
3. Email configuration sudah di-setup
4. Telegram bot sudah dikonfigurasi
5. User test sudah dibuat dengan berbagai role

### Test Data yang Diperlukan
- User dengan role: Super Admin, Admin, Agent, Support Agent, User
- Master data: Departments, Help Topics, Statuses, Priorities, SLA Plans, Teams, Organizations
- Sample tickets dengan berbagai status dan priority
- Canned responses dan chatbot responses

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
- Test satu fitur secara lengkap sebelum pindah ke fitur lain
- Dokumentasikan semua bug/issue yang ditemukan
- Screenshot untuk bug yang ditemukan
- Test dengan berbagai role dan permission
- Test edge cases dan error scenarios
- Verifikasi notifikasi (email & Telegram) benar-benar terkirim

---

**Dibuat**: [Tanggal]  
**Tester**: [Nama Tester]  
**Version**: 1.0

