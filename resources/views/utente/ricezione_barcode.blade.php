@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Ricezione Merci con Barcode</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="alert alert-info mb-4 text-center">
                        <i class="ri-information-line me-2"></i>
                        Puoi scansionare qualsiasi codice a barre GS1-128 del tuo fornitore, se il codice articolo(GTIN) è già presente tra i tuoi articoli verrà associato automaticamente.
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="id_fornitore" class="form-label">Fornitore</label>
                                <select id="id_fornitore" class="form-select select2">
                                    <option value="">Seleziona fornitore</option>
                                    @foreach($fornitori as $fornitore)
                                        <option value="{{ $fornitore->id }}">{{ $fornitore->ragione_sociale }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_magazzino" class="form-label">Magazzino di destinazione</label>
                                <select id="id_magazzino" class="form-select">
                                    @foreach($magazzini as $magazzino)
                                        <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-8 mx-auto">
                                <div class="input-group mb-3">
                                    <input type="text" id="barcode_input" class="form-control form-control-lg text-center" placeholder="Scansiona codice a barre" autofocus>
                                    <button class="btn btn-primary" type="button" id="btn_cerca_barcode">
                                        <i class="ri-barcode-line me-1"></i> Cerca
                                    </button>
                                </div>
                                <div class="form-text text-center">
                                    Scansiona i codici a barre delle merci ricevute per registrarle rapidamente a magazzino
                                </div>
                            </div>
                        </div>

                        <!-- Sezione risultati (inizialmente nascosta) -->
                        <div id="result_section" class="mt-4" style="display: none;">
                            <div class="alert alert-info" id="barcode_info">
                                <!-- Informazioni barcode qui -->
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0" id="articolo_title">Articolo trovato</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" id="articolo_id">
                                            <input type="hidden" id="barcode_originale">
                                            <div class="mb-3">
                                                <label class="form-label">Codice articolo</label>
                                                <input type="text" id="codice_articolo" class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descrizione</label>
                                                <input type="text" id="descrizione_articolo" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Lotto</label>
                                                <input type="text" id="lotto" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Data Scadenza</label>
                                                <input type="date" id="data_scadenza" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Quantità</label>
                                                <input type="number" id="quantita" class="form-control" min="0.01" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Unità di misura</label>
                                                <input type="text" id="um" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Prezzo acquisto</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">€</span>
                                                    <input type="number" id="prezzo_unitario" class="form-control" min="0" step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-light" id="btn_annulla">Annulla</button>
                                    <button class="btn btn-success" id="btn_carica">
                                        <i class="ri-arrow-right-up-line me-1"></i> Carica a magazzino
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sezione articolo non trovato (inizialmente nascosta) -->
                        <div id="not_found_section" class="mt-4" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="ri-error-warning-line me-2"></i>
                                <span id="not_found_message">Articolo non trovato!</span>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Associa barcode fornitore</h5>
                                </div>
                                <div class="card-body">
                                    <form id="form_mapping" method="post" action="{{ url('utente/ricezione_barcode') }}">
                                        @csrf
                                        <input type="hidden" name="salva_mapping" value="1">
                                        <input type="hidden" id="barcode_fornitore" name="barcode_fornitore">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Barcode letto</label>
                                                    <input type="text" id="barcode_display" class="form-control" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Fornitore</label>
                                                    <select id="mapping_fornitore" name="id_fornitore" class="form-select" required>
                                                        <option value="">Seleziona fornitore</option>
                                                        @foreach($fornitori as $fornitore)
                                                            <option value="{{ $fornitore->id }}">{{ $fornitore->ragione_sociale }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Articolo interno</label>
                                                    <select id="mapping_articolo" name="id_articolo" class="form-select select2" required>
                                                        <option value="">Cerca articolo...</option>
                                                        @foreach($articoli ?? [] as $articolo)
                                                            <option value="{{ $articolo->id }}">{{ $articolo->codice_articolo }} - {{ $articolo->titolo }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Note</label>
                                                    <input type="text" id="mapping_note" name="note" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-light" id="btn_annulla_mapping">Annulla</button>
                                    <button class="btn btn-primary" id="btn_salva_mapping">
                                        <i class="ri-link me-1"></i> Salva associazione
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riepilogo ultimi carichi -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ultimi 10 carichi effettuati</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Articolo</th>
                                    <th>Lotto</th>
                                    <th>Quantità</th>
                                    <th>Magazzino</th>
                                    <th>Barcode</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ultimi_carichi as $carico)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($carico->datamov)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $carico->titolo ?? 'N/D' }}</td>
                                        <td>{{ $carico->lotto ?? 'N/D' }}</td>
                                        <td>{{ $carico->qta }}</td>
                                        <td>{{ $carico->magazzino_descrizione ?? 'N/D' }}</td>
                                        <td>{{ $carico->barcode ?? 'N/D' }}</td>
                                    </tr>
                                @endforeach
                                @if(count($ultimi_carichi) == 0)
                                    <tr>
                                        <td colspan="6" class="text-center">Nessun carico recente</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale di conferma carico -->
