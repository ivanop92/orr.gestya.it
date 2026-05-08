@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Articoli</h4>

                    <div class="page-title-right">
                        <!--
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                            <li class="breadcrumb-item active">Contacts</li>
                        </ol>-->
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <button class="btn btn-info add-btn" onclick="aggiungi();">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Articolo
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
@if($tipo === 'prodotto_finito')
            <div class="row">
                <!-- Inizio sezione Prodotti Finiti (tipologia == 0) -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Prodotti Finiti</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Titolo</th>
                                    <th>Descrizione</th>
                                    <th>Magazzino</th>
                                    <th>Giacenza</th>
                                    <th>Prezzo</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($articoli as $a){ ?>
                                    <?php if($a->tipologia == 0){ // Prodotti Finiti ?>
                                <tr>
                                    <td>
                                        <!-- Visualizza il barcode e cliccandoci apre l'immagine in una nuova scheda -->
                                        <a href="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($a->barcode) }}&code=Code128&translate-esc=on" target="_blank">
                                            <img style="width: 30%;" alt="Barcode" src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($a->barcode) }}&code=Code128&translate-esc=on"/>
                                        </a>
                                        <!-- Aggiungi il pulsante per stampare solo il barcode -->
                                        <button onclick="printBarcode('{{ urlencode($a->barcode) }}')" class="btn btn-success print-hidden">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </td>
                                    <td><?php echo $a->titolo ?><br><small>Prodotto Finito</small></td>
                                    <td><small>Descrizione: <?php echo nl2br($a->descrizione) ?></small>
                                            <?php foreach($a->distinta_base as $db){ ?>
                                        <small><?php echo '<br>'.$db->materiale.' ('.$db->qta.' '.$db->um.')' ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>{{ $a->magazzino_descrizione }}</td>
                                    <td><?php echo $a->giacenza ?> <?php echo $a->um ?></td>
                                    <td>&euro;<?php echo $a->prezzo ?>/<?php echo $a->um ?></td>
                                    <td>
                                        <div style="display: flex">
                                            <a style="margin-left:5px;" onclick="distinta_base(<?php echo $a->id ?>,<?php echo $a->prezzo ?>)" class="btn btn-sm btn-primary">DB</a>
                                            <a style="margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo articolo ?')">
                                                <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                                <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
                                            <a style="margin-left:5px;" onclick="carica(<?php echo $a->id ?>)" class="btn btn-sm btn-success"><i class="ri-add-line"></i></a>
                                            <a style="margin-left:5px;" onclick="scarica(<?php echo $a->id ?>)" class="btn btn-sm btn-warning"><i class="ri-subtract-line"></i></a>
                                            <a style="margin-left:5px;" onclick="rettifica(<?php echo $a->id ?>)" class="btn btn-sm btn-info">R</a>
                                            <a style="margin-left:5px;" onclick="movimenti(<?php echo $a->id ?>)" class="btn btn-sm btn-info">M</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @endif
                <!-- Fine sezione Prodotti Finiti -->

                @if($tipo === 'materia_prima')
                <!-- Inizio sezione Materie Prime (tipologia == 1) -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Materie Prime</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Titolo</th>
                                    <th>Descrizione</th>
                                    <th>Magazzino</th>
                                    <th>Giacenza</th>
                                    <th>Prezzo</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($articoli as $a){ ?>
                                    <?php if($a->tipologia == 1){ // Materie Prime ?>
                                <tr>
                                    <td>
                                        <!-- Visualizza il barcode e cliccandoci apre l'immagine in una nuova scheda -->
                                        <a href="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($a->barcode) }}&code=Code128&translate-esc=on" target="_blank">
                                            <img  style="width: 30%;" alt="Barcode" src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($a->barcode) }}&code=Code128&translate-esc=on"/>
                                        </a>
                                        <!-- Aggiungi il pulsante per stampare solo il barcode -->
                                        <button onclick="printBarcode('{{ urlencode($a->barcode) }}')" class="btn btn-success print-hidden">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </td>
                                    <td><?php echo $a->titolo ?><br><small>Materiale</small></td>
                                    <td><small>Descrizione: <?php echo nl2br($a->descrizione) ?></small>
                                            <?php foreach($a->distinta_base as $db){ ?>
                                        <small><?php echo '<br>'.$db->materiale.' ('.$db->qta.' '.$db->um.')' ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>{{ $a->magazzino_descrizione }}</td>
                                    <td><?php echo $a->giacenza ?> <?php echo $a->um ?></td>
                                    <td>&euro;<?php echo $a->prezzo ?>/<?php echo $a->um ?></td>
                                    <td>
                                        <div style="display: flex">
                                            <a style="margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo articolo ?')">
                                                <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                                <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
                                            <a style="margin-left:5px;" onclick="carica(<?php echo $a->id ?>)" class="btn btn-sm btn-success"><i class="ri-add-line"></i></a>
                                            <a style="margin-left:5px;" onclick="scarica(<?php echo $a->id ?>)" class="btn btn-sm btn-warning"><i class="ri-subtract-line"></i></a>
                                            <a style="margin-left:5px;" onclick="rettifica(<?php echo $a->id ?>)" class="btn btn-sm btn-info">R</a>
                                            <a style="margin-left:5px;" onclick="movimenti(<?php echo $a->id ?>)" class="btn btn-sm btn-info">M</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @endif
                <!-- Fine sezione Materie Prime -->
            </div>


        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>
<div class="modal fade" id="modal_aggiungi" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Articolo</h5>
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
                                            <img src="/placehold_immagine.png" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" class="form-control" placeholder="Titolo" required/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Codice Articolo<b style="color:red">*</b></label>
                            <input type="text" id="codice_articolo" name="codice_articolo" class="form-control" placeholder="Codice Articolo" required/>
                        </div>

                        {{--<div class="col-md-12">
                            <label class="form-label">Magazzino<b style="color:red">*</b></label>
                            <select name="id_mg" class="form-control" required>
                                <option value="">Seleziona Magazzino</option>
                                @foreach ($magazzini as $magazzino)
                                    <option value="{{ $magazzino->codice_magazzino }}">{{ $magazzino->descrizione }}</option>
                                @endforeach
                            </select>
                        </div>--}}


                            <div class="col-lg-4" >
                                <label for="fasi" class="form-label">Fasi<b style="color:red">*</b></label>
                                <select data-choices data-choices-removeItem multiple name="fasi[]" style="width: 100%;" >
                                    @foreach ($fasi as $fase)
                                        <option value="{{ $fase->id }}"
                                                @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                            {{ $fase->descrizione }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>



                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0">Prodotto Finito</option>
                                <option value="1">Materia Prima</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo<b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" placeholder="Unità di Misura" required/>
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



<?php foreach($articoli as $a){ ?>


<div class="modal fade" id="modal_modifica_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Articolo</h5>
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
                                            <img src="<?php echo $a->immagine ?>" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" value="<?php echo $a->titolo ?>" class="form-control" placeholder="Titolo" required/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Codice Articolo<b style="color:red">*</b></label>
                            <input type="text" id="codice_articolo" value="<?php echo $a->codice_articolo ?>" name="codice_articolo" class="form-control" placeholder="Codice Articolo" required/>
                        </div>
                        {{--<div class="col-md-12">
                            <label class="form-label">Magazzino<b style="color:red">*</b></label>
                            <select name="id_mg" class="form-control" required>
                                <option value="">Seleziona Magazzino</option>
                                @foreach ($magazzini as $magazzino)
                                    <option value="{{ $magazzino->codice_magazzino }}">{{ $magazzino->descrizione }}</option>
                                @endforeach
                            </select>
                        </div>
--}}
                        <div class="col-md-12">
                            <label for="fasi" class="form-label">Fasi<b style="color:red">*</b></label>
                            <select data-choices data-choices-removeItem multiple name="fasi[]" id="fasi_{{ $a->id }}"  style="width: 100%;" >
                                @foreach ($fasi as $fase)
                                    <option value="{{ $fase->id }}"
                                            @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                        {{ $fase->descrizione }}
                                    </option>
                                @endforeach
                            </select>
                        </div>





                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0" <?php echo ($a->tipologia == 0)?'selected':'' ?>>Prodotto Finito</option>
                                <option value="1" <?php echo ($a->tipologia == 1)?'selected':'' ?>>Materia Prima</option>
                            </select>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"><?php echo $a->descrizione ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo<b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" value="<?php echo $a->prezzo ?>" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" value="<?php echo $a->um ?>" placeholder="Unità di Misura" required/>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{--distinta base vecchia--}}
{{--<div class="modal fade" id="modal_db_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Distinta Base</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                            <?php for($i=0;$i<5;$i++){ ?>

                        <div class="row">

                            <div class="col-md-9">
                                    <?php if($i == 0) { ?><label>Materiale <b style="color:red">*</b></label><?php } ?>
                                <select id="db_<?php echo $a->id ?>_<?php echo $i ?>" name="materiale[<?php echo $i ?>]" class="form-control select2" style="width:100%;">
                                    <option value="">Nessun Materiale</option>
                                        <?php foreach($materiali as $m){ ?>
                                    <option value="<?php echo $m->id ?>" costo="<?php echo $m->prezzo ?>" <?php echo (isset($a->distinta_base[$i]) && $a->distinta_base[$i]->id_materiale ==$m->id)?'selected':'' ?>><?php echo $m->titolo ?> (<?php echo $m->um ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                    <?php if($i == 0) { ?><label>Qta<b style="color:red">*</b></label><?php } ?>
                                <input id="qta_db_<?php echo $a->id ?>_<?php echo $i ?>" type="number" min="0" step="0.0001" name="quantita[<?php echo $i ?>]" value="<?php echo (isset($a->distinta_base[$i]))?$a->distinta_base[$i]->qta:'' ?>" class="form-control" onkeyup="calcola_costo_db(<?php echo $a->id ?>,<?php echo $a->prezzo ?>)" onchange="calcola_costo_db(<?php echo $a->id; ?>, <?php echo $a->prezzo ?>)">
                            </div>

                        </div>

                        <?php } ?>


                        <div class="row">

                            <div class="col-md-6" style="text-align:right"><br>
                                <b style="font-size: 15px;">Costo Materie Prime:</b><br><br>
                                <b style="font-size: 15px;">Prezzo di Vendita:</b><br><br>
                                <b style="font-size: 15px;">Percentuale Costo:</b><br>
                            </div>

                            <div class="col-md-3" style="text-align:left"><br>
                                <b style="font-size: 20px;" id="costo_materia_prima_<?php echo $a->id ?>"></b><br>
                                <input name="prezzo"  id="prezzo_<?php echo $a->id ?>" class="form-control" value="<?php echo $a->prezzo ?>" onkeyup="RicalcoloPercentuale(<?php echo $a->id ?>)"><br>
                                <b style="font-size: 20px;" id="incidenza_<?php echo $a->id ?>"></b><br>
                            </div>

                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica_db" value="Salva Distinta Base" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>--}}
{{--distinta base vecchia--}}





@foreach($articoli as $articolo)
    <div class="modal fade modal-xl" id="modal_db_{{ $articolo->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Modifica Distinta Base - Articolo: {{ $articolo->titolo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <!-- Cicla solo sulle fasi associate a questo articolo -->
                        @if(isset($fasi_associate[$articolo->id]) && count($fasi_associate[$articolo->id]) > 0)
                            @foreach($fasi_associate[$articolo->id] as $fase)
                                <div class="card mb-3">
                                    <div class="card-header" id="heading_{{ $fase->id }}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#fase_{{ $articolo->id }}_{{ $fase->id }}" aria-expanded="true" aria-controls="fase_{{ $articolo->id }}_{{ $fase->id }}">
                                                Fase: {{ $fase->descrizione }}
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="fase_{{ $articolo->id }}_{{ $fase->id }}" class="collapse" aria-labelledby="heading_{{ $fase->id }}" data-bs-parent="#accordion">
                                        <div class="card-body">
                                            <!-- Itera sui 5 slot per i materiali -->
                                            @for($i=0; $i<5; $i++)
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <label>Materiale <b style="color:red">*</b></label>
                                                        <!-- Nota: L'ID della fase ($fase->id) viene utilizzato qui -->
                                                        <select id="db_{{ $articolo->id }}_{{ $fase->id }}_{{ $i }}" name="materiale[{{ $fase->id }}][{{ $i }}]" class="form-control select2" style="width:100%;" onchange="calcolaCostoTotale({{ $articolo->id }})">
                                                            <option value="">Nessun Materiale</option>
                                                            @foreach($materiali as $m)
                                                                <option value="{{ $m->id }}" costo="{{ $m->prezzo }}" {{ isset($fase->distinta_base[$i]) && $fase->distinta_base[$i]->id_materiale == $m->id ? 'selected' : '' }}>
                                                                    {{ $m->titolo }} ({{ $m->um }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label>Qta <b style="color:red">*</b></label>
                                                        <!-- Nota: L'ID della fase ($fase->id) viene utilizzato qui -->
                                                        <input id="qta_db_{{ $articolo->id }}_{{ $fase->id }}_{{ $i }}" type="number" min="0" step="0.0001" name="quantita[{{ $fase->id }}][{{ $i }}]" value="{{ isset($fase->distinta_base[$i]) ? $fase->distinta_base[$i]->qta : '' }}" class="form-control" onkeyup="calcolaCostoTotale({{ $articolo->id }})" onchange="calcolaCostoTotale({{ $articolo->id }})">
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Nessuna fase associata a questo articolo.</p>
                        @endif

                        <!-- Sezione di calcolo complessiva sotto tutte le fasi per l'articolo -->
                        <div class="row">

                            <div class="col-md-6" style="text-align:right">
                                <b>Costo Materie Prime Totale:</b><br><br>
                                <b>Prezzo di Vendita Totale:</b><br><br>
                                <b>Percentuale Costo Totale:</b><br>
                            </div>

                            <div class="col-md-3" style="text-align:left">
                                <b id="costo_materia_prima_totale_{{ $articolo->id }}"></b><br>
                                <input name="prezzo_totale" id="prezzo_totale_{{ $articolo->id }}" class="form-control" value="{{ $articolo->prezzo }}" onkeyup="ricalcoloPercentualeTotale({{ $articolo->id }})" onchange="ricalcoloPercentualeTotale({{ $articolo->id }})"><br>
                                <b id="incidenza_totale_{{ $articolo->id }}"></b><br>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="{{ $articolo->id }}">
                        <input type="submit" class="btn btn-success" name="modifica_db" value="Salva Distinta Base" >
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach






<?php } ?>


<div id="ajax_loader"></div>


@include('default.common.footer')
<!-- Includi jQuery se non già incluso -->


<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc!important;
        border-color: #367fa9!important;
        padding: 1px 10px!important;
        color: #fff!important;
    }
</style>
<script type="text/javascript">







    /*function calcola_costo_db(faseId) {
        let costoTotale = 0;

        for (let i = 0; i < 5; i++) {
            const materialeSelect = document.getElementById(`db_${faseId}_${i}`);
            const quantitaInput = document.getElementById(`qta_db_${faseId}_${i}`);

            if (materialeSelect && quantitaInput) {
                const costoMateriale = parseFloat(materialeSelect.selectedOptions[0].getAttribute('costo')) || 0;
                const quantita = parseFloat(quantitaInput.value) || 0;
                costoTotale += costoMateriale * quantita;
            }
        }

        document.getElementById(`costo_materia_prima_${faseId}`).innerText = costoTotale.toFixed(4);
        RicalcoloPercentuale(faseId);
    }*/

    function printBarcode(barcodeData) {
        // URL per il barcode
        const url = `https://barcode.tec-it.com/barcode.ashx?data=${barcodeData}&code=Code128&translate-esc=on`;

        // Apri una nuova finestra con il barcode
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Stampa Barcode</title>
                </head>
                <body>
                    <img src="${url}" alt="Barcode" />
                </body>
            </html>
        `);

        // Attendi che l'immagine venga caricata e poi avvia la stampa
        printWindow.document.close(); // Chiude il documento per il rendering
        printWindow.focus(); // Imposta la finestra come attiva

        // Stampa il contenuto della finestra
        printWindow.onload = function() {
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close(); // Chiudi la finestra dopo la stampa
            };
        };
    }


    function calcolaCostoTotale(articoloId) {
        let costoTotale = 0;

        // Itera su tutte le fasi e i materiali per l'articolo specifico
                @foreach($fasi as $fase)
        for (let i = 0; i < 5; i++) {
            const materialeSelect = document.getElementById(`db_${articoloId}_{{ $fase->id }}_${i}`);
            const quantitaInput = document.getElementById(`qta_db_${articoloId}_{{ $fase->id }}_${i}`);

            if (materialeSelect && quantitaInput) {
                const costoMateriale = parseFloat(materialeSelect.selectedOptions[0]?.getAttribute('costo')) || 0;
                const quantita = parseFloat(quantitaInput.value) || 0;

                costoTotale += costoMateriale * quantita;
            }
        }
        @endforeach

        // Aggiorna il costo totale per l'articolo specifico
        document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText = costoTotale.toFixed(4);
        ricalcoloPercentualeTotale(articoloId);
    }

    function ricalcoloPercentualeTotale(articoloId) {
        const costoTotale = parseFloat(document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText) || 0;
        const prezzoVenditaInput = document.getElementById(`prezzo_totale_${articoloId}`).value;
        const prezzoVenditaFinale = parseFloat(prezzoVenditaInput) || 0;

        if (prezzoVenditaFinale > 0) {
            const incidenza = (costoTotale / prezzoVenditaFinale) * 100;
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = incidenza.toFixed(2) + '%';
        } else {
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = '0%';
        }
    }





    function scarica(id){
        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_scarico') ?>/"+id,
            type:'GET',
            success: function(result) {
                console.log(result);  // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_scarica_' + id).modal('show');

            }
        });
    }

    function carica(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_carico') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_carica_' + id).modal('show')

            }
        });
    }

    function rettifica(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_rettifica') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_rettifica_' + id).modal('show')

            }
        });
    }

    function movimenti(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_movimenti') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_mgmov_' + id).modal('show')

            }
        });
    }

   /* function RicalcoloPercentuale(id_articolo){
        let costo_mat = parseFloat(($('#costo_materia_prima_'+id_articolo)[0].innerHTML).slice(1));
        let prezzo = parseFloat($('#prezzo_'+id_articolo)[0].value);
        $('#incidenza_'+id_articolo)[0].innerHTML = ((( costo_mat/prezzo ) * 100).toFixed(2)) + "%";
    }*/

    function aggiungi(){
        // Mostra la modal
        $('#modal_aggiungi').modal('show');

        // Inizializza select2 all'interno della modal
        $('.js-example-basic-multiple').select2({
            width: '100%' // Aggiungi l'opzione per usare il 100% della larghezza
        });
    }


    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function distinta_base(id,prezzo_vendita){
        $('#modal_db_'+id).modal('show');

        calcola_costo_db(id,prezzo_vendita);
    }

    /*function calcola_costo_db(id_articolo,prezzo_vendita){
        totale_costo = 0;
        for(i = 0;i<5;i++) {

            if($('#db_' + id_articolo + '_'+i).val() != '') {
                totale_costo = totale_costo + (parseFloat($('#db_' + id_articolo + '_' + i).find(':selected').attr('costo')) * parseFloat($('#qta_db_' + id_articolo + '_' + i).val()));
            }
        }

        $('#costo_materia_prima_'+id_articolo).html('&euro;'+parseFloat(totale_costo).toFixed(4));

        incidenza = parseFloat(((parseFloat(totale_costo) / parseFloat(prezzo_vendita))) * 100).toFixed(2);

        $('#incidenza_'+id_articolo).html('% '+parseFloat(incidenza).toFixed(2));
        $('#totale_costo_'+id_articolo)[0].value = totale_costo;

    }*/

</script>

<style>

    div.dataTables_wrapper div.dataTables_filter label {
        font-weight: normal;
        white-space: nowrap;
        text-align: left;
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: .5em;
        display: inline-block;
        width: 89%;
    }
</style>