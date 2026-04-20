<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\PetugasSaranaController;
use App\Http\Controllers\SiswaAspirasiController;
use App\Http\Controllers\GuruAspirasiController;
use App\Http\Controllers\PetugasAspirasiController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;

// ─── ROOT ──────────────────────────────────────────────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');

    $role = auth()->user()->role;

    // FIX: handle guru redirect di sini langsung, tanpa $this->redirectGuru()
    if ($role === 'guru') {
        $guru = \App\Models\Guru::where('user_id', auth()->id())->first();
        if ($guru && $guru->jabatan === 'kepala_sekolah') {
            return redirect()->route('kepala_sekolah.dashboard');
        }
        return redirect()->route('guru.dashboard');
    }

    return match ($role) {
        'admin'          => redirect()->route('admin.dashboard'),
        'siswa'          => redirect()->route('siswa.dashboard'),
        'petugas_sarana' => redirect()->route('petugas.dashboard'),
        default          => redirect()->route('login'),
    };
});

// ─── AUTH ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── NOTIFIKASI (semua role) ───────────────────────────────────
Route::middleware('auth')->prefix('notif')->name('notif.')->group(function () {
    Route::get('/',            [NotifikasiController::class, 'index'])->name('index');
    Route::get('/polling',     [NotifikasiController::class, 'polling'])->name('polling');
    Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('baca-semua');
    Route::post('/{id}/baca',  [NotifikasiController::class, 'baca'])->name('baca');
    Route::delete('/{id}',     [NotifikasiController::class, 'destroy'])->name('destroy');
});

