@extends('produzione.layout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dettaglio ODL #{{ $odl->numero }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ url('produzione/dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Dettaglio ODL</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <!-- Informazioni generali dell'ODL -->
                <div class="col-xl-12 mb-4">
                    <div class="card overflow-hidden {{ $odl->stato == 0 ? 'border-danger' : ($odl->stato == 1 ? 'border-warning' : 'border-success') }}">
                        <div class="card-header {{ $odl->stato == 0 ? 'bg-danger' : ($odl->stato == 1 ? 'bg-warning' : 'bg-success') }} bg-gradient">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm rounded-circle bg-white text-{{ $odl->stato == 0 ? 'danger' : ($odl->stato == 1 ? 'warning' : 'success') }} me-3">
                                        <i class="ri-file-list-3-line fs-20"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0 text-white">Informazioni Ordine di Lavoro</h5>
                                </div>
                                <div>
                                    @if($odl->stato == 0)
                                        <span class="badge rounded-pill bg-white text-danger fs-12 fw-medium">In Attesa</span>
                                    @elseif($odl->stato == 1)
                                        <span class="badge rounded-pill bg-white text-warning fs-12 fw-medium">In Produzione</span>
                                    @elseif($odl->stato == 2)
                                        <span class="badge rounded-pill bg-white text-success fs-12 fw-medium">Completato</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body position-relative">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 {{ $odl->stato == 0 ? 'bg-soft-danger' : ($odl->stato == 1 ? 'bg-soft-warning' : 'bg-soft-success') }} rounded">
                                            <i class="ri-hashtag fs-20 {{ $odl->stato == 0 ? 'text-danger' : ($odl->stato == 1 ? 'text-warning' : 'text-success') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Numero ODL</h6>
                                            <p class="text-muted mb-0 fs-13">{{ $odl->numero }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 {{ $odl->stato == 0 ? 'bg-soft-danger' : ($odl->stato == 1 ? 'bg-soft-warning' : 'bg-soft-success') }} rounded">
                                            <i class="ri-calendar-line fs-20 {{ $odl->stato == 0 ? 'text-danger' : ($odl->stato == 1 ? 'text-warning' : 'text-success') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Data</h6>
                                            <p class="text-muted mb-0 fs-13">{{ date('d/m/Y', strtotime($odl->data)) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 {{ $odl->stato == 0 ? 'bg-soft-danger' : ($odl->stato == 1 ? 'bg-soft-warning' : 'bg-soft-success') }} rounded">
                                            <i class="ri-box-3-line fs-20 {{ $odl->stato == 0 ? 'text-danger' : ($odl->stato == 1 ? 'text-warning' : 'text-success') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Articolo</h6>
                                            <p class="text-muted mb-0 fs-13 text-truncate" title="{{ $articolo->titolo }} ({{ $articolo->codice_articolo }})">
                                                {{ $articolo->titolo }} ({{ $articolo->codice_articolo }})
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex h-100">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 {{ $odl->stato == 0 ? 'bg-soft-danger' : ($odl->stato == 1 ? 'bg-soft-warning' : 'bg-soft-success') }} rounded">
                                            <i class="ri-numbers-line fs-20 {{ $odl->stato == 0 ? 'text-danger' : ($odl->stato == 1 ? 'text-warning' : 'text-success') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Quantità</h6>
                                            <p class="text-muted mb-0 fs-13">{{ $odl->qta }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($odl->note)
                                <div class="mt-4">
                                    <div class="d-flex">
                                        <div class="avatar flex-shrink-0 avatar-md me-3 {{ $odl->stato == 0 ? 'bg-soft-danger' : ($odl->stato == 1 ? 'bg-soft-warning' : 'bg-soft-success') }} rounded">
                                            <i class="ri-file-text-line fs-20 {{ $odl->stato == 0 ? 'text-danger' : ($odl->stato == 1 ? 'text-warning' : 'text-success') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14 mb-1">Note</h6>
                                            <p class="text-muted mb-0 fs-13">{{ $odl->note }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Titolo sezione fasi -->
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm rounded-circle {{ $odl->stato == 0 ? 'bg-danger' : ($odl->stato == 1 ? 'bg-warning' : 'bg-success') }} me-2">
                                <i class="ri-list-check-2 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Fasi di Lavorazione</h5>
                        </div>
                    </div>
                </div>

                <!-- Fasi di lavorazione -->
                <div class="col-xl-12">
                    <div class="row">
                        @foreach($odl_righe as $index => $riga)
                            <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm overflow-hidden">
                                    <div class="ribbon-box">
                                        <div class="ribbon ribbon-{{ $riga->completato == 1 ? 'success' : ($riga->inizio ? 'warning' : 'danger') }} ribbon-shape">
                                            Fase {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="card-header bg-{{ $riga->completato == 1 ? 'success' : ($riga->inizio ? 'warning' : 'danger') }} bg-gradient">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm rounded-circle bg-white me-3">
                                                    <i class="ri-{{ $riga->completato == 1 ? 'check-double-line' : ($riga->inizio ? 'loader-4-line' : 'time-line') }} text-{{ $riga->completato == 1 ? 'success' : ($riga->inizio ? 'warning' : 'danger') }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0 text-white">{{ $riga->nome_fase }}</h5>
                                            </div>
                                            <div>
                                                @if($riga->completato == 1)
                                                    <span class="badge rounded-pill bg-white text-success">Completata</span>
                                                @elseif($riga->inizio)
                                                    <span class="badge rounded-pill bg-white text-warning">In corso</span>
                                                @else
                                                    <span class="badge rounded-pill bg-white text-danger">In attesa</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">

                                        <!-- Nome Fase ben visibile -->
                                        <div class="text-center mb-3">
                                            <span class="badge bg-{{ $riga->completato == 1 ? 'success' : ($riga->inizio ? 'warning' : 'danger') }} fs-16 px-3 py-2">
                                                <i class="ri-settings-3-line me-1"></i> {{ $riga->nome_fase }}
                                            </span>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-xs rounded bg-light text-muted me-2">
                                                            <i class="ri-play-circle-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <p class="text-muted mb-0 fs-12">Inizio</p>
                                                        <p class="mb-0 fs-13 fw-medium">{{ $riga->inizio ? date('d/m/Y H:i', strtotime($riga->inizio)) : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-xs rounded bg-light text-muted me-2">
                                                            <i class="ri-stop-circle-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <p class="text-muted mb-0 fs-12">Fine</p>
                                                        <p class="mb-0 fs-13 fw-medium">{{ $riga->fine ? date('d/m/Y H:i', strtotime($riga->fine)) : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar avatar-xs rounded bg-light text-muted me-2">
                                                        <i class="ri-stack-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="text-muted mb-0 fs-12">Avanzamento</p>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1 me-2">
                                                            <div class="progress mt-2" style="height: 6px;">
                                                                @php
                                                                    $percentuale = $riga->qta > 0 ? round((($riga->qta_fatta ?? 0) / $riga->qta) * 100) : 0;
                                                                @endphp
                                                                <div class="progress-bar bg-{{ $riga->completato == 1 ? 'success' : ($riga->inizio ? 'warning' : 'danger') }}"
                                                                     role="progressbar"
                                                                     style="width: {{ $percentuale }}%;"
                                                                     aria-valuenow="{{ $percentuale }}"
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <span class="fs-13 fw-medium">{{ $percentuale }}%</span>
                                                    </div>
                                                    <p class="mb-0 mt-1 fs-13 fw-medium">{{ $riga->qta_fatta ?? 0 }} / {{ $riga->qta }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Prerequisiti mancanti -->
                                        @if(isset($riga->fasi_precedenti_ok) && !$riga->fasi_precedenti_ok)
                                            <div class="alert alert-warning mt-3 mb-3 py-2">
                                                <i class="ri-error-warning-line me-1"></i>
                                                <strong>Prerequisiti mancanti:</strong><br>
                                                @foreach($riga->prerequisiti_mancanti as $prereq)
                                                    <span class="badge bg-warning text-dark me-1">{{ $prereq }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="d-grid mt-4">
                                            @if(isset($riga->fasi_precedenti_ok) && !$riga->fasi_precedenti_ok)
                                                <button type="button" class="btn btn-secondary rounded-pill waves-effect waves-light" disabled>
                                                    <i class="ri-lock-line me-1 align-middle"></i> In attesa fasi precedenti
                                                </button>
                                            @elseif($riga->inizio == null)
                                                <form action="{{ url('produzione/start_fase') }}" method="POST">
                                                    <input type="hidden" name="id" value="{{ $riga->id }}">
                                                    <button type="submit" class="btn btn-danger rounded-pill waves-effect waves-light w-100">
                                                        <i class="ri-play-fill me-1 align-middle"></i> Inizia Fase
                                                    </button>
                                                </form>
                                            @elseif($riga->fine == null)
                                                <button type="button" class="btn btn-warning rounded-pill waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#fineFaseModal{{ $riga->id }}">
                                                    <i class="ri-stop-fill me-1 align-middle"></i> Termina Fase
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success rounded-pill waves-effect waves-light" disabled>
                                                    <i class="ri-check-double-line me-1 align-middle"></i> Fase Completata
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Allegati della fase -->
                                        @if(isset($riga->allegati) && count($riga->allegati) > 0)
                                        <div class="mt-4">
                                            <h6 class="fs-14 mb-2"><i class="ri-attachment-2 me-1"></i> Allegati</h6>
                                            <div class="list-group list-group-flush">
                                                @foreach($riga->allegati as $allegato)
                                                <a href="/{{ $allegato->path_file }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center px-0 py-2">
                                                    <div class="avatar avatar-xs rounded bg-light text-muted me-2">
                                                        @if(str_contains($allegato->tipo_file ?? '', 'image'))
                                                            <i class="ri-image-line"></i>
                                                        @elseif(str_contains($allegato->tipo_file ?? '', 'pdf'))
                                                            <i class="ri-file-pdf-line"></i>
                                                        @else
                                                            <i class="ri-file-line"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1 ms-1">
                                                        <p class="mb-0 fs-13 fw-medium">{{ $allegato->nome_originale }}</p>
                                                        @if($allegato->descrizione)
                                                        <p class="mb-0 fs-12 text-muted">{{ $allegato->descrizione }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="fs-12 text-muted">{{ date('d/m/Y', strtotime($allegato->created_at)) }}</span>
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Semilavorati da produrre in questa fase -->
                                        @if(isset($odl_semilavorati))
                                            @php
                                                $semi_fase = array_filter($odl_semilavorati, function($s) use ($riga) {
                                                    return $s->id_fase == $riga->id_fase;
                                                });
                                            @endphp
                                            @if(count($semi_fase) > 0)
                                            <div class="mt-4">
                                                <h6 class="fs-14 mb-2"><i class="ri-settings-3-line me-1 text-warning"></i> Semilavorati da produrre</h6>
                                                @foreach($semi_fase as $semi)
                                                <div class="border rounded p-2 mb-2 {{ $semi->stato == 1 ? 'border-success bg-success bg-opacity-10' : 'border-warning bg-warning bg-opacity-10' }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-secondary">{{ $semi->codice_articolo }}</span>
                                                            <strong class="ms-1">{{ $semi->titolo }}</strong>
                                                            <small class="text-muted ms-1">Qta: {{ $semi->qta }}</small>
                                                        </div>
                                                        @if($semi->stato == 1)
                                                            <span class="badge bg-success"><i class="ri-check-line"></i> Completato</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark"><i class="ri-time-line"></i> Da fare</span>
                                                        @endif
                                                    </div>
                                                    @if($semi->completato_at)
                                                        <small class="text-success d-block mt-1">
                                                            <i class="ri-check-double-line"></i> Completato il {{ date('d/m/Y H:i', strtotime($semi->completato_at)) }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        @endif

                                        <!-- Nota operatore - INGRANDITA -->
                                        <div class="mt-4">
                                            <h6 class="fs-16 mb-2"><i class="ri-edit-line me-1"></i> Nota Operatore</h6>
                                            <form action="{{ url('produzione/salva_nota_fase') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_riga" value="{{ $riga->id }}">
                                                <textarea name="nota" class="form-control" rows="5" style="font-size: 16px;" placeholder="Scrivi una nota...">{{ $riga->note }}</textarea>
                                                <button type="submit" class="btn btn-primary mt-3 rounded-pill w-100" style="font-size: 16px; padding: 10px;">
                                                    <i class="ri-save-line me-1"></i> Salva Nota
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal per terminare la fase -->
                            @if($riga->inizio != null && $riga->fine == null)
                                <div class="modal fade" id="fineFaseModal{{ $riga->id }}" tabindex="-1" aria-labelledby="fineFaseModalLabel{{ $riga->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="fineFaseModalLabel{{ $riga->id }}">
                                                    <i class="ri-stop-fill me-1"></i> Termina Fase: {{ $riga->nome_fase }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('produzione/fine_fase') }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="id" value="{{ $riga->id }}">
                                                    <input type="hidden" name="id_fase" value="{{ $riga->id_fase }}">
                                                    <input type="hidden" name="id_dorig" value="{{ $riga->id_dorig }}">

                                                    <div class="mb-4">
                                                        <label for="quantita{{ $riga->id }}" class="form-label fw-medium">Quantità completata</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="ri-stack-line"></i></span>
                                                            <input type="number" class="form-control" id="quantita{{ $riga->id }}" name="quantita_fatta" value="{{ $odl->qta }}" min="0" step="1" required>
                                                        </div>
                                                    </div>

                                                        <?php
                                                        // Verifica se questa è l'ultima fase dell'ODL
                                                        $is_ultima_fase = DB::select('
                                                        SELECT COUNT(*) as count
                                                        FROM odl_righe
                                                        WHERE id_odl = ?
                                                        AND id_azienda = ?
                                                        AND id > ?',
                                                                [$odl->id, $azienda->id, $riga->id]
                                                            )[0]->count == 0;

                                                    if ($is_ultima_fase) {
                                                        ?>
                                                    <div class="mb-4">
                                                        <label for="lotto{{ $riga->id }}" class="form-label fw-medium">Lotto produzione</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="ri-barcode-line"></i></span>
                                                            <input type="text" class="form-control" id="lotto_produzione{{ $riga->id }}" name="lotto_produzione" value="<?php echo date('ymd') ?>" placeholder="Inserisci il lotto di produzione">
                                                        </div>
                                                    </div>
                                                    <?php } ?>

                                                    <div class="mb-4">
                                                        <label for="note{{ $riga->id }}" class="form-label fw-medium">Note</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="ri-file-text-line"></i></span>
                                                            <textarea class="form-control" id="note{{ $riga->id }}" name="note" rows="3" placeholder="Inserisci eventuali note"></textarea>
                                                        </div>
                                                    </div>

                                                        <?php if(isset($riga->materiali) && count($riga->materiali) > 0): ?>
                                                    <div class="card border shadow-none mb-4">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Materiali utilizzati</h6>
                                                        </div>
                                                        <div class="card-body">
                                                                <?php foreach($riga->materiali as $i => $m): ?>
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-md-5">
                                                                    <label class="form-label fs-13 text-muted mb-0">Materiale</label>
                                                                    <input readonly type="text" name="materiale[<?php echo $i ?>]" value="<?php echo $m->titolo ?>" class="form-control form-control-sm">
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label class="form-label fs-13 text-muted mb-0">Quantità</label>
                                                                    <div class="input-group input-group-sm">
                                                                        <span id="qta_display_<?php echo $riga->id ?>_<?php echo $i ?>" class="form-control form-control-sm"><?php echo number_format($m->qta * $odl->qta,2,'.','') ?></span>
                                                                        <span class="input-group-text"><?php echo $m->um ?></span>
                                                                    </div>
                                                                </div>

                                                                <?php $usa_lotti = !empty($azienda->usa_lotti); ?>
                                                                <?php if($usa_lotti) { ?>
                                                                <div class="col-md-4">
                                                                    <label class="form-label fs-13 text-muted mb-0">Lotto</label>
                                                                    <select name="lotto[<?php echo $i ?>]" class="form-select form-select-sm" onchange="updateScadenza(this, '<?php echo $riga->id ?>_<?php echo $i ?>')">
                                                                        <option value="">Seleziona lotto</option>
                                                                            <?php
                                                                            $lotti_disponibili = DB::select('
                                                                                    SELECT DISTINCT lotto, scadenza_lotto
                                                                                    FROM mgmov
                                                                                    WHERE id_articolo = ?
                                                                                    AND id_azienda = ?
                                                                                    AND lotto IS NOT NULL
                                                                                    AND scadenza_lotto IS NOT NULL
                                                                                    AND (
                                                                                        SELECT SUM(CASE
                                                                                            WHEN car = 1 THEN qta
                                                                                            WHEN sca = 1 THEN -qta
                                                                                            ELSE 0
                                                                                        END)
                                                                                        FROM mgmov m2
                                                                                        WHERE m2.id_articolo = mgmov.id_articolo
                                                                                        AND m2.lotto = mgmov.lotto
                                                                                    ) > 0
                                                                                    ORDER BY scadenza_lotto ASC',
                                                                                [$m->id, $azienda->id]
                                                                            );

                                                                            foreach($lotti_disponibili as $lotto) {
                                                                                $scadenza = date('Y-m-d', strtotime($lotto->scadenza_lotto));
                                                                                echo "<option value='{$lotto->lotto}' data-scadenza='{$scadenza}'>{$lotto->lotto} (Scad: " . date('d/m/Y', strtotime($lotto->scadenza_lotto)) . ")</option>";
                                                                            }
                                                                            ?>
                                                                    </select>
                                                                    <input type="hidden" name="scadenza_lotto[<?php echo $i ?>]" id="scadenza_lotto_<?php echo $riga->id ?>_<?php echo $i ?>">
                                                                </div>
                                                                <?php } else { ?>
                                                                    <input type="hidden" name="lotto[<?php echo $i ?>]" value="">
                                                                    <input type="hidden" name="scadenza_lotto[<?php echo $i ?>]" value="">
                                                                <?php } ?>

                                                                <input type="hidden" name="materiale_id[<?php echo $i ?>]" value="<?php echo $m->id ?>">
                                                                <input type="hidden" name="quantita_originale[<?php echo $i ?>]" value="<?php echo $m->qta ?>">
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>

                                                    @if(isset($odl_semilavorati))
                                                        @php
                                                            $semi_fase_modal = array_filter($odl_semilavorati, function($s) use ($riga) {
                                                                return $s->id_fase == $riga->id_fase && $s->stato == 0;
                                                            });
                                                        @endphp
                                                        @if(count($semi_fase_modal) > 0)
                                                        <div class="card border border-warning shadow-none mb-4">
                                                            <div class="card-header bg-warning bg-opacity-25">
                                                                <h6 class="mb-0"><i class="ri-settings-3-line me-1"></i> Semilavorati prodotti in questa fase</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="text-muted fs-13 mb-3">I seguenti semilavorati verranno segnati come completati alla chiusura della fase:</p>
                                                                @foreach($semi_fase_modal as $semi)
                                                                <div class="d-flex align-items-center mb-2 p-2 border rounded bg-light">
                                                                    <input type="hidden" name="semilavorati_completati[]" value="{{ $semi->id }}">
                                                                    <i class="ri-checkbox-circle-fill text-success me-2 fs-18"></i>
                                                                    <span class="badge bg-secondary me-2">{{ $semi->codice_articolo }}</span>
                                                                    <strong>{{ $semi->titolo }}</strong>
                                                                    <small class="text-muted ms-auto">Qta: {{ $semi->qta }}</small>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                        <i class="ri-close-line me-1 align-middle"></i> Annulla
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="ri-check-line me-1 align-middle"></i> Completa Fase
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('utente.common.footer')

<style>
    /* Card styles */
    .card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Avatar styles */
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .avatar-md {
        width: 48px;
        height: 48px;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
    }

    /* Font sizes */
    .fs-20 {
        font-size: 20px;
    }

    .fs-16 {
        font-size: 16px;
    }

    .fs-14 {
        font-size: 14px;
    }

    .fs-13 {
        font-size: 13px;
    }

    .fs-12 {
        font-size: 12px;
    }

    /* Border colors */
    .border-danger {
        border-left: 4px solid #dc3545 !important;
    }

    .border-warning {
        border-left: 4px solid #ffc107 !important;
    }

    .border-success {
        border-left: 4px solid #28a745 !important;
    }

    /* Background gradient */
    .bg-gradient {
        background-image: linear-gradient(to right, rgba(255,255,255,0.1), rgba(255,255,255,0)) !important;
    }

    /* Ribbon styles */
    .ribbon-box {
        position: relative;
        overflow: hidden;
    }

    .ribbon {
        position: absolute;
        right: -5px;
        top: -5px;
        z-index: 1;
        overflow: hidden;
        width: 75px;
        height: 75px;
        text-align: right;
    }

    .ribbon-shape {
        font-size: 12px;
        color: #fff;
        text-transform: uppercase;
        text-align: center;
        line-height: 20px;
        transform: rotate(45deg);
        width: 120px;
        display: block;
        background: #dc3545;
        box-shadow: 0 5px 10px rgba(0,0,0,.1);
        position: absolute;
        top: 19px;
        right: -29px;
    }

    .ribbon-shape:before,
    .ribbon-shape:after {
        position: absolute;
        content: "";
        display: block;
        border: 3px solid;
        border-color: inherit;
    }

    .ribbon-shape:before {
        top: 0;
        left: 0;
    }

    .ribbon-shape:after {
        bottom: 0;
        right: 0;
    }

    .ribbon-danger {
        background-color: #dc3545 !important;
    }

    .ribbon-warning {
        background-color: #ffc107 !important;
    }

    .ribbon-success {
        background-color: #28a745 !important;
    }

    /* Custom button styles */
    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }

    .btn-danger, .btn-warning, .btn-success {
        color: white !important;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    /* Form controls */
    .form-control, .form-select {
        padding: 0.4375rem 0.875rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
    }

    /* Modal customization */
    .modal-content {
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Per ogni modale di fine fase
        <?php foreach($odl_righe as $riga): ?>
        const quantitaInput<?php echo $riga->id ?> = document.getElementById('quantita<?php echo $riga->id ?>');
        if (quantitaInput<?php echo $riga->id ?>) {
            quantitaInput<?php echo $riga->id ?>.addEventListener('input', function() {
                const quantitaOriginale = <?php echo $riga->qta ?>;
                const quantitaNuova = parseFloat(this.value) || 0;
                const fattore = quantitaNuova / quantitaOriginale;

                // Aggiorna tutte le quantità visualizzate dei materiali
                    <?php if(isset($riga->materiali)): ?>
                    <?php foreach($riga->materiali as $i => $m): ?>
                const qtaDisplay_<?php echo $riga->id ?>_<?php echo $i ?> = document.getElementById('qta_display_<?php echo $riga->id ?>_<?php echo $i ?>');
                if (qtaDisplay_<?php echo $riga->id ?>_<?php echo $i ?>) {
                    qtaDisplay_<?php echo $riga->id ?>_<?php echo $i ?>.textContent = (parseFloat(this.value) * <?php echo $m->qta ?>).toFixed(2);
                }
                <?php endforeach; ?>
                <?php endif; ?>
            });
        }
        <?php endforeach; ?>
    });

    function updateScadenza(selectElement, key) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const scadenza = selectedOption.getAttribute('data-scadenza');
        document.getElementById('scadenza_lotto_' + key).value = scadenza || '';
    }
</script>
