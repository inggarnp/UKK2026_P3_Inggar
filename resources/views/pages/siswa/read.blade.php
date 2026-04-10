{{-- Modal Detail Siswa --}}
<div class="modal fade" id="showSiswaModal" tabindex="-1" aria-labelledby="showSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSiswaModalLabel">
                    <i class="bx bx-user me-2"></i>Detail Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        <h6 class="mb-3 text-muted border-bottom pb-2">Data Siswa</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">NIS</label>
                            <p id="show_nis" class="mb-0 font-monospace fw-semibold">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Lengkap</label>
                            <p id="show_nama" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Kelas</label>
                            <p id="show_kelas" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Jurusan</label>
                            <p id="show_jurusan" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Jenis Kelamin</label>
                            <p id="show_jk" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tanggal Lahir</label>
                            <p id="show_tgl_lahir" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Alamat</label>
                            <p id="show_alamat" class="mb-0">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">No. HP</label>
                            <p id="show_no_hp" class="mb-0">-</p>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-md-6">
                        <h6 class="mb-3 text-muted border-bottom pb-2">Akun Login</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Email</label>
                            <p id="show_email" class="mb-0">-</p>
                        </div>

                        <h6 class="mb-3 mt-4 text-muted border-bottom pb-2">Sistem</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Dibuat Pada</label>
                            <p id="show_created_at" class="mb-0 text-muted small">-</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Terakhir Update</label>
                            <p id="show_updated_at" class="mb-0 text-muted small">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>