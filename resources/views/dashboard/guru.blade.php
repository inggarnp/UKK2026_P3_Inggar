@extends('layouts.app')
@section('title', 'Dashboard | Guru')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Greeting --}}
        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
            <iconify-icon icon="solar:user-speak-bold-duotone" class="fs-22 me-2"></iconify-icon>
            <div>
                Selamat datang, <strong>{{ $guru->nama ?? auth()->user()->email }}</strong>!
                @if($guru->kelasWali->isNotEmpty())
                    Kamu adalah wali kelas
                    @foreach($guru->kelasWali as $k)
                        <span class="badge bg-soft-primary text-primary ms-1">{{ $k->nama_kelas }}</span>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            {{-- Menunggu Review (dari siswa) --}}
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Menunggu Review</p>
                                <h3 class="text-warning mb-0">{{ $menungguReview }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-warning rounded">
                                <iconify-icon icon="solar:clock-circle-bold-duotone" class="avatar-title fs-24 text-warning"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('guru.review.index') }}" class="text-reset fw-semibold fs-12">Lihat Sekarang →</a>
                    </div>
                </div>
            </div>

            {{-- Total Aspirasi Sendiri --}}
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
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

            {{-- Disetujui --}}
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Sudah Disetujui</p>
                                <h3 class="text-success mb-0">{{ $sudahDisetujui }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('guru.review.index') }}" class="text-reset fw-semibold fs-12">Lihat Review →</a>
                    </div>
                </div>
            </div>

            {{-- Ditolak --}}
            <div class="col-6 col-md-3">
                <div class="card overflow-hidden h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small">Ditolak</p>
                                <h3 class="text-danger mb-0">{{ $sudahDitolak }}</h3>
                            </div>
                            <div class="avatar-md bg-soft-danger rounded">
                                <iconify-icon icon="solar:close-circle-bold-duotone" class="avatar-title fs-24 text-danger"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <a href="{{ route('guru.review.index') }}" class="text-reset fw-semibold fs-12">Lihat Review →</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shortcut --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <a href="{{ route('guru.review.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-warning rounded">
                            <iconify-icon icon="solar:clipboard-check-bold-duotone" class="avatar-title fs-24 text-warning"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Review Aspirasi Siswa</h6>
                            <p class="text-muted mb-0 small">Setujui atau tolak aspirasi dari siswa kelasmu</p>
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
                            <p class="text-muted mb-0 small">Kirim aspirasi langsung ke Petugas Sarana</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('guru.aspirasi.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-info rounded">
                            <iconify-icon icon="solar:list-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Daftar Aspirasi Saya</h6>
                            <p class="text-muted mb-0 small">Pantau status aspirasi yang sudah dikirim</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Tabel aspirasi siswa menunggu review --}}
        @if($guru->kelasWali->isNotEmpty() && $menungguReview > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1 text-warning"></iconify-icon>
                    Menunggu Review
                    <span class="badge bg-warning ms-1">{{ $menungguReview }}</span>
                </h5>
                <a href="{{ route('guru.review.index') }}" class="btn btn-sm btn-outline-warning">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Siswa</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aspirasiMenunggu as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->user?->siswa?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->user?->siswa?->kelas?->nama_kelas ?? '-' }}</small>
                                    </td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $item->kategori?->nama_kategori ?? '-' }}</span></td>
                                    <td class="small">{{ $item->lokasi_display }}</td>
                                    <td class="small text-muted">{{ $item->created_at_format }}</td>
                                    <td class="text-center" style="white-space:nowrap">
                                        <a href="{{ route('guru.review.index') }}" class="btn btn-sm btn-soft-success">
                                            <i class="bx bx-check"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection