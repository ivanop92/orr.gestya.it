@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Tutti le Commesse in corso...</h4>

                    <div class="page-title-right">
                        <input type="text" id="search-input" class="form-control" placeholder="Cerca..." oninput="filterCards()">
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

            <div class="row">
                <div class="col-12 d-flex justify-content-end align-items-center mb-4">
                    <button class="btn btn-info add-btn" onclick="aggiungi_lavoro();">
                        <i class="ri-add-fill me-1 align-bottom"></i> Nuovo Lavoro
                    </button>
                </div>
            </div>
            <div class="row">
                <?php foreach ($lavori as $l) { ?>
                    <div class="col-md-6 lavoro-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php $cliente = DB::table('utenti')->where('id', $l->id_cliente)->first(); ?>
                                    <h5 id="ragione_sociale_cliente" class="card-title mb-0">{{$cliente->ragione_sociale}}</h5>
                                    <div class="d-flex">
                                        <button class="btn btn-success add-btn mx-2" onclick="aggiungi_task(<?php echo $l->id ?>);">
                                            <i class="ri-add-fill me-1 align-bottom"></i> Aggiungi Task
                                        </button>
                                        <?php
                                            // Esegui la query per ottenere tutti i task con l'id del lavoro
                                            $totalTasks = DB::table('task')
                                                            ->where('id_lavoro', $l->id)
                                                            ->count();

                                            // Esegui la query per contare i task con stato 0
                                            $tasksWithStato0 = DB::table('task')
                                                                 ->where('id_lavoro', $l->id)
                                                                 ->where('stato', 0)
                                                                 ->count();

                                            ?>
                                        <form method="post" onsubmit="return confirm('Vuoi Chiudere questo lavoro ?')">
                                            <input type="hidden" name="id_cliente" value="{{$l->id_cliente}}">
                                            <input type="hidden" name="id_chiusura" value="{{$l->id}}">
                                            <input <?php if($tasksWithStato0 < $totalTasks){ ?> readonly disabled <?php } ?> type="submit" class="btn btn-danger" name="chiudi_lavoro" value="Termina">
                                        </form>
                                    </div>
                                </div>
                                <?php
                                    $scadenza = strtotime($l->scadenza);
                                    $creazione = strtotime($l->created_at);
                                    ?>
                                <p style="font-size: 14px; margin-top: 10px; margin-bottom: 15px" class="m-0"><span style="font-weight: bold">Creato il: </span> {{date('d/m/Y', $creazione)}}</p>
                                <p style="font-size: 14px;" class="m-0"><span style="font-weight: bold">Scadenza il: </span> {{date('d/m/Y', $scadenza)}}</p>
                            </div>
                            <?php $task = DB::table('task')->where('id_lavoro', $l->id)->orderBy('id_dipendente', 'asc')->get(); ?>
                            <div class="card-body">
                                <?php foreach ($task as $t){ ?>
                                    <?php $dipendente = DB::table('utenti')->where('id', $t->id_dipendente)->first(); ?>
                                    <div style="position: relative; margin-bottom: 7px; border-radius: 10px; padding: 10px; <?php if($t->id_dipendente == '' || $t->id_dipendente == null){ ?> border: 1px solid yellow; <?php }else{ ?>border: 1px solid white;<?php } ?> <?php if($t->stato == 2){ ?> background-color: rgba(255,255,255, 0.5); <?php } ?> <?php if($t->stato == 0){ ?> background-color: rgba(39, 174, 96, 0.3); <?php } ?>">
                                        <?php if($t->stato == 2){ ?>
                                            <p style="position: absolute; left: 50%; transform: translateX(-50%); top: 12px; color: #0a0c0d; font-size: 16px; font-weight: bold">Sospeso</p>
                                        <?php } ?>
                                        <?php if($t->stato == 0){ ?>
                                            <p style="position: absolute; left: 50%; transform: translateX(-50%); top: 12px; color: #0a0c0d; font-size: 16px; font-weight: bold">Completato</p>
                                        <?php } ?>
                                        <?php if($t->stato == 1){ ?>
                                            <style>
                                                @keyframes spin {
                                                    from {
                                                        transform: rotate(0deg);
                                                    }
                                                    to {
                                                        transform: rotate(360deg);
                                                    }
                                                }

                                                .rotating-icon {
                                                    display: inline-block;
                                                    animation: spin 2s linear infinite;
                                                }
                                            </style>
                                            <div style="position: absolute; left: 15px; top: 15px; color: white; font-size: 12px; font-weight: bold; display: flex; align-items: center">
                                                <i style="font-size: 16px" class="rotating-icon ri-loader-2-fill"></i>
                                                <p class="m-0 ms-1">
                                                    In Lavorazione
                                                </p>
                                            </div>
                                        <?php } ?>
                                        <div class="m-0 mt-5 pt-3" style="border-top: 1px solid white">
                                            <p>
                                                {{$t->descrizione}}
                                            </p>
                                            <?php if($dipendente && $utente->id == $dipendente->id){ ?>
                                                <?php if($t->stato == 1){ ?>
                                                <div style="display: flex; justify-content: space-between">
                                                    <div style="display: flex; justify-content: left">
                                                        <div style="display: flex; justify-content: right">
                                                            <div class="mx-2" style="display: flex; justify-content: right">
                                                                <form method="post" onsubmit="return confirm('Vuoi Eliminare il task:  <?php echo $t->descrizione ?> ?')">
                                                                    <input type="hidden" name="elimina_task" value="{{$t->id}}">
                                                                    <input type="hidden" name="id" value="{{$t->id}}">
                                                                    <button class="btn btn-sm btn-danger add-btn" type="submit">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                            <button class="btn btn-sm btn-primary add-btn" onclick="modifica_task(<?php echo $t->id ?>);">
                                                                <i class="ri-edit-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; justify-content: right">
                                                        <div style="display: flex; justify-content: right">
                                                            <button class="btn btn-sm btn-primary add-btn" onclick="assegna_a(<?php echo $t->id ?>);">
                                                                Cambia Assegnatario
                                                            </button>
                                                        </div>
                                                        <div class="mx-2" style="display: flex; justify-content: right">
                                                            <button class="btn btn-sm btn-warning add-btn" onclick="sospendi(<?php echo $t->id ?>);">
                                                                Sospendi
                                                            </button>
                                                        </div>
                                                        <div style="display: flex; justify-content: right">
                                                            <button class="btn btn-sm btn-danger add-btn" onclick="chiudi(<?php echo $t->id ?>);">
                                                                Chiudi
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php }else{ ?>
                                                    <div style="display: flex; justify-content: right">
                                                        <button class="btn btn-sm btn-primary add-btn me-2" <?php if($t->stato == 0){ ?> onclick="info_task_chiuso(<?php echo $t->id ?>)" <?php }else{ ?> onclick="info_task_sospeso(<?php echo $t->id ?>)" <?php } ?>>
                                                            <i class="ri-information-line"></i>
                                                        </button>
                                                        <form method="post" onsubmit="return confirm('Vuoi Riaprire questo Task ?')">
                                                            <input type="hidden" name="id_task" value="{{$t->id}}">
                                                            <input type="submit" class="btn btn-sm btn-warning" name="riapri_task" value="Riapri">
                                                        </form>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <?php if($dipendente){ ?>
                                        <div class="position-absolute d-flex align-items-center" style="top: 15px; right: 15px;">
                                            <div class="position-relative d-inline-block" style="width: 30px; height: 30px">
                                                <div class="avatar-lg p-1" style="width: 30px; height: 30px">
                                                    <div class="avatar-title bg-light rounded-circle" >
                                                        <img style="width: 100%; height: 100%" src="{{asset($dipendente->immagine)}}" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="m-0">{{$dipendente->nome}}</p>
                                        </div>
                                        <?php }else{ ?>
                                        <div class="position-absolute d-flex align-items-center" style="top: 20px; right: 15px; color: yellow; font-weight: bold">
                                            <p class="m-0" style="
                                            animation: pulse 1s infinite;
                                            @keyframes pulse {
                                                0% { opacity: 1; }
                                                50% { opacity: 0.5; }
                                                100% { opacity: 1; }
                                            }">Non Assegnato</p>
                                        </div>
                                        <div class="position-absolute d-flex align-items-center" style="top: 15px; right: 120px; color: yellow; font-weight: bold">
                                            <button class="btn btn-sm btn-warning add-btn mx-2" onclick="assegna_a(<?php echo $t->id ?>);">
                                                Assegna
                                            </button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div><!--end col-->
                <?php } ?>
            </div><!--end row-->

    </div>
    <!-- container-fluid -->
</div>


<div class="modal modal-xl fade" id="modal_aggiungi_lavoro" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Nuovo Lavoro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Cliente<b style="color:red">*</b></label>
                            <select name="id_cliente" class="form-control select2" required >
                                <option selected disabled>-- Seleziona un cliente --</option>
                                <?php foreach ($clienti as $c) { ?>
                                    <option value="{{$c->id}}">{{$c->ragione_sociale}}</option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione<b style="color:red">*</b></label>
                                <textarea placeholder="Descrizione..." rows="6" name="descrizione" class="form-control" required ></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Scadenza<b style="color:red">*</b></label>
                            <input type="date" name="scadenza" class="form-control" required/>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_lavoro" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="ajax_loader"></div>

@include('default.common.footer')

<script type="text/javascript">

    function aggiungi_lavoro(){
        $('#modal_aggiungi_lavoro').modal('show');
    }

    function aggiungi_task(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/lavoro/aggiungi_task') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_aggiungi_task_'+id).modal('show');
            }
        });
    }

    function modifica_task(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/modifica_task') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_task_'+id).modal('show');
            }
        });
    }


    function assegna_a(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/task/assegna') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_assegna_a_'+id).modal('show');
            }
        });

    }

    function chiudi(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/chiudi_task') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_chiudi_task_'+id).modal('show');
            }
        });
    }

    function sospendi(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/sospendi_task') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_sospendi_task_'+id).modal('show');
            }
        });
    }

    function info_task_chiuso(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/info_task_chiuso') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_info_task_chiuso_'+id).modal('show');
            }
        });
    }

    function info_task_sospeso(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/info_task_sospeso') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_info_task_sospeso_'+id).modal('show');
            }
        });
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

    function filterCards() {
        // Ottieni il valore di ricerca dall'input
        var searchValue = document.getElementById('search-input').value.toLowerCase();

        // Ottieni tutte le card
        var cards = document.querySelectorAll('.lavoro-card');

        // Itera su ogni card e controlla se il testo corrisponde alla ricerca
        cards.forEach(function(card) {
            var clienteName = card.querySelector('#ragione_sociale_cliente').innerText.toLowerCase();

            // Controlla se il nome cliente contiene il valore di ricerca
            if (clienteName.indexOf(searchValue) > -1) {
                card.style.display = ''; // Mostra la card se corrisponde
            } else {
                card.style.display = 'none'; // Nascondi la card se non corrisponde
            }
        });
    }

</script>