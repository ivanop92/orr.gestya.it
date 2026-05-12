@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dettaglio Documento</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Documenti</a></li>
                            <li class="breadcrumb-item active">Dettaglio</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @include('utente.common.workflow_accettazione', ['dotes' => $dotes, 'utente' => $utente])
        @include('utente.common.applica_lavorazioni', ['dotes' => $dotes, 'utente' => $utente])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed p-4">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="hstack gap-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <h6 class="text-muted mb-0">Documento N°:</h6>
                                        <h5 class="mb-0">{{$dotes->numero_doc}}</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="text-muted mb-0">Data:</h6>
                                        <h5 class="mb-0">{{date('d/m/Y', strtotime($dotes->data_doc))}}</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="text-muted mb-0">Tipo:</h6>
                                        <h5 class="mb-0">{{$dotes->cd_do}}</h5>
                                    </div>
                                    @if($dotes->id_commessa)
                                    <div class="flex-shrink-0">
                                        <h6 class="text-muted mb-0">Commessa:</h6>
                                        @php
                                            $commesse = [];
                                            if ($dotes->id_commessa) {
                                                $commessa = DB::table('commesse')
                                                    ->where('id', $dotes->id_commessa)
                                                    ->where('id_azienda', $utente->id_azienda)
                                                    ->first();
                                                if ($commessa) {
                                                    $commesse = [$commessa];
                                                }
                                            }
                                        @endphp
                                        @if($commessa)
                                            <h5 class="mb-0">
                                                <a href="{{ url('utente/commesse/'.$commessa->id.'/attivita') }}">
                                                    {{$commessa->codice_commessa}} - {{$commessa->descrizione}}
                                                </a>
                                            </h5>
                                        @else
                                            <h5 class="mb-0">ID: {{$dotes->id_commessa}}</h5>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 border-top border-top-dashed">
                        <div class="row g-3">
                            <!-- Informazioni Cliente -->
                            <div class="col-6">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Informazioni Cliente</h6>
                                <p class="fw-medium mb-2">{{$dotes->ragione_sociale}}</p>
                                <p class="text-muted mb-1">{{$dotes->indirizzo}}</p>
                                <p class="text-muted mb-1">{{$dotes->cap}} {{$dotes->comune}} ({{$dotes->provincia}})</p>
                                <p class="text-muted mb-1">P.IVA: {{$dotes->partita_iva}}</p>
                                @if($dotes->pec)
                                    <p class="text-muted mb-1">PEC: {{$dotes->pec}}</p>
                                @endif
                                @if($dotes->sdi)
                                    <p class="text-muted">Codice SDI: {{$dotes->sdi}}</p>
                                @endif
                            </div>
                            <!-- Note e Info Aggiuntive -->
                            <div class="col-6">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Note e Info Aggiuntive</h6>
                                @if($dotes->oggetto_visibile)
                                    <p class="text-muted mb-1">{{$dotes->oggetto_visibile}}</p>
                                @endif
                                @if($dotes->oggetto_interno)
                                    <p class="text-muted mb-1">Note Interne: {{$dotes->oggetto_interno}}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                <thead>
                                <tr class="table-active">
                                    <th scope="col" style="width:32px;"></th>
                                    <th scope="col">Prodotto</th>
                                    <th scope="col">Dettagli</th>
                                    <th scope="col">Lotto</th>
                                    <th scope="col">Q.tà</th>
                                    <th scope="col">Prezzo</th>
                                    <th scope="col">IVA</th>
                                    <th scope="col" style="width: 150px;">Importo</th>
                                </tr>
                                </thead>
                                <tbody id="righe-doc-sortable">
                                @foreach($righe as $riga)
                                    <tr data-id="{{$riga->id}}">
                                        <td class="text-center riga-drag-handle" style="cursor:grab; width:32px;" title="Trascina per riordinare"><i class="ri-drag-move-2-line text-muted"></i></td>
                                        <td class="fw-medium">
                                            @if(!empty($riga->servizio))<span class="badge bg-soft-info text-info me-1">{{ $riga->servizio }}</span>@endif
                                            {{ $riga->nome_prodotto ?: $riga->descrizione }}
                                            @if(!empty($riga->setup_tank))<span class="badge bg-soft-warning text-warning ms-1" title="Setup Task">Setup</span>@endif
                                        </td>
                                        <td>
                                            {{$riga->dettagli_prodotto}}
                                            @if(!empty($riga->materiale) && $riga->materiale > 0)
                                                <br><small class="text-muted">Materiale: € {{ number_format($riga->materiale,2,',','.') }}@if(!empty($riga->descrizione_materiale)) — {{ $riga->descrizione_materiale }}@endif</small>
                                            @endif
                                        </td>
                                        <td>{{$riga->lotto}}</td>
                                        <td>
                                            {{$riga->qta}} {{$riga->um}}
                                            @if(!empty($riga->minuti) && $riga->minuti > 0)
                                                <br><small class="text-muted">{{ rtrim(rtrim(number_format($riga->minuti,2,',',''), '0'), ',') }} min</small>
                                            @endif
                                            @if(!empty($riga->attivita) && (float)$riga->attivita != 1.0)
                                                <br><small class="text-muted">att: {{ rtrim(rtrim(number_format($riga->attivita,2,',',''), '0'), ',') }}</small>
                                            @endif
                                        </td>
                                        <td>€ {{number_format($riga->prezzo_unitario, 2, ',', '.')}}</td>
                                        <td>{{$riga->iva}}%</td>
                                        <td class="text-end">€ {{number_format($riga->prezzo_totale, 2, ',', '.')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-top border-top-dashed mt-2">
                            <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width: 250px;">
                                <tbody>
                                <tr>
                                    <td>Imponibile</td>
                                    <td class="text-end">€ {{number_format($dotes->imponibile, 2, ',', '.')}}</td>
                                </tr>
                                <tr>
                                    <td>IVA</td>
                                    <td class="text-end">€ {{number_format($dotes->imposta, 2, ',', '.')}}</td>
                                </tr>
                                <tr class="border-top border-top-dashed fs-15">
                                    <th scope="row">Totale</th>
                                    <th class="text-end">€ {{number_format($dotes->totale, 2, ',', '.')}}</th>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <span class="fw-semibold">NOTE:</span>
                                    <span id="note">Documento emesso ai sensi dell'art. 21 DPR 633/72</span>
                                </p>
                            </div>
                        </div>

                        @if($dotes->nome_file_fattura)
                            <a href="{{ url('utente/visualizza_xml_da_file/'.$dotes->id) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="ri-file-code-line align-middle me-1"></i>Visualizza XML
                            </a>
                        @endif

                    </div>

                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Pagamenti</h4>
                    </div>
                    <div class="card-body">
                        <div id="payment-rows">
                            @forelse($scadenze as $scadenza)
                                <div class="payment-row mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Importo</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control importo" step="0.01"
                                                           value="{{ $scadenza->importo }}">
                                                    <span class="input-group-text">€</span>
                                                    <small><?php echo $scadenza->note ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Termini pagam.</label>
                                                <select class="form-select termini">
                                                    <option value="immediato" {{ $scadenza->modalita_pagamento == 'immediato' ? 'selected' : '' }}>Immediato</option>
                                                    <option value="30gg" {{ $scadenza->modalita_pagamento == '30gg' ? 'selected' : '' }}>30 giorni</option>
                                                    <option value="60gg" {{ $scadenza->modalita_pagamento == '60gg' ? 'selected' : '' }}>60 giorni</option>
                                                    <option value="90gg" {{ $scadenza->modalita_pagamento == '90gg' ? 'selected' : '' }}>90 giorni</option>
                                                    <option value="120gg" {{ $scadenza->modalita_pagamento == '120gg' ? 'selected' : '' }}>120 giorni</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Scadenza</label>
                                                <input type="date" class="form-control scadenza"
                                                       value="{{ date('Y-m-d', strtotime($scadenza->data_scadenza)) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Stato</label>
                                                <select class="form-select stato">
                                                    <option value="da_pagare" {{ $scadenza->stato == 'da_pagare' ? 'selected' : '' }}>Non saldato</option>
                                                    <option value="pagato" {{ $scadenza->stato == 'pagato' ? 'selected' : '' }}>Saldato</option>
                                                    <option value="parziale" {{ $scadenza->stato == 'parziale' ? 'selected' : '' }}>Parziale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex">
                                                    <button type="button" class="btn btn-light btn-sm me-2 btn-advanced"
                                                            data-bs-toggle="collapse" data-bs-target="#advanced-{{ $loop->index }}">
                                                        <i class="ri-settings-4-line"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                        <i class="ri-delete-bin-2-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Attributi avanzati collassabili -->
                                    <div class="collapse mt-2" id="advanced-{{ $loop->index }}">
                                        <div class="card card-body bg-light">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">IBAN</label>
                                                        <input type="text" class="form-control iban" value="{{ $scadenza->iban }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Modalità Pagamento</label>
                                                        <select class="form-select modalita">
                                                            <option value="bonifico" {{ $scadenza->modalita_pagamento == 'bonifico' ? 'selected' : '' }}>Bonifico</option>
                                                            <option value="rid" {{ $scadenza->modalita_pagamento == 'rid' ? 'selected' : '' }}>RID</option>
                                                            <option value="riba" {{ $scadenza->modalita_pagamento == 'riba' ? 'selected' : '' }}>RIBA</option>
                                                            <option value="contanti" {{ $scadenza->modalita_pagamento == 'contanti' ? 'selected' : '' }}>Contanti</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Note</label>
                                                        <input type="text" class="form-control note" value="{{ $scadenza->note }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" class="scadenza-id" value="{{ $scadenza->id }}">
                                </div>
                            @empty
                                <!-- Se non ci sono scadenze, mostra una riga vuota -->
                                <div class="payment-row mb-3">
                                    <!-- Il contenuto della riga vuota come prima -->
                                </div>
                            @endforelse
                        </div>

                        <!-- Pulsante per aggiungere nuova riga -->
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-info btn-sm" id="addPaymentRow">
                                <i class="ri-add-line align-middle me-1"></i>
                                Aggiungi scadenza di pagamento
                            </button>
                        </div>

                        <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                            <button type="button" class="btn btn-primary" onclick="if(validaImportoTotale()) salvaScadenze()">
                                Salva Scadenze
                            </button>
                        </div>

                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let rowCount = {{ $scadenze->count() }};

                        // Funzione per aggiungere nuova riga
                        document.getElementById('addPaymentRow').addEventListener('click', function() {
                            rowCount++;
                            // ... (resto del codice per aggiungere riga come prima)
                        });

                        // Aggiorna salvaScadenze per gestire anche l'aggiornamento
                        function salvaScadenze() {
                            const scadenze = [];

                            document.querySelectorAll('.payment-row').forEach(row => {
                                const scadenza = {
                                    id: row.querySelector('.scadenza-id')?.value, // ID esistente se presente
                                    importo: row.querySelector('.importo').value,
                                    modalita_pagamento: row.querySelector('.termini').value,
                                    data_scadenza: row.querySelector('.scadenza').value,
                                    stato: row.querySelector('.stato').value,
                                    iban: row.querySelector('.iban').value,
                                    note: row.querySelector('.note').value
                                };

                                if (scadenza.importo) {
                                    scadenze.push(scadenza);
                                }
                            });

                            // Invia al server per aggiornamento
                            fetch('/utente/salva_scadenze/{{ $dotes->id }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ scadenze: scadenze })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Toastify({
                                            text: "Scadenze aggiornate con successo",
                                            duration: 3000,
                                            gravity: "top",
                                            position: "right",
                                            style: {
                                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                                            }
                                        }).showToast();

                                        // Ricarica la pagina per mostrare i dati aggiornati
                                        setTimeout(() => location.reload(), 1000);
                                    }
                                })
                                .catch(error => {
                                    Toastify({
                                        text: "Errore nel salvataggio: " + error.message,
                                        duration: 3000,
                                        gravity: "top",
                                        position: "right",
                                        style: {
                                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                                        }
                                    }).showToast();
                                });
                        }

                        // Inizializza gli event listeners per le righe esistenti
                        updateEventListeners();
                    });
                </script>
            </div>
        </div>
    </div>
