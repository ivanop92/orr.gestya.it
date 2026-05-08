@include('utente.common.header')
<div class="page-content">
    <div class="container-fluid">

        @php
            // Recupera le voci extra
            $voci_extra = DB::table('commesse_extra')
                ->where('id_commessa', $commessa->id)
                ->orderBy('data', 'desc')
                ->get();
            
            // Calcola i totali delle voci extra
            $totale_ricavi_extra = $voci_extra->where('tipo', 'ricavo')->sum('importo');
            $totale_costi_extra = $voci_extra->where('tipo', 'costo')->sum('importo');
            
            // Aggiorna i totali complessivi (include costi produzione: materiali + manodopera)
            $totale_ricavi = $totale_fatturato + $totale_ricavi_extra;
            $totale_costi_aggiornato = $totale_costi + $totale_costi_extra + $totale_costi_produzione;
            $margine_aggiornato = $totale_ricavi - $totale_costi_aggiornato;

            // Calcolo del totale costi attività in base al completamento
            $totale_costi_attivita_effettivi = 0;
            foreach($attivita as $attivita_item) {
                $totale_costi_attivita_effettivi += ($attivita_item->costo * ($attivita_item->completamento / 100));
            }

            // Calcolo del cashflow considerando i costi attività in base al completamento
            $cashflow = ($totale_incassato + $totale_ricavi_extra) - ($totale_pagato + $totale_costi_attivita_effettivi + $totale_costi_extra);
            $cashflow_positivo = $cashflow >= 0;
        @endphp

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard Commessa: {{ $commessa->codice_commessa }} - {{ $commessa->descrizione }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/commesse') }}">Commesse</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Riepilogo Commessa -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Riepilogo Commessa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Codice Commessa:</h5>
                                    <p class="text-primary">{{ $commessa->codice_commessa }}</p>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Descrizione:</h5>
                                    <p>{{ $commessa->descrizione }}</p>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Cliente:</h5>
                                    <p>{{ DB::table('clienti')->where('id', $commessa->id_utente)->value('ragione_sociale') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Data Inizio:</h5>
                                    <p>{{ date('d/m/Y', strtotime($commessa->data_apertura)) }}</p>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Data Fine Prevista:</h5>
                                    <p>{{ date('d/m/Y', strtotime($commessa->data_chiusura)) }}</p>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-muted fw-medium">Stato:</h5>
                                    <p>
                                        @if($commessa->stato == 'aperta')
                                            <span class="badge bg-success">Aperta</span>
                                        @elseif($commessa->stato == 'in_corso')
                                            <span class="badge bg-warning">In Corso</span>
                                        @elseif($commessa->stato == 'chiusa')
                                            <span class="badge bg-danger">Chiusa</span>
                                        @elseif($commessa->stato == 'archiviata')
                                            <span class="badge bg-secondary">Archiviata</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicatori Economici -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Totale Ricavi</p>
                                <h4 class="fs-22 fw-semibold mb-0">€ {{ number_format($totale_ricavi, 2, ',', '.') }}</h4>
                                <small class="text-muted">
                                    Fatturato: € {{ number_format($totale_fatturato, 2, ',', '.') }} | 
                                    Extra: € {{ number_format($totale_ricavi_extra, 2, ',', '.') }}
                                </small>
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
                                <p class="text-uppercase fw-medium text-muted mb-0">Totale Costi</p>
                                <h4 class="fs-22 fw-semibold mb-0">€ {{ number_format($totale_costi_aggiornato, 2, ',', '.') }}</h4>
                                <small class="text-muted">
                                    Documenti: &euro; {{ number_format($totale_costi_documenti, 2, ',', '.') }} |
                                    Attivita: &euro; {{ number_format($totale_costi_attivita, 2, ',', '.') }} |
                                    Produzione: &euro; {{ number_format($totale_costi_produzione, 2, ',', '.') }} |
                                    Extra: &euro; {{ number_format($totale_costi_extra, 2, ',', '.') }}
                                </small>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-danger rounded fs-3">
                                    <i class="ri-shopping-bag-line text-danger"></i>
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
                                <p class="text-uppercase fw-medium text-muted mb-0">Margine</p>
                                <h4 class="fs-22 fw-semibold mb-0">€ {{ number_format($margine_aggiornato, 2, ',', '.') }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-info rounded fs-3">
                                    <i class="ri-funds-line text-info"></i>
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
                                <p class="text-uppercase fw-medium text-muted mb-0">Margine %</p>
                                <h4 class="fs-22 fw-semibold mb-0">
                                    @if($totale_ricavi > 0)
                                        {{ number_format(($margine_aggiornato / $totale_ricavi) * 100, 2, ',', '.') }}%
                                    @else
                                        0,00%
                                    @endif
                                </h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary rounded fs-3">
                                    <i class="ri-percent-line text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicatori Finanziari -->
        <div class="row mt-4">
            @php
                // Calcolo dei valori per i quadranti finanziari
                // Calcolo importo totale fatture attive
                $totale_fatture_attive = $documenti_ciclo_attivo->where('cd_do', 'FTV')->sum('totale');
                
                // Calcolo importo totale fatture passive
                $totale_fatture_passive = $documenti_ciclo_passivo->where('cd_do', 'FTP')->sum('totale');
                
                // Calcolo importo totale incassato (documenti + manuali)
                $totale_incassato = $incassi->sum('importo_pagato') + $totale_incassi_manuali;

                // Calcolo importo totale pagato (documenti + manuali)
                $totale_pagato = $pagamenti->sum('importo_pagato') + $totale_pagamenti_manuali;
                
                // Calcolo importo da incassare (totale fatture attive - totale incassato)
                $totale_da_incassare = $totale_fatture_attive - $totale_incassato;
                
                // Calcolo importo da pagare (totale fatture passive - totale pagato)
                $totale_da_pagare = $totale_fatture_passive - $totale_pagato;
                
                // Assicuriamoci che i valori non siano negativi
                $totale_da_incassare = max(0, $totale_da_incassare);
                $totale_da_pagare = max(0, $totale_da_pagare);
            @endphp
            
            <div class="col-xl-4 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Da Incassare</p>
                                <h4 class="fs-22 fw-semibold mb-0 text-warning">€ {{ number_format($totale_da_incassare, 2, ',', '.') }}</h4>
                                <small class="text-muted">
                                    {{ number_format(($totale_da_incassare / max(1, $totale_fatture_attive)) * 100, 1) }}% del fatturato
                                </small>
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
            
            <div class="col-xl-4 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Da Pagare</p>
                                <h4 class="fs-22 fw-semibold mb-0 text-danger">€ {{ number_format($totale_da_pagare, 2, ',', '.') }}</h4>
                                <small class="text-muted">
                                    {{ number_format(($totale_da_pagare / max(1, $totale_fatture_passive)) * 100, 1) }}% dei costi
                                </small>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-danger rounded fs-3">
                                    <i class="ri-time-line text-danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @php
                // Calcolo del cashflow considerando anche attività e voci extra
                $cashflow = ($totale_incassato + $totale_ricavi_extra) - ($totale_pagato + $totale_costi_attivita_effettivi + $totale_costi_extra + $totale_costi_produzione);
                $cashflow_positivo = $cashflow >= 0;
            @endphp
            
            <div class="col-xl-4 col-md-6 mt-md-0 mt-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Cashflow</p>
                                <h4 class="fs-22 fw-semibold mb-0 {{ $cashflow_positivo ? 'text-success' : 'text-danger' }}">
                                    € {{ number_format($cashflow, 2, ',', '.') }}
                                </h4>
                                <small class="text-muted">
                                    {{ $cashflow_positivo ? 'Commessa in attivo' : 'Commessa in passivo' }}<br>
                                    Incassi: € {{ number_format($totale_incassato + $totale_ricavi_extra, 2, ',', '.') }} |
                                    Uscite: € {{ number_format($totale_pagato + $totale_costi_attivita_effettivi + $totale_costi_extra, 2, ',', '.') }}
                                </small>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title {{ $cashflow_positivo ? 'bg-soft-success' : 'bg-soft-danger' }} rounded fs-3">
                                    <i class="{{ $cashflow_positivo ? 'ri-exchange-dollar-line text-success' : 'ri-exchange-dollar-line text-danger' }}"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigazione -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#documenti-ciclo-attivo" role="tab" id="tab-documenti-ciclo-attivo">
                                    <i class="fas fa-file-invoice me-1 align-middle"></i> Ciclo Attivo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#documenti-ciclo-passivo" role="tab" id="tab-documenti-ciclo-passivo">
                                    <i class="fas fa-file-invoice-dollar me-1 align-middle"></i> Ciclo Passivo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#incassi-pagamenti" role="tab" id="tab-incassi-pagamenti">
                                    <i class="fas fa-money-bill-wave me-1 align-middle"></i> Incassi e Pagamenti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#produzione" role="tab" id="tab-produzione">
                                    <i class="fas fa-industry me-1 align-middle"></i> Produzione (ODL)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#movimenti-magazzino" role="tab" id="tab-movimenti-magazzino">
                                    <i class="fas fa-warehouse me-1 align-middle"></i> Movimenti Magazzino
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#attivita" role="tab" id="tab-attivita">
                                    <i class="fas fa-tasks me-1 align-middle"></i> Attività
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#voci-extra" role="tab" id="tab-voci-extra">
                                    <i class="fas fa-euro-sign me-1 align-middle"></i> Voci Extra
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cashflow-details" role="tab" id="tab-cashflow">
                                    <i class="fas fa-chart-line me-1 align-middle"></i> Cashflow
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Documenti Ciclo Attivo -->
                            <div class="tab-pane active" id="documenti-ciclo-attivo" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Numero</th>
                                                <th>Data</th>
                                                <th>Cliente</th>
                                                <th>Imponibile</th>
                                                <th>Imposta</th>
                                                <th>Totale</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($documenti_ciclo_attivo) > 0)
                                                @foreach($documenti_ciclo_attivo as $doc)
                                                    <tr>
                                                        <td>{{ $doc->cd_do }}</td>
                                                        <td>{{ $doc->numero_doc }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($doc->data_doc)) }}</td>
                                                        <td>{{ $doc->ragione_sociale }}</td>
                                                        <td>€ {{ number_format($doc->imponibile, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($doc->imposta, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($doc->totale, 2, ',', '.') }}</td>
                                                        <td>
                                                            <a href="{{ url('utente/dettaglio_documento/'.$doc->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ url('utente/modifica_documento/'.$doc->id) }}" class="btn btn-sm btn-info">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">Nessun documento trovato</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab Documenti Ciclo Passivo -->
                            <div class="tab-pane" id="documenti-ciclo-passivo" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Numero</th>
                                                <th>Data</th>
                                                <th>Fornitore</th>
                                                <th>Imponibile</th>
                                                <th>Imposta</th>
                                                <th>Totale</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($documenti_ciclo_passivo) > 0)
                                                @foreach($documenti_ciclo_passivo as $doc)
                                                    <tr>
                                                        <td>{{ $doc->cd_do }}</td>
                                                        <td>{{ $doc->numero_doc }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($doc->data_doc)) }}</td>
                                                        <td>{{ $doc->ragione_sociale }}</td>
                                                        <td>€ {{ number_format($doc->imponibile, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($doc->imposta, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($doc->totale, 2, ',', '.') }}</td>
                                                        <td>
                                                            <a href="{{ url('utente/dettaglio_documento/'.$doc->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ url('utente/modifica_documento/'.$doc->id) }}" class="btn btn-sm btn-info">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">Nessun documento trovato</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab Incassi e Pagamenti -->
                            <div class="tab-pane" id="incassi-pagamenti" role="tabpanel">
                                <div class="d-flex justify-content-end mb-3 gap-2">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalMovimentoCassa" onclick="document.getElementById('tipo_movimento').value='entrata'; document.getElementById('modal_titolo_movimento').innerText='Registra Incasso';">
                                        <i class="ri-add-line me-1"></i> Registra Incasso
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalMovimentoCassa" onclick="document.getElementById('tipo_movimento').value='uscita'; document.getElementById('modal_titolo_movimento').innerText='Registra Pagamento';">
                                        <i class="ri-add-line me-1"></i> Registra Pagamento
                                    </button>
                                </div>
                                <div class="row">
                                    <!-- Incassi -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Incassi</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Documento</th>
                                                        <th>Importo Totale</th>
                                                        <th>Importo Pagato</th>
                                                        <th>Data Pagamento</th>
                                                        <th>Metodo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(count($incassi) > 0)
                                                        @foreach($incassi as $incasso)
                                                            @php
                                                                // Recupera l'importo totale del documento se non disponibile
                                                                $importo_totale = $incasso->importo_totale ?? DB::table('dotes')->where('id', $incasso->id_dotes)->value('totale') ?? $incasso->importo;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $incasso->cd_do }} {{ $incasso->numero_doc }}</td>
                                                                <td>€ {{ number_format($importo_totale, 2, ',', '.') }}</td>
                                                                <td>€ {{ number_format($incasso->importo_pagato, 2, ',', '.') }}</td>
                                                                <td>
                                                                    @if(!empty($incasso->data_pagamento))
                                                                        {{ date('d/m/Y', strtotime($incasso->data_pagamento)) }}
                                                                    @else
                                                                        <span class="badge bg-warning">In attesa</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $incasso->modalita_pagamento ?? $incasso->metodo_pagamento ?? 'N/D' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @if(count($incassi_manuali) == 0)
                                                        <tr>
                                                            <td colspan="5" class="text-center">Nessun incasso trovato</td>
                                                        </tr>
                                                        @endif
                                                    @endif
                                                    @foreach($incassi_manuali as $im)
                                                        <tr class="table-success table-active">
                                                            <td><span class="badge bg-success">Manuale</span></td>
                                                            <td>{{ $im->descrizione }}</td>
                                                            <td>€ {{ number_format($im->importo, 2, ',', '.') }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($im->data_movimento)) }}</td>
                                                            <td>
                                                                {{ $im->modalita_pagamento ?? '-' }}
                                                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Eliminare questo movimento?')">
                                                                    @csrf
                                                                    <input type="hidden" name="id_movimento" value="{{ $im->id }}">
                                                                    <button type="submit" name="elimina_movimento_cassa" value="1" class="btn btn-sm btn-outline-danger p-0 px-1"><i class="ri-delete-bin-line"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-end">Totale Incassato:</th>
                                                        <th colspan="3">€ {{ number_format($totale_incassato, 2, ',', '.') }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Pagamenti -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Pagamenti</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Documento</th>
                                                        <th>Importo Totale</th>
                                                        <th>Importo Pagato</th>
                                                        <th>Data Pagamento</th>
                                                        <th>Metodo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(count($pagamenti) > 0)
                                                        @foreach($pagamenti as $pagamento)
                                                            @php
                                                                // Recupera l'importo totale del documento se non disponibile
                                                                $importo_totale = $pagamento->importo_totale ?? DB::table('dotes')->where('id', $pagamento->id_dotes)->value('totale') ?? $pagamento->importo;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $pagamento->cd_do }} {{ $pagamento->numero_doc }}</td>
                                                                <td>€ {{ number_format($importo_totale, 2, ',', '.') }}</td>
                                                                <td>€ {{ number_format($pagamento->importo_pagato, 2, ',', '.') }}</td>
                                                                <td>
                                                                    @if(!empty($pagamento->data_pagamento))
                                                                        {{ date('d/m/Y', strtotime($pagamento->data_pagamento)) }}
                                                                    @else
                                                                        <span class="badge bg-warning">In attesa</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $pagamento->modalita_pagamento ?? $pagamento->metodo_pagamento ?? 'N/D' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @if(count($pagamenti_manuali) == 0)
                                                        <tr>
                                                            <td colspan="5" class="text-center">Nessun pagamento trovato</td>
                                                        </tr>
                                                        @endif
                                                    @endif
                                                    @foreach($pagamenti_manuali as $pm)
                                                        <tr class="table-warning table-active">
                                                            <td><span class="badge bg-warning text-dark">Manuale</span></td>
                                                            <td>{{ $pm->descrizione }}</td>
                                                            <td>€ {{ number_format($pm->importo, 2, ',', '.') }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($pm->data_movimento)) }}</td>
                                                            <td>
                                                                {{ $pm->modalita_pagamento ?? '-' }}
                                                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Eliminare questo movimento?')">
                                                                    @csrf
                                                                    <input type="hidden" name="id_movimento" value="{{ $pm->id }}">
                                                                    <button type="submit" name="elimina_movimento_cassa" value="1" class="btn btn-sm btn-outline-danger p-0 px-1"><i class="ri-delete-bin-line"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-end">Totale Pagato:</th>
                                                        <th colspan="3">€ {{ number_format($totale_pagato, 2, ',', '.') }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Movimenti Magazzino -->
                            <!-- Tab Produzione (ODL) -->
                            <div class="tab-pane" id="produzione" role="tabpanel">
                                <!-- Card riepilogo costi produzione -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card border card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Costi Materiali</p>
                                                        <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi_materiali[0]->totale, 2, ',', '.') }}</h4>
                                                        <small class="text-muted">Calcolato dai movimenti di scarico ODL</small>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                                            <i class="ri-stack-line text-danger"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Costi Manodopera</p>
                                                        <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi_manodopera[0]->totale, 2, ',', '.') }}</h4>
                                                        <small class="text-muted">{{ number_format($costi_manodopera[0]->ore_totali, 1, ',', '.') }} ore lavorate</small>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-soft-primary rounded fs-3">
                                                            <i class="ri-user-settings-line text-primary"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Totale Costi Produzione</p>
                                                        <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($totale_costi_produzione, 2, ',', '.') }}</h4>
                                                        <small class="text-muted">Materiali + Manodopera</small>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-soft-warning rounded fs-3">
                                                            <i class="ri-calculator-line text-warning"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                    <i class="ri-information-line me-2"></i>
                                    Gli ordini di lavoro mostrati qui sono quelli generati da documenti associati a questa commessa.
                                    Quando un ordine viene mandato in produzione (ODL), i movimenti di magazzino e i costi di manodopera vengono automaticamente collegati.
                                    Per vedere i costi manodopera, assicurarsi di assegnare un operatore alle fasi ODL e di impostare il costo orario nell'anagrafica dipendenti.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <!-- Tabella ODL -->
                                <h5 class="mb-3">Ordini di Lavoro</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>N. ODL</th>
                                                <th>Documento Origine</th>
                                                <th>Articolo</th>
                                                <th>Quantita</th>
                                                <th>Data</th>
                                                <th>Stato</th>
                                                <th>Data Chiusura</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($odl_commessa) > 0)
                                                @foreach($odl_commessa as $odl)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url('utente/dettaglio_odl/'.$odl->id) }}">
                                                                #{{ $odl->numero }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($odl->cd_do && $odl->numero_doc)
                                                                {{ $odl->cd_do }} {{ $odl->numero_doc }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $odl->articolo_nome ?? 'N/A' }} {{ $odl->codice_articolo ? '('.$odl->codice_articolo.')' : '' }}</td>
                                                        <td>{{ $odl->qta }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($odl->data)) }}</td>
                                                        <td>
                                                            @if($odl->stato == 0)
                                                                <span class="badge bg-secondary">In Attesa</span>
                                                            @elseif($odl->stato == 1)
                                                                <span class="badge bg-warning">In Lavorazione</span>
                                                            @elseif($odl->stato == 2)
                                                                <span class="badge bg-success">Completato</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $odl->data_chiusura ? date('d/m/Y', strtotime($odl->data_chiusura)) : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">Nessun ordine di lavoro collegato a questa commessa</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Dettaglio Manodopera -->
                                @if(count($dettaglio_manodopera) > 0)
                                <h5 class="mt-4 mb-3">Dettaglio Costi Manodopera per Dipendente</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Dipendente</th>
                                                <th>Costo Orario</th>
                                                <th>Ore Lavorate</th>
                                                <th>Costo Totale</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dettaglio_manodopera as $dm)
                                                <tr>
                                                    <td>{{ $dm->dipendente }}</td>
                                                    <td>&euro; {{ number_format($dm->costo_orario, 2, ',', '.') }}/h</td>
                                                    <td>{{ number_format($dm->ore, 1, ',', '.') }} h</td>
                                                    <td>&euro; {{ number_format($dm->costo_totale, 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-bold">
                                                <td>Totale</td>
                                                <td>-</td>
                                                <td>{{ number_format($costi_manodopera[0]->ore_totali, 1, ',', '.') }} h</td>
                                                <td>&euro; {{ number_format($costi_manodopera[0]->totale, 2, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @endif
                            </div>

                            <div class="tab-pane" id="movimenti-magazzino" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                {{--<th>Documento</th>--}}
                                                <th>Articolo</th>
                                                <th>Magazzino</th>
                                                <th>Tipo</th>
                                                <th>Quantità</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($movimenti_magazzino) > 0)
                                                @foreach($movimenti_magazzino as $movimento)
                                                    <tr>
                                                        <td>{{ date('d/m/Y', strtotime($movimento->datamov)) }}</td>
                                                        {{--<td>{{ $movimento->cd_do }} {{ $movimento->numero_doc }}</td>--}}
                                                        <td>{{ $movimento->nome_articolo }}</td>
                                                        <td>{{ $movimento->magazzino_nome }}</td>
                                                        <td>
                                                            @if($movimento->car == '1')
                                                                <span class="badge bg-success">Carico</span>
                                                            @elseif($movimento->sca == '1')
                                                                <span class="badge bg-danger">Scarico</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $movimento->qta }}</td>
                                                    </tr>
                                                @endforeach

                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">Nessun movimento trovato</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab Attività -->
                            <div class="tab-pane" id="attivita" role="tabpanel">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-success" onclick="aggiungiAttivita()">
                                        <i class="ri-add-line me-1"></i> Nuova Attività
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Titolo</th>
                                                <th>Data Inizio</th>
                                                <th>Data Fine</th>
                                                <th>Responsabile</th>
                                                <th>Completamento</th>
                                                <th>Costo Totale</th>
                                                <th>Costo Effettivo</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($attivita) > 0)
                                                @foreach($attivita as $att)
                                                    @php
                                                        $costo_effettivo = ($att->costo * ($att->completamento / 100));
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $att->titolo }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($att->data_inizio)) }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($att->data_fine)) }}</td>
                                                        <td>
                                                            @php
                                                                $responsabile = DB::table('utenti')->where('id', $att->id_responsabile)->first();
                                                                echo $responsabile ? $responsabile->nome . ' ' . $responsabile->cognome : 'N/D';
                                                            @endphp
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $att->completamento }}%;" aria-valuenow="{{ $att->completamento }}" aria-valuemin="0" aria-valuemax="100">{{ $att->completamento }}%</div>
                                                            </div>
                                                        </td>
                                                        <td>€ {{ number_format($att->costo ?? 0, 2, ',', '.') }}</td>
                                                        <td>
                                                            <span class="text-muted">€ {{ number_format($costo_effettivo, 2, ',', '.') }}</span>
                                                                <br>
                                                                <small class="text-muted">({{ $att->completamento }}%)</small>
                                                            </td>
                                                        <td>
                                                            <a href="javascript:void(0)" onclick="modificaAttivita({{ $att->id }})" class="btn btn-sm btn-info me-1">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="confermaEliminaAttivita({{ $att->id }})" class="btn btn-sm btn-danger">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">Nessuna attività trovata</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab Voci Extra -->
                            <div class="tab-pane" id="voci-extra" role="tabpanel">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-success me-2" onclick="aggiungiVoceExtra('ricavo')">
                                        <i class="ri-add-line me-1"></i> Nuovo Ricavo Extra
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="aggiungiVoceExtra('costo')">
                                        <i class="ri-add-line me-1"></i> Nuovo Costo Extra
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Data</th>
                                                <th>Descrizione</th>
                                                <th>Importo</th>
                                                <th>Note</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($voci_extra) > 0)
                                                @foreach($voci_extra as $voce)
                                                    <tr>
                                                        <td>
                                                            @if($voce->tipo == 'ricavo')
                                                                <span class="badge bg-success">Ricavo</span>
                                                            @else
                                                                <span class="badge bg-danger">Costo</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d/m/Y', strtotime($voce->data)) }}</td>
                                                        <td>{{ $voce->descrizione }}</td>
                                                        <td>€ {{ number_format($voce->importo, 2, ',', '.') }}</td>
                                                        <td>{{ $voce->note }}</td>
                                                        <td>
                                                            <a href="javascript:void(0)" onclick="modificaVoceExtra({{ $voce->id }})" class="btn btn-sm btn-info me-1">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="confermaEliminaVoceExtra({{ $voce->id }})" class="btn btn-sm btn-danger">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">Nessuna voce extra trovata</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <th colspan="3" class="text-end">Totale Ricavi Extra:</th>
                                                <th>€ {{ number_format($totale_ricavi_extra, 2, ',', '.') }}</th>
                                                <th colspan="2"></th>
                                            </tr>
                                            <tr class="table-light">
                                                <th colspan="3" class="text-end">Totale Costi Extra:</th>
                                                <th>€ {{ number_format($totale_costi_extra, 2, ',', '.') }}</th>
                                                <th colspan="2"></th>
                                            </tr>
                                            <tr class="table-primary">
                                                <th colspan="3" class="text-end">Saldo Voci Extra:</th>
                                                <th>€ {{ number_format($totale_ricavi_extra - $totale_costi_extra, 2, ',', '.') }}</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Cashflow Details -->
                            <div class="tab-pane" id="cashflow-details" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-success-subtle">
                                                <h5 class="card-title mb-0 text-success">Entrate</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td>Incassi da Fatture</td>
                                                            <td class="text-end">€ {{ number_format($totale_incassato, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ricavi Extra</td>
                                                            <td class="text-end">€ {{ number_format($totale_ricavi_extra, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="table-success">
                                                            <th>Totale Entrate</th>
                                                            <th class="text-end">€ {{ number_format($totale_incassato + $totale_ricavi_extra, 2, ',', '.') }}</th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-danger-subtle">
                                                <h5 class="card-title mb-0 text-danger">Uscite</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td>Pagamenti Fatture</td>
                                                            <td class="text-end">€ {{ number_format($totale_pagato, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Costi Attività</td>
                                                            <td class="text-end">€ {{ number_format($totale_costi_attivita_effettivi, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Costi Extra</td>
                                                            <td class="text-end">€ {{ number_format($totale_costi_extra, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="table-danger">
                                                            <th>Totale Uscite</th>
                                                            <th class="text-end">€ {{ number_format($totale_pagato + $totale_costi_attivita_effettivi + $totale_costi_extra, 2, ',', '.') }}</th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header {{ $cashflow_positivo ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                                <h5 class="card-title mb-0 {{ $cashflow_positivo ? 'text-success' : 'text-danger' }}">Riepilogo Cashflow</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <th>Totale Entrate</th>
                                                            <td class="text-end text-success">€ {{ number_format($totale_incassato + $totale_ricavi_extra, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Totale Uscite</th>
                                                            <td class="text-end text-danger">€ {{ number_format($totale_pagato + $totale_costi_attivita_effettivi + $totale_costi_extra, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="{{ $cashflow_positivo ? 'table-success' : 'table-danger' }}">
                                                            <th>Cashflow Netto</th>
                                                            <th class="text-end {{ $cashflow_positivo ? 'text-success' : 'text-danger' }}">
                                                                € {{ number_format($cashflow, 2, ',', '.') }}
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="alert {{ $cashflow_positivo ? 'alert-success' : 'alert-danger' }}">
                                            <h5 class="alert-heading">Analisi Cashflow</h5>
                                            <p class="mb-0">
                                                La commessa è attualmente {{ $cashflow_positivo ? 'in attivo' : 'in passivo' }} con un cashflow di 
                                                <strong>€ {{ number_format($cashflow, 2, ',', '.') }}</strong>.
                                                @if($cashflow_positivo)
                                                    Questo indica una buona gestione della liquidità della commessa.
                                                @else
                                                    Si consiglia di monitorare attentamente i flussi di cassa e valutare azioni correttive.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal per Aggiungere/Modificare Attività -->
<div class="modal fade" id="attivitaModal" tabindex="-1" aria-labelledby="attivitaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attivitaModalLabel">Nuova Attività</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="attivitaForm" method="POST">
                    @csrf
                    <input type="hidden" id="id_attivita" name="id_attivita">
                    <input type="hidden" id="id_commessa" name="id_commessa" value="{{ $commessa->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="titolo" class="form-label">Titolo</label>
                            <input type="text" class="form-control" id="titolo" name="titolo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="id_responsabile" class="form-label">Responsabile</label>
                            <select class="form-select" id="id_responsabile" name="id_responsabile" required>
                                <option value="">Seleziona Responsabile</option>
                                @foreach(DB::table('utenti')->where('id_azienda', $utente->id_azienda)->get() as $dipendente)
                                    <option value="{{ $dipendente->id }}">{{ $dipendente->nome }} {{ $dipendente->cognome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data_inizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="data_fine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="data_fine" name="data_fine" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="completamento" class="form-label">Completamento (%)</label>
                            <input type="number" class="form-control" id="completamento" name="completamento" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-4">
                            <label for="priorita" class="form-label">Priorità</label>
                            <select class="form-select" id="priorita" name="priorita">
                                <option value="bassa">Bassa</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="costo" class="form-label">Costo (€)</label>
                            <input type="number" class="form-control" id="costo" name="costo" min="0" step="0.01" value="0.00">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="stato" class="form-label">Stato</label>
                            <select class="form-select" id="stato" name="stato">
                                <option value="da_iniziare">Da Iniziare</option>
                                <option value="in_corso">In Corso</option>
                                <option value="completata">Completata</option>
                                <option value="in_ritardo">In Ritardo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_attivita_precedente" class="form-label">Attività Precedente</label>
                            <select class="form-select" id="id_attivita_precedente" name="id_attivita_precedente">
                                <option value="">Nessuna</option>
                                @foreach($attivita as $att)
                                    <option value="{{ $att->id }}">{{ $att->titolo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnEliminaAttivita" style="display:none;float:left">Elimina</button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="btnSalvaAttivita">Salva</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per Aggiungere/Modificare Voci Extra -->
<div class="modal fade" id="voceExtraModal" tabindex="-1" aria-labelledby="voceExtraModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voceExtraModalLabel">Nuova Voce Extra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="voceExtraForm" method="POST">
                    @csrf
                    <input type="hidden" id="id_voce_extra" name="id_voce_extra">
                    <input type="hidden" id="id_commessa_extra" name="id_commessa" value="{{ $commessa->id }}">
                    <input type="hidden" id="tipo_voce" name="tipo" value="ricavo">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descrizione_extra" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" id="descrizione_extra" name="descrizione" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data_extra" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data_extra" name="data" required>
                        </div>
                        <div class="col-md-6">
                            <label for="importo" class="form-label">Importo (€)</label>
                            <input type="number" class="form-control" id="importo" name="importo" min="0" step="0.01" value="0.00" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="note_extra" class="form-label">Note</label>
                        <textarea class="form-control" id="note_extra" name="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnEliminaVoceExtra" style="display:none;float:left">Elimina</button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-primary" id="btnSalvaVoceExtra">Salva</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Funzione per aprire il modal di creazione attività
    function aggiungiAttivita() {
        // Reset form
        document.getElementById('attivitaForm').reset();
        document.getElementById('id_attivita').value = '';
        document.getElementById('attivitaModalLabel').textContent = 'Nuova Attività';
        document.getElementById('btnEliminaAttivita').style.display = 'none';
        
        // Imposta data inizio a oggi
        var oggi = new Date();
        var dataInizioStr = oggi.getFullYear() + '-' + 
                           ('0' + (oggi.getMonth() + 1)).slice(-2) + '-' + 
                           ('0' + oggi.getDate()).slice(-2);
        document.getElementById('data_inizio').value = dataInizioStr;
        
        // Imposta data fine a oggi + 7 giorni
        var dataFine = new Date();
        dataFine.setDate(dataFine.getDate() + 7);
        var dataFineStr = dataFine.getFullYear() + '-' + 
                         ('0' + (dataFine.getMonth() + 1)).slice(-2) + '-' + 
                         ('0' + dataFine.getDate()).slice(-2);
        document.getElementById('data_fine').value = dataFineStr;
        
        // Mostra il modal
        var modal = new bootstrap.Modal(document.getElementById('attivitaModal'));
        modal.show();
    }
    
    // Funzione per aprire il modal di modifica attività
    function modificaAttivita(id) {
        // Recupera i dati dell'attività
        fetch('{{ url("utente/commesse/attivita") }}/' + id)
            .then(response => response.json())
            .then(data => {
                // Popola il form
                document.getElementById('id_attivita').value = data.id;
                document.getElementById('titolo').value = data.titolo;
                document.getElementById('descrizione').value = data.descrizione || '';
                document.getElementById('data_inizio').value = data.data_inizio;
                document.getElementById('data_fine').value = data.data_fine;
                document.getElementById('completamento').value = data.completamento;
                document.getElementById('costo').value = data.costo || 0;
                document.getElementById('stato').value = data.stato;
                document.getElementById('priorita').value = data.priorita;
                document.getElementById('id_responsabile').value = data.id_responsabile;
                document.getElementById('id_attivita_precedente').value = data.id_attivita_precedente || '';
                document.getElementById('note').value = data.note || '';
                
                // Aggiorna titolo e mostra pulsante elimina
                document.getElementById('attivitaModalLabel').textContent = 'Modifica Attività';
                document.getElementById('btnEliminaAttivita').style.display = 'block';
                
                // Mostra il modal
                var modal = new bootstrap.Modal(document.getElementById('attivitaModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Errore nel recupero dati:', error);
                alert('Errore nel recupero dei dati dell\'attività');
            });
    }

    function confermaEliminaAttivita(id) {
        if (confirm('Sei sicuro di voler eliminare questa attività?')) {
            eliminaAttivita(id);
        }
    }
    
    function eliminaAttivita(id) {
        fetch('{{ url("utente/commesse/attivita") }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ricarica la pagina per aggiornare i dati
                window.location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante l\'eliminazione');
        });
    }

    // Funzioni per la gestione delle voci extra
    function aggiungiVoceExtra(tipo) {
        // Reset form
        document.getElementById('voceExtraForm').reset();
        document.getElementById('id_voce_extra').value = '';
        document.getElementById('tipo_voce').value = tipo;
        
        // Imposta titolo in base al tipo
        if (tipo === 'ricavo') {
            document.getElementById('voceExtraModalLabel').textContent = 'Nuovo Ricavo Extra';
        } else {
            document.getElementById('voceExtraModalLabel').textContent = 'Nuovo Costo Extra';
        }
        
        document.getElementById('btnEliminaVoceExtra').style.display = 'none';
        
        // Imposta data a oggi
        var oggi = new Date();
        var dataStr = oggi.getFullYear() + '-' + 
                    ('0' + (oggi.getMonth() + 1)).slice(-2) + '-' + 
                    ('0' + oggi.getDate()).slice(-2);
        document.getElementById('data_extra').value = dataStr;
        
        // Mostra il modal
        var modal = new bootstrap.Modal(document.getElementById('voceExtraModal'));
        modal.show();
    }
    
    function modificaVoceExtra(id) {
        // Recupera i dati della voce extra
        fetch('{{ url("utente/commesse/voci-extra") }}/' + id)
            .then(response => response.json())
            .then(data => {
                // Popola il form
                document.getElementById('id_voce_extra').value = data.id;
                document.getElementById('tipo_voce').value = data.tipo;
                document.getElementById('descrizione_extra').value = data.descrizione;
                document.getElementById('data_extra').value = data.data;
                document.getElementById('importo').value = data.importo;
                document.getElementById('note_extra').value = data.note || '';
                
                // Aggiorna titolo e mostra pulsante elimina
                if (data.tipo === 'ricavo') {
                    document.getElementById('voceExtraModalLabel').textContent = 'Modifica Ricavo Extra';
                } else {
                    document.getElementById('voceExtraModalLabel').textContent = 'Modifica Costo Extra';
                }
                document.getElementById('btnEliminaVoceExtra').style.display = 'block';
                
                // Mostra il modal
                var modal = new bootstrap.Modal(document.getElementById('voceExtraModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Errore nel recupero dati:', error);
                alert('Errore nel recupero dei dati della voce extra');
            });
    }

    function confermaEliminaVoceExtra(id) {
        if (confirm('Sei sicuro di voler eliminare questa voce extra?')) {
            eliminaVoceExtra(id);
        }
    }
    
    function eliminaVoceExtra(id) {
        fetch('{{ url("utente/commesse/voci-extra") }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ricarica la pagina per aggiornare i dati
                window.location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante l\'eliminazione');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Gestione salvataggio attività
        document.getElementById('btnSalvaAttivita').addEventListener('click', function() {
            var form = document.getElementById('attivitaForm');
            var formData = new FormData(form);
            var id = document.getElementById('id_attivita').value;
            
            // Prepara l'URL corretto in base all'operazione (creazione o modifica)
            var url = id ? '{{ url("utente/commesse/attivita") }}/' + id : '{{ url("utente/commesse/attivita") }}';
            var method = id ? 'PUT' : 'POST';
            
            // Per il metodo PUT, dobbiamo aggiungere _method=PUT
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
                // Cambiamo il metodo a POST perché Laravel gestisce PUT tramite _method
                method = 'POST';
            }
            
            // Log per debug
            console.log('Invio dati attività:', {
                url: url,
                method: method,
                id: id,
                titolo: formData.get('titolo'),
                id_responsabile: formData.get('id_responsabile'),
                id_commessa: formData.get('id_commessa')
            });
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Chiudi il modal
                    bootstrap.Modal.getInstance(document.getElementById('attivitaModal')).hide();
                    
                    // Ricarica la pagina per aggiornare i dati
                    window.location.reload();
                } else {
                    alert('Errore: ' + (data.message || 'Si è verificato un errore durante il salvataggio'));
                    console.error('Errore risposta server:', data);
                }
            })
            .catch(error => {
                console.error('Errore durante la richiesta:', error);
                alert('Si è verificato un errore durante il salvataggio');
            });
        });
        
        // Gestione eliminazione attività
        document.getElementById('btnEliminaAttivita').addEventListener('click', function() {
            if (confirm('Sei sicuro di voler eliminare questa attività?')) {
                var id = document.getElementById('id_attivita').value;
                eliminaAttivita(id);
                // Chiudi il modal dopo l'eliminazione
                bootstrap.Modal.getInstance(document.getElementById('attivitaModal')).hide();
            }
        });

        // Gestione delle tab e salvataggio dell'ultima tab aperta
        const storageKey = 'commessa_{{ $commessa->id }}_active_tab';
        
        // Ripristina l'ultima tab aperta
        const lastActiveTab = localStorage.getItem(storageKey);
        if (lastActiveTab) {
            // Rimuovi la classe active da tutte le tab
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Rimuovi la classe active da tutti i contenuti delle tab
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active', 'show');
            });
            
            // Attiva la tab salvata
            const savedTab = document.getElementById(lastActiveTab);
            if (savedTab) {
                savedTab.classList.add('active');
                const targetId = savedTab.getAttribute('href');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('active', 'show');
                }
            }
        }
        
        // Salva la tab attiva quando viene cambiata
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem(storageKey, event.target.id);
            });
        });

        // Gestione salvataggio voce extra
        document.getElementById('btnSalvaVoceExtra').addEventListener('click', function() {
            var form = document.getElementById('voceExtraForm');
            var formData = new FormData(form);
            var id = document.getElementById('id_voce_extra').value;
            var url = id ? '{{ url("utente/commesse/voci-extra") }}/' + id : '{{ url("utente/commesse/voci-extra") }}';
            var method = id ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Chiudi il modal
                    bootstrap.Modal.getInstance(document.getElementById('voceExtraModal')).hide();
                    
                    // Ricarica la pagina per aggiornare i dati
                    window.location.reload();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante il salvataggio');
            });
        });
        
        // Gestione eliminazione voce extra
        document.getElementById('btnEliminaVoceExtra').addEventListener('click', function() {
            if (confirm('Sei sicuro di voler eliminare questa voce extra?')) {
                var id = document.getElementById('id_voce_extra').value;
                eliminaVoceExtra(id);
                // Chiudi il modal dopo l'eliminazione
                bootstrap.Modal.getInstance(document.getElementById('voceExtraModal')).hide();
            }
        });
    });
</script>
<!-- Modal Movimento Cassa Manuale -->
<div class="modal fade" id="modalMovimentoCassa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                @csrf
                <input type="hidden" name="tipo_movimento" id="tipo_movimento" value="entrata">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_titolo_movimento">Registra Incasso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Importo <b style="color:red">*</b></label>
                            <div class="input-group">
                                <span class="input-group-text">&euro;</span>
                                <input type="number" name="importo" class="form-control" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data <b style="color:red">*</b></label>
                            <input type="date" name="data_movimento" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrizione <b style="color:red">*</b></label>
                            <input type="text" name="descrizione_movimento" class="form-control" placeholder="es. Acconto cliente, Pagamento fornitore..." required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Modalità di pagamento</label>
                            <select name="modalita_pagamento" class="form-control">
                                <option value="">-- Seleziona --</option>
                                <option value="Bonifico">Bonifico</option>
                                <option value="Contanti">Contanti</option>
                                <option value="Assegno">Assegno</option>
                                <option value="Carta di credito">Carta di credito</option>
                                <option value="RiBa">RiBa</option>
                                <option value="Altro">Altro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note_movimento" class="form-control" rows="2" placeholder="Note aggiuntive..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_movimento_cassa" value="1" class="btn btn-primary">Salva Movimento</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer')