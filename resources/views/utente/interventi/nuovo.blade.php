@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0"><i class="ri-add-line me-2"></i>Nuovo Intervento Manutenzione</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/utente/interventi">Interventi</a></li>
                            <li class="breadcrumb-item active">Nuovo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-10">

                <div class="alert alert-info">
                    <i class="ri-information-line me-1"></i>
                    <strong>Step 1 — Ufficio:</strong> apri il ticket di intervento con cliente, vagone e motivo del rientro. Le righe di lavoro verranno aggiunte negli step successivi.
                </div>

                <form method="post" autocomplete="off" enctype="multipart/form-data">
                    @csrf

                    <div class="card border-primary mb-3">
                        <div class="card-header bg-soft-primary">
                            <h5 class="card-title mb-0"><i class="ri-file-upload-line me-1"></i> Ordinativo Cliente (allegato)</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                Carica il PDF/email dell'ordinativo ricevuto dal cliente. I dati di dettaglio (CUU, attività richieste, ecc.) sono già nel documento e il manutentore li leggerà da lì — qui sotto basta indicare i campi minimi per identificare l'intervento.
                            </p>
                            <input type="file" name="ordinativo_file" id="ordinativo_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.eml,.msg,.doc,.docx" onchange="parseOrdinativo(this)">
                            <div id="ordinativo_parse_status" class="small mt-2"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Dati Intervento <small class="text-muted">(solo i campi minimi)</small></h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente <b style="color:red">*</b></label>
                                    <select name="id_cliente" class="form-select" required>
                                        <option value="">— Seleziona cliente —</option>
                                        @foreach($clienti as $c)
                                            <option value="{{ $c->id }}">{{ $c->ragione_sociale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Data apertura</label>
                                    <input type="date" name="data_apertura" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Priorità</label>
                                    <select name="priorita" class="form-select">
                                        <option value="bassa">Bassa</option>
                                        <option value="media" selected>Media</option>
                                        <option value="alta">Alta</option>
                                    </select>
                                </div>

                                @if(count($vagoni) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label">Vagone</label>
                                        <select name="id_vagone" class="form-select">
                                            <option value="">— Nessun vagone in anagrafica —</option>
                                            @foreach($vagoni as $v)
                                                <option value="{{ $v->id }}">{{ $v->codice }}@if($v->tipo) ({{ $v->tipo }})@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">Automezzo (testo libero, se non in anagrafica)</label>
                                    <input type="text" name="automezzo" class="form-control" placeholder="numero carro o identificativo">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Impianto</label>
                                    <input type="text" name="impianto" class="form-control" placeholder="es. NOLA, BARI, SAN VITALIANO" list="impianti_list">
                                    <datalist id="impianti_list">
                                        <option value="NOLA">
                                        <option value="BARI">
                                        <option value="SAN VITALIANO">
                                        <option value="MARCIANISE">
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Località intervento</label>
                                    <input type="text" name="localita" class="form-control" placeholder="città/indirizzo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">PdM Riferimento</label>
                                    <input type="text" name="pdm_riferimento" class="form-control" value="VPI" placeholder="es. VPI">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Codice avaria CUU</label>
                                    <input type="text" name="codice_cuu" class="form-control" placeholder="es. 1.3.1.2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">N. Ordine Cliente</label>
                                    <input type="text" name="numero_ordine_cliente" class="form-control" placeholder="es. 1861750">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">N. OdL ORR</label>
                                    <input type="text" name="odl_numero" class="form-control" placeholder="progressivo">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Motivo del rientro (sintomo)</label>
                                    <input type="text" name="reason_intake" class="form-control" placeholder="es. Avaria sale, perdita olio, rumore freno...">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="note aggiuntive"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <a href="/utente/interventi" class="btn btn-light">Annulla</a>
                        <button type="submit" name="salva" value="1" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Apri Intervento
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')

<script>
    function parseOrdinativo(input){
        var status = document.getElementById('ordinativo_parse_status');
        if (!input.files || input.files.length === 0) { status.innerHTML = ''; return; }
        var f = input.files[0];
        if (!/\.pdf$/i.test(f.name)) {
            status.innerHTML = '<i class="ri-information-line text-muted me-1"></i> Estrazione automatica supportata solo per PDF testuali. Compila i campi manualmente.';
            return;
        }
        status.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1 text-primary"></i> Analisi PDF in corso...';
        var fd = new FormData();
        fd.append('ordinativo_file', f);
        fd.append('_token', document.querySelector('input[name=_token]').value);
        fetch('/utente/interventi/ajax/parse_ordinativo', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (!d.ok || !d.parsed) {
                    status.innerHTML = '<i class="ri-information-line text-muted me-1"></i> ' + (d.note || 'Estrazione non disponibile per questo file');
                    return;
                }
                var p = d.parsed, riempiti = [];
                function setIfEmpty(name, value, label){
                    if (!value) return;
                    var el = document.querySelector('[name="'+name+'"]');
                    if (!el) return;
                    if (el.tagName === 'SELECT') {
                        var opt = Array.from(el.options).find(function(o){ return o.value.toUpperCase() === String(value).toUpperCase() || o.text.toUpperCase() === String(value).toUpperCase(); });
                        if (opt && !el.value) { el.value = opt.value; riempiti.push(label); }
                    } else if (!el.value || (name === 'pdm_riferimento' && el.value === 'VPI')) {
                        el.value = value;
                        riempiti.push(label);
                    }
                }
                setIfEmpty('numero_ordine_cliente', p.numero_ordine_cliente, 'N. ordine');
                setIfEmpty('codice_cuu',            p.codice_cuu,            'CUU');
                setIfEmpty('impianto',              p.impianto,              'Impianto');
                setIfEmpty('odl_numero',            p.odl_numero,            'OdL');
                setIfEmpty('pdm_riferimento',       p.pdm_riferimento,       'PdM');
                setIfEmpty('data_apertura',         p.data_apertura,         'Data');
                setIfEmpty('reason_intake',         p.reason_intake,         'Motivo');
                setIfEmpty('automezzo',             p.automezzo,             'Automezzo');
                if (riempiti.length > 0) {
                    status.innerHTML = '<i class="ri-check-line text-success me-1"></i> Auto-compilati: <strong>' + riempiti.join(', ') + '</strong>. Verifica e correggi se necessario.';
                } else {
                    status.innerHTML = '<i class="ri-information-line text-muted me-1"></i> Nessun campo riconosciuto automaticamente. Compila manualmente i dati minimi.';
                }
            })
            .catch(function(){
                status.innerHTML = '<i class="ri-error-warning-line text-danger me-1"></i> Errore durante l\'analisi del PDF';
            });
    }
</script>

