<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Exception;

class FileEncryptionService
{
    protected $disk;

    protected function assertStrongCipher(): void
    {
        $cipher = (string) config('app.cipher', '');
        if (!str_starts_with($cipher, 'AES-256-')) {
            throw new Exception("Konfigurasi cipher tidak sesuai. Diharapkan AES-256-*, saat ini: {$cipher}");
        }
    }

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'local');
    }

    /**
     * Enkripsi dan simpan file
     */
    public function storeEncrypted($file, string $directory = 'attachments'): array
    {
        try {
            $this->assertStrongCipher();

            // Baca konten file
            $content = file_get_contents($file->getRealPath());
            
            // Enkripsi konten menggunakan Laravel Crypt (AES-256-CBC)
            $encryptedContent = Crypt::encrypt($content);
            
            // Generate nama file yang unik dengan ekstensi .enc
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $encryptedFilename = $filename . '_' . time() . '_' . uniqid() . '.enc';
            
            // Simpan file terenkripsi
            $path = $directory . '/' . $encryptedFilename;
            $saved = Storage::disk($this->disk)->put($path, $encryptedContent);
            
            if (!$saved) {
                throw new Exception("Gagal menulis file ke disk. Pastikan folder storage memiliki izin tulis.");
            }
            
            return [
                'path' => $path,
                'original_filename' => $originalName,
                'encrypted_filename' => $encryptedFilename,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'encrypted_size' => strlen($encryptedContent),
                'is_encrypted' => true,
            ];
        } catch (Exception $e) {
            Log::error('File encryption failed: ' . $e->getMessage());
            throw new Exception('Gagal mengenkripsi file: ' . $e->getMessage());
        }
    }

    /**
     * Dekripsi dan ambil file
     */
    public function getDecrypted(string $encryptedPath): ?string
    {
        try {
            $this->assertStrongCipher();

            // Cek apakah file ada
            if (!Storage::disk($this->disk)->exists($encryptedPath)) {
                Log::warning('Encrypted file not found: ' . $encryptedPath);
                return null;
            }

            // Baca file terenkripsi
            $encryptedContent = Storage::disk($this->disk)->get($encryptedPath);
            
            // Dekripsi konten
            $decryptedContent = Crypt::decrypt($encryptedContent);
            
            return $decryptedContent;
        } catch (Exception $e) {
            Log::error('File decryption failed: ' . $e->getMessage(), [
                'path' => $encryptedPath,
            ]);
            throw new Exception('Gagal mendekripsi file: ' . $e->getMessage());
        }
    }

    /**
     * Hapus file terenkripsi
     */
    public function deleteEncrypted(string $encryptedPath): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($encryptedPath)) {
                return Storage::disk($this->disk)->delete($encryptedPath);
            }
            return false;
        } catch (Exception $e) {
            Log::error('Failed to delete encrypted file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek apakah file terenkripsi (berdasarkan path atau extension)
     */
    public function isEncrypted(string $path): bool
    {
        return str_ends_with($path, '.enc') || str_contains($path, '_encrypted');
    }

    /**
     * Get file size (encrypted)
     */
    public function getFileSize(string $encryptedPath): int
    {
        try {
            return Storage::disk($this->disk)->size($encryptedPath);
        } catch (Exception $e) {
            return 0;
        }
    }
}
