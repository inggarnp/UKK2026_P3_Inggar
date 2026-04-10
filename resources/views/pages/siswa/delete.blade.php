{{-- Modal Konfirmasi Hapus Siswa --}}
<div class="modal fade" id="deleteSiswaModal" tabindex="-1" aria-labelledby="deleteSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSiswaModalLabel">
                    <i class="bx bx-error-circle me-2"></i>Konfirmasi Hapus Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bx bx-trash text-danger" style="font-size: 3rem;"></i>
                </div>

                <p class="text-center mb-2">Apakah Anda yakin ingin menghapus data siswa ini?</p>

                <div class="alert alert-light border text-center mb-3">
                    <strong class="fs-5" id="delete_siswa_name">-</strong><br>
                    <small class="text-muted">NIS: <span id="delete_siswa_nis" class="font-monospace">-</span></small>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="bx bx-error-circle me-1"></i>
                    <strong>Perhatian:</strong> Akun login siswa ini juga akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSiswaBtn">
                    <i class="bx bx-trash me-1"></i>
                    <span id="btnDeleteSiswaText">Ya, Hapus</span>
                    <span id="btnDeleteSiswaLoader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>