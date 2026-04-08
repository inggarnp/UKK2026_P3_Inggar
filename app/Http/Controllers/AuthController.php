<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required',
        ]);

        $identifier = $request->identifier;
        $user = User::where('email', $identifier)->first();

        // Cek user & password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Email atau password salah'])
                ->withInput();
        }

        // Cek role SEBELUM login (fix bug lama)
        if ($user->role !== 'admin') {
            return back()
                ->withErrors(['identifier' => 'Hanya admin yang bisa login'])
                ->withInput();
        }

        // Generate JWT token via api guard
        $token = auth('api')->login($user);

        // Login session seperti biasa (untuk middleware web)
        Auth::login($user);

        // Simpan JWT token ke session agar bisa dipakai di blade
        session([
            'jwt_token' => $token,
            'user_role' => $user->role,
            'user_email' => $user->email,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
        } catch (\Exception $e) {
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('clear_storage', true);
    }
}
