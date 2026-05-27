<?php

namespace App\Providers;

use App\Notifications\Channels\TelegramChannel;
use App\Services\TelegramService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (Vercel) to prevent mixed content errors
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register Telegram notification channel
        Notification::extend('telegram', function ($app) {
            return new TelegramChannel($app->make(TelegramService::class));
        });
    }
}
