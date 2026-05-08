@include('utente.common.header')

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Gestione Commesse</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/utente/index">Dashboard</a></li>
                                <li class="breadcrumb-item active">Commesse</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-lista" role="tab">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Lista Commesse
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-nuova" role="tab">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Nuova Commessa
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab Lista -->
                <div class="tab-pane active" id="tab-lista" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="scroll-horizontal" class="table table-bordered table-hover nowrap" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Data Apertura</th>
                                            <th>Data Chiusura</th>
                                            <th>Stato</th>
                                            <th>Budget</th>
                                            <th>Costi</th>
                                            <th>Ricavi</th>
                                            <th style="width:150px;">Azioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($commesse as $c)
                                            <tr>
                                                <td>{{ $c->codice_commessa }}</td>
                                                <td>{{ $c->descrizione }}</td>
                                                <td>{{ date('d/m/Y', strtotime($c->data_apertura)) }}</td>
                                                <td>{{ $c->data_chiusura ? date('d/m/Y', strtotime($c->data_chiusura)) : '-' }}</td>
                                                <td>
                                                    @if($c->stato == 'aperta')
                                                        <span class="badge bg-success">Aperta</span>
                                                    @elseif($c->stato == 'in_corso')
                                                        <span class="badge bg-warning">In Corso</span>
                                                    @elseif($c->stato == 'completata')
                                                        <span class="badge bg-info">Completata</span>
                                                    @else
                                                        <span class="badge bg-danger">Annullata</span>
                                                    @endif
                                                </td>
                                                <td>&euro; {{ number_format($c->budget, 2, ',', '.') }}</td>
                                                <td>&euro; {{ number_format($c->costi, 2, ',', '.') }}</td>
                                                <td>&euro; {{ number_format($c->ricavi, 2, ',', '.') }}</td>
                                                <td>
                                                    <a href="/commesse/{{ $c->id }}/dashboard" class="btn btn-sm btn-primary" title="Dashboard">
                                                        <i class="ri-dashboard-line"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalModifica{{ $c->id }}" title="Modifica">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalElimina{{ $c->id }}" title="Elimina">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal Modifica -->
                                            <div class="modal fade" id="modalModifica{{ $c->id }}" tabindex="-1" aria-labelledby="modalModificaLabel{{ $c->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalModificaLabel{{ $c->id }}">Modifica Commessa</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="/commesse" method="post">
                                                            @csrf
                                                            <input type="hidden" name="modifica" value="1">
                                                            <input type="hidden" name="id" value="{{ $c->id }}">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="codice_commessa" class="form-label">Codice Commessa</label>
                                                                    <input type="text" class="form-control" id="codice_commessa" name="codice_commessa" value="{{ $c->codice_commessa }}" readonly>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="descrizione" class="form-label">Descrizione</label>
                                                                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required>{{ $c->descrizione }}</textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="data_apertura" class="form-label">Data Apertura</label>
                                                                        <input type="date" class="form-control" id="data_apertura" name="data_apertura" value="{{ $c->data_apertura }}" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="data_chiusura" class="form-label">Data Chiusura</label>
                                                                        <input type="date" class="form-control" id="data_chiusura" name="data_chiusura" value="{{ $c->data_chiusura }}">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="stato" class="form-label">Stato</label>
                                                                    <select class="form-select" id="stato" name="stato" required>
                                                                        <option value="aperta" {{ $c->stato == 'aperta' ? 'selected' : '' }}>Aperta</option>
                                                                        <option value="in_corso" {{ $c->stato == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                                                                        <option value="completata" {{ $c->stato == 'completata' ? 'selected' : '' }}>Completata</option>
                                                                        <option value="annullata" {{ $c->stato == 'annullata' ? 'selected' : '' }}>Annullata</option>
                                                                    </select>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="budget" class="form-label">Budget</label>
                                                                        <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="{{ $c->budget }}" required>
                                                                    </div>
                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="costi" class="form-label">Costi</label>
                                                                        <input type="number" step="0.01" class="form-control" id="costi" name="costi" value="{{ $c->costi }}" required>
                                                                    </div>
                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="ricavi" class="form-label">Ricavi</label>
                                                                        <input type="number" step="0.01" class="form-control" id="ricavi" name="ricavi" value="{{ $c->ricavi }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="note" class="form-label">Note</label>
                                                                    <textarea class="form-control" id="note" name="note" rows="3">{{ $c->note }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Elimina -->
                                            <div class="modal fade" id="modalElimina{{ $c->id }}" tabindex="-1" aria-labelledby="modalEliminaLabel{{ $c->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalEliminaLabel{{ $c->id }}">Conferma Eliminazione</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Sei sicuro di voler eliminare la commessa <strong>{{ $c->codice_commessa }}</strong>?<br>
                                                            Questa operazione non può essere annullata.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                            <form action="/commesse" method="post">
                                                                @csrf
                                                                <input type="hidden" name="elimina" value="1">
                                                                <input type="hidden" name="id" value="{{ $c->id }}">
                                                                <button type="submit" class="btn btn-danger">Elimina</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Nuova Commessa -->
                <div class="tab-pane" id="tab-nuova" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="/commesse" method="post">
                                        @csrf
                                        <input type="hidden" name="aggiungi" value="1">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="descrizione" class="form-label">Descrizione</label>
                                                <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="data_apertura" class="form-label">Data Apertura</label>
                                                <input type="date" class="form-control" id="data_apertura" name="data_apertura" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="data_chiusura" class="form-label">Data Chiusura (opzionale)</label>
                                                <input type="date" class="form-control" id="data_chiusura" name="data_chiusura">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="budget" class="form-label">Budget</label>
                                                <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="costi" class="form-label">Costi</label>
                                                <input type="number" step="0.01" class="form-control" id="costi" name="costi" value="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="ricavi" class="form-label">Ricavi</label>
                                                <input type="number" step="0.01" class="form-control" id="ricavi" name="ricavi" value="0.00" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note" class="form-label">Note</label>
                                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">Crea Commessa</button>
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


@include('utente.common.footer')

<script>
    $(document).ready(function() {
        $('#scroll-horizontal').DataTable({
            scrollX: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Italian.json'
            }
        });
    });
</script> 