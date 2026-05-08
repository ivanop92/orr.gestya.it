@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gantt Preventivo #{{ $preventivo->numero_doc }}</h4>
                    <div class="page-title-right">
                        <a href="{{ url('utente/preventivi') }}" class="btn btn-primary">
                            <i class="ri-arrow-left-line"></i> Torna ai Preventivi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Pianificazione Temporale: {{ $preventivo->oggetto_visibile }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong> {{ $preventivo->ragione_sociale }}</p>
                                <p><strong>Data Inizio:</strong> {{ $info['data_inizio'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Data Consegna:</strong> {{ $info['data_consegna'] }}</p>
                                <p><strong>Durata:</strong> {{ $info['durata_giorni'] }} giorni</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;"
                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenitore del Gantt -->
                        <div id="gantt_container" style="width:100%; height:500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script per DHTMLX Gantt -->
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM caricato');

        // Configurazione del Gantt
        console.log('Configurazione gantt');
        gantt.config.date_format = "%Y-%m-%d";
        gantt.config.autosize = "y";
        gantt.config.readonly = true; // Solo visualizzazione

        // Personalizza le colonne
        console.log('Configurazione colonne');
        gantt.config.columns = [
            {name: "text", label: "Attività", tree: true, width: '*'},
            {name: "start_date", label: "Data Inizio", align: "center", width: 100},
            {name: "end_date", label: "Data Fine", align: "center", width: 100},
            {name: "duration", label: "Durata (giorni)", align: "center", width: 80}
        ];

        // Inizializzazione
        console.log('Inizializzazione gantt');
        gantt.init("gantt_container");

        // Caricamento dati
        console.log('Caricamento dati gantt');
        var ganttData = {
            data: @json($gantt_data)
        };
        console.log('Dati gantt:', ganttData);

        gantt.parse(ganttData);

        // Visualizza una timeline appropriata
        console.log('Configurazione timeline');

        // Prendi le date direttamente dai dati del gantt se esistono
        var ganttItem = ganttData.data[0]; // Prendiamo il primo item

        if (ganttItem) {
            var min_date = ganttItem.start_date;
            var max_date = ganttItem.end_date;

            console.log('min_date da dati:', min_date);
            console.log('max_date da dati:', max_date);

            // Aggiungi un po' di spazio prima e dopo
            var startDate = new Date(min_date);
            startDate.setDate(startDate.getDate() - 3);

            var endDate = new Date(max_date);
            endDate.setDate(endDate.getDate() + 3);

            gantt.config.start_date = startDate;
            gantt.config.end_date = endDate;
        } else {
            // Date fallback
            console.log('Nessun dato trovato, uso date predefinite');
            gantt.config.start_date = new Date("2025-04-01");
            gantt.config.end_date = new Date("2025-08-01");
        }

        gantt.render();

        // Eventi oggi e progresso
        var today = new Date();
        gantt.addMarker({
            start_date: today,
            css: "today",
            text: "Oggi"
        });

        // Calcola il progresso basato su oggi
        if (ganttItem) {
            var start = new Date(ganttItem.start_date);
            var end = new Date(ganttItem.end_date);

            console.log('Data inizio per calcolo progresso:', start);
            console.log('Data fine per calcolo progresso:', end);

            var totalDays = Math.round((end - start) / (1000 * 60 * 60 * 24));
            var daysElapsed = Math.max(0, Math.min(totalDays, Math.round((today - start) / (1000 * 60 * 60 * 24))));
            var progress = Math.round((daysElapsed / totalDays) * 100);

            // Aggiorna la barra di progresso
            document.querySelector('.progress-bar').style.width = progress + '%';
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', progress);
            document.querySelector('.progress-bar').textContent = progress + '%';

            console.log('Progresso calcolato:', progress + '%');
        }
    });
</script>

<style>
    .gantt_marker {
        position: absolute;
        top: 0;
        width: 1px;
        height: 100%;
        background-color: #ff0000;
        z-index: 1;
    }

    .gantt_marker .gantt_marker_content {
        padding: 1px 4px;
        background: #ff0000;
        color: #fff;
        border-radius: 2px;
    }

    .today {
        background-color: #ff7777;
    }
</style>

@include('utente.common.footer')