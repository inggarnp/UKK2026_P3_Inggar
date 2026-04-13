<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaAspirasiController extends Controller
{
    // ─── Helper: ambil data siswa yang login ──────────────────
    private function getSiswa()
    {
        return DB::table('tbl_siswa')
            ->where('user_id', auth()->id())
            ->first();
    }

    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $siswa = $this->getSiswa();
        $userId = auth()->id();

        $totalAspirasi = DB::table('tbl_input_aspirasi')
            ->where('user_id', $userId)->count();

        $menunggu = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->where('ia.user_id', $userId)
            ->where('a.status', 'Menunggu')->count();

        $proses = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->where('ia.user_id', $userId)
            ->where('a.status', 'Proses')->count();

        $selesai = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->where('ia.user_id', $userId)
            ->where('a.status', 'Selesai')->count();

        // 5 aspirasi terbaru
        $terbaru = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->where('ia.user_id', $userId)
            ->select('ia.id', 'k.nama_kategori', 'ia.lokasi', 'ia.keterangan', 'a.status', 'ia.created_at')
            ->orderByDesc('ia.created_at')
            ->limit(5)
            ->get();

        return view('dashboard.siswa', compact(
            'siswa', 'totalAspirasi', 'menunggu', 'proses', 'selesai', 'terbaru'
        ));
    }

    // ─── FORM INPUT ASPIRASI ──────────────────────────────────
    public function create()
    {
        $kategoriList = DB::table('tbl_kategori')->orderBy('nama_kategori')->get();
        return view('pages.siswa.aspirasi.create', compact('kategoriList'));
    }

    // ─── STORE ASPIRASI ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:tbl_kategori,id',
            'lokasi'      => 'required|string|max:100',
            'keterangan'  => 'required|string|max:500',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('aspirasi', 'public');
        }

        $inputId = DB::table('tbl_input_aspirasi')->insertGetId([
            'user_id'     => auth()->id(),
            'id_kategori' => $request->id_kategori,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan,
            'foto'        => $fotoPath,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $aspirasId = DB::table('tbl_aspirasi')->insertGetId([
            'id_pelaporan' => $inputId,
            'status'       => 'Menunggu',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('tbl_history_status')->insert([
            'id_aspirasi' => $aspirasId,
            'status'      => 'Menunggu',
            'keterangan'  => 'Aspirasi berhasil dikirim.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aspirasi berhasil dikirim!',
        ]);
    }

    // ─── DAFTAR ASPIRASI (AJAX DataTable) ────────────────────
    public function index()
    {
        $kategoriList = DB::table('tbl_kategori')->orderBy('nama_kategori')->get();
        return view('pages.siswa.aspirasi.index', compact('kategoriList'));
    }

    public function data(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');
        $userId  = auth()->id();

        $query = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->where('ia.user_id', $userId)
            ->select(
                'ia.id', 'a.id as aspirasi_id',
                'k.nama_kategori', 'ia.lokasi',
                'ia.keterangan', 'ia.foto',
                'a.status', 'a.feedback',
                'ia.created_at'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('k.nama_kategori', 'like', "%$search%")
                  ->orWhere('ia.lokasi', 'like', "%$search%")
                  ->orWhere('ia.keterangan', 'like', "%$search%");
            });
        }

        if ($status) $query->where('a.status', $status);

        $total = $query->count();
        $data  = $query->orderByDesc('ia.created_at')
                       ->offset(($page - 1) * $perPage)
                       ->limit($perPage)
                       ->get();

        $data->transform(function ($item) {
            $item->foto_url = $item->foto ? asset('storage/' . $item->foto) : null;
            $item->created_at_fmt = \Carbon\Carbon::parse($item->created_at)
                ->locale('id')->isoFormat('D MMM Y, HH:mm');
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

    // ─── DETAIL ASPIRASI (AJAX) ───────────────────────────────
    public function show($id)
    {
        $item = DB::table('tbl_input_aspirasi as ia')
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'ia.id')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->where('ia.id', $id)
            ->where('ia.user_id', auth()->id()) // hanya milik sendiri
            ->select(
                'ia.id', 'a.id as aspirasi_id',
                'k.nama_kategori', 'ia.lokasi',
                'ia.keterangan', 'ia.foto',
                'a.status', 'a.feedback',
                'ia.created_at', 'ia.updated_at'
            )
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $item->foto_url = $item->foto ? asset('storage/' . $item->foto) : null;
        $item->created_at_fmt = \Carbon\Carbon::parse($item->created_at)
            ->locale('id')->isoFormat('D MMM Y, HH:mm');

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

    // ─── HISTORY ─────────────────────────────────────────────
    public function history()
    {
        $userId = auth()->id();

        $histori = DB::table('tbl_history_status as hs')
            ->join('tbl_aspirasi as a', 'a.id', '=', 'hs.id_aspirasi')
            ->join('tbl_input_aspirasi as ia', 'ia.id', '=', 'a.id_pelaporan')
            ->join('tbl_kategori as k', 'k.id', '=', 'ia.id_kategori')
            ->where('ia.user_id', $userId)
            ->select(
                'hs.id', 'hs.status', 'hs.keterangan',
                'hs.created_at', 'ia.id as input_id',
                'k.nama_kategori', 'ia.lokasi'
            )
            ->orderByDesc('hs.created_at')
            ->paginate(15);

        // Format tanggal
        $histori->getCollection()->transform(function ($h) {
            $h->created_at_fmt = \Carbon\Carbon::parse($h->created_at)
                ->locale('id')->isoFormat('D MMM Y, HH:mm');
            return $h;
        });

        return view('pages.siswa.aspirasi.history', compact('histori'));
    }
}