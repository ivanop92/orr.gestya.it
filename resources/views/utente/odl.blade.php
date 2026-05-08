
@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errore!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">


                <div class="card">
                    <div class="card-header">
                        <h2>Modulo Produzione</h2><br>
                                <a class="btn btn-success" onclick="aggiungi()" style="float:right;">Aggiungi ODL</a>
                                <a class="btn btn-primary" onclick="imposta_commessa()" style="float:right;margin-right:20px;">Imposta Commessa</a>

                                <a class="btn btn-primary" onclick="esporta_odl()" style="float:right;margin-right:20px;">Esporta ODL</a>
                        <a class="btn btn-warning" onclick="selezionaOrdine()" style="float:right;margin-right:20px;">Seleziona Ordine</a>
                                <div class="clearfix"></div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="clearfix" style="margin-bottom:10px"></div>

                        <table class="table table-bordered table-hover datatable" border="0" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:50px;">Numero</th>
                                <th style="width:50px;">Data Pianificazione</th>
                                <th style="width:300px;">Articolo</th>
                                <th style="width:200px;">Cliente</th>
                                <th style="width:50px;">Qta</th>
                                <th style="width:50px;">Note</th>
                                <th style="width:50px;">Stato</th>
                                <th style="width:300px;"></th>
                            </tr>
                            </thead>

                            <tbody>

                            <?php foreach($odl as $o){ ?>

                                <?php
                                // Conta tutte le righe-fase dell'ODL e quante non sono completate
                                $totale_fasi = DB::select('SELECT COUNT(*) as tot FROM odl_righe WHERE id_odl = ?', [$o->id])[0]->tot;
                                $fasi_non_completate = DB::select('SELECT * FROM odl_righe WHERE id_odl = ? AND completato != 1', [$o->id]);

                                $stato = 'In Attesa';
                                $background = 'white'; // Default

                                if($totale_fasi == 0) {
                                    // ODL senza fasi: configurazione articolo incompleta
                                    $stato = 'Senza fasi';
                                    $background = 'rgba(231, 76, 60, 0.15)'; // Rosso chiaro per segnalare l'errore
                                } elseif(count($fasi_non_completate) == 0) {
                                    // Tutte le fasi sono completate
                                    $stato = 'Completato';
                                    $background = 'rgba(46, 204, 113,0.1)'; // Verde chiaro
                                } elseif ($o->stato == 1) {
                                    $stato = 'In Lavorazione';
                                    $background = 'rgba(241, 196, 15,0.2)'; // Giallo chiaro
                                } elseif ($o->stato == 0) {
                                    $stato = 'In Attesa';
                                    $background = 'white';
                                }
                                ?>

                            <tr style="background:<?php echo $background ?>">
                                <td><?php echo $o->numero ?><br><small><?php echo $o->commessa ?></small></td>
                                <td style="width:200px;"><?php echo date('d/m/Y    H:i',strtotime($o->data)) ?></td>
                                <td><?php echo $o->codice_articolo ? $o->codice_articolo.' - ' : '' ?><?php echo $o->articolo ?></td>
                                <td><?php echo $o->cliente_ragione_sociale ?? '-' ?></td>
                                <td><?php echo $o->qta ?></td>
                                <td><?php echo $o->note ?></td>
                                <td><?php echo $stato ?></td>
                                <td style="text-align: center;">
                                    <div class="row">

                                            <?php if($o->stato == 0){ ?>

                                        <div class="col-md-12">
                                            <a style="width:100%;" class="btn btn-success" href="<?php echo URL::asset('utente/dettaglio_odl/'.$o->id) ?>">Gestisci OL</a>
                                        </div>


                                        <div class="col-md-12">
                                            <a style="width:100%;" class="btn btn-primary" onclick="modifica(<?php echo $o->id ?>)">Modifica</a>
                                        </div>

                                        <div class="col-md-12">
                                            <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questo ODL ?')">
                                                <input type="hidden" name="id" value="<?php echo $o->id ?>">
                                                <button type="submit" name="elimina" class="btn btn-danger" value="elimina" style="width:100%">Elimina</button>
                                            </form>
                                        </div>

                                        <?php } ?>

                                            <?php if($o->stato == 1){ ?>

                                        <div class="col-md-12">
                                            <a style="width:100%;" class="btn btn-success" href="<?php echo URL::asset('utente/dettaglio_odl/'.$o->id) ?>">Gestsci OL</a>
                                        </div>


                                        <?php } ?>

                                            <?php if($o->stato == 2) { ?>
                                        <b><?php echo date('d/m/Y H:i:s',strtotime($o->data_chiusura)) ?></b>
                                        <div class="col-md-12">
                                            <a style="width:100%;" class="btn btn-success" href="<?php echo URL::asset('utente/dettaglio_odl/'.$o->id) ?>">Dettagli</a>
                                        </div>
                                        <?php } ?>

                                    </div>

                                </td>

                            </tr>

                            <?php } ?>

                            </tbody>
                        </table>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </div>
    <!-- container-fluid -->
