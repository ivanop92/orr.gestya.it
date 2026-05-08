@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Cashflow</h4>

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
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Riga
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
                            <h5 class="card-title mb-0">Cashflow <?php echo ($status == 0)?'Da Incassare/Pagare':'Incassato/Pagato' ?></h5>
                        </div>
                        <div class="card-body">
                            <table class="datatable table nowrap align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="no-sort">Data</th>
                                    <th>Descrizione</th>
                                    <th>Allegato</th>
                                    <th>Valore</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $totale_totale = 0; ?>
                                <?php foreach($cashflow as $c){ $totale_totale += $c->valore; ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y',strtotime($c->data)) ?></td>
                                        <td>
                                            <?php echo $c->descrizione ?>

                                            <?php if($c->cliente != ''){ ?>
                                                <br><small>Cliente: <?php echo $c->cliente ?></small>
                                            <?php } ?>

                                            <?php if($c->preventivo != ''){ ?>
                                                <br><small>Preventivo: <?php echo $c->preventivo ?></small>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if($c->allegato != ''){ ?>
                                            <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($c->allegato) ?>">Allegato</a>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $c->valore ?></td>
                                        <td>
                                            <a style="float:left" onclick="modifica(<?php echo $c->id ?>)" class="btn btn-sm btn-primary">M</a>

                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questa scadenza ?')">
                                                <input type="hidden" name="id" value="<?php echo $c->id ?>">
                                                <input style="float:left;margin-left:5px;" type="submit" name="elimina" value="E" class="btn btn-sm btn-danger">
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">Totale</td>
                                        <td><h5>&euro;<?php echo number_format($totale_totale,2,'.','') ?></h5></td>
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
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Scadenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-md-12">
                            <label>Data <b style="color:red">*</b></label>
                            <input class="form-control" type="date" name="data" required>
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
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($cashflow as $c){ ?>


<div class="modal fade" id="modal_modifica_<?php echo $c->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Riga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-md-12">
                            <label>Data <b style="color:red">*</b></label>
                            <input class="form-control" type="date" name="data" value="<?php echo $c->data ?>" required>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Descrizione <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" value="<?php echo $c->descrizione ?>" class="form-control" placeholder="Descrizione" required />
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
                                <input type="number" name="valore" value="<?php echo $c->valore ?>" step="0.01" class="form-control" placeholder="Valore" required />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Incassato / Pagato</label>
                                <select name="incassato" class="form-control select2">
                                    <option value="0" <?php echo ($c->incassato == 0)?'selected':'' ?>>NO</option>
                                    <option value="1" <?php echo ($c->incassato == 1)?'selected':'' ?>>SI</option>
                                </select>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $c->id ?>">
                        <input type="hidden" name="id_preventivo" value="<?php echo $c->id_preventivo ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
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

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

</script>