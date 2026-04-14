<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaController extends Controller
{
    // ─── Path helper ──────────────────────────────────────
    private function fotoDir(): string
    {
        return public_path('assets/images/users/siswa');
    }

    private function fotoUrl(?string $filename): ?string
    {
        return $filename ? asset('assets/images/users/siswa/' . $filename) : null;
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

    // ─── INDEX ────────────────────────────────────────────
    public function index()
    {
        $kelasList   = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        return view('pages.siswa.index', compact('kelasList', 'jurusanList'));
    }

    // ─── DATA (AJAX) ──────────────────────────────────────
    public function data(Request $request)
    {
        $query = Siswa::with(['user', 'kelas', 'jurusan'])->select('tbl_siswa.*');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nis',  'like', "%$s%")
                  ->orWhere('nama', 'like', "%$s%")
                  ->orWhereHas('kelas',   fn($q2) => $q2->where('nama_kelas',   'like', "%$s%"))
                  ->orWhereHas('jurusan', fn($q2) => $q2->where('nama_jurusan', 'like', "%$s%"));
            });
        }

        $sortCol = $request->get('sort_column', 'id');
        $sortDir = $request->get('sort_dir', 'asc');
        if (in_array($sortCol, ['id', 'nis', 'nama', 'jenis_kelamin'])) {
            $query->orderBy($sortCol, $sortDir);
        }

        $data = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => $data->map(fn($s) => [
                'id'            => $s->id,
                'nis'           => $s->nis,
                'nama'          => $s->nama,
                'kelas'         => $s->kelas?->nama_kelas ?? $s->kelas ?? '-',
                'jurusan'       => $s->jurusan?->nama_jurusan ?? $s->jurusan ?? '-',
                'jenis_kelamin' => $s->jenis_kelamin,
                'email'         => $s->user?->email ?? '-',
                'foto'          => $this->fotoUrl($s->foto),
            ]),
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ]);
    }

    // ─── SHOW ─────────────────────────────────────────────
    public function show($id)
    {
        $siswa = Siswa::with(['user', 'kelas', 'jurusan'])->findOrFail($id);
        return response()->json([
            'id'            => $siswa->id,
            'nis'           => $siswa->nis,
            'nama'          => $siswa->nama,
            'kelas_id'      => $siswa->kelas_id,
            'kelas'         => $siswa->kelas?->nama_kelas ?? $siswa->kelas,
            'jurusan_id'    => $siswa->jurusan_id,
            'jurusan'       => $siswa->jurusan?->nama_jurusan ?? $siswa->jurusan,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'tanggal_lahir' => $siswa->tanggal_lahir,
            'alamat'        => $siswa->alamat,
            'no_hp'         => $siswa->no_hp,
            'foto'          => $this->fotoUrl($siswa->foto),
            'email'         => $siswa->user?->email,
            'created_at'    => $siswa->created_at,
            'updated_at'    => $siswa->updated_at,
        ]);
    }

    // ─── STORE ────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis'           => 'required|string|max:20|unique:tbl_siswa,nis',
            'nama'          => 'required|string|max:100',
            'kelas_id'      => 'required|exists:tbl_kelas,id',
            'jurusan_id'    => 'required|exists:tbl_jurusan,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:15',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email'         => 'required|email|unique:tbl_users,email',
            'password'      => 'required|string|min:6',
        ], [
            'nis.unique'          => 'NIS sudah terdaftar.',
            'email.unique'        => 'Email sudah digunakan.',
            'kelas_id.required'   => 'Kelas wajib dipilih.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $fotoFilename = null;
            if ($request->hasFile('foto')) {
                $fotoFilename = $this->simpanFoto($request->file('foto'));
            }

            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'siswa',
            ]);

            Siswa::create([
                'user_id'       => $user->id,
                'nis'           => $request->nis,
                'nama'          => $request->nama,
                'kelas_id'      => $request->kelas_id,
                'jurusan_id'    => $request->jurusan_id,
                'kelas'         => Kelas::find($request->kelas_id)?->nama_kelas,
                'jurusan'       => Jurusan::find($request->jurusan_id)?->nama_jurusan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
                'foto'          => $fotoFilename,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Siswa berhasil ditambahkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($fotoFilename)) $this->hapusFoto($fotoFilename);
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    // ─── UPDATE ───────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nis'           => 'required|string|max:20|unique:tbl_siswa,nis,' . $siswa->id,
            'nama'          => 'required|string|max:100',
            'kelas_id'      => 'required|exists:tbl_kelas,id',
            'jurusan_id'    => 'required|exists:tbl_jurusan,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:15',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email'         => 'required|email|unique:tbl_users,email,' . $siswa->user_id,
            'password'      => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $fotoFilename = $siswa->foto;
            if ($request->hasFile('foto')) {
                $this->hapusFoto($siswa->foto);
                $fotoFilename = $this->simpanFoto($request->file('foto'));
            }

            $userUpdate = ['email' => $request->email];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $siswa->user->update($userUpdate);

            $siswa->update([
                'nis'           => $request->nis,
                'nama'          => $request->nama,
                'kelas_id'      => $request->kelas_id,
                'jurusan_id'    => $request->jurusan_id,
                'kelas'         => Kelas::find($request->kelas_id)?->nama_kelas,
                'jurusan'       => Jurusan::find($request->jurusan_id)?->nama_jurusan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
                'foto'          => $fotoFilename,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data siswa berhasil diupdate.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    // ─── DESTROY ──────────────────────────────────────────
    public function destroy($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        DB::beginTransaction();
        try {
            $this->hapusFoto($siswa->foto);
            $userId = $siswa->user_id;
            $siswa->delete();
            User::destroy($userId);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    // ─── IMPORT ───────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        $rows    = IOFactory::load($request->file('file')->getPathname())->getActiveSheet()->toArray(null, true, true, true);
        $success = 0;
        $errors  = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue;
            $nis  = trim($row['A'] ?? '');
            $nama = trim($row['B'] ?? '');
            if (empty($nis) && empty($nama)) continue;

            $kelas    = trim($row['C'] ?? '');
            $jurusan  = trim($row['D'] ?? '');
            $jk       = strtoupper(trim($row['E'] ?? ''));
            $tglLahir = trim($row['F'] ?? '');
            $alamat   = trim($row['G'] ?? '');
            $noHp     = trim($row['H'] ?? '');
            $email    = trim($row['I'] ?? '');
            $password = trim($row['J'] ?? '');

            $rowErrors = [];
            if (empty($nis))      $rowErrors[] = 'NIS kosong';
            if (empty($nama))     $rowErrors[] = 'Nama kosong';
            if (empty($email))    $rowErrors[] = 'Email kosong';
            if (empty($password)) $rowErrors[] = 'Password kosong';
            if (!in_array($jk, ['L','P'])) $rowErrors[] = 'JK harus L/P';
            if (Siswa::where('nis', $nis)->exists())    $rowErrors[] = 'NIS sudah ada';
            if (User::where('email', $email)->exists()) $rowErrors[] = 'Email sudah ada';

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $i, 'nis' => $nis, 'nama' => $nama, 'error' => implode(', ', $rowErrors)];
                continue;
            }

            $kelasObj   = Kelas::where('nama_kelas', $kelas)->first();
            $jurusanObj = Jurusan::where('nama_jurusan', $jurusan)->first();

            DB::beginTransaction();
            try {
                $user = User::create(['email' => $email, 'password' => Hash::make($password), 'role' => 'siswa']);
                Siswa::create([
                    'user_id'       => $user->id,
                    'nis'           => $nis,
                    'nama'          => $nama,
                    'kelas_id'      => $kelasObj?->id,
                    'jurusan_id'    => $jurusanObj?->id,
                    'kelas'         => $kelas,
                    'jurusan'       => $jurusan,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $tglLahir ?: null,
                    'alamat'        => $alamat,
                    'no_hp'         => $noHp,
                ]);
                DB::commit();
                $success++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = ['row' => $i, 'nis' => $nis, 'nama' => $nama, 'error' => $e->getMessage()];
            }
        }

        return response()->json(['success' => true, 'success_count' => $success, 'error_count' => count($errors), 'errors' => $errors]);
    }

    public function importTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['NIS','Nama','Kelas','Jurusan','JK (L/P)','Tgl Lahir (YYYY-MM-DD)','Alamat','No HP','Email','Password'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
            $sheet->getStyle(chr(65+$i).'1')->getFont()->setBold(true);
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $path   = public_path('assets/template_import_siswa.xlsx');
        $writer->save($path);
        return response()->download($path, 'template_import_siswa.xlsx')->deleteFileAfterSend(true);
    }
}