@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Nuovo Listino Prezzi</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/listini') }}">Listini Prezzi</a></li>
                            <li class="breadcrumb-item active">Nuovo Listino</li>
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
                        <h5 class="card-title mb-0">Crea Nuovo Listino</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ url('utente/listini') }}">

                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="codice" class="form-label">Codice Listino <span class="text-danger">*</span></label>
                                    <input type="text" id="codice" name="codice" class="form-control" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="descrizione" class="form-label">Descrizione <span class="text-danger">*</span></label>
                                    <input type="text" id="descrizione" name="descrizione" class="form-control" required>
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

                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label for="priorita" class="form-label">Priorità</label>
                                    <input type="number" id="priorita" name="priorita" class="form-control" min="0" value="0">
                                    <div class="form-text">Valore più alto = priorità maggiore</div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex flex-column h-100 justify-content-end">
                                        <div class="form-check form-switch form-switch-success">
                                            <input class="form-check-input" type="checkbox" role="switch" id="attivo" name="attivo" checked>
                                            <label class="form-check-label" for="attivo">Listino Attivo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea id="note" name="note" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ url('utente/listini') }}" class="btn btn-light">Annulla</a>
                                <button type="submit" class="btn btn-primary" name="aggiungi" value="1">Salva Listino</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')