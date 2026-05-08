@include('utente.common.header')

<?php
    $mesi = [1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'];
?>

<div class="page-content">
    <div class="container-fluid">
    <!-- Intestazione pagina -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-primary mb-0">
                    <i class="ri-file-list-3-line me-2"></i>
                    @if($utente->id_tipologia == 1)
                        Riepilogo Documenti {{ $cd_do }} di: {{ $utente->nome }} {{ $utente->cognome }}
                    @else
                        Riepilogo Documenti {{ $cd_do }}
                    @endif
                </h2>
                <div class="d-flex gap-2">
                    <a href="/utente/crea_documento/{{ $cd_do }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ri-add-line me-1"></i>Crea Documento
                    </a>
                    <button class="btn btn-success d-flex align-items-center" onclick="importa();">
                        <i class="ri-upload-2-line me-1"></i>Importa
                    </button>
                    <button class="btn btn-warning d-flex align-items-center">
                        <i class="ri-download-2-line me-1"></i>Esporta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout principale con sidebar e contenuto -->
    <div class="row">
        <!-- Sidebar con filtri (visibile solo su desktop) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0"><i class="ri-filter-3-line me-2"></i>Filtri</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo URL::asset('utente/riepilogo_documenti/'.$cd_do) ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo (!isset($mese)) ? 'active' : ''; ?>">
                            <span><i class="ri-calendar-line me-2"></i>Tutto l'Anno</span>
                            <span class="badge bg-primary rounded-pill"><?php echo array_sum(array_column($riepilogo_anno, 'numero_documenti')); ?></span>
                        </a>
                        
                        <?php foreach($riepilogo_anno as $ra){ ?>
                        <a href="<?php echo URL::asset('utente/riepilogo_documenti/'.$cd_do.'/'.$ra->mese_numero) ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo ($mese == $ra->mese_numero) ? 'active' : ''; ?>">
                            <span><i class="ri-calendar-2-line me-2"></i>{{ $ra->nome_mese }}</span>
                            <div class="d-flex align-items-center">
                                <span class="text-primary me-2">€ <?php echo number_format($ra->imponibile,2,',','.') ?></span>
                                <span class="badge bg-primary rounded-pill"><?php echo $ra->numero_documenti ?></span>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenuto principale -->
        <div class="col-lg-9 col-12">

            <!-- Tabella con i Documenti -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-list-3-line me-2"></i>Documenti {{ $cd_do }}
                        <span class="badge bg-primary ms-2"><?php echo count($dotes); ?> documenti</span>
                    </h5>
                </div>

                <div class="card-body">
                    <?php $totale = 0; ?>

                    <div class="table-responsive">
                        <table id="scroll-horizontal" class="table table-striped table-hover " style="width:100%">
                            <thead class="table-light">
                            <tr>
                                <th><i class="ri-hashtag me-1"></i>Numero</th>
                                <th><i class="ri-calendar-line me-1"></i>Data</th>
                                <th><i class="ri-user-line me-1"></i>Cliente</th>
                                <th><i class="ri-money-euro-circle-line me-1"></i>Importo</th>
                                <th><i class="ri-checkbox-circle-line me-1"></i>Stato Evasione</th>
                                <th><i class="ri-bar-chart-line me-1"></i>Evasione</th>
                                <th><i class="ri-settings-line me-1"></i>Azioni</th>
                            </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <?php foreach($dotes as $d){ $totale += $d->imponibile - $d->importo_cassa; ?>
                                    <tr>
                                        <td class="fw-medium"><?php echo $d->numero_doc ?></td>
                                        <td><?php echo date('d/m/Y',strtotime($d->data_doc)) ?></td>
                                        <td><?php echo $d->ragione_sociale_fatturazione ?></td>
                                        <td class="fw-medium">&euro; <?php echo number_format($d->imponibile - $d->importo_cassa,2,',','.') ?> <small class="text-muted">+IVA</small></td>

                                        <td>
                                            <?php if($d->num_righe_evase == 0){ ?>
                                                <span class="badge bg-danger"><i class="ri-close-circle-line me-1"></i>Non Evaso</span>
                                            <?php } ?>
                                            <?php if($d->num_righe_evase > 0 && $d->num_righe_evase < $d->num_righe_totale){ ?>
                                                <span class="badge bg-warning"><i class="ri-error-warning-line me-1"></i>Evaso Parzialmente</span>
                                            <?php } ?>
                                            <?php if($d->num_righe_evase == $d->num_righe_totale){ ?>
                                                <span class="badge bg-success"><i class="ri-check-line me-1"></i>Evaso</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px; width: 80px;">
                                                <div class="progress-bar <?php echo ($d->num_righe_evase == 0) ? 'bg-danger' : (($d->num_righe_evase == $d->num_righe_totale) ? 'bg-success' : 'bg-warning'); ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo ($d->num_righe_totale > 0) ? ($d->num_righe_evase / $d->num_righe_totale * 100) : 0; ?>%" 
                                                     aria-valuenow="<?php echo $d->num_righe_evase ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?php echo $d->num_righe_totale ?>">
                                                </div>
                                            </div>
                                            <small class="mt-1 d-block"><?php echo $d->num_righe_evase ?>/<?php echo $d->num_righe_totale ?></small>
                                        </td>

                                        <td>

                                            <div class="d-flex gap-2">
                                                @if($d->cd_do == 'PRE')
                                                    <a href="{{ url('utente/preventivi/gantt/' . $d->id) }}" class="btn btn-info">
                                                        <i class="ri-bar-chart-horizontal-line"></i> Visualizza Gantt
                                                    </a>
                                                @endif
                                                <a class="btn btn-sm btn-info text-white" href="<?php echo URL::asset('utente/modifica_documento/'.$d->id) ?>">
                                                    <i class="ri-edit-line me-1"></i>Gestisci
                                                </a>

                                                <?php if($d->stato == 0 && ($d->num_righe_evase == 0 || $d->num_righe_evase > 0 && $d->num_righe_evase < $d->num_righe_totale)){ ?>
                                                    <form method="post" onsubmit="return confirm('Vuoi Eliminare questo documento?')">
                                                        <input type="hidden" name="id" value="<?php echo $d->id ?>">
                                                        <button name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger">
                                                            <i class="ri-delete-bin-2-line"></i>
                                                        </button>
                                                    </form>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end fw-bold">Totale:</td>
                                    <td colspan="4" class="fw-bold text-primary">&euro; <?php echo number_format($totale,2,',','.') ?> <small class="text-muted">+IVA</small></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importa -->
<div class="modal fade" id="modal_importa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white p-3">
                <h5 class="modal-title" id="exampleModalLabel"><i class="ri-upload-2-line me-2"></i>Importa <?php echo $cd_do ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="file-upload" class="form-label">Seleziona file da importare</label>
                        <div class="input-group">
                            <input class="form-control" type="file" id="file-upload" name="allegato" required>
                            <label class="input-group-text" for="file-upload"><i class="ri-file-upload-line"></i></label>
                        </div>
                        <div class="form-text">Seleziona il file contenente i documenti da importare</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Chiudi</button>
                    <button type="submit" class="btn btn-success" id="add-btn" name="importa"><i class="ri-upload-2-line me-1"></i>Importa</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script type="text/javascript">
    function importa(){
        $('#modal_importa').modal('show');
    }
    
    // Inizializzazione DataTable con opzioni migliorate
    $(document).ready(function() {
        $('#scroll-horizontal').DataTable({
            scrollX: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            },
            order: [[1, 'desc']], // Ordina per data decrescente
            responsive: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tutti"]]
        });
        
        // Inizializzazione tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<style>
    /* Stili generali */
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    /* Stili per i filtri a pillola */
    .month-pills-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    
    .month-pills-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    
    .month-pills {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.5rem;
        padding: 0.25rem 0;
    }
    
    .month-pill {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #495057;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    .month-pill:hover {
        background-color: rgba(52, 152, 219, 0.1);
        border-color: rgba(52, 152, 219, 0.5);
        color: #3498db;
        transform: translateY(-2px);
    }
    
    .month-pill.active {
        background-color: #3498db;
        border-color: #3498db;
        color: white;
    }
    
    /* Stili per la sidebar */
    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .list-group-item.active {
        background-color: rgba(52, 152, 219, 0.1);
        color: #3498db;
        border-color: rgba(0,0,0,0.05);
        font-weight: 600;
    }
    
    .list-group-item:hover:not(.active) {
        background-color: rgba(0,0,0,0.02);
    }
    
    /* Stili per la tabella */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }
    
    /* Stili per i badge */
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.65em;
        font-weight: 500;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
    }
    
    /* Stili per i bottoni */
    .btn {
        border-radius: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Progress bar */
    .progress {
        height: 8px;
        border-radius: 30px;
        margin-bottom: 0.25rem;
        background-color: #f1f1f1;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .month-pill {
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>