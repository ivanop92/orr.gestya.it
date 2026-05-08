<?php foreach ($task as $t){ ?>
<div class="modal fade" id="modal_modifica_task_<?php echo $t->id ?>" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Dipendente</label>
                            <select name="id_dipendente" class="form-control"  >
                                <?php foreach ($dipendenti as $d) { ?>
                                    <option <?php if($t->id_dipendente == $d->id){ ?> selected <?php } ?> value="{{$d->id}}">{{$d->nome}} {{$d->cognome}}</option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione<b style="color:red">*</b></label>
                                <textarea placeholder="Descrizione..." rows="6" name="descrizione" class="form-control" required >{{$t->descrizione}}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Allegato</label>
                                <input type="file" class="form-control"  name="allegato">
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden"  name="id" value="{{$t->id}}" >
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica_task" value="Modifica" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>