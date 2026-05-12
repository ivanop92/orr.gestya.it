@include('utente.common.header')

@php
    $tariffa_default = (float) ($azienda->manut_tariffa_oraria_default ?? 33.75);
    // Pre-fetch righe di tutte le lavorazioni (per il modale Applica Lavorazione lato JS)
    $lavorazioni_payload = [];
    if (isset($lavorazioni_disponibili)) {
        foreach ($lavorazioni_disponibili as $lav) {
            $righe = DB::table('lavorazioni_righe')
                ->where('id_lavorazione', $lav->id)
                ->where('id_azienda', $utente->id_azienda)
                ->orderBy('ordinamento')
                ->get();
            $lavorazioni_payload[] = [
                'id'          => $lav->id,
                'codice'      => $lav->codice,
                'descrizione' => $lav->descrizione,
                'totale'      => $lav->totale,
                'righe'       => $righe,
            ];
        }
    }
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        Nuovo {{ $cd_do === 'PRE' ? 'Preventivo' : 'Ordinativo' }} Manutenzione
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/utente/riepilogo_documenti/{{ $cd_do }}">{{ $cd_do }}</a></li>
                            <li class="breadcrumb-item active">Nuovo</li>
                        </ol>
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

        <form method="post" autocomplete="off" id="form_preventivo_manutenzione">
            @csrf
            <input type="hidden" name="cd_do" value="{{ $cd_do }}">

            {{-- ============ TESTATA ============ --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-file-text-line me-2"></i>Dati Documento</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Numero <b style="color:red">*</b></label>
                            <input type="text" name="numero_doc" class="form-control" value="{{ $numero_doc ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data <b style="color:red">*</b></label>
                            <input type="date" name="data_doc" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cliente <b style="color:red">*</b></label>
                            <select name="id_cliente" class="form-select" id="id_cliente_sel" required>
                                <option value="">— Seleziona cliente —</option>
                                @foreach($clienti as $c)
                                    <option value="{{ $c->id }}"
                                        data-rs="{{ $c->ragione_sociale }}"
                                        data-piva="{{ $c->partita_iva ?? '' }}"
                                        data-cf="{{ $c->codice_fiscale ?? '' }}"
                                        data-ind="{{ $c->indirizzo ?? '' }}"
                                        data-cap="{{ $c->cap ?? '' }}"
                                        data-com="{{ $c->comune ?? '' }}"
                                        data-prov="{{ $c->provincia ?? '' }}"
                                        data-pec="{{ $c->pec ?? '' }}"
                                        data-sdi="{{ $c->codice_sdi ?? '' }}">
                                        {{ $c->ragione_sociale }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="ragione_sociale" id="rs_hidden">
                            <input type="hidden" name="partita_iva" id="piva_hidden">
                            <input type="hidden" name="cf" id="cf_hidden">
                            <input type="hidden" name="indirizzo" id="ind_hidden">
                            <input type="hidden" name="cap" id="cap_hidden">
                            <input type="hidden" name="comune" id="com_hidden">
                            <input type="hidden" name="provincia" id="prov_hidden">
                            <input type="hidden" name="pec" id="pec_hidden">
                            <input type="hidden" name="sdi" id="sdi_hidden">
                        </div>

                        @if(!empty($azienda->manut_anagrafica_vagoni_attiva) && isset($vagoni))
                            <div class="col-md-6">
                                <label class="form-label">Vagone</label>
                                <select name="id_vagone" class="form-select">
                                    <option value="">— Nessun vagone —</option>
                                    @foreach($vagoni as $v)
                                        <option value="{{ $v->id }}">{{ $v->codice }}@if($v->tipo) ({{ $v->tipo }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="col-md-6">
                                <label class="form-label">Automezzo / Vagone</label>
                                <input type="text" name="automezzo" class="form-control" placeholder="numero carro">
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Località intervento</label>
                            <input type="text" name="localita" class="form-control" placeholder="es. Marcianise">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Riferimento Ordine Cliente</label>
                            <input type="text" name="numero_ordine_rif" class="form-control" placeholder="N. ordine del cliente">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Motivo rientro</label>
                            <input type="text" name="reason_intake" class="form-control" placeholder="motivo del rientro in officina">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Note operatore</label>
                            <textarea name="note_operatore" class="form-control" rows="2" placeholder="note per il manutentore"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ RIGHE LAVORAZIONE ============ --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0"><i class="ri-list-check-2 me-2"></i>Righe Lavorazione</h5>
                            <small class="text-muted">Trascina ⠿ per riordinare. Click "Applica Lavorazione" per importare righe dal catalogo.</small>
                        </div>
                        <div class="flex-shrink-0 hstack gap-2">
                            <button type="button" class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal_applica_lav_form">
                                <i class="mdi mdi-package-variant me-1"></i> Applica Lavorazione
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="aggiungiRigaManutenzione()">
                                <i class="ri-add-line me-1"></i> Aggiungi Riga
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0" id="tabella_righe_manutenzione">
                            <thead class="table-light">
                            <tr>
                                <th style="width:32px;"></th>
                                <th style="width:70px;">Serv.</th>
                                <th style="width:100px;">Codice</th>
                                <th>Descrizione</th>
                                <th style="width:60px;">Att.</th>
                                <th style="width:90px;">Qta</th>
                                <th style="width:80px;">Min.</th>
                                <th style="width:100px;">P.U. €</th>
                                <th style="width:70px;">IVA%</th>
                                <th style="width:100px;">Materiale €</th>
                                <th style="width:110px;">Totale €</th>
                                <th style="width:50px;"></th>
                            </tr>
                            </thead>
                            <tbody id="righe_body">
                                {{-- popolata da JS --}}
                            </tbody>
                            <tfoot class="table-light">
                            <tr>
                                <th colspan="10" class="text-end">Imponibile</th>
                                <th class="text-end"><span id="agg_imponibile">0,00</span> €</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="10" class="text-end">Imposta</th>
                                <th class="text-end"><span id="agg_imposta">0,00</span> €</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="10" class="text-end fs-5">Totale</th>
                                <th class="text-end fs-5"><strong><span id="agg_totale">0,00</span> €</strong></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Hidden inputs aggregati per submit --}}
                    <input type="hidden" name="imponibile" id="imponibile_hidden" value="0">
                    <input type="hidden" name="imposta" id="imposta_hidden" value="0">
                    <input type="hidden" name="totale" id="totale_hidden" value="0">
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="/utente/riepilogo_documenti/{{ $cd_do }}" class="btn btn-light">Annulla</a>
                <button type="submit" name="aggiungi_dotes" value="1" class="btn btn-success">
                    <i class="ri-save-line me-1"></i> Salva {{ $cd_do === 'PRE' ? 'Preventivo' : 'Ordinativo' }}
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ============ Modal Applica Lavorazione (selezione righe singole o macro intera) ============ --}}
<div class="modal fade" id="modal_applica_lav_form" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success p-3">
                <h5 class="modal-title"><i class="mdi mdi-package-variant me-2"></i>Seleziona righe dal catalogo lavorazioni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom">
                    <input type="text" id="filtro_lav_modal" class="form-control form-control-sm" placeholder="🔍 Cerca per codice, servizio, descrizione (sia macro che righe)...">
                </div>
                <div style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-hover table-sm mb-0 align-middle">
                        <thead class="table-light" style="position:sticky; top:0; z-index:2;">
                        <tr>
                            <th style="width:36px;"></th>
                            <th style="width:70px;">Servizio</th>
                            <th style="width:100px;">Codice</th>
                            <th>Descrizione</th>
                            <th class="text-end" style="width:80px;">Qta</th>
                            <th class="text-end" style="width:70px;">Min</th>
                            <th class="text-end" style="width:90px;">P.U.</th>
                            <th class="text-end" style="width:100px;">Totale</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($lavorazioni_payload as $lav)
                                <tr class="table-secondary lav-macro-header" data-lav-id="{{ $lav['id'] }}" data-search="{{ strtolower($lav['codice'].' '.$lav['descrizione']) }}">
                                    <td><input type="checkbox" class="form-check-input lav-macro-checkall" data-lav-id="{{ $lav['id'] }}" title="Spunta tutte le righe di questa macro"></td>
                                    <td colspan="7">
                                        <strong>{{ $lav['codice'] }}</strong> — {{ $lav['descrizione'] }}
                                        <small class="text-muted">({{ count($lav['righe']) }} righe · totale macro € {{ number_format($lav['totale'],2,',','.') }})</small>
                                    </td>
                                </tr>
                                @foreach($lav['righe'] as $r)
                                    <tr class="lav-riga-row" data-lav-id="{{ $lav['id'] }}" data-search="{{ strtolower(($r->servizio?:'').' '.($r->codice?:'').' '.($r->descrizione?:'').' '.$lav['codice'].' '.$lav['descrizione']) }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input lav-riga-check"
                                                data-lav-id="{{ $lav['id'] }}"
                                                data-servizio="{{ $r->servizio }}"
                                                data-codice="{{ $r->codice }}"
                                                data-descrizione="{{ $r->descrizione }}"
                                                data-attivita="{{ $r->attivita }}"
                                                data-qta="{{ $r->qta }}"
                                                data-minuti="{{ $r->minuti }}"
                                                data-pu="{{ $r->pu }}"
                                                data-aliquota="{{ $r->aliquota }}"
                                                data-materiale="{{ $r->materiale }}"
                                                data-descrizione_materiale="{{ $r->descrizione_materiale }}"
                                                data-setup_tank="{{ $r->setup_tank }}">
                                        </td>
                                        <td>{{ $r->servizio }}</td>
                                        <td>{{ $r->codice }}</td>
                                        <td>{{ $r->descrizione }}</td>
                                        <td class="text-end">{{ rtrim(rtrim(number_format($r->qta,3,',','.'),'0'),',') ?: '0' }}</td>
                                        <td class="text-end">{{ rtrim(rtrim(number_format($r->minuti,2,',','.'),'0'),',') ?: '0' }}</td>
                                        <td class="text-end">€ {{ number_format($r->pu,2,',','.') }}</td>
                                        <td class="text-end">€ {{ number_format($r->pt,2,',','.') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted me-auto"><strong id="conteggio_selezione">0</strong> righe selezionate</span>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" onclick="applicaRigheDaModal()">
                    <i class="ri-add-circle-line me-1"></i> Aggiungi righe selezionate
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var LAVORAZIONI_PAYLOAD = @json($lavorazioni_payload);
    var TARIFFA_DEFAULT = {{ $tariffa_default }};
    var rowCounter = 0;

    function parseNum(v) {
        if (v === null || v === undefined || v === '') return 0;
        return parseFloat(String(v).replace(',', '.')) || 0;
    }
    function formatEur(n) {
        return n.toFixed(2).replace('.', ',');
    }
    function ricalcolaRiga(rowEl) {
        var qta = parseNum(rowEl.querySelector('[data-f="qta"]').value);
        var min = parseNum(rowEl.querySelector('[data-f="minuti"]').value);
        var pu  = parseNum(rowEl.querySelector('[data-f="pu"]').value);
        var att = parseNum(rowEl.querySelector('[data-f="attivita"]').value) || 1;
        var iva = parseNum(rowEl.querySelector('[data-f="aliquota"]').value);
        var pt;
        if (min > 0) {
            pt = Math.round(pu * min / 60 * 100) / 100;
        } else {
            pt = Math.round(pu * att * qta * 100) / 100;
        }
        var imposta = Math.round(pt * iva / 100 * 100) / 100;
        rowEl.querySelector('[data-f="pt"]').textContent = formatEur(pt);
        rowEl.dataset.imponibile = pt;
        rowEl.dataset.imposta = imposta;
    }
    function ricalcolaAggregati() {
        var imp = 0, ivat = 0;
        document.querySelectorAll('#righe_body > tr').forEach(function(tr) {
            imp += parseNum(tr.dataset.imponibile);
            ivat += parseNum(tr.dataset.imposta);
        });
        var tot = imp + ivat;
        document.getElementById('agg_imponibile').textContent = formatEur(imp);
        document.getElementById('agg_imposta').textContent = formatEur(ivat);
        document.getElementById('agg_totale').textContent = formatEur(tot);
        document.getElementById('imponibile_hidden').value = imp.toFixed(2);
        document.getElementById('imposta_hidden').value = ivat.toFixed(2);
        document.getElementById('totale_hidden').value = tot.toFixed(2);
    }

    function buildRiga(data) {
        rowCounter++;
        var i = rowCounter;
        data = data || {};
        var tr = document.createElement('tr');
        tr.dataset.rowIdx = i;
        tr.innerHTML =
            '<td class="text-center" style="cursor:grab;"><i class="ri-drag-move-2-line text-muted riga-mh-handle"></i></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="righe_lavorazione['+i+'][servizio]" data-f="servizio" maxlength="10" value="'+(data.servizio||'')+'"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="righe_lavorazione['+i+'][codice]" data-f="codice" value="'+(data.codice||'')+'"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="righe_lavorazione['+i+'][descrizione]" data-f="descrizione" value="'+((data.descrizione||'').replace(/"/g,'&quot;'))+'" placeholder="descrizione lavorazione"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][attivita]" data-f="attivita" step="0.01" min="0" value="'+(data.attivita!=null?data.attivita:1)+'"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][qta]" data-f="qta" step="0.001" min="0" value="'+(data.qta!=null?data.qta:0)+'"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][minuti]" data-f="minuti" step="0.01" min="0" value="'+(data.minuti!=null?data.minuti:0)+'"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][pu]" data-f="pu" step="0.01" min="0" value="'+(data.pu!=null?data.pu:TARIFFA_DEFAULT)+'"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][aliquota]" data-f="aliquota" min="0" max="100" value="'+(data.aliquota!=null?data.aliquota:22)+'"></td>' +
            '<td><input type="number" class="form-control form-control-sm text-end" name="righe_lavorazione['+i+'][materiale]" data-f="materiale" step="0.01" min="0" value="'+(data.materiale!=null?data.materiale:0)+'"></td>' +
            '<td class="text-end fw-medium"><span data-f="pt">0,00</span></td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-soft-danger" onclick="eliminaRigaManutenzione(this)" title="Rimuovi"><i class="ri-delete-bin-line"></i></button></td>';
        // hidden setup_tank (per ora false; UI aggiungibile in futuro)
        var hidSetup = document.createElement('input');
        hidSetup.type = 'hidden';
        hidSetup.name = 'righe_lavorazione['+i+'][setup_tank]';
        hidSetup.value = data.setup_tank ? '1' : '0';
        tr.appendChild(hidSetup);
        return tr;
    }
    function aggiungiRigaManutenzione(data) {
        var tbody = document.getElementById('righe_body');
        var tr = buildRiga(data);
        tbody.appendChild(tr);
        // Listener input -> ricalcolo
        tr.querySelectorAll('input[data-f]').forEach(function(inp) {
            inp.addEventListener('input', function() { ricalcolaRiga(tr); ricalcolaAggregati(); });
        });
        ricalcolaRiga(tr);
        ricalcolaAggregati();
        return tr;
    }
    function eliminaRigaManutenzione(btn) {
        var tr = btn.closest('tr');
        if (tr && confirm('Eliminare questa riga?')) {
            tr.parentNode.removeChild(tr);
            ricalcolaAggregati();
        }
    }
    function aggiornaContegnoSelezione() {
        var n = document.querySelectorAll('.lav-riga-check:checked').length;
        var el = document.getElementById('conteggio_selezione');
        if (el) el.textContent = n;
    }
    function applicaRigheDaModal() {
        var checked = document.querySelectorAll('.lav-riga-check:checked');
        if (checked.length === 0) {
            alert('Seleziona almeno una riga');
            return;
        }
        checked.forEach(function(cb) {
            aggiungiRigaManutenzione({
                servizio: cb.dataset.servizio || '',
                codice: cb.dataset.codice || '',
                descrizione: cb.dataset.descrizione || '',
                attivita: parseFloat(cb.dataset.attivita) || 1,
                qta: parseFloat(cb.dataset.qta) || 0,
                minuti: parseFloat(cb.dataset.minuti) || 0,
                pu: parseFloat(cb.dataset.pu) || 0,
                aliquota: parseInt(cb.dataset.aliquota, 10) || 22,
                materiale: parseFloat(cb.dataset.materiale) || 0,
                descrizione_materiale: cb.dataset.descrizione_materiale || '',
                setup_tank: cb.dataset.setup_tank === '1' ? 1 : 0,
            });
            cb.checked = false;
        });
        document.querySelectorAll('.lav-macro-checkall').forEach(function(cb){ cb.checked = false; });
        aggiornaContegnoSelezione();
        var modal = bootstrap.Modal.getInstance(document.getElementById('modal_applica_lav_form'));
        if (modal) modal.hide();
    }

    // Cliente -> popola hidden snapshot
    document.getElementById('id_cliente_sel').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        document.getElementById('rs_hidden').value   = opt.dataset.rs   || '';
        document.getElementById('piva_hidden').value = opt.dataset.piva || '';
        document.getElementById('cf_hidden').value   = opt.dataset.cf   || '';
        document.getElementById('ind_hidden').value  = opt.dataset.ind  || '';
        document.getElementById('cap_hidden').value  = opt.dataset.cap  || '';
        document.getElementById('com_hidden').value  = opt.dataset.com  || '';
        document.getElementById('prov_hidden').value = opt.dataset.prov || '';
        document.getElementById('pec_hidden').value  = opt.dataset.pec  || '';
        document.getElementById('sdi_hidden').value  = opt.dataset.sdi  || '';
    });

    // Filtro live nella modale (su righe E macro header)
    document.getElementById('filtro_lav_modal').addEventListener('input', function(e) {
        var q = e.target.value.toLowerCase().trim();
        var matchByMacro = {};
        document.querySelectorAll('.lav-riga-row').forEach(function(row) {
            var match = (q === '' || row.dataset.search.indexOf(q) !== -1);
            row.style.display = match ? '' : 'none';
            if (match) matchByMacro[row.dataset.lavId] = true;
        });
        document.querySelectorAll('.lav-macro-header').forEach(function(h) {
            var idLav = h.dataset.lavId;
            var macroMatchesText = (q === '' || h.dataset.search.indexOf(q) !== -1);
            h.style.display = (matchByMacro[idLav] || macroMatchesText) ? '' : 'none';
        });
    });

    // "Spunta tutte le righe di questa macro"
    document.querySelectorAll('.lav-macro-checkall').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var idLav = cb.dataset.lavId;
            document.querySelectorAll('.lav-riga-check[data-lav-id="'+idLav+'"]').forEach(function(rcb) {
                // applica solo alle righe visibili (per rispettare il filtro)
                if (rcb.closest('tr').style.display !== 'none') {
                    rcb.checked = cb.checked;
                }
            });
            aggiornaContegnoSelezione();
        });
    });

    // Aggiorna conteggio al cambio singola riga
    document.querySelectorAll('.lav-riga-check').forEach(function(cb) {
        cb.addEventListener('change', aggiornaContegnoSelezione);
    });

    // Add: parte con 1 riga vuota
    document.addEventListener('DOMContentLoaded', function() {
        aggiungiRigaManutenzione();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('righe_body');
        if (el && typeof Sortable !== 'undefined') {
            Sortable.create(el, { handle: '.riga-mh-handle', animation: 150 });
        }
    });
</script>

@include('utente.common.footer')
