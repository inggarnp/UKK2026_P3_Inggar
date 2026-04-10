{{-- ============================================================ --}}
{{-- SIMPAN FILE INI SEBAGAI: resources/views/pages/kelas/read.blade.php --}}
{{-- ============================================================ --}}
<div class="modal fade" id="showKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-detail me-2"></i>Detail Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-bold text-muted small" width="40%">Nama Kelas</td>
                        <td id="show_nama_kelas" class="fw-semibold">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Tingkat</td>
                        <td id="show_tingkat">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Jurusan</td>
                        <td id="show_jurusan">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Ruangan</td>
                        <td id="show_ruangan">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Tahun Ajaran</td>
                        <td id="show_tahun_ajaran">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Jumlah Siswa</td>
                        <td id="show_jumlah_siswa">-</td>
                    </tr>
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <tr>
                        <td class="fw-bold text-muted small">Dibuat</td>
                        <td id="show_created_at" class="small text-muted">-</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted small">Diupdate</td>
                        <td id="show_updated_at" class="small text-muted">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>