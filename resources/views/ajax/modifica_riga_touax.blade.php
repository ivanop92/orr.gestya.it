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
                            <th class="no-sort" style="width:60px;">Minuti</th>
                            <th class="no-sort" style="width:60px;">P.U.</th>
                            <th class="no-sort">Iva</th>
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
                            <td><input type="number" min="0" step="0.001" name="minuti[<?php echo $r->id ?>]" class="form-control" id="minuti_<?php echo $r->id ?>" placeholder="Minuti" value="<?php echo $r->minuti ?>" required onkeyup="cambia_totale_modifica(<?php echo $r->id ?>)"></td>
                            <td><input type="number"  step="0.01" name="pu[<?php echo $r->id ?>]" class="form-control" id="pu_<?php echo $r->id ?>" value="<?php echo $r->pu ?>" placeholder="Prezzo Unitario" required onkeyup="cambia_totale_modifica(<?php echo $r->id ?>)"></td>
                            <td><input type="number" min="0" step="1" name="aliquota[<?php echo $r->id ?>]" class="form-control" id="imposta_<?php echo $r->id ?>" value="<?php echo $r->aliquota ?>" placeholder="Aliquota" value="22" required></td>
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

<script type="text/javascript">

    function cambia_totale_modifica(id){

        pu = parseFloat($('#pu_'+id).val());
        costo_orario = (parseFloat($('#minuti_'+id).val())/60);
        materiale = parseFloat($('#materiale_'+id).val());
        pt = (pu*costo_orario)+parseFloat(materiale);
        console.log(pt);
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
                $.get( "/admin/ajax/modifica_righe_touax/<?php echo $p->id ?>", function( data ) {
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