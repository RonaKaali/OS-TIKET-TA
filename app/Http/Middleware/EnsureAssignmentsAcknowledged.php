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

        if (AssignmentAcknowledgment::hasPending($user, $request)) {
            return redirect()
                ->route('agent.dashboard')
                ->withErrors([
                    'assignment' => 'Anda memiliki surat tugas baru. Buka popup dan konfirmasi terlebih dahulu sebelum mengakses tiket.',
                ]);
        }

        return $next($request);
    }
}
