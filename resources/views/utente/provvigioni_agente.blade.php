@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Provvigioni Agente</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('utente/agenti') }}">Agenti</a></li>
                            <li class="breadcrumb-item active">Provvigioni</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Provvigioni di {{ $agente->nome }} {{ $agente->cognome }}</h5>
                            </div>

                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Provvigione
                                    </button>
                                    <a href="{{ url('utente/agenti') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line me-1 align-bottom"></i>Torna agli Agenti
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Valore</th>
                                <th>Descrizione</th>
                                <th>Data Creazione</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($provvigioni as $p){ ?>
                            <tr>
                                <td><?php echo $p->id ?></td>
                                <td>
                                        <?php if($p->tipo_provvigione == 'percentuale'): ?>
                                    <span class="badge bg-success">Percentuale</span>
                                    <?php else: ?>
                                    <span class="badge bg-primary">Importo Fisso</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                        <?php if($p->tipo_provvigione == 'percentuale'): ?>
                                        <?php echo number_format($p->valore, 2) ?>%
                                    <?php else: ?>
                                    &euro; <?php echo number_format($p->valore, 2) ?>
                                           <?php endif; ?>
                                </td>
                                <td><?php echo $p->descrizione ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p->data_creazione)) ?></td>
                                <td>
                                    <div style="display: flex">
                                        <a style="margin-left:5px;" onclick="modifica(<?php echo $p->id ?>)" class="btn btn-sm btn-primary">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questa provvigione?')">
                                            <input type="hidden" name="id" value="<?php echo $p->id ?>">
                                            <button style="margin-left:5px;" name="elimina_provvigione" value="Elimina" type="submit" class="btn btn-sm btn-danger">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Provvigione -->
<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Provvigione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tipo Provvigione<b style="color:red">*</b></label>
                            <select name="tipo_provvigione" id="tipo_provvigione" class="form-control" required onchange="changeTipoProvvigione()">
                                <option value="percentuale">Percentuale (%)</option>
                                <option value="fisso">Importo Fisso (€)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Valore<b style="color:red">*</b></label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="valore" name="valore" class="form-control" placeholder="Valore" required/>
                                <span class="input-group-text" id="simbolo">%</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" placeholder="Descrizione (opzionale)" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" name="aggiungi_provvigione" value="Aggiungi" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Provvigione -->
<?php foreach($provvigioni as $p){ ?>
<div class="modal fade" id="modal_modifica_<?php echo $p->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Provvigione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tipo Provvigione<b style="color:red">*</b></label>
                            <select name="tipo_provvigione" id="tipo_provvigione_<?php echo $p->id ?>" class="form-control" required onchange="changeTipoProvvigioneEdit(<?php echo $p->id ?>)">
                                <option value="percentuale" <?php echo ($p->tipo_provvigione == 'percentuale') ? 'selected' : '' ?>>Percentuale (%)</option>
                                <option value="fisso" <?php echo ($p->tipo_provvigione == 'fisso') ? 'selected' : '' ?>>Importo Fisso (€)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Valore<b style="color:red">*</b></label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="valore_<?php echo $p->id ?>" name="valore" value="<?php echo $p->valore ?>" class="form-control" placeholder="Valore" required/>
                                <span class="input-group-text" id="simbolo_<?php echo $p->id ?>"><?php echo ($p->tipo_provvigione == 'percentuale') ? '%' : '€' ?></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" placeholder="Descrizione (opzionale)" rows="3"><?php echo $p->descrizione ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $p->id ?>">
                        <input type="submit" class="btn btn-success" name="modifica_provvigione" value="Modifica" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function changeTipoProvvigione() {
        let tipo = document.getElementById('tipo_provvigione').value;
        let simbolo = document.getElementById('simbolo');

        if (tipo === 'percentuale') {
            simbolo.textContent = '%';
        } else {
            simbolo.textContent = '€';
        }
    }

    function changeTipoProvvigioneEdit(id) {
        let tipo = document.getElementById('tipo_provvigione_' + id).value;
        let simbolo = document.getElementById('simbolo_' + id);

        if (tipo === 'percentuale') {
            simbolo.textContent = '%';
        } else {
            simbolo.textContent = '€';
        }
    }
</script>

@include('utente.common.footer')