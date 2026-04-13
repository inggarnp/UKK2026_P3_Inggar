@extends('layouts.app')
@section('title', 'Data Petugas Sarana')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Data Petugas Sarana</h4>
        <p class="text-muted mb-0">Kelola data petugas sarana dan akun login mereka</p>
    </div>
    <button class="btn btn-primary btn-sm" id="btnBukaTambahPetugas">
        <i class="bx bx-plus me-1"></i> Tambah Petugas
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 text-muted small">Tampilkan</label>
                <select id="perPagePetugas" class="form-select form-select-sm" style="width:80px">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="mb-0 text-muted small">data</label>
            </div>
            <div class="d-flex gap-2">
                <select id="filterStatus" class="form-select form-select-sm" style="width:130px">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <div class="input-group input-group-sm" style="width:240px">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchPetugas" class="form-control" placeholder="Cari nama, NIP...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="6%">Foto</th>
                        <th width="15%">NIP</th>
                        <th>Nama</th>
                        <th width="6%" class="text-center">J.K</th>
                        <th width="14%">No. HP</th>
                        <th width="10%" class="text-center">Status</th>
                        <th>Email</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="petugasTableBody">
                    <tr><td colspan="9" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="ms-2 text-muted">Memuat data...</span>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <div id="petugasInfo" class="text-muted small"></div>
            <nav><ul class="pagination pagination-sm mb-0" id="petugasPagination"></ul></nav>
        </div>
    </div>
</div>

{{-- ============ MODAL TAMBAH ============ --}}
<div class="modal fade" id="addPetugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user-plus me-2"></i>Tambah Petugas Sarana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPetugasForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="addPetugasError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Petugas</h6>
                            <div class="mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nip" placeholder="Nomor Induk (opsional)" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama lengkap petugas" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="add_p_jk_l" value="L" required>
                                        <label class="form-check-label" for="add_p_jk_l">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="add_p_jk_p" value="P">
                                        <label class="form-check-label" for="add_p_jk_p">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp" placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="aktif" selected>Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2"><i class="bx bx-lock-alt me-1"></i>Akun Login</h6>
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Akun ini digunakan petugas untuk login dan mengelola aspirasi.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="email@sekolah.sch.id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="add_petugas_password" placeholder="Minimal 6 karakter" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('add_petugas_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2"><i class="bx bx-image me-1"></i>Foto Profil</h6>
                            <div class="mb-3">
                                <input type="file" class="form-control" name="foto" id="add_petugas_foto"
                                    accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'add_petugas_foto_preview')">
                                <small class="text-muted">JPG, PNG — Maks 2MB. Opsional.</small>
                            </div>
                            <img id="add_petugas_foto_preview" src="" class="rounded d-none"
                                style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddPetugas">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddPetugas" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL DETAIL ============ --}}
<div class="modal fade" id="showPetugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user me-2"></i>Detail Petugas Sarana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center border-end">
                        <img id="show_petugas_foto" src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                            class="rounded-circle mb-3"
                            style="width:110px;height:110px;object-fit:cover;border:3px solid #dee2e6">
                        <p id="show_petugas_nama" class="fw-bold mb-1">-</p>
                        <p id="show_petugas_nip" class="text-muted small font-monospace mb-2">-</p>
                        <span id="show_petugas_status_badge" class="badge bg-success">-</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Jenis Kelamin</label>
                                <p id="show_petugas_jk" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Tanggal Lahir</label>
                                <p id="show_petugas_tgl_lahir" class="mb-0">-</p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted">Alamat</label>
                                <p id="show_petugas_alamat" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">No. HP</label>
                                <p id="show_petugas_no_hp" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <p id="show_petugas_email" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-1">
                                <label class="form-label fw-bold small text-muted">Dibuat</label>
                                <p id="show_petugas_created_at" class="mb-0 text-muted small">-</p>
                            </div>
                            <div class="col-6 mb-1">
                                <label class="form-label fw-bold small text-muted">Diupdate</label>
                                <p id="show_petugas_updated_at" class="mb-0 text-muted small">-</p>
                            </div>
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

{{-- ============ MODAL EDIT ============ --}}
<div class="modal fade" id="editPetugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Petugas Sarana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPetugasForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_petugas_id">
                <div class="modal-body">
                    <div id="editPetugasError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Petugas</h6>
                            <div class="mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" id="edit_petugas_nip" name="nip" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_petugas_nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="edit_p_jk_l" value="L" required>
                                        <label class="form-check-label" for="edit_p_jk_l">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="edit_p_jk_p" value="P">
                                        <label class="form-check-label" for="edit_p_jk_p">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="edit_petugas_tgl_lahir" name="tanggal_lahir">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" id="edit_petugas_alamat" name="alamat" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="edit_petugas_no_hp" name="no_hp" maxlength="15">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_petugas_status" name="status" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2"><i class="bx bx-lock-alt me-1"></i>Akun Login</h6>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_petugas_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="edit_petugas_password" name="password" placeholder="Kosongkan jika tidak ingin ubah">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('edit_petugas_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2"><i class="bx bx-image me-1"></i>Foto</h6>
                            <img id="edit_petugas_foto_preview" src="" class="rounded d-none mb-2"
                                style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="foto" id="edit_petugas_foto"
                                    accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'edit_petugas_foto_preview')">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditPetugas">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditPetugas" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL HAPUS ============ --}}
