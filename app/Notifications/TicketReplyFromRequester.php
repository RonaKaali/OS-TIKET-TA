<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyFromRequester extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public TicketThread $thread)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        // Tambahkan telegram jika user memiliki telegram_username
        if (!empty($notifiable->telegram_username)) {
            $channels[] = 'telegram';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('agent.tickets.show', $this->ticket);

        $mail = (new MailMessage)
            ->subject('Balasan Baru dari Pelapor - ' . $this->ticket->ticket_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pelapor telah memberikan balasan untuk laporan insiden siber yang ditugaskan kepada Anda.')
            ->line('**Nomor Laporan:** ' . $this->ticket->ticket_number)
            ->line('**Subjek:** ' . $this->ticket->subject)
            ->line('**Pelapor:** ' . ($this->ticket->requester_name ?: $this->ticket->requester_email))
            ->line('**Balasan:**')
            ->line($this->thread->body)
            ->action('Lihat Detail & Balas', $url)
            ->line('Silakan login ke dashboard untuk melihat detail lengkap dan memberikan respons.')
            ->salutation('Terima kasih,')
            ->line('**Sistem CSIRT Kalselprov**');

        // Lampirkan file dari pelapor (jika ada)
        foreach ($this->thread->attachments as $att) {
            $path = storage_path('app/public/' . $att->path);
            if (is_file($path)) {
                $mail->attach($path, [
                    'as' => $att->filename,
                    'mime' => $att->mime,
                ]);
            }
        }

        return $mail;
    }

    public function toTelegram(object $notifiable): ?string
    {
        $url = route('agent.tickets.show', $this->ticket);

        $message = "<b>💬 Balasan Baru dari Pelapor</b>\n\n";
        $message .= "Pelapor telah memberikan balasan untuk laporan insiden siber yang ditugaskan kepada Anda.\n\n";
        $message .= "<b>📋 Detail:</b>\n";
        $message .= "• <b>Nomor Laporan:</b> {$this->ticket->ticket_number}\n";
        $message .= "• <b>Subjek:</b> {$this->ticket->subject}\n";
        $message .= "• <b>Pelapor:</b> " . ($this->ticket->requester_name ?: $this->ticket->requester_email) . "\n\n";
        $message .= "<b>💬 Balasan:</b>\n";
        $message .= htmlspecialchars($this->thread->body) . "\n\n";
        $message .= "<a href=\"{$url}\">🔗 Lihat Detail & Balas</a>\n\n";
        $message .= "Silakan login ke dashboard untuk melihat detail lengkap dan memberikan respons.";

        return $message;
    }
}
