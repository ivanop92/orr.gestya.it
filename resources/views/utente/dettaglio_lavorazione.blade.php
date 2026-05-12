@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Lavorazione: {{ $lavorazione->descrizione }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/utente/lavorazioni">Catalogo Lavorazioni</a></li>
                            <li class="breadcrumb-item active">{{ $lavorazione->codice ?: '#'.$lavorazione->id }}</li>
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

        {{-- Card intestazione --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="mdi mdi-package-variant me-2"></i>Intestazione</h5>
            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Codice</label>
                            <input type="text" name="codice" class="form-control" value="{{ $lavorazione->codice }}">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Descrizione <b style="color:red">*</b></label>
                            <input type="text" name="descrizione" class="form-control" value="{{ $lavorazione->descrizione }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Stato</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="attivo" name="attivo" value="1" {{ $lavorazione->attivo ? 'checked' : '' }}>
                                <label class="form-check-label" for="attivo">Attiva</label>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" name="modifica_testata" value="1" class="btn btn-sm btn-soft-success">
                                <i class="ri-save-line me-1"></i> Salva Intestazione
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card righe --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0"><i class="ri-list-check-2 me-2"></i>Righe Lavorazione</h5>
                        <small class="text-muted">Trascina le righe per riordinarle. L'ordine sarà applicato anche nei documenti che usano questo template.</small>
                    </div>
                    <div class="flex-shrink-0">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_riga">
                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Riga
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th style="width:32px;"></th>
                            <th>Servizio</th>
                            <th>Codice</th>
                            <th>Descrizione</th>
                            <th class="text-end">Qta</th>
                            <th class="text-end">Min.</th>
                            <th class="text-end">P.U.</th>
                            <th class="text-end">IVA%</th>
                            <th class="text-end">Imponibile</th>
                            <th class="text-end">Materiale</th>
                            <th style="width:100px;">Azioni</th>
                        </tr>
                        </thead>
                        <tbody id="righe-sortable">
                        @foreach($righe as $r)
                            <tr data-id="{{ $r->id }}"
                                data-servizio="{{ $r->servizio }}"
                                data-codice="{{ $r->codice }}"
                                data-setup_tank="{{ $r->setup_tank }}"
                                data-descrizione="{{ $r->descrizione }}"
                                data-attivita="{{ $r->attivita }}"
                                data-qta="{{ $r->qta }}"
                                data-minuti="{{ $r->minuti }}"
                                data-pu="{{ $r->pu }}"
                                data-aliquota="{{ $r->aliquota }}"
                                data-materiale="{{ $r->materiale }}"
                                data-descrizione_materiale="{{ $r->descrizione_materiale }}">
                                <td class="text-center" style="cursor:grab;"><i class="ri-drag-move-2-line text-muted"></i></td>
                                <td>{{ $r->servizio }}</td>
                                <td>{{ $r->codice }}</td>
                                <td>{{ $r->descrizione }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format($r->qta,2,',','.'), '0'), ',') }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format($r->minuti,2,',','.'), '0'), ',') }}</td>
                                <td class="text-end">€ {{ number_format($r->pu,2,',','.') }}</td>
                                <td class="text-end">{{ $r->aliquota }}</td>
                                <td class="text-end"><strong>€ {{ number_format($r->imponibile,2,',','.') }}</strong></td>
                                <td class="text-end">{{ $r->materiale > 0 ? '€ '.number_format($r->materiale,2,',','.') : '' }}</td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="apriModificaRiga({{ $r->id }}); return false;">
                                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica
                                                </a>
                                            </li>
                                            <li>
                                                <form method="post" onsubmit="return confirm('Eliminare questa riga?')" style="display:block; margin:0;">
                                                    @csrf
                                                    <input type="hidden" name="id_riga" value="{{ $r->id }}">
                                                    <button type="submit" name="elimina_riga" value="1" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Elimina
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($righe) === 0)
                            <tr><td colspan="11" class="text-center text-muted py-4">Nessuna riga. Aggiungine una con il bottone in alto.</td></tr>
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="8" class="text-end">Totale Lavorazione</th>
                            <th class="text-end">€ {{ number_format($lavorazione->totale,2,',','.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Aggiungi Riga --}}
<div class="modal fade" id="modal_aggiungi_riga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title">Aggiungi Riga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" autocomplete="off">
                @csrf
                <div class="modal-body">
                    @include('utente.common._lavorazione_riga_fields', ['prefisso' => 'add_'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="aggiungi_riga" value="1" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Aggiungi Riga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Modifica Riga --}}
<div class="modal fade" id="modal_modifica_riga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-warning p-3">
                <h5 class="modal-title">Modifica Riga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" autocomplete="off" id="form_modifica_riga">
                @csrf
                <input type="hidden" name="id_riga" id="mod_id_riga">
                <div class="modal-body">
                    @include('utente.common._lavorazione_riga_fields', ['prefisso' => 'mod_'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="modifica_riga" value="1" class="btn btn-warning">
                        <i class="ri-save-line me-1"></i> Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('righe-sortable');
        if (el && typeof Sortable !== 'undefined') {
            Sortable.create(el, {
                handle: 'td:first-child',
                animation: 150,
                onEnd: function() {
                    var ids = Array.from(el.querySelectorAll('tr[data-id]')).map(function(tr){ return tr.getAttribute('data-id'); });
                    var fd = new FormData();
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || $('input[name="_token"]').val());
                    ids.forEach(function(id){ fd.append('ids[]', id); });
                    fetch('/utente/ajax/ordina_righe_lavorazione/{{ $lavorazione->id }}', { method: 'POST', body: fd, credentials: 'same-origin' })
                        .then(function(r){ return r.json(); })
                        .then(function(json){
                            if (!json.ok) { alert('Errore nel riordino: ' + (json.error || 'sconosciuto')); }
                        });
                }
            });
        }
    });

    function apriModificaRiga(idRiga) {
        var tr = document.querySelector('tr[data-id="' + idRiga + '"]');
        if (!tr) return;
        document.getElementById('mod_id_riga').value = idRiga;
        var campi = ['servizio','codice','descrizione','attivita','qta','minuti','pu','aliquota','materiale','descrizione_materiale'];
        campi.forEach(function(c){
            var el = document.getElementById('mod_' + c);
            if (el) el.value = tr.getAttribute('data-' + c) || '';
        });
        var setupCk = document.getElementById('mod_setup_tank');
        if (setupCk) setupCk.checked = tr.getAttribute('data-setup_tank') === '1';

        var modal = new bootstrap.Modal(document.getElementById('modal_modifica_riga'));
        modal.show();
    }
</script>

@include('utente.common.footer')
