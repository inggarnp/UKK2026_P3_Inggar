@extends('layouts.app')
@section('title', 'Review Aspirasi Siswa | Guru')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">
                    <iconify-icon icon="solar:clipboard-check-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Review Aspirasi Siswa
                </h4>
                <p class="text-muted mb-0">Aspirasi dari siswa di kelas yang kamu wali</p>
            </div>
        </div>

        @if($kelasIds->isEmpty())
            <div class="alert alert-warning">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                Kamu belum ditugaskan sebagai wali kelas manapun. Hubungi admin untuk pengaturan wali kelas.
            </div>
        @else

        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Status Review</label>
                        <select id="filterAlur" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="menunggu_review" selected>⏳ Menunggu Review</option>
                            <option value="disetujui">✅ Sudah Disetujui</option>
                            <option value="ditolak">❌ Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama siswa, lokasi...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-secondary" id="btnResetFilter">
                            <i class="bx bx-reset"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="mb-0 text-muted small">Tampilkan</label>
                    <select id="perPageSelect" class="form-select form-select-sm" style="width:80px">
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                    <label class="mb-0 text-muted small">data</label>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th>Siswa</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th>Saksi</th>
                                <th width="14%">Status</th>
                                <th width="10%">Tanggal</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody">
                            <tr><td colspan="9" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <span class="ms-2 text-muted">Memuat data...</span>
                            </td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                    <div id="tableInfo" class="text-muted small"></div>
                    <nav><ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul></nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ═══════ MODAL DETAIL + AKSI ═══════ --}}
<div class="modal fade" id="detailReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <iconify-icon icon="solar:document-bold-duotone" class="me-2"></iconify-icon>
                    Detail Aspirasi Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info aspirasi --}}
                <div id="detailBody">
                    <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                </div>

                {{-- Form feedback + aksi — hanya muncul kalau masih menunggu review --}}
                <div id="sectionAksiReview" class="d-none">
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone" class="me-1 text-primary"></iconify-icon>
                        Feedback untuk Siswa
                    </h6>
                    <div class="mb-3">
                        <label class="form-label">Pesan Feedback <span class="text-muted small">(opsional tapi disarankan)</span></label>
                        <textarea class="form-control" id="inputFeedback" rows="3"
                            placeholder="Contoh: Aspirasi kamu sudah diterima dan akan segera ditindaklanjuti oleh petugas sarana..."
                            maxlength="500"></textarea>
                        <small class="text-muted">Pesan ini akan terlihat oleh siswa di halaman daftar aspirasinya.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Internal <span class="text-muted small">(untuk petugas sarana)</span></label>
                        <textarea class="form-control" id="inputCatatan" rows="2"
                            placeholder="Tambahkan catatan untuk petugas sarana jika diperlukan..."
                            maxlength="300"></textarea>
                    </div>

                    <div class="alert alert-info py-2 mb-3">
                        <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                        Setelah disetujui, aspirasi akan otomatis diteruskan ke <strong>Petugas Sarana</strong> untuk ditindaklanjuti.
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="detailFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════ MODAL REJECT ═══════ --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-x-circle me-2"></i>Tolak Aspirasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <input type="hidden" id="rejectId">
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        Aspirasi akan dikembalikan ke siswa dengan alasan penolakan.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectCatatan" rows="3"
                            placeholder="Jelaskan alasan penolakan kepada siswa..." maxlength="300" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback Tambahan <span class="text-muted small">(opsional)</span></label>
                        <textarea class="form-control" id="rejectFeedback" rows="2"
                            placeholder="Saran atau arahan untuk siswa..." maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btnReject">
                        <i class="bx bx-x me-1"></i> Tolak Aspirasi
                        <span id="loaderReject" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1, perPage = 10, searchQuery = '';
let currentDetailId = null;

function loadData() {
    $('#tabelBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("guru.review.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery, alur: $('#filterAlur').val() },
        success: renderTable,
        error: () => $('#tabelBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data.</td></tr>')
    });
}