</div>
<!-- Sezione Scadenze -->



<style>
    @media print {
        .navbar-header, .app-menu, .footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>

@include('utente.common.footer')
<script>

        function updateEventListeners() {
            // Rimuovi riga
            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.onclick = function() {
                    if (document.querySelectorAll('.payment-row').length > 1) {
                        this.closest('.payment-row').remove();
                    }
                };
            });

            // Aggiorna scadenza in base ai termini
            document.querySelectorAll('.termini').forEach(select => {
                select.onchange = function() {
                    const scadenzaInput = this.closest('.payment-row').querySelector('.scadenza');
                    const oggi = new Date();
                    let scadenza = new Date();

                    switch(this.value) {
                        case 'immediato':
                            scadenza = oggi;
                            break;
                        case '30gg':
                            scadenza.setDate(oggi.getDate() + 30);
                            break;
                        case '60gg':
                            scadenza.setDate(oggi.getDate() + 60);
                            break;
                        case '90gg':
                            scadenza.setDate(oggi.getDate() + 90);
                            break;
                        case '120gg':
                            scadenza.setDate(oggi.getDate() + 120);
                            break;
                    }

                    scadenzaInput.value = scadenza.toISOString().split('T')[0];
                };
            });

            // Calcola totali quando cambiano gli importi
            document.querySelectorAll('.importo').forEach(input => {
                input.onchange = function() {
                    calcolaTotali();
                };
            });
        }

        function calcolaTotali() {
            let totalePagamenti = 0;
            document.querySelectorAll('.importo').forEach(input => {
                totalePagamenti += parseFloat(input.value || 0);
            });

            // Verifica se il totale supera l'importo del documento
            const importoDocumento = parseFloat('{{ $dotes->totale }}');
            if (totalePagamenti > importoDocumento) {
                alert('Attenzione: il totale dei pagamenti supera l\'importo del documento');
            }
        }

        function validaImportoTotale() {
            let totalePagamenti = 0;
            document.querySelectorAll('.importo').forEach(input => {
                totalePagamenti += parseFloat(input.value || 0);
            });

            const importoDocumento = parseFloat('{{ $dotes->totale }}');
            if (totalePagamenti !== importoDocumento) {
                alert('Attenzione: il totale dei pagamenti deve corrispondere all\'importo del documento');
                return false;
            }
            return true;
        }

        // Inizializza gli event listeners per le righe esistenti
        updateEventListeners();

        let rowCount = {{ $scadenze->count() }};

        // Funzione per aggiungere nuova riga
        document.getElementById('addPaymentRow').addEventListener('click', function() {
            rowCount++;
            const template = document.querySelector('.payment-row').cloneNode(true);

            // Reset valori
            template.querySelectorAll('input').forEach(input => input.value = '');
            template.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Aggiorna ID del collapse
            const advancedDiv = template.querySelector('.collapse');
            const newId = 'advanced-' + rowCount;
            advancedDiv.id = newId;
            template.querySelector('.btn-advanced').setAttribute('data-bs-target', '#' + newId);

            document.getElementById('payment-rows').appendChild(template);

            // Aggiorna event listeners
            updateEventListeners();
        });

        function salvaScadenze() {
            if (!validaImportoTotale()) {
                return false;
            }

            const scadenze = [];

            document.querySelectorAll('.payment-row').forEach(row => {
                const scadenza = {
                    id: row.querySelector('.scadenza-id')?.value, // ID esistente se presente
                    importo: row.querySelector('.importo').value,
                    importo_pagato: 0,
                    tipo_movimento: row.closest('.payment-row').querySelector('.tipo_movimento')?.value || 'entrata',
                    data_scadenza: row.querySelector('.scadenza').value,
                    stato: row.querySelector('.stato').value,
                    iban: row.querySelector('.iban')?.value || '',
                    modalita_pagamento: row.querySelector('.modalita')?.value || '',
                    note: row.querySelector('.note')?.value || '',
                    id_dotes: '{{ $dotes->id }}',
                    numero_rata: row.closest('.payment-row').querySelector('.numero_rata')?.value || null,
                    totale_rate: row.closest('.payment-row').querySelector('.totale_rate')?.value || null
                };

                if (scadenza.importo && scadenza.data_scadenza) {
                    scadenze.push(scadenza);
                }
            });

            fetch('/utente/salva_scadenze/{{ $dotes->id }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ scadenze: scadenze })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: "Scadenze salvate con successo",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                            }
                        }).showToast();

                        // Aggiorna gli ID delle nuove scadenze nella vista
                        if (data.newIds) {
                            data.newIds.forEach(item => {
                                const row = document.querySelector(`.payment-row input[value="${item.tempId}"]`)?.closest('.payment-row');
                                if (row) {
                                    row.querySelector('.scadenza-id').value = item.newId;
                                }
                            });
                        }
                    } else {
                        throw new Error(data.message || 'Errore durante il salvataggio');
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    Toastify({
                        text: "Errore nel salvataggio: " + error.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }
                    }).showToast();
                });
        }

</script>

<style>
    .payment-row {
        position: relative;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }

    .btn-remove {
        opacity: 0.8;
    }

    .btn-remove:hover {
        opacity: 1;
    }

    .btn-advanced {
        opacity: 0.8;
    }

    .btn-advanced:hover {
        opacity: 1;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('righe-doc-sortable');
        if (el && typeof Sortable !== 'undefined') {
            Sortable.create(el, {
                handle: '.riga-drag-handle',
                animation: 150,
                onEnd: function() {
                    var ids = Array.from(el.querySelectorAll('tr[data-id]')).map(function(tr){ return tr.getAttribute('data-id'); });
                    var fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    ids.forEach(function(id){ fd.append('ids[]', id); });
                    fetch('/utente/ajax/ordina_righe_documento/{{ $dotes->id }}', {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(function(r){ return r.json(); })
                      .then(function(json){
                          if (!json.ok) { alert('Errore nel riordino: ' + (json.error || 'sconosciuto')); }
                      });
                }
            });
        }
    });
</script>