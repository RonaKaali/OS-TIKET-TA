# Panduan Dekripsi File Attachment

## Overview

File yang sudah dienkripsi dapat didekripsi menggunakan `FileEncryptionService`. Dekripsi dilakukan secara otomatis saat user mengunduh file melalui sistem, atau dapat dilakukan secara manual untuk keperluan backup/recovery.

## Cara Dekripsi File

### 1. Melalui Web Interface (Otomatis)

File akan otomatis didekripsi saat user mengklik tombol download:

```
User klik download → AttachmentController → FileEncryptionService::getDecrypted() → File terdekripsi dikirim
```

**URL**: `/attachments/{attachment_id}/download`

### 2. Melalui Code (Manual)

#### A. Menggunakan FileEncryptionService

```php
use App\Services\FileEncryptionService;
use App\Models\Attachment;

// Ambil attachment dari database
$attachment = Attachment::find($attachmentId);

// Inisialisasi service
$encryptionService = app(FileEncryptionService::class);

// Dekripsi file
try {
    $decryptedContent = $encryptionService->getDecrypted($attachment->path);
    
    // Simpan file terdekripsi (opsional)
    $originalFilename = $attachment->original_filename ?? $attachment->filename;
    file_put_contents(storage_path('app/decrypted/' . $originalFilename), $decryptedContent);
    
    echo "File berhasil didekripsi: " . $originalFilename;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

#### B. Menggunakan Laravel Tinker

```bash
php artisan tinker
```

Kemudian di tinker:

```php
// Ambil attachment
$attachment = App\Models\Attachment::find(1);

// Dekripsi
$service = app(App\Services\FileEncryptionService::class);
$decrypted = $service->getDecrypted($attachment->path);

// Simpan ke file
$filename = $attachment->original_filename ?? $attachment->filename;
file_put_contents(storage_path('app/decrypted/' . $filename), $decrypted);

echo "File didekripsi: " . $filename;
```

#### C. Menggunakan Artisan Command (Custom)

Buat command untuk dekripsi batch:

```php
// app/Console/Commands/DecryptAttachment.php
<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Services\FileEncryptionService;
use Illuminate\Console\Command;

class DecryptAttachment extends Command
{
    protected $signature = 'attachment:decrypt {attachment_id} {--output=}';
    protected $description = 'Dekripsi file attachment';

