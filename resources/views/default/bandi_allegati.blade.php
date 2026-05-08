@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Allegati Bandi</h4>

                    <div class="page-title-right">
                        <!--
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                            <li class="breadcrumb-item active">Contacts</li>
                        </ol>-->
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <button class="btn btn-info add-btn" onclick="aggiungi();">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Allegato
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Allegati Bandi</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Descrizione</th>
                                    <th>Formati Richiesti</th>
                                    <th>Valore Richiesto</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($bandi_allegati as $ba){ ?>
                                <tr>
                                    <td><?php echo $ba->descrizione ?></td>
                                    <?php $formati_array = explode(',',$ba->formati ) ?>
                                    <td>
                                        <?php if ($ba->formati != NULL || $ba->formati != ''){ ?>
                                            <?php foreach ($formati_array as $fa){ ?>
                                            <p class="btn btn-primary btn-sm m-0"><?php echo $fa ?></p>
                                            <?php } ?>
                                        <?php }else{ ?>
                                        <p style="text-align: left; font-weight: bold">-</p>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center">
                                            <?php if ($ba->valore_si_no > 0){ ?>
                                                <p href="#" class="btn btn-sm btn-success" style="text-align: left;">Si</p>
                                            <?php }else{ ?>
                                                <p href="#" class="btn btn-sm btn-danger" style="text-align: left;">No</p>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex">
                                            <a onclick="modifica(<?php echo $ba->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo Allegato ?')">
                                                <input type="hidden" name="id" value="<?php echo $ba->id ?>">
                                                <input type="hidden" name="elimina" value="<?php echo $ba->id ?>">
                                                <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->

        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>


<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <input placeholder="Descrizione..." name="descrizione" class="form-control" required >
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Formati<b style="color:red">*</b></label>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".pdf"><span>PDF</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".docx"><span>DOCX</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".doc"><span>DOC</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".xls"><span>XLS</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".png"><span>PNG</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".jpeg"><span>JPEG</span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="formati[]" value=".txt"><span>TXT</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="col-md-12">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: left; margin-bottom: 15px">
                                    <input style="width: 20px; height: 20px; margin-right: 7px; " type="checkbox" name="valore_si_no"><span>Valore Richiesto<b style="color:red">*</b></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="ajax_loader"></div>

@include('default.common.footer')

<script type="text/javascript">

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        jQuery.ajax({
            url: "<?php echo URL::asset('admin/modifica_bandi_allegati') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_bandi_allegati_'+id).modal('show');
            }
        });
    }

</script>