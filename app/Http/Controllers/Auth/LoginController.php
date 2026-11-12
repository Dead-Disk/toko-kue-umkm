<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan form login penjual/admin.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login memakai username dan password.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            ActivityLog::record('login', 'Masuk ke sistem (login)', $user, user: $user);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout dan akhiri sesi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            ActivityLog::record('logout', 'Keluar dari sistem (logout)', $user, user: $user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
