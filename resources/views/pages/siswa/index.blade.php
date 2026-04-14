@extends('layouts.app')
@section('title', 'Data Siswa | Admin')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Siswa</h4>
                <p class="text-muted mb-0">Kelola data siswa dan akun login mereka</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-sm" id="btnBukaImportSiswa">
                    <i class="bx bx-upload me-1"></i> Import Excel
                </button>
                <button class="btn btn-primary btn-sm" id="btnBukaTambahSiswa">
                    <i class="bx bx-user-plus me-1"></i> Tambah Siswa
                </button>
            </div>
        </div>

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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari NIS, nama, kelas...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="5%">Foto</th>
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
    </div>
</div>

@include('pages.siswa.create')
@include('pages.siswa.read')
@include('pages.siswa.update')
@include('pages.siswa.delete')
@include('pages.siswa.import')
@endsection

@push('scripts')
<script>
let currentPage = 1, perPage = 10, searchQuery = '', deleteId = null;

// ─── LOAD DATA ────────────────────────────────────────────
function loadData() {
    $('#tabelBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("admin.siswa.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery },
        success: renderTable,
        error: function() { $('#tabelBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data.</td></tr>'); }
    });
}

function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data siswa.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';
    res.data.forEach(function(s, i) {
        let jk     = s.jenis_kelamin === 'L' ? '<span class="badge bg-soft-primary text-primary">L</span>' : '<span class="badge bg-soft-danger text-danger">P</span>';
        let foto   = s.foto ? `<img src="${s.foto}" class="rounded-circle" width="32" height="32" style="object-fit:cover">` : `<div class="rounded-circle bg-soft-secondary d-flex align-items-center justify-content-center" style="width:32px;height:32px"><i class="bx bx-user text-muted"></i></div>`;
        let email  = s.email !== '-' ? `<span class="text-muted small">${s.email}</span>` : '<span class="text-muted small">-</span>';
        html += `<tr>
            <td class="text-muted">${start + i}</td>
            <td>${foto}</td>
            <td><code class="text-dark">${s.nis}</code></td>
            <td class="fw-semibold">${s.nama}</td>
            <td><span class="badge bg-soft-warning text-warning">${s.kelas}</span></td>
            <td class="small">${s.jurusan}</td>
            <td>${jk}</td>
            <td>${email}</td>
            <td class="text-center" style="white-space:nowrap">
                <button class="btn btn-sm btn-soft-info me-1" onclick="showSiswa(${s.id})"><i class="bx bx-show"></i></button>
                <button class="btn btn-sm btn-soft-warning me-1" onclick="editSiswa(${s.id})"><i class="bx bx-edit"></i></button>
                <button class="btn btn-sm btn-soft-danger" onclick="confirmDelete(${s.id},'${s.nama}','${s.nis}')"><i class="bx bx-trash"></i></button>
            </td>
        </tr>`;
    });
    $('#tabelBody').html(html);
    let to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${start}–${to} dari ${res.total} siswa`);
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

let st;
$('#searchInput').on('input', function() { clearTimeout(st); st = setTimeout(function() { searchQuery=$('#searchInput').val(); currentPage=1; loadData(); }, 400); });
$('#perPageSelect').on('change', function() { perPage=$(this).val(); currentPage=1; loadData(); });

// ─── SHOW DETAIL ──────────────────────────────────────────
function showSiswa(id) {
    $.get('{{ url("admin/siswa") }}/' + id, function(s) {
        $('#show_foto').attr('src', s.foto || '{{ asset("assets/images/users/siswa/") }}');
        $('#show_nis').text(s.nis || '-');
        $('#show_nama').text(s.nama || '-');
        $('#show_kelas').text(s.kelas || '-');
        $('#show_jurusan').text(s.jurusan || '-');
        $('#show_jk').text(s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#show_tgl_lahir').text(s.tanggal_lahir || '-');
        $('#show_alamat').text(s.alamat || '-');
        $('#show_no_hp').text(s.no_hp || '-');
        $('#show_email').text(s.email || '-');
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
        $('#edit_kelas_id').val(s.kelas_id);
        $('#edit_jurusan_id').val(s.jurusan_id);
        $('#edit_tanggal_lahir').val(s.tanggal_lahir);
        $('#edit_alamat').val(s.alamat);
        $('#edit_no_hp').val(s.no_hp);
        $('#edit_email').val(s.email);
        $('#edit_password').val('');
        $('#editSiswaForm input[name="jenis_kelamin"][value="' + s.jenis_kelamin + '"]').prop('checked', true);
        // Preview foto
        if (s.foto) {
            $('#edit_foto_preview').attr('src', s.foto).removeClass('d-none');
        } else {
            $('#edit_foto_preview').addClass('d-none');
        }
        $('#editSiswaModal').modal('show');
    });
}

// ─── SUBMIT TAMBAH (pakai FormData karena ada file) ───────
$('#addSiswaForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    $('#btnSubmitAddSiswa').prop('disabled', true);
    $('#btnLoaderAddSiswa').removeClass('d-none');
    $('#addSiswaError').addClass('d-none');
    $.ajax({
        url: '{{ route("admin.siswa.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#addSiswaModal').modal('hide');
                $('#addSiswaForm')[0].reset();
                $('#add_foto_preview').addClass('d-none');
                loadData();
                showToast('Siswa berhasil ditambahkan!', 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#addSiswaError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitAddSiswa').prop('disabled', false); $('#btnLoaderAddSiswa').addClass('d-none'); }
    });
});

// ─── SUBMIT EDIT (pakai FormData karena ada file) ─────────
$('#editSiswaForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_siswa_id').val();
    let formData = new FormData(this);
    formData.append('_method', 'PUT');
    $('#btnSubmitEditSiswa').prop('disabled', true);
    $('#btnLoaderEditSiswa').removeClass('d-none');
    $('#editSiswaError').addClass('d-none');
    $.ajax({
        url: '{{ url("admin/siswa") }}/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#editSiswaModal').modal('hide');
                loadData();
                showToast('Data siswa berhasil diupdate!', 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#editSiswaError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitEditSiswa').prop('disabled', false); $('#btnLoaderEditSiswa').addClass('d-none'); }
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
    $(this).prop('disabled', true);
    $('#btnDeleteSiswaLoader').removeClass('d-none');
    $.ajax({
        url: '{{ url("admin/siswa") }}/' + deleteId,
        method: 'POST',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res) { $('#deleteSiswaModal').modal('hide'); loadData(); showToast(res.message, 'success'); },
        error: function() { showToast('Gagal menghapus siswa.', 'danger'); },
        complete: function() { $('#confirmDeleteSiswaBtn').prop('disabled', false); $('#btnDeleteSiswaLoader').addClass('d-none'); deleteId = null; }
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
        url: '{{ route("admin.siswa.import") }}',
        method: 'POST', data: formData, processData: false, contentType: false,
        success: function(res) {
            $('#importSuccessCount').text(res.success_count);
            $('#importErrorCount').text(res.error_count);
            $('#importResultSiswa').removeClass('d-none');
            $('#importLoadingSiswa').addClass('d-none');
            if (res.errors.length > 0) {
                let rows = res.errors.map(e => `<tr><td>${e.row}</td><td>${e.nis}</td><td>${e.nama}</td><td>${e.error}</td></tr>`).join('');
                $('#importErrorTableBodySiswa').html(rows);
                $('#importErrorDetailSiswa').removeClass('d-none');
            }
            if (res.success_count > 0) { loadData(); $('#btnRefreshAfterImportSiswa').removeClass('d-none'); }
        },
        error: function() { alert('Gagal mengimport file.'); $('#importLoadingSiswa').addClass('d-none'); },
        complete: function() { $('#btnSubmitImportSiswa').prop('disabled', false); $('#btnLoaderImportSiswa').addClass('d-none'); }
    });
});

// ─── TOAST ────────────────────────────────────────────────
function showToast(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto"></button></div></div></div>`);
    $('body').append(t);
    setTimeout(() => t.remove(), 3500);
}

// ─── INIT — semua bind di sini ─────────────────────────────
$(document).ready(function() {
    loadData();

    // fix tombol tambah — bind di ready
    $('#btnBukaTambahSiswa').on('click', function() {
        $('#addSiswaForm')[0].reset();
        $('#addSiswaError').addClass('d-none');
        $('#add_foto_preview').addClass('d-none');
        $('#addSiswaModal').modal('show');
    });

    $('#btnBukaImportSiswa').on('click', function() {
        $('#importResultSiswa').addClass('d-none');
        $('#importErrorDetailSiswa').addClass('d-none');
        $('#importFileSiswa').val('');
        $('#importSiswaModal').modal('show');
    });
});
</script>
@endpush