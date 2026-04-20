@extends('layouts.app')
@section('title', 'Input Aspirasi | Siswa')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">
                    <iconify-icon icon="solar:pen-new-square-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Input Aspirasi
                </h4>
                <p class="text-muted mb-0">Sampaikan masukan atau laporan terkait sarana dan prasarana sekolah</p>
            </div>
            <span class="badge {{ $sisaLimit > 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} fs-6 px-3 py-2">
                <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1"></iconify-icon>
                Sisa hari ini: {{ $sisaLimit }}/3
            </span>
        </div>

        {{-- Belum punya kode verifikasi --}}
        @if(!$sudahSetKode)
            <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-22 mt-1 flex-shrink-0"></iconify-icon>
                <div>
                    <h6 class="mb-1">Kamu belum punya kode verifikasi!</h6>
                    <p class="mb-2 small">Kode verifikasi diperlukan untuk mengirim aspirasi. Silakan set kode verifikasi di halaman profil terlebih dahulu.</p>
                    <a href="{{ route($role.'.profile') }}" class="btn btn-warning btn-sm">
                        <iconify-icon icon="solar:lock-bold-duotone" class="me-1"></iconify-icon>
                        Set Kode Verifikasi Sekarang
                    </a>
                </div>
            </div>
        @endif

        @if($sisaLimit <= 0)
            <div class="alert alert-danger">
                <iconify-icon icon="solar:danger-bold-duotone" class="me-1"></iconify-icon>
                <strong>Batas tercapai!</strong> Kamu sudah mengirim 3 aspirasi hari ini. Coba lagi besok.
            </div>
        @elseif($sudahSetKode)

        @if($sisaLimit == 1)
            <div class="alert alert-warning mb-3">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                Ini adalah aspirasi terakhirmu untuk hari ini.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                    Aspirasi kamu akan langsung diteruskan ke <strong>Petugas Sarana</strong> untuk ditindaklanjuti.
                </div>

                {{-- Form (tidak ada kode verif di sini, dipindah ke modal) --}}
                <form id="formInputAspirasi" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoriList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Saksi <span class="text-muted small">(opsional)</span></label>
                                <select class="form-select" name="saksi_id">
                                    <option value="">-- Pilih Saksi --</option>
                                    @foreach ($siswaSaksiList as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} — {{ $s->kelas?->nama_kelas ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi / Ruangan <span class="text-danger">*</span></label>
                        <select class="form-select" name="ruangan_id" id="selectRuangan">
                            <option value="">-- Pilih Ruangan (atau isi manual di bawah) --</option>
                            @foreach ($ruanganList as $r)
                                <option value="{{ $r->id }}"
                                    data-kode="{{ $r->kode_ruangan }}"
                                    data-lantai="{{ $r->lantai }}"
                                    data-gedung="{{ $r->gedung }}">
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="infoRuangan" class="alert alert-info d-none mb-3 py-2">
                        <div class="row g-2">
                            <div class="col-4"><small class="text-muted d-block">Kode Ruangan</small><strong id="infoKode">-</strong></div>
                            <div class="col-4"><small class="text-muted d-block">Lantai</small><strong id="infoLantai">-</strong></div>
                            <div class="col-4"><small class="text-muted d-block">Gedung</small><strong id="infoGedung">-</strong></div>
                        </div>
                    </div>

                    <div id="lokasiManualWrapper" class="mb-3">
                        <label class="form-label">Atau Isi Lokasi Manual <span class="text-muted small">(jika tidak ada di daftar)</span></label>
                        <input type="text" class="form-control" name="lokasi_manual" id="lokasiManualInput"
                            placeholder="Contoh: Lapangan basket, Kantin, Toilet belakang..." maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan / Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="4"
                            placeholder="Jelaskan secara detail masalah atau aspirasi..." maxlength="500" required id="keteranganInput"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 500 karakter.</small>
                            <small class="text-muted"><span id="charCount">0</span>/500</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Foto Bukti <span class="text-muted">(opsional)</span></label>
                        <input type="file" class="form-control" name="foto" id="fotoInput"
                            accept="image/jpg,image/jpeg,image/png" onchange="previewFoto(this)">
                        <small class="text-muted">Format: JPG, PNG — Maksimal 2MB.</small>
                        <div class="mt-2">
                            <img id="fotoPreview" src="" class="rounded d-none"
                                style="max-height:200px;object-fit:cover;border:2px solid #dee2e6">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        {{-- Tombol ini trigger modal konfirmasi, BUKAN submit langsung --}}
                        <button type="button" class="btn btn-primary" id="btnBukaKonfirmasi">
                            <iconify-icon icon="solar:send-square-bold-duotone" class="me-1"></iconify-icon>
                            Kirim Aspirasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ═══════ MODAL 1: KONFIRMASI ═══════ --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 pb-2">
                <iconify-icon icon="solar:chat-square-like-bold-duotone" class="text-primary" style="font-size:3.5rem"></iconify-icon>
                <h5 class="mt-3 mb-2">Kirim Aspirasi?</h5>
                <p class="text-muted mb-0">
                    Pastikan semua informasi sudah benar. Aspirasi yang sudah dikirim tidak bisa diubah.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal, Cek Lagi
                </button>
                <button type="button" class="btn btn-primary" id="btnLanjutKode">
                    <iconify-icon icon="solar:lock-bold-duotone" class="me-1"></iconify-icon>
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════ MODAL 2: KODE VERIFIKASI ═══════ --}}
<div class="modal fade" id="modalKodeVerif" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" id="btnTutupKodeVerif"></button>
            </div>
            <div class="modal-body px-4 pb-2">
                <div class="text-center mb-4">
                    <iconify-icon icon="solar:shield-check-bold-duotone" class="text-warning" style="font-size:3rem"></iconify-icon>
                    <h5 class="mt-3 mb-1">Masukkan Kode Verifikasi</h5>
                    <p class="text-muted small mb-0">Masukkan kode rahasia yang sudah kamu set di profil untuk membuktikan bahwa ini benar-benar kamu.</p>
                </div>

                <div id="kodeVerifModalAlert" class="d-none mb-3"></div>

                <div class="mb-4">
                    <label class="form-label">Kode Verifikasi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg text-center"
                            id="inputKodeVerif" placeholder="••••••"
                            autocomplete="off" maxlength="20">
                        <button class="btn btn-outline-secondary" type="button" id="btnToggleKodeVerif">
                            <i class="bx bx-hide"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary" id="btnBatalKode">
                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Form
                </button>
                <button type="button" class="btn btn-success" id="btnSubmitDenganKode">
                    <iconify-icon icon="solar:send-square-bold-duotone" class="me-1"></iconify-icon>
                    Kirim Sekarang
                    <span id="loaderSubmit" class="spinner-border spinner-border-sm d-none ms-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════ MODAL 3: SUKSES ═══════ --}}
