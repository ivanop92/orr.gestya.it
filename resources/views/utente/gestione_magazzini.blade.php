@include('utente.common.header')


<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        Gestione Magazzini
                        <a href="#" tabindex="0" class="text-info ms-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="Come funziona" data-bs-content="Ogni azienda puo' avere piu' magazzini, organizzati per tipologia.<br><br><b>Tipologia</b> dice al sistema a cosa serve quel magazzino.<br><b>Default</b> e' quello scelto automaticamente quando il sistema deve fare un movimento di quella tipologia (es. caricare un prodotto finito a chiusura ODL).<br><br>Puoi tenerne piu' d'uno per tipologia (es. 'Prodotti Finiti Capannone A' + 'Prodotti Finiti Capannone B'), ma solo uno puo' essere il default."><i class="ri-information-line"></i></a>
                    </h4>

                    <div class="page-title-right">
                        <form method="post" style="display:inline-block;" onsubmit="return confirm('Inserisce i magazzini standard mancanti. Continuare?');">
                            @csrf
                            <button type="submit" name="ripristina_standard" value="1" class="btn btn-warning">
                                <i class="ri-refresh-line me-1 align-bottom"></i>Ripristina magazzini standard
                            </button>
                        </form>
                        <button class="btn btn-info add-btn" onclick="aggiungi();">
                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                        </button>
                    </div>

                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">Magazzini configurati</h5>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th>
                                        Tipologia
                                        <a href="#" tabindex="0" class="text-info" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="Tipologia" data-bs-content="Categoria funzionale del magazzino:<br><br><b>Prodotti Finiti</b>: dove vengono caricati gli articoli a chiusura ODL.<br><b>Materie Prime</b>: dove vengono scaricati i materiali consumati dalla produzione.<br><b>Articoli Commerciali</b>: per articoli rivenduti senza lavorazione.<br><b>Altro</b>: magazzino di servizio (resi, scarti, conto deposito), non gestito automaticamente."><i class="ri-information-line"></i></a>
                                    </th>
                                    <th>
                                        Default
                                        <a href="#" tabindex="0" class="text-info" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-title="Default" data-bs-content="Il magazzino marcato come <b>default</b> per la sua tipologia e' quello che il sistema usa automaticamente quando deve scegliere (es. carico ODL).<br><br>Solo uno per tipologia puo' essere default. Se ne segni un altro, quello precedente perde automaticamente il default."><i class="ri-information-line"></i></a>
                                    </th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $current_tipologia = null; @endphp
                                @foreach($magazzini as $magazzino)
                                    @php $meta = $tipologie_meta[$magazzino->tipologia] ?? ['label' => ucfirst($magazzino->tipologia)]; @endphp
                                    <tr>
                                        <td><code>{{ $magazzino->codice_magazzino }}</code></td>
                                        <td>{{ $magazzino->descrizione }}</td>
                                        <td>
                                            <span class="badge bg-{{ $magazzino->tipologia === 'prodotti_finiti' ? 'success' : ($magazzino->tipologia === 'materie_prime' ? 'primary' : ($magazzino->tipologia === 'commerciali' ? 'info' : 'secondary')) }}">
                                                {{ $meta['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($magazzino->is_default)
                                                <span class="badge bg-success"><i class="ri-check-line"></i> Default</span>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary" onclick="modifica({{ $magazzino->id }})">Modifica</a>
                                            <a href="/utente/mg/movimenti/{{ $magazzino->id }}" class="btn btn-sm btn-success">Movimenti</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($magazzini) == 0)
                                    <tr><td colspan="5" class="text-center text-muted py-4">Nessun magazzino. Premi <b>Ripristina magazzini standard</b> per creare il set base.</td></tr>
                                @endif
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Aggiungi -->
<div class="modal fade" id="modal_aggiungi">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title">Aggiungi Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form autocomplete="off" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">
                                Codice<b style="color:red">*</b>
                                <a href="#" tabindex="0" class="text-info" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="Codice breve identificativo del magazzino (es. MGPF). Usato nei movimenti."><i class="ri-information-line"></i></a>
                            </label>
                            <input type="text" name="codice_magazzino" class="form-control" placeholder="Es. MGPF" required/>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrizione<b style="color:red">*</b></label>
                            <input type="text" name="descrizione" class="form-control" placeholder="Es. Magazzino Prodotti Finiti" required/>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                Tipologia<b style="color:red">*</b>
                                <a href="#" tabindex="0" class="text-info" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-content="<b>Prodotti Finiti</b>: ricevono il carico a chiusura ODL.<br><b>Materie Prime</b>: scaricano i materiali a chiusura ODL.<br><b>Articoli Commerciali</b>: rivendita senza lavorazione.<br><b>Altro</b>: di servizio, gestito a mano."><i class="ri-information-line"></i></a>
                            </label>
                            <select name="tipologia" class="form-select" required>
                                @foreach($tipologie_meta as $key => $meta)
                                    <option value="{{ $key }}">{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_default" value="1" id="is_default_add">
                                <label class="form-check-label" for="is_default_add">
                                    Default per questa tipologia
                                    <a href="#" tabindex="0" class="text-info" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="Se attivato, questo magazzino viene scelto automaticamente dal sistema per i movimenti della sua tipologia. Eventuali altri magazzini default della stessa tipologia perdono il flag."><i class="ri-information-line"></i></a>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" name="aggiungi" value="1" class="btn btn-success">Aggiungi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica per ogni magazzino -->
@foreach($magazzini as $m)
<div class="modal fade" id="modal_modifica_{{ $m->id }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title">Modifica {{ $m->codice_magazzino }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form autocomplete="off" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $m->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Codice<b style="color:red">*</b></label>
                            <input type="text" name="codice_magazzino" class="form-control" value="{{ $m->codice_magazzino }}" required/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrizione<b style="color:red">*</b></label>
                            <input type="text" name="descrizione" class="form-control" value="{{ $m->descrizione }}" required/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-select" required>
                                @foreach($tipologie_meta as $key => $meta)
                                    <option value="{{ $key }}" {{ $m->tipologia === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_default" value="1" id="is_default_edit_{{ $m->id }}" {{ $m->is_default ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default_edit_{{ $m->id }}">Default per questa tipologia</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" name="modifica" value="1" class="btn btn-success">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


@include('utente.common.footer')

<script>
    function aggiungi() {
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id) {
        $('#modal_modifica_' + id).modal('show');
    }

    // Inizializza popover bootstrap (icone "i")
    document.addEventListener('DOMContentLoaded', function () {
        var triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        triggers.forEach(function (el) {
            new bootstrap.Popover(el, { container: 'body' });
            el.addEventListener('click', function (e) { e.preventDefault(); });
        });
    });
</script>
