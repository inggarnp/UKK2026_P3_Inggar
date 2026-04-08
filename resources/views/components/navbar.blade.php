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

                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">3</span>
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline"><small>Clear All</small></a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
                            <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom text-wrap">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="img-fluid me-2 avatar-sm rounded-circle" alt="avatar-1" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0"><span class="fw-medium">Josephine Thompson</span> commented on admin panel</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3">
                            <a href="javascript:void(0);" class="link-primary">View All Notifications <i class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32"
                                src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="avatar-1">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome!</h6>
                        
                        {{-- Info user yang sedang login --}}
                        <div class="px-3 py-2 border-bottom">
                            <p class="mb-0 fw-semibold text-dark">{{ Auth::user()->email }}</p>
                            <span class="badge bg-primary">{{ Auth::user()->role }}</span>
                        </div>

                        <a class="dropdown-item" href="#">
                            <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i>
                            <span class="align-middle">Profile</span>
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-message-dots text-muted fs-18 align-middle me-1"></i>
                            <span class="align-middle">Messages</span>
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-cog text-muted fs-18 align-middle me-1"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-log-out fs-18 align-middle me-1"></i>
                                <span class="align-middle">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>

{{-- Script: simpan JWT & user ke localStorage saat login --}}
@if(session('jwt_token'))
<script>
    localStorage.setItem('jwt_token', '{{ session('jwt_token') }}');
    localStorage.setItem('user', JSON.stringify({
        id: {{ Auth::user()->id }},
        email: '{{ Auth::user()->email }}',
        role: '{{ Auth::user()->role }}'
    }));
</script>
@endif

{{-- Script: hapus localStorage saat logout --}}
@if(session('clear_storage'))
<script>
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('user');
</script>
@endif
<!-- ========== Topbar End ========== -->