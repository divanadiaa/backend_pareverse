<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Izinkan akses ke halaman registrasi untuk pengguna yang belum login
        if ($request->is('admin/register') || $request->is('admin/register/*')) {
            return $next($request);
        }

        // Cek role hanya untuk pengguna yang sudah login
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return $next($request);
            }
            return response()->json(['error' => 'Akses ditolak. Hanya admin yang dapat mengakses panel ini.'], 403);
        }

        // Jika belum login, arahkan ke halaman login
        return redirect('/admin/login');
    }
}