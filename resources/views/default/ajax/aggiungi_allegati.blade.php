
<?php foreach ($bandi as $b){ ?>
<div class="modal fade" id="modal_modifica_aggiungi_allegati_<?php echo $b->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Allegati Richiesti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <?php foreach ($allegati_bandi as $alb){ ?>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 10px;">
                                    <input <?php if(strpos($b->id_allegati, (string)$alb->id) !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 6px" type="checkbox" name="array_allegati[]" value="{{$alb->id}}"><span>{{$alb->descrizione}}</span>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $b->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi_allegati" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

