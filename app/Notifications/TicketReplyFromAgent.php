<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyFromAgent extends Notification
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
        $url = route('portal.ticket.show', $this->ticket->ticket_number);
        $agentName = $this->thread->user ? $this->thread->user->name : 'Tim CSIRT';

        $mail = (new MailMessage)
            ->subject('Balasan untuk Laporan Anda - ' . $this->ticket->ticket_number)
            ->greeting('Halo ' . ($this->ticket->requester_name ?: 'Yang Terhormat') . ',')
            ->line('Anda menerima balasan dari **' . $agentName . '** untuk laporan insiden siber Anda.')
            ->line('**Nomor Laporan:** ' . $this->ticket->ticket_number)
            ->line('**Subjek:** ' . $this->ticket->subject)
            ->line('**Balasan:**')
            ->line($this->thread->body)
            ->action('Lihat Detail & Balas', $url)
            ->line('Silakan login atau gunakan email Anda untuk melihat detail lengkap dan memberikan balasan jika diperlukan.')
            ->salutation('Terima kasih,')
            ->line('**CSIRT Kalselprov**');

        // Lampirkan file yang dikirim bersama balasan (jika ada)
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
        $url = route('portal.ticket.show', $this->ticket->ticket_number);
        $agentName = $this->thread->user ? $this->thread->user->name : 'Tim CSIRT';

        $message = "<b>💬 Balasan untuk Laporan Anda</b>\n\n";
        $message .= "Anda menerima balasan dari <b>{$agentName}</b> untuk laporan insiden siber Anda.\n\n";
        $message .= "<b>📋 Detail:</b>\n";
        $message .= "• <b>Nomor Laporan:</b> {$this->ticket->ticket_number}\n";
        $message .= "• <b>Subjek:</b> {$this->ticket->subject}\n\n";
        $message .= "<b>💬 Balasan:</b>\n";
        $message .= htmlspecialchars($this->thread->body) . "\n\n";
        $message .= "<a href=\"{$url}\">🔗 Lihat Detail & Balas</a>\n\n";
        $message .= "Silakan login atau gunakan email Anda untuk melihat detail lengkap dan memberikan balasan jika diperlukan.";

        return $message;
    }
}
