@extends('auth.Auth')

@section('form')

    {{-- Hapus localStorage saat halaman login dimuat setelah logout --}}
    @if (session('clear_storage'))
        <script>
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user');
        </script>
    @endif


@section('form')
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="index.html" class="logo-dark">
                                        <img src="assets/images/logo-dark.png" height="24" alt="logo dark">
                                    </a>
                                    <a href="index.html" class="logo-light">
                                        <img src="assets/images/logo-light.png" height="24" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">Sign In</h2>
                                <p class="text-muted mt-1 mb-4">Enter your email address and password to access admin panel.
                                </p>

                                <div class="mb-4">
                                    <form action="{{ route('login') }}" method="POST" class="authentication-form">
                                        @csrf

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                {{ $errors->first() }}
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label" for="example-email">Email</label>
                                            <input type="email" id="example-email" name="identifier" class="form-control"
                                                placeholder="Masukkan email, NIS, atau NIP" value="{{ old('identifier') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="example-password">Password</label>
                                            <input type="password" id="example-password" name="password"
                                                class="form-control" placeholder="Enter your password">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="mb-1 text-center d-grid">
                                            <button type="submit" class="btn btn-soft-primary">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="assets/images/small/img-10.jpg" alt="" class="w-100 h-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
