<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url?}';
    protected $description = 'Set webhook URL untuk Telegram bot';

    public function handle()
    {
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            $this->error('Bot token tidak dikonfigurasi di .env!');
            return 1;
        }

        $url = $this->argument('url');

        if (!$url) {
            $url = $this->ask('Masukkan URL webhook (contoh: https://yourdomain.com/telegram/webhook)');
        }

        $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";

        $this->info("Mengatur webhook ke: {$url}");

        try {
            $response = Http::post($apiUrl, [
                'url' => $url,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['ok'] ?? false) {
                    $this->info('✅ Webhook berhasil di-set!');
                    $this->info("URL: {$url}");
                    if (isset($result['description'])) {
                        $this->info("Description: {$result['description']}");
                    }
                } else {
                    $this->error('❌ Gagal set webhook: ' . ($result['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ Gagal set webhook: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

