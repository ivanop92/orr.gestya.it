<?php foreach ($bandi as $b){ ?>
<?php $array_clienti = explode(',', $b->id_clienti) ?>
<?php $clienti = DB::table('utenti')->whereIn('id', $array_clienti)->get() ?>
<?php $array_mail_inviate = explode(',', $b->mail_inviate) ?>


    <div class="modal fade" id="modal_invia_mail_<?php echo $b->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Invio Mail Clienti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                       <?php foreach ($clienti as $cl){ ?>
                            <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 10px;">
                                <input <?php if(!in_array($cl->id, $array_mail_inviate)){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 6px" type="checkbox" name="array_clienti_per_mail[]" value="{{$cl->id}}"><span>{{$cl->ragione_sociale}}</span><?php if(in_array($cl->id, $array_mail_inviate)){ ?> <span style="margin-left: 20px; font-size: 10px">(Mail già inviata)</span> <?php } ?>
                            </div>
                       <?php } ?>
                        <input type="hidden" name="info_bando" value="{{$b->id}}">
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="hidden" name="id" value="<?php echo $b->id ?>">
                            <input type="submit" class="btn btn-success" id="add-btn" name="invia_mail" value="Invia" >
                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php } ?>