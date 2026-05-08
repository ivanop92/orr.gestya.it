@include('default.common.header')

<div class="container-fluid" style="margin-top: 100px; margin-bottom: 100px;">
    <h3>Dettaglio Fattura -  {{ $testata->numero }}/{{ $testata->anno }}</h3>

    <!-- Dettagli della testata -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Informazioni Fattura:</h4>
            <p><strong>Numero:</strong> {{ $testata->numero }}/{{ $testata->anno }}</p>
            <p><strong>Data:</strong> {{ date('d/m/Y', strtotime($testata->data)) }}</p>
            <p><strong>Cliente:</strong> {{ $testata->nominativo }}<br>P.IVA: {{ $testata->piva }}</p>
            <p><strong>Totale Fattura:</strong> € {{ number_format($testata->totale, 2, ',', '.') }}</p>
            @if($testata->saldata == 0)
            <button id="markAsPaidButton" class="btn btn-success">Saldata</button>
            @else
                <p class="text-success">La Fattura è stata Saldata<strong></strong></p>
            @endif
        </div>
    </div>

    <!-- Righe della fattura -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Dettaglio Righe:</h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Descrizione</th>
                    <th>Quantità</th>
                    <th>Prezzo Unitario</th>
                    <th>Prezzo Totale</th>
                    <th>IVA</th>
                </tr>
                </thead>
                <tbody>
                @foreach($righe as $riga)
                    <tr>
                        <td>{{ $riga->descrizione }}</td>
                        <td>{{ $riga->qta }}</td>
                        <td>€ {{ number_format($riga->pu, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($riga->pt, 2, ',', '.') }}</td>
                        <td>{{ $riga->iva }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gestione Pagamento Dilazionato -->
    <!-- Gestione Pagamento Dilazionato -->
    @if($testata->split_payment == 1)
        <div class="card mb-4">
            <div class="card-body">
                <h4>Pagamento Dilazionato</h4>
                <form id="rateForm" action="{{ route('fattura.aggiorna_rate', $testata->id) }}" method="POST">
                    @csrf
                    <div id="rateContainer">
                        @foreach($importo_rate as $index => $importo)
                            <div class="form-group d-flex align-items-center mb-2 rate-row" data-index="{{ $index }}">
                                <label class="me-2">Rata {{ $index + 1 }}:</label>
                                <input type="number" name="rate[{{ $index }}]" value="{{ $importo }}" class="form-control me-2" style="width: 120px;" required>
                                <input type="date" name="scadenza[{{ $index }}]" value="{{ $scadenze_rate[$index] }}" class="form-control me-2" style="width: 160px;" required>
                                <select name="status[{{ $index }}]" class="form-control me-2" style="width: 150px;">
                                    <option value="saldato" {{ $status_rate[$index] == 'saldato' ? 'selected' : '' }}>Saldata</option>
                                    <option value="da_saldare" {{ $status_rate[$index] == 'da_saldare' ? 'selected' : '' }}>Da saldare</option>
                                </select>
                                <button type="button" class="btn btn-danger btn-sm remove-rate">🗑️</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" id="addRateButton">+ Aggiungi Rata</button>
                    <button type="submit" class="btn btn-success btn-sm mt-3">Aggiorna Rate</button>
                </form>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const markAsPaidButton = document.getElementById("markAsPaidButton");

        if (markAsPaidButton) {
            markAsPaidButton.addEventListener("click", function () {
                if (confirm("Sei sicuro di voler contrassegnare questa fattura come saldata?")) {
                    fetch('{{ route("fattura.marca_saldata", $testata->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ id: {{ $testata->id }} })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Fattura contrassegnata come saldata con successo!');
                                location.reload(); // Ricarica la pagina per aggiornare lo stato
                            } else {
                                alert('Errore durante l\'aggiornamento dello stato della fattura.');
                            }
                        })
                        .catch(error => {
                            console.error('Errore:', error);
                            alert('Errore durante l\'operazione.');
                        });
                }
            });
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rateContainer = document.getElementById("rateContainer");
        const addRateButton = document.getElementById("addRateButton");

        // Contatore per le rate esistenti
        let rateCount = {{ count($importo_rate ?? []) }};

        // Funzione per aggiungere una nuova rata
        if (addRateButton && rateContainer) {
            addRateButton.addEventListener("click", function () {
                const newRateRow = `
                 <div class="form-group d-flex align-items-center mb-2 rate-row" data-index="${rateCount}">
                        <label class="me-2">Rata ${rateCount + 1}:</label>
                        <input type="number" name="rate[${rateCount}]" class="form-control me-2" style="width: 120px;" required>
                        <input type="date" name="scadenza[${rateCount}]" class="form-control me-2" style="width: 160px;" required>
                        <select name="status[${rateCount}]" class="form-control me-2" style="width: 150px;">
                            <option value="da_saldare">Da saldare</option>
                            <option value="saldato">Saldato</option>
                        </select>
                        <button type="button" class="btn btn-danger btn-sm remove-rate">🗑️</button>
                    </div>

            `;
                rateContainer.insertAdjacentHTML("beforeend", newRateRow);
                rateCount++;
            });
        }

        // Rimuovi una rata
        rateContainer.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-rate")) {
                const rateRow = e.target.closest(".rate-row");
                const rateIndex = rateRow.dataset.index;

                console.log("Rate index:", rateIndex); // Debug: stampa il valore di rateIndex

                if (!rateIndex) {
                    alert("Errore: indice della rata non trovato!");
                    return;
                }

                if (confirm('Sei sicuro di voler eliminare questa rata?')) {
                    fetch('{{ route('fattura.elimina_rata', $testata->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ rate_index: rateIndex }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data); // Debug: stampa la risposta del server
                            if (data.success) {
                                rateRow.remove(); // Rimuovi visivamente la rata
                                alert('Rata eliminata con successo!');
                            } else {
                                alert('Errore durante l\'eliminazione della rata: ' + (data.message || ''));
                            }
                        })
                        .catch(error => {
                            console.error('Errore durante l\'eliminazione:', error);
                            alert('Errore durante l\'eliminazione della rata.');
                        });
                }
            }
        });
    });

</script>

@include('default.common.footer')




