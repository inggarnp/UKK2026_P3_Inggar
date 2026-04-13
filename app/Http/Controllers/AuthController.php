<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $allowedRoles = ['admin', 'guru', 'siswa'];
        if (!in_array($user->role, $allowedRoles)) {
            return back()
                ->withErrors(['identifier' => 'Role tidak dikenali, hubungi administrator.'])
                ->withInput();
        }

        // Login session
        Auth::login($user);

        // Simpan ke session
        session([
            'user_role'  => $user->role,
            'user_email' => $user->email,
        ]);

        // Redirect sesuai role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => redirect()->route('guru.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('clear_storage', true);
    }
}