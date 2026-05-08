@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Commesse</h4>
                    <div class="page-title-right">
                        <button class="btn btn-info add-btn" onclick="aggiungi();">
                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <!-- Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-lista" role="tab">
            <i class="ri-list-check-2 me-1 align-bottom"></i> Lista Commesse
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-resource-gantt" role="tab">
            <i class="ri-bar-chart-horizontal-line me-1 align-bottom"></i> Resource Gantt
        </a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <!-- Tab Lista -->
    <div class="tab-pane active" id="tab-lista" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <table id="scroll-horizontal" class="table table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th>Data Apertura</th>
                                <th>Data Chiusura</th>
                                <th>Stato</th>
                                <th>Budget</th>
                                <th>Costi</th>
                                <th>Ricavi</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($commesse as $c)
                                <tr>
                                    <td>{{ $c->codice_commessa }}</td>
                                    <td>{{ $c->descrizione }}</td>
                                    <td>{{ date('d/m/Y', strtotime($c->data_apertura)) }}</td>
                                    <td>{{ $c->data_chiusura ? date('d/m/Y', strtotime($c->data_chiusura)) : '-' }}</td>
                                    <td>
                                        @if($c->stato == 'aperta')
                                            <span class="badge bg-success">Aperta</span>
                                        @elseif($c->stato == 'in_corso')
                                            <span class="badge bg-warning">In Corso</span>
                                        @elseif($c->stato == 'completata')
                                            <span class="badge bg-info">Completata</span>
                                        @else
                                            <span class="badge bg-danger">Annullata</span>
                                        @endif
                                    </td>
                                    <td>&euro; {{ number_format($c->budget, 2, ',', '.') }}</td>
                                    <td>&euro; {{ number_format($c->costi, 2, ',', '.') }}</td>
                                    <td>&euro; {{ number_format($c->ricavi, 2, ',', '.') }}</td>
                                    <td>
                                        <div style="display: flex">

                                            <a class="btn btn-success btn-sm" target="_blank" href="/utente/commesse/{{ $c->id }}/attivita">Attività</a>
                                            <a style="margin-left:5px;" class="btn btn-info btn-sm" target="_blank" href="/utente/commesse/{{ $c->id }}/dashboard">Dashboard</a>
                                            <a style="margin-left:5px;" onclick="modifica({{ $c->id }})" class="btn btn-sm btn-primary">
                                                <i class="ri-edit-2-line"></i>
                                            </a>
                                            <form method="post" onsubmit="return confirm('Vuoi eliminare questa commessa?')">
                                                <input type="hidden" name="id" value="{{ $c->id }}">
                                                <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </form>
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

    <!-- Tab Resource Gantt -->
    <div class="tab-pane" id="tab-resource-gantt" role="tabpanel">
        <div class="card" style="height: 800px;">
            <div class="card-body" style="height: 100%; padding: 0;">
                <div class="gantt-toolbar p-3">
                    <div class="btn-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ri-calendar-line me-1"></i> Scala
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('day')">Giorno</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('week')">Settimana</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setZoomLevel('month')">Mese</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="gantt-container">
                    <div id="resource_gantt"></div>
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
<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="modalLabel">Aggiungi Commessa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea id="descrizione" name="descrizione" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="data_apertura" class="form-label">Data Apertura</label>
                        <input type="date" id="data_apertura" name="data_apertura" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="budget" class="form-label">Budget</label>
                        <input type="number" step="0.01" id="budget" name="budget" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success" name="aggiungi" value="Aggiungi">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica -->
