@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dettaglio Listino Prezzi</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini') }}">Listini Prezzi</a></li>
                            <li class="breadcrumb-item active">Dettaglio</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

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

        <!-- Scheda info listino -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Informazioni Listino</h5>
                            <div class="flex-shrink-0">
                                <div class="dropdown">
                                    <a href="{{ url('utente/listini') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line align-bottom me-1"></i> Torna ai Listini
                                    </a>
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modificaListinoModal">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Elimina
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Codice</h6>
                                    <p class="text-muted">{{ $listino->codice }}</p>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Descrizione</h6>
                                    <p class="text-muted">{{ $listino->descrizione }}</p>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Stato</h6>
                                    <p>
                                        @if($listino->attivo)
                                            <span class="badge bg-success">Attivo</span>
                                        @else
                                            <span class="badge bg-danger">Disattivato</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Priorità</h6>
                                    <p class="text-muted">{{ $listino->priorita }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Validità</h6>
                                    <p class="text-muted">
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
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <h6 class="fw-semibold">Note</h6>
                                    <p class="text-muted">{{ $listino->note ?? 'Nessuna nota' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab articoli e clienti -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#articoli-tab" role="tab">
                                    <i class="ri-price-tag-3-line me-1 align-bottom"></i> Articoli <span class="badge rounded-pill bg-primary">{{ count($articoli_listino) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#clienti-tab" role="tab">
                                    <i class="ri-user-2-line me-1 align-bottom"></i> Clienti <span class="badge rounded-pill bg-primary">{{ count($clienti_listino) }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Articoli -->
                            <div class="tab-pane active" id="articoli-tab" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Articoli nel Listino</h5>
                                    <div class="flex-shrink-0">
                                        <a href="{{ url('utente/listini/aggiungi_articolo/'.$listino->id) }}" class="btn btn-primary">
                                            <i class="ri-add-line align-bottom me-1"></i> Aggiungi Articolo
                                        </a>
                                        <a href="{{ url('utente/listini/importa_csv/'.$listino->id) }}" class="btn btn-soft-success">
                                            <i class="ri-upload-2-line align-bottom me-1"></i> Importa da CSV
                                        </a>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="articoli-table" class="table table-bordered dt-responsive nowrap table-striped align-middle">
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
                                        <tbody>
                                        @if(count($articoli_listino) > 0)
                                            @foreach($articoli_listino as $articolo)
                                                <tr>
                                                    <td>{{ $articolo->codice_articolo }}</td>
                                                    <td>{{ $articolo->nome_articolo }}</td>
                                                    <td>&euro; {{ number_format($articolo->prezzo_base, 2, ',', '.') }}</td>
                                                    <td>&euro; {{ number_format($articolo->prezzo, 2, ',', '.') }}</td>
                                                    <td>{{ number_format($articolo->sconto_percentuale, 2) }}%</td>
                                                    <td>{{ $articolo->quantita_minima }}</td>
                                                    <td>
                                                        @if($articolo->data_inizio && $articolo->data_fine)
                                                            {{ date('d/m/Y', strtotime($articolo->data_inizio)) }} - {{ date('d/m/Y', strtotime($articolo->data_fine)) }}
                                                        @elseif($articolo->data_inizio)
                                                            Dal {{ date('d/m/Y', strtotime($articolo->data_inizio)) }}
                                                        @elseif($articolo->data_fine)
                                                            Fino al {{ date('d/m/Y', strtotime($articolo->data_fine)) }}
                                                        @else
                                                            Sempre valido
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a href="#" class="dropdown-item edit-articolo"
                                                                       data-id="{{ $articolo->id }}"
                                                                       data-idarticolo="{{ $articolo->id_articolo }}"
                                                                       data-prezzo="{{ $articolo->prezzo }}"
                                                                       data-sconto="{{ $articolo->sconto_percentuale }}"
                                                                       data-qta="{{ $articolo->quantita_minima }}"
                                                                       data-datainizio="{{ $articolo->data_inizio }}"
                                                                       data-datafine="{{ $articolo->data_fine }}">
                                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ url('utente/listini/elimina_articolo/'.$listino->id.'/'.$articolo->id) }}" class="dropdown-item remove-item-btn" onclick="return confirm('Sei sicuro di voler rimuovere questo articolo dal listino?')">
                                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Rimuovi
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">Nessun articolo associato a questo listino</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Clienti -->
                            <div class="tab-pane" id="clienti-tab" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Clienti Associati</h5>
                                    <div class="flex-shrink-0">
                                        <a href="{{ url('utente/listini/aggiungi_cliente/'.$listino->id) }}" class="btn btn-primary">
                                            <i class="ri-add-line align-bottom me-1"></i> Associa Cliente
                                        </a>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="clienti-table" class="table table-bordered dt-responsive nowrap table-striped align-middle w-100">
                                        <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>P. IVA / C.F.</th>
                                            <th>Città</th>
                                            <th>Validità</th>
                                            <th>Note</th>
                                            <th>Azioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($clienti_listino) > 0)
                                            @foreach($clienti_listino as $cliente)
                                                <tr>
                                                    <td>{{ $cliente->ragione_sociale ?: $cliente->nome.' '.$cliente->cognome }}</td>
                                                    <td>{{ $cliente->piva }}</td>
                                                    <td>{{ $cliente->comune }}</td>
                                                    <td>
                                                        @if($cliente->data_inizio && $cliente->data_fine)
                                                            {{ date('d/m/Y', strtotime($cliente->data_inizio)) }} - {{ date('d/m/Y', strtotime($cliente->data_fine)) }}
                                                        @elseif($cliente->data_inizio)
                                                            Dal {{ date('d/m/Y', strtotime($cliente->data_inizio)) }}
                                                        @elseif($cliente->data_fine)
                                                            Fino al {{ date('d/m/Y', strtotime($cliente->data_fine)) }}
                                                        @else
                                                            Sempre valido
                                                        @endif
                                                    </td>
                                                    <td>{{ $cliente->note }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a href="#" class="dropdown-item edit-cliente"
                                                                       data-id="{{ $cliente->id }}"
                                                                       data-idcliente="{{ $cliente->id_cliente }}"
                                                                       data-datainizio="{{ $cliente->data_inizio }}"
                                                                       data-datafine="{{ $cliente->data_fine }}"
                                                                       data-note="{{ $cliente->note }}">
                                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ url('utente/listini/elimina_cliente/'.$listino->id.'/'.$cliente->id) }}" class="dropdown-item remove-item-btn" onclick="return confirm('Sei sicuro di voler rimuovere questo cliente dal listino?')">
                                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Rimuovi
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">Nessun cliente associato a questo listino</td>
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
    </div>
</div>

<!-- Modal Modifica Listino -->
<div class="modal fade" id="modificaListinoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{ $listino->id }}">

                    <div class="mb-3">
                        <label for="codice" class="form-label">Codice</label>
                        <input type="text" id="codice" name="codice" class="form-control" value="{{ $listino->codice }}" required />
                    </div>

                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <input type="text" id="descrizione" name="descrizione" class="form-control" value="{{ $listino->descrizione }}" required />
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="data_inizio" class="form-label">Data Inizio</label>
                            <input type="date" id="data_inizio" name="data_inizio" class="form-control" value="{{ $listino->data_inizio }}" />
                        </div>
                        <div class="col-lg-6">
                            <label for="data_fine" class="form-label">Data Fine</label>
                            <input type="date" id="data_fine" name="data_fine" class="form-control" value="{{ $listino->data_fine }}" />
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="priorita" class="form-label">Priorità</label>
                            <input type="number" id="priorita" name="priorita" class="form-control" min="0" value="{{ $listino->priorita }}" />
                            <div class="form-text">Valore più alto = priorità maggiore</div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-check form-switch form-switch-success mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="attivo" name="attivo" {{ $listino->attivo ? 'checked' : '' }}>
                                <label class="form-check-label" for="attivo">Listino Attivo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="3">{{ $listino->note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary" name="modifica" value="1">Salva modifiche</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Elimina -->
<div class="modal fade zoomIn" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Conferma Eliminazione</h4>
                        <p class="text-muted mx-4 mb-0">Sei sicuro di voler eliminare questo listino?</p>
                        <p class="text-danger mx-4 mb-0">Non è possibile eliminare listini con articoli o clienti associati.</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <form method="post" action="{{ url('utente/listini') }}">
                        <input type="hidden" name="id" value="{{ $listino->id }}">
                        <button type="submit" class="btn w-sm btn-danger" name="elimina" value="1">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        // Inizializza DataTables
        $('#articoli-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json'
            },
            responsive: true
        });

        $('#clienti-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json'
            },
            responsive: true
        });

        // Attiva la tab selezionata al caricamento della pagina
        var activeTab = window.location.hash;
        if (activeTab) {
            $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        }

        // Aggiorna l'hash nell'URL quando si cambia tab
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>