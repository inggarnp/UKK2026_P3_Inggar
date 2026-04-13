{{-- Modal Update Status Aspirasi --}}
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-transfer me-2"></i>Update Status Aspirasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStatusForm">
                <input type="hidden" id="edit_status_input_id">
                <div class="modal-body">
                    <div id="editStatusError" class="alert alert-danger d-none"></div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="editStatusSelect" name="status" required>
                            <option value="Menunggu">⏳ Menunggu</option>
                            <option value="Proses">🔄 Proses</option>
                            <option value="Selesai">✅ Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Feedback / Catatan Admin</label>
                        <textarea class="form-control" id="editFeedbackInput" name="feedback"
                            rows="4" maxlength="500"
                            placeholder="Tulis tanggapan atau keterangan penyelesaian..."></textarea>
                        <small class="text-muted">Maksimal 500 karakter. Feedback ini akan dilihat oleh pelapor.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEditStatus">
                        <i class="bx bx-save me-1"></i> Simpan
                        <span id="btnLoaderEditStatus" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>