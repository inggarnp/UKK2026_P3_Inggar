@extends('layouts.app')

@section('title', 'Data Siswa | Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .badge-kelas { font-size: 0.75rem; }
    .action-btn  { white-space: nowrap; }
    #tabelSiswa_wrapper .dataTables_filter { display: none; } /* pakai search custom */
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Siswa</h4>
                <p class="text-muted mb-0">Kelola data siswa dan akun login mereka</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importSiswaModal">
                    <i class="bx bx-upload me-1"></i> Import Excel
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
                    <i class="bx bx-user-plus me-1"></i> Tambah Siswa
                </button>
            </div>
        </div>

        {{-- Card Tabel --}}
        <div class="card">
            <div class="card-body">

                {{-- Toolbar search + per_page --}}
                <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-muted small">Tampilkan</label>
                        <select id="perPageSelect" class="form-select form-select-sm" style="width:80px">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="mb-0 text-muted small">data</label>
                    </div>
                    <div class="input-group input-group-sm" style="width:260px">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari NIS, nama, kelas...">
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelSiswa">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>J.K</th>
                                <th>Email</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody">
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ms-2 text-muted">Memuat data...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                    <div id="tableInfo" class="text-muted small"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                    </nav>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Include Modals --}}
@include('pages.siswa.create')
@include('pages.siswa.read')
@include('pages.siswa.update')
@include('pages.siswa.delete')
@include('pages.siswa.import')
@endsection

@push('scripts')
{{-- Tambahkan ini jika jQuery belum ada di layout --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
// ─── STATE ────────────────────────────────────────────────
let currentPage = 1;
let perPage     = 10;
let searchQuery = '';
let sortCol     = 'id';
let sortDir     = 'asc';
let deleteId    = null;

// ─── LOAD DATA ────────────────────────────────────────────
function loadData() {
    $('#tabelBody').html(`
        <tr><td colspan="8" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 text-muted">Memuat data...</span>
        </td></tr>
    `);

    $.ajax({
        url: '{{ route("admin.siswa-data.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery, sort_column: sortCol, sort_dir: sortDir },
        success: function(res) {
            renderTable(res);
        },
        error: function() {
            $('#tabelBody').html('<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
        }
    });
}

// ─── RENDER TABLE ─────────────────────────────────────────
function renderTable(res) {
    if (res.data.length === 0) {
        $('#tabelBody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data siswa.</td></tr>');
        $('#tableInfo').text('');
        $('#paginationLinks').html('');
        return;
    }

    let html = '';
    let start = (res.current_page - 1) * res.per_page + 1;
    res.data.forEach(function(s, i) {
        let jk = s.jenis_kelamin === 'L'
            ? '<span class="badge bg-soft-primary text-primary">Laki-laki</span>'
            : '<span class="badge bg-soft-danger text-danger">Perempuan</span>';
        let email = s.user ? s.user.email : '<span class="text-muted">-</span>';

        html += `
        <tr>
            <td class="text-muted">${start + i}</td>
            <td><code class="text-dark">${s.nis}</code></td>
            <td class="fw-semibold">${s.nama}</td>
            <td><span class="badge bg-soft-warning text-warning badge-kelas">${s.kelas}</span></td>
            <td>${s.jurusan}</td>
            <td>${jk}</td>
            <td class="text-muted small">${email}</td>
            <td class="text-center action-btn">
                <button class="btn btn-sm btn-soft-info me-1" onclick="showSiswa(${s.id})" title="Detail">
                    <i class="bx bx-show"></i>
                </button>
                <button class="btn btn-sm btn-soft-warning me-1" onclick="editSiswa(${s.id})" title="Edit">
                    <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-sm btn-soft-danger" onclick="confirmDelete(${s.id}, '${s.nama}', '${s.nis}')" title="Hapus">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    $('#tabelBody').html(html);

    // Info
    let from = start;
    let to   = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${from}–${to} dari ${res.total} siswa`);

    // Pagination
    renderPagination(res.current_page, res.last_page);
}

// ─── PAGINATION ───────────────────────────────────────────
function renderPagination(current, last) {
    if (last <= 1) { $('#paginationLinks').html(''); return; }

    let html = '';
    html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="goPage(${current - 1})">‹</a></li>`;

    for (let p = 1; p <= last; p++) {
        if (p === 1 || p === last || (p >= current - 1 && p <= current + 1)) {
            html += `<li class="page-item ${p === current ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goPage(${p})">${p}</a></li>`;
        } else if (p === current - 2 || p === current + 2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }

    html += `<li class="page-item ${current === last ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="goPage(${current + 1})">›</a></li>`;

    $('#paginationLinks').html(html);
}

function goPage(p) {
    currentPage = p;
    loadData();
    return false;
}

// ─── SEARCH & PER PAGE ────────────────────────────────────
let searchTimer;
$('#searchInput').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        searchQuery = $('#searchInput').val();
        currentPage = 1;
        loadData();
    }, 400);
});

$('#perPageSelect').on('change', function() {
    perPage     = $(this).val();
    currentPage = 1;
    loadData();
});

// ─── SHOW DETAIL ──────────────────────────────────────────
function showSiswa(id) {
    $.get('{{ url("admin/siswa") }}/' + id, function(s) {
        $('#show_nis').text(s.nis || '-');
        $('#show_nama').text(s.nama || '-');
        $('#show_kelas').text(s.kelas || '-');
        $('#show_jurusan').text(s.jurusan || '-');
        $('#show_jk').text(s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#show_tgl_lahir').text(s.tanggal_lahir || '-');
        $('#show_alamat').text(s.alamat || '-');
        $('#show_no_hp').text(s.no_hp || '-');
        $('#show_email').text(s.user ? s.user.email : '-');
        $('#show_created_at').text(s.created_at || '-');
        $('#show_updated_at').text(s.updated_at || '-');
        $('#showSiswaModal').modal('show');
    });
}

// ─── EDIT ─────────────────────────────────────────────────
function editSiswa(id) {
    $.get('{{ url("admin/siswa") }}/' + id, function(s) {
        $('#edit_siswa_id').val(s.id);
        $('#edit_nis').val(s.nis);
        $('#edit_nama').val(s.nama);
        $('#edit_kelas').val(s.kelas);
        $('#edit_jurusan').val(s.jurusan);
        $('#edit_tanggal_lahir').val(s.tanggal_lahir);
        $('#edit_alamat').val(s.alamat);
        $('#edit_no_hp').val(s.no_hp);
        $('#edit_email').val(s.user ? s.user.email : '');
        $('#edit_password').val('');

        // Radio jenis kelamin
        $(`input[name="jenis_kelamin"][value="${s.jenis_kelamin}"]`).prop('checked', true);

        $('#editSiswaModal').modal('show');
    });
}

// ─── SUBMIT TAMBAH ────────────────────────────────────────
$('#addSiswaForm').on('submit', function(e) {
    e.preventDefault();
    let btn = $('#btnSubmitAddSiswa');
    btn.prop('disabled', true);
    $('#btnLoaderAddSiswa').removeClass('d-none');
    $('#addSiswaError').addClass('d-none');

    $.ajax({
        url: '{{ route("admin.siswa-data.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            if (res.success) {
                $('#addSiswaModal').modal('hide');
                $('#addSiswaForm')[0].reset();
                loadData();
                showToast('Siswa berhasil ditambahkan!', 'success');
            }
        },
        error: function(xhr) {
            let errs = xhr.responseJSON?.errors;
            let msg  = errs ? Object.values(errs).flat().join('<br>') : 'Terjadi kesalahan.';
            $('#addSiswaError').html(msg).removeClass('d-none');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#btnLoaderAddSiswa').addClass('d-none');
        }
    });
});

