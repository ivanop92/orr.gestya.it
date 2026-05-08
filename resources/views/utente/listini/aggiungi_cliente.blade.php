@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Associa Cliente al Listino</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini') }}">Listini Prezzi</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}">{{ $listino->codice }}</a></li>
                            <li class="breadcrumb-item active">Associa Cliente</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Associa Cliente al Listino {{ $listino->codice }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ url('utente/listini/aggiungi_cliente/'.$listino->id) }}">

                            <div class="row g-3 mb-3">
                                <div class="col-lg-12">
                                    <label for="id_cliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select id="id_cliente" name="id_cliente" class="form-select select2" required>
                                        <option value="">Seleziona un cliente</option>
                                        @foreach($clienti as $cliente)
                                            <option value="{{ $cliente->id }}">
                                                {{ $cliente->ragione_sociale ?: $cliente->nome.' '.$cliente->cognome }}
                                                @if($cliente->piva)
                                                    ({{ $cliente->piva }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="data_inizio" class="form-label">Data Inizio Validità</label>
                                    <input type="date" id="data_inizio" name="data_inizio" class="form-control">
                                    <div class="form-text">Lasciare vuoto se sempre valido</div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="data_fine" class="form-label">Data Fine Validità</label>
                                    <input type="date" id="data_fine" name="data_fine" class="form-control">
                                    <div class="form-text">Lasciare vuoto se sempre valido</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea id="note" name="note" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}" class="btn btn-light">Annulla</a>
                                <button type="submit" class="btn btn-primary" name="aggiungi" value="1">Associa Cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        // Inizializza Select2
        $('.select2').select2({
            width: '100%'
        });
    });
</script>