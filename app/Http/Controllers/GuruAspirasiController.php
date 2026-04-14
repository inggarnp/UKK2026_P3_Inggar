<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\HistoryStatus;
use App\Models\Feedback;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruAspirasiController extends Controller
{
    // ─── Helper: ambil data guru login ────────────────────────
    private function getGuru(): Guru
    {
        return Guru::where('user_id', auth()->id())->firstOrFail();
    }

    // ─── DASHBOARD GURU ───────────────────────────────────────
    public function dashboard()
    {
        $guru   = $this->getGuru();
        $userId = auth()->id();

        // Aspirasi dari siswa di kelas yang guru ini wali-i
        $kelasIds = $guru->kelasWali->pluck('id');

        $menungguReview = InputAspirasi::whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->where('status_alur', InputAspirasi::ALUR_MENUNGGU)->count();

        // Aspirasi guru sendiri
        $totalAspirasiSendiri = InputAspirasi::where('user_id', $userId)->count();

        return view('dashboard.guru', compact('guru', 'menungguReview', 'totalAspirasiSendiri'));
    }

    // ─── REVIEW ASPIRASI SISWA ────────────────────────────────
    // Guru hanya bisa review aspirasi dari siswa di kelasnya

    public function reviewIndex()
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');
        return view('pages.guru.aspirasi.review', compact('guru', 'kelasIds'));
    }

    public function reviewData(Request $request)
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');

        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $alur    = $request->get('alur', '');

        $query = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa', 'aspirasi', 'saksi'])
            ->whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
                   ->orWhere('keterangan', 'like', "%$search%")
                   ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"));
            }))
            ->when($alur, fn($q) => $q->where('status_alur', $alur));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(fn($item) => [
            'id'                => $item->id,
            'nama_siswa'        => $item->user?->siswa?->nama ?? '-',
            'kelas'             => $item->user?->siswa?->kelas?->nama_kelas ?? '-',
            'nama_kategori'     => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display'    => $item->lokasi_display,
            'keterangan'        => $item->keterangan,
            'foto_url'          => $item->foto_url,
            'saksi_nama'        => $item->saksi?->nama ?? '-',
            'status_alur'       => $item->status_alur,
            'status_alur_label' => $item->status_alur_label,
            'status_alur_badge' => $item->status_alur_badge,
            'created_at_fmt'    => $item->created_at_format,
        ]);

        return response()->json([
            'data' => $data, 'total' => $total,
            'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    // ─── APPROVE ASPIRASI ─────────────────────────────────────
    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:300',
        ]);

        $guru = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');

        $item = InputAspirasi::whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->where('id', $id)
            ->where('status_alur', InputAspirasi::ALUR_MENUNGGU)
            ->firstOrFail();

        $item->update([
            'status_alur'    => InputAspirasi::ALUR_DISETUJUI,
            'reviewed_by'    => $guru->id,
            'reviewed_at'    => now(),
            'catatan_review' => $request->catatan ?? 'Disetujui oleh wali kelas.',
        ]);

        // Update status aspirasi → Proses (lanjut ke petugas)
        $item->aspirasi?->update(['status' => 'Proses']);

        // Catat histori
        HistoryStatus::create([
            'id_aspirasi' => $item->aspirasi->id,
            'status_lama' => 'Menunggu',
            'status_baru' => 'Proses',
            'status'      => 'Proses',
            'keterangan'  => 'Disetujui oleh wali kelas: ' . ($request->catatan ?? '-'),
            'diubah_oleh' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Aspirasi berhasil disetujui dan diteruskan ke Petugas Sarana.']);
    }

    // ─── REJECT ASPIRASI ──────────────────────────────────────
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:300',
        ]);

        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');

        $item = InputAspirasi::whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->where('id', $id)
            ->where('status_alur', InputAspirasi::ALUR_MENUNGGU)
            ->firstOrFail();

        $item->update([
            'status_alur'    => InputAspirasi::ALUR_DITOLAK,
            'reviewed_by'    => $guru->id,
            'reviewed_at'    => now(),
            'catatan_review' => $request->catatan,
        ]);

        HistoryStatus::create([
            'id_aspirasi' => $item->aspirasi->id,
            'status_lama' => 'Menunggu',
            'status_baru' => 'Menunggu',
            'status'      => 'Menunggu',
            'keterangan'  => 'Ditolak oleh wali kelas: ' . $request->catatan,
            'diubah_oleh' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Aspirasi ditolak dan dikembalikan ke siswa.']);
    }

    // ─── INPUT ASPIRASI GURU SENDIRI ──────────────────────────
    public function create()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $ruanganList  = Ruangan::orderBy('nama_ruangan')->get();
        return view('pages.guru.aspirasi.create', compact('kategoriList', 'ruanganList'));
    }

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
            $file     = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/aspirasi'), $namaFile);
            $fotoPath = $namaFile;
        }

        // Guru langsung disetujui — tidak perlu review
        $input = InputAspirasi::create([
            'user_id'       => auth()->id(),
            'id_kategori'   => $request->id_kategori,
            'ruangan_id'    => $request->ruangan_id ?: null,
            'lokasi_manual' => $request->ruangan_id ? null : $request->lokasi_manual,
            'status_alur'   => InputAspirasi::ALUR_DISETUJUI,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
        ]);

        $aspirasi = Aspirasi::create([
            'id_pelaporan' => $input->id,
            'status'       => 'Proses',
        ]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => null,
            'status_baru' => 'Proses',
            'status'      => 'Proses',
            'keterangan'  => 'Aspirasi dikirim oleh guru, langsung diteruskan ke Petugas Sarana.',
            'diubah_oleh' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Aspirasi berhasil dikirim ke Petugas Sarana.']);
    }

    // ─── DAFTAR ASPIRASI GURU SENDIRI ─────────────────────────
    public function index()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('pages.guru.aspirasi.index', compact('kategoriList'));
    }

    public function data(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');
        $userId  = auth()->id();

        $query = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi'])
            ->where('user_id', $userId)
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('kategori', fn($k) => $k->where('nama_kategori', 'like', "%$search%"))
                   ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
                   ->orWhere('lokasi_manual', 'like', "%$search%")
                   ->orWhere('keterangan', 'like', "%$search%");
            }))
            ->when($status, fn($q) => $q->whereHas('aspirasi', fn($a) => $a->where('status', $status)));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(fn($item) => [
            'id'             => $item->id,
            'aspirasi_id'    => $item->aspirasi?->id,
            'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display' => $item->lokasi_display,
            'keterangan'     => $item->keterangan,
            'foto_url'       => $item->foto_url,
            'status'         => $item->aspirasi?->status ?? '-',
            'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
            'created_at_fmt' => $item->created_at_format,
        ]);

        return response()->json([
            'data' => $data, 'total' => $total,
            'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    public function show($id)
    {
        $item = InputAspirasi::with([
            'kategori', 'ruangan',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',
        ])->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$item) return response()->json(['message' => 'Tidak ditemukan.'], 404);

        $aspirasi = $item->aspirasi;
        return response()->json([
            'id'             => $item->id,
            'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display' => $item->lokasi_display,
            'keterangan'     => $item->keterangan,
            'foto_url'       => $item->foto_url,
            'status'         => $aspirasi?->status ?? '-',
            'status_badge'   => $aspirasi?->status_badge ?? 'bg-secondary',
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
}