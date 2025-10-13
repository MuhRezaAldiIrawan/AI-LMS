<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bosowa LMS - B-Learning</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        .sidebar-divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin: 16px 0;
        }

        .sidebar-divider::before,
        .sidebar-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ccc;
        }

        .sidebar-divider:not(:empty)::before {
            margin-right: .75em;
        }

        .sidebar-divider:not(:empty)::after {
            margin-left: .75em;
        }
    </style>

    @yield('css')
</head>

<body>

    <!--==================== Preloader Start ====================-->
    <div class="preloader">
        <div class="loader"></div>
    </div>

    @include('layouts.sidebar')


    <div class="dashboard-main-wrapper">
        @include('layouts.navbar')


        <div class="dashboard-body">
            @yield('content')
        </div>


        <div class="dashboard-footer">
            <div class="flex-between flex-wrap gap-16">
                <p class="text-gray-300 text-13 fw-normal"> &copy; Copyright Edmate 2024, All Right Reserverd</p>
                <div class="flex-align flex-wrap gap-16">
                    <a href="#"
                        class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">License</a>
                    <a href="#"
                        class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">More
                        Themes</a>
                    <a href="#"
                        class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Documentation</a>
                    <a href="#"
                        class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Support</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/boostrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/phosphor-icon.js') }}"></script>

    @yield('js')

    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
