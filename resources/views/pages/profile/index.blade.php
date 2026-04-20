@extends('layouts.app')
@section('title', 'Profil & Pengaturan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="mb-4">
            <h4 class="mb-1">
                <iconify-icon icon="solar:user-circle-bold-duotone" class="me-2 text-primary"></iconify-icon>
                Profil & Pengaturan
            </h4>
            <p class="text-muted mb-0">Kelola informasi akun dan keamanan kamu</p>
        </div>

        {{-- Notif first login --}}
        @if($firstLoginNotif && in_array($role, ['siswa', 'guru']) && !$sudahSetKode)
        <div class="alert alert-warning alert-dismissible d-flex align-items-start gap-3 mb-4" role="alert">
            <iconify-icon icon="solar:shield-warning-bold-duotone" class="fs-28 flex-shrink-0 mt-1"></iconify-icon>
            <div>
                <h6 class="alert-heading mb-1">Amankan Akunmu! 🔐</h6>
                <p class="mb-0 small">
                    Selamat datang! Kamu perlu membuat <strong>kode verifikasi</strong> untuk bisa mengirim aspirasi.
                    Lengkapi di bagian bawah ini.
                </p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">

            {{-- ═══ KOLOM KIRI ═══ --}}
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body py-4">
                        <div class="position-relative d-inline-block mb-3">
                            <img id="fotoPreviewSidebar" src="{{ $fotoUrl }}"
                                class="rounded-circle" width="100" height="100"
                                style="object-fit:cover;border:3px solid #dee2e6">
                            <label for="fotoInputMain"
                                class="position-absolute bottom-0 end-0 bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;cursor:pointer" title="Ganti foto">
                                <iconify-icon icon="solar:camera-bold-duotone" style="font-size:14px;color:white"></iconify-icon>
                            </label>
                        </div>
                        <h6 class="mb-1">{{ $profil?->nama ?? $user->email }}</h6>
                        <span class="badge bg-soft-primary text-primary">
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        </span>
                        <div class="small text-muted mt-1">{{ $user->email }}</div>

                        {{-- Info per role --}}
                        <div class="mt-3 pt-3 border-top text-start">
                            @if($role === 'siswa')
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">NIS</span><strong>{{ $profil?->nis ?? '-' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Kelas</span><strong>{{ $profil?->nama_kelas ?? '-' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Jurusan</span><strong>{{ $profil?->nama_jurusan ?? '-' }}</strong>
                                </div>
                            @elseif(in_array($role, ['guru', 'kepala_sekolah']))
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">NIP</span><strong>{{ $profil?->nip ?? '-' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Jabatan</span>
                                    <strong>{{ $profil?->jabatan_label ?? ucfirst(str_replace('_',' ',$profil?->jabatan ?? '-')) }}</strong>
                                </div>
                            @elseif($role === 'petugas_sarana')
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">NIP</span><strong>{{ $profil?->nip ?? '-' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Status</span>
                                    <span class="badge {{ ($profil?->status ?? '') === 'aktif' ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                        {{ ucfirst($profil?->status ?? '-') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Badge kode verif --}}
                        @if(in_array($role, ['siswa', 'guru']))
                        <div class="mt-3 pt-3 border-top">
                            @if($sudahSetKode)
                                <span class="badge bg-soft-success text-success">
                                    <iconify-icon icon="solar:shield-check-bold-duotone" class="me-1"></iconify-icon>
                                    Kode Verifikasi Aktif
                                </span>
                            @else
                                <span class="badge bg-soft-danger text-danger">
                                    <iconify-icon icon="solar:shield-warning-bold-duotone" class="me-1"></iconify-icon>
                                    Kode Verifikasi Belum Diset
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══ KOLOM KANAN ═══ --}}
            <div class="col-md-8">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <iconify-icon icon="solar:pen-bold-duotone" class="me-2 text-primary"></iconify-icon>
                            Edit Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="profileAlert" class="d-none mb-3"></div>

                        <form id="formEditProfile" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="fotoInputMain" name="foto" class="d-none"
                                accept="image/jpg,image/jpeg,image/png"
                                onchange="previewFotoProfil(this)">

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama"
                                    value="{{ $profil?->nama ?? '' }}" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="no_hp"
                                    value="{{ $profil?->no_hp ?? '' }}" maxlength="15">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-muted small">(kosongkan jika tidak ubah)</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                        id="inputPasswordBaru" placeholder="Min. 6 karakter" minlength="6">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('inputPasswordBaru', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4" id="konfirmasiPassWrapper" style="display:none">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="inputPasswordKonfirm" placeholder="Ulangi password baru">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('inputPasswordKonfirm', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>

                            @php
                                $updateRoute = match($role) {
                                    'siswa'           => 'siswa.profile.update',
                                    'guru'            => 'guru.profile.update',
                                    'kepala_sekolah'  => 'kepala_sekolah.profile.update',
                                    'petugas_sarana'  => 'petugas.profile.update',
                                    default           => null,
                                };
                            @endphp

                            <button type="submit" class="btn btn-primary" id="btnSimpanProfil">
                                <iconify-icon icon="solar:floppy-disk-bold-duotone" class="me-1"></iconify-icon>
                                Simpan Perubahan
                                <span id="loaderProfil" class="spinner-border spinner-border-sm d-none ms-1"></span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ═══ KODE VERIFIKASI — hanya siswa & guru, hanya jika belum diset ═══ --}}
                {{-- $sudahSetKode sudah di-query fresh dari DB saat controller berjalan --}}
                @if(in_array($role, ['siswa', 'guru']))
                    @if(!$sudahSetKode)
                    <div class="card border-warning">
                        <div class="card-header bg-soft-warning border-warning">
                            <h5 class="card-title mb-0 text-warning">
                                <iconify-icon icon="solar:lock-bold-duotone" class="me-2"></iconify-icon>
                                Set Kode Verifikasi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-4">
                                <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                Kode ini adalah kode rahasia yang hanya kamu tahu. Akan diminta saat mengirim aspirasi.<br>
                                <strong class="text-danger">⚠ Hanya bisa diset SEKALI dan tidak bisa diubah.</strong>
                            </div>

                            <div id="kodeVerifAlert" class="d-none mb-3"></div>

                            <form id="formSetKode">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Kode Verifikasi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="kode_verifikasi"
                                            id="inputKodeVerif" placeholder="Min. 4 karakter" minlength="4" maxlength="20" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePass('inputKodeVerif', this)">
                                            <i class="bx bx-hide"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Konfirmasi Kode <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="kode_verifikasi_confirmation"
                                            id="inputKodeVerifKonfirm" placeholder="Ulangi kode" minlength="4" maxlength="20" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePass('inputKodeVerifKonfirm', this)">
                                            <i class="bx bx-hide"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning text-dark" id="btnSimpanKode">
                                    <iconify-icon icon="solar:lock-bold-duotone" class="me-1"></iconify-icon>
                                    Simpan Kode Verifikasi
                                    <span id="loaderKode" class="spinner-border spinner-border-sm d-none ms-1"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    {{-- Kode verif sudah diset — card sukses --}}
                    <div class="card border-success">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="avatar-md bg-soft-success rounded-circle d-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:shield-check-bold-duotone" class="fs-24 text-success"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="mb-1 text-success">Kode Verifikasi Sudah Aktif ✅</h6>
                                <p class="text-muted mb-0 small">Akun kamu sudah aman. Kode verifikasi tidak dapat diubah.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(id, btn) {
    let inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.innerHTML = inp.type === 'password' ? '<i class="bx bx-hide"></i>' : '<i class="bx bx-show"></i>';
}

