<!-- ========== Topbar Start ========== -->
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome!</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">

                {{-- Dark mode --}}
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                {{-- ═══════ NOTIFIKASI ═══════ --}}
                <div class="dropdown topbar-item" id="notifDropdown">
                    <button type="button" class="topbar-button position-relative" id="notifBtn"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        {{-- Badge jumlah belum dibaca --}}
                        <span id="notifBadge"
                            class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill d-none">0</span>
                    </button>

                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" style="min-width:360px">
                        {{-- Header --}}
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="m-0 fw-semibold">Notifikasi</h6>
                            <button class="btn btn-sm btn-link p-0 text-muted" id="btnBacaSemua">
                                Tandai semua dibaca
                            </button>
                        </div>

                        {{-- List notif --}}
                        <div data-simplebar style="max-height:320px" id="notifList">
                            <div class="text-center py-4 text-muted" id="notifEmpty">
                                <iconify-icon icon="solar:bell-off-bold-duotone"
                                    class="fs-32 d-block mb-2"></iconify-icon>
                                <small>Tidak ada notifikasi baru</small>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="text-center py-2 border-top">
                            <a href="#" class="small text-primary" id="btnLihatSemua">Lihat semua notifikasi</a>
                        </div>
                    </div>
                </div>

                {{-- ═══════ USER DROPDOWN ═══════ --}}
                <div class="dropdown topbar-item">
                    @php
                        $role = auth()->user()->role;

                        $fotoUser = null;
                        $namaUser = auth()->user()->email;

                        if ($role === 'siswa') {
                            $profilUser = \App\Models\Siswa::where('user_id', auth()->id())->first();
                            $fotoUser = $profilUser?->foto
                                ? asset('assets/images/users/siswa/' . $profilUser->foto)
                                : null;
                            $namaUser = $profilUser?->nama ?? auth()->user()->email;
                        } elseif ($role === 'guru') {
                            $profilUser = \App\Models\Guru::where('user_id', auth()->id())->first();
                            $fotoUser = $profilUser?->foto
                                ? asset('assets/images/users/guru/' . $profilUser->foto)
                                : null;
                            $namaUser = $profilUser?->nama ?? auth()->user()->email;
                            // Cek kepala sekolah
                            $isKepsek = $profilUser?->jabatan === 'kepala_sekolah';
                        } elseif ($role === 'petugas_sarana') {
                            $profilUser = \App\Models\PetugasSarana::where('user_id', auth()->id())->first();
                            $fotoUser = $profilUser?->foto
                                ? asset('assets/images/users/petugas/' . $profilUser->foto)
                                : null;
                            $namaUser = $profilUser?->nama ?? auth()->user()->email;
                        }

                        $fotoUser = $fotoUser ?? asset('assets/images/users/avatar-1.jpg');

                        // FIX: kepala_sekolah pakai route kepala_sekolah.profile
                        $profileRoute = match (true) {
                            $role === 'siswa' => 'siswa.profile',
                            $role === 'guru' && ($isKepsek ?? false) => 'kepala_sekolah.profile',
                            $role === 'guru' => 'guru.profile',
                            $role === 'petugas_sarana' => 'petugas.profile',
                            default => null,
                        };

                        $roleLabel = match ($role) {
                            'siswa' => 'Siswa',
                            'guru' => isset($isKepsek) && $isKepsek ? 'Kepala Sekolah' : 'Guru',
                            'petugas_sarana' => 'Petugas Sarana',
                            'admin' => 'Admin',
                            default => ucfirst(str_replace('_', ' ', $role)),
                        };
                    @endphp


                    <a type="button" class="topbar-button" data-bs-toggle="dropdown">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32" height="32" src="{{ $fotoUser }}"
                                alt="avatar" style="object-fit:cover">
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" style="min-width:220px">
                        {{-- Info user --}}
                        <div class="px-3 py-3 d-flex align-items-center gap-2 border-bottom">
                            <img src="{{ $fotoUser }}" class="rounded-circle" width="40" height="40"
                                style="object-fit:cover">
                            <div class="overflow-hidden">
                                <p class="mb-0 fw-semibold text-dark text-truncate small">{{ $namaUser }}</p>
                                <p class="mb-0 text-muted" style="font-size:11px">{{ auth()->user()->email }}</p>
                                <span class="badge bg-soft-primary text-primary"
                                    style="font-size:10px">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                            </div>
                        </div>

                        {{-- Menu --}}
                        @if ($profileRoute)
                            <a class="dropdown-item" href="{{ route($profileRoute) }}">
                                <iconify-icon icon="solar:user-circle-bold-duotone"
                                    class="text-muted fs-18 align-middle me-2"></iconify-icon>
                                <span class="align-middle">Profil & Pengaturan</span>
                            </a>
                        @endif

                        <div class="dropdown-divider my-1"></div>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <iconify-icon icon="solar:logout-2-bold-duotone"
                                    class="fs-18 align-middle me-2"></iconify-icon>
                                <span class="align-middle">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>

