# Modifiche alla View Scarico Magazzino

Ecco le modifiche da apportare alla view `resources/views/utente/scarico.blade.php` per supportare la scansione sia del barcode dell'articolo che del barcode della giacenza.

```php
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
                    <h4 class="mb-sm-0">Scarico da Magazzino</h4>
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
                                <a class="nav-link {{ $usa_barcode ? '' : 'active' }}" data-bs-toggle="tab" href="#tab-articoli" role="tab">
                                    <i class="ri-list-check me-1 align-middle"></i> Lista Articoli
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

                                        <!-- Messaggio informativo per l'utente -->
                                        <div class="alert alert-info mb-4">
                                            <i class="ri-information-line me-2"></i>
                                            Puoi scansionare sia il barcode dell'articolo per vedere tutti i lotti disponibili, sia il barcode specifico della giacenza per uno scarico diretto.
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
                            @endif

                            <!-- Tab Lista Articoli -->
                            <div class="tab-pane {{ $usa_barcode ? '' : 'active' }}" id="tab-articoli" role="tabpanel">
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
                                                    <button class="btn btn-sm btn-primary" onclick="checkScaricoDisponibilita({{ $articolo->id }}, '{{ $articolo->titolo }}')">
                                                        <i class="ri-subtract-line"></i> Scarica
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
            <div class="modal-header bg-soft-primary">
                <h5 class="modal-title" id="lottoModalLabel">{{ $usa_lotti ? 'Seleziona Lotto e Magazzino' : 'Seleziona Magazzino' }}</h5>
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
                            <th>Giacenza Disponibile</th>
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

<!-- Modale per lo scarico a magazzino -->
<div class="modal fade" id="scaricoModal" tabindex="-1" aria-labelledby="scaricoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-primary">
                <h5 class="modal-title" id="scaricoModalLabel">Scarico da Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="lotto_selezionato" class="alert alert-info mb-3"></div>
                <form id="scaricoForm">
                    <input type="hidden" id="articolo_id">
                    <input type="hidden" id="magazzino_id">
                    <input type="hidden" id="lotto_id">
                    <input type="hidden" id="scadenza_lotto">
                    <input type="hidden" id="barcode_giacenza">

                    <div class="mb-3">
                        <label for="removeQuantity" class="form-label">Quantità da Scaricare</label>
                        <div class="input-group">
                            <input type="number" step="0.01" id="removeQuantity" class="form-control" required>
                            <span class="input-group-text" id="um_display">PZ</span>
                        </div>
                        <div class="form-text text-muted">Giacenza disponibile: <span id="giacenza_disponibile">0</span> <span id="um_disponibile">PZ</span></div>
                    </div>

                    <div class="mb-3">
                        <label for="commessa" class="form-label">Commessa</label>
                        <select id="commessa" class="form-select">
                            <option value="">Nessuna Commessa</option>
                            @foreach ($commesse as $commessa)
                                <option value="{{ $commessa->id }}">{{ $commessa->codice_commessa }} - {{ $commessa->descrizione }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="causale" class="form-label">Causale</label>
                        <input type="text" id="causale" class="form-control" value="Scarico manuale" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitScarico()">Conferma Scarico</button>
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
    let disponibilitaArticolo = [];

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

        // Configura lo scanner barcode
        setupBarcodeScanner();
    });

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
        fetch(`/utente/controllo-articolo-scarico?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayArticle(data.article);

                    // Verifica se è un barcode di giacenza specifica
                    if (data.isBarcodeGiacenza) {
                        // È un barcode di giacenza specifica, apriamo direttamente la modal di scarico
                        const giacenza = data.giacenza;

                        // Formatta la data di scadenza se presente
                        const scadenzaFormatted = giacenza.scadenza_lotto ?
                            new Date(giacenza.scadenza_lotto).toLocaleDateString() : 'N/D';

                        // Apertura diretta del modale di scarico per la giacenza specifica
                        openScaricoModal(
                            giacenza.id_articolo,
                            giacenza.id_magazzino,
                            giacenza.lotto || '',
                            giacenza.scadenza_lotto || '',
                            data.giacenzaAttuale,
                            data.article.um,
                            giacenza.magazzino_descrizione,
                            giacenza.barcode || ''
                        );
                    } else {
                        // È un barcode di articolo, verifichiamo le giacenze disponibili
                        if (data.article.giacenza > 0) {
                            checkScaricoDisponibilita(data.article.id, data.article.titolo);
                        } else {
                            Swal.fire({
                                title: 'Attenzione',
                                text: 'L\'articolo non ha giacenze disponibili',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                } else {
                    Swal.fire({
                        title: 'Attenzione',
                        text: data.message || 'Articolo non trovato',
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
                    <button class="btn btn-primary" onclick="checkScaricoDisponibilita(${article.id}, '${cleanTitle}')">
                        <i class="ri-subtract-line"></i> Scarica
                    </button>
                </td>
            </tr>
        `);
    }

    // Funzione per verificare la disponibilità per lo scarico
    function checkScaricoDisponibilita(articleId, titolo) {
        currentArticleId = articleId;

        fetch(`/utente/get-disponibilita/${articleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    disponibilitaArticolo = data.disponibilita;

                    if (disponibilitaArticolo.length === 0) {
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
                    disponibilitaArticolo.forEach(disp => {
                        const scadenzaFormatted = disp.scadenza_lotto ? new Date(disp.scadenza_lotto).toLocaleDateString() : 'N/D';
                        lottiHTML += `
                            <tr>
                                <td>${disp.magazzino_descrizione}</td>
                                <td>${disp.lotto || 'N/D'}</td>
                                <td>${scadenzaFormatted}</td>
                                <td>${disp.giacenza} ${disp.um}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="openScaricoModal(${disp.id_articolo}, ${disp.id_magazzino}, '${disp.lotto || ''}', '${disp.scadenza_lotto || ''}', ${disp.giacenza}, '${disp.um}', '${disp.magazzino_descrizione}', '${disp.barcode || ''}')">
                                        Seleziona
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

    // Funzione per aprire la modale per scaricare la giacenza
    function openScaricoModal(idArticolo, idMagazzino, lotto, scadenza, giacenza, um, magazzino, barcodeGiacenza = '') {
        // Chiudi il modale dei lotti se è aperto
        let lottoModal = document.getElementById('lottoModal');
        if (lottoModal) {
            let bsLottoModal = bootstrap.Modal.getInstance(lottoModal);
            if (bsLottoModal) {
                bsLottoModal.hide();
            }
        }

        // Imposta i dati nel form
        $('#articolo_id').val(idArticolo);
        $('#magazzino_id').val(idMagazzino);
        $('#lotto_id').val(lotto);
        $('#scadenza_lotto').val(scadenza);
        $('#barcode_giacenza').val(barcodeGiacenza);
        $('#um_display').text(um);
        $('#um_disponibile').text(um);
        $('#giacenza_disponibile').text(giacenza);

        // Informazioni visualizzate
        $('#lotto_selezionato').html(`
            <strong>Magazzino:</strong> ${magazzino}<br>
            <strong>Lotto:</strong> ${lotto || 'N/D'}<br>
            <strong>Scadenza:</strong> ${scadenza ? new Date(scadenza).toLocaleDateString() : 'N/D'}<br>
            <strong>Giacenza disponibile:</strong> ${giacenza} ${um}
            ${barcodeGiacenza ? '<br><strong>Barcode giacenza:</strong> ' + barcodeGiacenza : ''}
        `);

        // Reset del form
        $('#scaricoForm')[0].reset();

        // Imposta il valore predefinito della quantità alla giacenza disponibile
        $('#removeQuantity').val(giacenza);

        // Mostra il modale
        let scaricoModal = new bootstrap.Modal(document.getElementById('scaricoModal'));
        scaricoModal.show();
    }

    // Funzione per sottomettere lo scarico a magazzino
    function submitScarico() {
        const articleId = $('#articolo_id').val();
        const magazzinoId = $('#magazzino_id').val();
        const lotto = $('#lotto_id').val();
        const scadenza = $('#scadenza_lotto').val();
        const barcodeGiacenza = $('#barcode_giacenza').val();
        const quantity = $('#removeQuantity').val();
        const causale = $('#causale').val();
        const commessa = $('#commessa').val();
        console.log('Valore selezionato dalla select:', commessa);
        const giacenzaDisponibile = parseFloat($('#giacenza_disponibile').text());

        // Validazione
        if (!quantity || !causale) {
            Swal.fire({
                title: 'Attenzione',
                text: 'Tutti i campi obbligatori devono essere compilati',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (parseFloat(quantity) > giacenzaDisponibile) {
            Swal.fire({
                title: 'Attenzione',
                text: 'La quantità da scaricare non può essere maggiore della giacenza disponibile',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Prepara il token CSRF
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Invio dei dati al server con jQuery AJAX
        $.ajax({
            url: `/utente/scarico-magazzino/${articleId}`,
            type: 'POST',
            data: JSON.stringify({
                giacenza: quantity,
                causale: causale,
                lotto: lotto,
                magazzinoId: magazzinoId,
                scadenza: scadenza,
                id_commessa: commessa,
                barcode_giacenza: barcodeGiacenza
            }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        title: 'Successo',
                        text: 'Scarico da magazzino effettuato con successo!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Nascondi la modale
                        let modal = bootstrap.Modal.getInstance($('#scaricoModal'));
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
                        text: data.message || 'Errore nello scarico da magazzino',
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
```