@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Aggiungi Articolo al Listino</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini') }}">Listini Prezzi</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}">{{ $listino->codice }}</a></li>
                            <li class="breadcrumb-item active">Aggiungi Articolo</li>
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
                        <h5 class="card-title mb-0">Aggiungi Articolo al Listino {{ $listino->codice }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ url('utente/listini/aggiungi_articolo/'.$listino->id) }}">

                            <div class="row g-3 mb-3">
                                <div class="col-lg-12">
                                    <label for="id_articolo" class="form-label">Articolo <span class="text-danger">*</span></label>
                                    <select id="id_articolo" name="id_articolo" class="form-select select2" required>
                                        <option value="">Seleziona un articolo</option>
                                        @foreach($articoli as $articolo)
                                            <option value="{{ $articolo->id }}" data-prezzo="{{ $articolo->prezzo }}">
                                                {{ $articolo->titolo }} ({{ $articolo->codice_articolo }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="prezzo" class="form-label">Prezzo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" id="prezzo" name="prezzo" class="form-control" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="sconto_percentuale" class="form-label">Sconto (%)</label>
                                    <div class="input-group">
                                        <input type="number" id="sconto_percentuale" name="sconto_percentuale" class="form-control" step="0.01" min="0" max="100" value="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="quantita_minima" class="form-label">Quantità Minima</label>
                                    <input type="number" id="quantita_minima" name="quantita_minima" class="form-control" min="1" value="1">
                                    <div class="form-text">Quantità minima per applicare questo prezzo</div>
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

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}" class="btn btn-light">Annulla</a>
                                <button type="submit" class="btn btn-primary" name="aggiungi" value="1">Aggiungi al Listino</button>
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

        // Popolamento automatico del prezzo quando si seleziona l'articolo
        $('#id_articolo').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var prezzo = selectedOption.data('prezzo');

            if (prezzo) {
                $('#prezzo').val(prezzo);
            } else {
                $('#prezzo').val('');
            }
        });
    });
</script>