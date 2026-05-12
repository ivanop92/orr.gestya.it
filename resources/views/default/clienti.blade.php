@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Clienti</h4>

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
                                    <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Cliente
                                </button>
                            </div>
                            <!--
                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addmembers"><i class="ri-filter-2-line me-1 align-bottom"></i> Filtri</button>
                                    <button class="btn btn-soft-success">Import</button>
                                    <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-info"><i class="ri-more-2-fill"></i></button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                        <li><a class="dropdown-item" href="#">All</a></li>
                                        <li><a class="dropdown-item" href="#">Last Week</a></li>
                                        <li><a class="dropdown-item" href="#">Last Month</a></li>
                                        <li><a class="dropdown-item" href="#">Last Year</a></li>
                                    </ul>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Clienti</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Immagine</th>
                                    <th>Ragione Sociale</th>
                                    <th>Categoria</th>
                                    <th>CCIAA</th>
                                    <th>Regione</th>
                                    <th>Grandezza</th>
                                    <th style="width:100px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($clienti as $c){ ?>

                                    <?php
                                        $grandezza = '';
                                        if($c->grandezza_azienda == 0) $grandezza = 'MICRO';
                                        if($c->grandezza_azienda == 1) $grandezza = 'PICCOLA';
                                        if($c->grandezza_azienda == 2) $grandezza = 'MEDIA';
                                        if($c->grandezza_azienda == 3) $grandezza = 'GRANDE';
                                    ?>

                                <tr>
                                    <td><img style="max-width: 60px; background: white;" src="<?php echo URL::asset($c->immagine) ?>"></td>
                                    <td><?php echo $c->ragione_sociale ?><br>P.IVA : <?php echo $c->piva ?><br>Codice Cliente: <?php echo $c->cd_cf ?> </td>
                                    <td>Sezione : <?php echo $c->sezione ?><br>Ateco: <?php echo $c->ateco_codice ?><br><?php echo $c->ateco_descrizione ?></td>
                                    <td><?php echo $c->cciaa ?></td>
                                    <td><?php echo $c->regione ?></td>
                                    <td><?php echo $grandezza ?></td>
                                    <td>
                                        <div style="display: flex">
                                            <a href="/admin/dettaglio_utente/<?php echo $c->id ?>" class="btn btn-sm btn-success"><i class="ri-information-line"></i></a>
                                            <a style="margin-left:5px;" onclick="modifica(<?php echo $c->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo cliente ?')">
                                                <input type="hidden" name="id" value="<?php echo $c->id ?>">
                                                <input type="hidden" name="elimina" value="<?php echo $c->id ?>">
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
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Cliente</h5>
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
                                            <img src="/default/assets/images/users/user-dummy-img.jpg" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

<!--
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Nome <b style="color:red">*</b></label>
                                <input type="text" name="nome" class="form-control" placeholder="Nome" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Cognome <b style="color:red">*</b></label>
                                <input type="text" name="cognome" class="form-control" placeholder="Cognome" required />
                            </div>
                        </div>


