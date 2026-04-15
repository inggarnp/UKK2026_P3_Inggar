@extends('layouts.app')
@section('title', 'Dashboard | Petugas Sarana')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="alert alert-primary d-flex align-items-center mb-4">
            <iconify-icon icon="solar:user-speak-bold-duotone" class="fs-22 me-2"></iconify-icon>
            <div>
                Selamat datang, <strong>{{ $petugas->nama ?? auth()->user()->email }}</strong>!
                Kelola dan tindaklanjuti laporan aspirasi di bawah ini.
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Total Laporan</p>
                                <h3 class="text-dark mb-0">{{ $total }}</h3>
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
                                <h3 class="text-warning mb-0">{{ $menunggu }}</h3>
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
                                <h3 class="text-info mb-0">{{ $proses }}</h3>
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
                                <h3 class="text-success mb-0">{{ $selesai }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shortcut --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <a href="{{ route('petugas.aspirasi.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:list-check-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Daftar Laporan Aspirasi</h6>
                            <p class="text-muted mb-0 small">Lihat semua laporan yang perlu ditindaklanjuti</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-warning rounded">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="avatar-title fs-24 text-warning"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Perlu Ditindaklanjuti</h6>
                            <p class="text-muted mb-0 small"><strong class="text-warning">{{ $menunggu + $proses }}</strong> laporan sedang aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel laporan terbaru --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1 text-warning"></iconify-icon>
                    Laporan Terbaru
                </h5>
                <a href="{{ route('petugas.aspirasi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
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
                                        <div class="fw-semibold">{{ $profil?->nama ?? $user?->email ?? '-' }}</div>
                                        <small class="badge bg-soft-secondary text-secondary">{{ ucfirst($user?->role ?? '-') }}</small>
                                    </td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $item->kategori?->nama_kategori ?? '-' }}</span></td>
                                    <td class="small">{{ $item->lokasi_display }}</td>
                                    <td><span class="badge {{ $statusMap[$item->aspirasi?->status] ?? 'bg-secondary' }}">{{ $item->aspirasi?->status ?? '-' }}</span></td>
                                    <td class="small text-muted">{{ $item->created_at_format }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada laporan masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection