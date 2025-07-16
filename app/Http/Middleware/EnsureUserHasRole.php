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
     * @param  array<string>  $roles  Daftar role yang diizinkan.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek jika user sudah login DAN rolenya ada di dalam daftar role yang diizinkan.
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'ANDA TIDAK PUNYA AKSES.');
        }

        return $next($request);
    }
}