@php
    $lavorazioni_disponibili = DB::table('lavorazioni')
        ->where('id_azienda', $utente->id_azienda)
        ->where('attivo', 1)
        ->orderBy('descrizione')
        ->get();

    $preventivi_esistenti = collect();
    if (isset($dotes)) {
        $preventivi_esistenti = DB::table('dotes as d')
            ->leftJoin('clienti as c', 'c.id', '=', 'd.id_cliente')
            ->where('d.cd_do', 'PRE')
            ->where('d.id_azienda', $utente->id_azienda)
            ->where('d.id', '!=', $dotes->id)
            ->select('d.id', 'd.numero', 'd.data_doc', 'd.data', 'd.totale', DB::raw("COALESCE(c.ragione_sociale, '') AS cliente"))
            ->orderByDesc('d.id')
            ->limit(200)
            ->get();
    }
@endphp

@if(isset($dotes) && (count($lavorazioni_disponibili) > 0 || count($preventivi_esistenti) > 0))
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="flex-grow-1">
                    <i class="mdi mdi-package-variant me-2 text-muted"></i>
                    <span class="text-muted">Applica righe da template di lavorazione o da preventivi esistenti</span>
                </div>
                <button class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_applica_lavorazioni">
                    <i class="ri-add-circle-line me-1"></i> Applica Lavorazioni
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_applica_lavorazioni" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-primary p-3">
                    <h5 class="modal-title"><i class="mdi mdi-package-variant me-2"></i>Applica Lavorazioni al Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="/utente/applica_lavorazioni_a_documento/{{ $dotes->id }}">
                    @csrf
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_catalogo">Catalogo Lavorazioni <span class="badge bg-secondary ms-1">{{ count($lavorazioni_disponibili) }}</span></a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_preventivi">Da Preventivi Esistenti <span class="badge bg-secondary ms-1">{{ count($preventivi_esistenti) }}</span></a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab_catalogo">
                                <p class="text-muted mb-3 small">
                                    Seleziona <strong>una macro lavorazione intera</strong> (checkbox a sinistra) oppure <strong>espandi</strong> e scegli singole righe.
                                </p>
                                <div class="accordion" id="accordion_lavorazioni" style="max-height: 420px; overflow-y: auto;">
                                    @foreach($lavorazioni_disponibili as $lav)
                                        @php $hid = 'lav_'.$lav->id; @endphp
                                        <div class="accordion-item border mb-2">
                                            <div class="d-flex align-items-center px-2 py-2 bg-light">
                                                <input type="checkbox" name="id_lavorazioni[]" value="{{ $lav->id }}" class="form-check-input me-2" title="Applica TUTTA la lavorazione">
                                                <button class="accordion-button collapsed bg-light shadow-none p-2 flex-grow-1" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse_{{ $hid }}"
                                                        onclick="caricaRigheLavorazione({{ $lav->id }})">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $lav->codice }}</strong>
                                                        <span class="text-muted">— {{ $lav->descrizione }}</span>
                                                    </div>
                                                    <span class="badge bg-primary me-3">€ {{ number_format($lav->totale,2,',','.') }}</span>
                                                </button>
                                            </div>
                                            <div id="collapse_{{ $hid }}" class="accordion-collapse collapse" data-bs-parent="#accordion_lavorazioni">
                                                <div class="accordion-body p-2">
                                                    <div id="righe_{{ $hid }}" class="text-muted small">
                                                        <i class="mdi mdi-loading mdi-spin me-1"></i> Caricamento righe...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab_preventivi">
                                <p class="text-muted mb-3 small">
                                    Seleziona <strong>un preventivo intero</strong> per copiare tutte le sue righe, oppure espandi e scegli singole righe. Se cancelli un preventivo, sparisce da questa lista.
                                </p>
                                <div class="accordion" id="accordion_preventivi" style="max-height: 420px; overflow-y: auto;">
                                    @forelse($preventivi_esistenti as $p)
                                        @php $pid = 'pre_'.$p->id; @endphp
                                        <div class="accordion-item border mb-2">
                                            <div class="d-flex align-items-center px-2 py-2 bg-light">
                                                <input type="checkbox" name="id_dotes_origine[]" value="{{ $p->id }}" class="form-check-input me-2" title="Copia TUTTE le righe">
                                                <button class="accordion-button collapsed bg-light shadow-none p-2 flex-grow-1" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse_{{ $pid }}"
                                                        onclick="caricaRigheDotes({{ $p->id }})">
                                                    <div class="flex-grow-1">
                                                        <strong>Preventivo n. {{ $p->numero ?? $p->id }}</strong>
                                                        @if(!empty($p->data_doc) || !empty($p->data))
                                                            <span class="text-muted ms-2">{{ \Carbon\Carbon::parse($p->data_doc ?? $p->data)->format('d/m/Y') }}</span>
                                                        @endif
                                                        @if(!empty($p->cliente))
                                                            <span class="text-muted ms-2">— {{ $p->cliente }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="badge bg-primary me-3">€ {{ number_format($p->totale ?? 0,2,',','.') }}</span>
                                                </button>
                                            </div>
                                            <div id="collapse_{{ $pid }}" class="accordion-collapse collapse" data-bs-parent="#accordion_preventivi">
                                                <div class="accordion-body p-2">
                                                    <div id="righe_{{ $pid }}" class="text-muted small">
                                                        <i class="mdi mdi-loading mdi-spin me-1"></i> Caricamento righe...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="mdi mdi-information-outline me-1"></i> Nessun preventivo esistente disponibile
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-add-circle-line me-1"></i> Applica al Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window._righeLavCache  = window._righeLavCache || {};
        window._righeDotesCache = window._righeDotesCache || {};

        function caricaRigheLavorazione(idLav){
            if (window._righeLavCache[idLav]) return;
            window._righeLavCache[idLav] = true;
            var box = document.getElementById('righe_lav_'+idLav);
            fetch('/utente/ajax/lavorazione_righe/'+idLav, {headers: {'Accept':'application/json'}})
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (!d.righe || d.righe.length === 0){
                        box.innerHTML = '<em class="text-muted">Nessuna riga</em>';
                        return;
                    }
                    var html = '<table class="table table-sm table-hover mb-0"><thead class="table-light"><tr>';
                    html += '<th style="width:30px;"></th><th>Servizio</th><th>Codice</th><th>Descrizione</th><th class="text-end">P.U.</th><th class="text-end">Qta/Min</th><th class="text-end">Totale</th></tr></thead><tbody>';
                    d.righe.forEach(function(r){
                        var qta = (parseFloat(r.minuti) > 0) ? (r.minuti + ' min') : (r.qta + ' pz');
                        var tot = (parseFloat(r.imponibile) + parseFloat(r.imposta)).toFixed(2);
                        var srv = r.servizio ? '<span class="badge bg-soft-info text-info">'+r.servizio+'</span>' : '';
                        html += '<tr>';
                        html += '<td><input type="checkbox" name="id_lavorazioni_righe[]" value="'+r.id+'" class="form-check-input"></td>';
                        html += '<td>'+srv+'</td>';
                        html += '<td><code>'+(r.codice||'-')+'</code></td>';
                        html += '<td>'+(r.descrizione||'')+'</td>';
                        html += '<td class="text-end">€ '+parseFloat(r.pu||0).toFixed(2)+'</td>';
                        html += '<td class="text-end">'+qta+'</td>';
                        html += '<td class="text-end">€ '+tot+'</td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table>';
                    box.innerHTML = html;
                })
                .catch(function(){
                    window._righeLavCache[idLav] = false;
                    box.innerHTML = '<span class="text-danger">Errore di caricamento</span>';
                });
        }

        function caricaRigheDotes(idDotes){
            if (window._righeDotesCache[idDotes]) return;
            window._righeDotesCache[idDotes] = true;
            var box = document.getElementById('righe_pre_'+idDotes);
            fetch('/utente/ajax/dotes_righe/'+idDotes, {headers: {'Accept':'application/json'}})
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (!d.righe || d.righe.length === 0){
                        box.innerHTML = '<em class="text-muted">Nessuna riga in questo preventivo</em>';
                        return;
                    }
                    var html = '<table class="table table-sm table-hover mb-0"><thead class="table-light"><tr>';
                    html += '<th style="width:30px;"></th><th>Servizio</th><th>Codice</th><th>Descrizione</th><th class="text-end">P.U.</th><th class="text-end">Qta</th><th class="text-end">Totale</th></tr></thead><tbody>';
                    d.righe.forEach(function(r){
                        var qta = (parseFloat(r.minuti) > 0) ? (r.minuti + ' min') : ((r.qta||0) + ' ' + (r.um||''));
                        var tot = parseFloat(r.totale||0).toFixed(2);
                        var srv = r.servizio ? '<span class="badge bg-soft-info text-info">'+r.servizio+'</span>' : '';
                        html += '<tr>';
                        html += '<td><input type="checkbox" name="id_dorig_origine[]" value="'+r.id+'" class="form-check-input"></td>';
                        html += '<td>'+srv+'</td>';
                        html += '<td><code>'+(r.cd_ar||'-')+'</code></td>';
                        html += '<td>'+(r.descrizione||'')+'</td>';
                        html += '<td class="text-end">€ '+parseFloat(r.prezzo_unitario||0).toFixed(2)+'</td>';
                        html += '<td class="text-end">'+qta+'</td>';
                        html += '<td class="text-end">€ '+tot+'</td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table>';
                    box.innerHTML = html;
                })
                .catch(function(){
                    window._righeDotesCache[idDotes] = false;
                    box.innerHTML = '<span class="text-danger">Errore di caricamento</span>';
                });
        }
    </script>
@endif
