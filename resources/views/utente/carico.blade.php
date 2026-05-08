@include('utente.common.header')

<!-- Aggiungi riferimenti a DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Carico a Magazzino</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    @php $usa_barcode = !empty($azienda->usa_barcode); @endphp
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            @if($usa_barcode)
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-barcode" role="tab">
                                    <i class="ri-barcode-fill me-1 align-middle"></i> Scansione Barcode
                                </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link {{ $usa_barcode ? '' : 'active' }}" data-bs-toggle="tab" href="#tab-materie-prime" role="tab">
                                    <i class="ri-list-check me-1 align-middle"></i> Materie Prime
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @if($usa_barcode)
                            <!-- Tab Scansione Barcode -->
                            <div class="tab-pane active" id="tab-barcode" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        <!-- Input per la scansione barcode -->
                                        <div class="input-group mb-4">
                                            <input type="text" id="barcodeInput" class="form-control form-control-lg text-center" placeholder="Scansiona un barcode">
                                            <button class="btn btn-primary" type="button" onclick="checkArticleManual()">Cerca</button>
                                        </div>

                                        <!-- Tabella per mostrare l'articolo trovato -->
                                        <table class="table table-bordered table-striped" id="articleTable" style="display: none;">
                                            <thead class="table-dark">
                                            <tr>
                                                <th>Codice</th>
                                                <th>Descrizione</th>
                                                <th>UM</th>
                                                <th>Giacenza Attuale</th>
                                                <th>Azioni</th>
                                            </tr>
                                            </thead>
                                            <tbody id="articleDetails">
                                            <!-- Righe di dettaglio articolo vengono aggiunte dinamicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Tab Materie Prime -->
                            <div class="tab-pane {{ $usa_barcode ? '' : 'active' }}" id="tab-materie-prime" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <input type="text" id="searchMateriePrime" class="form-control" placeholder="Cerca materie prime...">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="tableMateriePrime">
                                        <thead>
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>UM</th>
                                            <th>Giacenza</th>
                                            <th>Azioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($materiali as $materiale)
                                            <tr>
                                                <td>{{ $materiale->codice_articolo }}</td>
                                                <td>{{ $materiale->titolo }}</td>
                                                <td>{{ $materiale->um }}</td>
                                                <td>{{ $materiale->giacenza ?? 0 }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="openCaricoModal({{ $materiale->id }}, '{{ $materiale->titolo }}', '{{ $materiale->um }}')">
                                                        <i class="ri-add-line"></i> Carica
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale per il carico a magazzino -->
<!-- Modale per il carico a magazzino con nuovi campi -->
<!-- Modale per il carico a magazzino con campo allegato -->
<div class="modal fade" id="caricoModal" tabindex="-1" aria-labelledby="caricoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-primary">
                <h5 class="modal-title" id="caricoModalLabel">Carico a Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="articolo_selezionato" class="alert alert-info mb-3"></div>
                <form id="caricoForm" enctype="multipart/form-data">
                    <input type="hidden" id="articolo_id">

                    <ul class="nav nav-tabs mb-3" id="carTabContent" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Informazioni Base</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="economici-tab" data-bs-toggle="tab" data-bs-target="#economici" type="button" role="tab" aria-controls="economici" aria-selected="false">Dati Economici</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documenti-tab" data-bs-toggle="tab" data-bs-target="#documenti" type="button" role="tab" aria-controls="documenti" aria-selected="false">Documenti</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="carTabContent">
                        <!-- Tab Informazioni Base -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="addQuantity" class="form-label">Quantità da Caricare<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" id="addQuantity" name="giacenza" class="form-control" required>
                                        <span class="input-group-text" id="um_display">PZ</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Magazzino<span class="text-danger">*</span></label>
                                    <select name="mg" id="mg" class="form-select" required>
                                        <option value="">Seleziona Magazzino</option>
                                        @foreach ($magazzini as $magazzino)
                                            <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="commessa" class="form-label">Commessa</label>
                                    <select id="commessa" name="id_commessa" class="form-select">
                                        <option value="">Nessuna Commessa</option>
                                        @foreach ($commesse as $commessa)
                                            <option value="{{ $commessa->id }}">{{ $commessa->codice_commessa }} - {{ $commessa->descrizione }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($usa_lotti)
                                <div class="col-md-6 mb-3">
                                    <label for="lotto" class="form-label">Lotto<span class="text-danger">*</span></label>
                                    <input type="text" id="lotto" name="lotto" class="form-control" required>
                                </div>
                                @else
                                <input type="hidden" id="lotto" name="lotto" value="">
                                @endif
                            </div>

                            <div class="row">
                                @if($usa_lotti)
                                <div class="col-md-6 mb-3">
                                    <label for="addScadenza" class="form-label">Scadenza</label>
                                    <input type="date" id="addScadenza" name="addScadenza" class="form-control">
                                </div>
                                @else
                                <input type="hidden" id="addScadenza" name="addScadenza" value="">
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label for="causale" class="form-label">Causale</label>
                                    <input type="text" id="causale" name="causale" class="form-control" value="Carico manuale" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Dati Economici -->
                        <div class="tab-pane fade" id="economici" role="tabpanel" aria-labelledby="economici-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_fornitore" class="form-label">Fornitore<span class="text-danger">*</span></label>
                                    <select id="id_fornitore" name="id_fornitore" class="form-select select2" required>
                                        <option value="">Seleziona Fornitore</option>
                                        @foreach ($fornitori as $fornitore)
                                            <option value="{{ $fornitore->id }}">{{ $fornitore->ragione_sociale }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="aliquota_iva" class="form-label">Aliquota IVA<span class="text-danger">*</span></label>
                                    <select id="aliquota_iva" name="aliquota_iva" class="form-select" required>
                                        <option value="22">22%</option>
                                        <option value="10">10%</option>
                                        <option value="5">5%</option>
                                        <option value="4">4%</option>
                                        <option value="0">Esente IVA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="prezzo_unitario" class="form-label">Prezzo Unitario<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-euro-fill"></i></span>
                                        <input type="number" step="0.01" min="0" id="prezzo_unitario" name="prezzo_unitario" class="form-control" required onchange="calcolaImponibile()" onkeyup="calcolaImponibile()">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="imponibile" class="form-label">Imponibile (calcolato)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-euro-fill"></i></span>
                                        <input type="number" step="0.01" id="imponibile" name="imponibile" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="valuta" class="form-label">Valuta</label>
                                    <select id="valuta" name="valuta" class="form-select">
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="USD">USD - Dollaro USA</option>
                                        <option value="GBP">GBP - Sterlina Britannica</option>
                                        <option value="JPY">JPY - Yen Giapponese</option>
                                        <option value="CHF">CHF - Franco Svizzero</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Documenti -->
                        <div class="tab-pane fade" id="documenti" role="tabpanel" aria-labelledby="documenti-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="numero_ddt" class="form-label">N° DDT ricevuto</label>
                                    <input type="text" id="numero_ddt" name="numero_ddt" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="data_ddt" class="form-label">Data DDT</label>
                                    <input type="date" id="data_ddt" name="data_ddt" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="allegato_ddt" class="form-label">Allegato DDT</label>
                                    <input type="file" id="allegato_ddt" name="allegato_ddt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Carica una scansione o una foto del DDT (formati accettati: PDF, JPG, PNG)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitCarico()">Conferma Carico</button>
            </div>
        </div>
    </div>
</div>

<!-- Modale per la stampa dell'etichetta -->
<div class="modal fade" id="etichettaModal" tabindex="-1" aria-labelledby="etichettaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-success">
                <h5 class="modal-title" id="etichettaModalLabel">Etichetta Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="alert alert-success mb-3">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    Carico a magazzino completato con successo!
                </div>

                <div id="etichettaContainer" class="border p-3 mb-3 mx-auto" style="width: 300px; max-width: 100%;">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-2 fw-bold" id="etichettaTitolo"></div>
                        <div class="small mb-2">
                            <span>Codice: <span id="etichettaCodice"></span></span>
                            @if($usa_lotti)
                            <span class="ms-2">Lotto: <span id="etichettaLotto"></span></span>
                            @endif
                        </div>
                        <div id="barcodeImage" class="my-2"></div>
                        <div class="small" id="etichettaBarcode"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6 offset-3">
                        <label for="numCopie" class="form-label">Numero di copie</label>
                        <input type="number" id="numCopie" class="form-control text-center" value="1" min="1" max="100">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" onclick="downloadEtichetta()">
                    <i class="ri-download-line me-1"></i> Scarica
                </button>
                <button type="button" class="btn btn-success" onclick="printEtichetta()">
                    <i class="ri-printer-line me-1"></i> Stampa
                </button>
            </div>
        </div>
    </div>
</div>
@include('utente.common.footer')

<!-- DataTables JS -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- OnScan.js per la scansione barcode -->
<script src="https://unpkg.com/onscan.js/onscan.min.js"></script>

<script>

    // Aggiungi queste funzioni alla sezione script del file carico.blade.php

    // Funzione per mostrare il modale con l'etichetta barcode
    function showEtichetta(data) {
        // Imposta i dati dell'etichetta
        $('#etichettaTitolo').text(data.titolo);
        $('#etichettaCodice').text(data.codice);
        $('#etichettaLotto').text(data.lotto);
        $('#etichettaBarcode').text(data.barcode);

        // Genera l'immagine del barcode
        generateBarcodeImage(data.barcode);

        // Mostra il modale
        let etichettaModal = new bootstrap.Modal(document.getElementById('etichettaModal'));
        etichettaModal.show();
    }

    // Funzione per generare l'immagine del barcode
    function generateBarcodeImage(barcode) {
        // Utilizziamo l'API di barcodeapi.org per generare l'immagine del barcode
        const barcodeUrl = `https://barcodeapi.org/api/code128/${encodeURIComponent(barcode)}`;

        // Imposta l'immagine
        $('#barcodeImage').html(`<img src="${barcodeUrl}" alt="Barcode ${barcode}" class="img-fluid">`);
    }



    // Funzione per stampare l'etichetta
    function printEtichetta() {
        const numCopie = parseInt($('#numCopie').val()) || 1;
        const etichettaContent = document.getElementById('etichettaContainer').innerHTML;

        // Crea un iframe nascosto per la stampa
        const printFrame = document.createElement('iframe');
        printFrame.style.position = 'absolute';
        printFrame.style.top = '-10000px';
        document.body.appendChild(printFrame);

        // Costruisci il contenuto HTML con il numero di copie richiesto
        let contentToPrint = `
        <html>
        <head>
            <title>Etichetta Barcode</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
                .etichetta {
                    width: 100mm;
                    padding: 5mm;
                    margin-bottom: 5mm;
                    page-break-inside: avoid;
                    border: 1px dashed #ccc;
                    box-sizing: border-box;
                }
                img { max-width: 90mm; height: auto; }
                @media print {
                    .etichetta { border: none; }
                    @page {
                        size: 100mm 60mm;
                        margin: 0;
                    }
                }
            </style>
        </head>
        <body>`;

        // Aggiungi il numero di copie richieste
        for (let i = 0; i < numCopie; i++) {
            contentToPrint += `<div class="etichetta">${etichettaContent}</div>`;
        }

        contentToPrint += `</body></html>`;

        // Scrivi il contenuto nell'iframe e stampa
        printFrame.contentDocument.write(contentToPrint);
        printFrame.contentDocument.close();

        // Attendi il caricamento delle immagini prima di stampare
        printFrame.onload = function() {
            setTimeout(function() {
                printFrame.contentWindow.print();
                document.body.removeChild(printFrame);
            }, 500);
        };
    }

    // Funzione per scaricare l'etichetta come PDF
    function downloadEtichetta() {
        const numCopie = parseInt($('#numCopie').val()) || 1;
        const articoloId = $('#articolo_id').val();
        const barcode = $('#etichettaBarcode').text();
        const lotto = $('#etichettaLotto').text();

        // Creiamo un form temporaneo per inviare la richiesta di download
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/utente/download-etichetta';
        form.style.display = 'none';

        // Aggiungi i campi nascosti con i dati necessari
        const fields = {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'articolo_id': articoloId,
            'barcode': barcode,
            'lotto': lotto,
            'num_copie': numCopie
        };

        // Crea gli input per ogni campo
        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        // Aggiungi il form al body e invialo
        document.body.appendChild(form);
        form.submit();

        // Rimuovi il form dopo l'invio
        setTimeout(() => {
            document.body.removeChild(form);
        }, 100);
    }

    // Modifica la funzione submitCarico per mostrare l'etichetta dopo il carico
    function submitCarico() {
        // ... codice esistente per la validazione ...

        // Prepara il FormData per l'invio dei dati (necessario per l'allegato)
        const formData = new FormData(document.getElementById('caricoForm'));
        const articleId = $('#articolo_id').val();

        // Aggiungi il token CSRF
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        formData.append('_token', csrfToken);

        // Mostra un indicatore di caricamento
        Swal.fire({
            title: 'Caricamento in corso...',
            text: 'Stiamo elaborando la tua richiesta',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Invia la richiesta AJAX
        $.ajax({
            url: `/utente/carico-magazzino/${articleId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();

                if (response.success) {
                    // Chiudi la modale di carico
                    const modal = bootstrap.Modal.getInstance(document.getElementById('caricoModal'));
                    modal.hide();

                    // Mostra l'etichetta barcode
                    const articolo = response.articolo || {};

                    showEtichetta({
                        titolo: articolo.titolo || $('#articolo_selezionato').text(),
                        codice: articolo.codice_articolo || 'N/A',
                        lotto: $('#lotto').val(),
                        barcode: response.barcode
                    });

                    // Non ricarichiamo subito la pagina, lasciamo l'utente gestire l'etichetta
                } else {
                    Swal.fire({
                        title: 'Errore',
                        text: response.message || 'Si è verificato un errore durante il carico',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                // Prova a estrarre il messaggio di errore dalla risposta
                let errorMessage = 'Si è verificato un errore durante il carico';

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Errore nel parsing della risposta:', e);
                }

                Swal.fire({
                    title: 'Errore',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    }


    let currentArticleId = null;
    let currentUm = null;

    $(document).ready(function() {
        // Inizializzazione DataTable per materie prime
        if (!$.fn.dataTable.isDataTable('#tableMateriePrime')) {
            var table = $('#tableMateriePrime').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
                }
            });

            // Filtro di ricerca in tempo reale
            $('#searchMateriePrime').keyup(function() {
                table.search($(this).val()).draw();
            });
        }

        // Focus sull'input barcode nella tab barcode
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') === '#tab-barcode') {
                $('#barcodeInput').focus();
            }
        });

        // Inizializza il focus sull'input barcode
        $('#barcodeInput').focus();

        // Handler per enter sul campo barcode
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkArticleManual();
            }
        });

        // Inizializza Select2 per il campo fornitore
        if ($.fn.select2) {
            $('#id_fornitore').select2({
                dropdownParent: $('#caricoModal'),
                placeholder: 'Cerca fornitore...',
                width: '100%'
            });
        }
    });

    // Configurazione scanner barcode
    if (typeof onScan !== 'undefined') {
        onScan.attachTo(document, {
            onScan: function(barcode) {
                console.log('Barcode scansionato:', barcode);

                // Se siamo nella tab barcode, usiamo il barcode
                if ($('#tab-barcode').hasClass('active')) {
                    $('#barcodeInput').val(barcode);
                    checkArticle(barcode);
                }
            },
            onScanError: function(e) {
                console.error('Errore di scansione:', e);
            }
        });
    }

    // Cerca articolo al click del pulsante
    function checkArticleManual() {
        const barcode = $('#barcodeInput').val();
        if (barcode) {
            checkArticle(barcode);
        }
    }

    // Funzione per verificare l'esistenza dell'articolo nel database
    function checkArticle(barcode) {
        fetch(`/check?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayArticle(data.article);
                } else {
                    Swal.fire({
                        title: 'Attenzione',
                        text: 'Articolo non trovato',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
                Swal.fire({
                    title: 'Errore',
                    text: 'Si è verificato un errore durante la ricerca',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    // Funzione per visualizzare i dettagli dell'articolo nella tabella
    function displayArticle(article) {
        currentArticleId = article.id;
        $('#articleTable').show();

        const cleanTitle = article.titolo.replace(/'/g, "&#39;");

        $('#articleDetails').html(`
            <tr>
                <td>${article.codice_articolo || article.barcode}</td>
                <td>${article.titolo}</td>
                <td>${article.um}</td>
                <td>${article.giacenza || 0}</td>
                <td>
                    <button class="btn btn-primary" onclick="openCaricoModal(${article.id}, '${cleanTitle}', '${article.um}')">
                        <i class="ri-add-line"></i> Carica
                    </button>
                </td>
            </tr>
        `);
    }


    // Funzione per il calcolo dell'imponibile
    function calcolaImponibile() {
        const giacenza = parseFloat($('#addQuantity').val()) || 0;
        const prezzoUnitario = parseFloat($('#prezzo_unitario').val()) || 0;
        const imponibile = giacenza * prezzoUnitario;

        $('#imponibile').val(imponibile.toFixed(2));
    }

    // Funzione per aprire la modale per caricare la giacenza
    function openCaricoModal(articleId, titolo, um) {
        // Aggiorna le informazioni nell'alert
        $('#articolo_selezionato').html(`<strong>${titolo}</strong> (UM: ${um})`);
        $('#um_display').text(um);

        // Inserisci l'id dell'articolo nel form
        $('#articolo_id').val(articleId);

        // Reset del form
        $('#caricoForm')[0].reset();

        // Precompila la data di scadenza con la data di oggi + 12 mesi
        let today = new Date();
        today.setMonth(today.getMonth() + 12);
        let futureDate = today.toISOString().split('T')[0];
        $('#addScadenza').val(futureDate);

        // Precompila causale
        $('#causale').val('Carico manuale');

        // Imposta valori default
        $('#aliquota_iva').val('22');
        $('#valuta').val('EUR');

        // Reinizializza Select2 all'apertura della modal
        if ($.fn.select2) {
            setTimeout(() => {
                $('#id_fornitore').select2({
                    dropdownParent: $('#caricoModal'),
                    placeholder: 'Cerca fornitore...',
                    width: '100%'
                });
            }, 100);
        }

        // Visualizza la modale
        let caricoModal = new bootstrap.Modal(document.getElementById('caricoModal'));
        caricoModal.show();
    }

    // Funzione per sottomettere il carico a magazzino
    function submitCarico() {
        // Validazione dei campi obbligatori
        const requiredFields = [
            { id: 'addQuantity', name: 'Quantità' },
            { id: 'mg', name: 'Magazzino' },
            { id: 'lotto', name: 'Lotto' },
            { id: 'id_fornitore', name: 'Fornitore' },
            { id: 'prezzo_unitario', name: 'Prezzo unitario' },
            { id: 'aliquota_iva', name: 'Aliquota IVA' }
        ];

        let isValid = true;
        let firstInvalidField = null;

        // Controlla ogni campo obbligatorio
        for (const field of requiredFields) {
            const value = $(`#${field.id}`).val();
            if (!value || value.trim() === '') {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }

                // Aggiungi classe di errore
                $(`#${field.id}`).addClass('is-invalid');

                // Se il campo è in un tab diverso da quello attivo, attiva il tab
                const tabId = $(`#${field.id}`).closest('.tab-pane').attr('id');
                if (tabId && !$(`#${tabId}`).hasClass('active')) {
                    $(`[data-bs-target="#${tabId}"]`).tab('show');
                }
            } else {
                // Rimuovi classe di errore
                $(`#${field.id}`).removeClass('is-invalid');
            }
        }

        // Se la validazione fallisce, mostra errore e fermati
        if (!isValid) {
            Swal.fire({
                title: 'Attenzione',
                text: `Compila tutti i campi obbligatori. Manca: ${firstInvalidField.name}`,
                icon: 'warning'
            });
            return;
        }

        // Prepara il FormData per l'invio dei dati (necessario per l'allegato)
        const formData = new FormData(document.getElementById('caricoForm'));
        const articleId = $('#articolo_id').val();

        // Aggiungi il token CSRF
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        formData.append('_token', csrfToken);

        // Mostra un indicatore di caricamento
        Swal.fire({
            title: 'Caricamento in corso...',
            text: 'Stiamo elaborando la tua richiesta',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Invia la richiesta AJAX
        $.ajax({
            url: `/utente/carico-magazzino/${articleId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();

                if (response.success) {
                    // Chiudi la modale di carico
                    const modal = bootstrap.Modal.getInstance(document.getElementById('caricoModal'));
                    modal.hide();

                    // Mostra l'etichetta barcode
                    const articolo = response.articolo || {};

                    showEtichetta({
                        titolo: articolo.titolo || $('#articolo_selezionato').text(),
                        codice: articolo.codice_articolo || '',
                        lotto: $('#lotto').val(),
                        barcode: response.barcode
                    });

                    // Non ricarichiamo subito la pagina, lasciamo l'utente gestire l'etichetta
                } else {
                    Swal.fire({
                        title: 'Errore',
                        text: response.message || 'Si è verificato un errore durante il carico',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                // Prova a estrarre il messaggio di errore dalla risposta
                let errorMessage = 'Si è verificato un errore durante il carico';

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Errore nel parsing della risposta:', e);
                }

                Swal.fire({
                    title: 'Errore',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    }

    // Inizializzazione dei componenti quando il documento è pronto
    $(document).ready(function() {
        // Inizializza select2 per la ricerca avanzata del fornitore
        if ($.fn.select2) {
            $('#id_fornitore').select2({
                dropdownParent: $('#caricoModal'),
                placeholder: 'Cerca fornitore...',
                width: '100%'
            });
        }

        // Gestione eventi per il calcolo automatico dell'imponibile
        $('#addQuantity, #prezzo_unitario').on('input', calcolaImponibile);

        // Gestione degli errori di validazione
        $('input, select').on('change', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });

        // Gestione tab nella modale
        $('#carTabContent a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Gestione del file allegato
        $('#allegato_ddt').on('change', function() {
            const fileInput = this;
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                // Controlla dimensione del file
                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'File troppo grande',
                        text: 'Il file selezionato è troppo grande. La dimensione massima consentita è 5MB.',
                        icon: 'error'
                    });

                    // Reset del campo file
                    $(this).val('');
                    return;
                }

                // Controlla il tipo di file
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        title: 'Formato non supportato',
                        text: 'Per favore carica un file in formato PDF, JPG o PNG.',
                        icon: 'error'
                    });

                    // Reset del campo file
                    $(this).val('');
                    return;
                }

                // Mostra il nome del file selezionato
                const fileName = file.name;
                $(this).next('.form-text').text(`File selezionato: ${fileName}`);
            }
        });
    });

</script>