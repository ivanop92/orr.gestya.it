@include('admin.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestione Aziende</h4>

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

        <!-- Pulsante per aggiungere una nuova azienda -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modal_aggiungi">Aggiungi Azienda</button>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table datatable w-100">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ragione Sociale</th>
                    <th>Partita IVA</th>
                    <th>Comune</th>
                    <th>Indirizzo</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($aziende as $azienda)
                    <tr>
                        <td>{{ $azienda->id }}</td>
                        <td>{{ $azienda->ragione_sociale }}</td>
                        <td>{{ $azienda->partita_iva }}</td>
                        <td>{{ $azienda->comune }}</td>
                        <td>{{ $azienda->indirizzo }}</td>
                        <td>
                            <!-- Pulsante per aprire la modal di modifica -->
                            <button class="btn btn-sm btn-primary" onclick="openEditModalAzienda({{ $azienda->id }}, '{{ $azienda->titolo }}', '{{ $azienda->descrizione }}', '{{ $azienda->ragione_sociale }}', '{{ $azienda->partita_iva }}', '{{ $azienda->comune }}', '{{ $azienda->indirizzo }}')">Modifica</button>
                            <a href="#" class="btn btn-sm btn-danger" onclick="setAziendaIdToDelete({{ $azienda->id }})">Elimina</a>
                            <form method="post">
                                <input type="hidden" name="id_utente" value="{{$azienda->id_utente}}">
                                <button type="submit" name="effettua_login" value="Effettua Login" class="btn btn-success custom-toggle mt-1">
                                    <span class="icon-on"><i class="ri-login-box-line me-1"></i> Effettua Login</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
                </div>
            </div>
        </div>

        <!-- Modal per aggiungere una nuova azienda -->
        <div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 position-relative">
                    <div id="loading-overlay" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.5); z-index:10; align-items:center; justify-content:center;">
                        <div>
                            <div class="spinner-border text-light" role="status">
                                <span class="visually-hidden">Caricamento...</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-header bg-soft-info p-3">
                        <h5 class="modal-title" id="exampleModalLabel">Nuova Azienda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                    </div>

                    <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-lg-3">
                                    <div class="text-center">
                                        <div class="position-relative d-inline-block">
                                            <div class="avatar-lg p-1">
                                                <div class="avatar-title bg-light rounded-circle">
                                                    <img src="/placehold_immagine.png" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-10">
                                    <label class="form-label">Partita IVA <b style="color:red">*</b></label>
                                    <input type="text" id="piva" name="p_iva" class="form-control" placeholder="Partita IVA" required/>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <a id="carica_dati" class="form-control btn btn-success" onclick="carica_dati();">CARICA DATI</a>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Nazione</label>
                                        <input type="text" id="nazione" name="nazione" class="form-control" placeholder="Nazione" maxlength="2" />
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Regime Fiscale</label>
                                        <input type="text" id="regime_fiscale" name="regime_fiscale" class="form-control" placeholder="regime_fiscale" />
                                    </div>
                                </div>

                                <div class="col-md-10">
                                    <div>
                                        <label for="company_name-field" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                        <input  type="text" id="ragione_sociale" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" required />
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div>
                                        <label class="form-label">Dipendenti</label>
                                        <input type="text" id="dipendenti" name="dipendenti" class="form-control" placeholder="Dipendenti" />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Codice Ateco</label>
                                        <input type="text" id="ateco_codice" name="codice_ateco" class="form-control" placeholder="Codice Ateco" />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Descrizione Codice Ateco</label>
                                        <textarea rows="3" id="ateco_descrizione" name="descrizione_codice_ateco" class="form-control" placeholder="Descrizione Codice Ateco"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Regione <b style="color:red">*</b></label>
                                        <input type="text" id="regione" name="regione" class="form-control" placeholder="Provincia" required />
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div>
                                        <label class="form-label">Indirizzo <b style="color:red">*</b></label>
                                        <input type="text" id="indirizzo" name="indirizzo" class="form-control" placeholder="Indirizzo" required />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">CAP <b style="color:red">*</b></label>
                                        <input type="text" id="cap" name="cap" class="form-control" placeholder="Comune" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Comune <b style="color:red">*</b></label>
                                        <input type="text" id="comune" name="comune" class="form-control" placeholder="Comune" required />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Provincia <b style="color:red">*</b></label>
                                        <input type="text" id="provincia" name="provincia" class="form-control" placeholder="Provincia" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Codice SDI</label>
                                        <input type="text" id="sdi" name="codice_sdi" class="form-control" placeholder="Codice SDI" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">PEC</label>
                                        <input type="text" id="pec" name="pec" class="form-control" placeholder="pec" />
                                    </div>
                                </div>


                            </div>
                            <div class="row g-3 mt-4">
                                <div class="col-md-12 text-center">
                                    <h3>Utente Admin</h3>
                                </div>
                                <hr>
                            </div>
                            <div class="row g-3">

                                <div class="col-md-9 d-flex align-items-center">
                                    <input class="form-control" type="file" name="immagine-user" id="immagine-user" accept="image/png, image/gif, image/jpeg">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nome<b style="color:red">*</b></label>
                                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome" required/>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label for="company_name-field" class="form-label">Cognome <b style="color:red">*</b></label>
                                        <input  type="text" id="cognome" name="cognome" class="form-control" placeholder="Cognome" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Data di Nascita</label>
                                        <input type="date" id="data_nascita" name="data_nascita" class="form-control"/>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Luogo di Nascita</label>
                                        <input type="text" id="luogo_nascita" name="luogo_nascita" class="form-control" placeholder="Luogo di Nascita" />
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Email <b style="color:red">*</b></label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Password <b style="color:red">*</b></label>
                                        <input type="text" id="password" name="password" class="form-control" placeholder="Password" required />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Telefono</label>
                                        <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Telefono" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="formCheck6" name="abilitato" checked>
                                        <label class="form-check-label" for="formCheck6">
                                            Abilita Accesso
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                                <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Crea" >
                            </div>
                        </div>
                    </form>
                  </div>
            </div>
        </div>

        <!-- Modal per la modifica delle aziende -->
                <!-- Modal per la modifica delle aziende -->
                <div class="modal fade" id="editAziendaModal" tabindex="-1" aria-labelledby="editAziendaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <form method="POST">
                                <input type="hidden" name="id_azienda" id="edit_id_azienda">
                                <div class="modal-header bg-soft-info p-3">
                                    <h5 class="modal-title" id="editAziendaLabel">Modifica Azienda</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-10">
                                            <label class="form-label">Partita IVA <b style="color:red">*</b></label>
                                            <input type="text" id="edit_partita_iva" name="edit_partita_iva" class="form-control" required/>
                                        </div>

                                        <div class="col-md-10">
                                            <label class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                            <input type="text" id="edit_ragione_sociale" name="edit_ragione_sociale" class="form-control" required />
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">Indirizzo <b style="color:red">*</b></label>
                                            <input type="text" id="edit_indirizzo" name="edit_indirizzo" class="form-control" required />
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Comune <b style="color:red">*</b></label>
                                            <input type="text" id="edit_comune" name="edit_comune" class="form-control" required />
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Provincia <b style="color:red">*</b></label>
                                            <input type="text" id="edit_provincia" name="edit_provincia" class="form-control" required />
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">CAP <b style="color:red">*</b></label>
                                            <input type="text" id="edit_cap" name="edit_cap" class="form-control" required />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Codice Ateco</label>
                                            <input type="text" id="edit_ateco_codice" name="edit_ateco_codice" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Descrizione Codice Ateco</label>
                                            <textarea rows="3" id="edit_ateco_descrizione" name="edit_ateco_descrizione" class="form-control"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Codice SDI</label>
                                            <input type="text" id="edit_sdi" name="edit_sdi" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">PEC</label>
                                            <input type="text" id="edit_pec" name="edit_pec" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Dipendenti</label>
                                            <input type="text" id="edit_dipendenti" name="edit_dipendenti" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                                    <input type="submit" class="btn btn-primary" name="modifica" value="Salva Modifiche">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="deleteAziendaModal" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        @csrf
                        <input type="hidden" name="id_azienda" value="" id="id_azienda">
                        <div class="modal-body p-5 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                            <div class="mt-4 text-center">
                                <h4>Sei sicuro di eliminare questa Azienda?</h4>
                                <p class="text-muted fs-15 mb-4">Cancellando questa azienda verranno rimossi tutti i relativi dati</p>
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


        <!-- Modals Javascript -->
        <script>
            function carica_dati() {
                // Recupera il valore della partita IVA dal campo input
                let piva = $('#piva').val();

                const settings = {
                    "async": true,
                    "crossDomain": true,
                    "url": "https://company.openapi.com/IT-advanced/" + piva,
                    "method": "GET",
                    "headers": {
                        "Authorization": "Bearer 66cdb99c9c5ff0e89b0bec98"
                    }
                };

                // Effettua la richiesta API
                $.ajax(settings).done(function(response) {
                    if (response.data && response.data[0]) {
                        const azienda = response.data[0];
                        console.log(response.data);
                        // Mappa i dati restituiti dall'API nei campi del form
                        $('#ragione_sociale').val(azienda.companyName);
                        $('#indirizzo').val(azienda.address.registeredOffice.streetName);
                        $('#comune').val(azienda.address.registeredOffice.town);
                        $('#provincia').val(azienda.address.registeredOffice.province);
                        $('#cap').val(azienda.address.registeredOffice.zipCode);
                        $('#regione').val(azienda.address.registeredOffice.region.description);
                        $('#ateco_codice').val(azienda.atecoClassification.ateco.code);
                        $('#ateco_descrizione').val(azienda.atecoClassification.ateco.description);
                        $('#sdi').val(azienda.sdiCode);
                        $('#pec').val(azienda.pec);

                        // Gestione numero dipendenti e fatturato se disponibili
                        if (azienda.balanceSheets && azienda.balanceSheets.all) {
                            $('#dipendenti').val(azienda.balanceSheets.all[0].employees);
                        }
                    } else {
                        console.error("Dati azienda non disponibili");
                    }
                }).fail(function(error) {
                    console.error("Errore nella chiamata API: ", error);
                });
            }





            function setAziendaIdToDelete(id) {
                $('#deleteAziendaModal').modal('show');
                document.getElementById('id_azienda').value = id;
            }

            function openEditModalAzienda(id, titolo, descrizione, ragione_sociale, partita_iva, comune, indirizzo, provincia, cap, ateco_codice, ateco_descrizione, sdi, pec, dipendenti) {
                $('#editAziendaModal').modal('show');
                $('#edit_id_azienda').val(id);
                $('#edit_partita_iva').val(partita_iva);
                $('#edit_ragione_sociale').val(ragione_sociale);
                $('#edit_descrizione').val(descrizione);
                $('#edit_comune').val(comune);
                $('#edit_indirizzo').val(indirizzo);
                $('#edit_provincia').val(provincia);
                $('#edit_cap').val(cap);
                $('#edit_ateco_codice').val(ateco_codice);
                $('#edit_ateco_descrizione').val(ateco_descrizione);
                $('#edit_sdi').val(sdi);
                $('#edit_pec').val(pec);
                $('#edit_dipendenti').val(dipendenti);
            }

        </script>
    </div>
</div>

@include('admin.common.footer')
