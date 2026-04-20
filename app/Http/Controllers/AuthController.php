<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru; // ⬅️ tambahin ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        $user = User::where('email', $request->identifier)->first();

        // Cek user & password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Email atau password salah'])
                ->withInput();
        }

        // Cek role valid
        $allowedRoles = ['admin', 'guru', 'siswa', 'petugas_sarana'];
        if (!in_array($user->role, $allowedRoles)) {
            return back()
                ->withErrors(['identifier' => 'Role tidak dikenali, hubungi administrator.'])
                ->withInput();
        }

        // Login
        Auth::login($user);

        session([
            'user_role'  => $user->role,
            'user_email' => $user->email,
        ]);

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => $this->redirectGuru(), 
            'siswa' => redirect()->route('siswa.dashboard'),
            'petugas_sarana' => redirect()->route('petugas.dashboard'),
        };
    }

    private function redirectGuru()
    {
        $guru = Guru::where('user_id', auth()->id())->first();

        if ($guru && $guru->jabatan === 'kepala_sekolah') {
            return redirect()->route('kepala_sekolah.dashboard');
        }

        return redirect()->route('guru.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('clear_storage', true);
    }
}