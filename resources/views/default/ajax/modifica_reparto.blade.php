<?php foreach ($reparti as $r){ ?>
<div class="modal modal-md fade" id="modal_modifica_reparto_<?php echo $r->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica {{$r->descrizione}} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div>
                                <label  class="form-label">Nome Reparto <b style="color:red">*</b></label>
                                <input type="text" value="{{$r->descrizione}}" name="descrizione" class="form-control" placeholder="Nome Reparto..." required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $r->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica_reparto" value="Modifica" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

