<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telegram_username' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/', // Hanya alphanumeric dan underscore
            ],
        ]);

        // Bersihkan @ dari awal telegram_username jika ada
        $telegramUsername = $request->telegram_username;
        if (!empty($telegramUsername)) {
            $telegramUsername = ltrim($telegramUsername, '@');
        }

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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

        event(new Registered($user));

        // Notifikasi email selamat datang
        try {
            $user->notify(new \App\Notifications\UserRegistered($user));
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email selamat datang: ' . $e->getMessage());
        }

        Auth::login($user);

        // Redirect ke welcome page untuk pelaporan insiden
        return redirect()->route('welcome')->with('status', 'Registrasi berhasil! Silakan gunakan fitur di bawah untuk melaporkan insiden siber.');
    }
}
