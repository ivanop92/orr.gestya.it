@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Fornitori</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Gestya</a></li>
                            <li class="breadcrumb-item active">Fornitori</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successo!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errore!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Elenco Fornitori</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Fornitore
                                    </button>
                                    <button class="btn btn-soft-success" onclick="importa_fornitori();">
                                        <i class="ri-upload-cloud-2-line me-1"></i> Importa
                                    </button>
                                    <button class="btn btn-soft-info" data-bs-toggle="modal" data-bs-target="#modal_import_winfatt_fornitori">
                                        <i class="ri-download-2-line me-1"></i>Import Fornitori Winfatt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="fornitori-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                            <tr>
                                <th>Ragione Sociale</th>
                                <th>Città / Prov.</th>
                                <th>P.IVA</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Cod. SDI</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($fornitori as $f){ ?>
                            <tr>
                                <td>
                                    <?php echo $f->ragione_sociale ?><br>
                                    <small class="text-muted"><?php echo $f->cd_cf ?></small>
                                </td>
                                <td>
                                    <?php echo $f->comune ?>
                                    <?php if($f->provincia) echo '<small class="text-muted">('.$f->provincia.')</small>' ?>
                                </td>
                                <td><?php echo $f->piva ?></td>
                                <td><?php echo $f->telefono ?></td>
                                <td><?php echo $f->email ?></td>
                                <td><?php echo $f->sdi ?></td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="/utente/dettaglio_fornitore/<?php echo $f->id ?>"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> Dettaglio</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="modifica(<?php echo $f->id ?>); return false;"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica</a></li>
                                            <li>
                                                <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questo fornitore?')">
                                                    <input type="hidden" name="id" value="<?php echo $f->id ?>">
                                                    <button type="submit" name="elimina" value="1" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Elimina
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
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
    <!-- container-fluid -->
</div>

<!-- Modal Aggiungi Fornitore -->
<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Nuovo Fornitore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#info_generali_fornitore" role="tab">
                                        Informazioni Generali
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#info_fiscali_fornitore" role="tab">
                                        Dati Fiscali
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#contatti_fornitore" role="tab">
                                        Contatti
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <!-- Tab Informazioni Generali -->
                            <div class="tab-pane active" id="info_generali_fornitore" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-lg-12">
                                        <div class="text-center">
                                            <div class="position-relative d-inline-block">
                                                <div class="avatar-lg p-1">
                                                    <div class="avatar-title bg-light rounded-circle">
                                                        <img src="/default/assets/images/users/user-dummy-img.jpg" id="fornitore-img" class="avatar-md rounded-circle object-cover" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Partita IVA <b style="color:red">*</b></label>
                                        <input type="text" id="piva_fornitore" name="piva" class="form-control piva-input" placeholder="P.IVA" required/>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <a id="carica_dati_fornitore" class="form-control btn btn-success" onclick="carica_dati_fornitore();">
                                            <i class="ri-refresh-line me-1"></i> CARICA DATI
                                        </a>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="ragione_sociale_fornitore" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                        <input type="text" id="ragione_sociale_fornitore" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" required />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Nome</label>
                                        <input type="text" id="nome_fornitore" name="nome" class="form-control" placeholder="Nome" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Cognome</label>
                                        <input type="text" id="cognome_fornitore" name="cognome" class="form-control" placeholder="Cognome" />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="cciaa_fornitore" class="form-label">CCIAA</label>
                                        <input type="text" id="cciaa_fornitore" name="cciaa" class="form-control" placeholder="CCIAA" />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="rea_fornitore" class="form-label">REA</label>
                                        <input type="text" id="rea_fornitore" name="rea" class="form-control" placeholder="REA" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Dati Fiscali -->
                            <div class="tab-pane" id="info_fiscali_fornitore" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Indirizzo</label>
                                        <input type="text" id="indirizzo_fornitore" name="indirizzo" class="form-control" placeholder="Indirizzo" />
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">CAP</label>
                                        <input type="text" id="cap_fornitore" name="cap" class="form-control" placeholder="CAP" />
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Comune</label>
                                        <input type="text" id="comune_fornitore" name="comune" class="form-control" placeholder="Comune" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Provincia</label>
                                        <input type="text" id="provincia_fornitore" name="provincia" class="form-control" placeholder="Provincia" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Regione</label>
                                        <input type="text" id="regione_fornitore" name="regione" class="form-control" placeholder="Regione" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Codice Fiscale</label>
                                        <input type="text" id="cf_fornitore" name="cf" class="form-control" placeholder="CF" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Codice SDI</label>
                                        <input type="text" id="sdi_fornitore" name="sdi" class="form-control" placeholder="Codice SDI" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Contatti -->
                            <div class="tab-pane" id="contatti_fornitore" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Telefono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Telefono" />
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">PEC</label>
                                        <input type="email" id="pec_fornitore" name="pec" class="form-control" placeholder="PEC" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi">
                            <i class="ri-save-line me-1"></i> Salva
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Fornitori Winfatt -->
<div class="modal fade" id="modal_import_winfatt_fornitori" tabindex="-1" aria-labelledby="importWinfattFornitoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="importWinfattFornitoriLabel">
                    <i class="ri-download-2-line me-2"></i>Import Fornitori da Winfatt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading"><i class="mdi mdi-information-outline me-2"></i>Istruzioni</h6>
                    <hr>
                    <p class="mb-2"><strong>1.</strong> Esporta i fornitori dal tuo software Winfatt in formato TXT</p>
                    <p class="mb-2"><strong>2.</strong> Seleziona il file TXT dei fornitori</p>
                    <p class="mb-2"><strong>3.</strong> Clicca su "Importa Fornitori"</p>
                    <p class="mb-0"><strong>Nota:</strong> I fornitori esistenti verranno aggiornati, quelli nuovi verranno creati</p>
                </div>

                <form id="form_import_winfatt_fornitori" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <label for="file_winfatt_fornitori" class="form-label">
                            <strong>Seleziona File TXT Winfatt Fornitori</strong>
                        </label>
                        <input
                                type="file"
                                class="form-control"
                                id="file_winfatt_fornitori"
                                name="file_winfatt_fornitori"
                                accept=".txt"
                                required
                        />
                        <small class="form-text text-muted">
                            Seleziona il file TXT esportato da Winfatt contenente i dati dei fornitori
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-info" onclick="importaWinfattFornitori()">
                        <i class="ri-download-2-line me-1"></i>Importa Fornitori
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importa Fornitori -->
<div class="modal fade" id="modal_importa_fornitori" tabindex="-1" aria-labelledby="importaFornitoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="importaFornitoriModalLabel">Importa Fornitori da Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('utente/import_fornitori_excel') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <p>Seleziona il file Excel contenente l'elenco dei fornitori da importare.</p>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xlsx,.xls" required>
                                <label class="input-group-text" for="file_excel">Scegli file</label>
                            </div>
                            <small class="text-muted">Formati supportati: .xlsx, .xls</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">Importa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modali di modifica (generati dinamicamente dal PHP) -->
<?php foreach($fornitori as $f){ ?>
<div class="modal fade" id="modal_modifica_<?php echo $f->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Fornitore <?php echo $f->nome.' '.$f->cognome ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg p-1">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img style="width:50%;margin:0 auto;display: block;" src="<?php echo URL::asset($f->immagine) ?>"  />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Partita IVA</label>
                            <input type="text" id="piva_mod_<?php echo $f->id ?>" name="piva" class="form-control" value="<?php echo $f->piva ?>" placeholder="P.IVA" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <a class="form-control btn btn-success" id="carica_dati_mod_<?php echo $f->id ?>" onclick="carica_dati_modifica(<?php echo $f->id ?>);">
                                <i class="ri-refresh-line me-1"></i> CARICA DATI
                            </a>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                            <input type="text" id="ragione_sociale_mod_<?php echo $f->id ?>" name="ragione_sociale" class="form-control" value="<?php echo $f->ragione_sociale ?>" placeholder="Ragione Sociale" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo $f->nome ?>" placeholder="Nome" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cognome</label>
                            <input type="text" name="cognome" class="form-control" value="<?php echo $f->cognome ?>" placeholder="Cognome" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CCIAA</label>
                            <input type="text" id="cciaa_mod_<?php echo $f->id ?>" name="cciaa" class="form-control" value="<?php echo $f->cciaa ?? '' ?>" placeholder="CCIAA" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">REA</label>
                            <input type="text" id="rea_mod_<?php echo $f->id ?>" name="rea" class="form-control" value="<?php echo $f->rea ?? '' ?>" placeholder="REA" />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" id="indirizzo_mod_<?php echo $f->id ?>" name="indirizzo" class="form-control" value="<?php echo $f->indirizzo ?>" placeholder="Indirizzo" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">CAP</label>
                            <input type="text" id="cap_mod_<?php echo $f->id ?>" name="cap" class="form-control" value="<?php echo $f->cap ?? '' ?>" placeholder="CAP" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Comune</label>
                            <input type="text" id="comune_mod_<?php echo $f->id ?>" name="comune" class="form-control" value="<?php echo $f->comune ?>" placeholder="Comune" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Provincia</label>
                            <input type="text" id="provincia_mod_<?php echo $f->id ?>" name="provincia" class="form-control" value="<?php echo $f->provincia ?>" placeholder="Provincia" />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Regione</label>
                            <input type="text" id="regione_mod_<?php echo $f->id ?>" name="regione" class="form-control" value="<?php echo $f->regione ?? '' ?>" placeholder="Regione" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Codice Fiscale</label>
                            <input type="text" id="cf_mod_<?php echo $f->id ?>" name="cf" class="form-control" value="<?php echo $f->cf ?? '' ?>" placeholder="CF" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Codice SDI</label>
                            <input type="text" id="sdi_mod_<?php echo $f->id ?>" name="sdi" class="form-control" value="<?php echo $f->sdi ?? '' ?>" placeholder="Codice SDI" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $f->email ?>" placeholder="Email" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" class="form-control" value="<?php echo $f->telefono ?>" placeholder="Telefono" />
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">PEC</label>
                            <input type="email" id="pec_mod_<?php echo $f->id ?>" name="pec" class="form-control" value="<?php echo $f->pec ?? '' ?>" placeholder="PEC" />
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $f->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<div id="piva-error-fornitore" class="alert alert-danger" style="display: none; margin-top: 10px;"></div>
<div id="ajax_loader"></div>

@include('utente.common.footer')

<script type="text/javascript">
    // Caricamento automatico dati fornitore tramite P.IVA
    function carica_dati_fornitore(){
        var piva = $('#piva_fornitore').val();

        if(!piva) {
            alert('Inserire una Partita IVA valida');
            return;
        }

        // Mostra un loader
        $('#carica_dati_fornitore').html('<i class="spinner-border spinner-border-sm"></i> CARICAMENTO...');
        $('#carica_dati_fornitore').prop('disabled', true);

        const settings = {
            "async": true,
            "crossDomain": true,
            "url": "https://company.openapi.com/IT-advanced/" + piva,
            "method": "GET",
            "headers": {
                "Authorization": "Bearer 68dc0564cc8194517e0ca866"
            }
        };

        $.ajax(settings).done(function (response) {
            $('#ragione_sociale_fornitore').val(response.data[0].companyName);
            $('#cf_fornitore').val($('#piva_fornitore').val());
            $('#cciaa_fornitore').val(response.data[0].cciaa);
            $('#rea_fornitore').val(response.data[0].reaCode);
            $('#indirizzo_fornitore').val(response.data[0].address.registeredOffice.streetName);
            $('#cap_fornitore').val(response.data[0].address.registeredOffice.zipCode);
            $('#comune_fornitore').val(response.data[0].address.registeredOffice.town);
            $('#provincia_fornitore').val(response.data[0].address.registeredOffice.province);
            $('#regione_fornitore').val(response.data[0].address.registeredOffice.region.description);

            $('#sdi_fornitore').val(response.data[0].sdiCode);
            $('#pec_fornitore').val(response.data[0].pec);

            // Ripristina il pulsante
            $('#carica_dati_fornitore').html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            $('#carica_dati_fornitore').prop('disabled', false);

            // Mostra un messaggio di successo
            if(typeof toastr !== 'undefined') {
                toastr.success('Dati caricati con successo!');
            } else {
                alert('Dati caricati con successo!');
            }
        }).fail(function(error) {
            console.error(error);
            // Ripristina il pulsante
            $('#carica_dati_fornitore').html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            $('#carica_dati_fornitore').prop('disabled', false);

            // Mostra un messaggio di errore
            if(typeof toastr !== 'undefined') {
                toastr.error('Errore durante il caricamento dei dati. Controlla la Partita IVA.');
            } else {
                alert('Errore durante il caricamento dei dati. Controlla la Partita IVA.');
            }
        });
    }

    // Caricamento dati da P.IVA nel modale modifica
    function carica_dati_modifica(id){
        var piva = $('#piva_mod_'+id).val();

        if(!piva) {
            alert('Inserire una Partita IVA valida');
            return;
        }

        var btn = $('#carica_dati_mod_'+id);
        btn.html('<i class="spinner-border spinner-border-sm"></i> CARICAMENTO...');
        btn.prop('disabled', true);

        const settings = {
            "async": true,
            "crossDomain": true,
            "url": "https://company.openapi.com/IT-advanced/" + piva,
            "method": "GET",
            "headers": {
                "Authorization": "Bearer 68dc0564cc8194517e0ca866"
            }
        };

        $.ajax(settings).done(function (response) {
            $('#ragione_sociale_mod_'+id).val(response.data[0].companyName);
            $('#cf_mod_'+id).val(piva);
            $('#cciaa_mod_'+id).val(response.data[0].cciaa);
            $('#rea_mod_'+id).val(response.data[0].reaCode);
            $('#indirizzo_mod_'+id).val(response.data[0].address.registeredOffice.streetName);
            $('#cap_mod_'+id).val(response.data[0].address.registeredOffice.zipCode);
            $('#comune_mod_'+id).val(response.data[0].address.registeredOffice.town);
            $('#provincia_mod_'+id).val(response.data[0].address.registeredOffice.province);
            $('#regione_mod_'+id).val(response.data[0].address.registeredOffice.region.description);
            $('#sdi_mod_'+id).val(response.data[0].sdiCode);
            $('#pec_mod_'+id).val(response.data[0].pec);

            btn.html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            btn.prop('disabled', false);

            if(typeof toastr !== 'undefined') {
                toastr.success('Dati caricati con successo!');
            } else {
                alert('Dati caricati con successo!');
            }
        }).fail(function(error) {
            console.error(error);
            btn.html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            btn.prop('disabled', false);

            if(typeof toastr !== 'undefined') {
                toastr.error('Errore durante il caricamento dei dati. Controlla la Partita IVA.');
            } else {
                alert('Errore durante il caricamento dei dati. Controlla la Partita IVA.');
            }
        });
    }

    // Funzione per controllo P.IVA duplicata
    function checkPartitaIvaFornitore(pivaValue, idFornitore = null) {
        if (!pivaValue || pivaValue.length < 11) {
            return;
        }

        $.ajax({
            url: '{{ url("utente/check_partita_iva_fornitore") }}',
            type: 'POST',
            data: {
                piva: pivaValue,
                id_fornitore: idFornitore,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.exists) {
                    alert('ATTENZIONE: La Partita IVA ' + pivaValue + ' è già presente per il fornitore: ' + response.fornitore);
                    $('.btn-submit').prop('disabled', true);
                    $('#piva-error-fornitore').text('Partita IVA già esistente!').show();
                } else {
                    $('.btn-submit').prop('disabled', false);
                    $('#piva-error-fornitore').hide();
                }
            },
            error: function() {
                console.log('Errore nel controllo della partita IVA');
            }
        });
    }

    /*import fornitori winfatt*/
    function importaWinfattFornitori() {
        const fileInput = document.getElementById('file_winfatt_fornitori');

        if (!fileInput.files.length) {
            alert('Seleziona un file TXT');
            return;
        }

        // Crea FormData per l'upload del file
        const formData = new FormData();
        formData.append('file_winfatt_fornitori', fileInput.files[0]);
        formData.append('importa_winfatt_fornitori', '1');

        // Prova a prendere il token CSRF da diversi posti
        let csrfToken = null;

        // Prova dal meta tag
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            csrfToken = metaToken.getAttribute('content');
        }

        // Se non c'è il meta tag, prova da un input hidden
        if (!csrfToken) {
            const hiddenToken = document.querySelector('input[name="_token"]');
            if (hiddenToken) {
                csrfToken = hiddenToken.value;
            }
        }

        // Se non c'è neanche quello, prova dal form
        if (!csrfToken) {
            const formToken = document.querySelector('#form_import_winfatt_fornitori input[name="_token"]');
            if (formToken) {
                csrfToken = formToken.value;
            }
        }

        // Aggiungi il token se lo trovi
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        // Disabilita il pulsante e mostra loading
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-2-line"></i> Importazione in corso...';

        fetch('/winfatt/import_fornitori', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    alert('Successo!\n' + data.message);

                    // Chiudi il modal
                    const modal = document.getElementById('modal_import_winfatt_fornitori');
                    if (modal) {
                        const closeBtn = modal.querySelector('.btn-close');
                        if (closeBtn) {
                            closeBtn.click();
                        }
                    }

                    // Resetta il form
                    document.getElementById('file_winfatt_fornitori').value = '';

                    // Ricarica la pagina per vedere i nuovi fornitori
                    location.reload();
                } else {
                    alert('Errore!\n' + data.message);
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                console.error('Errore:', error);
                alert('Si è verificato un errore durante l\'importazione:\n' + error.message);
            });
    }
    /*fine import fornitori winfatt*/

    $(document).ready(function() {
        // Inizializzazione della DataTable
        $('#fornitori-datatable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            }
        });

        // Auto-hide della notifica dopo 5 secondi
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Per il modal di aggiunta fornitore
        $('#modal_aggiungi input[name="piva"]').on('blur', function() {
            checkPartitaIvaFornitore($(this).val());
        });

        // Per i modal di modifica (dinamici)
        $(document).on('blur', 'input[name="piva"]', function() {
            var idFornitore = $(this).closest('form').find('input[name="id"]').val();
            checkPartitaIvaFornitore($(this).val(), idFornitore);
        });

        // Reset quando si apre un nuovo modal
        $('.modal').on('show.bs.modal', function() {
            $('.btn-submit').prop('disabled', false);
            $('#piva-error-fornitore').hide();
        });
    });

    function importa_fornitori() {
        $('#modal_importa_fornitori').modal('show');
    }

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }
</script>