</div>


<div id="ajax_loader"></div>


<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi ODL</h4>
                </div>
                <div class="modal-body row">


                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Numero <b style="color:red">*</b></label>
                            <input type="text" class="form-control" name="numero" value="<?php echo $num_odl  ?>" placeholder="Numero" required>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Data Pianificazione<b style="color:red">*</b></label>
                            <input type="text" class="form-control datetime-picker" value="<?php echo date('d/m/Y H:i:00') ?>" name="data" placeholder="Data" required>
                        </div>
                    </div>


                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Articolo <b style="color:red">*</b></label>
                            <select name="id_articolo" class="form-control select2">
                                <?php foreach($articoli as $a){ ?>
                                <option value="<?php echo $a->id ?>"><?php echo $a->titolo ?> (<?php echo $a->um ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Qta <b style="color:red">*</b></label>
                            <input type="number" step="0.01" class="form-control" name="qta" placeholder="Qta" required>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                    <input type="submit" class="btn btn-primary pull-right" name="aggiungi" value="Aggiungi" style="margin-right:5px;">
                </div>
            </div>
        </div>
    </div>
</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_esporta_odl">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Esporta ODL</h4>
                </div>
                <div class="modal-body row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data Inizio <b style="color:red">*</b></label>
                            <input type="text" class="form-control date-picker" name="data_inizio" value="<?php echo date('d/m/Y') ?>"  required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data Fine <b style="color:red">*</b></label>
                            <input type="text" class="form-control date-picker" name="data_fine" value="<?php echo date('d/m/Y') ?>"  required>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                    <input type="submit" class="btn btn-primary pull-right" name="esporta_odl" value="Esporta ODL" style="margin-right:5px;">
                </div>
            </div>
        </div>
    </div>
</form>
<div class="modal fade" id="modal_seleziona_ordine" tabindex="-1" aria-labelledby="modalSelezionaOrdineLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalSelezionaOrdineLabel">Seleziona Ordine</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabella per mostrare gli ordini -->
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID Ordine</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Azioni</th>
                    </tr>
                    </thead>
                    <tbody id="ordineDetails">
                    <!-- Contenuto dinamico -->
                    </tbody>
                </table>

                <!-- Tabella per mostrare gli articoli collegati -->
                <h5 class="mt-4">Articoli Collegati</h5>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID Articolo</th>
                        <th>Nome Prodotto</th>
                        <th>Quantità</th>
                    </tr>
                    </thead>
                    <tbody id="articoliDetails">
                    <!-- Contenuto dinamico -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        @if(session('success'))
            toastr.options = { closeButton: true, timeOut: 6000, positionClass: 'toast-top-right' };
            toastr.success(@json(session('success')));
        @endif
        @if(session('error'))
            toastr.options = { closeButton: true, timeOut: 0, extendedTimeOut: 0, positionClass: 'toast-top-right' };
            toastr.error(@json(session('error')));
        @endif
    });
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




<div id="ajax_loader"></div>


