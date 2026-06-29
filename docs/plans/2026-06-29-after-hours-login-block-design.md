# Desain Pemblokiran Login di Luar Jam Kerja

## Tujuan

Menolak login pengguna di luar jam kerja melalui pemeriksaan server-side, menampilkan halaman penolakan yang jelas, dan mencatat kejadian ke audit keamanan.

## Aturan Akses

- Jam kerja default: Senin-Jumat, 08.00-17.00 WITA.
- Pukul 08.00 termasuk jam kerja; pukul 17.00 dan setelahnya ditolak.
- Sabtu dan Minggu ditolak sepanjang hari.
- Pengguna dengan `allow_after_hours_access = true` dikecualikan secara eksplisit.
- Aturan dapat dinonaktifkan atau diubah melalui environment variables tanpa mengubah kode.

## Alur Autentikasi

1. Laravel memvalidasi kredensial dan rate limit seperti biasa.
2. Setelah kredensial valid tetapi sebelum sesi login diselesaikan, service jam kerja mengevaluasi waktu dengan timezone konfigurasi.
3. Jika akses ditolak, sistem mencatat event `after_hours_login_blocked` dengan user ID, IP, waktu, timezone, dan jadwal; password atau token tidak pernah dicatat.
4. Guard di-logout, session di-invalidasi, dan CSRF token dirotasi.
5. Pengguna diarahkan ke halaman guest `login/work-hours-blocked`.
6. Jika akses diizinkan, alur VPN, regenerasi session, MFA, dan redirect berjalan seperti sebelumnya.

## Antarmuka

Halaman penolakan mengikuti bahasa visual portal: kartu keamanan berwarna merah lembut, ikon peringatan, status "Akses Ditolak", waktu percobaan, jadwal yang diizinkan, zona waktu, informasi audit, dan tombol kembali ke login. Data ditampilkan dengan escaping Blade.

## Konfigurasi

- `WORKING_HOURS_LOGIN_BLOCK_ENABLED=true`
- `WORKING_HOURS_START=08:00`
- `WORKING_HOURS_END=17:00`
- `WORKING_HOURS_DAYS=1,2,3,4,5` (ISO-8601; Senin = 1)
- `WORKING_HOURS_TIMEZONE=Asia/Makassar`

## Pengujian

Feature test mencakup login pada 07.59, 08.00, 16.59, 17.00, akhir pekan, pengecualian per pengguna, dan kondisi fitur dinonaktifkan. Test memastikan pengguna yang ditolak tetap guest dan event keamanan tercatat.
