@include('default.common.header')

<div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="flex-grow-1">


                                    <form method="post" style="margin-bottom:10px;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="titolo" placeholder="inserisci un task Veloce" required>
                                                <input type="hidden" name="id_reparto" value="<?php echo $reparto_attuale->id ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input  type="submit" name="crea_task_veloce" value="Crea Task Veloce" class="btn btn-primary" style="width:100%">
                                            </div>
                                        </div>
                                    </form>
                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Progetto
                                    </button>
                                    <button class="btn btn-warning" onclick="stampa_report();">
                                        <i class="align-bottom"></i>Stampa Report
                                    </button>
                                </div>
                                <!--
                                <div class="flex-shrink-0">
                                    <div class="hstack text-nowrap gap-2">
                                        <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addmembers"><i class="ri-filter-2-line me-1 align-bottom"></i> Filtri</button>
                                        <button class="btn btn-soft-success">Import</button>
                                        <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-info"><i class="ri-more-2-fill"></i></button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                            <li><a class="dropdown-item" href="#">All</a></li>
                                            <li><a class="dropdown-item" href="#">Last Week</a></li>
                                            <li><a class="dropdown-item" href="#">Last Month</a></li>
                                            <li><a class="dropdown-item" href="#">Last Year</a></li>
                                        </ul>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Progetti Reparto <?php echo $reparto_attuale->descrizione ?></h5>
                            </div>
                            <div class="card-body">
                                <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th class="no-sort">Cliente</th>
                                        <th class="no-sort" style="width:250px;">Campi Extra</th>
                                        <th class="no-sort" >Ultimo Status</th>
                                        <th class="no-sort" >Status</th>
                                        <th class="no-sort"  style="width:100px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                            $stati = explode(';',$reparto_attuale->stati);
                                            $colori = explode(';',$reparto_attuale->colori);
                                        ?>

                                        <?php foreach($progetti as $p){ ?>

                                            <?php

                                                $status = $stati[$p->status];
                                                $badge = $colori[$p->status];

                                                if($p->scadenza > 7) $background = 'rgba(39, 174, 96,0.3)';
                                                if($p->scadenza <= 7) $background = 'rgba(241, 196, 15,0.3)';
                                                if($p->scadenza <= 0) $background = 'rgba(192, 57, 43,0.3)';

                                            ?>


                                            <tr style="background: <?php echo $background ?>">
                                                <td><?php echo $p->titolo ?><br><?php echo $p->cliente ?><br><?php echo $p->reparto ?><br>
                                                    <a onclick="allegati(<?php echo $p->id ?>)" class="btn btn-sm btn-primary">Allegati (<?php echo sizeof($p->allegati) ?>)</a>
                                                </td>
                                                <td>
                                                    <?php echo ($p->label_ex1 != '')?$p->label_ex1.':'.$p->val_ex1.'<br>':'' ?>
                                                    <?php echo ($p->label_ex2 != '')?$p->label_ex2.':'.$p->val_ex2.'<br>':'' ?>
                                                    <?php echo ($p->label_ex3 != '')?$p->label_ex3.':'.$p->val_ex3.'<br>':'' ?>
                                                    <?php echo ($p->label_ex4 != '')?$p->label_ex4.':'.$p->val_ex4.'<br>':'' ?>
                                                    <?php echo ($p->label_ex5 != '')?$p->label_ex5.':'.$p->val_ex5.'<br>':'' ?>
                                                    <?php echo ($p->label_ex6 != '')?$p->label_ex6.':'.$p->val_ex6.'<br>':'' ?>
                                                    <?php echo ($p->label_ex7 != '')?$p->label_ex7.':'.$p->val_ex7.'<br>':'' ?>
                                                    <?php echo ($p->label_ex8 != '')?$p->label_ex8.':'.$p->val_ex8.'<br>':'' ?>
                                                    <?php echo ($p->label_ex9 != '')?$p->label_ex9.':'.$p->val_ex9.'<br>':'' ?>
                                                    <?php echo ($p->label_ex10 != '')?$p->label_ex10.':'.$p->val_ex10.'<br>':'' ?>
                                                </td>
                                                <td><?php echo nl2br($p->descrizione_ultimo_sal) ?></td>
                                                <td><b><?php echo $p->assegnatario ?></b><br><span class="badge <?php echo $badge ?>" style="font-size:14px;"><?php echo $status ?></span><br><?php if($p->timestamp_prossimo_sal != ''){ ?>Prossimo Check:<br><?php echo date('d/m/Y H:i:s',strtotime($p->timestamp_prossimo_sal)); } ?></be></td>
                                                <td>

                                                    <a style="width:100%;" onclick="avanzamento(<?php echo $p->id ?>)" class="btn btn-sm btn-primary">Avanzamento</a>

                                                    <a style="width:100%;margin-top:5px;" onclick="modifica(<?php echo $p->id ?>)" class="btn btn-sm btn-success">Modifica</a>

                                                    <form method="post" onsubmit="return confirm('Vuoi Archiviare questo progetto ?')">
                                                        <input type="hidden" name="id" value="<?php echo $p->id ?>">
                                                        <input style="width:100%;margin-top:5px;" type="submit" name="archivia" value="Archivia" class="btn btn-sm btn-warning">
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->

            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div>


<div class="modal fade" id="modal_aggiungi" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Progetto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Titolo <b style="color:red">*</b></label>
                            <input type="text" name="titolo" class="form-control" placeholder="Titolo" required />
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Descrizione del Progetto <b style="color:red">*</b></label>
                            <textarea name="descrizione" class="form-control" required style="height:150px;"/></textarea>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Descrizione Ultima Attività <b style="color:red">*</b></label>
                            <input type="text" name="descrizione_ultimo_sal" class="form-control" placeholder="Descrizione Ultimo SAL" required />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Data Prossimo Check<b style="color:red">*</b></label>
                            <input type="datetime-local" name="timestamp_prossimo_sal" value="<?php echo date('Y-m-d\TH:i', strtotime("+1 day")) ?>" class="form-control" placeholder="Timestamp Prossimo SAL" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cliente <b style="color:red">*</b></label>

                            <select id="id_utente" name="id_utente" data-choices data-choices-search-true required>
                                <option value="">Scegli un Cliente</option>
                                <?php foreach($clienti as $c){ ?>
                                <option value="<?php echo $c->id ?>"><?php echo $c->ragione_sociale ?></option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reparto <b style="color:red">*</b></label>

                            <select name="id_reparto" class="form-control" data-choices data-choices-search-false required>
                                <option value="">Scegli un Reparto</option>
                                <?php foreach($reparti as $r){ ?>
                                    <option value="<?php echo $r->id ?>" <?php echo ($r->id == $reparto_attuale->id)?'selected':'' ?>><?php echo $r->descrizione ?></option>
                                <?php } ?>

                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Operatore <b style="color:red">*</b></label>

                            <select name="id_assegnatario" data-choices data-choices-search-false required>
                                <option value="">Assegna Task</option>
                                <?php foreach($operatori as $o){ ?>
                                    <option value="<?php echo $o->id ?>" <?php echo ($o->id == $utente->id)?'selected':'' ?>><?php echo $o->nome.' '.$o->cognome ?></option>
                                <?php } ?>

                            </select>
                        </div>



                        <div class="col-md-6">
                            <label class="form-label">Status <b style="color:red">*</b></label>

                            <select name="status" data-choices data-choices-search-false required>
                                <?php foreach($stati as $chiave => $valore){ ?>
                                    <option value="<?php echo $chiave ?>"><?php echo $valore ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_report" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Stampa Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Data Report<b style="color:red">*</b></label>
                            <input type="date" name="data" value="<?php echo date('Y-m-d') ?>" class="form-control" placeholder="Timestamp Prossimo SAL" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reparto <b style="color:red">*</b></label>

                            <select name="id_reparto" data-choices data-choices-search-false required>
                                <option value="">Scegli un Reparto</option>
                                <?php foreach($reparti as $r){ ?>
                                <option value="<?php echo $r->id ?>"><?php echo $r->descrizione ?></option>
                                <?php } ?>

                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" name="stampa_report" value="Stampa Report" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php foreach($progetti as $p){ ?>


<div class="modal fade" id="modal_modifica_<?php echo $p->id ?>" aria-labelledby="exampleModalLabel" aria-hidden="true" style="--vz-modal-width: 80%;">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Progetto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">


                            <div class="row g-3">

                            <div class="col-md-12">
                                <label class="form-label">Titolo <b style="color:red">*</b></label>
                                <input type="text" name="titolo" class="form-control" value="<?php echo $p->titolo ?>" placeholder="Titolo" required />
                            </div>


                            <div class="col-md-12">
                                <label class="form-label">Descrizione del Progetto <b style="color:red">*</b></label>
                                <textarea name="descrizione" class="form-control" required  style="height:150px;"/><?php echo $p->descrizione ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cliente <b style="color:red">*</b></label>

                                <select id="id_utente" name="id_utente" data-choices data-choices-search-true required>
                                    <option value="">Scegli un Cliente</option>
                                        <?php foreach($clienti as $c){ ?>
                                    <option value="<?php echo $c->id ?>" <?php echo ($c->id == $p->id_utente)?'selected':'' ?>><?php echo $c->ragione_sociale ?></option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Reparto <b style="color:red">*</b></label>

                                <select name="id_reparto" class="form-control" data-choices data-choices-search-false required>
                                    <option value="">Scegli un Reparto</option>
                                        <?php foreach($reparti as $r){ ?>
                                    <option value="<?php echo $r->id ?>" <?php echo ($r->id == $reparto_attuale->id)?'selected':'' ?>><?php echo $r->descrizione ?></option>
                                    <?php } ?>

                                </select>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Operatore <b style="color:red">*</b></label>

                                <select name="id_assegnatario" data-choices data-choices-search-false required>
                                    <option value="">Assegna Task</option>
                                        <?php foreach($operatori as $o){ ?>
                                    <option value="<?php echo $o->id ?>" <?php echo ($o->id == $p->id_assegnatario)?'selected':'' ?>><?php echo $o->nome.' '.$o->cognome ?></option>
                                    <?php } ?>

                                </select>
                            </div>



                            <div class="col-md-6">
                                <label class="form-label">Status <b style="color:red">*</b></label>

                                <select name="status" data-choices data-choices-search-false required>
                                        <?php foreach($stati as $chiave => $valore){ ?>
                                    <option value="<?php echo $chiave ?>" <?php echo ($chiave == $p->status)?'selected':'' ?>><?php echo $valore ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                        </div>

                        <div class="col-md-6">

                            <h3>Campi Extra</h3>

                            <?php for($i = 1;$i<=10;$i++){ ?>

                            <?php
                                $label = 'label_ex'.strval($i);
                                $val = 'val_ex'.strval($i);
                            ?>


                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo $p->$label ?></label>
                                    <input type="text" name="<?php echo $label ?>" class="form-control" value="<?php echo $p->$label ?>" placeholder="Campo <?php echo $label ?>" />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo $p->$label ?></label>
                                    <input type="text" name="<?php echo $val ?>" class="form-control" value="<?php echo $p->$val ?>" placeholder="Valore <?php echo $p->$label ?>" />
                                </div>

                            </div>

                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $p->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade modal-lg" id="modal_avanzamento_<?php echo $p->id ?>"  aria-labelledby="exampleModalLabel" aria-hidden="true" style="--vz-modal-width: 80%;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Avanzamento Progetto - <?php echo $p->titolo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">

                            <div class="row">

                                <div class="col-md-12">
                                    <label class="form-label">Descrizione Ultima Attività <b style="color:red">*</b></label>
                                    <input type="text" name="descrizione_ultimo_sal" value="<?php echo $p->descrizione_ultimo_sal ?>" class="form-control" placeholder="Descrizione Ultimo SAL" required />
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Data Prossimo Check<b style="color:red">*</b></label>
                                    <input type="datetime-local" name="timestamp_prossimo_sal" value="<?php echo date('Y-m-d\TH:i', strtotime("+1 day")) ?>" class="form-control" placeholder="Timestamp Prossimo SAL" required />
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label">Assegna Task <b style="color:red">*</b></label>

                                    <select name="id_assegnatario" data-choices data-choices-search-false required>
                                        <option value="">Seleziona</option>
                                            <?php foreach($operatori as $o){ ?>
                                        <option value="<?php echo $o->id ?>" <?php echo ($o->id == $p->id_assegnatario)?'selected':'' ?>><?php echo $o->nome.' '.$o->cognome ?></option>
                                        <?php } ?>

                                    </select>
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label">Reparto <b style="color:red">*</b></label>

                                    <select name="id_reparto" class="form-control" data-choices data-choices-search-false required>
                                        <option value="">Scegli un Reparto</option>
                                            <?php foreach($reparti as $r){ ?>
                                        <option value="<?php echo $r->id ?>" <?php echo ($r->id == $p->id_reparto)?'selected':'' ?>><?php echo $r->descrizione ?></option>
                                        <?php } ?>

                                    </select>
                                </div>



                                <div class="col-md-12">
                                    <label class="form-label">Status <b style="color:red">*</b></label>

                                    <select name="status" data-choices data-choices-search-true required>
                                            <?php foreach($stati as $chiave => $valore){ ?>
                                        <option value="<?php echo $chiave ?>" <?php echo ($p->status == $chiave)?'selected':'' ?>><?php echo $valore ?></option>
                                        <?php } ?>
                                    </select>
                                </div>


                                <div class="col-md-12">
                                    <label class="form-label">Allegato</label>
                                    <input type="file" name="allegato" class="form-control"/>
                                </div>


                            </div>

                        </div>

                        <div class="col-md-8">
                            <b>Storico Attività</b>
                            <table id="scroll-horizontal" class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="no-sort">Timestamp</th>
                                    <th class="no-sort">Prossimo Check</th>
                                    <th class="no-sort" style="width:300px;">Descrizione</th>
                                    <th class="no-sort" >Operatore</th>
                                    <th class="no-sort" >Allegato</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($p->storico as $s){ ?>
                                <tr>
                                    <td><?php echo $s->timestamp_ultimo_sal ?></td>
                                    <td><?php echo $s->timestamp_prossimo_sal ?></td>
                                    <td><?php echo nl2br($s->descrizione_ultimo_sal) ?></td>
                                    <td><?php echo $s->operatore ?></td>
                                    <td>
                                            <?php if($s->allegato != ''){ ?>
                                        <a href="<?php echo URL::asset($s->allegato) ?>" target="_blank" class="btn btn-primary">Allegato</a>
                                        <?php } ?>
                                    </td>

                                </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="float:left;">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_sal" value="Effettua Avanzamento" style="float:left;">
                        <input type="hidden" name="id" value="<?php echo $p->id ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-lg" id="modal_allegati_<?php echo $p->id ?>"  aria-labelledby="exampleModalLabel" aria-hidden="true" style="--vz-modal-width: 80%;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Allegati Progetto - <?php echo $p->titolo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">

                            <div class="row">

                                <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">

                                    <div class="col-md-12">
                                        <label class="form-label">Nome Allegato<b style="color:red">*</b></label>
                                        <input type="text" name="nome_allegato" class="form-control" placeholder="Nome Allegato" required />
                                    </div>


                                    <div class="col-md-12">
                                        <label class="form-label">Allegato</label>
                                        <input type="file" name="allegato" class="form-control"/>
                                    </div>

                                    <div class="col-md-12" style="margin-top:10px;">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="float:left;">Chiudi</button>
                                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_allegato" value="Aggiungi Allegato" style="float:left;margin-left:10px">
                                        <input type="hidden" name="id_progetto" value="<?php echo $p->id ?>">
                                        <input type="hidden" name="id_utente" value="<?php echo $p->id_utente ?>">

                                    </div>

                                </form>


                            </div>

                        </div>

                        <div class="col-md-8">
                            <b>Lista Allegati</b>
                            <table id="scroll-horizontal" class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="no-sort">Data Caricamento</th>
                                    <th class="no-sort">Nome Allegato</th>
                                    <th class="no-sort" style="width:300px;">Allegato</th>
                                    <th class="no-sort" ></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($p->allegati as $a){ ?>
                                <tr>
                                    <td><?php echo $a->timestamp ?></td>
                                    <td><?php echo $a->nome_allegato ?></td>
                                    <td>
                                        <?php if($a->allegato != ''){ ?>
                                            <a href="<?php echo URL::asset($a->allegato) ?>" target="_blank" class="btn btn-primary">Allegato</a>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questo allegato ?')">
                                            <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                            <input style="width:100%;margin-top:5px;" type="submit" name="elimina_allegato" value="Elimina Allegato" class="btn btn-danger">
                                        </form>
                                    </td>

                                </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-start;">
                    <div class="hstack gap-2 justify-content-end">
                    </div>
                </div>
        </div>
    </div>
</div>

<?php } ?>


@include('default.common.footer')

<script type="text/javascript">

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }


    function stampa_report(){
        $('#modal_report').modal('show');
    }


    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function avanzamento(id){
        $('#modal_avanzamento_'+id).modal('show');
    }

    function allegati(id){
        $('#modal_allegati_'+id).modal('show');
    }


    function visualizza_sal(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function elimina(id){
        $('#id_elimina_utente').val(id);
        $('#modal_elimina').modal('show');
    }

</script>