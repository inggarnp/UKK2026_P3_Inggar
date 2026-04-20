<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\HistoryStatus;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SiswaAspirasiController extends Controller
{
    const LIMIT_HARIAN = 3;

    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Fresh query
        $siswa = Siswa::with(['kelas', 'jurusan'])->where('user_id', $userId)->first();

        $totalAspirasi = InputAspirasi::where('user_id', $userId)->count();

        $countByStatus = InputAspirasi::where('tbl_input_aspirasi.user_id', $userId)
            ->join('tbl_aspirasi as a', 'a.id_pelaporan', '=', 'tbl_input_aspirasi.id')
            ->selectRaw('a.status, COUNT(*) as total')
            ->groupBy('a.status')
            ->pluck('total', 'status');

        $menunggu = $countByStatus['Menunggu'] ?? 0;
        $proses   = $countByStatus['Proses']   ?? 0;
        $selesai  = $countByStatus['Selesai']  ?? 0;

        $terkirimHariIni = $this->hitungTerkirimHariIni($userId);
        $sisaLimit       = max(0, self::LIMIT_HARIAN - $terkirimHariIni);

        $terbaru = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi'])
            ->where('user_id', $userId)->latest()->limit(5)->get();

        return view('dashboard.siswa', compact(
            'siswa', 'totalAspirasi', 'menunggu', 'proses', 'selesai', 'terbaru', 'sisaLimit'
        ));
    }

    // ─── CREATE — fresh query untuk kode verif ────────────────
    public function create()
    {
        $userId          = auth()->id();
        $terkirimHariIni = $this->hitungTerkirimHariIni($userId);
        $sisaLimit       = max(0, self::LIMIT_HARIAN - $terkirimHariIni);

        if ($sisaLimit <= 0) {
            return redirect()->route('siswa.aspirasi.index')
                ->with('error', 'Kamu sudah mencapai batas maksimal 3 aspirasi per hari.');
        }

        // FRESH query langsung ke DB — tidak pakai cached relation
        $siswa        = Siswa::where('user_id', $userId)->first();
        $sudahSetKode = !is_null($siswa?->kode_verifikasi);
        $role         = 'siswa';

        $kategoriList   = Kategori::orderBy('nama_kategori')->get();
        $ruanganList    = Ruangan::orderBy('nama_ruangan')->get();
        $siswaSaksiList = Siswa::with('kelas')
            ->where('user_id', '!=', $userId)
            ->orderBy('nama')->get();

        return view('pages.siswa.aspirasi.create', compact(
            'kategoriList', 'ruanganList', 'siswaSaksiList',
            'sisaLimit', 'sudahSetKode', 'role'
        ));
    }

    // ─── STORE ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $userId = auth()->id();

        if ($this->hitungTerkirimHariIni($userId) >= self::LIMIT_HARIAN) {
            return response()->json([
                'success' => false,
                'message' => 'Batas maksimal 3 aspirasi per hari sudah tercapai.',
            ], 429);
        }

        $request->validate([
            'id_kategori'     => 'required|exists:tbl_kategori,id',
            'ruangan_id'      => 'nullable|exists:tbl_ruangan,id',
            'lokasi_manual'   => 'nullable|string|max:150',
            'saksi_id'        => 'nullable|exists:tbl_siswa,id',
            'kode_verifikasi' => 'nullable|string|max:20',
            'keterangan'      => 'required|string|max:500',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            'user_id'         => $userId,
            'id_kategori'     => $request->id_kategori,
            'ruangan_id'      => $request->ruangan_id ?: null,
            'lokasi_manual'   => $request->ruangan_id ? null : $request->lokasi_manual,
            'saksi_id'        => $request->saksi_id ?: null,
            'kode_verifikasi' => $request->kode_verifikasi,
            'status_alur'     => InputAspirasi::ALUR_DISETUJUI,
            'keterangan'      => $request->keterangan,
            'foto'            => $fotoPath,
        ]);

        $aspirasi = Aspirasi::create([
            'id_pelaporan' => $input->id,
            'status'       => 'Menunggu',
        ]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => null,
            'status_baru' => 'Menunggu',
            'status'      => 'Menunggu',
            'keterangan'  => 'Aspirasi berhasil dikirim ke Petugas Sarana.',
            'diubah_oleh' => $userId,
        ]);

        // Kirim notif ke semua petugas sarana
        $this->notifPetugas($input);

        $sisa = self::LIMIT_HARIAN - $this->hitungTerkirimHariIni($userId);

        return response()->json([
            'success'    => true,
            'message'    => 'Aspirasi berhasil dikirim ke Petugas Sarana!',
            'sisa_limit' => $sisa,
        ]);
    }

    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $userId          = auth()->id();
        $terkirimHariIni = $this->hitungTerkirimHariIni($userId);
        $sisaLimit       = max(0, self::LIMIT_HARIAN - $terkirimHariIni);
        $kategoriList    = Kategori::orderBy('nama_kategori')->get();
        return view('pages.siswa.aspirasi.index', compact('kategoriList', 'sisaLimit'));
    }

    // ─── DATA AJAX ────────────────────────────────────────────
    public function data(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');
        $userId  = auth()->id();

        $query = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi', 'saksi'])
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
                'saksi_nama'     => $item->saksi?->nama ?? '-',
                'status'         => $item->aspirasi?->status ?? '-',
                'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
                'created_at_fmt' => $item->created_at_format,
            ]),
            'total' => $total, 'current_page' => $page, 'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    // ─── SHOW — include foto_url progres ──────────────────────
    public function show($id)
    {
        $item = InputAspirasi::with([
            'kategori', 'ruangan', 'saksi',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',
        ])->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$item) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

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
            // FIX: include foto_url
            'progres' => $aspirasi?->progres->map(fn($p) => [
                'keterangan_progres' => $p->keterangan_progres,
                'foto_url'           => $p->foto_url,
                'nama_petugas'       => $p->user?->nama ?? '-',
                'created_at_fmt'     => $p->created_at_format,
            ]) ?? [],
        ]);
    }

    // ─── HISTORY ─────────────────────────────────────────────
    public function history()
    {
        $histori = HistoryStatus::with([
            'aspirasi.inputAspirasi.kategori',
            'aspirasi.inputAspirasi.ruangan',
        ])
        ->whereHas('aspirasi.inputAspirasi', fn($q) => $q->where('user_id', auth()->id()))
        ->latest()->paginate(15);

        return view('pages.siswa.aspirasi.history', compact('histori'));
    }

    // ─── GET RUANGAN ──────────────────────────────────────────
    public function getRuangan($id)
    {
        $r = Ruangan::find($id);
        if (!$r) return response()->json(['message' => 'Tidak ditemukan.'], 404);
        return response()->json([
            'id' => $r->id, 'nama_ruangan' => $r->nama_ruangan,
            'kode_ruangan' => $r->kode_ruangan, 'lantai' => $r->lantai, 'gedung' => $r->gedung,
        ]);
    }

    // ─── Helper: notif ke semua petugas sarana ────────────────
    private function notifPetugas(InputAspirasi $input): void
    {
        $siswa    = Siswa::where('user_id', $input->user_id)->first();
        $kategori = $input->kategori?->nama_kategori ?? 'aspirasi';
        $lokasi   = $input->lokasi_display ?? '-';
        $nama     = $siswa?->nama ?? 'Siswa';

        $petugasUsers = User::where('role', 'petugas_sarana')->get();
        foreach ($petugasUsers as $petugas) {
            Notifikasi::kirim(
                $petugas->id,
                '📋 Laporan Baru dari Siswa',
                "{$nama} melaporkan masalah di {$lokasi} ({$kategori}). Segera tindaklanjuti.",
                'warning',
                url('/petugas/aspirasi'),
                'solar:document-add-bold-duotone'
            );
        }
    }

    private function hitungTerkirimHariIni(int $userId): int
    {
        return InputAspirasi::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())->count();
    }
}