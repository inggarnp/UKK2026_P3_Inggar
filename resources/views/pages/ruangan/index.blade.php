@extends('layouts.app')
@section('title', 'Data Ruangan | Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Data Ruangan</h4>
                <p class="text-muted mb-0">Kelola daftar ruangan dan fasilitas sekolah</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="$('#addRuanganModal').modal('show')">
                <i class="bx bx-plus me-1"></i> Tambah Ruangan
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari ruangan...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">Kode</th>
                                <th>Nama Ruangan</th>
                                <th>Jenis</th>
                                <th>Lokasi</th>
                                <th class="text-center">Kapasitas</th>
                                <th class="text-center">Kondisi</th>
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

{{-- Modal Tambah --}}
<div class="modal fade" id="addRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-building me-2"></i>Tambah Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRuanganForm">
                @csrf
                <div class="modal-body">
                    <div id="addRuanganError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="kode_ruangan" placeholder="Contoh: R-101, LAB-KOM-1" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_ruangan" placeholder="Nama ruangan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Ruangan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jenis_ruangan" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="kelas">Kelas</option>
                                    <option value="laboratorium">Laboratorium</option>
                                    <option value="perpustakaan">Perpustakaan</option>
                                    <option value="aula">Aula</option>
                                    <option value="kantor">Kantor</option>
                                    <option value="toilet">Toilet</option>
                                    <option value="lapangan">Lapangan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select" name="kondisi" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lantai</label>
                                <input type="text" class="form-control" name="lantai" placeholder="Contoh: 1, 2, 3" maxlength="5">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gedung</label>
                                <input type="text" class="form-control" name="gedung" placeholder="Contoh: Gedung A" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" name="kapasitas" placeholder="Jumlah orang" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="2" placeholder="Keterangan tambahan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddRuangan">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddRuangan" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRuanganForm">
                @csrf
                <input type="hidden" id="edit_ruangan_id">
                <div class="modal-body">
                    <div id="editRuanganError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_kode_ruangan" name="kode_ruangan" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_ruangan" name="nama_ruangan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Ruangan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_ruangan" name="jenis_ruangan" required>
                                    <option value="kelas">Kelas</option>
                                    <option value="laboratorium">Laboratorium</option>
                                    <option value="perpustakaan">Perpustakaan</option>
                                    <option value="aula">Aula</option>
                                    <option value="kantor">Kantor</option>
                                    <option value="toilet">Toilet</option>
                                    <option value="lapangan">Lapangan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_kondisi" name="kondisi" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lantai</label>
                                <input type="text" class="form-control" id="edit_lantai" name="lantai" maxlength="5">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gedung</label>
                                <input type="text" class="form-control" id="edit_gedung" name="gedung" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi_ruangan" name="deskripsi" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditRuangan">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditRuangan" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="deleteRuanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-error-circle me-2"></i>Hapus Ruangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                <p class="mt-2">Hapus ruangan <strong id="delete_ruangan_name"></strong>?</p>
                <div class="alert alert-danger">Data tidak bisa dikembalikan!</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteRuanganBtn">
                    Ya, Hapus <span id="btnDeleteRuanganLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage=1,perPage=10,searchQuery='',deleteId=null;
const jenisLabel = {kelas:'Kelas',laboratorium:'Laboratorium',perpustakaan:'Perpustakaan',aula:'Aula',kantor:'Kantor',toilet:'Toilet',lapangan:'Lapangan',lainnya:'Lainnya'};
const kondisiBadge = {baik:'bg-soft-success text-success',rusak_ringan:'bg-soft-warning text-warning',rusak_berat:'bg-soft-danger text-danger'};
const kondisiLabel = {baik:'Baik',rusak_ringan:'Rusak Ringan',rusak_berat:'Rusak Berat'};

function loadData() {
    $('#tabelBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("admin.ruangan.data") }}',
        data: {page:currentPage,per_page:perPage,search:searchQuery},
        success: function(res) {
            if(!res.data.length){$('#tabelBody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>');$('#tableInfo').text('');$('#paginationLinks').html('');return;}
            let start=(res.current_page-1)*res.per_page+1,html='';
            res.data.forEach(function(r,i){
                let kb=kondisiBadge[r.kondisi]||'bg-soft-secondary', kl=kondisiLabel[r.kondisi]||r.kondisi;
                let lokasi=[r.gedung,r.lantai?'Lt.'+r.lantai:null].filter(Boolean).join(', ')||'-';
                html+=`<tr>
                    <td class="text-muted">${start+i}</td>
                    <td><span class="badge bg-soft-info text-info font-monospace">${r.kode_ruangan}</span></td>
                    <td class="fw-semibold">${r.nama_ruangan}</td>
                    <td>${jenisLabel[r.jenis_ruangan]||r.jenis_ruangan}</td>
                    <td class="text-muted small">${lokasi}</td>
                    <td class="text-center">${r.kapasitas?r.kapasitas+' org':'-'}</td>
                    <td class="text-center"><span class="badge ${kb}">${kl}</span></td>
                    <td class="text-center" style="white-space:nowrap">
                        <button class="btn btn-sm btn-soft-warning me-1" onclick="editRuangan(${r.id})"><i class="bx bx-edit"></i></button>
                        <button class="btn btn-sm btn-soft-danger" onclick="confirmDeleteRuangan(${r.id},'${r.nama_ruangan}',${r.kelas_count})"><i class="bx bx-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tabelBody').html(html);
            $('#tableInfo').text(`Menampilkan ${start}–${Math.min(start+res.per_page-1,res.total)} dari ${res.total} ruangan`);
            renderPagination(res.current_page,res.last_page);
        },
        error:function(){$('#tabelBody').html('<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');}
    });
}

function renderPagination(c,l){if(l<=1){$('#paginationLinks').html('');return;}let h=`<li class="page-item ${c===1?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c-1})">‹</a></li>`;for(let p=1;p<=l;p++){if(p===1||p===l||(p>=c-1&&p<=c+1))h+=`<li class="page-item ${p===c?'active':''}"><a class="page-link" href="#" onclick="return goPage(${p})">${p}</a></li>`;else if(p===c-2||p===c+2)h+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;}h+=`<li class="page-item ${c===l?'disabled':''}"><a class="page-link" href="#" onclick="return goPage(${c+1})">›</a></li>`;$('#paginationLinks').html(h);}
function goPage(p){currentPage=p;loadData();return false;}
let st;$('#searchInput').on('input',function(){clearTimeout(st);st=setTimeout(function(){searchQuery=$('#searchInput').val();currentPage=1;loadData();},400);});
$('#perPageSelect').on('change',function(){perPage=$(this).val();currentPage=1;loadData();});

function editRuangan(id){
    $.get('{{ url("admin/ruangan") }}/'+id,function(r){
        $('#edit_ruangan_id').val(r.id);$('#edit_kode_ruangan').val(r.kode_ruangan);$('#edit_nama_ruangan').val(r.nama_ruangan);
        $('#edit_jenis_ruangan').val(r.jenis_ruangan);$('#edit_kondisi').val(r.kondisi);$('#edit_lantai').val(r.lantai||'');
        $('#edit_gedung').val(r.gedung||'');$('#edit_kapasitas').val(r.kapasitas||'');$('#edit_deskripsi_ruangan').val(r.deskripsi||'');
        $('#editRuanganModal').modal('show');
    });
}

$('#addRuanganForm').on('submit',function(e){e.preventDefault();$('#btnSubmitAddRuangan').prop('disabled',true);$('#btnLoaderAddRuangan').removeClass('d-none');$('#addRuanganError').addClass('d-none');$.ajax({url:'{{ route("admin.ruangan.store") }}',method:'POST',data:$(this).serialize(),success:function(r){if(r.success){$('#addRuanganModal').modal('hide');$('#addRuanganForm')[0].reset();loadData();showToast('Ruangan berhasil ditambahkan!','success');}},error:function(x){let e=x.responseJSON?.errors;$('#addRuanganError').html(e?Object.values(e).flat().join('<br>'):'Terjadi kesalahan.').removeClass('d-none');},complete:function(){$('#btnSubmitAddRuangan').prop('disabled',false);$('#btnLoaderAddRuangan').addClass('d-none');}});});

$('#editRuanganForm').on('submit',function(e){e.preventDefault();let id=$('#edit_ruangan_id').val();$('#btnSubmitEditRuangan').prop('disabled',true);$('#btnLoaderEditRuangan').removeClass('d-none');$('#editRuanganError').addClass('d-none');$.ajax({url:'{{ url("admin/ruangan") }}/'+id,method:'POST',data:$(this).serialize()+'&_method=PUT',success:function(r){if(r.success){$('#editRuanganModal').modal('hide');loadData();showToast('Ruangan berhasil diupdate!','success');}},error:function(x){let e=x.responseJSON?.errors;$('#editRuanganError').html(e?Object.values(e).flat().join('<br>'):'Terjadi kesalahan.').removeClass('d-none');},complete:function(){$('#btnSubmitEditRuangan').prop('disabled',false);$('#btnLoaderEditRuangan').addClass('d-none');}});});

function confirmDeleteRuangan(id,nama,jml){if(jml>0){showToast(`Ruangan "${nama}" tidak bisa dihapus, dipakai ${jml} kelas.`,'danger');return;}deleteId=id;$('#delete_ruangan_name').text(nama);$('#deleteRuanganModal').modal('show');}
$('#confirmDeleteRuanganBtn').on('click',function(){if(!deleteId)return;$(this).prop('disabled',true);$('#btnDeleteRuanganLoader').removeClass('d-none');$.ajax({url:'{{ url("admin/ruangan") }}/'+deleteId,method:'POST',data:{_method:'DELETE',_token:'{{ csrf_token() }}'},success:function(r){$('#deleteRuanganModal').modal('hide');loadData();showToast(r.message,r.success?'success':'danger');},error:function(x){showToast(x.responseJSON?.message||'Gagal menghapus.','danger');},complete:function(){$('#confirmDeleteRuanganBtn').prop('disabled',false);$('#btnDeleteRuanganLoader').addClass('d-none');deleteId=null;}});});

function showToast(msg,type){let t=$(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"><div class="toast show align-items-center text-white ${type==='success'?'bg-success':'bg-danger'} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>`);$('body').append(t);setTimeout(()=>t.remove(),3500);}
$(document).ready(function(){loadData();});
</script>
@endpush