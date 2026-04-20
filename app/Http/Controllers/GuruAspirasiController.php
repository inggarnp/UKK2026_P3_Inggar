<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\HistoryStatus;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Guru;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class GuruAspirasiController extends Controller
{
    private function getGuru(): Guru
    {
        return Guru::where('user_id', auth()->id())->firstOrFail();
    }

    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $userId   = auth()->id();
        $kelasIds = $guru->kelasWali->pluck('id');

        $totalSiswaAspirasi   = InputAspirasi::whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))->count();
        $totalAspirasiSendiri = InputAspirasi::where('user_id', $userId)->count();

        $aspirasiTerbaruSiswa = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa.kelas', 'aspirasi'])
            ->whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->latest()->limit(5)->get();

        return view('dashboard.guru', compact(
            'guru', 'totalSiswaAspirasi', 'totalAspirasiSendiri', 'aspirasiTerbaruSiswa'
        ));
    }

    // ─── LIHAT ASPIRASI SISWA (read only) ─────────────────────
    public function siswaIndex()
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('pages.guru.aspirasi.siswa', compact('guru', 'kelasIds', 'kategoriList'));
    }

    public function siswaData(Request $request)
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');

        $query = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa.kelas', 'aspirasi', 'saksi'])
            ->whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
                   ->orWhere('keterangan', 'like', "%$search%")
                   ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
                   ->orWhere('lokasi_manual', 'like', "%$search%");
            }))
            ->when($status, fn($q) => $q->whereHas('aspirasi', fn($a) => $a->where('status', $status)));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'data' => $items->map(fn($item) => [
                'id'             => $item->id,
                'nama_siswa'     => $item->user?->siswa?->nama ?? '-',
                'kelas'          => $item->user?->siswa?->kelas?->nama_kelas ?? '-',
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'saksi_nama'     => $item->saksi?->nama ?? '-',
                'status'         => $item->aspirasi?->status ?? '-',
                'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
                'created_at_fmt' => $item->created_at_format,
            ]),
            'total' => $total, 'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    public function siswaShow($id)
    {
        $guru     = $this->getGuru()->load('kelasWali');
        $kelasIds = $guru->kelasWali->pluck('id');

        $item = InputAspirasi::with([
            'kategori', 'ruangan', 'saksi', 'user.siswa.kelas',
            'aspirasi.historyStatus', 'aspirasi.feedback.user', 'aspirasi.progres.user',
        ])->whereHas('user.siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))
          ->where('id', $id)->first();

        if (!$item) return response()->json(['message' => 'Tidak ditemukan.'], 404);

        return response()->json($this->buildShowResponse($item));
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
            $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/aspirasi'), $namaFile);
            $fotoPath = $namaFile;
        }

        $input = InputAspirasi::create([
            'user_id'       => auth()->id(),
            'id_kategori'   => $request->id_kategori,
            'ruangan_id'    => $request->ruangan_id ?: null,
            'lokasi_manual' => $request->ruangan_id ? null : $request->lokasi_manual,
            'status_alur'   => InputAspirasi::ALUR_DISETUJUI,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
        ]);

        // FIX: Status Menunggu (sama seperti siswa), bukan langsung Proses
        $aspirasi = Aspirasi::create([
            'id_pelaporan' => $input->id,
            'status'       => 'Menunggu',
        ]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => null,
            'status_baru' => 'Menunggu',
            'status'      => 'Menunggu',
            'keterangan'  => 'Aspirasi dikirim oleh guru, langsung diteruskan ke Petugas Sarana.',
            'diubah_oleh' => auth()->id(),
        ]);

        // FIX: Kirim notif ke SEMUA petugas sarana
        $this->notifPetugas($input);

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

        return response()->json([
            'data' => $items->map(fn($item) => [
                'id'             => $item->id,
                'aspirasi_id'    => $item->aspirasi?->id,
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'status'         => $item->aspirasi?->status ?? '-',
                'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
                'created_at_fmt' => $item->created_at_format,
            ]),
            'total' => $total, 'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    public function show($id)
    {
        $item = InputAspirasi::with([
            'kategori', 'ruangan',
            'aspirasi.feedback.user', 'aspirasi.historyStatus', 'aspirasi.progres.user',
        ])->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$item) return response()->json(['message' => 'Tidak ditemukan.'], 404);

        return response()->json($this->buildShowResponse($item));
    }

    // ─── HISTORI ─────────────────────────────────────────────
    public function history()
    {
        $histori = HistoryStatus::with([
            'aspirasi.inputAspirasi.kategori',
            'aspirasi.inputAspirasi.ruangan',
        ])
        ->whereHas('aspirasi.inputAspirasi', fn($q) => $q->where('user_id', auth()->id()))
        ->latest()->paginate(15);

        return view('pages.guru.aspirasi.history', compact('histori'));
    }

    // ─── Helper: build response show (include foto progres) ───
    private function buildShowResponse(InputAspirasi $item): array
    {
        $aspirasi = $item->aspirasi;
        $siswa    = $item->user?->siswa;

        return [
            'id'             => $item->id,
            'aspirasi_id'    => $aspirasi?->id,
            'nama_siswa'     => $siswa?->nama ?? null,
            'kelas'          => $siswa?->kelas?->nama_kelas ?? null,
            'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display' => $item->lokasi_display,
            'kode_ruangan'   => $item->ruangan?->kode_ruangan,
            'lantai'         => $item->ruangan?->lantai,
            'gedung'         => $item->ruangan?->gedung,
            'keterangan'     => $item->keterangan,
            'foto_url'       => $item->foto_url,
            'saksi_nama'     => $item->saksi?->nama ?? '-',
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
            // FIX: include foto_url di progres
            'progres' => $aspirasi?->progres->map(fn($p) => [
                'keterangan_progres' => $p->keterangan_progres,
                'foto_url'           => $p->foto_url,
                'nama_petugas'       => $p->user?->nama ?? '-',
                'created_at_fmt'     => $p->created_at_format,
            ]) ?? [],
        ];
    }

    // ─── Helper: notif ke semua petugas sarana ────────────────
    private function notifPetugas(InputAspirasi $input): void
    {
        $guru     = $this->getGuru();
        $kategori = $input->kategori?->nama_kategori ?? 'aspirasi';
        $lokasi   = $input->lokasi_display ?? '-';

        // Kirim ke semua user dengan role petugas_sarana
        $petugasUsers = User::where('role', 'petugas_sarana')->get();
        foreach ($petugasUsers as $petugas) {
            Notifikasi::kirim(
                $petugas->id,
                '📋 Laporan Baru dari Guru',
                "Guru {$guru->nama} melaporkan masalah di {$lokasi} ({$kategori}). Segera tindaklanjuti.",
                'warning',
                url('/petugas/aspirasi'),
                'solar:document-add-bold-duotone'
            );
        }
    }
}