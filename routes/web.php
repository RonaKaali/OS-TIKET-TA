<?php

use App\Http\Controllers\Portal\TicketController as PortalTicket;
use App\Http\Controllers\Agent\{
    DashboardController as AgentDashboard,
    TicketController as AgentTicket,
    AssignmentController as AgentAssignment,
    NewAssignmentController as AgentNewAssignment,
    NoteController as AgentNote
};
use App\Http\Controllers\Admin\{
    DepartmentController,
    HelpTopicController,
    SlaPlanController,
    PriorityController,
    StatusController,
    TeamController,
    CannedResponseController,
    OrganizationController,
    UserController,
    ChatbotResponseController,
    SecurityDashboardController
};
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ZeroTrustGpsController;
use Illuminate\Http\Request;

Route::get('/', fn() => view('welcome'))->name('welcome');

# Chatbot API (public access, rate-limited: 20 request per menit per IP)
Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->name('chatbot.message')
    ->middleware('throttle:20,1');

# Portal (Harus login untuk melaporkan)
Route::prefix('portal')->group(function () {
    // Route untuk melaporkan - memerlukan login
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('portal.dashboard');
        Route::get('ticket/new', [PortalTicket::class, 'create'])->name('portal.ticket.create');
        Route::post('ticket', [PortalTicket::class, 'store'])->name('portal.ticket.store');
        Route::post('ticket/{number}/reply', [PortalTicket::class, 'reply'])->name('portal.ticket.reply');
    });

    // Route untuk cek status dan lihat tiket - bisa tanpa login (setelah verifikasi)
    Route::get('ticket/status', [PortalTicket::class, 'statusForm'])->name('portal.ticket.status.form');
    Route::post('ticket/status', [PortalTicket::class, 'statusCheck'])->name('portal.ticket.status.check');
    Route::get('ticket/{number}', [PortalTicket::class, 'show'])->name('portal.ticket.show'); // bisa diakses setelah verifikasi atau jika login
});

# Auth bawaan Breeze
require __DIR__ . '/auth.php';

# Dashboard (redirect ke agent dashboard - hanya untuk admin/agent)
Route::middleware(['auth', 'permission:admin.panel'])->get('/dashboard', function () {
    return redirect()->route('agent.dashboard');
})->name('dashboard');

# Panel Agen
Route::middleware(['auth', 'permission:admin.panel'])->prefix('agent')->group(function () {
    Route::get('/', AgentDashboard::class)->name('agent.dashboard');
    Route::get('/assignments/pending', [AgentNewAssignment::class, 'index'])->name('agent.assignments.pending');
    Route::post('/assignments/acknowledge', [AgentNewAssignment::class, 'acknowledge'])->name('agent.assignments.acknowledge');

    Route::get('/tickets', [AgentTicket::class, 'index'])->name('agent.tickets.index');
    Route::get('/tickets/{ticket}', [AgentTicket::class, 'show'])->name('agent.tickets.show');
    Route::post('/tickets/{ticket}/reply', [AgentTicket::class, 'reply'])->name('agent.tickets.reply');
    Route::post('/tickets/{ticket}/status', [AgentTicket::class, 'setStatus'])->name('agent.tickets.status');
    Route::post('/tickets/{ticket}/note', AgentNote::class)->name('agent.tickets.note');

    Route::post('/tickets/{ticket}/assign', AgentAssignment::class)
        ->name('agent.tickets.assign')
        ->middleware('permission:tickets.assign');
});

# Panel Admin (Hanya Super Admin)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('index');
    Route::get('/reports', [App\Http\Controllers\Admin\DashboardController::class, 'reports'])->name('reports');

    Route::resource('departments', DepartmentController::class)->except('show');
    Route::resource('help-topics', HelpTopicController::class)->except('show');
    Route::resource('sla', SlaPlanController::class)->except('show');
    Route::resource('priorities', PriorityController::class)->except('show');
    Route::resource('statuses', StatusController::class)->except('show');
    Route::resource('teams', TeamController::class)->except('show');
    Route::resource('canned', CannedResponseController::class)->except('show');
    Route::resource('organizations', OrganizationController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
    Route::resource('chatbot-responses', ChatbotResponseController::class);

    // Zero Trust Security Dashboard
    Route::get('/security-dashboard', [SecurityDashboardController::class, 'index'])->name('security.dashboard');
    Route::get('/api/security-events/latest', [SecurityDashboardController::class, 'getLatestEvents'])->name('security.api.latest');
    Route::get('/api/security-events/export', [SecurityDashboardController::class, 'exportLogs'])->name('security.api.export');
    Route::post('/api/security-events/revoke/{user}', [SecurityDashboardController::class, 'revokeAccess'])->name('security.api.revoke');
});

