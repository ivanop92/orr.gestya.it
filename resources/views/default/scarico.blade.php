@include('default.common.header')
<div class="container-fluid" style="margin-top: 100px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Scarico a Magazzino</h1>
            <!-- Input per la scansione barcode -->
            <div class="input-group mb-5">
                <input type="text" id="barcodeInput" class="form-control form-control-lg text-center" placeholder="Scansiona un barcode" disabled>
            </div>

            <!-- Tabella per mostrare l'articolo trovato -->
            <table class="table table-bordered table-striped" id="articleTable" style="display: none;">
                <thead class="table-dark">
                <tr>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th>Giacenza Attuale</th>
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

<!-- Modale per lo scarico a magazzino -->
<div class="modal fade" id="scaricoModal" tabindex="-1" aria-labelledby="scaricoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scaricoModalLabel">Scarico a Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scaricoForm">
                    <div class="mb-3">
                        <label for="removeQuantity" class="form-label">Quantità da Scaricare</label>
                        <input type="number" id="removeQuantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="lottoMagazzinoSelect" class="form-label">Lotto e Magazzino</label>
                        <select id="lottoMagazzinoSelect" class="form-select" required>
                            <!-- Le opzioni vengono aggiunte dinamicamente -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="causale" class="form-label">Causale</label>
                        <input type="text" id="causale" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitScarico()">Conferma Scarico</button>
            </div>
        </div>
    </div>
</div>

@include('default.common.footer')

<script src="https://unpkg.com/onscan.js/onscan.min.js"></script>
<script>
    let currentArticleId = null;

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
                    displayArticle(data.article);
                    populateLottoMagazzinoSelect(data.lottiMagazzini);
                } else {
                    alert('Articolo non trovato.');
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
            });
    }

    // Funzione per visualizzare i dettagli dell'articolo nella tabella
    function displayArticle(article) {
        currentArticleId = article.id;
        document.getElementById('articleTable').style.display = 'table';
        document.getElementById('articleDetails').innerHTML = `
            <tr>
                <td>${article.barcode}</td>
                <td>${article.titolo}</td>
                <td>${article.giacenza}</td>

               <td>
                    <button class="btn btn-primary" onclick="openScaricoModal(${article.id})">Scarica</button>
                </td>
            </tr>
        `;
    }
    // Funzione per popolare il menu a tendina con lotti e magazzini
    function populateLottoMagazzinoSelect(lottiMagazzini) {
        const select = document.getElementById('lottoMagazzinoSelect');
        select.innerHTML = ''; // Resetta le opzioni
        lottiMagazzini.forEach(lm => {
            const option = document.createElement('option');
            option.value = `${lm.lotto}|${lm.id_magazzino} | ${lm.scadenza}`;
            option.textContent = `${lm.lotto} - ${lm.magazzino_descrizione} - ${lm.scadenza}`;
            select.appendChild(option);
        });
    }
    // Funzione per aprire la modale per scaricare la giacenza
    function openScaricoModal(articleId) {
        currentArticleId = articleId;
        document.getElementById('scaricoForm').reset(); // Resetta i campi della modale
        let scaricoModal = new bootstrap.Modal(document.getElementById('scaricoModal'));
        scaricoModal.show();
    }

    // Funzione per sottomettere lo scarico a magazzino
    function submitScarico() {
        const removeQuantity = document.getElementById('removeQuantity').value;
        const causale = document.getElementById('causale').value;
        const [lotto, magazzinoId, scadenza] = document.getElementById('lottoMagazzinoSelect').value.split('|');

        if (removeQuantity && causale) {
            fetch(`/scarico-magazzino/${currentArticleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    giacenza: removeQuantity,
                    causale: causale,
                    lotto: lotto,
                    magazzinoId: magazzinoId,
                    scadenza: scadenza
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Scarico a magazzino effettuato con successo!');
                        document.getElementById('articleDetails').innerHTML = '';
                        document.getElementById('barcodeInput').value = '';
                        let scaricoModal = bootstrap.Modal.getInstance(document.getElementById('scaricoModal'));
                        scaricoModal.hide();
                    } else {
                        alert('Errore nello scarico a magazzino.');
                    }
                })
                .catch(error => {
                    console.error('Errore AJAX:', error);
                });
        }
    }
</script>