function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada aspirasi ditemukan.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';
    let alurBadge = { 'menunggu_review': 'bg-soft-warning text-warning', 'disetujui': 'bg-soft-success text-success', 'ditolak': 'bg-soft-danger text-danger' };
    let alurLabel = { 'menunggu_review': '⏳ Menunggu Review', 'disetujui': '✅ Disetujui', 'ditolak': '❌ Ditolak' };

    res.data.forEach(function(d, i) {
        let ket = d.keterangan?.length > 40 ? d.keterangan.substring(0, 40) + '...' : (d.keterangan || '-');
        html += `<tr>
            <td class="text-muted">${start + i}</td>
            <td><div class="fw-semibold">${d.nama_siswa}</div><small class="text-muted">${d.kelas}</small></td>
            <td><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></td>
            <td class="small">${d.lokasi_display}</td>
            <td class="small text-muted">${ket}</td>
            <td class="small">${d.saksi_nama !== '-' ? `<span class="badge bg-soft-info text-info">${d.saksi_nama}</span>` : '<span class="text-muted">-</span>'}</td>
            <td><span class="badge ${alurBadge[d.status_alur] || 'bg-secondary'}">${alurLabel[d.status_alur] || d.status_alur}</span></td>
            <td class="small text-muted">${d.created_at_fmt}</td>
            <td class="text-center" style="white-space:nowrap">
                <button class="btn btn-sm btn-soft-info" onclick="lihatDetail(${d.id})" title="Detail"><i class="bx bx-show"></i></button>
            </td>
        </tr>`;
    });
    $('#tabelBody').html(html);
    let to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${start}–${to} dari ${res.total} aspirasi`);
    renderPagination(res.current_page, res.last_page);
}

function renderPagination(c, l) {
    if (l <= 1) { $('#paginationLinks').html(''); return; }
    let h = `<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c-1})">‹</a></li>`;
    for (let p = 1; p <= l; p++) {
        if (p===1||p===l||(p>=c-1&&p<=c+1)) h += `<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return goPage(${p})">${p}</a></li>`;
        else if (p===c-2||p===c+2) h += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    h += `<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c+1})">›</a></li>`;
    $('#paginationLinks').html(h);
}
function goPage(p) { currentPage = p; loadData(); return false; }

