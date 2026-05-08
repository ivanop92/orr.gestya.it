@include('utente.common.header')

<!-- DHTMLX Gantt -->
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<script src="https://cdn.dhtmlx.com/gantt/edge/ext/dhtmlxgantt_zoom.js"></script>
<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

<!-- Stili custom per il Gantt -->
<style>
    .gantt-container {
        height: 600px;
        width: 100%;
    }
    .gantt_task_line {
        border-radius: 3px;
    }
    .gantt_task_progress {
        background-color: rgba(0,0,0,0.2);
    }
    .complete .gantt_task_progress {
        background-color: #34c38f;
    }
    .not-started .gantt_task_progress {
        background-color: #f1b44c;
    }
    .in-progress .gantt_task_progress {
        background-color: #556ee6;
    }
    /* Assicuriamoci che il contenitore del Gantt abbia dimensioni corrette */
    #gantt_here {
        height: 600px !important;
        width: 100% !important;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Attività Commessa: {{ $commessa->codice_commessa }}</h4>
                    <div class="page-title-right">
                        <a href="/utente/commesse" class="btn btn-light add-btn me-2">
                            <i class="ri-arrow-left-line align-bottom me-1"></i>Torna alle Commesse
                        </a>
                        <a href="/utente/commesse/{{ $commessa->id }}/dashboard" class="btn btn-primary add-btn me-2">
                            <i class="ri-dashboard-line align-bottom me-1"></i>Dashboard
                        </a>
                        <button class="btn btn-info add-btn" onclick="aggiungi();">
                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Attività
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-lista" role="tab">
                            <i class="ri-list-check-2 me-1 align-bottom"></i> Lista Attività
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-gantt" role="tab">
                            <i class="ri-bar-chart-horizontal-line me-1 align-bottom"></i> Diagramma Gantt
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab Lista -->
                    <div class="tab-pane active" id="tab-lista" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <table id="attivita-table" class="table table-bordered table-hover nowrap" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Titolo</th>
                                        <th>Responsabile</th>
                                        <th>Data Inizio</th>
                                        <th>Data Fine</th>
                                        <th>Stato</th>
                                        <th>Completamento</th>
                                        <th>Priorità</th>
                                        <th style="width:100px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($attivita as $a)
                                        <tr>
                                            <td>{{ $a->titolo }}</td>
                                            <td>{{ $a->responsabile }}</td>
                                            <td>{{ date('d/m/Y', strtotime($a->data_inizio)) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($a->data_fine)) }}</td>
                                            <td>
                                                @if($a->stato == 'da_iniziare')
                                                    <span class="badge bg-warning">Da Iniziare</span>
                                                @elseif($a->stato == 'in_corso')
                                                    <span class="badge bg-info">In Corso</span>
                                                @elseif($a->stato == 'completata')
                                                    <span class="badge bg-success">Completata</span>
                                                @else
                                                    <span class="badge bg-danger">In Ritardo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $a->completamento == 100 ? 'bg-success' : ($a->completamento == 0 ? 'bg-warning' : 'bg-info') }}"
                                                         role="progressbar"
                                                         style="width: {{ $a->completamento }}%"
                                                         aria-valuenow="{{ $a->completamento }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $a->completamento }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($a->priorita == 'bassa')
                                                    <span class="badge bg-success">Bassa</span>
                                                @elseif($a->priorita == 'media')
                                                    <span class="badge bg-warning">Media</span>
                                                @else
                                                    <span class="badge bg-danger">Alta</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ url('utente/commesse/'.$commessa->id.'/attivita/'.$a->id) }}" class="btn btn-sm btn-info">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-primary" onclick="modifica({{ $a->id }})">
                                                        <i class="ri-edit-2-line"></i>
                                                    </button>
                                                    <form method="post" class="d-inline" onsubmit="return confirm('Vuoi eliminare questa attività?')">
                                                        <input type="hidden" name="id" value="{{ $a->id }}">
                                                        <input name="elimina" type="submit" value="Elimina" class="btn btn-sm btn-danger">
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <!-- Resource Timeline Table -->
                                <h4 class="mt-4 mb-3">Carico di Lavoro Dipendenti</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Dipendente</th>
                                                <th>Attività Assegnate</th>
                                                <th>Ore Totali</th>
                                                <th>Periodo</th>
                                                <th>Stato Carico</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dipendenti as $d)
                                                @php
                                                    $attivitaDipendente = DB::table('commesse_attivita')->where('id_responsabile', $d->id)->where('id_commessa', $commessa->id)->get();
                                                    $numAttivita = $attivitaDipendente->count();
                                                    
                                                    // Trova il periodo di lavoro
                                                    $dataInizio = $attivitaDipendente->min('data_inizio');
                                                    $dataFine = $attivitaDipendente->max('data_fine');
                                                    
                                                    // Calcola giorni totali tra data inizio e fine
                                                    $giorniTotali = 0;
                                                    if($dataInizio && $dataFine) {
                                                        $giorniTotali = (strtotime($dataFine) - strtotime($dataInizio)) / (60 * 60 * 24);
                                                    }
                                                    
                                                    // Calcola ore totali (8 ore per giorno)
                                                    $oreTotali = $giorniTotali * 8;
                                                    
                                                    // Calcola lo stato del carico
                                                    $statoCarico = $numAttivita > 3 ? 'Sovraccarico' : 'Normale';
                                                @endphp
                                                <tr>
                                                    <td>{{ $d->nome }} {{ $d->cognome }}</td>
                                                    <td>
                                                        {{ $numAttivita }}
                                                        @if($numAttivita > 0)
                                                            <button class="btn btn-sm btn-link" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#attivita-{{ $d->id }}">
                                                                Dettagli
                                                            </button>
                                                            <div class="collapse" id="attivita-{{ $d->id }}">
                                                                <ul class="list-unstyled">
                                                                    @foreach($attivitaDipendente as $att)
                                                                        <li>
                                                                            {{ $att->titolo }} ({{ DB::table('commesse')->where('id', $att->id_commessa)->first()->descrizione }})
                                                                            <small class="text-muted">
                                                                                ({{ date('d/m/Y', strtotime($att->data_inizio)) }} - 
                                                                                {{ date('d/m/Y', strtotime($att->data_fine)) }})
                                                                            </small>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $oreTotali }}</td>
                                                    <td>
                                                        @if($dataInizio && $dataFine)
                                                            {{ date('d/m/Y', strtotime($dataInizio)) }} - 
                                                            {{ date('d/m/Y', strtotime($dataFine)) }}
                                                        @else
                                                            Nessuna attività
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($statoCarico == 'Sovraccarico')
                                                            <span class="badge bg-danger">Sovraccarico</span>
                                                        @else
                                                            <span class="badge bg-success">Normale</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Gantt -->
                    <div class="tab-pane" id="tab-gantt" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="gantt-toolbar mb-3">
                                    <div class="btn-group">
                                        <button onclick="gantt.createTask()" class="btn btn-primary btn-sm">
                                            <i class="ri-add-fill me-1"></i> Nuova Attività
                                        </button>
                                    </div>
                                    <div class="btn-group ms-2">
                                        <button onclick="gantt.ext.zoom.zoomIn();" class="btn btn-light btn-sm">
                                            <i class="ri-zoom-in-line me-1"></i> Zoom In
                                        </button>
                                        <button onclick="gantt.ext.zoom.zoomOut();" class="btn btn-light btn-sm">
                                            <i class="ri-zoom-out-line me-1"></i> Zoom Out
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-calendar-line me-1"></i> Scala
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('day')">Giorno</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('week')">Settimana</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('month')">Mese</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('quarter')">Trimestre</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('year')">Anno</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="gantt-container">
                                    <div id="gantt_here"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi -->
