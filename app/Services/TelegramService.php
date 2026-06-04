<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Mengirim pesan ke Telegram user berdasarkan username
     * Catatan: User harus sudah pernah memulai chat dengan bot terlebih dahulu
     *
     * @param string $username Username Telegram (tanpa @)
     * @param string $message Pesan yang akan dikirim
     * @return bool
     */
    public function sendMessage(string $username, string $message): bool
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token tidak dikonfigurasi');
            return false;
        }

        // Hapus @ jika ada di awal username
        $username = ltrim($username, '@');

        try {
            // Coba cari user di database yang memiliki nama_pengguna_telegram ini
            $user = \App\Models\User::where('nama_pengguna_telegram', $username)->first();

            // Jika user memiliki id_chat_telegram yang sudah disimpan, gunakan itu
            if ($user && $user->id_chat_telegram) {
                Log::info("Menggunakan chat_id dari database untuk @{$username}: {$user->id_chat_telegram}");
                return $this->sendMessageByChatId($user->id_chat_telegram, $message);
            }

            // Jika tidak ada chat_id di database, coba dapatkan dari getUpdates
            Log::info("Mencari chat_id untuk @{$username} dari getUpdates...");
            $chatId = $this->getChatIdByUsername($username);

            if (!$chatId) {
                Log::warning("Tidak dapat menemukan chat_id untuk username Telegram: @{$username}. Pastikan user sudah memulai chat dengan bot dengan mengirim /start.");

                // Jika user ada tapi belum punya chat_id, kirim instruksi
                if ($user) {
                    $this->sendInstructionMessage($username);
                }

                return false;
            }

            // Simpan chat_id ke database untuk penggunaan selanjutnya
            if ($user) {
                $user->id_chat_telegram = $chatId;
                $user->save();
                Log::info("Chat ID {$chatId} telah disimpan ke database untuk user @{$username}");
            }

            // Kirim pesan menggunakan chat_id
            return $this->sendMessageByChatId($chatId, $message);
        } catch (\Exception $e) {
            Log::error("Error mengirim pesan Telegram ke @{$username}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Mencoba mendapatkan chat_id saat user register atau update profil
     * 
     * @param string $username Username Telegram (tanpa @)
     * @return string|int|null
     */
    public function tryGetChatId(string $username): string|int|null
    {
        if (empty($this->botToken)) {
            return null;
        }

        $username = ltrim($username, '@');

        try {
            $chatId = $this->getChatIdByUsername($username);

            if ($chatId) {
                // Simpan ke database jika user ditemukan
                $user = \App\Models\User::where('nama_pengguna_telegram', $username)->first();
                if ($user && $user->id_chat_telegram != $chatId) {
                    $user->id_chat_telegram = $chatId;
                    $user->save();
                    Log::info("Chat ID {$chatId} otomatis disimpan untuk user @{$username}");
                }
            }

            return $chatId;
        } catch (\Exception $e) {
            Log::error("Error mencoba mendapatkan chat_id untuk @{$username}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim pesan instruksi ke user untuk memulai chat dengan bot
     */
    protected function sendInstructionMessage(string $username): void
    {
        // Tidak bisa mengirim pesan tanpa chat_id
        // User harus memulai chat dengan bot terlebih dahulu
        Log::info("User @{$username} perlu memulai chat dengan bot. Chat_id akan otomatis tersimpan saat user mengirim /start.");
    }

    /**
     * Mengirim pesan langsung menggunakan chat_id
     *
     * @param string|int $chatId Chat ID Telegram
     * @param string $message Pesan yang akan dikirim
     * @return bool
     */
    public function sendMessageByChatId($chatId, string $message): bool
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token tidak dikonfigurasi');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
            $response = Http::timeout(5)->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info("Pesan Telegram berhasil dikirim ke chat_id: {$chatId}");
                return true;
            } else {
                Log::error("Gagal mengirim pesan Telegram ke chat_id {$chatId}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error mengirim pesan Telegram ke chat_id {$chatId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan chat_id dari username Telegram
     * Catatan: Bot harus sudah pernah berinteraksi dengan user untuk mendapatkan chat_id
     * 
     * Untuk mendapatkan chat_id, user harus:
     * 1. Mencari bot di Telegram (gunakan username bot)
     * 2. Memulai chat dengan bot (kirim /start)
     * 3. Sistem akan menyimpan chat_id dari update yang diterima
     *
     * @param string $username Username Telegram (tanpa @)
     * @return string|int|null
     */
    protected function getChatIdByUsername(string $username): string|int|null
    {
        try {
            // Coba dapatkan chat_id dari update terakhir
            // Metode ini memerlukan bot sudah pernah berinteraksi dengan user
            $response = Http::get("{$this->apiUrl}/getUpdates", [
            $response = Http::timeout(5)->get("{$this->apiUrl}/getUpdates", [
                'offset' => 0,
                'limit' => 100, // Ambil 100 update terakhir
            ]);

            if ($response->successful()) {
                $updates = $response->json('result', []);

                if (!is_array($updates)) {
                    return null;
                }

                Log::info("Mendapatkan " . count($updates) . " update(s) dari Telegram API");

                // Cari dari update terbaru ke terlama
                $updates = array_reverse($updates);

                foreach ($updates as $update) {
                    $message = $update['message'] ?? null;
                    if ($message) {
                        $from = $message['from'] ?? [];
                        $chat = $message['chat'] ?? [];

                        // Cek apakah username cocok (case-insensitive)
                        $fromUsername = strtolower($from['username'] ?? '');
                        $chatUsername = strtolower($chat['username'] ?? '');
                        $searchUsername = strtolower($username);

                        Log::debug("Mencocokkan: from={$fromUsername}, chat={$chatUsername}, search={$searchUsername}");

                        if ($fromUsername === $searchUsername || $chatUsername === $searchUsername) {
                            $chatId = $chat['id'] ?? null;
                            if ($chatId) {
                                Log::info("Chat ID ditemukan untuk @{$username}: {$chatId}");
                                return $chatId;
                            }
                        }
                    }
                }

                Log::warning("Username @{$username} tidak ditemukan dalam " . count($updates) . " update terakhir");
            } else {
                Log::error("Gagal mendapatkan updates dari Telegram API: " . $response->body());
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error mendapatkan chat_id untuk username {$username}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Format pesan untuk notifikasi tiket
     *
     * @param string $title Judul notifikasi
     * @param array $details Detail notifikasi
     * @param string|null $url URL untuk aksi
     * @return string
     */
    public function formatTicketMessage(string $title, array $details, ?string $url = null): string
    {
        $message = "<b>{$title}</b>\n\n";

        foreach ($details as $label => $value) {
            $message .= "<b>{$label}:</b> {$value}\n";
        }

        if ($url) {
            $message .= "\n<a href=\"{$url}\">Lihat Detail</a>";
        }

        return $message;
    }
}
