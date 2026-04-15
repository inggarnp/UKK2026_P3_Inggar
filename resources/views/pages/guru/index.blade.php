@extends('layouts.app')
@section('title', 'Data Guru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Data Guru</h4>
        <p class="text-muted mb-0">Kelola data guru dan akun login mereka</p>
    </div>
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
                <tbody id="guruTableBody"></tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <div id="guruInfo" class="text-muted small"></div>
            <ul class="pagination pagination-sm mb-0" id="guruPagination"></ul>
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
// ─── KONSTANTA URL ──────────────────────────────────────────────
const GURU_URL        = '{{ route('admin.guru.index') }}';
const GURU_DATA       = '{{ route('admin.guru.data') }}';
const KELAS_AVAILABLE = '{{ route('admin.kelas.available') }}';
const CSRF            = '{{ csrf_token() }}';

let guruPage    = 1,
    guruPerPage = 10,
    guruSearch  = '',
    deleteGuruId = null;

const JABATAN_BADGE = {
    'kepala_sekolah':       'bg-danger',
    'wakil_kepala_sekolah': 'bg-warning text-dark',
    'guru':                 'bg-primary',
    'wali_kelas':           'bg-info text-dark',
    'kepala_jurusan':       'bg-success',
    'bendahara':            'bg-secondary',
    'tata_usaha':           'bg-dark',
};

// ================= UTILITY =================
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bx-hide', 'bx-show');
    } else {
        input.type = 'password';
        icon.classList.replace('bx-show', 'bx-hide');
    }
}

