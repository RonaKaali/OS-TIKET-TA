<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('dashboard');

        return (new MailMessage)
            ->subject('Selamat Datang di CSIRT Kalselprov')
            ->greeting('Selamat Datang, ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di sistem CSIRT Kalselprov.')
            ->line('Akun Anda telah berhasil dibuat dan Anda dapat mulai menggunakan sistem.')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Nama:** ' . $notifiable->name)
            ->action('Masuk ke Dashboard', $url)
            ->line('Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi tim CSIRT.')
            ->salutation('Terima kasih,')
            ->line('**CSIRT Kalselprov**')
            ->line('Computer Security Incident Response Team');
    }
}
