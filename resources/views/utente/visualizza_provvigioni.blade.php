@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Provvigioni di: {{ $utente->nome }} {{ $utente->cognome }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Utente</a></li>
                            <li class="breadcrumb-item active">Provvigioni</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-12">
                <!-- Card Filtri -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Filtri</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            @if($utente->admin_azienda == 1)
                                <div class="col-md-3">
                                    <label class="form-label">Agente</label>
                                    <select name="filtro_agente" class="form-control form-select">
                                        <option value="">Tutti gli agenti</option>
                                        @foreach($agenti as $agente)
                                            <option value="{{ $agente->id }}" {{ $filtro_agente == $agente->id ? 'selected' : '' }}>
                                                {{ $agente->nome }} {{ $agente->cognome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-{{ $utente->admin_azienda == 1 ? '3' : '4' }}">
                                <label class="form-label">Stato Provvigioni</label>
                                <select name="filtro_stato" class="form-control form-select">
                                    <option value="tutte" {{ $filtro_stato == 'tutte' ? 'selected' : '' }}>Tutte</option>
                                    <option value="pagate" {{ $filtro_stato == 'pagate' ? 'selected' : '' }}>Pagate</option>
                                    <option value="da_pagare" {{ $filtro_stato == 'da_pagare' ? 'selected' : '' }}>Da pagare</option>
                                </select>
                            </div>
                            <div class="col-md-{{ $utente->admin_azienda == 1 ? '2' : '3' }}">
                                <label class="form-label">Data Inizio</label>
                                <input type="date" name="filtro_data_inizio" class="form-control" value="{{ $filtro_data_inizio }}">
                            </div>
                            <div class="col-md-{{ $utente->admin_azienda == 1 ? '2' : '3' }}">
                                <label class="form-label">Data Fine</label>
                                <input type="date" name="filtro_data_fine" class="form-control" value="{{ $filtro_data_fine }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filtra</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card Statistiche -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="text-uppercase fw-medium text-muted mb-0">Totale Fatturato</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span>&euro; {{ number_format($totali['importo_fatturato'], 2, ',', '.') }}</span>
                                        </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-success rounded fs-3">
                                            <i class="ri-file-list-3-line text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="text-uppercase fw-medium text-muted mb-0">Totale Provvigioni</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span>&euro; {{ number_format($totali['importo_provvigioni'], 2, ',', '.') }}</span>
                                        </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-primary rounded fs-3">
                                            <i class="ri-wallet-3-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="text-uppercase fw-medium text-muted mb-0">Provvigioni Pagate</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span>&euro; {{ number_format($totali['importo_pagate'], 2, ',', '.') }}</span>
                                        </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-success rounded fs-3">
                                            <i class="ri-check-double-line text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="text-uppercase fw-medium text-muted mb-0">Provvigioni da Ricevere</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span>&euro; {{ number_format($totali['importo_da_pagare'], 2, ',', '.') }}</span>
                                        </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-warning rounded fs-3">
                                            <i class="ri-time-line text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sezione per amministratori: Gestione delle configurazioni di provvigione -->
                @if($utente->admin_azienda == 1)
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title flex-grow-1 mb-0">Configurazioni Provvigioni</h5>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalAggiungiProvvigione">
                                        <i class="ri-add-line align-middle me-1"></i> Nuova Provvigione Manuale
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAggiungiConfigurazione">
                                        <i class="ri-add-line align-middle me-1"></i> Nuova Configurazione
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable-configurazioni" class="table table-bordered table-hover dt-responsive nowrap">
                                    <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Agente</th>
                                        <th>Tipo</th>
                                        <th>Valore</th>
                                        <th>Descrizione</th>
                                        <th>Data Creazione</th>
                                        <th>Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tipi_provvigioni as $config)
                                        <tr>
                                            <td>{{ $config->id }}</td>
                                            <td>{{ $config->nome }} {{ $config->cognome }}</td>
                                            <td>{{ ucfirst($config->tipo_provvigione) }}</td>
                                            <td>
                                                @if($config->tipo_provvigione == 'percentuale')
                                                    {{ number_format($config->valore, 2) }}%
                                                    @else
                                                        &euro; {{ number_format($config->valore, 2, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>{{ $config->descrizione }}</td>
                                            <td>{{ date('d/m/Y', strtotime($config->data_creazione)) }}</td>
                                            <td>
                                                <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questa configurazione?')">
                                                    <input type="hidden" name="id" value="{{ $config->id }}">
                                                    <button type="submit" name="elimina_configurazione" value="1" class="btn btn-sm btn-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Modal per aggiungere provvigione manuale -->
                    <div class="modal fade" id="modalAggiungiProvvigione" tabindex="-1" aria-labelledby="modalAggiungiProvvigioneLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAggiungiProvvigioneLabel">Aggiungi Provvigione Manuale</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="id_agente_provv" class="form-label">Agente</label>
                                                <select class="form-select" id="id_agente_provv" name="id_agente_provv" required>
                                                    <option value="">Seleziona agente</option>
                                                    @foreach($agenti as $agente)
                                                        <option value="{{ $agente->id }}">{{ $agente->nome }} {{ $agente->cognome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="importo_provvigione" class="form-label">Importo Provvigione</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">€</span>
                                                    <input type="number" class="form-control" id="importo_provvigione" name="importo_provvigione" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="riferimento_manuale" class="form-label">Riferimento</label>
                                                <input type="text" class="form-control" id="riferimento_manuale" name="riferimento_manuale" placeholder="Es. Provvigione per cliente X">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="data_scadenza_provv" class="form-label">Data Scadenza</label>
                                                <input type="date" class="form-control" id="data_scadenza_provv" name="data_scadenza_provv" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note_provv" class="form-label">Note</label>
                                            <textarea class="form-control" id="note_provv" name="note_provv" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                        <button type="submit" name="aggiungi_provvigione_manuale" value="1" class="btn btn-success">Crea Provvigione</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal per aggiungere configurazioni -->
                    <div class="modal fade" id="modalAggiungiConfigurazione" tabindex="-1" aria-labelledby="modalAggiungiConfigurazioneLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAggiungiConfigurazioneLabel">Nuova Configurazione Provvigione</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="id_agente" class="form-label">Agente</label>
                                            <select class="form-select" id="id_agente" name="id_agente" required>
                                                <option value="">Seleziona agente</option>
                                                @foreach($agenti as $agente)
                                                    <option value="{{ $agente->id }}">{{ $agente->nome }} {{ $agente->cognome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipo_provvigione" class="form-label">Tipo Provvigione</label>
                                            <select class="form-select" id="tipo_provvigione" name="tipo_provvigione" required>
                                                <option value="percentuale">Percentuale</option>
                                                <option value="fisso">Importo Fisso</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="valore" class="form-label">Valore</label>
                                            <input type="number" class="form-control" id="valore" name="valore" step="0.01" min="0" required>
                                            <small class="text-muted" id="valore_hint">Inserire la percentuale (es: 5 per 5%)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descrizione" class="form-label">Descrizione</label>
                                            <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                        <button type="submit" name="aggiungi_configurazione" value="1" class="btn btn-primary">Salva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Pulsante per pagamento multiplo (solo per admin) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPagamentoMultiplo">
                                <i class="ri-money-euro-circle-line me-1"></i> Paga Provvigioni Selezionate
                            </button>
                        </div>
                    </div>

                    <!-- Modal per pagamento multiplo -->
                    <div class="modal fade" id="modalPagamentoMultiplo" tabindex="-1" aria-labelledby="modalPagamentoMultiploLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalPagamentoMultiploLabel">Pagamento Provvigioni Multiple</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="provvigioni_selezionate" id="provvigioni_selezionate">
                                        <div class="mb-3">
                                            <label for="data_pagamento_multipla" class="form-label">Data Pagamento</label>
                                            <input type="date" class="form-control" id="data_pagamento_multipla" name="data_pagamento_multipla" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note_multipla" class="form-label">Note</label>
                                            <textarea class="form-control" id="note_multipla" name="note_multipla" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                        <button type="submit" name="paga_multiple" value="1" class="btn btn-primary">Paga Selezionate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Per agenti visualizza solo le configurazioni associate a loro -->
                @if($utente->id_tipologia == 1)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Le mie Configurazioni Provvigioni</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Tipo Provvigione</th>
                                        <th>Valore</th>
                                        <th>Descrizione</th>
                                        <th>Data Configurazione</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($tipi_provvigioni) > 0)
                                        @foreach($tipi_provvigioni as $tipo)
                                            <tr>
                                                <td>{{ ucfirst($tipo->tipo_provvigione) }}</td>
                                                <td>
                                                    @if($tipo->tipo_provvigione == 'percentuale')
                                                        {{ number_format($tipo->valore, 2) }}%
                                                        @else
                                                            &euro; {{ number_format($tipo->valore, 2, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td>{{ $tipo->descrizione }}</td>
                                                <td>{{ date('d/m/Y', strtotime($tipo->data_creazione)) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">Nessuna configurazione di provvigione trovata</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Card Elenco Provvigioni -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Elenco Provvigioni</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-provvigioni" class="table table-bordered table-hover dt-responsive nowrap">
                                <thead class="table-light">
                                <tr>
                                    @if($utente->admin_azienda == 1)
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                    @endif
                                    <th>Riferimento</th>
                                    @if($utente->admin_azienda == 1)
                                        <th>Agente</th>
                                    @endif
                                    <th>Cliente</th>
                                    <th>Data Fattura</th>
                                    <th>Importo Fattura</th>
                                    <th>Provvigione</th>
                                    <th>Tipo</th>
                                    <th>Stato</th>
                                    <th>Data Pagamento</th>
                                    <th>Note</th>
                                    @if($utente->admin_azienda == 1)
                                        <th>Azioni</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($provvigioni) > 0)
                                    @foreach($provvigioni as $provvigione)
                                        <tr>
                                            @if($utente->admin_azienda == 1)
                                                <td>
                                                    @if($provvigione->pagata == 0)
                                                        <div class="form-check">
                                                            <input class="form-check-input select-provvigione" type="checkbox" value="{{ $provvigione->id }}">
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>{{ $provvigione->riferimento_fattura ?? $provvigione->numero_doc }}</td>
                                            @if($utente->admin_azienda == 1)
                                                <td>{{ $provvigione->agente_nome }} {{ $provvigione->agente_cognome }}</td>
                                            @endif
                                            <td>{{ $provvigione->ragione_sociale }}</td>
                                            <td>{{ date('d/m/Y', strtotime($provvigione->data_doc)) }}</td>
                                            <td>&euro; {{ number_format($provvigione->importo_fattura, 2, ',', '.') }}</td>
                                            <td>&euro; {{ number_format($provvigione->importo_provvigione, 2, ',', '.') }}</td>
                                            <td>
                                                @if($provvigione->tipo_provvigione == 'percentuale')
                                                    {{ number_format($provvigione->valore, 2) }}%
                                                    @else
                                                        &euro; {{ number_format($provvigione->valore, 2, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($provvigione->pagata == 1)
                                                    <span class="badge bg-success">Pagata</span>
                                                @else
                                                    <span class="badge bg-warning">Da Pagare</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($provvigione->data_pagamento)
                                                    {{ date('d/m/Y', strtotime($provvigione->data_pagamento)) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $provvigione->note ?? '-' }}</td>
                                            @if($utente->admin_azienda == 1)
                                                <td>
                                                    @if($provvigione->pagata == 0)
                                                        <button type="button" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalPagaProvvigione{{ $provvigione->id }}">
                                                            <i class="ri-money-euro-circle-line"></i>
                                                        </button>

                                                        <!-- Modal per pagamento singola provvigione -->
                                                        <div class="modal fade" id="modalPagaProvvigione{{ $provvigione->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Paga Provvigione</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="post">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id" value="{{ $provvigione->id }}">
                                                                            <div class="mb-3">
                                                                                <label for="dataPagamento{{ $provvigione->id }}" class="form-label">Data Pagamento</label>
                                                                                <input type="date" class="form-control" id="dataPagamento{{ $provvigione->id }}" name="data_pagamento" value="{{ date('Y-m-d') }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="note{{ $provvigione->id }}" class="form-label">Note</label>
                                                                                <textarea class="form-control" id="note{{ $provvigione->id }}" name="note" rows="3"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                                            <button type="submit" name="paga_provvigione" value="1" class="btn btn-primary">Paga</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-dark">Pagata</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ $utente->admin_azienda == 1 ? '12' : '9' }}" class="text-center">Nessuna provvigione trovata</td>
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

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        // Inizializza DataTable per provvigioni
        $('#datatable-provvigioni').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            "pageLength": 25,
            "order": [[{{ $utente->admin_azienda == 1 ? '4' : '3' }}, "desc"]], // Ordina per data fattura (discendente)
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        @if($utente->admin_azienda == 1)
        // Inizializza DataTable per configurazioni
        $('#datatable-configurazioni').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            "pageLength": 10,
            "order": [[5, "desc"]] // Ordina per data creazione (discendente)
        });

        // Gestione cambio tipo provvigione
        $('#tipo_provvigione').change(function() {
            if ($(this).val() == 'percentuale') {
                $('#valore_hint').text('Inserire la percentuale (es: 5 per 5%)');
            } else {
                $('#valore_hint').text('Inserire l\'importo fisso in euro');
            }
        });

        // Seleziona/deseleziona tutte le provvigioni
        $('#selectAll').change(function() {
            $('.select-provvigione').prop('checked', this.checked);
            updateSelectedProvvigioni();
        });

        // Aggiorna l'elenco delle provvigioni selezionate
        $(document).on('change', '.select-provvigione', function() {
            updateSelectedProvvigioni();
        });

        function updateSelectedProvvigioni() {
            var selected = [];
            $('.select-provvigione:checked').each(function() {
                selected.push($(this).val());
            });
            $('#provvigioni_selezionate').val(selected.join(','));
        }
        @endif
    });
</script>