{{-- ═══════ MODAL NOTIF SEMUA ═══════ --}}
<div class="modal fade" id="modalSemuaNotif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <iconify-icon icon="solar:bell-bing-bold-duotone" class="me-2 text-primary"></iconify-icon>
                    Semua Notifikasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="allNotifBody">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // ─── POLLING NOTIFIKASI ───────────────────────────────────────
        const POLLING_INTERVAL = 15000; // 15 detik
        let lastNotifCount = 0;

        function renderNotifItem(n) {
            let tipeColor = {
                info: 'text-info',
                success: 'text-success',
                warning: 'text-warning',
                danger: 'text-danger'
            };
            let tipeBg = {
                info: 'bg-soft-info',
                success: 'bg-soft-success',
                warning: 'bg-soft-warning',
                danger: 'bg-soft-danger'
            };
            let color = tipeColor[n.tipe] || 'text-primary';
            let bg = tipeBg[n.tipe] || 'bg-soft-primary';
            return `
        <div class="dropdown-item py-2 px-3 border-bottom notif-item d-flex gap-2 align-items-start"
             style="cursor:pointer; white-space:normal"
             data-id="${n.id}" data-url="${n.url || ''}">
            <div class="avatar-xs ${bg} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                <iconify-icon icon="${n.icon}" class="${color}" style="font-size:16px"></iconify-icon>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <p class="mb-0 fw-semibold small text-dark">${n.judul}</p>
                <p class="mb-0 small text-muted text-truncate">${n.pesan}</p>
                <span class="text-muted" style="font-size:11px">${n.waktu}</span>
            </div>
            <button class="btn btn-sm p-0 text-muted btn-hapus-notif flex-shrink-0" data-id="${n.id}" title="Hapus">
                <i class="bx bx-x"></i>
            </button>
        </div>`;
        }

        function loadNotifDropdown() {
            $.get('{{ url('/notif') }}', function(res) {
                let total = res.total_belum_baca;

                // Update badge
                if (total > 0) {
                    $('#notifBadge').text(total > 99 ? '99+' : total).removeClass('d-none');
                } else {
                    $('#notifBadge').addClass('d-none');
                }

                // Play suara notif jika ada notif baru
                if (total > lastNotifCount && lastNotifCount !== null) {
                    playNotifSound();
                }
                lastNotifCount = total;

                // Render list
                if (res.notifikasi.length === 0) {
                    $('#notifList').html(`<div class="text-center py-4 text-muted" id="notifEmpty">
                <iconify-icon icon="solar:bell-off-bold-duotone" class="fs-32 d-block mb-2"></iconify-icon>
                <small>Tidak ada notifikasi baru</small></div>`);
                } else {
                    let html = res.notifikasi.map(renderNotifItem).join('');
                    $('#notifList').html(html);
                }
            });
        }

        function playNotifSound() {
            try {
                let ctx = new(window.AudioContext || window.webkitAudioContext)();
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.setValueAtTime(520, ctx.currentTime);
                osc.frequency.setValueAtTime(440, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) {}
        }

        // Klik item notif → tandai dibaca → redirect jika ada url
        $(document).on('click', '.notif-item', function(e) {
            if ($(e.target).hasClass('btn-hapus-notif') || $(e.target).closest('.btn-hapus-notif').length) return;
            let id = $(this).data('id');
            let url = $(this).data('url');
            $.post('{{ url('/notif') }}/' + id + '/baca', {
                _token: '{{ csrf_token() }}'
            });
            if (url) {
                window.location.href = url;
            } else {
                loadNotifDropdown();
            }
        });

        // Hapus satu notif
        $(document).on('click', '.btn-hapus-notif', function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            $.ajax({
                url: '{{ url('/notif') }}/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: () => loadNotifDropdown()
            });
        });

        // Tandai semua dibaca
        $('#btnBacaSemua').on('click', function() {
            $.post('{{ url('/notif/baca-semua') }}', {
                _token: '{{ csrf_token() }}'
            }, function() {
                loadNotifDropdown();
            });
        });

        // Lihat semua notif → modal
        $('#btnLihatSemua').on('click', function(e) {
            e.preventDefault();
            $('#modalSemuaNotif').modal('show');
            $.get('{{ url('/notif') }}', function(res) {
                if (res.notifikasi.length === 0) {
                    $('#allNotifBody').html(
                        '<div class="text-center py-5 text-muted"><iconify-icon icon="solar:bell-off-bold-duotone" class="fs-48 d-block mb-2"></iconify-icon><p>Belum ada notifikasi</p></div>'
                        );
                    return;
                }
                let html = res.notifikasi.map(function(n) {
                    let tipeColor = {
                        info: 'text-info',
                        success: 'text-success',
                        warning: 'text-warning',
                        danger: 'text-danger'
                    };
                    let tipeBg = {
                        info: 'bg-soft-info',
                        success: 'bg-soft-success',
                        warning: 'bg-soft-warning',
                        danger: 'bg-soft-danger'
                    };
                    return `<div class="d-flex gap-3 align-items-start p-3 border-bottom ${n.dibaca ? 'opacity-75' : ''}">
                <div class="avatar-sm ${tipeBg[n.tipe]||'bg-soft-primary'} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                    <iconify-icon icon="${n.icon}" class="${tipeColor[n.tipe]||'text-primary'}" style="font-size:20px"></iconify-icon>
                </div>
                <div class="flex-grow-1">
                    <p class="mb-0 fw-semibold ${n.dibaca ? 'text-muted' : 'text-dark'}">${n.judul}</p>
                    <p class="mb-1 small text-muted">${n.pesan}</p>
                    <span class="text-muted" style="font-size:11px">${n.waktu}</span>
                    ${!n.dibaca ? `<span class="badge bg-primary ms-2" style="font-size:10px">Baru</span>` : ''}
                </div>
            </div>`;
                }).join('');
                $('#allNotifBody').html(html);
            });
        });

        // ─── INIT ────────────────────────────────────────────────────
        $(document).ready(function() {
            loadNotifDropdown();
            // Polling setiap 15 detik
            setInterval(loadNotifDropdown, POLLING_INTERVAL);
        });
    </script>
@endpush

{{-- Hapus localStorage saat logout --}}
@if (session('clear_storage'))
    <script>
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user');
    </script>
@endif
<!-- ========== Topbar End ========== -->
