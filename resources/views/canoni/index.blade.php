@include('utente.common.header')

<!-- Aggiungo Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.badge {
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 4px;
}
.badge-success {
    background-color: #28a745;
    color: white;
}
.badge-danger {
    background-color: #dc3545;
    color: white;
}
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestione Canoni di Manutenzione</h4>

                <div class="page-title-right">
                    <!--
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                        <li class="breadcrumb-item active">Contacts</li>
                    </ol>-->
                </div>

            </div>
        </div>
    </div>
</div>

<div class="container-fluid" >
    <div class="row">
        <div class="col-12">
            <div class="card" style="margin-top:20px;">
                <div class="card-header">
                    <h3 class="card-title">Gestione Canoni di Manutenzione</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" onclick="nuovo_canone();">
                            <i class="fas fa-plus"></i> Nuovo Canone
                        </button>
                        <button type="button" class="btn btn-success" onclick="generaFatture()">
                            <i class="fas fa-file-invoice"></i> Genera Fatture
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Descrizione</th>
                                <th style="width:100px;">Importo</th>
                                <th>Data Inizio</th>
                                <th>Data Fine</th>
                                <th>Prossima Ricorrenza</th>
                                <th>Stato</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totale = 0; ?>
                            <?php foreach ($canoni as $canone) { $totale += $canone->importo; ?>
                            <tr>
                                <td>{{ $canone->cliente }}</td>
                                <td>{{ $canone->descrizione }}</td>
                                <td>€ {{ number_format($canone->importo, 2, ',', '.') }}</td>
                                <td>{{ date('d/m/Y', strtotime($canone->data_inizio)) }}</td>
                                <td>{{ $canone->data_fine ? date('d/m/Y', strtotime($canone->data_fine)) : '-' }}</td>
                                <td>{{ date('d/m/Y', strtotime($canone->prossima_ricorrenza)) }}</td>
                                <td>
                                    <span class="badge badge-{{ $canone->stato == 'attivo' ? 'success' : 'danger' }}">
                                        {{ ucfirst($canone->stato) }}
                                    </span>
                                </td>
                                <td>
                                    <button style="float:left;" type="button" class="btn btn-sm btn-info" onclick="modificaCanone({{ $canone->id }},{{ $canone->id_cliente }}, '{{ $canone->descrizione }}', {{ $canone->importo }}, '{{ $canone->tipo_ricorrenza }}', {{ $canone->valore_ricorrenza }}, '{{ $canone->data_inizio }}', '{{ $canone->data_fine }}', '{{ $canone->note }}', '{{ $canone->stato }}')">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </button>
                                    <button style="float:left;margin-left:5px;" type="button" class="btn btn-sm btn-danger" onclick="eliminaCanone({{ $canone->id }})">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                    @if($canone->stato == 'attivo' && strtotime($canone->prossima_ricorrenza) <= strtotime('today'))
                                    <button style="float:left;margin-left:5px;" type="button" class="btn btn-sm btn-success" onclick="creaFattura({{ $canone->id }})">
                                        <i class="fas fa-file-invoice fa-lg"></i> Crea Fattura
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th><?php echo $totale ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuovo Canone -->
<div class="modal fade" id="modalNuovoCanone" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('canoni.store') }}" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nuovo Canone</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Cliente</label>
                        <select class="form-control" name="id_cliente" required data-choices data-choices-search-true>
                            <?php foreach($clienti as $c){ ?>
                                <option value="<?php echo $c->id ?>"><?php echo $c->ragione_sociale ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descrizione</label>
                        <input type="text" class="form-control" name="descrizione" required>
                    </div>
                    <div class="form-group">
                        <label>Importo</label>
                        <input type="number" step="0.01" class="form-control" name="importo" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo Ricorrenza</label>
                        <select class="form-control" name="tipo_ricorrenza" required onchange="cambiaTipoRicorrenza(this.value)">
                            <option value="giorni">Ogni X giorni</option>
                            <option value="mensile">Giorno X del mese</option>
                            <option value="annuale">Giorno X dell'anno</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valore Ricorrenza</label>
                        <input type="number" class="form-control" name="valore_ricorrenza" required>
                    </div>
                    <div class="form-group">
                        <label>Data Inizio</label>
                        <input type="date" class="form-control" name="data_inizio" required>
                    </div>
                    <div class="form-group">
                        <label>Data Fine (opzionale)</label>
                        <input type="date" class="form-control" name="data_fine">
                    </div>
                    <div class="form-group">
                        <label>Note</label>
                        <textarea class="form-control" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifica Canone -->
<div class="modal fade" id="modalModificaCanone" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formModificaCanone" method="POST">
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Modifica Canone</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">



                    <div class="form-group">
                        <label>Cliente</label>
                        <select class="form-control" id="edit_id_cliente" name="id_cliente">
                            <?php foreach($clienti as $c){ ?>
                            <option value="<?php echo $c->id ?>"><?php echo $c->ragione_sociale ?></option>
                            <?php } ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Descrizione</label>
                        <input type="text" class="form-control" name="descrizione" id="edit_descrizione" required>
                    </div>
                    <div class="form-group">
                        <label>Importo</label>
                        <input type="number" step="0.01" class="form-control" name="importo" id="edit_importo" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo Ricorrenza</label>
                        <select class="form-control" name="tipo_ricorrenza" id="edit_tipo_ricorrenza" required onchange="cambiaTipoRicorrenza(this.value, 'edit')">
                            <option value="giorni">Ogni X giorni</option>
                            <option value="mensile">Giorno X del mese</option>
                            <option value="annuale">Giorno X dell'anno</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valore Ricorrenza</label>
                        <input type="number" class="form-control" name="valore_ricorrenza" id="edit_valore_ricorrenza" required>
                    </div>
                    <div class="form-group">
                        <label>Data Inizio</label>
                        <input type="date" class="form-control" name="data_inizio" id="edit_data_inizio" required>
                    </div>
                    <div class="form-group">
                        <label>Data Fine (opzionale)</label>
                        <input type="date" class="form-control" name="data_fine" id="edit_data_fine">
                    </div>
                    <div class="form-group">
                        <label>Note</label>
                        <textarea class="form-control" name="note" id="edit_note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Stato</label>
                        <select class="form-control" name="stato" id="edit_stato" required>
                            <option value="attivo">Attivo</option>
                            <option value="inattivo">Inattivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('utente.common.footer')


<script>

    function nuovo_canone(){

        $('#modalNuovoCanone').modal('show');
    }

function cambiaTipoRicorrenza(tipo, prefix = '') {
    const valoreInput = document.querySelector(`input[name="valore_ricorrenza"]${prefix ? '#' + prefix + '_valore_ricorrenza' : ''}`);
    
    switch(tipo) {
        case 'giorni':
            valoreInput.placeholder = 'Numero di giorni';
            break;
        case 'mensile':
            valoreInput.placeholder = 'Giorno del mese (1-31)';
            valoreInput.min = 1;
            valoreInput.max = 31;
            break;
        case 'annuale':
            valoreInput.placeholder = 'Giorno dell\'anno (1-365)';
            valoreInput.min = 1;
            valoreInput.max = 365;
            break;
    }
}

function modificaCanone(id,id_cliente, descrizione, importo, tipo_ricorrenza, valore_ricorrenza, data_inizio, data_fine, note, stato) {
    // Imposta i valori nel form di modifica
    $('#edit_id_cliente').val(id_cliente);

    $('#edit_descrizione').val(descrizione);
    $('#edit_importo').val(importo);
    $('#edit_tipo_ricorrenza').val(tipo_ricorrenza);
    $('#edit_valore_ricorrenza').val(valore_ricorrenza);
    $('#edit_data_inizio').val(data_inizio);
    $('#edit_data_fine').val(data_fine);
    $('#edit_note').val(note);
    $('#edit_stato').val(stato);
    
    // Imposta l'action del form
    $('#formModificaCanone').attr('action', `/canoni/${id}`);
    
    // Mostra il modal
    $('#modalModificaCanone').modal('show');
}

function eliminaCanone(id) {
    if (confirm('Sei sicuro di voler eliminare questo canone?')) {
        fetch(`/canoni/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

function generaFatture() {
    if (confirm('Vuoi generare le fatture per tutti i canoni attivi?')) {
        fetch('/canoni/genera-fatture', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

function creaFattura(id) {
    if (confirm('Vuoi creare la fattura per questo canone?')) {
        fetch(`/canoni/${id}/crea-fattura`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

// Inizializza i modal quando il documento è pronto
$(document).ready(function() {
    $('.modal').modal({
        show: false
    });
});
</script>