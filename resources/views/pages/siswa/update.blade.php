{{-- Modal Edit Siswa --}}
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSiswaForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_siswa_id" name="siswa_id">
                <div class="modal-body">
                    <div id="editSiswaError" class="alert alert-danger d-none"></div>
                    <div class="row">

                        {{-- Kolom Kiri --}}
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

                            {{-- Dropdown Kelas dari DB --}}
                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_kelas_id" name="kelas_id" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dropdown Jurusan dari DB --}}
                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jurusan_id" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusanList as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                    @endforeach
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

                        {{-- Kolom Kanan --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2"><i class="bx bx-lock-alt me-1"></i>Akun Login</h6>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="edit_password" name="password"
                                        placeholder="Kosongkan jika tidak ingin ubah">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('edit_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti password.</small>
                            </div>

                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2"><i class="bx bx-image me-1"></i>Foto Profil</h6>
                            <div class="mb-2">
                                <img id="edit_foto_preview" src="" class="rounded d-none mb-2"
                                    style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ganti Foto</label>
                                <input type="file" class="form-control" name="foto" id="edit_foto_input"
                                    accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'edit_foto_preview')">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditSiswa">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditSiswa" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>