<div class="modal fade" id="modalConfermaCarico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Carico completato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="ri-checkbox-circle-line text-success" style="font-size: 3rem;"></i>
                </div>
                <h4>Carico effettuato con successo</h4>
                <p id="conferma_dettagli" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continua</button>
                <button type="button" class="btn btn-success" id="btn_stampa_etichetta">
                    <i class="ri-printer-line me-1"></i> Stampa etichetta
                </button>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<!-- Script necessari per la pagina -->
<script src="https://unpkg.com/onscan.js@1.5.2/onscan.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Inizializzazione select2
        if ($.fn.select2) {
            $('#id_fornitore, #mapping_articolo').select2({
                placeholder: 'Cerca...',
                width: '100%'
            });
        }

        // Inizializzazione scanner barcode
        if (typeof onScan !== 'undefined') {
            onScan.attachTo(document, {
                onScan: function(barcode) {
                    console.log('Barcode scansionato:', barcode);
                    $('#barcode_input').val(barcode);
                    processBarcode(barcode);
                },
                onScanError: function(e) {
                    console.error('Errore di scansione:', e);
                }
            });
        }

        // Handler per enter sul campo barcode
        $('#barcode_input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const barcode = $(this).val().trim();
                if (barcode) {
                    processBarcode(barcode);
                }
            }
        });

        // Handler per il pulsante cerca
        $('#btn_cerca_barcode').on('click', function() {
            const barcode = $('#barcode_input').val().trim();
            if (barcode) {
                processBarcode(barcode);
            }
        });

        // Handler annulla operazione
        $('#btn_annulla, #btn_annulla_mapping').on('click', function() {
            resetForm();
        });

        // Handler salva associazione barcode
        $('#btn_salva_mapping').on('click', function() {
            const idFornitore = $('#mapping_fornitore').val();
            const idArticolo = $('#mapping_articolo').val();

            if (!idFornitore || !idArticolo) {
                alert('Seleziona fornitore e articolo per salvare l\'associazione');
                return;
            }

            $('#form_mapping').submit();
        });

        // Handler carica a magazzino
        $('#btn_carica').on('click', function() {
            const idArticolo = $('#articolo_id').val();
            const idMagazzino = $('#id_magazzino').val();
            const quantita = $('#quantita').val();
            const lotto = $('#lotto').val();
            const dataScadenza = $('#data_scadenza').val();
            const prezzoUnitario = $('#prezzo_unitario').val();
            const barcodeOriginale = $('#barcode_originale').val();

            if (!idArticolo || !idMagazzino || !quantita || quantita <= 0 || !lotto) {
                alert('Compila tutti i campi obbligatori: quantità, lotto');
                return;
            }

            // Mostra un indicatore di caricamento
            Swal.fire({
                title: 'Caricamento in corso...',
                text: 'Stiamo elaborando la tua richiesta',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Effettua carico via AJAX
            $.ajax({
                url: '/utente/carico_barcode',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id_articolo: idArticolo,
                    quantita: quantita,
                    id_magazzino: idMagazzino,
                    lotto: lotto,
                    data_scadenza: dataScadenza,
                    prezzo_unitario: prezzoUnitario,
                    id_fornitore: $('#id_fornitore').val(),
                    barcode_originale: barcodeOriginale,
                    causale: 'Carico da barcode fornitore'
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        // Mostra modale di conferma
                        $('#conferma_dettagli').html(
                            `Caricati <strong>${quantita}</strong> ${$('#um').val()} di <strong>${$('#descrizione_articolo').val()}</strong><br>` +
                            `Lotto: <strong>${lotto}</strong> | Magazzino: <strong>${$('#id_magazzino option:selected').text()}</strong>`
                        );

                        // Salva i dati per la stampa etichetta
                        $('#btn_stampa_etichetta').data('id', idArticolo);
                        $('#btn_stampa_etichetta').data('barcode', barcodeOriginale);
                        $('#btn_stampa_etichetta').data('lotto', lotto);

                        const modal = new bootstrap.Modal(document.getElementById('modalConfermaCarico'));
                        modal.show();

                        // Reset del form e ricarica la pagina per aggiornare la lista dei carichi recenti
                        resetForm();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: 'Errore',
                            text: response.message || 'Si è verificato un errore durante il carico',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    let errorMsg = 'Si è verificato un errore durante il carico';
                    try {
                        const resp = JSON.parse(xhr.responseText);
                        if (resp && resp.message) {
                            errorMsg = resp.message;
                        }
                    } catch (e) {}

                    Swal.fire({
                        title: 'Errore',
                        text: errorMsg,
                        icon: 'error'
                    });
                }
            });
        });

        // Handler stampa etichetta
        $('#btn_stampa_etichetta').on('click', function() {
            const idArticolo = $(this).data('id');
            const barcode = $(this).data('barcode');
            const lotto = $(this).data('lotto');

            if (!idArticolo || !barcode) {
                alert('Dati per l\'etichetta non disponibili');
                return;
            }

            // Crea form per il submit della richiesta di stampa
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/utente/stampa_etichetta_barcode';
            form.style.display = 'none';

            // Aggiungi i campi nascosti
            const fields = {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'articolo_id': idArticolo,
                'barcode': barcode,
                'lotto': lotto,
                'num_copie': 1
            };

            for (const key in fields) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();

            setTimeout(() => {
                document.body.removeChild(form);
            }, 100);
        });
    });

    // Funzione per processare il barcode
    function processBarcode(barcode) {
        // Ottiene il fornitore selezionato
        const idFornitore = $('#id_fornitore').val();

        // Effettua la richiesta AJAX per decodificare il barcode
        $.ajax({
            url: '/utente/decode_barcode',
            type: 'GET',
            data: {
                barcode: barcode,
                id_fornitore: idFornitore
            },
            success: function(response) {
                if (response.success) {
                    // Articolo trovato, mostra i dettagli
                    $('#articolo_id').val(response.articolo.id);
                    $('#barcode_originale').val(response.barcode_originale);
                    $('#codice_articolo').val(response.articolo.codice_articolo);
                    $('#descrizione_articolo').val(response.articolo.titolo);
                    $('#um').val(response.articolo.um);
                    $('#prezzo_unitario').val(response.articolo.prezzo || '');

                    // Imposta lotto, data scadenza e quantità se presenti nei dati del barcode
                    if (response.lotto) {
                        $('#lotto').val(response.lotto);
                    }

                    if (response.data_scadenza) {
                        $('#data_scadenza').val(response.data_scadenza);
                    }

                    if (response.quantita) {
                        $('#quantita').val(response.quantita);
                    } else {
                        $('#quantita').val(1);
                    }

                    // Costruisci l'info barcode
                    let barcodeInfo = 'Barcode letto: <strong>' + barcode + '</strong>';
                    if (response.barcode_data) {
                        barcodeInfo += '<br>Dati decodificati: ';
                        for (const [key, value] of Object.entries(response.barcode_data)) {
                            barcodeInfo += `<span class="badge bg-info me-1">(${key}) ${value}</span>`;
                        }
                    }
                    $('#barcode_info').html(barcodeInfo);

                    // Mostra la sezione risultati
                    $('#result_section').show();
                    $('#not_found_section').hide();
                } else {
                    // Articolo non trovato
                    $('#result_section').hide();

                    if (response.show_mapping) {
                        // Proponi mappatura barcode -> articolo
                        $('#barcode_fornitore').val(barcode);
                        $('#barcode_display').val(barcode);

                        // Preseleziona il fornitore se è stato scelto
                        if (idFornitore) {
                            $('#mapping_fornitore').val(idFornitore).trigger('change');
                        }

                        // Mostra info barcode se disponibili
                        let notFoundMsg = 'Articolo non trovato per il barcode: <strong>' + barcode + '</strong>';
                        if (response.barcode_data) {
                            notFoundMsg += '<br>Dati decodificati: ';
                            for (const [key, value] of Object.entries(response.barcode_data)) {
                                notFoundMsg += `<span class="badge bg-info me-1">(${key}) ${value}</span>`;
                            }
                        }
                        $('#not_found_message').html(notFoundMsg);

                        // Mostra sezione per il mapping
                        $('#not_found_section').show();
                    } else {
                        // Errore generico
                        alert('Errore: ' + (response.message || 'Barcode non riconosciuto'));
                        resetForm();
                    }
                }
            },
            error: function() {
                alert('Si è verificato un errore durante l\'elaborazione del barcode');
                resetForm();
            }
        });
    }

    // Funzione per resettare il form
    function resetForm() {
        $('#barcode_input').val('').focus();
        $('#result_section').hide();
        $('#not_found_section').hide();
    }
</script>