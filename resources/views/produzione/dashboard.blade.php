@extends('produzione.layout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard Produzione</h4>

                        <div class="page-title-right">
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Notifiche e messaggi di sistema -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($tipo_lavoro == 'tutti')
                <!-- Tabs solo se l'azienda usa entrambi -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-odl" role="tab">
                                    <span class="d-block d-sm-none"><i class="ri-file-list-line"></i></span>
                                    <span class="d-none d-sm-block">Ordini di Lavoro</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-attivita" role="tab">
                                    <span class="d-block d-sm-none"><i class="ri-task-line"></i></span>
                                    <span class="d-none d-sm-block">Attività Commesse</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Tab Contents -->
            <div class="tab-content">
                <!-- Tab ODL -->
                @if($tipo_lavoro == 'odl' || $tipo_lavoro == 'tutti')
                    <div class="tab-pane active" id="tab-odl" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            Ordini di Lavoro Attivi
                                            <span class="badge bg-primary ms-2">{{ count($odl_attivi) }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($odl_attivi) > 0)
                                            <table id="odlTable" class="table table-bordered table-hover dt-responsive nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Data</th>
                                                    <th>Articolo</th>
                                                    <th>Quantità</th>
                                                    <th>Stato</th>
                                                    <th>Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($odl_attivi as $odl)
                                                    <tr>
                                                        <td>{{ $odl->numero }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($odl->data)) }}</td>
                                                        <td>{{ $odl->articolo }}</td>
                                                        <td>{{ $odl->qta }}</td>
                                                        <td>
                                                            @if($odl->stato == 0)
                                                                <span class="badge bg-warning">In Attesa</span>
                                                            @elseif($odl->stato == 1)
                                                                <span class="badge bg-info">In Produzione</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('produzione/dettaglio_odl/'.$odl->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="ri-eye-line"></i> Dettaglio
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <i class="ri-information-line me-2"></i> Nessun ordine di lavoro attivo per la fase selezionata.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tab Attività Commesse -->
                @if($tipo_lavoro == 'commesse' || $tipo_lavoro == 'tutti')
                    <div class="tab-pane {{ $tipo_lavoro == 'commesse' ? 'active' : '' }}" id="tab-attivita" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            Attività Commesse
                                            <span class="badge bg-primary ms-2">{{ count($attivita_commesse) }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($attivita_commesse) > 0)
                                            <table id="attivitaTable" class="table table-bordered table-hover dt-responsive nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Codice Commessa</th>
                                                    <th>Attività</th>
                                                    <th>Priorità</th>
                                                    <th>Scadenza</th>
                                                    <th>Completamento</th>
                                                    <th>Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($attivita_commesse as $attivita)
                                                    <tr>
                                                        <td>
                                                            <span class="fw-medium">{{ $attivita->codice_commessa }}</span>
                                                            <small class="d-block text-muted">{{ $attivita->commessa_descrizione }}</small>
                                                        </td>
                                                        <td>{{ $attivita->titolo }}</td>
                                                        <td>
                                                            @if($attivita->priorita == 'alta')
                                                                <span class="badge bg-danger">Alta</span>
                                                            @elseif($attivita->priorita == 'media')
                                                                <span class="badge bg-warning">Media</span>
                                                            @else
                                                                <span class="badge bg-info">Bassa</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($attivita->data_fine)
                                                                @php
                                                                    $giorni_rimasti = (strtotime($attivita->data_fine) - time()) / (60 * 60 * 24);
                                                                @endphp

                                                                {{ date('d/m/Y', strtotime($attivita->data_fine)) }}

                                                                @if($giorni_rimasti < 0)
                                                                    <span class="badge bg-danger ms-1">Scaduta</span>
                                                                @elseif($giorni_rimasti <= 3)
                                                                    <span class="badge bg-warning ms-1">{{ ceil($giorni_rimasti) }} giorni</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Non definita</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                                    <div class="progress-bar bg-success" role="progressbar"
                                                                         style="width: {{ $attivita->completamento }}%;"
                                                                         aria-valuenow="{{ $attivita->completamento }}"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"></div>
                                                                </div>
                                                                <span class="ms-2">{{ $attivita->completamento }}%</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('produzione/dettaglio_attivita/'.$attivita->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="ri-eye-line"></i> Dettaglio
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <i class="ri-information-line me-2"></i> Nessuna attività di commessa in corso.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#odlTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Italian.json'
                },
                responsive: true
            });

            $('#attivitaTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Italian.json'
                },
                responsive: true
            });
        });
    </script>
@endsection

@section('scripts')
    <script>
        // Variabili per l'auto-refresh
        let autoRefreshEnabled = true;
        let refreshInterval = 60; // secondi
        let refreshTimer;
        let countdownTimer;
        let countdown = refreshInterval;

        // Funzione per aggiornare il contatore
        function updateCountdown() {
            $('#refreshTimer').text(countdown);
            countdown--;

            if (countdown < 0) {
                clearInterval(countdownTimer);
                refreshPage();
            }
        }

        // Funzione per aggiornare la pagina
        function refreshPage() {
            top.location.href = '{{ url("produzione/dashboard") }}';
        }

        // Inizializziamo il conteggio alla rovescia
        updateCountdown();
        countdownTimer = setInterval(updateCountdown, 1000);

        // Gestione toggle auto-refresh
        $('#autoRefreshToggle').change(function() {
            autoRefreshEnabled = $(this).is(':checked');

            if (autoRefreshEnabled) {
                // Riavviamo il conteggio alla rovescia
                countdown = refreshInterval;
                updateCountdown();
                countdownTimer = setInterval(updateCountdown, 1000);

                // Mostriamo il contatore
                $('#refreshTimer').show();
            } else {
                // Fermiamo il conteggio alla rovescia
                clearInterval(countdownTimer);

                // Nascondiamo il contatore
                $('#refreshTimer').hide();
            }
        });

        // Refresh manuale
        $('#manualRefresh').click(function() {
            refreshPage();
        });
    </script>
@endsection

@include('utente.common.footer')

<style>
    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .text-opacity-75 {
        opacity: 0.75;
    }

    .fs-18 {
        font-size: 18px;
    }
</style>