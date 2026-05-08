@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Scadenziario</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary me-2" onclick="aggiungi()">
                            <i class="ri-add-line align-bottom me-1"></i> Aggiungi
                        </button>
                        <button type="button" class="btn btn-success me-2" onclick="$('#modal_importa').modal('show')">
                            <i class="ri-file-upload-line align-bottom me-1"></i> Importa
                        </button>
                        <a href="/utente/esporta_scadenze_pdf" class="btn btn-danger me-2">
                            <i class="ri-file-pdf-line align-bottom me-1"></i> Esporta PDF
                        </a>
                        <button type="button" class="btn btn-warning" onclick="$('#modalRecuperoCrediti').modal('show')">
                            <i class="ri-file-excel-line align-bottom me-1"></i> Recupero Crediti
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Filtri -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Data Inizio</label>
                                <input type="date" class="form-control" name="data_inizio" value="{{ session('scadenziario_data_inizio', date('Y-01-01')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Data Fine</label>
                                <input type="date" class="form-control" name="data_fine" value="{{ session('scadenziario_data_fine', date('Y-12-31'))  }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filtra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Footer con Totali -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Totale Da Incassare</h5>
                                        <h3 class="mt-3 mb-0" style="color:white;">€ {{ number_format($totale_entrate, 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Totale Da Pagare</h5>
                                        <h3 class="mt-3 mb-0" style="color:white;">€ {{ number_format($totale_uscite, 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card {{ $utile >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Totale Cashflow</h5>
                                        <h3 class="mt-3 mb-0" style="color:white;">€ {{ number_format($utile, 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scadenze in Entrata -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Scadenze in Entrata</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle datatable" style="width:100%;">
                            <thead>
                            <tr>
                                <th class="no-sort">Data</th>
                                <th class="no-sort">Cliente</th>
                                <th class="no-sort">Documento</th>
                                <th class="no-sort">Importo</th>
                                <th class="no-sort">Importo Pagato</th>
                                <th class="no-sort">Stato</th>
                                <th class="no-sort">Stato Solleciti</th>
                                <th class="no-sort">Numero Solleciti</th>
                                <th class="no-sort">Processi Response</th>
                                <th class="no-sort">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scadenze_entrata as $s)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($s->data_scadenza)) }}</td>
                                    <td>{{ $s->cliente }}<br><small>{{ $s->note }}</small></td>
                                    <td>@if($s->numero_doc)<a target="_blank" href="/utente/modifica_documento/{{ $s->id_dotes }}">{{ $s->numero_doc }} del {{ date('d/m/Y', strtotime($s->data_doc)) }}</a>@endif</td>
                                    <td>€ {{ number_format($s->importo, 2, ',', '.') }}</td>
                                    <td>€ {{ number_format($s->importo_pagato, 2, ',', '.') }}</td>
                                    <td>
                                        @if($s->importo_pagato == $s->importo)
                                            <span class="badge bg-success">Completata</span>
                                        @else
                                            <span class="badge bg-danger">Da Completare</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($s->data_ultimo_sollecito)
                                            <div class="d-flex flex-column">
                                                <small>
                                                    <i class="ri-mail-send-line"></i>
                                                    Inviato: {{ date('d/m/Y H:i', strtotime($s->data_ultimo_sollecito)) }}
                                                </small>

                                                @if($s->email_aperta)
                                                    <small class="text-success">
                                                        <i class="ri-mail-open-line"></i>
                                                        Letto: {{ date('d/m/Y H:i', strtotime($s->data_apertura_email)) }}
                                                    </small>
                                                @else
                                                    <small class="text-warning">
                                                        <i class="ri-mail-line"></i>
                                                        Non ancora letto
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Nessun sollecito inviato</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $s->numero_solleciti ?? 0 }}</td>
                                    <td>{{ $s->processi_response ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    onclick="apriSollecito(
                                    {{ $s->id }},
                                    '{{ $s->email }}',
                                    {{ $s->importo - $s->importo_pagato }},
                                    '{{ date('d/m/Y',strtotime($s->data_scadenza)) }}',
                                    '',
                                    '{{ addslashes($s->note) }}'
                                )"
                                                    @if($s->data_ultimo_sollecito && now()->diffInDays(Carbon\Carbon::parse($s->data_ultimo_sollecito)) < 3)
                                                        disabled
                                                    title="Devi attendere 7 giorni dall'ultimo sollecito"
                                                    @endif
                                            >
                                                <i class="ri-mail-send-line"></i>
                                            </button>

                                            <button class="btn btn-sm btn-success" onclick="modifica({{ $s->id }})">
                                                <i class="ri-edit-2-line"></i>
                                            </button>
                                            <!--
                                            <form method="post" onsubmit="return confirm('Vuoi eliminare questa scadenza?')">
                                                <input type="hidden" name="id" value="{{ $s->id }}">
                                                <button type="submit" name="elimina" value="Elimina" class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </form>
                                            -->
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

        <!-- Scadenze in Uscita -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Scadenze in Uscita</h5>
                    </div>
                    <div class="card-body">
                        <table id="scadenze-uscita" class="table table-bordered dt-responsive nowrap table-striped align-middle">
                            <thead>
                            <tr>
                                <th>Data</th>
                                <th>Cliente/Agente</th>
                                <th>Documento</th>
                                <th>Importo</th>
                                <th>Importo Pagato</th>
                                <th>Stato</th>
                                <th>Note</th>
                                <th>Processi Response</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scadenze_uscita as $s)
                                <tr class="{{ $s->note && strpos($s->note, 'Provvigione') !== false ? 'table-info' : '' }}">
                                    <td>{{ date('d/m/Y', strtotime($s->data_scadenza)) }}</td>
                                    <td>
                                        @if(strpos(strtolower($s->note), 'provvigione') !== false && isset($s->agente_nome))
                                            <strong>Provvigione per {{ $s->agente_nome }} {{ $s->agente_cognome }}</strong><br>
                                            <small>Rif. fattura {{ $s->numero_doc }} del {{ date('d/m/Y', strtotime($s->data_doc)) }}</small><br>
                                            <small>Cliente: {{ $s->cliente }}</small>
                                        @else
                                            {{ $s->cliente }}
                                        @endif
                                    </td>
                                    <td>@if($s->numero_doc)<a target="_blank" href="/utente/visualizza_xml_da_file/{{ $s->id_dotes }}">{{ $s->numero_doc }} del {{ date('d/m/Y', strtotime($s->data_doc)) }}</a>@endif</td>
                                    <td>€ {{ number_format($s->importo, 2, ',', '.') }}</td>
                                    <td>€ {{ number_format($s->importo_pagato, 2, ',', '.') }}</td>
                                    <td>
                                        @if($s->importo_pagato == $s->importo)
                                            <span class="badge bg-success">Completata</span>
                                        @else
                                            <span class="badge bg-danger">Da Completare</span>
                                        @endif
                                    </td>
                                    <td>{{ $s->note }}</td>
                                    <td>{{ $s->processi_response ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-success" onclick="modifica({{ $s->id }})">
                                                <i class="ri-edit-2-line"></i>
                                            </button>
                                            <form method="post" onsubmit="return confirm('Vuoi eliminare questa scadenza?')">
                                                <input type="hidden" name="id" value="{{ $s->id }}">
                                                <button type="submit" name="elimina" value="Elimina" class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </form>
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

<!-- Modal Importa -->
<div class="modal fade" id="modal_importa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title">Importa Scadenze</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel</label>
                        <input type="file" class="form-control" name="file_scadenze" accept=".xls,.xlsx" required>
                    </div>

                    <div class="alert alert-info">
                        <h6>Formato file Excel richiesto:</h6>
                        <ul class="mb-0">
                            <li>Data</li>
                            <li>Importo</li>
                            <li>Tipo (entrata/uscita)</li>
                            <li>Note</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-info btn-sm" onclick="downloadTemplate()">
                            <i class="ri-download-line align-bottom"></i> Scarica Template
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success" name="importa_scadenze" value="Importa">Importa</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Aggiungi -->
<div class="modal fade" id="modal_aggiungi">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title">Aggiungi Scadenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" class="form-control" name="data_scadenza" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-control" name="id_cliente" data-choices data-choices-search-true>
                            <option value="0">Seleziona Cliente</option>
                            @foreach($clienti as $c)
                                <option value="{{ $c->id }}">{{ $c->ragione_sociale }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-control" name="tipo_movimento" required>
                            <option value="entrata">Entrata</option>
                            <option value="uscita">Uscita</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Importo</label>
                        <input type="number" step="0.01" class="form-control" name="importo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Importo Pagato</label>
                        <input type="number" step="0.01" class="form-control" value="0" name="importo_pagato" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary" name="aggiungi" value="Aggiungi">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica per ogni scadenza -->
@foreach($scadenze_entrata as $s)
    <div class="modal fade" id="modal_modifica_{{ $s->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">Modifica Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $s->id }}">
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" name="data_scadenza" value="{{ $s->data_scadenza }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-control" name="id_cliente" data-choices data-choices-search-true>
                                <option value="">Seleziona Cliente</option>
                                @foreach($clienti as $c)
                                        <?php if($c->id_tipologia == 2){ ?>
                                    <option value="{{ $c->id }}" {{ $c->id == $s->id_cliente ? 'selected' : '' }}>
                                        {{ $c->ragione_sociale }}
                                    </option>
                                    <?php } ?>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Importo</label>
                            <input type="number" step="0.01" class="form-control" name="importo" value="{{ $s->importo }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Importo Pagato</label>
                            <input type="number" step="0.01" class="form-control" name="importo_pagato" value="{{ $s->importo_pagato }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" rows="3">{{ $s->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $s->id ?>">
                        <button type="submit" class="btn btn-primary" name="modifica" value="Modifica">Salva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Ripeti i modal di modifica per le scadenze in uscita -->
@foreach($scadenze_uscita as $s)
    <div class="modal fade" id="modal_modifica_{{ $s->id }}" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">Modifica Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $s->id }}">
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" name="data_scadenza" value="{{ $s->data_scadenza }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-control" name="id_cliente" data-choices data-choices-search-true>
                                <option value="">Seleziona Cliente</option>
                                    <?php if($c->id_tipologia == 1){ ?>

                                @foreach($clienti as $c)
                                    <option value="{{ $c->id }}" {{ $c->id == $s->id_cliente ? 'selected' : '' }}>
                                        {{ $c->ragione_sociale }}
                                    </option>
                                @endforeach
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Importo</label>
                            <input type="number" step="0.01" class="form-control" name="importo" value="{{ $s->importo }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Importo Pagato</label>
                            <input type="number" step="0.01" class="form-control" name="importo_pagato" value="{{ $s->importo_pagato }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" rows="3">{{ $s->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $s->id ?>">
                        <button type="submit" class="btn btn-primary" name="modifica" value="Modifica">Salva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Sollecito -->
<div class="modal fade" id="modalSollecito" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invia Sollecito di Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSollecito">

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Email destinatario</label>
                                <input type="text" class="form-control" id="email_destinatari" name="email_destinatari">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Messaggio</label>
                                <div class="bg-light p-2 border rounded">
                                    <textarea class="form-control" id="email_messaggio" name="email_messaggio" rows="6"></textarea>
                                </div>
                            </div>
                        </div>

                        <b>Invia Chiamata di recupero credito tramite l'IA (solo se collegato con il CRM)</b>

                        <div class="row mb-3">
                            <div class="col-12">
                                <input type="datetime-local" class="form-control" id="ora_chiamata" name="ora_chiamata">
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <input type="hidden" id="scadenza_id" name="id">
                    <input type="submit" class="btn btn-success" name="invia_sollecito" value="Invia Sollecito">
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Recupero Crediti -->
<div class="modal fade" id="modalRecuperoCrediti" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="/utente/esporta_scadenze_recupero_crediti">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subappalto Recupero Crediti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6>Esportazione per agenzia di recupero crediti</h6>
                        <p>Usa questa funzione per esportare le scadenze non pagate in un formato compatibile con le agenzie di recupero crediti.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seleziona criteri</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="solo_scadute" name="solo_scadute" value="1" checked>
                            <label class="form-check-label" for="solo_scadute">Solo scadenze già scadute</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="solo_sollecitate" name="solo_sollecitate" value="1">
                            <label class="form-check-label" for="solo_sollecitate">Solo scadenze già sollecitate</label>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Scadute da almeno (giorni)</label>
                            <input type="number" class="form-control" name="giorni_scadenza" value="30" min="0">
                            <small class="text-muted">Lascia 0 per includere tutte le scadenze</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Importo minimo</label>
                            <input type="number" step="0.01" class="form-control" name="importo_minimo" value="100" min="0">
                            <small class="text-muted">Importo minimo della scadenza</small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning">Esporta per recupero crediti</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('utente.common.footer')

<!-- Prima della chiusura del body -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>


<script>
    $(document).ready(function() {
        $('#scadenze-entrata').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            },
            order: [[0, 'asc']]
        });

        $('#scadenze-uscita').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            },
            order: [[0, 'asc']]
        });

    });

    function aggiungi() {
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id) {
        $('#modal_modifica_' + id).modal('show');
    }


    async function downloadTemplate() {
        try {
            // Create the workbook with formatted dates
            const workbook = {
                SheetNames: ['Template'],
                Sheets: {
                    'Template': {
                        '!ref': 'A1:D3',
                        'A1': { t: 's', v: 'Data' },
                        'B1': { t: 's', v: 'Importo' },
                        'C1': { t: 's', v: 'Tipo' },
                        'D1': { t: 's', v: 'Note' },
                        // Example entry row
                        'A2': { t: 's', v: '2024-02-01' }, // Data in YYYY-MM-DD format
                        'B2': { t: 'n', v: 1000 },
                        'C2': { t: 's', v: 'entrata' },
                        'D2': { t: 's', v: 'Esempio nota entrata' },
                        // Example exit row
                        'A3': { t: 's', v: '2024-02-02' }, // Data in YYYY-MM-DD format
                        'B3': { t: 'n', v: 500 },
                        'C3': { t: 's', v: 'uscita' },
                        'D3': { t: 's', v: 'Esempio nota uscita' }
                    }
                }
            };

            const wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            const blob = new Blob([wbout], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template_scadenze.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

        } catch (error) {
            console.error('Errore durante la creazione del template:', error);
            alert('Si è verificato un errore durante la creazione del template.');
        }
    }

</script>

<style>
    .dataTables_filter {
        float: right;
    }
    .dataTables_filter input {
        margin-left: 0.5em;
    }
    .card {
        margin-bottom: 1.5rem;
    }
</style>

<script type="text/javascript">

    function apriSollecito(id, email, importo, scadenza,numero_fattura,note) {
        $('#scadenza_id').val(id);
        $('#email_destinatari').val(email);

        // Template messaggio sollecito
        const messaggioTemplate = `Gentile Cliente,

La presente per sollecitare il pagamento della fattura N. ${numero_fattura} in scadenza/scaduta il ${scadenza}
per un importo di € ${importo}.

${note}
Vi preghiamo di provvedere al saldo nel più breve tempo possibile.

Cordiali saluti,
<?php echo $azienda->ragione_sociale ?>`;

        $('#email_messaggio').val(messaggioTemplate);
        $('#modalSollecito').modal('show');
    }

</script>