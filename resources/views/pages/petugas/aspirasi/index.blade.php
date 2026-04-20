@extends('layouts.app')
@section('title', 'Daftar Laporan Aspirasi | Petugas Sarana')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">
                    <iconify-icon icon="solar:list-check-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Daftar Laporan Aspirasi
                </h4>
                <p class="text-muted mb-0">Laporan yang sudah disetujui dan perlu ditindaklanjuti</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">⏳ Menunggu</option>
                            <option value="Proses">🔄 Proses</option>
                            <option value="Selesai">✅ Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Kategori</label>
                        <select id="filterKategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, lokasi...">
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
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th width="11%">Status</th>
                                <th width="12%">Tanggal</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody">
                            <tr><td colspan="8" class="text-center py-4">
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
    </div>
</div>

{{-- Modal Detail + Tindak Lanjut --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <iconify-icon icon="solar:document-bold-duotone" class="me-2"></iconify-icon>
                    Detail Laporan Aspirasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailBody">
                    <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                </div>

                <hr class="my-4">

                {{-- Alert Selesai --}}
                <div id="alertSelesai" class="alert alert-success d-none">
                    <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                    <strong>Aspirasi ini sudah selesai ditangani.</strong> Tidak ada tindakan lebih lanjut yang diperlukan.
                </div>

                {{-- Form Aksi (tersembunyi jika sudah Selesai) --}}
                <div id="sectionAksi">
                    <div class="row g-3">
                        {{-- Update Status --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">
                                <iconify-icon icon="solar:transfer-horizontal-bold-duotone" class="me-1 text-primary"></iconify-icon>
                                Update Status
                            </h6>
                            <div class="mb-3">
                                <label class="form-label">Status Baru</label>
                                <select class="form-select" id="selectStatusBaru">
                                    <option value="Proses">🔄 Proses</option>
                                    <option value="Selesai">✅ Selesai</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan <span class="text-muted small">(opsional)</span></label>
                                <textarea class="form-control" id="inputKeteranganStatus" rows="2"
                                    placeholder="Contoh: Perbaikan selesai dilakukan..." maxlength="500"></textarea>
                            </div>
                            {{-- Foto bukti selesai --}}
                            <div class="mb-3" id="fotoSelesaiWrapper">
                                <label class="form-label">
                                    Foto Bukti
                                    <span class="text-muted small" id="labelFotoStatus">(opsional)</span>
                                </label>
                                <input type="file" class="form-control form-control-sm" id="inputFotoSelesai"
                                    accept="image/jpg,image/jpeg,image/png">
                                <small class="text-muted">Format: JPG, PNG — Maks 3MB</small>
                                <div class="mt-2">
                                    <img id="previewFotoSelesai" src="" class="rounded d-none"
                                        style="max-height:120px;object-fit:cover;border:2px solid #dee2e6">
                                </div>
                            </div>
                            <button class="btn btn-primary" id="btnUpdateStatus">
                                <iconify-icon icon="solar:transfer-horizontal-bold-duotone" class="me-1"></iconify-icon>
                                Update Status
                                <span id="loaderStatus" class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>

                        {{-- Tambah Progres + Foto --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">
                                <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1 text-success"></iconify-icon>
                                Tambah Catatan Progres
                            </h6>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Progres <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="inputProgres" rows="3"
                                    placeholder="Jelaskan progres perbaikan..." maxlength="500"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Foto Progres <span class="text-muted small">(opsional)</span>
                                </label>
                                <input type="file" class="form-control form-control-sm" id="inputFotoProgres"
                                    accept="image/jpg,image/jpeg,image/png">
                                <small class="text-muted">Foto kondisi saat ini / hasil perbaikan. Maks 3MB.</small>
                                <div class="mt-2">
                                    <img id="previewFotoProgres" src="" class="rounded d-none"
                                        style="max-height:120px;object-fit:cover;border:2px solid #dee2e6">
                                </div>
                            </div>
                            <button class="btn btn-success" id="btnTambahProgres">
                                <i class="bx bx-plus me-1"></i> Tambah Progres
                                <span id="loaderProgres" class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                    </div>
                </div>

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
let currentPage = 1, perPage = 10, searchQuery = '';
let currentId = null, currentAspirasId = null;

function loadData() {
    $('#tabelBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("petugas.aspirasi.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery, status: $('#filterStatus').val(), kategori: $('#filterKategori').val() },
        success: renderTable,
        error: () => $('#tabelBody').html('<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data.</td></tr>')
    });
}

function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada laporan.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';
    let statusMap = { 'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success' };
    res.data.forEach(function(d, i) {
        let ket = d.keterangan?.length > 45 ? d.keterangan.substring(0, 45) + '...' : (d.keterangan || '-');
        html += `<tr>
            <td class="text-muted">${start + i}</td>
            <td><div class="fw-semibold">${d.nama_pelapor}</div><span class="badge bg-soft-secondary text-secondary small">${d.role === 'siswa' ? 'Siswa' : 'Guru'}</span></td>
            <td><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></td>
            <td class="small">${d.lokasi_display}</td>
            <td class="small text-muted">${ket}</td>
            <td><span class="badge ${statusMap[d.status]||'bg-secondary'}">${d.status}</span></td>
            <td class="small text-muted">${d.created_at_fmt}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-soft-info" onclick="lihatDetail(${d.id}, ${d.aspirasi_id})">
                    <i class="bx bx-show"></i>
                </button>
            </td>
        </tr>`;
    });
    $('#tabelBody').html(html);
    let to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${start}–${to} dari ${res.total} laporan`);
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

// ─── Preview foto ─────────────────────────────────────────────
$('#inputFotoProgres').on('change', function() {
    previewImg(this, 'previewFotoProgres');
});
$('#inputFotoSelesai').on('change', function() {
    previewImg(this, 'previewFotoSelesai');
});
function previewImg(input, previewId) {
    let preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    } else { preview.classList.add('d-none'); }
}

// Update label foto berdasarkan status yang dipilih
$('#selectStatusBaru').on('change', function() {
    let isSelesai = $(this).val() === 'Selesai';
    $('#labelFotoStatus').text(isSelesai ? '(wajib jika ada bukti selesai)' : '(opsional)');
});

// ─── DETAIL ───────────────────────────────────────────────────
function lihatDetail(id, aspirasId) {
    currentId        = id;
    currentAspirasId = aspirasId;
    // Reset form
    $('#inputProgres').val('');
    $('#inputKeteranganStatus').val('');
    $('#inputFotoProgres').val('');
    $('#inputFotoSelesai').val('');
    $('#previewFotoProgres, #previewFotoSelesai').addClass('d-none');
    $('#alertSelesai').addClass('d-none');
    $('#sectionAksi').removeClass('d-none');
    $('#detailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    $('#detailModal').modal('show');

    $.get('{{ url("petugas/aspirasi") }}/' + id, function(d) {
        let statusMap = {'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success'};
        let fotoHtml  = d.foto_url ? `<img src="${d.foto_url}" class="img-fluid rounded mt-1" style="max-height:160px;object-fit:cover">` : '<span class="text-muted small">Tidak ada foto</span>';
        let ruanganInfo = (d.kode_ruangan||d.lantai||d.gedung)
            ? `<div class="d-flex gap-3 mt-1 small text-muted">
                ${d.kode_ruangan?`<span>Kode: <strong>${d.kode_ruangan}</strong></span>`:''}
                ${d.lantai?`<span>Lantai: <strong>${d.lantai}</strong></span>`:''}
                ${d.gedung?`<span>Gedung: <strong>${d.gedung}</strong></span>`:''}
               </div>` : '';

        let feedbackHtml = d.feedback?.length
            ? d.feedback.map(f=>`<div class="border rounded p-2 mb-2"><div class="small fw-semibold text-muted">${f.nama_pemberi} • ${f.created_at_fmt}</div><p class="mb-0 small">${f.isi_feedback}</p></div>`).join('')
            : '<p class="text-muted small mb-0">Belum ada feedback.</p>';

        let historiHtml = d.histori?.length
            ? d.histori.map(h=>`<div class="d-flex gap-2 mb-2"><span class="badge ${h.status_badge} mt-1">${h.status}</span><div><div class="small text-muted">${h.created_at_fmt}</div>${h.keterangan?`<div class="small">${h.keterangan}</div>`:''}</div></div>`).join('')
            : '<p class="text-muted small mb-0">-</p>';

        let progresHtml = d.progres?.length
            ? d.progres.map(p=>`<div class="border-start border-success border-2 ps-3 mb-3">
                <div class="small text-muted">${p.nama_petugas} • ${p.created_at_fmt}</div>
                <p class="mb-1 small">${p.keterangan_progres}</p>
                ${p.foto_url ? `<img src="${p.foto_url}" class="rounded img-fluid" style="max-height:100px;object-fit:cover">` : ''}
               </div>`).join('')
            : '<p class="text-muted small mb-0">Belum ada catatan progres.</p>';

        // Set default status baru
        $('#selectStatusBaru').val(d.status === 'Proses' ? 'Selesai' : 'Proses');

        $('#detailBody').html(`
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Info Pelapor</h6>
                    <div class="mb-2"><small class="text-muted d-block">Nama</small><strong>${d.nama_pelapor}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Kategori</small><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></div>
                    <div class="mb-2"><small class="text-muted d-block">Lokasi</small><strong>${d.lokasi_display}</strong>${ruanganInfo}</div>
                    <div class="mb-2"><small class="text-muted d-block">Keterangan</small><p class="mb-0 small">${d.keterangan}</p></div>
                    <div class="mb-3"><small class="text-muted d-block">Status</small><span class="badge ${statusMap[d.status]||'bg-secondary'} fs-6 px-3 py-2">${d.status}</span></div>
                    <small class="text-muted d-block mb-1">Foto Bukti Laporan</small>${fotoHtml}
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Feedback</h6>
                    <div class="mb-4" style="max-height:150px;overflow-y:auto">${feedbackHtml}</div>
                    <h6 class="text-muted border-bottom pb-2 mb-3">Histori Status</h6>
                    <div style="max-height:180px;overflow-y:auto">${historiHtml}</div>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Catatan & Foto Progres</h6>
                    <div id="progresContainer" style="max-height:350px;overflow-y:auto">${progresHtml}</div>
                </div>
            </div>
        `);

        // Lock form jika sudah Selesai
        if (d.is_selesai) {
            $('#sectionAksi').addClass('d-none');
            $('#alertSelesai').removeClass('d-none');
        }
    }).fail(() => $('#detailBody').html('<div class="text-center text-danger py-3">Gagal memuat detail.</div>'));
}

// ─── UPDATE STATUS ────────────────────────────────────────────
$('#btnUpdateStatus').on('click', function() {
    if (!currentAspirasId) return;
    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('status', $('#selectStatusBaru').val());
    formData.append('keterangan_progres', $('#inputKeteranganStatus').val());
    let fotoFile = $('#inputFotoSelesai')[0].files[0];
    if (fotoFile) formData.append('foto_selesai', fotoFile);

    let btn = $(this);
    btn.prop('disabled', true);
    $('#loaderStatus').removeClass('d-none');

    $.ajax({
        url: '{{ url("petugas/aspirasi/status") }}/' + currentAspirasId,
        method: 'POST', data: formData, processData: false, contentType: false,
        success: function(res) {
            showToast(res.message, 'success');
            $('#inputKeteranganStatus').val('');
            $('#inputFotoSelesai').val('');
            $('#previewFotoSelesai').addClass('d-none');
            lihatDetail(currentId, currentAspirasId);
            loadData();
        },
        error: function(xhr) { showToast(xhr.responseJSON?.message || 'Gagal update status.', 'danger'); },
        complete: () => { btn.prop('disabled', false); $('#loaderStatus').addClass('d-none'); }
    });
});

// ─── TAMBAH PROGRES ───────────────────────────────────────────
$('#btnTambahProgres').on('click', function() {
    if (!currentAspirasId) return;
    let ket = $('#inputProgres').val().trim();
    if (!ket) { alert('Keterangan progres wajib diisi.'); return; }

    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('keterangan_progres', ket);
    let fotoFile = $('#inputFotoProgres')[0].files[0];
    if (fotoFile) formData.append('foto_progres', fotoFile);

    let btn = $(this);
    btn.prop('disabled', true);
    $('#loaderProgres').removeClass('d-none');

    $.ajax({
        url: '{{ url("petugas/aspirasi/progres") }}/' + currentAspirasId,
        method: 'POST', data: formData, processData: false, contentType: false,
        success: function(res) {
            showToast(res.message, 'success');
            $('#inputProgres').val('');
            $('#inputFotoProgres').val('');
            $('#previewFotoProgres').addClass('d-none');
            lihatDetail(currentId, currentAspirasId);
        },
        error: function(xhr) { showToast(xhr.responseJSON?.message || 'Gagal tambah progres.', 'danger'); },
        complete: () => { btn.prop('disabled', false); $('#loaderProgres').addClass('d-none'); }
    });
});

// ─── FILTER ───────────────────────────────────────────────────
let st;
$('#searchInput').on('input', function() { clearTimeout(st); st = setTimeout(() => { searchQuery = $(this).val(); currentPage = 1; loadData(); }, 400); });
$('#filterStatus, #filterKategori').on('change', () => { currentPage = 1; loadData(); });
$('#perPageSelect').on('change', function() { perPage = $(this).val(); currentPage = 1; loadData(); });
$('#btnResetFilter').on('click', function() {
    $('#filterStatus, #filterKategori').val(''); $('#searchInput').val('');
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