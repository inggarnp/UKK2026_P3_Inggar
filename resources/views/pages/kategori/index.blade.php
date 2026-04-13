@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Kelola Kategori</h4>
        <p class="text-muted mb-0">Kategori pengaduan sarana sekolah</p>
    </div>
    <button class="btn btn-primary btn-sm" id="btnBukaTambahKategori">
        <i class="bx bx-plus me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 text-muted small">Tampilkan</label>
                <select id="perPageKat" class="form-select form-select-sm" style="width:80px">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="mb-0 text-muted small">data</label>
            </div>
            <div class="input-group input-group-sm" style="width:260px">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text" id="searchKat" class="form-control" placeholder="Cari kategori...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center" width="15%">Jml Aspirasi</th>
                        <th class="text-center" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="katTableBody">
                    <tr><td colspan="6" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="ms-2 text-muted">Memuat...</span>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <div id="katInfo" class="text-muted small"></div>
            <nav><ul class="pagination pagination-sm mb-0" id="katPagination"></ul></nav>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="addKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-category me-2"></i>Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addKatForm">
                @csrf
                <div class="modal-body">
                    <div id="addKatError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori"
                            placeholder="Contoh: Kebersihan, Fasilitas Kelas" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2"
                            placeholder="Keterangan singkat kategori ini" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnAddKat">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="loaderAddKat" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editKatForm">
                @csrf
                <input type="hidden" id="edit_kat_id">
                <div class="modal-body">
                    <div id="editKatError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_kat_nama"
                            name="nama_kategori" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_kat_deskripsi"
                            name="deskripsi" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnEditKat">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="loaderEditKat" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="deleteKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-error-circle me-2"></i>Hapus Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                <p class="mt-2 mb-2">Hapus kategori <strong id="delete_kat_nama">-</strong>?</p>
                <div class="alert alert-warning mb-0 text-start">
                    <i class="bx bx-error-circle me-1"></i>
                    Kategori yang sudah digunakan aspirasi tidak dapat dihapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteKatBtn">
                    Ya, Hapus
                    <span id="loaderDeleteKat" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let katPage = 1, katPer = 10, katSearch = '', katTimer = null, deleteKatId = null;
const KAT_BASE = '{{ url("admin/kategori") }}';
const KAT_DATA = '{{ route("admin.kategori.data") }}';

