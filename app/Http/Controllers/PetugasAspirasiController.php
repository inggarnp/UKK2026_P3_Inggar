<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\HistoryStatus;
use App\Models\Progres;
use App\Models\Kategori;
use App\Models\PetugasSarana;
use Illuminate\Http\Request;

class PetugasAspirasiController extends Controller
{
    private function getPetugas()
    {
        return PetugasSarana::where('user_id', auth()->id())->first();
    }

    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $petugas = $this->getPetugas();

        $total    = InputAspirasi::where('status_alur', InputAspirasi::ALUR_DISETUJUI)->count();
        $menunggu = InputAspirasi::where('status_alur', InputAspirasi::ALUR_DISETUJUI)
            ->whereHas('aspirasi', fn($q) => $q->where('status', 'Menunggu'))->count();
        $proses   = InputAspirasi::where('status_alur', InputAspirasi::ALUR_DISETUJUI)
            ->whereHas('aspirasi', fn($q) => $q->where('status', 'Proses'))->count();
        $selesai  = InputAspirasi::where('status_alur', InputAspirasi::ALUR_DISETUJUI)
            ->whereHas('aspirasi', fn($q) => $q->where('status', 'Selesai'))->count();

        $terbaru = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa', 'user.guru', 'aspirasi'])
            ->where('status_alur', InputAspirasi::ALUR_DISETUJUI)
            ->latest()->limit(5)->get();

        return view('dashboard.petugas', compact('petugas', 'total', 'menunggu', 'proses', 'selesai', 'terbaru'));
    }

    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('pages.petugas.aspirasi.index', compact('kategoriList'));
    }

    // ─── DATA AJAX ────────────────────────────────────────────
    public function data(Request $request)
    {
        $page     = (int) $request->get('page', 1);
        $perPage  = (int) $request->get('per_page', 10);
        $search   = $request->get('search', '');
        $status   = $request->get('status', '');
        $kategori = $request->get('kategori', '');

        $query = InputAspirasi::with([
            'kategori', 'ruangan',
            'user.siswa', 'user.guru',
            'aspirasi', 'reviewer'
        ])
        ->where('status_alur', InputAspirasi::ALUR_DISETUJUI)
        ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
            $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
               ->orWhereHas('user.guru', fn($g) => $g->where('nama', 'like', "%$search%"))
               ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
               ->orWhere('lokasi_manual', 'like', "%$search%")
               ->orWhere('keterangan', 'like', "%$search%");
        }))
        ->when($status, fn($q) => $q->whereHas('aspirasi', fn($a) => $a->where('status', $status)))
        ->when($kategori, fn($q) => $q->where('id_kategori', $kategori));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(function ($item) {
            $user   = $item->user;
            $profil = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;
            return [
                'id'             => $item->id,
                'aspirasi_id'    => $item->aspirasi?->id,
                'nama_pelapor'   => $profil?->nama ?? $user?->email ?? '-',
                'role'           => $user?->role ?? '-',
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'status'         => $item->aspirasi?->status ?? '-',
                'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
                'disetujui_oleh' => $item->reviewer?->nama ?? '-',
                'created_at_fmt' => $item->created_at_format,
            ];
        });

        return response()->json([
            'data' => $data, 'total' => $total,
            'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    // ─── SHOW AJAX ────────────────────────────────────────────
    public function show($id)
    {
        $item = InputAspirasi::with([
            'kategori', 'ruangan',
            'user.siswa', 'user.guru', 'reviewer',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',
        ])->where('id', $id)
          ->where('status_alur', InputAspirasi::ALUR_DISETUJUI)
          ->first();

        if (!$item) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

        $aspirasi = $item->aspirasi;
        $user     = $item->user;
        $profil   = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;

        return response()->json([
            'id'             => $item->id,
            'aspirasi_id'    => $aspirasi?->id,
            'nama_pelapor'   => $profil?->nama ?? $user?->email ?? '-',
            'role'           => $user?->role ?? '-',
            'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display' => $item->lokasi_display,
            'kode_ruangan'   => $item->ruangan?->kode_ruangan,
            'lantai'         => $item->ruangan?->lantai,
            'gedung'         => $item->ruangan?->gedung,
            'keterangan'     => $item->keterangan,
            'foto_url'       => $item->foto_url,
            'status'         => $aspirasi?->status ?? '-',
            'status_badge'   => $aspirasi?->status_badge ?? 'bg-secondary',
            'disetujui_oleh' => $item->reviewer?->nama ?? '-',
            'created_at_fmt' => $item->created_at_format,

            'feedback' => $aspirasi?->feedback->map(fn($f) => [
                'isi_feedback'   => $f->isi_feedback,
                'nama_pemberi'   => $f->user?->nama ?? '-',
                'created_at_fmt' => $f->created_at_format,
            ]) ?? [],
            'histori' => $aspirasi?->historyStatus->map(fn($h) => [
                'status'         => $h->status_baru ?? $h->status,
                'status_badge'   => $h->status_badge,
                'keterangan'     => $h->keterangan,
                'created_at_fmt' => $h->created_at_format,
            ]) ?? [],
            'progres' => $aspirasi?->progres->map(fn($p) => [
                'keterangan_progres' => $p->keterangan_progres,
                'nama_petugas'       => $p->user?->nama ?? '-',
                'created_at_fmt'     => $p->created_at_format,
            ]) ?? [],
        ]);
    }

    // ─── TAMBAH PROGRES ───────────────────────────────────────
    public function tambahProgres(Request $request, $aspirasi_id)
    {
        $request->validate(['keterangan_progres' => 'required|string|max:500']);

        $aspirasi = Aspirasi::find($aspirasi_id);
        if (!$aspirasi) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        Progres::create([
            'id_aspirasi'        => $aspirasi->id,
            'user_id'            => auth()->id(),
            'keterangan_progres' => $request->keterangan_progres,
        ]);

        return response()->json(['success' => true, 'message' => 'Progres berhasil ditambahkan.']);
    }

    // ─── UPDATE STATUS — pakai POST bukan PUT untuk hindari konflik ─
    public function updateStatus(Request $request, $aspirasi_id)
    {
        $request->validate([
            'status'             => 'required|in:Proses,Selesai',
            'keterangan_progres' => 'nullable|string|max:500',
        ]);

        $aspirasi = Aspirasi::find($aspirasi_id);
        if (!$aspirasi) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        $statusLama = $aspirasi->status;
        $aspirasi->update(['status' => $request->status]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => $statusLama,
            'status_baru' => $request->status,
            'keterangan'  => $request->keterangan_progres ?? 'Status diperbarui oleh petugas sarana.',
            'diubah_oleh' => auth()->id(),
        ]);

        if ($request->keterangan_progres) {
            Progres::create([
                'id_aspirasi'        => $aspirasi->id,
                'user_id'            => auth()->id(),
                'keterangan_progres' => $request->keterangan_progres,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }
}