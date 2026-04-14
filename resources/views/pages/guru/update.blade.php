<div class="modal fade" id="editGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGuruForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_guru_id">
                <div class="modal-body">
                    <div id="editGuruError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Guru</h6>
                            <div class="mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_guru_nip"
                                    name="nip" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_guru_nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_guru_jabatan" name="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    <optgroup label="Pimpinan">
                                        <option value="kepala_sekolah">Kepala Sekolah</option>
                                        <option value="wakil_kepala_sekolah">Wakil Kepala Sekolah</option>
                                    </optgroup>
                                    <optgroup label="Pengajar">
                                        <option value="guru">Guru Mata Pelajaran</option>
                                        <option value="wali_kelas">Wali Kelas</option>
                                        <option value="kepala_jurusan">Kepala Jurusan</option>
                                    </optgroup>
                                    <optgroup label="Staff">
                                        <option value="bendahara">Bendahara</option>
                                        <option value="tata_usaha">Tata Usaha</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mata Pelajaran</label>
                                <input type="text" class="form-control" id="edit_guru_mapel" name="mata_pelajaran">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jenis_kelamin" value="L" required>
                                        <label class="form-check-label">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="jenis_kelamin" value="P">
                                        <label class="form-check-label">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="edit_guru_tgl_lahir" name="tanggal_lahir">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" id="edit_guru_alamat" name="alamat" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="edit_guru_no_hp"
                                    name="no_hp" maxlength="15">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="bx bx-lock-alt me-1"></i>Akun Login
                            </h6>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_guru_email"
                                    name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="edit_guru_password"
                                        name="password" placeholder="Kosongkan jika tidak ingin ubah">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('edit_guru_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>
                            </div>
                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2">
                                <i class="bx bx-image me-1"></i>Foto Profil
                            </h6>
                            <img id="edit_guru_foto_preview" src="" class="rounded d-none mb-2"
                                style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="foto"
                                    id="edit_guru_foto" accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'edit_guru_foto_preview')">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditGuru">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditGuru" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>