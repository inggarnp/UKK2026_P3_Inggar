@extends('layouts.app')
@section('title', 'Histori Status | Kepala Sekolah')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1">Histori Status Aspirasi</h4>
                <p class="text-muted mb-0 small">Riwayat seluruh perubahan status aspirasi sarana</p>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Cari</label>
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Nama pelapor, kategori...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="btnReset" class="btn btn-sm btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Status Lama</th>
                                <th>Status Baru</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
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
            search: document.getElementById('searchInput').value,
            status: document.getElementById('filterStatus').value,
        });

        document.getElementById('historyBody').innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
            </td></tr>`;

        fetch(`{{ route('kepala_sekolah.history.data') }}?${params}`)
            .then(r => r.json())
            .then(res => renderTable(res));
    }

    function renderTable(res) {
        const tbody = document.getElementById('historyBody');
        if (!res.data || res.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data histori.</td></tr>`;
            document.getElementById('paginationInfo').textContent = '';
            document.getElementById('paginationLinks').innerHTML = '';
            return;
        }

        let html = '';
        const start = (res.current_page - 1) * res.per_page;
        res.data.forEach((item, i) => {
            const badgeNew  = statusMap[item.status_baru]  || 'bg-soft-secondary text-secondary';
            const badgeOld  = statusMap[item.status_lama]  || 'bg-soft-secondary text-secondary';
            html += `
            <tr>
                <td class="ps-3 text-muted small">${start + i + 1}</td>
                <td>
                    <div class="fw-semibold small">${item.nama_pelapor}</div>
                    <span class="badge bg-soft-secondary text-secondary">${item.role}</span>
                </td>
                <td><span class="badge bg-soft-secondary text-secondary">${item.nama_kategori}</span></td>
                <td><span class="badge ${badgeOld}">${item.status_lama}</span></td>
                <td><span class="badge ${badgeNew}">${item.status_baru}</span></td>
                <td class="small text-muted">${item.keterangan ?? '-'}</td>
                <td class="small text-muted">${item.created_at_fmt}</td>
            </tr>`;
        });
        tbody.innerHTML = html;

        const from = start + 1;
        const to   = Math.min(start + res.per_page, res.total);
        document.getElementById('paginationInfo').textContent =
            `Menampilkan ${from}–${to} dari ${res.total} data`;

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

    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadData(1), 400);
    });
    document.getElementById('filterStatus').addEventListener('change', () => loadData(1));
    document.getElementById('btnReset').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterStatus').value = '';
        loadData(1);
    });

    loadData();
</script>
@endpush