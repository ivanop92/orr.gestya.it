@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Agenti</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Utente</a></li>
                            <li class="breadcrumb-item active">Agenti</li>
                        </ol>
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
                                <h5 class="card-title mb-0">Lista Agenti</h5>
                            </div>

                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                            <thead>
                            <tr>
                                <th>Immagine</th>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($agenti as $a){ ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $a->immagine ?>" class="avatar-xs rounded-circle" />
                                </td>
                                <td><?php echo $a->nome ?></td>
                                <td><?php echo $a->cognome ?></td>
                                <td><?php echo $a->email ?></td>
                                <td><?php echo $a->telefono ?></td>
                                <td>
                                    <div style="display: flex">
                                        <a href="{{ url('utente/provvigioni_agente/' . $a->id) }}" style="margin-left:5px;" class="btn btn-sm btn-success">
                                            <i class="ri-percent-line"></i>
                                        </a>
                                        <a style="margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questo agente?')">
                                            <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                            <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi -->
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
                            <label class="form-label">Nome<b style="color:red">*</b></label>
                            <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cognome<b style="color:red">*</b></label>
                            <input type="text" id="cognome" name="cognome" class="form-control" placeholder="Cognome" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email<b style="color:red">*</b></label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password<b style="color:red">*</b></label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Telefono"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Nascita</label>
                            <input type="date" id="data_nascita" name="data_nascita" class="form-control"/>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="abilitato" name="abilitato" value="1" checked>
                                <label class="form-check-label" for="abilitato">Abilitato</label>
                            </div>
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

<!-- Modal Modifica -->
<?php foreach($agenti as $a){ ?>
<div class="modal fade" id="modal_modifica_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Agente</h5>
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
                        <div class="col-md-6">
                            <label class="form-label">Nome<b style="color:red">*</b></label>
                            <input type="text" id="nome" name="nome" value="<?php echo $a->nome ?>" class="form-control" placeholder="Nome" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cognome<b style="color:red">*</b></label>
                            <input type="text" id="cognome" name="cognome" value="<?php echo $a->cognome ?>" class="form-control" placeholder="Cognome" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email<b style="color:red">*</b></label>
                            <input type="email" id="email" name="email" value="<?php echo $a->email ?>" class="form-control" placeholder="Email" required/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Lascia vuoto per non modificare"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" id="telefono" name="telefono" value="<?php echo $a->telefono ?>" class="form-control" placeholder="Telefono"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Nascita</label>
                            <input type="date" id="data_nascita" name="data_nascita" value="<?php echo $a->data_nascita ?>" class="form-control"/>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="abilitato" name="abilitato" value="1" <?php echo ($a->abilitato == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="abilitato">Abilitato</label>
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

<script type="text/javascript">
    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }
</script>

@include('utente.common.footer')