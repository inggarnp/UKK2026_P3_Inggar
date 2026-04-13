{{-- Modal Tambah Aspirasi --}}
<div class="modal fade" id="addAspirasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Tambah Aspirasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAspirasForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="addAspirasError" class="alert alert-danger d-none"></div>

                    {{-- Pilih Pelapor --}}
                    <div class="mb-3">
                        <label class="form-label">Role Pelapor <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role_pelapor" id="role_siswa" value="siswa" checked>
                                <label class="form-check-label" for="role_siswa">Siswa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role_pelapor" id="role_guru" value="guru">
                                <label class="form-check-label" for="role_guru">Guru</label>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Pelapor (dinamis berdasarkan role) --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
                        <select class="form-select" name="user_id" id="selectPelapor" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswaList as $s)
                                <option value="{{ $s->user_id }}" data-role="siswa">{{ $s->nama }} ({{ $s->nis }})</option>
                            @endforeach
                            @foreach($guruList as $g)
                                <option value="{{ $g->user_id }}" data-role="guru" class="d-none">{{ $g->nama }} ({{ $g->nip ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Kategori --}}
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoriList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Lokasi --}}
                            <div class="mb-3">
                                <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="lokasi"
                                    placeholder="Contoh: Kelas X-RPL, Lapangan, Toilet lantai 2"
                                    maxlength="100" required>
                            </div>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="4"
                            placeholder="Jelaskan detail masalah atau aspirasi yang ingin disampaikan..."
                            maxlength="500" required></textarea>
                        <small class="text-muted">Maksimal 500 karakter.</small>
                    </div>

                    {{-- Foto --}}
                    <div class="mb-3">
                        <label class="form-label">Foto Bukti <span class="text-muted">(opsional)</span></label>
                        <input type="file" class="form-control" name="foto" id="add_aspiras_foto"
                            accept="image/jpg,image/jpeg,image/png"
                            onchange="previewAspirasiFoto(this)">
                        <small class="text-muted">Format: JPG, PNG — Maksimal 2MB.</small>
                        <div class="mt-2">
                            <img id="add_aspiras_foto_preview" src="" class="rounded d-none"
                                style="max-height:150px;object-fit:cover;border:2px solid #dee2e6">
                        </div>
                    </div>

                    {{-- Status Awal --}}
                    <div class="mb-3">
                        <label class="form-label">Status Awal</label>
                        <select class="form-select" name="status">
                            <option value="Menunggu" selected>⏳ Menunggu</option>
                            <option value="Proses">🔄 Proses</option>
                            <option value="Selesai">✅ Selesai</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddAspiras">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddAspiras" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview foto aspirasi
function previewAspirasiFoto(input) {
    let preview = document.getElementById('add_aspiras_foto_preview');
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle dropdown pelapor berdasarkan role
document.querySelectorAll('input[name="role_pelapor"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        let role = this.value;
        let select = document.getElementById('selectPelapor');
        select.value = '';

        // Tampilkan/sembunyikan option sesuai role
        select.querySelectorAll('option[data-role]').forEach(function(opt) {
            if (opt.dataset.role === role) {
                opt.classList.remove('d-none');
            } else {
                opt.classList.add('d-none');
                opt.selected = false;
            }
        });

        // Ubah placeholder
        select.querySelector('option[value=""]').textContent =
            role === 'siswa' ? '-- Pilih Siswa --' : '-- Pilih Guru --';
    });
});
</script>