<script type="text/javascript">
    function selezionaOrdine() {
        // Mostra la modale
        $('#modal_seleziona_ordine').modal('show');

        // Effettua la chiamata AJAX per ottenere gli ordini
        fetch('/get-ordini')
            .then(response => response.json())
            .then(data => {
                let ordineDetails = '';
                data.ordini.forEach(ordine => {
                    ordineDetails += `
                        <tr>
                            <td>${ordine.id}</td>
                            <td>${ordine.ragione_sociale || ordine.cd_cf || '-'}</td>
                            <td>${ordine.data_doc}</td>
                            <td><button class="btn btn-primary" onclick="caricaArticoli(${ordine.id})">Mostra Articoli</button></td>
                        </tr>
                    `;
                });
                document.getElementById('ordineDetails').innerHTML = ordineDetails;
            })
            .catch(error => {
                console.error('Errore durante il caricamento degli ordini:', error);
            });
    }

    function caricaArticoli(idOrdine) {
        // Effettua la chiamata AJAX per ottenere gli articoli collegati all'ordine selezionato
        fetch(`/get-articoli?id_ordine=${idOrdine}`)
            .then(response => response.json())
            .then(data => {
                let articoliDetails = '';
                data.articoli.forEach(articolo => {
                    // Controlla lo stato_prod e cambia il pulsante di conseguenza
                    let buttonHtml = '';
                    if (articolo.stato_prod === 1) {
                        buttonHtml = `<button class="btn btn-warning" disabled>In Produzione</button>`;
                    } else if (articolo.stato_prod === 2) {
                        buttonHtml = `<button class="btn btn-danger" disabled>Prodotto</button>`;
                    } else {
                        buttonHtml = `
                        <form method="post" action="/utente/odl">
                            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                            <input type="hidden" name="id_dorig" value="${articolo.id}">
                            <input type="hidden" name="id_dotes" value="${idOrdine}">
                            <input type="hidden" name="id_articolo" value="${articolo.id_articolo}">
                            <input type="hidden" name="qta" value="${articolo.qta}">
                            <input type="hidden" name="data" value="${new Date().toISOString().slice(0, 16).replace('T', ' ')}">
                            <input type="hidden" name="numero" value="<?php echo $num_odl ?>">
                            <input type="hidden" name="aggiungi" value="true">
                            <input type="submit" class="btn btn-success" value="Produci">
                        </form>
                    `;
                    }

                    articoliDetails += `
                    <tr>
                        <td>${articolo.codice_articolo || articolo.id_articolo}</td>
                        <td>${articolo.nome_prodotto || articolo.descrizione || '-'}</td>
                        <td>${articolo.qta_evadibile_prod}</td>
                        <td>${buttonHtml}</td>
                    </tr>
                `;
                });
                document.getElementById('articoliDetails').innerHTML = articoliDetails;
            })
            .catch(error => {
                console.error('Errore durante il caricamento degli articoli:', error);
            });
    }



    function popolaFormAggiungiODL(idArticolo, nomeProdotto, quantita) {
        // Popola i campi della modale "Aggiungi ODL"
        document.querySelector('input[name="numero"]').value = '<?php echo $num_odl ?>'; // Imposta il numero di ODL
        document.querySelector('select[name="id_articolo"]').value = idArticolo; // Seleziona l'articolo corretto
        document.querySelector('input[name="qta"]').value = quantita; // Imposta la quantità
        document.querySelector('input[name="data"]').value = new Date().toISOString().slice(0, 16).replace('T', ' '); // Imposta la data corrente

        // Mostra la modale "Aggiungi ODL"
        $('#modal_aggiungi').modal('show');
    }


    function produciArticolo(idArticolo, nomeProdotto, quantita) {
        // Prepara i dati per la richiesta
        const dati = {
            id_articolo: idArticolo,
            qta: quantita,
            numero: '<?php echo $num_odl ?>',
            data: new Date().toISOString(), // Data attuale formattata
            aggiungi: true
        };

        // Effettua una chiamata AJAX per creare l'ODL
        fetch('/utente/odl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(dati)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ODL creato con successo per l\'articolo ' + nomeProdotto);
                    // Puoi ricaricare la pagina o aggiornare la lista ODL qui
                    location.reload();
                } else {
                    alert('Errore durante la creazione dell\'ODL: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore durante la creazione dell\'ODL:', error);
            });
    }





    window.onload = (event) => {

        $('body').addClass('sidebar-collapse');

    };

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function imposta_commessa(){
        $('#modal_commessa').modal('show');
    }

    function esporta_odl(){
        $('#modal_esporta_odl').modal('show');
    }

    function modifica(id){

        $.get("<?php echo URL::ASSET('ajax/modifica_odl') ?>/"+id, function( data ) {
            $("#ajax_loader" ).html( data );
            $('#modal_modifica_'+id).modal('show');

            $('.datetime-picker').attr('autocomplete','off');
            $('.datetime-picker').datetimepicker({
                format: "dd/mm/yyyy H:i:s",
                language: "it",
                autoclose:true
            });

            $('.select2').select2();


            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });


        });

    }


</script>
