<?php
namespace App\Http\Controllers;

use App\Models\InputAspirasi;
use App\Models\Aspirasi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\PetugasSarana;
use App\Models\Kelas;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Stat pengguna ──────────────────────────────────────
        $totalSiswa   = Siswa::count();
        $totalGuru    = Guru::count();
        $totalPetugas = PetugasSarana::count();
        $totalKelas   = Kelas::count();

        // ── Stat aspirasi ──────────────────────────────────────
        $totalAspirasi = InputAspirasi::count();

        $countByStatus = Aspirasi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $aspirasiMenunggu = $countByStatus['Menunggu'] ?? 0;
        $aspirasiProses   = $countByStatus['Proses']   ?? 0;
        $aspirasiSelesai  = $countByStatus['Selesai']  ?? 0;

        // ── 5 aspirasi terbaru ─────────────────────────────────
        $terbaru = InputAspirasi::with([
            'kategori',
            'ruangan',
            'user.siswa',
            'user.guru',
            'aspirasi',
        ])->latest()->limit(5)->get();

        return view('dashboard.admin', compact(
            'totalSiswa',
            'totalGuru',
            'totalPetugas',
            'totalKelas',
            'totalAspirasi',
            'aspirasiMenunggu',
            'aspirasiProses',
            'aspirasiSelesai',
            'terbaru'
        ));
    }
}