function previewFoto(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ================= LOAD DATA TABEL =================
function loadGuru() {
    $('#guruTableBody').html(`<tr><td colspan="9" class="text-center py-4">
        <div class="spinner-border spinner-border-sm text-primary"></div>
        <span class="ms-2 text-muted">Memuat data...</span></td></tr>`);

    $.get(GURU_DATA, {
        page:     guruPage,
        per_page: guruPerPage,
        search:   guruSearch
    }, function(res) {
        renderGuru(res.data, res.meta);
        renderPagination(res.meta);
        $('#guruInfo').text(`Menampilkan ${res.meta.from}–${res.meta.to} dari ${res.meta.total} data`);
    });
}

function renderGuru(data, meta) {
    if (!data.length) {
        $('#guruTableBody').html(`<tr><td colspan="9" class="text-center text-muted py-4">
            Tidak ada data guru.</td></tr>`);
        return;
    }

    let html = '';
    data.forEach((g, i) => {
        const badge = JABATAN_BADGE[g.jabatan] || 'bg-secondary';
        html += `<tr>
            <td>${meta.from + i}</td>
            <td><img src="${g.foto}" class="rounded-circle" width="36" height="36" style="object-fit:cover"></td>
            <td class="font-monospace small">${g.nip}</td>
            <td class="fw-semibold">${g.nama}</td>
            <td><span class="badge ${badge}">${g.jabatan_label}</span></td>
            <td>${g.mata_pelajaran || '-'}</td>
            <td class="text-center">${g.jenis_kelamin}</td>
            <td>${g.email}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-soft-info"    onclick="showGuru(${g.id})"          title="Detail"><i class="bx bx-show"></i></button>
                <button class="btn btn-sm btn-soft-warning" onclick="editGuru(${g.id})"          title="Edit"><i class="bx bx-edit"></i></button>
                <button class="btn btn-sm btn-soft-danger"  onclick="confirmDeleteGuru(${g.id})" title="Hapus"><i class="bx bx-trash"></i></button>
            </td>
        </tr>`;
    });

    $('#guruTableBody').html(html);
}

function renderPagination(meta) {
    let html = '';
    for (let p = 1; p <= meta.last_page; p++) {
        html += `<li class="page-item ${p === meta.current_page ? 'active' : ''}">
            <button class="page-link" onclick="goPageGuru(${p})">${p}</button>
        </li>`;
    }
    $('#guruPagination').html(html);
}

function goPageGuru(p) {
    guruPage = p;
    loadGuru();
}

// ================= KELAS AVAILABLE DROPDOWN =================
/**
 * Load dropdown kelas yang available (belum ada wali kelas)
 * @param {string} selectId   - selector jQuery target dropdown
 * @param {number|null} selected   - kelas_id yang ingin dipilih default
 * @param {number|null} guruId     - id guru saat edit (agar kelasnya sendiri tetap muncul)
 */
function loadKelasDropdown(selectId, selected = null, guruId = null) {
    const $select = $(selectId);
    $select.html('<option value="">Memuat kelas...</option>').prop('disabled', true);

    const params = {};
    if (guruId) params.current_guru_id = guruId;

    $.get(KELAS_AVAILABLE, params, function(res) {
        if (!res.length) {
            $select.html('<option value="">-- Tidak ada kelas tersedia --</option>').prop('disabled', false);
            return;
        }
        let html = '<option value="">-- Pilih Kelas --</option>';
        res.forEach(k => {
            html += `<option value="${k.id}">${k.nama}</option>`;
        });
        $select.html(html).prop('disabled', false);
        if (selected) $select.val(selected);
    }).fail(function() {
        $select.html('<option value="">Gagal memuat kelas</option>').prop('disabled', false);
    });
}

// ================= CREATE (TAMBAH GURU) =================
$('#btnBukaTambahGuru').on('click', function() {
    // Reset form
    $('#addGuruForm')[0].reset();
    $('#add_guru_foto_preview').addClass('d-none').attr('src', '');
    $('#waliKelasWrapper').addClass('d-none');
    $('#kelas_id').html('<option value="">-- Pilih Kelas --</option>');
    $('#addGuruError').addClass('d-none').text('');
    $('#addGuruModal').modal('show');
});

// Saat jabatan CREATE berubah
$('#addGuruForm select[name="jabatan"]').on('change', function() {
    if ($(this).val() === 'wali_kelas') {
        $('#waliKelasWrapper').removeClass('d-none');
        // Tambah baru: tidak kirim current_guru_id, hanya kelas kosong
        loadKelasDropdown('#kelas_id', null, null);
    } else {
        $('#waliKelasWrapper').addClass('d-none');
        $('#kelas_id').val('');
    }
});

// Submit tambah guru
$('#addGuruForm').on('submit', function(e) {
    e.preventDefault();
    const $btn    = $('#btnSubmitAddGuru');
    const $loader = $('#btnLoaderAddGuru');
    $btn.prop('disabled', true);
    $loader.removeClass('d-none');
    $('#addGuruError').addClass('d-none').text('');

    const formData = new FormData(this);

    $.ajax({
        url:         '{{ route('admin.guru.store') }}',
        method:      'POST',
        data:        formData,
        contentType: false,
        processData: false,
        headers:     { 'X-CSRF-TOKEN': CSRF },
        success: function(res) {
            if (res.success) {
                $('#addGuruModal').modal('hide');
                loadGuru();
                // Toast / alert sukses (opsional)
                alert(res.message);
            } else {
                $('#addGuruError').removeClass('d-none').text(res.message || 'Terjadi kesalahan.');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                const msg = Object.values(errors).flat().join('\n');
                $('#addGuruError').removeClass('d-none').text(msg);
            } else {
                $('#addGuruError').removeClass('d-none').text(xhr.responseJSON?.message || 'Terjadi kesalahan.');
            }
        },
        complete: function() {
            $btn.prop('disabled', false);
            $loader.addClass('d-none');
        }
    });
});

// ================= READ (DETAIL GURU) =================
function showGuru(id) {
    $('#showGuruModal').modal('show');

    $.get(`${GURU_URL}/${id}`, function(res) {
        const d = res.data;
        $('#show_guru_foto').attr('src', d.foto);
        $('#show_guru_nama').text(d.nama);
        $('#show_guru_nip').text(d.nip);
        $('#show_guru_jabatan_badge').text(d.jabatan_label)
            .removeClass().addClass('badge ' + (JABATAN_BADGE[d.jabatan] || 'bg-secondary'));
        $('#show_guru_jabatan').text(d.jabatan_label);
        $('#show_guru_mapel').text(d.mata_pelajaran || '-');
        $('#show_guru_jk').text(d.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#show_guru_tgl_lahir').text(d.tanggal_lahir_format || '-');
        $('#show_guru_alamat').text(d.alamat || '-');
        $('#show_guru_no_hp').text(d.no_hp || '-');
        $('#show_guru_email').text(d.email);
        $('#show_guru_created_at').text(d.created_at);
        $('#show_guru_updated_at').text(d.updated_at);
    });
}

// ================= UPDATE (EDIT GURU) =================
function editGuru(id) {
    // Reset form edit dulu
    $('#editGuruForm')[0].reset();
    $('#edit_guru_foto_preview').addClass('d-none').attr('src', '');
    $('#editWaliKelasWrapper').addClass('d-none');
    $('#edit_kelas_id').html('<option value="">-- Pilih Kelas --</option>');
    $('#editGuruError').addClass('d-none').text('');

    $.get(`${GURU_URL}/${id}`, function(res) {
        const d = res.data;

        $('#edit_guru_id').val(d.id);
        $('#edit_guru_nip').val(d.nip);
        $('#edit_guru_nama').val(d.nama);
        $('#edit_guru_jabatan').val(d.jabatan);
        $('#edit_guru_mapel').val(d.mata_pelajaran);
        // Jenis kelamin
        $(`#editGuruForm input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`).prop('checked', true);
        $('#edit_guru_tgl_lahir').val(d.tanggal_lahir || '');
        $('#edit_guru_alamat').val(d.alamat);
        $('#edit_guru_no_hp').val(d.no_hp);
        $('#edit_guru_email').val(d.email);

        // Foto preview
        if (d.foto) {
            $('#edit_guru_foto_preview').attr('src', d.foto).removeClass('d-none');
        }

        // Wali kelas: tampilkan dropdown dan pre-select kelas yang sudah dipegang
        if (d.jabatan === 'wali_kelas') {
            $('#editWaliKelasWrapper').removeClass('d-none');
            // Kirim current_guru_id agar kelas milik guru ini tetap muncul
            loadKelasDropdown('#edit_kelas_id', d.kelas_id, d.id);
        }

        $('#editGuruModal').modal('show');
    });
}

// Saat jabatan EDIT berubah
$('#edit_guru_jabatan').on('change', function() {
    const guruId = $('#edit_guru_id').val();
    if ($(this).val() === 'wali_kelas') {
        $('#editWaliKelasWrapper').removeClass('d-none');
        loadKelasDropdown('#edit_kelas_id', null, guruId || null);
    } else {
        $('#editWaliKelasWrapper').addClass('d-none');
        $('#edit_kelas_id').val('');
    }
});

// Submit edit guru
$('#editGuruForm').on('submit', function(e) {
    e.preventDefault();
    const guruId  = $('#edit_guru_id').val();
    const $btn    = $('#btnSubmitEditGuru');
    const $loader = $('#btnLoaderEditGuru');
    $btn.prop('disabled', true);
    $loader.removeClass('d-none');
    $('#editGuruError').addClass('d-none').text('');

    const formData = new FormData(this);
    // Laravel tidak mendukung PUT dengan FormData, gunakan _method spoofing
    formData.append('_method', 'PUT');

    $.ajax({
        url:         `${GURU_URL}/${guruId}`,
        method:      'POST',
        data:        formData,
        contentType: false,
        processData: false,
        headers:     { 'X-CSRF-TOKEN': CSRF },
        success: function(res) {
            if (res.success) {
                $('#editGuruModal').modal('hide');
                loadGuru();
                alert(res.message);
            } else {
                $('#editGuruError').removeClass('d-none').text(res.message || 'Terjadi kesalahan.');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                const msg = Object.values(errors).flat().join('\n');
                $('#editGuruError').removeClass('d-none').text(msg);
            } else {
                $('#editGuruError').removeClass('d-none').text(xhr.responseJSON?.message || 'Terjadi kesalahan.');
            }
        },
        complete: function() {
            $btn.prop('disabled', false);
            $loader.addClass('d-none');
        }
    });
});

// ================= DELETE =================
function confirmDeleteGuru(id) {
    deleteGuruId = id;

    // Ambil data guru untuk ditampilkan di modal konfirmasi
    $.get(`${GURU_URL}/${id}`, function(res) {
        const d = res.data;
        $('#delete_guru_name').text(d.nama);
        $('#delete_guru_nip').text(d.nip);
        $('#deleteGuruModal').modal('show');
    });
}

$('#confirmDeleteGuruBtn').on('click', function() {
    if (!deleteGuruId) return;
    const $btn    = $(this);
    const $loader = $('#btnDeleteGuruLoader');
    $btn.prop('disabled', true);
    $loader.removeClass('d-none');

    $.ajax({
        url:     `${GURU_URL}/${deleteGuruId}`,
        method:  'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF },
        success: function(res) {
            $('#deleteGuruModal').modal('hide');
            deleteGuruId = null;
            loadGuru();
            alert(res.message);
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Gagal menghapus guru.');
        },
        complete: function() {
            $btn.prop('disabled', false);
            $loader.addClass('d-none');
        }
    });
});

// ================= SEARCH & PER PAGE =================
let searchTimeout;
$('#searchGuru').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        guruSearch = $('#searchGuru').val();
        guruPage   = 1;
        loadGuru();
    }, 400);
});

$('#perPageGuru').on('change', function() {
    guruPerPage = $(this).val();
    guruPage    = 1;
    loadGuru();
});

// ================= INIT =================
$(document).ready(function() {
    loadGuru();
});
</script>
@endpush