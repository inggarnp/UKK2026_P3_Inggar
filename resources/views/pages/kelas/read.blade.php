
{{-- ============================================================ --}}
{{-- SIMPAN FILE INI SEBAGAI: resources/views/pages/kelas/update.blade.php --}}
{{-- ============================================================ --}}
<div class="modal fade" id="editKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editKelasForm">
                @csrf
                <input type="hidden" id="edit_kelas_id">
                <div class="modal-body">
                    <div id="editKelasError" class="alert alert-danger d-none"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tingkat" name="tingkat" required>
                                    <option value="X">X (Kelas 10)</option>
                                    <option value="XI">XI (Kelas 11)</option>
                                    <option value="XII">XII (Kelas 12)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_tahun_ajaran" name="tahun_ajaran" maxlength="10" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jurusan_id" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach(\App\Models\Jurusan::orderBy('nama_jurusan')->get() as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ruangan</label>
                                <select class="form-select" id="edit_ruangan_id" name="ruangan_id">
                                    <option value="">-- Belum ditentukan --</option>
                                    @foreach(\App\Models\Ruangan::orderBy('nama_ruangan')->get() as $r)
                                        <option value="{{ $r->id }}">{{ $r->kode_ruangan }} — {{ $r->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditKelas">
                        <i class="bx bx-save me-1"></i> Update
                        <span id="btnLoaderEditKelas" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>