<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center px-4 py-5">
                <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success" style="font-size:4rem"></iconify-icon>
                <h5 class="mt-3 mb-2 text-success">Aspirasi Berhasil Dikirim!</h5>
                <p class="text-muted mb-1" id="suksesPesan">-</p>
                <p class="text-muted small" id="suksesLimit"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2">
                <a href="{{ route('siswa.aspirasi.index') }}" class="btn btn-outline-primary btn-sm">
                    <iconify-icon icon="solar:list-bold-duotone" class="me-1"></iconify-icon>
                    Lihat Daftar Aspirasi
                </a>
                <button type="button" class="btn btn-primary btn-sm" id="btnKirimLagi">
                    <iconify-icon icon="solar:pen-new-square-bold-duotone" class="me-1"></iconify-icon>
                    Kirim Lagi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Preview foto ──────────────────────────────────────────
function previewFoto(input) {
    let preview = document.getElementById('fotoPreview');
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    } else { preview.classList.add('d-none'); }
}

$('#keteranganInput').on('input', function() { $('#charCount').text($(this).val().length); });

$('#selectRuangan').on('change', function() {
    let selected = $(this).find('option:selected');
    if ($(this).val()) {
        $('#infoKode').text(selected.data('kode') || '-');
        $('#infoLantai').text(selected.data('lantai') || '-');
        $('#infoGedung').text(selected.data('gedung') || '-');
        $('#infoRuangan').removeClass('d-none');
        $('#lokasiManualInput').val('').prop('disabled', true);
        $('#lokasiManualWrapper').addClass('d-none');
    } else {
        $('#infoRuangan').addClass('d-none');
        $('#lokasiManualInput').prop('disabled', false);
        $('#lokasiManualWrapper').removeClass('d-none');
    }
});

// ─── Step 1: Klik Kirim → validasi form → buka modal konfirmasi ──
$('#btnBukaKonfirmasi').on('click', function() {
    // Validasi manual dulu
    let form = document.getElementById('formInputAspirasi');
    if (!form.checkValidity()) { form.reportValidity(); return; }

    let ruanganId    = $('#selectRuangan').val();
    let lokasiManual = $('#lokasiManualInput').val().trim();
    if (!ruanganId && !lokasiManual) {
        alert('Pilih ruangan atau isi lokasi manual terlebih dahulu.');
        return;
    }

    $('#modalKonfirmasi').modal('show');
});

