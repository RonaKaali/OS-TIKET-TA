<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use App\Support\AssignmentAcknowledgment;
use App\Support\RoleUi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAssignmentsAcknowledged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !RoleUi::isFieldAgent($user)) {
            return $next($request);
        }

        $map = AssignmentAcknowledgment::map($request);

        // Cari tiket yang masih pending (belum di-acknowledge baik session maupun DB)
        $pending = Ticket::query()
            ->where('assigned_to', $user->id)
            ->whereHas('status', fn($q) => $q->whereIn('slug', ['assigned', 'in_progress']))
            ->get()
            ->filter(fn(Ticket $ticket) => !AssignmentAcknowledgment::isAcknowledged($ticket, $map) && is_null($ticket->acknowledged_at));

        if ($pending->isNotEmpty()) {
            return redirect()
                ->route('agent.dashboard')
                ->withErrors([
                    'assignment' => 'Anda memiliki surat tugas baru yang belum dikonfirmasi. Silakan buka tiket tugas tersebut untuk mengonfirmasi.',
                ]);
        }

        // Sinkronisasi: tiket yang sudah acknowledged di DB tapi belum di session
        $dbAcknowledged = Ticket::query()
            ->where('assigned_to', $user->id)
            ->whereNotNull('acknowledged_at')
            ->pluck('id');

        if ($dbAcknowledged->isNotEmpty()) {
            AssignmentAcknowledgment::acknowledge($request, $user, $dbAcknowledged->toArray());
        }

        return $next($request);
    }
}
