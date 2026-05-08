@include('default.common.header')

<div class="container-fluid" style="margin-top: 100px;">
    <!-- Barra Superiore con Anno e Mesi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center bg-light p-5 rounded">
                <!-- Select per l'anno -->
                <div>
                    <select id="selectAnno" class="form-select me-3">
                        @foreach(range(date('Y') - 5, date('Y')) as $year)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Mesi -->
                @foreach(range(1, 12) as $month)
                    <div class="month-selector d-flex flex-column align-items-center border justify-content-center mx-2 p-2 rounded"
                         data-month="{{ $month }}" style="width: 90px">
                        <div class="text-muted">{{ DateTime::createFromFormat('!m', $month)->format('M') }}</div>
                        <div class="fw-bold text-success" id="doc-count-{{ $month }}">0 doc</div>
                        <div class="fw-bold text-success" id="doc-total-{{ $month }}">0 €</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filtro Tipi di Documento -->
    <div class="d-flex mb-4 " id="tipoDocumentiContainer">
        <!-- Pulsanti dinamici saranno caricati tramite AJAX -->
    </div>

    <!-- Tabella con i Documenti -->
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Stato</th>
                    <th>Cliente</th>
                    <th>Oggetto</th>
                    <th>Data e Numero</th>
                    <th>Prox. Scadenza</th>
                    <th>Importo</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody id="documentTableBody">
                <!-- I dati verranno caricati tramite AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const documentTableBody = document.getElementById('documentTableBody');
        const selectAnno = document.getElementById('selectAnno');
        const tipoDocumentiContainer = document.getElementById('tipoDocumentiContainer');
        let currentMonth = new Date().getMonth() + 1; // Ottieni il mese corrente (JavaScript usa 0-based index)
        let currentYear = selectAnno.value; // Prendi l'anno corrente dalla select
        let currentTipoDocumento = '';

        // Funzione per caricare i documenti di un mese e tipo
        function loadDocuments(month, year, tipoDocumento = '') {
            fetch(`/get-documenti-per-mese?mese=${month}&anno=${year}&tipo_documento=${tipoDocumento}`)
                .then(response => response.json())
                .then(data => {
                    // Svuota la tabella
                    documentTableBody.innerHTML = '';
                    if (data.documenti.length === 0) {
                        documentTableBody.innerHTML = `<tr><td colspan="7" class="text-center">Nessun documento trovato</td></tr>`;
                    } else {
                        data.documenti.forEach(doc => {
                            documentTableBody.innerHTML += `
                        <tr>
                            <td>
                                <span class="badge ${doc.stato === 1 ? 'bg-success' : doc.stato === 0 ? 'bg-warning' : doc.stato === 2 ? 'bg-danger' : 'bg-secondary'}">
                                    ${doc.stato === 1 ? 'Inviata' : doc.stato === 0 ? 'Da inviare' : doc.stato === 2 ? 'Rifiutata' : 'Sconosciuto'}
                                </span>
                            </td>
                            <td>${doc.cliente || 'N/D'}</td>
                            <td>${doc.oggetto || 'N/D'}</td>
                            <td>${doc.data_doc.split(' ')[0]} (#${doc.numero || 'N/D'})</td>
                            <td class="${doc.saldata === 1 ? 'text-success' : 'text-danger'}">
                                ${doc.saldata === 1 ? '<strong>Saldata</strong>' : (doc.prox_scadenza || '-')}
                            </td>
                            <td>€ ${parseFloat(doc.importo || 0).toLocaleString('it-IT', { minimumFractionDigits: 2 })}</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Scarica</button>
                                <button class="btn btn-secondary btn-sm">Invia</button>
                            </td>
                        </tr>`;
                        });
                    }

                    // Aggiorna i dati dei mesi
                    document.getElementById(`doc-count-${month}`).innerText = `${data.count_documenti} doc`;
                    document.getElementById(`doc-total-${month}`).innerText = `€ ${parseFloat(data.total_importo || 0).toLocaleString('it-IT', { minimumFractionDigits: 2 })}`;

                    // Aggiorna i pulsanti per i tipi di documento
                    // Aggiorna i pulsanti per i tipi di documento
                    // Aggiorna i pulsanti per i tipi di documento
                    tipoDocumentiContainer.innerHTML = '';
                    data.tipi_documento.forEach(tipo => {
                        tipoDocumentiContainer.innerHTML += `
        <button class="btn btn-outline-primary me-2 tipo-documento-btn" data-tipo="${tipo.tipo_documento}">
            ${tipo.tipo_documento} (${tipo.count})
        </button>`;
                    });

// Aggiungi eventi ai pulsanti dei tipi di documento
                    document.querySelectorAll('.tipo-documento-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            // Rimuovi lo stato attivo da tutti i pulsanti
                            document.querySelectorAll('.tipo-documento-btn').forEach(button => {
                                button.classList.remove('bg-primary', 'text-white');
                                button.classList.add('btn-outline-primary');
                            });

                            // Aggiungi lo stato attivo al pulsante selezionato
                            this.classList.remove('btn-outline-primary');
                            this.classList.add('bg-primary', 'text-white');

                            // Carica i documenti per il tipo selezionato
                            const tipoDocumento = this.dataset.tipo;
                            loadDocuments(currentMonth, currentYear, tipoDocumento);
                        });
                    });

                })
                .catch(error => console.error('Errore nel caricamento dei documenti:', error));
        }

        // Evento per selezione mese
        document.querySelectorAll('.month-selector').forEach(selector => {
            selector.addEventListener('click', function () {
                const month = this.dataset.month;
                currentMonth = month;
                currentYear = selectAnno.value;
                loadDocuments(month, currentYear, currentTipoDocumento);

                // Evidenzia il mese selezionato
                document.querySelectorAll('.month-selector').forEach(btn => btn.classList.remove('bg-primary', 'text-white'));
                this.classList.add('bg-primary', 'text-white');
            });
        });

        // Evento per selezione anno
        selectAnno.addEventListener('change', function () {
            currentYear = this.value;

            // Resetta la vista
            document.querySelectorAll('.month-selector').forEach(selector => {
                const month = selector.dataset.month;
                document.getElementById(`doc-count-${month}`).innerText = '0 doc';
                document.getElementById(`doc-total-${month}`).innerText = '0 €';
            });

            // Svuota la tabella e il filtro dei documenti
            documentTableBody.innerHTML = `<tr><td colspan="7" class="text-center">Seleziona un mese per visualizzare i documenti</td></tr>`;
            tipoDocumentiContainer.innerHTML = '';
        });

        // Carica i documenti per il mese corrente all'avvio
        loadDocuments(currentMonth, currentYear);
        document.querySelector(`.month-selector[data-month="${currentMonth}"]`).classList.add('bg-primary', 'text-white');
    });



</script>


@include('default.common.footer')
