@extends('layouts.app')
@section('title', 'Dashboard | Siswa')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Greeting --}}
        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
            <iconify-icon icon="solar:user-speak-bold-duotone" class="fs-22 me-2"></iconify-icon>
            <div>
                Selamat datang, <strong>{{ $siswa->nama ?? auth()->user()->email }}</strong>!
                Kamu bisa menyampaikan aspirasi melalui menu di samping.
            </div>
        </div>

        {{-- Stat Cards --}}
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
                                <p class="text-muted mb-1 small">Diproses</p>
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

        {{-- Shortcut Buttons --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <a href="{{ route('siswa.aspirasi.create') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-primary rounded">
                            <iconify-icon icon="solar:pen-new-square-bold-duotone" class="avatar-title fs-24 text-primary"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Input Aspirasi</h6>
                            <p class="text-muted mb-0 small">Sampaikan masukan atau laporan baru</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('siswa.aspirasi.index') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-info rounded">
                            <iconify-icon icon="solar:list-bold-duotone" class="avatar-title fs-24 text-info"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Daftar Aspirasi</h6>
                            <p class="text-muted mb-0 small">Lihat semua aspirasi yang sudah dikirim</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('siswa.aspirasi.history') }}" class="card text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar-md bg-soft-success rounded">
                            <iconify-icon icon="solar:history-bold-duotone" class="avatar-title fs-24 text-success"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-1 text-dark">Histori Status</h6>
                            <p class="text-muted mb-0 small">Pantau perkembangan aspirasimu</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Tabel Aspirasi Terbaru --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1 text-primary"></iconify-icon>
                    Aspirasi Terbaru
                </h5>
                <a href="{{ route('siswa.aspirasi.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($terbaru as $item)
                                @php
                                    $statusMap = [
                                        'Menunggu' => 'bg-soft-warning text-warning',
                                        'Proses'   => 'bg-soft-info text-info',
                                        'Selesai'  => 'bg-soft-success text-success',
                                    ];
                                    $ket = strlen($item->keterangan) > 60
                                        ? substr($item->keterangan, 0, 60) . '...'
                                        : $item->keterangan;
                                @endphp
                                <tr>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $item->nama_kategori }}</span></td>
                                    <td class="small">{{ $item->lokasi }}</td>
                                    <td class="small text-muted">{{ $ket }}</td>
                                    <td><span class="badge {{ $statusMap[$item->status] ?? 'bg-secondary' }}">{{ $item->status }}</span></td>
                                    <td class="small text-muted">
                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada aspirasi. <a href="{{ route('siswa.aspirasi.create') }}">Kirim sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection