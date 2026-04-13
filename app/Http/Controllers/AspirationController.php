<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AspirationController extends Controller
{
    // ─── INDEX — Halaman Utama ─────────────────────────────────
    public function index()
    {
        $kategoriList = DB::table('tbl_kategori')->orderBy('nama_kategori')->get();
        $siswaList    = DB::table('tbl_siswa')->orderBy('nama')->get(['user_id', 'nama', 'nis']);
        $guruList     = DB::table('tbl_guru')->orderBy('nama')->get(['user_id', 'nama', 'nip']);
        return view('pages.input_aspirasi.index', compact('kategoriList', 'siswaList', 'guruList'));
    }

    // ─── STORE — Simpan aspirasi baru ────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:tbl_users,id',
            'id_kategori' => 'required|exists:tbl_kategori,id',
            'lokasi'      => 'required|string|max:100',
            'keterangan'  => 'required|string|max:500',
            'status'      => 'required|in:Menunggu,Proses,Selesai',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('aspirasi', 'public');
        }

        // Simpan ke tbl_input_aspirasi
        $inputId = DB::table('tbl_input_aspirasi')->insertGetId([
            'user_id'     => $request->user_id,
            'id_kategori' => $request->id_kategori,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan,
            'foto'        => $fotoPath,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Simpan ke tbl_aspirasi
        $aspirasId = DB::table('tbl_aspirasi')->insertGetId([
            'id_pelaporan' => $inputId,
            'status'       => $request->status,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Simpan histori awal
        DB::table('tbl_history_status')->insert([
            'id_aspirasi' => $aspirasId,
            'status'      => $request->status,
            'keterangan'  => 'Aspirasi dibuat oleh admin.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aspirasi berhasil ditambahkan.',
        ]);
    }

    // ─── DATA — AJAX untuk tabel ──────────────────────────────
    public function data(Request $request)
    {
        $page      = (int) $request->get('page', 1);
        $perPage   = (int) $request->get('per_page', 10);
        $search    = $request->get('search', '');
        $kategori  = $request->get('kategori', '');
        $status    = $request->get('status', '');
        $role      = $request->get('role', '');
        $dateFrom  = $request->get('date_from', '');
        $dateTo    = $request->get('date_to', '');

        $query = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->join('tbl_users as u', 'u.id', '=', 'ia.user_id')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->leftJoin('tbl_siswa as s', function ($join) {
                $join->on('s.user_id', '=', 'u.id')->where('u.role', '=', 'siswa');
            })
            ->leftJoin('tbl_guru as g', function ($join) {
                $join->on('g.user_id', '=', 'u.id')->where('u.role', '=', 'guru');
            })
            ->select(
                'ia.id',
                'a.id as aspirasi_id',
                DB::raw("COALESCE(s.nama, g.nama, u.email) as nama_pelapor"),
                DB::raw("COALESCE(s.nis, g.nip, '-') as identitas"),
                'u.role',
                'k.nama_kategori',
                'ia.lokasi',
                'ia.keterangan',
                'ia.foto',
                'a.status',
                'ia.created_at'
            );

        // ─── Filter pencarian ──────────────────────────────────
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('s.nama', 'like', "%$search%")
                  ->orWhere('g.nama', 'like', "%$search%")
                  ->orWhere('u.email', 'like', "%$search%")
                  ->orWhere('ia.lokasi', 'like', "%$search%")
                  ->orWhere('ia.keterangan', 'like', "%$search%")
                  ->orWhere('k.nama_kategori', 'like', "%$search%");
            });
        }

        if ($kategori)  $query->where('ia.id_kategori', $kategori);
        if ($status)    $query->where('a.status', $status);
        if ($role)      $query->where('u.role', $role);
        if ($dateFrom)  $query->whereDate('ia.created_at', '>=', $dateFrom);
        if ($dateTo)    $query->whereDate('ia.created_at', '<=', $dateTo);

        $total   = $query->count();
        $data    = $query->orderByDesc('ia.created_at')
                         ->offset(($page - 1) * $perPage)
                         ->limit($perPage)
                         ->get();

        // Format foto URL
        $data->transform(function ($item) {
            $item->foto_url = $item->foto
                ? asset('storage/' . $item->foto)
                : null;
            $item->created_at_fmt = \Carbon\Carbon::parse($item->created_at)
                ->locale('id')
                ->isoFormat('D MMM Y, HH:mm');
            return $item;
        });

        return response()->json([
            'data'         => $data,
            'total'        => $total,
            'current_page' => $page,
            'per_page'     => $perPage,
            'last_page'    => (int) ceil($total / $perPage),
        ]);
    }

    // ─── SHOW — Detail aspirasi (AJAX) ────────────────────────
    public function show($id)
    {
        $item = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->join('tbl_users as u', 'u.id', '=', 'ia.user_id')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->leftJoin('tbl_siswa as s', function ($join) {
                $join->on('s.user_id', '=', 'u.id')->where('u.role', '=', 'siswa');
            })
            ->leftJoin('tbl_guru as g', function ($join) {
                $join->on('g.user_id', '=', 'u.id')->where('u.role', '=', 'guru');
            })
            ->select(
                'ia.id',
                'a.id as aspirasi_id',
                DB::raw("COALESCE(s.nama, g.nama, u.email) as nama_pelapor"),
                DB::raw("COALESCE(s.nis, g.nip, '-') as identitas"),
                'u.role',
                'u.email',
                'k.nama_kategori',
                'k.deskripsi as kategori_deskripsi',
                'ia.lokasi',
                'ia.keterangan',
                'ia.foto',
                'a.status',
                'a.feedback',
                'ia.created_at',
                'ia.updated_at'
            )
            ->where('ia.id', $id)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $item->foto_url = $item->foto
            ? asset('storage/' . $item->foto)
            : null;
        $item->created_at_fmt = \Carbon\Carbon::parse($item->created_at)
            ->locale('id')->isoFormat('D MMM Y, HH:mm');
        $item->updated_at_fmt = \Carbon\Carbon::parse($item->updated_at)
            ->locale('id')->isoFormat('D MMM Y, HH:mm');

        // Histori status
        $item->histori = DB::table('tbl_history_status')
            ->where('id_aspirasi', $item->aspirasi_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                $h->created_at_fmt = \Carbon\Carbon::parse($h->created_at)
                    ->locale('id')->isoFormat('D MMM Y, HH:mm');
                return $h;
            });

        return response()->json($item);
    }

    // ─── UPDATE STATUS — AJAX ─────────────────────────────────
    public function updateStatus(Request $request, $aspirasi_id)
    {
        $request->validate([
            'status'   => 'required|in:Menunggu,Proses,Selesai',
            'feedback' => 'nullable|string|max:500',
        ]);

        $exists = DB::table('tbl_aspirasi')->where('id', $aspirasi_id)->first();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        DB::table('tbl_aspirasi')->where('id', $aspirasi_id)->update([
            'status'     => $request->status,
            'feedback'   => $request->feedback,
            'updated_at' => now(),
        ]);

        // Simpan ke histori
        DB::table('tbl_history_status')->insert([
            'id_aspirasi' => $aspirasi_id,
            'status'      => $request->status,
            'keterangan'  => $request->feedback,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status aspirasi berhasil diperbarui.',
        ]);
    }

    // ─── DESTROY — Hapus aspirasi ─────────────────────────────
    public function destroy($id)
    {
        $item = DB::table('tbl_input_aspirasi')->where('id', $id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Hapus foto dari storage jika ada
        if ($item->foto && \Storage::disk('public')->exists($item->foto)) {
            \Storage::disk('public')->delete($item->foto);
        }

        // Cari aspirasi terkait
        $aspirasi = DB::table('tbl_aspirasi')->where('id_pelaporan', $id)->first();
        if ($aspirasi) {
            DB::table('tbl_history_status')->where('id_aspirasi', $aspirasi->id)->delete();
            DB::table('tbl_aspirasi')->where('id', $aspirasi->id)->delete();
        }

        DB::table('tbl_input_aspirasi')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aspirasi berhasil dihapus.',
        ]);
    }
}