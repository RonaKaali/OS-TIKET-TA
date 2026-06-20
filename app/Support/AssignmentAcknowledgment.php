<?php

namespace App\Support;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssignmentAcknowledgment
{
    public const SESSION_KEY = 'acknowledged_assignments';

    /**
     * @return array<int, int> ticket_id => assigned_at timestamp
     */
    public static function map(Request $request): array
    {
        $raw = $request->session()->get(self::SESSION_KEY, []);

        if (!is_array($raw)) {
            return [];
        }

        $map = [];
        foreach ($raw as $ticketId => $timestamp) {
            $map[(int) $ticketId] = (int) $timestamp;
        }

        // Sinkronisasi: tambahkan tiket yang sudah acknowledged di DB tapi belum di session
        if ($request->user()) {
            $dbAcknowledged = Ticket::query()
                ->where('assigned_to', $request->user()->id)
                ->whereNotNull('acknowledged_at')
                ->get();

            foreach ($dbAcknowledged as $ticket) {
                $ts = $ticket->acknowledged_at?->timestamp;
                if ($ts && !isset($map[$ticket->id])) {
                    $map[$ticket->id] = $ts;
                }
            }
        }

        return $map;
    }

    public static function isAcknowledged(Ticket $ticket, array $map): bool
    {
        // Cek DB acknowledged_at terlebih dahulu
        if ($ticket->acknowledged_at !== null) {
            return true;
        }

        // Fallback ke session
        $stored = $map[$ticket->id] ?? null;
        if ($stored === null) {
            return false;
        }

        $current = $ticket->assigned_at?->timestamp ?? $ticket->updated_at?->timestamp;

        return $current !== null && (int) $stored === (int) $current;
    }

    public static function pendingFor(User $user, array $map): Collection
    {
        return Ticket::query()
            ->with(['status', 'priority', 'department'])
            ->where('assigned_to', $user->id)
            ->whereHas('status', fn ($q) => $q->whereIn('slug', ['assigned', 'in_progress']))
            ->orderByDesc('assigned_at')
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn (Ticket $ticket) => !self::isAcknowledged($ticket, $map));
    }

    public static function hasPending(User $user, Request $request): bool
    {
        return self::pendingFor($user, self::map($request))->isNotEmpty();
    }

    public static function acknowledge(Request $request, User $user, array $ticketIds): void
    {
        $map = self::map($request);

        $tickets = Ticket::where('assigned_to', $user->id)
            ->whereIn('id', $ticketIds)
            ->get();

        foreach ($tickets as $ticket) {
            $map[$ticket->id] = $ticket->assigned_at?->timestamp ?? now()->timestamp;

            // Juga update acknowledged_at di DB jika masih null
            if (is_null($ticket->acknowledged_at)) {
                $ticket->update(['acknowledged_at' => now()]);
            }
        }

        $request->session()->put(self::SESSION_KEY, $map);
    }
}
