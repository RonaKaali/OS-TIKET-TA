<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information, email address, and notification settings.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Nomor Telepon')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="telegram_username" :value="__('Username Telegram')" />
            <div class="mt-1 flex rounded-md shadow-sm">
                <span
                    class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    @
                </span>
                <x-text-input id="telegram_username" name="telegram_username" type="text"
                    class="block w-full rounded-none rounded-r-md" :value="old('telegram_username', $user->telegram_username)" placeholder="username_telegram" autocomplete="off" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('telegram_username')" />
            <p class="mt-1 text-sm text-gray-500">
                Masukkan username Telegram Anda (tanpa @). Sistem akan otomatis mencari chat ID Anda.
            </p>
            <ul class="mt-1.5 text-xs text-gray-500 list-disc list-inside space-y-1">
                <li>Jika chat ID belum ditemukan, kirim pesan <code class="bg-gray-100 px-1 rounded">/start</code> ke
                    bot CSIRT di Telegram @kominfokalsel_bot</li>
                <li>Chat ID akan otomatis tersimpan dan notifikasi akan terkirim</li>
            </ul>
            @if(isset($user->telegram_chat_id) && $user->telegram_chat_id)
                <p class="mt-2 text-xs text-green-600 font-medium">
                    ✅ Chat ID sudah terdaftar. Notifikasi Telegram aktif!
                </p>
            @elseif(isset($user->telegram_username) && $user->telegram_username)
                <p class="mt-2 text-xs text-amber-600 font-medium">
                    ⚠️ Chat ID belum terdaftar. Kirim <code class="bg-amber-50 px-1 rounded">/start</code> ke bot untuk
                    mengaktifkan notifikasi.
                </p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>