// ─── DETAIL ───────────────────────────────────────────────
function lihatDetail(id) {
    currentDetailId = id;
    $('#inputFeedback').val('');
    $('#inputCatatan').val('');
    $('#sectionAksiReview').addClass('d-none');
    $('#detailFooter').html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>');
    $('#detailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    $('#detailReviewModal').modal('show');

    $.get('{{ url("guru/review") }}/' + id, function(d) {
        let fotoHtml = d.foto_url
            ? `<img src="${d.foto_url}" class="img-fluid rounded mt-1" style="max-height:160px;object-fit:cover">`
            : '<span class="text-muted small"><i class="bx bx-image-alt me-1"></i>Tidak ada foto</span>';

        let ruanganInfo = (d.kode_ruangan || d.lantai || d.gedung)
            ? `<div class="d-flex gap-3 mt-2">
                ${d.kode_ruangan ? `<div><small class="text-muted d-block">Kode</small><strong>${d.kode_ruangan}</strong></div>` : ''}
                ${d.lantai ? `<div><small class="text-muted d-block">Lantai</small><strong>${d.lantai}</strong></div>` : ''}
                ${d.gedung ? `<div><small class="text-muted d-block">Gedung</small><strong>${d.gedung}</strong></div>` : ''}
               </div>` : '';

        let saksiHtml = d.saksi_nama && d.saksi_nama !== '-'
            ? `<span class="badge bg-soft-info text-info">${d.saksi_nama}</span>`
            : '<span class="text-muted small">Tidak ada saksi</span>';

        let alurBadge = { 'menunggu_review': 'bg-soft-warning text-warning', 'disetujui': 'bg-soft-success text-success', 'ditolak': 'bg-soft-danger text-danger' };
        let alurLabel = { 'menunggu_review': '⏳ Menunggu Review', 'disetujui': '✅ Disetujui', 'ditolak': '❌ Ditolak' };

        $('#detailBody').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Informasi Pelapor</h6>
                    <div class="mb-2"><small class="text-muted d-block">Nama Siswa</small><strong>${d.nama_siswa ?? '-'}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Kelas</small><span>${d.kelas ?? '-'}</span></div>
                    <div class="mb-3"><small class="text-muted d-block">Saksi</small>${saksiHtml}</div>

                    <h6 class="text-muted border-bottom pb-2 mb-3">Detail Aspirasi</h6>
                    <div class="mb-2"><small class="text-muted d-block">Kategori</small><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></div>
                    <div class="mb-2"><small class="text-muted d-block">Lokasi</small><strong>${d.lokasi_display}</strong>${ruanganInfo}</div>
                    <div class="mb-2"><small class="text-muted d-block">Keterangan</small><p class="mb-0">${d.keterangan ?? '-'}</p></div>
                    <div class="mb-2"><small class="text-muted d-block">Tanggal Kirim</small><span>${d.created_at_fmt}</span></div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Foto Bukti</h6>
                    <div class="mb-4">${fotoHtml}</div>

                    <h6 class="text-muted border-bottom pb-2 mb-3">Status Review</h6>
                    <div class="mb-2">
                        <span class="badge ${alurBadge[d.status_alur] || 'bg-secondary'} fs-6 px-3 py-2">
                            ${alurLabel[d.status_alur] || d.status_alur}
                        </span>
                    </div>
                    ${d.catatan_review ? `<div class="mt-2"><small class="text-muted d-block">Catatan Review</small><p class="mb-0 small">${d.catatan_review}</p></div>` : ''}
                </div>
            </div>
        `);

        // Tampilkan form aksi hanya jika masih menunggu review
        if (d.status_alur === 'menunggu_review') {
            $('#sectionAksiReview').removeClass('d-none');
            $('#detailFooter').html(`
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnBukaReject">
                    <i class="bx bx-x me-1"></i> Tolak
                </button>
                <button type="button" class="btn btn-success" id="btnKirimApprove">
                    <i class="bx bx-check me-1"></i> Setujui & Teruskan ke Petugas
                    <span id="loaderApprove" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            `);

            // Bind tombol tolak
            $('#btnBukaReject').off('click').on('click', function() {
                $('#rejectId').val(currentDetailId);
                $('#rejectCatatan').val('');
                $('#rejectFeedback').val('');
                $('#detailReviewModal').modal('hide');
                setTimeout(() => $('#rejectModal').modal('show'), 400);
            });

            // Bind tombol setujui
            $('#btnKirimApprove').off('click').on('click', function() {
                let btn = $(this);
                btn.prop('disabled', true);
                $('#loaderApprove').removeClass('d-none');

                $.ajax({
                    url: '{{ url("guru/review/approve") }}/' + currentDetailId,
                    method: 'POST',
                    data: {
                        _token:       '{{ csrf_token() }}',
                        catatan:      $('#inputCatatan').val(),
                        isi_feedback: $('#inputFeedback').val(),
                    },
                    success: function(res) {
                        $('#detailReviewModal').modal('hide');
                        loadData();
                        showToast(res.message, 'success');
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Gagal menyetujui aspirasi.';
                        showToast(msg, 'danger');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        $('#loaderApprove').addClass('d-none');
                    }
                });
            });
        }
    }).fail(() => $('#detailBody').html('<div class="text-center text-danger py-3">Gagal memuat detail.</div>'));
}

// ─── REJECT FORM ──────────────────────────────────────────
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#rejectId').val();
    if (!$('#rejectCatatan').val().trim()) { alert('Alasan penolakan wajib diisi.'); return; }
    $('#btnReject').prop('disabled', true);
    $('#loaderReject').removeClass('d-none');

    $.ajax({
        url: '{{ url("guru/review/reject") }}/' + id,
        method: 'POST',
        data: {
            _token:       '{{ csrf_token() }}',
            catatan:      $('#rejectCatatan').val(),
            isi_feedback: $('#rejectFeedback').val(),
        },
        success: function(res) {
            $('#rejectModal').modal('hide');
            loadData();
            showToast(res.message, 'success');
        },
        error: () => showToast('Gagal menolak aspirasi.', 'danger'),
        complete: () => { $('#btnReject').prop('disabled', false); $('#loaderReject').addClass('d-none'); }
    });
});

// ─── FILTER ───────────────────────────────────────────────
let st;
$('#searchInput').on('input', function() { clearTimeout(st); st = setTimeout(() => { searchQuery = $(this).val(); currentPage = 1; loadData(); }, 400); });
$('#filterAlur').on('change', () => { currentPage = 1; loadData(); });
$('#perPageSelect').on('change', function() { perPage = $(this).val(); currentPage = 1; loadData(); });
$('#btnResetFilter').on('click', function() {
    $('#filterAlur').val('menunggu_review'); $('#searchInput').val('');
    searchQuery = ''; currentPage = 1; loadData();
});

function showToast(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0">
            <div class="d-flex"><div class="toast-body">${msg}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"></button></div>
        </div></div>`);
    $('body').append(t); setTimeout(() => t.remove(), 3500);
}

$(document).ready(() => loadData());
</script>
@endpush