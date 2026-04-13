@extends('layouts.app')
@section('title', 'Data Aspirasi | Admin')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Aspirasi</h4>
                <p class="text-muted mb-0">Kelola semua aspirasi dari siswa dan guru</p>
            </div>
            <div>
                <button class="btn btn-primary btn-sm" id="btnBukaTambahAspiras">
                    <i class="bx bx-plus me-1"></i> Tambah Aspirasi
                </button>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small mb-1">Kategori</label>
                        <select id="filterKategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Role Pelapor</label>
                        <select id="filterRole" class="form-select form-select-sm">
                            <option value="">Siswa & Guru</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Dari Tanggal</label>
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Sampai Tanggal</label>
                        <input type="date" id="filterDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1 col-6">
                        <button class="btn btn-sm btn-outline-secondary w-100" id="btnResetFilter">
                            <i class="bx bx-reset"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Card --}}
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-muted small">Tampilkan</label>
                        <select id="perPageSelect" class="form-select form-select-sm" style="width:80px">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <label class="mb-0 text-muted small">data</label>
                    </div>
                    <div class="input-group input-group-sm" style="width:260px">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, lokasi, kategori...">
                    </div>
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
                                <th width="10%" class="text-center">Aksi</th>
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

@include('pages.input_aspirasi.create')
@include('pages.input_aspirasi.read')
@include('pages.input_aspirasi.update')
@include('pages.input_aspirasi.delete')
@endsection

@push('scripts')
<script>
let currentPage = 1, perPage = 10, searchQuery = '', deleteId = null;

// ─── LOAD DATA ────────────────────────────────────────────
function loadData() {
    $('#tabelBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("admin.aspirasi.data") }}',
        data: {
            page:      currentPage,
            per_page:  perPage,
            search:    searchQuery,
            kategori:  $('#filterKategori').val(),
            status:    $('#filterStatus').val(),
            role:      $('#filterRole').val(),
            date_from: $('#filterDateFrom').val(),
            date_to:   $('#filterDateTo').val(),
        },
        success: renderTable,
        error: function() {
            $('#tabelBody').html('<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
        }
    });
}

// ─── RENDER TABEL ─────────────────────────────────────────
function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data aspirasi.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';

    res.data.forEach(function(d, i) {
        // Badge status
        let statusMap = {
            'Menunggu': 'bg-soft-warning text-warning',
            'Proses':   'bg-soft-info text-info',
            'Selesai':  'bg-soft-success text-success',
        };
        let statusBadge = `<span class="badge ${statusMap[d.status] || 'bg-secondary'}">${d.status}</span>`;

        // Badge role
        let roleBadge = d.role === 'siswa'
            ? `<span class="badge bg-soft-primary text-primary small">Siswa</span>`
            : `<span class="badge bg-soft-purple text-purple small">Guru</span>`;

        // Keterangan truncate
        let ket = d.keterangan && d.keterangan.length > 50
            ? d.keterangan.substring(0, 50) + '...'
            : (d.keterangan || '-');

        html += `<tr>
            <td class="text-muted">${start + i}</td>
            <td>
                <div class="fw-semibold">${d.nama_pelapor}</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    ${roleBadge}
                    <code class="small text-muted">${d.identitas}</code>
                </div>
            </td>
            <td><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></td>
            <td class="small">${d.lokasi || '-'}</td>
            <td class="small text-muted">${ket}</td>
            <td>${statusBadge}</td>
            <td class="small text-muted">${d.created_at_fmt}</td>
            <td class="text-center" style="white-space:nowrap">
                <button class="btn btn-sm btn-soft-info me-1" onclick="showAspirasi(${d.id})" title="Detail">
                    <i class="bx bx-show"></i>
                </button>
                <button class="btn btn-sm btn-soft-warning me-1" onclick="editStatus(${d.id}, ${d.aspirasi_id})" title="Update Status">
                    <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-sm btn-soft-danger" onclick="confirmDelete(${d.id}, '${d.nama_pelapor}')" title="Hapus">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    $('#tabelBody').html(html);
    let to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${start}–${to} dari ${res.total} aspirasi`);
    renderPagination(res.current_page, res.last_page);
}

// ─── PAGINATION ───────────────────────────────────────────
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

