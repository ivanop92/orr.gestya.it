@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">
        <h2>Gestione Contratti</h2>

        <!-- Pulsante per aggiungere un nuovo contratto -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_contratto">Aggiungi Contratto</button>
        <div class="d-flex justify-content-end mb-3">
            <form action="{{ route('contratti.evadiTuttoFattura') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('Vuoi evadere tutti i contratti in fattura?')">
                    Evadi Tutto in Fattura
                </button>
            </form>
            <button class="btn btn-primary mb-3 mx-3" data-bs-toggle="modal" data-bs-target="#modal_fatturazione_periodica">Configura Fatturazione Periodica</button>
        </div>

        <!-- Elenco contratti -->
        <div class=" mt-4">
            <table id="scroll-horizontal" class="table table-bordered table-hover datatable bg-white" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Descrizione</th>
                    <th>Tipologia Contratto</th>
                    <th>Prezzo</th>
                    <th>IVA</th>
                    <th>Ore</th>
                    <th>Costo Orario</th>
                    <th>Data Inizio Contratto</th>
                    <th>Giorno Periodico di Fatturazione</th>
                    <th>Prossima Fattura</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($contratti as $contratto)
                    <tr>
                        <td>{{ $contratto->id }}</td>
                        <td>{{ $contratto->cliente_ragione_sociale }}</td>
                        <td>{{ $contratto->descrizione }}</td>
                        <td>
                            @if ($contratto->contratto_orario)
                                Contratto Ad Ore
                            @else
                                Contratto Ordinario
                            @endif
                        </td>
                        <td>
                                {{ $contratto->prezzo}} €
                        </td>
                        <td>
                                {{ $contratto->iva}}%

                        </td>
                        <td>
                            @if ($contratto->contratto_orario)
                                {{ $contratto->ore ?? '0' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if ($contratto->contratto_orario)
                                {{ $contratto->costo_orario ?? '0.00' }} €
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $contratto->data }}</td>
                        <td>
                            @if ($contratto->giorno_fatturazione)
                                Ogni {{ $contratto->giorno_fatturazione ?? 'Non configurato' }} del mese
                            @else
                                Non Configurato
                            @endif
                        </td>
                        <td>{{ $contratto->prossima_fattura ?? 'Non configurata' }}</td>
                        <td>
                            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                                <!-- Modifica Contratto -->
                                <button class="btn btn-sm btn-primary" onclick="openEditModal({{ $contratto->id }}, {{ $contratto->cliente_id }}, '{{ $contratto->descrizione }}', '{{ $contratto->data }}', '{{ json_encode($contratto->allegati) }}', '{{ $contratto->prezzo }}', '{{ $contratto->iva }}')">
                                    Modifica
                                </button>

                                <!-- Elimina Contratto -->
                                <form method="post" onsubmit="return confirm('Vuoi Eliminare questo Contratto?')" class="d-inline">
                                    <input type="hidden" name="id_contratto" value="{{ $contratto->id }}">
                                    <input type="submit" name="elimina" value="Elimina" class="btn btn-sm btn-danger" style="cursor: pointer;">
                                </form>


                                <!-- Info Contratto -->
                                <a href="{{ url('contratti/dettagli', ['id' => $contratto->id]) }}" class="btn btn-sm btn-info">
                                    <i class="ri-information-line"></i> Info
                                </a>

                                <!-- Aggiungi Ore (solo per contratti ad ore) -->
                                @if ($contratto->contratto_orario)
                                    <button class="btn btn-sm btn-success" onclick="openAggiungiOreModal({{ $contratto->id }}, {{ $contratto->ore ?? 0 }}, {{ $contratto->costo_orario ?? 0 }})">
                                        Aggiungi Ore
                                    </button>
                                @endif

                                <!-- Fattura Contratto -->
                                <form method="post" action="{{ route('contratti.fattura') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_contratto" value="{{ $contratto->id }}">
                                    <button class="btn btn-sm btn-warning" onclick="return confirm('Vuoi fatturare questo contratto?')">
                                       Genera Fattura
                                    </button>
                                </form>
                            </div>
                        </td>


                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        <script>
            function openAggiungiOreModal(id, ore, costoOrario) {
                $('#contratto_id').val(id);
                $('#ore').val(ore);
                $('#costo_orario').val(costoOrario);
                $('#modal_aggiungi_ore').modal('show');
            }
        </script>


        <!-- Modal per aggiungere un contratto -->
        <div class="modal fade" id="modal_aggiungi_contratto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-soft-info p-3">
                        <h5 class="modal-title">Nuovo Contratto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ url('contratti') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="azione" value="aggiungi">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="cliente_id" class="form-label">Cliente <b style="color:red">*</b></label>
                                <select id="cliente_id" name="cliente_id" class="form-control" required>
                                    <option value="">Seleziona un cliente</option>
                                    @foreach ($clienti as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->ragione_sociale }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="descrizione" class="form-label">Descrizione <b style="color:red">*</b></label>
                                <textarea id="descrizione" name="descrizione" class="form-control" placeholder="Descrizione del contratto" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-check-label">
                                    <input type="checkbox" id="contratto_orario" name="contratto_orario" class="form-check-input">
                                    Contratto ad Ore
                                </label>
                            </div>
                            <div id="prezzo_iva_container">
                                <div class="mb-3">
                                    <label for="prezzo" class="form-label">Costo Mensile <b style="color:red">*</b></label>
                                    <input type="number" id="prezzo" name="prezzo" class="form-control" placeholder="Prezzo">
                                </div>
                                <div class="mb-3">
                                    <label for="iva" class="form-label">Iva <b style="color:red">*</b></label>
                                    <input type="number" id="iva" name="iva" class="form-control" placeholder="IVA">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="data" class="form-label">Data Inizio Contratto<b style="color:red">*</b></label>
                                <input type="date" id="data" name="data" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="allegati" class="form-label">Allegati</label>
                                <input type="file" id="allegati" name="allegati[]" class="form-control" multiple>
                                <small class="text-muted">Puoi caricare più file.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <button type="submit" class="btn btn-success">Crea Contratto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const contrattoOrarioCheckbox = document.getElementById("contratto_orario");
                const prezzoIvaContainer = document.getElementById("prezzo_iva_container");

                contrattoOrarioCheckbox.addEventListener("change", function () {
                    if (this.checked) {
                        prezzoIvaContainer.style.display = "none";
                    } else {
                        prezzoIvaContainer.style.display = "block";
                    }
                });
            });
        </script>


        <!-- Modal per modificare un contratto -->
        <div class="modal fade" id="modal_modifica_contratto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-soft-info p-3">
                        <h5 class="modal-title">Modifica Contratto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ url('contratti') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="azione" value="modifica">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_cliente_id" class="form-label">Cliente <b style="color:red">*</b></label>
                                <select id="edit_cliente_id" name="cliente_id" class="form-control" required>
                                    @foreach ($clienti as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->ragione_sociale }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_descrizione" class="form-label">Descrizione <b style="color:red">*</b></label>
                                <textarea id="edit_descrizione" name="descrizione" class="form-control" required></textarea>
                            </div>


                            <div class="mb-3">
                                <label for="edit_prezzo" class="form-label">Prezzo <b style="color:red">*</b></label>
                                <input type="number"  id="edit_prezzo" name="prezzo" class="form-control" placeholder="Prezzo" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_iva" class="form-label">Iva <b style="color:red">*</b></label>
                                <input type="number"  id="edit_iva" name="iva" class="form-control" placeholder="IVA" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_data" class="form-label">Data <b style="color:red">*</b></label>
                                <input type="date" id="edit_data" name="data" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_allegati" class="form-label">Allegati</label>
                                <input type="file" id="edit_allegati" name="allegati[]" class="form-control" multiple>
                                <small class="text-muted">Puoi caricare più file.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <button type="submit" class="btn btn-success">Salva Modifiche</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_aggiungi_ore" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-soft-info p-3">
                        <h5 class="modal-title">Aggiorna Ore, Costo Orario e IVA</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('contratti/aggiornaOre') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="contratto_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ore" class="form-label">Ore</label>
                                <input type="number" id="ore" name="ore" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="costo_orario" class="form-label">Costo Orario</label>
                                <input type="number" id="costo_orario" name="costo_orario" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="iva" class="form-label">IVA (%)</label>
                                <input type="number" id="iva" name="iva" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <button type="submit" class="btn btn-success">Aggiorna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal per configurare fatturazione periodica -->
        <div class="modal fade" id="modal_fatturazione_periodica" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Configura Fatturazione Periodica</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('contratti/fatturazione-periodica') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="giorno_fatturazione">Giorno del mese</label>
                                <input type="number" name="giorno_fatturazione" id="giorno_fatturazione" class="form-control" min="1" max="31" required>
                            </div>

                            <div class="form-group mt-4">
                                <label>Contratti da includere</label>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Seleziona</th>
                                            <th>Cliente</th>
                                            <th>Descrizione</th>
                                            <th>Giorno Fatturazione</th>
                                            <th>Prossima Fattura</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($contratti as $contratto)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="contratti[]" value="{{ $contratto->id }}" id="contratto_{{ $contratto->id }}">
                                                </td>
                                                <td>{{ $contratto->cliente_ragione_sociale }}</td>
                                                <td>{{ $contratto->descrizione }}</td>
                                                <td>
                                                    @if ($contratto->giorno_fatturazione)
                                                        {{ $contratto->giorno_fatturazione }}
                                                    @else
                                                        Non configurato
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($contratto->prossima_fattura)
                                                        {{ $contratto->prossima_fattura }}
                                                    @else
                                                        Non configurata
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                            <button type="submit" class="btn btn-success">Configura</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
</div>

<script>
    function openEditModal(id, clienteId, descrizione, data, allegati, prezzo, iva) {
        $('#modal_modifica_contratto').modal('show');
        $('#edit_id').val(id);
        $('#edit_iva').val(iva);
        $('#edit_prezzo').val(prezzo);
        $('#edit_cliente_id').val(clienteId);
        $('#edit_descrizione').val(descrizione);
        $('#edit_data').val(data);
    }
</script>

@include('default.common.footer')
