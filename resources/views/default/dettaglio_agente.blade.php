@include('default.common.header')


<div class="page-content">
    <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="/default/assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        <img src="<?php echo URL::asset($user->immagine) ?>" alt="user-img" class="img-thumbnail rounded-circle" />
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1"><?php echo $user->nome.' '.$user->cognome ?> (Saldo: &euro;<?php echo number_format($user->budget,2,'.','') ?>)</h3>
                        <p class="text-white-75"><?php echo $user->ragione_sociale ?></p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i><?php echo $user->indirizzo ?>, <?php echo $user->comune ?> (<?php echo $user->provincia ?>)</div>
                            <div>
                                <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>Agente
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end row-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex profile-wrapper">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                            <li class="nav-item" >
                                <a class="nav-link fs-14" data-bs-toggle="tab" id="accordion_tab_riepilogo" href="#tab_riepilogo" role="tab" onclick="salva_stato('tab_riepilogo')">
                                    <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Riepilogo</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab"  id="accordion_tab_clienti" href="#tab_clienti" role="tab" onclick="salva_stato('tab_clienti')">
                                    <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Clienti</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab"  id="accordion_tab_leads" href="#tab_leads" role="tab" onclick="salva_stato('tab_leads')">
                                    <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Leads</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab"  id="accordion_tab_preventivi" href="#tab_preventivi" role="tab" onclick="salva_stato('tab_preventivi')">
                                    <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Preventivi</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab"  id="accordion_tab_leads_assegnate" href="#tab_leads_assegnate" role="tab" onclick="salva_stato('tab_leads_assegnate')">
                                    <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Leads Assegnate</span>
                                </a>
                            </li>

                        </ul>

                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content pt-4 text-muted">
                        <div class="tab-pane fade" id="tab_riepilogo" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Scadenziario</h5>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table class="table nowrap align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Cliente</th>
                                                        <th>Descrizione</th>
                                                        <th>Totale</th>
                                                        <th>Incassato</th>
                                                        <th>Provvigione</th>
                                                        <th>Pagata</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $totale_valore = 0;$totale_provvigione = 0;$totale_incassato = 0;$totale_pagato = 0; ?>
                                                <?php foreach($preventivi as $p){ $totale_valore += $p->totale; $totale_provvigione += $p->provvigione; $totale_incassato += $p->incassato;  $totale_pagato += $p->pagato;  ?>
                                                <tr>

                                                    <td><?php echo date('d/m/Y',strtotime($p->data)) ?></td>
                                                    <td><?php echo $p->ragione_sociale ?></td>
                                                    <td><?php echo $p->descrizione ?></td>
                                                    <td>&euro;<?php echo number_format($p->totale,2,'.','') ?></td>
                                                    <td>&euro;<?php echo number_format($p->incassato,2,'.','') ?></td>
                                                    <td>&euro;<?php echo number_format($p->provvigione,2,'.','') ?></td>
                                                    <td>&euro;<?php echo number_format($p->pagato,2,'.','') ?></td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>

                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>&euro;<?php echo number_format($totale_valore,2,'.','') ?></td>
                                                        <td>&euro;<?php echo number_format($totale_incassato,2,'.','') ?></td>
                                                        <td>&euro;<?php echo number_format($totale_provvigione,2,'.','') ?></td>
                                                        <td>&euro;<?php echo number_format($totale_pagato,2,'.','') ?></td>

                                                    </tr>
                                                </tfoot>
                                            </table>


                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->

                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>

                        <div class="tab-pane fade" id="tab_clienti" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Clienti</h5>

                                            <a onclick="aggiungi_cliente()" style="float:right;" class="btn btn-success">Aggiungi Cliente</a>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table id="scroll-horizontal" class=" table nowrap align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Immagine</th>
                                                    <th>Ragione Sociale</th>
                                                    <th>Email</th>
                                                    <th>Preventivato</th>
                                                    <th>Incassato</th>
                                                    <th>Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($clienti as $c){ ?>
                                                <tr>
                                                    <td><img style="max-width:50px;height: auto;" src="<?php echo URL::asset($c->immagine) ?>"></td>
                                                    <td><?php echo $c->ragione_sociale ?></td>
                                                    <td><?php echo $c->email ?></td>
                                                    <td><span class="badge bg-primary">&euro; <?php echo number_format($c->preventivato,2,'.','') ?></span></td>
                                                    <td><span class="badge bg-primary">&euro; <?php echo number_format($c->incassato,2,'.','') ?></span></td>
                                                    <td>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <a style="float:left" onclick="modifica_cliente(<?php echo $c->id ?>)" class="btn btn-sm btn-primary">Modifica</a>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <form method="post" onsubmit="return confirm('Vuoi effettuare login Per conto del cliente <?php echo $c->ragione_sociale ?>?')">
                                                                    <input type="hidden" name="id" value="<?php echo $c->id ?>">
                                                                    <input style="float:left;margin-left:10px;" type="submit" name="effettua_login" value="Login" class="btn btn-sm btn-success">
                                                                </form>
                                                            </div>

                                                        </div>

                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>

                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->

                                </div>
                                <!--end col-->
                            </div>
                            <!--end card-->
                        </div>

                        <div class="tab-pane fade" id="tab_leads" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Leads</h5>

                                            <?php if($utente->id_tipologia == 0){ ?>
                                                <a onclick="aggiungi_lead()" style="float:right;" class="btn btn-success">Aggiungi Lead</a>
                                            <?php } ?>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Cliente</th>
                                                    <th>Descrizione</th>
                                                    <th>Totale</th>
                                                    <th>Recapiti</th>
                                                    <th style="width:150px;">Status</th>
                                                    <th style="width:150px;">Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $totale = 0;  ?>
                                                <?php foreach($leads as $l){ if($l->status == 2) $totale += $l->totale; ?>
                                                <tr>

                                                    <td>
                                                        <?php echo date('d/m/Y',strtotime($l->data)) ?>
                                                    </td>
                                                    <td><a href="<?php echo URL::asset('admin/dettaglio_utente/'.$l->id_utente) ?>" target="_blank"><?php echo $l->ragione_sociale ?></a></td>

                                                    <td>
                                                        <?php echo $l->descrizione ?>
                                                        <?php if($l->note != ''){ ?>
                                                        <br><small><?php echo $l->note ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td>&euro;<?php echo number_format($l->totale,2,'.','') ?></td>

                                                    <td>
                                                        <?php echo $l->referente ?>

                                                        <?php if($l->mail_leads != ''){ ?>
                                                        <br><?php echo $l->mail_leads ?>
                                                        <?php } ?>
                                                        <?php if($l->telefono_referente != ''){ ?>
                                                        <br><a href="tel:<?php echo $l->telefono_referente ?>"><?php echo $l->telefono_referente ?></a>
                                                        <?php } ?>
                                                    </td>

                                                    <td>

                                                        <?php if($l->status == 0){ ?>
                                                            <span class="badge bg-primary">Da Inviare</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 1){ ?>
                                                            <span class="badge bg-warning">Inviato</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 2){ ?>
                                                            <span class="badge bg-success">Confermato</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 3){ ?>
                                                            <span class="badge bg-danger">Annullato</span>
                                                        <?php } ?>

                                                        <br>

                                                        Mail Inviata: <?php echo ($l->mail_inviata == 1)?'SI':'NO' ?><br>
                                                        Telefono: <?php echo ($l->contatto_telefonico == 1)?'SI':'NO' ?><br>
                                                    </td>
                                                    <td>
                                                        <?php if($utente->id_tipologia == 0){ ?>
                                                            <a style="float:left" onclick="modifica_lead(<?php echo $l->id ?>)" class="btn btn-sm btn-primary">M</a>
                                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questa Lead ?')">
                                                                <input type="hidden" name="id" value="<?php echo $l->id ?>">
                                                                <input style="float:left;margin-left:5px;" type="submit" name="elimina_lead" value="E" class="btn btn-sm btn-danger">
                                                            </form>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>

                                                <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Totale Leads</td>
                                                    <td><h5>&euro;<?php echo number_format($totale,2,'.','') ?></h5></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                </tfoot>



                                            </table>

                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->

                                </div>
                                <!--end col-->
                            </div>

                        </div>

                        <div class="tab-pane" id="tab_preventivi" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Preventivi</h5>

                                            <?php if($utente->id_tipologia == 0){ ?>
                                                <a onclick="aggiungi_preventivo()" style="float:right;" class="btn btn-success">Aggiungi Preventivi</a>
                                            <?php } ?>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Descrizione</th>
                                                    <th>Allegato</th>
                                                    <th>Totale</th>
                                                    <th>Incassato</th>
                                                    <th>Status</th>
                                                    <th>Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $totale_valore = 0;?>
                                                <?php foreach($preventivi as $p){ if($p->status >= 1) $totale_valore += $p->totale;  ?>
                                                <tr>

                                                    <td>
                                                        <?php echo date('d/m/Y',strtotime($p->data)) ?>
                                                        <?php if($p->note != ''){ ?>
                                                        <br><small><?php echo $p->note ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $p->descrizione ?></td>
                                                    <td>
                                                        <?php if($p->allegato != ''){ ?>
                                                        <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($p->allegato) ?>">Allegato</a>
                                                        <?php } ?>
                                                    </td>
                                                    <td>&euro;<?php echo number_format($p->totale,2,'.','') ?></td>
                                                    <td>&euro;<?php echo number_format($p->incassato,2,'.','') ?></td>
                                                    <td>
                                                        <?php if($p->status == 0){ ?>
                                                        <span class="badge bg-warning">Inviato</span>
                                                        <?php } ?>
                                                        <?php if($p->status == 1){ ?>
                                                        <span class="badge bg-info">Confermato</span>
                                                        <?php } ?>
                                                        <?php if($p->status == 2){ ?>
                                                        <span class="badge bg-primary">In Lavorazione</span>
                                                        <?php } ?>
                                                        <?php if($p->status == 3){ ?>
                                                        <span class="badge bg-success">Completato</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if($utente->id_tipologia == 0){ ?>
                                                            <a style="float:left" onclick="modifica_preventivo(<?php echo $p->id ?>)" class="btn btn-sm btn-primary">M</a>
                                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo preventivo ?')">
                                                                <input type="hidden" name="id" value="<?php echo $p->id ?>">
                                                                <input style="float:left;margin-left:10px;" type="submit" name="elimina_preventivo" value="E" class="btn btn-sm btn-danger">
                                                            </form>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>

                                                <tfoot>
                                                <tr>
                                                    <td colspan="3" style="text-align: right;">Totale Preventivi Accettati</td>
                                                    <td>&euro;<?php echo number_format($totale_valore,2,'.','') ?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                </tfoot>
                                            </table>

                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->

                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>

                        <div class="tab-pane fade" id="tab_leads_assegnate" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Leads Assegnate</h5>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Cliente</th>
                                                    <th>Descrizione</th>
                                                    <th>Totale</th>
                                                    <th>Recapiti</th>
                                                    <th style="width:150px;">Status</th>
                                                    <th style="width:150px;">Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $totale = 0;  ?>
                                                <?php foreach($leads_assegnate as $l){ $totale += $l->totale; ?>
                                                <tr>

                                                    <td>
                                                        <?php echo date('d/m/Y',strtotime($l->data)) ?>
                                                    </td>
                                                    <td><a href="<?php echo URL::asset('admin/dettaglio_utente/'.$l->id_utente) ?>" target="_blank"><?php echo $l->ragione_sociale ?></a></td>

                                                    <td>
                                                        <?php echo $l->descrizione ?>
                                                        <?php if($l->note != ''){ ?>
                                                        <br><small><?php echo $l->note ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td>&euro;<?php echo number_format($l->totale,2,'.','') ?></td>

                                                    <td>
                                                        <?php echo $l->referente ?>

                                                        <?php if($l->mail_leads != ''){ ?>
                                                        <br><?php echo $l->mail_leads ?>
                                                        <?php } ?>
                                                        <?php if($l->telefono_referente != ''){ ?>
                                                        <br><a href="tel:<?php echo $l->telefono_referente ?>"><?php echo $l->telefono_referente ?></a>
                                                        <?php } ?>
                                                    </td>

                                                    <td>

                                                        <?php if($l->status == 0){ ?>
                                                        <span class="badge bg-primary">Da Inviare</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 1){ ?>
                                                        <span class="badge bg-warning">Inviato</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 2){ ?>
                                                        <span class="badge bg-success">Confermato</span>
                                                        <?php } ?>

                                                        <?php if($l->status == 3){ ?>
                                                        <span class="badge bg-danger">Annullato</span>
                                                        <?php } ?>

                                                        <br>

                                                        Mail Inviata: <?php echo ($l->mail_inviata == 1)?'SI':'NO' ?><br>
                                                        Telefono: <?php echo ($l->contatto_telefonico == 1)?'SI':'NO' ?><br>
                                                    </td>
                                                    <td>
                                                        <a style="float:left" onclick="modifica_lead(<?php echo $l->id ?>)" class="btn btn-sm btn-primary">M</a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>

                                                <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Totale Leads</td>
                                                    <td><h5>&euro;<?php echo number_format($totale,2,'.','') ?></h5></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                </tfoot>



                                            </table>

                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->

                                </div>
                                <!--end col-->
                            </div>

                        </div>

                    </div>
                    <!--end tab-content-->
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
</div>

<div class="modal fade" id="modal_aggiungi_cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg p-1">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="/default/assets/images/users/user-dummy-img.jpg" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                <input type="text" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" required />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Agente</label>
                                <select name="id_agente" class="form-control select2">
                                    <option value="<?php echo $user->id ?>"><?php echo $user->nome.' '.$user->cognome ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Email <b style="color:red">*</b></label>
                                <input type="email" name="email" class="form-control" placeholder="Email" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" class="form-control" placeholder="Telefono" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Indirizzo</label>
                                <input type="text" name="indirizzo" class="form-control" placeholder="Indirizzo" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">CAP</label>
                                <input type="text" name="cap" class="form-control" placeholder="Comune" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Comune</label>
                                <input type="text" name="comune" class="form-control" placeholder="Comune" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" class="form-control" placeholder="Provincia" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Regione</label>
                                <input type="text" name="regione" class="form-control" placeholder="Provincia" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Fatture</label>
                                <input type="text" name="mail_recapito" class="form-control" placeholder="Mail Fatture" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Leads</label>
                                <input type="text" name="mail_leads" class="form-control" placeholder="Mail Leads" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice Fiscale</label>
                                <input type="text" name="cf" class="form-control" placeholder="CF" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Partita IVA</label>
                                <input type="text" name="piva" class="form-control" placeholder="P.IVA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice SDI</label>
                                <input type="text" name="sdi" class="form-control" placeholder="P.IVA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">PEC</label>
                                <input type="text" name="pec" class="form-control" placeholder="pec" />
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_cliente" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($clienti as $c){ ?>

    <div class="modal fade" id="modal_modifica_cliente_<?php echo $c->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Modifica Cliente <?php echo $c->ragione_sociale ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-lg-12">
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">

                                        <div class="avatar-lg p-1">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <img src="<?php echo URL::asset($c->immagine) ?>" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Nome <b style="color:red">*</b></label>
                                        <input type="text" name="nome" class="form-control" placeholder="Nome" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Cognome <b style="color:red">*</b></label>
                                        <input type="text" name="cognome" class="form-control" placeholder="Cognome" required />
                                    </div>
                                </div>
                            -->

                            <div class="col-md-12">
                                <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                            </div>


                            <div class="col-md-12">
                                <div>
                                    <label for="company_name-field" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                    <input type="text" name="ragione_sociale" value="<?php echo $c->ragione_sociale ?>" class="form-control" placeholder="Ragione Sociale" required />
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div>
                                    <label for="company_name-field" class="form-label">Agente</label>
                                    <select name="id_agente" class="form-control select2">
                                        <option value="<?php echo $user->id ?>"><?php echo $user->nome.' '.$user->cognome ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Email <b style="color:red">*</b></label>
                                    <input type="email" name="email" value="<?php echo $c->email ?>" class="form-control" placeholder="Email" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Telefono</label>
                                    <input type="text" name="telefono" value="<?php echo $c->telefono ?>" class="form-control" placeholder="Telefono" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div>
                                    <label class="form-label">Indirizzo</label>
                                    <input type="text" name="indirizzo" value="<?php echo $c->indirizzo ?>" class="form-control" placeholder="Indirizzo" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">CAP</label>
                                    <input type="text" name="cap" value="<?php echo $c->cap ?>" class="form-control" placeholder="Comune" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Comune</label>
                                    <input type="text" name="comune" value="<?php echo $c->comune ?>" class="form-control" placeholder="Comune" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Provincia</label>
                                    <input type="text" name="provincia" value="<?php echo $c->provincia ?>" class="form-control" placeholder="Provincia" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Regione</label>
                                    <input type="text" name="regione" value="<?php echo $c->regione ?>" class="form-control" placeholder="Provincia" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Mail Fatture</label>
                                    <input type="text" name="mail_recapito" value="<?php echo $c->mail_recapito ?>" class="form-control" placeholder="Mail Fatture" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Mail Leads</label>
                                    <input type="text" name="mail_leads" value="<?php echo $c->mail_leads ?>" class="form-control" placeholder="Mail Leads" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Nome Referente</label>
                                    <input type="text" name="referente" value="<?php echo $c->referente ?>" class="form-control" placeholder="Mail Referente" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Telefono Referente</label>
                                    <input type="text" name="telefono_referente" value="<?php echo $c->telefono_referente ?>" class="form-control" placeholder="Telefono Referente" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Codice Fiscale</label>
                                    <input type="text" name="cf" value="<?php echo $c->cf ?>" class="form-control" placeholder="CF" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Partita IVA</label>
                                    <input type="text" name="piva" value="<?php echo $c->piva ?>" class="form-control" placeholder="P.IVA" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Codice SDI</label>
                                    <input type="text" name="sdi" value="<?php echo $c->sdi ?>" class="form-control" placeholder="P.IVA" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">PEC</label>
                                    <input type="text" name="pec" value="<?php echo $c->pec ?>" class="form-control" placeholder="pec" />
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="hidden" name="id" value="<?php echo $c->id ?>">
                            <input type="submit" class="btn btn-success" id="add-btn" name="modifica_cliente" value="Modifica" >
                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php } ?>


<div class="modal fade" id="modal_aggiungi_lead" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d') ?>" placeholder="Data" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                <select name="id_utente" class="form-control js-example-basic-single" Required>
                                    <option value="">Scegli un Cliente</option>
                                    <?php foreach($clienti as $c){ ?>
                                    <option value="<?php echo $c->id ?>"><?php echo $c->ragione_sociale ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" class="form-control" placeholder="Descrizione" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Totale <b style="color:red">*</b></label>
                                <input type="number" step="1" name="totale" value="0" class="form-control" placeholder="Totale" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Mail Inviata <b style="color:red">*</b></label>
                                <select name="mail_inviata" class="form-control select2">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Contatto Telefonico<b style="color:red">*</b></label>
                                <select name="contatto_telefonico" class="form-control select2">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Status <b style="color:red">*</b></label>
                                <select name="status" class="form-control select2">
                                    <option value="0">Da Inviare</option>
                                    <option value="1">Inviato</option>
                                    <option value="2">Confermato</option>
                                    <option value="3">Annullato</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Note <b style="color:red">*</b></label>
                                <textarea name="note" class="form-control" style="height:200px" placeholder="note"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_utente" value="<?php echo $user->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_lead" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($leads as $l){ ?>
    <div class="modal fade" id="modal_modifica_lead_<?php echo $l->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Modifica Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div>
                                    <label class="form-label">Data <b style="color:red">*</b></label>
                                    <input type="date" name="data" class="form-control" value="<?php echo $l->data ?>" placeholder="Data" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                    <select name="id_utente" class="form-control js-example-basic-single" Required>
                                        <option value="">Scegli un Cliente</option>
                                        <?php foreach($clienti as $c){ ?>
                                            <option value="<?php echo $c->id ?>" <?php echo ($c->id == $l->id_utente)?'selected':'' ?>><?php echo $c->ragione_sociale ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <label class="form-label">Descrizione <b style="color:red">*</b></label>
                                    <input type="text" name="descrizione" class="form-control" value="<?php echo $l->descrizione ?>" placeholder="Descrizione" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <label class="form-label">Totale <b style="color:red">*</b></label>
                                    <input type="number" step="1" name="totale" value="<?php echo $l->totale ?>" class="form-control" placeholder="Totale" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Mail Inviata <b style="color:red">*</b></label>
                                    <select name="mail_inviata" class="form-control select2">
                                        <option value="0" <?php echo ($l->mail_inviata == 0)?'selected':'' ?>>NO</option>
                                        <option value="1" <?php echo ($l->mail_inviata == 1)?'selected':'' ?>>SI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Contatto Telefonico<b style="color:red">*</b></label>
                                    <select name="contatto_telefonico" class="form-control select2">
                                        <option value="0" <?php echo ($l->contatto_telefonico == 0)?'selected':'' ?>>NO</option>
                                        <option value="1" <?php echo ($l->contatto_telefonico == 1)?'selected':'' ?>>SI</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div>
                                    <label  class="form-label">Status <b style="color:red">*</b></label>
                                    <select name="status" class="form-control select2">
                                        <option value="0" <?php echo ($l->status == 0)?'selected':'' ?>>Da Inviare</option>
                                        <option value="1" <?php echo ($l->status == 1)?'selected':'' ?>>Inviato</option>
                                        <option value="2" <?php echo ($l->status == 2)?'selected':'' ?>>Confermato</option>
                                        <option value="3" <?php echo ($l->status == 3)?'selected':'' ?>>Annullato</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <label  class="form-label">Note <b style="color:red">*</b></label>
                                    <textarea name="note" class="form-control" style="height:200px" placeholder="note"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="hidden" name="id" value="<?php echo $l->id ?>">
                            <input type="submit" class="btn btn-success" id="add-btn" name="modifica_lead" value="Modifica" >
                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<?php foreach($leads_assegnate as $l){ ?>
    <div class="modal fade" id="modal_modifica_lead_<?php echo $l->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Modifica Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Mail Inviata <b style="color:red">*</b></label>
                                    <select name="mail_inviata" class="form-control select2">
                                        <option value="0" <?php echo ($l->mail_inviata == 0)?'selected':'' ?>>NO</option>
                                        <option value="1" <?php echo ($l->mail_inviata == 1)?'selected':'' ?>>SI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label  class="form-label">Contatto Telefonico<b style="color:red">*</b></label>
                                    <select name="contatto_telefonico" class="form-control select2">
                                        <option value="0" <?php echo ($l->contatto_telefonico == 0)?'selected':'' ?>>NO</option>
                                        <option value="1" <?php echo ($l->contatto_telefonico == 1)?'selected':'' ?>>SI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <label  class="form-label">Note <b style="color:red">*</b></label>
                                    <textarea name="note" class="form-control" style="height:200px" placeholder="note"><?php echo $l->note ?></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="hidden" name="id" value="<?php echo $l->id ?>">
                            <input type="submit" class="btn btn-success" id="add-btn" name="modifica_lead" value="Modifica" >
                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


@include('default.common.footer')


<script type="text/javascript">

    function salva_stato(tab_id){
        localStorage.setItem('accordion',tab_id);
    }

    function get_stato(){

        var tab_id = localStorage.getItem('accordion');
        if (tab_id) {
            $('#'+tab_id).addClass( "active show" );
            $('#accordion_'+tab_id).addClass( "active" );
        }
    }

    function aggiungi_lead(){
        $('#modal_aggiungi_lead').modal('show');
    }

    function aggiungi_cliente(){
        $('#modal_aggiungi_cliente').modal('show');
    }

    function modifica_cliente(id){
        $('#modal_modifica_cliente_'+id).modal('show');
    }

    function modifica_lead(id){
        $('#modal_modifica_lead_'+id).modal('show');
    }

    get_stato();

</script>