// ─── Step 2: Klik Ya → tutup konfirmasi → buka modal kode ────
$('#btnLanjutKode').on('click', function() {
    $('#modalKonfirmasi').modal('hide');
    $('#inputKodeVerif').val('');
    $('#kodeVerifModalAlert').addClass('d-none');
    setTimeout(() => $('#modalKodeVerif').modal('show'), 400);
});

// ─── Toggle show/hide kode verif ─────────────────────────────
$('#btnToggleKodeVerif').on('click', function() {
    let inp = $('#inputKodeVerif');
    inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
    $(this).html(inp.attr('type') === 'password' ? '<i class="bx bx-hide"></i>' : '<i class="bx bx-show"></i>');
});

// ─── Batal dari modal kode → kembali ke form ─────────────────
$('#btnBatalKode, #btnTutupKodeVerif').on('click', function() {
    $('#modalKodeVerif').modal('hide');
});

// ─── Step 3: Submit dengan kode verifikasi ───────────────────
$('#btnSubmitDenganKode').on('click', function() {
    let kode = $('#inputKodeVerif').val().trim();
    if (!kode) {
        $('#kodeVerifModalAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
            .html('<i class="bx bx-error me-1"></i> Masukkan kode verifikasi terlebih dahulu.');
        return;
    }

    let btn = $(this);
    btn.prop('disabled', true);
    $('#loaderSubmit').removeClass('d-none');
    $('#kodeVerifModalAlert').addClass('d-none');

    // Cek kode ke server dulu
    $.ajax({
        url: '{{ route("siswa.profile.cek-kode") }}',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}', kode: kode },
        success: function(res) {
            if (!res.valid) {
                $('#kodeVerifModalAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                    .html('<i class="bx bx-error me-1"></i> ' + res.message);
                btn.prop('disabled', false);
                $('#loaderSubmit').addClass('d-none');
                return;
            }

            // Kode valid → submit form
            let formData = new FormData(document.getElementById('formInputAspirasi'));
            let ruanganId = $('#selectRuangan').val();
            if (ruanganId) formData.delete('lokasi_manual');
            // Tambahkan kode verifikasi ke formData
            formData.append('kode_verifikasi', kode);

            $.ajax({
                url: '{{ route("siswa.aspirasi.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(storeRes) {
                    if (storeRes.success) {
                        $('#modalKodeVerif').modal('hide');
                        $('#suksesPesan').text(storeRes.message);
                        let sisa = parseInt(storeRes.sisa_limit) || 0;
                        $('#suksesLimit').text('Sisa aspirasi hari ini: ' + sisa + '/3');

                        // Update badge di header
                        let badge = $('.badge.fs-6');
                        badge.html('<iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1"></iconify-icon> Sisa hari ini: ' + sisa + '/3');
                        if (sisa <= 0) badge.removeClass('bg-soft-success text-success').addClass('bg-soft-danger text-danger');
                        else if (sisa == 1) badge.removeClass('bg-soft-success text-success').addClass('bg-soft-warning text-warning');

                        setTimeout(() => $('#modalSukses').modal('show'), 400);

                        // Reset form
                        document.getElementById('formInputAspirasi').reset();
                        $('#fotoPreview').addClass('d-none');
                        $('#charCount').text('0');
                        $('#infoRuangan').addClass('d-none');
                        $('#lokasiManualInput').prop('disabled', false);
                        $('#lokasiManualWrapper').removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.status === 429
                        ? xhr.responseJSON?.message
                        : (Object.values(xhr.responseJSON?.errors ?? {}).flat().join('<br>') || 'Terjadi kesalahan.');
                    $('#kodeVerifModalAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                        .html('<i class="bx bx-error me-1"></i> ' + msg);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    $('#loaderSubmit').addClass('d-none');
                }
            });
        },
        error: function() {
            $('#kodeVerifModalAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                .html('<i class="bx bx-error me-1"></i> Gagal memverifikasi kode. Coba lagi.');
            btn.prop('disabled', false);
            $('#loaderSubmit').addClass('d-none');
        }
    });
});

// ─── Modal sukses: kirim lagi ─────────────────────────────────
$('#btnKirimLagi').on('click', function() {
    $('#modalSukses').modal('hide');
});
</script>
@endpush