// ─── SUBMIT EDIT ──────────────────────────────────────────
$('#editSiswaForm').on('submit', function(e) {
    e.preventDefault();
    let id  = $('#edit_siswa_id').val();
    let btn = $('#btnSubmitEditSiswa');
    btn.prop('disabled', true);
    $('#btnLoaderEditSiswa').removeClass('d-none');
    $('#editSiswaError').addClass('d-none');

    $.ajax({
        url: '{{ url("admin/siswa") }}/' + id,
        method: 'POST',
        data: $(this).serialize() + '&_method=PUT',
        success: function(res) {
            if (res.success) {
                $('#editSiswaModal').modal('hide');
                loadData();
                showToast('Data siswa berhasil diupdate!', 'success');
            }
        },
        error: function(xhr) {
            let errs = xhr.responseJSON?.errors;
            let msg  = errs ? Object.values(errs).flat().join('<br>') : 'Terjadi kesalahan.';
            $('#editSiswaError').html(msg).removeClass('d-none');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#btnLoaderEditSiswa').addClass('d-none');
        }
    });
});

// ─── DELETE ───────────────────────────────────────────────
function confirmDelete(id, nama, nis) {
    deleteId = id;
    $('#delete_siswa_name').text(nama);
    $('#delete_siswa_nis').text(nis);
    $('#deleteSiswaModal').modal('show');
}

