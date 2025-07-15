<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Role yang diizinkan untuk mengakses route.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login DAN rolenya sesuai dengan yang diizinkan.
        // Fungsi helper (isAdmin, isAtasan, isKaryawan) yang kita buat tadi tidak bisa dipakai di sini
        // karena middleware tidak tahu instance user mana yang harus dicek.
        // Jadi kita menggunakan $request->user()->role secara langsung.
        if (! $request->user() || $request->user()->role !== $role) {
            // Jika tidak sesuai, lempar ke halaman 403 (Forbidden/Akses Ditolak)
            abort(403, 'ANDA TIDAK PUNYA AKSES.');
        }

        return $next($request);
    }
}