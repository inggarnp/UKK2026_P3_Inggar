

{{-- ============================================================ --}}
{{-- SIMPAN FILE INI SEBAGAI: resources/views/pages/kelas/delete.blade.php --}}
{{-- ============================================================ --}}
<div class="modal fade" id="deleteKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bx bx-error-circle me-2"></i>Konfirmasi Hapus Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bx bx-trash text-danger" style="font-size:3rem"></i>
                </div>
                <p class="text-center mb-2">Apakah Anda yakin ingin menghapus kelas ini?</p>
                <div class="alert alert-light border text-center mb-3">
                    <strong class="fs-5" id="delete_kelas_name">-</strong>
                </div>
                <div class="alert alert-danger mb-0">
                    <i class="bx bx-error-circle me-1"></i>
                    <strong>Peringatan:</strong> Kelas hanya bisa dihapus jika tidak ada siswa di dalamnya!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteKelasBtn">
                    <i class="bx bx-trash me-1"></i> Ya, Hapus
                    <span id="btnDeleteKelasLoader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>