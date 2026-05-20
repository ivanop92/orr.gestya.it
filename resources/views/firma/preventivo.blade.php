<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>Preventivo n. {{ $dotes->numero_doc }}{{ isset($azienda) && $azienda ? ' · '.$azienda->ragione_sociale : '' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body { background: #f6f7fa; }
        .topbar { background: #0f766e; color: #fff; padding: 14px 16px; }
        .doc-card { background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 16px; }
        .doc-header { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; }
        .doc-header > div { flex: 1; min-width: 250px; }
        .label { color: #6b7280; font-size: 0.78rem; text-transform: uppercase; }
        .total-box { background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 8px; padding: 14px; text-align: right; }
        .firma-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 18px; }
        .firma-ok { background: #ecfdf5; border-color: #6ee7b7; }
    </style>
</head>
<body>

<div class="topbar">
    <strong><i class="ri-file-text-line me-1"></i>Preventivo n. {{ $dotes->numero_doc }}/{{ date('Y', strtotime($dotes->data_doc)) }}</strong>
    @if(!empty($azienda->ragione_sociale))
        <span class="ms-2 opacity-75">· {{ $azienda->ragione_sociale }}</span>
    @endif
</div>

<div class="container py-3" style="max-width: 920px;">

    <div class="doc-card">
        <div class="doc-header">
            <div>
                <div class="label">Mittente</div>
                <div><strong>{{ $azienda->ragione_sociale ?? '' }}</strong></div>
                <div class="text-muted small">{{ $azienda->indirizzo ?? '' }}<br>{{ $azienda->cap ?? '' }} {{ $azienda->comune ?? '' }} ({{ $azienda->provincia ?? '' }})</div>
                @if(!empty($azienda->partita_iva)) <div class="text-muted small">P.IVA {{ $azienda->partita_iva }}</div>@endif
            </div>
            <div>
                <div class="label">Destinatario</div>
                <div><strong>{{ $dotes->ragione_sociale }}</strong></div>
                <div class="text-muted small">{{ $dotes->indirizzo }}<br>{{ $dotes->cap }} {{ $dotes->comune }} ({{ $dotes->provincia }})</div>
                @if(!empty($dotes->partita_iva)) <div class="text-muted small">P.IVA {{ $dotes->partita_iva }}</div>@endif
            </div>
            <div>
                <div class="label">Documento</div>
                <div>N. <strong>{{ $dotes->numero_doc }}</strong> del {{ \Carbon\Carbon::parse($dotes->data_doc)->format('d/m/Y') }}</div>
                @if(!empty($dotes->automezzo) || !empty($dotes->id_vagone))
                    <div class="text-muted small">Vagone: {{ $dotes->automezzo ?: '#'.$dotes->id_vagone }}</div>
                @endif
                @if(!empty($dotes->localita)) <div class="text-muted small">Località: {{ $dotes->localita }}</div>@endif
                @if(!empty($dotes->reason_intake)) <div class="text-muted small">Motivo: {{ $dotes->reason_intake }}</div>@endif
            </div>
        </div>
    </div>

    <div class="doc-card p-0">
        <table class="table table-bordered mb-0 align-middle">
            <thead class="table-light">
            <tr>
                <th style="width:50px;">#</th>
                <th style="width:70px;">Serv.</th>
                <th style="width:90px;">Codice</th>
                <th>Descrizione</th>
                <th class="text-end" style="width:70px;">Qta</th>
                <th class="text-end" style="width:90px;">P.U.</th>
                <th class="text-end" style="width:60px;">IVA%</th>
                <th class="text-end" style="width:100px;">Totale</th>
            </tr>
            </thead>
            <tbody>
                @foreach($righe as $idx => $r)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $r->servizio }}</td>
                        <td>{{ $r->cd_ar }}</td>
                        <td>
                            <strong>{{ $r->descrizione ?: $r->nome_prodotto }}</strong>
                            @if(!empty($r->descrizione_materiale))
                                <br><small class="text-muted">Materiale: {{ $r->descrizione_materiale }}@if(!empty($r->materiale)) (€ {{ number_format($r->materiale,2,',','.') }})@endif</small>
                            @endif
                            @if(!empty($r->minuti) && $r->minuti > 0)
                                <br><small class="text-muted">Tempo: {{ rtrim(rtrim(number_format($r->minuti,2,',',''),'0'),',') }} min</small>
                            @endif
                        </td>
                        <td class="text-end">{{ rtrim(rtrim(number_format($r->qta,3,',','.'),'0'),',') }} {{ $r->um }}</td>
                        <td class="text-end">€ {{ number_format($r->prezzo_unitario,2,',','.') }}</td>
                        <td class="text-end">{{ $r->iva }}</td>
                        <td class="text-end"><strong>€ {{ number_format($r->prezzo_totale,2,',','.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="doc-card">
        <div class="row">
            <div class="col-md-7">
                @if(!empty($dotes->note_operatore))
                    <div class="label">Note</div>
                    <div class="small text-muted" style="white-space:pre-wrap;">{{ $dotes->note_operatore }}</div>
                @endif
            </div>
            <div class="col-md-5">
                <div class="total-box">
                    <div>Imponibile: <strong>€ {{ number_format($dotes->imponibile, 2, ',', '.') }}</strong></div>
                    <div>Imposta: <strong>€ {{ number_format($dotes->imposta, 2, ',', '.') }}</strong></div>
                    <hr class="my-2">
                    <div style="font-size:1.3rem;">Totale: <strong>€ {{ number_format($dotes->totale, 2, ',', '.') }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- BLOCCO FIRMA --}}
    @if(!empty($viewOnly) && empty($dotes->firmato_il))
        <div class="doc-card" style="background:#f1f5f9; border:1px solid #cbd5e1;">
            <small class="text-muted"><i class="ri-information-line me-1"></i>Documento in sola lettura. Se hai bisogno di firmarlo digitalmente, contatta l'ufficio per l'invio del link con firma.</small>
        </div>
    @elseif(!empty($dotes->firmato_il))
        <div class="doc-card firma-box firma-ok">
            <h5 class="mb-2"><i class="ri-check-double-line text-success me-1"></i>Preventivo firmato</h5>
            <div><strong>{{ $dotes->firmato_da_nome }}</strong> ha firmato questo preventivo il <strong>{{ \Carbon\Carbon::parse($dotes->firmato_il)->format('d/m/Y H:i') }}</strong></div>
            <div class="small text-muted mt-1">Firma digitale via SMS OTP (numero {{ $dotes->firma_telefono }}, IP {{ $dotes->firma_ip }})</div>
        </div>
        <div class="text-center my-3">
            <button type="button" class="btn btn-link text-muted btn-sm" onclick="document.getElementById('segnalazione_box').scrollIntoView({behavior:'smooth'})">
                <i class="ri-error-warning-line me-1"></i>Hai un problema con questo preventivo? Segnalalo
            </button>
        </div>
    @else
        <div class="doc-card firma-box">
            <h5 class="mb-3"><i class="ri-pen-nib-line me-1"></i>Firma il preventivo</h5>
            <p class="text-muted">Per accettare questo preventivo riceverai un codice OTP via SMS sul numero che indichi. Inseriscilo per confermare la firma.</p>

            <div id="step_telefono">
                <div class="mb-2">
                    <label class="form-label">Il tuo numero di cellulare</label>
                    <input type="tel" id="telefono" class="form-control" placeholder="es. 3331234567" required>
                </div>
                <button type="button" class="btn btn-primary" onclick="inviaOtp()">
                    <i class="ri-send-plane-line me-1"></i> Invia codice OTP via SMS
                </button>
                <div id="err_telefono" class="text-danger small mt-2" style="display:none;"></div>
            </div>

            <div id="step_otp" style="display:none;">
                <div class="alert alert-info small">Codice inviato a <strong id="numero_inviato"></strong></div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Codice OTP</label>
                        <input type="text" id="otp" class="form-control" placeholder="6 cifre" maxlength="6" inputmode="numeric">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nome e cognome</label>
                        <input type="text" id="nome" class="form-control" placeholder="Mario Rossi">
                    </div>
                </div>
                <button type="button" class="btn btn-success" onclick="verificaOtp()">
                    <i class="ri-check-line me-1"></i> Firma il preventivo
                </button>
                <button type="button" class="btn btn-link btn-sm" onclick="tornaTelefono()">Cambia numero</button>
                <div id="err_otp" class="text-danger small mt-2" style="display:none;"></div>
            </div>

            <div id="step_done" style="display:none;" class="alert alert-success mt-3">
                <i class="ri-check-double-line me-1"></i> Preventivo firmato con successo! Ricarica la pagina per vedere la conferma.
            </div>
        </div>
    @endif

    {{-- SEGNALA UN PROBLEMA --}}
    <div class="doc-card" id="segnalazione_box" style="background:#fff7ed; border:1px solid #fdba74;">
        <h6 class="mb-2"><i class="ri-error-warning-line text-warning me-1"></i>Hai un problema con il preventivo?</h6>
        <p class="small text-muted mb-2">Se la firma non funziona o hai un dubbio su qualcosa, segnalacelo. Ti ricontatteremo a breve.</p>
        <div id="segnala_form">
            <div class="mb-2">
                <textarea id="seg_testo" class="form-control" rows="3" placeholder="Descrivi il problema: cosa hai trovato che non va, cosa non torna nel preventivo, oppure un problema con la firma SMS..."></textarea>
            </div>
            <div class="mb-2">
                <input type="text" id="seg_contatto" class="form-control" placeholder="Il tuo contatto (telefono o email) — opzionale">
            </div>
            <button type="button" class="btn btn-warning" onclick="inviaSegnalazione()">
                <i class="ri-send-plane-line me-1"></i> Invia segnalazione
            </button>
            <div id="err_segnala" class="text-danger small mt-2" style="display:none;"></div>
        </div>
        <div id="segnala_done" style="display:none;" class="alert alert-success mt-2 mb-0">
            <i class="ri-check-line me-1"></i> Segnalazione inviata. L'ufficio è stato avvisato.
        </div>
    </div>

</div>

<script>
    const TOKEN = @json($dotes->firma_token);

    async function inviaOtp() {
        const tel = document.getElementById('telefono').value.trim();
        const err = document.getElementById('err_telefono');
        err.style.display = 'none';
        if (!tel) { err.textContent = 'Inserisci un numero'; err.style.display = 'block'; return; }
        try {
            const fd = new FormData();
            fd.append('telefono', tel);
            fd.append('_token', @json(csrf_token()));
            const r = await fetch('/firma/'+TOKEN+'/invia_otp', { method:'POST', body: fd, credentials:'same-origin' });
            const data = await r.json();
            if (!data.ok) { err.textContent = data.error || 'Errore'; err.style.display = 'block'; return; }
            document.getElementById('numero_inviato').textContent = tel;
            document.getElementById('step_telefono').style.display = 'none';
            document.getElementById('step_otp').style.display = 'block';
        } catch (e) {
            err.textContent = 'Errore di rete: ' + e; err.style.display = 'block';
        }
    }
    async function verificaOtp() {
        const otp = document.getElementById('otp').value.trim();
        const nome = document.getElementById('nome').value.trim();
        const err = document.getElementById('err_otp');
        err.style.display = 'none';
        if (!otp || otp.length !== 6) { err.textContent = 'OTP deve essere 6 cifre'; err.style.display = 'block'; return; }
        if (!nome) { err.textContent = 'Inserisci nome e cognome'; err.style.display = 'block'; return; }
        try {
            const fd = new FormData();
            fd.append('otp', otp);
            fd.append('nome', nome);
            fd.append('_token', @json(csrf_token()));
            const r = await fetch('/firma/'+TOKEN+'/verifica_otp', { method:'POST', body: fd, credentials:'same-origin' });
            const data = await r.json();
            if (!data.ok) { err.textContent = data.error || 'Errore'; err.style.display = 'block'; return; }
            document.getElementById('step_otp').style.display = 'none';
            document.getElementById('step_done').style.display = 'block';
            setTimeout(()=> location.reload(), 1500);
        } catch (e) {
            err.textContent = 'Errore di rete: ' + e; err.style.display = 'block';
        }
    }
    function tornaTelefono() {
        document.getElementById('step_otp').style.display = 'none';
        document.getElementById('step_telefono').style.display = 'block';
    }

    async function inviaSegnalazione() {
        const testo = document.getElementById('seg_testo').value.trim();
        const contatto = document.getElementById('seg_contatto').value.trim();
        const err = document.getElementById('err_segnala');
        err.style.display = 'none';
        if (!testo) { err.textContent = 'Scrivi una descrizione del problema'; err.style.display = 'block'; return; }
        try {
            const fd = new FormData();
            fd.append('testo', testo);
            fd.append('contatto', contatto);
            fd.append('_token', @json(csrf_token()));
            const r = await fetch('/firma/'+TOKEN+'/segnala', { method:'POST', body: fd, credentials:'same-origin' });
            const data = await r.json();
            if (!data.ok) { err.textContent = data.error || 'Errore'; err.style.display = 'block'; return; }
            document.getElementById('segnala_form').style.display = 'none';
            document.getElementById('segnala_done').style.display = 'block';
        } catch (e) {
            err.textContent = 'Errore di rete: ' + e; err.style.display = 'block';
        }
    }
</script>

</body>
</html>
