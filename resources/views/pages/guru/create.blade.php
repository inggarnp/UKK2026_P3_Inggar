<div class="modal fade" id="addGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user-plus me-2"></i>Tambah Guru Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addGuruForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="addGuruError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        {{-- Kiri --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Guru</h6>
                            <div class="mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nip"
                                    placeholder="Nomor Induk Pegawai" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama"
                                    placeholder="Nama lengkap guru" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jabatan" required>
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
                                <input type="text" class="form-control" name="mata_pelajaran"
                                    placeholder="Contoh: Matematika">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" required>
                                        <label class="form-check-label">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="P">
                                        <label class="form-check-label">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2"
                                    placeholder="Alamat lengkap"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp"
                                    placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                        </div>
                        {{-- Kanan --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="bx bx-lock-alt me-1"></i>Akun Login
                            </h6>
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Akun ini digunakan guru untuk login ke aplikasi.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email"
                                    placeholder="email@sekolah.sch.id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                        id="add_guru_password" placeholder="Minimal 6 karakter" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('add_guru_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2">
                                <i class="bx bx-image me-1"></i>Foto Profil
                            </h6>
                            <div class="mb-3">
                                <input type="file" class="form-control" name="foto" id="add_guru_foto"
                                    accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'add_guru_foto_preview')">
                                <small class="text-muted">JPG, PNG — Maks 2MB. Opsional.</small>
                            </div>
                            <img id="add_guru_foto_preview" src="" class="rounded d-none"
                                style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddGuru">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddGuru" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>