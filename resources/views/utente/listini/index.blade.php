@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Listini Prezzi</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Gestione</a></li>
                            <li class="breadcrumb-item active">Listini Prezzi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-double-line me-1 align-middle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-1 align-middle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Gestione Listini</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ url('utente/listini/create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuovo Listino
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-listini" role="tab">
                                    <i class="ri-price-tag-3-line me-1 align-bottom"></i> Listini
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-clienti" role="tab">
                                    <i class="ri-user-2-line me-1 align-bottom"></i> Clienti e Listini
                                </a>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content">
                            <!-- Tab Listini -->
                            <div class="tab-pane active" id="tab-listini" role="tabpanel">
                                <table id="listini-table" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Descrizione</th>
                                        <th>Validità</th>
                                        <th>Stato</th>
                                        <th>Priorità</th>
                                        <th>Articoli</th>
                                        <th>Clienti</th>
                                        <th>Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listini as $listino)
                                        <tr>
                                            <td>{{ $listino->codice }}</td>
                                            <td>{{ $listino->descrizione }}</td>
                                            <td>
                                                @if($listino->data_inizio && $listino->data_fine)
                                                    Dal {{ date('d/m/Y', strtotime($listino->data_inizio)) }}
                                                    al {{ date('d/m/Y', strtotime($listino->data_fine)) }}
                                                @elseif($listino->data_inizio)
                                                    Dal {{ date('d/m/Y', strtotime($listino->data_inizio)) }}
                                                @elseif($listino->data_fine)
                                                    Fino al {{ date('d/m/Y', strtotime($listino->data_fine)) }}
                                                @else
                                                    Sempre valido
                                                @endif
                                            </td>
                                            <td>
                                                @if($listino->attivo)
                                                    <span class="badge bg-success">Attivo</span>
                                                @else
                                                    <span class="badge bg-danger">Disattivato</span>
                                                @endif
                                            </td>
                                            <td>{{ $listino->priorita }}</td>
                                            <td>{{ $listino->num_articoli }}</td>
                                            <td>{{ $listino->num_clienti }}</td>
                                            <td>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a href="{{ url('utente/listini/dettaglio/'.$listino->id) }}" class="dropdown-item">
                                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Visualizza
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modificaListinoModal"
                                                               onclick="modificaListino({{ $listino->id }}, '{{ $listino->codice }}', '{{ $listino->descrizione }}', '{{ $listino->data_inizio }}', '{{ $listino->data_fine }}', {{ $listino->priorita }}, {{ $listino->attivo }}, '{{ $listino->note }}')">
                                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="dropdown-item remove-item-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="$('#id_to_delete').val({{ $listino->id }})">
                                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Elimina
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tab Clienti e Listini -->
                            <div class="tab-pane" id="tab-clienti" role="tabpanel">
                                <table id="clienti-listini-table" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>P.IVA</th>
                                        <th>Listini Associati</th>
                                        <th>Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clienti_con_listini as $cliente)
                                        <tr>
                                            <td>{{ $cliente->ragione_sociale ?: $cliente->nome.' '.$cliente->cognome }}</td>
                                            <td>{{ $cliente->piva }}</td>
                                            <td>
                                                @if(count($cliente->listini) > 0)
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($cliente->listini as $listino_cliente)
                                                            <li class="list-group-item ps-0 border-0">
                                                                {{-- bottone/link --}}
                                                                <a  href="{{ url('utente/listini/dettaglio/'.$listino_cliente->id_listino) }}"
                                                                    class="btn btn-outline-primary btn-sm w-100 text-start"   {{-- puoi cambiare btn-outline-primary / btn-primary ecc. --}}
                                                                    role="button">
                                                                    {{ $listino_cliente->codice_listino }}
                                                                </a>

                                                                {{-- date di validità, se presenti --}}
                                                                @if($listino_cliente->data_inizio || $listino_cliente->data_fine)
                                                                    <small class="text-muted d-block mt-1">
                                                                        @if($listino_cliente->data_inizio && $listino_cliente->data_fine)
                                                                            Dal {{ date('d/m/Y', strtotime($listino_cliente->data_inizio)) }}
                                                                            al {{ date('d/m/Y', strtotime($listino_cliente->data_fine)) }}
                                                                        @elseif($listino_cliente->data_inizio)
                                                                            Dal {{ date('d/m/Y', strtotime($listino_cliente->data_inizio)) }}
                                                                        @elseif($listino_cliente->data_fine)
                                                                            Fino al {{ date('d/m/Y', strtotime($listino_cliente->data_fine)) }}
                                                                        @endif
                                                                    </small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Nessun listino associato</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="{{ url('utente/clienti/scheda/'.$cliente->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-fill align-bottom"></i> Scheda Cliente
                                                </a>
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
        </div>
    </div>
</div>

<!-- Modal Modifica Listino -->
<div class="modal fade" id="modificaListinoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Listino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form method="post" action="{{ url('utente/listini') }}">

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label for="edit_codice" class="form-label">Codice</label>
                        <input type="text" id="edit_codice" name="codice" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="edit_descrizione" class="form-label">Descrizione</label>
                        <input type="text" id="edit_descrizione" name="descrizione" class="form-control" required />
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="edit_data_inizio" class="form-label">Data Inizio</label>
                            <input type="date" id="edit_data_inizio" name="data_inizio" class="form-control" />
                        </div>
                        <div class="col-lg-6">
                            <label for="edit_data_fine" class="form-label">Data Fine</label>
                            <input type="date" id="edit_data_fine" name="data_fine" class="form-control" />
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="edit_priorita" class="form-label">Priorità</label>
                            <input type="number" id="edit_priorita" name="priorita" class="form-control" min="0" value="0" />
                            <div class="form-text">Valore più alto = priorità maggiore</div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-check form-switch form-switch-success mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_attivo" name="attivo">
                                <label class="form-check-label" for="edit_attivo">Listino Attivo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_note" class="form-label">Note</label>
                        <textarea id="edit_note" name="note" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary" name="modifica" value="1">Salva modifiche</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Elimina -->
<div class="modal fade zoomIn" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Conferma Eliminazione</h4>
                        <p class="text-muted mx-4 mb-0">Sei sicuro di voler eliminare questo listino?</p>
                        <p class="text-danger mx-4 mb-0">Non è possibile eliminare listini con articoli o clienti associati.</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <form method="post" action="{{ url('utente/listini') }}">

                        <input type="hidden" name="id" id="id_to_delete">
                        <button type="submit" class="btn w-sm btn-danger" name="elimina" value="1">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        // Inizializza DataTables per la tabella Listini
        $('#listini-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json'
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });

        // Inizializza DataTables per la tabella Clienti con Listini
        $('#clienti-listini-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json'
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });

        // Mantieni la tab attiva al refresh
        var activeTab = localStorage.getItem('activeListiniTab');
        if (activeTab) {
            $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        }

        // Salva la tab attiva quando l'utente cambia tab
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeListiniTab', $(e.target).attr('href'));
        });
    });

    function modificaListino(id, codice, descrizione, data_inizio, data_fine, priorita, attivo, note) {
        $('#edit_id').val(id);
        $('#edit_codice').val(codice);
        $('#edit_descrizione').val(descrizione);
        $('#edit_data_inizio').val(data_inizio);
        $('#edit_data_fine').val(data_fine);
        $('#edit_priorita').val(priorita);
        $('#edit_attivo').prop('checked', attivo == 1);
        $('#edit_note').val(note);
    }
</script>