    public function handle(FileEncryptionService $service)
    {
        $attachmentId = $this->argument('attachment_id');
        $attachment = Attachment::find($attachmentId);

        if (!$attachment) {
            $this->error("Attachment tidak ditemukan!");
            return 1;
        }

        if (!$attachment->is_encrypted) {
            $this->warn("File tidak terenkripsi!");
            return 0;
        }

        try {
            $decrypted = $service->getDecrypted($attachment->path);
            $filename = $attachment->original_filename ?? $attachment->filename;
            $outputPath = $this->option('output') ?? storage_path('app/decrypted/' . $filename);

            // Buat directory jika belum ada
            $dir = dirname($outputPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($outputPath, $decrypted);
            $this->info("File berhasil didekripsi: {$outputPath}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
```

**Usage:**
```bash
php artisan attachment:decrypt 1
php artisan attachment:decrypt 1 --output=/path/to/output/file.pdf
```

## Dekripsi Batch (Multiple Files)

### Dekripsi Semua File dari Ticket Tertentu

```php
use App\Models\Ticket;
use App\Services\FileEncryptionService;

$ticket = Ticket::find($ticketId);
$service = app(FileEncryptionService::class);

foreach ($ticket->threads as $thread) {
    foreach ($thread->attachments as $attachment) {
        if ($attachment->is_encrypted) {
            try {
                $decrypted = $service->getDecrypted($attachment->path);
                $filename = $attachment->original_filename ?? $attachment->filename;
                $outputPath = storage_path('app/decrypted/' . $filename);
                
                file_put_contents($outputPath, $decrypted);
                echo "Decrypted: {$filename}\n";
            } catch (\Exception $e) {
                echo "Error decrypting {$filename}: " . $e->getMessage() . "\n";
            }
        }
    }
}
```

## Dekripsi File Langsung dari Storage

Jika Anda tahu path file terenkripsi:

```php
use App\Services\FileEncryptionService;
use Illuminate\Support\Facades\Storage;

$service = app(FileEncryptionService::class);
$encryptedPath = 'attachments/document_123.enc';

try {
    $decrypted = $service->getDecrypted($encryptedPath);
    
    // Simpan file terdekripsi
    Storage::disk('local')->put('decrypted/document.pdf', $decrypted);
    
    echo "File berhasil didekripsi!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Verifikasi Dekripsi

Untuk memastikan file terdekripsi dengan benar:

```php
use App\Models\Attachment;
use App\Services\FileEncryptionService;

$attachment = Attachment::find($attachmentId);
$service = app(FileEncryptionService::class);

// Dekripsi
$decrypted = $service->getDecrypted($attachment->path);

// Verifikasi ukuran file (harus sama dengan size di database)
$decryptedSize = strlen($decrypted);
$originalSize = $attachment->size;

if ($decryptedSize === $originalSize) {
    echo "✓ Dekripsi berhasil! Ukuran file sesuai.";
} else {
    echo "⚠ Warning: Ukuran file tidak sesuai!";
    echo "Original: {$originalSize} bytes";
    echo "Decrypted: {$decryptedSize} bytes";
}

// Verifikasi MIME type (opsional)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_buffer($finfo, $decrypted);
finfo_close($finfo);

if ($mimeType === $attachment->mime) {
    echo "✓ MIME type sesuai: {$mimeType}";
} else {
    echo "⚠ MIME type berbeda: Expected {$attachment->mime}, Got {$mimeType}";
}
```

## Troubleshooting

### Error: "Gagal mendekripsi file"

**Kemungkinan penyebab:**
1. `APP_KEY` tidak sesuai dengan saat file dienkripsi
2. File terenkripsi corrupt atau tidak lengkap
3. Path file salah

**Solusi:**
```php
// Cek apakah file ada
$exists = Storage::disk('local')->exists($attachment->path);
if (!$exists) {
    echo "File tidak ditemukan di: " . $attachment->path;
}

// Cek APP_KEY
echo "APP_KEY: " . substr(config('app.key'), 0, 20) . "...";
```

### Error: "The payload is invalid"

Ini berarti `APP_KEY` berbeda dengan saat file dienkripsi.

**Solusi:**
- Pastikan menggunakan `APP_KEY` yang sama
- Jika `APP_KEY` berubah, file lama tidak dapat didekripsi

### File Terdekripsi Tapi Corrupt

**Cek:**
1. Ukuran file sesuai dengan `size` di database
2. MIME type sesuai
3. File dapat dibuka dengan aplikasi yang sesuai

## Best Practices

1. **Backup APP_KEY**: Simpan `APP_KEY` dengan aman - jika hilang, semua file tidak dapat didekripsi
2. **Test Dekripsi**: Setelah enkripsi, selalu test dekripsi untuk memastikan file tidak corrupt
3. **Logging**: Log semua operasi dekripsi untuk audit trail
4. **Access Control**: Selalu verifikasi akses user sebelum dekripsi

## Contoh Lengkap: Script Dekripsi

```php
<?php
// scripts/decrypt_attachments.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attachment;
use App\Services\FileEncryptionService;

$service = app(FileEncryptionService::class);
$outputDir = storage_path('app/decrypted');

// Buat directory output
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Ambil semua attachment terenkripsi
$attachments = Attachment::where('is_encrypted', true)->get();

echo "Found {$attachments->count()} encrypted files\n\n";

foreach ($attachments as $attachment) {
    try {
        echo "Decrypting: {$attachment->original_filename}... ";
        
        $decrypted = $service->getDecrypted($attachment->path);
        $filename = $attachment->original_filename ?? $attachment->filename;
        $outputPath = $outputDir . '/' . $filename;
        
        file_put_contents($outputPath, $decrypted);
        
        echo "✓ Done\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\nDecryption complete! Files saved to: {$outputDir}\n";
```

**Jalankan:**
```bash
php scripts/decrypt_attachments.php
```

## API Reference

### FileEncryptionService::getDecrypted()

```php
public function getDecrypted(string $encryptedPath): ?string
```

**Parameters:**
- `$encryptedPath`: Path file terenkripsi (relative dari storage/app/)

**Returns:**
- `string`: Konten file terdekripsi (binary)
- `null`: Jika file tidak ditemukan

**Throws:**
- `Exception`: Jika dekripsi gagal (APP_KEY salah, file corrupt, dll)

## Security Notes

⚠️ **PENTING:**
- Jangan simpan file terdekripsi di public directory
- Hapus file terdekripsi setelah digunakan (jika untuk temporary)
- Log semua operasi dekripsi untuk audit
- Verifikasi akses user sebelum dekripsi



