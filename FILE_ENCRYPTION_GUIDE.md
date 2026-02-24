# Panduan Enkripsi File Attachment

## Overview

Sistem ini menggunakan **enkripsi AES-256-CBC** untuk semua file attachment yang diupload oleh pelapor, admin, atau agen. Ini memastikan bahwa jika website diretas, file-file sensitif tidak dapat langsung diakses tanpa kunci enkripsi.

## Keamanan

### Algoritma Enkripsi
- **Algoritma**: AES-256-CBC (Advanced Encryption Standard)
- **Key Management**: Menggunakan Laravel's `APP_KEY` dari file `.env`
- **Storage**: File terenkripsi disimpan di `storage/app/private/attachments/` (tidak dapat diakses langsung dari web)

### Keuntungan
1. **Proteksi Data**: File tidak dapat dibaca tanpa kunci enkripsi
2. **Compliance**: Memenuhi standar keamanan data untuk informasi sensitif
3. **Zero Trust**: Bahkan jika server diretas, file tetap aman
4. **Automatic**: Enkripsi/dekripsi otomatis, transparan untuk user

## Cara Kerja

### 1. Upload File (Enkripsi)
```php
// File diupload → Dienkripsi → Disimpan
$fileData = $encryptionService->storeEncrypted($file, 'attachments');
```

**Proses:**
1. File dibaca dari upload
2. Konten dienkripsi menggunakan `Crypt::encrypt()`
3. File disimpan dengan ekstensi `.enc`
4. Metadata (nama asli, mime, size) disimpan di database

### 2. Download File (Dekripsi)
```php
// File dibaca → Didekripsi → Dikirim ke user
$decryptedContent = $encryptionService->getDecrypted($attachment->path);
```

**Proses:**
1. File terenkripsi dibaca dari storage
2. Konten didekripsi menggunakan `Crypt::decrypt()`
3. File dikirim ke user dengan nama asli

## Database Schema

Tabel `lampiran` memiliki kolom tambahan:
- `is_encrypted` (boolean): Flag apakah file terenkripsi
- `original_filename` (string): Nama file asli sebelum enkripsi

## File Structure

### Sebelum Enkripsi
```
storage/app/public/attachments/
  └── document.pdf
```

### Setelah Enkripsi
```
storage/app/private/attachments/
  └── document_1707891234_abc123.enc
```

## API Usage

### Service: FileEncryptionService

#### Store Encrypted File
```php
$encryptionService = app(FileEncryptionService::class);
$fileData = $encryptionService->storeEncrypted($file, 'attachments');

// Returns:
[
    'path' => 'attachments/document_123.enc',
    'original_filename' => 'document.pdf',
    'encrypted_filename' => 'document_123.enc',
    'mime' => 'application/pdf',
    'size' => 1024000,
    'encrypted_size' => 1024128,
    'is_encrypted' => true,
]
```

#### Get Decrypted File
```php
$decryptedContent = $encryptionService->getDecrypted($attachment->path);
// Returns: Binary content of decrypted file
```

#### Delete Encrypted File
```php
$encryptionService->deleteEncrypted($attachment->path);
```

## Route & Controller

### Download Attachment
```
GET /attachments/{attachment}/download
```

**Controller**: `AttachmentController@download`

**Access Control:**
- User yang membuat tiket
- Admin/Agent dengan permission `admin.panel`
- User yang ditugaskan ke tiket

**Response**: File terdekripsi dengan nama asli

## Migration

Jalankan migration untuk menambahkan kolom enkripsi:
```bash
php artisan migrate
```

## Backward Compatibility

File lama yang tidak terenkripsi (`is_encrypted = false`) masih dapat diakses melalui:
- Storage disk `public` (untuk file lama)
- Route download akan otomatis handle kedua tipe file

## Best Practices

1. **APP_KEY Security**: Pastikan `APP_KEY` di `.env` aman dan tidak di-commit ke repository
2. **Backup**: Backup `APP_KEY` dengan aman - jika hilang, semua file tidak dapat didekripsi
3. **Storage Location**: File terenkripsi disimpan di `storage/app/private/` (tidak accessible via web)
4. **Access Control**: Selalu verifikasi akses user sebelum dekripsi file

## Troubleshooting

### File tidak bisa di-download
1. Cek apakah `APP_KEY` sudah di-set di `.env`
2. Cek permission folder `storage/app/private/attachments/`
3. Cek log di `storage/logs/laravel.log` untuk error detail

### Error: "Gagal mendekripsi file"
- Pastikan `APP_KEY` sama dengan saat file dienkripsi
- Jika `APP_KEY` berubah, file lama tidak dapat didekripsi

### File terlalu besar
- Enkripsi menambah overhead ~10-20% dari ukuran asli
- Pertimbangkan kompresi sebelum enkripsi untuk file besar

## Security Notes

⚠️ **PENTING:**
- Jangan pernah commit `APP_KEY` ke repository
- Backup `APP_KEY` dengan aman (jika hilang, semua file tidak dapat didekripsi)
- Gunakan environment variables untuk production
- Rotate `APP_KEY` dengan hati-hati (file lama tidak dapat didekripsi dengan key baru)

## Testing

Test enkripsi/dekripsi:
```php
$file = request()->file('attachment');
$encryptionService = app(FileEncryptionService::class);

// Encrypt
$fileData = $encryptionService->storeEncrypted($file, 'attachments');

// Decrypt
$decrypted = $encryptionService->getDecrypted($fileData['path']);

// Verify
assert($decrypted === file_get_contents($file->getRealPath()));
```

