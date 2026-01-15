<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> -->

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

    <title>ParlourPOS - The ultimate beauty parlour POS.</title>

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css"
        integrity="sha512-57lHfvbE5GLWA20qDTCTRUpKntBnFlG0V+x8f3q3DqZK6XlHGVsbpCCI/kadtaFBMEYmq5IeVZnsWjWx2QoQtw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" />

    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('ckeditor5/ckeditor5.css') }}">
    <style>
    body {
        font-family: 'Rubik', sans-serif !important;
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
    
    <!-- Attendance Modal Styles -->
    <style>
    .attendance-table-container {
        overflow-y: scroll !important;
    }

    .attendance-table-container::-webkit-scrollbar {
        width: 8px;
        display: block;
    }

    .attendance-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .attendance-table-container::-webkit-scrollbar-thumb {
        background: #5e1d66;
        border-radius: 4px;
    }

    .attendance-table-container::-webkit-scrollbar-thumb:hover {
        background: #7b3b84;
    }

    .attendance-table-container {
        scrollbar-width: thin;
        scrollbar-color: #5e1d66 #f1f1f1;
    }
    </style>
    
    <!-- Scripts -->
    <script>
        var WinPos = {};
        var enabledSitefeatures = @json(getSiteFeatureNames());
        var userPermissions = @json(getUserPermissionNames());
        var loyaltySettings = @json(getLoyaltySettings());
        var accountInfoSettings = @json(getAccountInfoSettings());

        // Attendance URLs (available globally)
        var AttendanceUrls = {
            'getAttendanceData': "{{ route('attendance.data') }}",
            'saveAttendance': "{{ route('attendance.save') }}",
            'markAllPresent': "{{ route('attendance.mark-all-present') }}",
            'getDesignations': "{{ route('attendance.designations') }}",
            'checkTodayStatus': "{{ route('attendance.check-today-status') }}"
        };

    </script>
    
    @stack('url-scripts')

    @vite(['resources/sass/app.scss'])
    @vite(['resources/css/custom-theme/theme-purple.css'])
    @vite(['resources/css/custom-style.css'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img width="50px" src="{{ asset('images') }}/{{ session('accountInfo.POSID') }}/{{ session('accountInfo.logo') }}" alt="Logo">
                </div>
                <div class="sidebar-brand-text mx-1">{{ session('accountInfo.companyName') }}</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('service.*') || request()->routeIs('product.*') ? 'not-collapsed' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#serviceDropdown" aria-expanded="true"
                    aria-controls="serviceDropdown">
                    <i class="fa-solid fa-spa"></i>
                    <span>Product & Service</span>
                </a>
                <div id="serviceDropdown" class="collapse {{ request()->routeIs('service.*') || request()->routeIs('product.*') ? 'show' : '' }}" aria-labelledby="headingserviceDropdown" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item {{ request()->routeIs('product.*') ? 'active' : '' }}" href="{{ route('product.index') }}">
                            <i class="fa-solid fa-box"></i> Product
                        </a>
                        

                        @if(hasAccess('service.index'))
                        <a class="collapse-item {{ request()->routeIs('service.index') || request()->routeIs('service.show') ? 'active' : '' }}" href="{{ route('service.index') }}">
                            <i class="fa-solid fa-magic"></i> Service
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Services.">
                            <i class="fa-solid fa-magic"></i> Service
                        </a>
                        @endif

                        @if(hasAccess('service.category.index'))
                        <a class="collapse-item {{ request()->routeIs('service.category.*') ? 'active' : '' }}" href="{{ route('service.category.index') }}">
                            <i class="fa-solid fa-tags"></i> Category
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Categories.">
                            <i class="fa-solid fa-tags"></i> Category
                        </a>
                        @endif

                        @if(hasAccess('service.brand.index'))
                        <a class="collapse-item {{ request()->routeIs('service.brand.*') ? 'active' : '' }}" href="{{ route('service.brand.index') }}">
                            <i class="fa-solid fa-layer-group"></i> Brand
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Brands.">
                            <i class="fa-solid fa-layer-group"></i> Brand
                        </a>
                        @endif

                        @if(hasAccess('service.unit.index'))
                        <a class="collapse-item {{ request()->routeIs('service.unit.*') ? 'active' : '' }}" href="{{ route('service.unit.index') }}">
                            <i class="fa-solid fa-ruler-horizontal"></i> Unit
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Units.">
                            <i class="fa-solid fa-ruler-horizontal"></i> Unit
                        </a>
                        @endif

                        <a class="collapse-item {{ request()->routeIs('service.supplier.*') ? 'active' : '' }}" href="{{ route('service.supplier.index') }}">
                            <i class="fa-solid fa-truck"></i> Supplier
                        </a>
                    </div>
                </div>

            </li>
            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#salesDropdown" aria-expanded="true" aria-controls="salesDropdown">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Sales</span>
                </a>
                <div id="salesDropdown" class="collapse {{ request()->routeIs('sales.*') ? 'show' : '' }}" aria-labelledby="headingsalesDropdown" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        @if(hasAccess('sales.sale.index'))
                        <a class="collapse-item {{ request()->routeIs('sales.sale.index') || request()->routeIs('sales.sale.show') ? 'active' : '' }}" href="{{ route('sales.sale.index') }}">
                            <i class="fa-solid fa-dolly"></i> Sales
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Sales.">
                            <i class="fa-solid fa-dolly"></i> Sales
                        </a>
                        @endif

                        @if(hasAccess('sales.customer.index'))
                        <a class="collapse-item {{ request()->routeIs('sales.customer.index') || request()->routeIs('sales.customer.details') || request()->routeIs('sales.customer.loyalty') ? 'active' : '' }}" href="{{ route('sales.customer.index') }}">
                            <i class="fa-solid fa-people-carry-box"></i> Customer
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Customers.">
                            <i class="fa-solid fa-people-carry-box"></i> Customer
                        </a>
                        @endif

                    </div>
                </div>

            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#stockDropdown" aria-expanded="true" aria-controls="stockDropdown">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Stock</span>
                </a>
                <div id="stockDropdown" class="collapse {{ request()->routeIs('stock.*') ? 'show' : '' }}" aria-labelledby="headingstockDropdown" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item {{ request()->routeIs('stock.purchase.*') ? 'active' : '' }}" href="{{ route('stock.purchase.index') }}">
                            <i class="fa-solid fa-cart-shopping"></i> Purchases
                        </a>

                        <a class="collapse-item {{ request()->routeIs('stock.return.*') ? 'active' : '' }}" href="{{ route('stock.return.index') }}">
                            <i class="fa-solid fa-rotate-left"></i> Returns
                        </a>

                    </div>
                </div>

            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('utilities.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#UtilitiesDropdown" aria-expanded="true" aria-controls="UtilitiesDropdown">
                    <i class="fa-solid fa-toolbox"></i>
                    <span>Utilities</span>
                </a>
                <div id="UtilitiesDropdown" class="collapse {{ request()->routeIs('utilities.*') ? 'show' : '' }}" aria-labelledby="headingUtilitiesDropdown" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        {{-- Expenses --}}
                        @if(hasAccess('utilities.expenses.index'))
                        <a class="collapse-item {{ request()->routeIs('utilities.expenses.index') ? 'active' : '' }}" href="{{ route('utilities.expenses.index') }}">
                            <i class="fa-regular fa-handshake"></i> Expenses
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Expenses.">
                            <i class="fa-regular fa-handshake"></i> Expenses
                        </a>
                        @endif

                        {{-- Expenses Category --}}
                        @if(hasAccess('utilities.expense.category.index'))
                        <a class="collapse-item {{ request()->routeIs('utilities.expense.category.*') ? 'active' : '' }}" href="{{ route('utilities.expense.category.index') }}">
                            <i class="fa-solid fa-tags"></i> Expenses Category
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Expense Categories.">
                            <i class="fa-solid fa-tags"></i> Expenses Category
                        </a>
                        @endif

                    </div>
                </div>
            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#ReportsDropdown" aria-expanded="true" aria-controls="ReportsDropdown">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Reports</span>
                </a>
                <div id="ReportsDropdown" class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" aria-labelledby="headingReportsDropdown" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if(hasAccess('reports.sales.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.sales.details') ? 'active' : '' }}" href="{{ route('reports.sales.details') }}">
                            Sales Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Sales Reports.">
                            Sales Report
                        </a>
                        @endif

                        @if(hasAccess('reports.expense.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.expense.details') ? 'active' : '' }}" href="{{ route('reports.expense.details') }}">
                            Expense Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Sales Reports.">
                            Expense Report
                        </a>
                        @endif

                        
                        <a class="collapse-item {{ request()->routeIs('reports.discount-adjustment.details') ? 'active' : '' }}" href="{{ route('reports.discount-adjustment.details') }}">
                            Discount & Adjustment
                        </a>
                        
                        @if(hasAccess('reports.revenue.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.revenue.details') ? 'active' : '' }}" href="{{ route('reports.revenue.details') }}">
                            Revenue Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Revenue Reports.">
                            Revenue Report
                        </a>
                        @endif
                        
                        @if(hasAccess('reports.net-profit.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.net-profit.details') ? 'active' : '' }}" href="{{ route('reports.net-profit.details') }}">
                            Net Profit Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Net Profit Reports.">
                            Net Profit Report
                        </a>
                        @endif
                        
                        @if(hasAccess('reports.customer.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.customer.details') ? 'active' : '' }}" href="{{ route('reports.customer.details') }}">
                            Customer Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Customer Reports.">
                            Customer Report
                        </a>
                        @endif
                        
                        @if(hasAccess('reports.employee.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.employee.details') ? 'active' : '' }}" href="{{ route('reports.employee.details') }}">
                            Employee Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Employee Reports.">
                            Employee Report
                        </a>
                        @endif

                        @if(hasAccess('reports.staff.details'))
                        <a class="collapse-item {{ request()->routeIs('reports.staff.details') ? 'active' : '' }}" href="{{ route('reports.staff.details') }}">
                            Staff Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Staff Reports.">
                            Staff Report
                        </a>
                        @endif
                        
                        @if(hasAccess('reports.sms.history'))
                        <a class="collapse-item {{ request()->routeIs('reports.sms.history') ? 'active' : '' }}" href="{{ route('reports.sms.history') }}">
                            SMS History Report
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view SMS History Reports.">
                            SMS History Report
                        </a>
                        @endif
                        
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employee.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#EmployeeDropdown" aria-expanded="true" aria-controls="EmployeeDropdown">
                    <i class="fa-solid fa-users"></i>
                    <span>Employee</span>
                </a>
                <div id="EmployeeDropdown" class="collapse {{ request()->routeIs('employee.*') ? 'show' : '' }}" aria-labelledby="headingEmployeeDropdown" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if(hasAccess('employee.index'))
                        <a class="collapse-item {{ request()->routeIs('employee.index') || request()->routeIs('employee.details') || request()->routeIs('employee.show') || request()->routeIs('employee.edit') ? 'active' : '' }}" href="{{ route('employee.index') }}">
                            <i class="fa-solid fa-list"></i> Employee List
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to view Employees.">
                            <i class="fa-solid fa-list"></i> Employee List
                        </a>
                        @endif
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('media.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#MediaDropdown" aria-expanded="true" aria-controls="MediaDropdown">
                    <i class="fa-solid fa-images"></i>
                    <span>Media</span>
                </a>
                <div id="MediaDropdown" class="collapse {{ request()->routeIs('media.*') ? 'show' : '' }}" aria-labelledby="headingMediaDropdown" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('media.image.*') ? 'active' : '' }}" href="{{ route('media.image.index') }}">
                            <i class="fa-solid fa-image"></i> Image
                        </a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link @if(!\Request::is('*setup*') || Request::is('*setup/profile*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#SetupDropdown" aria-expanded="true"
                    aria-controls="SetupDropdown">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Setup</span>
                </a>
                <div id="SetupDropdown" class="collapse @if(\Request::is('*setup*') && !Request::is('*setup/profile*')) show @endif" aria-labelledby="SetupDropdownTwo" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        {{-- Account --}}
                        @if(hasAccess('setup.account.index'))
                        <a class="collapse-item {{ request()->routeIs('setup.account.*') ? 'active' : '' }}" href="{{ route('setup.account.index') }}">
                            Account
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to access Account.">
                            Account
                        </a>
                        @endif

                        {{-- User --}}
                        @if(hasAccess('setup.user.index'))
                        <a class="collapse-item {{ request()->routeIs('setup.user.*') ? 'active' : '' }}" href="{{ route('setup.user.index') }}">
                            User
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to access Users.">
                            User
                        </a>
                        @endif

                        {{-- Role --}}
                        @if(hasAccess('setup.role.index'))
                        <a class="collapse-item {{ request()->routeIs('setup.role.*') ? 'active' : '' }}" href="{{ route('setup.role.index') }}">
                            Role
                        </a>
                        @else
                        <a href="javascript:void(0);" class="collapse-item text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to access Roles.">
                            Role
                        </a>
                        @endif

                    </div>
                </div>

            </li>

            <hr class="sidebar-divider my-0">

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
                        <li class="nav-item position-relative">
                            <a title="Online Order" class="nav-link online-order-shortcut" 
                            href="{{ route('sales.sale.index') }}" id="onlineOrderBtnShotcut">
                            
                                <i class="fa-solid fa-basket-shopping fa-lg"></i>

                                <!-- Order count badge -->
                                <span class="menu-shortcut-icon-count" id="onlineOrderCount" style="display: none;">0</span>
                            </a>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item position-relative">
                            <a title="Unpublished Products" class="nav-link unpublished-product-shortcut" 
                            href="{{ route('product.index') }}" id="unpublishedProductBtnShortcut">
                            
                                <i class="fa-solid fa-box fa-lg"></i>

                                <!-- Unpublished products count badge -->
                                <span class="menu-shortcut-icon-count" id="unpublishedProductCount" style="display: none;">0</span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        @if(hasAccess('attendance.data'))
                        <li class="nav-item">
                            <a title="Attendance" class="nav-link" href="javascript:void(0);" id="attendanceBtnGlobal">
                                <i class="fa-solid fa-calendar-check"></i>
                            </a>
                        </li>
                        @endif

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item">
                            @if(hasAccess('pos.index'))
                            <a title="POS Terminal" class="nav-link pos-terminal" href="{{ route('pos.index') }}">
                                <i class="fa-solid fa-desktop"></i>
                            </a>
                            @else
                            <a href="javascript:void(0);" class="nav-link pos-terminal text-gray-400 cursor-not-allowed no-access-link" data-message="You do not have permission to access the POS Terminal."
                                title="POS Terminal">
                                <i class="fa-solid fa-desktop"></i>
                            </a>
                            @endif
                        </li>

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

    <!-- Attendance Modal Container (Global - available on all pages) -->
    <div id="attendanceModalContainerGlobal">
        @include('attendance/modal')
    </div>

    <!-- Session Expired Modal -->
    <div class="modal fade" id="sessionExpiredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Session Expired</h5>
                </div>
                <div class="modal-body">
                    Your session has expired. Please log in again.
                </div>
                <div class="modal-footer">
                    <a href="{{ route('login') }}" class="btn thm-btn-bg thm-btn-text-color">Login</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subscriptionAlertModal" tabindex="-1" role="dialog" aria-labelledby="subscriptionAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-warning">
            <div class="modal-header thm-btn-bg thm-btn-text-color">
                <h5 class="modal-title" id="subscriptionAlertModalLabel">
                Subscription Alert
                </h5>
                <button type="button" class="close btn thm-btn-text-color" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p>Your subscription period is ending soon. Please renew to avoid interruption of service.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="thm-btn-bg thm-btn-text-color btn-sm" data-dismiss="modal">OK</button>
            </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.min.js"></script>

    <script type="importmap">
    {
    "imports": {
        "ckeditor5": "{{ asset('ckeditor5/ckeditor5.js') }}",
        "ckeditor5/": "{{ asset('ckeditor5/') }}"
    }
    }
    </script>

    <script type="module" src="{{ asset('ckeditor-main.js') }}"></script>



    @vite([
        'resources/js/app.js',
        'resources/js/common.js',
        'resources/js/datatable_service.js',
        'resources/js/validator-script.js',
        'resources/js/subscription.js',
        'resources/js/attendance/attendance-script.js'
    ])
    
    @stack('vite-scripts')


    <script>


    $(document).ready(async function() {

        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-bottom-right",
            timeOut: 2000
        };

        WinPos.Common.CommonVariables = WinPos.Common.CommonVariables || {};

        const logoKey = 'accountLogo_' + accountInfoSettings.logo; // ✅ Cache key depends on filename
        const storedLogo = localStorage.getItem(logoKey);

        if (storedLogo) {
            WinPos.Common.CommonVariables.accountLogo = storedLogo;
        }else{
            try {
                const logoUrl = window.origin + '/images/' + accountInfoSettings.POSID + '/' + accountInfoSettings.logo;
                const logoBase64 = await WinPos.Common.getImageAsBase64(logoUrl);
                WinPos.Common.CommonVariables.accountLogo = logoBase64;

                localStorage.setItem(logoKey, logoBase64);

                // ✅ Optionally, remove all older logo caches
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith('accountLogo_') && key !== logoKey) {
                        localStorage.removeItem(key);
                    }
                });

            } catch (error) {
                console.error("⚠️ Logo load error:", error);
            }
        }


        $("#sidebarToggle").click(function() {
            $(".sidebar").toggleClass("toggled");
            $(".collapse").removeClass("show");
        });

        setTimeout(() => showSubscriptionModal(false), 1000);

        $('[data-toggle="tooltip"]').tooltip({
            html: true
        });

        // Online Order Notification Count - Poll every 1 second
        let onlineOrderCountUrl = "{{ route('sales.sale.pending.count') }}";
        let onlineOrderCountInterval = null;

        function updateOnlineOrderCount() {
            WinPos.Common.getAjaxCall(onlineOrderCountUrl, function(response) {
                if (response.status === 'success') {
                    let count = response.count || 0;
                    let $countSpan = $('#onlineOrderCount');
                    
                    if (count > 0) {
                        $countSpan.text(count).show();
                    } else {
                        $countSpan.hide();
                    }
                }
            }, function(error) {
                // Silently fail - don't show error for polling
                console.error('Failed to fetch online order count');
            });
        }

        // Start polling on page load
        updateOnlineOrderCount();
        onlineOrderCountInterval = setInterval(updateOnlineOrderCount, 1000);

        // Unpublished Products Notification Count - Poll every 5 minutes
        let unpublishedProductCountUrl = "{{ route('product.unpublished.count') }}";
        let unpublishedProductCountInterval = null;

        function updateUnpublishedProductCount() {
            WinPos.Common.getAjaxCall(unpublishedProductCountUrl, function(response) {
                if (response.status === 'success') {
                    let count = response.count || 0;
                    let $countSpan = $('#unpublishedProductCount');
                    
                    if (count > 0) {
                        $countSpan.text(count).show();
                    } else {
                        $countSpan.hide();
                    }
                }
            }, function(error) {
                // Silently fail - don't show error for polling
                console.error('Failed to fetch unpublished product count');
            });
        }

        // Start polling on page load (every 5 minutes = 300000ms)
        updateUnpublishedProductCount();
        unpublishedProductCountInterval = setInterval(updateUnpublishedProductCount, 300000);

        // Clear intervals when page is hidden (to save resources)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (onlineOrderCountInterval) {
                    clearInterval(onlineOrderCountInterval);
                    onlineOrderCountInterval = null;
                }
                if (unpublishedProductCountInterval) {
                    clearInterval(unpublishedProductCountInterval);
                    unpublishedProductCountInterval = null;
                }
            } else {
                if (!onlineOrderCountInterval) {
                    updateOnlineOrderCount();
                    onlineOrderCountInterval = setInterval(updateOnlineOrderCount, 1000);
                }
                if (!unpublishedProductCountInterval) {
                    updateUnpublishedProductCount();
                    unpublishedProductCountInterval = setInterval(updateUnpublishedProductCount, 300000);
                }
            }
        });
    });

    $(document).ajaxError(function(event, xhr) {
        if (xhr.status === 401 || xhr.status === 419) {
            $('#sessionExpiredModal').modal('show');
        }
    });

    $(document).on('click', '.no-access-link', function(e) {
        e.preventDefault();
        const message = $(this).data('message') || "Access denied!";
        toastr.error(message); // or your custom toast function
    });
    </script>

    @yield('script')
</body>

</html>
