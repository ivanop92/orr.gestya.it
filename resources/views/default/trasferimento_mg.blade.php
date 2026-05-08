@include('default.common.header')
<div class="container-fluid" style="margin-top: 100px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Trasferimento Articoli tra Magazzini</h1>
            <!-- Input per la scansione barcode -->
            <div class="input-group mb-5">
                <input type="text" id="barcodeInput" class="form-control form-control-lg text-center" placeholder="Scansiona un barcode" disabled>
            </div>

            <!-- Tabella per mostrare l'articolo trovato -->
            <table class="table table-bordered table-striped" id="articleTable" style="display: none;">
                <thead class="table-dark">
                <tr>
                    <th>Descrizione</th>
                    <th>Lotto</th>
                    <th>Scadenza</th>
                    <th>Magazzino</th>
                    <th>Giacenza</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody id="articleDetails">
                <!-- Righe di dettaglio articolo vengono aggiunte dinamicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modale per il trasferimento tra magazzini -->
<div class="modal fade" id="trasferimentoModal" tabindex="-1" aria-labelledby="trasferimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trasferimentoModalLabel">Trasferimento Giacenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trasferimentoForm">
                    <div class="mb-3">
                        <label for="transferQuantity" class="form-label">Quantità da Trasferire</label>
                        <input type="number" id="transferQuantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="targetWarehouse" class="form-label">Magazzino di Destinazione</label>
                        <select id="targetWarehouse" class="form-select" required>
                            <option value="" disabled selected>Seleziona Magazzino</option>
                            <!-- Aggiungi i magazzini dalla variabile passata dal controller -->
                            @foreach($magazzini as $magazzino)
                                <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p id="lottoInfo" class="text-muted">Lotto: </p>
                    <p id="scadenzaInfo" class="text-muted">Scadenza: </p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitTrasferimento()">Conferma Trasferimento</button>
            </div>
        </div>
    </div>
</div>

@include('default.common.footer')

<script src="https://unpkg.com/onscan.js/onscan.min.js"></script>
<script>
    let currentArticleId = null;
    let currentWarehouseId = null;

    onScan.attachTo(document, {
        onScan: function(barcode) {
            let input = document.getElementById('barcodeInput');
            input.value = barcode;
            checkArticle(barcode);
            console.log(barcode);
        },
        onScanError: function(e) {
            console.error('Errore di scansione:', e);
        }
    });

    // Funzione per verificare l'esistenza dell'articolo nel database tramite AJAX
    function checkArticle(barcode) {
        fetch(`/check?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayArticle(data.article, data.giacenze);
                } else {
                    alert('Articolo non trovato.');
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
            });
    }

    // Funzione per visualizzare i dettagli dell'articolo nella tabella
    function displayArticle(article, giacenze) {
        currentArticleId = article.id;
        document.getElementById('articleTable').style.display = 'table';

        if (!giacenze || giacenze.length === 0) {
            document.getElementById('articleDetails').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">Nessuna giacenza trovata per questo articolo.</td>
                </tr>
            `;
            return;
        }

        let rows = '';
        giacenze.forEach(giacenza => {
            rows += `
            <tr>
                <td>${article.titolo}</td>
                <td>${giacenza.lotto}</td>
                <td>${giacenza.scadenza}</td>
                <td>${giacenza.magazzino_descrizione}</td>
                <td>${giacenza.totale_giacenza}</td>
                <td>
                    <button class="btn btn-primary" onclick="openTrasferimentoModal(${giacenza.id_magazzino}, '${giacenza.lotto}', '${giacenza.scadenza}')">Trasferisci</button>
                </td>
            </tr>
        `;
        });

        document.getElementById('articleDetails').innerHTML = rows;
    }

    // Funzione per aprire la modale per il trasferimento
    function openTrasferimentoModal(magazzinoId, lotto, scadenza) {
        currentWarehouseId = magazzinoId;
        document.getElementById('trasferimentoForm').reset(); // Resetta i campi della modale
        document.getElementById('lottoInfo').innerText = `Lotto: ${lotto}`;
        document.getElementById('scadenzaInfo').innerText = `Scadenza: ${scadenza}`; // Mostra il lotto selezionato nella modale
        let trasferimentoModal = new bootstrap.Modal(document.getElementById('trasferimentoModal'));
        trasferimentoModal.show();
    }

    // Funzione per sottomettere il trasferimento a magazzino
    function submitTrasferimento() {
        const transferQuantity = document.getElementById('transferQuantity').value;
        const targetWarehouse = document.getElementById('targetWarehouse').value;
        const lotto = document.getElementById('lottoInfo').innerText.replace('Lotto: ', '').trim(); // Ottieni il lotto visualizzato
        const scadenza = document.getElementById('scadenzaInfo').innerText.replace('Scadenza: ', '').trim(); // Ottieni il lotto visualizzato


        if (transferQuantity && targetWarehouse) {
            fetch(`/trasferimento-magazzino/${currentArticleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    quantita: transferQuantity,
                    magazzino_destinazione: targetWarehouse,
                    magazzino_origine: currentWarehouseId,
                    lotto: lotto,
                    scadenza: scadenza,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Trasferimento completato con successo!');
                        document.getElementById('articleDetails').innerHTML = '';
                        document.getElementById('barcodeInput').value = '';
                        let trasferimentoModal = bootstrap.Modal.getInstance(document.getElementById('trasferimentoModal'));
                        trasferimentoModal.hide();
                    } else {
                        alert(data.message || 'Errore nel trasferimento.');
                    }
                })
                .catch(error => {
                    console.error('Errore AJAX:', error);
                });
        }
    }
</script>
