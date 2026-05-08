<?php foreach ($bandi_allegati as $bl){ ?>
<div class="modal fade" id="modal_modifica_bandi_allegati_<?php echo $bl->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <div>
                                <label class="form-label">Descrizione<b style="color:red">*</b></label>
                                <input value="{{$bl->descrizione}}" name="descrizione" class="form-control" required >
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Formati<b style="color:red">*</b></label>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.pdf') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".pdf"><span>PDF</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.docx') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".docx"><span>DOCX</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.doc') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".doc"><span>DOC</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.xls') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".xls"><span>XLS</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.png') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".png"><span>PNG</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.jpeg') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".jpeg"><span>JPEG</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if(strpos($bl->formati, '.txt') !== false){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".txt"><span>TXT</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="col-md-12">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input <?php if($bl->valore_si_no > 0){ ?> checked <?php } ?> style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="valore_si_no"><span>Valore Richiesto<b style="color:red">*</b></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $bl->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>