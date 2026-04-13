<?php
namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index()
    {
        return view('pages.guru.index');
    }

    public function data(Request $request)
    {
        $query = Guru::with('user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%")
                  ->orWhere('jabatan', 'like', "%{$request->search}%");
            });
        }

        $perPage = $request->per_page ?? 10;
        $result  = $query->orderBy('nama')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->map(fn($g) => [
                'id'             => $g->id,
                'nip'            => $g->nip,
                'nama'           => $g->nama,
                'jabatan'        => $g->jabatan,
                'jabatan_label'  => $g->jabatan_label,
                'mata_pelajaran' => $g->mata_pelajaran,
                'jenis_kelamin'  => $g->jenis_kelamin,
                'email'          => $g->user->email ?? '-',
                'foto'           => $g->foto_url,
            ]),
            'meta' => [
                'total'        => $result->total(),
                'from'         => $result->firstItem() ?? 0,
                'to'           => $result->lastItem() ?? 0,
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $g = Guru::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id'             => $g->id,
                'nip'            => $g->nip,
                'nama'           => $g->nama,
                'jabatan'        => $g->jabatan,
                'jabatan_label'  => $g->jabatan_label,
                'mata_pelajaran' => $g->mata_pelajaran ?? '',
                'jenis_kelamin'  => $g->jenis_kelamin,
                'tanggal_lahir'  => $g->tanggal_lahir
                    ? $g->tanggal_lahir->format('Y-m-d') : '',
                'tanggal_lahir_format' => $g->tanggal_lahir_format,
                'alamat'         => $g->alamat ?? '',
                'no_hp'          => $g->no_hp ?? '',
                'email'          => $g->user->email ?? '-',
                'foto'           => $g->foto_url,
                'created_at'     => $g->created_at->format('d M Y, H:i'),
                'updated_at'     => $g->updated_at->format('d M Y, H:i'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'            => 'required|string|max:20|unique:tbl_guru,nip',
            'nama'           => 'required|string|max:100',
            'jabatan'        => 'required|in:guru,kepala_sekolah,wakil_kepala_sekolah,wali_kelas,kepala_jurusan,bendahara,tata_usaha',
            'mata_pelajaran' => 'nullable|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'nullable|date',
            'alamat'         => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:15',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email'          => 'required|email|unique:tbl_users,email',
            'password'       => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'guru',
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto/guru', 'public');
            }

            Guru::create([
                'user_id'        => $user->id,
                'nip'            => $request->nip,
                'nama'           => $request->nama,
                'jabatan'        => $request->jabatan,
                'mata_pelajaran' => $request->mata_pelajaran,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'no_hp'          => $request->no_hp,
                'foto'           => $fotoPath,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data guru berhasil ditambahkan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nip'            => ['required', 'string', 'max:20', Rule::unique('tbl_guru', 'nip')->ignore($guru->id)],
            'nama'           => 'required|string|max:100',
            'jabatan'        => 'required|in:guru,kepala_sekolah,wakil_kepala_sekolah,wali_kelas,kepala_jurusan,bendahara,tata_usaha',
            'mata_pelajaran' => 'nullable|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'nullable|date',
            'alamat'         => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:15',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email'          => ['required', 'email', Rule::unique('tbl_users', 'email')->ignore($guru->user_id)],
            'password'       => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            User::where('id', $guru->user_id)->update($userData);

            $fotoPath = $guru->foto;
            if ($request->hasFile('foto')) {
                if ($guru->foto) Storage::disk('public')->delete($guru->foto);
                $fotoPath = $request->file('foto')->store('foto/guru', 'public');
            }

            $guru->update([
                'nip'            => $request->nip,
                'nama'           => $request->nama,
                'jabatan'        => $request->jabatan,
                'mata_pelajaran' => $request->mata_pelajaran,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'no_hp'          => $request->no_hp,
                'foto'           => $fotoPath,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data guru berhasil diupdate!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        DB::beginTransaction();
        try {
            if ($guru->foto) Storage::disk('public')->delete($guru->foto);
            User::where('id', $guru->user_id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data guru berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}