function previewFotoProfil(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => { document.getElementById('fotoPreviewSidebar').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}

$('#inputPasswordBaru').on('input', function() {
    if ($(this).val().length > 0) $('#konfirmasiPassWrapper').slideDown(200);
    else { $('#konfirmasiPassWrapper').slideUp(200); $('#inputPasswordKonfirm').val(''); }
});

// ─── Submit edit profil ───────────────────────────────────────
$('#formEditProfile').on('submit', function(e) {
    e.preventDefault();
    let btn = $('#btnSimpanProfil');
    btn.prop('disabled', true);
    $('#loaderProfil').removeClass('d-none');
    $('#profileAlert').addClass('d-none');

    $.ajax({
        url: '{{ $updateRoute ? route($updateRoute) : "#" }}',
        method: 'POST',
        data: new FormData(this),
        processData: false, contentType: false,
        success: function(res) {
            $('#profileAlert').removeClass('d-none alert-danger').addClass('alert alert-success')
                .html('<i class="bx bx-check-circle me-1"></i> ' + res.message);
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            let msg = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Gagal menyimpan.');
            $('#profileAlert').removeClass('d-none alert-success').addClass('alert alert-danger').html(msg);
        },
        complete: () => { btn.prop('disabled', false); $('#loaderProfil').addClass('d-none'); }
    });
});

// ─── Submit kode verifikasi ───────────────────────────────────
@if(in_array($role, ['siswa', 'guru']) && !$sudahSetKode)
@php
    $setKodeRoute = match($role) {
        'siswa' => 'siswa.profile.set-kode',
        'guru'  => 'guru.profile.set-kode',
        default => null,
    };
@endphp
$('#formSetKode').on('submit', function(e) {
    e.preventDefault();
    let kode        = $('#inputKodeVerif').val();
    let kodeKonfirm = $('#inputKodeVerifKonfirm').val();

    if (kode !== kodeKonfirm) {
        $('#kodeVerifAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
            .html('Konfirmasi kode tidak cocok.');
        return;
    }
    if (kode.length < 4) {
        $('#kodeVerifAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
            .html('Kode minimal 4 karakter.');
        return;
    }

    $('#btnSimpanKode').prop('disabled', true);
    $('#loaderKode').removeClass('d-none');
    $('#kodeVerifAlert').addClass('d-none');

    $.ajax({
        url: '{{ $setKodeRoute ? route($setKodeRoute) : "#" }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            if (res.success) {
                $('#kodeVerifAlert').removeClass('d-none alert-danger').addClass('alert alert-success')
                    .html('<iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon> '
                        + res.message + ' Memuat ulang halaman...');
                // Hard reload setelah 1.5 detik agar controller baca fresh dari DB
                setTimeout(function() {
                    window.location.href = window.location.pathname + '?t=' + Date.now();
                }, 1500);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            let msg = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Gagal menyimpan.');
            $('#kodeVerifAlert').removeClass('d-none alert-success').addClass('alert alert-danger').html(msg);
            $('#btnSimpanKode').prop('disabled', false);
            $('#loaderKode').addClass('d-none');
        }
    });
});
@endif
</script>
@endpush