<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon/favicon-96x96.png') }}" sizes="96x96">

    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ParlourPOS - Help Portal.</title>

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css"
        integrity="sha512-57lHfvbE5GLWA20qDTCTRUpKntBnFlG0V+x8f3q3DqZK6XlHGVsbpCCI/kadtaFBMEYmq5IeVZnsWjWx2QoQtw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Rubik', sans-serif !important;
        overflow: hidden ! important;
    }
    .custom-verflow-scroll-y{
        overflow-y: scroll !important;
        overflow-x: hidden !important;
        height: 90vh !important;
        scrollbar-width: thin;
        scrollbar-color: #5e1d66 #f5f5f5;
    }
    .thm-outline-btn{
        border: 1px solid #5e1d66 !important;
        color: #5e1d66 !important;
    }
    .thm-outline-btn:hover{
        background-color: #5e1d66 !important;
        color: #ffffff !important;
    }
    .thm-active-btn{
        background-color: #5e1d66 !important;
        color: #ffffff !important;
    }
    </style>

    <style>
    .sidebar .nav-item .collapse .collapse-inner .collapse-item.disabled,
    .sidebar .nav-item .collapsing .collapse-inner .collapse-item.disabled {
        color: #a9adb1ff;
        /* Bootstrap's $gray-600 */
        cursor: not-allowed;
        text-decoration: none;
    }
    </style>

    @yield('style')

    @vite(['resources/sass/app.scss'])
    @vite(['resources/css/custom-style.css'])


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-icon ">
                    <img width="50px" src="{{ asset('images/favicon/favicon-96x96.png') }}" alt="Logo">
                </div>
                <div class="sidebar-brand-text mx-1">Help Portal</div>
            </a>

            <li class="nav-item">
                <a class="nav-link not-collapsed {{ request()->routeIs('help.*') ? 'not-collapsed' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#SetupDropdown" aria-expanded="true" aria-controls="SetupDropdown">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Setup</span>
                </a>
                <div id="SetupDropdown" class="collapse {{ request()->routeIs('help.*') ? 'show' : '' }}" aria-labelledby="SetupDropdownTwo" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('help.setup.loyalty') ? 'active' : '' }}" href="{{ route('help.setup.loyalty') }}">
                            Loyalty
                        </a>
                    </div>
                </div>
            </li>


            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline mt-2">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <div class="sidebar-footer">
                © 2025 ParlourPOS.com
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top toolbar-border">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Notification Dropdown -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- <span class="badge badge-danger badge-counter">1</span> -->
                            </a>
                            <!-- Dropdown - Alerts -->
                            <!-- <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown" style="width: 300px;">
                                <h6 class="dropdown-header">
                                    Notifications
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold">
                                            Your subscription ended on <b>September 30, 2025</b>.
                                            You are now in a <b>7-day grace period</b>, ending on <b>October 7, 2025</b>.
                                            Please make payment as soon as possible to extend your subscription and avoid service interruption.
                                        </span>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Notifications</a>
                            </div> -->
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 user-name">{{ $authUser->name }}({{ $authUser->POSID }})</span>
                                <img class="img-profile rounded-circle" src="{{asset('images/default-profile.png')}}">
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('setup.profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous">
    </script>


    @vite([
    'resources/js/app.js'
    ])


    <script>
    $(document).ready(async function() {

        $("#sidebarToggle").click(function() {
            $(".sidebar").toggleClass("toggled");
            $(".collapse").removeClass("show");
        });

        $('[data-toggle="tooltip"]').tooltip({
            html: true
        });
    });
    </script>


    @yield('script')
</body>

</html>