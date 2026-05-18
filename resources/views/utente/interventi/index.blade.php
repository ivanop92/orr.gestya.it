@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0"><i class="ri-tools-line me-2"></i>Interventi Manutenzione</h4>
                    <div class="page-title-right">
                        <a href="/utente/interventi/nuovo" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i> Nuovo Intervento
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errore!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <form method="get" class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cerca per cliente, vagone, sintomo...">
                                <button type="submit" class="btn btn-soft-primary"><i class="ri-search-line"></i></button>
                                @if(!empty($q) || !empty($filtro_stato))
                                    <a href="/utente/interventi" class="btn btn-light">Annulla</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="stato" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Tutti gli stati</option>
                                <option value="in_corso" {{ $filtro_stato === 'in_corso' ? 'selected' : '' }}>In corso</option>
                                <option value="completato" {{ $filtro_stato === 'completato' ? 'selected' : '' }}>Completati</option>
                                <option value="annullato" {{ $filtro_stato === 'annullato' ? 'selected' : '' }}>Annullati</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <small class="text-muted">
                                <strong>{{ $interventi->total() }}</strong> interventi
                                @if($interventi->total() > 0)
                                    · Pagina {{ $interventi->currentPage() }} di {{ max($interventi->lastPage(), 1) }}
                                @endif
                            </small>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Cliente / Vagone</th>
                        <th>Apertura</th>
                        <th>Sintomo</th>
                        <th>Operatore</th>
                        <th style="width:160px;">Step</th>
                        <th style="width:100px;">Stato</th>
                        <th style="width:60px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($interventi as $i)
                        <tr>
                            <td>#{{ $i->id }}</td>
                            <td>
                                <strong>{{ $i->cliente_ragione_sociale ?: '—' }}</strong>
                                @if($i->vagone_codice || $i->automezzo)
                                    <br><small class="text-muted">{{ $i->vagone_codice ?: $i->automezzo }}</small>
                                @endif
                            </td>
                            <td>{{ $i->data_apertura ? \Carbon\Carbon::parse($i->data_apertura)->format('d/m/Y') : '' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($i->reason_intake, 60) }}</td>
                            <td>
                                @if($i->operatore_nome)
                                    {{ $i->operatore_nome }} {{ $i->operatore_cognome }}
                                @else
                                    <small class="text-muted">non assegnato</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stepLabel = ['1.Ufficio','2.Programma','3.Manutentore','4.Programma','5.Ufficio','6.Programma'];
                                    $stepColor = ['secondary','info','warning','info','primary','success'];
                                    $idx = (int) $i->step_corrente - 1;
                                @endphp
                                <span class="badge bg-{{ $stepColor[$idx] ?? 'secondary' }}">Step {{ $i->step_corrente }} — {{ $stepLabel[$idx] ?? '?' }}</span>
                            </td>
                            <td>
                                @if($i->stato === 'completato')
                                    <span class="badge bg-success">Completato</span>
                                @elseif($i->stato === 'annullato')
                                    <span class="badge bg-danger">Annullato</span>
                                @else
                                    <span class="badge bg-info">In corso</span>
                                @endif
                            </td>
                            <td>
                                <a href="/utente/interventi/{{ $i->id }}" class="btn btn-sm btn-soft-primary" title="Apri">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if($interventi->total() === 0)
                        <tr><td colspan="8" class="text-center text-muted py-4">
                            @if(!empty($q) || !empty($filtro_stato))
                                Nessun intervento trovato. <a href="/utente/interventi">Annulla filtri</a>.
                            @else
                                Nessun intervento ancora. <a href="/utente/interventi/nuovo">Apri il primo</a>.
                            @endif
                        </td></tr>
                    @endif
                    </tbody>
                </table>

                @if($interventi->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <small class="text-muted">Mostra <strong>{{ $interventi->firstItem() }}</strong>–<strong>{{ $interventi->lastItem() }}</strong> di <strong>{{ $interventi->total() }}</strong></small>
                        {!! $interventi->links('utente.common._pagination') !!}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')
