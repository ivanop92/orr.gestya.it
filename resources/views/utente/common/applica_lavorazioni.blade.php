@php
    $lavorazioni_disponibili = DB::table('lavorazioni')
        ->where('id_azienda', $utente->id_azienda)
        ->where('attivo', 1)
        ->orderBy('descrizione')
        ->get();
@endphp

@if(isset($dotes) && count($lavorazioni_disponibili) > 0)
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="flex-grow-1">
                    <i class="mdi mdi-package-variant me-2 text-muted"></i>
                    <span class="text-muted">Applica righe da template di lavorazione (copia righe nel documento)</span>
                </div>
                <button class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_applica_lavorazioni">
                    <i class="ri-add-circle-line me-1"></i> Applica Lavorazioni
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_applica_lavorazioni" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-primary p-3">
                    <h5 class="modal-title"><i class="mdi mdi-package-variant me-2"></i>Applica Lavorazioni al Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="/utente/applica_lavorazioni_a_documento/{{ $dotes->id }}">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Seleziona una o più lavorazioni. Le loro righe saranno copiate in coda al documento corrente.
                            Gli aggregati testata (imponibile / imposta / totale) verranno ricalcolati automaticamente.
                        </p>
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width:42px;"></th>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th class="text-end">Totale</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lavorazioni_disponibili as $lav)
                                    <tr>
                                        <td><input type="checkbox" name="id_lavorazioni[]" value="{{ $lav->id }}" class="form-check-input"></td>
                                        <td><strong>{{ $lav->codice }}</strong></td>
                                        <td>{{ $lav->descrizione }}</td>
                                        <td class="text-end">€ {{ number_format($lav->totale,2,',','.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-add-circle-line me-1"></i> Applica al Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
