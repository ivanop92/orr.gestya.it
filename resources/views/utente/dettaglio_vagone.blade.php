@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Vagone: {{ $vagone->codice }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/utente/vagoni">Vagoni</a></li>
                            <li class="breadcrumb-item active">{{ $vagone->codice }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errore!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-xxl-10">
                <form method="post" autocomplete="off">
                    @csrf
                    <input type="hidden" name="id" value="{{ $vagone->id }}">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-train-line me-2"></i>Anagrafica Vagone</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Codice / Identificativo <b style="color:red">*</b></label>
                                    <input type="text" name="codice" class="form-control" value="{{ $vagone->codice }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipo</label>
                                    <input type="text" name="tipo" class="form-control" value="{{ $vagone->tipo }}" placeholder="es. Carro merci, Cisterna, Carrozza passeggeri">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Cliente proprietario</label>
                                    <select name="id_cliente" class="form-select">
                                        <option value="">— Nessun cliente —</option>
                                        @foreach($clienti as $c)
                                            <option value="{{ $c->id }}" {{ $vagone->id_cliente == $c->id ? 'selected' : '' }}>{{ $c->ragione_sociale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="attivo" name="attivo" value="1" {{ $vagone->attivo ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attivo">Vagone attivo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-tools-line me-2"></i>Dati ferroviari</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Numero UIC (12 cifre)</label>
                                    <input type="text" name="numero_uic" class="form-control" value="{{ $vagone->numero_uic }}" placeholder="80 80 0 000 000-0" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data Immatricolazione</label>
                                    <input type="date" name="data_immatricolazione" class="form-control" value="{{ $vagone->data_immatricolazione }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ultima Revisione Generale</label>
                                    <input type="date" name="data_ultima_revisione_generale" class="form-control" value="{{ $vagone->data_ultima_revisione_generale }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Intervallo Revisione (mesi)</label>
                                    <input type="number" name="intervallo_revisione_mesi" class="form-control" min="0" value="{{ $vagone->intervallo_revisione_mesi }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Peso a vuoto (kg)</label>
                                    <input type="number" name="peso_a_vuoto_kg" class="form-control" step="0.01" min="0" value="{{ $vagone->peso_a_vuoto_kg }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Portata massima (kg)</label>
                                    <input type="number" name="portata_massima_kg" class="form-control" step="0.01" min="0" value="{{ $vagone->portata_massima_kg }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lunghezza (metri)</label>
                                    <input type="number" name="lunghezza_metri" class="form-control" step="0.01" min="0" value="{{ $vagone->lunghezza_metri }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" rows="3">{{ $vagone->note }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <a href="/utente/vagoni" class="btn btn-light">Annulla</a>
                        <button type="submit" name="modifica" value="1" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')
