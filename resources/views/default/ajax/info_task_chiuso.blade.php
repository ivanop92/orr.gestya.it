<?php foreach ($task as $t){ ?>
<div class="modal modal-lg fade" id="modal_info_task_chiuso_<?php echo $t->id ?>" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Chiusura Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 fs-5">
                        Data di Chiusura del Task
                    </div>
                    <?php
                    $chiusura = strtotime($t->data_chiusura);
                    ?>
                    <div class="col-md-6 fs-5 mb-2">
                        {{date('d/m/Y', $chiusura)}}
                    </div>
                    <hr>
                    <div class="col-md-12 text-center mt-2 fs-5">
                        Note
                    </div>
                    <div class="col-md-6 fs-6">
                        {{$t->note_chiusura}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>