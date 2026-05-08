@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Magazzino <?php echo $magazzino->codice_magazzino ?></h4>

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
                            <h5 class="card-title mb-0">Giacenze Magazzino <?php echo $magazzino->codice_magazzino ?></h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Lotto</th>
                                    <th>Giacenza</th>
                                    {{--<th style="width:100px;">Azioni</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($articoli as $a){ ?>
                                <tr class="articolo-row" data-id="<?php echo $a->id_articolo ?>">
                                    <td><?php echo $a->titolo ?></td>
                                    <td><?php echo ($a->lotto) ?></td>
                                    <td class="giacenza"><?php echo $a->totale_giacenza ?> <?php echo $a->um ?></td>
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
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Articolo</h5>
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
                                            <img src="/placehold_immagine.png" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" class="form-control" placeholder="Titolo" required/>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0">Prodotto Finito</option>
                                <option value="1">Materia Prima</option>
                            </select>
                        </div>
                        <div class="col-lg-4" >
                            <label for="fasi" class="form-label">Fasi<b style="color:red">*</b></label>
                           {{-- <select data-choices data-choices-removeItem multiple name="fasi[]" style="width: 100%;" >
                                @foreach ($fasi as $fase)
                                    <option value="{{ $fase->id }}"
                                            @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                        {{ $fase->descrizione }}
                                    </option>
                                @endforeach
                            </select>--}}

                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo<b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" placeholder="Unità di Misura" required/>
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

<?php foreach($articoli as $a){ ?>


{{--

<div class="modal fade" id="modal_modifica_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Articolo</h5>
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


                        <div class="col-md-12">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" value="<?php echo $a->titolo ?>" class="form-control" placeholder="Titolo" required/>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0" <?php echo ($a->tipologia == 0)?'selected':'' ?>>Prodotto Finito</option>
                                <option value="1" <?php echo ($a->tipologia == 1)?'selected':'' ?>>Materia Prima</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="fasi"  class="form-label">Fasi<b style="color:red">*</b></label>
                            <select data-choices data-choices-removeItem multiple name="fasi[]" id="fasi_{{ $a->id }}"  style="width: 100%;" >
                                @foreach ($fasi as $fase)
                                    <option value="{{ $fase->id }}"
                                            @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                        {{ $fase->descrizione }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"><?php echo $a->descrizione ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo<b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" value="<?php echo $a->prezzo ?>" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" value="<?php echo $a->um ?>" placeholder="Unità di Misura" required/>
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

--}}

{{--
<div class="modal fade" id="modal_db_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Distinta Base</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                            <?php for($i=0;$i<5;$i++){ ?>

                        <div class="row">

                            <div class="col-md-9">
                                    <?php if($i == 0) { ?><label>Materiale <b style="color:red">*</b></label><?php } ?>
                                <select id="db_<?php echo $a->id ?>_<?php echo $i ?>" name="materiale[<?php echo $i ?>]" class="form-control select2" style="width:100%;">
                                    <option value="">Nessun Materiale</option>
                                        <?php foreach($materiali as $m){ ?>
                                    <option value="<?php echo $m->id ?>" costo="<?php echo $m->prezzo ?>" <?php echo (isset($a->distinta_base[$i]) && $a->distinta_base[$i]->id_materiale ==$m->id)?'selected':'' ?>><?php echo $m->titolo ?> (<?php echo $m->um ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                    <?php if($i == 0) { ?><label>Qta<b style="color:red">*</b></label><?php } ?>
                                <input id="qta_db_<?php echo $a->id ?>_<?php echo $i ?>" type="number" min="0" step="0.0001" name="quantita[<?php echo $i ?>]" value="<?php echo (isset($a->distinta_base[$i]))?$a->distinta_base[$i]->qta:'' ?>" class="form-control" onkeyup="calcola_costo_db(<?php echo $a->id ?>,<?php echo $a->prezzo ?>)" onchange="calcola_costo_db(<?php echo $a->id; ?>, <?php echo $a->prezzo ?>)">
                            </div>

                        </div>

                        <?php } ?>


                        <div class="row">

                            <div class="col-md-6" style="text-align:right"><br>
                                <b style="font-size: 15px;">Costo Materie Prime:</b><br><br>
                                <b style="font-size: 15px;">Prezzo di Vendita:</b><br><br>
                                <b style="font-size: 15px;">Percentuale Costo:</b><br>
                            </div>

                            <div class="col-md-3" style="text-align:left"><br>
                                <b style="font-size: 20px;" id="costo_materia_prima_<?php echo $a->id ?>"></b><br>
                                <input name="prezzo"  id="prezzo_<?php echo $a->id ?>" class="form-control" value="<?php echo $a->prezzo ?>" onkeyup="RicalcoloPercentuale(<?php echo $a->id ?>)"><br>
                                <b style="font-size: 20px;" id="incidenza_<?php echo $a->id ?>"></b><br>
                            </div>

                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica_db" value="Salva Distinta Base" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>--}}


<?php } ?>


<div id="ajax_loader"></div>


<style>
    .icona-riordino {
        color: grey;
        margin-left: 50px;
        animation: lampeggio 1.5s infinite alternate;
        font-size: 30px;
    }

    @keyframes lampeggio {
        0% { opacity: 1; }
        100% { opacity: 0; }
    }

</style>
@include('utente.common.footer')

<script type="text/javascript">

    function printBarcode(barcodeData) {
        // URL per il barcode
        const url = `https://barcode.tec-it.com/barcode.ashx?data=${barcodeData}&code=Code128&translate-esc=on`;

        // Apri una nuova finestra con il barcode
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Stampa Barcode</title>
                </head>
                <body>
                    <img src="${url}" alt="Barcode" />
                </body>
            </html>
        `);

        // Attendi che l'immagine venga caricata e poi avvia la stampa
        printWindow.document.close(); // Chiude il documento per il rendering
        printWindow.focus(); // Imposta la finestra come attiva

        // Stampa il contenuto della finestra
        printWindow.onload = function() {
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close(); // Chiudi la finestra dopo la stampa
            };
        };
    }

        $(document).ready(function() {
        $('.punto-riordino').on('change', function() {
            var articoloId = $(this).data('id');
            var puntoRiordino = $(this).val();

            $.ajax({
                url: "<?php echo URL::to('admin/salva_punto_riordino'); ?>",
                type: 'POST',
                data: {
                    id: articoloId,
                    punto_riordino: puntoRiordino,
                    _token: '<?php echo csrf_token(); ?>'
                },
                success: function(response) {
                    console.log('Punto di riordino aggiornato con successo');

                    // Verifica giacenza e applica effetto lampeggiante se necessario
                    var giacenza = parseFloat($(`tr[data-id="${articoloId}"] .giacenza`).text());
                    if (giacenza < puntoRiordino) {
                        $(`tr[data-id="${articoloId}"]`).addClass('lampeggia');
                    } else {
                        $(`tr[data-id="${articoloId}"]`).removeClass('lampeggia');
                    }
                },
                error: function(error) {
                    console.error('Errore durante il salvataggio del punto di riordino:', error);
                }
            });
        });
    });




function scarica(id){
        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_scarico') ?>/"+id,
            type:'GET',
            success: function(result) {
                console.log(result);  // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_scarica_' + id).modal('show');

            }
        });
    }

    function carica(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_carico') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_carica_' + id).modal('show')

            }
        });
    }

    function rettifica(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_rettifica') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_rettifica_' + id).modal('show')

            }
        });
    }

    function movimenti(id) {

        jQuery.ajax({
            url: "<?php echo URL::ASSET('ajax/modifica_articolo_movimenti') ?>/"+id,
            type:'GET',
            success: function(result) { // Aggiungi questo per vedere il contenuto caricato via AJAX
                $('#ajax_loader').html(result);

                $('#modal_mgmov_' + id).modal('show')

            }
        });
    }

    function RicalcoloPercentuale(id_articolo){
        let costo_mat = parseFloat(($('#costo_materia_prima_'+id_articolo)[0].innerHTML).slice(1));
        let prezzo = parseFloat($('#prezzo_'+id_articolo)[0].value);
        $('#incidenza_'+id_articolo)[0].innerHTML = ((( costo_mat/prezzo ) * 100).toFixed(2)) + "%";
    }

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function distinta_base(id,prezzo_vendita){
        $('#modal_db_'+id).modal('show');

        calcola_costo_db(id,prezzo_vendita);
    }

    function calcola_costo_db(id_articolo,prezzo_vendita){
        totale_costo = 0;
        for(i = 0;i<5;i++) {

            if($('#db_' + id_articolo + '_'+i).val() != '') {
                totale_costo = totale_costo + (parseFloat($('#db_' + id_articolo + '_' + i).find(':selected').attr('costo')) * parseFloat($('#qta_db_' + id_articolo + '_' + i).val()));
            }
        }

        $('#costo_materia_prima_'+id_articolo).html('&euro;'+parseFloat(totale_costo).toFixed(4));

        incidenza = parseFloat(((parseFloat(totale_costo) / parseFloat(prezzo_vendita))) * 100).toFixed(2);

        $('#incidenza_'+id_articolo).html('% '+parseFloat(incidenza).toFixed(2));
        $('#totale_costo_'+id_articolo)[0].value = totale_costo;

    }

</script>

<style>

    div.dataTables_wrapper div.dataTables_filter label {
        font-weight: normal;
        white-space: nowrap;
        text-align: left;
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: .5em;
        display: inline-block;
        width: 89%;
    }
</style>