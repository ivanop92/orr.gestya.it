@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">
        <h2 class="text-center">Contratto di Vendita</h2>

        <div class="card" id="contrattoPrint">
            <div class="card-body">
                <h4 class="text-center">Dettagli Contratto</h4>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Riferimenti Cliente</h6>
                        <p><strong>Cliente:</strong> {{ $contratto->cliente_ragione_sociale }}</p>
                        <p><strong>P. IVA:</strong> {{ $contratto->cliente_piva ?? 'Non disponibile' }}</p>
                        <p><strong>Indirizzo:</strong> {{ $contratto->cliente_indirizzo ?? 'Non disponibile' }}</p>
                        <p><strong>Città:</strong> {{ $contratto->cliente_comune ?? 'Non disponibile' }}</p>
                        <p><strong>CAP:</strong> {{ $contratto->cliente_cap ?? 'Non disponibile' }}</p>
                        {{--<p><strong>SDI:</strong> {{ $contratto->cliente_sdi ?? 'Non disponibile' }}</p>--}}
                        <p><strong>PEC:</strong> {{ $contratto->cliente_pec ?? 'Non disponibile' }}</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <h6>Riferimenti Contratto</h6>
                        <p><strong>ID Contratto:</strong> {{ $contratto->id }}</p>
                        <p><strong>Data:</strong> {{ $contratto->data }}</p>
                        <p><strong>Tipologia:</strong>
                            @if($contratto->contratto_orario)
                                Contratto ad Ore
                            @else
                                Contratto Ordinario
                            @endif
                        </p>
                        @if($contratto->contratto_orario)
                            <p><strong>Ore:</strong> {{ $contratto->ore ?? 'Non specificate' }}</p>
                            <p><strong>Costo Orario:</strong> {{ $contratto->costo_orario ?? 'Non specificato' }} €</p>
                            <p><strong>Totale:</strong> {{ $contratto->ore * $contratto->costo_orario }} €</p>
                        @else
                            <p><strong>Prezzo:</strong> {{ $contratto->prezzo }} €</p>
                            <p><strong>IVA Inclusa:</strong> {{ $contratto->iva }}%</p>
                            <p><strong>Totale:</strong> {{ $contratto->prezzo }} €</p>
                        @endif
                    </div>
                </div>

                <hr>

                <h6>Oggetto del Contratto</h6>
                <p>{{ $contratto->descrizione }}</p>

                <hr>

                <h6>Allegati</h6>
                @if ($contratto->allegati)
                    @foreach (json_decode($contratto->allegati, true) as $allegato)
                        <a href="{{ url($allegato) }}" target="_blank" class="btn btn-link">{{ basename($allegato) }}</a><br>
                    @endforeach
                @else
                    <p>Nessun allegato disponibile.</p>
                @endif


                <hr>

                <p class="text-center mt-4"><strong>Il presente contratto è valido e vincolante per entrambe le parti.</strong></p>
            </div>
        </div>

        <div class="text-center mt-3">
            <button onclick="printContract()" class="btn btn-primary">Stampa Contratto</button>
            <a href="{{ url('contratti') }}" class="btn btn-secondary">Torna alla lista</a>
        </div>
    </div>
</div>

<script>
    function printContract() {
        const printContent = document.getElementById('contrattoPrint').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
    }
</script>

@include('default.common.footer')
