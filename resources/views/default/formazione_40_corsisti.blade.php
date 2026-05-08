@include('default.common.header')

<div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Corsisti Corso <?php echo $c->descrizione ?></h4>

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
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Corsista
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
                                <h5 class="card-title mb-0">Moduli</h5>
                            </div>
                            <div class="card-body">
                                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nominativo</th>
                                        <th style="width:100px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($corsisti as $c){ ?>
                                            <tr>
                                                <td><?php echo $c->id ?></td>
                                                <td><?php echo $c->nominativo ?></td>
                                                <td>


                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <a style="float:left" onclick="modifica(<?php echo $c->id ?>)" class="btn btn-sm btn-primary">M</a>

                                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo corsista ?')">
                                                                <input type="hidden" name="id" value="<?php echo $c->id ?>">
                                                                <input style="float:left;margin-left:5px;" type="submit" name="elimina" value="E" class="btn btn-sm btn-danger">
                                                            </form>
                                                        </div>
                                                    </div>


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


<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Corsista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Nominativo <b style="color:red">*</b></label>
                                <input type="text" name="nominativo" class="form-control" placeholder="Nominativo" required />
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_corso" value="<?php echo $id_corso ?>">
                        <input type="hidden" name="id_cliente" value="<?php echo $utente->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end add modal-->

<?php foreach($corsisti as $c){ ?>


<div class="modal fade" id="modal_modifica_<?php echo $c->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Corsista <?php echo $c->nominativo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Nominativo <b style="color:red">*</b></label>
                                <input type="text" name="nominativo" value="<?php echo $c->nominativo ?>" class="form-control" placeholder="Nominativo" required />
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_corso" value="<?php echo $id_corso ?>">
                        <input type="hidden" name="id_cliente" value="<?php echo $utente->id ?>">
                        <input type="hidden" name="id" value="<?php echo $c->id ?>">
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

    function elimina(id){
        $('#id_elimina_utente').val(id);
        $('#modal_elimina').modal('show');
    }

</script>