function loadKat() {
    $('#katTableBody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat...</span></td></tr>');

    $.ajax({
        url: KAT_DATA,
        data: { page: katPage, per_page: katPer, search: katSearch },
        success: function(res) {
            if (!res.success) return;
            const { data, meta } = res;

            if (!data.length) {
                $('#katTableBody').html('<tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori.</td></tr>');
                $('#katInfo').text(''); $('#katPagination').html(''); return;
            }

            let html = '';
            data.forEach(function(k, i) {
                html += `<tr>
                    <td class="text-muted">${meta.from + i}</td>
                    <td class="fw-semibold">${k.nama_kategori}</td>
                    <td class="text-muted small">${k.deskripsi}</td>
                    <td class="text-center">
                        <span class="badge bg-soft-info text-info">${k.jumlah_aspirasi} aspirasi</span>
                    </td>
                    <td class="text-center" style="white-space:nowrap">
                        <button class="btn btn-sm btn-soft-warning me-1"
                            onclick="editKat(${k.id}, '${k.nama_kategori.replace(/'/g,"\\'")}', '${(k.deskripsi||'').replace(/'/g,"\\'")}')">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-soft-danger"
                            onclick="confirmDeleteKat(${k.id}, '${k.nama_kategori.replace(/'/g,"\\'")}', ${k.jumlah_aspirasi})">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#katTableBody').html(html);
            $('#katInfo').text(`Menampilkan ${meta.from}–${meta.to} dari ${meta.total} kategori`);
            renderPaginationKat(meta.current_page, meta.last_page);
        },
        error: function() {
            $('#katTableBody').html('<tr><td colspan="6" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
        }
    });
}

function renderPaginationKat(c, l) {
    if (l <= 1) { $('#katPagination').html(''); return; }
    let h = `<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return katGoPage(${c-1})">‹</a></li>`;
    for (let p = 1; p <= l; p++) {
        if (p===1||p===l||(p>=c-1&&p<=c+1)) h += `<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return katGoPage(${p})">${p}</a></li>`;
        else if (p===c-2||p===c+2) h += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    h += `<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return katGoPage(${c+1})">›</a></li>`;
    $('#katPagination').html(h);
}
function katGoPage(p) { katPage = p; loadKat(); return false; }

function editKat(id, nama, deskripsi) {
    $('#edit_kat_id').val(id);
    $('#edit_kat_nama').val(nama);
    $('#edit_kat_deskripsi').val(deskripsi);
    $('#editKatError').addClass('d-none');
    $('#editKategoriModal').modal('show');
}

function confirmDeleteKat(id, nama, jumlah) {
    if (jumlah > 0) {
        showToastKat(`Kategori "${nama}" tidak bisa dihapus, masih digunakan ${jumlah} aspirasi!`, 'danger');
        return;
    }
    deleteKatId = id;
    $('#delete_kat_nama').text(nama);
    $('#deleteKategoriModal').modal('show');
}

$('#addKatForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnAddKat').prop('disabled', true);
    $('#loaderAddKat').removeClass('d-none');
    $('#addKatError').addClass('d-none');
    $.ajax({
        url: KAT_BASE, method: 'POST', data: $(this).serialize(),
        success: function(res) {
            if (res.success) {
                $('#addKategoriModal').modal('hide');
                $('#addKatForm')[0].reset();
                loadKat();
                showToastKat('Kategori berhasil ditambahkan!', 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON;
            $('#addKatError').text(e?.message || 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnAddKat').prop('disabled', false); $('#loaderAddKat').addClass('d-none'); }
    });
});

$('#editKatForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_kat_id').val();
    $('#btnEditKat').prop('disabled', true);
    $('#loaderEditKat').removeClass('d-none');
    $('#editKatError').addClass('d-none');
    $.ajax({
        url: `${KAT_BASE}/${id}`, method: 'POST', data: $(this).serialize() + '&_method=PUT',
        success: function(res) {
            if (res.success) {
                $('#editKategoriModal').modal('hide');
                loadKat();
                showToastKat('Kategori berhasil diupdate!', 'success');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON;
            $('#editKatError').text(e?.message || 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnEditKat').prop('disabled', false); $('#loaderEditKat').addClass('d-none'); }
    });
});

$('#confirmDeleteKatBtn').on('click', function() {
    if (!deleteKatId) return;
    $(this).prop('disabled', true); $('#loaderDeleteKat').removeClass('d-none');
    $.ajax({
        url: `${KAT_BASE}/${deleteKatId}`, method: 'POST', data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res) {
            $('#deleteKategoriModal').modal('hide');
            loadKat();
            showToastKat(res.message, res.success ? 'success' : 'danger');
        },
        error: function(xhr) { showToastKat(xhr.responseJSON?.message || 'Gagal menghapus.', 'danger'); },
        complete: function() { $('#confirmDeleteKatBtn').prop('disabled', false); $('#loaderDeleteKat').addClass('d-none'); deleteKatId = null; }
    });
});

function showToastKat(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0">
            <div class="d-flex"><div class="toast-body">${msg}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
            </div></div></div>`);
    $('body').append(t);
    setTimeout(() => t.remove(), 3500);
}

let st;
$('#searchKat').on('input', function() { clearTimeout(st); st = setTimeout(function(){ katSearch=$('#searchKat').val(); katPage=1; loadKat(); }, 400); });
$('#perPageKat').on('change', function() { katPer=$(this).val(); katPage=1; loadKat(); });

$(document).ready(function() {
    loadKat();
    // ✅ Fix tombol tambah
    $('#btnBukaTambahKategori').on('click', function() {
        $('#addKatForm')[0].reset();
        $('#addKatError').addClass('d-none');
        $('#addKategoriModal').modal('show');
    });
});
</script>
@endpush