<?php $utente = session('utente'); ?>

<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title"><span data-key="t-menu">Menu</span></li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/admin/index">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/admin/aziende">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Aziende</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link menu-link" href="/admin/utenti">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Utenti</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link menu-link" href="/admin/logout">
                    <i class="ri-logout-box-r-line"></i> <span data-key="t-widgets">Logout</span>
                </a>
            </li>

        </ul>
    </div>
    <!-- Sidebar -->
</div>
