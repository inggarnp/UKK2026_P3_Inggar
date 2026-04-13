@extends('layouts.app')
@section('title', 'Data Guru')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Data Guru</h4>
        <p class="text-muted mb-0">Kelola data guru dan akun login mereka</p>
    </div>
    {{-- ✅ Fix: id saja, bind di ready --}}
    <button class="btn btn-primary btn-sm" id="btnBukaTambahGuru">
        <i class="bx bx-plus me-1"></i> Tambah Guru
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 text-muted small">Tampilkan</label>
                <select id="perPageGuru" class="form-select form-select-sm" style="width:80px">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="mb-0 text-muted small">data</label>
            </div>
            <div class="input-group input-group-sm" style="width:260px">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text" id="searchGuru" class="form-control" placeholder="Cari NIP, nama, jabatan...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="6%">Foto</th>
                        <th width="14%">NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Mata Pelajaran</th>
                        <th width="6%" class="text-center">J.K</th>
                        <th>Email</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="guruTableBody">
                    <tr><td colspan="9" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="ms-2 text-muted">Memuat data...</span>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <div id="guruInfo" class="text-muted small"></div>
            <nav><ul class="pagination pagination-sm mb-0" id="guruPagination"></ul></nav>
        </div>
    </div>
</div>

@include('pages.guru.create')
@include('pages.guru.read')
@include('pages.guru.update')
@include('pages.guru.delete')

@endsection

@push('scripts')
<script>
const GURU_URL  = '{{ route("admin.guru.index") }}';
const GURU_DATA = '{{ route("admin.guru.data") }}';
const CSRF      = '{{ csrf_token() }}';
let guruPage = 1, guruPerPage = 10, guruSearch = '', guruTimer = null, deleteGuruId = null;

const JABATAN_BADGE = {
    'kepala_sekolah':       'bg-danger',
    'wakil_kepala_sekolah': 'bg-warning text-dark',
    'guru':                 'bg-primary',
    'wali_kelas':           'bg-info text-dark',
    'kepala_jurusan':       'bg-success',
    'bendahara':            'bg-secondary',
    'tata_usaha':           'bg-dark',
};

function loadGuru() {
    $('#guruTableBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: GURU_DATA,
        data: { page: guruPage, per_page: guruPerPage, search: guruSearch },
        success: function(res) {
            if (!res.success) return;
            renderGuru(res.data, res.meta);
        },
        error: function() {
            $('#guruTableBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
        }
    });
}

function renderGuru(data, meta) {
    if (!data.length) {
        $('#guruTableBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data guru.</td></tr>');
        $('#guruInfo').text(''); $('#guruPagination').html(''); return;
    }

    let html = '';
    data.forEach(function(g, i) {
        let badge = JABATAN_BADGE[g.jabatan] || 'bg-secondary';
        let avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
        html += `<tr>
            <td class="text-muted">${meta.from + i}</td>
            <td class="text-center">
                <img src="${g.foto || avatar}" class="rounded-circle"
                    width="36" height="36" style="object-fit:cover"
                    onerror="this.src='${avatar}'">
            </td>
            <td class="font-monospace small">${g.nip}</td>
            <td class="fw-semibold">${g.nama}</td>
            <td><span class="badge ${badge}">${g.jabatan_label}</span></td>
            <td class="text-muted small">${g.mata_pelajaran || '-'}</td>
            <td class="text-center">
                <span class="badge ${g.jenis_kelamin == 'L' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger'}">${g.jenis_kelamin}</span>
            </td>
            <td class="small">${g.email}</td>
            <td class="text-center" style="white-space:nowrap">
                <button class="btn btn-sm btn-soft-info me-1" onclick="showGuru(${g.id})"><i class="bx bx-show"></i></button>
                <button class="btn btn-sm btn-soft-warning me-1" onclick="editGuru(${g.id})"><i class="bx bx-edit"></i></button>
                <button class="btn btn-sm btn-soft-danger" onclick="confirmDeleteGuru(${g.id},'${g.nama.replace(/'/g,"\\'")}','${g.nip}')"><i class="bx bx-trash"></i></button>
            </td>
        </tr>`;
    });
    $('#guruTableBody').html(html);

    let to = Math.min(meta.from + meta.per_page - 1, meta.total);
    $('#guruInfo').text(`Menampilkan ${meta.from}–${meta.to} dari ${meta.total} data`);
    renderPaginationGuru(meta.current_page, meta.last_page);
}

