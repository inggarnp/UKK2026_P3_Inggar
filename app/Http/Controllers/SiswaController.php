<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────
    public function index()
    {
        return view('pages.siswa.index');
    }

    // ─── DATA (untuk DataTable AJAX) ──────────────────────
    public function data(Request $request)
    {
        $query = Siswa::with('user')
            ->select('tbl_siswa.*');

        // Search global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%$search%")
                  ->orWhere('nama', 'like', "%$search%")
                  ->orWhere('kelas', 'like', "%$search%")
                  ->orWhere('jurusan', 'like', "%$search%");
            });
        }

        // Sort
        $sortColumn = $request->get('sort_column', 'id');
        $sortDir    = $request->get('sort_dir', 'asc');
        $allowed    = ['id', 'nis', 'nama', 'kelas', 'jurusan', 'jenis_kelamin'];
        if (in_array($sortColumn, $allowed)) {
            $query->orderBy($sortColumn, $sortDir);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $data    = $query->paginate($perPage);

        return response()->json([
            'data'         => $data->items(),
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ]);
    }

    // ─── SHOW (detail 1 siswa) ────────────────────────────
    public function show($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return response()->json($siswa);
    }

    // ─── STORE (tambah siswa + akun) ─────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis'          => 'required|string|max:20|unique:tbl_siswa,nis',
            'nama'         => 'required|string|max:100',
            'kelas'        => 'required|string|max:10',
            'jurusan'      => 'required|string|max:50',
            'jenis_kelamin'=> 'required|in:L,P',
            'tanggal_lahir'=> 'nullable|date',
            'alamat'       => 'nullable|string|max:255',
            'no_hp'        => 'nullable|string|max:15',
            'email'        => 'required|email|unique:tbl_users,email',
            'password'     => 'required|string|min:6',
        ], [
            'nis.unique'   => 'NIS sudah terdaftar.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Buat akun user
            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'siswa',
            ]);

            // Buat data siswa
            Siswa::create([
                'user_id'       => $user->id,
                'nis'           => $request->nis,
                'nama'          => $request->nama,
                'kelas'         => $request->kelas,
                'jurusan'       => $request->jurusan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Siswa berhasil ditambahkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    // ─── UPDATE (edit siswa) ──────────────────────────────
    public function update(Request $request, $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nis'          => 'required|string|max:20|unique:tbl_siswa,nis,' . $siswa->id,
            'nama'         => 'required|string|max:100',
            'kelas'        => 'required|string|max:10',
            'jurusan'      => 'required|string|max:50',
            'jenis_kelamin'=> 'required|in:L,P',
            'tanggal_lahir'=> 'nullable|date',
            'alamat'       => 'nullable|string|max:255',
            'no_hp'        => 'nullable|string|max:15',
            'email'        => 'required|email|unique:tbl_users,email,' . $siswa->user_id,
            'password'     => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update user
            $userUpdate = ['email' => $request->email];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $siswa->user->update($userUpdate);

            // Update siswa
            $siswa->update([
                'nis'           => $request->nis,
                'nama'          => $request->nama,
                'kelas'         => $request->kelas,
                'jurusan'       => $request->jurusan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data siswa berhasil diupdate.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    // ─── DESTROY (hapus siswa) ────────────────────────────
    public function destroy($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            $userId = $siswa->user_id;
            $siswa->delete();
            User::destroy($userId);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    // ─── IMPORT EXCEL ─────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file        = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        // Skip header row (row 1)
        $success = 0;
        $errors  = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) continue; // skip header

            $nis           = trim($row['A'] ?? '');
            $nama          = trim($row['B'] ?? '');
            $kelas         = trim($row['C'] ?? '');
            $jurusan       = trim($row['D'] ?? '');
            $jenisKelamin  = strtoupper(trim($row['E'] ?? ''));
            $tanggalLahir  = trim($row['F'] ?? '');
            $alamat        = trim($row['G'] ?? '');
            $noHp          = trim($row['H'] ?? '');
            $email         = trim($row['I'] ?? '');
            $password      = trim($row['J'] ?? '');

            // Skip baris kosong
            if (empty($nis) && empty($nama)) continue;

            // Validasi per baris
            $rowErrors = [];
            if (empty($nis))   $rowErrors[] = 'NIS kosong';
            if (empty($nama))  $rowErrors[] = 'Nama kosong';
            if (empty($kelas)) $rowErrors[] = 'Kelas kosong';
            if (empty($email)) $rowErrors[] = 'Email kosong';
            if (empty($password)) $rowErrors[] = 'Password kosong';
            if (!in_array($jenisKelamin, ['L', 'P'])) $rowErrors[] = 'Jenis kelamin harus L/P';

            if (Siswa::where('nis', $nis)->exists()) $rowErrors[] = 'NIS sudah terdaftar';
            if (User::where('email', $email)->exists()) $rowErrors[] = 'Email sudah digunakan';

            if (!empty($rowErrors)) {
                $errors[] = [
                    'row'   => $rowIndex,
                    'nis'   => $nis,
                    'nama'  => $nama,
                    'error' => implode(', ', $rowErrors),
                ];
                continue;
            }

            DB::beginTransaction();
            try {
                $user = User::create([
                    'email'    => $email,
                    'password' => Hash::make($password),
                    'role'     => 'siswa',
                ]);

                Siswa::create([
                    'user_id'       => $user->id,
                    'nis'           => $nis,
                    'nama'          => $nama,
                    'kelas'         => $kelas,
                    'jurusan'       => $jurusan,
                    'jenis_kelamin' => $jenisKelamin,
                    'tanggal_lahir' => $tanggalLahir ?: null,
                    'alamat'        => $alamat,
                    'no_hp'         => $noHp,
                ]);

                DB::commit();
                $success++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = [
                    'row'   => $rowIndex,
                    'nis'   => $nis,
                    'nama'  => $nama,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success'       => true,
            'success_count' => $success,
            'error_count'   => count($errors),
            'errors'        => $errors,
        ]);
    }

    // ─── DOWNLOAD TEMPLATE EXCEL ──────────────────────────
    public function importTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['NIS', 'Nama', 'Kelas', 'Jurusan', 'Jenis Kelamin (L/P)',
                    'Tanggal Lahir (YYYY-MM-DD)', 'Alamat', 'No HP', 'Email', 'Password'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i); // A, B, C, ...
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }

        // Contoh baris
        $sheet->fromArray([
            '12345', 'Budi Santoso', 'XII RPL 1', 'Rekayasa Perangkat Lunak',
            'L', '2006-05-15', 'Jl. Merdeka No. 1', '081234567890',
            'budi@sekolah.sch.id', 'password123',
        ], null, 'A2');

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_siswa.xlsx';
        $path     = storage_path('app/public/' . $filename);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}