<!DOCTYPE html>
<html lang="it" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid">
<head>
    <meta charset="utf-8" />
    <title>Produzione | {{ session('azienda_produzione')->ragione_sociale }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema di produzione gestya.it" name="description" />
    <meta content="gestya.it" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Layout config Js -->
    <script src="/default/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="/default/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/default/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/default/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="/default/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="/default/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- Begin page -->
<div id="layout-wrapper">
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="/produzione/dashboard" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ URL::asset(URL::asset(session('azienda_produzione')->immagine)) ?? '/logo.png' }}" alt="" height="22">
                                </span>
                            <span class="logo-lg">
                                    <img src="{{ URL::asset(URL::asset(session('azienda_produzione')->immagine)) ?? '/logo.png' }}" alt="" height="40">
                                </span>
                        </a>

                        <a href="/produzione/dashboard" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ URL::asset(URL::asset(session('azienda_produzione')->immagine)) ?? '/logo.png' }}" alt="" height="22">
                                </span>
                            <span class="logo-lg">
                                    <img src="{{ URL::asset(URL::asset(session('azienda_produzione')->immagine)) ?? '/logo.png' }}" alt="" height="40">
                                </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <div class="header-item d-flex align-items-center px-2">
                        <img class="rounded-circle" style="width:42px;height:42px;object-fit:cover;" src="{{ URL::asset(session('utente_produzione')->immagine) ?? '/default/assets/images/users/user-dummy-img.jpg' }}" alt="Avatar">
                        <span class="ms-2 d-none d-sm-flex flex-column lh-1">
                            <span class="fw-semibold">{{ session('utente_produzione')->nome }} {{ session('utente_produzione')->cognome }}</span>
                            <span class="fs-12 text-muted">Operatore</span>
                        </span>
                    </div>

                    <a href="{{ url('produzione/logout') }}"
                       class="btn btn-danger ms-2"
                       style="font-size:16px; padding:10px 18px; font-weight:600;"
                       onclick="return confirm('Vuoi davvero uscire?');">
                        <i class="mdi mdi-logout fs-18 align-middle me-1"></i>
                        <span class="d-none d-sm-inline">Esci</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box" style="background:white;">
            <a href="/produzione/dashboard" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/logo_gestya.jpg" alt="Gestya" height="30">
                    </span>
                <span class="logo-lg">
                        <img src="/logo_gestya.jpg" alt="Gestya" height="50" style="max-width:100%;object-fit:contain;">
                    </span>
            </a>
            <a href="/produzione/dashboard" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/logo_gestya.jpg" alt="Gestya" height="30">
                    </span>
                <span class="logo-lg">
                        <img src="/logo_gestya.jpg" alt="Gestya" height="50" style="max-width:100%;object-fit:contain;">
                    </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ url('produzione/dashboard') }}">
                            <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        @yield('content')
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © gestya.it
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            {{ session('azienda_produzione')->ragione_sociale }}
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->

<!-- JAVASCRIPT -->
<script src="/default/assets/libs/jquery/jquery.min.js"></script>
<script src="/default/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/default/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/default/assets/libs/node-waves/waves.min.js"></script>
<script src="/default/assets/libs/feather-icons/feather.min.js"></script>
<script src="/default/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="/default/assets/js/plugins.js"></script>

<!-- DataTables -->
<script src="/default/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/default/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/default/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/default/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<script>
    // Gestione della chiusura della sidebar
    $(document).ready(function() {
        $('.vertical-menu-btn').on('click', function (e) {
            $('body').toggleClass('sidebar-enable');
            if ($(window).width() >= 992) {
                $('body').toggleClass('vertical-collpsed');
            }
        });

        // Chiudi sidebar quando si clicca fuori
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.vertical-menu-btn, .app-menu').length) {
                $('body').removeClass('sidebar-enable');
            }
        });
    });
</script>

@yield('scripts')
</body>
</html>