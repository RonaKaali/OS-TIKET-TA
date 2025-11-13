<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public ?TicketThread $thread = null)
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

        $mail = (new MailMessage)
            ->subject('Laporan Insiden Siber Anda Telah Diterima - ' . $this->ticket->ticket_number)
            ->greeting('Halo ' . ($this->ticket->requester_name ?? $this->ticket->requester_email) . ',')
            ->line('Laporan insiden siber Anda dengan nomor **' . $this->ticket->ticket_number . '** telah berhasil kami terima.')
            ->line('Berikut adalah detail laporan Anda:')
            ->line('**Nomor Laporan:** ' . $this->ticket->ticket_number)
            ->line('**Subjek:** ' . $this->ticket->subject)
            ->line('**Status:** ' . ($this->ticket->status->name ?? 'Terbuka'))
            ->line('**Departemen:** ' . ($this->ticket->department->name ?? 'N/A'))
            ->line('**Prioritas:** ' . ($this->ticket->priority->name ?? 'Normal'))
            ->line('**Dibuat:** ' . $this->ticket->created_at->format('d/m/Y H:i'));

        if ($this->ticket->due_at) {
            $mail->line('**Batas Waktu:** ' . $this->ticket->due_at->format('d/m/Y H:i'));
        }

        $mail->line('')
            ->line('Tim CSIRT Kalselprov akan segera meninjau laporan Anda dan memberikan pembaruan secepatnya.')
            ->action('Lihat Status Laporan', $url)
            ->line('Anda dapat melacak status laporan melalui link di atas atau menggunakan nomor laporan dan email Anda.')
            ->line('Terima kasih atas laporan Anda.')
            ->salutation('Salam,')
            ->line('**Tim CSIRT Kalselprov**')
            ->line('Computer Security Incident Response Team');

        // Lampirkan file yang dikirim saat pembuatan tiket (jika ada)
        if ($this->thread) {
            foreach ($this->thread->attachments as $att) {
                $path = storage_path('app/public/' . $att->path);
                if (is_file($path)) {
                    $mail->attach($path, [
                        'as' => $att->filename,
                        'mime' => $att->mime,
                    ]);
                }
            }
        }

        return $mail;
    }

    public function toTelegram(object $notifiable): ?string
    {
        $url = route('portal.ticket.show', $this->ticket->ticket_number);

        $message = "<b>✅ Laporan Insiden Siber Diterima</b>\n\n";
        $message .= "Laporan insiden siber Anda dengan nomor <b>{$this->ticket->ticket_number}</b> telah berhasil kami terima.\n\n";
        $message .= "<b>📋 Detail Laporan:</b>\n";
        $message .= "• <b>Nomor Laporan:</b> {$this->ticket->ticket_number}\n";
        $message .= "• <b>Subjek:</b> {$this->ticket->subject}\n";
        $message .= "• <b>Status:</b> " . ($this->ticket->status->name ?? 'Terbuka') . "\n";
        $message .= "• <b>Departemen:</b> " . ($this->ticket->department->name ?? 'N/A') . "\n";
        $message .= "• <b>Prioritas:</b> " . ($this->ticket->priority->name ?? 'Normal') . "\n";
        $message .= "• <b>Dibuat:</b> " . $this->ticket->created_at->format('d/m/Y H:i') . "\n";

        if ($this->ticket->due_at) {
            $message .= "• <b>Batas Waktu:</b> " . $this->ticket->due_at->format('d/m/Y H:i') . "\n";
        }

        $message .= "\nTim CSIRT Kalselprov akan segera meninjau laporan Anda dan memberikan pembaruan secepatnya.\n\n";
        $message .= "<a href=\"{$url}\">🔗 Lihat Status Laporan</a>\n\n";
        $message .= "Terima kasih atas laporan Anda.";

        return $message;
    }
}
