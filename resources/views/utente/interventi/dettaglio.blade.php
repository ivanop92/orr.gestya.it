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

        @if(isset($segnalazioni_cliente) && count($segnalazioni_cliente) > 0)
            @foreach($segnalazioni_cliente as $seg)
                <div class="alert alert-warning border-warning">
                    <div class="d-flex align-items-start">
                        <i class="ri-error-warning-fill text-warning me-2" style="font-size:1.5rem;"></i>
                        <div class="flex-grow-1">
                            <strong>Segnalazione dal cliente</strong>
                            <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($seg->created_at)->format('d/m/Y H:i') }}</small>
                            <p class="mb-1 mt-1" style="white-space:pre-wrap;">{{ $seg->testo }}</p>
                            @if($seg->contatto)
                                <small><i class="ri-phone-line me-1"></i><strong>Contatto fornito:</strong> {{ $seg->contatto }}</small><br>
                            @endif
                            <small class="text-muted">Consiglio: ricontattare il cliente per chiarire prima di proseguire.</small>
                        </div>
                    </div>
                </div>
            @endforeach
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
                            <p>Emetti il <strong>preventivo</strong> (con prezzi) e il <strong>certificato di manutenzione</strong> (dichiarazione tecnica). Le righe vengono prese dalle lavorazioni proposte dal manutentore.</p>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="mb-2"><i class="ri-file-text-line me-1 text-success"></i>Preventivo</h6>
                                            @if($intervento->id_dotes_preventivo)
                                                <p class="mb-2 small text-success"><i class="ri-check-line"></i> Già emesso</p>
                                                <a href="/utente/dettaglio_documento/{{ $intervento->id_dotes_preventivo }}" class="btn btn-sm btn-soft-primary">Apri</a>
                                                <a href="/utente/modifica_documento/{{ $intervento->id_dotes_preventivo }}" class="btn btn-sm btn-soft-warning"><i class="ri-pencil-line"></i> Modifica</a>
                                            @else
                                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/step4_emetti_preventivo">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Creare il preventivo?');">
                                                        <i class="ri-file-add-line me-1"></i>Crea Preventivo
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="mb-2"><i class="ri-shield-check-line me-1 text-info"></i>Certificato di Manutenzione</h6>
                                            @if($intervento->id_dotes_certificato)
                                                <p class="mb-2 small text-success"><i class="ri-check-line"></i> Già emesso</p>
                                                <a href="/utente/interventi/{{ $intervento->id }}/certificato/pdf" class="btn btn-sm btn-soft-primary" target="_blank">
                                                    <i class="ri-file-pdf-line"></i> Scarica PDF
                                                </a>
                                                <a href="/utente/modifica_documento/{{ $intervento->id_dotes_certificato }}" class="btn btn-sm btn-soft-warning"><i class="ri-pencil-line"></i> Modifica</a>
                                            @else
                                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/step4_emetti_certificato">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Creare il certificato di manutenzione?');" {{ !$intervento->id_dotes_preventivo ? 'disabled' : '' }}>
                                                        <i class="ri-shield-check-line me-1"></i>Crea Certificato
                                                    </button>
                                                </form>
                                                @if(!$intervento->id_dotes_preventivo)
                                                    <small class="text-muted d-block mt-1">Crea prima il preventivo</small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($intervento->id_dotes_preventivo)
                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/completa_step">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-arrow-right-line me-1"></i> Passa allo Step 5
                                    </button>
                                </form>
                            @endif

                        @elseif($cur === 5)
                            @if($intervento->id_dotes_preventivo)
                                <div class="alert alert-info mb-3">
                                    Preventivo emesso:
                                    <a href="/utente/dettaglio_documento/{{ $intervento->id_dotes_preventivo }}" class="alert-link">
                                        Apri preventivo #{{ $intervento->id_dotes_preventivo }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($firma_preventivo) && !empty($firma_preventivo->firmato_il))
                                <div class="alert alert-success mb-3">
                                    <strong><i class="ri-check-double-line me-1"></i>Firmato dal cliente</strong><br>
                                    <strong>{{ $firma_preventivo->firmato_da_nome }}</strong> ha firmato il
                                    {{ \Carbon\Carbon::parse($firma_preventivo->firmato_il)->format('d/m/Y H:i') }}
                                    <small class="text-muted d-block">SMS OTP · numero {{ $firma_preventivo->firma_telefono }} · IP {{ $firma_preventivo->firma_ip }}</small>
                                </div>
                            @elseif(!empty($firma_preventivo) && !empty($firma_preventivo->firma_token))
                                <div class="alert alert-warning mb-3">
                                    <i class="ri-time-line me-1"></i>In attesa di firma cliente —
                                    <a href="/firma/{{ $firma_preventivo->firma_token }}" target="_blank" class="alert-link">Link pubblico</a>
                                </div>
                            @endif
                            <p>Invia il preventivo al cliente, oppure decidi internamente.</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_invia_preventivo">
                                    <i class="ri-mail-send-line me-1"></i> Invia preventivo al cliente
                                </button>
                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/step5_decisione">
                                    @csrf
                                    <input type="hidden" name="azione" value="accetta">
                                    <button type="submit" class="btn btn-soft-success" onclick="return confirm('Accettare il preventivo?');">
                                        <i class="ri-check-line me-1"></i> Accetta
                                    </button>
                                </form>
                                <button type="button" class="btn btn-soft-danger" data-bs-toggle="modal" data-bs-target="#modal_rifiuta_intervento">
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

                            @if(!$intervento->id_dotes_fattura)
                                <p>Crea la fattura dal preventivo accettato. Le righe verranno copiate e potrai modificarle prima di emetterla.</p>
                                <form method="post" action="/utente/interventi/{{ $intervento->id }}/step6_crea_fattura">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="ri-file-text-line me-1"></i> Crea Fattura dal Preventivo
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info mb-3">
                                    Fattura emessa:
                                    <a href="/utente/dettaglio_documento/{{ $intervento->id_dotes_fattura }}" class="alert-link">Apri</a> ·
                                    <a href="/utente/modifica_documento/{{ $intervento->id_dotes_fattura }}" class="alert-link"><i class="ri-pencil-line"></i> Modifica righe e prezzi</a>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <a href="/utente/interventi/{{ $intervento->id }}/fattura/pdf" class="btn btn-soft-primary" target="_blank">
                                        <i class="ri-file-pdf-line me-1"></i> Scarica PDF Fattura
                                    </a>
                                    <a href="/utente/interventi/{{ $intervento->id }}/fattura/xml" class="btn btn-soft-info" target="_blank">
                                        <i class="ri-file-code-line me-1"></i> Scarica XML SDI
                                    </a>
                                    <button type="button" class="btn btn-soft-secondary" disabled title="Funzionalità in arrivo">
                                        <i class="ri-send-plane-line me-1"></i> Invia SDI <small>(in arrivo)</small>
                                    </button>
                                </div>

                                @if(empty($intervento->fattura_inviata_il))
                                    <hr>
                                    <p class="text-muted small mb-2">Quando hai inviato la fattura (manualmente o via SDI), segna l'intervento come completato:</p>
                                    <form method="post" action="/utente/interventi/{{ $intervento->id }}/step6_completa">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Confermi la chiusura dell\'intervento?');">
                                            <i class="ri-check-double-line me-1"></i> Marca Inviata → Completa Intervento
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                {{-- DATI DAL MANUTENTORE (visibili dopo step 3) --}}
                @if((isset($proposte) && count($proposte) > 0) || (isset($materiali) && count($materiali) > 0) || (isset($allegati) && count($allegati) > 0))
                    <div class="card">
                        <div class="card-header bg-soft-warning">
                            <h5 class="card-title mb-0"><i class="ri-tools-line me-2"></i>Dati dal manutentore</h5>
                        </div>
                        <div class="card-body">

                            {{-- Righe lavorazione proposte --}}
                            @if(count($proposte) > 0)
                                <h6 class="mb-2"><i class="ri-list-check-2 me-1"></i>Lavorazioni proposte ({{ count($proposte) }})
                                    @php $totProposte = $proposte->sum('pt'); @endphp
                                    <small class="text-muted">— totale stimato € {{ number_format($totProposte,2,',','.') }}</small>
                                </h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered align-middle small">
                                        <thead class="table-light">
                                        <tr>
                                            <th style="width:60px;">Servizio</th>
                                            <th style="width:90px;">Codice</th>
                                            <th>Descrizione</th>
                                            <th class="text-end" style="width:70px;">Qta</th>
                                            <th class="text-end" style="width:60px;">Min</th>
                                            <th class="text-end" style="width:50px;">Att.</th>
                                            <th class="text-end" style="width:80px;">P.U.</th>
                                            <th class="text-end" style="width:60px;">IVA%</th>
                                            <th class="text-end" style="width:90px;">Materiale</th>
                                            <th class="text-end" style="width:90px;">Totale</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($proposte as $p)
                                            <tr>
                                                <td>{{ $p->servizio }}</td>
                                                <td>{{ $p->codice }}</td>
                                                <td>{{ $p->descrizione }}</td>
                                                <td class="text-end">{{ rtrim(rtrim(number_format($p->qta,3,',','.'),'0'),',') }}</td>
                                                <td class="text-end">{{ rtrim(rtrim(number_format($p->minuti,2,',',''),'0'),',') ?: '—' }}</td>
                                                <td class="text-end">{{ rtrim(rtrim(number_format($p->attivita,2,',',''),'0'),',') }}</td>
                                                <td class="text-end">€ {{ number_format($p->pu,2,',','.') }}</td>
                                                <td class="text-end">{{ $p->aliquota }}</td>
                                                <td class="text-end">{{ $p->materiale > 0 ? '€ '.number_format($p->materiale,2,',','.') : '—' }}</td>
                                                <td class="text-end fw-medium">€ {{ number_format($p->pt,2,',','.') }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-muted small mb-3">
                                    <i class="ri-information-line me-1"></i>
                                    Queste righe verranno
                                    @if($intervento->id_dotes_preventivo) copiate @else automaticamente caricate @endif
                                    nel preventivo PRE allo step 4.
                                </p>
                            @endif

                            {{-- Materiali scaricati --}}
                            @if(count($materiali) > 0)
                                <h6 class="mb-2"><i class="ri-box-3-line me-1"></i>Materiali utilizzati ({{ count($materiali) }})</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered align-middle small">
                                        <thead class="table-light">
                                        <tr>
                                            <th style="width:90px;">Codice</th>
                                            <th>Descrizione</th>
                                            <th class="text-end" style="width:90px;">Qta</th>
                                            <th style="width:60px;">UM</th>
                                            <th style="width:130px;">Magazzino</th>
                                            <th>Note</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($materiali as $m)
                                            <tr>
                                                <td>{{ $m->codice }}</td>
                                                <td>{{ $m->descrizione }}@if($m->articolo_titolo && $m->articolo_titolo != $m->descrizione) <small class="text-muted">({{ $m->articolo_titolo }})</small>@endif</td>
                                                <td class="text-end">{{ rtrim(rtrim(number_format($m->qta,3,',','.'),'0'),',') }}</td>
                                                <td>{{ $m->um }}</td>
                                                <td>
                                                    @if($m->id_mgmov)
                                                        <span class="badge bg-success" title="Movimento {{ $m->id_mgmov }}"><i class="ri-store-2-line"></i> Scaricato</span>
                                                        @if($m->magazzino_descrizione)
                                                            <br><small class="text-muted">{{ $m->magazzino_descrizione }}</small>
                                                        @endif
                                                        @if(!is_null($m->articolo_giacenza_attuale))
                                                            <br><small class="text-muted">Giac. attuale: {{ rtrim(rtrim(number_format($m->articolo_giacenza_attuale,3,',','.'),'0'),',') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary" title="Solo dichiarato dal manutentore">Manuale</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $m->note }}</small></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            {{-- Allegati / Foto --}}
                            @if(count($allegati) > 0)
                                <h6 class="mb-2"><i class="ri-attachment-line me-1"></i>Allegati / Foto ({{ count($allegati) }})</h6>
                                <div class="row g-2">
                                    @foreach($allegati as $a)
                                        <div class="col-3 col-md-2">
                                            @if(strpos($a->mime ?? '', 'image/') === 0)
                                                <a href="/{{ $a->filename }}" target="_blank">
                                                    <img src="/{{ $a->filename }}" alt="" class="img-fluid rounded border" style="aspect-ratio:1; object-fit:cover; width:100%;">
                                                </a>
                                            @else
                                                <a href="/{{ $a->filename }}" target="_blank" class="d-block text-center p-3 border rounded text-decoration-none" style="aspect-ratio:1;">
                                                    <i class="ri-file-text-line" style="font-size:2rem;"></i>
                                                    <div class="small text-truncate">{{ $a->original_name }}</div>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

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

<div class="modal fade" id="modal_invia_preventivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="post" action="/utente/interventi/{{ $intervento->id }}/invia_preventivo_email">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-primary p-3">
                    <h5 class="modal-title"><i class="ri-mail-send-line me-2"></i>Invia preventivo via email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Destinatari <b style="color:red">*</b></label>
                        <input type="text" name="destinatari" class="form-control" value="{{ $intervento->cliente_email ?? '' }}" required placeholder="email@dominio.it; altra@dominio.it">
                        <small class="text-muted">Separa più indirizzi con <code>;</code> o <code>,</code></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CC (opzionale)</label>
                        <input type="text" name="cc" class="form-control" placeholder="copia@dominio.it">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Oggetto</label>
                        <input type="text" name="oggetto" class="form-control" value="Preventivo per intervento di manutenzione - {{ $intervento->cliente_ragione_sociale }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Messaggio</label>
                        @php
                            $vagoneLabel = $intervento->vagone_codice ?: $intervento->automezzo;
                            $msgDefault = "Buongiorno,\n\nin allegato trovate il preventivo relativo all'intervento di manutenzione" . ($vagoneLabel ? " sul vagone $vagoneLabel" : "") . ".\n\nRestiamo a disposizione per qualsiasi chiarimento.\n\nCordiali saluti";
                        @endphp
                        <textarea name="messaggio" class="form-control" rows="6">{{ $msgDefault }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Firma <small class="text-muted">(appare in fondo all'email)</small></label>
                        <textarea name="firma" class="form-control" rows="3" placeholder="Nome Cognome&#10;Ruolo&#10;Email · Telefono">{{ $firma_utente ?? ($utente->nome.' '.$utente->cognome) }}</textarea>
                        <div class="form-check form-check-sm mt-1">
                            <input class="form-check-input" type="checkbox" name="salva_firma" id="salva_firma" value="1">
                            <label class="form-check-label small" for="salva_firma">Salva questa firma sul mio profilo per la prossima volta</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-send-plane-line me-1"></i> Invia Email
                    </button>
                </div>
            </div>
        </form>
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
