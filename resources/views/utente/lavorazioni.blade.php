@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Catalogo Lavorazioni</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Produzione</a></li>
                            <li class="breadcrumb-item active">Catalogo Lavorazioni</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Template di lavorazione</h5>
                                <small class="text-muted">Macro di righe pre-confezionate da applicare a preventivi, certificati, ODL</small>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="hstack gap-2">
                                    <button class="btn btn-soft-success" data-bs-toggle="modal" data-bs-target="#modal_importa_csv_lavorazioni">
                                        <i class="ri-upload-cloud-2-line me-1"></i>Importa CSV
                                    </button>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_lavorazione">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Nuova Lavorazione
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <form method="get" class="mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cerca per codice o descrizione...">
                                        <button type="submit" class="btn btn-soft-primary">
                                            <i class="ri-search-line"></i>
                                        </button>
                                        @if(!empty($q))
                                            <a href="/utente/lavorazioni" class="btn btn-light">Annulla</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-7 text-md-end">
                                    <small class="text-muted">
                                        <strong>{{ $lavorazioni->total() }}</strong> lavorazioni totali
                                        @if($lavorazioni->total() > 0)
                                            · Pagina {{ $lavorazioni->currentPage() }} di {{ max($lavorazioni->lastPage(), 1) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered nowrap table-striped align-middle mb-0" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th class="text-end">Totale</th>
                                <th>Stato</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lavorazioni as $l)
                                <tr>
                                    <td><strong>{{ $l->codice }}</strong></td>
                                    <td>{{ $l->descrizione }}</td>
                                    <td class="text-end">€ {{ number_format($l->totale, 2, ',', '.') }}</td>
                                    <td>
                                        @if($l->attivo)
                                            <span class="badge bg-success">Attiva</span>
                                        @else
                                            <span class="badge bg-secondary">Inattiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="/utente/dettaglio_lavorazione/{{ $l->id }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Apri / Modifica
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="post" onsubmit="return confirm('Eliminare la lavorazione {{ $l->descrizione }} e tutte le sue righe?')" style="display:block; margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $l->id }}">
                                                        <button type="submit" name="elimina" value="1" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Elimina
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($lavorazioni->total() === 0)
                                <tr><td colspan="5" class="text-center text-muted py-4">
                                    @if(!empty($q))
                                        Nessuna lavorazione trovata per "<strong>{{ $q }}</strong>". <a href="/utente/lavorazioni">Annulla ricerca</a>.
                                    @else
                                        Nessuna lavorazione nel catalogo. Crea la prima con "Nuova Lavorazione" o importa il CSV.
                                    @endif
                                </td></tr>
                            @endif
                            </tbody>
                        </table>

                        @if($lavorazioni->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <small class="text-muted">
                                    Mostra <strong>{{ $lavorazioni->firstItem() }}</strong>–<strong>{{ $lavorazioni->lastItem() }}</strong> di <strong>{{ $lavorazioni->total() }}</strong>
                                </small>
                                {!! $lavorazioni->links('utente.common._pagination') !!}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modal_aggiungi_lavorazione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title">Nuova Lavorazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Codice</label>
                            <input type="text" name="codice" class="form-control" placeholder="es. LAV-001">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Descrizione <b style="color:red">*</b></label>
                            <input type="text" name="descrizione" class="form-control" placeholder="es. Sostituzione Suole Carro Tipo X" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi" value="1" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Crea e Aggiungi Righe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_importa_csv_lavorazioni" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success p-3">
                <h5 class="modal-title"><i class="ri-upload-cloud-2-line me-2"></i>Importa Lavorazioni da CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/utente/lavorazioni/importa_csv" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2"><i class="ri-information-line me-1"></i>Formato richiesto</h6>
                        <p class="mb-2">File CSV con queste colonne (header obbligatorio sulla prima riga). Separatore <code>;</code> o <code>,</code> (auto-rilevato). Encoding consigliato UTF-8.</p>
                        <small class="d-block">
                            <code>codice_lavorazione</code>, <code>descrizione_lavorazione</code>, <code>ordinamento</code>, <code>servizio</code>, <code>codice_riga</code>, <code>setup_tank</code>, <code>descrizione_riga</code>, <code>attivita</code>, <code>qta</code>, <code>minuti</code>, <code>pu</code>, <code>aliquota</code>, <code>materiale</code>, <code>descrizione_materiale</code>
                        </small>
                    </div>
                    <div class="alert alert-light small mb-3">
                        <strong>Note:</strong>
                        <ul class="mb-0">
                            <li>Una riga del CSV = una riga della lavorazione. Le righe con lo stesso <code>codice_lavorazione</code> vengono raggruppate sotto la stessa testata.</li>
                            <li>Le lavorazioni con <strong>codice già presente</strong> nel catalogo vengono <strong>ignorate</strong> (non sovrascritte). Per re-importarle elimina prima la lavorazione esistente.</li>
                            <li>Decimali: usa il punto (37.50) o la virgola (37,50), entrambi accettati.</li>
                            <li>Default: <code>attivita=1</code>, <code>aliquota=22</code> se vuoti. <code>setup_tank</code> = 1/true/yes/si.</li>
                        </ul>
                    </div>

                    <label class="form-label">File CSV <b style="color:red">*</b></label>
                    <input type="file" name="csv" class="form-control" accept=".csv,text/csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-upload-cloud-2-line me-1"></i> Carica e Importa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="import_loading_overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; align-items:center; justify-content:center;">
    <div class="card shadow-lg" style="max-width:540px;">
        <div class="card-body text-center p-4">
            <div class="spinner-border text-success mb-3" style="width:3.5rem; height:3.5rem;" role="status">
                <span class="visually-hidden">Caricamento...</span>
            </div>
            <h5 class="mb-2"><i class="ri-upload-cloud-2-line me-1"></i>Importazione in corso...</h5>
            <p class="text-muted mb-3">
                Sto leggendo il CSV e inserendo le lavorazioni e le righe nel catalogo.<br>
                <strong class="text-danger">Non chiudere né ricaricare questa finestra.</strong><br>
                Per file con migliaia di righe può richiedere <strong>fino a 1–2 minuti</strong>.
            </p>
            <div class="progress" style="height:8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width:100%"></div>
            </div>
            <p class="text-muted small mt-3 mb-0" id="import_loading_timer">Tempo trascorso: 0 s</p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Loader overlay sull'import CSV
        var importForm = document.querySelector('#modal_importa_csv_lavorazioni form');
        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                var fileInput = importForm.querySelector('input[type="file"]');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;

                var overlay = document.getElementById('import_loading_overlay');
                if (!overlay) return;
                overlay.style.display = 'flex';

                // Disabilita il bottone per evitare doppi submit
                var submitBtn = importForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                // Timer secondi trascorsi
                var startTime = Date.now();
                var timerEl = document.getElementById('import_loading_timer');
                setInterval(function() {
                    var sec = Math.floor((Date.now() - startTime) / 1000);
                    if (timerEl) {
                        var mm = Math.floor(sec / 60);
                        var ss = sec % 60;
                        timerEl.textContent = 'Tempo trascorso: ' + (mm > 0 ? mm + ' min ' : '') + ss + ' s';
                    }
                }, 1000);
            });
        }
    });
</script>

@include('utente.common.footer')
