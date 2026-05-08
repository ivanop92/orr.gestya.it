@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">leads</h4>

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
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Lead
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Leads</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Data</th>
                                    <th style="width:150px;">Cliente</th>
                                    <th style="width:350px;">Descrizione</th>
                                    <th>Totale</th>
                                    <th style="width: 50px;">Recapiti</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $totale = 0;  ?>
                                <?php foreach($leads as $l){ $totale += $l->totale; ?>
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

                                        <span class="badge bg-success"><?php echo $l->operatore ?></span>

                                    </td>
                                    <td>
                                        <a style="float:left" onclick="modifica(<?php echo $l->id ?>)" class="btn btn-sm btn-primary">M</a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questa Lead ?')">
                                            <input type="hidden" name="id" value="<?php echo $l->id ?>">
                                            <input style="float:left;margin-left:5px;" type="submit" name="elimina" value="E" class="btn btn-sm btn-danger">
                                        </form>

                                        <a style="float:left;margin-left:5px;" onclick="crea_preventivo(<?php echo $l->id_utente ?>,<?php echo $l->id ?>)" class="btn btn-sm btn-success">+</a>

                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Totale Leads</td>
                                        <td><h5>&euro;<?php echo number_format($totale,2,'.','') ?></h5></td>
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



<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Lead</h5>
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

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Operatore <b style="color:red">*</b></label>
                                <select name="id_assegnazione" class="form-control js-example-basic-single">
                                    <option value="">Nessun Operatore</option>
                                    <?php foreach($operatori as $o){ ?>
                                        <option value="<?php echo $o->id ?>"><?php echo $o->ragione_sociale ?></option>
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
                                    <option value="0" <?php echo ($status == 0)?'selected':'' ?>>Da Inviare</option>
                                    <option value="1" <?php echo ($status == 1)?'selected':'' ?>>Inviato</option>
                                    <option value="2" <?php echo ($status == 2)?'selected':'' ?>>Confermato</option>
                                    <option value="3" <?php echo ($status == 3)?'selected':'' ?>>Annullato</option>
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
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($leads as $l){ ?>

<div class="modal fade" id="modal_modifica_<?php echo $l->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d',strtotime($l->data)) ?>" placeholder="Data" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                <select name="id_utente"  class="form-control js-example-basic-single">
                                    <option value="">Scegli un Cliente</option>
                                    <?php foreach($clienti as $c){ ?>
                                    <option value="<?php echo $c->id ?>" <?php echo ($c->id == $l->id_utente)?'selected':'' ?>><?php echo $c->ragione_sociale ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Operatore <b style="color:red">*</b></label>
                                <select name="id_assegnazione" class="form-control js-example-basic-single">
                                    <option value="">Nessun Operatore</option>
                                    <?php foreach($operatori as $o){ ?>
                                        <option value="<?php echo $o->id ?>" <?php echo ($o->id == $l->id_assegnazione)?'selected':'' ?>><?php echo $o->ragione_sociale ?></option>
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
                                <textarea name="note" class="form-control" style="height:200px" placeholder="note"><?php echo $l->note ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $l->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                        <?php if($l->id_assegnazione > 0){ ?>
                            <input style="float:left;" type="submit" class="btn btn-primary" id="add-btn" name="assegna_lead" value="Invia Mail Assegnazione" >
                        <?php } ?>
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php } ?>


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

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d') ?>" placeholder="Data" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Cliente <b style="color:red">*</b></label>
                                <select name="id_utente" id="id_utente_preventivo" class="form-control select2" Required>
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
                                <input type="number" step="0.00" name="canone" class="form-control" placeholder="Canone" value="0" required />
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
                        <input type="hidden" name="id_lead" id="id_lead" value="0">
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_preventivo" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('default.common.footer')

<script type="text/javascript">

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }


    function crea_preventivo(id_utente_preventivo,id_lead){
        $('#id_utente_preventivo').val(id_utente_preventivo);
        $('#id_lead').val(id_lead);
        $('#modal_aggiungi_preventivo').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }


</script>
