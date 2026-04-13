{{-- Modal Tambah Siswa --}}
<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user-plus me-2"></i>Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSiswaForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="addSiswaError" class="alert alert-danger d-none"></div>
                    <div class="row">

                        {{-- Kolom Kiri --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2">Data Siswa</h6>

                            <div class="mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nis" placeholder="Nomor Induk Siswa" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama lengkap siswa" required>
                            </div>

                            {{-- Dropdown Kelas dari DB --}}
                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" name="kelas_id" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dropdown Jurusan dari DB --}}
                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jurusan_id" required>
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
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="add_jk_l" value="L" required>
                                        <label class="form-check-label" for="add_jk_l">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="add_jk_p" value="P">
                                        <label class="form-check-label" for="add_jk_p">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap siswa"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp" placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted border-bottom pb-2"><i class="bx bx-lock-alt me-1"></i>Akun Login</h6>
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Akun ini digunakan siswa untuk login dan melihat status pengaduan.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="email@sekolah.sch.id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="add_password" placeholder="Minimal 6 karakter" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('add_password', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>

                            <h6 class="mb-3 mt-4 text-muted border-bottom pb-2"><i class="bx bx-image me-1"></i>Foto Profil</h6>
                            <div class="mb-3">
                                <label class="form-label">Upload Foto</label>
                                <input type="file" class="form-control" name="foto" id="add_foto_input"
                                    accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFoto(this, 'add_foto_preview')">
                                <small class="text-muted">Format: JPG, PNG — Maks 2MB. Bisa diisi nanti.</small>
                            </div>
                            <div class="mb-3">
                                <img id="add_foto_preview" src="" class="rounded d-none"
                                    style="width:100px;height:100px;object-fit:cover;border:2px solid #dee2e6">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddSiswa">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddSiswa" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    let input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.innerHTML = input.type === 'password' ? '<i class="bx bx-hide"></i>' : '<i class="bx bx-show"></i>';
}
function previewFoto(input, previewId) {
    let preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>