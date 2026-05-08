@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Importa Prezzi da CSV</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini') }}">Listini Prezzi</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}">{{ $listino->codice }}</a></li>
                            <li class="breadcrumb-item active">Importa CSV</li>
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
                        <h5 class="card-title mb-0">Importa Prezzi nel Listino {{ $listino->codice }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="ri-information-line fs-16 align-middle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Formato del file CSV</h5>
                                    <p class="mb-1">Il file CSV deve contenere le seguenti colonne:</p>
                                    <ul class="ps-3 mb-0">
                                        <li><strong>codice_articolo</strong> (obbligatorio) - Il codice dell'articolo</li>
                                        <li><strong>prezzo</strong> (obbligatorio) - Il prezzo da applicare nel listino</li>
                                        <li><strong>sconto_percentuale</strong> (opzionale) - Lo sconto percentuale da applicare</li>
                                        <li><strong>quantita_minima</strong> (opzionale) - La quantità minima per applicare questo prezzo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a href="{{ url('utente/listini/esempio_csv') }}" class="btn btn-soft-info">
                                <i class="ri-download-2-line align-bottom me-1"></i> Scarica esempio CSV
                            </a>
                        </div>

                        <form method="post" action="{{ url('utente/listini/importa_csv/'.$listino->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file_csv" class="form-label">File CSV <span class="text-danger">*</span></label>
                                <input type="file" id="file_csv" name="file_csv" class="form-control" accept=".csv" required>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}" class="btn btn-light">Annulla</a>
                                <button type="submit" class="btn btn-primary">Importa Prezzi</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Istruzioni aggiuntive -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Istruzioni per l'importazione</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Segui questi passaggi per importare correttamente i prezzi:</p>
                        <ol class="mb-0">
                            <li class="mb-2">Prepara un file CSV con le colonne richieste: <code>codice_articolo</code>, <code>prezzo</code>, <code>sconto_percentuale</code> (opzionale), <code>quantita_minima</code> (opzionale).</li>
                            <li class="mb-2">Assicurati che ogni riga del file contenga almeno il codice articolo e il prezzo.</li>
                            <li class="mb-2">Usa il punto come separatore decimale per i prezzi e gli sconti (es. 10.50).</li>
                            <li class="mb-2">I codici articolo devono corrispondere esattamente a quelli presenti nel sistema.</li>
                            <li class="mb-2">Per gli articoli già presenti nel listino, i valori verranno aggiornati.</li>
                            <li>Gli articoli che non esistono nel sistema verranno ignorati.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')