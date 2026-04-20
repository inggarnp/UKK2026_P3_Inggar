<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\PetugasSarana;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // ─── AMBIL PROFIL — selalu fresh dari DB ──────────────────
    private function getProfilFresh()
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

        return match ($role) {
            'siswa' => DB::table('tbl_siswa')
                ->leftJoin('tbl_kelas',   'tbl_siswa.kelas_id',   '=', 'tbl_kelas.id')
                ->leftJoin('tbl_jurusan', 'tbl_siswa.jurusan_id', '=', 'tbl_jurusan.id')
                ->where('tbl_siswa.user_id', $userId)
                ->select(
                    'tbl_siswa.*',
                    'tbl_kelas.nama_kelas',
                    'tbl_jurusan.nama_jurusan',
                )
                ->first(),

            'guru' => DB::table('tbl_guru')
                ->where('user_id', $userId)
                ->first(),

            'petugas_sarana' => DB::table('tbl_petugas_sarana')
                ->where('user_id', $userId)
                ->first(),

            default => null,
        };
    }

    // ─── Cek sudah set kode verif — LANGSUNG dari DB ──────────
    private function cekSudahSetKodeFresh(): bool
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

        if (!in_array($role, ['siswa', 'guru'])) return true; // petugas tidak perlu

        $tabel = $role === 'siswa' ? 'tbl_siswa' : 'tbl_guru';

        $row = DB::table($tabel)
            ->where('user_id', $userId)
            ->select('kode_verifikasi')
            ->first();

        return !is_null($row?->kode_verifikasi);
    }

    private function getFotoUrl($profil, string $role): string
    {
        $map = ['siswa' => 'siswa', 'guru' => 'guru', 'petugas_sarana' => 'petugas'];
        $sub = $map[$role] ?? 'users';

        $foto = is_object($profil) ? $profil->foto : ($profil->foto ?? null);
        return $foto
            ? asset("assets/images/users/{$sub}/{$foto}")
            : asset('assets/images/users/avatar-1.jpg');
    }

    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $user            = auth()->user();
        $role            = $user->role;
        $profil          = $this->getProfilFresh();
        $sudahSetKode    = $this->cekSudahSetKodeFresh(); // FRESH dari DB, tidak ada cache
        $fotoUrl         = $this->getFotoUrl($profil, $role);
        $firstLoginNotif = session()->pull('notif_set_kode', false);

        // Ambil Eloquent model untuk relasi (hanya untuk tampilan)
        $eloquentProfil = match ($role) {
            'siswa' => Siswa::with(['kelas', 'jurusan'])->where('user_id', $user->id)->first(),
            'guru'  => Guru::with('kelasWali')->where('user_id', $user->id)->first(),
            'petugas_sarana' => PetugasSarana::where('user_id', $user->id)->first(),
            default => null,
        };

        return view('pages.profile.index', compact(
            'user', 'eloquentProfil', 'profil', 'role',
            'sudahSetKode', 'fotoUrl', 'firstLoginNotif'
        ))->with('profil', $eloquentProfil); // pastikan view dapat profil Eloquent
    }

    // ─── UPDATE PROFIL ────────────────────────────────────────
    public function update(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $request->validate([
            'nama'     => 'required|string|max:100',
            'no_hp'    => 'nullable|string|max:15',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
            'email'    => ['required', 'email', Rule::unique('tbl_users', 'email')->ignore($user->id)],
        ]);

        // Ambil profil Eloquent untuk update
        $profil = match ($role) {
            'siswa'          => Siswa::where('user_id', $user->id)->first(),
            'guru'           => Guru::where('user_id', $user->id)->first(),
            'petugas_sarana' => PetugasSarana::where('user_id', $user->id)->first(),
            default          => null,
        };

        $map = ['siswa' => 'siswa', 'guru' => 'guru', 'petugas_sarana' => 'petugas'];
        $sub = $map[$role] ?? 'users';

        $fotoFilename = $profil?->foto;
        if ($request->hasFile('foto')) {
            $dir = public_path("assets/images/users/{$sub}");
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            if ($fotoFilename && file_exists($dir . '/' . $fotoFilename)) {
                unlink($dir . '/' . $fotoFilename);
            }
            $file         = $request->file('foto');
            $fotoFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fotoFilename);
        }

        if ($profil) {
            $profil->update([
                'nama'  => $request->nama,
                'no_hp' => $request->no_hp,
                'foto'  => $fotoFilename,
            ]);
        }

        $userData = ['email' => $request->email];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui!']);
    }

    // ─── SET KODE VERIFIKASI ──────────────────────────────────
    public function setKodeVerifikasi(Request $request)
    {
        $request->validate([
            'kode_verifikasi' => 'required|string|min:4|max:20|confirmed',
        ], [
            'kode_verifikasi.confirmed' => 'Konfirmasi kode tidak cocok.',
            'kode_verifikasi.min'       => 'Kode minimal 4 karakter.',
        ]);

        $user = auth()->user();

        if (!in_array($user->role, ['siswa', 'guru'])) {
            return response()->json(['success' => false, 'message' => 'Role ini tidak memerlukan kode verifikasi.'], 422);
        }

        $tabel = $user->role === 'siswa' ? 'tbl_siswa' : 'tbl_guru';

        // Cek langsung dari DB — tidak pakai Eloquent sama sekali
        $row = DB::table($tabel)->where('user_id', $user->id)->select('id', 'kode_verifikasi')->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data profil tidak ditemukan.'], 404);
        }

        if (!is_null($row->kode_verifikasi)) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi sudah pernah diset.'], 422);
        }

        // Update langsung via DB::table agar tidak ada cache Eloquent
        DB::table($tabel)
            ->where('user_id', $user->id)
            ->update(['kode_verifikasi' => Hash::make($request->kode_verifikasi)]);

        Notifikasi::kirim(
            $user->id,
            'Kode Verifikasi Aktif 🔐',
            'Kode verifikasi kamu sudah berhasil diset. Akun kamu sekarang lebih aman!',
            'success',
            null,
            'solar:shield-check-bold-duotone'
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi berhasil disimpan!',
        ]);
    }

    // ─── CEK KODE VERIFIKASI ──────────────────────────────────
    public function cekKodeVerifikasi(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $user  = auth()->user();
        $tabel = match ($user->role) {
            'siswa' => 'tbl_siswa',
            'guru'  => 'tbl_guru',
            default => null,
        };

        if (!$tabel) {
            return response()->json(['valid' => false, 'message' => 'Role tidak mendukung kode verifikasi.']);
        }

        $row = DB::table($tabel)
            ->where('user_id', $user->id)
            ->select('kode_verifikasi')
            ->first();

        if (!$row) {
            return response()->json(['valid' => false, 'message' => 'Data profil tidak ditemukan.']);
        }

        if (is_null($row->kode_verifikasi)) {
            return response()->json(['valid' => false, 'message' => 'Belum set kode verifikasi. Silakan ke halaman Profil.']);
        }

        if (!Hash::check($request->kode, $row->kode_verifikasi)) {
            return response()->json(['valid' => false, 'message' => 'Kode verifikasi salah. Coba lagi.']);
        }

        return response()->json(['valid' => true]);
    }
}