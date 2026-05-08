@include('utente.common.header')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard Commessa: {{ $commessa->codice_commessa }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/utente/index">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/commesse">Commesse</a></li>
                                <li class="breadcrumb-item active">{{ $commessa->codice_commessa }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stato Commessa e Azioni -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0 flex-grow-1">{{ $commessa->descrizione }}</h5>
                                <div class="flex-shrink-0">
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-settings-3-line align-middle me-1"></i> Azioni
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="/commesse/{{ $commessa->id }}/attivita">Gestisci Attività</a></li>
                                            <li><a class="dropdown-item" href="/commesse/{{ $commessa->id }}/documenti">Gestisci Documenti</a></li>
                                            <li><a class="dropdown-item" href="/commesse/{{ $commessa->id }}/magazzino">Gestisci Magazzino</a></li>
                                            <li><a class="dropdown-item" href="/commesse/{{ $commessa->id }}/pagamenti">Gestisci Pagamenti</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalModificaStato">Cambia Stato</a></li>
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalModificaValori">Aggiorna Valori</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Stato</p>
                                            @if($commessa->stato == 'aperta')
                                                <h5 class="mb-0"><span class="badge bg-success">Aperta</span></h5>
                                            @elseif($commessa->stato == 'in_corso')
                                                <h5 class="mb-0"><span class="badge bg-warning">In Corso</span></h5>
                                            @elseif($commessa->stato == 'completata')
                                                <h5 class="mb-0"><span class="badge bg-info">Completata</span></h5>
                                            @else
                                                <h5 class="mb-0"><span class="badge bg-danger">Annullata</span></h5>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Data Apertura</p>
                                            <h5 class="mb-0">{{ date('d/m/Y', strtotime($commessa->data_apertura)) }}</h5>
                                        </div>
                                    </div>
                                    @if($commessa->data_chiusura)
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Data Chiusura</p>
                                            <h5 class="mb-0">{{ date('d/m/Y', strtotime($commessa->data_chiusura)) }}</h5>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Budget</p>
                                            <h5 class="mb-0">&euro; {{ number_format($commessa->budget, 2, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Costi</p>
                                            <h5 class="mb-0">&euro; {{ number_format($commessa->costi, 2, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-1">Ricavi</p>
                                            <h5 class="mb-0">&euro; {{ number_format($commessa->ricavi, 2, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($commessa->note)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="text-muted mb-1">Note</h6>
                                    <p>{{ $commessa->note }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riepilogo Finanziario -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Ricavi Totali</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($ricavi[0]->totale, 2, ',', '.') }}</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="ri-arrow-up-circle-line text-success"></i>
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
                                    <p class="text-uppercase fw-medium text-muted mb-0">Costi Totali</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi[0]->totale, 2, ',', '.') }}</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-danger rounded fs-3">
                                        <i class="ri-arrow-down-circle-line text-danger"></i>
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
                                    <p class="text-uppercase fw-medium text-muted mb-0">Incassi Ricevuti</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($incassi[0]->totale, 2, ',', '.') }}</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                        <i class="ri-wallet-3-line text-info"></i>
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
                                    <p class="text-uppercase fw-medium text-muted mb-0">Pagamenti Effettuati</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($pagamenti[0]->totale, 2, ',', '.') }}</h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="ri-wallet-3-line text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Costi Produzione (Materiali + Manodopera) -->
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Costi Materiali (Produzione)</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi_materiali[0]->totale, 2, ',', '.') }}</h4>
                                    <p class="text-muted mb-0 mt-1"><small>Calcolato dai movimenti di scarico ODL</small></p>
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
                <div class="col-xl-4 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Costi Manodopera</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi_manodopera[0]->totale, 2, ',', '.') }}</h4>
                                    <p class="text-muted mb-0 mt-1"><small>{{ number_format($costi_manodopera[0]->ore_totali, 1, ',', '.') }} ore lavorate</small></p>
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
                <div class="col-xl-4 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Totale Costi Produzione</p>
                                    <h4 class="fs-22 fw-semibold mb-0">&euro; {{ number_format($costi_materiali[0]->totale + $costi_manodopera[0]->totale, 2, ',', '.') }}</h4>
                                    <p class="text-muted mb-0 mt-1"><small>Materiali + Manodopera</small></p>
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

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Incassi da Ricevere</h4>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    Totale: &euro; {{ number_format($incassi_da_ricevere[0]->totale, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <a href="/commesse/{{ $commessa->id }}/pagamenti" class="btn btn-primary btn-sm mb-3">
                                    <i class="ri-eye-line align-middle"></i> Visualizza Dettaglio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Pagamenti da Effettuare</h4>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    Totale: &euro; {{ number_format($pagamenti_da_effettuare[0]->totale, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <a href="/commesse/{{ $commessa->id }}/pagamenti" class="btn btn-primary btn-sm mb-3">
                                    <i class="ri-eye-line align-middle"></i> Visualizza Dettaglio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attività Recenti -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Attività</h4>
                            <div class="flex-shrink-0">
                                <a href="/commesse/{{ $commessa->id }}/attivita" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line align-middle"></i> Gestisci Attività
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Titolo</th>
                                            <th>Responsabile</th>
                                            <th>Data Inizio</th>
                                            <th>Data Fine</th>
                                            <th>Stato</th>
                                            <th>Completamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attivita as $a)
                                            <tr>
                                                <td>{{ $a->titolo }}</td>
                                                <td>{{ $a->responsabile ?? 'Non assegnato' }}</td>
                                                <td>{{ date('d/m/Y', strtotime($a->data_inizio)) }}</td>
                                                <td>{{ $a->data_fine ? date('d/m/Y', strtotime($a->data_fine)) : '-' }}</td>
                                                <td>
                                                    @if($a->stato == 'da_iniziare')
                                                        <span class="badge bg-secondary">Da Iniziare</span>
                                                    @elseif($a->stato == 'in_corso')
                                                        <span class="badge bg-primary">In Corso</span>
                                                    @elseif($a->stato == 'completata')
                                                        <span class="badge bg-success">Completata</span>
                                                    @else
                                                        <span class="badge bg-danger">In Ritardo</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $a->completamento }}%;" aria-valuenow="{{ $a->completamento }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span class="mt-1 d-block text-muted">{{ $a->completamento }}%</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Nessuna attività registrata</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ordini di Lavoro (ODL) collegati alla commessa -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Ordini di Lavoro (Produzione)</h4>
                            <div class="flex-shrink-0">
                                <span class="badge bg-info">{{ count($odl_commessa) }} ODL</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <i class="ri-information-line me-2"></i>
                                Gli ordini di lavoro mostrati qui sono quelli generati da documenti associati a questa commessa.
                                Quando un ordine viene mandato in produzione (ODL), i movimenti di magazzino e i costi di manodopera vengono automaticamente collegati a questa commessa.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
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
                                        @forelse($odl_commessa as $odl)
                                            <tr>
                                                <td>
                                                    <a href="/utente/dettaglio_odl/{{ $odl->id }}">
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
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Nessun ordine di lavoro collegato a questa commessa</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dettaglio Manodopera per Dipendente -->
            @if(count($dettaglio_manodopera) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Dettaglio Costi Manodopera</h4>
                            <div class="flex-shrink-0">
                                <span class="text-muted">Totale: &euro; {{ number_format($costi_manodopera[0]->totale, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                                <i class="ri-alert-line me-2"></i>
                                Il costo manodopera viene calcolato in base alle ore effettive di lavorazione (inizio/fine fase) moltiplicate per il costo orario del dipendente impostato nell'anagrafica.
                                Se il costo orario di un dipendente e 0, le sue ore saranno conteggiate ma non avranno impatto economico.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
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
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Documenti Collegati -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Documenti Collegati</h4>
                            <div class="flex-shrink-0">
                                <a href="/commesse/{{ $commessa->id }}/documenti" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line align-middle"></i> Gestisci Documenti
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Numero</th>
                                            <th>Data</th>
                                            <th>Cliente/Fornitore</th>
                                            <th>Totale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($documenti as $d)
                                            <tr>
                                                <td>{{ $d->cd_do }}</td>
                                                <td>{{ $d->numero_doc }}</td>
                                                <td>{{ date('d/m/Y', strtotime($d->data_doc)) }}</td>
                                                <td>{{ $d->cliente_nome }}</td>
                                                <td>&euro; {{ number_format($d->totale, 2, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Nessun documento collegato</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Movimenti Magazzino -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Movimenti Magazzino</h4>
                            <div class="flex-shrink-0">
                                <a href="/commesse/{{ $commessa->id }}/magazzino" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line align-middle"></i> Gestisci Movimenti
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Articolo</th>
                                            <th>Magazzino</th>
                                            <th>Quantità</th>
                                            <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($movimenti_magazzino as $m)

                                            <tr>
                                                <td>{{ date('d/m/Y H:i', strtotime($m->datamov)) }}</td>
                                                <td>{{ $m->articolo_nome ?? 'N/A' }} ({{ $m->codice_articolo }})</td>
                                                <td>{{ $m->magazzino_nome }}</td>
                                                <td>{{ $m->qta }}</td>
                                                <td>
                                                    @if($m->car == 1)
                                                        <span class="badge bg-success">Carico</span>
                                                    @elseif($m->sca == 1)
                                                        <span class="badge bg-danger">Scarico</span>
                                                    @elseif($m->ret == 1)
                                                        <span class="badge bg-warning">Reso</span>
                                                    @else
                                                        <span class="badge bg-info">Inventario</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Nessun movimento di magazzino collegato</td>
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
    </div>
</div>

<!-- Modal Modifica Stato -->
<div class="modal fade" id="modalModificaStato" tabindex="-1" aria-labelledby="modalModificaStatoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificaStatoLabel">Modifica Stato Commessa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/commesse/{{ $commessa->id }}/aggiorna-stato" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stato" class="form-label">Stato</label>
                        <select class="form-select" id="stato" name="stato" required>
                            <option value="aperta" {{ $commessa->stato == 'aperta' ? 'selected' : '' }}>Aperta</option>
                            <option value="in_corso" {{ $commessa->stato == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                            <option value="completata" {{ $commessa->stato == 'completata' ? 'selected' : '' }}>Completata</option>
                            <option value="annullata" {{ $commessa->stato == 'annullata' ? 'selected' : '' }}>Annullata</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Valori -->
<div class="modal fade" id="modalModificaValori" tabindex="-1" aria-labelledby="modalModificaValoriLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificaValoriLabel">Aggiorna Valori Commessa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/commesse/{{ $commessa->id }}/aggiorna-valori" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="{{ $commessa->budget }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="costi" class="form-label">Costi</label>
                            <input type="number" step="0.01" class="form-control" id="costi" name="costi" value="{{ $commessa->costi }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ricavi" class="form-label">Ricavi</label>
                            <input type="number" step="0.01" class="form-control" id="ricavi" name="ricavi" value="{{ $commessa->ricavi }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer') 