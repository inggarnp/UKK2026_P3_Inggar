@extends('layouts.app')
@section('title', 'Dashboard | Admin')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
            <iconify-icon icon="solar:shield-user-bold-duotone" class="fs-22 me-2"></iconify-icon>
            <div>Selamat datang di <strong>Dashboard Admin</strong>! Kelola seluruh data dan pantau aspirasi.</div>
        </div>

        {{-- ── Stat: Data Pengguna ── --}}
        <p class="text-muted fw-semibold mb-2 small text-uppercase">
            <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="me-1"></iconify-icon>
            Data Pengguna
        </p>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Siswa</p>
                                <h3 class="text-dark mb-0">{{ $totalSiswa }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <iconify-icon icon="solar:book-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('admin.siswa.index') }}" class="text-reset fw-semibold fs-12">Kelola Siswa →</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Guru</p>
                                <h3 class="text-dark mb-0">{{ $totalGuru }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-info rounded">
                                <iconify-icon icon="solar:user-id-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('admin.guru.index') }}" class="text-reset fw-semibold fs-12">Kelola Guru →</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Petugas</p>
                                <h3 class="text-dark mb-0">{{ $totalPetugas }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-warning rounded">
                                <iconify-icon icon="solar:settings-bold-duotone" class="avatar-title fs-24 text-warning"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('admin.petugas.index') }}" class="text-reset fw-semibold fs-12">Kelola Petugas →</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Kelas</p>
                                <h3 class="text-dark mb-0">{{ $totalKelas }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <iconify-icon icon="solar:buildings-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('admin.kelas.index') }}" class="text-reset fw-semibold fs-12">Kelola Kelas →</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Stat: Aspirasi ── --}}
        <p class="text-muted fw-semibold mb-2 small text-uppercase">
            <iconify-icon icon="solar:chart-bold-duotone" class="me-1"></iconify-icon>
            Rekap Aspirasi
        </p>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Aspirasi</p>
                                <h3 class="text-dark mb-0">{{ $totalAspirasi }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <iconify-icon icon="solar:chat-square-like-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Menunggu</p>
                                <h3 class="text-warning mb-0">{{ $aspirasiMenunggu }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-warning rounded">
                                <iconify-icon icon="solar:clock-circle-bold-duotone" class="avatar-title fs-24 text-warning"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Sedang Diproses</p>
                                <h3 class="text-info mb-0">{{ $aspirasiProses }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-info rounded">
                                <iconify-icon icon="solar:refresh-circle-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Selesai</p>
                                <h3 class="text-success mb-0">{{ $aspirasiSelesai }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tabel Aspirasi Terbaru ── --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1 text-primary"></iconify-icon>
                    Aspirasi Terbaru
                </h5>
                <a href="{{ route('admin.aspirasi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terbaru as $item)
                                @php
                                    $statusMap = ['Menunggu'=>'bg-soft-warning text-warning','Proses'=>'bg-soft-info text-info','Selesai'=>'bg-soft-success text-success'];
                                    $user   = $item->user;
                                    $profil = $user?->role === 'siswa' ? $user?->siswa : $user?->guru;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold small">{{ $profil?->nama ?? $user?->email ?? '-' }}</div>
                                        <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($user?->role ?? '-') }}</span>
                                    </td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $item->kategori?->nama_kategori ?? '-' }}</span></td>
                                    <td class="small">{{ $item->lokasi_display }}</td>
                                    <td><span class="badge {{ $statusMap[$item->aspirasi?->status] ?? 'bg-secondary' }}">{{ $item->aspirasi?->status ?? '-' }}</span></td>
                                    <td class="small text-muted">{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aspirasi masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection