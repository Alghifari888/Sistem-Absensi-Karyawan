<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): Response|RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        // Blokir login jika akun sudah dinonaktifkan admin
        if (! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin untuk informasi lebih lanjut.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $defaultUrl = route('dashboard');

        if ($user->isAdmin()) {
            $defaultUrl = route('admin.dashboard');
        } elseif ($user->isAtasan()) {
            $defaultUrl = route('atasan.dashboard');
        } elseif ($user->isKaryawan()) {
            $defaultUrl = route('karyawan.dashboard');
        }

        $targetUrl = \Illuminate\Support\Js::from(redirect()->intended($defaultUrl)->getTargetUrl());

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Autentikasi Berhasil</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body { background-color: #f3f4f6; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: 'Mengarahkan ke dashboard...',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => { Swal.showLoading(); }
                }).then(() => { window.location.href = {$targetUrl}; });
            </script>
        </body>
        </html>
        HTML;

        return response($html);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}