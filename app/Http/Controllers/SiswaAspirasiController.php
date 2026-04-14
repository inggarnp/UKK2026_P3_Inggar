<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\HistoryStatus;
use App\Models\Kategori;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class SiswaAspirasiController extends Controller
{
    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $user   = auth()->user();
        $siswa  = $user->siswa;
        $userId = $user->id;

        $totalAspirasi = InputAspirasi::where('user_id', $userId)->count();

        $countByStatus = InputAspirasi::where('user_id', $userId)
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'tbl_input_aspirasi.id')
            ->selectRaw('a.status, COUNT(*) as total')
            ->groupBy('a.status')
            ->pluck('total', 'status');

        $menunggu = $countByStatus['Menunggu'] ?? 0;
        $proses   = $countByStatus['Proses']   ?? 0;
        $selesai  = $countByStatus['Selesai']  ?? 0;

        $terbaru = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi'])
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.siswa', compact(
            'siswa',
            'totalAspirasi',
            'menunggu',
            'proses',
            'selesai',
            'terbaru'
        ));
    }

    // ─── CREATE ───────────────────────────────────────────────
    public function create()
    {
        $userId = auth()->id();

        $jumlahHariIni = InputAspirasi::where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $limit = 3;
        $sisaLimit = max(0, $limit - $jumlahHariIni);

        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $ruanganList  = Ruangan::orderBy('nama_ruangan')->get();

        $siswaSaksiList = \App\Models\User::where('role', 'siswa')
            ->where('id', '!=', $userId)
            ->get();

        return view('pages.siswa.aspirasi.create', compact(
            'kategoriList',
            'ruanganList',
            'sisaLimit',
            'siswaSaksiList'
        ));
    }

    // ─── STORE ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori'   => 'required|exists:tbl_kategori,id',
            'ruangan_id'    => 'nullable|exists:tbl_ruangan,id',
            'lokasi_manual' => 'nullable|string|max:150',
            'keterangan'    => 'required|string|max:500',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (!$request->ruangan_id && !$request->lokasi_manual) {
            return response()->json([
                'success' => false,
                'errors'  => ['lokasi_manual' => ['Pilih ruangan atau isi lokasi manual.']]
            ], 422);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('assets/images/aspirasi'), $namaFile);

            $fotoPath = $namaFile;
        }

        // Simpan ke tbl_input_aspirasi
        $input = InputAspirasi::create([
            'user_id'       => auth()->id(),
            'id_kategori'   => $request->id_kategori,
            'ruangan_id'    => $request->ruangan_id ?: null,
            'lokasi_manual' => $request->ruangan_id ? null : $request->lokasi_manual,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
        ]);

        // Simpan ke tbl_aspirasi
        $aspirasi = Aspirasi::create([
            'id_pelaporan' => $input->id,
            'status'       => 'Menunggu',
        ]);

        // Simpan histori awal
        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => null,
            'status_baru' => 'Menunggu',
            'keterangan'  => 'Aspirasi berhasil dikirim.',
            'diubah_oleh' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Aspirasi berhasil dikirim!']);
    }

    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('pages.siswa.aspirasi.index', compact('kategoriList'));
    }

    // ─── DATA AJAX ────────────────────────────────────────────
    public function data(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');
        $userId  = auth()->id();

        $query = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi'])
            ->where('user_id', $userId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->whereHas('kategori', fn($k) => $k->where('nama_kategori', 'like', "%$search%"))
                        ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
                        ->orWhere('lokasi_manual', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->whereHas('aspirasi', fn($a) => $a->where('status', $status));
            });

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(function ($item) {
            $aspirasi = $item->aspirasi;
            return [
                'id'             => $item->id,
                'aspirasi_id'    => $aspirasi?->id,
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,
                'kode_ruangan'   => $item->ruangan?->kode_ruangan,
                'lantai'         => $item->ruangan?->lantai,
                'gedung'         => $item->ruangan?->gedung,
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'status'         => $aspirasi?->status ?? '-',
                'status_badge'   => $aspirasi?->status_badge ?? 'bg-secondary',
                'feedback_count' => $aspirasi?->feedback()->count() ?? 0,
                'created_at_fmt' => $item->created_at_format,
            ];
        });

        return response()->json([
            'data'         => $data,
            'total'        => $total,
            'current_page' => $page,
            'per_page'     => $perPage,
            'last_page'    => (int) ceil($total / $perPage),
        ]);
    }

    // ─── SHOW AJAX ────────────────────────────────────────────
    public function show($id)
    {
        $item = InputAspirasi::with([
            'kategori',
            'ruangan',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',
        ])->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $aspirasi = $item->aspirasi;

        return response()->json([
            'id'             => $item->id,
            'aspirasi_id'    => $aspirasi?->id,
            'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display' => $item->lokasi_display,
            'kode_ruangan'   => $item->ruangan?->kode_ruangan,
            'lantai'         => $item->ruangan?->lantai,
            'gedung'         => $item->ruangan?->gedung,
            'keterangan'     => $item->keterangan,
            'foto_url'       => $item->foto_url,
            'status'         => $aspirasi?->status ?? '-',
            'status_badge'   => $aspirasi?->status_badge ?? 'bg-secondary',
            'created_at_fmt' => $item->created_at_format,

            // Feedback dari admin/petugas
            'feedback' => $aspirasi?->feedback->map(fn($f) => [
                'isi_feedback'    => $f->isi_feedback,
                'nama_pemberi'    => $f->user?->nama ?? '-',
                'created_at_fmt'  => $f->created_at_format,
            ]) ?? [],

            // Histori perubahan status
            'histori' => $aspirasi?->historyStatus->map(fn($h) => [
                'status'         => $h->status,
                'status_badge'   => $h->status_badge,
                'keterangan'     => $h->keterangan,
                'created_at_fmt' => $h->created_at_format,
            ]) ?? [],

            // Progres perbaikan
            'progres' => $aspirasi?->progres->map(fn($p) => [
                'keterangan_progres' => $p->keterangan_progres,
                'nama_petugas'       => $p->user?->nama ?? '-',
                'created_at_fmt'     => $p->created_at_format,
            ]) ?? [],
        ]);
    }

    // ─── HISTORY ─────────────────────────────────────────────
    public function history()
    {
        $histori = HistoryStatus::with(['aspirasi.inputAspirasi.kategori', 'aspirasi.inputAspirasi.ruangan'])
            ->whereHas('aspirasi.inputAspirasi', fn($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->paginate(15);

        return view('pages.siswa.aspirasi.history', compact('histori'));
    }

    // ─── GET DETAIL RUANGAN (AJAX autofill) ───────────────────
    public function getRuangan($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }
        return response()->json([
            'id'           => $ruangan->id,
            'nama_ruangan' => $ruangan->nama_ruangan,
            'kode_ruangan' => $ruangan->kode_ruangan,
            'lantai'       => $ruangan->lantai,
            'gedung'       => $ruangan->gedung,
            'kondisi'      => $ruangan->kondisi,
        ]);
    }
}
