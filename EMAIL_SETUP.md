# Setup Email untuk CSIRT Kalselprov

## Masalah: Email Tidak Terkirim

Jika email tidak terkirim, kemungkinan masalahnya adalah:

### 1. Konfigurasi Email di `.env`

Pastikan konfigurasi email di file `.env` sudah benar:

#### Untuk Development (Menggunakan Log):

```env
MAIL_MAILER=log
```

Email akan tersimpan di `storage/logs/laravel.log` sebagai file log.

#### Untuk Production (Menggunakan SMTP):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@csirt.kalselprov.go.id
MAIL_FROM_NAME="CSIRT Kalselprov"
```

### 2. Setup Gmail App Password

Jika menggunakan Gmail:

1. **Aktifkan 2-Step Verification** di akun Google Anda
2. **Buat App Password**:
    - Buka: https://myaccount.google.com/apppasswords
    - Pilih "Mail" dan "Other (Custom name)"
    - Masukkan nama: "CSIRT Kalselprov"
    - Salin password yang dihasilkan (16 karakter)
3. **Gunakan App Password** di `.env`:
    ```env
    MAIL_PASSWORD=your-16-character-app-password
    ```

### 3. Setup Email Custom Domain

Jika menggunakan email custom domain:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="CSIRT Kalselprov"
```

### 4. Testing Email

Untuk testing, gunakan `log` mailer:

```env
MAIL_MAILER=log
```

Kemudian cek email di:

```
storage/logs/laravel.log
```

### 5. Queue Worker (Jika Menggunakan Queue)

Jika menggunakan queue (untuk production), pastikan queue worker berjalan:

```bash
php artisan queue:work
```

Atau untuk development, gunakan sync:

```env
QUEUE_CONNECTION=sync
```

## Catatan Penting

-   **Development**: Gunakan `MAIL_MAILER=log` untuk testing
-   **Production**: Gunakan SMTP dengan konfigurasi yang benar
-   **Gmail**: Harus menggunakan App Password, bukan password biasa
-   **Queue**: Pastikan queue worker berjalan jika menggunakan queue

## Troubleshooting

### Error: "Username and Password not accepted"

-   Pastikan menggunakan App Password (bukan password Gmail biasa)
-   Pastikan 2-Step Verification sudah aktif
-   Cek apakah email dan password di `.env` sudah benar

### Email Masuk Queue Tapi Tidak Terkirim

-   Jalankan `php artisan queue:work`
-   Atau ubah `QUEUE_CONNECTION=sync` di `.env`

### Email Tidak Terlihat di Log

-   Cek `storage/logs/laravel.log`
-   Pastikan `MAIL_MAILER=log` di `.env`
-   Clear cache: `php artisan config:clear`
