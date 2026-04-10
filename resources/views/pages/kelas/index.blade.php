{{-- ============================================================
     resources/views/pages/kelas/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Data Kelas | Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Kelas</h4>
                <p class="text-muted mb-0">Kelola data kelas, jurusan, dan ruangan</p>
            </div>
            {{-- ✅ FIX: pakai onclick jQuery bukan data-bs-toggle --}}
            <button class="btn btn-primary btn-sm" onclick="$('#addKelasModal').modal('show')">
                <i class="bx bx-plus me-1"></i> Tambah Kelas
            </button>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari kelas, jurusan...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Ruangan</th>
                                <th>Tahun Ajaran</th>
                                <th class="text-center">Siswa</th>
                                <th width="13%" class="text-center">Aksi</th>
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

@include('pages.kelas.create')
@include('pages.kelas.read')
@include('pages.kelas.update')
@include('pages.kelas.delete')
@endsection

@push('scripts')
<script>
let currentPage = 1, perPage = 10, searchQuery = '', deleteId = null;

function loadData() {
    $('#tabelBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("admin.kelas.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery },
        success: renderTable,
        error: function() { $('#tabelBody').html('<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data.</td></tr>'); }
    });
}

function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data kelas.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';
    res.data.forEach(function(k, i) {
        let badge = {'X':'bg-soft-success text-success','XI':'bg-soft-warning text-warning','XII':'bg-soft-primary text-primary'}[k.tingkat] || 'bg-soft-secondary';
        html += `<tr>
            <td class="text-muted">${start+i}</td>
            <td class="fw-semibold">${k.nama_kelas}</td>
            <td><span class="badge ${badge}">${k.tingkat}</span></td>
            <td>${k.jurusan}</td>
            <td>${k.ruangan !== '-' ? `<span class="badge bg-soft-info text-info">${k.kode_ruangan}</span> ${k.ruangan}` : '<span class="text-muted small">-</span>'}</td>
            <td><span class="badge bg-light text-dark border">${k.tahun_ajaran}</span></td>
            <td class="text-center"><span class="badge bg-soft-dark text-dark">${k.jumlah_siswa} siswa</span></td>
            <td class="text-center" style="white-space:nowrap">
                <button class="btn btn-sm btn-soft-info me-1" onclick="showKelas(${k.id})"><i class="bx bx-show"></i></button>
                <button class="btn btn-sm btn-soft-warning me-1" onclick="editKelas(${k.id})"><i class="bx bx-edit"></i></button>
                <button class="btn btn-sm btn-soft-danger" onclick="confirmDelete(${k.id},'${k.nama_kelas}',${k.jumlah_siswa})"><i class="bx bx-trash"></i></button>
            </td>
        </tr>`;
    });
    $('#tabelBody').html(html);
    let from = start, to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${from}–${to} dari ${res.total} kelas`);
    renderPagination(res.current_page, res.last_page);
}

function renderPagination(current, last) {
    if (last <= 1) { $('#paginationLinks').html(''); return; }
    let html = `<li class="page-item ${current===1?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${current-1})">‹</a></li>`;
    for (let p = 1; p <= last; p++) {
        if (p===1||p===last||(p>=current-1&&p<=current+1)) html += `<li class="page-item ${p===current?'active':''}"><a class="page-link" href="#" onclick="return goPage(${p})">${p}</a></li>`;
        else if (p===current-2||p===current+2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    html += `<li class="page-item ${current===last?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${current+1})">›</a></li>`;
    $('#paginationLinks').html(html);
}
function goPage(p) { currentPage = p; loadData(); return false; }

let st;
$('#searchInput').on('input', function() { clearTimeout(st); st = setTimeout(function(){ searchQuery=$('#searchInput').val(); currentPage=1; loadData(); }, 400); });
$('#perPageSelect').on('change', function() { perPage=$(this).val(); currentPage=1; loadData(); });

function showKelas(id) {
    $.get('{{ url("admin/kelas") }}/' + id, function(k) {
        $('#show_nama_kelas').text(k.nama_kelas);
        $('#show_tingkat').text(k.tingkat);
        $('#show_jurusan').text(k.jurusan || '-');
        $('#show_ruangan').text(k.ruangan ? k.kode_ruangan + ' — ' + k.ruangan : '-');
        $('#show_tahun_ajaran').text(k.tahun_ajaran);
        $('#show_jumlah_siswa').text(k.jumlah_siswa + ' siswa');
        $('#show_created_at').text(k.created_at || '-');
        $('#show_updated_at').text(k.updated_at || '-');
        $('#showKelasModal').modal('show');
    });
}

function editKelas(id) {
    $.get('{{ url("admin/kelas") }}/' + id, function(k) {
        $('#edit_kelas_id').val(k.id);
        $('#edit_nama_kelas').val(k.nama_kelas);
        $('#edit_tingkat').val(k.tingkat);
        $('#edit_jurusan_id').val(k.jurusan_id);
        $('#edit_ruangan_id').val(k.ruangan_id || '');
        $('#edit_tahun_ajaran').val(k.tahun_ajaran);
        $('#editKelasModal').modal('show');
    });
}

$('#addKelasForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnSubmitAddKelas').prop('disabled', true);
    $('#btnLoaderAddKelas').removeClass('d-none');
    $('#addKelasError').addClass('d-none');
    $.ajax({
        url: '{{ route("admin.kelas.store") }}', method: 'POST', data: $(this).serialize(),
        success: function(res) { if(res.success) { $('#addKelasModal').modal('hide'); $('#addKelasForm')[0].reset(); loadData(); showToast('Kelas berhasil ditambahkan!', 'success'); } },
        error: function(xhr) { let e = xhr.responseJSON?.errors; $('#addKelasError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none'); },
        complete: function() { $('#btnSubmitAddKelas').prop('disabled', false); $('#btnLoaderAddKelas').addClass('d-none'); }
    });
});

$('#editKelasForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_kelas_id').val();
    $('#btnSubmitEditKelas').prop('disabled', true);
    $('#btnLoaderEditKelas').removeClass('d-none');
    $('#editKelasError').addClass('d-none');
    $.ajax({
        url: '{{ url("admin/kelas") }}/' + id, method: 'POST', data: $(this).serialize() + '&_method=PUT',
        success: function(res) { if(res.success) { $('#editKelasModal').modal('hide'); loadData(); showToast('Kelas berhasil diupdate!', 'success'); } },
        error: function(xhr) { let e = xhr.responseJSON?.errors; $('#editKelasError').html(e ? Object.values(e).flat().join('<br>') : 'Terjadi kesalahan.').removeClass('d-none'); },
        complete: function() { $('#btnSubmitEditKelas').prop('disabled', false); $('#btnLoaderEditKelas').addClass('d-none'); }
    });
});

function confirmDelete(id, nama, jml) {
    if (jml > 0) { showToast(`Kelas "${nama}" tidak bisa dihapus, masih ada ${jml} siswa.`, 'danger'); return; }
    deleteId = id; $('#delete_kelas_name').text(nama); $('#deleteKelasModal').modal('show');
}

$('#confirmDeleteKelasBtn').on('click', function() {
    if (!deleteId) return;
    $(this).prop('disabled', true); $('#btnDeleteKelasLoader').removeClass('d-none');
    $.ajax({
        url: '{{ url("admin/kelas") }}/' + deleteId, method: 'POST', data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res) { $('#deleteKelasModal').modal('hide'); loadData(); showToast(res.message, res.success?'success':'danger'); },
        error: function(xhr) { showToast(xhr.responseJSON?.message || 'Gagal menghapus.', 'danger'); },
        complete: function() { $('#confirmDeleteKelasBtn').prop('disabled', false); $('#btnDeleteKelasLoader').addClass('d-none'); deleteId = null; }
    });
});

function showToast(msg, type) {
    let t = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>`);
    $('body').append(t); setTimeout(() => t.remove(), 3500);
}

$(document).ready(function() { loadData(); });
</script>
@endpush