<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f766e">
    <title>Intervento #{{ $intervento->id }} · Manutenzione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body { background: #f6f7fa; }
        .navbar-manut { background: #0f766e; color: #fff; padding: 12px 16px; position: sticky; top: 0; z-index: 100; }
        .card-info { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 12px; }
        .card-info .head { padding: 12px 16px; border-bottom: 1px solid #eef0f4; font-weight: 600; }
        .card-info .body { padding: 12px 16px; }
        .info-row { padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 0.95rem; }
        .info-row:last-child { border: none; }
        .info-row .label { color: #6b7280; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-row .val { font-weight: 500; }
        .step-info { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; padding: 12px 14px; border-radius: 10px; font-size: 0.9rem; }
        .step-info-done { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
        textarea { font-size: 16px; }
        .btn-sticky {
            position: sticky; bottom: 0; background: #f6f7fa; padding: 12px 0; margin: 0 -12px -12px;
            padding-left: 12px; padding-right: 12px;
        }
    </style>
</head>
<body>

<div class="navbar-manut">
    <div class="d-flex align-items-center">
        <a href="/manutentore/dashboard" class="text-white text-decoration-none me-3"><i class="ri-arrow-left-line" style="font-size: 1.4rem;"></i></a>
        <div class="flex-grow-1">
            <div class="fw-semibold">Intervento #{{ $intervento->id }}</div>
            <div class="small" style="opacity: 0.85;">{{ $intervento->cliente_ragione_sociale ?: '—' }}</div>
        </div>
        <span class="badge bg-{{ $intervento->priorita === 'alta' ? 'danger' : ($intervento->priorita === 'bassa' ? 'secondary' : 'warning') }}">
            {{ strtoupper($intervento->priorita) }}
        </span>
    </div>
</div>

<div class="container py-3" style="max-width: 720px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small">
            <i class="ri-check-line me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show small">
            <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Dati intervento --}}
    <div class="card-info">
        <div class="head"><i class="ri-train-line me-1"></i>Dati intervento</div>
        <div class="body">
            <div class="info-row">
                <div class="label">Cliente</div>
                <div class="val">{{ $intervento->cliente_ragione_sociale ?: '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Vagone</div>
                <div class="val">{{ $intervento->vagone_codice ?: $intervento->automezzo ?: '—' }}@if($intervento->vagone_tipo) <small class="text-muted">({{ $intervento->vagone_tipo }})</small>@endif</div>
            </div>
            @if($intervento->data_apertura)
                <div class="info-row">
                    <div class="label">Apertura</div>
                    <div class="val">{{ \Carbon\Carbon::parse($intervento->data_apertura)->format('d/m/Y') }}</div>
                </div>
            @endif
            @if($intervento->localita)
                <div class="info-row">
                    <div class="label">Località</div>
                    <div class="val"><i class="ri-map-pin-line text-muted"></i> {{ $intervento->localita }}</div>
                </div>
            @endif
            @if($intervento->reason_intake)
                <div class="info-row">
                    <div class="label">Motivo rientro (sintomo)</div>
                    <div class="val">{{ $intervento->reason_intake }}</div>
                </div>
            @endif
            @if($intervento->note)
                <div class="info-row">
                    <div class="label">Note ufficio</div>
                    <div class="val">{{ $intervento->note }}</div>
                </div>
            @endif
            @if(!empty($intervento->ordinativo_file))
                <div class="info-row">
                    <div class="label">Ordinativo cliente</div>
                    <div class="val">
                        <a href="/manutentore/intervento/{{ $intervento->id }}/ordinativo" target="_blank" class="btn btn-sm btn-primary">
                            <i class="ri-file-pdf-line me-1"></i> Apri ordinativo
                        </a>
                        @if(!empty($intervento->ordinativo_filename_originale))
                            <div class="text-muted small mt-1">{{ $intervento->ordinativo_filename_originale }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Step info --}}
    @if($intervento->step_corrente == 3)
        <div class="step-info mb-3">
            <strong><i class="ri-pencil-line me-1"></i>Tocca a te</strong><br>
            Valuta il vagone, scrivi il <strong>report danni</strong>, allega foto e indica i materiali utilizzati. Quando confermi, l'intervento passa all'ufficio.
        </div>

        <form method="post" action="/manutentore/intervento/{{ $intervento->id }}/invia_report" enctype="multipart/form-data">
            @csrf
            <div class="card-info">
                <div class="head"><i class="ri-tools-line me-1"></i>Report danni</div>
                <div class="body">
                    <textarea name="report_danni" class="form-control" rows="6" required placeholder="Descrivi i danni rilevati, le parti da sostituire, le lavorazioni necessarie...">{{ $intervento->report_danni }}</textarea>
                </div>
            </div>

            <div class="card-info">
                <div class="head"><i class="ri-camera-line me-1"></i>Foto / Allegati</div>
                <div class="body">
                    <input type="file" name="allegati[]" class="form-control" multiple accept="image/*,application/pdf" capture="environment">
                    <small class="text-muted">Tocca per scattare foto o selezionare file dal dispositivo. Puoi caricare più foto in una volta.</small>
                </div>
            </div>

            <div class="card-info">
                <div class="head d-flex align-items-center flex-wrap gap-2">
                    <span class="flex-grow-1"><i class="ri-box-3-line me-1"></i>Materiali utilizzati</span>
                    <button type="button" class="btn btn-sm btn-soft-success" data-bs-toggle="modal" data-bs-target="#modal_cerca_articolo"><i class="ri-search-line"></i> Da magazzino</button>
                    <button type="button" class="btn btn-sm btn-soft-primary" onclick="aggiungiMateriale()"><i class="ri-add-line"></i> Manuale</button>
                </div>
                <div class="body p-0">
                    <div id="materiali_list" class="px-2 pb-2">
                        {{-- Righe dinamiche --}}
                    </div>
                    <p class="text-muted small px-3 pb-2 mb-0">"<strong>Da magazzino</strong>" scarica davvero la giacenza al submit. "<strong>Manuale</strong>" è solo una nota (nessun movimento magazzino).</p>
                </div>
            </div>

            <div class="card-info">
                <div class="head d-flex align-items-center flex-wrap gap-2">
                    <span class="flex-grow-1"><i class="ri-list-check-2 me-1"></i>Lavorazioni proposte (per preventivo)</span>
                    <button type="button" class="btn btn-sm btn-soft-success" data-bs-toggle="modal" data-bs-target="#modal_cerca_lav"><i class="ri-search-line"></i> Catalogo</button>
                    <button type="button" class="btn btn-sm btn-soft-primary" onclick="aggiungiLavorazione()"><i class="ri-add-line"></i> Manuale</button>
                </div>
                <div class="body p-0">
                    <div id="lav_proposte_list" class="px-2 pb-2">
                        {{-- Righe dinamiche --}}
                    </div>
                    <p class="text-muted small px-3 pb-2 mb-0">Righe di lavorazione che proponi per il preventivo. L'ufficio le riceverà già pronte allo step 4.</p>
                </div>
            </div>

            <div class="btn-sticky">
                <button type="submit" class="btn btn-success w-100 btn-lg" onclick="return confirm('Inviare il report all\'ufficio?');">
                    <i class="ri-send-plane-line me-1"></i> Invia Report all'Ufficio
                </button>
            </div>
        </form>

        <script>
            var materialeIdx = 0;
            function aggiungiMateriale(prefill) {
                materialeIdx++;
                var i = materialeIdx;
                prefill = prefill || {};
                var fromStock = !!prefill.id_articolo;
                var bgClass = fromStock ? 'bg-success-subtle' : 'bg-light';
                var badge = fromStock ? '<span class="badge bg-success ms-2"><i class="ri-store-2-line"></i> Magazzino</span>' : '';
                var html =
                    '<div class="border rounded p-2 mb-2 '+bgClass+' position-relative" id="mat_row_'+i+'">' +
                        '<button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="document.getElementById(\'mat_row_'+i+'\').remove()" title="Rimuovi"></button>' +
                        '<input type="hidden" name="materiali['+i+'][id_articolo]" value="'+(prefill.id_articolo||'')+'">' +
                        '<div class="row g-2">' +
                            '<div class="col-12">' +
                                '<label class="form-label mb-1 small">Descrizione *' + badge + '</label>' +
                                '<input type="text" name="materiali['+i+'][descrizione]" class="form-control form-control-sm" placeholder="es. Suole freno Tipo X" required value="'+(prefill.descrizione||'').replace(/"/g,'&quot;')+'">' +
                            '</div>' +
                            '<div class="col-4">' +
                                '<label class="form-label mb-1 small">Qta</label>' +
                                '<input type="number" step="0.001" min="0" name="materiali['+i+'][qta]" class="form-control form-control-sm" value="'+(prefill.qta||1)+'">' +
                            '</div>' +
                            '<div class="col-3">' +
                                '<label class="form-label mb-1 small">UM</label>' +
                                '<input type="text" name="materiali['+i+'][um]" class="form-control form-control-sm" value="'+(prefill.um||'PZ')+'">' +
                            '</div>' +
                            '<div class="col-5">' +
                                '<label class="form-label mb-1 small">Codice</label>' +
                                '<input type="text" name="materiali['+i+'][codice]" class="form-control form-control-sm" value="'+(prefill.codice||'').replace(/"/g,'&quot;')+'">' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                document.getElementById('materiali_list').insertAdjacentHTML('beforeend', html);
            }

            // Ricerca articoli a magazzino (AJAX server-side)
            var ricercaArtTimer = null;
            function ricercaArticoli() {
                clearTimeout(ricercaArtTimer);
                ricercaArtTimer = setTimeout(function() {
                    var q = document.getElementById('art_search_q').value.trim();
                    var resBox = document.getElementById('art_search_results');
                    if (q.length < 2) {
                        resBox.innerHTML = '<div class="text-muted text-center py-3 small">Digita almeno 2 caratteri…</div>';
                        return;
                    }
                    resBox.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div></div>';
                    fetch('/manutentore/ajax/cerca_articoli?q=' + encodeURIComponent(q), { credentials:'same-origin' })
                        .then(function(r){ return r.json(); })
                        .then(function(data) {
                            var arr = data.articoli || [];
                            if (arr.length === 0) {
                                resBox.innerHTML = '<div class="text-muted text-center py-3 small">Nessun articolo trovato.</div>';
                                return;
                            }
                            var html = '<div class="list-group list-group-flush">';
                            arr.forEach(function(a) {
                                var giac = parseFloat(a.giacenza || 0);
                                var giacBadge = giac > 0 ? '<span class="badge bg-success">'+giac.toString().replace('.',',')+' '+(a.um||'PZ')+'</span>' : '<span class="badge bg-danger">esaurito</span>';
                                var dataAttr = encodeURIComponent(JSON.stringify(a));
                                html += '<a href="#" class="list-group-item list-group-item-action py-2" onclick="aggiungiDaMagazzino(\''+dataAttr+'\'); return false;">' +
                                    '<div class="d-flex"><div class="flex-grow-1 small">' +
                                    '<strong>'+ (a.codice_articolo||'') + '</strong> — ' + (a.titolo || a.descrizione || '') +
                                    '@if(0)@endif' +
                                    '</div>' +
                                    '<div class="text-end">' + giacBadge + '</div>' +
                                    '</div></a>';
                            });
                            html += '</div>';
                            resBox.innerHTML = html;
                        })
                        .catch(function(){ resBox.innerHTML = '<div class="text-danger small text-center py-2">Errore ricerca</div>'; });
                }, 300);
            }
            function aggiungiDaMagazzino(encodedJson) {
                var a = JSON.parse(decodeURIComponent(encodedJson));
                aggiungiMateriale({
                    id_articolo: a.id,
                    codice: a.codice_articolo || '',
                    descrizione: a.titolo || a.descrizione || '',
                    qta: 1,
                    um: a.um || 'PZ',
                });
                var modal = bootstrap.Modal.getInstance(document.getElementById('modal_cerca_articolo'));
                if (modal) modal.hide();
            }

            // Lavorazioni proposte
            var lavIdx = 0;
            function _esc(s) { return s === null || s === undefined ? '' : String(s).replace(/"/g,'&quot;'); }
            function aggiungiLavorazione(p) {
                lavIdx++;
                var i = lavIdx;
                p = p || {};
                var html =
                    '<div class="border rounded p-2 mb-2 bg-light position-relative" id="lav_row_'+i+'">' +
                        '<button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="document.getElementById(\'lav_row_'+i+'\').remove()" title="Rimuovi"></button>' +
                        '<input type="hidden" name="lavorazioni_proposte['+i+'][id_lavorazione_origine]" value="'+_esc(p.id_lavorazione_origine||'')+'">' +
                        '<input type="hidden" name="lavorazioni_proposte['+i+'][id_lavorazione_riga_origine]" value="'+_esc(p.id_lavorazione_riga_origine||'')+'">' +
                        '<div class="row g-2">' +
                            '<div class="col-12">' +
                                '<label class="form-label mb-1 small">Descrizione *</label>' +
                                '<input type="text" name="lavorazioni_proposte['+i+'][descrizione]" class="form-control form-control-sm" placeholder="es. Sostituzione suole" value="'+_esc(p.descrizione)+'">' +
                            '</div>' +
                            '<div class="col-4">' +
                                '<label class="form-label mb-1 small">Servizio</label>' +
                                '<input type="text" name="lavorazioni_proposte['+i+'][servizio]" class="form-control form-control-sm" maxlength="10" value="'+_esc(p.servizio)+'">' +
                            '</div>' +
                            '<div class="col-8">' +
                                '<label class="form-label mb-1 small">Codice</label>' +
                                '<input type="text" name="lavorazioni_proposte['+i+'][codice]" class="form-control form-control-sm" value="'+_esc(p.codice)+'">' +
                            '</div>' +
                            '<div class="col-4">' +
                                '<label class="form-label mb-1 small">Qta</label>' +
                                '<input type="number" step="0.001" min="0" name="lavorazioni_proposte['+i+'][qta]" class="form-control form-control-sm" value="'+(p.qta!=null?p.qta:0)+'">' +
                            '</div>' +
                            '<div class="col-4">' +
                                '<label class="form-label mb-1 small">Minuti</label>' +
                                '<input type="number" step="0.01" min="0" name="lavorazioni_proposte['+i+'][minuti]" class="form-control form-control-sm" value="'+(p.minuti!=null?p.minuti:0)+'">' +
                            '</div>' +
                            '<div class="col-4">' +
                                '<label class="form-label mb-1 small">Att.</label>' +
                                '<input type="number" step="0.01" min="0" name="lavorazioni_proposte['+i+'][attivita]" class="form-control form-control-sm" value="'+(p.attivita!=null?p.attivita:1)+'">' +
                            '</div>' +
                            '<div class="col-6">' +
                                '<label class="form-label mb-1 small">P.U. €</label>' +
                                '<input type="number" step="0.01" min="0" name="lavorazioni_proposte['+i+'][pu]" class="form-control form-control-sm" value="'+(p.pu!=null?p.pu:0)+'">' +
                            '</div>' +
                            '<div class="col-6">' +
                                '<label class="form-label mb-1 small">IVA%</label>' +
                                '<input type="number" min="0" max="100" name="lavorazioni_proposte['+i+'][aliquota]" class="form-control form-control-sm" value="'+(p.aliquota!=null?p.aliquota:22)+'">' +
                            '</div>' +
                            '<div class="col-12">' +
                                '<label class="form-label mb-1 small">Materiale €</label>' +
                                '<input type="number" step="0.01" min="0" name="lavorazioni_proposte['+i+'][materiale]" class="form-control form-control-sm" value="'+(p.materiale!=null?p.materiale:0)+'">' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                document.getElementById('lav_proposte_list').insertAdjacentHTML('beforeend', html);
            }

            // Ricerca catalogo (AJAX server-side) - righe raggruppate per macro
            var ricercaTimer = null;
            var righeCache = []; // ultimi risultati per "aggiungi macro intera"
            function ricercaCatalogo() {
                clearTimeout(ricercaTimer);
                ricercaTimer = setTimeout(function() {
                    var q = document.getElementById('lav_search_q').value.trim();
                    var resBox = document.getElementById('lav_search_results');
                    if (q.length < 2) {
                        resBox.innerHTML = '<div class="text-muted text-center py-3 small">Digita almeno 2 caratteri…</div>';
                        return;
                    }
                    resBox.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div></div>';
                    fetch('/manutentore/ajax/cerca_righe_catalogo?q=' + encodeURIComponent(q), { credentials:'same-origin' })
                        .then(function(r){ return r.json(); })
                        .then(function(data) {
                            var righe = data.righe || [];
                            righeCache = righe;
                            if (righe.length === 0) {
                                resBox.innerHTML = '<div class="text-muted text-center py-3 small">Nessuna riga trovata.</div>';
                                return;
                            }
                            // Raggruppa per id_lavorazione preservando l'ordine
                            var gruppi = []; var seen = {};
                            righe.forEach(function(r) {
                                if (!seen[r.id_lavorazione]) {
                                    seen[r.id_lavorazione] = { id: r.id_lavorazione, codice: r.lav_codice, descrizione: r.lav_descrizione, righe: [] };
                                    gruppi.push(seen[r.id_lavorazione]);
                                }
                                seen[r.id_lavorazione].righe.push(r);
                            });
                            var html = '';
                            gruppi.forEach(function(g) {
                                html += '<div class="border rounded mb-2">';
                                // Header macro con bottone "aggiungi tutta"
                                html += '<div class="d-flex align-items-center p-2 bg-light border-bottom">' +
                                    '<div class="flex-grow-1"><strong>' + _esc(g.codice||'') + '</strong> — ' + _esc(g.descrizione||'') +
                                    ' <small class="text-muted">(' + g.righe.length + ' righe)</small></div>' +
                                    '<button type="button" class="btn btn-sm btn-success" onclick="aggiungiMacroIntera('+g.id+')"><i class="ri-add-line"></i> Tutta la macro</button>' +
                                    '</div>';
                                // Righe singole
                                html += '<div class="list-group list-group-flush">';
                                g.righe.forEach(function(r) {
                                    var pt = parseFloat(r.pt || 0).toFixed(2).replace('.', ',');
                                    html += '<a href="#" class="list-group-item list-group-item-action py-2" onclick="aggiungiSingolaDalCatalogo('+r.id+'); return false;">' +
                                        '<div class="d-flex"><div class="flex-grow-1 small">' +
                                        '<strong>'+ _esc(r.servizio||'') + '</strong> ' + _esc(r.codice||'') + ' — ' + _esc(r.descrizione||'') +
                                        '</div>' +
                                        '<div class="text-end"><span class="badge bg-soft-success text-success">€ '+pt+'</span></div>' +
                                        '</div></a>';
                                });
                                html += '</div></div>';
                            });
                            resBox.innerHTML = html;
                        })
                        .catch(function(){ resBox.innerHTML = '<div class="text-danger small text-center py-2">Errore ricerca</div>'; });
                }, 300);
            }
            function _addRigaDaObj(r) {
                aggiungiLavorazione({
                    servizio: r.servizio || '',
                    codice: r.codice || '',
                    descrizione: r.descrizione || '',
                    attivita: r.attivita || 1,
                    qta: r.qta || 0,
                    minuti: r.minuti || 0,
                    pu: r.pu || 0,
                    aliquota: r.aliquota || 22,
                    materiale: r.materiale || 0,
                    id_lavorazione_origine: r.id_lavorazione,
                    id_lavorazione_riga_origine: r.id,
                });
            }
            function aggiungiSingolaDalCatalogo(idRiga) {
                var r = righeCache.find(function(x){ return x.id === idRiga; });
                if (!r) return;
                _addRigaDaObj(r);
                var modal = bootstrap.Modal.getInstance(document.getElementById('modal_cerca_lav'));
                if (modal) modal.hide();
            }
            function aggiungiMacroIntera(idLav) {
                var righe = righeCache.filter(function(x){ return x.id_lavorazione === idLav; });
                righe.forEach(_addRigaDaObj);
                var modal = bootstrap.Modal.getInstance(document.getElementById('modal_cerca_lav'));
                if (modal) modal.hide();
            }
        </script>

        {{-- Modale ricerca catalogo lavorazioni --}}
        <div class="modal fade" id="modal_cerca_lav" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h6 class="modal-title"><i class="ri-search-line me-1"></i>Cerca riga lavorazione</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-2">
                        <input type="text" id="lav_search_q" class="form-control mb-2" placeholder="Digita codice, servizio, descrizione..." oninput="ricercaCatalogo()" autofocus>
                        <div id="lav_search_results" style="max-height: 60vh; overflow-y:auto;">
                            <div class="text-muted text-center py-3 small">Digita almeno 2 caratteri…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modale ricerca articoli a magazzino --}}
        <div class="modal fade" id="modal_cerca_articolo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h6 class="modal-title"><i class="ri-store-2-line me-1"></i>Cerca articolo a magazzino</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-2">
                        <input type="text" id="art_search_q" class="form-control mb-2" placeholder="Digita codice, descrizione o barcode..." oninput="ricercaArticoli()" autofocus>
                        <div id="art_search_results" style="max-height: 60vh; overflow-y:auto;">
                            <div class="text-muted text-center py-3 small">Digita almeno 2 caratteri…</div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Selezionando un articolo dal magazzino, al submit del report verrà creato un movimento di scarico nella giacenza.</p>
                    </div>
                </div>
            </div>
        </div>

    @elseif($intervento->step_corrente < 3)
        <div class="step-info mb-3">
            <strong><i class="ri-time-line me-1"></i>In attesa</strong><br>
            L'intervento è in fase di apertura/assegnazione presso l'ufficio. Non c'è nulla da fare ora.
        </div>

    @elseif($intervento->step_corrente > 3)
        <div class="step-info step-info-done mb-3">
            <strong><i class="ri-check-double-line me-1"></i>Report già inviato</strong><br>
            Il tuo report è in carico all'ufficio. Lo step corrente è {{ $intervento->step_corrente }}/6.
        </div>

        @if($intervento->report_danni)
            <div class="card-info">
                <div class="head"><i class="ri-tools-line me-1"></i>Il tuo report</div>
                <div class="body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $intervento->report_danni }}</p>
                </div>
            </div>
        @endif
    @endif

    {{-- Allegati gia' caricati (visibili in tutti gli step >=3) --}}
    @if(isset($allegati) && count($allegati) > 0)
        <div class="card-info">
            <div class="head"><i class="ri-attachment-line me-1"></i>Allegati ({{ count($allegati) }})</div>
            <div class="body">
                <div class="row g-2">
                    @foreach($allegati as $a)
                        <div class="col-4">
                            @if(strpos($a->mime ?? '', 'image/') === 0)
                                <a href="/{{ $a->filename }}" target="_blank">
                                    <img src="/{{ $a->filename }}" alt="" class="img-fluid rounded" style="aspect-ratio:1; object-fit:cover;">
                                </a>
                            @else
                                <a href="/{{ $a->filename }}" target="_blank" class="d-block text-center p-3 border rounded text-decoration-none">
                                    <i class="ri-file-text-line" style="font-size:2rem;"></i>
                                    <div class="small text-truncate">{{ $a->original_name }}</div>
                                </a>
                            @endif
                            @if($intervento->step_corrente == 3)
                                <form method="post" action="/manutentore/allegato/{{ $a->id }}/elimina" class="mt-1 d-grid">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminare questo allegato?');">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Lavorazioni proposte gia' dichiarate --}}
    @if(isset($proposte) && count($proposte) > 0)
        <div class="card-info">
            <div class="head"><i class="ri-list-check-2 me-1"></i>Lavorazioni proposte ({{ count($proposte) }})</div>
            <div class="body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($proposte as $p)
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    @if($p->servizio)<span class="badge bg-soft-info text-info me-1">{{ $p->servizio }}</span>@endif
                                    @if($p->codice)<small class="text-muted">[{{ $p->codice }}]</small>@endif
                                    <strong>{{ $p->descrizione }}</strong>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">€ {{ number_format($p->pt,2,',','.') }}</span>
                                </div>
                            </div>
                            <div class="small text-muted mt-1">
                                Qta {{ rtrim(rtrim(number_format($p->qta,3,',','.'),'0'),',') }}
                                @if($p->minuti > 0) · {{ rtrim(rtrim(number_format($p->minuti,2,',',''),'0'),',') }} min @endif
                                @if($p->attivita != 1) · att {{ rtrim(rtrim(number_format($p->attivita,2,',',''),'0'),',') }} @endif
                                · P.U. € {{ number_format($p->pu,2,',','.') }}
                                @if($p->materiale > 0) · Materiale € {{ number_format($p->materiale,2,',','.') }} @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Materiali gia' dichiarati --}}
    @if(isset($materiali) && count($materiali) > 0)
        <div class="card-info">
            <div class="head"><i class="ri-box-3-line me-1"></i>Materiali utilizzati ({{ count($materiali) }})</div>
            <div class="body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($materiali as $m)
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <strong>{{ $m->descrizione }}</strong>
                                    @if($m->codice) <small class="text-muted">[{{ $m->codice }}]</small>@endif
                                    @if($m->id_mgmov)
                                        <span class="badge bg-success ms-1" title="Scaricato dal magazzino"><i class="ri-store-2-line"></i> Magazzino</span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-soft-primary text-primary">{{ rtrim(rtrim(number_format($m->qta,3,',','.'),'0'),',') }} {{ $m->um }}</span>
                                </div>
                            </div>
                            @if($m->note)<div class="text-muted small mt-1">{{ $m->note }}</div>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