$('#confirmDeleteSiswaBtn').on('click', function() {
    if (!deleteId) return;
    let btn = $(this);
    btn.prop('disabled', true);
    $('#btnDeleteSiswaLoader').removeClass('d-none');

    $.ajax({
        url: '{{ url("admin/siswa") }}/' + deleteId,
        method: 'POST',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.success) {
                $('#deleteSiswaModal').modal('hide');
                loadData();
                showToast('Siswa berhasil dihapus!', 'danger');
            }
        },
        error: function() {
            showToast('Gagal menghapus siswa.', 'danger');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#btnDeleteSiswaLoader').addClass('d-none');
            deleteId = null;
        }
    });
});

// ─── IMPORT ───────────────────────────────────────────────
$('#btnSubmitImportSiswa').on('click', function() {
    let file = $('#importFileSiswa')[0].files[0];
    if (!file) { alert('Pilih file terlebih dahulu.'); return; }

    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    $(this).prop('disabled', true);
    $('#btnLoaderImportSiswa').removeClass('d-none');
    $('#importLoadingSiswa').removeClass('d-none');
    $('#importResultSiswa').addClass('d-none');

    $.ajax({
        url: '{{ route("admin.siswa-data.import") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            $('#importSuccessCount').text(res.success_count);
            $('#importErrorCount').text(res.error_count);
            $('#importResultSiswa').removeClass('d-none');
            $('#importLoadingSiswa').addClass('d-none');

            if (res.errors.length > 0) {
                let rows = res.errors.map(e =>
                    `<tr><td>${e.row}</td><td>${e.nis}</td><td>${e.nama}</td><td>${e.error}</td></tr>`
                ).join('');
                $('#importErrorTableBodySiswa').html(rows);
                $('#importErrorDetailSiswa').removeClass('d-none');
            }

            if (res.success_count > 0) {
                loadData();
                $('#btnRefreshAfterImportSiswa').removeClass('d-none');
            }
        },
        error: function() {
            alert('Gagal mengimport file.');
            $('#importLoadingSiswa').addClass('d-none');
        },
        complete: function() {
            $('#btnSubmitImportSiswa').prop('disabled', false);
            $('#btnLoaderImportSiswa').addClass('d-none');
        }
    });
});

// ─── TOAST HELPER ─────────────────────────────────────────
function showToast(msg, type) {
    let color = type === 'success' ? 'bg-success' : 'bg-danger';
    let toast = $(`
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
            <div class="toast show align-items-center text-white ${color} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `);
    $('body').append(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ─── INIT ─────────────────────────────────────────────────
$(document).ready(function() {
    loadData();
});
</script>
@endpush