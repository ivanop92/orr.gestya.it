<?php $utente = session('utente'); ?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="dark" data-sidebar="gradient" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Gestya.it | <?php echo $utente->nome.' '.$utente->cognome ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="/icona.png">
    <link href="/default/assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="/default/assets/js/layout.js"></script>
    <link href="/default/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .page-content {
            padding: calc(40px + 1.5rem) calc(1.5rem* .5) 60px calc(1.5rem* .5);
        }
    </style>

</head>


<body>


<div id="layout-wrapper">
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header" style="height:40px;">
                <div class="d-flex">
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="/admin/login" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="/logo.jpg" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="/logo.jpg" alt="" height="17">
                        </span>
                        </a>

                        <a href="/admin/login" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="/logo.jpg" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="/logo.jpg" alt="" height="17">
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
<!--
                    <div class="dropdown d-md-none topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-search fs-22"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
-->
                    <div class="dropdown ms-sm-3 header-item topbar-user" style="height: 40px;">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text" style="color:white!important;">Anno <?php echo session('anno') ?></span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Scegli l'Anno</h6>
                            <a class="dropdown-item" href="/utente/cambia_anno/2025"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">2025</span></a>
                            <a class="dropdown-item" href="/utente/cambia_anno/2024"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">2024</span></a>
                            <a class="dropdown-item" href="/utente/cambia_anno/2023"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">2023</span></a>
                            <a class="dropdown-item" href="/utente/cambia_anno/2022"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">2022</span></a>

                            <!-- Aggiungi il form per tornare a Super Admin se l'utente è loggato come azienda -->
                            <?php if (isset($utente->torna_super_admin)) { ?>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ URL::asset('utente/effettua_login') }}">
                                <input type="hidden" name="id_super_admin" value="<?php echo $utente->torna_super_admin; ?>">
                                <div style="position: relative; display: inline-block;">
                                    <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i>
                                    <input type="submit" class="dropdown-item" name="torna_super_admin" value="Torna a Super Admin" style="padding-left: 30px;">
                                </div>
                            </form>


                            <?php } ?>

                            <a class="dropdown-item" href="/admin/logout"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- removeNotificationModal -->
    <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <!-- Dark Logo-->
            <a href="/admin/index" class="logo logo-dark">
                    <span class="logo-sm">
                            <img src="/logo_gestya.jpg" alt="" height="22">
                    </span>
                <span class="logo-lg">
                            <img src="/logo_gestya.jpg" style="margin:0 auto;display:block;width:100%;margin-top:20px;">
                    </span>
            </a>
            <!-- Light Logo-->
            <a href="/admin/index" class="logo logo-light">
                    <span class="logo-sm">
                            <img src="/logo_gestya.jpg" alt="" height="22">
                    </span>
                <span class="logo-lg">
                            <img src="/logo_gestya.jpg" style="margin:0 auto;display:block;width:100%;margin-top:20px;">
                    </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        @include('produzione.common.sidebar')

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    @if(session('notifica_riordino'))
        <div id="notifica-riordino" class="notifica-riordino" style="position: fixed; top: 20px; right: 20px; background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; z-index: 1000;">
            {{ session('notifica_riordino') }}
            <button onclick="document.getElementById('notifica-riordino').style.display='none'" style="background: none; border: none; color: #721c24; font-size: 16px; float: right;">&times;</button>
        </div>
    @endif
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <style>
        .notifica-riordino {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }


    </style>

    <div class="main-content">
        <!-- Toastr JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Script per visualizzare notifiche dalla sessione -->

