<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketCreated extends Notification
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
        $url = route('agent.tickets.show', $this->ticket);

        $mail = (new MailMessage)
            ->subject('Laporan Insiden Siber Baru - ' . $this->ticket->ticket_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Terdapat laporan insiden siber baru yang masuk ke sistem.')
            ->line('Berikut adalah detail laporan:')
            ->line('**Nomor Laporan:** ' . $this->ticket->ticket_number)
            ->line('**Subjek:** ' . $this->ticket->subject)
            ->line('**Pelapor:** ' . ($this->ticket->requester_name ?? $this->ticket->requester_email))
            ->line('**Email Pelapor:** ' . $this->ticket->requester_email)
            ->line('**Status:** ' . ($this->ticket->status->name ?? 'Terbuka'))
            ->line('**Departemen:** ' . ($this->ticket->department->name ?? 'N/A'))
            ->line('**Prioritas:** ' . ($this->ticket->priority->name ?? 'Normal'))
            ->line('**Dibuat:** ' . $this->ticket->created_at->format('d/m/Y H:i'));

        if ($this->ticket->due_at) {
            $mail->line('**Batas Waktu:** ' . $this->ticket->due_at->format('d/m/Y H:i'));
        }

        $mail->line('')
            ->action('Lihat Detail Laporan', $url)
            ->line('Silakan login ke dashboard untuk meninjau dan menangani laporan ini segera.')
            ->line('Terima kasih atas perhatian Anda.')
            ->salutation('Salam,')
            ->line('**Sistem CSIRT Kalselprov**');

        // Lampirkan file dari pelapor saat pembuatan tiket (jika ada)
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
        $url = route('agent.tickets.show', $this->ticket);

        $message = "<b>🔔 Laporan Insiden Siber Baru</b>\n\n";
        $message .= "Terdapat laporan insiden siber baru yang masuk ke sistem.\n\n";
        $message .= "<b>📋 Detail Laporan:</b>\n";
        $message .= "• <b>Nomor Laporan:</b> {$this->ticket->ticket_number}\n";
        $message .= "• <b>Subjek:</b> {$this->ticket->subject}\n";
        $message .= "• <b>Pelapor:</b> " . ($this->ticket->requester_name ?? $this->ticket->requester_email) . "\n";
        $message .= "• <b>Email Pelapor:</b> {$this->ticket->requester_email}\n";
        $message .= "• <b>Status:</b> " . ($this->ticket->status->name ?? 'Terbuka') . "\n";
        $message .= "• <b>Departemen:</b> " . ($this->ticket->department->name ?? 'N/A') . "\n";
        $message .= "• <b>Prioritas:</b> " . ($this->ticket->priority->name ?? 'Normal') . "\n";
        $message .= "• <b>Dibuat:</b> " . $this->ticket->created_at->format('d/m/Y H:i') . "\n";

        if ($this->ticket->due_at) {
            $message .= "• <b>Batas Waktu:</b> " . $this->ticket->due_at->format('d/m/Y H:i') . "\n";
        }

        $message .= "\n<a href=\"{$url}\">🔗 Lihat Detail Laporan</a>\n\n";
        $message .= "Silakan login ke dashboard untuk meninjau dan menangani laporan ini segera.";

        return $message;
    }
}
