<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Services\FileEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DecryptAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachment:decrypt 
                            {attachment_id : ID attachment yang akan didekripsi}
                            {--output= : Path output file (opsional)}
                            {--list : List semua attachment terenkripsi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dekripsi file attachment yang sudah terenkripsi';

    /**
     * Execute the console command.
     */
    public function handle(FileEncryptionService $service)
    {
        // Jika --list, tampilkan list attachment terenkripsi
        if ($this->option('list')) {
            return $this->listEncryptedAttachments();
        }

        $attachmentId = $this->argument('attachment_id');
        $attachment = Attachment::find($attachmentId);

        if (!$attachment) {
            $this->error("❌ Attachment dengan ID {$attachmentId} tidak ditemukan!");
            return 1;
        }

        if (!$attachment->is_encrypted) {
            $this->warn("⚠️  File tidak terenkripsi (is_encrypted = false)");
            $this->info("Path: {$attachment->path}");
            return 0;
        }

        $filename = $attachment->original_filename ?: $attachment->filename;
        $this->info("📁 Attachment: {$filename}");
        $this->info("📂 Path: {$attachment->path}");
        $this->info("📊 Size: " . number_format($attachment->size) . " bytes");

        try {
            // Dekripsi file
            $this->line("🔓 Mendekripsi file...");
            $decryptedContent = $service->getDecrypted($attachment->path);

            if (!$decryptedContent) {
                $this->error("❌ File tidak ditemukan atau gagal didekripsi!");
                return 1;
            }

            // Tentukan output path
            $filename = $attachment->original_filename ?: $attachment->filename;
            // Hapus ekstensi .enc jika ada
            $filename = str_replace('.enc', '', $filename);
            
            $outputPath = $this->option('output') 
                ? $this->option('output')
                : storage_path('app/decrypted/' . $filename);

            // Buat directory jika belum ada
            $dir = dirname($outputPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $this->line("📁 Directory dibuat: {$dir}");
            }

            // Simpan file terdekripsi
            file_put_contents($outputPath, $decryptedContent);
            
            $decryptedSize = strlen($decryptedContent);
            $this->info("✅ File berhasil didekripsi!");
            $this->line("📄 Output: {$outputPath}");
            $this->line("📊 Size: " . number_format($decryptedSize) . " bytes");

            // Verifikasi ukuran
            if ($decryptedSize === $attachment->size) {
                $this->info("✓ Ukuran file sesuai dengan database");
            } else {
                $this->warn("⚠️  Ukuran file berbeda!");
                $this->line("   Database: " . number_format($attachment->size) . " bytes");
                $this->line("   Decrypted: " . number_format($decryptedSize) . " bytes");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->line("   Pastikan APP_KEY di .env sudah benar!");
            return 1;
        }
    }

    /**
     * List semua attachment terenkripsi
     */
    protected function listEncryptedAttachments()
    {
        $attachments = Attachment::where('is_encrypted', true)
            ->with('thread.ticket')
            ->latest()
            ->get();

        if ($attachments->isEmpty()) {
            $this->info("Tidak ada attachment terenkripsi.");
            return 0;
        }

        $this->info("📋 Daftar Attachment Terenkripsi ({$attachments->count()} file):\n");

        $headers = ['ID', 'Filename', 'Size', 'Ticket', 'Path'];
        $rows = [];

        foreach ($attachments as $attachment) {
            $ticket = $attachment->thread->ticket ?: null;
            $ticketInfo = $ticket 
                ? "#{$ticket->ticket_number}" 
                : 'N/A';

            $filename = $attachment->original_filename ?: $attachment->filename;
            $rows[] = [
                $attachment->id,
                $filename,
                number_format($attachment->size) . ' bytes',
                $ticketInfo,
                substr($attachment->path, 0, 50) . '...',
            ];
        }

        $this->table($headers, $rows);
        $this->line("\n💡 Gunakan: php artisan attachment:decrypt {ID} untuk dekripsi");

        return 0;
    }
}
