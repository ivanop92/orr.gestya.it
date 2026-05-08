@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bandi</h4>

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
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0">Bandi</h5>
                            <button class="btn btn-info add-btn" onclick="aggiungi();">
                                <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Bando
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Bando</th>
                                    <th>Descrizione</th>
                                    <th style="max-width: 400px; min-width: 400px">Allegati Richiesti</th>
                                    <th>Clienti</th>
                                    <th style="width:130px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($bandi as $b){ ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center">
                                            <?php if($b->immagine_bando != null){ ?>
                                                <img style="width: 150px" src="{{asset($b->immagine_bando)}}" alt="Logo">
                                            <?php } ?>
                                            <p class="m-0 mt-2" style="margin-top: 5px">
                                              <?php echo $b->titolo ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td><p class="btn btn-light btn-sm m-0" onclick="show_descrizione('<?php echo $b->descrizione ?>')" >Descrzione del Bando</p></td>
                                    <td>
                                        <?php $array_allegati = (explode(',', $b->id_allegati)) ?>
                                        <?php $allegati = DB::table('bandi_allegati')->whereIn('id', $array_allegati)->get() ?>
                                        <div style="display: flex; flex-wrap: wrap">
                                            <?php foreach ($allegati as $all){ ?>
                                                <p style="margin: 0; margin-right: 10px; margin-bottom: 7px" class="btn btn-primary btn-sm"  href="#">{{$all->descrizione}}</p>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                            <?php $array_utenti = (explode(',', $b->id_clienti)) ?>
                                            <?php $utenti_selezionati = DB::table('utenti')->whereIn('id', $array_utenti)->get() ?>
                                        <div style="display: flex; flex-wrap: wrap">
                                                <?php foreach ($utenti_selezionati as $ute){ ?>
                                            <a target="_blank" href="{{asset('bandi/' . $ute->token_utente_per_bando . '/' . $b->token_bando)}}" style="display: block; margin: 0; margin-right: 10px; margin-bottom: 7px" class="btn btn-primary btn-sm"  href="#">{{$ute->ragione_sociale}}</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div style="margin-bottom: 10px; display: flex; justify-content: space-between">
                                            <?php if($b->allegati != '' || $b->allegati != NULL){ ?>
                                                <a class="btn btn-danger btn-sm" target="_blank" href="<?php echo URL::asset($b->allegati) ?>">PDF Bando</a>
                                            <?php } ?>
                                            <?php if($b->decreto != '' || $b->decreto != NULL){ ?>
                                                <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($b->decreto) ?>">Decreto</a>
                                            <?php } ?>
                                            </div>
                                            <div style="display: flex; justify-content: space-between">
                                                <div>
                                                    <a onclick="aggiungi_clienti(<?php echo $b->id ?>)" class="btn btn-sm btn-success"><i class="ri-user-2-line"></i></a>
                                                    <a onclick="aggiungi_allegati(<?php echo $b->id ?>)" class="btn btn-sm btn-success"><i class="ri-article-line"></i></a>
                                                </div>
                                                <div style="display: flex">
                                                    <a onclick="modifica(<?php echo $b->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                                    <form method="post" onsubmit="return confirm('Vuoi Archiviare questo bando ?')">
                                                        <input type="hidden" name="id" value="<?php echo $b->id ?>">
                                                        <input type="hidden" name="archivia" value="<?php echo $b->id ?>">
                                                        <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-danger"><i class="ri-inbox-archive-line"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div style="margin-top: 10px">
                                                <a style="width: 50%" onclick="visualizza_clienti(<?php echo $b->id ?>)" class="btn btn-sm btn-success">Clienti</a>
                                                <a style="width: 50%" onclick="modal_invia_mail(<?php echo $b->id ?>)" class="btn btn-sm btn-success">Invia Mail</a>
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
    <!-- container-fluid -->
</div>


<div class="modal modal-xl fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Bando</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Logo per Bando</label>
                            <input class="form-control" type="file" name="immagine_bando" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" name="titolo" class="form-control" placeholder="Titolo..." required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Numero di Preventivi</label>
                            <input type="text" name="n_preventivi" class="form-control" placeholder="Esempio: 5..." />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">PDF Bando</label>
                            <input class="form-control" type="file" name="allegati" accept="application/pdf">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Decreto</label>
                            <input class="form-control" type="file" name="decreto" accept="application/pdf">
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione<b style="color:red">*</b></label>
                                <textarea {{--id="mytextarea"--}} placeholder="Descrizione..." rows="10" name="descrizione" class="form-control" required ></textarea>
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

<div class="modal modal-xl fade" id="modal_descrizione" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="ajax_loader"></div>

@include('default.common.footer')

<script type="text/javascript">

    function visualizza_clienti(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/visualizza_stato_clienti') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_visualizza_stato_clienti_'+id).modal('show');
            }
        });
    }

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/modifica_bando') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_bando_'+id).modal('show');
            }
        });
    }

    function aggiungi_allegati(id) {
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/aggiungi_allegati') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_aggiungi_allegati_'+id).modal('show');
            }
        });
    }

    function aggiungi_clienti(id) {
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/aggiungi_utenti') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_aggiungi_utenti_'+id).modal('show');
            }
        });
    }

    function show_descrizione(testo){
        $('#modal_descrizione .modal-body').text(testo);
        $('#modal_descrizione').modal('show');
    }

    function modal_invia_mail(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/invia_mail_bando_clienti') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_invia_mail_'+id).modal('show');
            }
        });
    }

</script>