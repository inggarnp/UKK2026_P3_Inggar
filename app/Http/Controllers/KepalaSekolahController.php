<?php

namespace App\Http\Controllers;

use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kategori;
use App\Models\HistoryStatus;
use Illuminate\Http\Request;

class KepalaSekolahController extends Controller
{
    private function getGuru(): Guru
    {
        return Guru::where('user_id', auth()->id())->firstOrFail();
    }

    // ─── DASHBOARD ────────────────────────────────────────────
    public function dashboard()
    {
        $guru = $this->getGuru();

        $totalAspirasi = InputAspirasi::count();

        $countByStatus = Aspirasi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $menunggu = $countByStatus['Menunggu'] ?? 0;
        $proses   = $countByStatus['Proses']   ?? 0;
        $selesai  = $countByStatus['Selesai']  ?? 0;

        $totalSiswa    = Siswa::count();
        $totalGuru     = Guru::count();
        $aspirasiSiswa = InputAspirasi::whereHas('user', fn($q) => $q->where('role', 'siswa'))->count();
        $aspirasiGuru  = InputAspirasi::whereHas('user', fn($q) => $q->where('role', 'guru'))->count();

        $terbaru = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa', 'user.guru', 'aspirasi'])
            ->latest()->limit(5)->get();

        return view('dashboard.kepala_sekolah', compact(
            'guru',
            'totalAspirasi',
            'menunggu',
            'proses',
            'selesai',
            'totalSiswa',
            'totalGuru',
            'aspirasiSiswa',
            'aspirasiGuru',
            'terbaru'
        ));
    }

    public function aspirasi()
    {
        $guru         = $this->getGuru();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        // Tambahkan variabel yang dibutuhkan view
        $totalAspirasi = InputAspirasi::count();

        $countByStatus = Aspirasi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $menunggu = $countByStatus['Menunggu'] ?? 0;
        $proses   = $countByStatus['Proses']   ?? 0;
        $selesai  = $countByStatus['Selesai']  ?? 0;

        $totalSiswa    = Siswa::count();
        $totalGuru     = Guru::count();
        $aspirasiSiswa = InputAspirasi::whereHas('user', fn($q) => $q->where('role', 'siswa'))->count();
        $aspirasiGuru  = InputAspirasi::whereHas('user', fn($q) => $q->where('role', 'guru'))->count();

        $terbaru = InputAspirasi::with(['kategori', 'ruangan', 'user.siswa', 'user.guru', 'aspirasi'])
            ->latest()->limit(5)->get();

        return view('pages.kepala_sekolah.aspirasi.index', compact(
            'guru',
            'kategoriList',
            'totalAspirasi',
            'menunggu',
            'proses',
            'selesai',
            'totalSiswa',
            'totalGuru',
            'aspirasiSiswa',
            'aspirasiGuru',
            'terbaru'
        ));
    }

    // ─── DATA AJAX ────────────────────────────────────────────
    public function aspirasData(Request $request)
    {
        $page     = (int) $request->get('page', 1);
        $perPage  = (int) $request->get('per_page', 10);
        $search   = $request->get('search', '');
        $status   = $request->get('status', '');
        $kategori = $request->get('kategori', '');
        $role     = $request->get('role', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $query = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi', 'user.siswa', 'user.guru'])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
                    ->orWhereHas('user.guru',  fn($g) => $g->where('nama', 'like', "%$search%"))
                    ->orWhereHas('ruangan',    fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
                    ->orWhere('lokasi_manual', 'like', "%$search%")
                    ->orWhere('keterangan',    'like', "%$search%")
                    ->orWhereHas('kategori',   fn($k) => $k->where('nama_kategori', 'like', "%$search%"));
            }))
            ->when($kategori, fn($q) => $q->where('id_kategori', $kategori))
            ->when($status,   fn($q) => $q->whereHas('aspirasi', fn($a) => $a->where('status', $status)))
            ->when($role,     fn($q) => $q->whereHas('user', fn($u) => $u->where('role', $role)))
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(function ($item) {
            $user   = $item->user;
            $profil = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;
            return [
                'id'             => $item->id,
                'nama_pelapor'   => $profil?->nama ?? $user?->email ?? '-',
                'identitas'      => ($user?->role === 'siswa' ? $profil?->nis : $profil?->nip) ?? '-',
                'role'           => ucfirst($user?->role ?? '-'),
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,   // FIX: gunakan accessor
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'status'         => $item->aspirasi?->status ?? '-',
                'status_badge'   => $item->aspirasi?->status_badge ?? 'bg-secondary',
                'created_at_fmt' => $item->created_at_format,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    // ─── SHOW DETAIL ──────────────────────────────────────────
    public function aspirasShow($id)
    {
        $item = InputAspirasi::with([
            'kategori',
            'ruangan',
            'user.siswa',
            'user.guru',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',  // FIX: include progres
        ])->find($id);

        if (!$item) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

        $aspirasi = $item->aspirasi;
        $user     = $item->user;
        $profil   = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;

        return response()->json([
            'id'             => $item->id,
            'nama_pelapor'   => $profil?->nama ?? $user?->email ?? '-',
            'identitas'      => ($user?->role === 'siswa' ? $profil?->nis : $profil?->nip) ?? '-',
            'role'           => ucfirst($user?->role ?? '-'),
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
            // FIX: sertakan foto_url progres
            'progres' => $aspirasi?->progres->map(fn($p) => [
                'keterangan_progres' => $p->keterangan_progres,
                'foto_url'           => $p->foto_url,
                'nama_petugas'       => $p->user?->nama ?? $p->user?->email ?? '-',
                'created_at_fmt'     => $p->created_at_format,
            ]) ?? [],
        ]);
    }

    // ─── HISTORY STATUS ───────────────────────────────────────
    public function history(Request $request)
    {
        $guru         = $this->getGuru(); // FIX
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('pages.kepala_sekolah.aspirasi.history', compact('guru', 'kategoriList'));
    }

    public function historyData(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', '');

        $query = HistoryStatus::with([
            'aspirasi.inputAspirasi.kategori',
            'aspirasi.inputAspirasi.ruangan',
            'aspirasi.inputAspirasi.user.siswa',
            'aspirasi.inputAspirasi.user.guru',
        ])
            ->when($search, fn($q) => $q->whereHas('aspirasi.inputAspirasi', function ($q2) use ($search) {
                $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
                    ->orWhereHas('user.guru', fn($g) => $g->where('nama', 'like', "%$search%"))
                    ->orWhereHas('kategori', fn($k) => $k->where('nama_kategori', 'like', "%$search%"));
            }))
            ->when($status, fn($q) => $q->where('status_baru', $status));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(function ($h) {
            $input  = $h->aspirasi?->inputAspirasi;
            $user   = $input?->user;
            $profil = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;
            return [
                'id'             => $h->id,
                'nama_pelapor'   => $profil?->nama ?? $user?->email ?? '-',
                'role'           => ucfirst($user?->role ?? '-'),
                'nama_kategori'  => $input?->kategori?->nama_kategori ?? '-',
                'status_lama'    => $h->status_lama ?? '-',
                'status_baru'    => $h->status_baru ?? $h->status ?? '-',
                'status_badge'   => $h->status_badge ?? 'bg-secondary',
                'keterangan'     => $h->keterangan ?? '-',
                'created_at_fmt' => $h->created_at_format ?? '-',
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }
}
