@extends('produzione.layout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dettaglio Attività</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ url('produzione/dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Dettaglio Attività</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <!-- Notifiche e messaggi di sistema -->
                @if(session('success'))
                    <div class="col-12 mb-3">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="col-12 mb-3">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                <!-- Card informazioni commessa -->
                <div class="col-xl-12 mb-4">
                    <div class="card overflow-hidden border-primary">
                        <div class="card-header bg-primary bg-gradient">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm rounded-circle bg-white text-primary me-3">
                                        <i class="ri-briefcase-4-line fs-20"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0 text-white">Informazioni Commessa</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body position-relative">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 bg-soft-primary rounded">
                                            <i class="ri-hashtag fs-20 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Codice Commessa</h6>
                                            <p class="text-muted mb-0 fs-13">{{ $attivita->codice_commessa }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 bg-soft-primary rounded">
                                            <i class="ri-file-text-line fs-20 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Descrizione Commessa</h6>
                                            <p class="text-muted mb-0 fs-13">{{ $attivita->commessa_descrizione }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card dettaglio attività -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header bg-soft-info">
                            <h5 class="card-title mb-0">Dettaglio Attività</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                    <tr>
                                        <th class="ps-0" width="200">Titolo:</th>
                                        <td>{{ $attivita->titolo }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Descrizione:</th>
                                        <td>{{ $attivita->descrizione ?: 'Nessuna descrizione disponibile' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Priorità:</th>
                                        <td>
                                            @if($attivita->priorita == 'alta')
                                                <span class="badge bg-danger">Alta</span>
                                            @elseif($attivita->priorita == 'media')
                                                <span class="badge bg-warning">Media</span>
                                            @else
                                                <span class="badge bg-info">Bassa</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Stato:</th>
                                        <td>
                                            @if($attivita->stato == 'da_iniziare')
                                                <span class="badge bg-warning">Da iniziare</span>
                                            @elseif($attivita->stato == 'in_corso')
                                                <span class="badge bg-info">In corso</span>
                                            @elseif($attivita->stato == 'completata')
                                                <span class="badge bg-success">Completata</span>
                                            @else
                                                <span class="badge bg-danger">In ritardo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Data Inizio Prevista:</th>
                                        <td>{{ $attivita->data_inizio ? date('d/m/Y', strtotime($attivita->data_inizio)) : 'Non definita' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Data Fine Prevista:</th>
                                        <td>
                                            @if($attivita->data_fine)
                                                @php
                                                    $giorni_rimasti = (strtotime($attivita->data_fine) - time()) / (60 * 60 * 24);
                                                @endphp

                                                {{ date('d/m/Y', strtotime($attivita->data_fine)) }}

                                                @if($giorni_rimasti < 0)
                                                    <span class="badge bg-danger ms-1">Scaduta da {{ abs(ceil($giorni_rimasti)) }} giorni</span>
                                                @elseif($giorni_rimasti <= 3)
                                                    <span class="badge bg-warning ms-1">Scade fra {{ ceil($giorni_rimasti) }} giorni</span>
                                                @endif
                                            @else
                                                Non definita
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Data Inizio Effettiva:</th>
                                        <td>
                                            @if($attivita->data_inizio_effettiva)
                                                {{ date('d/m/Y H:i', strtotime($attivita->data_inizio_effettiva)) }}
                                            @else
                                                <span class="text-warning">Non ancora iniziata</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Data Fine Effettiva:</th>
                                        <td>
                                            @if($attivita->data_fine_effettiva)
                                                {{ date('d/m/Y H:i', strtotime($attivita->data_fine_effettiva)) }}
                                            @else
                                                <span class="text-warning">Non ancora completata</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Responsabile:</th>
                                        <td>
                                            @if(isset($attivita->nome_responsabile))
                                                {{ $attivita->nome_responsabile }} {{ $attivita->cognome_responsabile }}
                                            @else
                                                Non assegnato
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Note:</th>
                                        <td>{!! nl2br(e($attivita->note ?: 'Nessuna nota disponibile')) !!}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Completamento:</th>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar {{ $attivita->completamento == 100 ? 'bg-success' : 'bg-info' }}" role="progressbar"
                                                         style="width: {{ $attivita->completamento }}%;"
                                                         aria-valuenow="{{ $attivita->completamento }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <span class="ms-2">{{ $attivita->completamento }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Card allegati -->
                    <div class="card mt-4">
                        <div class="card-header bg-soft-secondary d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Allegati e Foto</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAllegatoModal">
                                <i class="ri-add-line me-1 align-middle"></i>Aggiungi allegato
                            </button>
                        </div>
                        <div class="card-body">
                            @if(isset($allegati) && count($allegati) > 0)
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap table-striped">
                                        <thead class="table-light">
                                        <tr>
                                            <th width="60">Tipo</th>
                                            <th>Descrizione</th>
                                            <th>Nome file</th>
                                            <th>Data</th>
                                            <th width="100">Azioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($allegati as $allegato)
                                            <tr>
                                                <td class="text-center">
                                                    @if(strpos($allegato->tipo_file, 'image') !== false)
                                                        <i class="ri-image-line fs-22 text-info"></i>
                                                    @elseif(strpos($allegato->tipo_file, 'pdf') !== false)
                                                        <i class="ri-file-pdf-line fs-22 text-danger"></i>
                                                    @elseif(strpos($allegato->tipo_file, 'word') !== false || strpos($allegato->tipo_file, 'msword') !== false)
                                                        <i class="ri-file-word-line fs-22 text-primary"></i>
                                                    @elseif(strpos($allegato->tipo_file, 'excel') !== false || strpos($allegato->tipo_file, 'spreadsheet') !== false)
                                                        <i class="ri-file-excel-line fs-22 text-success"></i>
                                                    @else
                                                        <i class="ri-file-line fs-22 text-secondary"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $allegato->descrizione }}</td>
                                                <td>{{ $allegato->nome_originale }}</td>
                                                <td>{{ date('d/m/Y H:i', strtotime($allegato->created_at)) }}</td>
                                                <td>
                                                    <div class="hstack gap-2">
                                                        <a href="{{ asset($allegato->path_file) }}" target="_blank" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Visualizza">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ url('produzione/elimina_allegato/'.$allegato->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo allegato?')" data-bs-toggle="tooltip" title="Elimina">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center p-4 border rounded">
                                    <div class="avatar-md mx-auto mb-4">
                                        <div class="avatar-title bg-light text-secondary rounded-circle">
                                            <i class="ri-file-text-line fs-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Nessun allegato disponibile</h5>
                                    <p class="text-muted mb-0">Clicca su "Aggiungi allegato" per caricare documenti o foto relative a questa attività.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Galleria foto -->
                    @if(isset($allegati) && count($allegati->where('tipo', 'foto')) > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-soft-info">
                                <h5 class="card-title mb-0">Galleria foto</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($allegati->where('tipo', 'foto') as $foto)
                                        @if(strpos($foto->tipo_file, 'image') !== false)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="card mb-1">
                                                    <a href="{{ asset($foto->path_file) }}" data-lightbox="galleria-attivita" data-title="{{ $foto->descrizione }}">
                                                        <img src="{{ asset($foto->path_file) }}" class="card-img-top img-fluid" alt="{{ $foto->descrizione }}">
                                                    </a>
                                                    <div class="card-body p-2">
                                                        <p class="card-text small">{{ $foto->descrizione }}</p>
                                                        <p class="card-text small text-muted">{{ date('d/m/Y', strtotime($foto->created_at)) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Destra -->
                <div class="col-xl-4">
                    <!-- Card per avvio e completamento attività -->
                    <div class="card">
                        <div class="card-header bg-soft-primary">
                            <h5 class="card-title mb-0">Gestione Attività</h5>
                        </div>
                        <div class="card-body">
                            @if(!$attivita->data_inizio_effettiva && $attivita->stato != 'completata')
                                <!-- Bottone per avviare l'attività -->
                                <form action="{{ url('produzione/start_attivita/'.$attivita->id) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="ri-play-circle-line me-1 align-middle"></i> Avvia Attività
                                    </button>
                                </form>
                            @elseif($attivita->data_inizio_effettiva && !$attivita->data_fine_effettiva)
                                <!-- Se l'attività è iniziata ma non completata -->
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-time-line fs-3 me-2"></i>
                                        <div>
                                            <strong>Attività in corso</strong><br>
                                            <small>Iniziata il {{ date('d/m/Y H:i', strtotime($attivita->data_inizio_effettiva)) }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form per completare l'attività -->
                                <form action="{{ url('produzione/fine_attivita/'.$attivita->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note di chiusura</label>
                                        <textarea name="note" id="note" rows="3" class="form-control" placeholder="Inserisci eventuali note di chiusura">{{ $attivita->note }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-lg w-100">
                                        <i class="ri-stop-circle-line me-1 align-middle"></i> Completa Attività
                                    </button>
                                </form>
                            @else
                                <!-- Attività già completata -->
                                <div class="alert alert-success mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-check-double-line fs-3 me-2"></i>
                                        <div>
                                            <strong>Attività completata</strong><br>
                                            <small>Completata il {{ date('d/m/Y H:i', strtotime($attivita->data_fine_effettiva)) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card per aggiornamento note e percentuale completamento -->
                    @if($attivita->data_inizio_effettiva && !$attivita->data_fine_effettiva)
                        <div class="card mt-4">
                            <div class="card-header bg-soft-info">
                                <h5 class="card-title mb-0">Avanzamento Attività</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('produzione/aggiorna_attivita/'.$attivita->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="completamento" class="form-label">Percentuale completamento</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="completamento" name="completamento"
                                                   value="{{ $attivita->completamento }}" min="0" max="99" step="5" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">Per completare l'attività al 100%, usa il pulsante "Completa Attività"</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note di avanzamento</label>
                                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="Inserisci eventuali note di avanzamento">{{ $attivita->note }}</textarea>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-info">
                                            <i class="ri-save-line me-1 align-middle"></i> Aggiorna Avanzamento
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Azioni rapide -->
                    <div class="card mt-4">
                        <div class="card-header bg-soft-secondary">
                            <h5 class="card-title mb-0">Azioni Rapide</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ url('produzione/dashboard') }}" class="btn btn-outline-secondary w-100">
                                <i class="ri-arrow-left-line me-1 align-middle"></i> Torna alla dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per caricamento allegati -->
    <div class="modal fade" id="uploadAllegatoModal" tabindex="-1" aria-labelledby="uploadAllegatoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadAllegatoModalLabel">Carica nuovo allegato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('produzione/carica_allegato/' . $attivita->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="allegato" class="form-label">Seleziona file</label>
                            <input type="file" class="form-control" id="allegato" name="allegato" required>
                            <div class="form-text">Formati supportati: immagini, PDF, documenti Word/Excel, file di testo</div>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo di allegato</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="documento">Documento</option>
                                <option value="foto">Foto</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" id="descrizione" name="descrizione" placeholder="Descrivi brevemente il contenuto del file">
                        </div>
                        <div class="mb-3">
                            <label for="nota_allegato" class="form-label">Nota da aggiungere all'attività</label>
                            <textarea class="form-control" id="nota_allegato" name="nota_allegato" rows="2" placeholder="Inserisci una nota relativa a questo allegato (opzionale)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Carica</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@include('utente.common.footer')
    <!-- Lightbox per la visualizzazione delle immagini -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inizializza i tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inizializza lightbox
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': "Immagine %1 di %2"
            });
        });
    </script>
