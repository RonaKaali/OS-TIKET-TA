<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = $request->all();

            // Log update untuk debugging
            Log::info('Telegram webhook received', ['update' => $update]);

            // Handle message
            if (isset($update['message'])) {
                $message = $update['message'];
                $chat = $message['chat'] ?? [];
                $from = $message['from'] ?? [];
                $text = $message['text'] ?? '';

                $chatId = $chat['id'] ?? null;
                $username = $from['username'] ?? null;
                $firstName = $from['first_name'] ?? '';
                $lastName = $from['last_name'] ?? '';

                if (!$chatId) {
                    Log::warning('Missing chat_id in Telegram update', ['update' => $update]);
                    return response()->json(['ok' => true]);
                }

                // Jika tidak ada username, coba cari berdasarkan chat_id yang sudah ada
                if (!$username) {
                    $user = User::where('id_chat_telegram', $chatId)->first();
                    if ($user) {
                        Log::info("Update diterima dari chat_id {$chatId} untuk user {$user->email} (tanpa username)");
                        if (str_starts_with($text, '/')) {
                            $this->handleCommand($chatId, $text, $user, $firstName);
                        }
                        return response()->json(['ok' => true]);
                    }
                    Log::warning('Missing username in Telegram update and no user found with chat_id', ['chat_id' => $chatId]);
                    return response()->json(['ok' => true]);
                }

                // Normalisasi username (lowercase, trim)
                $username = strtolower(trim($username));

                // Cari user berdasarkan nama_pengguna_telegram (case-insensitive)
                $user = User::whereRaw('LOWER(nama_pengguna_telegram) = ?', [$username])->first();

                if ($user) {
                    // Update chat_id jika belum ada atau berbeda
                    // Gunakan kolom database yang sebenarnya: id_chat_telegram
                    $oldChatId = $user->id_chat_telegram;
                    if ($oldChatId != $chatId) {
                        $user->id_chat_telegram = $chatId;
                        $user->save();
                        Log::info("Chat ID {$chatId} telah disimpan untuk user @{$username} ({$user->email}). Chat ID sebelumnya: " . ($oldChatId ?? 'null'));

                        // Reload user untuk memastikan data ter-update
                        $user->refresh();
                    } else {
                        Log::info("Chat ID {$chatId} sudah terdaftar untuk user @{$username} ({$user->email})");
                    }

                    // Handle commands
                    if (str_starts_with($text, '/')) {
                        $this->handleCommand($chatId, $text, $user, $firstName);
                    }
                } else {
                    Log::warning("User dengan username Telegram @{$username} tidak ditemukan di database");
                    // User belum terdaftar, kirim pesan bantuan
                    if (str_starts_with($text, '/start') || str_starts_with($text, '/register')) {
                        $this->sendMessage($chatId, "Halo {$firstName}! 👋\n\nSaya adalah bot notifikasi CSIRT Kalselprov.\n\nUntuk menerima notifikasi, Anda perlu:\n1. Login ke sistem CSIRT\n2. Isi username Telegram Anda di halaman Profile\n3. Username Telegram Anda: <b>@{$username}</b>\n\nSetelah itu, notifikasi akan otomatis terkirim ke Telegram Anda.");
                    }
                }
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Error handling Telegram webhook: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    protected function handleCommand($chatId, $command, $user, $firstName)
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            return;
        }

        $apiUrl = "https://api.telegram.org/bot{$botToken}";

        switch ($command) {
            case '/start':
                $message = "Halo {$firstName}! 👋\n\n";
                $message .= "Selamat datang di Bot Notifikasi CSIRT Kalselprov.\n\n";
                $message .= "✅ Chat ID Anda telah terdaftar: <b>{$chatId}</b>\n";
                $message .= "✅ Username: <b>@{$user->telegram_username}</b>\n";
                $message .= "✅ Email: <b>{$user->email}</b>\n\n";
                $message .= "Anda akan menerima notifikasi otomatis untuk:\n";
                $message .= "• Laporan insiden siber baru\n";
                $message .= "• Perubahan status laporan\n";
                $message .= "• Balasan dari agent\n\n";
                $message .= "Gunakan /help untuk melihat perintah lainnya.";
                $this->sendMessage($chatId, $message);
                break;

            case '/help':
                $message = "📋 <b>Perintah yang Tersedia:</b>\n\n";
                $message .= "/start - Memulai bot dan mendaftarkan chat ID\n";
                $message .= "/help - Menampilkan bantuan\n";
                $message .= "/status - Cek status registrasi Anda\n";
                $this->sendMessage($chatId, $message);
                break;

            case '/status':
                $message = "📊 <b>Status Registrasi:</b>\n\n";
                $message .= "✅ Chat ID: <b>{$chatId}</b>\n";
                $message .= "✅ Username: <b>@{$user->telegram_username}</b>\n";
                $message .= "✅ Email: <b>{$user->email}</b>\n";
                $message .= "✅ Nama: <b>{$user->name}</b>\n\n";
                $message .= "Status: <b>Aktif</b> ✅\n";
                $message .= "Anda akan menerima notifikasi otomatis.";
                $this->sendMessage($chatId, $message);
                break;

            default:
                $this->sendMessage($chatId, "Perintah tidak dikenali. Gunakan /help untuk melihat daftar perintah.");
        }
    }

    protected function sendMessage($chatId, $message)
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            return;
        }

        $apiUrl = "https://api.telegram.org/bot{$botToken}";

        try {
            $response = \Illuminate\Support\Facades\Http::post("{$apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful()) {
                Log::error("Gagal mengirim pesan ke chat_id {$chatId}: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("Error mengirim pesan ke chat_id {$chatId}: " . $e->getMessage());
        }
    }
}

