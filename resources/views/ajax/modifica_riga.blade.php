<form method="post" enctype="multipart/form-data">
    <div class="modal" id="modal_modifica_riga">
        <div class="modal-dialog modal-lg" style="max-width:95%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modifica Righe <?php echo $p->numero.'/'.$p->anno ?></h4>
                </div>
                <div class="modal-body">

                <table id="tabella_immagini" class="table table-bordered table-hover" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="no-sort" style="width:10px;display:none">ID</th>
                            <th class="no-sort" style="width:20px;">Ord</th>
                            <th class="no-sort" style="width:20px;">Servizio</th>
                            <th class="no-sort" style="width:80px;">Cod. Art</th>
                            <th class="no-sort" style="width:70px;">Setup</th>
                            <th class="no-sort" style="width:300px;">Desc.</th>
                            <th class="no-sort">Attività</th>
                            <th class="no-sort" style="width:60px;">Qta</th>
                            <th class="no-sort" style="width:60px;">P.U.</th>
                            <th class="no-sort">Desc. Mat.</th>
                            <th class="no-sort">Materiale</th>
                            <th class="no-sort">Totale</th>
                            <th class="no-sort" style="width:50px;"></th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach($righe as $r){ ?>
                        <tr>
                            <td style="display:none"><?php echo $r->id ?></td>
                            <td><?php echo $r->ordinamento ?></td>
                            <td><input type="text" class="form-control" id="servizio_<?php echo $r->id ?>" value="<?php echo $r->servizio ?>" name="servizio[<?php echo $r->id ?>]" placeholder="Servizio"></td>
                            <td><input type="text" class="form-control" id="codice_<?php echo $r->id ?>" value="<?php echo $r->codice ?>" name="codice[<?php echo $r->id ?>]" placeholder="Codice Articolo"></td>
                            <td>
                                <select class="form-control select2" id="setup_tank_<?php echo $r->id ?>" name="setup_tank[<?php echo $r->id ?>]">
                                    <option value="0" <?php echo ($r->setup_tank == 0)?'selected':'' ?>>NO</option>
                                    <option value="1" <?php echo ($r->setup_tank == 1)?'selected':'' ?>>SI</option>
                                </select>
                            </td>
                            <td><textarea class="form-control" id="descrizione_<?php echo $r->id ?>" name="descrizione[<?php echo $r->id ?>]" placeholder="Descrizione" style="height:90px;resize: none;"><?php echo $r->descrizione ?></textarea></td>
                            <td><input type="number" min="0" step="1" name="attivita[<?php echo $r->id ?>]" class="form-control" id="attivita_<?php echo $r->id ?>" placeholder="Attivita" value="<?php echo $r->attivita ?>" required></td>
                            <td><input type="number" min="0" step="0.01" name="qta[<?php echo $r->id ?>]" class="form-control" id="qta_<?php echo $r->id ?>" placeholder="qta" value="<?php echo $r->qta ?>" required onkeyup="$('#minuti_<?php echo $r->id ?>').val(0);cambia_totale_modifica(<?php echo $r->id ?>)"></td>
                            <td><input type="number"  step="0.01" name="pu[<?php echo $r->id ?>]" class="form-control" id="pu_<?php echo $r->id ?>" value="<?php echo $r->pu ?>" placeholder="Prezzo Unitario" required onkeyup="cambia_totale_modifica(<?php echo $r->id ?>)"></td>
                            <td><input type="text" name="descrizione_materiale[<?php echo $r->id ?>]" class="form-control" id="descrizione_materiale_<?php echo $r->id ?>" placeholder="Descrizione Materiale" value="<?php echo $r->descrizione_materiale ?>" placeholder="Descrizione Materiale"></td>
                            <td><input type="number" min="0" step="0.01" name="materiale[<?php echo $r->id ?>]" class="form-control" id="materiale_<?php echo $r->id ?>" placeholder="Materiale" value="<?php echo $r->materiale ?>"></td>
                            <td><input type="number"  step="0.01" name="pt[<?php echo $r->id ?>]" class="form-control" id="pt_<?php echo $r->id ?>" placeholder="Prezzo Totale" value="<?php echo $r->pt ?>" required readonly></td>
                            <td><a class="btn btn-danger btn-sm" onclick="elimina_riga(<?php echo $r->id ?>)">ELIMINA</a></td>
                        </tr>
                        <?php } ?>

                        </tbody>
                    </table>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id_testata" value="<?php echo $p->id ?>">
                    <input type="submit" class="btn btn-primary" name="modifica_righe" value="Modifica Righe">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>


