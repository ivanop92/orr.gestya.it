@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- Titolo pagina -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestione Listini Prezzi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Gestione</a></li>
                            <li class="breadcrumb-item active">Listini Prezzi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messaggi di feedback -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-double-line me-1 align-middle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-1 align-middle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card principale -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Lista Listini Prezzi</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAggiungiListino">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Nuovo Listino
                                    </button>
                                    <button class="btn btn-soft-success" data-bs-toggle="modal" data-bs-target="#modalImportaPrezzi">
                                        <i class="ri-upload-2-line me-1 align-bottom"></i>Importa Prezzi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="listini-table" class="table table-bordered table-hover dt-responsive nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th>Validità</th>
                                <th>Stato</th>
                                <th>Priorità</th>
                                <th>Articoli</th>
                                <th>Clienti</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($listini as $listino)
                                <tr>
                                    <td>{{ $listino->codice }}</td>
                                    <td>{{ $listino->descrizione }}</td>
                                    <td>
                                        @if($listino->data_inizio && $listino->data_fine)
                                            Dal {{ date('d/m/Y', strtotime($listino->data_inizio)) }}
                                            al {{ date('d/m/Y', strtotime($listino->data_fine)) }}
                                        @elseif($listino->data_inizio)
                                            Dal {{ date('d/m/Y', strtotime($listino->data_inizio)) }}
                                        @elseif($listino->data_fine)
                                            Fino al {{ date('d/m/Y', strtotime($listino->data_fine)) }}
                                        @else
                                            Sempre valido
                                        @endif
                                    </td>
                                    <td>
                                        @if($listino->attivo)
                                            <span class="badge bg-success">Attivo</span>
                                        @else
                                            <span class="badge bg-danger">Disattivato</span>
                                        @endif
                                    </td>
                                    <td>{{ $listino->priorita }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-soft-info" onclick="mostraArticoliListino({{ $listino->id }})">
                                            {{ $listino->num_articoli }} articoli
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-soft-info" onclick="mostraClientiListino({{ $listino->id }})">
                                            {{ $listino->num_clienti }} clienti
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-primary" onclick="modificaListino({{ $listino->id }}, '{{ $listino->codice }}', '{{ $listino->descrizione }}', '{{ $listino->data_inizio }}', '{{ $listino->data_fine }}', {{ $listino->priorita }}, {{ $listino->attivo }}, '{{ $listino->note }}')">
                                                <i class="ri-edit-2-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="aggiungiArticoloListino({{ $listino->id }})">
                                                <i class="ri-price-tag-3-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="aggiungiClienteListino({{ $listino->id }})">
                                                <i class="ri-user-add-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="confermaEliminaListino({{ $listino->id }})">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
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

<!-- Modal Aggiungi Listino -->
<div class="modal fade" id="modalAggiungiListino" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aggiungi Nuovo Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="codice" class="form-label">Codice Listino <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="codice" name="codice" required>
                        </div>
                        <div class="col-md-6">
                            <label for="descrizione" class="form-label">Descrizione <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="descrizione" name="descrizione" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data_inizio" class="form-label">Data Inizio Validità</label>
                            <input type="date" class="form-control" id="data_inizio" name="data_inizio">
                        </div>
                        <div class="col-md-6">
                            <label for="data_fine" class="form-label">Data Fine Validità</label>
                            <input type="date" class="form-control" id="data_fine" name="data_fine">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="priorita" class="form-label">Priorità</label>
                            <input type="number" class="form-control" id="priorita" name="priorita" min="0" value="0">
                            <small class="text-muted">Priorità più alta verrà considerata per prima</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch form-switch-lg mt-4">
                                <input class="form-check-input" type="checkbox" id="attivo" name="attivo" checked>
                                <label class="form-check-label" for="attivo">Listino Attivo</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_listino" value="1" class="btn btn-primary">Salva Listino</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Listino -->
<div class="modal fade" id="modalModificaListino" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifica Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="mod_id" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mod_codice" class="form-label">Codice Listino <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mod_codice" name="codice" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mod_descrizione" class="form-label">Descrizione <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mod_descrizione" name="descrizione" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mod_data_inizio" class="form-label">Data Inizio Validità</label>
                            <input type="date" class="form-control" id="mod_data_inizio" name="data_inizio">
                        </div>
                        <div class="col-md-6">
                            <label for="mod_data_fine" class="form-label">Data Fine Validità</label>
                            <input type="date" class="form-control" id="mod_data_fine" name="data_fine">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mod_priorita" class="form-label">Priorità</label>
                            <input type="number" class="form-control" id="mod_priorita" name="priorita" min="0" value="0">
                            <small class="text-muted">Priorità più alta verrà considerata per prima</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch form-switch-lg mt-4">
                                <input class="form-check-input" type="checkbox" id="mod_attivo" name="attivo">
                                <label class="form-check-label" for="mod_attivo">Listino Attivo</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="mod_note" class="form-label">Note</label>
                            <textarea class="form-control" id="mod_note" name="note" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="modifica_listino" class="btn btn-primary">Aggiorna Listino</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Articolo a Listino -->
<div class="modal fade" id="modalAggiungiArticolo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aggiungi Articolo al Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="art_id_listino" name="id_listino">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="id_articolo" class="form-label">Articolo <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="id_articolo" name="id_articolo" required>
                                <option value="">Seleziona Articolo</option>
                                @foreach($articoli as $articolo)
                                    <option value="{{ $articolo->id }}" data-prezzo="{{ $articolo->prezzo }}">
                                        {{ $articolo->codice_articolo }} - {{ $articolo->titolo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="prezzo_base" class="form-label">Prezzo Base</label>
                            <input type="text" class="form-control" id="prezzo_base" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="prezzo" class="form-label">Prezzo Listino <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="prezzo" name="prezzo" required>
                        </div>
                        <div class="col-md-4">
                            <label for="sconto_percentuale" class="form-label">Sconto %</label>
                            <input type="number" step="0.01" class="form-control" id="sconto_percentuale" name="sconto_percentuale" value="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="quantita_minima" class="form-label">Quantità Min.</label>
                            <input type="number" class="form-control" id="quantita_minima" name="quantita_minima" value="1" min="1">
                        </div>
                        <div class="col-md-4">
                            <label for="art_data_inizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="art_data_inizio" name="data_inizio">
                        </div>
                        <div class="col-md-4">
                            <label for="art_data_fine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="art_data_fine" name="data_fine">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_articolo" class="btn btn-primary">Aggiungi Articolo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Cliente a Listino -->
<div class="modal fade" id="modalAggiungiCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Associa Cliente al Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="cli_id_listino" name="id_listino">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="id_cliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="id_cliente" name="id_cliente" required>
                                <option value="">Seleziona Cliente</option>
                                @foreach($clienti as $cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ $cliente->ragione_sociale ?: $cliente->nome.' '.$cliente->cognome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cli_data_inizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="cli_data_inizio" name="data_inizio">
                        </div>
                        <div class="col-md-6">
                            <label for="cli_data_fine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="cli_data_fine" name="data_fine">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="cli_note" class="form-label">Note</label>
                            <textarea class="form-control" id="cli_note" name="note" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_cliente" class="btn btn-primary">Associa Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Importa Prezzi -->
<div class="modal fade" id="modalImportaPrezzi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importa Prezzi da CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <p class="mb-1">Il file CSV deve contenere le seguenti colonne:</p>
                        <ul class="mb-0">
                            <li>codice_articolo (obbligatorio)</li>
                            <li>prezzo (obbligatorio)</li>
                            <li>sconto_percentuale (opzionale)</li>
                            <li>quantita_minima (opzionale)</li>
                        </ul>
                        <div class="mt-2">
                            <a href="{{ url('utente/listini/esempio_csv') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-download-2-line me-1"></i> Scarica esempio CSV
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="id_listino" class="form-label">Seleziona Listino <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_listino" name="id_listino" required>
                                <option value="">Seleziona Listino</option>
                                @foreach($listini as $listino)
                                    <option value="{{ $listino->id }}">{{ $listino->codice }} - {{ $listino->descrizione }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="file_csv" class="form-label">File CSV <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file_csv" name="file_csv" accept=".csv" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="importa_csv" class="btn btn-primary">Importa Prezzi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Dettaglio Articoli Listino -->
<div class="modal fade" id="modalArticoliListino" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Articoli nel Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center p-4" id="loadingArticoli">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                    <p class="mt-2">Caricamento articoli...</p>
                </div>
                <div id="contentArticoli" style="display: none;">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Articolo</th>
                            <th>Prezzo Base</th>
                            <th>Prezzo Listino</th>
                            <th>Sconto %</th>
                            <th>Qta. Min.</th>
                            <th>Validità</th>
                            <th>Azioni</th>
                        </tr>
                        </thead>
                        <tbody id="bodyArticoliListino">
                        <!-- Qui verranno caricati gli articoli via Ajax -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" onclick="aggiungiNuovoArticolo()">Aggiungi Articolo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dettaglio Clienti Listino -->
<div class="modal fade" id="modalClientiListino" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clienti Associati al Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center p-4" id="loadingClienti">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                    <p class="mt-2">Caricamento clienti...</p>
                </div>
                <div id="contentClienti" style="display: none;">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Validità</th>
                            <th>Note</th>
                            <th>Azioni</th>
                        </tr>
                        </thead>
                        <tbody id="bodyClientiListino">
                        <!-- Qui verranno caricati i clienti via Ajax -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" onclick="aggiungiNuovoCliente()">Aggiungi Cliente</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Elimina Listino -->
<div class="modal fade" id="modalEliminaListino" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Conferma Eliminazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questo listino? L'operazione non può essere annullata.</p>
                <p class="text-danger">Nota: non è possibile eliminare un listino che ha articoli o clienti associati.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                <form method="post" action="{{ url('utente/listini') }}">
                    @csrf
                    <input type="hidden" id="del_id" name="id">
                    <button type="submit" name="elimina_listino" class="btn btn-danger">Elimina</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    var listiniTable;

    $(document).ready(function() {
        // Inizializza la DataTable
        listiniTable = $('#listini-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json',
            }
        });

        // Inizializza Select2
        $('.select2').select2({
            dropdownParent: $('.modal'),
            width: '100%'
        });

        // Gestione del prezzo base e prezzo listino
        $('#id_articolo').on('change', function() {
            var option = $(this).find('option:selected');
            var prezzo = option.data('prezzo') || 0;
            $('#prezzo_base').val(prezzo.toFixed(2));
            $('#prezzo').val(prezzo.toFixed(2));
        });

        // Calcolo del prezzo in base allo sconto
        $('#sconto_percentuale').on('input', function() {
            var prezzoBase = parseFloat($('#prezzo_base').val() || 0);
            var sconto = parseFloat($(this).val() || 0);

            if (prezzoBase > 0 && sconto > 0) {
                var prezzoScontato = prezzoBase * (1 - sconto / 100);
                $('#prezzo').val(prezzoScontato.toFixed(2));
            }
        });
    });

    // Funzione per aprire il modal di modifica listino
    function modificaListino(id, codice, descrizione, dataInizio, dataFine, priorita, attivo, note) {
        $('#mod_id').val(id);
        $('#mod_codice').val(codice);
        $('#mod_descrizione').val(descrizione);
        $('#mod_data_inizio').val(dataInizio);
        $('#mod_data_fine').val(dataFine);
        $('#mod_priorita').val(priorita);
        $('#mod_attivo').prop('checked', attivo == 1);
        $('#mod_note').val(note);

        $('#modalModificaListino').modal('show');
    }

    // Funzione per aprire il modal di aggiunta articolo
    function aggiungiArticoloListino(idListino) {
        $('#art_id_listino').val(idListino);
        $('#modalAggiungiArticolo').modal('show');
    }

    // Funzione per aprire il modal di aggiunta cliente
    function aggiungiClienteListino(idListino) {
        $('#cli_id_listino').val(idListino);
        $('#modalAggiungiCliente').modal('show');
    }

    // Funzione per confermare l'eliminazione di un listino
    function confermaEliminaListino(id) {
        $('#del_id').val(id);
        $('#modalEliminaListino').modal('show');
    }

    // Funzione per mostrare gli articoli di un listino
    function mostraArticoliListino(idListino) {
        $('#loadingArticoli').show();
        $('#contentArticoli').hide();
        $('#bodyArticoliListino').empty();
        $('#modalArticoliListino').modal('show');

        // Simuliamo il caricamento (in produzione qui ci sarebbe una chiamata AJAX)
        setTimeout(function() {
            $('#loadingArticoli').hide();
            $('#contentArticoli').show();

            // In produzione, qui popolerai la tabella con i dati ricevuti dall'AJAX
            // Questo è solo un esempio di come potrebbe apparire la tabella
            var html = '';

            // Se non ci sono articoli
            if (true) {
                html = '<tr><td colspan="8" class="text-center">Nessun articolo associato a questo listino</td></tr>';
            }

            $('#bodyArticoliListino').html(html);

        }, 1000);
    }

    // Funzione per mostrare i clienti di un listino
    function mostraClientiListino(idListino) {
        $('#loadingClienti').show();
        $('#contentClienti').hide();
        $('#bodyClientiListino').empty();
        $('#modalClientiListino').modal('show');

        // Simuliamo il caricamento (in produzione qui ci sarebbe una chiamata AJAX)
        setTimeout(function() {
            $('#loadingClienti').hide();
            $('#contentClienti').show();

            // In produzione, qui popolerai la tabella con i dati ricevuti dall'AJAX
            // Questo è solo un esempio di come potrebbe apparire la tabella
            var html = '';

            // Se non ci sono clienti
            if (true) {
                html = '<tr><td colspan="4" class="text-center">Nessun cliente associato a questo listino</td></tr>';
            }

            $('#bodyClientiListino').html(html);

        }, 1000);
    }

    // Funzione per aprire il modal di aggiunta articolo durante la visualizzazione degli articoli
    function aggiungiNuovoArticolo() {
        // Chiudi il modal corrente
        $('#modalArticoliListino').modal('hide');

        // Apri il modal di aggiunta articolo
        setTimeout(function() {
            // Recupera l'ID listino dal modal corrente
            var idListino = $('#art_id_listino').val();
            aggiungiArticoloListino(idListino);
        }, 500);
    }

    // Funzione per aprire il modal di aggiunta cliente durante la visualizzazione dei clienti
    function aggiungiNuovoCliente() {
        // Chiudi il modal corrente
        $('#modalClientiListino').modal('hide');

        // Apri il modal di aggiunta cliente
        setTimeout(function() {
            // Recupera l'ID listino dal modal corrente
            var idListino = $('#cli_id_listino').val();
            aggiungiClienteListino(idListino);
        }, 500);
    }
</script>