@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dettaglio Cliente</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('utente/clienti') }}">Clienti</a></li>
                            <li class="breadcrumb-item active">Dettaglio Cliente</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

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
            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                <img src="{{ URL::asset($cliente->immagine) }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="Cliente">
                            </div>
                            <h5 class="fs-16 mb-1">{{ $cliente->ragione_sociale }}</h5>
                            <p class="text-muted mb-0">{{ $cliente->piva }}</p>
                            <p class="text-muted mb-0">Codice Cliente: {{ $cliente->cd_cf }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riepilogo Contabile</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">Fatturato Totale:</th>
                                    <td class="text-muted">€ {{ number_format($fatturato_totale, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Scadenze Non Pagate:</th>
                                    <td class="text-muted">
                                        € {{ number_format($scadenze_scadute, 2, ',', '.') }}
                                        @if($scadenze_scadute > 0)
                                            <span class="badge bg-danger ms-1">Scaduto</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Ordini Aperti:</th>
                                    <td class="text-muted">{{ count($ordini_aperti) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Contatti</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">Email:</th>
                                    <td class="text-muted">{{ $cliente->email }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">PEC:</th>
                                    <td class="text-muted">{{ $cliente->pec }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Email Fatture:</th>
                                    <td class="text-muted">{{ $cliente->mail_recapito }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Telefono:</th>
                                    <td class="text-muted">{{ $cliente->telefono }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Indirizzo:</th>
                                    <td class="text-muted">
                                        {{ $cliente->indirizzo }}<br>
                                        {{ $cliente->cap }} {{ $cliente->comune }} ({{ $cliente->provincia }})<br>
                                        {{ $cliente->regione }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Referente:</th>
                                    <td class="text-muted">
                                        {{ $cliente->referente }}
                                        @if($cliente->telefono_referente)
                                            <br>Tel: {{ $cliente->telefono_referente }}
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#documenti" role="tab">
                                    <i class="fas fa-file-invoice me-1 align-bottom"></i> Documenti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#scadenze" role="tab">
                                    <i class="fas fa-calendar-alt me-1 align-bottom"></i> Scadenze
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#attivita" role="tab">
                                    <i class="fas fa-tasks me-1 align-bottom"></i> Attività
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#info" role="tab">
                                    <i class="fas fa-info-circle me-1 align-bottom"></i> Informazioni
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sedi" role="tab">
                                    <i class="fas fa-map-marker-alt me-1 align-bottom"></i> Sedi
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Tab Documenti -->
                            <div class="tab-pane active" id="documenti" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Ultimi Ordini</h5>
                                            </div>
                                            <div class="card-body">
                                                @if(count($ordini_recenti) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Numero</th>
                                                                <th>Data</th>
                                                                <th>Totale</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($ordini_recenti as $ordine)
                                                                <tr>
                                                                    <td>{{ $ordine->numero_doc }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($ordine->data_doc)->format('d/m/Y') }}</td>
                                                                    <td>€ {{ number_format($ordine->totale, 2, ',', '.') }}</td>
                                                                    <td>
                                                                        <a href="{{ url('utente/modifica_documento/'.$ordine->id) }}" class="btn btn-sm btn-soft-primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <a href="{{ url('utente/riepilogo_documenti/ORD') }}" class="btn btn-sm btn-soft-primary">
                                                            Vedi tutti
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info mb-0">
                                                        Nessun ordine disponibile.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Ultime Fatture</h5>
                                            </div>
                                            <div class="card-body">
                                                @if(count($fatture_recenti) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Numero</th>
                                                                <th>Data</th>
                                                                <th>Totale</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($fatture_recenti as $fattura)
                                                                <tr>
                                                                    <td>{{ $fattura->numero_doc }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($fattura->data_doc)->format('d/m/Y') }}</td>
                                                                    <td>€ {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                                                                    <td>
                                                                        <a href="{{ url('utente/modifica_documento/'.$fattura->id) }}" class="btn btn-sm btn-soft-primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <a href="{{ url('utente/riepilogo_documenti/FTV') }}" class="btn btn-sm btn-soft-primary">
                                                            Vedi tutte
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info mb-0">
                                                        Nessuna fattura disponibile.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Fatturato Annuale</h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="fatturato-chart" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-header d-flex align-items-center">
                                                <h5 class="card-title mb-0 flex-grow-1">Tutti i Documenti</h5>
                                                <div class="flex-shrink-0">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-primary" type="button" id="crea-documento">
                                                            <i class="ri-add-line align-middle"></i> Crea Documento
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" id="tipi-documento" style="display: none;">
                                                            <a class="dropdown-item" href="{{ url('utente/crea_documento/PRE?cliente='.$cliente->id) }}">
                                                                <i class="ri-file-list-3-line me-2"></i> Preventivo
                                                            </a>
                                                            <a class="dropdown-item" href="{{ url('utente/crea_documento/ORD?cliente='.$cliente->id) }}">
                                                                <i class="ri-file-list-3-line me-2"></i> Ordine
                                                            </a>
                                                            <a class="dropdown-item" href="{{ url('utente/crea_documento/DDT?cliente='.$cliente->id) }}">
                                                                <i class="ri-file-list-3-line me-2"></i> DDT
                                                            </a>
                                                            <a class="dropdown-item" href="{{ url('utente/crea_documento/FTV?cliente='.$cliente->id) }}">
                                                                <i class="ri-file-list-3-line me-2"></i> Fattura
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="documenti-datatable" class="table table-bordered dt-responsive nowrap table-hover align-middle">
                                                        <thead class="table-light">
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <th>Numero</th>
                                                            <th>Data</th>
                                                            <th>Totale</th>
                                                            <th>Stato</th>
                                                            <th>Azioni</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($documenti as $doc)
                                                            <tr>
                                                                <td>
                                                                    @if($doc->cd_do == 'PRE')
                                                                        <span class="badge bg-info">Preventivo</span>
                                                                    @elseif($doc->cd_do == 'ORD')
                                                                        <span class="badge bg-warning">Ordine</span>
                                                                    @elseif($doc->cd_do == 'DDT')
                                                                        <span class="badge bg-primary">DDT</span>
                                                                    @elseif($doc->cd_do == 'FTV')
                                                                        <span class="badge bg-success">Fattura</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ $doc->cd_do }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $doc->numero_doc }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($doc->data_doc)->format('d/m/Y') }}</td>
                                                                <td>€ {{ number_format($doc->totale, 2, ',', '.') }}</td>
                                                                <td>
                                                                    @if($doc->stato == 0)
                                                                        <span class="badge bg-warning">In lavorazione</span>
                                                                    @elseif($doc->stato == 1)
                                                                        <span class="badge bg-success">Completato</span>
                                                                    @elseif($doc->stato == 2)
                                                                        <span class="badge bg-danger">Scartato</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Altro</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li>
                                                                                <a class="dropdown-item" href="{{ url('utente/modifica_documento/'.$doc->id) }}">
                                                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Visualizza
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item" href="{{ url('stampa/documento/'.$doc->id) }}" target="_blank">
                                                                                    <i class="ri-printer-fill align-bottom me-2 text-muted"></i> Stampa
                                                                                </a>
                                                                            </li>
                                                                            @if($doc->cd_do == 'ORD' || $doc->cd_do == 'PRE')
                                                                                <li>
                                                                                    <a class="dropdown-item" href="{{ url('utente/evadi_documento/'.$doc->id) }}">
                                                                                        <i class="ri-arrow-right-line align-bottom me-2 text-muted"></i> Evadi
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        </ul>
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
                            </div>

                            <!-- Tab Scadenze -->
                            <div class="tab-pane" id="scadenze" role="tabpanel">
                                <div class="card border">
                                    <div class="card-header d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Scadenze</h5>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#aggiungiScadenzaModal">
                                                <i class="ri-add-line align-middle"></i> Nuova Scadenza
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="scadenze-datatable" class="table table-bordered dt-responsive nowrap table-hover align-middle">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>Data Scadenza</th>
                                                    <th>Importo</th>
                                                    <th>Pagato</th>
                                                    <th>Rimanente</th>
                                                    <th>Stato</th>
                                                    <th>Documento</th>
                                                    <th>Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($scadenze as $scadenza)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($scadenza->data_scadenza)->format('d/m/Y') }}</td>
                                                        <td>€ {{ number_format($scadenza->importo, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($scadenza->importo_pagato, 2, ',', '.') }}</td>
                                                        <td>€ {{ number_format($scadenza->importo - $scadenza->importo_pagato, 2, ',', '.') }}</td>
                                                        <td>
                                                            @if($scadenza->stato == 'da_pagare')
                                                                <span class="badge bg-warning">Da Pagare</span>
                                                            @elseif($scadenza->stato == 'parziale')
                                                                <span class="badge bg-info">Pagato Parzialmente</span>
                                                            @elseif($scadenza->stato == 'pagato')
                                                                <span class="badge bg-success">Pagato</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($scadenza->id_dotes)
                                                                @php
                                                                    $doc_scadenza = DB::table('dotes')
                                                                        ->where('id', $scadenza->id_dotes)
                                                                        ->first();
                                                                @endphp
                                                                @if($doc_scadenza)
                                                                    <a href="{{ url('utente/modifica_documento/'.$scadenza->id_dotes) }}">
                                                                        {{ $doc_scadenza->cd_do }} {{ $doc_scadenza->numero_doc }}
                                                                    </a>
                                                                @else
                                                                    N/D
                                                                @endif
                                                            @else
                                                                N/D
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="ri-more-fill"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" onclick="registraPagamento({{ $scadenza->id }}); return false;">
                                                                            <i class="ri-money-euro-circle-line align-bottom me-2 text-muted"></i> Registra Pagamento
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" onclick="inviaPromemoria({{ $scadenza->id }}); return false;">
                                                                            <i class="ri-mail-send-line align-bottom me-2 text-muted"></i> Invia Promemoria
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" onclick="modificaScadenza({{ $scadenza->id }}); return false;">
                                                                            <i class="ri-edit-2-line align-bottom me-2 text-muted"></i> Modifica
                                                                        </a>
                                                                    </li>
                                                                </ul>
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

                            <!-- Tab Attività -->
                            <div class="tab-pane" id="attivita" role="tabpanel">
                                <div class="card border">
                                    <div class="card-header d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Attività</h5>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#aggiungiAttivitaModal">
                                                <i class="ri-add-line align-middle"></i> Nuova Attività
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Informazioni -->
                            <div class="tab-pane" id="info" role="tabpanel">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Dati Fiscali</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless mb-0">
                                                        <tbody>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Partita IVA:</th>
                                                            <td class="text-muted">{{ $cliente->piva }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Codice Fiscale:</th>
                                                            <td class="text-muted">{{ $cliente->cf }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Codice Cliente:</th>
                                                            <td class="text-muted">{{ $cliente->cd_cf }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Codice SDI:</th>
                                                            <td class="text-muted">{{ $cliente->sdi }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">PEC:</th>
                                                            <td class="text-muted">{{ $cliente->pec }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Esigibilità IVA:</th>
                                                            <td class="text-muted">
                                                                @if($cliente->esigibilita_iva == 'I')
                                                                    Immediata
                                                                @elseif($cliente->esigibilita_iva == 'D')
                                                                    Differita
                                                                @elseif($cliente->esigibilita_iva == 'S')
                                                                    Split Payment
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if(!empty($cliente->esportatore_abituale))
                                                        <tr>
                                                            <th class="ps-0" scope="row">Esportatore abituale:</th>
                                                            <td><span class="badge bg-success">SI</span></td>
                                                        </tr>
                                                        @if($cliente->dich_intento_protocollo)
                                                            <tr><th class="ps-0">Protocollo dich. intento:</th><td class="text-muted">{{ $cliente->dich_intento_protocollo }}</td></tr>
                                                        @endif
                                                        @if($cliente->dich_intento_data)
                                                            <tr><th class="ps-0">Data dichiarazione:</th><td class="text-muted">{{ date('d/m/Y', strtotime($cliente->dich_intento_data)) }}</td></tr>
                                                        @endif
                                                        @if($cliente->dich_intento_validita_da || $cliente->dich_intento_validita_a)
                                                            <tr><th class="ps-0">Validità:</th><td class="text-muted">
                                                                @if($cliente->dich_intento_validita_da)dal {{ date('d/m/Y', strtotime($cliente->dich_intento_validita_da)) }}@endif
                                                                @if($cliente->dich_intento_validita_a) al {{ date('d/m/Y', strtotime($cliente->dich_intento_validita_a)) }}@endif
                                                            </td></tr>
                                                        @endif
                                                        @if($cliente->dich_intento_importo)
                                                            <tr><th class="ps-0">Importo plafond:</th><td class="text-muted">€ {{ number_format($cliente->dich_intento_importo, 2, ',', '.') }}</td></tr>
                                                        @endif
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <h5 class="mb-3">Informazioni Aziendali</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless mb-0">
                                                        <tbody>
                                                        <tr>
                                                            <th class="ps-0" scope="row">CCIAA:</th>
                                                            <td class="text-muted">{{ $cliente->cciaa }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">REA:</th>
                                                            <td class="text-muted">{{ $cliente->rea }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Codice ATECO:</th>
                                                            <td class="text-muted">{{ $cliente->ateco_codice }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Descrizione ATECO:</th>
                                                            <td class="text-muted">{{ $cliente->ateco_descrizione }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Sezione:</th>
                                                            <td class="text-muted">{{ $sezione->descrizione ?? 'N/D' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Dipendenti:</th>
                                                            <td class="text-muted">{{ $cliente->dipendenti }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Fatturato:</th>
                                                            <td class="text-muted">
                                                                @if($cliente->fatturato)
                                                                    € {{ number_format($cliente->fatturato, 2, ',', '.') }}
                                                                @else
                                                                    N/D
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Grandezza:</th>
                                                            <td class="text-muted">
                                                                @if($cliente->grandezza_azienda == 0)
                                                                    <span class="badge bg-info">MICRO</span>
                                                                @elseif($cliente->grandezza_azienda == 1)
                                                                    <span class="badge bg-success">PICCOLA</span>
                                                                @elseif($cliente->grandezza_azienda == 2)
                                                                    <span class="badge bg-warning">MEDIA</span>
                                                                @elseif($cliente->grandezza_azienda == 3)
                                                                    <span class="badge bg-danger">GRANDE</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Agente:</th>
                                                            <td class="text-muted">
                                                                @if($agente)
                                                                    {{ $agente->nome }} {{ $agente->cognome }}
                                                                @else
                                                                    Nessun agente assegnato
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Sedi -->
                            <div class="tab-pane" id="sedi" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Sedi</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAggiungiSede">
                                        <i class="ri-add-line me-1"></i> Aggiungi Sede
                                    </button>
                                </div>

                                @if(isset($sedi) && count($sedi) > 0)
                                    <div class="row g-3">
                                        @foreach($sedi as $sede)
                                        <div class="col-md-6">
                                            <div class="card border h-100">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i> {{ $sede->nome }}</h6>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-soft-primary" onclick="modificaSede({{ json_encode($sede) }})">
                                                            <i class="ri-pencil-line"></i>
                                                        </button>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Eliminare questa sede?')">
                                                            @csrf
                                                            <input type="hidden" name="elimina_sede" value="1">
                                                            <input type="hidden" name="id_sede" value="{{ $sede->id }}">
                                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="ri-delete-bin-line"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-borderless table-sm mb-0">
                                                        <tr>
                                                            <th style="width:100px">Indirizzo:</th>
                                                            <td>{{ $sede->indirizzo ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Città:</th>
                                                            <td>{{ $sede->cap }} {{ $sede->comune }} @if($sede->provincia)({{ $sede->provincia }})@endif</td>
                                                        </tr>
                                                        @if($sede->telefono)
                                                        <tr>
                                                            <th>Telefono:</th>
                                                            <td>{{ $sede->telefono }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($sede->email)
                                                        <tr>
                                                            <th>Email:</th>
                                                            <td>{{ $sede->email }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($sede->note)
                                                        <tr>
                                                            <th>Note:</th>
                                                            <td>{{ $sede->note }}</td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line me-2"></i> Nessuna sede registrata. Clicca "Aggiungi Sede" per inserirne una.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Sede -->
<div class="modal fade" id="modalAggiungiSede" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-map-pin-add-line me-1"></i> Aggiungi Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome Sede <b style="color:red">*</b></label>
                            <input type="text" name="nome_sede" class="form-control" placeholder="es. Sede Legale, Magazzino, Filiale Nord..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo_sede" class="form-control" placeholder="Via/Piazza...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap_sede" class="form-control" placeholder="CAP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comune</label>
                            <input type="text" name="comune_sede" class="form-control" placeholder="Comune">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prov.</label>
                            <input type="text" name="provincia_sede" class="form-control" placeholder="Prov." maxlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono_sede" class="form-control" placeholder="Telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_sede" class="form-control" placeholder="Email">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note_sede" class="form-control" rows="2" placeholder="Note..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_sede" value="1" class="btn btn-primary">Salva Sede</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Sede -->
<div class="modal fade" id="modalModificaSede" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                @csrf
                <input type="hidden" name="id_sede" id="mod_id_sede">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-pencil-line me-1"></i> Modifica Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome Sede <b style="color:red">*</b></label>
                            <input type="text" name="nome_sede" id="mod_nome_sede" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo_sede" id="mod_indirizzo_sede" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap_sede" id="mod_cap_sede" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comune</label>
                            <input type="text" name="comune_sede" id="mod_comune_sede" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prov.</label>
                            <input type="text" name="provincia_sede" id="mod_provincia_sede" class="form-control" maxlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono_sede" id="mod_telefono_sede" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_sede" id="mod_email_sede" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note_sede" id="mod_note_sede" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="modifica_sede" value="1" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function modificaSede(sede) {
    document.getElementById('mod_id_sede').value = sede.id;
    document.getElementById('mod_nome_sede').value = sede.nome || '';
    document.getElementById('mod_indirizzo_sede').value = sede.indirizzo || '';
    document.getElementById('mod_cap_sede').value = sede.cap || '';
    document.getElementById('mod_comune_sede').value = sede.comune || '';
    document.getElementById('mod_provincia_sede').value = sede.provincia || '';
    document.getElementById('mod_telefono_sede').value = sede.telefono || '';
    document.getElementById('mod_email_sede').value = sede.email || '';
    document.getElementById('mod_note_sede').value = sede.note || '';
    new bootstrap.Modal(document.getElementById('modalModificaSede')).show();
}
</script>

<!-- Modal Aggiungi Scadenza -->
<div class="modal fade" id="aggiungiScadenzaModal" tabindex="-1" aria-labelledby="aggiungiScadenzaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="aggiungiScadenzaModalLabel">Aggiungi Scadenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="data_scadenza" class="form-label">Data Scadenza</label>
                        <input type="date" class="form-control" id="data_scadenza" name="data_scadenza" required>
                    </div>
                    <div class="mb-3">
                        <label for="importo" class="form-label">Importo</label>
                        <input type="number" step="0.01" class="form-control" id="importo" name="importo" required>
                    </div>
                    <div class="mb-3">
                        <label for="modalita_pagamento" class="form-label">Modalità Pagamento</label>
                        <select class="form-select" id="modalita_pagamento" name="modalita_pagamento">
                            <option value="MP01">Contanti</option>
                            <option value="MP02">Assegno</option>
                            <option value="MP05">Bonifico</option>
                            <option value="MP08">Carta di pagamento</option>
                        </select>
                    </div>
                    <input type="hidden" name="id_cliente" value="{{ $cliente->id }}">
                    <input type="hidden" name="tipo_movimento" value="entrata">
                    <input type="hidden" name="stato" value="da_pagare">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" name="aggiungi_scadenza" value="1" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Attività -->
<div class="modal fade" id="aggiungiAttivitaModal" tabindex="-1" aria-labelledby="aggiungiAttivitaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="aggiungiAttivitaModalLabel">Nuova Attività</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titolo" class="form-label">Titolo</label>
                        <input type="text" class="form-control" id="titolo" name="titolo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="data_scadenza" class="form-label">Data Scadenza</label>
                        <input type="date" class="form-control" id="data_scadenza" name="data_scadenza" required>
                    </div>
                    <div class="mb-3">
                        <label for="priorita" class="form-label">Priorità</label>
                        <select class="form-select" id="priorita" name="priorita">
                            <option value="bassa">Bassa</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" name="crea_attivita" value="1" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registra Pagamento -->
<div class="modal fade" id="registraPagamentoModal" tabindex="-1" aria-labelledby="registraPagamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="registraPagamentoModalLabel">Registra Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ url('utente/registra_pagamento') }}">
                @csrf
                <div class="modal-body" id="pagamento-modal-body">
                    <!-- Contenuto caricato dinamicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success">Registra Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ajax_loader"></div>

@include('utente.common.footer')

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    $(document).ready(function() {
        // Inizializzazione delle DataTables
        $('#documenti-datatable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            order: [[2, 'desc']]
        });

        $('#scadenze-datatable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            order: [[0, 'asc']]
        });

        $('#attivita-datatable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            }
        });

        // Gestione delle note
        $('#edit-notes-btn').click(function() {
            $('#view-notes').hide();
            $('#edit-notes').show();
        });

        $('#cancel-notes-btn').click(function() {
            $('#edit-notes').hide();
            $('#view-notes').show();
        });

        // Gestione del dropdown per la creazione di documenti
        $('#crea-documento').click(function() {
            $('#tipi-documento').toggle();
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#crea-documento, #tipi-documento').length) {
                $('#tipi-documento').hide();
            }
        });

        // Inizializzazione grafico fatturato
        @if(isset($fatturato_per_anno) && count($fatturato_per_anno) > 0)
        var options = {
            series: [{
                name: 'Fatturato',
                data: [
                    @foreach($fatturato_per_anno as $fatturato)
                            {{ $fatturato->totale }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
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
            xaxis: {
                categories: [
                    @foreach($fatturato_per_anno as $fatturato)
                        '{{ $fatturato->anno }}',
                    @endforeach
                ],
            },
            yaxis: {
                title: {
                    text: 'Euro'
                },
                labels: {
                    formatter: function(val) {
                        return '€ ' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return '€ ' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            },
            colors: ['#0ab39c']
        };

        var chart = new ApexCharts(document.querySelector("#fatturato-chart"), options);
        chart.render();
        @endif
    });

    // Funzione per registrare un pagamento
    function registraPagamento(id) {
        $.ajax({
            url: "{{ url('utente/get_scadenza') }}/" + id,
            type: 'GET',
            success: function(response) {
                // Popola i dati nel modale
                let html = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Importo Totale</label>
                        <input type="text" class="form-control" value="€ ${parseFloat(response.importo).toFixed(2)}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Già Pagato</label>
                        <input type="text" class="form-control" value="€ ${parseFloat(response.importo_pagato).toFixed(2)}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Data Pagamento</label>
                        <input type="date" class="form-control" name="data_pagamento" value="${new Date().toISOString().split('T')[0]}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Importo Pagato</label>
                        <input type="number" step="0.01" class="form-control" name="importo_pagato" value="${parseFloat(response.importo - response.importo_pagato).toFixed(2)}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Modalità Pagamento</label>
                        <select class="form-select" name="modalita_pagamento">
                            <option value="MP01" ${response.modalita_pagamento === 'MP01' ? 'selected' : ''}>Contanti</option>
                            <option value="MP02" ${response.modalita_pagamento === 'MP02' ? 'selected' : ''}>Assegno</option>
                            <option value="MP05" ${response.modalita_pagamento === 'MP05' ? 'selected' : ''}>Bonifico</option>
                            <option value="MP08" ${response.modalita_pagamento === 'MP08' ? 'selected' : ''}>Carta di pagamento</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" rows="3">${response.note || ''}</textarea>
                    </div>
                    <input type="hidden" name="id_scadenza" value="${response.id}">
                </div>
                `;

                $('#pagamento-modal-body').html(html);
                $('#registraPagamentoModal').modal('show');
            }
        });
    }

    // Funzioni per la gestione delle attività
    function dettaglioAttivita(id) {
        // Implementare la visualizzazione del dettaglio attività
        alert("Dettaglio attività " + id);
    }

    function modificaAttivita(id) {
        // Implementare la modifica dell'attività
        alert("Modifica attività " + id);
    }

    function completaAttivita(id) {
        // Implementare il completamento dell'attività
        if (confirm("Contrassegnare questa attività come completata?")) {
            // Qui l'AJAX per completare l'attività
            alert("Attività " + id + " completata");
        }
    }

    // Funzione per l'invio di un promemoria
    function inviaPromemoria(id) {
        // Implementare l'invio del promemoria
        if (confirm("Inviare un promemoria per questa scadenza?")) {
            // Qui l'AJAX per inviare il promemoria
            alert("Promemoria inviato per la scadenza " + id);
        }
    }

    function modificaScadenza(id) {
        // Implementare la modifica della scadenza
        alert("Modifica scadenza " + id);
    }
</script>