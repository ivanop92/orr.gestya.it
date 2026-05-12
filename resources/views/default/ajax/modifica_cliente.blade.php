<?php foreach($clienti as $c){ ?>

<div class="modal fade" id="modal_modifica_<?php echo $c->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-8">
                            <label class="form-label">Partita IVA <b style="color:red">*</b></label>
                            <input type="text" id="piva_<?php echo $c->id ?>" name="piva" value="<?php echo $c->piva ?>" class="form-control" placeholder="P.IVA" required/>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <a id="carica_dati" class="form-control btn btn-success" onclick="carica_dati_modifica(<?php echo $c->id ?>);">CARICA DATI</a>
                        </div>

                        <div class="col-md-12">
                            <div>
                                <label for="company_name-field" class="form-label">Ragione Sociale </label>
                                <input type="text" id="ragione_sociale_<?php echo $c->id ?>" value="<?php echo $c->ragione_sociale ?>" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">CCIAA</label>
                                <input type="text" id="cciaa_<?php echo $c->id ?>"  value="<?php echo $c->cciaa ?>" name="cciaa" class="form-control" placeholder="CCIAA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">REA</label>
                                <input type="text" id="rea_<?php echo $c->id ?>" name="rea"  value="<?php echo $c->rea ?>" class="form-control" placeholder="REA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Fatturato</label>
                                <input type="text" id="fatturato_<?php echo $c->id ?>"  value="<?php echo $c->fatturato ?>" name="fatturato" class="form-control" placeholder="Fatturato" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Dipendenti</label>
                                <input type="text" id="dipendenti_<?php echo $c->id ?>"  value="<?php echo $c->dipendenti ?>" name="dipendenti" class="form-control" placeholder="Dipendenti" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Grandezza Azienda</label>
                                <select id="grandezza_azienda_<?php echo $c->id ?>" name="grandezza_azienda" class="form-control select2">
                                    <option value="0" <?php echo ($c->grandezza_azienda == 0)?'selected':'' ?>>MICRO</option>
                                    <option value="1" <?php echo ($c->grandezza_azienda == 1)?'selected':'' ?>>PICCOLA</option>
                                    <option value="2" <?php echo ($c->grandezza_azienda == 2)?'selected':'' ?>>MEDIA</option>
                                    <option value="3" <?php echo ($c->grandezza_azienda == 3)?'selected':'' ?>>GRANDE</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Ateco Codice</label>
                                <input type="text" id="ateco_codice_<?php echo $c->id ?>" name="ateco_codice" value="<?php echo $c->ateco_codice ?>" class="form-control" placeholder="Ateco Codice" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Ateco Descrizione</label>
                                <input type="text" id="ateco_descrizione_<?php echo $c->id ?>" name="ateco_descrizione" value="<?php echo $c->ateco_descrizione ?>" class="form-control" placeholder="Ateco Descrizione" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <label class="form-label">Indirizzo</label>
                                <input type="text" id="indirizzo_<?php echo $c->id ?>" name="indirizzo" value="<?php echo $c->indirizzo ?>" class="form-control" placeholder="Indirizzo" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">CAP</label>
                                <input type="text" id="cap_<?php echo $c->id ?>" name="cap" value="<?php echo $c->cap ?>"  class="form-control" placeholder="Comune" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Comune</label>
                                <input type="text" id="comune_<?php echo $c->id ?>" name="comune" value="<?php echo $c->comune ?>"  class="form-control" placeholder="Comune" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Provincia</label>
                                <input type="text" id="provincia_<?php echo $c->id ?>" name="provincia" value="<?php echo $c->provincia ?>"  class="form-control" placeholder="Provincia" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Regione</label>
                                <input type="text" id="regione_<?php echo $c->id ?>" name="regione" value="<?php echo $c->regione ?>"  class="form-control" placeholder="Provincia" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label for="company_name-field" class="form-label">Agente</label>
                                <select name="id_agente" class="form-control select2">
                                    <?php foreach($agenti as $a){ ?>
                                        <option value="<?php echo $a->id ?>" <?php echo ($c->id_agente == $a->id)?'selected':'' ?>><?php echo $a->nome.' '.$a->cognome ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label  class="form-label">Email</label>
                                <input type="email_<?php echo $c->id ?>" name="email" value="<?php echo $c->email ?>"  class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" value="<?php echo $c->telefono ?>"  class="form-control" placeholder="Telefono" />
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Fatture</label>
                                <input type="text" name="mail_recapito" value="<?php echo $c->mail_recapito ?>"  class="form-control" placeholder="Mail Fatture" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Mail Leads</label>
                                <input type="text" name="mail_leads" value="<?php echo $c->mail_leads ?>" class="form-control" placeholder="Mail Leads" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice Fiscale</label>
                                <input type="text" id="cf_<?php echo $c->id ?>" name="cf" value="<?php echo $c->cf ?>" class="form-control" placeholder="CF" />
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div>
                                <label class="form-label">Codice SDI</label>
                                <input type="text" id="sdi_<?php echo $c->id ?>" name="sdi" value="<?php echo $c->sdi ?>" class="form-control" placeholder="P.IVA" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <label class="form-label">PEC</label>
                                <input type="text" id="pec_<?php echo $c->id ?>" name="pec" value="<?php echo $c->pec ?>" class="form-control" placeholder="pec" />
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                        <input type="hidden" name="id" value="<?php echo $c->id ?>">
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    function carica_dati_modifica(id){

        piva = $('#piva_'+id).val();

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

            console.log(response);
            $('#ragione_sociale_'+id).val(response.data[0].companyName);
            $('#cf_'+id).val($('#piva_'+id).val());
            $('#cciaa_'+id).val(response.data[0].cciaa);
            $('#rea_'+id).val(response.data[0].reaCode);
            $('#indirizzo_'+id).val(response.data[0].address.registeredOffice.streetName);
            $('#cap_'+id).val(response.data[0].address.registeredOffice.zipCode);
            $('#comune_'+id).val(response.data[0].address.registeredOffice.town);
            $('#provincia_'+id).val(response.data[0].address.registeredOffice.province);
            $('#regione_'+id).val(response.data[0].address.registeredOffice.region.description);

            if(response.data[0].balanceSheets.all[2].turnover  !== null) {
                $('#fatturato_'+id).val(response.data[0].balanceSheets.all[2].turnover);
            }

            if(response.data[0].balanceSheets.all[1].turnover  !== null) {
                $('#fatturato_'+id).val(response.data[0].balanceSheets.all[1].turnover);
            }

            if(response.data[0].balanceSheets.all[0].turnover  !== null) {
                $('#fatturato_'+id).val(response.data[0].balanceSheets.all[0].turnover);
            }


            $('#dipendenti_'+id).val(response.data[0].balanceSheets.all[0].employees);

            if(parseInt($('#dipendenti_'+id).val()) > 250 || parseInt($('#fatturato_'+id).val()) > 50000000 ) $('#grandezza_azienda_'+id).val(3);
            if(parseInt($('#dipendenti_'+id).val()) < 250 && parseInt($('#fatturato_'+id).val()) < 50000000) $('#grandezza_azienda_'+id).val(2);
            if(parseInt($('#dipendenti_'+id).val()) < 50 && parseInt($('#fatturato_'+id).val()) < 10000000) $('#grandezza_azienda_'+id).val(1);
            if(parseInt($('#dipendenti_'+id).val()) < 10 && parseInt($('#fatturato_'+id).val()) < 2000000) $('#grandezza_azienda_'+id).val(0);

            $('#ateco_codice_'+id).val(response.data[0].atecoClassification.ateco.code);
            $('#ateco_descrizione_'+id).val(response.data[0].atecoClassification.ateco.description);

            $('#sdi_'+id).val(response.data[0].sdiCode);
            $('#pec_'+id).val(response.data[0].pec);
        });

    }

</script>

<?php } ?>
