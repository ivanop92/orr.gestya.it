<?php $utente = session('utente'); ?>
<?php $reparti = DB::select('SELECT * from reparti order by descrizione DESC'); ?>
<?php $documenti = DB::table('do')
    ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
    ->get(); ?>

<?php
// Recupera l'azienda corrente dall'utente in sessione
$aziendaId = session('utente')->id_azienda;

// Verifica se l'azienda è presente nella colonna azienda_id di moduli
$haAccesso = DB::table('moduli')
    ->whereRaw("FIND_IN_SET(?, azienda_id)", [$aziendaId])
    ->exists();
?>
<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title"><span data-key="t-menu">Menu</span></li>



            <?php if($utente->id_tipologia == 0 || $utente->id_tipologia == 3){  ?>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/admin/index">
                        <i class="ri-home-line"></i> <span data-key="t-widgets">Dashboard</span>
                    </a>
                </li>

                <?php if($utente->super_admin !== 1){ ?>
                    <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebar_clienti" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_clienti">
                        <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Anagrafiche</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebar_clienti">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a href="/admin/dipendenti" class="nav-link" data-key="t-analytics">Dipendenti</a>
                            </li>

                            <li class="nav-item">
                                <a href="/admin/clienti" class="nav-link" data-key="t-crm">Clienti</a>
                            </li>

                            <li class="nav-item">
                                <a href="/admin/fornitori" class="nav-link" data-key="t-analytics">Fornitori</a>
                            </li>

                        </ul>
                    </div>
                    </li>





            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_articoli" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_articoli">
                    <i class="ri-home-line"></i> <span data-key="t-dashboards">Articoli</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_articoli">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/admin/articoli?tipo=prodotto_finito">
                                <i class="ri-home-line"></i> <span data-key="t-widgets">Prodotto Finito</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/admin/articoli?tipo=materia_prima">
                                <i class="ri-home-line"></i> <span data-key="t-widgets">Materia Prima</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>





            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_pianificazione" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_pianificazione">
                    <i class="ri-command-fill"></i> <span data-key="t-dashboards">Pianificazione</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_pianificazione">
                    <ul class="nav nav-sm flex-column">

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/fasi_di_lavorazione">
                                <i class="mdi mdi-alpha-f-box-outline"></i> <span data-key="t-widgets">Fasi di Lavorazione</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/prodotti_finiti">
                                <i class="mdi mdi-alpha-d-circle-outline"></i> <span data-key="t-widgets">Distinta Base</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/odl" class="nav-link" data-key="t-level-2.1"><i class="ri-share-line"></i> <span>Produzione</span></a>
                        </li>

                    </ul>
                </div>
            </li>


            <li class="nav-item">
                @if ($haAccesso)
                    <a class="nav-link menu-link" href="/contratti">
                        <i class="ri-file-text-line"></i> <span data-key="t-widgets">Contratti</span>
                    </a>
                @endif
            </li>



            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_documenti" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_documenti">
                    <i class="ri-share-line"></i> <span data-key="t-multi-level">Documenti</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_documenti">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/gestione_documenti">
                               <span data-key="t-widgets">Gestione_documenti</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/riepilogo_documenti" class="nav-link" data-key="t-level-2.1">Riepilogo Documenti</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarCicloAttivo" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCicloAttivo">Ciclo Attivo</a>
                            <div class="collapse menu-dropdown" id="sidebarCicloAttivo">
                                <ul class="nav nav-sm flex-column">
                                        <?php
                                        // Recupera i documenti attivi
                                    $documentiAttivi = DB::table('do')->where('attivo', 1)->where('id_azienda', $utente->id_azienda)->orderBy('ordinamento')->get();

                                    foreach ($documentiAttivi as $doc) { ?>
                                        <li class="nav-item">
                                            <a href="/documenti/ca/<?= $doc->cd_do ?>" class="nav-link" data-key="t-level-2.1"><?= $doc->descrizione ?></a>
                                        </li>
                                    <?php } ?>


                                        <li class="nav-item">
                                            <a href="/admin/fatture/2024" class="nav-link" data-key="t-level-2.1">Fatture</a>
                                        </li>

                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarCicloPassivo" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCicloPassivo">Ciclo Passivo</a>
                            <div class="collapse menu-dropdown" id="sidebarCicloPassivo">
                                <ul class="nav nav-sm flex-column">
                                        <?php
                                        // Recupera i documenti passivi
                                        $documentiPassivi = DB::table('do')->where('attivo', 0)->where('id_azienda', $utente->id_azienda)->orderBy('descrizione')->get();
                                    foreach ($documentiPassivi as $doc) { ?>
                                    <li class="nav-item">
                                        <a href="/documenti/cp/<?= $doc->cd_do ?>" class="nav-link" data-key="t-level-2.1"><?= $doc->descrizione ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
                </li>
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
                <a class="nav-link menu-link" href="{{ route('preferenze')}}">
                    <i class="mdi mdi-alpha-f-box-outline"></i> <span data-key="t-widgets">Preferenze</span>
                </a>
            </li>


                <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebar_magazzino" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebar_magazzino">
                    <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Magazzino</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebar_magazzino">
                    <ul class="nav nav-sm flex-column">


                        <li class="nav-item">
                            <a href="/gestione_magazzini" class="nav-link" data-key="t-analytics">Gestione Magazzini</a>
                        </li>
                            <?php
                            // Recupera i documenti passivi
                            $magazzini = DB::table('mg')->where('id_azienda', $utente->id_azienda)->orderBy('descrizione')->get();
                        foreach ($magazzini as $mg) { ?>
                        <li class="nav-item">
                            <a href="/mg/<?= $mg->id ?>/<?= $mg->codice_magazzino ?>" class="nav-link" data-key="t-level-2.1"><?= $mg->descrizione ?></a>
                        </li>
                        <?php } ?>


                        <li class="nav-item">
                            <a href="/carico" class="nav-link" data-key="t-analytics">Carico</a>
                        </li>

                        <li class="nav-item">
                            <a href="/scarico" class="nav-link" data-key="t-crm">Scarico</a>
                        </li>

                      {{--  <li class="nav-item">
                            <a href="/trasferimento_mg" class="nav-link" data-key="t-analytics">Trasferimento</a>
                        </li>--}}

                        <li class="nav-item">
                            <a href="/inventario" class="nav-link" data-key="t-analytics">Inventario</a>
                        </li>

                    </ul>
                </div>
                    <?php } ?>

                </li>
                        <?php if($utente->super_admin === 1){ ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/aziende">
                            <i class="ri-home-line"></i> <span data-key="t-widgets">Aziende</span>
                        </a>
                    </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="/moduli">
                    <i class="ri-home-line"></i> <span data-key="t-widgets">Moduli</span>
                </a>
            </li>


                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/utenti">
                            <i class="ri-home-line"></i> <span data-key="t-widgets">Utenti</span>
                        </a>
                    </li>
            <?php } ?>
                <?php if($utente->super_admin !== 1 && $utente->admin_azienda == 1){ ?>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/utentiAdmin">
                            <i class="ri-home-line"></i> <span data-key="t-widgets">Utenti</span>
                        </a>
                    </li>
            <?php } ?>


                    <li class="nav-item">
                            <a class="nav-link menu-link" href="/admin/logout">
                                <i class="ri-logout-box-r-line"></i> <span data-key="t-widgets">Logout</span>
                            </a>
                        </li>
            <?php } ?>

            <?php if($utente->id_tipologia == 1){ ?>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/agente/index">
                    <i class="ri-honour-line"></i> <span data-key="t-widgets">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="/agente/logout">
                    <i class="ri-honour-line"></i> <span data-key="t-widgets">Logout</span>
                </a>
            </li>


            <?php } ?>

            <?php if($utente->id_tipologia == 2){ ?>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/cliente/index">
                        <i class="ri-honour-line"></i> <span data-key="t-widgets">Dashboard</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link menu-link" href="/cliente/formazione_40">
                        <i class="ri-honour-line"></i> <span data-key="t-widgets">Formazione 4.0</span>
                    </a>
                </li>

                <?php if($utente->torna_admin > 0){ ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/cliente/torna_admin">
                            <i class="ri-honour-line"></i> <span data-key="t-widgets">Torna Admin</span>
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/cliente/logout">
                        <i class="ri-honour-line"></i> <span data-key="t-widgets">Logout</span>
                    </a>
                </li>

            <?php } ?>


        </ul>
    </div>
    <!-- Sidebar -->
</div>
