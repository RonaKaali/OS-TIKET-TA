# Log Perbaikan Keamanan & Bug

Tanggal: 2025

---

## 🔴 KRITIS — Diperbaiki

### Fix 1: `/deploy-db` Route Dilindungi Secret Token
**File:** `routes/web.php`
**Masalah:** Route terbuka tanpa autentikasi, siapapun bisa jalankan migrasi paksa.
**Solusi:** Ditambahkan validasi `DEPLOY_SECRET` dari environment variable.
**Cara pakai:** Isi `DEPLOY_SECRET=xxx` di `.env`, akses via `/deploy-db?secret=xxx`.

### Fix 2: Chatbot Rate Limiting
**File:** `routes/web.php`
**Masalah:** Endpoint publik tanpa rate limit, bisa di-flood.
**Solusi:** Ditambahkan `throttle:20,1` (20 request per menit per IP).

### Fix 3: Validasi Tipe File Lampiran
**File:** `Portal/TicketController.php`, `Agent/TicketController.php`
**Masalah:** Hanya ada validasi ukuran (`max:10240`), tipe file bebas termasuk `.php`, `.exe`.
**Solusi:** Ditambahkan `mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip`.

### Fix 4: Akses Lampiran — Prinsip Least Privilege
**File:** `AttachmentController.php`
**Masalah:** Semua agent dengan permission `admin.panel` bisa download semua lampiran.
**Solusi:** Agent hanya bisa akses lampiran dari tiket yang ditugaskan kepadanya. Admin/Super Admin tetap bisa semua.

### Fix 5: IP Spoofing via X-Forwarded-For
**File:** `ZeroTrustVerification.php`, `ContextAwareAccessService.php`
**Masalah:** Header X-Forwarded-For dipercaya tanpa verifikasi trusted proxy.
**Solusi:** X-Forwarded-For hanya dipakai jika `REMOTE_ADDR` ada di daftar `TRUSTED_PROXIES`.

---

## 🟠 TINGGI — Diperbaiki

### Fix 6: Status Tiket Tidak Tertimpa Saat Agent Balas
**File:** `Agent/TicketController.php`
**Masalah:** Setiap kali agent membalas, status selalu dikembalikan ke `open` — menimpa `in_progress`, `assigned`, dll.
**Solusi:** Status hanya dikembalikan ke `open` jika status saat ini bukan `in_progress`, `assigned`, `closed`, atau `cancelled`.

### Fix 7: Agent Tidak Bisa Akses Tiket yang Belum Di-assign
**File:** `Agent/TicketController.php`
**Masalah:** Jika `assigned_to === null`, perbandingan `null !== user->id` bisa lolos secara implisit.
**Solusi:** Ditambahkan `is_null()` check eksplisit.

### Fix 8: NoteController Cek Kepemilikan Tiket
**File:** `Agent/NoteController.php`
**Masalah:** Agent bisa tambah catatan ke tiket apapun tanpa cek kepemilikan.
**Solusi:** Hanya agent yang punya tiket (atau Admin/Super Admin) yang bisa tambah catatan.

### Fix 9: Dashboard Overdue Query — Efisien
**File:** `Agent/DashboardController.php`, `Models/Ticket.php`
**Masalah:** Semua tiket dimuat ke memori PHP lalu difilter satu-satu (N+1 / memory issue).
**Solusi:** Diganti dengan query SQL langsung `where('due_at', '<', now()->subDays(7))`.

### Fix 10: `session()->now()` Tidak Tampil di View
**File:** `Portal/TicketController.php`
**Masalah:** `session()->now()` adalah flash untuk request berikutnya, tidak cocok untuk `return view()`.
**Solusi:** Diganti ke `session()->flash()`.

### Fix 11: Session Verifikasi Tiket Punya Expiry
**File:** `Portal/TicketController.php`
**Masalah:** Session `ticket_verified_email_*` tidak punya expiry — berlaku selamanya.
**Solusi:** Ditambahkan timestamp verifikasi, valid 2 jam saja.

---

## 🟡 SEDANG — Diperbaiki

### Fix 12: Backup Code Generator Pakai CSPRNG
**File:** `Services/MfaService.php`
**Masalah:** `md5(uniqid(rand(), true))` bukan kriptografis aman.
**Solusi:** Diganti ke `bin2hex(random_bytes(4))` yang adalah CSPRNG.

### Fix 13: Ticket Number Fallback Pakai CSPRNG
**File:** `Models/Ticket.php`
**Masalah:** `str_shuffle()` tidak menggunakan CSPRNG untuk suffix fallback.
**Solusi:** Diganti ke `bin2hex(random_bytes(4))`.

### Fix 14: MfaVerificationController::completeLogin() Try-Catch
**File:** `Auth/MfaVerificationController.php`
**Masalah:** `createToken()` tanpa try-catch, crash jika tabel `personal_access_tokens` belum ada.
**Solusi:** Ditambahkan try-catch sama seperti `AuthenticatedSessionController`.

### Fix 15: FileEncryptionService — Validasi & Logging File Besar
**File:** `Services/FileEncryptionService.php`
**Masalah:** File 10MB di-load ke memori tanpa warning.
**Solusi:** Ditambahkan warning log jika file >8MB, dan validasi `file_get_contents` gagal.

---

## 🔵 MINOR — Diperbaiki

### Fix 16: File Debug `toArray())` Dihapus
**File:** Root direktori proyek
**Masalah:** File sisa debug yang tidak sengaja tersimpan.
**Solusi:** Dihapus.

### Fix 17: `.env.example` Lengkap
**File:** `.env.example`
**Masalah:** Variabel penting `DEPLOY_SECRET`, `TRUSTED_PROXIES`, Zero Trust tidak terdokumentasi.
**Solusi:** Ditambahkan semua variabel keamanan dengan komentar.

---

## Yang Perlu Dilakukan Secara Manual

1. **Isi `DEPLOY_SECRET` di `.env`** — generate dengan: `openssl rand -hex 32`
2. **Isi `TRUSTED_PROXIES` di `.env`** — isi dengan IP Vercel/Cloudflare jika pakai reverse proxy
3. **Hapus atau ganti nama route `/deploy-db`** setelah migrasi production selesai
4. **Test fitur MFA** setelah perubahan backup code generator
5. **Verifikasi ulang lampiran** — pastikan file `.enc` lama masih bisa dibuka

