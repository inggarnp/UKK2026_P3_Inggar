<!-- ========== App Menu Start ========== -->
<div class="main-nav">

    @php
        $dashboardRoute = match(auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'guru'  => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => 'login'
        };
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

            {{-- ADMIN ONLY --}}
            @if(auth()->user()->role == 'admin')

            {{-- Manajemen Users --}}
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarUsers" data-bs-toggle="collapse">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Manajemen Users </span>
                </a>
                <div class="collapse" id="sidebarUsers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="#">Users</a>
                        </li>
                    </ul>
                </div>
            </li>

            @endif

            {{-- SEMUA ROLE --}}
            {{-- Buat Surat --}}
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarSurat" data-bs-toggle="collapse">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Buat Surat </span>
                </a>
                <div class="collapse" id="sidebarSurat">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">SKTM</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Domisili</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Kematian</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Ahli Waris</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Pindah</a></li>
                    </ul>
                </div>
            </li>

            {{-- Arsip --}}
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarArsip" data-bs-toggle="collapse">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:folder-with-files-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Arsip Surat </span>
                </a>
                <div class="collapse" id="sidebarArsip">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="#">Arsip</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- ADMIN ONLY --}}
            @if(auth()->user()->role == 'admin')

            {{-- Manajemen Warga --}}
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarWarga" data-bs-toggle="collapse">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Manajemen Warga </span>
                </a>
                <div class="collapse" id="sidebarWarga">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Rukun</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Warga</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link" href="#">Keluarga</a></li>
                    </ul>
                </div>
            </li>

            @endif

            <li class="menu-title mt-2">Other</li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Profil Kelurahan </span>
                </a>
            </li>

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->