-->

                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-8">
                            <label class="form-label">Partita IVA <b style="color:red">*</b></label>
                            <input type="text" id="piva" name="piva" class="form-control" placeholder="P.IVA" required/>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <a id="carica_dati" class="form-control btn btn-success" onclick="carica_dati();">CARICA DATI</a>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Ragione Sociale </label>
                                <input type="text" id="ragione_sociale" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">CCIAA</label>
                                <input type="text" id="cciaa" name="cciaa" class="form-control" placeholder="CCIAA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">REA</label>
                                <input type="text" id="rea" name="rea" class="form-control" placeholder="REA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Fatturato</label>
                                <input type="text" id="fatturato" name="fatturato" class="form-control" placeholder="Fatturato" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Dipendenti</label>
                                <input type="text" id="dipendenti" name="dipendenti" class="form-control" placeholder="Dipendenti" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Grandezza Azienda</label>
                                <select id="grandezza_azienda" name="grandezza_azienda" data-choices data-choices-search-false>
                                    <option value="0">MICRO</option>
                                    <option value="1">PICCOLA</option>
                                    <option value="2">MEDIA</option>
                                    <option value="3">GRANDE</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Ateco Codice</label>
                                <input type="text" id="ateco_codice" name="ateco_codice" class="form-control" placeholder="Ateco Codice" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Ateco Descrizione</label>
                                <input type="text" id="ateco_descrizione" name="ateco_descrizione" class="form-control" placeholder="Ateco Descrizione" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Indirizzo</label>
                                <input type="text" id="indirizzo" name="indirizzo" class="form-control" placeholder="Indirizzo" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">CAP</label>
                                <input type="text" id="cap" name="cap" class="form-control" placeholder="Comune" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Comune</label>
                                <input type="text" id="comune" name="comune" class="form-control" placeholder="Comune" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Provincia</label>
                                <input type="text" id="provincia" name="provincia" class="form-control" placeholder="Provincia" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Regione</label>
                                <input type="text" id="regione" name="regione" class="form-control" placeholder="Provincia" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Agente</label>
                                <select name="id_agente" class="form-control select2">
                                    <?php foreach($agenti as $a){ ?>
                                        <option value="<?php echo $a->id ?>"><?php echo $a->nome.' '.$a->cognome ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" class="form-control" placeholder="Telefono" />
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Fatture</label>
                                <input type="text" name="mail_recapito" class="form-control" placeholder="Mail Fatture" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Leads</label>
                                <input type="text" name="mail_leads" class="form-control" placeholder="Mail Leads" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice Fiscale</label>
                                <input type="text" id="cf" name="cf" class="form-control" placeholder="CF" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice SDI</label>
                                <input type="text" id="sdi" name="sdi" class="form-control" placeholder="P.IVA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">PEC</label>
                                <input type="text" id="pec" name="pec" class="form-control" placeholder="pec" />
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
        $.ajax({
            url: "<?php echo URL::asset('admin/ajax/modifica_cliente') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_'+id).modal('show');
            }
        });


    }

    function carica_dati(){

        piva = $('#piva').val();

        const settings = {
            "async": true,
            "crossDomain": true,
            "url": "https://company.openapi.com/IT-advanced/"+piva,
            "method": "GET",
            "headers": {
                "Authorization": "Bearer 68dc0564cc8194517e0ca866"
            }
        };

        $.ajax(settings).done(function (response) {

            $('#ragione_sociale').val(response.data[0].companyName);
            $('#cf').val($('#piva').val());
            $('#cciaa').val(response.data[0].cciaa);
            $('#rea').val(response.data[0].reaCode);
            $('#indirizzo').val(response.data[0].address.registeredOffice.streetName);
            $('#cap').val(response.data[0].address.registeredOffice.zipCode);
            $('#comune').val(response.data[0].address.registeredOffice.town);
            $('#provincia').val(response.data[0].address.registeredOffice.province);
            $('#regione').val(response.data[0].address.registeredOffice.region.description);


            if(response.data[0].balanceSheets.all[2].turnover  !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[2].turnover);
            }

            if(response.data[0].balanceSheets.all[1].turnover  !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[1].turnover);
            }

            if(response.data[0].balanceSheets.all[0].turnover  !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[0].turnover);
            }

            $('#dipendenti').val(response.data[0].balanceSheets.all[0].employees);

            if(parseInt($('#dipendenti').val()) > 250 || parseInt($('#fatturato').val()) > 50000000 ) $('#grandezza_azienda').val(3);
            if(parseInt($('#dipendenti').val()) < 250 && parseInt($('#fatturato').val()) < 50000000) $('#grandezza_azienda').val(2);
            if(parseInt($('#dipendenti').val()) < 50 && parseInt($('#fatturato').val()) < 10000000) $('#grandezza_azienda').val(1);
            if(parseInt($('#dipendenti').val()) < 10 && parseInt($('#fatturato').val()) < 2000000) $('#grandezza_azienda').val(0);


            $('#ateco_codice').val(response.data[0].atecoClassification.ateco.code);
            $('#ateco_descrizione').val(response.data[0].atecoClassification.ateco.description);

            $('#sdi').val(response.data[0].sdiCode);
            $('#pec').val(response.data[0].pec);
        });

    }

</script>

<style>

    div.dataTables_wrapper div.dataTables_filter label {
        font-weight: normal;
        white-space: nowrap;
        text-align: left;
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: .5em;
        display: inline-block;
        width: 89%;
    }
</style>