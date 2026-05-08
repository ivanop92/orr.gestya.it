@include('utente.common.header')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Fasi di Lavorazione</h4>

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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <button class="btn btn-info add-btn" onclick="aggiungi();">
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Fase
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table mt-4">
                            <thead>
                            <tr>
                                <th>Descrizione</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($fasi as $fase)
                                    <tr>
                                        <td>{{ $fase->descrizione }}</td>
                                        <td>
                                            <button style="float:left;margin-left:5px;" type="button" class="btn btn-sm btn-primary" onclick="modifica({{ $fase->id }}, '{{ $fase->descrizione }}')">Modifica</button>

                                            <form style="float:left;margin-left:5px;" method="post" onsubmit="return confirm('Vuoi Eliminare questa fase ?')">
                                                <input type="hidden" name="id" value="<?php echo $fase->id ?>">
                                                <input style="margin-left:5px;" type="submit" name="elimina" value="Elimina" class="btn btn-sm btn-danger">
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Modal for delete confirmation -->
    <div class="modal fade" id="deleteFase" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_fase" value="" id="id_fase">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Sei sicuro di eliminare questa Fase di Lavorazione?</h4>
                            <p class="text-muted fs-15 mb-4">Cancellando questa fase verranno cancellati tutti i suoi dati a database.</p>
                            <div class="hstack gap-2 justify-content-center remove">
                                <button type="button" class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Chiudi</button>
                                <button type="submit" class="btn btn-danger" name="elimina" value="1">Sì, Elimina</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@include('utente.common.footer')

<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Fase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Nome <b style="color:red">*</b></label>
                                <input type="text" name="descrizione" class="form-control" placeholder="Descrizione" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal for editing fase -->
<div class="modal fade" id="modifica" tabindex="-1" aria-labelledby="editFaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="id" value="" id="edit_id_fase">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaseLabel">Modifica Fase di Lavorazione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_descrizione" class="form-label">Descrizione</label>
                        <input type="text" class="form-control" id="edit_descrizione" name="descrizione" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary" name="modifica_fase" value="1">Modifica</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>


    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }


    // Funzione per aprire la modale di modifica
    function modifica(idFase, descrizione) {
        $('#modifica').modal('show');
        document.getElementById('edit_id_fase').value = idFase;
        document.getElementById('edit_descrizione').value = descrizione;
    }

    function setFaseIdToDelete(idFase) {
        $('#deleteFase').modal('show');
        document.getElementById('id_fase').value = idFase;
    }
</script>
