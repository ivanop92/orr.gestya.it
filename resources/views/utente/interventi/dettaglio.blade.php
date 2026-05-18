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
                            <p>Verifica i dati di apertura nella sidebar a destra. Quando tutto è ok clicca <strong>"Completa Step 1"</strong> per passare alla fase di assegnazione manutentore.</p>
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/completa_step" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Completare lo step 1?');">
                                    <i class="ri-check-line me-1"></i> Completa Step 1 → vai allo Step 2
                                </button>
                            </form>

                        @elseif($cur === 2)
                            <p>Assegna il manutentore che dovrà valutare il vagone.</p>
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/step2_assegna">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Manutentore <b style="color:red">*</b></label>
                                    <select name="id_operatore_assegnato" class="form-select" required>
                                        <option value="">— Seleziona manutentore —</option>
                                        @foreach($operatori as $op)
                                            <option value="{{ $op->id }}" {{ $intervento->id_operatore_assegnato == $op->id ? 'selected' : '' }}>
                                                {{ $op->nome }} {{ $op->cognome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(count($operatori) === 0)
                                        <small class="text-danger">Nessun operatore di produzione disponibile. Crealo prima in Anagrafiche → Dipendenti.</small>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-send-plane-line me-1"></i> Assegna e passa allo Step 3
                                </button>
                            </form>

                        @elseif($cur === 3)
                            <p>Il manutentore inserisce il <strong>report danni</strong> a seguito della valutazione del vagone.</p>
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/step3_report">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Report danni <b style="color:red">*</b></label>
                                    <textarea name="report_danni" class="form-control" rows="6" required placeholder="Descrivi i danni rilevati, le parti da sostituire, lo stato del vagone...">{{ $intervento->report_danni }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line me-1"></i> Invia report e passa allo Step 4
                                </button>
                            </form>

                        @elseif($cur === 4)
                            @if(!empty($intervento->report_danni))
                                <div class="alert alert-light border mb-3">
                                    <strong><i class="ri-tools-line me-1"></i>Report danni dal manutentore:</strong>
                                    <p class="mb-0 mt-1 text-muted">{{ $intervento->report_danni }}</p>
                                </div>
                            @endif
                            @if(!empty($intervento->motivo_rifiuto))
                                <div class="alert alert-warning mb-3">
                                    <strong>⚠️ Preventivo precedente rifiutato:</strong> {{ $intervento->motivo_rifiuto }}
                                    <br><small>Rilavora e riemetti un nuovo preventivo.</small>
                                </div>
                            @endif
                            <p>Emetti il preventivo collegato all'intervento. Cliente, vagone, motivo e report danni verranno pre-popolati. Aggiungerai le righe lavorazione dopo la creazione.</p>
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/step4_emetti_preventivo">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Creare il preventivo collegato a questo intervento?');">
                                    <i class="ri-file-add-line me-1"></i> Crea Preventivo Collegato
                                </button>
                            </form>

                        @elseif($cur === 5)
                            @if($intervento->id_dotes_preventivo)
                                <div class="alert alert-info mb-3">
                                    Preventivo emesso:
                                    <a href="/utente/dettaglio_documento/{{ $intervento->id_dotes_preventivo }}" class="alert-link">
                                        Apri preventivo #{{ $intervento->id_dotes_preventivo }}
                                    </a>
                                </div>
                            @endif
                            <p>Decidi se accettare o rifiutare il preventivo.</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/step5_decisione">
                                    @csrf
                                    <input type="hidden" name="azione" value="accetta">
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Accettare il preventivo?');">
                                        <i class="ri-check-line me-1"></i> Accetta → Step 6
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_rifiuta_intervento">
                                    <i class="ri-close-circle-line me-1"></i> Rifiuta
                                </button>
                            </div>

                        @elseif($cur === 6)
                            @if($intervento->id_dotes_preventivo)
                                <div class="alert alert-success mb-3">
                                    Preventivo accettato il {{ $intervento->accettato_il ? \Carbon\Carbon::parse($intervento->accettato_il)->format('d/m/Y H:i') : '' }} —
                                    <a href="/utente/dettaglio_documento/{{ $intervento->id_dotes_preventivo }}" class="alert-link">apri preventivo</a>
                                </div>
                            @endif
                            <p>Genera la fattura dal preventivo accettato (via flusso Gestya esistente). Quando l'hai inviata, segna l'intervento come <strong>completato</strong>.</p>
                            <form method="post" action="/utente/interventi/{{ $intervento->id }}/step6_fattura">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Confermi che la fattura è stata inviata?');">
                                    <i class="ri-bill-line me-1"></i> Marca Fattura Inviata → Completa Intervento
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

<div class="modal fade" id="modal_rifiuta_intervento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="/utente/interventi/{{ $intervento->id }}/step5_decisione">
            @csrf
            <input type="hidden" name="azione" value="rifiuta">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-danger p-3">
                    <h5 class="modal-title">Rifiuta Preventivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Indica il motivo del rifiuto. L'intervento tornerà allo Step 4 (Programma) per rilavorazione.</p>
                    <label class="form-label">Motivo rifiuto <b style="color:red">*</b></label>
                    <textarea name="motivo_rifiuto" class="form-control" rows="3" required placeholder="Spiega perché il preventivo è rifiutato"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-close-circle-line me-1"></i> Conferma Rifiuto
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('utente.common.footer')
