<!-- ========== App Menu Start ========== -->
<div class="main-nav">

    @php
        $dashboardRoute = match (auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'guru'  => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => 'login',
        };
        $role = auth()->user()->role;
    @endphp

    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route($dashboardRoute) }}" class="logo-dark">
            <img src="{{ asset('assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset('assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
        </a>
        <a href="{{ route($dashboardRoute) }}" class="logo-light">
            <img src="{{ asset('assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset('assets/images/logo-light.png') }}" class="logo-lg" alt="logo light">
        </a>
    </div>

    <!-- Menu Toggle Button -->
    <button type="button" class="button-sm-hover">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route($dashboardRoute) }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            {{-- ═══════════ MENU ADMIN ═══════════ --}}
            @if ($role === 'admin')

                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarUser" data-bs-toggle="collapse">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Manajemen User </span>
                    </a>
                    <div class="collapse" id="sidebarUser">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.siswa.index') }}">Siswa</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.guru.index') }}">Guru</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.petugas.index') }}">Petugas Sarana</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarKategori" data-bs-toggle="collapse">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:folder-with-files-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Manajemen Kategori </span>
                    </a>
                    <div class="collapse" id="sidebarKategori">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.kelas.index') }}">Kelas</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.jurusan.index') }}">Jurusan</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.ruangan.index') }}">Ruangan</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('admin.kategori.index') }}">Kategori</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">Aspirasi</li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.aspirasi.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Kelola Aspirasi </span>
                    </a>
                </li>

            @endif

            {{-- ═══════════ MENU GURU ═══════════ --}}
            @if ($role === 'guru')

                <li class="menu-title mt-2">Aspirasi</li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guru.aspirasi.create') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:pen-new-square-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Input Aspirasi </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guru.aspirasi.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:list-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Daftar Aspirasi </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guru.aspirasi.history') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:history-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Histori Aspirasi </span>
                    </a>
                </li>

            @endif

            {{-- ═══════════ MENU SISWA ═══════════ --}}
            @if ($role === 'siswa')

                <li class="menu-title mt-2">Aspirasi</li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('siswa.aspirasi.create') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:pen-new-square-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Input Aspirasi </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('siswa.aspirasi.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:list-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Daftar Aspirasi </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('siswa.aspirasi.history') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:history-bold-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Histori Aspirasi </span>
                    </a>
                </li>

            @endif

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->