@include('default.common.header')

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <!-- Select per l'anno -->
            <label for="selectAnno">Seleziona Anno:</label>
            <select id="selectAnno" class="form-select">
                @foreach(range(date('Y'), date('Y') - 5) as $anno)
                    <option value="{{ $anno }}">{{ $anno }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 text-end">
            <!-- Bottoni per i mesi -->
            <div class="btn-group" role="group">
                @foreach(range(1, 12) as $mese)
                    <button type="button" class="btn btn-outline-primary btn-mese" data-mese="{{ $mese }}">
                        {{ DateTime::createFromFormat('!m', $mese)->format('F') }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tabella dei documenti -->
    <div id="documentiContainer">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Numero</th>
                <th>Data Documento</th>
                <th>Tipologia</th>
                <th>Totale</th>
                <th>Cliente</th>
            </tr>
            </thead>
            <tbody id="documentiTableBody">
            <tr>
                <td colspan="5" class="text-center">Seleziona un mese per visualizzare i documenti</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAnno = document.getElementById('selectAnno');
        const btnMesi = document.querySelectorAll('.btn-mese');
        const documentiTableBody = document.getElementById('documentiTableBody');

        // Funzione per aggiornare la tabella
        function aggiornaTabella(anno, mese) {
            fetch('{{ route('documenti.per.mese') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ anno, mese }),
            })
                .then(response => response.json())
                .then(data => {
                    documentiTableBody.innerHTML = '';
                    if (data.documenti.length > 0) {
                        data.documenti.forEach(doc => {
                            documentiTableBody.innerHTML += `
                                <tr>
                                    <td>${doc.numero}</td>
                                    <td>${new Date(doc.data_doc).toLocaleDateString()}</td>
                                    <td>${doc.tipologia_documento}</td>
                                    <td>€ ${parseFloat(doc.totale).toFixed(2)}</td>
                                    <td>${doc.cliente}</td>
                                </tr>`;
                        });
                    } else {
                        documentiTableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">Nessun documento trovato</td>
                            </tr>`;
                    }
                })
                .catch(error => console.error('Errore:', error));
        }

        // Event Listener per i bottoni dei mesi
        btnMesi.forEach(btn => {
            btn.addEventListener('click', function () {
                const mese = this.dataset.mese;
                const anno = selectAnno.value;
                aggiornaTabella(anno, mese);
            });
        });
    });
</script>

@include('default.common.footer')
