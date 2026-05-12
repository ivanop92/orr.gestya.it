@php
    $manutWorkflowOn = DB::table('aziende')->where('id', $utente->id_azienda)->value('manut_workflow_accettazione_multistep');
    if (!empty($manutWorkflowOn) && isset($dotes)) {
        $statoAttuale = $dotes->stato_accettazione ?? null;
        $labels = \App\Services\WorkflowAccettazione::labels();
        $colori = \App\Services\WorkflowAccettazione::colori();
        $statoLabel  = $statoAttuale ? ($labels[$statoAttuale] ?? $statoAttuale) : 'Non Avviato';
        $statoColore = $statoAttuale ? ($colori[$statoAttuale] ?? 'secondary') : 'secondary';
        $azioniDisponibili = \App\Services\WorkflowAccettazione::azioniDisponibili($statoAttuale);
    }
@endphp

@if(!empty($manutWorkflowOn) && isset($dotes))
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <div>
                    <span class="text-muted me-2"><i class="ri-flow-chart me-1"></i>Stato Accettazione:</span>
                    <span class="badge bg-{{ $statoColore }} fs-6">{{ $statoLabel }}</span>
                    @if(!empty($dotes->tentativi) && $dotes->tentativi > 0)
                        <small class="text-muted ms-2">Tentativi: {{ $dotes->tentativi }}</small>
                    @endif
                </div>

                @if($statoAttuale === 'rifiutato' && !empty($dotes->motivo_rifiuto))
                    <div class="flex-grow-1">
                        <small class="text-danger">
                            <i class="ri-error-warning-line me-1"></i>
                            <strong>Motivo rifiuto:</strong> {{ $dotes->motivo_rifiuto }}
                        </small>
                    </div>
                @endif

                <div class="ms-auto d-flex flex-wrap gap-2">
                    @foreach($azioniDisponibili as $azione)
                        @if($azione === 'rifiuta')
                            <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#modal_rifiuto_workflow">
                                <i class="ri-close-circle-line me-1"></i> Rifiuta
                            </button>
                        @else
                            <form method="post" action="/utente/documento_workflow/{{ $dotes->id }}/{{ $azione }}" style="display:inline; margin:0;">
                                @csrf
                                @if($azione === 'invia_revisione')
                                    <button type="submit" class="btn btn-sm btn-soft-info">
                                        <i class="ri-send-plane-line me-1"></i> Invia in Revisione
                                    </button>
                                @elseif($azione === 'accetta')
                                    <button type="submit" class="btn btn-sm btn-soft-success">
                                        <i class="ri-check-line me-1"></i>
                                        {{ (!empty($dotes->tentativi) && $dotes->tentativi > 0) ? 'Riapprova' : 'Accetta' }}
                                    </button>
                                @elseif($azione === 'rilavora')
                                    <button type="submit" class="btn btn-sm btn-soft-warning">
                                        <i class="ri-restart-line me-1"></i> Rilavora
                                    </button>
                                @elseif($azione === 'marca_fatturabile')
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri-bill-line me-1"></i> Marca Fatturabile
                                    </button>
                                @endif
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(in_array('rifiuta', $azioniDisponibili))
        <div class="modal fade" id="modal_rifiuto_workflow" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="/utente/documento_workflow/{{ $dotes->id }}/rifiuta">
                    @csrf
                    <div class="modal-content border-0">
                        <div class="modal-header bg-soft-danger p-3">
                            <h5 class="modal-title">Rifiuta Documento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Motivo del rifiuto <b style="color:red">*</b></label>
                            <textarea name="motivo_rifiuto" class="form-control" rows="3" required placeholder="Spiega perché il documento è rifiutato"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="ri-close-circle-line me-1"></i> Conferma Rifiuto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
