@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Clienti</h4>

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
                                <button class="btn btn-info add-btn" onclick="aggiungi_preventivo();">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Preventivo
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
                            <h5 class="card-title mb-0">Preventivi</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Data</th>
                                    <th style="width:150px;">Cliente</th>
                                    <th style="width:300px;">Descrizione</th>
                                    <th>Allegato</th>
                                    <th>Totale</th>
                                    <th>Da Incassare</th>
                                    <th>Status</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $totale_valore = 0; $totale_da_incassare = 0; ?>
                                <?php foreach($preventivi as $p){ $totale_valore += $p->totale; $totale_da_incassare += ($p->totale - $p->incassato);  ?>
                                <tr>

                                    <td>
                                        <?php echo date('d/m/Y',strtotime($p->data)) ?>
                                    </td>
                                    <td><a href="<?php echo URL::asset('admin/dettaglio_utente/'.$p->id_utente) ?>" target="_blank"><?php echo $p->ragione_sociale ?></a>&nbsp;
                                        <?php if($p->ordine_di_lavoro > 0){ ?>
                                            <span class="badge bg-success" style="font-size:15px;"><?php echo $p->ordine_di_lavoro ?></span>
                                        <?php } ?>

                                    </td>
                                    <td>
                                        <?php echo $p->descrizione ?>
                                        <?php if($p->note != ''){ ?>
                                            <br><small><?php echo $p->note ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($p->allegato != ''){ ?>
                                            <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($p->allegato) ?>">Allegato</a>
                                        <?php } ?>
                                    </td>
                                    <td>&euro;<?php echo number_format($p->totale,2,'.','') ?></td>
                                    <td>&euro;<?php echo number_format($p->totale - $p->incassato,2,'.','') ?></td>
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

                                        <?php if($p->status == 4){ ?>
                                            <span class="badge bg-danger">Annullato</span>
                                        <?php } ?>

                                    </td>
                                    <td>
                                        <a style="float:left" onclick="modifica_preventivo(<?php echo $p->id ?>)" class="btn btn-sm btn-primary">M</a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questo preventivo ?')">
                                            <input type="hidden" name="id" value="<?php echo $p->id ?>">
                                            <input style="float:left;margin-left:5px;" type="submit" name="elimina_preventivo" value="E" class="btn btn-sm btn-danger">
                                        </form>

                                        <a style="float:left;margin-left:5px;" onclick="aggiungi_cashflow(<?php echo $p->id ?>)" class="btn btn-sm btn-success">+</a>

                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right;">Totale Preventivi</td>
                                    <td><h5>&euro;<?php echo number_format($totale_valore,2,'.','') ?></h5></td>
                                    <td><h5>&euro;<?php echo number_format($totale_da_incassare,2,'.','') ?></h5></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
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



<div class="modal fade" id="modal_aggiungi_preventivo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Preventivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d') ?>" placeholder="Data" required />
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
                                <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                <select name="id_utente" class="form-control select2" Required>
                                    <option value="">Scegli un Cliente</option>
                                    <?php foreach($clienti as $c){ ?>
                                        <option value="<?php echo $c->id ?>"><?php echo $c->ragione_sociale ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Allegato <b style="color:red">*</b></label>
                                <input type="file" name="allegato" class="form-control"  />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Totale<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="totale" class="form-control" placeholder="Totale" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Canone <b style="color:red">*</b></label>
                                <input type="number" step="0.00" name="canone" class="form-control" placeholder="Canone" required />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Data Canone<b style="color:red">*</b></label>
                                <input type="date" name="data_canone" class="form-control" value="<?php echo date('Y-01-01',strtotime('+1 year')) ?>" placeholder="Data" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Provvigione Agente<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="provvigione" value="0" class="form-control" placeholder="Provvigione" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Incassato<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="incassato" value="0" class="form-control" placeholder="Incassato" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Pagato<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="pagato" value="0" class="form-control" placeholder="Pagato" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Status <b style="color:red">*</b></label>
                                <select name="status" class="form-control select2">
                                    <option value="0" <?php echo ($status == 0)?'selected':'' ?>>Inviato</option>
                                    <option value="1" <?php echo ($status == 1)?'selected':'' ?>>Confermato</option>
                                    <option value="2" <?php echo ($status == 2)?'selected':'' ?>>In Lavorazione</option>
                                    <option value="3" <?php echo ($status == 3)?'selected':'' ?>>Completato</option>
                                    <option value="4" <?php echo ($status == 4)?'selected':'' ?>>Annullato</option>
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
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_preventivo" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_aggiungi_cashflow" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Cashflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label>Data <b style="color:red">*</b></label>
                            <input class="form-control" type="date" name="data" value="<?php echo date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Descrizione <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" class="form-control" placeholder="Descrizione" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Allegato <b style="color:red">*</b></label>
                                <input type="file" name="allegato" class="form-control"  />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Valore <b style="color:red">*</b></label>
                                <input type="number" name="valore" step="0.01" class="form-control" placeholder="Valore" required />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Incassato / Pagato</label>
                                <select name="incassato" class="form-control select2">
                                    <option value="0" <?php echo ($status == 0)?'selected':'' ?>>NO</option>
                                    <option value="1" <?php echo ($status == 1)?'selected':'' ?>>SI</option>
                                </select>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_preventivo" id="id_preventivo_cashflow">
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_cashflow" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php foreach($preventivi as $p){ ?>

<div class="modal fade" id="modal_modifica_preventivo_<?php echo $p->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Preventivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d',strtotime($p->data)) ?>" placeholder="Data" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" class="form-control" value="<?php echo $p->descrizione ?>" placeholder="Descrizione" required />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                <select name="id_utente" class="form-control select2">
                                    <option value="">Scegli un Cliente</option>
                                    <?php foreach($clienti as $c){ ?>
                                        <option value="<?php echo $c->id ?>" <?php echo ($c->id == $p->id_utente)?'selected':'' ?>><?php echo $c->ragione_sociale ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Allegato <b style="color:red">*</b></label>
                                <input type="file" name="allegato" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Totale<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="totale" value="<?php echo $p->totale ?>" class="form-control" placeholder="Totale" required />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Canone <b style="color:red">*</b></label>
                                <input type="number" step="0.00" name="canone" value="<?php echo $p->canone ?>" class="form-control" placeholder="Canone" required />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Data Canone<b style="color:red">*</b></label>
                                <input type="date" name="data_canone" class="form-control" value="<?php echo date('Y-01-01',strtotime($p->data_canone)) ?>" placeholder="Data" required />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Provvigione Agente<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="provvigione" value="<?php echo $p->provvigione ?>" class="form-control" placeholder="Provvigione" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Incassato<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="incassato" value="<?php echo $p->incassato ?>" class="form-control" placeholder="Incassato" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Pagato<b style="color:red">*</b></label>
                                <input type="number" step="0.00"  name="pagato" value="<?php echo $p->pagato ?>" class="form-control" placeholder="Pagato" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Status <b style="color:red">*</b></label>
                                <select name="status" class="form-control select2">
                                    <option value="0" <?php echo ($p->status == 0)?'selected':'' ?>>Inviato</option>
                                    <option value="1" <?php echo ($p->status == 1)?'selected':'' ?>>Confermato</option>
                                    <option value="2" <?php echo ($p->status == 2)?'selected':'' ?>>In Lavorazione</option>
                                    <option value="3" <?php echo ($p->status == 3)?'selected':'' ?>>Completato</option>
                                    <option value="4" <?php echo ($p->status == 4)?'selected':'' ?>>Annullato</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Ordine di Lavoro <b style="color:red">*</b></label>
                                <select name="ordine_di_lavoro" class="form-control select2">
                                    <option value="0" <?php echo ($p->ordine_di_lavoro == 0)?'selected':'' ?>>Nessun Ordine</option>
                                    <option value="1" <?php echo ($p->ordine_di_lavoro == 1)?'selected':'' ?>>1</option>
                                    <option value="2" <?php echo ($p->ordine_di_lavoro == 2)?'selected':'' ?>>2</option>
                                    <option value="3" <?php echo ($p->ordine_di_lavoro == 3)?'selected':'' ?>>3</option>
                                    <option value="4" <?php echo ($p->ordine_di_lavoro == 4)?'selected':'' ?>>4</option>
                                    <option value="5" <?php echo ($p->ordine_di_lavoro == 5)?'selected':'' ?>>5</option>
                                    <option value="6" <?php echo ($p->ordine_di_lavoro == 6)?'selected':'' ?>>6</option>
                                    <option value="7" <?php echo ($p->ordine_di_lavoro == 7)?'selected':'' ?>>7</option>
                                    <option value="8" <?php echo ($p->ordine_di_lavoro == 8)?'selected':'' ?>>8</option>
                                    <option value="9" <?php echo ($p->ordine_di_lavoro == 9)?'selected':'' ?>>9</option>
                                    <option value="10" <?php echo ($p->ordine_di_lavoro == 10)?'selected':'' ?>>10</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Note <b style="color:red">*</b></label>
                                <textarea name="note" class="form-control" style="height:200px" placeholder="note"><?php echo $p->note ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $p->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica_preventivo" value="Modifica" >
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

    function aggiungi_cashflow(id){
        $('#id_preventivo_cashflow').val(id);
        $('#modal_aggiungi_cashflow').modal('show');
    }

    function aggiungi_preventivo(){
        $('#modal_aggiungi_preventivo').modal('show');
    }

    function modifica_preventivo(id){
        $('#modal_modifica_preventivo_'+id).modal('show');
    }


</script>
