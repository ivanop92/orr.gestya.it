<?php foreach ($bandi as $b){ ?>
<div class="modal modal-xl fade" id="modal_modifica_bando_<?php echo $b->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Allegato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Logo per Bando</label>
                            <input class="form-control" type="file" name="immagine_bando" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" name="titolo" class="form-control" value="{{$b->titolo}}" required />
                        </div>

                        <div class="col-md-6">
                            <?php
                                // Supponiamo che $b->n_preventivi contenga la stringa "1,2,3,4,5,6,7,8,9,10"
                                $n_preventivi = $b->n_preventivi;

                                // Dividiamo la stringa in un array utilizzando la virgola come delimitatore
                                $numbersArray = explode(',', $n_preventivi);

                                // Prendiamo l'ultimo elemento dell'array
                                $lastNumber = end($numbersArray);
                            ?>
                            <label class="form-label">Numero di Preventivi</label>
                            <input type="text" name="n_preventivi" class="form-control" value="{{$lastNumber}}" />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">PDF Bando</label>
                            <input class="form-control" type="file" name="allegati" accept="application/pdf">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Decreto</label>
                            <input class="form-control" type="file" name="decreto" accept="application/pdf">
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Descrizione<b style="color:red">*</b></label>
                                <textarea rows="10" name="descrizione" class="form-control" >{{$b->descrizione}}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $b->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

