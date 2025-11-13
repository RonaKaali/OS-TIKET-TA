<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetTelegramChatId extends Command
{
    protected $signature = 'telegram:set-chat-id {email} {chat_id}';
    protected $description = 'Set chat_id Telegram secara manual untuk user';

    public function handle()
    {
        $email = $this->argument('email');
        $chatId = $this->argument('chat_id');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }

        $user->telegram_chat_id = $chatId;
        $user->save();

        $this->info("✅ Chat ID {$chatId} telah disimpan untuk user {$email}");
        $this->info("Username: @{$user->telegram_username}");

        return 0;
    }
}

