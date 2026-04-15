<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    public function index()
    {
        return view('pages.kelas.index');
    }

    // AJAX DataTable
    public function data(Request $request)
    {
        $query = Kelas::with(['jurusan', 'ruangan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_kelas',   'like', "%$s%")
                    ->orWhere('tingkat',    'like', "%$s%")
                    ->orWhere('tahun_ajaran', 'like', "%$s%")
                    ->orWhereHas('jurusan', fn($q2) => $q2->where('nama_jurusan', 'like', "%$s%"));
            });
        }

        $sortCol = $request->get('sort_column', 'nama_kelas');
        $sortDir = $request->get('sort_dir', 'asc');
        if (in_array($sortCol, ['nama_kelas', 'tingkat', 'tahun_ajaran'])) {
            $query->orderBy($sortCol, $sortDir);
        }

        $perPage = $request->get('per_page', 10);
        $data    = $query->paginate($perPage);

        return response()->json([
            'data'         => $data->map(function ($k) {
                return [
                    'id'           => $k->id,
                    'nama_kelas'   => $k->nama_kelas,
                    'tingkat'      => $k->tingkat,
                    'tahun_ajaran' => $k->tahun_ajaran,
                    'jurusan'      => $k->jurusan?->nama_jurusan ?? '-',
                    'jurusan_id'   => $k->jurusan_id,
                    'ruangan'      => $k->ruangan?->nama_ruangan ?? '-',
                    'ruangan_id'   => $k->ruangan_id,
                    'kode_ruangan' => $k->ruangan?->kode_ruangan ?? '-',
                    'jumlah_siswa' => $k->siswa()->count(),
                ];
            }),
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ]);
    }

    public function show($id)
    {
        $kelas = Kelas::with(['jurusan', 'ruangan'])->findOrFail($id);
        return response()->json([
            'id'           => $kelas->id,
            'nama_kelas'   => $kelas->nama_kelas,
            'tingkat'      => $kelas->tingkat,
            'tahun_ajaran' => $kelas->tahun_ajaran,
            'jurusan_id'   => $kelas->jurusan_id,
            'jurusan'      => $kelas->jurusan?->nama_jurusan,
            'ruangan_id'   => $kelas->ruangan_id,
            'ruangan'      => $kelas->ruangan?->nama_ruangan,
            'kode_ruangan' => $kelas->ruangan?->kode_ruangan,
            'jumlah_siswa' => $kelas->siswa()->count(),
            'created_at'   => $kelas->created_at,
            'updated_at'   => $kelas->updated_at,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas'   => 'required|string|max:20',
            'tingkat'      => 'required|in:X,XI,XII',
            'jurusan_id'   => 'required|exists:tbl_jurusan,id',
            'ruangan_id'   => 'nullable|exists:tbl_ruangan,id',
            'tahun_ajaran' => 'required|string|max:10',
        ], [
            'nama_kelas.unique' => 'Nama kelas sudah ada di tahun ajaran ini.',
        ]);

        // Cek duplikat nama_kelas + tahun_ajaran
        $exists = Kelas::where('nama_kelas', $request->nama_kelas)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'errors'  => ['nama_kelas' => ['Nama kelas sudah ada di tahun ajaran ' . $request->tahun_ajaran]],
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Kelas::create($request->only(['nama_kelas', 'tingkat', 'jurusan_id', 'ruangan_id', 'tahun_ajaran']));

        return response()->json(['success' => true, 'message' => 'Kelas berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_kelas'   => 'required|string|max:20',
            'tingkat'      => 'required|in:X,XI,XII',
            'jurusan_id'   => 'required|exists:tbl_jurusan,id',
            'ruangan_id'   => 'nullable|exists:tbl_ruangan,id',
            'tahun_ajaran' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $exists = Kelas::where('nama_kelas', $request->nama_kelas)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'errors'  => ['nama_kelas' => ['Nama kelas sudah ada di tahun ajaran ' . $request->tahun_ajaran]],
            ], 422);
        }

        $kelas->update($request->only(['nama_kelas', 'tingkat', 'jurusan_id', 'ruangan_id', 'tahun_ajaran']));

        return response()->json(['success' => true, 'message' => 'Kelas berhasil diupdate.']);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->siswa()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak bisa dihapus karena masih ada ' . $kelas->siswa()->count() . ' siswa.',
            ], 422);
        }

        $kelas->delete();
        return response()->json(['success' => true, 'message' => 'Kelas berhasil dihapus.']);
    }

    // Untuk dropdown di form (dipakai create/edit siswa juga)
    public function getJurusan()
    {
        return response()->json(Jurusan::orderBy('nama_jurusan')->get(['id', 'kode_jurusan', 'nama_jurusan']));
    }

    public function getRuangan()
    {
        return response()->json(Ruangan::orderBy('nama_ruangan')->get(['id', 'kode_ruangan', 'nama_ruangan', 'jenis_ruangan']));
    }

    /**
     * FIX: Ambil kelas yang belum punya wali kelas,
     * ATAU kelas yang wali kelasnya adalah guru dengan current_guru_id (untuk edit).
     * Hanya kelas yang kosong wali_kelas_id yang muncul di dropdown tambah guru baru.
     */
    public function getAvailableKelas(Request $request)
    {
        $currentGuruId = $request->get('current_guru_id');

        $query = Kelas::query();

        if ($currentGuruId) {
            // Mode edit: tampilkan kelas yang belum punya wali ATAU yang memang milik guru ini
            $query->where(function ($q) use ($currentGuruId) {
                $q->whereNull('wali_kelas_id')
                  ->orWhere('wali_kelas_id', $currentGuruId);
            });
        } else {
            // Mode tambah baru: hanya tampilkan kelas yang belum punya wali kelas
            $query->whereNull('wali_kelas_id');
        }

        $kelas = $query->orderBy('nama_kelas')->get();

        return response()->json($kelas->map(function ($k) {
            return [
                'id'   => $k->id,
                'nama' => $k->nama_kelas . ' - ' . $k->tahun_ajaran,
            ];
        }));
    }
}