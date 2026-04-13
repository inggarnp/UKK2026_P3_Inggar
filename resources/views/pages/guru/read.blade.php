<div class="modal fade" id="showGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user me-2"></i>Detail Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center border-end">
                        <img id="show_guru_foto"
                            src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                            class="rounded-circle mb-3"
                            style="width:110px;height:110px;object-fit:cover;border:3px solid #dee2e6">
                        <p id="show_guru_nama" class="fw-bold mb-1">-</p>
                        <p id="show_guru_nip" class="text-muted small font-monospace mb-1">-</p>
                        <span id="show_guru_jabatan_badge" class="badge bg-primary">-</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Jabatan</label>
                                <p id="show_guru_jabatan" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Mata Pelajaran</label>
                                <p id="show_guru_mapel" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Jenis Kelamin</label>
                                <p id="show_guru_jk" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Tanggal Lahir</label>
                                <p id="show_guru_tgl_lahir" class="mb-0">-</p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted">Alamat</label>
                                <p id="show_guru_alamat" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">No. HP</label>
                                <p id="show_guru_no_hp" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <p id="show_guru_email" class="mb-0">-</p>
                            </div>
                            <div class="col-6 mb-1">
                                <label class="form-label fw-bold small text-muted">Dibuat</label>
                                <p id="show_guru_created_at" class="mb-0 text-muted small">-</p>
                            </div>
                            <div class="col-6 mb-1">
                                <label class="form-label fw-bold small text-muted">Diupdate</label>
                                <p id="show_guru_updated_at" class="mb-0 text-muted small">-</p>
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