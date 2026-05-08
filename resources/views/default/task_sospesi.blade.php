@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Task Sospesi</h4>

                    <div class="page-title-right">
                        <input type="text" id="search-input" class="form-control" placeholder="Cerca..." oninput="filterCards()">
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


            <div class="row">
                <?php foreach ($lavori as $l) { ?>
                    <div class="col-md-6 lavoro-card">
                        <div class="card" >
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php $cliente = DB::table('utenti')->where('id', $l->id_cliente)->first(); ?>
                                    <h5 id="ragione_sociale_cliente" class="card-title mb-0">{{$cliente->ragione_sociale}}</h5>
                                </div>
                                    <?php
                                    $scadenza = strtotime($l->scadenza);
                                    $creazione = strtotime($l->created_at);
                                    $oggi = strtotime(now());

                                    // Calcolo dei giorni rimanenti
                                    $secondiInUnGiorno = 86400; // 60 * 60 * 24
                                    $giorniRimanenti = ($scadenza - $oggi) / $secondiInUnGiorno;

                                    if ($giorniRimanenti >= 0) {
                                        $giorniRimanenti += 1;
                                    } else {
                                        $giorniRimanenti -= 1;
                                    }

                                    ?>
                                <p style="font-size: 14px; margin-top: 10px; margin-bottom: 15px" class="m-0"><span style="font-weight: bold">Creato il: </span> {{date('d/m/Y', $creazione)}}</p>
                                <p style="font-size: 14px;" class="m-0">
                                    <span style="font-weight: bold">Scadenza il: </span>
                                    {{date('d/m/Y', $scadenza)}}
                                    <span>(
                                        <?php
                                            if ($giorniRimanenti > 0) {
                                                echo 'Scade tra: ' . abs(floor($giorniRimanenti)) . ' giorni';
                                            } elseif ($giorniRimanenti < 0) {
                                                echo 'Scaduto da: '. abs(floor($giorniRimanenti)) . ' giorni';
                                            } else {
                                                echo 'Scade oggi';
                                            }
                                            ?> )
                                    </span>
                                </p>
                            </div>
                            <?php $task = DB::table('task')->where('id_lavoro', $l->id)->where('id_dipendente', $utente->id)->where('stato', 2)->get(); ?>
                            <div class="card-body">
                                <?php if(!$task->isEmpty()){ ?>
                                    <?php foreach ($task as $t){ ?>
                                        <?php $dipendente = DB::table('utenti')->where('id', $t->id_dipendente)->first(); ?>
                                        <div style="background-color: rgba(255, 255, 255, 0.3); position: relative; margin-bottom: 7px; border-radius: 10px; padding: 10px; <?php if($t->id_dipendente == '' || $t->id_dipendente == null){ ?> border: 1px solid yellow; <?php }else{ ?>border: 1px solid white;<?php } ?>">
                                            <p style="position: absolute; left: 50%; transform: translateX(-50%); top: 12px; color: #0a0c0d; font-size: 16px; font-weight: bold">Sospeso</p>
                                            <div class="m-0 mt-5 pt-3" style="border-top: 1px solid white">
                                                <p>
                                                    {{$t->descrizione}}
                                                </p>
                                                <?php if($dipendente && $utente->id == $dipendente->id){ ?>
                                                <div style="display: flex; justify-content: space-between">

                                                    <div style="display: flex; justify-content: left">
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

                                                    <div style="display: flex; justify-content: right">
                                                        <button class="btn btn-sm btn-primary add-btn me-2" onclick="info_task_sospeso(<?php echo $t->id ?>)">
                                                            <i class="ri-information-line"></i>
                                                        </button>
                                                        <form method="post" onsubmit="return confirm('Vuoi Riaprire questo Task ?')">
                                                            <input type="hidden" name="id_task" value="{{$t->id}}">
                                                            <input type="submit" class="btn btn-sm btn-warning" name="riapri_task" value="Riapri">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
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
                                        </div>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <p>
                                        Nessun task di questa commessa è assegnato a te!
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