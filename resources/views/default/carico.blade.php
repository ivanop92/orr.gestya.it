@include('default.common.header')
<div class="container-fluid" style="margin-top: 100px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Carico a Magazzino</h1>
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

<!-- Modale per il carico a magazzino -->
<div class="modal fade" id="caricoModal" tabindex="-1" aria-labelledby="caricoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caricoModalLabel">Carico a Magazzino</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="caricoForm">
                    <div class="mb-3">
                        <label for="addQuantity" class="form-label">Quantità da Caricare</label>
                        <input type="number" id="addQuantity" class="form-control" required>
                    </div>
                    <label class="form-label">Magazzino<b style="color:red">*</b></label>
                    <select name="id_mg" id="mg" class="form-control" required>
                        <option value="">Seleziona Magazzino</option>
                        @foreach ($magazzini as $magazzino)
                            <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                        @endforeach
                    </select>
                    <div class="mb-3">
                        <label for="lotto" class="form-label">Lotto</label>
                        <input type="text" id="lotto" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="addScadenza" class="form-label">Scadenza</label>
                        <input type="date" id="addScadenza" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="causale" class="form-label">Causale</label>
                        <input type="text" id="causale" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitCarico()">Conferma Carico</button>
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
            decode(barcode);
            console.log(barcode);
        },
        onScanError: function(e) {
            console.error('Errore di scansione:', e);
        }
    });

    function decode(barcode) {
        jQuery.ajax({
            url: "<?php echo URL::asset('decode-barcode') ?>",
            type:'GET',
            data:{
                barcode: barcode,
            },
            success: function(result){
                console.log('result', result);
            }
        });
    }

    // Funzione per verificare l'esistenza dell'articolo nel database tramite AJAX
    function checkArticle(barcode) {
        fetch(`/check?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayArticle(data.article);
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
                    <button class="btn btn-primary" onclick="openCaricoModal(${article.id})">Carica</button>
                </td>
            </tr>
        `;
    }

    // Funzione per aprire la modale per caricare la giacenza
    function openCaricoModal(articleId) {
        currentArticleId = articleId;
        document.getElementById('caricoForm').reset(); // Resetta i campi della modale
        let caricoModal = new bootstrap.Modal(document.getElementById('caricoModal'));
        caricoModal.show();
    }

    // Funzione per sottomettere il carico a magazzino
    function submitCarico() {
        const addQuantity = document.getElementById('addQuantity').value;
        const causale = document.getElementById('causale').value;
        const lotto = document.getElementById('lotto').value;
        const mg = document.getElementById('mg').value;
        const addScadenza = document.getElementById('addScadenza').value;

        if (addQuantity && causale) {
            fetch(`/carico-magazzino/${currentArticleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ giacenza: addQuantity, causale: causale, lotto: lotto, mg: mg, addScadenza: addScadenza })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Carico a magazzino effettuato con successo!');
                        document.getElementById('articleDetails').innerHTML = '';
                        document.getElementById('barcodeInput').value = '';
                        let caricoModal = bootstrap.Modal.getInstance(document.getElementById('caricoModal'));
                        caricoModal.hide();
                    } else {
                        alert('Errore nel carico a magazzino.');
                    }
                })
                .catch(error => {
                    console.error('Errore AJAX:', error);
                });
        }
    }
</script>
