<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Bersihkan @ dari awal telegram_username jika ada
        $telegramUsernameChanged = false;
        if (isset($validated['telegram_username']) && !empty($validated['telegram_username'])) {
            $newTelegramUsername = ltrim($validated['telegram_username'], '@');
            $telegramUsernameChanged = ($user->telegram_username !== $newTelegramUsername);
            $validated['telegram_username'] = $newTelegramUsername;
        }

        $user->fill($validated);

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
