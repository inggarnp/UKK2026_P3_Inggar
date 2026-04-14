<?php

namespace App\Http\Controllers;

use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\Feedback;
use App\Models\HistoryStatus;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AspirationController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $siswaList    = Siswa::orderBy('nama')->get(['user_id', 'nama', 'nis']);
        $guruList     = Guru::orderBy('nama')->get(['user_id', 'nama', 'nip']);
        $ruanganList  = Ruangan::orderBy('nama_ruangan')->get();
        return view('pages.input_aspirasi.index', compact('kategoriList', 'siswaList', 'guruList', 'ruanganList'));
    }

    // ─── STORE ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:tbl_users,id',
            'id_kategori'   => 'required|exists:tbl_kategori,id',
            'ruangan_id'    => 'nullable|exists:tbl_ruangan,id',
            'lokasi_manual' => 'nullable|string|max:150',
            'keterangan'    => 'required|string|max:500',
            'status'        => 'required|in:Menunggu,Proses,Selesai',
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
            $fotoPath = $request->file('foto')->store('aspirasi', 'public');
        }

        $input = InputAspirasi::create([
            'user_id'       => $request->user_id,
            'id_kategori'   => $request->id_kategori,
            'ruangan_id'    => $request->ruangan_id ?: null,
            'lokasi_manual' => $request->ruangan_id ? null : $request->lokasi_manual,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
        ]);

        $aspirasi = Aspirasi::create([
            'id_pelaporan' => $input->id,
            'status'       => $request->status,
        ]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => null,
            'status_baru' => $request->status,
            'status'      => $request->status,
            'keterangan'  => 'Aspirasi berhasil dikirim.',
            'diubah_oleh' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Aspirasi berhasil ditambahkan.']);
    }

    // ─── DATA AJAX ────────────────────────────────────────────
    public function data(Request $request)
    {
        $page     = (int) $request->get('page', 1);
        $perPage  = (int) $request->get('per_page', 10);
        $search   = $request->get('search', '');
        $kategori = $request->get('kategori', '');
        $status   = $request->get('status', '');
        $role     = $request->get('role', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $query = InputAspirasi::with(['kategori', 'ruangan', 'aspirasi', 'user.siswa', 'user.guru'])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('user.siswa', fn($s) => $s->where('nama', 'like', "%$search%"))
                    ->orWhereHas('user.guru', fn($g) => $g->where('nama', 'like', "%$search%"))
                    ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%$search%"))
                    ->orWhere('lokasi_manual', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%")
                    ->orWhereHas('kategori', fn($k) => $k->where('nama_kategori', 'like', "%$search%"));
            }))
            ->when($kategori, fn($q) => $q->where('id_kategori', $kategori))
            ->when($status, fn($q) => $q->whereHas('aspirasi', fn($a) => $a->where('status', $status)))
            ->when($role, fn($q) => $q->whereHas('user', fn($u) => $u->where('role', $role)))
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo));

        $total = $query->count();
        $items = $query->latest()->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $data = $items->map(function ($item) {
            $aspirasi     = $item->aspirasi;
            $user         = $item->user;
            $profil       = $user->role === 'siswa' ? $user->siswa : $user->guru;
            $namaPerlapor = $profil?->nama ?? $user->email;
            $identitas    = ($user->role === 'siswa' ? $profil?->nis : $profil?->nip) ?? '-';

            return [
                'id'             => $item->id,
                'aspirasi_id'    => $aspirasi?->id,
                'nama_pelapor'   => $namaPerlapor,
                'identitas'      => $identitas,
                'role'           => $user->role,
                'nama_kategori'  => $item->kategori?->nama_kategori ?? '-',
                'lokasi_display' => $item->lokasi_display,
                'keterangan'     => $item->keterangan,
                'foto_url'       => $item->foto_url,
                'status'         => $aspirasi?->status ?? '-',
                'status_badge'   => $aspirasi?->status_badge ?? 'bg-secondary',
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
            'user.siswa',
            'user.guru',
            'aspirasi.feedback.user',
            'aspirasi.historyStatus',
            'aspirasi.progres.user',
        ])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $aspirasi  = $item->aspirasi;
        $user      = $item->user;
        $profil    = $user->role === 'siswa' ? $user->siswa : $user->guru;

        return response()->json([
            'id'              => $item->id,
            'aspirasi_id'     => $aspirasi?->id,
            'nama_pelapor'    => $profil?->nama ?? $user->email,
            'identitas'       => ($user->role === 'siswa' ? $profil?->nis : $profil?->nip) ?? '-',
            'role'            => $user->role,
            'email'           => $user->email,
            'nama_kategori'   => $item->kategori?->nama_kategori ?? '-',
            'lokasi_display'  => $item->lokasi_display,
            'kode_ruangan'    => $item->ruangan?->kode_ruangan,
            'lantai'          => $item->ruangan?->lantai,
            'gedung'          => $item->ruangan?->gedung,
            'keterangan'      => $item->keterangan,
            'foto_url'        => $item->foto_url,
            'status'          => $aspirasi?->status ?? '-',
            'status_badge'    => $aspirasi?->status_badge ?? 'bg-secondary',
            'created_at_fmt'  => $item->created_at_format,

            'feedback' => $aspirasi?->feedback->map(fn($f) => [
                'isi_feedback'   => $f->isi_feedback,
                'nama_pemberi'   => $f->user?->nama ?? '-',
                'created_at_fmt' => $f->created_at_format,
            ]) ?? [],

            'histori' => $aspirasi?->historyStatus->map(fn($h) => [
                'status'         => $h->status,
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

    // ─── UPDATE STATUS ────────────────────────────────────────
    public function updateStatus(Request $request, $aspirasi_id)
    {
        $request->validate([
            'status'       => 'required|in:Menunggu,Proses,Selesai',
            'isi_feedback' => 'nullable|string|max:500',
        ]);

        $aspirasi = Aspirasi::find($aspirasi_id);
        if (!$aspirasi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $statusLama = $aspirasi->status;

        $aspirasi->update(['status' => $request->status]);

        HistoryStatus::create([
            'id_aspirasi' => $aspirasi->id,
            'status_lama' => $statusLama,
            'status_baru' => $request->status,
            'status'      => $request->status,
            'keterangan'  => $request->isi_feedback ?? 'Status diperbarui.',
            'diubah_oleh' => auth()->id(),
        ]);

        // Simpan feedback jika diisi
        if ($request->isi_feedback) {
            Feedback::create([
                'id_aspirasi'  => $aspirasi->id,
                'user_id'      => auth()->id(),
                'isi_feedback' => $request->isi_feedback,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    // ─── DESTROY ──────────────────────────────────────────────
    public function destroy($id)
    {
        $item = InputAspirasi::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        if ($item->foto && Storage::disk('public')->exists($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }

        $aspirasi = $item->aspirasi;
        if ($aspirasi) {
            $aspirasi->feedback()->delete();
            $aspirasi->historyStatus()->delete();
            $aspirasi->progres()->delete();
            $aspirasi->delete();
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Aspirasi berhasil dihapus.']);
    }
}