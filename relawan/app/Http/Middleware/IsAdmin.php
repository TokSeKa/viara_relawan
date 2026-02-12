<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  <-- PENTING: Menangkap parameter role dari web.php
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek Login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. BYPASS: Kalau dia 'admin_super', boleh masuk ke mana saja.
        if ($user->jabatan === 'admin_super') {
            return $next($request);
        }

        // 3. CEK SPESIFIK: Apakah jabatannya ada di daftar yang diizinkan route ini?
        // Contoh route: role:admin_dana -> $roles isinya ['admin_dana']
        if (!empty($roles) && in_array($user->jabatan, $roles)) {
            return $next($request);
        }

        // 4. FALLBACK: Jika route tidak minta role spesifik (cuma 'admin'),
        // cek apakah jabatannya mengandung kata 'admin'
        if (empty($roles) && str_contains($user->jabatan, 'admin')) {
            return $next($request);
        }

        // Kalau gagal semua pengecekan di atas:
        return redirect('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki wewenang.');
    }
}