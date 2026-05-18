@include('utente.common.header')

@php
    $steps = [
        1 => ['label' => 'Ufficio', 'desc' => 'Apertura ordinativo', 'icon' => 'ri-file-add-line'],
        2 => ['label' => 'Programma', 'desc' => 'Invio al manutentore', 'icon' => 'ri-send-plane-line'],
        3 => ['label' => 'Manutentore', 'desc' => 'Report danni + magazzino', 'icon' => 'ri-tools-line'],
        4 => ['label' => 'Programma', 'desc' => 'Emissione preventivo/certificato', 'icon' => 'ri-file-text-line'],
        5 => ['label' => 'Ufficio', 'desc' => 'Accettazione', 'icon' => 'ri-check-double-line'],
        6 => ['label' => 'Programma', 'desc' => 'Invio fattura', 'icon' => 'ri-bill-line'],
    ];
    $cur = (int) $intervento->step_corrente;
@endphp

<style>
    .stepper-wrap { display: flex; align-items: flex-start; gap: 0; flex-wrap: nowrap; overflow-x: auto; padding: 12px 4px; }
    .stepper-item { flex: 1; min-width: 130px; text-align: center; position: relative; }
    .stepper-item::after {
        content: ''; position: absolute; top: 22px; right: -50%; width: 100%; height: 3px;
        background: #e5e7eb; z-index: 0;
    }
    .stepper-item:last-child::after { display: none; }
    .stepper-item.done::after { background: #16a34a; }
    .stepper-bubble {
        width: 44px; height: 44px; border-radius: 50%; background: #e5e7eb;
        color: #6b7280; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 600; position: relative; z-index: 1; border: 3px solid #fff;
        box-shadow: 0 0 0 1px #e5e7eb;
    }
    .stepper-item.active .stepper-bubble { background: #3b82f6; color: #fff; box-shadow: 0 0 0 1px #3b82f6; }
    .stepper-item.done .stepper-bubble { background: #16a34a; color: #fff; box-shadow: 0 0 0 1px #16a34a; }
    .stepper-item.blocked .stepper-bubble { opacity: 0.5; }
    .stepper-label { margin-top: 6px; font-size: 0.85rem; font-weight: 600; }
    .stepper-desc { font-size: 0.72rem; color: #6b7280; }
</style>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Intervento #{{ $intervento->id }}
                            @if($intervento->stato === 'completato') <span class="badge bg-success">Completato</span>
                            @elseif($intervento->stato === 'annullato') <span class="badge bg-danger">Annullato</span>
                            @else <span class="badge bg-info">In corso</span>
                            @endif
                        </h4>
                        <small class="text-muted">
                            {{ $intervento->cliente_ragione_sociale ?: '—' }}
                            @if($intervento->vagone_codice || $intervento->automezzo) · Vagone {{ $intervento->vagone_codice ?: $intervento->automezzo }}@endif
                            @if($intervento->data_apertura) · Aperto il {{ \Carbon\Carbon::parse($intervento->data_apertura)->format('d/m/Y') }}@endif
                        </small>
                    </div>
                    <div class="page-title-right">
                        <a href="/utente/interventi" class="btn btn-sm btn-light"><i class="ri-arrow-left-line me-1"></i>Torna alla lista</a>
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

        {{-- STEPPER --}}
        <div class="card">
            <div class="card-body">
                <div class="stepper-wrap">
                    @foreach($steps as $n => $s)
                        @php
                            $cls = '';
                            if ($n < $cur) $cls = 'done';
                            elseif ($n === $cur && $intervento->stato !== 'completato') $cls = 'active';
                            else $cls = 'blocked';
                            if ($intervento->stato === 'completato' && $n <= 6) $cls = 'done';
                        @endphp
                        <div class="stepper-item {{ $cls }}">
                            <div class="stepper-bubble">
                                @if($cls === 'done')
                                    <i class="ri-check-line"></i>
                                @else
                                    {{ $n }}
                                @endif
                            </div>
                            <div class="stepper-label">{{ $s['label'] }}</div>
                            <div class="stepper-desc">{{ $s['desc'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            {{-- COLONNA AZIONE STEP CORRENTE --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-soft-primary">
                        <h5 class="card-title mb-0">
                            <i class="{{ $steps[$cur]['icon'] }} me-2"></i>
                            Step {{ $cur }} — {{ $steps[$cur]['label'] }}: {{ $steps[$cur]['desc'] }}
                        </h5>
                    </div>
                    <div class="card-body">

                        @if($intervento->stato === 'completato')
                            <div class="alert alert-success mb-0"><i class="ri-check-double-line me-1"></i> Intervento completato. Tutti gli step sono stati eseguiti.</div>
                        @elseif($cur === 1)
                            <p>Lo step di apertura è iniziato quando hai creato l'intervento. Conferma i dati nella sidebar a destra. Quando tutto è ok, clicca "Completa Step 1" per inviare l'intervento alla fase successiva.</p>
                        @elseif($cur === 2)
                            <p>Devi <strong>assegnare un manutentore</strong> per la valutazione. (UI assegnazione: prossima sessione)</p>
                            <p class="text-muted small">Per ora: completa lo step quando hai comunicato verbalmente al manutentore.</p>
                        @elseif($cur === 3)
                            <p>Il manutentore sta valutando il vagone. (UI report danni + scarico magazzino: prossima sessione)</p>
                        @elseif($cur === 4)
                            <p>Il programma deve emettere il preventivo (e l'eventuale certificato). (Bottone "Crea Preventivo collegato": prossima sessione, intanto puoi crearlo manualmente da Documenti → PRE)</p>
                        @elseif($cur === 5)
                            <p>L'ufficio deve accettare o rifiutare il preventivo emesso. (UI accettazione: prossima sessione)</p>
                        @elseif($cur === 6)
                            <p>Il programma genera e invia la fattura. (UI emissione fattura: prossima sessione)</p>
                        @endif

                        @if($intervento->stato !== 'completato')
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/completa_step" class="mt-3">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">Note di completamento (opzionali)</label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="cosa è stato fatto in questo step"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success" onclick="return confirm('Confermi la chiusura dello step {{ $cur }}?');">
                                    <i class="ri-check-line me-1"></i> Completa Step {{ $cur }} e passa al prossimo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- LOG --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-history-line me-2"></i>Storico</h5>
                    </div>
                    <div class="card-body">
                        @if(count($log) === 0)
                            <p class="text-muted mb-0">Nessuno storico ancora.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($log as $entry)
                                    <li class="list-group-item">
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($entry->created_at)->format('d/m/Y H:i') }}</small>
                                        — <span class="badge bg-soft-info text-info">Step {{ $entry->step ?? '-' }}</span>
                                        <strong>{{ ucfirst($entry->azione) }}</strong>
                                        @if($entry->note)<br><small>{{ $entry->note }}</small>@endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SIDEBAR DATI INTERVENTO --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dati dell'intervento</h5>
                    </div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt class="small text-muted">Cliente</dt>
                            <dd>{{ $intervento->cliente_ragione_sociale ?: '—' }}</dd>

                            <dt class="small text-muted">Vagone</dt>
                            <dd>{{ $intervento->vagone_codice ?: $intervento->automezzo ?: '—' }}@if($intervento->vagone_tipo) <small class="text-muted">({{ $intervento->vagone_tipo }})</small>@endif</dd>

                            <dt class="small text-muted">Data apertura</dt>
                            <dd>{{ $intervento->data_apertura ? \Carbon\Carbon::parse($intervento->data_apertura)->format('d/m/Y') : '—' }}</dd>

                            <dt class="small text-muted">Località</dt>
                            <dd>{{ $intervento->localita ?: '—' }}</dd>

                            <dt class="small text-muted">Motivo rientro</dt>
                            <dd>{{ $intervento->reason_intake ?: '—' }}</dd>

                            <dt class="small text-muted">Priorità</dt>
                            <dd>
                                @if($intervento->priorita === 'alta')<span class="badge bg-danger">Alta</span>
                                @elseif($intervento->priorita === 'bassa')<span class="badge bg-secondary">Bassa</span>
                                @else<span class="badge bg-warning">Media</span>
                                @endif
                            </dd>

                            <dt class="small text-muted">Operatore assegnato</dt>
                            <dd>
                                @if($intervento->operatore_nome)
                                    {{ $intervento->operatore_nome }} {{ $intervento->operatore_cognome }}
                                @else
                                    <small class="text-muted">non assegnato</small>
                                @endif
                            </dd>

                            @if($intervento->note)
                                <dt class="small text-muted">Note</dt>
                                <dd>{{ $intervento->note }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')
