@extends('layouts.app')
@section('title', 'Data Jurusan | Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Jurusan</h4>
                <p class="text-muted mb-0">Kelola daftar jurusan yang tersedia di sekolah</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="$('#addJurusanModal').modal('show')">
                <i class="bx bx-plus me-1"></i> Tambah Jurusan
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-muted small">Tampilkan</label>
                        <select id="perPageSelect" class="form-select form-select-sm" style="width:80px">
                            <option value="10">10</option><option value="25">25</option><option value="50">50</option>
                        </select>
                        <label class="mb-0 text-muted small">data</label>
                    </div>
                    <div class="input-group input-group-sm" style="width:260px">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari jurusan...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">Kode</th>
                                <th>Nama Jurusan</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Kelas</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody">
                            <tr><td colspan="6" class="text-center py-4">
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

{{-- Modal Tambah --}}
<div class="modal fade" id="addJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Tambah Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addJurusanForm">
                @csrf
                <div class="modal-body">
                    <div id="addJurusanError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_jurusan" placeholder="Contoh: RPL, TKJ, MM" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_jurusan" placeholder="Nama lengkap jurusan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2" placeholder="Deskripsi singkat jurusan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddJurusan">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddJurusan" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editJurusanForm">
                @csrf
                <input type="hidden" id="edit_jurusan_id">
                <div class="modal-body">
                    <div id="editJurusanError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_kode_jurusan" name="kode_jurusan" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_jurusan" name="nama_jurusan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi_jurusan" name="deskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditJurusan">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditJurusan" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="deleteJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-error-circle me-2"></i>Hapus Jurusan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                <p class="mt-2">Hapus jurusan <strong id="delete_jurusan_name"></strong>?</p>
                <div class="alert alert-danger">Data tidak bisa dikembalikan!</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteJurusanBtn">
                    Ya, Hapus <span id="btnDeleteJurusanLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage=1, perPage=10, searchQuery='', deleteId=null;

function loadData() {
    $('#tabelBody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("admin.jurusan.data") }}',
        data: { page:currentPage, per_page:perPage, search:searchQuery },
        success: function(res) {
            if (!res.data.length) { $('#tabelBody').html('<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>'); $('#tableInfo').text(''); $('#paginationLinks').html(''); return; }
            let start=(res.current_page-1)*res.per_page+1, html='';
            res.data.forEach(function(j,i) {
                html+=`<tr>
                    <td class="text-muted">${start+i}</td>
                    <td><span class="badge bg-soft-primary text-primary font-monospace">${j.kode_jurusan}</span></td>
                    <td class="fw-semibold">${j.nama_jurusan}</td>
                    <td class="text-muted small">${j.deskripsi||'-'}</td>
                    <td class="text-center"><span class="badge bg-soft-dark text-dark">${j.kelas_count} kelas</span></td>
                    <td class="text-center" style="white-space:nowrap">
                        <button class="btn btn-sm btn-soft-warning me-1" onclick="editJurusan(${j.id})"><i class="bx bx-edit"></i></button>
                        <button class="btn btn-sm btn-soft-danger" onclick="confirmDeleteJurusan(${j.id},'${j.nama_jurusan}',${j.kelas_count})"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tabelBody').html(html);
            $('#tableInfo').text(`Menampilkan ${start}–${Math.min(start+res.per_page-1,res.total)} dari ${res.total} jurusan`);
            renderPagination(res.current_page, res.last_page);
        },
        error: function() { $('#tabelBody').html('<tr><td colspan="6" class="text-center text-danger py-3">Gagal memuat data.</td></tr>'); }
    });
}

function renderPagination(c,l) {
    if(l<=1){$('#paginationLinks').html('');return;}
    let h=`<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c-1})">‹</a></li>`;
    for(let p=1;p<=l;p++){if(p===1||p===l||(p>=c-1&&p<=c+1))h+=`<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return goPage(${p})">${p}</a></li>`;else if(p===c-2||p===c+2)h+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;}
    h+=`<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c+1})">›</a></li>`;
    $('#paginationLinks').html(h);
}
function goPage(p){currentPage=p;loadData();return false;}

let st;
$('#searchInput').on('input',function(){clearTimeout(st);st=setTimeout(function(){searchQuery=$('#searchInput').val();currentPage=1;loadData();},400);});
$('#perPageSelect').on('change',function(){perPage=$(this).val();currentPage=1;loadData();});

function editJurusan(id) {
    $.get('{{ url("admin/jurusan") }}/'+id, function(j) {
        $('#edit_jurusan_id').val(j.id);
        $('#edit_kode_jurusan').val(j.kode_jurusan);
        $('#edit_nama_jurusan').val(j.nama_jurusan);
        $('#edit_deskripsi_jurusan').val(j.deskripsi||'');
        $('#editJurusanModal').modal('show');
    });
}

$('#addJurusanForm').on('submit',function(e){
    e.preventDefault();
    $('#btnSubmitAddJurusan').prop('disabled',true);$('#btnLoaderAddJurusan').removeClass('d-none');$('#addJurusanError').addClass('d-none');
    $.ajax({url:'{{ route("admin.jurusan.store") }}',method:'POST',data:$(this).serialize(),
        success:function(r){if(r.success){$('#addJurusanModal').modal('hide');$('#addJurusanForm')[0].reset();loadData();showToast('Jurusan berhasil ditambahkan!','success');}},
        error:function(x){let e=x.responseJSON?.errors;$('#addJurusanError').html(e?Object.values(e).flat().join('<br>'):'Terjadi kesalahan.').removeClass('d-none');},
        complete:function(){$('#btnSubmitAddJurusan').prop('disabled',false);$('#btnLoaderAddJurusan').addClass('d-none');}
    });
});

$('#editJurusanForm').on('submit',function(e){
    e.preventDefault();
    let id=$('#edit_jurusan_id').val();
    $('#btnSubmitEditJurusan').prop('disabled',true);$('#btnLoaderEditJurusan').removeClass('d-none');$('#editJurusanError').addClass('d-none');
    $.ajax({url:'{{ url("admin/jurusan") }}/'+id,method:'POST',data:$(this).serialize()+'&_method=PUT',
        success:function(r){if(r.success){$('#editJurusanModal').modal('hide');loadData();showToast('Jurusan berhasil diupdate!','success');}},
        error:function(x){let e=x.responseJSON?.errors;$('#editJurusanError').html(e?Object.values(e).flat().join('<br>'):'Terjadi kesalahan.').removeClass('d-none');},
        complete:function(){$('#btnSubmitEditJurusan').prop('disabled',false);$('#btnLoaderEditJurusan').addClass('d-none');}
    });
});

function confirmDeleteJurusan(id,nama,jml){
    if(jml>0){showToast(`Jurusan "${nama}" tidak bisa dihapus, masih ada ${jml} kelas.`,'danger');return;}
    deleteId=id;$('#delete_jurusan_name').text(nama);$('#deleteJurusanModal').modal('show');
}
$('#confirmDeleteJurusanBtn').on('click',function(){
    if(!deleteId)return;$(this).prop('disabled',true);$('#btnDeleteJurusanLoader').removeClass('d-none');
    $.ajax({url:'{{ url("admin/jurusan") }}/'+deleteId,method:'POST',data:{_method:'DELETE',_token:'{{ csrf_token() }}'},
        success:function(r){$('#deleteJurusanModal').modal('hide');loadData();showToast(r.message,r.success?'success':'danger');},
        error:function(x){showToast(x.responseJSON?.message||'Gagal menghapus.','danger');},
        complete:function(){$('#confirmDeleteJurusanBtn').prop('disabled',false);$('#btnDeleteJurusanLoader').addClass('d-none');deleteId=null;}
    });
});

function showToast(msg,type){let t=$(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>`);$('body').append(t);setTimeout(()=>t.remove(),3500);}
$(document).ready(function(){loadData();});
</script>
@endpush