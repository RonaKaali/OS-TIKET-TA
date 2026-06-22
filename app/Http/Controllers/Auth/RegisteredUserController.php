<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Organization};
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $organizations = Organization::orderBy('name')->get();
        return view('auth.register', compact('organizations'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pengguna,email'],
                'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
                'organization_name' => ['nullable', 'string', 'max:255'],
                'telegram_username' => [
                    'nullable',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z0-9_]+$/', // Hanya alphanumeric dan underscore
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Validation/DB Error: ' . $e->getMessage());
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Kesalahan koneksi database/sistem: ' . $e->getMessage()]);
        }

        try {
            // Cari atau buat organisasi baru
            $orgId = null;
            if ($request->filled('organization_name')) {
                $org = Organization::firstOrCreate(['name' => $request->organization_name]);
                $orgId = $org->id;
            }

            // Bersihkan @ dari awal telegram_username jika ada
            $telegramUsername = $request->telegram_username;
            if (!empty($telegramUsername)) {
                $telegramUsername = ltrim($telegramUsername, '@');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Laravel akan auto hash karena ada cast 'hashed'
                'id_organisasi' => $orgId,
                'nama_pengguna_telegram' => $telegramUsername ?: null,
            ]);

            // Coba dapatkan chat_id otomatis jika user mengisi telegram_username
            if (!empty($telegramUsername)) {
                try {
                    $telegramService = app(\App\Services\TelegramService::class);
                    $chatId = $telegramService->tryGetChatId($telegramUsername);

                    if ($chatId) {
                        $user->id_chat_telegram = $chatId;
                        $user->save();
                        \Log::info("Chat ID {$chatId} otomatis didapatkan saat registrasi untuk @{$telegramUsername}");
                    } else {
                        \Log::info("Chat ID belum ditemukan untuk @{$telegramUsername}. User perlu mengirim /start ke bot.");
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Gagal mendapatkan chat_id saat registrasi: ' . $e->getMessage());
                }
            }

            // Assign role default "User" untuk user biasa
            try {
                $user->assignRole('User');
            } catch (\Throwable $e) {
                \Log::warning('Gagal assign role User: ' . $e->getMessage());
            }

            // Trigger Registered event (may trigger email verification notification)
            // Wrapped in try-catch because SendEmailVerificationNotification can fail
            // in serverless environments (Vercel) due to cache/mail issues
            try {
                event(new Registered($user));
            } catch (\Throwable $e) {
                \Log::warning('Registered event listener error (non-fatal): ' . $e->getMessage());
            }

            // Notifikasi email selamat datang
            try {
                $user->notify(new \App\Notifications\UserRegistered($user));
            } catch (\Throwable $e) {
                \Log::warning('Gagal mengirim email selamat datang: ' . $e->getMessage());
            }

            Auth::login($user);

            // Setelah registrasi, arahkan user untuk mengaktifkan 2FA terlebih dahulu
            return redirect()
                ->route('mfa.setup')
                ->with('status', 'Registrasi berhasil! Demi keamanan akun, silakan aktifkan Two-Factor Authentication (2FA) terlebih dahulu.');

        } catch (\Throwable $e) {
            \Log::error('Registration Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Terjadi kesalahan sistem saat registrasi: ' . $e->getMessage()]);
        }
    }
}
