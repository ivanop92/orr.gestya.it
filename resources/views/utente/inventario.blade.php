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
                    <h4 class="mb-sm-0">Rettifica Giacenze</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-barcode" role="tab">
                                    <i class="ri-barcode-fill me-1 align-middle"></i> Scansione Barcode
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-articoli" role="tab">
                                    <i class="ri-list-check me-1 align-middle"></i> Lista Articoli
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
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
                                                <th>Giacenza Totale</th>
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

                            <!-- Tab Lista Articoli -->
                            <div class="tab-pane" id="tab-articoli" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <input type="text" id="searchArticoli" class="form-control" placeholder="Cerca articoli...">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="tableArticoli">
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
                                        @foreach($articoli as $articolo)
                                            <tr>
                                                <td>{{ $articolo->codice_articolo }}</td>
                                                <td>{{ $articolo->titolo }}</td>
                                                <td>{{ $articolo->um }}</td>
                                                <td>{{ $articolo->giacenza ?? 0 }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" onclick="checkRettificaDisponibilita({{ $articolo->id }}, '{{ $articolo->titolo }}')">
                                                        <i class="ri-edit-line"></i> Rettifica
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

<!-- Modale per selezionare il lotto/magazzino -->
<div class="modal fade" id="lottoModal" tabindex="-1" aria-labelledby="lottoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-warning">
                <h5 class="modal-title" id="lottoModalLabel">{{ $usa_lotti ? 'Seleziona Lotto e Magazzino da Rettificare' : 'Seleziona Magazzino da Rettificare' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="articolo_scelto" class="alert alert-info mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableLotti">
                        <thead>
                        <tr>
                            <th>Magazzino</th>
                            @if($usa_lotti)
                            <th>Lotto</th>
                            <th>Scadenza</th>
                            @endif
                            <th>Giacenza Attuale</th>
                            <th>Azioni</th>
                        </tr>
                        </thead>
                        <tbody id="lottiList">
                        <!-- I lotti saranno aggiunti dinamicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modale per la rettifica giacenza -->
<div class="modal fade" id="rettificaModal" tabindex="-1" aria-labelledby="rettificaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-warning">
                <h5 class="modal-title" id="rettificaModalLabel">Rettifica Giacenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="lotto_selezionato" class="alert alert-info mb-3"></div>
                <form id="rettificaForm">
                    <input type="hidden" id="articolo_id">
                    <input type="hidden" id="magazzino_id">
                    <input type="hidden" id="lotto_id">
                    <input type="hidden" id="scadenza_lotto">

                    <div class="mb-3">
                        <label for="giacenzaAttuale" class="form-label">Giacenza Attuale</label>
                        <div class="input-group">
                            <input type="text" id="giacenzaAttuale" class="form-control" readonly>
                            <span class="input-group-text" id="um_display">PZ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="newQuantity" class="form-label">Nuova Giacenza</label>
                        <div class="input-group">
                            <input type="number" step="0.01" id="newQuantity" class="form-control" required>
                            <span class="input-group-text" id="um_display2">PZ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="differenzaGiacenza" class="form-label">Differenza (verrà registrata come movimento)</label>
                        <div class="input-group">
                            <input type="text" id="differenzaGiacenza" class="form-control" readonly>
                            <span class="input-group-text" id="um_display3">PZ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="causale" class="form-label">Causale</label>
                        <input type="text" id="causale" class="form-control" value="Rettifica inventario" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-warning" onclick="submitRettifica()">Conferma Rettifica</button>
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
    let currentArticleId = null;
    let currentUm = null;
    let giacenzeDisponibili = [];

    $(document).ready(function() {
        // Inizializza DataTable per gli articoli
        if (!$.fn.dataTable.isDataTable('#tableArticoli')) {
            var tableArticoli = $('#tableArticoli').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
                }
            });

            $('#searchArticoli').keyup(function() {
                tableArticoli.search($(this).val()).draw();
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

        // Calcola automaticamente la differenza quando viene modificata la nuova giacenza
        $('#newQuantity').on('input', function() {
            calcolaDifferenza();
        });

        // Configura lo scanner barcode
        setupBarcodeScanner();
    });

    // Funzione per calcolare la differenza tra la giacenza attuale e la nuova giacenza
    function calcolaDifferenza() {
        const giacenzaAttuale = parseFloat($('#giacenzaAttuale').val()) || 0;
        const nuovaGiacenza = parseFloat($('#newQuantity').val()) || 0;
        const differenza = nuovaGiacenza - giacenzaAttuale;

        $('#differenzaGiacenza').val(differenza.toFixed(2));

        // Evidenzia in rosso o verde in base al segno della differenza
        if (differenza < 0) {
            $('#differenzaGiacenza').removeClass('text-success').addClass('text-danger');
        } else if (differenza > 0) {
            $('#differenzaGiacenza').removeClass('text-danger').addClass('text-success');
        } else {
            $('#differenzaGiacenza').removeClass('text-danger text-success');
        }
    }

    // Configurazione scanner barcode
    function setupBarcodeScanner() {
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

                    // Se ci sono giacenze disponibili, procediamo con la verifica
                    if (data.giacenze && data.giacenze.length > 0) {
                        checkRettificaDisponibilita(data.article.id, data.article.titolo);
                    } else {
                        Swal.fire({
                            title: 'Attenzione',
                            text: 'L\'articolo non ha giacenze da rettificare',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
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
        currentUm = article.um;

        $('#articleTable').show();

        const cleanTitle = article.titolo.replace(/'/g, "&#39;");

        $('#articleDetails').html(`
            <tr>
                <td>${article.codice_articolo || article.barcode}</td>
                <td>${article.titolo}</td>
                <td>${article.um}</td>
                <td>${article.giacenza || 0}</td>
                <td>
                    <button class="btn btn-warning" onclick="checkRettificaDisponibilita(${article.id}, '${cleanTitle}')">
                        <i class="ri-edit-line"></i> Rettifica
                    </button>
                </td>
            </tr>
        `);
    }

    // Funzione per verificare le giacenze disponibili per rettifica
    function checkRettificaDisponibilita(articleId, titolo) {
        currentArticleId = articleId;

        fetch(`/utente/get-disponibilita/${articleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    giacenzeDisponibili = data.disponibilita;

                    if (giacenzeDisponibili.length === 0) {
                        Swal.fire({
                            title: 'Attenzione',
                            text: 'Non ci sono giacenze disponibili per questo articolo',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Mostra il modale con i lotti disponibili
                    $('#articolo_scelto').text(`Articolo: ${titolo}`);

                    let lottiHTML = '';
                    giacenzeDisponibili.forEach(disp => {
                        const scadenzaFormatted = disp.scadenza_lotto ? new Date(disp.scadenza_lotto).toLocaleDateString() : 'N/D';
                        lottiHTML += `
                            <tr>
                                <td>${disp.magazzino_descrizione}</td>
                                <td>${disp.lotto || 'N/D'}</td>
                                <td>${scadenzaFormatted}</td>
                                <td>${disp.giacenza} ${disp.um}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openRettificaModal(${disp.id_articolo}, ${disp.id_magazzino}, '${disp.lotto}', '${disp.scadenza_lotto || ''}', ${disp.giacenza}, '${disp.um}', '${disp.magazzino_descrizione}')">
                                        Rettifica
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    $('#lottiList').html(lottiHTML);

                    let lottoModal = new bootstrap.Modal(document.getElementById('lottoModal'));
                    lottoModal.show();
                } else {
                    Swal.fire({
                        title: 'Errore',
                        text: data.message || 'Errore nel recupero delle giacenze',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
                Swal.fire({
                    title: 'Errore',
                    text: 'Si è verificato un errore durante il recupero delle giacenze',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    // Funzione per aprire la modale per rettificare la giacenza
    function openRettificaModal(idArticolo, idMagazzino, lotto, scadenza, giacenza, um, magazzino) {
        // Chiudi il modale dei lotti
        let lottoModal = bootstrap.Modal.getInstance(document.getElementById('lottoModal'));
        lottoModal.hide();

        // Imposta i dati nel form
        $('#articolo_id').val(idArticolo);
        $('#magazzino_id').val(idMagazzino);
        $('#lotto_id').val(lotto);
        $('#scadenza_lotto').val(scadenza);
        $('#um_display').text(um);
        $('#um_display2').text(um);
        $('#um_display3').text(um);
        $('#giacenzaAttuale').val(giacenza);
        $('#newQuantity').val(giacenza); // Precompila con la giacenza attuale
        $('#differenzaGiacenza').val('0.00'); // Inizialmente nessuna differenza

        // Informazioni visualizzate
        $('#lotto_selezionato').html(`
            <strong>Magazzino:</strong> ${magazzino}<br>
            <strong>Lotto:</strong> ${lotto || 'N/D'}<br>
            <strong>Scadenza:</strong> ${scadenza ? new Date(scadenza).toLocaleDateString() : 'N/D'}<br>
            <strong>Giacenza attuale:</strong> ${giacenza} ${um}
        `);

        // Mostra il modale
        let rettificaModal = new bootstrap.Modal(document.getElementById('rettificaModal'));
        rettificaModal.show();

        // Focus sul campo nuova giacenza
        setTimeout(() => {
            $('#newQuantity').focus().select();
        }, 500);
    }

    // Funzione per sottomettere la rettifica giacenza
    function submitRettifica() {
        const articleId = $('#articolo_id').val();
        const magazzinoId = $('#magazzino_id').val();
        const lotto = $('#lotto_id').val();
        const scadenza = $('#scadenza_lotto').val();
        const newQuantity = $('#newQuantity').val();
        const causale = $('#causale').val();
        const giacenzaAttuale = parseFloat($('#giacenzaAttuale').val());
        const differenza = parseFloat($('#differenzaGiacenza').val());

        // Validazione
        if (!newQuantity || !causale) {
            Swal.fire({
                title: 'Attenzione',
                text: 'Tutti i campi obbligatori devono essere compilati',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (parseFloat(newQuantity) < 0) {
            Swal.fire({
                title: 'Attenzione',
                text: 'La nuova giacenza non può essere negativa',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Prepara il token CSRF
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Invio dei dati al server con jQuery AJAX
        $.ajax({
            url: `/update-giacenza/${articleId}`,
            type: 'POST',
            data: JSON.stringify({
                giacenza: newQuantity,
                causale: causale,
                lotto: lotto,
                magazzinoId: magazzinoId,
                scadenza: scadenza
            }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(data) {
                if (data.success) {
                    // Prepara un messaggio più dettagliato
                    let message = `Rettifica completata con successo!\n`;
                    if (differenza > 0) {
                        message += `È stato registrato un movimento di carico di ${differenza.toFixed(2)} unità.`;
                    } else if (differenza < 0) {
                        message += `È stato registrato un movimento di scarico di ${Math.abs(differenza).toFixed(2)} unità.`;
                    } else {
                        message += `Non è stata registrata alcuna differenza.`;
                    }

                    Swal.fire({
                        title: 'Successo',
                        text: message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Nascondi la modale
                        let modal = bootstrap.Modal.getInstance($('#rettificaModal'));
                        modal.hide();

                        // Reset dei campi
                        $('#articleTable').hide();
                        $('#articleDetails').html('');
                        $('#barcodeInput').val('').focus();

                        // Ricarica la pagina per aggiornare le giacenze
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    });
                } else {
                    Swal.fire({
                        title: 'Errore',
                        text: data.message || 'Errore nella rettifica giacenza',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore AJAX:', error);
                Swal.fire({
                    title: 'Errore',
                    text: 'Si è verificato un errore durante l\'operazione',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>