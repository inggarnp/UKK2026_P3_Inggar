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
            {{-- Info limit harian --}}
            <div class="text-end">
                <span class="badge {{ $sisaLimit > 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} fs-6 px-3 py-2">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1"></iconify-icon>
                    Sisa hari ini: {{ $sisaLimit }}/3
                </span>
            </div>
        </div>

        @if($sisaLimit <= 0)
            <div class="alert alert-danger">
                <iconify-icon icon="solar:danger-bold-duotone" class="me-1"></iconify-icon>
                <strong>Batas tercapai!</strong> Kamu sudah mengirim 3 aspirasi hari ini. Coba lagi besok.
            </div>
        @else

        @if($sisaLimit == 1)
            <div class="alert alert-warning mb-3">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                Perhatian: ini adalah aspirasi terakhirmu untuk hari ini.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div id="aspirasAlert" class="d-none mb-3"></div>

                {{-- Info alur --}}
                <div class="alert alert-info mb-4">
                    <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                    Aspirasi kamu akan direview oleh <strong>wali kelas</strong> terlebih dahulu sebelum diteruskan ke Petugas Sarana.
                </div>

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
                            {{-- Saksi --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Saksi <span class="text-muted small">(opsional)</span>
                                </label>
                                <select class="form-select" name="saksi_id" id="selectSaksi">
                                    <option value="">-- Pilih Saksi (siswa lain) --</option>
                                    @foreach ($siswaSaksiList as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->nama }} — {{ $s->kelas?->nama_kelas ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih siswa lain sebagai saksi aspirasi ini.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Lokasi: Dropdown Ruangan --}}
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

                    {{-- Info Ruangan Autofill --}}
                    <div id="infoRuangan" class="alert alert-info d-none mb-3 py-2">
                        <div class="row g-2">
                            <div class="col-4">
                                <small class="text-muted d-block">Kode Ruangan</small>
                                <strong id="infoKode">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Lantai</small>
                                <strong id="infoLantai">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Gedung</small>
                                <strong id="infoGedung">-</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Lokasi Manual --}}
                    <div id="lokasiManualWrapper" class="mb-3">
                        <label class="form-label">
                            Atau Isi Lokasi Manual
                            <span class="text-muted small">(jika tidak ada di daftar)</span>
                        </label>
                        <input type="text" class="form-control" name="lokasi_manual"
                            id="lokasiManualInput"
                            placeholder="Contoh: Lapangan basket, Kantin, Toilet belakang..."
                            maxlength="150">
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="5"
                            placeholder="Jelaskan secara detail masalah atau aspirasi yang ingin kamu sampaikan..."
                            maxlength="500" required id="keteranganInput"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 500 karakter.</small>
                            <small class="text-muted"><span id="charCount">0</span>/500</small>
                        </div>
                    </div>

                    {{-- Foto --}}
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
                        <button type="submit" class="btn btn-primary" id="btnKirim">
                            <iconify-icon icon="solar:send-square-bold-duotone" class="me-1"></iconify-icon>
                            Kirim Aspirasi
                            <span id="btnLoader" class="spinner-border spinner-border-sm d-none ms-1"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
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

$('#formInputAspirasi').on('submit', function(e) {
    e.preventDefault();
    let ruanganId    = $('#selectRuangan').val();
    let lokasiManual = $('#lokasiManualInput').val().trim();
    if (!ruanganId && !lokasiManual) {
        $('#aspirasAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
            .html('<i class="bx bx-error me-1"></i> Pilih ruangan atau isi lokasi manual.');
        return;
    }
    let formData = new FormData(this);
    if (ruanganId) formData.delete('lokasi_manual');

    $('#btnKirim').prop('disabled', true);
    $('#btnLoader').removeClass('d-none');
    $('#aspirasAlert').addClass('d-none');

    $.ajax({
        url: '{{ route("siswa.aspirasi.store") }}',
        method: 'POST', data: formData, processData: false, contentType: false,
        success: function(res) {
            if (res.success) {
                let sisa = parseInt(res.sisa_limit) || 0;

                $('#aspirasAlert').removeClass('d-none alert-danger').addClass('alert alert-success')
                    .html('<i class="bx bx-check-circle me-1"></i> ' + res.message);

                // Update badge sisa limit di header
                let badgeEl = $('.badge.fs-6');
                badgeEl.html('<iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1"></iconify-icon> Sisa hari ini: ' + sisa + '/3');
                if (sisa <= 0) {
                    badgeEl.removeClass('bg-soft-success text-success').addClass('bg-soft-danger text-danger');
                    setTimeout(() => location.reload(), 1500);
                } else if (sisa == 1) {
                    badgeEl.removeClass('bg-soft-success text-success').addClass('bg-soft-warning text-warning');
                }

                $('#formInputAspirasi')[0].reset();
                $('#fotoPreview').addClass('d-none');
                $('#charCount').text('0');
                $('#infoRuangan').addClass('d-none');
                $('#lokasiManualInput').prop('disabled', false);
                $('#lokasiManualWrapper').removeClass('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        error: function(xhr) {
            let msg = xhr.status === 429
                ? xhr.responseJSON?.message
                : (Object.values(xhr.responseJSON?.errors ?? {}).flat().join('<br>') || 'Terjadi kesalahan.');
            $('#aspirasAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                .html('<i class="bx bx-error me-1"></i> ' + msg);
        },
        complete: function() { $('#btnKirim').prop('disabled', false); $('#btnLoader').addClass('d-none'); }
    });
});
</script>
@endpush