<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_modifica_testata_<?php echo $p->id ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title">Modifica Preventivo <?php echo $p->numero.'/'.$p->anno ?></h4>
                </div>
                <div class="modal-body row">


                    <div class="col-sm-6">
                        <label>Cliente <b style="color:red">*</b></label>
                        <select name="id_cliente" class="form-control select2" required style="width:100%">
                            <?php foreach($clienti as $c) { ?>
                            <option value="<?php echo $c->id ?>" <?php echo ($p->id_cliente == $c->id)?'selected':'' ?>><?php echo $c->descrizione ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label>Numero <b style="color:red">*</b></label>
                        <input type="text" name="numero" class="form-control" id="numero" placeholder="Numero" value="<?php echo $p->numero ?>">
                    </div>

                    <div class="col-sm-3">
                        <label>Data <b style="color:red">*</b></label>
                        <input type="text" name="data" class="form-control date-picker" id="data" placeholder="data" value="<?php echo date('d/m/Y',strtotime($p->data)) ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label>Stato<b style="color:red">*</b></label>
                        <select id="stato_<?php echo $p->id ?>" name="stato" class="form-control select2" onchange="mostra_operatori(<?php echo $p->id ?>);" required style="width:100%">
                            <option value="0" <?php echo ($p->stato == 0)?'selected':'' ?>>Preventivo</option>
                            <option value="1" <?php echo ($p->stato == 1)?'selected':'' ?>>Ordine</option>
                            <option value="2" <?php echo ($p->stato == 2)?'selected':'' ?>>Ordine Completato</option>
                            <option value="3" <?php echo ($p->stato == 3)?'selected':'' ?>>Ordine In Ritardo</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label>Fatturato<b style="color:red">*</b></label>
                        <select id="fatturato_<?php echo $p->id ?>" name="fatturato" class="form-control select2" required style="width:100%">
                            <option value="0" <?php echo ($p->fatturato == 0)?'selected':'' ?>>NO</option>
                            <option value="1" <?php echo ($p->fatturato == 1)?'selected':'' ?>>SI</option>
                        </select>
                    </div>

                    <div class="col-sm-4">
                        <label>Nr. Automezzo</label>
                        <input type="text" name="automezzo" class="form-control" id="automezzo" placeholder="Automezzo" value="<?php echo $p->automezzo ?>">
                    </div>

                    <div class="col-sm-4">
                        <label>Nr. Ordine Rif.</label>
                        <input type="text" name="numero_ordine_rif" class="form-control" id="numero_ordine_rif" placeholder="Numero Ordine Rif." value="<?php echo $p->numero_ordine_rif ?>">
                    </div>

                    <div class="col-sm-4">
                        <label>Localita</label>
                        <input type="text" name="localita" class="form-control" id="localita" placeholder="Localita" value="<?php echo $p->localita ?>">
                    </div>

                    <div class="col-sm-4" style="margin">
                        <label>Nome Referente</label>
                        <input type="text" name="nome_recapito" class="form-control" id="nome_recapito" placeholder="Nome Recapito" value="<?php echo $p->nome_recapito ?>">
                    </div>

                    <div class="col-sm-4">
                        <label>Email Referente</label>
                        <input type="text" name="email_recapito" class="form-control" id="email_recapito" placeholder="Email Recapito" value="<?php echo $p->email_recapito ?>">
                    </div>

                    <div class="col-sm-4">
                        <label>Reason Intake</label>
                        <input type="text" name="reason_intake" class="form-control" id="reason_intake" placeholder="Reason Intake" value="<?php echo $p->reason_intake ?>">
                    </div>


                    <div id="operatori_<?php echo $p->id ?>" class="col-sm-12" style="display:none;">
                        <label>Operatore Associato<b style="color:red">*</b></label>
                        <select id="id_utente_<?php echo $p->id ?>" name="id_utente" class="form-control select2" style="width:100%">
                            <option value="0">Scegli un Operatore</option>
                            <?php foreach($utenti as $u){ ?>
                            <option value="<?php echo $u->id ?>" <?php echo ($u->id == $p->id_utente)?'selected':'' ?>><?php echo $u->ragione_sociale ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-sm-12">
                        <label>Note Operatore</label>
                        <textarea style="height:150px;" name="note_operatore" class="form-control" id="note_operatore_<?php echo $p->id ?>" placeholder="Note Operatore"><?php echo $p->note_operatore ?></textarea>
                    </div>



                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?php echo $p->id ?>">
                    <input type="submit" class="btn btn-primary" name="modifica_testata" value="Modifica Testata">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi_riga_<?php echo $p->id ?>">
        <div class="modal-dialog modal-lg" style="max-width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Righe <?php echo $p->numero.'/'.$p->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="clearfix" style="margin-bottom:20px;"></div>

                    <?php for($i = 0;$i<10;$i++){ ?>


                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 0){ ?><label>Servizio</label> <?php } ?>
                            <input type="text" class="form-control" id="servizio_<?php echo $i ?>" name="servizio[<?php echo $i ?>]" placeholder="Servizio">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 0){ ?><label>Cod. Art.</label> <?php } ?>
                            <input type="text" min="0" class="form-control" id="codice_<?php echo $i ?>" name="codice[<?php echo $i ?>]" placeholder="Codice Articolo">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 0){ ?><label>Setup Tank</label> <?php } ?>
                            <select class="form-control select2" id="setup_tank_<?php echo $i ?>" name="setup_tank[<?php echo $i ?>]">
                                <option value="0">NO</option>
                                <option value="1">SI</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <?php if($i == 0){ ?><label>Descrizione</label> <?php } ?>
                            <input type="text" min="0" class="form-control" id="descrizione_<?php echo $i ?>" name="descrizione[<?php echo $i ?>]" placeholder="Descrizione">
                        </div>
                    </div>


                    <div class="col-sm-1">
                        <?php if($i == 0){ ?><label>Attività<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="1" name="attivita[<?php echo $i ?>]" class="form-control" id="attivita_<?php echo $i ?>" placeholder="Attivita" value="1" required>
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 0){ ?><label>Qta<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.01" name="qta[<?php echo $i ?>]" class="form-control" id="qta_<?php echo $i ?>" placeholder="qta" value="1" required onkeyup="cambia_totale(<?php echo $i ?>)">
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 0){ ?><label>P.U.<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.01" name="pu[<?php echo $i ?>]" class="form-control" id="pu_<?php echo $i ?>" placeholder="Prezzo Unitario" value="0" required onkeyup="cambia_totale(<?php echo $i ?>)">
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 0){ ?><label>Aliquota Iva<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="1" name="aliquota[<?php echo $i ?>]" class="form-control" id="imposta_<?php echo $i ?>" placeholder="Aliquota" value="0" required>
                    </div>


                    <div class="col-sm-2">
                        <?php if($i == 0){ ?><label>Totale<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.01" name="pt[<?php echo $i ?>]" class="form-control" id="pt_<?php echo $i ?>" placeholder="Prezzo Totale" value="0" required readonly>
                    </div>

                    <?php } ?>


                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id_testata" value="<?php echo $p->id ?>">
                    <input type="submit" class="btn btn-primary" name="aggiungi_riga" value="Aggiungi Riga">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal_aggiungi_riga_touax_<?php echo $p->id ?>">
        <div class="modal-dialog modal-lg" style="max-width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Righe <?php echo $p->numero.'/'.$p->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="clearfix" style="margin-bottom:20px;"></div>

                    <?php for($i = 10;$i<20;$i++){ ?>


                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 10){ ?><label>Servizio</label> <?php } ?>
                            <input type="text" class="form-control" id="servizio_<?php echo $i ?>" name="servizio[<?php echo $i ?>]" placeholder="Servizio">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 10){ ?><label>Cod. Art.</label> <?php } ?>
                            <input type="text" min="0" class="form-control" id="codice_<?php echo $i ?>" name="codice[<?php echo $i ?>]" placeholder="Codice Articolo">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group">
                            <?php if($i == 10){ ?><label>Setup Tank</label> <?php } ?>
                            <select class="form-control select2" id="setup_tank_<?php echo $i ?>" name="setup_tank[<?php echo $i ?>]">
                                <option value="0">NO</option>
                                <option value="1">SI</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <?php if($i == 10){ ?><label>Descrizione</label> <?php } ?>
                            <input type="text" min="0" class="form-control" id="descrizione_<?php echo $i ?>" name="descrizione[<?php echo $i ?>]" placeholder="Descrizione">
                        </div>
                    </div>


                    <div class="col-sm-1">
                        <?php if($i == 10){ ?><label>Attività<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="1" name="attivita[<?php echo $i ?>]" class="form-control" id="attivita_<?php echo $i ?>" placeholder="Attivita" value="1" required>
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 10){ ?><label>Minuti<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.001" name="minuti[<?php echo $i ?>]" class="form-control" id="minuti_<?php echo $i ?>" placeholder="minuti" value="1" required onkeyup="cambia_totale_touax(<?php echo $i ?>)">
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 10){ ?><label>P.U.<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.01" name="pu[<?php echo $i ?>]" class="form-control" id="pu_<?php echo $i ?>" placeholder="Prezzo Unitario" value="0" required onkeyup="cambia_totale_touax(<?php echo $i ?>)">
                    </div>

                    <div class="col-sm-1">
                        <?php if($i == 10){ ?><label>Aliquota Iva<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="1" name="aliquota[<?php echo $i ?>]" class="form-control" id="imposta_<?php echo $i ?>" placeholder="Aliquota" value="0" required>
                    </div>


                    <div class="col-sm-2">
                        <?php if($i == 10){ ?><label>Totale<b style="color:red">*</b></label> <?php } ?>
                        <input type="number" min="0" step="0.01" name="pt[<?php echo $i ?>]" class="form-control" id="pt_<?php echo $i ?>" placeholder="Prezzo Totale" value="0" required readonly>
                    </div>

                    <?php } ?>


                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id_testata" value="<?php echo $p->id ?>">
                    <input type="submit" class="btn btn-primary" name="aggiungi_riga" value="Aggiungi Riga">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>


<script type="text/javascript">

    function cambia_totale_modifica(id){

        pu = parseFloat($('#pu_'+id).val()).toFixed(2);
        qta = $('#qta_'+id).val();
        pt = pu*qta
        $('#pt_'+id).val(parseFloat(pt).toFixed(2));

    }

    function sposta_riga(id,direzione){

        $.get( "/admin/ajax/sposta_riga/"+id+"/"+direzione, function( data ) {
            if(data == "ok"){
                $.get( "/admin/ajax/modifica_righe/<?php echo $p->id ?>", function( data ) {
                    $("#ajax_loader").html(data);
                    $('.modal-backdrop').remove();
                    $('#modal_modifica_riga').modal('show');
                });
            }
        });

    }

    function elimina_riga(id){

        $.get( "/admin/ajax/elimina_riga/"+id, function( data ) {
            if(data == "ok"){
                $.get( "/admin/ajax/modifica_righe/<?php echo $p->id ?>", function( data ) {
                    $("#ajax_loader").html(data);
                    $('.modal-backdrop').remove();
                    $('#modal_modifica_riga').modal('show');
                });
            }
        });

    }


    var tabella_immagini = $('#tabella_immagini').DataTable({
        rowReorder: true,
        paging: false,
        ordering: false,
        info: false,
        searching: false,

        columnDefs: [
            { targets: [0], visible: false }
        ],
    });


    tabella_immagini.on('row-reorder', function (e, diff, edit) {

        for (var i = 0, ien = diff.length; i < ien; i++) {
            var rowData = tabella_immagini.row(diff[i].node).data();

            console.log(diff[i]);


            $.get("<?php echo URL::ASSET('admin/ajax/aggiorna_posizione_preventivo') ?>/"+rowData[0]+"/"+diff[i].newPosition, function( data ) {
            });

        }

        $('#result').html('Risultato:<br>' + result);
    });

    setTimeout(function(){
        tabella_immagini.columns.adjust();
    }, 200);



</script>


<style>
    .arrows{
        display: inline;
    }

    .fa-chevron-circle-down,
    .fa-chevron-circle-up{
        cursor:pointer;
    }

    #tabella_immagini td, #tabella_immagini th {
        padding: 5px;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

</style>