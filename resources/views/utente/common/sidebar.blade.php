<?php
$utente = session('utente');
$azienda = DB::select('SELECT * from aziende where id ='.$utente->id_azienda);
if(sizeof($azienda) > 0){
    $azienda = $azienda[0];
}
$num_da_registrare = DB::select('SELECT ifnull(COUNT(id),0) AS num FROM dotes WHERE id_azienda = '.$utente->id_azienda.' AND da_registrare = 1')[0]->num;
?>

<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title"><span data-key="t-menu">Menu</span></li>
            <?php if ($utente->id_tipologia != 1) { // Solo per non agenti ?>
            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/index">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Dashboard</span>
                </a>
            </li>
            <?php } ?>
            <!-- Sezione Anagrafiche - visibile a tutti, ma con voci limitate per gli agenti -->
            <li class="nav-item">
                <?php if ($utente->id_tipologia != 1) { // Solo per non agenti ?>
                <a class="nav-link menu-link" data-menu-id="sidebar_clienti" href="#sidebar_clienti" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_clienti">
                    <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Anagrafiche</span>
                </a>
                <?php } ?>
                <div class="collapse menu-dropdown" id="sidebar_clienti">
                    <ul class="nav nav-sm flex-column">

                        <?php if ($utente->id_tipologia != 1) { // Solo per non agenti ?>
                        <li class="nav-item">
                            <a href="/utente/dipendenti" class="nav-link" data-key="t-analytics">Dipendenti</a>
                        </li>
                        <?php } ?>

                                <!-- Clienti - visibile a tutti, inclusi gli agenti -->
                        <li class="nav-item">
                            <a href="/utente/clienti" class="nav-link" data-key="t-crm">Clienti</a>
                        </li>

                        <?php if ($utente->id_tipologia != 1) { // Solo per non agenti ?>
                        <li class="nav-item">
                            <a href="/utente/fornitori" class="nav-link" data-key="t-analytics">Fornitori</a>
                        </li>

                        <li class="nav-item">
                            <a href="/utente/agenti" class="nav-link" data-key="t-analytics">Agenti</a>
                        </li>
                        <?php } ?>

                    </ul>
                </div>
            </li>

            <?php if ($utente->id_tipologia != 1) { // Queste sezioni solo per i non agenti ?>
            <li class="nav-item">
                <a class="nav-link menu-link" data-menu-id="sidebar_articoli" href="#sidebar_articoli" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_articoli">
                    <i class="ri-home-line"></i> <span data-key="t-dashboards">Articoli</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_articoli">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/articoli?tipo=prodotto_finito">
                                <i class="ri-home-line"></i> <span data-key="t-widgets">Prodotto Finito</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/articoli?tipo=materia_prima">
                                <i class="ri-home-line"></i> <span data-key="t-widgets">Materia Prima</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/articoli?tipo=commerciale">
                                <i class="ri-home-line"></i> <span data-key="t-widgets">Commerciale</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/articoli?tipo=semilavorato">
                                <i class="ri-settings-3-line"></i> <span data-key="t-widgets">Semilavorati</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" data-menu-id="sidebar_pianificazione" href="#sidebar_pianificazione" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_pianificazione">
                    <i class="ri-command-fill"></i> <span data-key="t-dashboards">Produzione</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_pianificazione">
                    <ul class="nav nav-sm flex-column">

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/fasi_di_lavorazione">
                                <i class="mdi mdi-alpha-f-box-outline"></i> <span data-key="t-widgets">Fasi di Lavorazione</span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="/utente/programmazione" class="nav-link" data-key="t-level-2.1"><i class="ri-share-line"></i> <span>Programmazione</span></a>
                        </li>

                        <li class="nav-item">
                            <a href="/utente/odl" class="nav-link" data-key="t-level-2.1"><i class="ri-share-line"></i> <span>Ordini di Lavoro</span></a>
                        </li>

                        <li class="nav-item">
                            <a target="_blank" href="/produzione/login/<?php echo $azienda->token_azienda ?>" class="nav-link" data-key="t-level-2.1"><i class="ri-share-line"></i> <span>Accesso Operatore</span></a>
                        </li>

                    </ul>
                </div>
            </li>
            <?php } // Fine sezioni solo per non agenti ?>

                    <!-- Sezione Documenti - modificata per renderla accessibile agli agenti con limitazioni -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_documenti" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_documenti">
                    <i class="ri-share-line"></i> <span data-key="t-multi-level">Documenti</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_documenti">
                    <ul class="nav nav-sm flex-column">
                        <?php if ($utente->id_tipologia != 1) { // Gestione documenti solo per non agenti ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/gestione_documenti">
                                <span data-key="t-widgets">Gestione documenti</span>
                            </a>
                        </li>
                        <?php } ?>

                        <li class="nav-item">
                            <a class="nav-link" data-menu-id="sidebar_ciclo_attivo" href="#sidebarCicloAttivo" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCicloAttivo">Ciclo Attivo</a>
                            <div class="collapse menu-dropdown" id="sidebarCicloAttivo">
                                <ul class="nav nav-sm flex-column">
                                    <?php
                                    // Recupera i documenti attivi
                                    $documentiAttivi = DB::table('do')->where('attivo', 1)->where('id_azienda', $utente->id_azienda)->orderBy('ordinamento')->get();

                                    foreach ($documentiAttivi as $doc) {
                                        // Per gli agenti mostriamo solo preventivi e ordini
                                        if ($utente->id_tipologia == 1 && !in_array($doc->cd_do, ['PRE', 'ORD'])) {
                                            continue;  // Salta documenti non consentiti per agenti
                                        }
                                        ?>
                                    <li class="nav-item">
                                        <a href="/utente/riepilogo_documenti/<?php echo $doc->cd_do ?>" class="nav-link" data-key="t-level-2.1"><?= $doc->descrizione ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>

                        <?php if ($utente->id_tipologia != 1) { // Ciclo passivo solo per non agenti ?>
                        <li class="nav-item">
                            <a class="nav-link" data-menu-id="sidebar_ciclo_passivo" href="#sidebarCicloPassivo" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCicloPassivo">Ciclo Passivo</a>
                            <div class="collapse menu-dropdown" id="sidebarCicloPassivo">
                                <ul class="nav nav-sm flex-column">

                                    <li class="nav-item">
                                        <a href="/utente/documenti_da_registrare" class="nav-link" data-key="t-level-2.1">Da Registrare (<?php echo $num_da_registrare ?>)</a>
                                    </li>

                                        <?php
                                        // Recupera i documenti passivi
                                        $documentiPassivi = DB::table('do')->where('attivo', 0)->where('id_azienda', $utente->id_azienda)->orderBy('descrizione')->get();
                                    foreach ($documentiPassivi as $doc) { ?>
                                    <li class="nav-item">
                                        <a href="/utente/riepilogo_documenti/<?= $doc->cd_do ?>" class="nav-link" data-key="t-level-2.1"><?= $doc->descrizione ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </li>

            <?php if ($utente->id_tipologia != 1) { // Sezioni solo per non agenti ?>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_conti" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_conti">
                    <i class="ri-command-fill"></i> <span data-key="t-dashboards">Contabilità</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_conti">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/utente/scadenziario">
                                <i class="mdi mdi-alpha-f-box-outline"></i> <span data-key="t-widgets">Scadenziario</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/canoni">
                                <i class="ri-money-euro-circle-line"></i> <span data-key="t-widgets">Canoni di Manutenzione</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" data-menu-id="sidebar_magazzino" href="#sidebar_magazzino" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_magazzino">
                    <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Magazzino</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_magazzino">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="/utente/gestione_magazzini" class="nav-link" data-key="t-analytics">Gestione Magazzini</a>
                        </li>
                            <?php
                            // Recupera i documenti passivi
                            $magazzini = DB::table('mg')->where('id_azienda', $utente->id_azienda)->orderBy('descrizione')->get();
                        foreach ($magazzini as $mg) { ?>
                        <li class="nav-item">
                            <a href="/utente/magazzino/giacenze/<?= $mg->id ?>" class="nav-link" data-key="t-level-2.1"><?= $mg->descrizione ?></a>
                        </li>
                        <?php } ?>

                        <li class="nav-item">
                            <a href="/utente/mg/carico" class="nav-link" data-key="t-analytics">Carico</a>
                        </li>

                        <li class="nav-item">
                            <a href="/utente/mg/scarico" class="nav-link" data-key="t-crm">Scarico</a>
                        </li>

                        <li class="nav-item">
                            <a href="/utente/mg/inventario" class="nav-link" data-key="t-analytics">Inventario</a>
                        </li>
                        <li class="nav-item">
                            <a href="/utente/ricezione_barcode" class="nav-link" data-key="t-analytics">Barcode</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/commesse">
                    <i class="ri-profile-line"></i> <span data-key="t-widgets">Commesse</span>
                </a>
            </li>
            <?php } // Fine sezioni solo per non agenti ?>
            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/listini">
                    <i class="ri-profile-line"></i> <span data-key="t-widgets">Listino Prezzi</span>
                </a>
            </li>


                    <!-- Provvigioni - visibile a tutti, inclusi gli agenti -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/visualizza_provvigioni">
                    <i class="ri-account-circle-line"></i> <span data-key="t-widgets">Provvigioni</span>
                </a>
            </li>

            <?php if ($utente->id_tipologia != 1) { // Profilo solo per non agenti ?>
            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/profilo">
                    <i class="ri-profile-line"></i> <span data-key="t-widgets">Profilo</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="/utente/impostazioni">
                    <i class="ri-settings-3-line"></i> <span data-key="t-widgets">Impostazioni</span>
                </a>
            </li>
            <?php } ?>

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