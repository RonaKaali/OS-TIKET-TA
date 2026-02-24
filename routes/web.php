<?php

use App\Http\Controllers\Portal\TicketController as PortalTicket;
use App\Http\Controllers\Agent\{
    DashboardController as AgentDashboard,
    TicketController as AgentTicket,
    AssignmentController as AgentAssignment,
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
    ChatbotResponseController
};
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AttachmentController;

Route::get('/', fn() => view('welcome'))->name('welcome');

# Chatbot API (public access)
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');

# Portal (Harus login untuk melaporkan)
Route::prefix('portal')->group(function () {
    // Route untuk melaporkan - memerlukan login
    Route::middleware('auth')->group(function () {
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
    Route::get('/tickets', [AgentTicket::class, 'index'])->name('agent.tickets.index');
    Route::get('/tickets/{ticket}', [AgentTicket::class, 'show'])->name('agent.tickets.show');
    Route::post('/tickets/{ticket}/reply', [AgentTicket::class, 'reply'])->name('agent.tickets.reply');
    Route::post('/tickets/{ticket}/status', [AgentTicket::class, 'setStatus'])->name('agent.tickets.status');
    Route::post('/tickets/{ticket}/assign', AgentAssignment::class)
        ->name('agent.tickets.assign')
        ->middleware('permission:tickets.assign');
    Route::post('/tickets/{ticket}/note', AgentNote::class)->name('agent.tickets.note');
});

# Panel Admin (Hanya Super Admin)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.users.index');
    })->name('index');

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
});

# Profile (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Session check untuk auto logout
    Route::get('/session/check', [\App\Http\Controllers\SessionController::class, 'check'])->name('session.check');
    
    // Download attachment (dengan dekripsi otomatis)
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->name('attachments.download');
});

# Telegram Webhook (untuk menerima update dari bot Telegram)
Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');
