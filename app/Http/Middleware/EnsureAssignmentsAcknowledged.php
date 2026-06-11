<?php

namespace App\Http\Middleware;

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

        try {
            $hasPendingAssignment = AssignmentAcknowledgment::hasPending($user, $request);
        } catch (\Throwable $e) {
            \Log::warning('Assignment acknowledgment check failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return $next($request);
        }

        if ($hasPendingAssignment) {
            return redirect()
                ->route('agent.dashboard')
                ->withErrors([
                    'assignment' => 'Anda memiliki surat tugas baru. Buka popup dan konfirmasi terlebih dahulu sebelum mengakses tiket.',
                ]);
        }

        return $next($request);
    }
}
