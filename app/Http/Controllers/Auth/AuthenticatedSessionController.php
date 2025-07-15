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

        // LOGIKA PENGALIHAN BERDASARKAN ROLE PENGGUNA
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isAtasan()) {
            return redirect()->intended(route('atasan.dashboard'));
        } elseif ($user->isKaryawan()) {
            return redirect()->intended(route('karyawan.dashboard'));
        }

        // Pengalihan default jika tidak ada role yang cocok
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}