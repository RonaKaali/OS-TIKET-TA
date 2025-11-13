<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetTelegramWebhookInfo extends Command
{
    protected $signature = 'telegram:get-webhook-info';
    protected $description = 'Mendapatkan informasi webhook Telegram bot';

    public function handle()
    {
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            $this->error('Bot token tidak dikonfigurasi di .env!');
            return 1;
        }

        $apiUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";

        $this->info("Mendapatkan informasi webhook...");

        try {
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['ok'] ?? false) {
                    $webhookInfo = $result['result'] ?? [];

                    $this->info('📋 Informasi Webhook:');
                    $this->info('URL: ' . ($webhookInfo['url'] ?? 'Tidak di-set'));
                    $this->info('Pending Update Count: ' . ($webhookInfo['pending_update_count'] ?? 0));

                    if (isset($webhookInfo['last_error_date'])) {
                        $this->warn('⚠️  Last Error Date: ' . date('Y-m-d H:i:s', $webhookInfo['last_error_date']));
                        $this->warn('⚠️  Last Error Message: ' . ($webhookInfo['last_error_message'] ?? 'N/A'));
                    }

                    if (isset($webhookInfo['max_connections'])) {
                        $this->info('Max Connections: ' . $webhookInfo['max_connections']);
                    }
                } else {
                    $this->error('❌ Gagal mendapatkan info webhook');
                    return 1;
                }
            } else {
                $this->error('❌ Gagal mendapatkan info webhook: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

