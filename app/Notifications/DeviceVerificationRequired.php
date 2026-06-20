<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class DeviceVerificationRequired extends Notification
{
    use Queueable;

    public function __construct(
        protected string $fingerprint,
        protected ?string $ipAddress = null,
        protected ?string $userAgent = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'device.verify',
            now()->addMinutes(30),
            ['fingerprint' => $this->fingerprint]
        );

        return (new MailMessage)
            ->subject('Verifikasi Perangkat Baru')
            ->greeting('Verifikasi perangkat baru diperlukan')
            ->line('Sistem mendeteksi akses dari perangkat atau konteks baru pada akun Anda.')
            ->line('IP: ' . ($this->ipAddress ?: '-'))
            ->line('User-Agent: ' . ($this->userAgent ?: '-'))
            ->action('Verifikasi Perangkat', $url)
            ->line('Tautan ini berlaku selama 30 menit. Abaikan email ini jika aktivitas tersebut bukan dari Anda dan segera ubah password akun.');
    }
}
