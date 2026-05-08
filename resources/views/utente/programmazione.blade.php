@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Programmazione Produzione</h4>
                </div>
            </div>
        </div>

        @if(!empty($flash_msg) && $flash_type === 'success')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ $flash_msg }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(!empty($flash_msg) && $flash_type === 'error')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errore!</strong> {{ $flash_msg }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Ordini da Processare -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ordini da Processare</h5>
                    </div>
                    <div class="card-body">
                        <table id="ordini-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>N° Ordine</th>
                                <th>Data Ordine</th>
                                <th>Data Consegna</th>
                                <th>Prodotto</th>
                                <th>Quantità</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ordini as $ordine)
                                <tr>
                                    <td>{{ $ordine->cliente }}</td>
                                    <td>{{ $ordine->numero_doc }}</td>
                                    <td>{{ date('d/m/Y', strtotime($ordine->data_doc)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($ordine->data_consegna)) }}</td>
                                    <td>{{ $ordine->articolo }}</td>
                                    <td>{{ $ordine->qta }}</td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id_dotes" value="{{ $ordine->id }}">
                                            <input type="hidden" name="id_dorig" value="{{ $ordine->id_dorig }}">
                                            <button type="submit" name="crea_odl" class="btn btn-sm btn-success" value="Crea ODL">
                                                <i class="ri-add-line"></i> Crea ODL
                                            </button>
                                        </form>
                                        <a href="{{ url('utente/modifica_documento/'.$ordine->id) }}" class="btn btn-sm btn-info">
                                            <i class="ri-edit-line"></i> Modifica
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

        <!-- Ordini in Lavorazione -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ordini in Lavorazione</h5>
                    </div>
                    <div class="card-body">
                        <table id="lavorazione-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>N° Ordine</th>
                                <th>Data Consegna</th>
                                <th>Prodotto</th>
                                <th>Quantità</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ordini_in_lavorazione as $ordine)
                                <tr>
                                    <td>{{ $ordine->cliente }}</td>
                                    <td>{{ $ordine->numero_doc }}</td>
                                    <td>{{ date('d/m/Y', strtotime($ordine->data_consegna)) }}</td>
                                    <td>{{ $ordine->articolo }}</td>
                                    <td>{{ $ordine->qta }}</td>
                                    <td>
                                        @if($ordine->stato_odl == 0)
                                            <span class="badge bg-warning">Da Iniziare</span>
                                        @elseif($ordine->stato_odl == 1)
                                            <span class="badge bg-info">In Lavorazione</span>
                                        @elseif($ordine->stato_odl == 2)
                                            <span class="badge bg-success">Completato</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('utente/dettaglio_odl/'.$ordine->id_odl) }}" class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line"></i> ODL
                                        </a>
                                        <form method="post" style="display:inline;"
                                              onsubmit="return confirm('Sei sicuro di voler eliminare questo ODL?');">
                                            <input type="hidden" name="id_odl" value="{{ $ordine->id_odl }}">
                                            <input type="hidden" name="id_dorig" value="{{ $ordine->id_dorig }}">
                                            <button type="submit" name="elimina_odl" value="Elimina ODL"
                                                    class="btn btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i> Elimina ODL
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('utente.common.footer')

<script>
    $(document).ready(function() {
        $('#ordini-table, #lavorazione-table').DataTable({
            order: [[3, 'asc']], // Ordina per data consegna
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            }
        });

        @if(!empty($flash_msg) && $flash_type === 'success')
            toastr.options = { closeButton: true, timeOut: 6000, positionClass: 'toast-top-right' };
            toastr.success(@json($flash_msg));
        @endif
        @if(!empty($flash_msg) && $flash_type === 'error')
            toastr.options = { closeButton: true, timeOut: 0, extendedTimeOut: 0, positionClass: 'toast-top-right' };
            toastr.error(@json($flash_msg));
        @endif

        @if(!empty($flash_msg))
            // Pulisco l'URL dai parametri msg/msg_type cosi' un refresh non rimostra l'alert
            if (window.history.replaceState) {
                window.history.replaceState({}, '', '/utente/programmazione');
            }
        @endif
    });
</script>