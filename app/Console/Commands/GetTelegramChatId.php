<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetTelegramChatId extends Command
{
    protected $signature = 'telegram:get-chat-id {email}';
    protected $description = 'Mendapatkan chat_id dari Telegram untuk user tertentu';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }

        if (empty($user->telegram_username)) {
            $this->error("User {$email} tidak memiliki telegram_username!");
            return 1;
        }

        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            $this->error("Bot token tidak dikonfigurasi!");
            return 1;
        }

        $apiUrl = "https://api.telegram.org/bot{$botToken}";
        $username = ltrim($user->telegram_username, '@');

        $this->info("Mencari chat_id untuk @{$username}...");
        $this->info("Pastikan user sudah mengirim /start ke bot!");

        try {
            // Ambil semua update
            $response = Http::get("{$apiUrl}/getUpdates", [
                'offset' => 0,
                'limit' => 100,
            ]);

            if (!$response->successful()) {
                $this->error("Gagal mendapatkan updates: " . $response->body());
                return 1;
            }

            $updates = $response->json('result', []);
            $this->info("Ditemukan " . count($updates) . " update(s)");

            $found = false;
            foreach ($updates as $update) {
                $message = $update['message'] ?? null;
                if ($message) {
                    $from = $message['from'] ?? [];
                    $chat = $message['chat'] ?? [];

                    $fromUsername = strtolower($from['username'] ?? '');
                    $chatUsername = strtolower($chat['username'] ?? '');
                    $searchUsername = strtolower($username);

                    if ($fromUsername === $searchUsername || $chatUsername === $searchUsername) {
                        $chatId = $chat['id'] ?? null;
                        if ($chatId) {
                            $this->info("✅ Chat ID ditemukan: {$chatId}");

                            // Simpan ke database
                            $user->telegram_chat_id = $chatId;
                            $user->save();

                            $this->info("✅ Chat ID telah disimpan ke database!");
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if (!$found) {
                $this->warn("❌ Chat ID tidak ditemukan untuk @{$username}");
                $this->warn("");
                $this->warn("Pastikan:");
                $this->warn("1. User sudah mengirim /start ke bot");
                $this->warn("2. Username Telegram benar: @{$username}");
                $this->warn("3. Bot token benar di .env");
                $this->warn("");
                $this->warn("Coba kirim /start lagi ke bot, lalu jalankan command ini lagi.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}

