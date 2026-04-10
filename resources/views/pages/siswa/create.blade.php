{{-- Modal Tambah Siswa --}}
<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-labelledby="addSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSiswaModalLabel">
                    <i class="bx bx-user-plus me-2"></i>Tambah Siswa Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSiswaForm">
                @csrf
                <div class="modal-body">
                    <div id="addSiswaError" class="alert alert-danger d-none"></div>

                    <div class="row">
                        {{-- Kolom Kiri: Data Siswa --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Siswa</h6>

                            <div class="mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nis"
                                    placeholder="Nomor Induk Siswa" maxlength="20" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama"
                                    placeholder="Nama lengkap siswa" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="kelas"
                                    placeholder="Contoh: XII RPL 1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jurusan" required>
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
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_l" value="L" required>
                                        <label class="form-check-label" for="jk_l">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_p" value="P">
                                        <label class="form-check-label" for="jk_p">Perempuan</label>
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
                                    placeholder="Alamat lengkap siswa"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp"
                                    placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                        </div>

                        {{-- Kolom Kanan: Akun Login --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="bx bx-lock-alt me-1"></i>Akun Login Siswa
                            </h6>

                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Akun ini digunakan siswa untuk login ke sistem dan melihat status pengaduan mereka.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email"
                                    placeholder="email@sekolah.sch.id" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="add_password"
                                        placeholder="Minimal 6 karakter" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('add_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddSiswa">
                        <span><i class="bx bx-save me-1"></i> Simpan</span>
                        <span id="btnLoaderAddSiswa" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    let input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="bx bx-show"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="bx bx-hide"></i>';
    }
}
</script>