// ─── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/',                [SiswaController::class, 'index'])->name('index');
        Route::get('/data',            [SiswaController::class, 'data'])->name('data');
        Route::get('/import/template', [SiswaController::class, 'importTemplate'])->name('import.template');
        Route::post('/import',         [SiswaController::class, 'import'])->name('import');
        Route::post('/',               [SiswaController::class, 'store'])->name('store');
        Route::get('/{id}',            [SiswaController::class, 'show'])->name('show');
        Route::put('/{id}',            [SiswaController::class, 'update'])->name('update');
        Route::delete('/{id}',         [SiswaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/',        [GuruController::class, 'index'])->name('index');
        Route::get('/data',    [GuruController::class, 'data'])->name('data');
        Route::post('/',       [GuruController::class, 'store'])->name('store');
        Route::get('/{id}',    [GuruController::class, 'show'])->name('show');
        Route::put('/{id}',    [GuruController::class, 'update'])->name('update');
        Route::delete('/{id}', [GuruController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/',        [PetugasSaranaController::class, 'index'])->name('index');
        Route::get('/data',    [PetugasSaranaController::class, 'data'])->name('data');
        Route::post('/',       [PetugasSaranaController::class, 'store'])->name('store');
        Route::get('/{id}',    [PetugasSaranaController::class, 'show'])->name('show');
        Route::put('/{id}',    [PetugasSaranaController::class, 'update'])->name('update');
        Route::delete('/{id}', [PetugasSaranaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/',          [KelasController::class, 'index'])->name('index');
        Route::get('/data',      [KelasController::class, 'data'])->name('data');
        Route::get('/jurusan',   [KelasController::class, 'getJurusan'])->name('jurusan');
        Route::get('/ruangan',   [KelasController::class, 'getRuangan'])->name('ruangan');
        Route::get('/available', [KelasController::class, 'getAvailableKelas'])->name('available');
        Route::post('/',         [KelasController::class, 'store'])->name('store');
        Route::get('/{id}',      [KelasController::class, 'show'])->name('show');
        Route::put('/{id}',      [KelasController::class, 'update'])->name('update');
        Route::delete('/{id}',   [KelasController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('jurusan')->name('jurusan.')->group(function () {
        Route::get('/',        [JurusanController::class, 'index'])->name('index');
        Route::get('/data',    [JurusanController::class, 'data'])->name('data');
        Route::post('/',       [JurusanController::class, 'store'])->name('store');
        Route::get('/{id}',    [JurusanController::class, 'show'])->name('show');
        Route::put('/{id}',    [JurusanController::class, 'update'])->name('update');
        Route::delete('/{id}', [JurusanController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::get('/',        [RuanganController::class, 'index'])->name('index');
        Route::get('/data',    [RuanganController::class, 'data'])->name('data');
        Route::post('/',       [RuanganController::class, 'store'])->name('store');
        Route::get('/{id}',    [RuanganController::class, 'show'])->name('show');
        Route::put('/{id}',    [RuanganController::class, 'update'])->name('update');
        Route::delete('/{id}', [RuanganController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/',        [KategoriController::class, 'index'])->name('index');
        Route::get('/data',    [KategoriController::class, 'data'])->name('data');
        Route::post('/',       [KategoriController::class, 'store'])->name('store');
        Route::get('/{id}',    [KategoriController::class, 'show'])->name('show');
        Route::put('/{id}',    [KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',                      [AspirationController::class, 'index'])->name('index');
        Route::get('/data',                  [AspirationController::class, 'data'])->name('data');
        Route::post('/',                     [AspirationController::class, 'store'])->name('store');
        Route::get('/{id}',                  [AspirationController::class, 'show'])->name('show');
        Route::post('/status/{aspirasi_id}', [AspirationController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}',               [AspirationController::class, 'destroy'])->name('destroy');
    });
});

// ─── SISWA ──────────────────────────────────────────────────────
Route::middleware(['auth', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::get('/dashboard', [SiswaAspirasiController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile',                  [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update',          [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/kode-verifikasi', [ProfileController::class, 'setKodeVerifikasi'])->name('profile.set-kode');
    Route::post('/profile/cek-kode',        [ProfileController::class, 'cekKodeVerifikasi'])->name('profile.cek-kode');

    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',             [SiswaAspirasiController::class, 'index'])->name('index');
        Route::get('/data',         [SiswaAspirasiController::class, 'data'])->name('data');
        Route::get('/create',       [SiswaAspirasiController::class, 'create'])->name('create');
        Route::post('/',            [SiswaAspirasiController::class, 'store'])->name('store');
        Route::get('/history',      [SiswaAspirasiController::class, 'history'])->name('history');
        Route::get('/ruangan/{id}', [SiswaAspirasiController::class, 'getRuangan'])->name('ruangan.show');
        Route::get('/{id}',         [SiswaAspirasiController::class, 'show'])->name('show');
    });
});

// ─── GURU ───────────────────────────────────────────────────────
Route::middleware(['auth', 'guru'])->prefix('guru')->name('guru.')->group(function () {

    Route::get('/dashboard', [GuruAspirasiController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile',                  [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update',          [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/kode-verifikasi', [ProfileController::class, 'setKodeVerifikasi'])->name('profile.set-kode');
    Route::post('/profile/cek-kode',        [ProfileController::class, 'cekKodeVerifikasi'])->name('profile.cek-kode');

    // Lihat aspirasi siswa (read only)
    Route::prefix('siswa-aspirasi')->name('siswa-aspirasi.')->group(function () {
        Route::get('/',     [GuruAspirasiController::class, 'siswaIndex'])->name('index');
        Route::get('/data', [GuruAspirasiController::class, 'siswaData'])->name('data');
        Route::get('/{id}', [GuruAspirasiController::class, 'siswaShow'])->name('show');
    });

    // Aspirasi guru sendiri
    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',        [GuruAspirasiController::class, 'index'])->name('index');
        Route::get('/data',    [GuruAspirasiController::class, 'data'])->name('data');
        Route::get('/create',  [GuruAspirasiController::class, 'create'])->name('create');
        Route::post('/',       [GuruAspirasiController::class, 'store'])->name('store');
        Route::get('/history', [GuruAspirasiController::class, 'history'])->name('history');
        Route::get('/{id}',    [GuruAspirasiController::class, 'show'])->name('show');
    });
});

// ─── KEPALA SEKOLAH (guru jabatan kepala_sekolah) ───────────────
Route::middleware(['auth', 'kepala_sekolah'])->prefix('kepala-sekolah')->name('kepala_sekolah.')->group(function () {

    Route::get('/dashboard', [KepalaSekolahController::class, 'dashboard'])->name('dashboard');

    // Profile (pakai controller yang sama)
    Route::get('/profile',         [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/kode-verifikasi', [ProfileController::class, 'setKodeVerifikasi'])->name('profile.set-kode');
    Route::post('/profile/cek-kode',        [ProfileController::class, 'cekKodeVerifikasi'])->name('profile.cek-kode');

    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',     [KepalaSekolahController::class, 'aspirasi'])->name('index');
        Route::get('/data', [KepalaSekolahController::class, 'aspirasData'])->name('data');
        Route::get('/{id}', [KepalaSekolahController::class, 'aspirasShow'])->name('show');
    });

    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/',     [KepalaSekolahController::class, 'history'])->name('index');
        Route::get('/data', [KepalaSekolahController::class, 'historyData'])->name('data');
    });
});

// ─── PETUGAS SARANA ─────────────────────────────────────────────
Route::middleware(['auth', 'petugas_sarana'])->prefix('petugas')->name('petugas.')->group(function () {

    Route::get('/dashboard', [PetugasAspirasiController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile',         [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',                       [PetugasAspirasiController::class, 'index'])->name('index');
        Route::get('/data',                   [PetugasAspirasiController::class, 'data'])->name('data');
        Route::get('/{id}',                   [PetugasAspirasiController::class, 'show'])->name('show');
        Route::post('/progres/{aspirasi_id}', [PetugasAspirasiController::class, 'tambahProgres'])->name('progres');
        Route::post('/status/{aspirasi_id}',  [PetugasAspirasiController::class, 'updateStatus'])->name('status');
    });
});