<div class="modal fade" id="modal_aggiungi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title">Aggiungi Attività</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titolo</label>
                        <input type="text" name="titolo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrizione</label>
                        <textarea name="descrizione" class="form-control"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Data Inizio</label>
                            <input type="date" name="data_inizio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Fine</label>
                            <input type="date" name="data_fine" class="form-control" >
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsabile</label>
                        <select name="id_responsabile" class="form-control" >
                            <option value="">Seleziona Responsabile</option>
                            @foreach($dipendenti as $d)
                                <option value="{{ $d->id }}">{{ $d->nome }} {{ $d->cognome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Priorità</label>
                            <select name="priorita" class="form-control" required>
                                <option value="bassa">Bassa</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attività Precedente</label>
                            <select name="id_attivita_precedente" class="form-control">
                                <option value="">Nessuna</option>
                                @foreach($attivita as $a)
                                    <option value="{{ $a->id }}">{{ $a->titolo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <input type="submit" name="aggiungi" value="Aggiungi" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica -->
@foreach($attivita as $a)
    <div class="modal fade" id="modal_modifica_{{ $a->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title">Modifica Attività</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="id" value="{{ $a->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Titolo</label>
                            <input type="text" name="titolo" class="form-control" value="{{ $a->titolo }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrizione</label>
                            <textarea name="descrizione" class="form-control">{{ $a->descrizione }}</textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Data Inizio</label>
                                <input type="date" name="data_inizio" class="form-control" value="{{ $a->data_inizio }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data Fine</label>
                                <input type="date" name="data_fine" class="form-control" value="{{ $a->data_fine }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Data Inizio Effettiva</label>
                                <input type="date" name="data_inizio_effettiva" class="form-control" value="{{ $a->data_inizio_effettiva }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data Fine Effettiva</label>
                                <input type="date" name="data_fine_effettiva" class="form-control" value="{{ $a->data_fine_effettiva }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsabile</label>
                            <select name="id_responsabile" class="form-control" required>
                                <option value="">Seleziona Responsabile</option>
                                @foreach($dipendenti as $d)
                                    <option value="{{ $d->id }}" {{ $d->id == $a->id_responsabile ? 'selected' : '' }}>
                                        {{ $d->nome }} {{ $d->cognome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Stato</label>
                                <select name="stato" class="form-control" required>
                                    <option value="da_iniziare" {{ $a->stato == 'da_iniziare' ? 'selected' : '' }}>Da Iniziare</option>
                                    <option value="in_corso" {{ $a->stato == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                                    <option value="completata" {{ $a->stato == 'completata' ? 'selected' : '' }}>Completata</option>
                                    <option value="in_ritardo" {{ $a->stato == 'in_ritardo' ? 'selected' : '' }}>In Ritardo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Completamento %</label>
                                <input type="number" name="completamento" class="form-control" value="{{ $a->completamento }}" min="0" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Priorità</label>
                                <select name="priorita" class="form-control" required>
                                    <option value="bassa" {{ $a->priorita == 'bassa' ? 'selected' : '' }}>Bassa</option>
                                    <option value="media" {{ $a->priorita == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ $a->priorita == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attività Precedente</label>
                            <select name="id_attivita_precedente" class="form-control">
                                <option value="">Nessuna</option>
                                @foreach($attivita as $att)
                                    @if($att->id != $a->id)
                                        <option value="{{ $att->id }}" {{ $att->id == $a->id_attivita_precedente ? 'selected' : '' }}>
                                            {{ $att->titolo }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control">{{ $a->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" name="modifica" value="Salva" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    // Funzioni per i modal
    function aggiungi() {
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id) {
        $('#modal_modifica_' + id).modal('show');
    }

    // Inizializzazione DataTable
    $(document).ready(function() {
        $('#attivita-table').DataTable({
            scrollX: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            }
        });
    });


</script>

<script>
    function setZoomLevel(level) {
        gantt.ext.zoom.setLevel(level);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Configurazione base
        gantt.config.date_format = "%Y-%m-%d";
        gantt.config.xml_date = "%Y-%m-%d";

        // Configurazione dei dipendenti come risorse
        gantt.serverList("dipendenti", @json($dipendenti->map(function($d) {
            return ['key' => $d->id, 'label' => $d->nome . ' ' . $d->cognome];
        })));

        // Configurazione colonne
        gantt.config.columns = [
            {name: "text", label: "Attività", tree: true, width: 200},
            {
                name: "dipendente", 
                label: "Responsabile", 
                align: "center", 
                width: 120,
                template: function(task) {
                    if (task.id_responsabile) {
                        var dipendente = gantt.serverList("dipendenti").find(function(d) {
                            return d.key == task.id_responsabile;
                        });
                        return dipendente ? dipendente.label : "";
                    }
                    return "";
                }
            },
            {name: "start_date", label: "Inizio", align: "center", width: 80},
            {name: "duration", label: "Durata", align: "center", width: 60},
            {name: "add", label: "", width: 44}
        ];

        // Configurazione lightbox
        gantt.config.lightbox.sections = [
            {name: "description", height: 38, map_to: "text", type: "textarea", focus: true},
            {
                name: "dipendente",
                height: 22,
                map_to: "id_responsabile",
                type: "select",
                options: gantt.serverList("dipendenti")
            },
            {name: "time", type: "duration", map_to: "auto"}
        ];

        // Configurazione dello zoom
        var zoomConfig = {
            levels: [
                {
                    name: "day",
                    scale_height: 27,
                    min_column_width: 80,
                    scales: [
                        { unit: "day", step: 1, format: "%d %M" }
                    ]
                },
                {
                    name: "week",
                    scale_height: 50,
                    min_column_width: 50,
                    scales: [
                        { unit: "week", step: 1, format: "Settimana #%W" },
                        { unit: "day", step: 1, format: "%d %M" }
                    ]
                },
                {
                    name: "month",
                    scale_height: 50,
                    min_column_width: 120,
                    scales: [
                        { unit: "month", format: "%F, %Y" },
                        { unit: "week", format: "Settimana #%W" }
                    ]
                },
                {
                    name: "quarter",
                    height: 50,
                    min_column_width: 90,
                    scales: [
                        { unit: "month", step: 1, format: "%M" },
                        { unit: "quarter", step: 1, format: "%F, %Y" }
                    ]
                },
                {
                    name: "year",
                    scale_height: 50,
                    min_column_width: 30,
                    scales: [
                        { unit: "year", step: 1, format: "%Y" }
                    ]
                }
            ]
        };

        gantt.ext.zoom.init(zoomConfig);
        gantt.ext.zoom.setLevel("week");

        // Stili personalizzati per i task in base al dipendente
        gantt.templates.task_class = function(start, end, task) {
            var classes = [];
            if (task.progress == 0) classes.push("not-started");
            else if (task.progress >= 1) classes.push("complete");
            else classes.push("in-progress");
            
            if (task.id_responsabile) {
                classes.push("dipendente-" + task.id_responsabile);
            }
            
            return classes.join(" ");
        };

        // Configurazione della scala temporale
        gantt.config.scale_unit = "day";
        gantt.config.step = 1;
        gantt.config.date_scale = "%d %M";
        gantt.config.subscales = [
            {unit: "hour", step: 6, date: "%H"}
        ];

        // Aggiungi gli event listeners per lo zoom
        gantt.attachEvent("onGanttRender", function() {
            console.log("Gantt renderizzato");
        });

        // Abilitazione funzionalità
        gantt.config.order_branch = true;
        gantt.config.open_tree_initially = true;
        gantt.config.drag_progress = true;
        gantt.config.drag_resize = true;
        gantt.config.drag_move = true;
        gantt.config.drag_links = true;

        // Inizializzazione
        gantt.init("gantt_here");

        // Caricamento dati
        const tasks = {
            data: @json($gantt_data ?? [])
        };

        gantt.parse(tasks);

        // Event listeners per il CRUD
        gantt.attachEvent("onAfterTaskAdd", function(id, task) {
            saveTask("add", task);
        });

        gantt.attachEvent("onAfterTaskUpdate", function(id, task) {
            saveTask("update", task);
        });

        gantt.attachEvent("onAfterTaskDelete", function(id, task) {
            saveTask("delete", task);
        });

        function saveTask(action, task) {
            // Funzione helper per formattare le date
            function formatDate(date) {
                if (typeof date === 'string' && date.includes('GMT')) {
                    date = new Date(date);
                }
                if (date instanceof Date) {
                    return date.getFullYear() + '-' +
                        String(date.getMonth() + 1).padStart(2, '0') + '-' +
                        String(date.getDate()).padStart(2, '0');
                }
                return date;
            }

            const data = {
                action: action,
                task_id: task.id,
                text: task.text,
                start_date: formatDate(task.start_date),
                end_date: formatDate(task.end_date),
                progress: task.progress || 0,
                parent: task.parent || null,
                id_responsabile: task.id_responsabile || null,
                _token: '{{ csrf_token() }}'
            };

            console.log('Dati inviati:', data); // Debug

            $.ajax({
                url: '/utente/commesse/{{ $commessa->id }}/gantt/task',
                type: 'POST',
                data: data,
                success: function(response) {
                    if(response.action == "inserted") {
                        gantt.changeTaskId(task.id, response.tid);
                    }
                    gantt.message({type:"success", text:"Operazione completata con successo"});
                },
                error: function(xhr) {
                    console.error('Errore:', xhr.responseJSON);
                    gantt.message({type:"error", text: xhr.responseJSON?.error || "Si è verificato un errore"});
                    gantt.refresh();
                }
            });
        }

        // Gestisci il cambio di tab per ricalcolare le dimensioni
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.getAttribute('href') === '#tab-gantt') {
                gantt.render();
            }
        });
    });
</script>

@include('utente.common.footer')