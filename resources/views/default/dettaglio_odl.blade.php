

@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">


                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gestione ODL <?php echo $odl->numero ?> del <?php echo date('d/m/Y',strtotime($odl->data)) ?> - <b><?php echo $odl->articolo ?></b></h3>
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <a style="float:right;" class="btn btn-success" href="<?php echo URL::asset('admin/odl') ?>">Torna Indietro</a>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
@if($odl->stato === 2)
                            <a target="_blank" href="/stampa/tracciabilita/<?php echo $odl->id ?>"  class="btn btn-primary" style="width:10%">TRACCIABILITA'</a>
                        @endif
                        <div class="clearfix" style="margin-bottom:10px"></div>

                        <table class="table table-bordered table-hover datatable" border="0" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:20px;">Ordine</th>
                                <th style="width:100px;">ODL</th>
                                <th style="width:100px;">FASE</th>
                                <th style="width:100px;">Qta</th>
                                <th style="width:100px;">Inizio</th>
                                <th style="width:100px;">Fine</th>
                                <th style="width:100px;">Qta Fatte</th>
                                <th style="width:30px;">Completato</th>
                                <th style="width:250px;"></th>
                            </tr>
                            </thead>

                            <tbody>

                            <?php $i = 1; ?>
                            <?php foreach($odl_righe as $or){ ?>

                                <?php
                                $background = 'rgba(231, 76, 60,0.1)';
                                if($or->inizio != '' && $or->fine == '' && $or->completato == 0) $background = 'rgba(52, 152, 219,0.1)';
                                if($or->fine != '' && $or->completato == 1) $background = 'rgba(46, 204, 113,0.1)';
                                ?>

                            <tr style="background: <?php echo $background; ?>">
                                <td><?php echo $i ?></td>
                                <td><?php echo $or->odl ?></td>
                                <td><?php echo $or->nome_fase ?></td>
                                <td><?php echo $or->qta ?></td>
                                <td><?php echo $or->inizio ?></td>
                                <td><?php echo $or->fine ?></td>
                                <td><?php echo $or->qta_fatta.'/'.$or->qta ?></td>
                                <td><?php echo $or->completato ? 'si' : 'no'?></td>

                                <td style="text-align: left;">

                                        <?php if($or->inizio == '' && $or->completato == 0){ ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form method="post" onsubmit="return confirm('Sei sicuro di voler iniziare questa Fase ?')">

                                                    <?php if(sizeof($operatori) > 0){ ?>
                                                <select class="form-control select2" name="id_operatore" required style="width:100%;">
                                                    <option value="">Scegli Operatore</option>
                                                        <?php foreach($operatori as $o){ ?>
                                                    <option value="<?php echo $o->id ?>"><?php echo $o->nome ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php } else { ?>
                                                <input type="hidden" name="id_operatore" value="0">
                                                <?php } ?>

                                                <input type="hidden" name="id" value="<?php echo $or->id ?>">
                                                <button type="submit" name="start_fase" class="btn btn-success" value="start_fase" style="width:100%">START</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php } else if($or->fine == '' && $or->completato == 0){ ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <form method="post" onsubmit="return confirm('Sei sicuro di voler iniziare questa Fase ?')">
                                                    <?php if(sizeof($operatori) > 0){ ?>
                                                        <select class="form-control select2" name="id_operatore" required style="width:100%;">
                                                            <option value="">Scegli Operatore</option>
                                                                <?php foreach($operatori as $o){ ?>
                                                                <option value="<?php echo $o->id ?>" <?php echo ($o->id == $or->id)?'selected':'' ?>><?php echo $o->nome ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    <?php } else { ?>
                                                        <input type="hidden" name="id_operatore" value="0">
                                                    <?php } ?>


                                                <input type="hidden" name="id" value="<?php echo $or->id ?>">
                                                <button type="submit" name="start_fase" class="btn btn-success" value="start_fase" style="width:100%">RIAPRI</button>
                                            </form>
                                        </div>

                                        <div class="col-md-12">
                                            <a onclick="modal_chiudi_odl(<?php echo $or->id ?>)" class="btn btn-danger" style="width:100%">STOP</a>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                        <b>Completato</b><br>

                                        <?php if($or->note != ''){ ?>
                                            <br>Note:<br><?php echo nl2br($or->note) ?>
                                         <?php } ?>
                                     <?php } ?>

                                </td>

                            </tr>

                                <?php $i++;} ?>

                            </tbody>
                        </table>

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


@include('default.common.footer')


<style>

    div.dataTables_wrapper div.dataTables_filter label {
        font-weight: normal;
        white-space: nowrap;
        text-align: left;
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: .5em;
        display: inline-block;
        width: 89%;
    }
</style>


<script type="text/javascript">

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
                            <option value="<?php echo $o->id ?>" <?php echo ($o->id == $o->id)?'selected':'' ?>><?php echo $o->nome ?></option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                        <input type="hidden" name="id_operatore" value="0">
                        <?php } ?>
                    </div>

                    <!-- Materiali specifici per la fase -->
                        <?php foreach($or->materiali as $i => $m){ ?>
                    <div class="row">
                        <div class="col-md-6">
                                <?php if($i == 0) { ?><label>Materiale <b style="color:red">*</b></label><?php } ?>
                            <input readonly type="text" name="materiale[<?php echo $i ?>]" value="<?php echo $m->titolo ?>" class="form-control">
                        </div>

                        <div class="col-md-3">
                                <?php if($i == 0) { ?><label>Qta<b style="color:red">*</b></label><?php } ?>
                            <input readonly type="number" min="0" step="0.0001" name="quantita[<?php echo $i ?>]" value="<?php echo $m->qta ?>" class="form-control">
                        </div>

                        <div class="col-md-3">
                                <?php if($i == 0) { ?><label>Lotto<b style="color:red">*</b></label><?php } ?>
                            <input type="text" name="lotto[<?php echo $i ?>]" placeholder="Inserisci Lotto" class="form-control">
                        </div>
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


<script type="text/javascript">


    function modal_chiudi_odl(id_riga){

        $('#modal_chiudi_fase_'+id_riga).modal('show');
    }

    window.onload = (event) => {
        $('body').addClass('sidebar-collapse');
    };

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }



</script>
