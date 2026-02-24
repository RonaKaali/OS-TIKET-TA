<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected MfaService $mfaService
    ) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Cek status MFA
        $mfaEnabled = $user->mfa_enabled ?? false;
        if (!$mfaEnabled) {
            $mfaEnabled = $this->mfaService->isMfaEnabled($user);
        }

        return view('profile.edit', [
            'user' => $user,
            'mfaEnabled' => $mfaEnabled,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update name dan email
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update phone
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        // Bersihkan @ dari awal telegram_username jika ada
        $telegramUsernameChanged = false;
        if (isset($validated['telegram_username']) && !empty($validated['telegram_username'])) {
            $newTelegramUsername = ltrim($validated['telegram_username'], '@');
            $telegramUsernameChanged = ($user->telegram_username !== $newTelegramUsername);
            $user->telegram_username = $newTelegramUsername;
        } elseif (isset($validated['telegram_username']) && empty($validated['telegram_username'])) {
            // Jika dikosongkan, set ke null
            $user->telegram_username = null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Coba dapatkan chat_id otomatis jika telegram_username baru, berubah, atau belum ada chat_id
        if (!empty($user->telegram_username) && ($telegramUsernameChanged || empty($user->telegram_chat_id))) {
            try {
                $telegramService = app(\App\Services\TelegramService::class);
                $chatId = $telegramService->tryGetChatId($user->telegram_username);

                if ($chatId) {
                    $user->telegram_chat_id = $chatId;
                    $user->save();
                    \Log::info("Chat ID {$chatId} otomatis didapatkan saat update profil untuk @{$user->telegram_username}");
                } else {
                    \Log::info("Chat ID belum ditemukan untuk @{$user->telegram_username}. User perlu mengirim /start ke bot.");
                }
            } catch (\Throwable $e) {
                \Log::warning('Gagal mendapatkan chat_id saat update profil: ' . $e->getMessage());
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
