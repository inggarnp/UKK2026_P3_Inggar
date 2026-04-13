{{-- ============================================================ --}}
{{-- SIMPAN FILE INI SEBAGAI: resources/views/pages/kelas/create.blade.php --}}
{{-- ============================================================ --}}

{{-- Modal Tambah Kelas --}}
<div class="modal fade" id="addKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addKelasForm">
                @csrf
                <div class="modal-body">
                    <div id="addKelasError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_kelas"
                                    placeholder="Contoh: XII RPL 1" maxlength="20" required>
                                <small class="text-muted">Format: [Tingkat] [Jurusan Singkat] [Nomor]</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="X">X (Kelas 10)</option>
                                    <option value="XI">XI (Kelas 11)</option>
                                    <option value="XII">XII (Kelas 12)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tahun_ajaran"
                                    placeholder="Contoh: 2025/2026" maxlength="10" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach(\App\Models\Jurusan::orderBy('nama_jurusan')->get() as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ruangan</label>
                                <select class="form-select" name="ruangan_id">
                                    <option value="">-- Belum ditentukan --</option>
                                    @foreach(\App\Models\Ruangan::orderBy('nama_ruangan')->get() as $r)
                                        <option value="{{ $r->id }}">{{ $r->kode_ruangan }} — {{ $r->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kosongkan jika belum ada ruangan tetap.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAddKelas">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderAddKelas" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>