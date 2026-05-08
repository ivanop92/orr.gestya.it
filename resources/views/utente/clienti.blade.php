@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Clienti</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Gestya</a></li>
                            <li class="breadcrumb-item active">Clienti</li>
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
                                <h5 class="card-title mb-0">Elenco Clienti</h5>
                            </div>

                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <button class="btn btn-primary add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Cliente
                                    </button>

                                    <button class="btn btn-soft-success" onclick="importa_clienti();">
                                        <i class="ri-upload-cloud-2-line me-1"></i> Importa
                                    </button>
                                    <button class="btn btn-soft-info" data-bs-toggle="modal" data-bs-target="#modal_import_winfatt">
                                        <i class="ri-download-2-line me-1"></i>Import Winfatt
                                    </button>
                                    <button class="btn btn-soft-warning">
                                        <i class="ri-download-cloud-2-line me-1"></i> Esporta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="clienti-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
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
                            @foreach($clienti as $c)
                                <tr>
                                    <td>
                                        {{ $c->ragione_sociale }}<br>
                                        <small class="text-muted">{{ $c->cd_cf }}</small>
                                    </td>
                                    <td>
                                        {{ $c->comune }}
                                        @if($c->provincia) <small class="text-muted">({{ $c->provincia }})</small> @endif
                                    </td>
                                    <td>{{ $c->piva }}</td>
                                    <td>{{ $c->telefono }}</td>
                                    <td>{{ $c->email }}</td>
                                    <td>{{ $c->sdi }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="/utente/dettaglio_cliente/{{ $c->id }}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> Dettaglio</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="modifica({{ $c->id }}); return false;"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifica</a></li>
                                                <li>
                                                    <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questo cliente?')">
                                                        <input type="hidden" name="id" value="{{ $c->id }}">
                                                        <button type="submit" name="elimina" value="1" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Elimina
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </div>
    <!-- container-fluid -->
</div>
<!-- Modal Import Winfatt -->
<div class="modal fade" id="modal_import_winfatt" tabindex="-1" aria-labelledby="importWinfattLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="importWinfattLabel">
                    <i class="ri-download-2-line me-2"></i>Import Clienti da Winfatt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading"><i class="mdi mdi-information-outline me-2"></i>Istruzioni</h6>
                    <hr>
                    <p class="mb-2"><strong>1.</strong> Esporta i clienti dal tuo software Winfatt in formato TXT</p>
                    <p class="mb-2"><strong>2.</strong> Copia tutto il contenuto del file TXT</p>
                    <p class="mb-2"><strong>3.</strong> Incolla il contenuto nell'area di testo sottostante</p>
                    <p class="mb-0"><strong>Nota:</strong> I clienti esistenti verranno aggiornati, quelli nuovi verranno creati</p>
                </div>

                <form id="form_import_winfatt" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <label for="file_winfatt" class="form-label">
                            <strong>Seleziona File TXT Winfatt</strong>
                        </label>
                        <input
                                type="file"
                                class="form-control"
                                id="file_winfatt"
                                name="file_winfatt"
                                accept=".txt"
                                required
                        />
                        <small class="form-text text-muted">
                            Seleziona il file TXT esportato da Winfatt contenente i dati dei clienti
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-info" onclick="importaWinfatt()">
                        <i class="ri-download-2-line me-1"></i>Importa Clienti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Cliente -->
<div class="modal fade" id="modal_aggiungi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Nuovo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#info_generali" role="tab">
                                        Informazioni Generali
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#info_fiscali" role="tab">
                                        Dati Fiscali
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#contatti" role="tab">
                                        Contatti
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <!-- Tab Informazioni Generali -->
                            <div class="tab-pane active" id="info_generali" role="tabpanel">
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
                                        <input type="text" id="piva" name="piva" class="form-control piva-input" placeholder="P.IVA" required/>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <a id="carica_dati" class="form-control btn btn-success" onclick="carica_dati();">
                                            <i class="ri-refresh-line me-1"></i> CARICA DATI
                                        </a>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="ragione_sociale" class="form-label">Ragione Sociale <b style="color:red">*</b></label>
                                        <input type="text" id="ragione_sociale" name="ragione_sociale" class="form-control" placeholder="Ragione Sociale" required />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="cciaa" class="form-label">CCIAA</label>
                                        <input type="text" id="cciaa" name="cciaa" class="form-control" placeholder="CCIAA" />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="rea" class="form-label">REA</label>
                                        <input type="text" id="rea" name="rea" class="form-control" placeholder="REA" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Fatturato</label>
                                        <input type="text" id="fatturato" name="fatturato" class="form-control" placeholder="Fatturato" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Dipendenti</label>
                                        <input type="text" id="dipendenti" name="dipendenti" class="form-control" placeholder="Dipendenti" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Grandezza Azienda</label>
                                        <select id="grandezza_azienda" name="grandezza_azienda" class="form-select">
                                            <option value="0">MICRO</option>
                                            <option value="1">PICCOLA</option>
                                            <option value="2">MEDIA</option>
                                            <option value="3">GRANDE</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="id_agente" class="form-label">Agente</label>
                                        <select name="id_agente" class="form-select">
                                            <option value="">Seleziona agente...</option>
                                            @foreach($agenti as $a)
                                                <option value="{{ $a->id }}" {{ $utente->id_tipologia == 1 && $utente->id == $a->id ? 'selected' : '' }}>{{ $a->nome }} {{ $a->cognome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Dati Fiscali -->
                            <div class="tab-pane" id="info_fiscali" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Ateco Codice</label>
                                        <input type="text" id="ateco_codice" name="ateco_codice" class="form-control" placeholder="Ateco Codice" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Esigibilità IVA</label>
                                        <select id="esigibilita_iva" name="esigibilita_iva" class="form-select" required>
                                            <option value="I">Immediata</option>
                                            <option value="D">Differita</option>
                                            <option value="S">Split Payment</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Ateco Descrizione</label>
                                        <input type="text" id="ateco_descrizione" name="ateco_descrizione" class="form-control" placeholder="Ateco Descrizione" />
                                    </div>

                                    <div class="col-md-12">
                                        <hr>
                                        <h6 class="mb-2"><i class="ri-file-text-line me-1"></i>Dichiarazione d'Intento</h6>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="esportatore_abituale" name="esportatore_abituale" value="1">
                                            <label class="form-check-label" for="esportatore_abituale">Esportatore abituale</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Numero protocollo</label>
                                        <input type="text" id="dich_intento_protocollo" name="dich_intento_protocollo" class="form-control" placeholder="Es. 08060971890123456-000001">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Data dichiarazione</label>
                                        <input type="date" id="dich_intento_data" name="dich_intento_data" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Validità da</label>
                                        <input type="date" id="dich_intento_validita_da" name="dich_intento_validita_da" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Validità a</label>
                                        <input type="date" id="dich_intento_validita_a" name="dich_intento_validita_a" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Importo plafond (€)</label>
                                        <input type="number" id="dich_intento_importo" name="dich_intento_importo" class="form-control" step="0.01" placeholder="0.00">
                                    </div>

                                    <div class="col-md-12"><hr></div>

                                    <div class="col-md-12">
                                        <label class="form-label">Indirizzo</label>
                                        <input type="text" id="indirizzo" name="indirizzo" class="form-control" placeholder="Indirizzo" />
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">CAP</label>
                                        <input type="text" id="cap" name="cap" class="form-control" placeholder="CAP" />
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Comune</label>
                                        <input type="text" id="comune" name="comune" class="form-control" placeholder="Comune" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Provincia</label>
                                        <input type="text" id="provincia" name="provincia" class="form-control" placeholder="Provincia" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Regione</label>
                                        <input type="text" id="regione" name="regione" class="form-control" placeholder="Regione" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Codice Fiscale</label>
                                        <input type="text" id="cf" name="cf" class="form-control" placeholder="CF" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Codice SDI</label>
                                        <input type="text" id="sdi" name="sdi" class="form-control" placeholder="Codice SDI" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Contatti -->
                            <div class="tab-pane" id="contatti" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Telefono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Telefono" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Mail Fatture</label>
                                        <input type="email" name="mail_recapito" class="form-control" placeholder="Mail Fatture" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Mail Lead</label>
                                        <input type="email" name="mail_leads" class="form-control" placeholder="Mail Lead" />
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">PEC</label>
                                        <input type="email" id="pec" name="pec" class="form-control" placeholder="PEC" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Referente</label>
                                        <input type="text" name="referente" class="form-control" placeholder="Referente" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Telefono Referente</label>
                                        <input type="text" name="telefono_referente" class="form-control" placeholder="Telefono Referente" />
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

<!-- Modal Importa Clienti -->
<div class="modal fade" id="modal_importa_clienti" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success p-3">
                <h5 class="modal-title" id="exampleModalLabel">Importa Clienti da Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post" action="{{ url('utente/import_clienti_excel') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <h5>Istruzioni per l'importazione:</h5>
                                <p class="mb-1">Carica un file Excel (.xlsx/.xls) con le intestazioni nella prima riga. Colonne supportate:</p>
                                <ul class="mb-1 small">
                                    <li><strong>Denominazione</strong> — Ragione sociale</li>
                                    <li><strong>Partita Iva</strong> o <strong>Partita IVA</strong> — P.IVA</li>
                                    <li><strong>Città</strong> o <strong>Comune</strong> — Città</li>
                                    <li><strong>Prov.</strong> o <strong>Provincia</strong> — Provincia</li>
                                    <li><strong>Cod. destinatario</strong> o <strong>Codice SDI</strong> — Codice SDI</li>
                                    <li><strong>Indirizzo</strong>, <strong>Tel.</strong>, <strong>e-mail</strong></li>
                                    <li>Opzionali: CAP, PEC, Codice Fiscale, Referente, CCIAA, REA</li>
                                </ul>
                                <p class="mb-0">Campo obbligatorio: almeno <strong>Denominazione</strong> o <strong>Partita Iva</strong></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">File Excel</label>
                            <input class="form-control" type="file" name="file_excel" accept=".xlsx,.xls" required>
                            <small class="text-muted">Seleziona il file Excel contenente i dati dei clienti</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-success" id="import-btn">
                            <i class="ri-upload-cloud-2-line me-1"></i> Importa Clienti
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="piva-error" class="alert alert-danger" style="display: none; margin-top: 10px;"></div>


<div id="ajax_loader"></div>

@include('utente.common.footer')



<script type="text/javascript">

    /*inizio import winfatt*/
    function importaWinfatt() {
        const fileInput = document.getElementById('file_winfatt');

        if (!fileInput.files.length) {
            alert('Seleziona un file TXT');
            return;
        }

        // Crea FormData per l'upload del file
        const formData = new FormData();
        formData.append('file_winfatt', fileInput.files[0]);
        formData.append('importa_winfatt', '1');

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
            const formToken = document.querySelector('#form_import_winfatt input[name="_token"]');
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

        fetch('/winfatt/import_clienti', {
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
                    const modal = document.getElementById('modal_import_winfatt');
                    if (modal) {
                        const closeBtn = modal.querySelector('.btn-close');
                        if (closeBtn) {
                            closeBtn.click();
                        }
                    }

                    // Resetta il form
                    document.getElementById('file_winfatt').value = '';

                    // Ricarica la pagina per vedere i nuovi clienti
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

    // Funzione alternativa che non usa il CSRF token (se il tuo server non lo richiede)
    function importaWinfattSenzaCSRF() {
        const fileInput = document.getElementById('file_winfatt');

        if (!fileInput.files.length) {
            alert('Seleziona un file TXT');
            return;
        }

        // Crea FormData per l'upload del file
        const formData = new FormData();
        formData.append('file_winfatt', fileInput.files[0]);
        formData.append('importa_winfatt', '1');

        // Disabilita il pulsante e mostra loading
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-2-line"></i> Importazione in corso...';

        fetch('/winfatt/import_clienti', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    alert('Successo!\n' + data.message);
                    document.getElementById('modal_import_winfatt').querySelector('.btn-close').click();
                    document.getElementById('file_winfatt').value = '';
                    location.reload();
                } else {
                    alert('Errore!\n' + data.message);
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                console.error('Errore:', error);
                alert('Si è verificato un errore durante l\'importazione');
            });
    }

    /*Fine import Winfatt*/
    function checkPartitaIva(pivaValue, idCliente = null) {
        if (!pivaValue || pivaValue.length < 11) {
            return;
        }

        $.ajax({
            url: '{{ url("utente/check_partita_iva") }}',
            type: 'POST',
            data: {
                piva: pivaValue,
                id_cliente: idCliente,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.exists) {
                    alert('ATTENZIONE: La Partita IVA ' + pivaValue + ' è già presente per il cliente: ' + response.cliente);
                    // Puoi anche impedire l'invio del form
                    $('.btn-submit').prop('disabled', true);
                    $('#piva-error').text('Partita IVA già esistente!').show();
                } else {
                    $('.btn-submit').prop('disabled', false);
                    $('#piva-error').hide();
                }
            },
            error: function() {
                console.log('Errore nel controllo della partita IVA');
            }
        });
    }

    // Aggiungi questo nel modal di aggiunta cliente
    $(document).ready(function() {
        // Per il modal di aggiunta
        $('#modal_aggiungi input[name="piva"]').on('blur', function() {
            checkPartitaIva($(this).val());
        });

        // Per i modal di modifica (dinamici)
        $(document).on('blur', 'input[name="piva"]', function() {
            var idCliente = $(this).closest('form').find('input[name="id"]').val();
            checkPartitaIva($(this).val(), idCliente);
        });

        // Reset quando si apre un nuovo modal
        $('.modal').on('show.bs.modal', function() {
            $('.btn-submit').prop('disabled', false);
            $('#piva-error').hide();
        });
    });


    $(document).ready(function() {
        // Inizializzazione della DataTable
        $('#clienti-datatable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            }
        });

        // Auto-hide della notifica dopo 5 secondi
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    function importa_clienti() {
        $('#modal_importa_clienti').modal('show');
    }

    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function modifica(id){
        $.ajax({
            url: "{{ URL::asset('utente/ajax/modifica_cliente') }}/" + id,
            type: 'GET',
            success: function(result){
                $('#ajax_loader').html(result);
                $('#modal_modifica_' + id).modal('show');
            }
        });
    }

    function carica_dati(){
        var piva = $('#piva').val();

        if(!piva) {
            alert('Inserire una Partita IVA valida');
            return;
        }

        // Mostra un loader
        $('#carica_dati').html('<i class="spinner-border spinner-border-sm"></i> CARICAMENTO...');
        $('#carica_dati').prop('disabled', true);

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
            $('#ragione_sociale').val(response.data[0].companyName);
            $('#cf').val($('#piva').val());
            $('#cciaa').val(response.data[0].cciaa);
            $('#rea').val(response.data[0].reaCode);
            $('#indirizzo').val(response.data[0].address.registeredOffice.streetName);
            $('#cap').val(response.data[0].address.registeredOffice.zipCode);
            $('#comune').val(response.data[0].address.registeredOffice.town);
            $('#provincia').val(response.data[0].address.registeredOffice.province);
            $('#regione').val(response.data[0].address.registeredOffice.region.description);

            if(response.data[0].balanceSheets.all[2].turnover !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[2].turnover);
            }

            if(response.data[0].balanceSheets.all[1].turnover !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[1].turnover);
            }

            if(response.data[0].balanceSheets.all[0].turnover !== null) {
                $('#fatturato').val(response.data[0].balanceSheets.all[0].turnover);
            }

            $('#dipendenti').val(response.data[0].balanceSheets.all[0].employees);

            if(parseInt($('#dipendenti').val()) > 250 || parseInt($('#fatturato').val()) > 50000000) {
                $('#grandezza_azienda').val(3);
            } else if(parseInt($('#dipendenti').val()) < 250 && parseInt($('#fatturato').val()) < 50000000) {
                $('#grandezza_azienda').val(2);
            } else if(parseInt($('#dipendenti').val()) < 50 && parseInt($('#fatturato').val()) < 10000000) {
                $('#grandezza_azienda').val(1);
            } else if(parseInt($('#dipendenti').val()) < 10 && parseInt($('#fatturato').val()) < 2000000) {
                $('#grandezza_azienda').val(0);
            }

            $('#ateco_codice').val(response.data[0].atecoClassification.ateco.code);
            $('#ateco_descrizione').val(response.data[0].atecoClassification.ateco.description);

            $('#sdi').val(response.data[0].sdiCode);
            $('#pec').val(response.data[0].pec);

            // Ripristina il pulsante
            $('#carica_dati').html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            $('#carica_dati').prop('disabled', false);

            // Mostra un messaggio di successo
            toastr.success('Dati caricati con successo!');
        }).fail(function(error) {
            console.error(error);
            // Ripristina il pulsante
            $('#carica_dati').html('<i class="ri-refresh-line me-1"></i> CARICA DATI');
            $('#carica_dati').prop('disabled', false);

            // Mostra un messaggio di errore
            toastr.error('Errore durante il caricamento dei dati. Controlla la Partita IVA.');
        });
    }
</script>