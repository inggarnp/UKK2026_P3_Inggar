<?php
namespace App\Http\Controllers;

use App\Models\PetugasSarana;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PetugasSaranaController extends Controller
{
    private function fotoDir(): string
    {
        return public_path('assets/images/users/petugas');
    }

    private function fotoUrl(?string $filename): string
    {
        return $filename
            ? asset('assets/images/users/petugas/' . $filename)
            : asset('assets/images/users/avatar-1.jpg');
    }

    private function simpanFoto($file): string
    {
        $dir = $this->fotoDir();
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return $filename;
    }

    private function hapusFoto(?string $filename): void
    {
        if ($filename) {
            $path = $this->fotoDir() . '/' . $filename;
            if (file_exists($path)) unlink($path);
        }
    }

    public function index()
    {
        return view('pages.petugas.index');
    }

    public function data(Request $request)
    {
        $query = PetugasSarana::with('user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama',  'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $result  = $query->orderBy('nama')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->map(fn($p) => [
                'id'            => $p->id,
                'nip'           => $p->nip,
                'nama'          => $p->nama,
                'jenis_kelamin' => $p->jenis_kelamin,
                'no_hp'         => $p->no_hp,
                'status'        => $p->status,
                'status_label'  => $p->status_label,
                'email'         => $p->user?->email ?? '-',
                'foto'          => $this->fotoUrl($p->foto),
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
        $p = PetugasSarana::with('user')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id'                   => $p->id,
                'nip'                  => $p->nip,
                'nama'                 => $p->nama,
                'jenis_kelamin'        => $p->jenis_kelamin,
                'tanggal_lahir'        => $p->tanggal_lahir?->format('Y-m-d'),
                'tanggal_lahir_format' => $p->tanggal_lahir_format,
                'alamat'               => $p->alamat,
                'no_hp'                => $p->no_hp,
                'status'               => $p->status,
                'status_label'         => $p->status_label,
                'foto'                 => $this->fotoUrl($p->foto),
                'email'                => $p->user?->email,
                'created_at'           => $p->created_at->format('d M Y, H:i'),
                'updated_at'           => $p->updated_at->format('d M Y, H:i'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'           => 'nullable|string|max:20|unique:tbl_petugas_sarana,nip',
            'nama'          => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:15',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:aktif,nonaktif',
            'email'         => 'required|email|unique:tbl_users,email',
            'password'      => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $fotoFilename = null;
            if ($request->hasFile('foto')) {
                $fotoFilename = $this->simpanFoto($request->file('foto'));
            }

            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'petugas_sarana',
            ]);

            PetugasSarana::create([
                'user_id'       => $user->id,
                'nip'           => $request->nip,
                'nama'          => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
                'foto'          => $fotoFilename,
                'status'        => $request->status,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Petugas sarana berhasil ditambahkan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($fotoFilename)) $this->hapusFoto($fotoFilename);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $petugas = PetugasSarana::findOrFail($id);

        $request->validate([
            'nip'           => ['nullable', 'string', 'max:20', Rule::unique('tbl_petugas_sarana', 'nip')->ignore($petugas->id)],
            'nama'          => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:15',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:aktif,nonaktif',
            'email'         => ['required', 'email', Rule::unique('tbl_users', 'email')->ignore($petugas->user_id)],
            'password'      => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $fotoFilename = $petugas->foto;
            if ($request->hasFile('foto')) {
                $this->hapusFoto($petugas->foto);
                $fotoFilename = $this->simpanFoto($request->file('foto'));
            }

            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            User::where('id', $petugas->user_id)->update($userData);

            $petugas->update([
                'nip'           => $request->nip,
                'nama'          => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
                'foto'          => $fotoFilename,
                'status'        => $request->status,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data petugas berhasil diupdate!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $petugas = PetugasSarana::with('user')->findOrFail($id);
        DB::beginTransaction();
        try {
            $this->hapusFoto($petugas->foto);
            $userId = $petugas->user_id;
            $petugas->delete();
            User::destroy($userId);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Petugas berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}