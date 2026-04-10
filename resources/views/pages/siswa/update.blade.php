{{-- Modal Edit Siswa --}}
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-labelledby="editSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSiswaModalLabel">
                    <i class="bx bx-edit me-2"></i>Edit Data Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSiswaForm">
                @csrf
                <input type="hidden" id="edit_siswa_id" name="siswa_id">

                <div class="modal-body">
                    <div id="editSiswaError" class="alert alert-danger d-none"></div>

                    <div class="row">
                        {{-- Kolom Kiri: Data Siswa --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Siswa</h6>

                            <div class="mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nis" name="nis" maxlength="20" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_kelas" name="kelas" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jurusan" name="jurusan" required>
                                    <option value="">Pilih Jurusan</option>
                                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                    <option value="Teknik Komputer Jaringan">Teknik Komputer Jaringan</option>
                                    <option value="Multimedia">Multimedia</option>
                                    <option value="Akuntansi">Akuntansi</option>
                                    <option value="Administrasi Perkantoran">Administrasi Perkantoran</option>
                                    <option value="Pemasaran">Pemasaran</option>
                                    <option value="Teknik Kendaraan Ringan">Teknik Kendaraan Ringan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="edit_jk_l" value="L" required>
                                        <label class="form-check-label" for="edit_jk_l">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="edit_jk_p" value="P">
                                        <label class="form-check-label" for="edit_jk_p">Perempuan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="edit_no_hp" name="no_hp" maxlength="15">
                            </div>
                        </div>

                        {{-- Kolom Kanan: Akun Login --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="bx bx-lock-alt me-1"></i>Akun Login Siswa
                            </h6>

                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="edit_password" name="password"
                                        placeholder="Kosongkan jika tidak ingin ubah password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('edit_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle"></i>
                                    Kosongkan jika tidak ingin mengganti password.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditSiswa">
                        <span><i class="bx bx-save me-1"></i> Update</span>
                        <span id="btnLoaderEditSiswa" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>