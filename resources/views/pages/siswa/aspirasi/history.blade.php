@extends('layouts.app')
@section('title', 'Histori Aspirasi | Siswa')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1">
                    <iconify-icon icon="solar:history-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Histori Aspirasi
                </h4>
                <p class="text-muted mb-0">Perkembangan status semua aspirasi yang kamu kirimkan</p>
            </div>
            <a href="{{ route('siswa.aspirasi.create') }}" class="btn btn-primary btn-sm">
                <iconify-icon icon="solar:pen-new-square-bold-duotone" class="me-1"></iconify-icon>
                Input Aspirasi
            </a>
        </div>

        @if ($histori->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <iconify-icon icon="solar:history-bold-duotone" class="fs-48 text-muted mb-3 d-block"></iconify-icon>
                    <h5 class="text-muted">Belum ada histori</h5>
                    <p class="text-muted small mb-3">Kamu belum pernah mengirim aspirasi.</p>
                    <a href="{{ route('siswa.aspirasi.create') }}" class="btn btn-primary btn-sm">
                        Kirim Aspirasi Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    {{-- Timeline histori --}}
                    <div class="timeline-container" style="position:relative; padding-left: 2rem;">
                        {{-- Garis vertikal --}}
                        <div style="position:absolute; left:0.6rem; top:0; bottom:0; width:2px; background:#e9ecef;"></div>

                        @foreach ($histori as $h)
                            @php
                                $statusColor = match($h->status) {
                                    'Menunggu' => ['dot' => '#ffc107', 'badge' => 'bg-soft-warning text-warning'],
                                    'Proses'   => ['dot' => '#0dcaf0', 'badge' => 'bg-soft-info text-info'],
                                    'Selesai'  => ['dot' => '#198754', 'badge' => 'bg-soft-success text-success'],
                                    default    => ['dot' => '#6c757d', 'badge' => 'bg-secondary'],
                                };
                            @endphp
                            <div class="d-flex gap-3 mb-4" style="position:relative;">
                                {{-- Dot --}}
                                <div style="position:absolute; left:-1.62rem; top:0.25rem; width:14px; height:14px; border-radius:50%; background:{{ $statusColor['dot'] }}; border:2px solid white; z-index:1;"></div>

                                {{-- Konten --}}
                                <div class="card flex-grow-1 mb-0 shadow-none border">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $statusColor['badge'] }}">{{ $h->status }}</span>
                                                <span class="badge bg-soft-secondary text-secondary small">{{ $h->nama_kategori }}</span>
                                            </div>
                                            <small class="text-muted">{{ $h->created_at_fmt }}</small>
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <iconify-icon icon="solar:map-point-bold-duotone" class="me-1"></iconify-icon>
                                            {{ $h->lokasi }}
                                        </div>
                                        @if ($h->keterangan)
                                            <p class="mb-0 small">{{ $h->keterangan }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($histori->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $histori->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
@endsection