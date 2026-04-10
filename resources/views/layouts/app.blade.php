<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Vendor css -->
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @stack('styles')
</head>

<body>

    <div class="wrapper">

        @include('components.navbar')

        @include('components.sidebar')

        <div class="page-content">
            <div class="container-fluid">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>document.write(new Date().getFullYear())</script> &copy; Admin Dashboard
                        </div>
                    </div>
                </div>
            </footer>
        </div>

    </div>

    <!-- Vendor Javascript -->
    <script src="{{ asset('assets/js/vendor.js') }}"></script>

    <!-- App Javascript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- jQuery — harus ada sebelum @stack('scripts') -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @stack('scripts')

</body>

</html>