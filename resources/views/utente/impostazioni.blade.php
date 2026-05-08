@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Impostazioni</h4>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-xxl-8">
                <form method="post">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-settings-3-line me-2"></i>Produzione e Magazzino</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="usa_lotti" id="usa_lotti" value="1" {{ $azienda->usa_lotti ? 'checked' : '' }}>
                                <label class="form-check-label" for="usa_lotti">
                                    <strong>Gestione Lotti</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, in fase di chiusura ODL viene richiesto il lotto per ogni materiale scaricato dal magazzino.
                                <br>Disattivalo se non gestisci i materiali per lotto: lo scarico avverrà comunque ma senza distinzione di lotto.
                            </p>

                            <hr class="my-4">

                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="usa_barcode" id="usa_barcode" value="1" {{ ($azienda->usa_barcode ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="usa_barcode">
                                    <strong>Pistola Barcode (carico/scarico)</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, nelle pagine <i>Carico</i> e <i>Scarico magazzino</i> compare la tab "Scansione Barcode" come scelta predefinita.
                                <br>Disattivalo se non utilizzi una pistola barcode: la tab viene nascosta e si lavora solo dalla "Lista Articoli".
                            </p>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <button type="submit" name="salva_impostazioni" value="1" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Salva Impostazioni
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')
