{{-- Modal Import Siswa --}}
<div class="modal fade" id="importSiswaModal" tabindex="-1" aria-labelledby="importSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSiswaModalLabel">
                    <i class="bx bx-upload me-2"></i>Import Data Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {{-- Panduan --}}
                <div class="alert alert-info mb-3">
                    <h6 class="alert-heading"><i class="bx bx-info-circle me-1"></i>Panduan Import</h6>
                    <ul class="mb-2 ps-3">
                        <li>Format file: <strong>xlsx, xls, atau csv</strong>, maksimal 5MB</li>
                        <li>Kolom: <strong>NIS | Nama | Kelas | Jurusan | J.K (L/P) | Tgl Lahir | Alamat | No HP | Email | Password</strong></li>
                        <li>Format tanggal lahir: <strong>YYYY-MM-DD</strong> — contoh: 2006-05-15</li>
                        <li>Setiap baris akan otomatis membuat akun login untuk siswa</li>
                    </ul>
                    <a href="{{ route('admin.siswa.import.template') }}" class="btn btn-sm btn-outline-info mt-1">
                        <i class="bx bx-download me-1"></i> Download Template Excel
                    </a>
                </div>

                {{-- Form Upload --}}
                <div id="importFormSectionSiswa">
                    <div class="mb-3">
                        <label for="importFileSiswa" class="form-label">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="importFileSiswa" accept=".xlsx,.xls,.csv">
                        <small class="text-muted">Format: xlsx, xls, csv — Maksimal 5MB</small>
                    </div>
                </div>

                {{-- Loading --}}
                <div id="importLoadingSiswa" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Sedang memproses file, mohon tunggu...</p>
                </div>

                {{-- Hasil Import --}}
                <div id="importResultSiswa" class="d-none">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card border-success">
                                <div class="card-body text-center py-2">
                                    <h3 class="text-success mb-0" id="importSuccessCount">0</h3>
                                    <small class="text-muted">Berhasil diimport</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-danger">
                                <div class="card-body text-center py-2">
                                    <h3 class="text-danger mb-0" id="importErrorCount">0</h3>
                                    <small class="text-muted">Gagal / Di-skip</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="importErrorDetailSiswa" class="d-none">
                        <h6 class="text-danger mb-2">
                            <i class="bx bx-error-circle me-1"></i>Detail Baris yang Gagal:
                        </h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-danger">
                                    <tr>
                                        <th width="8%">Baris</th>
                                        <th width="15%">NIS</th>
                                        <th width="25%">Nama</th>
                                        <th>Keterangan Error</th>
                                    </tr>
                                </thead>
                                <tbody id="importErrorTableBodySiswa"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-success" id="btnSubmitImportSiswa">
                    <span id="btnTextImportSiswa"><i class="bx bx-upload me-1"></i> Mulai Import</span>
                    <span id="btnLoaderImportSiswa" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
                <button type="button" class="btn btn-primary d-none" id="btnRefreshAfterImportSiswa"
                    onclick="window.location.reload()">
                    <i class="bx bx-refresh me-1"></i> Refresh Halaman
                </button>
            </div>
        </div>
    </div>
</div>