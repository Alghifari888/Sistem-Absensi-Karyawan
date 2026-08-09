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
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        // LOGIKA PENENTUAN URL BERDASARKAN ROLE PENGGUNA
        $user = $request->user();
        $defaultUrl = route('dashboard');

        if ($user->isAdmin()) {
            $defaultUrl = route('admin.dashboard');
        } elseif ($user->isAtasan()) {
            $defaultUrl = route('atasan.dashboard');
        } elseif ($user->isKaryawan()) {
            $defaultUrl = route('karyawan.dashboard');
        }

        // Ambil intended URL (jika user sebelumnya mencoba akses halaman tertentu) atau gunakan default
        $targetUrl = redirect()->intended($defaultUrl)->getTargetUrl();

        // Render HTML langsung dengan Pop-up Animasi SweetAlert2
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Autentikasi Berhasil</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body { 
                    background-color: #f3f4f6; 
                    margin: 0; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    height: 100vh; 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                }
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
                    didOpen: () => {
                        Swal.showLoading();
                    }
                }).then(() => {
                    window.location.href = "{$targetUrl}";
                });
            </script>
        </body>
        </html>
        HTML;

        return response($html);
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