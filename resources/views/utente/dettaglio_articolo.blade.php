@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dettaglio Articolo</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Articoli</a></li>
                            <li class="breadcrumb-item active">Dettaglio</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{ $articolo->titolo }}</h5>
                            <div>
                                <a href="{{ url('utente/articoli') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line"></i> Torna all'elenco
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link mb-2 active" id="generale-tab" data-bs-toggle="pill" href="#generale" role="tab" aria-controls="generale" aria-selected="true">
                                        <i class="ri-file-info-line align-middle me-1"></i> Generale
                                    </a>
                                    <a class="nav-link mb-2" id="fattori-conversione-tab" data-bs-toggle="pill" href="#fattori-conversione" role="tab" aria-controls="fattori-conversione" aria-selected="false">
                                        <i class="ri-repeat-line align-middle me-1"></i> Fattori di Conversione
                                    </a>
                                    <a class="nav-link mb-2" id="contabilita-tab" data-bs-toggle="pill" href="#contabilita" role="tab" aria-controls="contabilita" aria-selected="false">
                                        <i class="ri-bank-line align-middle me-1"></i> Contabilità
                                    </a>
                                    <a class="nav-link mb-2" id="produzione-tab" data-bs-toggle="pill" href="#produzione" role="tab" aria-controls="produzione" aria-selected="false">
                                        <i class="ri-tools-line align-middle me-1"></i> Produzione
                                    </a>
                                    <a class="nav-link mb-2" id="alloggiato-tab" data-bs-toggle="pill" href="#alloggiato" role="tab" aria-controls="alloggiato" aria-selected="false">
                                        <i class="ri-map-pin-line align-middle me-1"></i> AllogIn
                                    </a>
                                    <a class="nav-link mb-2" id="fattura-elettronica-tab" data-bs-toggle="pill" href="#fattura-elettronica" role="tab" aria-controls="fattura-elettronica" aria-selected="false">
                                        <i class="ri-file-list-3-line align-middle me-1"></i> Fattura Elettronica
                                    </a>
                                    <a class="nav-link mb-2" id="alternative-tab" data-bs-toggle="pill" href="#alternative" role="tab" aria-controls="alternative" aria-selected="false">
                                        <i class="ri-stack-line align-middle me-1"></i> Alternative
                                    </a>
                                    <a class="nav-link mb-2" id="lotti-ubicazione-tab" data-bs-toggle="pill" href="#lotti-ubicazione" role="tab" aria-controls="lotti-ubicazione" aria-selected="false">
                                        <i class="ri-barcode-line align-middle me-1"></i> Lotti/Ubicazione
                                    </a>
                                    <a class="nav-link mb-2" id="immagini-tab" data-bs-toggle="pill" href="#immagini" role="tab" aria-controls="immagini" aria-selected="false">
                                        <i class="ri-image-line align-middle me-1"></i> Immagini
                                    </a>
                                    <a class="nav-link mb-2" id="e-business-tab" data-bs-toggle="pill" href="#e-business" role="tab" aria-controls="e-business" aria-selected="false">
                                        <i class="ri-shopping-cart-line align-middle me-1"></i> e-Business
                                    </a>
                                    @if($usa_lotti)
                                    <a class="nav-link mb-2" id="lotti-tab" data-bs-toggle="pill" href="#lotti" role="tab" aria-controls="lotti" aria-selected="false">
                                        <i class="ri-list-check align-middle me-1"></i> Lotti
                                    </a>
                                    @endif
                                    <a class="nav-link mb-2" id="valori-tab" data-bs-toggle="pill" href="#valori" role="tab" aria-controls="valori" aria-selected="false">
                                        <i class="ri-scales-3-line align-middle me-1"></i> Valori
                                    </a>
                                    <a class="nav-link mb-2" id="altro-tab" data-bs-toggle="pill" href="#altro" role="tab" aria-controls="altro" aria-selected="false">
                                        <i class="ri-more-line align-middle me-1"></i> Altro
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <!-- Tab Generale -->
                                    <div class="tab-pane fade show active" id="generale" role="tabpanel" aria-labelledby="generale-tab">
                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">CARATTERISTICHE</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <label for="stato" class="form-label">Stato</label>
                                                                    <select class="form-select" id="stato" name="stato">
                                                                        <option value="attivo" {{ $articolo->stato == 'attivo' ? 'selected' : '' }}>Attivo</option>
                                                                        <option value="disattivo" {{ $articolo->stato == 'disattivo' ? 'selected' : '' }}>Disattivo</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <label for="marca_modello" class="form-label">Marca/Modello</label>
                                                                    <input type="text" class="form-control" id="marca_modello" name="marca_modello" value="{{ $articolo->marca_modello }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <label for="tipologia" class="form-label">Tipologia</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="tipologia" id="tipologia_0" value="0" {{ $articolo->tipologia == 0 ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="tipologia_0">Prodotto Finito</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="tipologia" id="tipologia_1" value="1" {{ $articolo->tipologia == 1 ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="tipologia_1">Materia Prima</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="tipologia" id="tipologia_3" value="3" {{ $articolo->tipologia == 3 ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="tipologia_3">Semilavorato</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <label for="codice_articolo" class="form-label">Codice</label>
                                                                    <input type="text" class="form-control" id="codice_articolo" name="codice_articolo" value="{{ $articolo->codice_articolo }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label for="titolo" class="form-label">Descrizione</label>
                                                                    <input type="text" class="form-control" id="titolo" name="titolo" value="{{ $articolo->titolo }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label for="barcode" class="form-label">Barcode</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" id="barcode" name="barcode" value="{{ $articolo->barcode }}">
                                                                        <button class="btn btn-outline-secondary" type="button" id="button-barcode">
                                                                            <i class="ri-barcode-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($articolo->tipologia == 0)
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <label for="id_cliente" class="form-label">Cliente associato</label>
                                                                    <select class="form-select" id="id_cliente" name="id_cliente">
                                                                        <option value="">-- Nessun cliente --</option>
                                                                        @foreach($clienti as $cl)
                                                                            <option value="{{ $cl->id }}" {{ $articolo->id_cliente == $cl->id ? 'selected' : '' }}>
                                                                                {{ $cl->ragione_sociale }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="data_primo_carico" class="form-label">Data primo carico</label>
                                                                    <input type="date" class="form-control" id="data_primo_carico" name="data_primo_carico" value="{{ $articolo->data_primo_carico }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-4">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">DIMENSIONI / PESI</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="dimensione_l" class="form-label">Dimensione L</label>
                                                                    <input type="number" step="0.01" class="form-control" id="dimensione_l" name="dimensione_l" value="{{ $articolo->dimensione_l }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="dimensione_h" class="form-label">Dimensione H</label>
                                                                    <input type="number" step="0.01" class="form-control" id="dimensione_h" name="dimensione_h" value="{{ $articolo->dimensione_h }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="dimensione_p" class="form-label">Dimensione P</label>
                                                                    <input type="number" step="0.01" class="form-control" id="dimensione_p" name="dimensione_p" value="{{ $articolo->dimensione_p }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="lunghezza" class="form-label">Lunghezza (metri)</label>
                                                                    <input type="number" step="0.01" class="form-control" id="lunghezza" name="lunghezza" value="{{ $articolo->lunghezza }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="volume_metri" class="form-label">Volume (metri)</label>
                                                                    <input type="number" step="0.01" class="form-control" id="volume_metri" name="volume_metri" value="{{ $articolo->volume_metri }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="volume_metri_cubi" class="form-label">Volume (metri cubi)</label>
                                                                    <input type="number" step="0.000001" class="form-control" id="volume_metri_cubi" name="volume_metri_cubi" value="{{ $articolo->volume_metri_cubi }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="altezza" class="form-label">Altezza</label>
                                                                    <input type="number" step="0.01" class="form-control" id="altezza" name="altezza" value="{{ $articolo->altezza }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-4">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">PREZZI</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="prezzo" class="form-label">Prezzo di Listino</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">€</span>
                                                                        <input type="number" step="0.01" class="form-control" id="prezzo" name="prezzo" value="{{ $articolo->prezzo }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="prezzo_lordo" class="form-label">Prezzo Lordo</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">€</span>
                                                                        <input type="number" step="0.01" class="form-control" id="prezzo_lordo" name="prezzo_lordo" value="{{ $articolo->prezzo_lordo }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label for="prezzo_netto" class="form-label">Prezzo Netto</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">€</span>
                                                                        <input type="number" step="0.01" class="form-control" id="prezzo_netto" name="prezzo_netto" value="{{ $articolo->prezzo_netto }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end mt-3">
                                                <button type="submit" name="salva_generale" value="1" class="btn btn-success">Salva Modifiche</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!--  tab "Fattori di Conversione" -->
                                    <div class="tab-pane fade" id="fattori-conversione" role="tabpanel" aria-labelledby="fattori-conversione-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">FATTORI DI CONVERSIONE</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    @csrf
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-3 mb-3">
                                                                    <label class="form-label">UM Originale</label>
                                                                    <input type="text" class="form-control" value="{{ $articolo->um }}" readonly>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label class="form-label">Quantità Base</label>
                                                                    <input type="text" class="form-control" value="1" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <h6 class="mb-3">Fattori di Conversione</h6>

                                                            <div id="fattori-container">
                                                                @if(isset($fattori_conversione) && count($fattori_conversione) > 0)
                                                                    @foreach($fattori_conversione as $index => $fattore)
                                                                        <div class="row mb-2 fattore-row">
                                                                            <div class="col-md-4">
                                                                                <input type="number" step="0.001" class="form-control" name="fattore_valore[]" value="{{ $fattore->valore }}" placeholder="Valore">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <input type="text" class="form-control" name="fattore_um[]" value="{{ $fattore->unita_misura }}" placeholder="Unità di misura">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button type="button" class="btn btn-danger btn-sm w-100 rimuovi-fattore">
                                                                                    <i class="ri-delete-bin-line"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="row mb-2 fattore-row">
                                                                        <div class="col-md-4">
                                                                            <input type="number" step="0.001" class="form-control" name="fattore_valore[]" placeholder="Valore">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="fattore_um[]" placeholder="Unità di misura">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-danger btn-sm w-100 rimuovi-fattore">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex justify-content-end mt-2">
                                                                <button type="button" class="btn btn-primary btn-sm" id="aggiungi-fattore">
                                                                    <i class="ri-add-line align-bottom"></i> Aggiungi Fattore
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_fattori_conversione" value="1" class="btn btn-success">Salva Fattori</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Contabilità -->
                                    <div class="tab-pane fade" id="contabilita" role="tabpanel" aria-labelledby="contabilita-tab">
                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">CONTABILITÀ</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="aliquota_iva" class="form-label">Aliquota IVA</label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.01" class="form-control" id="aliquota_iva" name="aliquota_iva" value="{{ $articolo->aliquota_iva ?? 22 }}">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="natura_iva" class="form-label">Natura IVA</label>
                                                                    <select class="form-select" id="natura_iva" name="natura_iva">
                                                                        <option value="">Nessuna (Imponibile)</option>
                                                                        <option value="N1" {{ ($articolo->natura_iva ?? '') == 'N1' ? 'selected' : '' }}>N1 - Escluse</option>
                                                                        <option value="N2" {{ ($articolo->natura_iva ?? '') == 'N2' ? 'selected' : '' }}>N2 - Non soggette</option>
                                                                        <option value="N3" {{ ($articolo->natura_iva ?? '') == 'N3' ? 'selected' : '' }}>N3 - Non imponibili</option>
                                                                        <option value="N4" {{ ($articolo->natura_iva ?? '') == 'N4' ? 'selected' : '' }}>N4 - Esenti</option>
                                                                        <option value="N5" {{ ($articolo->natura_iva ?? '') == 'N5' ? 'selected' : '' }}>N5 - Regime del margine</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="conto_acquisti" class="form-label">Conto acquisti</label>
                                                                    <input type="text" class="form-control" id="conto_acquisti" name="conto_acquisti" value="{{ $articolo->conto_acquisti }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="conto_vendite" class="form-label">Conto vendite</label>
                                                                    <input type="text" class="form-control" id="conto_vendite" name="conto_vendite" value="{{ $articolo->conto_vendite }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-3">
                                                <button type="submit" name="salva_contabilita" value="1" class="btn btn-success">Salva Contabilità</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Tab Produzione -->
                                    <div class="tab-pane fade" id="produzione" role="tabpanel" aria-labelledby="produzione-tab">
                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">FASI DI PRODUZIONE</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="width: 10%">Seleziona</th>
                                                                        <th style="width: 60%">Fase</th>
                                                                        <th style="width: 30%">Tempo medio (min)</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($fasi as $fase)
                                                                        <tr>
                                                                            <td>
                                                                                <input class="form-check-input" type="checkbox" name="fasi[]" value="{{ $fase->id }}" id="fase_{{ $fase->id }}"
                                                                                        {{ in_array($fase->id, $fasi_associate) ? 'checked' : '' }}>
                                                                            </td>
                                                                            <td>{{ $fase->descrizione }}</td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="tempo_medio[{{ $fase->id }}]"
                                                                                       value="{{ $tempi_medi[$fase->id] ?? 0 }}"
                                                                                        {{ in_array($fase->id, $fasi_associate) ? '' : 'disabled' }}>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-4">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">DISTINTA BASE</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                @foreach($fasi_associate as $index => $id_fase)
                                                                    @php
                                                                        $fase = $fasi->firstWhere('id', $id_fase);
                                                                    @endphp
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                                                id="fase-{{ $id_fase }}-tab"
                                                                                data-bs-toggle="tab"
                                                                                data-bs-target="#fase-{{ $id_fase }}"
                                                                                type="button"
                                                                                role="tab"
                                                                                aria-controls="fase-{{ $id_fase }}"
                                                                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                                                            {{ $fase->descrizione }}
                                                                        </button>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                            <div class="tab-content" id="myTabContent">
                                                                @foreach($fasi_associate as $index => $id_fase)
                                                                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                                                         id="fase-{{ $id_fase }}"
                                                                         role="tabpanel"
                                                                         aria-labelledby="fase-{{ $id_fase }}-tab">
                                                                        <div class="p-3">
                                                                            <div class="d-flex justify-content-end mb-3">
                                                                                <button type="button" class="btn btn-primary btn-sm aggiungi-materiale" data-fase="{{ $id_fase }}">
                                                                                    <i class="ri-add-line align-bottom"></i> Aggiungi Materiale
                                                                                </button>
                                                                            </div>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered table-striped">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>Materiale</th>
                                                                                        <th>Quantità</th>
                                                                                        <th>U.M.</th>
                                                                                        <th>Azioni</th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody id="materiali-fase-{{ $id_fase }}">
                                                                                    @if(isset($distinta_per_fase[$id_fase]))
                                                                                        @foreach($distinta_per_fase[$id_fase] as $materiale)
                                                                                            <tr>
                                                                                                <td>{{ $materiale->materiale }}</td>
                                                                                                <td>{{ $materiale->qta }}</td>
                                                                                                <td>{{ $materiale->um }}</td>
                                                                                                <td>
                                                                                                    <button type="button" class="btn btn-danger btn-sm rimuovi-materiale">
                                                                                                        <i class="ri-delete-bin-line align-bottom"></i>
                                                                                                    </button>
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    @endif
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end mt-3">
                                                <button type="submit" name="salva_produzione" value="1" class="btn btn-success">Salva Produzione</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Tab AllogIn -->
                                    <div class="tab-pane fade" id="alloggiato" role="tabpanel" aria-labelledby="alloggiato-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">ALLOGIN</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Configurazione per AllogIn non disponibile per questo articolo.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Fattura Elettronica -->
                                    <div class="tab-pane fade" id="fattura-elettronica" role="tabpanel" aria-labelledby="fattura-elettronica-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">FATTURA ELETTRONICA</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="codice_tipo_articolo" class="form-label">Codice Tipo Articolo</label>
                                                                    <select class="form-select" id="codice_tipo_articolo" name="codice_tipo_articolo">
                                                                        <option value="">Nessuno</option>
                                                                        <option value="AAQQ" {{ ($articolo->codice_tipo_articolo ?? '') == 'AAQQ' ? 'selected' : '' }}>AAQQ - Alimenti e bevande</option>
                                                                        <option value="BBCC" {{ ($articolo->codice_tipo_articolo ?? '') == 'BBCC' ? 'selected' : '' }}>BBCC - Beni culturali</option>
                                                                        <option value="CARB" {{ ($articolo->codice_tipo_articolo ?? '') == 'CARB' ? 'selected' : '' }}>CARB - Carburante</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="codice_articolo_fe" class="form-label">Codice Articolo</label>
                                                                    <input type="text" class="form-control" id="codice_articolo_fe" name="codice_articolo_fe" value="{{ $articolo->codice_articolo_fe }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_fattura_elettronica" value="1" class="btn btn-success">Salva</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Alternative -->
                                    <!-- Tab Alternative -->
                                    <div class="tab-pane fade" id="alternative" role="tabpanel" aria-labelledby="alternative-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">ALTERNATIVE</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-end mb-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="aggiungi-alternativa">
                                                        <i class="ri-add-line align-bottom"></i> Aggiungi Alternativa
                                                    </button>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Codice</th>
                                                            <th>Descrizione</th>
                                                            <th>Prezzo</th>
                                                            <th>Giacenza</th>
                                                            <th>Azioni</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php
                                                            // Recupera gli articoli alternativi
                                                            $articoli_alternativi = DB::table('articoli_alternativi')
                                                                ->join('articoli', 'articoli.id', '=', 'articoli_alternativi.id_articolo_alternativo')
                                                                ->where('articoli_alternativi.id_articolo', $articolo->id)
                                                                ->where('articoli_alternativi.id_azienda', $utente->id_azienda)
                                                                ->select('articoli.*', 'articoli_alternativi.id as id_alternativa')
                                                                ->get();
                                                        @endphp

                                                        @if(count($articoli_alternativi) > 0)
                                                            @foreach($articoli_alternativi as $alternativa)
                                                                <tr>
                                                                    <td>{{ $alternativa->codice_articolo }}</td>
                                                                    <td>{{ $alternativa->titolo }}</td>
                                                                    <td>€ {{ number_format($alternativa->prezzo, 2) }}</td>
                                                                    <td>{{ number_format($alternativa->giacenza, 2) }} {{ $alternativa->um }}</td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger btn-sm rimuovi-alternativa" data-id="{{ $alternativa->id_alternativa }}">
                                                                            <i class="ri-delete-bin-line align-bottom"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="text-center">Nessuna alternativa disponibile per questo articolo.</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal per aggiungere un'alternativa -->
                                    <div class="modal fade" id="aggiungiAlternativaModal" tabindex="-1" aria-labelledby="aggiungiAlternativaModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="aggiungiAlternativaModalLabel">Aggiungi Articolo Alternativo</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control" id="cerca-articolo" placeholder="Cerca articolo (codice o descrizione)">
                                                    </div>

                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered table-striped" id="tabella-articoli-cercati">
                                                            <thead>
                                                            <tr>
                                                                <th>Codice</th>
                                                                <th>Descrizione</th>
                                                                <th>Prezzo</th>
                                                                <th>Giacenza</th>
                                                                <th>Azioni</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td colspan="5" class="text-center">Utilizza la ricerca per trovare articoli</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Lotti/Ubicazione -->
                                    <div class="tab-pane fade" id="lotti-ubicazione" role="tabpanel" aria-labelledby="lotti-ubicazione-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">LOTTI E UBICAZIONE</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="id_mg" class="form-label">Magazzino Default</label>
                                                                    <select class="form-select" id="id_mg" name="id_mg">
                                                                        <option value="">Seleziona magazzino</option>
                                                                        @foreach($magazzini as $magazzino)
                                                                            <option value="{{ $magazzino->id }}" {{ isset($articolo->id_mg) && $articolo->id_mg == $magazzino->id ? 'selected' : '' }}>
                                                                                {{ $magazzino->descrizione }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="um" class="form-label">Unità di Misura</label>
                                                                    <input type="text" class="form-control" id="um" name="um" value="{{ $articolo->um }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="giacenza" class="form-label">Giacenza Totale</label>
                                                                    <input type="number" step="0.01" class="form-control" id="giacenza" name="giacenza" value="{{ $articolo->giacenza ?? 0 }}" readonly>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="punto_riordino" class="form-label">Punto di Riordino</label>
                                                                    <input type="number" step="0.01" class="form-control" id="punto_riordino" name="punto_riordino" value="{{ $articolo->punto_riordino ?? 0 }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6>Dettaglio giacenze per magazzino</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped">
                                                                <thead>
                                                                <tr>
                                                                    <th>Magazzino</th>
                                                                    <th>Giacenza</th>
                                                                    <th>Azioni</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    // Calcola le giacenze per magazzino usando mgmov
                                                                    $giacenze_per_magazzino = DB::table('mgmov')
                                                                        ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
                                                                        ->where('mgmov.id_articolo', $articolo->id)
                                                                        ->where('mgmov.id_azienda', $utente->id_azienda)
                                                                        ->select('mg.id', 'mg.descrizione', DB::raw('SUM(mgmov.qta) as giacenza'))
                                                                        ->groupBy('mg.id', 'mg.descrizione')
                                                                        ->get();
                                                                @endphp

                                                                @foreach($giacenze_per_magazzino as $giacenza)
                                                                    <tr>
                                                                        <td>{{ $giacenza->descrizione }}</td>
                                                                        <td>{{ number_format($giacenza->giacenza, 2) }} {{ $articolo->um }}</td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-success me-1" onclick="mostraModalCarico({{ $articolo->id }}, {{ $giacenza->id }})">
                                                                                <i class="ri-arrow-up-line"></i> Carico
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-danger" onclick="mostraModalScarico({{ $articolo->id }}, {{ $giacenza->id }})">
                                                                                <i class="ri-arrow-down-line"></i> Scarico
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_magazzino" value="1" class="btn btn-success">Salva</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Immagini -->
                                    <div class="tab-pane fade" id="immagini" role="tabpanel" aria-labelledby="immagini-tab">
                                        <div class="row">
                                            <!-- Immagine Principale -->
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">IMMAGINE PRINCIPALE</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" enctype="multipart/form-data" novalidate>
                                                            @csrf
                                                            <div class="row g-3">
                                                                <div class="col-lg-12">
                                                                    <div class="text-center">
                                                                        <div class="position-relative d-inline-block">
                                                                            <div class="avatar-xl">
                                                                                <div class="avatar-title bg-light rounded">
                                                                                    <img src="{{ $articolo->immagine ?? '/placehold_immagine.png' }}" id="preview-img" class="avatar-lg rounded object-cover">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label for="immagine" class="form-label">Carica nuova immagine</label>
                                                                        <input class="form-control" type="file" id="immagine" name="immagine" accept="image/*">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end mt-3">
                                                                <button type="submit" name="salva_immagine" value="1" class="btn btn-success">Salva Immagine</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- File e Documenti -->
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="card-title mb-0">FILE E DOCUMENTI</h6>
                                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#aggiungiFileModal">
                                                                <i class="ri-add-line"></i> Aggiungi File
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                <tr>
                                                                    <th>Nome File</th>
                                                                    <th>Tipo</th>
                                                                    <th>Azioni</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    $files = DB::table('articoli_files')
                                                                        ->where('id_articolo', $articolo->id)
                                                                        ->where('id_azienda', $utente->id_azienda)
                                                                        ->get();
                                                                @endphp
                                                                @if(count($files) > 0)
                                                                    @foreach($files as $file)
                                                                        <tr>
                                                                            <td>{{ $file->nome_file }}</td>
                                                                            <td>
                                                                                <span class="badge bg-secondary">{{ $file->tipo_file }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ asset($file->path_file) }}" target="_blank" class="btn btn-sm btn-info">
                                                                                    <i class="ri-eye-line"></i>
                                                                                </a>
                                                                                <button type="button" class="btn btn-sm btn-danger" onclick="eliminaFile({{ $file->id }})">
                                                                                    <i class="ri-delete-bin-line"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">Nessun file caricato</td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Aggiungi File -->
                                    <div class="modal fade" id="aggiungiFileModal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Aggiungi File</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nuovo_file" class="form-label">Seleziona File</label>
                                                            <input type="file" class="form-control" id="nuovo_file" name="nuovo_file" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="tipo_file" class="form-label">Tipo File</label>
                                                            <select class="form-select" name="tipo_file" required>
                                                                <option value="immagine">Immagine</option>
                                                                <option value="documento">Documento</option>
                                                                <option value="scheda_tecnica">Scheda Tecnica</option>
                                                                <option value="certificato">Certificato</option>
                                                                <option value="altro">Altro</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                                                        <button type="submit" name="salva_file" value="1" class="btn btn-primary">Salva File</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tab e-Business -->
                                    <div class="tab-pane fade" id="e-business" role="tabpanel" aria-labelledby="e-business-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">E-BUSINESS</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="pubblicato_online" class="form-label">Pubblicato Online</label>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="pubblicato_online" name="pubblicato_online" {{ ($articolo->pubblicato_online ?? 0) == 1 ? 'checked' : '' }}>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="in_evidenza" class="form-label">In Evidenza</label>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="in_evidenza" name="in_evidenza" {{ ($articolo->in_evidenza ?? 0) == 1 ? 'checked' : '' }}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label for="descrizione_online" class="form-label">Descrizione per Online</label>
                                                                    <textarea class="form-control" id="descrizione_online" name="descrizione_online" rows="5">{{ $articolo->descrizione_online }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_ebusiness" value="1" class="btn btn-success">Salva</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Lotti -->
                                    <div class="tab-pane fade" id="lotti" role="tabpanel" aria-labelledby="lotti-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">LOTTI</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-end mb-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="aggiungi-lotto">
                                                        <i class="ri-add-line align-bottom"></i> Nuovo Lotto
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Codice Lotto</th>
                                                            <th>Data Creazione</th>
                                                            <th>Data Scadenza</th>
                                                            <th>Giacenza</th>
                                                            <th>Azioni</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <!-- Qui verranno mostrati i lotti tramite JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Valori -->
                                    <div class="tab-pane fade" id="valori" role="tabpanel" aria-labelledby="valori-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">VALORI</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="costo_acquisto" class="form-label">Costo di Acquisto</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">€</span>
                                                                        <input type="number" step="0.01" class="form-control" id="costo_acquisto" name="costo_acquisto" value="{{ $articolo->costo_acquisto }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="margine" class="form-label">Margine (%)</label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.01" class="form-control" id="margine" name="margine" value="{{ $articolo->margine }}">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_valori" value="1" class="btn btn-success">Salva</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Altro -->
                                    <div class="tab-pane fade" id="altro" role="tabpanel" aria-labelledby="altro-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">FAMIGLIA</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                            <div class="row g-3">
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <label for="id_famiglia" class="form-label">Seleziona Famiglia</label>
                                                                            <div class="input-group">
                                                                                <select class="form-select" id="id_famiglia" name="id_famiglia">
                                                                                    <option value="">Nessuna famiglia</option>
                                                                                    @foreach($famiglie as $fam)
                                                                                        <option value="{{ $fam->id }}" {{ $articolo->id_famiglia == $fam->id ? 'selected' : '' }}>
                                                                                            {{ $fam->nome }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <button class="btn btn-outline-secondary" type="button" id="aggiungi-famiglia">
                                                                                    <i class="ri-add-line"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end mt-3">
                                                                <button type="submit" name="salva_famiglia" value="1" class="btn btn-success">Salva</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">CATEGORIA</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                            <div class="row g-3">
                                                                <div class="col-lg-12">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <label for="id_categoria" class="form-label">Seleziona Categoria</label>
                                                                            <div class="input-group">
                                                                                <select class="form-select" id="id_categoria" name="id_categoria">
                                                                                    <option value="">Nessuna categoria</option>
                                                                                    @foreach($categorie as $cat)
                                                                                        <option value="{{ $cat->id }}" {{ $articolo->id_categoria == $cat->id ? 'selected': '' }}>
                                                                                            {{ $cat->nome }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <button class="btn btn-outline-secondary" type="button" id="aggiungi-categoria">
                                                                                    <i class="ri-add-line"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end mt-3">
                                                                <button type="submit" name="salva_categoria" value="1" class="btn btn-success">Salva</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">ATTRIBUTI</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-primary btn-sm" id="aggiungi-attributo">
                                                                    <i class="ri-add-line align-bottom"></i> Aggiungi Attributo
                                                                </button>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped" id="tabella-attributi">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Nome Attributo</th>
                                                                        <th>Valore</th>
                                                                        <th>Azioni</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php
                                                                        $attributi = json_decode($articolo->attributi ?? '[]', true);
                                                                    @endphp
                                                                    @if(is_array($attributi) && count($attributi) > 0)
                                                                        @foreach($attributi as $nome => $valore)
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="text" class="form-control" name="attributi[{{ $nome }}][nome]" value="{{ $nome }}">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" name="attributi[{{ $nome }}][valore]" value="{{ $valore }}">
                                                                                </td>
                                                                                <td>
                                                                                    <button type="button" class="btn btn-danger btn-sm rimuovi-attributo">
                                                                                        <i class="ri-delete-bin-line align-bottom"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_attributi" value="1" class="btn btn-success">Salva Attributi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">NOTE</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}" class="needs-validation" novalidate>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <textarea class="form-control" id="note" name="note" rows="5">{{ $articolo->note }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" name="salva_note" value="1" class="btn btn-success">Salva Note</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMovimentoMagazzino" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titoloModalMovimento">Carico Magazzino</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formMovimentoMagazzino" method="post" action="{{ url('utente/dettaglio_articolo/'.$articolo->id) }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="tipo_movimento" id="tipo_movimento" value="carico">
                            <input type="hidden" name="id_mg" id="id_mg" value="">

                            <div class="mb-3">
                                <label for="movimento_magazzino" class="form-label">Magazzino</label>
                                <input type="text" class="form-control" id="magazzino_nome" readonly>
                            </div>

                            <div class="mb-3" id="container_lotto_esistente">
                                <label for="lotto_esistente" class="form-label">Lotto Esistente</label>
                                <select class="form-select" id="lotto_esistente">
                                    <option value="">Seleziona lotto esistente</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="lotto" class="form-label">Lotto</label>
                                <input type="text" class="form-control" id="lotto" name="lotto">
                            </div>

                            <div class="mb-3">
                                <label for="scadenza_lotto" class="form-label">Scadenza Lotto</label>
                                <input type="date" class="form-control" id="scadenza_lotto" name="scadenza_lotto">
                            </div>

                            <div class="mb-3">
                                <label for="qta" class="form-label">Quantità</label>
                                <div class="input-group">
                                    <input type="number" step="0.001" class="form-control" id="qta" name="qta" required>
                                    <span class="input-group-text" id="um_movimento">{{ $articolo->um }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="causale" class="form-label">Causale</label>
                                <input type="text" class="form-control" id="causale" name="causale">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" name="esegui_movimento" value="1" class="btn btn-primary">Esegui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal per l'aggiunta del materiale alla distinta base -->
        <div class="modal fade" id="aggiungiMaterialeModal" tabindex="-1" aria-labelledby="aggiungiMaterialeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aggiungiMaterialeModalLabel">Aggiungi Materiale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-aggiungi-materiale">
                            <input type="hidden" id="id_fase_materiale" name="id_fase_materiale">
                            <div class="mb-3">
                                <label for="id_materiale" class="form-label">Materiale</label>
                                <select class="form-select" id="id_materiale" name="id_materiale" required>
                                    <option value="">Seleziona materiale</option>
                                    <!-- Le opzioni verranno caricate dinamicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantita_materiale" class="form-label">Quantità</label>
                                <input type="number" step="0.0001" class="form-control" id="quantita_materiale" name="quantita_materiale" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="salva-materiale">Aggiungi</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal per l'aggiunta di famiglia -->
        <div class="modal fade" id="aggiungiFamigliaModal" tabindex="-1" aria-labelledby="aggiungiFamigliaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aggiungiFamigliaModalLabel">Aggiungi Famiglia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-aggiungi-famiglia">
                            <div class="mb-3">
                                <label for="nome_famiglia" class="form-label">Nome Famiglia</label>
                                <input type="text" class="form-control" id="nome_famiglia" name="nome_famiglia" required>
                            </div>
                            <div class="mb-3">
                                <label for="descrizione_famiglia" class="form-label">Descrizione</label>
                                <textarea class="form-control" id="descrizione_famiglia" name="descrizione_famiglia" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="salva-famiglia">Salva</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal per l'aggiunta di categoria -->
        <div class="modal fade" id="aggiungiCategoriaModal" tabindex="-1" aria-labelledby="aggiungiCategoriaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aggiungiCategoriaModalLabel">Aggiungi Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-aggiungi-categoria">
                            <div class="mb-3">
                                <label for="nome_categoria" class="form-label">Nome Categoria</label>
                                <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" required>
                            </div>
                            <div class="mb-3">
                                <label for="descrizione_categoria" class="form-label">Descrizione</label>
                                <textarea class="form-control" id="descrizione_categoria" name="descrizione_categoria" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="salva-categoria">Salva</button>
                    </div>
                </div>
            </div>
        </div>

        @include('utente.common.footer')
        <!-- Includi SweetAlert2 dai CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Gestione fattori di conversione
                const fattoriContainer = document.getElementById('fattori-container');
                const aggiungifattore = document.getElementById('aggiungi-fattore');

                aggiungifattore.addEventListener('click', function() {
                    const newRow = document.createElement('div');
                    newRow.className = 'row mb-2 fattore-row';
                    newRow.innerHTML = `
                <div class="col-md-4">
                    <input type="number" step="0.001" class="form-control" name="fattore_valore[]" placeholder="Valore">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="fattore_um[]" placeholder="Unità di misura">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100 rimuovi-fattore">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
                    fattoriContainer.appendChild(newRow);

                    // Aggiungi l'event listener al nuovo pulsante di rimozione
                    const nuovoRimuovi = newRow.querySelector('.rimuovi-fattore');
                    nuovoRimuovi.addEventListener('click', function() {
                        newRow.remove();
                    });
                });

                // Aggiungi event listener ai pulsanti di rimozione esistenti
                document.querySelectorAll('.rimuovi-fattore').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.fattore-row').remove();
                    });
                });
            });

            // Funzioni per gestire le modali di carico/scarico
            function mostraModalCarico(idArticolo, idMg) {
                document.getElementById('tipo_movimento').value = 'carico';
                document.getElementById('id_mg').value = idMg;
                document.getElementById('titoloModalMovimento').textContent = 'Carico Magazzino';
                document.getElementById('container_lotto_esistente').style.display = 'none';

                // Reset campi
                document.getElementById('lotto').value = '';
                document.getElementById('scadenza_lotto').value = '';
                document.getElementById('qta').value = '';
                document.getElementById('causale').value = 'Carico manuale';

                // Carica i dati del magazzino
                caricaDatiMagazzino(idArticolo, idMg);

                // Mostra la modale
                const modal = new bootstrap.Modal(document.getElementById('modalMovimentoMagazzino'));
                modal.show();
            }

            function mostraModalScarico(idArticolo, idMg) {
                document.getElementById('tipo_movimento').value = 'scarico';
                document.getElementById('id_mg').value = idMg;
                document.getElementById('titoloModalMovimento').textContent = 'Scarico Magazzino';
                document.getElementById('container_lotto_esistente').style.display = 'block';

                // Reset campi
                document.getElementById('lotto').value = '';
                document.getElementById('scadenza_lotto').value = '';
                document.getElementById('qta').value = '';
                document.getElementById('causale').value = 'Scarico manuale';

                // Carica i dati del magazzino e i lotti disponibili
                caricaDatiMagazzino(idArticolo, idMg);

                // Mostra la modale
                const modal = new bootstrap.Modal(document.getElementById('modalMovimentoMagazzino'));
                modal.show();
            }

            function caricaDatiMagazzino(idArticolo, idMg) {
                fetch(`{{ url('utente/ajax/carico_scarico_magazzino') }}?id_articolo=${idArticolo}&id_mg=${idMg}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Imposta il nome del magazzino
                            document.getElementById('magazzino_nome').value = data.magazzino.descrizione;

                            // Imposta l'unità di misura
                            document.getElementById('um_movimento').textContent = data.articolo.um;

                            // Popola il select dei lotti
                            const lottoSelect = document.getElementById('lotto_esistente');
                            lottoSelect.innerHTML = '<option value="">Seleziona lotto esistente</option>';

                            data.lotti.forEach(lotto => {
                                const option = document.createElement('option');
                                option.value = lotto.lotto;
                                option.textContent = `${lotto.lotto} - Giacenza: ${lotto.giacenza} ${data.articolo.um}`;
                                option.dataset.scadenza = lotto.scadenza_lotto;
                                lottoSelect.appendChild(option);
                            });

                            // Aggiungi listener per compilare automaticamente in base al lotto selezionato
                            lottoSelect.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                if (selectedOption.value) {
                                    document.getElementById('lotto').value = selectedOption.value;
                                    if (selectedOption.dataset.scadenza) {
                                        document.getElementById('scadenza_lotto').value = selectedOption.dataset.scadenza;
                                    }
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Errore nel caricamento dei dati:', error));
            }


            $(document).ready(function() {
                // Controllo attivazione campi tempo medio in base alla selezione della fase
                $('input[name="fasi[]"]').on('change', function() {
                    var idFase = $(this).val();
                    var inputTempo = $('input[name="tempo_medio[' + idFase + ']"]');
                    if ($(this).is(':checked')) {
                        inputTempo.prop('disabled', false);
                    } else {
                        inputTempo.prop('disabled', true);
                    }
                });

                // Gestione click su aggiungi materiale
                $('.aggiungi-materiale').on('click', function() {
                    var idFase = $(this).data('fase');
                    $('#id_fase_materiale').val(idFase);

                    // Carica i materiali
                    $.ajax({
                        url: '{{ url("ajax/get_materiali") }}',
                        type: 'GET',
                        success: function(response) {
                            if(response.success) {
                                var options = '<option value="">Seleziona materiale</option>';
                                $.each(response.materiali, function(index, materiale) {
                                    options += '<option value="' + materiale.id + '">' + materiale.titolo + ' (' + materiale.um + ')</option>';
                                });
                                $('#id_materiale').html(options);
                                $('#aggiungiMaterialeModal').modal('show');
                            }
                        }
                    });
                });

                // Salvataggio materiale alla distinta base
                $('#salva-materiale').on('click', function() {
                    var idFase = $('#id_fase_materiale').val();
                    var idMateriale = $('#id_materiale').val();
                    var quantita = $('#quantita_materiale').val();

                    if(!idMateriale || !quantita) {
                        alert('Compila tutti i campi richiesti');
                        return;
                    }

                    $.ajax({
                        url: '{{ url("ajax/aggiungi_materiale_distinta_base") }}',
                        type: 'POST',
                        data: {
                            id_articolo: '{{ $articolo->id }}',
                            id_fase: idFase,
                            id_materiale: idMateriale,
                            qta: quantita,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                // Aggiorna la tabella
                                var html = '<tr>';
                                html += '<td>' + response.materiale.titolo + '</td>';
                                html += '<td>' + quantita + '</td>';
                                html += '<td>' + response.materiale.um + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="btn btn-danger btn-sm rimuovi-materiale" data-id="' + response.id + '">';
                                html += '<i class="ri-delete-bin-line align-bottom"></i>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';

                                $('#materiali-fase-' + idFase).append(html);
                                $('#aggiungiMaterialeModal').modal('hide');
                                $('#form-aggiungi-materiale')[0].reset();
                            }
                        }
                    });
                });

                // Rimozione materiale dalla distinta base
                $(document).on('click', '.rimuovi-materiale', function() {
                    var id = $(this).data('id');
                    var row = $(this).closest('tr');

                    if(confirm('Sei sicuro di voler rimuovere questo materiale dalla distinta base?')) {
                        $.ajax({
                            url: '{{ url("ajax/rimuovi_materiale_distinta_base") }}',
                            type: 'POST',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.success) {
                                    row.remove();
                                }
                            }
                        });
                    }
                });

                // Gestione modal aggiungi famiglia
                $('#aggiungi-famiglia').on('click', function() {
                    $('#aggiungiFamigliaModal').modal('show');
                });

                // Salvataggio nuova famiglia
                $('#salva-famiglia').on('click', function() {
                    var nome = $('#nome_famiglia').val();
                    var descrizione = $('#descrizione_famiglia').val();

                    if(!nome) {
                        alert('Il nome della famiglia è obbligatorio');
                        return;
                    }

                    $.ajax({
                        url: '{{ url("utente/aggiungi_famiglia") }}',
                        type: 'POST',
                        data: {
                            nome: nome,
                            descrizione: descrizione,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                var option = new Option(response.nome, response.id, true, true);
                                $('#id_famiglia').append(option).trigger('change');
                                $('#aggiungiFamigliaModal').modal('hide');
                                $('#form-aggiungi-famiglia')[0].reset();
                            }
                        }
                    });
                });

                // Gestione modal aggiungi categoria
                $('#aggiungi-categoria').on('click', function() {
                    $('#aggiungiCategoriaModal').modal('show');
                });

                // Salvataggio nuova categoria
                $('#salva-categoria').on('click', function() {
                    var nome = $('#nome_categoria').val();
                    var descrizione = $('#descrizione_categoria').val();

                    if(!nome) {
                        alert('Il nome della categoria è obbligatorio');
                        return;
                    }

                    $.ajax({
                        url: '{{ url("utente/aggiungi_categoria") }}',
                        type: 'POST',
                        data: {
                            nome: nome,
                            descrizione: descrizione,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                var option = new Option(response.nome, response.id, true, true);
                                $('#id_categoria').append(option).trigger('change');
                                $('#aggiungiCategoriaModal').modal('hide');
                                $('#form-aggiungi-categoria')[0].reset();
                            }
                        }
                    });
                });

                // Gestione attributi
                $('#aggiungi-attributo').on('click', function() {
                    var index = $('#tabella-attributi tbody tr').length;
                    var html = '<tr>';
                    html += '<td><input type="text" class="form-control" name="attributi[nuovo_' + index + '][nome]" placeholder="Nome attributo"></td>';
                    html += '<td><input type="text" class="form-control" name="attributi[nuovo_' + index + '][valore]" placeholder="Valore"></td>';
                    html += '<td>';
                    html += '<button type="button" class="btn btn-danger btn-sm rimuovi-attributo">';
                    html += '<i class="ri-delete-bin-line align-bottom"></i>';
                    html += '</button>';
                    html += '</td>';
                    html += '</tr>';

                    $('#tabella-attributi tbody').append(html);
                });

                // Rimozione attributo
                $(document).on('click', '.rimuovi-attributo', function() {
                    $(this).closest('tr').remove();
                });

                // Anteprima immagine caricata
                $("#immagine").change(function() {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-img').attr('src', e.target.result);
                    }
                    if (this.files[0]) {
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Funzione per eliminare file
                window.eliminaFile = function(id) {
                    if (confirm('Sei sicuro di voler eliminare questo file?')) {
                        $.post('{{ url("utente/elimina_file_articolo") }}', {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        }, function(data) {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Errore nell\'eliminazione del file');
                            }
                        });
                    }
                };
            });


            // Gestione del pulsante barcode
            document.getElementById('button-barcode').addEventListener('click', function() {
                const barcodeValue = document.getElementById('barcode').value;

                if (!barcodeValue) {
                    // Se il barcode è vuoto, genera un nuovo barcode
                    generaNuovoBarcode();
                } else {
                    // Se esiste già un barcode, mostra la preview e le opzioni di stampa
                    mostraPreviewBarcode(barcodeValue);
                }
            });

            // Funzione per generare un nuovo barcode
            function generaNuovoBarcode() {
                const articoloId = '{{ $articolo->id }}';

                // Effettua una richiesta AJAX per generare un nuovo barcode
                fetch(`{{ url('utente/ajax/genera_barcode') }}/${articoloId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Aggiorna il campo barcode con il nuovo valore
                            document.getElementById('barcode').value = data.barcode;

                            // Mostra notifica di successo
                            Swal.fire({
                                title: 'Barcode generato!',
                                text: 'Il barcode è stato generato con successo.',
                                icon: 'success',
                                confirmButtonText: 'Visualizza'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    mostraPreviewBarcode(data.barcode);
                                }
                            });
                        } else {
                            // Mostra errore
                            Swal.fire({
                                title: 'Errore',
                                text: data.message || 'Si è verificato un errore durante la generazione del barcode.',
                                icon: 'error',
                                confirmButtonText: 'Chiudi'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Errore:', error);
                        Swal.fire({
                            title: 'Errore',
                            text: 'Si è verificato un errore durante la generazione del barcode.',
                            icon: 'error',
                            confirmButtonText: 'Chiudi'
                        });
                    });
            }

            // Funzione per mostrare la preview del barcode e opzioni di stampa
            function mostraPreviewBarcode(barcodeValue) {
                // Crea una modal per mostrare il barcode
                Swal.fire({
                    title: 'Barcode',
                    html: `
            <div class="text-center mb-3">
                <img src="{{ url('utente/ajax/visualizza_barcode') }}?barcode=${encodeURIComponent(barcodeValue)}"
                     alt="Barcode" class="img-fluid">
                <p class="mt-2">${barcodeValue}</p>
            </div>
        `,
                    showCloseButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Stampa',
                    cancelButtonText: 'Chiudi',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Stampa il barcode usando MPdf
                        stampaBarcodeMPdf(barcodeValue);
                    }
                });
            }

            // Funzione per stampare il barcode usando MPdf
            function stampaBarcodeMPdf(barcodeValue) {
                // Apri la stampa in una nuova scheda
                window.open(`{{ url('utente/ajax/stampa_barcode') }}?barcode=${encodeURIComponent(barcodeValue)}`, '_blank');
            }

            // Aggiungi dopo il codice del barcode
            document.addEventListener('DOMContentLoaded', function() {
                // Gestione articoli alternativi
                const aggiungiAlternativa = document.getElementById('aggiungi-alternativa');
                if (aggiungiAlternativa) {
                    aggiungiAlternativa.addEventListener('click', function() {
                        // Mostra la modal
                        const modal = new bootstrap.Modal(document.getElementById('aggiungiAlternativaModal'));
                        modal.show();
                    });
                }

                // Gestione ricerca articoli
                const cercaArticolo = document.getElementById('cerca-articolo');
                if (cercaArticolo) {
                    cercaArticolo.addEventListener('input', function() {
                        const query = this.value;
                        if (query.length >= 3) {
                            cercaArticoli(query);
                        }
                    });
                }

                // Gestione rimozione alternativa
                document.querySelectorAll('.rimuovi-alternativa').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        if (confirm('Sei sicuro di voler rimuovere questa alternativa?')) {
                            rimuoviAlternativa(id);
                        }
                    });
                });
            });

            // Funzione per cercare articoli
            function cercaArticoli(query) {
                const tableBody = document.querySelector('#tabella-articoli-cercati tbody');
                const articoloId = {{ $articolo->id }}; // ID dell'articolo corrente

                // Effettua la richiesta AJAX per cercare gli articoli
                fetch(`{{ url('utente/ajax/cerca_articoli') }}?q=${encodeURIComponent(query)}&exclude=${articoloId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let html = '';

                            if (data.articoli.length > 0) {
                                data.articoli.forEach(articolo => {
                                    html += `
                        <tr>
                            <td>${articolo.codice_articolo}</td>
                            <td>${articolo.titolo}</td>
                            <td>€ ${parseFloat(articolo.prezzo).toFixed(2)}</td>
                            <td>${parseFloat(articolo.giacenza).toFixed(2)} ${articolo.um}</td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm" onclick="aggiungiAlternativa(${articoloId}, ${articolo.id})">
                                    <i class="ri-add-line align-bottom"></i> Aggiungi
                                </button>
                            </td>
                        </tr>
                    `;
                                });
                            } else {
                                html = '<tr><td colspan="5" class="text-center">Nessun articolo trovato</td></tr>';
                            }

                            tableBody.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Errore:', error);
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Errore nella ricerca</td></tr>';
                    });
            }

            // Funzione per aggiungere un'alternativa
            function aggiungiAlternativa(idArticolo, idAlternativa) {
                // Effettua la richiesta AJAX per aggiungere l'alternativa
                fetch('{{ url("utente/ajax/aggiungi_alternativa") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_articolo: idArticolo,
                        id_articolo_alternativo: idAlternativa
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Chiudi la modal
                            bootstrap.Modal.getInstance(document.getElementById('aggiungiAlternativaModal')).hide();

                            // Ricarica la pagina per mostrare la nuova alternativa
                            window.location.reload();
                        } else {
                            alert('Errore: ' + (data.message || 'Si è verificato un errore'));
                        }
                    })
                    .catch(error => {
                        console.error('Errore:', error);
                        alert('Si è verificato un errore durante l\'aggiunta dell\'alternativa');
                    });
            }

            // Funzione per rimuovere un'alternativa
            function rimuoviAlternativa(id) {
                // Effettua la richiesta AJAX per rimuovere l'alternativa
                fetch('{{ url("utente/ajax/rimuovi_alternativa") }}/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Ricarica la pagina per aggiornare la lista
                            window.location.reload();
                        } else {
                            alert('Errore: ' + (data.message || 'Si è verificato un errore'));
                        }
                    })
                    .catch(error => {
                        console.error('Errore:', error);
                        alert('Si è verificato un errore durante la rimozione dell\'alternativa');
                    });
            }
        </script>