<div class="modal fade" id="deletePetugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-error-circle me-2"></i>Hapus Petugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                <p class="mt-2 mb-2">Hapus petugas <strong id="delete_petugas_name">-</strong>?</p>
                <div class="alert alert-warning mb-0 text-start">
                    <i class="bx bx-error-circle me-1"></i>
                    Akun login petugas ini juga akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePetugasBtn">
                    Ya, Hapus
                    <span id="btnDeletePetugasLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const PETUGAS_URL  = '{{ route("admin.petugas.index") }}';
const PETUGAS_DATA = '{{ route("admin.petugas.data") }}';
const CSRF_P       = '{{ csrf_token() }}';
let pPage = 1, pPerPage = 10, pSearch = '', pStatus = '', pTimer = null, deletePId = null;

function loadPetugas() {
    $('#petugasTableBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat...</span></td></tr>');
    $.ajax({
        url: PETUGAS_DATA,
        data: { page: pPage, per_page: pPerPage, search: pSearch, status: pStatus },
        success: function(res) {
            if (!res.success) return;
            const { data, meta } = res;
            if (!data.length) {
                $('#petugasTableBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data petugas.</td></tr>');
                $('#petugasInfo').text(''); $('#petugasPagination').html(''); return;
            }
            let html = '', avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
            data.forEach(function(p, i) {
                let statusBadge = p.status === 'aktif' ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary';
                html += `<tr>
                    <td class="text-muted">${meta.from + i}</td>
                    <td class="text-center">
                        <img src="${p.foto || avatar}" class="rounded-circle" width="36" height="36"
                            style="object-fit:cover" onerror="this.src='${avatar}'">
                    </td>
                    <td class="font-monospace small">${p.nip || '-'}</td>
                    <td class="fw-semibold">${p.nama}</td>
                    <td class="text-center">
                        <span class="badge ${p.jenis_kelamin=='L'?'bg-soft-primary text-primary':'bg-soft-danger text-danger'}">${p.jenis_kelamin}</span>
                    </td>
                    <td class="small">${p.no_hp || '-'}</td>
                    <td class="text-center"><span class="badge ${statusBadge}">${p.status_label}</span></td>
                    <td class="small">${p.email}</td>
                    <td class="text-center" style="white-space:nowrap">
                        <button class="btn btn-sm btn-soft-info me-1" onclick="showPetugas(${p.id})"><i class="bx bx-show"></i></button>
                        <button class="btn btn-sm btn-soft-warning me-1" onclick="editPetugas(${p.id})"><i class="bx bx-edit"></i></button>
                        <button class="btn btn-sm btn-soft-danger" onclick="confirmDeletePetugas(${p.id},'${p.nama.replace(/'/g,"\\'")}')"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#petugasTableBody').html(html);
            $('#petugasInfo').text(`Menampilkan ${meta.from}–${meta.to} dari ${meta.total} petugas`);
            renderPagPetugas(meta.current_page, meta.last_page);
        },
        error: function() { $('#petugasTableBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data.</td></tr>'); }
    });
}

function renderPagPetugas(c, l) {
    if (l <= 1) { $('#petugasPagination').html(''); return; }
    let h = `<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return pGoPage(${c-1})">‹</a></li>`;
    for (let p = 1; p <= l; p++) {
        if (p===1||p===l||(p>=c-1&&p<=c+1)) h += `<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return pGoPage(${p})">${p}</a></li>`;
        else if (p===c-2||p===c+2) h += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    h += `<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return pGoPage(${c+1})">›</a></li>`;
    $('#petugasPagination').html(h);
}
function pGoPage(p) { pPage = p; loadPetugas(); return false; }

let pst;
$('#searchPetugas').on('input', function() { clearTimeout(pst); pst = setTimeout(function(){ pSearch=$('#searchPetugas').val(); pPage=1; loadPetugas(); }, 400); });
$('#perPagePetugas').on('change', function() { pPerPage=$(this).val(); pPage=1; loadPetugas(); });
$('#filterStatus').on('change', function() { pStatus=$(this).val(); pPage=1; loadPetugas(); });

function showPetugas(id) {
    $.get(`${PETUGAS_URL}/${id}`, function(res) {
        if (!res.success) return;
        const d = res.data;
        let avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
        $('#show_petugas_foto').attr('src', d.foto || avatar);
        $('#show_petugas_nama').text(d.nama);
        $('#show_petugas_nip').text(d.nip || '-');
        $('#show_petugas_status_badge').text(d.status_label)
            .removeClass('bg-success bg-secondary')
            .addClass(d.status === 'aktif' ? 'bg-success' : 'bg-secondary');
        $('#show_petugas_jk').text(d.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#show_petugas_tgl_lahir').text(d.tanggal_lahir_format);
        $('#show_petugas_alamat').text(d.alamat || '-');
        $('#show_petugas_no_hp').text(d.no_hp || '-');
        $('#show_petugas_email').text(d.email);
        $('#show_petugas_created_at').text(d.created_at);
        $('#show_petugas_updated_at').text(d.updated_at);
        $('#showPetugasModal').modal('show');
    });
}

function editPetugas(id) {
    $.get(`${PETUGAS_URL}/${id}`, function(res) {
        if (!res.success) return;
        const d = res.data;
        let avatar = '{{ asset("assets/images/users/avatar-1.jpg") }}';
        $('#edit_petugas_id').val(id);
        $('#edit_petugas_nip').val(d.nip || '');
        $('#edit_petugas_nama').val(d.nama);
        $('#edit_petugas_tgl_lahir').val(d.tanggal_lahir || '');
        $('#edit_petugas_alamat').val(d.alamat || '');
        $('#edit_petugas_no_hp').val(d.no_hp || '');
        $('#edit_petugas_status').val(d.status);
        $('#edit_petugas_email').val(d.email);
        $('#edit_petugas_password').val('');
        $(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`, '#editPetugasForm').prop('checked', true);
        $('#edit_petugas_foto_preview').attr('src', d.foto || avatar).removeClass('d-none');
        $('#editPetugasModal').modal('show');
    });
}

function confirmDeletePetugas(id, nama) {
    deletePId = id;
    $('#delete_petugas_name').text(nama);
    $('#deletePetugasModal').modal('show');
}

$('#confirmDeletePetugasBtn').on('click', function() {
    if (!deletePId) return;
    $(this).prop('disabled', true); $('#btnDeletePetugasLoader').removeClass('d-none');
    $.ajax({
        url: `${PETUGAS_URL}/${deletePId}`, method: 'POST',
        data: { _method: 'DELETE', _token: CSRF_P },
        success: function(res) { $('#deletePetugasModal').modal('hide'); loadPetugas(); showToastP(res.message, 'success'); },
        error: function() { showToastP('Gagal menghapus.', 'danger'); },
        complete: function() { $('#confirmDeletePetugasBtn').prop('disabled', false); $('#btnDeletePetugasLoader').addClass('d-none'); deletePId = null; }
    });
});

$('#addPetugasForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnSubmitAddPetugas').prop('disabled', true); $('#btnLoaderAddPetugas').removeClass('d-none');
    $('#addPetugasError').addClass('d-none');
    $.ajax({
        url: PETUGAS_URL, method: 'POST', data: new FormData(this),
        processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                $('#addPetugasModal').modal('hide');
                $('#addPetugasForm')[0].reset();
                $('#add_petugas_foto_preview').addClass('d-none');
                loadPetugas();
                showToastP('Petugas berhasil ditambahkan!', 'success');
            } else {
                $('#addPetugasError').text(res.message).removeClass('d-none');
            }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#addPetugasError').text(e ? Object.values(e).flat().join(', ') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitAddPetugas').prop('disabled', false); $('#btnLoaderAddPetugas').addClass('d-none'); }
    });
});

$('#editPetugasForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_petugas_id').val();
    let fd = new FormData(this); fd.append('_method', 'PUT');
    $('#btnSubmitEditPetugas').prop('disabled', true); $('#btnLoaderEditPetugas').removeClass('d-none');
    $('#editPetugasError').addClass('d-none');
    $.ajax({
        url: `${PETUGAS_URL}/${id}`, method: 'POST', data: fd,
        processData: false, contentType: false,
        success: function(res) {
            if (res.success) { $('#editPetugasModal').modal('hide'); loadPetugas(); showToastP('Petugas berhasil diupdate!', 'success'); }
            else { $('#editPetugasError').text(res.message).removeClass('d-none'); }
        },
        error: function(xhr) {
            let e = xhr.responseJSON?.errors;
            $('#editPetugasError').text(e ? Object.values(e).flat().join(', ') : 'Terjadi kesalahan.').removeClass('d-none');
        },
        complete: function() { $('#btnSubmitEditPetugas').prop('disabled', false); $('#btnLoaderEditPetugas').addClass('d-none'); }
    });
});

function showToastP(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto"></button></div></div></div>`);
    $('body').append(t); setTimeout(() => t.remove(), 3500);
}

$(document).ready(function() {
    loadPetugas();
    $('#btnBukaTambahPetugas').on('click', function() {
        let form = document.getElementById('addPetugasForm');
        if (form) form.reset();
        $('#addPetugasError').addClass('d-none');
        $('#add_petugas_foto_preview').addClass('d-none');
        $('#addPetugasModal').modal('show');
    });
});
</script>
@endpush