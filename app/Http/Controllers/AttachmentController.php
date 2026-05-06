<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\FileEncryptionService;
use App\Services\SecurityEventLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function __construct(
        protected FileEncryptionService $encryptionService,
        protected SecurityEventLogService $securityLog
    ) {
        $this->middleware('auth');
    }

    /**
     * Download attachment (dengan dekripsi otomatis)
     */
    public function download(Attachment $attachment): StreamedResponse
    {
        // Cek akses: user harus memiliki akses ke ticket yang terkait
        $ticket = $attachment->thread->ticket;
        $user = auth()->user();

        // Cek apakah user memiliki akses
        $hasAccess = false;
        
        // Admin/Agent dengan permission admin.panel
        if ($user->can('admin.panel')) {
            $hasAccess = true;
        }
        // User yang membuat tiket
        elseif ($ticket->user_id === $user->id || $ticket->reporter_email === $user->email) {
            $hasAccess = true;
        }
        // User yang ditugaskan ke tiket (untuk agent)
        elseif ($ticket->assigned_to === $user->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            $this->securityLog->logEvent([
                'user_id' => $user?->id,
                'event_type' => 'attachment_download_denied',
                'severity' => 'medium',
                'message' => 'Attachment download denied',
                'context' => [
                    'attachment_id' => $attachment->id,
                    'ticket_id' => $ticket?->id,
                    'path' => $attachment->path,
                ],
            ]);
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        try {
            $this->securityLog->logEvent([
                'user_id' => $user?->id,
                'event_type' => 'attachment_download_attempt',
                'severity' => 'low',
                'message' => 'Attachment download attempt',
                'context' => [
                    'attachment_id' => $attachment->id,
                    'ticket_id' => $ticket?->id,
                    'is_encrypted' => (bool) $attachment->is_encrypted,
                    'path' => $attachment->path,
                ],
            ]);

            // Jika file terenkripsi, dekripsi dulu
            if ($attachment->is_encrypted) {
                $decryptedContent = $this->encryptionService->getDecrypted($attachment->path);
                
                if (!$decryptedContent) {
                    abort(404, 'File tidak ditemukan atau gagal didekripsi.');
                }

                // Gunakan nama file asli jika ada
                $filename = $attachment->original_filename ?? $attachment->filename;
                // Hapus ekstensi .enc jika ada
                $filename = str_replace('.enc', '', $filename);

                return response()->streamDownload(function () use ($decryptedContent) {
                    echo $decryptedContent;
                }, $filename, [
                    'Content-Type' => $attachment->mime,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                // File lama yang tidak terenkripsi (backward compatibility)
                if (Storage::disk('public')->exists($attachment->path)) {
                    $stream = Storage::disk('public')->readStream($attachment->path);
                    if (!is_resource($stream)) {
                        abort(404, 'File tidak ditemukan.');
                    }

                    return response()->streamDownload(function () use ($stream) {
                        fpassthru($stream);
                        fclose($stream);
                    }, $attachment->filename);
                }
                abort(404, 'File tidak ditemukan.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download attachment: ' . $e->getMessage(), [
                'attachment_id' => $attachment->id,
                'user_id' => $user->id,
            ]);
            $this->securityLog->logEvent([
                'user_id' => $user?->id,
                'event_type' => 'attachment_download_error',
                'severity' => 'high',
                'message' => 'Attachment download error',
                'context' => [
                    'attachment_id' => $attachment->id,
                    'ticket_id' => $ticket?->id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(500, 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }
}

