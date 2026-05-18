@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0"><i class="ri-add-line me-2"></i>Nuovo Intervento Manutenzione</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/utente/interventi">Interventi</a></li>
                            <li class="breadcrumb-item active">Nuovo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-10">

                <div class="alert alert-info">
                    <i class="ri-information-line me-1"></i>
                    <strong>Step 1 — Ufficio:</strong> apri il ticket di intervento con cliente, vagone e motivo del rientro. Le righe di lavoro verranno aggiunte negli step successivi.
                </div>

                <form method="post" autocomplete="off">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Dati Intervento</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente <b style="color:red">*</b></label>
                                    <select name="id_cliente" class="form-select" required>
                                        <option value="">— Seleziona cliente —</option>
                                        @foreach($clienti as $c)
                                            <option value="{{ $c->id }}">{{ $c->ragione_sociale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Data apertura</label>
                                    <input type="date" name="data_apertura" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Priorità</label>
                                    <select name="priorita" class="form-select">
                                        <option value="bassa">Bassa</option>
                                        <option value="media" selected>Media</option>
                                        <option value="alta">Alta</option>
                                    </select>
                                </div>

                                @if(count($vagoni) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label">Vagone</label>
                                        <select name="id_vagone" class="form-select">
                                            <option value="">— Nessun vagone in anagrafica —</option>
                                            @foreach($vagoni as $v)
                                                <option value="{{ $v->id }}">{{ $v->codice }}@if($v->tipo) ({{ $v->tipo }})@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">Automezzo (testo libero, se non in anagrafica)</label>
                                    <input type="text" name="automezzo" class="form-control" placeholder="numero carro o identificativo">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Località intervento</label>
                                    <input type="text" name="localita" class="form-control" placeholder="es. Marcianise">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Motivo del rientro (sintomo)</label>
                                    <input type="text" name="reason_intake" class="form-control" placeholder="es. Avaria sale, perdita olio, rumore freno...">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="note aggiuntive"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <a href="/utente/interventi" class="btn btn-light">Annulla</a>
                        <button type="submit" name="salva" value="1" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Apri Intervento
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')
