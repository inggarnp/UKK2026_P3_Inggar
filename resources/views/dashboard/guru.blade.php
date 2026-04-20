@extends('layouts.app')
@section('title', 'Dashboard | Guru')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
            <iconify-icon icon="solar:user-speak-bold-duotone" class="fs-22 me-2"></iconify-icon>
            <div>
                Selamat datang, <strong>{{ $guru->nama ?? auth()->user()->email }}</strong>!
                @if($guru->kelasWali->isNotEmpty())
                    Wali kelas:
                    @foreach($guru->kelasWali as $k)
                        <span class="badge bg-soft-primary text-primary ms-1">{{ $k->nama_kelas }}</span>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Aspirasi Siswa</p>
                                <h3 class="text-info mb-0">{{ $totalSiswaAspirasi }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-info rounded">
                                <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('guru.siswa-aspirasi.index') }}" class="text-reset fw-semibold fs-12">Lihat Sekarang →</a>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Aspirasi Saya</p>
                                <h3 class="text-primary mb-0">{{ $totalAspirasiSendiri }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <iconify-icon icon="solar:chat-square-like-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('guru.aspirasi.index') }}" class="text-reset fw-semibold fs-12">Lihat Semua →</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <a href="{{ route('guru.siswa-aspirasi.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-info rounded">
                            <iconify-icon icon="solar:eye-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Lihat Aspirasi Siswa</h6>
                            <p class="text-muted mb-0 small">Pantau aspirasi siswa di kelasmu</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('guru.aspirasi.create') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:pen-new-square-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Input Aspirasi</h6>
                            <p class="text-muted mb-0 small">Kirim aspirasi ke Petugas Sarana</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('guru.aspirasi.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-success rounded">
                            <iconify-icon icon="solar:list-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Daftar Aspirasi Saya</h6>
                            <p class="text-muted mb-0 small">Pantau status aspirasi yang sudah dikirim</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Aspirasi terbaru dari siswa --}}
        @if($aspirasiTerbaruSiswa->isNotEmpty())
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:eye-bold-duotone" class="me-1 text-info"></iconify-icon>
                    Aspirasi Terbaru Siswa
                </h5>
                <a href="{{ route('guru.siswa-aspirasi.index') }}" class="btn btn-sm btn-outline-info">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Siswa</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aspirasiTerbaruSiswa as $item)
                                @php
                                    $statusMap = ['Menunggu'=>'bg-soft-warning text-warning','Proses'=>'bg-soft-info text-info','Selesai'=>'bg-soft-success text-success'];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->user?->siswa?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->user?->siswa?->kelas?->nama_kelas ?? '-' }}</small>
                                    </td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $item->kategori?->nama_kategori ?? '-' }}</span></td>
                                    <td class="small">{{ $item->lokasi_display }}</td>
                                    <td><span class="badge {{ $statusMap[$item->aspirasi?->status] ?? 'bg-secondary' }}">{{ $item->aspirasi?->status ?? '-' }}</span></td>
                                    <td class="small text-muted">{{ $item->created_at_format }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection