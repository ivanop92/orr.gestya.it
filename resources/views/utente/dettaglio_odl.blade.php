

@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gestione ODL <?php echo $odl->numero ?> del <?php echo date('d/m/Y',strtotime($odl->data)) ?> - <b><?php echo $odl->codice_articolo ?> - <?php echo $odl->articolo ?></b><?php if(!empty($odl->cliente_ragione_sociale)){ ?><br><small class="text-muted">Cliente: <b><?php echo $odl->cliente_ragione_sociale ?></b></small><?php } ?></h3>
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <a style="float:right;" class="btn btn-success" href="<?php echo URL::asset('utente/odl') ?>">Torna Indietro</a>
                            <a style="float:right;margin-right:10px;" class="btn btn-primary" href="/stampa/dettaglio_odl/<?php echo $odl->id ?>" target="_blank">
                                <i class="ri-printer-line me-1"></i> Stampa
                            </a>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        @if($odl->stato === 2)
                            <a target="_blank" href="/stampa/tracciabilita/<?php echo $odl->id ?>" class="btn btn-primary" style="width:10%">TRACCIABILITA'</a>
                        @endif
                        <div class="clearfix" style="margin-bottom:10px"></div>

                        <?php $i = 1; ?>
                        <?php foreach($odl_righe as $or){ ?>
                            <?php
                            $border_color = 'danger';
                            $bg_header = 'bg-danger';
                            $stato_label = '<span class="badge bg-danger">In attesa</span>';
                            if($or->inizio != '' && $or->fine == '' && $or->completato == 0) {
                                $border_color = 'primary';
                                $bg_header = 'bg-primary';
                                $stato_label = '<span class="badge bg-primary">In corso</span>';
                            }
                            if($or->fine != '' && $or->completato == 1) {
                                $border_color = 'success';
                                $bg_header = 'bg-success';
                                $stato_label = '<span class="badge bg-success">Completato</span>';
                            }

                            $durata_str = '-';
                            if($or->inizio != '' && $or->fine != ''){
                                $diff = strtotime($or->fine) - strtotime($or->inizio);
                                $ore = floor($diff / 3600);
                                $minuti = floor(($diff % 3600) / 60);
                                $durata_str = $ore.'h '.$minuti.'m';
                            }
                            ?>

                        <div class="card border border-<?php echo $border_color ?> mb-4 shadow-sm">
                            <!-- Header fase -->
                            <div class="card-header <?php echo $bg_header ?> bg-gradient d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 text-white">
                                    <span class="badge bg-white text-<?php echo $border_color ?> me-2"><?php echo $i ?></span>
                                    <?php echo $or->nome_fase ?>
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    <?php echo $stato_label ?>
                                    <span class="badge bg-white text-dark">Qta: <?php echo $or->qta_fatta.'/'.$or->qta ?></span>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Info tempi -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Inizio</small>
                                        <strong><?php echo $or->inizio ? date('d/m/Y H:i', strtotime($or->inizio)) : '-' ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Fine</small>
                                        <strong><?php echo $or->fine ? date('d/m/Y H:i', strtotime($or->fine)) : '-' ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Durata</small>
                                        <strong><?php echo $durata_str ?></strong>
                                        <?php if($or->inizio != ''){ ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="$('#modal_orari_<?php echo $or->id ?>').modal('show')">
                                                <i class="ri-time-line"></i>
                                            </button>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php if(isset($or->id_operatore_assegnato) && $or->id_operatore_assegnato > 0){
                                            $op_assegnato = null;
                                            foreach($operatori as $o){ if($o->id == $or->id_operatore_assegnato) $op_assegnato = $o->nome; }
                                            if($op_assegnato){ ?>
                                                <small class="text-muted d-block">Operatore</small>
                                                <strong><?php echo $op_assegnato ?></strong>
                                        <?php } } ?>
                                        <?php if(!empty($or->id_fornitore)){
                                            $fornitore_assegnato = null;
                                            foreach($fornitori_odl as $fo){ if($fo->id == $or->id_fornitore) $fornitore_assegnato = $fo->ragione_sociale; }
                                            if($fornitore_assegnato){ ?>
                                                <small class="text-muted d-block mt-1">Lavorazione Esterna</small>
                                                <strong><span class="badge bg-warning text-dark"><i class="ri-truck-line me-1"></i><?php echo $fornitore_assegnato ?></span></strong>
                                        <?php } } ?>
                                        <?php if($or->note != ''){ ?>
                                            <small class="text-muted d-block mt-1">Note</small>
                                            <small><?php echo nl2br($or->note) ?></small>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Materiali da scaricare per questa fase -->
                                @if(isset($or->materiali) && count($or->materiali) > 0)
                                <div class="mb-3">
                                    <h6 class="mb-2"><i class="ri-inbox-unarchive-line me-1 text-info"></i> Materiali da scaricare</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Materiale</th>
                                                    <th style="width:80px">Tipo</th>
                                                    <th style="width:120px">Qta per unità</th>
                                                    <th style="width:120px">Qta totale</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($or->materiali as $m)
                                                <tr>
                                                    <td>
                                                        {{ $m->titolo }}
                                                    </td>
                                                    <td>
                                                        @if($m->tipologia == 3)
                                                            <span class="badge bg-warning text-dark">SEMI</span>
                                                        @elseif($m->tipologia == 1)
                                                            <span class="badge bg-info">MP</span>
                                                        @else
                                                            <span class="badge bg-secondary">COMM</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($m->qta, 4, ',', '.') }}</td>
                                                    <td><strong>{{ number_format($m->qta * $odl->qta, 4, ',', '.') }}</strong></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                <!-- Semilavorati da produrre in questa fase -->
                                @if(isset($odl_semilavorati))
                                    @php
                                        $semi_fase = array_filter($odl_semilavorati, function($s) use ($or) {
                                            return $s->id_fase == $or->id_fase;
                                        });
                                    @endphp
                                    @if(count($semi_fase) > 0)
                                    <div class="mb-3">
                                        <h6 class="mb-2"><i class="ri-settings-3-line me-1 text-warning"></i> Semilavorati da produrre</h6>
                                        <div class="row g-2">
                                            @foreach($semi_fase as $semi)
                                            <div class="col-md-4">
                                                <div class="border rounded p-2 h-100 {{ $semi->stato == 1 ? 'border-success bg-success bg-opacity-10' : 'border-warning bg-warning bg-opacity-10' }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <span class="badge bg-secondary">{{ $semi->codice_articolo }}</span>
                                                            <strong class="d-block mt-1">{{ $semi->titolo }}</strong>
                                                            <small class="text-muted">Qta: {{ $semi->qta }}</small>
                                                        </div>
                                                        @if($semi->stato == 1)
                                                            <span class="badge bg-success"><i class="ri-check-line"></i> Completato</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark"><i class="ri-time-line"></i> Da fare</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($semi->fornitore_nome))
                                                        <div class="mt-1">
                                                            <span class="badge bg-warning text-dark"><i class="ri-truck-line me-1"></i>Lav. esterna: {{ $semi->fornitore_nome }}</span>
                                                        </div>
                                                    @endif
                                                    @if(isset($semi->materiali) && count($semi->materiali) > 0)
                                                        <div class="mt-2">
                                                            <small class="text-muted fw-bold d-block"><i class="ri-list-check me-1"></i>Materiali necessari:</small>
                                                            <ul class="list-unstyled mb-0 mt-1" style="font-size: 11px;">
                                                                @foreach($semi->materiali as $mat)
                                                                    <li>
                                                                        @if($mat->tipologia == 1)
                                                                            <span class="badge bg-info" style="font-size:9px;">MP</span>
                                                                        @elseif($mat->tipologia == 2)
                                                                            <span class="badge bg-secondary" style="font-size:9px;">COMM</span>
                                                                        @elseif($mat->tipologia == 3)
                                                                            <span class="badge bg-warning text-dark" style="font-size:9px;">SEMI</span>
                                                                        @endif
                                                                        {{ $mat->codice_articolo }} - {{ $mat->titolo }}
                                                                        <span class="text-muted">({{ number_format($mat->qta * $semi->qta, 4, ',', '.') }} {{ $mat->um }})</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if($semi->completato_at)
                                                        <small class="text-success d-block mt-1">
                                                            <i class="ri-check-double-line"></i> {{ date('d/m/Y H:i', strtotime($semi->completato_at)) }}
                                                            @if($semi->operatore_nome)
                                                                — {{ $semi->operatore_nome }} {{ $semi->operatore_cognome }}
                                                            @endif
                                                        </small>
                                                    @endif
                                                    <form method="post" class="mt-2">
                                                        @csrf
                                                        <input type="hidden" name="id_semilavorato" value="{{ $semi->id }}">
                                                        <button type="submit" name="toggle_semilavorato" value="1"
                                                            class="btn btn-sm w-100 {{ $semi->stato == 1 ? 'btn-outline-warning' : 'btn-success' }}">
                                                            @if($semi->stato == 1)
                                                                <i class="ri-refresh-line me-1"></i> Riapri
                                                            @else
                                                                <i class="ri-check-line me-1"></i> Segna completato
                                                            @endif
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endif

                                <!-- Allegati -->
                                <div class="mb-3">
                                    <h6 class="mb-2"><i class="ri-attachment-2 me-1 text-muted"></i> Allegati</h6>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <?php if(isset($or->allegati) && count($or->allegati) > 0){ ?>
                                                <table class="table table-sm table-striped mb-0">
                                                    <?php foreach($or->allegati as $allegato){ ?>
                                                    <tr>
                                                        <td>
                                                            <a href="/<?php echo $allegato->path_file ?>" target="_blank">
                                                                <i class="fa fa-file"></i> <?php echo $allegato->nome_originale ?>
                                                            </a>
                                                        </td>
                                                        <td style="width:200px"><?php echo $allegato->descrizione ?></td>
                                                        <td style="width:150px"><?php echo date('d/m/Y H:i', strtotime($allegato->created_at)) ?></td>
                                                        <td style="width:40px">
                                                            <a href="/utente/odl_elimina_allegato/<?php echo $allegato->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare questo allegato?')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </table>
                                            <?php } else { ?>
                                                <p class="text-muted mb-0">Nessun allegato</p>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-5">
                                            <form method="post" action="/utente/odl_carica_allegato/<?php echo $or->id ?>" enctype="multipart/form-data">
                                                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="file" name="allegato" class="form-control form-control-sm" required>
                                                    <input type="text" name="descrizione" class="form-control form-control-sm" placeholder="Descrizione">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Azioni -->
                                <div class="border-top pt-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <?php if(sizeof($operatori) > 0 && $or->completato == 0){ ?>
                                            <small class="text-muted d-block mb-1">Operatore (interno)</small>
                                            <form method="post" class="d-flex align-items-center" style="gap:5px;">
                                                <select class="form-control form-control-sm select2" name="id_operatore_assegnato" style="flex:1;">
                                                    <option value="">Scegli Operatore</option>
                                                    <?php foreach($operatori as $o){ ?>
                                                    <option value="<?php echo $o->id ?>" <?php echo (isset($or->id_operatore_assegnato) && $o->id == $or->id_operatore_assegnato)?'selected':'' ?>><?php echo $o->nome ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="id_riga" value="<?php echo $or->id ?>">
                                                <button type="submit" name="assegna_operatore" value="1" class="btn btn-sm btn-info">Assegna</button>
                                            </form>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?php if($or->completato == 0){ ?>
                                            <small class="text-muted d-block mb-1">Fornitore (lav. esterna)</small>
                                            <form method="post" class="d-flex align-items-center" style="gap:5px;">
                                                <select class="form-control form-control-sm" name="id_fornitore" style="flex:1;">
                                                    <option value="">Nessun fornitore</option>
                                                    <?php foreach($fornitori_odl as $fo){ ?>
                                                    <option value="<?php echo $fo->id ?>" <?php echo (!empty($or->id_fornitore) && $fo->id == $or->id_fornitore)?'selected':'' ?>><?php echo $fo->ragione_sociale ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="id_riga" value="<?php echo $or->id ?>">
                                                <button type="submit" name="assegna_fornitore" value="1" class="btn btn-sm btn-warning">Assegna</button>
                                            </form>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <?php if($or->inizio == '' && $or->completato == 0){ ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Sei sicuro di voler iniziare questa Fase ?')">
                                                    <input type="hidden" name="id" value="<?php echo $or->id ?>">
                                                    <button type="submit" name="start_fase" class="btn btn-success btn-sm" value="start_fase">
                                                        <i class="ri-play-line me-1"></i> START
                                                    </button>
                                                </form>
                                            <?php } else if($or->fine == '' && $or->completato == 0){ ?>
                                                <form method="post" class="d-inline mb-1" onsubmit="return confirm('Sei sicuro di voler riaprire questa Fase ?')">
                                                    <input type="hidden" name="id" value="<?php echo $or->id ?>">
                                                    <button type="submit" name="start_fase" class="btn btn-success btn-sm" value="start_fase">
                                                        <i class="ri-refresh-line me-1"></i> RIAPRI
                                                    </button>
                                                </form>
                                                <button onclick="modal_chiudi_odl(<?php echo $or->id ?>)" class="btn btn-danger btn-sm">
                                                    <i class="ri-stop-line me-1"></i> STOP
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $i++;} ?>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


            </div>
            <!-- /.col -->
        </div>

    </div>
    <!-- container-fluid -->
</div>


<div id="ajax_loader"></div>


@include('utente.common.footer')


<!-- MODALE CHIUDI FASE -->
<?php foreach($odl_righe as $or){ ?>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_chiudi_fase_<?php echo $or->id ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Chiudi Fase - <?php echo $or->nome_fase; ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Quantita Prodotta<b style="color:red">*</b></label>
                            <input type="text" id="id_qta_reale_<?php echo $or->id ?>" value="<?php echo $odl->qta ?>" class="form-control" name="quantita" placeholder="Qta Rilevata" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                            <?php if(sizeof($operatori) > 0){ ?>
                        <label>Operatore<b style="color:red">*</b></label>
                        <select class="form-control select2" name="id_operatore" required style="width:100%;">
                            <option value="">Scegli Operatore</option>
                                <?php foreach($operatori as $o){ ?>
                            <option value="<?php echo $o->id ?>" <?php echo (isset($or->id_operatore_assegnato) && $o->id == $or->id_operatore_assegnato)?'selected':'' ?>><?php echo $o->nome ?></option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                        <input type="hidden" name="id_operatore" value="0">
                        <?php } ?>
                    </div>

                    <!-- Materiali specifici per la fase -->
                        <?php foreach($or->materiali as $i => $m){ ?>
                    <div class="row mt-2">
                        <div class="col-md-5">
                                <?php if($i == 0) { ?><label>Materiale <b style="color:red">*</b></label><?php } ?>
                            <div class="input-group">
                                <input readonly type="text" name="materiale[<?php echo $i ?>]" value="<?php echo $m->titolo ?>" class="form-control">
                                <?php if($m->tipologia == 3) { ?>
                                    <span class="input-group-text bg-warning text-dark fw-bold" title="Semilavorato">⚙ SEMI</span>
                                <?php } ?>
                                <input type="hidden" name="id_materiale[<?php echo $i ?>]" value="<?php echo $m->id ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                                <?php if($i == 0) { ?><label>Qta da scaricare<b style="color:red">*</b></label><?php } ?>
                            <input type="number" min="0" step="0.0001" name="qta_materiale[<?php echo $i ?>]" value="<?php echo number_format($m->qta * $or->qta, 4, '.', '') ?>" data-default="<?php echo number_format($m->qta * $or->qta, 4, '.', '') ?>" class="form-control" title="Qta calcolata: <?php echo $m->qta ?> x <?php echo $or->qta ?> = <?php echo $m->qta * $or->qta ?>. Modifica solo se necessario.">
                        </div>

                        <?php $usa_lotti = isset($azienda) && !empty($azienda->usa_lotti); ?>
                        <?php if($usa_lotti) { ?>
                        <div class="col-md-4">
                                <?php if($i == 0) { ?><label>Lotto<b style="color:red">*</b></label><?php } ?>
                            <select name="lotto[<?php echo $i ?>]" class="form-control select2" required onchange="aggiornaScadenza(<?php echo $i ?>)">
                                <option value="">Seleziona lotto</option>
                                    <?php foreach($m->lotti_disponibili as $lotto){
                                    $data_scadenza = !empty($lotto->scadenza_lotto) ? date('Y-m-d', strtotime($lotto->scadenza_lotto)) : '';
                                    $display_scadenza = !empty($lotto->scadenza_lotto) ? ' (Scad: '.date('d/m/Y', strtotime($lotto->scadenza_lotto)).')' : '';
                                    ?>
                                <option value="<?php echo $lotto->lotto ?>" data-scadenza="<?php echo $data_scadenza ?>"><?php echo $lotto->lotto . $display_scadenza ?> - Disp: <?php echo $lotto->giacenza ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="scadenza_lotto[<?php echo $i ?>]" id="scadenza_<?php echo $i ?>" value="">
                        </div>
                        <?php } else { ?>
                            <input type="hidden" name="lotto[<?php echo $i ?>]" value="">
                            <input type="hidden" name="scadenza_lotto[<?php echo $i ?>]" value="">
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                    <input type="hidden" name="id" value="<?php echo $or->id ?>">
                    <input type="hidden" name="id_fase" value="<?php echo $or->id_fase ?>">
                    <input type="hidden" name="id_dorig" value="<?php echo $or->id_dorig ?>">

                    <input type="submit" class="btn btn-primary pull-right" name="fine_fase" value="Chiudi Fase" style="margin-right:5px;">
                </div>
            </div>
        </div>
    </div>
</form>

<?php } ?>


<!-- MODALE MODIFICA ORARI -->
<?php foreach($odl_righe as $or){ ?>
<?php if($or->inizio != ''){ ?>
<div class="modal fade" id="modal_orari_<?php echo $or->id ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modifica Orari - <?php echo $or->nome_fase; ?></h4>
                <button type="button" class="btn-close" data-dismiss="modal" onclick="$('#modal_orari_<?php echo $or->id ?>').modal('hide')"></button>
            </div>
            <form method="post">
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><b>Inizio</b></label>
                            <input type="datetime-local" name="inizio" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($or->inizio)) ?>" id="orario_inizio_<?php echo $or->id ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><b>Fine</b></label>
                            <input type="datetime-local" name="fine" class="form-control" value="<?php echo $or->fine ? date('Y-m-d\TH:i', strtotime($or->fine)) : '' ?>" id="orario_fine_<?php echo $or->id ?>">
                        </div>
                    </div>

                    <hr>
                    <p class="text-muted mb-2">Oppure inserisci la durata per calcolare automaticamente la fine:</p>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><b>Ore</b></label>
                            <input type="number" min="0" class="form-control" id="durata_ore_<?php echo $or->id ?>" placeholder="0" oninput="calcolaFine(<?php echo $or->id ?>)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><b>Minuti</b></label>
                            <input type="number" min="0" max="59" class="form-control" id="durata_min_<?php echo $or->id ?>" placeholder="0" oninput="calcolaFine(<?php echo $or->id ?>)">
                        </div>
                    </div>

                    <input type="hidden" name="id_riga" value="<?php echo $or->id ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modal_orari_<?php echo $or->id ?>').modal('hide')">Annulla</button>
                    <button type="submit" name="modifica_orari" value="1" class="btn btn-warning"><i class="ri-save-line"></i> Salva Orari</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
<?php } ?>


<script type="text/javascript">
    function aggiornaScadenza(index) {
        var select = document.getElementsByName('lotto[' + index + ']')[0];
        var option = select.options[select.selectedIndex];
        var scadenza = option.getAttribute('data-scadenza');
        document.getElementById('scadenza_' + index).value = scadenza || '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($or) && isset($or->materiali)) { foreach($or->materiali as $i => $m){ ?>
        aggiornaScadenza(<?php echo $i ?>);
        <?php } } ?>
    });

    function modal_chiudi_odl(id_riga){
        $('#modal_chiudi_fase_'+id_riga).modal('show');
    }

    function calcolaFine(idRiga) {
        var ore = parseInt(document.getElementById('durata_ore_' + idRiga).value) || 0;
        var minuti = parseInt(document.getElementById('durata_min_' + idRiga).value) || 0;
        var inizioInput = document.getElementById('orario_inizio_' + idRiga);
        var fineInput = document.getElementById('orario_fine_' + idRiga);

        if (inizioInput.value && (ore > 0 || minuti > 0)) {
            var inizio = new Date(inizioInput.value);
            inizio.setHours(inizio.getHours() + ore);
            inizio.setMinutes(inizio.getMinutes() + minuti);

            var anno = inizio.getFullYear();
            var mese = String(inizio.getMonth() + 1).padStart(2, '0');
            var giorno = String(inizio.getDate()).padStart(2, '0');
            var hh = String(inizio.getHours()).padStart(2, '0');
            var mm = String(inizio.getMinutes()).padStart(2, '0');
            fineInput.value = anno + '-' + mese + '-' + giorno + 'T' + hh + ':' + mm;
        }
    }

    window.onload = (event) => {
        $('body').addClass('sidebar-collapse');
    };

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function imposta_commessa(){
        $('#modal_commessa').modal('show');
    }

    function esporta_odl(){
        $('#modal_esporta_odl').modal('show');
    }

    function modifica(id){
        $.get("<?php echo URL::ASSET('ajax/modifica_odl') ?>/"+id, function( data ) {
            $("#ajax_loader" ).html( data );
            $('#modal_modifica_'+id).modal('show');

            $('.datetime-picker').attr('autocomplete','off');
            $('.datetime-picker').datetimepicker({
                format: "dd/mm/yyyy H:i:s",
                language: "it",
                autoclose:true
            });

            $('.select2').select2();

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        });
    }
</script>
