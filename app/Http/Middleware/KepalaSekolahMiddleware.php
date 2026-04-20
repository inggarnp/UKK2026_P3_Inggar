<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KepalaSekolahMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Harus role guru
        if ($user->role !== 'guru') {
            abort(403, 'Akses ditolak.');
        }

        // Harus jabatan kepala_sekolah
        $guru = \App\Models\Guru::where('user_id', $user->id)->first();

        if (!$guru || $guru->jabatan !== 'kepala_sekolah') {
            abort(403, 'Akses ditolak. Hanya Kepala Sekolah yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}