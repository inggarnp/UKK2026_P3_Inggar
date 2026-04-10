<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;

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

    Route::prefix('siswa')->name('siswa-data.')->group(function () {
        Route::get('/',                 [SiswaController::class, 'index'])->name('index');
        Route::get('/data',             [SiswaController::class, 'data'])->name('data');       // AJAX DataTable
        Route::get('/{id}',             [SiswaController::class, 'show'])->name('show');       // AJAX detail
        Route::post('/',                [SiswaController::class, 'store'])->name('store');
        Route::put('/{id}',             [SiswaController::class, 'update'])->name('update');
        Route::delete('/{id}',          [SiswaController::class, 'destroy'])->name('destroy');
        Route::post('/import',          [SiswaController::class, 'import'])->name('import');
        Route::get('/import/template',  [SiswaController::class, 'importTemplate'])->name('import.template');
    });
});

// ─── GURU ─────────────────────────────────────────────────
Route::middleware(['auth', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('dashboard');
});

// ─── SISWA ────────────────────────────────────────────────
Route::middleware(['auth', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', function () {
        return view('siswa.dashboard');
    })->name('dashboard');
});
