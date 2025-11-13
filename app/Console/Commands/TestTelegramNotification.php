<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramNotification extends Command
{
    protected $signature = 'telegram:test {email?} {--message=}';
    protected $description = 'Test mengirim notifikasi Telegram ke user';

    public function handle()
    {
        $email = $this->argument('email');
        $customMessage = $this->option('message') ?? 'Ini adalah pesan test dari sistem CSIRT Kalselprov.';

        if (!$email) {
            $email = $this->ask('Masukkan email user yang akan di-test');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }

        if (empty($user->telegram_username)) {
            $this->error("User {$email} tidak memiliki telegram_username!");
            return 1;
        }

        $this->info("Mengirim pesan test ke @{$user->telegram_username}...");

        $telegramService = app(TelegramService::class);
        $success = $telegramService->sendMessage($user->telegram_username, $customMessage);

        if ($success) {
            $this->info("✅ Pesan berhasil dikirim!");
            if ($user->telegram_chat_id) {
                $this->info("Chat ID: {$user->telegram_chat_id}");
            }
        } else {
            $this->error("❌ Gagal mengirim pesan. Cek log untuk detail error.");
            $this->warn("Pastikan:");
            $this->warn("1. Bot token sudah dikonfigurasi di .env");
            $this->warn("2. User sudah memulai chat dengan bot (kirim /start)");
            $this->warn("3. Username Telegram benar: @{$user->telegram_username}");
        }

        return 0;
    }
}

