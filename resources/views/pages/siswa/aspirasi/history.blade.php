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
                    <a href="{{ route('siswa.aspirasi.create') }}" class="btn btn-primary btn-sm">Kirim Aspirasi Pertama</a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="timeline-container" style="position:relative; padding-left: 2rem;">
                        <div style="position:absolute; left:0.6rem; top:0; bottom:0; width:2px; background:#e9ecef;"></div>

                        @foreach ($histori as $h)
                            @php
                                $statusBaru = $h->status_baru ?? $h->status ?? 'Menunggu';
                                $dotColor = match($statusBaru) {
                                    'Proses'   => '#0dcaf0',
                                    'Selesai'  => '#198754',
                                    default    => '#ffc107',
                                };
                                $badgeClass = match($statusBaru) {
                                    'Proses'  => 'bg-soft-info text-info',
                                    'Selesai' => 'bg-soft-success text-success',
                                    default   => 'bg-soft-warning text-warning',
                                };
                                $inputAspirasi = $h->aspirasi?->inputAspirasi;
                            @endphp
                            <div class="d-flex gap-3 mb-4" style="position:relative;">
                                <div style="position:absolute; left:-1.62rem; top:0.3rem; width:14px; height:14px; border-radius:50%; background:{{ $dotColor }}; border:2px solid white; z-index:1;"></div>

                                <div class="card flex-grow-1 mb-0 shadow-none border">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge {{ $badgeClass }}">{{ $statusBaru }}</span>
                                                @if($inputAspirasi?->kategori)
                                                    <span class="badge bg-soft-secondary text-secondary small">{{ $inputAspirasi->kategori->nama_kategori }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <small class="text-muted">{{ $h->created_at_fmt }}</small>
                                                @if($inputAspirasi)
                                                    <button class="btn btn-sm btn-soft-info py-0 px-2"
                                                        onclick="lihatDetailHistori({{ $inputAspirasi->id }})"
                                                        title="Lihat Detail">
                                                        <i class="bx bx-show"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($inputAspirasi)
                                            <div class="small text-muted mt-1">
                                                <iconify-icon icon="solar:map-point-bold-duotone" class="me-1"></iconify-icon>
                                                {{ $inputAspirasi->lokasi_display }}
                                            </div>
                                        @endif

                                        @if($h->keterangan)
                                            <p class="mb-0 small mt-1">{{ $h->keterangan }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

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

{{-- Modal Detail Aspirasi --}}
<div class="modal fade" id="detailHistoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <iconify-icon icon="solar:document-bold-duotone" class="me-2"></iconify-icon>
                    Detail Aspirasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailHistoriBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lihatDetailHistori(id) {
    $('#detailHistoriModal').modal('show');
    $('#detailHistoriBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');

    $.get('{{ url("siswa/aspirasi") }}/' + id, function(d) {
        let statusMap  = {'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success'};
        let alurMap    = {'menunggu_review':'bg-soft-warning text-warning','disetujui':'bg-soft-success text-success','ditolak':'bg-soft-danger text-danger'};
        let alurLabel  = {'menunggu_review':'⏳ Menunggu Review','disetujui':'✅ Disetujui','ditolak':'❌ Ditolak'};

        let fotoHtml = d.foto_url
            ? `<img src="${d.foto_url}" class="img-fluid rounded mt-2" style="max-height:180px;object-fit:cover">`
            : '<span class="text-muted small">Tidak ada foto</span>';

        let feedbackHtml = d.feedback?.length
            ? d.feedback.map(f => `<div class="border rounded p-2 mb-2">
                <div class="small text-muted fw-semibold">${f.nama_pemberi} • ${f.created_at_fmt}</div>
                <p class="mb-0 small">${f.isi_feedback}</p></div>`).join('')
            : '<p class="text-muted small mb-0">Belum ada feedback dari wali kelas / admin.</p>';

        let progresHtml = d.progres?.length
            ? d.progres.map(p => `<div class="border-start border-success border-2 ps-3 mb-2">
                <div class="small text-muted">${p.nama_petugas} • ${p.created_at_fmt}</div>
                <p class="mb-0 small">${p.keterangan_progres}</p></div>`).join('')
            : '<p class="text-muted small mb-0">Belum ada catatan progres perbaikan.</p>';

        let historiHtml = d.histori?.length
            ? d.histori.map(h => `<div class="d-flex gap-2 mb-2 align-items-start">
                <span class="badge ${h.status_badge} mt-1">${h.status}</span>
                <div><div class="small text-muted">${h.created_at_fmt}</div>
                ${h.keterangan ? `<div class="small">${h.keterangan}</div>` : ''}</div></div>`).join('')
            : '<p class="text-muted small mb-0">-</p>';

        let reviewInfo = d.status_alur === 'ditolak'
            ? `<div class="alert alert-danger py-2 mb-3">
                <strong>Ditolak oleh:</strong> ${d.reviewer_nama || '-'}<br>
                <strong>Alasan:</strong> ${d.catatan_review || '-'}
               </div>`
            : (d.status_alur === 'disetujui'
                ? `<div class="alert alert-success py-2 mb-3">
                    <strong>Disetujui oleh:</strong> ${d.reviewer_nama || '-'}<br>
                    ${d.catatan_review ? `<span>${d.catatan_review}</span>` : ''}
                   </div>`
                : `<div class="alert alert-warning py-2 mb-3">Menunggu review wali kelas.</div>`);

        $('#detailHistoriBody').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Detail Aspirasi</h6>
                    <div class="mb-2"><small class="text-muted d-block">Kategori</small><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></div>
                    <div class="mb-2"><small class="text-muted d-block">Lokasi</small><strong>${d.lokasi_display}</strong>
                        ${d.kode_ruangan ? `<div class="small text-muted">Kode: ${d.kode_ruangan} | Lantai: ${d.lantai||'-'} | Gedung: ${d.gedung||'-'}</div>` : ''}
                    </div>
                    <div class="mb-2"><small class="text-muted d-block">Saksi</small>${d.saksi_nama !== '-' ? `<span class="badge bg-soft-info text-info">${d.saksi_nama}</span>` : '<span class="text-muted small">-</span>'}</div>
                    <div class="mb-2"><small class="text-muted d-block">Keterangan</small><p class="mb-0 small">${d.keterangan}</p></div>
                    <div class="mb-3"><small class="text-muted d-block">Status</small>
                        <span class="badge ${statusMap[d.status]||'bg-secondary'}">${d.status}</span>
                        <span class="badge ${alurMap[d.status_alur]||'bg-secondary'} ms-1">${alurLabel[d.status_alur]||d.status_alur}</span>
                    </div>
                    ${reviewInfo}
                    <div class="mb-3"><small class="text-muted d-block">Foto Bukti</small>${fotoHtml}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Feedback dari Wali Kelas / Admin</h6>
                    <div class="mb-4">${feedbackHtml}</div>

                    <h6 class="text-muted border-bottom pb-2 mb-3">Progres Perbaikan</h6>
                    <div class="mb-4">${progresHtml}</div>

                    <h6 class="text-muted border-bottom pb-2 mb-3">Histori Status</h6>
                    <div style="max-height:200px;overflow-y:auto">${historiHtml}</div>
                </div>
            </div>
        `);
    }).fail(() => $('#detailHistoriBody').html('<div class="text-center text-danger py-3">Gagal memuat detail.</div>'));
}
</script>
@endpush