# Profile (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Zero Trust: Automatic Device Verification Route
    Route::get('/device/verify', function(Request $request) {
        $fingerprint = session('fingerprint');
        if ($fingerprint) {
            $request->session()->put('device_verified_' . $fingerprint, true);
            app(\App\Services\SecurityEventLogService::class)->logDeviceEvent(
                auth()->id(), 
                'device_verified', 
                ['fingerprint' => $fingerprint]
            );
        }
        return redirect()->intended('/');
    })->name('device.verify');

    // Session check untuk auto logout
    Route::get('/session/check', [\App\Http\Controllers\SessionController::class, 'check'])->name('session.check');
    
    // Download attachment (dengan dekripsi otomatis)
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->name('attachments.download');

    // Update GPS location untuk Zero Trust (opsional, berdasarkan izin browser)
    Route::post('/zero-trust/gps', [ZeroTrustGpsController::class, 'store'])
        ->name('zero_trust.gps.update');
});

# Telegram Webhook (untuk menerima update dari bot Telegram)
Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');

// Route deploy-db: hanya bisa diakses dengan secret token via env DEPLOY_SECRET
// Contoh: /deploy-db?secret=isi_DEPLOY_SECRET_di_env
Route::get('/deploy-db', function () {
    // Validasi secret token — wajib diisi di .env sebagai DEPLOY_SECRET
    $secret = env('DEPLOY_SECRET', '');
    if (empty($secret) || request()->query('secret') !== $secret) {
        abort(403, 'Akses ditolak. Secret token tidak valid.');
    }

    $lines = [];

    try {
        $lines[] = 'Menjalankan migrasi Laravel...';
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $lines[] = trim(\Illuminate\Support\Facades\Artisan::output()) ?: 'Migrasi selesai.';

        $lines[] = 'Memastikan kolom MFA (SQL langsung)...';
        foreach (\App\Support\MfaSchema::ensureColumns() as $line) {
            $lines[] = $line;
        }

        $lines[] = 'Memastikan kolom GPS (SQL langsung)...';
        foreach (\App\Support\GpsSchema::ensureColumns() as $line) {
            $lines[] = $line;
        }

        $lines[] = 'Memastikan kolom Cabut Akses (SQL langsung)...';
        foreach (\App\Support\AccessRevocationSchema::ensureColumns() as $line) {
            $lines[] = $line;
        }

        $lines[] = 'Membersihkan cache...';
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');

        $mfaReady = \App\Support\MfaSchema::columnsExist();
        $gpsReady = \App\Support\GpsSchema::columnsExist();
        $revokeReady = \App\Support\AccessRevocationSchema::columnsExist();

        if ($mfaReady && $gpsReady && $revokeReady) {
            $lines[] = 'SELESAI — Kolom MFA, GPS & Cabut Akses siap.';
        } elseif ($mfaReady) {
            $lines[] = 'SELESAI sebagian — Periksa kolom GPS / access_revoked_at di atas.';
        } else {
            $lines[] = 'PERINGATAN — Kolom database belum lengkap. Cek koneksi Supabase.';
        }

        return response('<pre>' . implode("\n", $lines) . '</pre>', 200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    } catch (\Throwable $e) {
        $lines[] = 'GAGAL: ' . $e->getMessage();

        return response('<pre>' . implode("\n", $lines) . '</pre>', 500)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }
});

// Route darurat: fix password + reset revokasi semua user
// Akses: /fix-revoke?secret=ISI_DEPLOY_SECRET
// Hapus route ini setelah masalah teratasi
Route::get('/fix-revoke', function () {
    $secret = env('DEPLOY_SECRET', '');
    if (empty($secret) || request()->query('secret') !== $secret) {
        abort(403, 'Akses ditolak.');
    }

    try {
        $lines = [];

        // 1. Reset access_revoked_at untuk semua user
        $revokedCount = \Illuminate\Support\Facades\DB::table('pengguna')
            ->whereNotNull('access_revoked_at')
            ->count();
        \Illuminate\Support\Facades\DB::table('pengguna')
            ->whereNotNull('access_revoked_at')
            ->update(['access_revoked_at' => null]);
        $lines[] = "✓ Reset access_revoked_at: {$revokedCount} user dipulihkan.";

        // 2. Reset password semua user ke bcrypt('password')
        // Ini diperlukan jika password tersimpan dalam format non-bcrypt
        $newHash = \Illuminate\Support\Facades\Hash::make('password');
        $users = \Illuminate\Support\Facades\DB::table('pengguna')->get(['id', 'name', 'email', 'password']);
        $fixed = 0;
        foreach ($users as $u) {
            // Cek apakah password adalah bcrypt (dimulai dengan $2y$ atau $2b$)
            if (!str_starts_with($u->password, '$2y$') && !str_starts_with($u->password, '$2b$')) {
                \Illuminate\Support\Facades\DB::table('pengguna')
                    ->where('id', $u->id)
                    ->update(['password' => $newHash]);
                $lines[] = "✓ Password di-reset untuk: {$u->email}";
                $fixed++;
            }
        }
        if ($fixed === 0) {
            $lines[] = "✓ Semua password sudah dalam format bcrypt — tidak perlu di-reset.";
        }

        // 3. Tampilkan status semua user
        $lines[] = "";
        $lines[] = "Status semua user:";
        $allUsers = \Illuminate\Support\Facades\DB::table('pengguna')
            ->get(['id', 'name', 'email', 'access_revoked_at', 'password']);
        foreach ($allUsers as $u) {
            $pwFormat = str_starts_with($u->password, '$2y$') || str_starts_with($u->password, '$2b$') ? 'bcrypt ✓' : 'BUKAN bcrypt ✗';
            $revoked  = $u->access_revoked_at ?? 'NULL (OK)';
            $lines[] = "ID:{$u->id} | {$u->email} | pw:{$pwFormat} | revoked:{$revoked}";
        }

        $lines[] = "";
        $lines[] = "SELESAI. Semua user bisa login dengan password: 'password'";

        return response('<pre style="font-family:monospace;font-size:14px;padding:20px">'
            . implode("\n", $lines) . '</pre>', 200)
            ->header('Content-Type', 'text/html; charset=utf-8');

    } catch (\Throwable $e) {
        return response('<pre>GAGAL: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>', 500)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }
});

// Route darurat: reset password semua agent ke bcrypt 'password'
// Akses: /fix-agents?secret=ISI_DEPLOY_SECRET
// HAPUS setelah berhasil!
Route::get('/fix-agents', function () {
    $secret = env('DEPLOY_SECRET', '');
    if (empty($secret) || request()->query('secret') !== $secret) {
        abort(403, 'Akses ditolak.');
    }

    $emails = [
        'agent@csirt.kalselprov.go.id',
        'agent2@csirt.kalselprov.go.id',
        'support@csirt.kalselprov.go.id',
        'admin@csirt.kalselprov.go.id',
        'admin1@csirt.kalselprov.go.id',
    ];

    $lines = [];
    foreach ($emails as $email) {
        try {
            $affected = \Illuminate\Support\Facades\DB::table('pengguna')
                ->where('email', $email)
                ->update([
                    'password'          => \Illuminate\Support\Facades\Hash::make('password'),
                    'access_revoked_at' => null,
                ]);

            $lines[] = ($affected ? '✓' : '–') . " {$email} → password=bcrypt('password'), revoked=NULL";
        } catch (\Throwable $e) {
            $lines[] = "✗ {$email} → GAGAL: " . $e->getMessage();
        }
    }

    // Tampilkan status semua user
    $users = \Illuminate\Support\Facades\DB::table('pengguna')
        ->select('id', 'name', 'email', 'access_revoked_at')
        ->get();

    $lines[] = '';
    $lines[] = '=== STATUS USER ===';
    foreach ($users as $u) {
        $lines[] = "ID:{$u->id} | {$u->email} | revoked: " . ($u->access_revoked_at ?? 'NULL (OK)');
    }

    return response('<pre style="font-size:14px;padding:20px">' . implode("\n", $lines) . '</pre>')
        ->header('Content-Type', 'text/html; charset=utf-8');
});
