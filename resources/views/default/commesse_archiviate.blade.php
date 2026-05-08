@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Tutte le Commesse Archiviate</h4>

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
                                    <form class="mx-2" method="post" onsubmit="return confirm('Vuoi Riaprire questa Commessa ?')">
                                        <input type="hidden" name="id_riapertura_commessa" value="{{$l->id}}">
                                        <input type="submit" class="btn btn-warning" name="riapri_commessa" value="Riapri la Commessa">
                                    </form>
                                </div>
                                <?php
                                    $scadenza = strtotime($l->scadenza);
                                    $creazione = strtotime($l->created_at);
                                    $archivio = strtotime($l->data_archiviazione);
                                    ?>
                                <p style="font-size: 14px; margin-top: 10px; margin-bottom: 15px" class="m-0"><span style="font-weight: bold">Creata il: </span> {{date('d/m/Y', $creazione)}}</p>
                                <p style="font-size: 14px;" class="m-0"><span style="font-weight: bold">Scadenza il: </span> {{date('d/m/Y', $scadenza)}}</p>
                                <br>
                                <p style="font-size: 14px;" class="m-0"><span style="font-weight: bold">Archiviata il: </span> {{date('d/m/Y', $archivio)}}</p>
                            </div>
                            <?php $task = DB::table('task')->where('id_lavoro', $l->id)->orderBy('id_dipendente', 'asc')->get(); ?>
                            <div class="card-body" style="display: none;">

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


</script>