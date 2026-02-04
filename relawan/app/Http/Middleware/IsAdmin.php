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
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user LOGIN dan jabatannya ADMIN
        if (Auth::check() && Auth::user()->jabatan == 'admin') {
            return $next($request); // Lanjut boleh masuk
        }

        // Kalau bukan admin, tendang balik atau kasih error 403
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses admin.');
        // atau: abort(403);
    }
}
