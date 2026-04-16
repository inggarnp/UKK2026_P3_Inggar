@extends('layouts.app')
@section('title', 'Aspirasi Siswa | Guru')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">
                    <iconify-icon icon="solar:eye-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Aspirasi Siswa
                </h4>
                <p class="text-muted mb-0">
                    Aspirasi dari siswa di kelas yang kamu wali
                    @foreach($kelasIds as $kid)
                        @php $k = \App\Models\Kelas::find($kid) @endphp
                        @if($k)<span class="badge bg-soft-primary text-primary ms-1">{{ $k->nama_kelas }}</span>@endif
                    @endforeach
                </p>
            </div>
        </div>

        @if($kelasIds->isEmpty())
            <div class="alert alert-warning">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                Kamu belum ditugaskan sebagai wali kelas. Hubungi admin.
            </div>
        @else

        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">⏳ Menunggu</option>
                            <option value="Proses">🔄 Proses</option>
                            <option value="Selesai">✅ Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama siswa, lokasi...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-secondary" id="btnResetFilter">
                            <i class="bx bx-reset"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="mb-0 text-muted small">Tampilkan</label>
                    <select id="perPageSelect" class="form-select form-select-sm" style="width:80px">
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                    <label class="mb-0 text-muted small">data</label>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th>Siswa</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th>Saksi</th>
                                <th width="11%">Status</th>
                                <th width="12%">Tanggal</th>
                                <th width="6%" class="text-center">Aksi</th>
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
        @endif
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="detailSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><iconify-icon icon="solar:document-bold-duotone" class="me-2"></iconify-icon>Detail Aspirasi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1, perPage = 10, searchQuery = '';

function loadData() {
    $('#tabelBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div><span class="ms-2 text-muted">Memuat data...</span></td></tr>');
    $.ajax({
        url: '{{ route("guru.siswa-aspirasi.data") }}',
        data: { page: currentPage, per_page: perPage, search: searchQuery, status: $('#filterStatus').val() },
        success: renderTable,
        error: () => $('#tabelBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat data.</td></tr>')
    });
}

function renderTable(res) {
    if (!res.data.length) {
        $('#tabelBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada aspirasi ditemukan.</td></tr>');
        $('#tableInfo').text(''); $('#paginationLinks').html(''); return;
    }
    let start = (res.current_page - 1) * res.per_page + 1, html = '';
    let statusMap = { 'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success' };
    res.data.forEach(function(d, i) {
        let ket = d.keterangan?.length > 40 ? d.keterangan.substring(0, 40) + '...' : (d.keterangan || '-');
        html += `<tr>
            <td class="text-muted">${start + i}</td>
            <td><div class="fw-semibold">${d.nama_siswa}</div><small class="text-muted">${d.kelas}</small></td>
            <td><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></td>
            <td class="small">${d.lokasi_display}</td>
            <td class="small text-muted">${ket}</td>
            <td class="small">${d.saksi_nama !== '-' ? `<span class="badge bg-soft-info text-info">${d.saksi_nama}</span>` : '<span class="text-muted">-</span>'}</td>
            <td><span class="badge ${statusMap[d.status]||'bg-secondary'}">${d.status}</span></td>
            <td class="small text-muted">${d.created_at_fmt}</td>
            <td class="text-center"><button class="btn btn-sm btn-soft-info" onclick="lihatDetail(${d.id})"><i class="bx bx-show"></i></button></td>
        </tr>`;
    });
    $('#tabelBody').html(html);
    let to = Math.min(start + res.per_page - 1, res.total);
    $('#tableInfo').text(`Menampilkan ${start}–${to} dari ${res.total} aspirasi`);
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

function lihatDetail(id) {
    $('#detailSiswaModal').modal('show');
    $('#detailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    $.get('{{ url("guru/siswa-aspirasi") }}/' + id, function(d) {
        let statusMap = {'Menunggu':'bg-soft-warning text-warning','Proses':'bg-soft-info text-info','Selesai':'bg-soft-success text-success'};
        let fotoHtml = d.foto_url ? `<img src="${d.foto_url}" class="img-fluid rounded mt-2" style="max-height:180px">` : '<span class="text-muted small">Tidak ada foto</span>';
        let ruanganInfo = d.kode_ruangan ? `<div class="small text-muted">Kode: ${d.kode_ruangan} | Lantai: ${d.lantai||'-'} | Gedung: ${d.gedung||'-'}</div>` : '';
        let progresHtml = d.progres?.length
            ? d.progres.map(p => `<div class="border-start border-success border-2 ps-3 mb-2"><div class="small text-muted">${p.nama_petugas} • ${p.created_at_fmt}</div><p class="mb-0 small">${p.keterangan_progres}</p></div>`).join('')
            : '<p class="text-muted small mb-0">Belum ada progres.</p>';
        let historiHtml = d.histori?.length
            ? d.histori.map(h => `<div class="d-flex gap-2 mb-2"><span class="badge ${h.status_badge} mt-1">${h.status}</span><div><div class="small text-muted">${h.created_at_fmt}</div>${h.keterangan ? `<div class="small">${h.keterangan}</div>` : ''}</div></div>`).join('')
            : '<p class="text-muted small mb-0">-</p>';

        $('#detailBody').html(`
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2"><small class="text-muted d-block">Siswa</small><strong>${d.nama_siswa}</strong> <small class="text-muted">(${d.kelas})</small></div>
                    <div class="mb-2"><small class="text-muted d-block">Saksi</small>${d.saksi_nama !== '-' ? `<span class="badge bg-soft-info text-info">${d.saksi_nama}</span>` : '<span class="text-muted small">-</span>'}</div>
                    <div class="mb-2"><small class="text-muted d-block">Kategori</small><span class="badge bg-soft-secondary text-secondary">${d.nama_kategori}</span></div>
                    <div class="mb-2"><small class="text-muted d-block">Lokasi</small><strong>${d.lokasi_display}</strong>${ruanganInfo}</div>
                    <div class="mb-2"><small class="text-muted d-block">Keterangan</small><p class="mb-0 small">${d.keterangan}</p></div>
                    <div class="mb-3"><small class="text-muted d-block">Status</small><span class="badge ${statusMap[d.status]||'bg-secondary'}">${d.status}</span></div>
                    ${fotoHtml}
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Progres Perbaikan</h6>
                    <div class="mb-4">${progresHtml}</div>
                    <h6 class="text-muted border-bottom pb-2 mb-3">Histori Status</h6>
                    <div style="max-height:200px;overflow-y:auto">${historiHtml}</div>
                </div>
            </div>
        `);
    }).fail(() => $('#detailBody').html('<div class="text-center text-danger py-3">Gagal memuat detail.</div>'));
}

let st;
$('#searchInput').on('input', function() { clearTimeout(st); st = setTimeout(() => { searchQuery = $(this).val(); currentPage = 1; loadData(); }, 400); });
$('#filterStatus').on('change', () => { currentPage = 1; loadData(); });
$('#perPageSelect').on('change', function() { perPage = $(this).val(); currentPage = 1; loadData(); });
$('#btnResetFilter').on('click', function() { $('#filterStatus').val(''); $('#searchInput').val(''); searchQuery = ''; currentPage = 1; loadData(); });
$(document).ready(() => loadData());
</script>
@endpush