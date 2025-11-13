<?php

namespace App\Notifications\Channels;

use App\Services\TelegramService;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            if (!method_exists($notification, 'toTelegram')) {
                \Log::debug("Notification " . get_class($notification) . " tidak memiliki method toTelegram");
                return;
            }

            // Cek apakah user memiliki telegram_username
            $telegramUsername = $notifiable->telegram_username ?? null;

            if (empty($telegramUsername)) {
                \Log::info("User {$notifiable->email} tidak memiliki telegram_username, skip notifikasi Telegram");
                return;
            }

            \Log::info("Mengirim notifikasi Telegram ke @{$telegramUsername} untuk user {$notifiable->email}");

            $message = $notification->toTelegram($notifiable);

            if (!$message) {
                \Log::warning("Method toTelegram mengembalikan null untuk notification " . get_class($notification));
                return;
            }

            // Kirim pesan via Telegram
            $success = $this->telegramService->sendMessage($telegramUsername, $message);

            if ($success) {
                \Log::info("Notifikasi Telegram berhasil dikirim ke @{$telegramUsername}");
            } else {
                \Log::error("Gagal mengirim notifikasi Telegram ke @{$telegramUsername}");
            }
        } catch (\Throwable $e) {
            \Log::error("Error di TelegramChannel: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}

