<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Generate bearer token untuk keamanan tambahan (disimpan di session, tidak ditampilkan)
        // Token akan digunakan untuk validasi request internal
        $tokenResult = $user->createToken('web-session-token', ['*'], now()->addMinutes(5));
        
        // Simpan token ID di session untuk validasi (bukan plain text token)
        $request->session()->put('auth_token_id', $tokenResult->accessToken->id);
        
        // Set last activity time untuk auto logout
        $request->session()->put('last_activity', now()->toDateTimeString());

        // Redirect sesuai permission
        if ($user->can('admin.panel')) {
            return redirect()->intended(route('dashboard', absolute: false));
        } else {
            // User biasa di-redirect ke welcome page
            return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Revoke semua token user saat logout
        if ($user) {
            $user->tokens()->delete();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