function renderPaginationGuru(c, l) {
    if (l <= 1) { $('#guruPagination').html(''); return; }
    let h = `<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return guruGoPage(${c-1})">‹</a></li>`;
    for (let p = 1; p <= l; p++) {
        if (p===1||p===l||(p>=c-1&&p<=c+1)) h += `<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return guruGoPage(${p})">${p}</a></li>`;
        else if (p===c-2||p===c+2) h += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    h += `<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return guruGoPage(${c+1})">›</a></li>`;
    $('#guruPagination').html(h);
}
function guruGoPage(p) { guruPage = p; loadGuru(); return false; }

let gst;
$('#searchGuru').on('input', function() { clearTimeout(gst); gst = setTimeout(function(){ guruSearch=$('#searchGuru').val(); guruPage=1; loadGuru(); }, 400); });
$('#perPageGuru').on('change', function() { guruPerPage=$(this).val(); guruPage=1; loadGuru(); });

function showGuru(id) {
    $.get(`${GURU_URL}/${id}`, function(res) {
        if (!res.success) return;
        const d = res.data;
        let avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
        $('#show_guru_foto').attr('src', d.foto || avatar);
        $('#show_guru_nama').text(d.nama);
        $('#show_guru_nip').text(d.nip);
        $('#show_guru_jabatan').text(d.jabatan_label);
        $('#show_guru_mapel').text(d.mata_pelajaran || '-');
        $('#show_guru_jk').text(d.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#show_guru_tgl_lahir').text(d.tanggal_lahir_format || '-');
        $('#show_guru_alamat').text(d.alamat || '-');
        $('#show_guru_no_hp').text(d.no_hp || '-');
        $('#show_guru_email').text(d.email);
        $('#show_guru_created_at').text(d.created_at);
        $('#show_guru_updated_at').text(d.updated_at);
        $('#showGuruModal').modal('show');
    });
}

function editGuru(id) {
    $.get(`${GURU_URL}/${id}`, function(res) {
        if (!res.success) return;
        const d = res.data;
        let avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
        $('#edit_guru_id').val(id);
        $('#edit_guru_nip').val(d.nip);
        $('#edit_guru_nama').val(d.nama);
        $('#edit_guru_jabatan').val(d.jabatan);
        $('#edit_guru_mapel').val(d.mata_pelajaran || '');
        $('#edit_guru_tgl_lahir').val(d.tanggal_lahir || '');
        $('#edit_guru_alamat').val(d.alamat || '');
        $('#edit_guru_no_hp').val(d.no_hp || '');
        $('#edit_guru_email').val(d.email);
        $('#edit_guru_password').val('');
        $(`input[name="jenis_kelamin_edit"][value="${d.jenis_kelamin}"]`).prop('checked', true);
        let fotoSrc = d.foto || avatar;
        $('#edit_guru_foto_preview').attr('src', fotoSrc).removeClass('d-none');
        $('#editGuruModal').modal('show');
    });
}

function confirmDeleteGuru(id, nama, nip) {
    deleteGuruId = id;
    $('#delete_guru_name').text(nama);
    $('#delete_guru_nip').text(nip);
    $('#deleteGuruModal').modal('show');
}

$('#confirmDeleteGuruBtn').on('click', function() {
    if (!deleteGuruId) return;
    $(this).prop('disabled', true); $('#btnDeleteGuruLoader').removeClass('d-none');
    $.ajax({
        url: `${GURU_URL}/${deleteGuruId}`, method: 'POST',
        data: { _method: 'DELETE', _token: CSRF },
        success: function(res) {
            $('#deleteGuruModal').modal('hide');
            loadGuru();
            showToastGuru(res.message, res.success ? 'success' : 'danger');
        },
        error: function() { showToastGuru('Gagal menghapus.', 'danger'); },
        complete: function() { $('#confirmDeleteGuruBtn').prop('disabled', false); $('#btnDeleteGuruLoader').addClass('d-none'); deleteGuruId = null; }
    });
});

$('#addGuruForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnSubmitAddGuru').prop('disabled', true); $('#btnLoaderAddGuru').removeClass('d-none');
    $('#addGuruError').addClass('d-none');
    $.ajax({
        url: GURU_URL, method: 'POST', data: new FormData(this),
        processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                $('#addGuruModal').modal('hide');
                $('#addGuruForm')[0].reset();
                $('#add_guru_foto_preview').addClass('d-none');
                loadGuru();
                showToastGuru('Guru berhasil ditambahkan!', 'success');
            } else {
                $('#addGuruError').text(res.message).removeClass('d-none');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#addGuruError').text(e ? Object.values(e).flat().join(', ') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitAddGuru').prop('disabled', false); $('#btnLoaderAddGuru').addClass('d-none'); }
    });
});

$('#editGuruForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_guru_id').val();
    let fd = new FormData(this); fd.append('_method', 'PUT');
    $('#btnSubmitEditGuru').prop('disabled', true); $('#btnLoaderEditGuru').removeClass('d-none');
    $('#editGuruError').addClass('d-none');
    $.ajax({
        url: `${GURU_URL}/${id}`, method: 'POST', data: fd,
        processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                $('#editGuruModal').modal('hide');
                loadGuru();
                showToastGuru('Data guru berhasil diupdate!', 'success');
            } else {
                $('#editGuruError').text(res.message).removeClass('d-none');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#editGuruError').text(e ? Object.values(e).flat().join(', ') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitEditGuru').prop('disabled', false); $('#btnLoaderEditGuru').addClass('d-none'); }
    });
});

function showToastGuru(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto"></button></div></div></div>`);
    $('body').append(t); setTimeout(() => t.remove(), 3500);
}

$(document).ready(function() {
    loadGuru();
    // ✅ Fix tombol tambah — bind di ready
    $('#btnBukaTambahGuru').on('click', function() {
        let form = document.getElementById('addGuruForm');
        if (form) form.reset();
        $('#addGuruError').addClass('d-none');
        $('#add_guru_foto_preview').addClass('d-none');
        $('#addGuruModal').modal('show');
    });
});
</script>
@endpush