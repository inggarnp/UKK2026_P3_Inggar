@extends('layouts.app')
@section('title', 'Semua Aspirasi | Kepala Sekolah')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1">Semua Aspirasi</h4>
                <p class="text-muted mb-0 small">Pantau seluruh laporan aspirasi sarana dari siswa dan guru</p>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Cari</label>
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Nama, lokasi, kategori...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Kategori</label>
                        <select id="filterKategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Role</label>
                        <select id="filterRole" class="form-select form-select-sm">
                            <option value="">Semua Role</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-1">Dari</label>
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-1">Sampai</label>
                        <input type="date" id="filterDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1">
                        <button id="btnReset" class="btn btn-sm btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="aspirasiTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="aspirasiBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <div class="text-muted small" id="paginationInfo">-</div>
                <div id="paginationLinks" class="d-flex gap-1"></div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Aspirasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let searchTimer;

    const statusMap = {
        'Menunggu': 'bg-soft-warning text-warning',
        'Proses':   'bg-soft-info text-info',
        'Selesai':  'bg-soft-success text-success',
    };

    function loadData(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({
            page,
            per_page: 10,
            search:    document.getElementById('searchInput').value,
            status:    document.getElementById('filterStatus').value,
            kategori:  document.getElementById('filterKategori').value,
            role:      document.getElementById('filterRole').value,
            date_from: document.getElementById('filterDateFrom').value,
            date_to:   document.getElementById('filterDateTo').value,
        });

        document.getElementById('aspirasiBody').innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
            </td></tr>`;

        fetch(`{{ route('kepala_sekolah.aspirasi.data') }}?${params}`)
            .then(r => r.json())
            .then(res => renderTable(res));
    }

    function renderTable(res) {
        const tbody = document.getElementById('aspirasiBody');
        if (!res.data || res.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data aspirasi.</td></tr>`;
            document.getElementById('paginationInfo').textContent = '';
            document.getElementById('paginationLinks').innerHTML = '';
            return;
        }

        let html = '';
        const start = (res.current_page - 1) * res.per_page;
        res.data.forEach((item, i) => {
            const badgeClass = statusMap[item.status] || 'bg-soft-secondary text-secondary';
            html += `
            <tr>
                <td class="ps-3 text-muted small">${start + i + 1}</td>
                <td>
                    <div class="fw-semibold small">${item.nama_pelapor}</div>
                    <span class="badge bg-soft-secondary text-secondary">${item.role}</span>
                </td>
                <td><span class="badge bg-soft-secondary text-secondary">${item.nama_kategori}</span></td>
                <td class="small">${item.lokasi_display}</td>
                <td><span class="badge ${badgeClass}">${item.status}</span></td>
                <td class="small text-muted">${item.created_at_fmt}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" onclick="showDetail(${item.id})">
                        <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                    </button>
                </td>
            </tr>`;
        });
        tbody.innerHTML = html;

        // Pagination info
        const from = start + 1;
        const to   = Math.min(start + res.per_page, res.total);
        document.getElementById('paginationInfo').textContent =
            `Menampilkan ${from}–${to} dari ${res.total} data`;

        // Pagination links
        renderPagination(res.current_page, res.last_page);
    }

    function renderPagination(current, last) {
        const container = document.getElementById('paginationLinks');
        let html = '';
        html += `<button class="btn btn-sm btn-outline-secondary" ${current <= 1 ? 'disabled' : ''} onclick="loadData(${current - 1})">‹</button>`;
        for (let p = Math.max(1, current - 2); p <= Math.min(last, current + 2); p++) {
            html += `<button class="btn btn-sm ${p === current ? 'btn-primary' : 'btn-outline-secondary'}" onclick="loadData(${p})">${p}</button>`;
        }
        html += `<button class="btn btn-sm btn-outline-secondary" ${current >= last ? 'disabled' : ''} onclick="loadData(${current + 1})">›</button>`;
        container.innerHTML = html;
    }

    function showDetail(id) {
        document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>`;
        new bootstrap.Modal(document.getElementById('modalDetail')).show();

        fetch(`{{ url('kepala-sekolah/aspirasi') }}/${id}`)
            .then(r => r.json())
            .then(d => {
                const badgeClass = statusMap[d.status] || 'bg-soft-secondary text-secondary';
                let fotoHtml = d.foto_url
                    ? `<img src="${d.foto_url}" class="img-fluid rounded mb-3" style="max-height:260px">`
                    : '';

                let feedbackHtml = (d.feedback || []).map(f => `
                    <div class="border rounded p-2 mb-2 small">
                        <div class="fw-semibold">${f.nama_pemberi} <span class="text-muted">(${f.created_at_fmt})</span></div>
                        <div>${f.isi_feedback}</div>
                    </div>`).join('') || '<p class="text-muted small">Belum ada feedback.</p>';

                let progresHtml = (d.progres || []).map(p => `
                    <div class="border rounded p-2 mb-2 small">
                        <div class="fw-semibold">${p.nama_petugas} <span class="text-muted">(${p.created_at_fmt})</span></div>
                        <div>${p.keterangan_progres}</div>
                        ${p.foto_url ? `<img src="${p.foto_url}" class="img-fluid rounded mt-1" style="max-height:160px">` : ''}
                    </div>`).join('') || '<p class="text-muted small">Belum ada progres.</p>';

                let historiHtml = (d.histori || []).map(h => `
                    <div class="d-flex gap-2 mb-2 small">
                        <span class="badge ${statusMap[h.status] || 'bg-secondary'} align-self-start">${h.status}</span>
                        <div>
                            <div class="text-muted">${h.created_at_fmt}</div>
                            ${h.keterangan ? `<div>${h.keterangan}</div>` : ''}
                        </div>
                    </div>`).join('') || '<p class="text-muted small">Belum ada histori.</p>';

                document.getElementById('modalBody').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Pelapor</p>
                            <p class="fw-semibold mb-0">${d.nama_pelapor} <span class="badge bg-soft-secondary text-secondary ms-1">${d.role}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Status</p>
                            <span class="badge ${badgeClass}">${d.status}</span>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Kategori</p>
                            <p class="mb-0">${d.nama_kategori}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Lokasi</p>
                            <p class="mb-0">${d.lokasi_display}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 small text-muted">Keterangan</p>
                            <p class="mb-0">${d.keterangan ?? '-'}</p>
                        </div>
                        ${fotoHtml ? `<div class="col-12">${fotoHtml}</div>` : ''}
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12">
                            <p class="fw-semibold small mb-2">Progres Pengerjaan</p>
                            ${progresHtml}
                        </div>
                        <div class="col-12">
                            <p class="fw-semibold small mb-2">Feedback</p>
                            ${feedbackHtml}
                        </div>
                        <div class="col-12">
                            <p class="fw-semibold small mb-2">Histori Status</p>
                            ${historiHtml}
                        </div>
                    </div>`;
            });
    }

    // Event listeners
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadData(1), 400);
    });
    ['filterStatus','filterKategori','filterRole','filterDateFrom','filterDateTo'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => loadData(1));
    });
    document.getElementById('btnReset').addEventListener('click', () => {
        ['searchInput','filterDateFrom','filterDateTo'].forEach(id => document.getElementById(id).value = '');
        ['filterStatus','filterKategori','filterRole'].forEach(id => document.getElementById(id).value = '');
        loadData(1);
    });

    loadData();
</script>
@endpush