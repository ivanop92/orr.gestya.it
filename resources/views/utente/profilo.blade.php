@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Profilo Aziendale</h4>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute top-100 start-100 translate-middle">
                                        <label for="immagine" class="btn btn-sm btn-primary btn-icon" aria-label="Upload Image">
                                            <i class="ri-camera-fill"></i>
                                        </label>
                                        <input type="file" class="d-none" id="immagine" name="immagine" accept="image/*">
                                    </div>
                                    <div class="avatar-xl">
                                        <img src="{{ asset($azienda->immagine ?? '/placehold_immagine.png') }}"
                                             alt="Profile"
                                             class="rounded-circle img-thumbnail user-profile-image"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ragione Sociale<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ragione_sociale" value="{{ $azienda->ragione_sociale }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Partita IVA<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="partita_iva" value="{{ $azienda->partita_iva }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Indirizzo<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="indirizzo" value="{{ $azienda->indirizzo }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Comune<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="comune" value="{{ $azienda->comune }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Provincia<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="provincia" value="{{ $azienda->provincia }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">CAP<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="cap" value="{{ $azienda->cap }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Regione</label>
                                <input type="text" class="form-control" name="regione" value="{{ $azienda->regione }}">
                            </div>
                        </div>
                    </div>


                    <!-- Aggiungi i nuovi campi -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Capitale Sociale</label>
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="capitale_sociale"
                                           value="{{ number_format($azienda->capitale_sociale ?? 0, 2, ',', '.') }}"
                                           placeholder="0,00">
                                    <span class="input-group-text">i.v.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nr. REA</label>
                                <input type="text" class="form-control"
                                       name="nr_rea"
                                       value="{{ $azienda->nr_rea }}"
                                       placeholder="AV - 000000">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Codice SDI<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="codice_sdi" value="{{ $azienda->codice_sdi }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">PEC<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="pec" value="{{ $azienda->pec }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Email Ricezione Fatture</label>
                                <input type="email" class="form-control" name="email_ricezione_fatture" value="{{ $azienda->email_ricezione_fatture }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Regime Fiscale<span class="text-danger">*</span></label>
                                <select class="form-select" name="regime_fiscale" required data-choices data-choices-search-true>
                                    @foreach($regimi_fiscali as $regime)
                                        <option value="{{ $regime->codice }}" {{ $azienda->regime_fiscale == $regime->codice ? 'selected' : '' }}>
                                            {{ $regime->codice }} - {{ $regime->descrizione }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Modalità Pagamento<span class="text-danger">*</span></label>
                                <select class="form-select" name="modalita_pagamento" required data-choices data-choices-search-true>
                                    @foreach($modalita_pagamenti as $modalita)
                                        <option value="{{ $modalita->codice }}" {{ $azienda->modalita_pagamento == $modalita->codice ? 'selected' : '' }}>
                                            {{ $modalita->codice }} - {{ $modalita->descrizione }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Natura<span class="text-danger">*</span></label>
                                <select class="form-select" name="natura"  data-choices data-choices-search-true>
                                    <option value="">Nessuna Natura Predefinita</option>

                                    @foreach($nature as $natura)
                                        <option value="{{ $natura->id }}" {{ $azienda->natura == $natura->id ? 'selected' : '' }}>
                                            {{ $natura->natura }} - {{ $natura->descrizione }} - {{ $natura->descrizione_pdf }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">IBAN</label>
                                <input type="text" class="form-control" name="iban" value="{{ $azienda->iban }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Istituto Finanziario</label>
                                <input type="text" class="form-control" name="istituto_finanziario" value="{{ $azienda->istituto_finanziario }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <input type="submit" name="modifica" value="Modifica" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<style>
    .user-profile-image {
        transition: all 0.3s ease;
    }

    .user-profile-image:hover {
        opacity: 0.8;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-left: none;
    }
</style>

<script>
    document.getElementById('immagine').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.user-profile-image').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Formattazione del campo capitale sociale
    document.querySelector('[name="capitale_sociale"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (parseInt(value) / 100).toFixed(2);
        e.target.value = new Intl.NumberFormat('it-IT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    });
</script>