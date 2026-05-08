@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">I miei Task</h4>

                    <div class="page-title-right">
                        <input type="text" id="search-input" class="form-control" placeholder="Cerca..." oninput="filterCards()">
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <style>
            @keyframes background-blink {
                0% {
                    background-color: transparent;
                }
                50% {
                    background-color: red;
                }
                100% {
                    background-color: transparent;
                }
            }

            .blinking-background {
                animation: background-blink 1s infinite;
            }
        </style>

            <div class="row">
                <?php foreach ($lavori as $l) { ?>
                    <?php
                    $scadenza = strtotime($l->scadenza);
                    $creazione = strtotime($l->created_at);
                    $oggi = time();

                    // Calcolo dei giorni rimanenti
                    $secondiInUnGiorno = 86400; // 60 * 60 * 24
                    $giorniRimanenti = ($scadenza - $oggi) / $secondiInUnGiorno;

                    if ($scadenza == $oggi) {
                        $giorniRimanenti = 0;
                    }

                    $giorniRimanenti += 1;

                    ?>
                @dump($giorniRimanenti)
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
                                    </div>
                                </div>
                                <p style="font-size: 14px; margin-top: 10px; margin-bottom: 15px" class="m-0"><span style="font-weight: bold">Creato il: </span> {{date('d/m/Y', $creazione)}}</p>
                                <p style="font-size: 14px;" class="m-0">
                                    <span style="font-weight: bold">Scadenza il: </span>
                                    {{date('d/m/Y', $scadenza)}}
                                    <span>
                                        <?php
                                            if (floor($giorniRimanenti) > 0) {
                                                echo '(Scade tra: ' . abs(floor($giorniRimanenti)) . ' giorni)';
                                            } elseif (floor($giorniRimanenti) < 0) {
                                                echo '(Scaduto da: '. abs(floor($giorniRimanenti)) . ' giorni)';
                                            } else {
                                                echo '(Scade oggi) <span class="blinking-background m-0 ms-1" style="display: inline-block; padding: 7px; width: 7px!important; height: 7px!important; border-radius: 50%;"></span>';
                                            }
                                            ?>
                                    </span>
                                </p>
                            </div>
                            <?php $task = DB::table('task')->where('id_lavoro', $l->id)->where('id_dipendente', $utente->id)->where('stato', 1)->get(); ?>
                            <div class="card-body">
                                <?php if(!$task->isEmpty()){ ?>
                                    <?php foreach ($task as $t){ ?>
                                        <?php $dipendente = DB::table('utenti')->where('id', $t->id_dipendente)->first(); ?>
                                        <div style="position: relative; margin-bottom: 7px; border-radius: 10px; padding: 10px; <?php if($t->id_dipendente == '' || $t->id_dipendente == null){ ?> border: 1px solid yellow; <?php }else{ ?>border: 1px solid white;<?php } ?>">
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
                                            <div class="m-0 mt-5 pt-3" style="border-top: 1px solid white">
                                                <p>
                                                    {{$t->descrizione}}
                                                </p>
                                                <?php if($dipendente && $utente->id == $dipendente->id){ ?>
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
                                <?php }else{ ?>
                                    <p>
                                        Nessun task di questo lavoro è assegnato a te!
                                    </p>
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