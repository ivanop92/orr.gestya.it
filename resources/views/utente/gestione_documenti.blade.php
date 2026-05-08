@include('utente.common.header')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestione Documenti</h4>

                    <div class="page-title-right">
                        <form method="post" style="display:inline-block;" onsubmit="return confirm('Inserisce le tipologie standard mancanti. Continuare?');">
                            @csrf
                            <button type="submit" name="ripristina_standard" value="1" class="btn btn-warning">
                                <i class="ri-refresh-line me-1 align-bottom"></i>Ripristina tipologie standard
                            </button>
                        </form>
                        <button class="btn btn-info add-btn" onclick="openEditModal('aggiungi',0,'','',0,0,0,0,0,0,0,0,0,0,0)">
                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Documento
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show w-100 mt-2 mb-0" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show w-100 mt-2 mb-0" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-lg-6">
            <div class="card">

                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <b>Ciclo Attivo</b>
                    </div>
                </div>

                <div class="card-body">
                    <div class="container mt-4">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Codice Documento</th>
                                <th>Descrizione</th>
                                <th>Flusso</th>
                                <th>Mg.P</th>
                                <th>Mg.A</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($documenti as $documento)
                                    <?php if($documento->attivo){ ?>
                                <tr>
                                    <td>{{ $documento->cd_do }}</td>
                                    <td>{{ $documento->descrizione }}</td>
                                    <td>{{ $documento->flusso }}</td>
                                    <td>
                                        @foreach($magazzini as $magazzino)
                                            @if($magazzino->id == $documento->id_mg_p)
                                                {{ $magazzino->codice_magazzino }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($magazzini as $magazzino)
                                            @if($magazzino->id == $documento->id_mg_a)
                                                {{ $magazzino->codice_magazzino }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="flusso_documentale('{{ $documento->cd_do }}')">Flusso</button>
                                        <button class="btn btn-sm btn-primary" onclick="openEditModal('modifica',{{ $documento->id }}, '{{ $documento->cd_do }}', '{{ $documento->descrizione }}', {{ $documento->attivo }}, {{ $documento->passivo }}, {{ $documento->scarico }}, {{ $documento->carico }}, {{ $documento->trasferimento }}, {{ $documento->fatturazione_ingresso }}, {{ $documento->fatturazione_uscita }}, {{ $documento->ordine }}, {{ $documento->scan_code }}, {{ $documento->id_mg_a ?? 'null' }}, {{ $documento->id_mg_p ?? 'null' }})">Modifica</button>
                                            <?php if($documento->fatturazione_ingresso == 0 and $documento->fatturazione_uscita == 0){ ?>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="setOrderIdToDelete({{ $documento->id }}, '{{ $documento->cd_do }}')">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">

                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <b>Ciclo Passivo</b>
                    </div>
                </div>

                <div class="card-body">
                    <div class="container mt-4">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Codice Documento</th>
                                <th>Descrizione</th>
                                <th>Flusso</th>
                                <th>Mg.P</th>
                                <th>Mg.A</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($documenti as $documento)
                                    <?php if(!$documento->attivo){ ?>
                                <tr>
                                    <td>{{ $documento->cd_do }}</td>
                                    <td>{{ $documento->descrizione }}</td>
                                    <td>{{ $documento->flusso }}</td>
                                    <td>
                                        @foreach($magazzini as $magazzino)
                                            @if($magazzino->id == $documento->id_mg_p)
                                                {{ $magazzino->codice_magazzino }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($magazzini as $magazzino)
                                            @if($magazzino->id == $documento->id_mg_a)
                                                {{ $magazzino->codice_magazzino }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="flusso_documentale('{{ $documento->cd_do }}')">Flusso</button>
                                        <button class="btn btn-sm btn-primary" onclick="openEditModal('modifica',{{ $documento->id }}, '{{ $documento->cd_do }}', '{{ $documento->descrizione }}', {{ $documento->attivo }}, {{ $documento->passivo }}, {{ $documento->scarico }}, {{ $documento->carico }}, {{ $documento->trasferimento }}, {{ $documento->fatturazione_ingresso }}, {{ $documento->fatturazione_uscita }}, {{ $documento->ordine }}, {{ $documento->scan_code }}, {{ $documento->id_mg_a ?? 'null' }}, {{ $documento->id_mg_p ?? 'null' }})">Modifica</button>
                                            <?php if($documento->fatturazione_ingresso == 0 and $documento->fatturazione_uscita == 0){ ?>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="setOrderIdToDelete({{ $documento->id }}, '{{ $documento->cd_do }}')">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <!-- Modal for delete confirmation -->
    <div class="modal fade" id="deleteDoc" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_documento" value="" id="id_documento">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Sei sicuro di eliminare questo documento?</h4>
                            <p class="text-muted fs-15 mb-4">Cancellando questo documento verranno cancellati tutti i suoi dati a database.</p>
                            <div class="hstack gap-2 justify-content-center remove">
                                <button type="button" class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Chiudi</button>
                                <button type="submit" class="btn btn-danger" name="elimina" value="1">Sì, Elimina</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing document -->
    <div class="modal fade modal-xl" id="editDocModal" tabindex="-1" aria-labelledby="editDocLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_documento" value="" id="edit_id_documento">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDocLabel">Modifica Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_cd_do" class="form-label">Codice Documento</label>
                            <input type="text" class="form-control" id="edit_cd_do" name="edit_cd_do" required>
                            <small id="edit_cd_do_lock_msg" class="text-muted" style="display:none;"><i class="ri-lock-line"></i> Codice standard: non modificabile (i moduli del sistema dipendono da questo nome esatto). Puoi cambiare la descrizione e i flag.</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" id="edit_descrizione" name="edit_descrizione" required>
                        </div>
                        <div class="d-flex justify-content-around">
                            <!-- Checkbox per ogni campo booleano -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_attivo" name="edit_attivo">
                                <label class="form-check-label" for="edit_attivo">Attivo</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_passivo" name="edit_passivo">
                                <label class="form-check-label" for="edit_passivo">Passivo</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_scarico" name="edit_scarico">
                                <label class="form-check-label" for="edit_scarico">Scarico</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_carico" name="edit_carico">
                                <label class="form-check-label" for="edit_carico">Carico</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_trasferimento" name="edit_trasferimento">
                                <label class="form-check-label" for="edit_trasferimento">Trasferimento</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_fatturazione_ingresso" name="edit_fatturazione_ingresso">
                                <label class="form-check-label" for="edit_fatturazione">Fatturazione Ingresso</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_fatturazione_uscita" name="edit_fatturazione_uscita">
                                <label class="form-check-label" for="edit_fatturazione">Fatturazione Uscita</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_ordine" name="edit_ordine">
                                <label class="form-check-label" for="edit_ordine">Ordine</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_scan_code" name="edit_scan_code">
                                <label class="form-check-label" for="edit_scan_code">Scan Code</label>
                            </div>


                        </div>

                        <div class="mb-3 magazzini-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="magazzino-partenza">
                                        <label for="edit_id_mg_p" class="form-label">Magazzino di Partenza</label>
                                        <select class="form-control" id="edit_id_mg_p" name="edit_id_mg_p">
                                            <option value="0">Seleziona Magazzino</option>
                                            @foreach($magazzini as $magazzino)
                                                <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="magazzino-arrivo">
                                        <label for="edit_id_mg_a" class="form-label">Magazzino di Arrivo</label>
                                        <select class="form-control" id="edit_id_mg_a" name="edit_id_mg_a">
                                            <option value="0">Seleziona Magazzino</option>
                                            @foreach($magazzini as $magazzino)
                                                <option value="{{ $magazzino->id }}">{{ $magazzino->descrizione }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" id="pulsante_salvataggio" class="btn btn-primary" name="modifica_documento" value="modifica">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php foreach($documenti as $d){ ?>

        <!-- Modal for editing document -->
<div class="modal fade modal-xl" id="flusso_documentale_<?php echo $d->cd_do ?>" aria-labelledby="editDocLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocLabel">Flusso Documentale <?php echo $d->cd_do ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select name="flusso[]" class="form-control" data-choices data-choices-removeItem multiple>
                            <?php foreach($documenti as $dn){ ?>
                                <?php if($dn->attivo == $d->attivo && $dn->cd_do != $d->cd_do){ ?>
                                    <option value="<?php echo $dn->cd_do ?>" <?php echo (in_array($dn->cd_do,explode(',',$d->flusso)))?'selected':'' ?>><?php echo $dn->cd_do ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <input type="hidden" name="id" value="<?php echo $d->id ?>">
                    <button type="submit" class="btn btn-primary" name="salva_flusso_documentale" value="1">Salva Flusso</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php } ?>


<div id="ajax_loader"></div>

@include('utente.common.footer')

<script>
    var CODICI_STANDARD = @json($codici_standard);

    function setOrderIdToDelete(docId, cd_do) {
        if (cd_do && CODICI_STANDARD.indexOf(cd_do) !== -1) {
            alert('Il codice "' + cd_do + '" e\' una tipologia standard del sistema e non puo\' essere eliminata. Se non la usi, lasciala disattivata.');
            return;
        }
        $('#deleteDoc').modal('show');
        document.getElementById('id_documento').value = docId;
    }

    function openEditModal(tipologia,id, cd_do, descrizione, attivo, passivo, scarico, carico, trasferimento, fatturazione_ingresso, fatturazione_uscita, ordine, scan_code, id_mg_a, id_mg_p) {
        $('#editDocModal').modal('show');
        document.getElementById('edit_id_documento').value = id;
        document.getElementById('edit_cd_do').value = cd_do;

        var isStandard = CODICI_STANDARD.indexOf(cd_do) !== -1 && tipologia !== 'aggiungi';
        document.getElementById('edit_cd_do').readOnly = isStandard;
        document.getElementById('edit_cd_do_lock_msg').style.display = isStandard ? 'block' : 'none';
        document.getElementById('edit_descrizione').value = descrizione;
        document.getElementById('edit_attivo').checked = attivo;
        document.getElementById('edit_passivo').checked = passivo;
        document.getElementById('edit_scarico').checked = scarico;
        document.getElementById('edit_carico').checked = carico;
        document.getElementById('edit_trasferimento').checked = trasferimento;
        document.getElementById('edit_fatturazione_ingresso').checked = fatturazione_ingresso;
        document.getElementById('edit_fatturazione_uscita').checked = fatturazione_uscita;
        document.getElementById('edit_ordine').checked = ordine;
        document.getElementById('edit_scan_code').checked = scan_code;

        function updateMagazzinoVisibility() {
            const trasferimentoCheck = document.getElementById('edit_trasferimento');
            const caricoCheck = document.getElementById('edit_carico');
            const scaricoCheck = document.getElementById('edit_scarico');

            if (trasferimentoCheck.checked) {
                // Se è trasferimento, mostra entrambi i magazzini
                $('.magazzini-section').show();
                $('.magazzino-arrivo').show();
                $('.magazzino-partenza').show();
                $('#edit_id_mg_p').prop('required', true);
                $('#edit_id_mg_a').prop('required', true);
            } else if (caricoCheck.checked || scaricoCheck.checked) {
                // Se è carico o scarico, mostra solo magazzino di partenza
                $('.magazzini-section').show();
                $('.magazzino-arrivo').show();
                $('.magazzino-partenza').hide();
                $('#edit_id_mg_p').prop('required', false);
                $('#edit_id_mg_a').prop('required', true);
            } else {
                // Altrimenti nascondi tutto
                $('.magazzini-section').hide();
                $('#edit_id_mg_p').prop('required', false);
                $('#edit_id_mg_a').prop('required', false);
            }
        }

        // Aggiungi gli event listener per tutti i checkbox rilevanti
        $('#edit_trasferimento').change(updateMagazzinoVisibility);
        $('#edit_carico').change(updateMagazzinoVisibility);
        $('#edit_scarico').change(updateMagazzinoVisibility);

        // Imposta i valori dei magazzini
        $('#edit_id_mg_a').val(id_mg_a);
        $('#edit_id_mg_p').val(id_mg_p);

        if(tipologia == 'aggiungi'){
            $('#pulsante_salvataggio').attr('name', 'aggiungi');
            $('#pulsante_salvataggio').attr('value', 'Aggiungi');
        } else {
            $('#pulsante_salvataggio').attr('name', 'modifica_documento');
            $('#pulsante_salvataggio').attr('value', 'Modifica');
        }

        // Esegui il controllo iniziale
        updateMagazzinoVisibility();
    }

    function flusso_documentale(cd_do) {
        $('#flusso_documentale_'+cd_do).modal('show');
    }

</script>

