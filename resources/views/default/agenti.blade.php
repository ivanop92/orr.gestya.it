@include('default.common.header')

<div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Agenti</h4>

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
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Agente
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
                                <h5 class="card-title mb-0">Agenti</h5>
                            </div>
                            <div class="card-body">
                                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Immagine</th>
                                        <th>Nome</th>
                                        <th>Cognome</th>
                                        <th>Ragione Sociale</th>
                                        <th>Email</th>
                                        <th>Budget</th>
                                        <th style="width:100px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($agenti as $a){ ?>
                                            <tr>
                                                <td><img style="max-width:60px;height: auto;" src="<?php echo URL::asset($a->immagine) ?>"></td>
                                                <td><?php echo $a->nome ?></td>
                                                <td><?php echo $a->cognome ?></td>
                                                <td><?php echo $a->ragione_sociale ?></td>
                                                <td><?php echo $a->email ?></td>
                                                <td><span class="badge bg-primary">&euro; <?php echo number_format($a->budget,2,'.','') ?></span></td>
                                                <td>
                                                    <a style="float:left" href="/admin/dettaglio_utente/<?php echo $a->id ?>" class="btn btn-sm btn-success">V</a>
                                                    <a style="float:left;margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary">M</a>
                                                    <form method="post" onsubmit="return confirm('Vuoi Eliminare questo agente ?')">
                                                        <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                                        <input style="float:left;margin-left:5px;" type="submit" name="elimina_attivita" value="E" class="btn btn-sm btn-danger">
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


<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Agente</h5>
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

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                <input type="text" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" required />
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
<!--end add modal-->

<?php foreach($agenti as $a){ ?>

    <div class="modal fade" id="modal_modifica_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Agente <?php echo $a->nome.' '.$a->cognome ?></h5>
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
                                            <img style="width:50%;margin:0 auto;display: block;" src="<?php echo URL::asset($a->immagine) ?>"  />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Nome <b style="color:red">*</b></label>
                                <input type="text" name="nome" class="form-control" value="<?php echo $a->nome ?>" placeholder="Nome" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Cognome <b style="color:red">*</b></label>
                                <input type="text" name="cognome" class="form-control" value="<?php echo $a->cognome ?>" placeholder="Cognome" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                <input type="text" name="ragione_sociale" class="form-control" value="<?php echo $a->ragione_sociale ?>" placeholder="Ragione Sociale" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Email <b style="color:red">*</b></label>
                                <input type="email" name="email" class="form-control" value="<?php echo $a->email ?>" placeholder="Email" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" class="form-control" value="<?php echo $a->telefono ?>" placeholder="Telefono" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Indirizzo</label>
                                <input type="text" name="indirizzo" class="form-control" value="<?php echo $a->indirizzo ?>" placeholder="Indirizzo" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Comune</label>
                                <input type="text" name="comune" class="form-control" value="<?php echo $a->comune ?>" placeholder="Comune" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" class="form-control" value="<?php echo $a->provincia ?>" placeholder="Provincia" />
                            </div>
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