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
                        <h3 class="text-white mb-1"><?php echo $user->ragione_sociale ?></h3>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i><?php echo $user->indirizzo ?>, <?php echo $user->comune ?> (<?php echo $user->provincia ?>)</div>
                            <div>
                                <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
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
                            <li class="nav-item">
                                <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                    <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Preventivi</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab" href="#activities" role="tab">
                                    <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Fatture</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">
                                    <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Allegati</span>
                                </a>
                            </li>
                        </ul>

                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content pt-4 text-muted">
                        <div class="tab-pane active" id="overview-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h5 class="card-title mb-3" style="float:left;">Preventivi</h5>

                                            <a onclick="aggiungi_preventivo()" style="float:right;" class="btn btn-success">Aggiungi Preventivi</a>

                                            <div class="clearfix" style="margin-bottom:30px;"></div>
                                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Descrizione</th>
                                                        <th>Allegato</th>
                                                        <th>Totale</th>
                                                        <th>Status</th>
                                                        <th>Azioni</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $totale_valore = 0;?>
                                                <?php foreach($preventivi as $p){ if($p->status == 2 || $p->status == 3) $totale_valore += $p->totale;  ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo date('d/m/Y',strtotime($p->data)) ?>
                                                            <?php if($p->note != ''){ ?>
                                                                <br><small><?php echo $p->note ?></small>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $p->descrizione ?></td>
                                                        <td><a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($p->allegato) ?>">Allegato</a></td>
                                                        <td>&euro;<?php echo number_format($p->totale,2,'.','') ?></td>
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
                                                                <input style="float:left;margin-left:10px;" type="submit" name="elimina_preventivo" value="E" class="btn btn-sm btn-danger">
                                                            </form>
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
                        <div class="tab-pane fade" id="activities" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                </div>
                                <!--end card-body-->
                            </div>
                            <!--end card-->
                        </div>
                        <div class="tab-pane fade" id="projects" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <!--end row-->
                                </div>
                                <!--end card-body-->
                            </div>
                            <!--end card-->
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
                                    <option value="0">Inviato</option>
                                    <option value="1">Confermato</option>
                                    <option value="2">In Lavorazione</option>
                                    <option value="3">Completato</option>
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

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Status <b style="color:red">*</b></label>
                                <select name="status" class="form-control select2">
                                    <option value="0" <?php echo ($p->status == 0)?'selected':'' ?>>Inviato</option>
                                    <option value="1" <?php echo ($p->status == 1)?'selected':'' ?>>Confermato</option>
                                    <option value="2" <?php echo ($p->status == 2)?'selected':'' ?>>In Lavorazione</option>
                                    <option value="3" <?php echo ($p->status == 3)?'selected':'' ?>>Completato</option>
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

    function aggiungi_preventivo(){
        $('#modal_aggiungi_preventivo').modal('show');
    }

    function modifica_preventivo(id){
        $('#modal_modifica_preventivo_'+id).modal('show');
    }


</script>