// ─── SHOW DETAIL ──────────────────────────────────────────
function showAspirasi(id) {
    $('#showAspirationModal').modal('show');
    $('#show_aspirasi_body').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    $.get('{{ url("admin/aspirasi") }}/' + id, function(d) {
        let fotoHtml = d.foto_url
            ? `<img src="${d.foto_url}" class="img-fluid rounded mb-2" style="max-height:200px;object-fit:cover">`
            : `<div class="text-muted small"><i class="bx bx-image-alt me-1"></i>Tidak ada foto</div>`;

        let roleBadge = d.role === 'siswa'
            ? `<span class="badge bg-soft-primary text-primary">Siswa</span>`
            : `<span class="badge bg-soft-purple text-purple">Guru</span>`;

        let statusMap = {'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success'};
        let statusBadge = `<span class="badge ${statusMap[d.status] || 'bg-secondary'}">${d.status}</span>`;

        let historiHtml = '';
        if (d.histori && d.histori.length) {
            historiHtml = d.histori.map(h => {
                let sc = {'Menunggu':'warning','Proses':'info','Selesai':'success'};
                return `<div class="d-flex gap-2 mb-2">
                    <div class="mt-1"><span class="badge bg-${sc[h.status]||'secondary'}">${h.status}</span></div>
                    <div>
                        <div class="small text-muted">${h.created_at_fmt}</div>
                        ${h.keterangan ? `<div class="small">${h.keterangan}</div>` : ''}
                    </div>
                </div>`;
            }).join('');
        } else {
            historiHtml = '<p class="text-muted small mb-0">Belum ada histori status.</p>';
        }

        $('#show_aspirasi_body').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Informasi Pelapor</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama Pelapor</small>
                        <span class="fw-semibold">${d.nama_pelapor}</span> ${roleBadge}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Identitas (NIS/NIP)</small>
                        <code>${d.identitas}</code>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Email</small>
                        <span>${d.email}</span>
                    </div>
                    <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">Detail Aspirasi</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Kategori</small>
                        <span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Lokasi</small>
                        <span>${d.lokasi || '-'}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Keterangan</small>
                        <p class="mb-0">${d.keterangan || '-'}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Tanggal Dibuat</small>
                        <span>${d.created_at_fmt}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Foto Bukti</h6>
                    ${fotoHtml}
                    <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">Status & Feedback</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Status Saat Ini</small>
                        ${statusBadge}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Feedback Admin</small>
                        <p class="mb-0">${d.feedback || '<span class="text-muted fst-italic">Belum ada feedback</span>'}</p>
                    </div>
                    <h6 class="text-muted border-bottom pb-2 mb-3">Histori Status</h6>
                    <div style="max-height:180px;overflow-y:auto">
                        ${historiHtml}
                    </div>
                </div>
            </div>
        `);

        // Simpan aspirasi_id untuk tombol update status di modal detail
        $('#btnEditStatusFromDetail').data('id', d.id).data('aspirasi_id', d.aspirasi_id);
    }).fail(function() {
        $('#show_aspirasi_body').html('<div class="text-center text-danger py-3">Gagal memuat data.</div>');
    });
}

// ─── EDIT STATUS ──────────────────────────────────────────
let currentAspirasId = null;

function editStatus(id, aspirasi_id) {
    currentAspirasId = aspirasi_id;
    $('#editStatusError').addClass('d-none');
    $('#edit_status_input_id').val(id);

    // Ambil data dulu untuk prefill
    $.get('{{ url("admin/aspirasi") }}/' + id, function(d) {
        $('#editStatusSelect').val(d.status);
        $('#editFeedbackInput').val(d.feedback || '');
        $('#editStatusModal').modal('show');
    });
}

$('#editStatusForm').on('submit', function(e) {
    e.preventDefault();
    if (!currentAspirasId) return;
    let btn = $('#btnSubmitEditStatus');
    btn.prop('disabled', true);
    $('#btnLoaderEditStatus').removeClass('d-none');
    $('#editStatusError').addClass('d-none');

    $.ajax({
        url: '{{ url("admin/aspirasi/status") }}/' + currentAspirasId,
        method: 'POST',
        data: {
            _token:   '{{ csrf_token() }}',
            _method:  'PUT',
            status:   $('#editStatusSelect').val(),
            feedback: $('#editFeedbackInput').val(),
        },
        success: function(res) {
            if (res.success) {
                $('#editStatusModal').modal('hide');
                loadData();
                showToast(res.message, 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#editStatusError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#btnLoaderEditStatus').addClass('d-none');
        }
    });
});

// ─── DELETE ───────────────────────────────────────────────
function confirmDelete(id, nama) {
    deleteId = id;
    $('#delete_aspirasi_name').text(nama);
    $('#deleteAspirasModal').modal('show');
}

$('#confirmDeleteAspirasBtn').on('click', function() {
    if (!deleteId) return;
    $(this).prop('disabled', true);
    $('#btnDeleteAspirasLoader').removeClass('d-none');
    $.ajax({
        url: '{{ url("admin/aspirasi") }}/' + deleteId,
        method: 'POST',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res) {
            $('#deleteAspirasModal').modal('hide');
            loadData();
            showToast(res.message, 'success');
        },
        error: function() { showToast('Gagal menghapus aspirasi.', 'danger'); },
        complete: function() {
            $('#confirmDeleteAspirasBtn').prop('disabled', false);
            $('#btnDeleteAspirasLoader').addClass('d-none');
            deleteId = null;
        }
    });
});

// ─── FILTER & SEARCH ──────────────────────────────────────
let st;
$('#searchInput').on('input', function() {
    clearTimeout(st);
    st = setTimeout(function() { searchQuery = $('#searchInput').val(); currentPage = 1; loadData(); }, 400);
});
$('#perPageSelect').on('change', function() { perPage = $(this).val(); currentPage = 1; loadData(); });
$('#filterKategori, #filterStatus, #filterRole, #filterDateFrom, #filterDateTo').on('change', function() {
    currentPage = 1; loadData();
});
$('#btnResetFilter').on('click', function() {
    $('#filterKategori, #filterStatus, #filterRole').val('');
    $('#filterDateFrom, #filterDateTo').val('');
    $('#searchInput').val('');
    searchQuery = ''; currentPage = 1; loadData();
});

// ─── TOAST ────────────────────────────────────────────────
function showToast(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0">
            <div class="d-flex">
                <div class="toast-body">${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
            </div>
        </div></div>`);
    $('body').append(t);
    setTimeout(() => t.remove(), 3500);
}

// Tombol edit status dari modal detail
$(document).on('click', '#btnEditStatusFromDetail', function() {
    let id = $(this).data('id');
    let aspirasi_id = $(this).data('aspirasi_id');
    $('#showAspirationModal').modal('hide');
    setTimeout(() => editStatus(id, aspirasi_id), 400);
});

// ─── SUBMIT TAMBAH ASPIRASI ───────────────────────────────
$('#addAspirasForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    $('#btnSubmitAddAspiras').prop('disabled', true);
    $('#btnLoaderAddAspiras').removeClass('d-none');
    $('#addAspirasError').addClass('d-none');
    $.ajax({
        url: '{{ route("admin.aspirasi.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#addAspirasModal').modal('hide');
                $('#addAspirasForm')[0].reset();
                $('#add_aspiras_foto_preview').addClass('d-none');
                loadData();
                showToast('Aspirasi berhasil ditambahkan!', 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#addAspirasError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() {
            $('#btnSubmitAddAspiras').prop('disabled', false);
            $('#btnLoaderAddAspiras').addClass('d-none');
        }
    });
});

// ─── INIT ─────────────────────────────────────────────────
$(document).ready(function() {
    loadData();

    $('#btnBukaTambahAspiras').on('click', function() {
        $('#addAspirasForm')[0].reset();
        $('#addAspirasError').addClass('d-none');
        $('#add_aspiras_foto_preview').addClass('d-none');
        // Reset ke siswa tab
        document.getElementById('role_siswa').checked = true;
        $('#selectPelapor option[data-role]').each(function() {
            if ($(this).data('role') === 'siswa') $(this).removeClass('d-none');
            else $(this).addClass('d-none');
        });
        $('#selectPelapor option[value=""]').text('-- Pilih Siswa --');
        $('#addAspirasModal').modal('show');
    });
});
</script>
@endpush