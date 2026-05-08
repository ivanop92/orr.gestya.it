@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Movimenti Articolo: {{ $articolo->titolo }} ({{ $articolo->codice_articolo }})</h4>
                    <div class="page-title-right">
                        <a href="{{ url('/utente/magazzino/giacenze/'.$magazzino->id) }}" class="btn btn-soft-primary">
                            <i class="ri-arrow-left-line align-middle me-1"></i> Torna alle Giacenze
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">Dettagli articolo</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th width="150">Codice</th>
                                            <td>{{ $articolo->codice_articolo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Descrizione</th>
                                            <td>{{ $articolo->titolo }}</td>
                                        </tr>
                                        <tr>
                                            <th>UM</th>
                                            <td>{{ $articolo->um }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th width="150">Magazzino</th>
                                            <td>{{ $magazzino->descrizione }}</td>
                                        </tr>
                                        <tr>
                                            <th>Giacenza attuale</th>
                                            <td>
                                                @php
                                                    $giacenza_totale = DB::table('mgmov')
                                                        ->where('id_articolo', $articolo->id)
                                                        ->where('id_mg', $magazzino->id)
                                                        ->sum('qta');
                                                @endphp
                                                {{ number_format($giacenza_totale, 2) }} {{ $articolo->um }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Punto di riordino</th>
                                            <td>{{ $articolo->punto_riordino ?? 'Non impostato' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Storia movimenti</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    @if($usa_lotti)<th>Lotto</th>@endif
                                    <th>Causale</th>
                                    <th>Quantità</th>
                                    <th>Commessa</th>
                                    <th>Documento</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($movimenti as $movimento)
                                    <tr>
                                        <td>{{ date('d/m/Y H:i', strtotime($movimento->datamov)) }}</td>
                                        <td>
                                            @if($movimento->car == 1)
                                                <span class="badge bg-success">Carico</span>
                                            @elseif($movimento->sca == 1)
                                                <span class="badge bg-danger">Scarico</span>
                                            @elseif($movimento->ret == 1)
                                                <span class="badge bg-warning">Rettifica</span>
                                            @elseif($movimento->ini == 1)
                                                <span class="badge bg-info">Inventario</span>
                                            @else
                                                <span class="badge bg-secondary">Altro</span>
                                            @endif
                                        </td>
                                        @if($usa_lotti)<td>{{ $movimento->lotto ?? '-' }}</td>@endif
                                        <td>{{ $movimento->causale }}</td>
                                        <td class="{{ $movimento->qta > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($movimento->qta, 2) }} {{ $articolo->um }}
                                        </td>
                                        <td>
                                            @if($movimento->id_commessa)
                                                <a href="{{ url('/utente/commesse/'.$movimento->id_commessa.'/attivita') }}" class="badge bg-info">
                                                    {{ $movimento->codice_commessa }}
                                                </a>
                                                <span class="d-block text-muted small">{{ Str::limit($movimento->descrizione_commessa, 30) }}</span>
                                                @if($movimento->stato_commessa)
                                                    <span class="badge bg-{{ $movimento->stato_commessa == 'aperta' ? 'success' : ($movimento->stato_commessa == 'in_corso' ? 'primary' : 'secondary') }}">
                                                        {{ ucfirst($movimento->stato_commessa) }}
                                                    </span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($movimento->cd_do && $movimento->numero_doc)
                                                <a href="{{ url('/utente/modifica_documento/'.$movimento->id_dotes) }}" class="link-primary">
                                                    {{ $movimento->cd_do }} {{ $movimento->numero_doc }}
                                                </a>
                                            @elseif($movimento->id_dorig)
                                                Doc. #{{ $movimento->id_dorig }}
                                            @elseif($movimento->id_odl)
                                                <a href="{{ url('/utente/dettaglio_odl/'.$movimento->id_odl) }}" class="link-primary">
                                                    ODL #{{ $movimento->id_odl }}
                                                </a>
                                            @else
                                                -
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
        </div>
    </div>
</div>

@include('utente.common.footer')