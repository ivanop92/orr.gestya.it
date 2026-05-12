@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Vagoni</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Anagrafiche</a></li>
                            <li class="breadcrumb-item active">Vagoni</li>
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
                                <h5 class="card-title mb-0">Elenco Vagoni</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_vagone">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Vagone
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="vagoni-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Numero UIC</th>
                                <th>Ultima Revisione</th>
                                <th>Stato</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($vagoni as $v)
                                <tr>
                                    <td><strong>{{ $v->codice }}</strong></td>
                                    <td>{{ $v->tipo }}</td>
                                    <td>{{ $v->cliente_ragione_sociale }}</td>
                                    <td>{{ $v->numero_uic }}</td>
                                    <td>
                                        @if($v->data_ultima_revisione_generale)
                                            {{ \Carbon\Carbon::parse($v->data_ultima_revisione_generale)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->attivo)
                                            <span class="badge bg-success">Attivo</span>
                                        @else
                                            <span class="badge bg-secondary">Inattivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="/utente/dettaglio_vagone/{{ $v->id }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="post" onsubmit="return confirm('Eliminare il vagone {{ $v->codice }}?')" style="display:block; margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $v->id }}">
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

<div class="modal fade" id="modal_aggiungi_vagone" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title">Aggiungi Nuovo Vagone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Codice / Identificativo <b style="color:red">*</b></label>
                            <input type="text" name="codice" class="form-control" placeholder="es. 12345 oppure ABC-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <input type="text" name="tipo" class="form-control" placeholder="es. Carro merci, Cisterna, Carrozza passeggeri">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Cliente proprietario</label>
                            <select name="id_cliente" class="form-select">
                                <option value="">— Nessun cliente —</option>
                                @foreach($clienti as $c)
                                    <option value="{{ $c->id }}">{{ $c->ragione_sociale }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <hr class="my-2">
                            <h6 class="text-muted mb-0">Dati ferroviari (opzionali)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Numero UIC (12 cifre)</label>
                            <input type="text" name="numero_uic" class="form-control" placeholder="80 80 0 000 000-0" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Immatricolazione</label>
                            <input type="date" name="data_immatricolazione" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ultima Revisione Generale</label>
                            <input type="date" name="data_ultima_revisione_generale" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Intervallo Revisione (mesi)</label>
                            <input type="number" name="intervallo_revisione_mesi" class="form-control" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Peso a vuoto (kg)</label>
                            <input type="number" name="peso_a_vuoto_kg" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Portata massima (kg)</label>
                            <input type="number" name="portata_massima_kg" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lunghezza (metri)</label>
                            <input type="number" name="lunghezza_metri" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi" value="1" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Salva Vagone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#vagoni-datatable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/it-IT.json' },
                pageLength: 25,
                order: [[0, 'asc']]
            });
        }
    });
</script>

@include('utente.common.footer')
