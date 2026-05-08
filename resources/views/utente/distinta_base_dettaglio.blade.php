@include('utente.common.header')

<div class="page-content mt-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h4>Modifica Distinta Base - Articolo: {{ $articolo->titolo }}</h4>
            </div>
            <div class="col-md-6">
                <a style="float:right;" class="btn btn-success my-2" href="{{ URL::asset('prodotti_finiti') }}">Torna Indietro</a>
            </div>
        </div>

        <form method="POST" autocomplete="off">
            @csrf
            <!-- Itera sulle fasi associate all'articolo -->
            @foreach($fasi_associate as $fase)
                <div class="card mb-3">
                    <div class="card-header">{{ $fase->descrizione }}</div>
                    <div class="card-body" id="fase_{{ $fase->id }}_container">
                        <!-- Contenitore per input dinamici dei materiali -->
                        @if(isset($distinta_base[$fase->id]))
                            @foreach($distinta_base[$fase->id] as $index => $materiale)
                                <div class="row mb-2 material-row">
                                    <div class="col-md-9">
                                        <label>Materiale <b style="color:red">*</b></label>
                                        <select name="materiale[{{ $fase->id }}][]" class="form-control select2"
                                                onchange="calcolaCostoTotale({{ $articolo->id }})">
                                            <option value="">Nessun Materiale</option>
                                            @foreach($materiali as $m)
                                                <option value="{{ $m->id }}" costo="{{ $m->prezzo }}"
                                                        {{ $materiale->id_materiale == $m->id ? 'selected' : '' }}>
                                                    {{ $m->titolo }} ({{ $m->um }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <label>Qta <b style="color:red">*</b></label>
                                        <input type="number" min="0" step="0.0001"
                                               name="quantita[{{ $fase->id }}][]"
                                               value="{{ $materiale->qta }}"
                                               class="form-control" onkeyup="calcolaCostoTotale({{ $articolo->id }})"
                                               onchange="calcolaCostoTotale({{ $articolo->id }})">
                                        <button type="button" class="btn btn-danger btn-sm ml-2" onclick="rimuoviRiga(this)">x</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <!-- Pulsante per aggiungere nuovi input -->
                        <button type="button" class="btn btn-primary mt-2" onclick="aggiungiRigaMateriale({{ $fase->id }})">+</button>
                    </div>
                </div>
            @endforeach

            <!-- Sezione di calcolo complessiva sotto tutte le fasi per l'articolo -->
            <div class="row">
                <div class="col-md-6 text-end">
                    <b>Costo Materie Prime Totale:</b><br><br>
                    <b>Prezzo di Vendita Totale:</b><br><br>
                    <b>Percentuale Costo Totale:</b><br>
                </div>
                <div class="col-md-3 text-start">
                    <b id="costo_materia_prima_totale_{{ $articolo->id }}"></b><br>
                    <input name="prezzo_totale" id="prezzo_totale_{{ $articolo->id }}" class="form-control" value="{{ $articolo->prezzo }}"
                           onkeyup="ricalcoloPercentualeTotale({{ $articolo->id }})" onchange="ricalcoloPercentualeTotale({{ $articolo->id }})"><br>
                    <b id="incidenza_totale_{{ $articolo->id }}"></b><br>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" name="modifica_db" class="btn btn-success">Salva Distinta Base</button>
            </div>
        </form>
    </div>
</div>

@include('utente.common.footer')

<!-- Javascript per il calcolo dei costi e l'aggiunta dinamica -->
<script type="text/javascript">
    function calcolaCostoTotale(articoloId) {
        let costoTotale = 0;

        @foreach($fasi_associate as $fase)
        document.querySelectorAll(`#fase_{{ $fase->id }}_container .material-row`).forEach(row => {
            const materialeSelect = row.querySelector('select');
            const quantitaInput = row.querySelector('input[type="number"]');

            if (materialeSelect && quantitaInput) {
                const costoMateriale = parseFloat(materialeSelect.selectedOptions[0]?.getAttribute('costo')) || 0;
                const quantita = parseFloat(quantitaInput.value) || 0;
                costoTotale += costoMateriale * quantita;
            }
        });
        @endforeach

        document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText = costoTotale.toFixed(4);
        ricalcoloPercentualeTotale(articoloId);
    }

    function ricalcoloPercentualeTotale(articoloId) {
        const costoTotale = parseFloat(document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText) || 0;
        const prezzoVenditaInput = document.getElementById(`prezzo_totale_${articoloId}`).value;
        const prezzoVenditaFinale = parseFloat(prezzoVenditaInput) || 0;

        if (prezzoVenditaFinale > 0) {
            const incidenza = (costoTotale / prezzoVenditaFinale) * 100;
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = incidenza.toFixed(2) + '%';
        } else {
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = '0%';
        }
    }

    function aggiungiRigaMateriale(faseId) {
        const container = document.getElementById(`fase_${faseId}_container`);
        const rowHtml = `
        <div class="row mb-2 material-row align-items-center">
            <div class="col-md-8">
                <label>Materiale <b style="color:red">*</b></label>
                <select name="materiale[${faseId}][]" class="form-control select2" onchange="calcolaCostoTotale({{ $articolo->id }})">
                    <option value="">Nessun Materiale</option>
                    @foreach($materiali as $m)
        <option value="{{ $m->id }}" costo="{{ $m->prezzo }}">{{ $m->titolo }} ({{ $m->um }})</option>
                    @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label>Qta <b style="color:red">*</b></label>
        <input type="number" min="0" step="0.0001" name="quantita[${faseId}][]" class="form-control" style="width: 100%;" onkeyup="calcolaCostoTotale({{ $articolo->id }})" onchange="calcolaCostoTotale({{ $articolo->id }})">
            </div>
            <div class="col-md-2 text-center">
                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="rimuoviRiga(this)">x</button>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', rowHtml);
    }


    function rimuoviRiga(button) {
        const row = button.closest('.material-row');
        row.remove();
        calcolaCostoTotale({{ $articolo->id }});
    }
</script>