@foreach($commesse as $c)
    <div class="modal fade" id="modal_modifica_{{ $c->id }}" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="modalLabel">Modifica Commessa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $c->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="descrizione" class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" required>{{ $c->descrizione }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="data_apertura" class="form-label">Data Apertura</label>
                            <input type="date" id="data_apertura" name="data_apertura" class="form-control" value="{{ $c->data_apertura }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="data_chiusura" class="form-label">Data Chiusura</label>
                            <input type="date" id="data_chiusura" name="data_chiusura" class="form-control" value="{{ $c->data_chiusura }}">
                        </div>
                        <div class="mb-3">
                            <label for="stato" class="form-label">Stato</label>
                            <select id="stato" name="stato" class="form-control" required>
                                <option value="aperta" {{ $c->stato == 'aperta' ? 'selected' : '' }}>Aperta</option>
                                <option value="in_corso" {{ $c->stato == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                                <option value="completata" {{ $c->stato == 'completata' ? 'selected' : '' }}>Completata</option>
                                <option value="annullata" {{ $c->stato == 'annullata' ? 'selected' : '' }}>Annullata</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number" step="0.01" id="budget" name="budget" class="form-control" value="{{ $c->budget }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea id="note" name="note" class="form-control">{{ $c->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-success" name="modifica" value="Modifica">Salva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- DHTMLX Gantt -->
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<script src="https://cdn.dhtmlx.com/gantt/edge/ext/dhtmlxgantt_grouping.js"></script>
<script src="https://cdn.dhtmlx.com/gantt/edge/ext/dhtmlxgantt_tooltip.js"></script>
<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

<!-- Stili custom per il Gantt -->
<style>
    .gantt-container {
        height: 700px !important;
        width: 100%;
    }
    #resource_gantt {
        height: 100% !important;
        width: 100% !important;
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
    .weekend {
        background: #f4f7f7;
    }
    .gantt_task_cell.weekend {
        background-color: #f4f7f7;
    }
    .resource_marker{
        text-align: center;
    }
    .resource_marker div{
        width: 28px;
        height: 28px;
        line-height: 29px;
        display: inline-block;
        border-radius: 15px;
        color: #FFF;
        margin: 3px;
    }
    .resource_marker.workday_ok div{
        background: #51c185;
    }
    .resource_marker.workday_over div{
        background: #ff8686;
    }
</style>



<script>
    function aggiungi() {
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id) {
        $('#modal_modifica_' + id).modal('show');
    }

    function setZoomLevel(level) {
        gantt.ext.zoom.setLevel(level);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Configurazione base
        gantt.config.date_format = "%Y-%m-%d";
        gantt.config.work_time = true;
        gantt.config.scale_height = 50;
        gantt.config.row_height = 45;
        gantt.config.fit_tasks = true;
        gantt.config.show_progress = true;
        gantt.config.auto_scheduling = true;
        gantt.config.auto_scheduling_strict = true;
        gantt.config.smart_rendering = true;
        gantt.config.show_errors = true;
        gantt.config.show_links = true;

        // Configurazione colonne
        gantt.config.columns = [
            {
                name: "text",
                label: "Dipendente",
                tree: true,
                width: 250,
                template: function(task) {
                    if (task.type == 'project') {
                        return task.text + " (" + task.numero_attivita + " attività)";
                    }
                    return task.text;
                }
            },
            {
                name: "commessa",
                label: "Commessa",
                align: "left",
                width: 150,
                template: function(task) {
                    return task.commessa || "";
                }
            },
            {
                name: "ore_totali",
                label: "Ore",
                align: "center",
                width: 80,
                template: function(task) {
                    if (task.type == 'project') {
                        return task.ore_totali + "h";
                    }
                    return task.ore_previste + "h";
                }
            }
        ];

        // Configurazione scale temporali
        var zoomConfig = {
            levels: [
                {
                    name: "day",
                    scale_height: 50,
                    min_column_width: 70,
                    scales: [
                        {unit: "month", step: 1, format: "%F %Y"},
                        {unit: "day", step: 1, format: "%d %D"}
                    ]
                },
                {
                    name: "week",
                    scale_height: 50,
                    min_column_width: 70,
                    scales: [
                        {unit: "month", step: 1, format: "%F %Y"},
                        {unit: "week", step: 1, format: "Settimana #%W"}
                    ]
                },
                {
                    name: "month",
                    scale_height: 50,
                    min_column_width: 120,
                    scales: [
                        {unit: "year", step: 1, format: "%Y"},
                        {unit: "month", step: 1, format: "%F"}
                    ]
                }
            ]
        };

        gantt.ext.zoom.init(zoomConfig);
        gantt.ext.zoom.setLevel("week");

        // Template per le task
        gantt.templates.task_class = function(start, end, task) {
            var classes = [];
            if (task.type == 'task') {
                if (task.progress == 1) classes.push("complete");
                else if (task.progress == 0) classes.push("not-started");
                else classes.push("in-progress");
            }
            return classes.join(" ");
        };

        // Template per il tooltip
        gantt.templates.tooltip_text = function(start, end, task) {
            if (task.type == 'task') {
                var stato = "";
                if (task.progress == 1) stato = "Completata";
                else if (task.progress == 0) stato = "Da Iniziare";
                else stato = "In Corso (" + Math.round(task.progress * 100) + "%)";

                return "<b>Attività:</b> " + task.text + "<br/>" +
                       "<b>Commessa:</b> " + task.commessa + "<br/>" +
                       "<b>Inizio:</b> " + gantt.templates.tooltip_date_format(start) + "<br/>" +
                       "<b>Fine:</b> " + gantt.templates.tooltip_date_format(end) + "<br/>" +
                       "<b>Stato:</b> " + stato + "<br/>" +
                       "<b>Ore Previste:</b> " + task.ore_previste + "h";
            }
            return "";
        };

        // Evidenzia i weekend
        gantt.templates.scale_cell_class = function(date) {
            if (date.getDay() == 0 || date.getDay() == 6) return "weekend";
        };
        gantt.templates.timeline_cell_class = function(task, date) {
            if (date.getDay() == 0 || date.getDay() == 6) return "weekend";
        };

        // Inizializzazione
        gantt.init("resource_gantt");

        // Caricamento dati con gestione errori migliorata
        $.ajax({
            url: '/utente/commesse/resource-gantt-data',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Dati ricevuti:', response); // Debug
                if (response && response.data && response.data.length > 0) {
                    gantt.parse({
                        data: response.data,
                        links: []
                    });
                    gantt.sort('start_date', false);
                    gantt.render();
                } else {
                    console.warn('Nessun dato ricevuto dal server');
                    Swal.fire({
                        title: 'Attenzione',
                        text: 'Nessuna attività trovata per la visualizzazione',
                        icon: 'warning'
                    });
                }
            },
            error: function(xhr) {
                console.error('Errore nel caricamento dei dati:', xhr);
                Swal.fire({
                    title: 'Errore',
                    text: 'Si è verificato un errore nel caricamento dei dati: ' + 
                          (xhr.responseJSON?.error || 'Errore sconosciuto'),
                    icon: 'error'
                });
            }
        });

        // Gestione cambio tab con reinizializzazione
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.getAttribute('href') === '#tab-resource-gantt') {
                gantt.render();
            }
        });
    });
</script>

@include('utente.common.footer')