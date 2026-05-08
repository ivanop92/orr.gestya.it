@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <h4 class="fs-16 mb-1">Benvenuto {{ $utente->nome }} {{ $utente->cognome }}</h4>
                        </div>
                    </div>

                </div> <!-- end .h-100-->
            </div> <!-- end col -->
        </div>



        <div class="row">
            <!-- Fatturato Netto -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate bg-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="text-white mb-0">Fatturato Netto</h4>
                                <p class="text-white text-opacity-75 mb-0">
                                    {{ number_format($fatturato_totale, 2, ',', '.') }} €
                                </p>
                                <p class="text-white text-opacity-50 mb-0">
                                    {{ $variazione_fatturato_perc }}% ultimi 310gg
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-light rounded-circle fs-3">
                                    <i class="ri-money-euro-circle-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Costi Totali -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate bg-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="text-white mb-0">Costi Totali</h4>
                                <p class="text-white text-opacity-75 mb-0">
                                    {{ number_format($totale_costi, 2, ',', '.') }} €
                                </p>
                                <p class="text-white text-opacity-50 mb-0">
                                    {{ $variazione_costi_perc }}% ultimi 310gg
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-light rounded-circle fs-3">
                                    <i class="ri-pie-chart-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Utile d'Impresa -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate bg-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="text-white mb-0">Utile d'Impresa</h4>
                                <p class="text-white text-opacity-75 mb-0">
                                    {{ number_format($utile_impresa, 2, ',', '.') }} €
                                </p>
                                <p class="text-white text-opacity-50 mb-0">
                                    {{ $variazione_utile_perc }}% ultimi 310gg
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-light rounded-circle fs-3">
                                    <i class="ri-line-chart-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indice Redditività -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate bg-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="text-white mb-0">Indice Redditività</h4>
                                <p class="text-white text-opacity-75 mb-0">
                                    {{ number_format($indice_redditivita, 2, ',', '.') }}%
                                </p>
                                <p class="text-white text-opacity-50 mb-0">
                                    Utile/Fatturato
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-light rounded-circle fs-3">
                                    <i class="ri-percent-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <?php if($fatture_da_registrare > 0){ ?>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                    Fatture da Registrare
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <span class="counter-value" data-target="{{ $fatture_da_registrare }}">
                                            {{ $fatture_da_registrare }}
                                        </span>
                                </h4>
                                <a href="{{ URL::asset('utente/documenti_da_registrare') }}" class="text-decoration-underline">
                                    Visualizza Fatture
                                </a>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="bx bx-file text-warning"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>

        <?php if(sizeof($fatture_scartate) > 0){ ?>
            <?php foreach($fatture_scartate as $fs){ ?>
                <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Fattura Scartata N. {{ $fs->numero_doc }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="1">
                                                {{ $fs->ns_descrizione }}
                                            </span>
                                    </h4>
                                    <a href="{{ URL::asset('utente/modifica_documento/'.$fs->id) }}" class="text-decoration-underline">
                                        Modifica Fattura
                                    </a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-warning rounded fs-3">
                                            <i class="bx bx-file text-warning"></i>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

        <?php } ?>



        <div class="row">
            <!-- Crediti verso Clienti -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Crediti verso Clienti</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($crediti_clienti, 2, ',', '.') }} €</h3>
                                <p class="text-muted mb-0">Al lordo dell'IVA (incluse proforma)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debiti verso Fornitori -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Debiti verso Fornitori</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="mb-0">{{ number_format($debiti_fornitori, 2, ',', '.') }} €</h3>
                                <p class="text-muted mb-0">Al lordo dell'IVA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="row">
                <!-- Pagamenti Scaduti -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Incassi Scaduti</h4>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    Totale: € {{ number_format($totale_da_incassare, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless datatable" style="width:100%">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">Fornitore</th>
                                        <th scope="col">Scaduto</th>
                                        <th scope="col">Importo</th>
                                        <th scope="col" style="width: 50px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($incassi_da_ricevere as $pagamento)
                                        <tr>
                                            <td>{{ $pagamento->ragione_sociale_fatturazione }}<small><?php echo ($pagamento->note != '')?'<br>'.$pagamento->note:'' ?></small></td>
                                            <td>
                                                    <?php if($pagamento->giorni_scaduto > 0){ ?>
                                                <span class="badge bg-danger">
                                                        Scaduto da {{ $pagamento->giorni_scaduto }} giorni
                                                    </span><br>
                                                <?php } else if($pagamento->giorni_scaduto <= 0){ ?>
                                                <span class="badge bg-success">
                                                            Scade tra {{ abs($pagamento->giorni_scaduto) }} giorni
                                                    </span><br>
                                                <?php } ?>

                                                <small class="text-muted">
                                                    Scadenza: {{ date('d/m/Y', strtotime($pagamento->data_scadenza)) }}
                                                </small>
                                            </td>
                                            <td>€ {{ number_format($pagamento->importo - $pagamento->importo_pagato, 2, ',', '.') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <?php if($pagamento->id_dotes != ''){ ?>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('utente/dettaglio_documento/'.$pagamento->id_dotes) }}">
                                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Visualizza
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="registraPagamento({{ $pagamento->id }})">
                                                                <i class="ri-money-euro-circle-fill align-bottom me-2 text-muted"></i> Registra Incasso
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nessun pagamento scaduto</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incassi Scaduti -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Pagamenti Scaduti</h4>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    Totale: € {{ number_format($totale_da_pagare, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Scaduto</th>
                                        <th scope="col">Importo</th>
                                        <th scope="col" style="width: 50px;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($pagamenti_da_effettuare as $incasso)
                                        <tr>
                                            <td>{{ $incasso->ragione_sociale_fatturazione }}<small><?php echo ($incasso->note != '')?'<br>'.$incasso->note:'' ?></small></td>
                                            <td>
                                                @if(strpos(strtolower($incasso->note ?? ''), 'provvigione') !== false && isset($incasso->agente_nome))
                                                    <span class="badge bg-info">Provvigione Agente</span><br>
                                                    <strong>{{ $incasso->agente_nome }} {{ $incasso->agente_cognome }}</strong><br>
                                                    <small>Rif. fattura {{ $incasso->numero_doc }}</small><br>
                                                    <small>Cliente: {{ $incasso->ragione_sociale_fatturazione }}</small>
                                                @else
                                                    {{ $incasso->ragione_sociale_fatturazione }}
                                                @endif
                                            </td>
                                            <td>€ {{ number_format($incasso->importo - $incasso->importo_pagato, 2, ',', '.') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">

                                                        <?php if($incasso->id_dotes != ''){ ?>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('utente/dettaglio_documento/'.$incasso->id_dotes) }}">
                                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Visualizza
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="registraIncasso({{ $incasso->id }})">
                                                                <i class="ri-money-euro-circle-fill align-bottom me-2 text-muted"></i> Registra Pagamento
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nessun incasso scaduto</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- Grafico Fatturato Mensile -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Andamento Fatturato Mensile</h4>
                    </div>
                    <div class="card-body">
                        <div id="fatturato_mensile_chart" class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Registrazione Pagamento/Incasso -->
<div class="modal fade" id="modalRegistrazione" tabindex="-1" aria-labelledby="modalRegistrazioneLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrazioneLabel">Registra Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('utente/registra_pagamento') }}" method="POST">
                @csrf
                <input type="hidden" name="id_scadenza" id="id_scadenza">
                <input type="hidden" name="tipo_movimento" id="tipo_movimento">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Importo da pagare</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="text" class="form-control" id="importo_totale" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Importo pagato</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" name="importo_pagato" id="importo_pagato" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data pagamento</label>
                        <input type="date" class="form-control" name="data_pagamento" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Modalità pagamento</label>
                        <select class="form-select" name="modalita_pagamento" required>
                            <option value="bonifico">Bonifico</option>
                            <option value="rid">RID</option>
                            <option value="riba">RIBA</option>
                            <option value="contanti">Contanti</option>
                            <option value="assegno">Assegno</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Registra</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer')


<!-- JavaScript per gestire la modal -->
<script>
    function registraPagamento(id) {
        apriModalRegistrazione(id, 'uscita');
    }

    function registraIncasso(id) {
        apriModalRegistrazione(id, 'entrata');
    }

    function apriModalRegistrazione(id, tipo) {
        // Recupera i dati della scadenza
        fetch(`{{ url('utente/get_scadenza') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                // Popola la modal
                document.getElementById('id_scadenza').value = id;
                document.getElementById('tipo_movimento').value = tipo;
                document.getElementById('importo_totale').value = data.importo;
                document.getElementById('importo_pagato').value = data.importo;
                document.getElementById('note').value = data.note;

                // Aggiorna il titolo della modal
                document.getElementById('modalRegistrazioneLabel').textContent =
                    tipo === 'entrata' ? 'Registra Incasso' : 'Registra Pagamento';

                // Mostra la modal
                new bootstrap.Modal(document.getElementById('modalRegistrazione')).show();
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore nel recupero dei dati della scadenza');
            });
    }

    // Validazione importo pagato
    document.getElementById('importo_pagato').addEventListener('input', function(e) {
        const max = parseFloat(this.max);
        const value = parseFloat(this.value);
        if (value > max) {
            this.value = max;
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Grafico Fatturato Mensile
    var options = {
        series: [{
            name: 'Fatturato',
            data: [
                @php
                // Inizializza un array di 12 mesi con valori a 0
                $dati_mensili = array_fill(1, 12, 0);
                
                // Popola l'array con i dati effettivi
                foreach($fatturato_mensile as $mese) {
                    $dati_mensili[$mese->mese] = $mese->totale_tot;
                }
                
                // Converti l'array in una stringa di valori separati da virgola
                echo implode(',', $dati_mensili);
                @endphp
            ]
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic']
        },
        yaxis: {
            title: {
                text: '€ (Migliaia)'
            }
        },
        fill: {
            opacity: 1
        }
    };

    var chart = new ApexCharts(document.querySelector("#fatturato_mensile_chart"), options);
    chart.render();
</script>
