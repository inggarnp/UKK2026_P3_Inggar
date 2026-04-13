{{-- Modal Konfirmasi Hapus Aspirasi --}}
<div class="modal fade" id="deleteAspirasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bx bx-error-circle me-2"></i>Konfirmasi Hapus Aspirasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bx bx-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-2">Apakah Anda yakin ingin menghapus aspirasi dari:</p>
                <div class="alert alert-light border text-center mb-3">
                    <strong class="fs-5" id="delete_aspirasi_name">-</strong>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bx bx-error-circle me-1"></i>
                    <strong>Perhatian:</strong> Seluruh histori status dan foto bukti terkait juga akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAspirasBtn">
                    <i class="bx bx-trash me-1"></i>
                    <span id="btnDeleteAspirasText">Ya, Hapus</span>
                    <span id="btnDeleteAspirasLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>