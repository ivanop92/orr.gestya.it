<?php
    $utente = session('utente');
 ?>

<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title"><span data-key="t-menu">Menu</span></li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/produzione/dashboard">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Dashboard</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/logout">
                    <i class="ri-logout-box-r-line"></i> <span data-key="t-widgets">Logout</span>
                </a>
            </li>

        </ul>
    </div>
    <!-- Sidebar -->
</div>

<!-- Aggiungi questo script dopo il codice della sidebar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recupera lo stato salvato
        const savedState = JSON.parse(localStorage.getItem('sidebarState') || '{}');

        // Funzione per salvare lo stato corrente
        function saveSidebarState() {
            const state = {
                activeLink: document.querySelector('.nav-link.active')?.getAttribute('href') || '',
                openMenus: Array.from(document.querySelectorAll('.menu-dropdown.show')).map(el => el.id)
            };
            localStorage.setItem('sidebarState', JSON.stringify(state));
        }

        // Gestisci i click sui link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                // Rimuovi active da tutti i link
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                // Aggiungi active al link cliccato
                this.classList.add('active');
                saveSidebarState();
            });

            // Se questo è il link salvato, marcalo come attivo
            if (link.getAttribute('href') === savedState.activeLink) {
                link.classList.add('active');
            }
        });

        // Gestisci i collapse menu
        document.querySelectorAll('.menu-dropdown').forEach(menu => {
            if (savedState.openMenus?.includes(menu.id)) {
                menu.classList.add('show');
                // Trova e attiva il pulsante correlato
                const trigger = document.querySelector(`[data-bs-toggle="collapse"][data-bs-target="#${menu.id}"]`);
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'true');
                    trigger.classList.remove('collapsed');
                }
            }

            // Aggiungi listener per il collapse
            menu.addEventListener('shown.bs.collapse', saveSidebarState);
            menu.addEventListener('hidden.bs.collapse', saveSidebarState);
        });

        // Evidenzia il menu attivo basato sull'URL corrente
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');

                // Trova e apri il menu padre se esiste
                let parentCollapse = link.closest('.menu-dropdown');
                while (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const trigger = document.querySelector(`[data-bs-toggle="collapse"][data-bs-target="#${parentCollapse.id}"]`);
                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'true');
                        trigger.classList.remove('collapsed');
                    }
                    parentCollapse = parentCollapse.parentElement.closest('.menu-dropdown');
                }

                saveSidebarState();
            }
        });
    });
</script>