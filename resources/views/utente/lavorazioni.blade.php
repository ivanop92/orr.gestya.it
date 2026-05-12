@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Catalogo Lavorazioni</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Produzione</a></li>
                            <li class="breadcrumb-item active">Catalogo Lavorazioni</li>
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Template di lavorazione</h5>
                                <small class="text-muted">Macro di righe pre-confezionate da applicare a preventivi, certificati, ODL</small>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_lavorazione">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Nuova Lavorazione
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="lavorazioni-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th class="text-end">Totale</th>
                                <th>Stato</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lavorazioni as $l)
                                <tr>
                                    <td><strong>{{ $l->codice }}</strong></td>
                                    <td>{{ $l->descrizione }}</td>
                                    <td class="text-end">€ {{ number_format($l->totale, 2, ',', '.') }}</td>
                                    <td>
                                        @if($l->attivo)
                                            <span class="badge bg-success">Attiva</span>
                                        @else
                                            <span class="badge bg-secondary">Inattiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="/utente/dettaglio_lavorazione/{{ $l->id }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Apri / Modifica
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="post" onsubmit="return confirm('Eliminare la lavorazione {{ $l->descrizione }} e tutte le sue righe?')" style="display:block; margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $l->id }}">
                                                        <button type="submit" name="elimina" value="1" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Elimina
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
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

<div class="modal fade" id="modal_aggiungi_lavorazione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title">Nuova Lavorazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Codice</label>
                            <input type="text" name="codice" class="form-control" placeholder="es. LAV-001">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Descrizione <b style="color:red">*</b></label>
                            <input type="text" name="descrizione" class="form-control" placeholder="es. Sostituzione Suole Carro Tipo X" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi" value="1" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Crea e Aggiungi Righe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#lavorazioni-datatable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/it-IT.json' },
                pageLength: 25,
                order: [[1, 'asc']]
            });
        }
    });
</script>

@include('utente.common.footer')
