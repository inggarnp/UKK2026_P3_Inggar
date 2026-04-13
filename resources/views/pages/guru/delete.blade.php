<div class="modal fade" id="deleteGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bx bx-error-circle me-2"></i>Konfirmasi Hapus Guru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                </div>
                <p class="text-center mb-2">Apakah Anda yakin ingin menghapus guru ini?</p>
                <div class="alert alert-light border text-center mb-3">
                    <strong class="fs-5" id="delete_guru_name">-</strong><br>
                    <small class="text-muted">NIP: <span id="delete_guru_nip" class="font-monospace">-</span></small>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bx bx-error-circle me-1"></i>
                    <strong>Perhatian:</strong> Akun login guru ini juga akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteGuruBtn">
                    <i class="bx bx-trash me-1"></i> Ya, Hapus
                    <span id="btnDeleteGuruLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>