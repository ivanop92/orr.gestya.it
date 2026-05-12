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

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-train-line me-2"></i>Manutenzione (officina ferroviaria)</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="manut_anagrafica_vagoni_attiva" id="manut_anagrafica_vagoni_attiva" value="1" {{ ($azienda->manut_anagrafica_vagoni_attiva ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manut_anagrafica_vagoni_attiva">
                                    <strong>Anagrafica Vagoni</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, viene mostrata la voce "Vagoni" nell'anagrafica e i documenti/ODL possono essere collegati a un vagone specifico del cliente.
                                <br>Disattivalo se i mezzi del cliente non vanno tracciati come asset: il riferimento al mezzo resta come testo libero sulla riga documento.
                            </p>

                            <hr class="my-4">

                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="manut_certificato_ecm_separato" id="manut_certificato_ecm_separato" value="1" {{ ($azienda->manut_certificato_ecm_separato ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manut_certificato_ecm_separato">
                                    <strong>Certificato di Manutenzione ECM separato</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, in Fase 4 viene generato un documento "Certificato di Manutenzione" dedicato (compatibile Reg. UE 445/2011 ECM) in aggiunta al preventivo, con propria numerazione e layout PDF.
                                <br>Disattivalo se il preventivo PDF funge anche da certificato (comportamento storico).
                            </p>

                            <hr class="my-4">

                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="manut_workflow_accettazione_multistep" id="manut_workflow_accettazione_multistep" value="1" {{ ($azienda->manut_workflow_accettazione_multistep ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manut_workflow_accettazione_multistep">
                                    <strong>Workflow Accettazione Multi-Step</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, i documenti (preventivo/certificato) passano attraverso stati intermedi: <i>emesso → in revisione → accettato / rifiutato → rilavorazione → riapprovato → fatturabile</i>. Mostra badge di stato e bottoni Accetta/Rifiuta.
                                <br>Disattivalo per il flusso semplice senza stati di accettazione.
                            </p>

                            <hr class="my-4">

                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="manut_magazzino_ricetta_default" id="manut_magazzino_ricetta_default" value="1" {{ ($azienda->manut_magazzino_ricetta_default ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manut_magazzino_ricetta_default">
                                    <strong>Ricetta Materiali da Distinta Base</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, all'apertura di un ODL di manutenzione i materiali della distinta base del tipo intervento vengono precaricati automaticamente come righe da scaricare a magazzino.
                                <br>Disattivalo se preferisci aggiungere i materiali manualmente intervento per intervento.
                            </p>

                            <hr class="my-4">

                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="manut_consuntivo_materiali_manutentore" id="manut_consuntivo_materiali_manutentore" value="1" {{ ($azienda->manut_consuntivo_materiali_manutentore ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manut_consuntivo_materiali_manutentore">
                                    <strong>Consuntivo Materiali da App Produzione</strong>
                                </label>
                            </div>
                            <p class="text-muted mb-0 ms-5">
                                Quando attivo, il manutentore può aggiungere o rimuovere materiali consumati direttamente dalla sua app durante la lavorazione; lo scarico magazzino avviene al consuntivo finale.
                                <br>Disattivalo se i materiali sono fissati a preventivo e il manutentore non li tocca.
                            </p>

                            <hr class="my-4">

                            <div class="mb-2">
                                <label class="form-label" for="manut_tariffa_oraria_default">
                                    <strong>Tariffa Oraria Manodopera (default)</strong>
                                </label>
                                <div class="input-group" style="max-width: 240px;">
                                    <input class="form-control" type="number" step="0.01" min="0" name="manut_tariffa_oraria_default" id="manut_tariffa_oraria_default" value="{{ number_format($azienda->manut_tariffa_oraria_default ?? 33.75, 2, '.', '') }}">
                                    <span class="input-group-text">€/h</span>
                                </div>
                            </div>
                            <p class="text-muted mb-0">
                                Tariffa oraria applicata come default alle righe di lavorazione che hanno <i>minuti</i> ma non un prezzo specifico. Modificabile riga per riga sul singolo documento.
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
