@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Tutte le Commesse Chiuse</h4>

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
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php $cliente = DB::table('utenti')->where('id', $l->id_cliente)->first(); ?>
                                    <h5 id="ragione_sociale_cliente" class="card-title mb-2 ">{{$cliente->ragione_sociale}}</h5>
                                    <button class="btn btn-light btn-sm toggle-button" onclick="toggleCardBody(this)">Mostra Task</button>
                                    <i style="position: absolute; bottom: 0; right: 18px; font-size: 35px; color: green" class="ri-check-double-line"></i>
                                </div>
                                <?php
                                    $scadenza = strtotime($l->scadenza);
                                    $chiusura = strtotime($l->data_chiusura);
                                    $creazione = strtotime($l->created_at);
                                    ?>
                                <p style="font-size: 14px; margin-top: 10px; margin-bottom: 15px" class="m-0"><span style="font-weight: bold">Creata il: </span> {{date('d/m/Y', $creazione)}}</p>
                                <p style="font-size: 14px;" class="m-0"><span style="font-weight: bold">Scadenza il: </span> {{date('d/m/Y', $scadenza)}}</p>
                                <br>
                                <p style="font-size: 14px;" class="m-0"><span style="font-weight: bold">Chiusa il: </span> {{date('d/m/Y', $chiusura)}}</p>
                            </div>
                            <?php $task = DB::table('task')->where('id_lavoro', $l->id)->orderBy('id_dipendente', 'asc')->get(); ?>
                            <div class="card-body" style="display: none;">
                                <?php foreach ($task as $t){ ?>
                                    <?php $dipendente = DB::table('utenti')->where('id', $t->id_dipendente)->first(); ?>
                                    <div style="position: relative; margin-bottom: 7px; border-radius: 10px; padding: 10px; border: 1px solid white; background-color: rgba(39, 174, 96, 0.3); ">
                                        <p style="position: absolute; left: 50%; transform: translateX(-50%); top: 12px; color: #0a0c0d; font-size: 16px; font-weight: bold">Completato</p>
                                        <div class="m-0 mt-5 pt-3" style="border-top: 1px solid white">
                                            <p>
                                                {{$t->descrizione}}
                                            </p>
                                            <div style="display: flex; justify-content: right">
                                                <button class="btn btn-sm btn-primary add-btn me-2" <?php if($t->stato == 0){ ?> onclick="info_task_chiuso(<?php echo $t->id ?>)" <?php }else{ ?> onclick="info_task_sospeso(<?php echo $t->id ?>)" <?php } ?>>
                                                    <i class="ri-information-line"></i>
                                                </button>
                                            </div>
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
                            </div>
                        </div>
                    </div><!--end col-->
                <?php } ?>
            </div><!--end row-->

    </div>
    <!-- container-fluid -->
</div>

<div id="ajax_loader"></div>

@include('default.common.footer')

<script type="text/javascript">

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

    function toggleCardBody(button) {
        var cardBody = button.closest('.card').querySelector('.card-body');
        if (cardBody.style.display === 'none') {
            cardBody.style.display = 'block';
            button.textContent = 'Nascondi Task';
        } else {
            cardBody.style.display = 'none';
            button.textContent = 'Mostra Task';
        }
    }

</script>