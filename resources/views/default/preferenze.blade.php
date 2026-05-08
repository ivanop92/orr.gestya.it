@include('default.common.header')

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h4>Impostazioni Solleciti Fatture</h4>
                </div>
                <div class="card-body">
                    <!-- Form per attivazione solleciti -->
                    <form action="{{ route('update.preferenze.solleciti') }}" method="POST">
                        @csrf
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="invioSolleciti" name="invio_solleciti"
                                    {{ $azienda->invio_mail_sollecito ? 'checked' : '' }}>
                            <label class="form-check-label" for="invioSolleciti">
                                Attiva invio automatico mail di sollecito per fatture non pagate
                            </label>
                        </div>

                        <!-- Template Email -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Template Email Sollecito (Fatture senza rate)</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="oggettoEmail">Oggetto Email</label>
                                    <input type="text" class="form-control" id="oggettoEmail" name="oggetto_email"
                                           value="{{ $azienda->template_oggetto_sollecito ?? 'Sollecito pagamento fattura {numero_fattura}' }}">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="templateEmail">Testo Email</label>
                                    <textarea class="form-control" id="templateEmail" name="template_email" rows="10">{{ $azienda->template_testo_sollecito ?? 'Gentile {ragione_sociale},

con la presente siamo a sollecitare il pagamento della fattura numero {numero_fattura} del {data_fattura} per un importo di € {importo_da_pagare}.

Vi preghiamo di procedere al saldo quanto prima.

Cordiali saluti,
{azienda_mittente}' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Template Email Sollecito (Fatture con rate)</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mt-3">
                                    <label for="templateEmailRate">Testo Email per Rate</label>
                                    <textarea class="form-control" id="templateEmailRate" name="template_email_rate" rows="10">{{ $azienda->template_testo_sollecito_rate ?? 'Gentile {ragione_sociale},

con la presente siamo a sollecitare il pagamento delle rate scadute relative alla fattura numero {numero_fattura} del {data_fattura}.

{dettaglio_rate}

L\'importo totale da saldare è di € {importo_da_pagare}.

Vi preghiamo di procedere al saldo quanto prima.

Cordiali saluti,
{azienda_mittente}' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                Campi disponibili:
                                <ul>
                                    <li>{ragione_sociale} - Nome dell'azienda cliente</li>
                                    <li>{numero_fattura} - Numero della fattura</li>
                                    <li>{data_fattura} - Data della fattura</li>
                                    <li>{importo_fattura} - Importo totale della fattura</li>
                                    <li>{importo_da_pagare} - Importo da saldare (totale fattura o somma rate scadute)</li>
                                    <li>{dettaglio_rate} - Elenco dettagliato delle rate scadute (solo per fatture con rate)</li>
                                    <li>{azienda_mittente} - Nome della vostra azienda</li>
                                </ul>
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">
                            Salva Impostazioni
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('default.common.footer')