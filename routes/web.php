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

Route::get('/', function () {
    // Kalau sudah login, redirect ke dashboard sesuai role
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => redirect()->route('guru.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ─── AUTH (hanya bisa diakses kalau BELUM login) ──────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── ADMIN ────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard');

    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/',                 [SiswaController::class, 'index'])->name('index');
        Route::get('/data',             [SiswaController::class, 'data'])->name('data');       // AJAX DataTable
        Route::get('/{id}',             [SiswaController::class, 'show'])->name('show');       // AJAX detail
        Route::post('/',                [SiswaController::class, 'store'])->name('store');
        Route::put('/{id}',             [SiswaController::class, 'update'])->name('update');
        Route::delete('/{id}',          [SiswaController::class, 'destroy'])->name('destroy');
        Route::post('/import',          [SiswaController::class, 'import'])->name('import');
        Route::get('/import/template',  [SiswaController::class, 'importTemplate'])->name('import.template');
    });

    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/',                 [GuruController::class, 'index'])->name('index');
        Route::get('/data',             [GuruController::class, 'data'])->name('data');
        Route::get('/{id}',             [GuruController::class, 'show'])->name('show');
        Route::post('/',                [GuruController::class, 'store'])->name('store');
        Route::put('/{id}',             [GuruController::class, 'update'])->name('update');
        Route::delete('/{id}',          [GuruController::class, 'destroy'])->name('destroy');
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
        Route::get('/',        [KelasController::class, 'index'])->name('index');
        Route::get('/data',    [KelasController::class, 'data'])->name('data');
        Route::get('/jurusan', [KelasController::class, 'getJurusan'])->name('jurusan');
        Route::get('/ruangan', [KelasController::class, 'getRuangan'])->name('ruangan');
        Route::post('/',       [KelasController::class, 'store'])->name('store');
        Route::get('/{id}',    [KelasController::class, 'show'])->name('show');
        Route::put('/{id}',    [KelasController::class, 'update'])->name('update');
        Route::delete('/{id}', [KelasController::class, 'destroy'])->name('destroy');
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

    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',           [AspirationController::class, 'index'])->name('index');
        Route::get('/data',       [AspirationController::class, 'data'])->name('data');
        Route::post('/',          [AspirationController::class, 'store'])->name('store');          // ← BARU
        Route::get('/{id}',       [AspirationController::class, 'show'])->name('show');
        Route::put('/status/{aspirasi_id}', [AspirationController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}',    [AspirationController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/',        [KategoriController::class, 'index'])->name('index');
        Route::get('/data',    [KategoriController::class, 'data'])->name('data');
        Route::post('/',       [KategoriController::class, 'store'])->name('store');
        Route::get('/{id}',    [KategoriController::class, 'show'])->name('show');
        Route::put('/{id}',    [KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });
});

// ─── GURU ─────────────────────────────────────────────────
Route::middleware(['auth', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('dashboard');
});

// ─────────────────────────────────────────────────────────────────
// GANTI blok Route siswa yang sudah ada di web.php dengan ini
// Tambahkan juga di bagian use:
// use App\Http\Controllers\Siswa\SiswaAspirasController;
// ─────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [SiswaAspirasiController::class, 'dashboard'])->name('dashboard');

    // Aspirasi
    Route::prefix('aspirasi')->name('aspirasi.')->group(function () {
        Route::get('/',        [SiswaAspirasiController::class, 'index'])->name('index');
        Route::get('/data',    [SiswaAspirasiController::class, 'data'])->name('data');       // AJAX
        Route::get('/create',  [SiswaAspirasiController::class, 'create'])->name('create');
        Route::post('/',       [SiswaAspirasiController::class, 'store'])->name('store');
        Route::get('/history', [SiswaAspirasiController::class, 'history'])->name('history');
        Route::get('/{id}',    [SiswaAspirasiController::class, 'show'])->name('show');       // AJAX detail
    });
});
