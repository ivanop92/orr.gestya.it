@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dettaglio Fornitore</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('utente/fornitori') }}">Fornitori</a></li>
                            <li class="breadcrumb-item active">Dettaglio Fornitore</li>
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

        <div class="row">
            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                <img src="{{ URL::asset($fornitore->immagine ?? 'assets/images/users/user-dummy-img.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="Fornitore">
                            </div>
                            <h5 class="fs-16 mb-1">{{ $fornitore->ragione_sociale }}</h5>
                            <p class="text-muted mb-0">{{ $fornitore->piva }}</p>
                            <p class="text-muted mb-0">Codice: {{ $fornitore->cd_cf ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Contatti</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">Email:</th>
                                    <td class="text-muted">{{ $fornitore->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">PEC:</th>
                                    <td class="text-muted">{{ $fornitore->pec ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Telefono:</th>
                                    <td class="text-muted">{{ $fornitore->telefono ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Indirizzo:</th>
                                    <td class="text-muted">
                                        {{ $fornitore->indirizzo ?? '' }}<br>
                                        {{ $fornitore->cap ?? '' }} {{ $fornitore->comune ?? '' }} @if($fornitore->provincia ?? '')({{ $fornitore->provincia }})@endif
                                    </td>
                                </tr>
                                @if($fornitore->referente ?? '')
                                <tr>
                                    <th class="ps-0" scope="row">Referente:</th>
                                    <td class="text-muted">
                                        {{ $fornitore->referente }}
                                        @if($fornitore->telefono_referente ?? '')
                                            <br>Tel: {{ $fornitore->telefono_referente }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#documenti" role="tab">
                                    <i class="fas fa-file-invoice me-1 align-bottom"></i> Documenti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#info" role="tab">
                                    <i class="fas fa-info-circle me-1 align-bottom"></i> Informazioni
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sedi" role="tab">
                                    <i class="fas fa-map-marker-alt me-1 align-bottom"></i> Sedi
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Tab Documenti -->
                            <div class="tab-pane active" id="documenti" role="tabpanel">
                                @if(count($documenti) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Numero</th>
                                                    <th>Data</th>
                                                    <th>Totale</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($documenti as $doc)
                                                <tr>
                                                    <td><span class="badge bg-primary">{{ $doc->cd_do }}</span></td>
                                                    <td>{{ $doc->numero_doc }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($doc->data_doc)) }}</td>
                                                    <td>&euro; {{ number_format($doc->totale ?? 0, 2, ',', '.') }}</td>
                                                    <td>
                                                        <a href="/utente/modifica_documento/{{ $doc->id }}" class="btn btn-sm btn-soft-primary">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line me-2"></i> Nessun documento associato a questo fornitore.
                                    </div>
                                @endif
                            </div>

                            <!-- Tab Informazioni -->
                            <div class="tab-pane" id="info" role="tabpanel">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Dati Fiscali</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless mb-0">
                                                        <tbody>
                                                        <tr>
                                                            <th class="ps-0">Partita IVA:</th>
                                                            <td class="text-muted">{{ $fornitore->piva ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">Codice Fiscale:</th>
                                                            <td class="text-muted">{{ $fornitore->cf ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">Codice SDI:</th>
                                                            <td class="text-muted">{{ $fornitore->sdi ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">PEC:</th>
                                                            <td class="text-muted">{{ $fornitore->pec ?? '-' }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Sede Principale</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless mb-0">
                                                        <tbody>
                                                        <tr>
                                                            <th class="ps-0">Indirizzo:</th>
                                                            <td class="text-muted">{{ $fornitore->indirizzo ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">Comune:</th>
                                                            <td class="text-muted">{{ $fornitore->comune ?? '-' }} @if($fornitore->provincia ?? '')({{ $fornitore->provincia }})@endif</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">CAP:</th>
                                                            <td class="text-muted">{{ $fornitore->cap ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0">Nazione:</th>
                                                            <td class="text-muted">{{ $fornitore->nazione ?? 'IT' }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Sedi -->
                            <div class="tab-pane" id="sedi" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Sedi</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAggiungiSede">
                                        <i class="ri-add-line me-1"></i> Aggiungi Sede
                                    </button>
                                </div>

                                @if(count($sedi) > 0)
                                    <div class="row g-3">
                                        @foreach($sedi as $sede)
                                        <div class="col-md-6">
                                            <div class="card border h-100">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i> {{ $sede->nome }}</h6>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-soft-primary" onclick="modificaSede({{ json_encode($sede) }})">
                                                            <i class="ri-pencil-line"></i>
                                                        </button>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Eliminare questa sede?')">
                                                            @csrf
                                                            <input type="hidden" name="elimina_sede" value="1">
                                                            <input type="hidden" name="id_sede" value="{{ $sede->id }}">
                                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="ri-delete-bin-line"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-borderless table-sm mb-0">
                                                        <tr>
                                                            <th style="width:100px">Indirizzo:</th>
                                                            <td>{{ $sede->indirizzo ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Città:</th>
                                                            <td>{{ $sede->cap }} {{ $sede->comune }} @if($sede->provincia)({{ $sede->provincia }})@endif</td>
                                                        </tr>
                                                        @if($sede->telefono)
                                                        <tr>
                                                            <th>Telefono:</th>
                                                            <td>{{ $sede->telefono }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($sede->email)
                                                        <tr>
                                                            <th>Email:</th>
                                                            <td>{{ $sede->email }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($sede->note)
                                                        <tr>
                                                            <th>Note:</th>
                                                            <td>{{ $sede->note }}</td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="ri-information-line me-2"></i> Nessuna sede registrata. Clicca "Aggiungi Sede" per inserirne una.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Sede -->
<div class="modal fade" id="modalAggiungiSede" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-map-pin-add-line me-1"></i> Aggiungi Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome Sede <b style="color:red">*</b></label>
                            <input type="text" name="nome_sede" class="form-control" placeholder="es. Sede Legale, Magazzino, Filiale Nord..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo_sede" class="form-control" placeholder="Via/Piazza...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap_sede" class="form-control" placeholder="CAP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comune</label>
                            <input type="text" name="comune_sede" class="form-control" placeholder="Comune">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prov.</label>
                            <input type="text" name="provincia_sede" class="form-control" placeholder="Prov." maxlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono_sede" class="form-control" placeholder="Telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_sede" class="form-control" placeholder="Email">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note_sede" class="form-control" rows="2" placeholder="Note..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_sede" value="1" class="btn btn-primary">Salva Sede</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Sede -->
<div class="modal fade" id="modalModificaSede" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                @csrf
                <input type="hidden" name="id_sede" id="mod_id_sede">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-pencil-line me-1"></i> Modifica Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome Sede <b style="color:red">*</b></label>
                            <input type="text" name="nome_sede" id="mod_nome_sede" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo_sede" id="mod_indirizzo_sede" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CAP</label>
                            <input type="text" name="cap_sede" id="mod_cap_sede" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comune</label>
                            <input type="text" name="comune_sede" id="mod_comune_sede" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prov.</label>
                            <input type="text" name="provincia_sede" id="mod_provincia_sede" class="form-control" maxlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono_sede" id="mod_telefono_sede" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_sede" id="mod_email_sede" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note_sede" id="mod_note_sede" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="modifica_sede" value="1" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function modificaSede(sede) {
    document.getElementById('mod_id_sede').value = sede.id;
    document.getElementById('mod_nome_sede').value = sede.nome || '';
    document.getElementById('mod_indirizzo_sede').value = sede.indirizzo || '';
    document.getElementById('mod_cap_sede').value = sede.cap || '';
    document.getElementById('mod_comune_sede').value = sede.comune || '';
    document.getElementById('mod_provincia_sede').value = sede.provincia || '';
    document.getElementById('mod_telefono_sede').value = sede.telefono || '';
    document.getElementById('mod_email_sede').value = sede.email || '';
    document.getElementById('mod_note_sede').value = sede.note || '';
    new bootstrap.Modal(document.getElementById('modalModificaSede')).show();
}
</script>

@include('utente.common.footer')
