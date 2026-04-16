<!-- ========== App Menu Start ========== -->
<div class="main-nav">

    @php
        $role = auth()->user()->role;
        $dashboardRoute = match ($role) {
            'admin'          => 'admin.dashboard',
            'guru'           => 'guru.dashboard',
            'siswa'          => 'siswa.dashboard',
            'petugas_sarana' => 'petugas.dashboard',
            default          => 'login',
        };
        $currentRoute = request()->route()?->getName() ?? '';
        $isActive = fn($name) => str_starts_with($currentRoute, $name) ? 'active' : '';
    @endphp

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

    <button type="button" class="button-sm-hover">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            <li class="nav-item">
                <a class="nav-link {{ $isActive($role.'.dashboard') }}" href="{{ route($dashboardRoute) }}">
                    <span class="nav-icon"><iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- ══════ ADMIN ══════ --}}
            @if ($role === 'admin')
                <li class="menu-title mt-2">Manajemen</li>

                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarUser" data-bs-toggle="collapse">
                        <span class="nav-icon"><iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Manajemen User</span>
                    </a>
                    <div class="collapse" id="sidebarUser">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.siswa') }}" href="{{ route('admin.siswa.index') }}">Siswa</a></li>
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.guru') }}" href="{{ route('admin.guru.index') }}">Guru</a></li>
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.petugas') }}" href="{{ route('admin.petugas.index') }}">Petugas Sarana</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarMaster" data-bs-toggle="collapse">
                        <span class="nav-icon"><iconify-icon icon="solar:folder-with-files-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Data Master</span>
                    </a>
                    <div class="collapse" id="sidebarMaster">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.kelas') }}" href="{{ route('admin.kelas.index') }}">Kelas</a></li>
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.jurusan') }}" href="{{ route('admin.jurusan.index') }}">Jurusan</a></li>
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.ruangan') }}" href="{{ route('admin.ruangan.index') }}">Ruangan</a></li>
                            <li class="sub-nav-item"><a class="sub-nav-link {{ $isActive('admin.kategori') }}" href="{{ route('admin.kategori.index') }}">Kategori Aspirasi</a></li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">Aspirasi</li>
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('admin.aspirasi') }}" href="{{ route('admin.aspirasi.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Kelola Aspirasi</span>
                    </a>
                </li>
            @endif

            {{-- ══════ GURU ══════ --}}
            @if ($role === 'guru')
                <li class="menu-title mt-2">Aspirasi Siswa</li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('guru.siswa-aspirasi') }}" href="{{ route('guru.siswa-aspirasi.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:eye-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Lihat Aspirasi Siswa</span>
                    </a>
                </li>

                <li class="menu-title mt-2">Aspirasi Saya</li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('guru.aspirasi.create') }}" href="{{ route('guru.aspirasi.create') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:pen-new-square-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Input Aspirasi</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('guru.aspirasi.index') }}" href="{{ route('guru.aspirasi.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:list-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Daftar Aspirasi</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('guru.aspirasi.history') }}" href="{{ route('guru.aspirasi.history') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:history-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Histori Aspirasi</span>
                    </a>
                </li>
            @endif

            {{-- ══════ SISWA ══════ --}}
            @if ($role === 'siswa')
                <li class="menu-title mt-2">Aspirasi</li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('siswa.aspirasi.create') }}" href="{{ route('siswa.aspirasi.create') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:pen-new-square-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Input Aspirasi</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('siswa.aspirasi.index') }}" href="{{ route('siswa.aspirasi.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:list-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Daftar Aspirasi</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('siswa.aspirasi.history') }}" href="{{ route('siswa.aspirasi.history') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:history-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Histori Aspirasi</span>
                    </a>
                </li>
            @endif

            {{-- ══════ PETUGAS SARANA ══════ --}}
            @if ($role === 'petugas_sarana')
                <li class="menu-title mt-2">Aspirasi</li>

                <li class="nav-item">
                    <a class="nav-link {{ $isActive('petugas.aspirasi') }}" href="{{ route('petugas.aspirasi.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:list-check-bold-duotone"></iconify-icon></span>
                        <span class="nav-text">Daftar Laporan</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->