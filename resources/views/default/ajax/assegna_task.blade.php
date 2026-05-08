<?php foreach ($task as $t){ ?>
<div class="modal fade" id="modal_assegna_a_<?php echo $t->id ?>" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Assegna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Cliente<b style="color:red">*</b></label>
                            <select name="id_dipendente" class="form-control select2" required >
                                <option selected disabled>-- Seleziona un cliente --</option>
                                <?php foreach ($dipendenti as $d) { ?>
                                <option value="{{$d->id}}">{{$d->nome}} {{$d->cognome}}</option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="hidden"  name="id" value="{{$t->id}}" >

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="assegna_task" value="Assegna" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>