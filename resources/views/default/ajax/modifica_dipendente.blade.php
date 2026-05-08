<?php foreach ($dipendenti as $d){ ?>
<div class="modal modal-xl fade" id="modal_modifica_dipendente_<?php echo $d->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica {{$d->nome}} {{$d->cognome}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">

                                    <div class="avatar-lg p-1">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="<?php echo URL::asset($d->immagine) ?>" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Nome <b style="color:red">*</b></label>
                                <input type="text" value="<?php echo $d->nome ?>" name="nome" class="form-control" placeholder="Nome" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Cognome <b style="color:red">*</b></label>
                                <input type="text" value="<?php echo $d->cognome ?>" name="cognome" class="form-control" placeholder="Cognome" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Reparto <b style="color:red">*</b></label>
                                <select name="id_reparto" class="form-control" required >
                                    <?php foreach ($reparti as $r) { ?>
                                        <option <?php if($d->id_reparto == $r->id) { ?> selected <?php } ?> value="{{$r->id}}">{{$r->descrizione}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Email <b style="color:red">*</b></label>
                                <input type="email" name="email" value="<?php echo $d->email ?>" class="form-control" placeholder="Email" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Password</label>
                                <input type="password" name="password" value="<?php echo $d->password ?>" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">PIN tablet produzione</label>
                                <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" name="pin" value="<?php echo $d->pin ?? '' ?>" class="form-control" placeholder="4 cifre" />
                                <small class="text-muted">Codice numerico a 4 cifre per accesso rapido sul tablet</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Costo Orario (&euro;/h)</label>
                                <input type="number" step="0.01" min="0" name="costo_orario" class="form-control" placeholder="0.00" value="<?php echo $d->costo_orario ?? 0 ?>" />
                                <small class="text-muted">Utilizzato per il calcolo dei costi manodopera nelle commesse</small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $d->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

