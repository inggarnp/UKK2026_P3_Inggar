{{--
    Sidebar Kepala Sekolah
    Simpan di: resources/views/partials/sidebar-kepala-sekolah.blade.php

    Cara integrasi di layouts/app.blade.php:
    @if(auth()->user()->role === 'guru')
        @php $jabatan = auth()->user()->guru?->jabatan ?? ''; @endphp
        @if($jabatan === 'kepala_sekolah')
            @include('partials.sidebar-kepala-sekolah')
        @else
            @include('partials.sidebar-guru')
        @endif
    @endif
--}}

<li class="menu-title">Menu Utama</li>

<li class="nav-item {{ request()->routeIs('kepala_sekolah.dashboard') ? 'active' : '' }}">
    <a href="{{ route('kepala_sekolah.dashboard') }}" class="nav-link">
        <span class="nav-icon">
            <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
        </span>
        <span class="nav-text">Dashboard</span>
    </a>
</li>

<li class="menu-title mt-2">Aspirasi Sarana</li>

<li class="nav-item {{ request()->routeIs('kepala_sekolah.aspirasi.*') ? 'active' : '' }}">
    <a href="{{ route('kepala_sekolah.aspirasi.index') }}" class="nav-link">
        <span class="nav-icon">
            <iconify-icon icon="solar:list-bold-duotone"></iconify-icon>
        </span>
        <span class="nav-text">Data Aspirasi</span>
        @php
            try {
                $totalMenunggu = \App\Models\Aspirasi::where('status', 'Menunggu')->count();
            } catch (\Exception $e) {
                $totalMenunggu = 0;
            }
        @endphp
        @if($totalMenunggu > 0)
            <span class="badge bg-warning text-dark ms-auto">{{ $totalMenunggu }}</span>
        @endif
    </a>
</li>

<li class="nav-item {{ request()->routeIs('kepala_sekolah.history.*') ? 'active' : '' }}">
    <a href="{{ route('kepala_sekolah.history.index') }}" class="nav-link">
        <span class="nav-icon">
            <iconify-icon icon="solar:history-bold-duotone"></iconify-icon>
        </span>
        <span class="nav-text">Histori Status</span>
    </a>
</li>