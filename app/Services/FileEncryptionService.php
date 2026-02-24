<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Exception;

class FileEncryptionService
{
    /**
     * Enkripsi dan simpan file
     */
    public function storeEncrypted($file, string $directory = 'attachments'): array
    {
        try {
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
            Storage::disk('local')->put($path, $encryptedContent);
            
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
            // Cek apakah file ada
            if (!Storage::disk('local')->exists($encryptedPath)) {
                Log::warning('Encrypted file not found: ' . $encryptedPath);
                return null;
            }

            // Baca file terenkripsi
            $encryptedContent = Storage::disk('local')->get($encryptedPath);
            
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
            if (Storage::disk('local')->exists($encryptedPath)) {
                return Storage::disk('local')->delete($encryptedPath);
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
            return Storage::disk('local')->size($encryptedPath);
        } catch (Exception $e) {
            return 0;
        }
    }
}

