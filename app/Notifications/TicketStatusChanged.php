<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\Status;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public Status $oldStatus, public Status $newStatus)
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

        // Tentukan pesan berdasarkan slug atau nama status
        $statusSlug = strtolower($this->newStatus->slug);
        $statusName = strtolower($this->newStatus->name);

        $message = '';
        if (str_contains($statusSlug, 'progress') || str_contains($statusName, 'dalam proses')) {
            $message = 'Laporan insiden siber Anda sedang dalam proses penanganan oleh tim CSIRT Kalselprov.';
        } elseif (str_contains($statusSlug, 'closed') || str_contains($statusName, 'tertutup')) {
            $message = 'Laporan insiden siber Anda telah selesai ditangani dan ditutup oleh tim CSIRT Kalselprov.';
        } elseif (str_contains($statusSlug, 'assigned') || str_contains($statusName, 'ditugaskan')) {
            $message = 'Laporan insiden siber Anda telah ditugaskan kepada agent untuk ditangani.';
        } else {
            $message = 'Status laporan insiden siber Anda telah diubah menjadi **' . $this->newStatus->name . '**.';
        }

        $mail = (new MailMessage)
            ->subject('Status Laporan Insiden Siber Diperbarui - ' . $this->ticket->ticket_number)
            ->greeting('Halo ' . ($this->ticket->requester_name ?? $this->ticket->requester_email) . ',')
            ->line('Status laporan insiden siber Anda dengan nomor **' . $this->ticket->ticket_number . '** telah diperbarui.')
            ->line('**Status Sebelumnya:** ' . $this->oldStatus->name)
            ->line('**Status Baru:** ' . $this->newStatus->name)
            ->line('')
            ->line($message)
            ->line('**Nomor Laporan:** ' . $this->ticket->ticket_number)
            ->line('**Subjek:** ' . $this->ticket->subject);

        $statusSlug = strtolower($this->newStatus->slug);
        $statusName = strtolower($this->newStatus->name);

        if (str_contains($statusSlug, 'closed') || str_contains($statusName, 'tertutup')) {
            $mail->line('Terima kasih telah melaporkan insiden siber kepada kami. Tim CSIRT Kalselprov telah selesai menangani laporan Anda.')
                ->line('Jika Anda memiliki pertanyaan atau memerlukan informasi tambahan, jangan ragu untuk menghubungi kami.');
        } else {
            $mail->line('Kami akan terus memberikan pembaruan terkait penanganan laporan Anda.');
        }

        $mail->action('Lihat Status Laporan', $url)
            ->line('Anda dapat melihat detail lengkap dan riwayat komunikasi melalui link di atas.')
            ->salutation('Salam,')
            ->line('**Tim CSIRT Kalselprov**')
            ->line('Computer Security Incident Response Team');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'old_status' => $this->oldStatus->name,
            'new_status' => $this->newStatus->name,
            'message' => 'Status laporan telah diubah.',
        ];
    }

    public function toTelegram(object $notifiable): ?string
    {
        $url = route('portal.ticket.show', $this->ticket->ticket_number);

        // Tentukan pesan berdasarkan slug atau nama status
        $statusSlug = strtolower($this->newStatus->slug);
        $statusName = strtolower($this->newStatus->name);

        $statusMessage = '';
        if (str_contains($statusSlug, 'progress') || str_contains($statusName, 'dalam proses')) {
            $statusMessage = 'Laporan insiden siber Anda sedang dalam proses penanganan oleh tim CSIRT Kalselprov.';
        } elseif (str_contains($statusSlug, 'closed') || str_contains($statusName, 'tertutup')) {
            $statusMessage = 'Laporan insiden siber Anda telah selesai ditangani dan ditutup oleh tim CSIRT Kalselprov.';
        } elseif (str_contains($statusSlug, 'assigned') || str_contains($statusName, 'ditugaskan')) {
            $statusMessage = 'Laporan insiden siber Anda telah ditugaskan kepada agent untuk ditangani.';
        } else {
            $statusMessage = 'Status laporan insiden siber Anda telah diubah menjadi <b>' . $this->newStatus->name . '</b>.';
        }

        $message = "<b>🔄 Status Laporan Diperbarui</b>\n\n";
        $message .= "Status laporan insiden siber Anda dengan nomor <b>{$this->ticket->ticket_number}</b> telah diperbarui.\n\n";
        $message .= "<b>Status Sebelumnya:</b> {$this->oldStatus->name}\n";
        $message .= "<b>Status Baru:</b> <b>{$this->newStatus->name}</b>\n\n";
        $message .= "{$statusMessage}\n\n";
        $message .= "<b>📋 Detail:</b>\n";
        $message .= "• <b>Nomor Laporan:</b> {$this->ticket->ticket_number}\n";
        $message .= "• <b>Subjek:</b> {$this->ticket->subject}\n";

        if (str_contains($statusSlug, 'closed') || str_contains($statusName, 'tertutup')) {
            $message .= "\nTerima kasih telah melaporkan insiden siber kepada kami. Tim CSIRT Kalselprov telah selesai menangani laporan Anda.\n";
        } else {
            $message .= "\nKami akan terus memberikan pembaruan terkait penanganan laporan Anda.\n";
        }

        $message .= "\n<a href=\"{$url}\">🔗 Lihat Status Laporan</a>";

        return $message;
    }
}

