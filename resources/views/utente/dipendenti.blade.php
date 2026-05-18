@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestione Dipendenti</h4>

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
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0">Dipendenti</h5>
                            <button class="btn btn-info add-btn" onclick="aggiungi();">
                                <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Dipendente
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%;">
                                <thead>
                                <tr>
                                    <th>Immagine</th>
                                    <th>Nome e Cognome</th>
                                    <th>Email</th>
                                    <th>Costo Orario</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($dipendenti as $d){ ?>
                                <tr>
                                    <td>
                                        <div class="position-relative d-inline-block">
                                            <div class="avatar-lg p-1">
                                                <div class="avatar-title bg-light rounded-circle" >
                                                    <img src="{{asset($d->immagine)}}" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <b>Nome:</b>
                                        <?php echo $d->nome ?>
                                        <br>
                                        <b>Cognome:</b>
                                        <?php echo $d->cognome ?>
                                        <br>
                                        <br>
                                        <?php $reparto_corrente = DB::table('reparti')->where('id', $d->id_reparto)->first(); ?>

                                        @if($reparto_corrente)
                                        <b>Reparto:</b> <?php echo $reparto_corrente->descrizione ?>
                                        @else
                                        <b>Nessun Reparto</b>
                                        @endif

                                    </td>
                                    <td><?php echo $d->email ?></td>
                                    <td>&euro; <?php echo number_format($d->costo_orario ?? 0, 2, ',', '.') ?>/h</td>
                                    <td>
                                        <div style="display: flex">
                                            <a style="margin-left:5px;" onclick="modifica(<?php echo $d->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo dipendente ?')">
                                                <input type="hidden" name="id" value="<?php echo $d->id ?>">
                                                <input type="hidden" name="elimina" value="<?php echo $d->id ?>">
                                                <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
                                        </div>
                                        <div style="display: flex">
                                            <form class="mt-2" method="post" onsubmit="return confirm('Vuoi inviare le credenziali di accesso a <?php echo $d->nome ?> <?php echo $d->cognome ?>')">
                                                <input type="hidden" name="id_per_credenziali" value="<?php echo $d->id ?>">
                                                <input type="hidden" name="invia_credenziali" value="<?php echo $d->id ?>">
                                                <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-primary"><i class="ri-mail-send-line"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0">Reparti</h5>
                            <button class="btn btn-info add-btn" onclick="aggiungi_reparto();">
                                <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Reparto
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%; overflow-y: scroll">
                                <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($reparti as $r){ ?>
                                <tr>
                                    <td><?php echo $r->descrizione ?></td>
                                    <td>
                                        <div style="display: flex">
                                            <a style="margin-left:5px;" onclick="modifica_reparto(<?php echo $r->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo reparto ?')">
                                                <input type="hidden" name="id" value="<?php echo $r->id ?>">
                                                <input type="hidden" name="elimina_reparto" value="<?php echo $r->id ?>">
                                                <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
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
    <!-- container-fluid -->
</div>


<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <label class="form-label">Reparto <b style="color:red">*</b></label>
                                <select name="id_reparto" class="form-control" required >
                                    <option selected disabled>-- Seleziona un reparto --</option>
                                    <?php foreach ($reparti as $r) { ?>
                                        <option value="{{$r->id}}">{{$r->descrizione}}</option>
                                    <?php } ?>
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
                                <label  class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">PIN tablet produzione</label>
                                <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" name="pin" class="form-control" placeholder="4 cifre" />
                                <small class="text-muted">Codice numerico a 4 cifre per accesso rapido sul tablet</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Costo Orario (&euro;/h)</label>
                                <input type="number" step="0.01" min="0" name="costo_orario" class="form-control" placeholder="0.00" value="0" />
                                <small class="text-muted">Utilizzato per il calcolo dei costi manodopera nelle commesse</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <input type="hidden" name="manutentore" value="0">
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" id="manutentore_add" name="manutentore" value="1">
                                <label class="form-check-label" for="manutentore_add">
                                    <strong><i class="ri-tools-line me-1"></i>Manutentore</strong>
                                </label>
                            </div>
                            <small class="text-muted">Marca questo dipendente come manutentore: potrà essere assegnato agli Interventi Manutenzione (step 2).</small>
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

<div class="modal fade" id="modal_aggiungi_reparto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Reparto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Nome Reparto <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" class="form-control" placeholder="Nome Reparto..." required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_reparto" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ajax_loader"></div>

@include('utente.common.footer')

<script type="text/javascript">

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function aggiungi_reparto(){
        $('#modal_aggiungi_reparto').modal('show');
    }

    function modifica(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/modifica_dipendente') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_dipendente_'+id).modal('show');
            }
        });
    }

    function modifica_reparto(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/modifica_reparto') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_reparto_'+id).modal('show');
            }
        });
    }

</script>