@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Giacenze Magazzino: {{ $magazzino->descrizione }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Articolo</th>
                                    <th>UM</th>
                                    @if($usa_lotti)
                                    <th>Lotto</th>
                                    <th>Data Scadenza</th>
                                    @endif
                                    <th>Giacenza</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($giacenze as $giacenza)
                                    <tr>
                                        <td>{{ $giacenza->codice_articolo }}</td>
                                        <td>{{ $giacenza->titolo }}</td>
                                        <td>{{ $giacenza->um }}</td>
                                        @if($usa_lotti)
                                        <td>{{ $giacenza->lotto }}</td>
                                        <td>{{ $giacenza->scadenza_lotto ? date('d/m/Y', strtotime($giacenza->scadenza_lotto)) : '-' }}</td>
                                        @endif
                                        <td>{{ number_format($giacenza->qta, 2) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Azioni
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="apriDettaglioGiacenza('{{ $giacenza->id_articolo }}', '{{ $magazzino->id }}', '{{ $giacenza->lotto }}')">
                                                            <i class="ri-eye-line me-2"></i>Dettaglio
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ url('/utente/magazzino/movimenti/'.$giacenza->id_articolo.'/'.$magazzino->id) }}">
                                                            <i class="ri-history-line me-2"></i>Movimenti
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="openTransferModal('{{ $giacenza->id_articolo }}', '{{ $giacenza->codice_articolo }}', '{{ $giacenza->titolo }}', '{{ $giacenza->qta }}', '{{ $giacenza->um }}', '{{ $giacenza->lotto }}', '{{ $giacenza->scadenza_lotto }}')">
                                                            <i class="ri-exchange-line me-2"></i>Trasferisci
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
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
<!-- Modal Dettaglio Giacenza -->
<div class="modal fade" id="modalDettaglioGiacenza" tabindex="-1" aria-labelledby="dettaglioGiacenzaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-primary">
                <h5 class="modal-title" id="dettaglioGiacenzaLabel">Dettaglio Giacenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="avatar-xl me-3">
                            <div class="avatar-title bg-light text-primary rounded">
                                <i class="ri-box-3-line fs-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h5 class="mb-1 text-truncate" id="dettaglio-titolo"></h5>
                        <p class="text-muted mb-0" id="dettaglio-codice"></p>
                        <p class="text-muted mb-0">Lotto: <span id="dettaglio-lotto"></span></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-primary mb-3">Informazioni Generali</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                    <tr>
                                        <th class="ps-0 w-50">Giacenza:</th>
                                        <td id="dettaglio-giacenza"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Magazzino:</th>
                                        <td id="dettaglio-magazzino"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Data Carico:</th>
                                        <td id="dettaglio-data-carico"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Scadenza:</th>
                                        <td id="dettaglio-scadenza"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Causale:</th>
                                        <td id="dettaglio-causale"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Commessa:</th>
                                        <td id="dettaglio-commessa"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Barcode:</th>
                                        <td id="dettaglio-barcode">
                                            <!-- Verrà popolato via JavaScript -->
                                        </td>
                                    </tr>
                                    <tr id="dettaglio-barcode-azioni">
                                        <th class="ps-0">Azioni Barcode:</th>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-soft-primary" id="stampa-barcode">
                                                <i class="ri-printer-line align-middle me-1"></i> Stampa Barcode
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-primary mb-3">Dettagli Economici</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                    <tr>
                                        <th class="ps-0 w-50">Fornitore:</th>
                                        <td id="dettaglio-fornitore"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Prezzo Unitario:</th>
                                        <td id="dettaglio-prezzo-unitario"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Imponibile:</th>
                                        <td id="dettaglio-imponibile"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Aliquota IVA:</th>
                                        <td id="dettaglio-aliquota-iva"></td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Valuta:</th>
                                        <td id="dettaglio-valuta"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3 mb-3">
                    <h5 class="text-primary mb-3">Documento di Trasporto</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                            <tr>
                                <th class="ps-0 w-25">N° DDT:</th>
                                <td id="dettaglio-numero-ddt"></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Data DDT:</th>
                                <td id="dettaglio-data-ddt"></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Allegato:</th>
                                <td id="dettaglio-allegato"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="dettaglio-documento-container" class="border rounded p-3 mb-3" style="display: none;">
                    <h5 class="text-primary mb-3">Documento Collegato</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                            <tr>
                                <th class="ps-0 w-25">Tipo Documento:</th>
                                <td id="dettaglio-tipo-documento"></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Numero Documento:</th>
                                <td id="dettaglio-numero-documento"></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Data Documento:</th>
                                <td id="dettaglio-data-documento"></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Azioni:</th>
                                <td>
                                    <a href="#" id="dettaglio-link-documento" class="btn btn-sm btn-soft-primary">
                                        <i class="ri-eye-line align-middle me-1"></i> Visualizza
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Trasferimento Magazzino -->
<div class="modal fade" id="trasferimentoModal" tabindex="-1" aria-labelledby="trasferimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="trasferimentoModalLabel">Trasferimento Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="trasferimentoForm" action="{{ url('/utente/magazzino/trasferisci') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p id="articolo_info"></p>
                        <p>Magazzino di origine: <strong>{{ $magazzino->descrizione }}</strong></p>
                    </div>
                    <input type="hidden" name="id_articolo" id="id_articolo">
                    <input type="hidden" name="magazzinoOrigine" value="{{ $magazzino->id }}">
                    <input type="hidden" name="lotto" id="lotto_transfer">
                    <input type="hidden" name="scadenza" id="scadenza_transfer">

                    <div class="mb-3">
                        <label for="magazzino_destinazione" class="form-label">Magazzino di Destinazione</label>
                        <select name="magazzino_destinazione" id="magazzino_destinazione" class="form-select" required>
                            <option value="">Seleziona magazzino</option>
                            @foreach($magazzini as $mag)
                                @if($mag->id != $magazzino->id)
                                    <option value="{{ $mag->id }}">{{ $mag->descrizione }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantita" class="form-label">Quantità da Trasferire</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="quantita" name="quantita" required>
                            <span class="input-group-text" id="um_display">PZ</span>
                        </div>
                        <div class="form-text text-muted">Giacenza disponibile: <span id="giacenza_disponibile">0</span> <span id="um_disponibile">PZ</span></div>
                    </div>
                    <div class="mb-3">
                        <label for="causale" class="form-label">Causale</label>
                        <input type="text" class="form-control" id="causale" name="causale" value="Trasferimento da magazzino {{ $magazzino->descrizione }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary" id="submitTransfer">Trasferisci</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    function stampaBarcode(barcode, titolo) {
        // Crea una finestra popup per la stampa
        const printWindow = window.open('', '_blank');

        // Genera l'URL del barcode utilizzando un servizio di generazione barcode
        const barcodeUrl = `https://barcode.tec-it.com/barcode.ashx?data=${encodeURIComponent(barcode)}&code=Code128&translate-esc=true`;

        // Crea il contenuto HTML per la stampa
        printWindow.document.write(`
        <html>
        <head>
            <title>Stampa Barcode</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                .barcode-container { margin: 20px auto; }
                .titolo { font-size: 14px; margin-bottom: 5px; }
                .barcode-number { font-size: 12px; margin-top: 5px; }
                @media print {
                    body { margin: 0; padding: 0; }
                    .print-button { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="barcode-container">
                <div class="titolo">${titolo}</div>
                <img src="${barcodeUrl}" alt="Barcode">
                <div class="barcode-number">${barcode}</div>
            </div>
            <button class="print-button" onclick="window.print(); setTimeout(() => window.close(), 500);">Stampa</button>
        </body>
        </html>
    `);

        printWindow.document.close();
    }


    /**
     * Funzione per stampare il barcode
     * @param {string} barcode - Il codice a barre da stampare
     * @param {string} titolo - Il titolo dell'articolo
     */
    function stampaBarcode(barcode, titolo) {
        // Crea una finestra popup per la stampa
        const printWindow = window.open('', '_blank');

        // Genera l'URL del barcode utilizzando un servizio di generazione barcode
        const barcodeUrl = `https://barcode.tec-it.com/barcode.ashx?data=${encodeURIComponent(barcode)}&code=Code128&translate-esc=true`;

        // Crea il contenuto HTML per la stampa
        printWindow.document.write(`
        <html>
        <head>
            <title>Stampa Barcode</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                .barcode-container { margin: 20px auto; }
                .titolo { font-size: 14px; margin-bottom: 5px; }
                .barcode-number { font-size: 12px; margin-top: 5px; }
                @media print {
                    body { margin: 0; padding: 0; }
                    .print-button { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="barcode-container">
                <div class="titolo">${titolo}</div>
                <img src="${barcodeUrl}" alt="Barcode">
                <div class="barcode-number">${barcode}</div>
            </div>
            <button class="print-button" onclick="window.print(); setTimeout(() => window.close(), 500);">Stampa</button>
        </body>
        </html>
    `);

        printWindow.document.close();
    }


    /**
     * Funzione per aprire il modal di dettaglio giacenza
     * @param {number} idArticolo - ID dell'articolo
     * @param {string} idMg - ID del magazzino
     * @param {string} lotto - Lotto
     */
    function apriDettaglioGiacenza(idArticolo, idMg, lotto) {
        // Mostra un messaggio di caricamento nel modal
        $('#modalDettaglioGiacenza').modal('show');

        // Mostra messaggio di caricamento
        const loadingHtml = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Caricamento...</span>
            </div>
            <p class="mt-2">Caricamento dettagli in corso...</p>
        </div>
    `;
        $('#modalDettaglioGiacenza .modal-body').html(loadingHtml);

        // Chiamata AJAX per recuperare i dettagli della giacenza
        $.ajax({
            url: '{{ url("/utente/dettaglio-giacenza") }}',
            type: 'GET',
            data: {
                id_articolo: idArticolo,
                id_mg: idMg,
                lotto: lotto
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const movimento = data.movimento;
                    const articolo = data.articolo;
                    const magazzino = data.magazzino;
                    const fornitore = data.fornitore;
                    const commessa = data.commessa;
                    const documento = data.documento;

                    // Ricarica il corpo del modal con il template originale
                    $('#modalDettaglioGiacenza .modal-body').html(`
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar-xl me-3">
                                <div class="avatar-title bg-light text-primary rounded">
                                    <i class="ri-box-3-line fs-1"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="mb-1 text-truncate" id="dettaglio-titolo"></h5>
                            <p class="text-muted mb-0" id="dettaglio-codice"></p>
                            <p class="text-muted mb-0">Lotto: <span id="dettaglio-lotto"></span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="border rounded p-3 mb-3">
                                <h5 class="text-primary mb-3">Informazioni Generali</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                        <tr>
                                            <th class="ps-0 w-50">Giacenza:</th>
                                            <td id="dettaglio-giacenza"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Magazzino:</th>
                                            <td id="dettaglio-magazzino"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Data Carico:</th>
                                            <td id="dettaglio-data-carico"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Scadenza:</th>
                                            <td id="dettaglio-scadenza"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Causale:</th>
                                            <td id="dettaglio-causale"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Commessa:</th>
                                            <td id="dettaglio-commessa"></td>
                                        </tr>
 <!-- Aggiungi queste due righe per il barcode -->
                                    <tr>
                                        <th class="ps-0">Barcode:</th>
                                        <td id="dettaglio-barcode">-</td>
                                    </tr>
                                    <tr id="dettaglio-barcode-azioni">
                                        <th class="ps-0">Azioni Barcode:</th>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-soft-primary" id="stampa-barcode">
                                                <i class="ri-printer-line align-middle me-1"></i> Stampa Barcode
                                            </button>
                                        </td>
                                    </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border rounded p-3 mb-3">
                                <h5 class="text-primary mb-3">Dettagli Economici</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                        <tr>
                                            <th class="ps-0 w-50">Fornitore:</th>
                                            <td id="dettaglio-fornitore"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Prezzo Unitario:</th>
                                            <td id="dettaglio-prezzo-unitario"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Imponibile:</th>
                                            <td id="dettaglio-imponibile"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Aliquota IVA:</th>
                                            <td id="dettaglio-aliquota-iva"></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Valuta:</th>
                                            <td id="dettaglio-valuta"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3">
                        <h5 class="text-primary mb-3">Documento di Trasporto</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0 w-25">N° DDT:</th>
                                    <td id="dettaglio-numero-ddt"></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Data DDT:</th>
                                    <td id="dettaglio-data-ddt"></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Allegato:</th>
                                    <td id="dettaglio-allegato"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="dettaglio-documento-container" class="border rounded p-3 mb-3" style="display: none;">
                        <h5 class="text-primary mb-3">Documento Collegato</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0 w-25">Tipo Documento:</th>
                                    <td id="dettaglio-tipo-documento"></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Numero Documento:</th>
                                    <td id="dettaglio-numero-documento"></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Data Documento:</th>
                                    <td id="dettaglio-data-documento"></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Azioni:</th>
                                    <td>
                                        <a href="#" id="dettaglio-link-documento" class="btn btn-sm btn-soft-primary">
                                            <i class="ri-eye-line align-middle me-1"></i> Visualizza
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);
                    // Gestione del barcode
                    if (movimento.barcode) {
                        $('#dettaglio-barcode').text(movimento.barcode);
                        $('#dettaglio-barcode-azioni').show();

                        // Aggiungi evento per la stampa del barcode
                        $('#stampa-barcode').off('click').on('click', function() {
                            stampaBarcode(movimento.barcode, articolo.titolo);
                        });
                    } else {
                        $('#dettaglio-barcode').text('Nessun barcode disponibile');
                        $('#dettaglio-barcode-azioni').hide();
                    }
                    // Popola le informazioni nel modal
                    $('#dettaglio-titolo').text(articolo.titolo);
                    $('#dettaglio-codice').text('Codice: ' + articolo.codice_articolo);
                    $('#dettaglio-lotto').text(movimento.lotto);
                    $('#dettaglio-giacenza').text(movimento.qta + ' ' + articolo.um);
                    $('#dettaglio-magazzino').text(magazzino ? magazzino.descrizione : 'N/D');
                    $('#dettaglio-data-carico').text(formatDate(movimento.datamov));
                    $('#dettaglio-scadenza').text(movimento.scadenza_lotto ? formatDate(movimento.scadenza_lotto) : 'N/D');
                    $('#dettaglio-causale').text(movimento.causale || 'N/D');
                    $('#dettaglio-commessa').text(commessa ? commessa.codice_commessa + ' - ' + commessa.descrizione : 'N/D');

                    // Dettagli economici
                    $('#dettaglio-fornitore').text(fornitore ? fornitore.ragione_sociale : 'N/D');
                    $('#dettaglio-prezzo-unitario').text(movimento.prezzo_unitario ? formatCurrency(movimento.prezzo_unitario) : 'N/D');
                    $('#dettaglio-imponibile').text(movimento.imponibile ? formatCurrency(movimento.imponibile) : 'N/D');
                    $('#dettaglio-aliquota-iva').text(movimento.aliquota_iva ? movimento.aliquota_iva + '%' : 'N/D');
                    $('#dettaglio-valuta').text(movimento.valuta || 'EUR');

                    // DDT
                    $('#dettaglio-numero-ddt').text(movimento.numero_ddt || 'N/D');
                    $('#dettaglio-data-ddt').text(movimento.data_ddt ? formatDate(movimento.data_ddt) : 'N/D');

                    // Allegato
                    if (movimento.percorso_allegato) {
                        $('#dettaglio-allegato').html(`
                    <a href="${movimento.percorso_allegato}" target="_blank" class="btn btn-sm btn-soft-info">
                        <i class="ri-file-download-line align-middle me-1"></i> Scarica
                    </a>
                `);
                    } else {
                        $('#dettaglio-allegato').text('Nessun allegato');
                    }

                    // Documento collegato (BDC)
                    if (documento) {
                        $('#dettaglio-documento-container').show();
                        $('#dettaglio-tipo-documento').text(documento.tipo_documento || 'BDC');
                        $('#dettaglio-numero-documento').text(documento.numero_doc);
                        $('#dettaglio-data-documento').text(formatDate(documento.data_doc));
                        $('#dettaglio-link-documento').attr('href', '/utente/modifica_documento/' + documento.id);
                    } else {
                        $('#dettaglio-documento-container').hide();
                    }
                } else {
                    // Mostra messaggio di errore nel modal
                    $('#modalDettaglioGiacenza .modal-body').html(`
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        ${response.message || 'Errore nel recupero dei dettagli della giacenza'}
                    </div>
                `);
                }
            },
            error: function(xhr, status, error) {
                // Mostra messaggio di errore nel modal
                $('#modalDettaglioGiacenza .modal-body').html(`
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line me-2"></i>
                    Si è verificato un errore nel recupero dei dettagli
                </div>
            `);
            }
        });
    }

    /**
     * Formatta una data nel formato italiano
     * @param {string} dateString - Data in formato stringa
     * @returns {string} Data formattata
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/D';

        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('it-IT', options);
    }

    /**
     * Formatta un importo come valuta
     * @param {number} amount - Importo
     * @returns {string} Importo formattato
     */
    function formatCurrency(amount) {
        if (!amount && amount !== 0) return 'N/D';

        return new Intl.NumberFormat('it-IT', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2
        }).format(amount);
    }

    function openTransferModal(id_articolo, codice, titolo, giacenza, um, lotto, scadenza) {
        document.getElementById('id_articolo').value = id_articolo;
        document.getElementById('lotto_transfer').value = lotto;
        document.getElementById('scadenza_transfer').value = scadenza;
        document.getElementById('articolo_info').innerHTML = `<strong>${titolo}</strong> (${codice})`;
        document.getElementById('giacenza_disponibile').textContent = parseFloat(giacenza).toFixed(2);
        document.getElementById('um_display').textContent = um;
        document.getElementById('um_disponibile').textContent = um;

        // Imposta il valore massimo per la quantità
        document.getElementById('quantita').setAttribute('max', giacenza);

        // Mostra il modal
        var myModal = new bootstrap.Modal(document.getElementById('trasferimentoModal'));
        myModal.show();
    }

    // Validazione della quantità
    document.getElementById('trasferimentoForm').addEventListener('submit', function(e) {
        var quantita = parseFloat(document.getElementById('quantita').value);
        var giacenza = parseFloat(document.getElementById('giacenza_disponibile').textContent);

        if (quantita <= 0) {
            e.preventDefault();
            alert('La quantità deve essere maggiore di zero');
            return false;
        }

        if (quantita > giacenza) {
            e.preventDefault();
            alert('La quantità da trasferire non può essere maggiore della giacenza disponibile');
            return false;
        }

        return true;
    });
</script>