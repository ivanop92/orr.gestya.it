@include('utente.common.header')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <?php if($modalita == 'crea'){ ?>
                    <h4 class="mb-sm-0">Crea Documento di tipo: {{ $cd_do }}</h4>
                    <?php } else { ?>
                    <h4 class="mb-sm-0">Modifica Documento : {{ $dotes->cd_do.' '.$dotes->numero_doc.' del '.date('d/m/Y',strtotime($dotes->data_doc)) }}</h4>
                    <?php } ?>

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
        <form enctype="multipart/form-data" method="post">


            @if($modalita != 'crea')
                <div class="row mb-3">
                    <div class="col-12">
                            <?php if($dotes->cd_do == 'FTI'){ ?>
                        <a href="<?php echo URL::asset('utente/visualizza_xml_da_file/'.$dotes->id) ?>" target="_blank" class="btn btn-secondary">   <i class="ri-file-pdf-line"></i> Visualizza Fattura</a>
                        <?php } else { ?>


                        <button type="button" onclick="duplicaDocumento()" class="btn btn-info">
                            <i class="ri-file-copy-line"></i> Duplica
                        </button>

                        <a href="<?php echo URL::asset('stampa/documento/'.$dotes->id) ?>" target="_blank" class="btn btn-secondary">   <i class="ri-file-pdf-line"></i> Apri PDF</a>

                        <button type="button" onclick="inviaEmail()" class="btn btn-info">
                            <i class="ri-mail-send-line"></i> Invia per e-mail
                        </button>


                        <button type="button" class="btn btn-primary" onclick="evadiDocumento()">
                            <i class="ri-arrow-right-line"></i> Evadi Documento
                        </button>

                        <?php } ?>


                    </div>
                </div>

                    <?php if($dotes->cd_do == 'FTV' || $dotes->cd_do == 'NC'){ ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-<?php echo ($dotes->stato == 2)?'danger':'info' ?>">
                            <h5>Stato fattura elettronica:
                                <span id="stato_fattura">
                            
                                    @if($dotes->stato == 1)
                                        Inviata <i class="ri-check-double-line"></i>
                                    @elseif($dotes->stato == 2)
                                        Scartata <i class="ri-check-double-line"></i><br>
                                        <small><?php echo $dotes->ns_descrizione ?></small>
                                    @else
                                        Da inviare
                                    @endif
                                                        </span>
                            </h5>
                            @if($dotes->stato == 1)
                                <div class="alert alert-warning">
                                    <i class="ri-alert-line"></i> Questo documento è stato inviato elettronicamente e non può essere modificato.
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Disabilita tutti gli input e select
                                        document.querySelectorAll('input, select, textarea').forEach(element => {
                                            element.disabled = true;
                                        });

                                        // Nascondi i pulsanti di modifica
                                        document.querySelectorAll('.btn-success, .btn-remove, .plus, .minus').forEach(button => {
                                            button.style.display = 'none';
                                        });

                                        // Nascondi il pulsante di submit
                                        document.querySelector('input[type="submit"]').style.display = 'none';

                                        // Nascondi il pulsante "Aggiungi Riga"
                                        document.querySelector('#add-item').style.display = 'none';

                                        // Nascondi il pulsante "Aggiungi scadenza di pagamento"
                                        document.querySelector('#addPaymentRow').style.display = 'none';
                                    });
                                </script>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" onclick="window.open('/utente/visualizza_xml/<?php echo $dotes->id ?>')" class="btn btn-info">
                            <i class="ri-file-list-3-line"></i> Visualizza Fattura Elettronica
                        </button>

                        <button type="button" onclick="window.open('/utente/scarica_xml/<?php echo $dotes->id ?>')" class="btn btn-warning">
                            <i class="ri-file-code-line"></i> Scarica XML
                        </button>

                            <?php if($dotes->stato == 0 || $dotes->stato == 2){ ?>

                        <button type="button" onclick="inviaFE()" class="btn btn-success">
                            <i class="ri-check-line"></i> Invia Fattura Elettronica
                        </button>
                        <?php } ?>

                    </div>
                </div>

                <?php } ?>
            @endif


            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#testata" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">Testata</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#righe" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                        <span class="d-none d-sm-block">Righe</span>
                                    </a>
                                </li>
                                @if($modalita != 'crea')
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#evasioni" role="tab">
                                            <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                            <span class="d-none d-sm-block">Evasioni</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3">
                                <!-- Tab Testata -->
                                <div class="tab-pane active" id="testata" role="tabpanel">

                                    <div class="card-body border-bottom border-bottom-dashed p-4">
                                        <div class="row">


                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    @if(in_array($cd_do, ['RDO', 'ORDF']))
                                                        <label class="form-label">Fornitore</label>
                                                        <select name="id_cliente" id="id_cliente" class="form-control" onchange="compilaCampi(this.value)" required data-choices data-choices-search-true>
                                                            <option value="">Seleziona Fornitore</option>
                                                            @foreach($fornitori as $fornitore)
                                                                <option value="{{ $fornitore->id }}" <?php if(isset($dotes) && $dotes->id_cliente == $fornitore->id) echo 'selected'; ?>>{{ $fornitore->ragione_sociale }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <label class="form-label">Cliente</label>
                                                        <select name="id_cliente" id="id_cliente" class="form-control" onchange="compilaCampi(this.value)" required data-choices data-choices-search-true>
                                                            <option value="">Seleziona Cliente</option>
                                                            @foreach($clienti as $cliente)
                                                                <option value="{{ $cliente->id }}" <?php if(isset($dotes) && $dotes->id_cliente == $cliente->id) echo 'selected'; ?>>{{ $cliente->ragione_sociale }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Commessa</label>
                                                    <select name="id_commessa" id="id_commessa" class="form-control">
                                                        <option value="">Seleziona Commessa (opzionale)</option>
                                                        @php
                                                            $commesse = DB::table('commesse')
                                                                ->where('id_azienda', $utente->id_azienda)
                                                                ->whereIn('stato', ['aperta', 'in_corso'])
                                                                ->orderBy('codice_commessa', 'asc')
                                                                ->get();
                                                        @endphp
                                                        @foreach($commesse as $commessa)
                                                            <option value="{{ $commessa->id }}" <?php if(isset($dotes) && $dotes->id_commessa == $commessa->id) echo 'selected'; ?>>{{ $commessa->codice_commessa }} - {{ $commessa->descrizione }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Indirizzo di consegna (opzionale)</label>
                                                    @php
                                                        $sedi_per_cliente = [];
                                                        $sedi_all = DB::table('sedi')
                                                            ->where('id_azienda', $utente->id_azienda)
                                                            ->where('tipo', 'cliente')
                                                            ->orderBy('nome')
                                                            ->get();
                                                        foreach($sedi_all as $s) {
                                                            $sedi_per_cliente[$s->id_riferimento][] = $s;
                                                        }
                                                        $id_cliente_corrente = isset($dotes) ? $dotes->id_cliente : null;
                                                        $id_sede_corrente = isset($dotes) ? ($dotes->id_sede_consegna ?? null) : null;
                                                    @endphp
                                                    <select name="id_sede_consegna" id="id_sede_consegna" class="form-control">
                                                        <option value="">-- Stessa sede di fatturazione --</option>
                                                        @if($id_cliente_corrente && isset($sedi_per_cliente[$id_cliente_corrente]))
                                                            @foreach($sedi_per_cliente[$id_cliente_corrente] as $sede)
                                                                <option value="{{ $sede->id }}" {{ $id_sede_corrente == $sede->id ? 'selected' : '' }}>
                                                                    {{ $sede->nome }} — {{ $sede->indirizzo }} {{ $sede->cap }} {{ $sede->comune }} ({{ $sede->provincia }})
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <small class="text-muted">Se lasci vuoto verrà usato l'indirizzo di fatturazione del cliente.</small>
                                                </div>
                                            </div>
                                            <script>
                                                window.sediClientiMap = @json($sedi_per_cliente);
                                                function aggiornaSediConsegna(idCliente) {
                                                    var sel = document.getElementById('id_sede_consegna');
                                                    if(!sel) return;
                                                    sel.innerHTML = '<option value="">-- Stessa sede di fatturazione --</option>';
                                                    if(idCliente && window.sediClientiMap[idCliente]) {
                                                        window.sediClientiMap[idCliente].forEach(function(s) {
                                                            var opt = document.createElement('option');
                                                            opt.value = s.id;
                                                            opt.textContent = s.nome + ' — ' + (s.indirizzo || '') + ' ' + (s.cap || '') + ' ' + (s.comune || '') + (s.provincia ? ' ('+s.provincia+')' : '');
                                                            sel.appendChild(opt);
                                                        });
                                                    }
                                                }
                                            </script>


                                            <div class="col-lg-4">

                                                <div>
                                                    <label for="billingName" class="text-muted text-uppercase fw-semibold">Numero e Data Documento</label>
                                                </div>
                                                <div class="mb-2">


                                                    <div class="row">

                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control bg-light border-0" name="numero_doc" value="<?php echo ($modalita == 'crea')?$numero_doc:$dotes->numero_doc ?>" placeholder="Numero Documento">
                                                        </div>

                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control bg-light border-0" id="date-field" name="data_doc" data-provider="flatpickr" data-time="true" placeholder="Data Documento" value="<?php echo ($modalita == 'crea')?date('Y-m-d'):$dotes->data_doc ?>">
                                                        </div>
                                                    </div>

                                                </div>


                                                <div>
                                                    <label for="billingName" class="text-muted text-uppercase fw-semibold">Oggetto (Visibile)</label>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" id="oggetto_visibile" name="oggetto_visibile" value="<?php echo ($modalita == 'crea')?'':$dotes->oggetto_visibile ?>" placeholder="Oggetto che Apparirà nel documento" />

                                                </div>
                                                <div>
                                                    <label for="billingName" class="text-muted text-uppercase fw-semibold">Oggetto (Interno)</label>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" id="oggetto_interno" name="oggetto_interno" value="<?php echo ($modalita == 'crea')?'':$dotes->oggetto_interno ?>" placeholder="Oggetto che Apparirà solo a te" />
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Data Inizio Lavori</label>
                                                    <input type="text" class="form-control bg-light border-0" id="date-field" name="data_inizio" data-provider="flatpickr" data-time="true" placeholder="Data Documento" value="<?php echo ($modalita == 'crea')?'':$dotes->data_inizio ?>">

                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Data Consegna</label>
                                                    <input type="text" class="form-control bg-light border-0" id="date-field" name="data_consegna" data-provider="flatpickr" data-time="true" placeholder="Data Documento" value="<?php echo ($modalita == 'crea')?'':$dotes->data_consegna ?>">

                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">CIG</label>
                                                    <input type="text" class="form-control bg-light border-0" name="cig" value="<?php echo ($modalita == 'crea')?'':($dotes->cig ?? '') ?>" placeholder="Codice CIG" />
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">CUP</label>
                                                    <input type="text" class="form-control bg-light border-0" name="cup" value="<?php echo ($modalita == 'crea')?'':($dotes->cup ?? '') ?>" placeholder="Codice CUP" />
                                                </div>
                                            </div>

                                            <div class="col-lg-4">

                                                <div>
                                                    <label for="billingName" class="text-muted text-uppercase fw-semibold">Indirizzo di fatturazione</label>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" id="ragioneSocialeFatturazione" name="ragione_sociale_fatturazione" value="<?php echo ($modalita == 'crea')?'':$dotes->ragione_sociale_fatturazione ?>" placeholder="Ragione Sociale" required />
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" name="comune_fatturazione" id="comune_fatturazione" value="<?php echo ($modalita == 'crea')?'':$dotes->comune_fatturazione ?>" placeholder="Comune" required />
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" name="provincia_fatturazione" id="provincia_fatturazione" value="<?php echo ($modalita == 'crea')?'':$dotes->provincia_fatturazione ?>" placeholder="Provincia" required />
                                                </div>
                                                <div class="mb-2">
                                                    <textarea class="form-control bg-light border-0" id="indirizzoFatturazione" name="indirizzo_fatturazione" rows="3" placeholder="Indirizzo" required><?php echo ($modalita == 'crea')?'':$dotes->indirizzo_fatturazione ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <input type="text" class="form-control bg-light border-0" id="partitaIvaFatturazione" name="partita_iva_fatturazione" value="<?php echo ($modalita == 'crea')?'':$dotes->partita_iva_fatturazione ?>" placeholder="Partita Iva" />
                                                </div>
                                                <div class="mb-3">
                                                    <input type="text" class="form-control bg-light border-0" id="codice_fiscale_fatturazione" name="codice_fiscale_fatturazione" value="<?php echo ($modalita == 'crea')?'':$dotes->codice_fiscale_fatturazione ?>" placeholder="Codice Fiscale" />
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" name="sdi" id="sdi" value="<?php echo ($modalita == 'crea')?'':$dotes->sdi ?>" placeholder="SDI" />
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" name="pec" id="pec" value="<?php echo ($modalita == 'crea')?'':$dotes->pec ?>" placeholder="PEC" />
                                                </div>
                                            </div>

                                            <div class="col-lg-4">

                                                <div>

                                                    <div>
                                                        <label for="billingName" class="text-muted text-uppercase fw-semibold">Indirizzo di Consegna</label>
                                                    </div>
                                                    <div class="mb-2">
                                                        <textarea class="form-control bg-light border-0" id="companyAddress" name="indirizzo" rows="3" placeholder="Indirizzo" required><?php echo ($modalita == 'crea')?'':$dotes->indirizzo ?></textarea>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="text" class="form-control bg-light border-0" id="comune" name="comune" value="<?php echo ($modalita == 'crea')?'':$dotes->comune ?>" placeholder="Comune" required />
                                                    </div>


                                                    <div>
                                                        <input type="text" class="form-control bg-light border-0" id="cap" name="cap" minlength="5" maxlength="6" value="<?php echo ($modalita == 'crea')?'':$dotes->cap ?>" placeholder="Codice Postale" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="id_agente" class="form-label">Agente</label>
                                                            <select class="form-select" id="id_agente" name="id_agente">
                                                                <option value="">Seleziona Agente</option>
                                                                @foreach ($agenti as $agente)
                                                                    <option value="{{ $agente->id }}" {{ isset($dotes) && $dotes->id_agente == $agente->id ? 'selected' : '' }}>
                                                                        {{ $agente->nome }} {{ $agente->cognome }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                    </div>

                                    <!-- Modalità e Condizioni di Pagamento (tutti i documenti) -->
                                    <div class="card-body border-bottom border-bottom-dashed p-4">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title mb-0">Pagamento</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Modalità Pagamento</label>
                                                                    <select name="modalita_pagamento" id="modalita_pagamento" class="form-control">
                                                                        <option value="MP01" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP01' ? 'selected' : '' }}>MP01 - Contanti</option>
                                                                        <option value="MP02" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP02' ? 'selected' : '' }}>MP02 - Assegno</option>
                                                                        <option value="MP03" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP03' ? 'selected' : '' }}>MP03 - Assegno circolare</option>
                                                                        <option value="MP05" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP05' ? 'selected' : '' }}>MP05 - Bonifico</option>
                                                                        <option value="MP08" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP08' ? 'selected' : '' }}>MP08 - Carta di pagamento</option>
                                                                        <option value="MP19" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP19' ? 'selected' : '' }}>MP19 - SEPA Direct Debit</option>
                                                                        <option value="MP21" {{ (isset($dotes) ? $dotes->modalita_pagamento ?? '' : $azienda->modalita_pagamento) == 'MP21' ? 'selected' : '' }}>MP21 - RIBA</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Condizioni Pagamento</label>
                                                                    <select name="condizioni_pagamento" class="form-control">
                                                                        <option value="TP01" {{ (isset($dotes) ? $dotes->condizioni_pagamento ?? '' : $azienda->condizioni_pagamento) == 'TP01' ? 'selected' : '' }}>TP01 - Pagamento a rate</option>
                                                                        <option value="TP02" {{ (isset($dotes) ? $dotes->condizioni_pagamento ?? '' : $azienda->condizioni_pagamento) == 'TP02' ? 'selected' : '' }}>TP02 - Pagamento completo</option>
                                                                        <option value="TP03" {{ (isset($dotes) ? $dotes->condizioni_pagamento ?? '' : $azienda->condizioni_pagamento) == 'TP03' ? 'selected' : '' }}>TP03 - Anticipo</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">IBAN</label>
                                                                    <input type="text" name="iban" class="form-control" value="{{ isset($dotes) ? $dotes->iban ?? $azienda->iban : $azienda->iban }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($documento->cd_do == 'FTV' || $documento->cd_do == 'NC')
                                        <div class="card-body border-bottom border-bottom-dashed p-4">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title mb-0">Dati Fatturazione Elettronica</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Tipologia Documento</label>
                                                                        <select name="tipologia_documento" class="form-control">
                                                                            @if($documento->cd_do == 'FTV')
                                                                                <option value="TD01">TD01 - Fattura</option>
                                                                                <option value="TD02">TD02 - Acconto/Anticipo su fattura</option>
                                                                                <option value="TD03">TD03 - Acconto/Anticipo su parcella</option>
                                                                                <option value="TD06">TD06 - Parcella</option>
                                                                            @elseif($documento->cd_do == 'NC')
                                                                                <option value="TD04">TD04 - Nota di Credito</option>
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Istituto Finanziario</label>
                                                                        <input type="text" name="istituto_finanziario" class="form-control" value="{{ isset($dotes) ? $dotes->istituto_finanziario ?? $azienda->istituto_finanziario : $azienda->istituto_finanziario }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Esigibilità IVA</label>
                                                                        <select id="esigibilita_iva" name="esigibilita_iva" class="form-control">
                                                                            <option value="I" {{ isset($dotes) && $dotes->esigibilita_iva == 'I' ? 'selected' : '' }}>Immediata</option>
                                                                            <option value="D" {{ isset($dotes) && $dotes->esigibilita_iva == 'D' ? 'selected' : '' }}>Differita</option>
                                                                            <option value="S" {{ isset($dotes) && $dotes->esigibilita_iva == 'S' ? 'selected' : '' }}>Split Payment</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                <!-- Tab Righe -->
                                <div class="tab-pane" id="righe" role="tabpanel">
                                    <div class="card-body" style="padding: 0px;">

                                        <div class="table-responsive">
                                            <div class="row">
                                                @if ($scanBarcodeEnabled)
                                                    <div class="row mb-3">
                                                        <div class="col-lg-4">
                                                            <label for="barcodeInput" class="form-label">Scan Barcode</label>
                                                            <input type="text" class="form-control" id="barcodeInput" disabled placeholder="Scan a barcode" />
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <table class="invoice-table table table-borderless table-nowrap mb-0">
                                                <thead class="align-middle">
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 50px;">#</th>
                                                    <th scope="col" style="width: 400px;">Dettagli Prodotto</th>
                                                    <th scope="col" style="width: 120px;">
                                                        <div class="d-flex currency-select input-light align-items-center">
                                                            Prezzo &euro;
                                                        </div>
                                                    </th>
                                                    <th scope="col" style="width: 80px;">Sc. %</th>
                                                    <th scope="col">Iva</th>
                                                    <th>Lotto</th>
                                                    <th>Qta.</th>
                                                    <th>UM</th>
                                                    <th scope="col" >Totale</th>
                                                    <th scope="col" >Imposta</th>
                                                    <th scope="col" class="text-end" style="width: 105px;"></th>
                                                </tr>
                                                </thead>
                                                <tbody id="newlink">
                                                <?php if($modalita == 'crea'){ ?>

                                                <tr id="1" class="product">
                                                    <th scope="row" class="product-id">1</th>
                                                    <td class="text-start">
                                                        <div class="mb-2">
                                                            <!-- Select per scegliere da elenco predefinito o selezionare "Altro" -->
                                                            <select style="width: 100%" class="form-control bg-light border-0 select2" id="productSelect-1" name="products[0][id_articolo]" onchange="updateProductNameField(1,this)" style="width: 100%;">
                                                                <option value="0">{{ in_array($cd_do, ['RDO', 'ORDF']) ? '-- Campo libero / Descrizione manuale --' : 'Seleziona i tuoi prodotti' }}</option>
                                                                @if(!in_array($cd_do, ['RDO', 'ORDF']))
                                                                <optgroup label="Prodotti Finiti">
                                                                    <?php foreach($prodotti_finiti as $prodotto): ?>
                                                                    <option value="<?= $prodotto->id ?>" data-prezzo="<?= $prodotto->prezzo ?>" data-descrizione="<?= htmlspecialchars($prodotto->descrizione) ?>" data-um="<?= $prodotto->um ?>">
                                                                        <?= htmlspecialchars($prodotto->codice_articolo) ?> - <?= htmlspecialchars($prodotto->descrizione) ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                </optgroup>
                                                                @endif
                                                                <optgroup label="Materie Prime">
                                                                    <?php foreach($materie_prime as $mp): ?>
                                                                    <option value="<?= $mp->id ?>" data-prezzo="<?= $mp->prezzo ?>" data-descrizione="<?= htmlspecialchars($mp->descrizione) ?>" data-um="<?= $mp->um ?>">
                                                                        <?= htmlspecialchars($mp->codice_articolo) ?> - <?= htmlspecialchars($mp->descrizione) ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                </optgroup>
                                                                <optgroup label="Commerciali / Ricambi">
                                                                    <?php foreach($commerciali as $cm): ?>
                                                                    <option value="<?= $cm->id ?>" data-prezzo="<?= $cm->prezzo ?>" data-descrizione="<?= htmlspecialchars($cm->descrizione) ?>" data-um="<?= $cm->um ?>">
                                                                        <?= htmlspecialchars($cm->codice_articolo) ?> - <?= htmlspecialchars($cm->descrizione) ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                </optgroup>
                                                            </select>

                                                            <textarea style="field-sizing: content" class="form-control bg-light border-0" name="products[0][descrizione]" id="descrizione_1" rows="1" placeholder="Descrizione Articolo" style="display: none;"></textarea>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control product-price bg-light border-0" name="products[0][prezzo_unitario]" id="productRate-1" step="0.01" placeholder="0.00" {{ !in_array($cd_do, ['RDO', 'ORDF']) ? 'required' : '' }} />
                                                        @if(!in_array($cd_do, ['RDO', 'ORDF']))
                                                        <div class="invalid-feedback">Please enter a rate</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control product-discount bg-light border-0" name="products[0][sconto_perc]" id="productDiscount-1" min="0" max="100" step="0.01" value="0" placeholder="0" onchange="updatePrice(1)" onkeyup="updatePrice(1)" />
                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                                <div class="col-md-8">
                                                                    <select class="select2" name="products[0][natura]" id="productNature-1" onchange="updateNatureFields(1)">
                                                                        <option value="">Seleziona Natura IVA</option>
                                                                        @foreach(DB::table('ft_nature')->orderBy('preferito', 'desc')->get() as $natura)
                                                                            <option value="{{ $natura->id }}" data-aliquota="{{ $natura->aliquota }}" data-descrizione="{{ $natura->descrizione }}" data-descrizione-pdf="{{ $natura->descrizione_pdf }}" data-iva="{{ $natura->aliquota }}"  {{ $azienda->natura == $natura->id ? 'selected' : '' }}>{{ $natura->aliquota }}% - {{ $natura->descrizione }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <input type="number" style="width: 50px" class="product-vat form-control  bg-light border-0" name="products[0][iva]" id="productIva-1" value="22" step="0.01" placeholder="0%" required />
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <input type="text" readonly placeholder="Rif. Normativo" class="form-control" name="products[0][rif_normativo]" id="rif_normativo_1">
                                                                    <input type="text" readonly placeholder="Rif. Normativo nel PDF" class="form-control" name="products[0][rif_normativo_pdf]" id="rif_normativo_pdf_1">
                                                                </div>
                                                        </div>

                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control bg-light border-0"
                                                                       name="products[0][lotto]"
                                                                       id="productLotto-1"
                                                                       placeholder="Lotto" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="date"
                                                                       class="form-control bg-light border-0"
                                                                       name="products[0][scadenza_lotto]"
                                                                       id="productScadenzaLotto-1"
                                                                       placeholder="Scadenza" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light border-0 product-quantity" name="products[0][qta]" id="product-qty-1" value="1" min="0" step="1" oninput="updatePrice(1)">
                                                    </td>
                                                    <td>
                                                        <input type="text" style="width: 50px" class="form-control bg-light border-0" name="products[0][um]" id="productUm-1" placeholder="UM">
                                                    </td>
                                                    <td class="text-end">
                                                        <div>
                                                            <input type="text" style="width: 100px" class="form-control bg-light border-0 product-line-price" name="products[0][imponibile]" id="productPrice-1" placeholder="$0.00" readonly />
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <div>
                                                            <input type="text" style="width: 100px" class="form-control bg-light border-0 product-vat-price" name="products[0][imposta]" id="vatPrice-1" placeholder="$0.00" readonly />
                                                        </div>
                                                    </td>
                                                    <td class="product-removal">
                                                        <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct(1)">Elimina</a>
                                                    </td>
                                                </tr>

                                                <?php } else { ?>

                                                @foreach($dorig as $index => $d)
                                                    @if($d->nota_riga === 'NOTA_SEPARATORE')
                                                    <tr id="{{ $d->id }}" class="product nota-separatore" style="background-color: #fff3cd;">
                                                        <th scope="row" class="product-id"></th>
                                                        <input type="hidden" name="products[{{ $d->id }}][tipologia]" value="update">
                                                        <input type="hidden" name="products[{{ $d->id }}][is_nota]" value="1">
                                                        <input type="hidden" name="products[{{ $d->id }}][id_articolo]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][prezzo_unitario]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][iva]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][qta]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][um]" value="">
                                                        <input type="hidden" name="products[{{ $d->id }}][imponibile]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][imposta]" value="0">
                                                        <input type="hidden" name="products[{{ $d->id }}][lotto]" value="">
                                                        <td colspan="9" class="text-start">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ri-sticky-note-line me-2 text-warning fs-20"></i>
                                                                <textarea class="form-control fw-bold" name="products[{{ $d->id }}][descrizione]" rows="1" style="field-sizing: content; font-weight: bold;" placeholder="Nota separatrice...">{{ $d->descrizione }}</textarea>
                                                            </div>
                                                        </td>
                                                        <td class="product-removal">
                                                            <a href="javascript:void(0)" class="btn btn-danger" onclick="removeProduct({{ $d->id }})"><i class="ri-delete-bin-line"></i></a>
                                                        </td>
                                                    </tr>
                                                    @else
                                                    <tr id="{{ $d->id }}" class="product">
                                                        <th scope="row" class="product-id">{{ $index + 1 }}</th>
                                                        <input type="hidden" name="products[{{ $d->id }}][tipologia]" value="update">

                                                        <td class="text-start">
                                                            <div class="mb-2">
                                                                <select style="width: 100%" class="form-control bg-light border-0 select2" name="products[{{ $d->id }}][id_articolo]" id="productSelect-{{ $index + 1 }}" onchange="updateProductNameField({{ $index + 1 }},this)">
                                                                    <option value="0">@if(in_array($dotes->cd_do, ['RDO', 'ORDF']))-- Campo libero / Descrizione manuale --@else Seleziona un Prodotto @endif</option>
                                                                    @if(!in_array($dotes->cd_do, ['RDO', 'ORDF']))
                                                                    <optgroup label="Prodotti Finiti">
                                                                        @foreach($prodotti_finiti as $prodotto)
                                                                            <option value="{{ $prodotto->id }}"
                                                                                    data-prezzo="{{ $prodotto->prezzo }}"
                                                                                    data-descrizione="{{ htmlspecialchars($prodotto->titolo) }}"
                                                                                    data-um="{{ $prodotto->um }}"
                                                                                    {{ $d->id_articolo == $prodotto->id ? 'selected' : '' }}>
                                                                                {{ $prodotto->codice_articolo }} - {{ $prodotto->descrizione }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                    @endif
                                                                    <optgroup label="Materie Prime">
                                                                        @foreach($materie_prime as $mp)
                                                                            <option value="{{ $mp->id }}"
                                                                                    data-prezzo="{{ $mp->prezzo }}"
                                                                                    data-descrizione="{{ htmlspecialchars($mp->titolo) }}"
                                                                                    data-um="{{ $mp->um }}"
                                                                                    {{ $d->id_articolo == $mp->id ? 'selected' : '' }}>
                                                                                {{ $mp->codice_articolo }} - {{ $mp->descrizione }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                    <optgroup label="Commerciali / Ricambi">
                                                                        @foreach($commerciali as $com)
                                                                            <option value="{{ $com->id }}"
                                                                                    data-prezzo="{{ $com->prezzo }}"
                                                                                    data-descrizione="{{ htmlspecialchars($com->titolo) }}"
                                                                                    data-um="{{ $com->um }}"
                                                                                    {{ $d->id_articolo == $com->id ? 'selected' : '' }}>
                                                                                {{ $com->codice_articolo }} - {{ $com->descrizione }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                </select>
                                                                <textarea style="field-sizing: content;display:{{ $d->id_articolo == 0 ? 'block' : 'none' }}" class="form-control bg-light border-0"  name="products[{{ $d->id }}][descrizione]" id="descrizione_{{ $index + 1 }}" rows="2" placeholder="Descrizione Articolo">{{ $d->descrizione }}</textarea>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control product-price bg-light border-0" value="{{ $d->prezzo_unitario }}" name="products[{{ $d->id }}][prezzo_unitario]" id="productRate-{{ $index + 1 }}" step="0.01" placeholder="0.00" @if(!in_array($dotes->cd_do, ['RDO', 'ORDF'])) required @endif />
                                                            <div class="invalid-feedback">
                                                                Please enter a rate
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control product-discount bg-light border-0" name="products[{{ $d->id }}][sconto_perc]" id="productDiscount-{{ $index + 1 }}" min="0" max="100" step="0.01" value="{{ $d->sconto_perc ?? 0 }}" placeholder="0" onchange="updatePrice({{ $index + 1 }})" onkeyup="updatePrice({{ $index + 1 }})" />
                                                        </td>

                                                        <td>
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <select class="select2" name="products[{{ $d->id }}][natura]" id="productNature-{{ $index + 1 }}" onchange="updateNatureFields({{ $index + 1 }})">
                                                                            <option value="">Seleziona Natura IVA</option>
                                                                            @foreach(DB::table('ft_nature')->orderBy('preferito', 'desc')->get() as $natura)
                                                                                <option value="{{ $natura->id }}" data-aliquota="{{ $natura->aliquota }}" data-descrizione="{{ $natura->descrizione }}" data-descrizione-pdf="{{ $natura->descrizione_pdf }}" data-iva="{{ $natura->aliquota }}"  {{ $d->natura == $natura->id ? 'selected' : '' }}>{{ $natura->aliquota }}% - {{ $natura->descrizione }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <input type="number" style="width: 50px" class="product-vat form-control  bg-light border-0" name="products[{{ $d->id }}][iva]" id="productIva-{{ $index + 1 }}" value="22" step="0.01" placeholder="0%" required />
                                                                    </div>

                                                                    <div class="col-md-12">
                                                                        <input type="text" readonly placeholder="Rif. Normativo" class="form-control" name="products[{{ $d->id }}][rif_normativo]" id="rif_normativo_{{ $index + 1 }}" value="{{ $d->rif_normativo }}">
                                                                        <input type="text" readonly placeholder="Rif. Normativo nel PDF" class="form-control" name="products[{{ $d->id }}][rif_normativo_pdf]" id="rif_normativo_pdf_{{ $index + 1 }}" value="{{ $d->rif_normativo_pdf }}">
                                                                    </div>
                                                                </div>
                                                        </td>

                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <input type="number" class="form-control bg-light border-0"
                                                                           name="products[{{ $d->id }}][lotto]"
                                                                           id="productLotto-{{ $index + 1 }}"
                                                                           value="{{ $d->lotto }}"
                                                                           placeholder="Lotto" />
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="date"
                                                                           class="form-control bg-light border-0"
                                                                           name="products[{{ $d->id }}][scadenza_lotto]"
                                                                           id="productScadenzaLotto-{{ $index + 1 }}"
                                                                           value="{{ $d->scadenza_lotto }}"
                                                                           placeholder="Scadenza" />
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control bg-light border-0 product-quantity" name="products[{{ $d->id }}][qta]" id="product-qty-{{ $index + 1 }}" value="{{ $d->qta }}" min="0" step="1" oninput="updatePrice({{ $index + 1 }})">
                                                        </td>
                                                        <td>
                                                            <input type="text" style="width: 50px" class="form-control bg-light border-0" name="products[{{ $d->id }}][um]" id="productUm-{{ $index + 1 }}" placeholder="UM" value="{{ $d->um }}">
                                                        </td>
                                                        <td class="text-end">
                                                            <div>
                                                                <input type="text" style="width: 120px" class="form-control bg-light border-0 product-line-price" value="{{ $d->imponibile }}" name="products[{{ $d->id }}][imponibile]" id="productPrice-{{ $index + 1 }}" placeholder="$0.00" readonly />
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div>
                                                                <input type="text" style="width: 120px" class="form-control bg-light border-0 product-vat-price" value="{{ $d->imposta }}" name="products[{{ $d->id }}][imposta]" id="vatPrice-{{ $index + 1 }}" placeholder="$0.00" readonly />
                                                            </div>
                                                        </td>

                                                        <td class="product-removal">
                                                            <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct({{ $d->id }})">Elimina</a>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach

                                                <?php } ?>


                                                </tbody>

                                                <tbody>
                                                <tr id="newForm" style="display: none;"><td class="d-none" colspan="5"><p>Add New Form</p></td></tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <a href="javascript:new_link()" id="add-item" class="btn btn-soft-secondary fw-medium"><i class="ri-add-fill me-1 align-bottom"></i>Aggiungi Riga</a>
                                                        <a href="javascript:add_nota()" class="btn btn-soft-warning fw-medium"><i class="ri-sticky-note-line me-1 align-bottom"></i>Aggiungi Nota</a>
                                                    </td>
                                                </tr>
                                                <tr class="border-top border-top-dashed mt-2">
                                                    <td colspan="6" style="width: 70%"></td>
                                                    <td colspan="2" class="p-0">
                                                        <table class="table table-borderless table-sm table-nowrap align-middle mb-0">
                                                            <tbody>
                                                            <tr>
                                                                <th scope="row">Imponibile</th>
                                                                <td style="width:150px;">
                                                                    <input style="width: 120px" type="text" class="form-control bg-light border-0" name="imponibile" id="cart-subtotal" placeholder="$0.00" readonly />
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="row">Imposta</th>
                                                                <td style="width:150px;">
                                                                    <input style="width: 120px" type="text" class="form-control bg-light border-0" name="imposta" id="cart-tax" placeholder="$0.00" readonly />
                                                                </td>
                                                            </tr>

                                                            <tr class="border-top border-top-dashed">
                                                                <th scope="row">Totale</th>
                                                                <td>
                                                                    <input type="text" style="width: 120px" class="form-control bg-light border-0" name="totale" id="cart-total" placeholder="$0.00" readonly />
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <!--end table-->
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <!--end table-->
                                        </div>

                                        <?php if($modalita == 'crea' && ($cd_do == 'FTV' || $cd_do == 'NC' || $cd_do == 'PRV')){ ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title mb-0">Pagamenti</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="payment-rows">
                                                    <div class="payment-row mb-3 riga">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Importo</label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control importo totale_importo_scadenza" step="0.01" value="0" id="totale_importo_scadenza" name="scadenziario[0][importo]">
                                                                        <span class="input-group-text">€</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="form-label">Termini pagam.</label>
                                                                    <select class="form-select termini" name="scadenziario[0][termini]">
                                                                        <option value="immediato">Immediato</option>
                                                                        <option value="30gg">30 giorni</option>
                                                                        <option value="60gg">60 giorni</option>
                                                                        <option value="90gg">90 giorni</option>
                                                                        <option value="120gg">120 giorni</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Scadenza</label>
                                                                    <input type="date" class="form-control scadenza" name="scadenziario[0][data_scadenza]"  value="{{ date('Y-m-d') }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Stato</label>
                                                                    <select class="form-select stato" name="scadenziario[0][stato]">
                                                                        <option value="da_pagare">Da Pagare</option>
                                                                        <option value="pagato">Pagato</option>
                                                                        <option value="parziale">Parziale</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">&nbsp;</label>
                                                                    <div class="d-flex">
                                                                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                                            <i class="ri-delete-bin-2-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="warning-scadenze" style="color:red;font-weight: bold;"></div>

                                                <!-- Pulsante per aggiungere nuova riga -->
                                                <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-info btn-sm" id="addPaymentRow">
                                                        <i class="ri-add-line align-middle me-1"></i>
                                                        Aggiungi scadenza di pagamento
                                                    </button>
                                                </div>


                                            </div>
                                        </div>

                                        <?php } else if($modalita != 'crea' && ($dotes->cd_do == 'FTV' || $dotes->cd_do == 'NC' || $dotes->cd_do == 'PRV')) { ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title mb-0">Pagamenti</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="payment-rows">
                                                        <?php $i = 0; foreach($scadenziario as $s){ ?>
                                                    <div class="<?php echo ($i==0)?'payment-row riga':'riga' ?> mb-3" style="    position: relative;padding: 1rem;border: 1px solid #e9ecef;border-radius: 0.25rem;margin-bottom: 1rem;">

                                                        <div class="row align-items-center">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Importo</label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control importo" step="0.01" onkeyup="updateTotals();"  name="scadenziario[<?php echo $i ?>][importo]" value="<?php echo $s->importo ?>">
                                                                        <span class="input-group-text">€</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="form-label">Termini pagam.</label>
                                                                    <select class="form-select termini" name="scadenziario[<?php echo $i ?>][termini]">
                                                                        <option value="immediato" <?php echo ($s->termini == 'immediato')?'selected':'' ?>>Immediato</option>
                                                                        <option value="30gg" <?php echo ($s->termini == '30gg')?'selected':'' ?>>30 giorni</option>
                                                                        <option value="60gg" <?php echo ($s->termini == '60gg')?'selected':'' ?>>60 giorni</option>
                                                                        <option value="90gg" <?php echo ($s->termini == '90gg')?'selected':'' ?>>90 giorni</option>
                                                                        <option value="120gg" <?php echo ($s->termini == '120gg')?'selected':'' ?>>120 giorni</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Scadenza</label>
                                                                    <input type="date" class="form-control scadenza" name="scadenziario[<?php echo $i ?>][data_scadenza]"  value="{{ date('Y-m-d',strtotime($s->data_scadenza)) }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">Stato</label>
                                                                    <select class="form-select stato" name="scadenziario[<?php echo $i ?>][stato]">
                                                                        <option value="da_pagare" <?php echo ($s->stato == 'da_pagare')?'selected':'' ?>>Da Pagare</option>
                                                                        <option value="pagato" <?php echo ($s->stato == 'pagato')?'selected':'' ?>>Pagato</option>
                                                                        <option value="parziale" <?php echo ($s->stato == 'parziale')?'selected':'' ?>>Parziale</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label class="form-label">&nbsp;</label>
                                                                    <div class="d-flex">
                                                                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                                            <i class="ri-delete-bin-2-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <?php $i++; } ?>
                                                </div>

                                                <div id="warning-scadenze" style="color:red;font-weight: bold;"></div>


                                                <!-- Pulsante per aggiungere nuova riga -->
                                                <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-info btn-sm" id="addPaymentRow">
                                                        <i class="ri-add-line align-middle me-1"></i>
                                                        Aggiungi scadenza di pagamento
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>




                                        <input type="hidden" name="deleted_rows" id="deleted_rows" value="">


                                    </div>
                                </div>

                                <!-- Tab Evasioni -->
                                @if($modalita != 'crea')
                                    <!-- Tab Evasioni -->
                                    <div class="tab-pane" id="evasioni" role="tabpanel">

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex align-items-center">
                                                            <h5 class="card-title mb-0 flex-grow-1">Righe Evase Flusso Precedente</h5>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-nowrap">
                                                                <thead>
                                                                <tr>
                                                                    <th>Documento Evaso Da</th>
                                                                    <th>Articolo</th>
                                                                    <th>Qtà Evasa</th>
                                                                    <th>Azioni</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    $evasioni = DB::select("
                                                                        SELECT
                                                                            do.cd_do as tipo_documento,
                                                                            do.numero_doc,
                                                                            do.data_doc,
                                                                            dr.descrizione,
                                                                            dr.qta as qta_evasa,
                                                                            dr_orig.qta as qta_origine,
                                                                            do.cd_do as documento_origine,
                                                                            do.numero_doc as numero_origine,
                                                                            do.data_doc as data_origine,
                                                                            do.id as id_dotes
                                                                        FROM dorig dr
                                                                        INNER JOIN dotes d ON d.id = dr.id_dotes
                                                                        JOIN dorig dr_orig ON dr.id_dorig_evade = dr_orig.id
                                                                        JOIN dotes do ON dr_orig.id_dotes = do.id
                                                                        WHERE dr.id_dotes = ?
                                                                        AND d.id_azienda = ?
                                                                        ORDER BY d.data_doc DESC, d.numero_doc DESC",
                                                                        [$dotes->id, $utente->id_azienda]
                                                                    );
                                                                @endphp

                                                                @foreach($evasioni as $evasione)

                                                                        <?php if($evasione->qta_origine > 0){ ?>

                                                                    @php
                                                                        $qta_residua = $evasione->qta_origine - $evasione->qta_evasa;
                                                                        $percentuale_evasione = ($evasione->qta_evasa / $evasione->qta_origine) * 100;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>
                                                                            <strong>{{$evasione->tipo_documento}} {{$evasione->numero_doc}}</strong><br>
                                                                            <small>del {{date('d/m/Y', strtotime($evasione->data_doc))}}</small>
                                                                        </td>
                                                                        <td>{{$evasione->descrizione}}</td>
                                                                        <td class="text-end">{{number_format($evasione->qta_evasa, 2)}}</td>
                                                                        <td>
                                                                            <div class="hstack gap-2">
                                                                                <a href="/utente/modifica_documento/{{$evasione->id_dotes}}"
                                                                                   class="btn btn-sm btn-soft-primary">
                                                                                    <i class="ri-eye-line"></i>
                                                                                </a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    <?php } ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        @if(count($evasioni) == 0)
                                                            <div class="text-center p-3">
                                                                <div class="avatar-sm mx-auto mb-4">
                                                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                                                        <i class="ri-file-text-line"></i>
                                                                    </div>
                                                                </div>
                                                                <p class="text-muted">Nessuna evasione trovata per questo documento</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex align-items-center">
                                                            <h5 class="card-title mb-0 flex-grow-1">Righe Evase Flusso Successivo</h5>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-nowrap">
                                                                <thead>
                                                                <tr>
                                                                    <th>Documento Evasione</th>
                                                                    <th>Articolo</th>
                                                                    <th>Qtà Evasa</th>
                                                                    <th>Azioni</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    $evasioni = DB::select("
                                                                        SELECT
                                                                            d.cd_do as tipo_documento,
                                                                            d.numero_doc,
                                                                            d.data_doc,
                                                                            dr.descrizione,
                                                                            dr.qta as qta_evasa,
                                                                            dr_orig.qta as qta_origine,
                                                                            do.cd_do as documento_origine,
                                                                            do.numero_doc as numero_origine,
                                                                            do.data_doc as data_origine,
                                                                            d.id as id_dotes
                                                                        FROM dorig dr
                                                                        INNER JOIN dotes d ON d.id = dr.id_dotes
                                                                        JOIN dorig dr_orig ON dr.id_dorig_evade = dr_orig.id
                                                                        JOIN dotes do ON dr_orig.id_dotes = do.id
                                                                        WHERE dr_orig.id_dotes = ?
                                                                        AND d.id_azienda = ?
                                                                        ORDER BY d.data_doc DESC, d.numero_doc DESC",
                                                                        [$dotes->id, $utente->id_azienda]
                                                                    );
                                                                @endphp

                                                                @foreach($evasioni as $evasione)

                                                                        <?php if($evasione->qta_origine > 0){ ?>

                                                                    @php
                                                                        $qta_residua = $evasione->qta_origine - $evasione->qta_evasa;
                                                                        $percentuale_evasione = ($evasione->qta_evasa / $evasione->qta_origine) * 100;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>
                                                                            <strong>{{$evasione->tipo_documento}} {{$evasione->numero_doc}}</strong><br>
                                                                            <small>del {{date('d/m/Y', strtotime($evasione->data_doc))}}</small>
                                                                        </td>
                                                                        <td>{{$evasione->descrizione}}</td>
                                                                        <td class="text-end">{{number_format($evasione->qta_evasa, 2)}}</td>
                                                                        <td>
                                                                            <div class="hstack gap-2">
                                                                                <a href="/utente/modifica_documento/{{$evasione->id_dotes}}"
                                                                                   class="btn btn-sm btn-soft-primary">
                                                                                    <i class="ri-eye-line"></i>
                                                                                </a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    <?php } ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        @if(count($evasioni) == 0)
                                                            <div class="text-center p-3">
                                                                <div class="avatar-sm mx-auto mb-4">
                                                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                                                        <i class="ri-file-text-line"></i>
                                                                    </div>
                                                                </div>
                                                                <p class="text-muted">Nessuna evasione trovata per questo documento</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                @endif

                                @if(!empty($azienda) && !empty($azienda->manut_workflow_accettazione_multistep) && in_array($cd_do, ['PRE','ORD']))
                                    <div class="card mt-3">
                                        <div class="card-header bg-soft-info p-3">
                                            <h5 class="card-title mb-0"><i class="ri-train-line me-2"></i>Manutenzione</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                @if(!empty($azienda->manut_anagrafica_vagoni_attiva) && isset($vagoni))
                                                    <div class="col-md-6">
                                                        <label class="form-label">Vagone</label>
                                                        <select name="id_vagone" class="form-select">
                                                            <option value="">— Nessun vagone —</option>
                                                            @foreach($vagoni as $v)
                                                                <option value="{{ $v->id }}" {{ (isset($dotes) && !empty($dotes->id_vagone) && $dotes->id_vagone == $v->id) ? 'selected' : '' }}>
                                                                    {{ $v->codice }}@if($v->tipo) ({{ $v->tipo }})@endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="col-md-6">
                                                        <label class="form-label">Automezzo / Vagone</label>
                                                        <input type="text" name="automezzo" class="form-control" placeholder="numero carro o identificativo" value="{{ $dotes->automezzo ?? '' }}">
                                                    </div>
                                                @endif

                                                <div class="col-md-6">
                                                    <label class="form-label">Località intervento</label>
                                                    <input type="text" name="localita" class="form-control" placeholder="es. Marcianise" value="{{ $dotes->localita ?? '' }}">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">Motivo rientro (Reason intake)</label>
                                                    <input type="text" name="reason_intake" class="form-control" placeholder="motivo per cui il vagone rientra in officina" value="{{ $dotes->reason_intake ?? '' }}">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">Note per l'operatore</label>
                                                    <textarea name="note_operatore" class="form-control" rows="2" placeholder="note per il manutentore">{{ $dotes->note_operatore ?? '' }}</textarea>
                                                </div>

                                                @if($modalita == 'crea' && isset($lavorazioni_disponibili) && count($lavorazioni_disponibili) > 0)
                                                    <div class="col-md-12">
                                                        <hr>
                                                        <label class="form-label"><strong>Applica lavorazioni dal catalogo</strong></label>
                                                        <p class="text-muted small mb-2">Le righe delle lavorazioni selezionate verranno aggiunte automaticamente al documento al salvataggio.</p>
                                                        <input type="text" id="filtro_lavorazioni" class="form-control form-control-sm mb-2" placeholder="🔍 Filtra per codice o descrizione...">
                                                        <div class="border rounded p-2" style="max-height: 260px; overflow-y: auto;">
                                                            @foreach($lavorazioni_disponibili as $lav)
                                                                <div class="form-check lav-apply-row" data-search="{{ strtolower($lav->codice.' '.$lav->descrizione) }}">
                                                                    <input class="form-check-input" type="checkbox" name="lavorazioni_applicate[]" value="{{ $lav->id }}" id="lav_apply_{{ $lav->id }}">
                                                                    <label class="form-check-label" for="lav_apply_{{ $lav->id }}">
                                                                        <strong>{{ $lav->codice }}</strong> — {{ $lav->descrizione }}
                                                                        <small class="text-muted">(€ {{ number_format($lav->totale,2,',','.') }})</small>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <script>
                                                            document.getElementById('filtro_lavorazioni').addEventListener('input', function(e) {
                                                                var q = e.target.value.toLowerCase().trim();
                                                                document.querySelectorAll('.lav-apply-row').forEach(function(row) {
                                                                    row.style.display = (q === '' || row.dataset.search.indexOf(q) !== -1) ? '' : 'none';
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <?php if(($modalita != 'crea' && ($dotes->stato == 0 || $dotes->stato == 2) && $dotes->cd_do != 'FTI') || $modalita == 'crea'){ ?>
                                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                    <input type="submit" id="modifica_dotes" name="<?php echo ($modalita == 'crea')?'aggiungi_dotes':'modifica_dotes' ?>" class="btn btn-success" value="Salva">
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </form> <!--end row-->



    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php if($modalita != 'crea'){ ?>

<div class="modal fade" id="modal_evadi" >
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title">Evadi Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Documento da Creare</label>
                            <select name="cd_do" class="form-control" required>
                                    <?php $flusso = explode(',',$do->flusso); ?>
                                    <?php foreach($flusso as $f){ ?>
                                <option value="<?php echo $f ?>"><?php echo $f ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Prodotto</th>
                                <th>Qtà Totale</th>
                                <th>Qtà già Evasa</th>
                                <th>Qtà da Evadere</th>
                            </tr>
                            </thead>
                            <tbody id="righe_da_evadere">
                                <?php foreach($dorig as $d){ ?>
                            <tr>
                                <td><?php echo $d->descrizione ?></td>
                                <td><?php echo $d->qta ?></td>
                                <td><?php echo $d->qta_evasa ?></td>
                                <td>
                                    <input type="number" name="quantita_evasa[<?php echo $d->id ?>]" class="form-control" max="<?php echo $d->qta - $d->qta_evasa ?>" value="<?php echo $d->qta - $d->qta_evasa ?>">
                                </td>
                            </tr>

                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <input type="hidden" name="id_dotes_originale" value="<?php echo $dotes->id ?>">
                    <button type="submit" name="evadi" value="Evadi" class="btn btn-success">Evadi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Invia Email -->
<div class="modal fade" id="modalInviaEmail" tabindex="-1" aria-labelledby="modalInviaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInviaEmailLabel">Invia tramite e-mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">A quali indirizzi e-mail dobbiamo inviare questo documento? (è possibile inserire più indirizzi separati da ;)</label>
                            <input type="text" class="form-control" id="email_destinatari"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}(;[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})*"
                                   name="email_destinatari" value="{{ $clienteDotes->mail_recapito ?? '' }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Che messaggio desideri inviare insieme al documento?</label>

                            <input type="text" class="form-control" name="oggetto" value="<?php echo $do->descrizione ?> nr. <?php echo $dotes->numero_doc ?> - <?php echo $azienda->ragione_sociale ?>" placeholder="Oggetto">
                            <div class="bg-light p-2 border rounded">
                                    <textarea class="form-control" id="corpo" name="corpo" rows="6">
Gentile {{ $dotes->ragione_sociale_fatturazione }},

In Allegato troverà il documento <?php echo $do->descrizione ?> N. {{ $dotes->numero_doc }}

Cordiali saluti,
{{ $azienda->ragione_sociale }}
                                    </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <input type="submit" class="btn btn-success" name="invia_mail" value="Invia Mail">
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalInviaFE" tabindex="-1" aria-labelledby="modalInviaDELabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInviaEmailLabel">Invia Fattura Elettronica <?php echo $dotes->numero_doc ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3>Vuoi Inviare la Fattura Elettronica ?</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>

                    <form method="post" onsubmit="return confirm('Vuoi Inviare La Fattura Elettronica ?')">
                        <input type="hidden" name="id" value="<?php echo $dotes->id ?>">
                        <input style="margin-left:5px;" type="submit" name="invia_sdi" value="Invia Fattura Elettronica" class="btn btn-success">
                    </form>

                </div>

            </form>

        </div>
    </div>
</div>
<?php } ?>

@include('utente.common.footer')


<script src="https://unpkg.com/onscan.js/onscan.min.js"></script>
<script>
    function applicaPrezzoListino(index, idArticolo) {
        console.log("Applica prezzo listino chiamata per indice:", index, "articolo:", idArticolo);

        // Se non c'è un articolo selezionato, esci
        if (!idArticolo || idArticolo === '0') {
            console.log("Nessun articolo selezionato");
            return;
        }

        // Recupera l'ID del cliente dal form
        var idClienteElement = document.getElementById('id_cliente');
        if (!idClienteElement) {
            console.log("Elemento id_cliente non trovato");
            return;
        }

        var idCliente = idClienteElement.value;
        if (!idCliente) {
            console.log("Nessun cliente selezionato");
            return;
        }

        console.log("Cliente trovato:", idCliente);

        // Trova il campo prezzo unitario corrispondente all'indice della riga
        var campoPrezzoUnitario = document.getElementById('productRate-' + index);
        if (!campoPrezzoUnitario) {
            console.log("Campo prezzo non trovato per indice", index);
            return;
        }

        console.log("Campo prezzo trovato:", campoPrezzoUnitario);

        // Effettua la chiamata AJAX per ottenere il prezzo dal listino
        console.log("Invio richiesta AJAX per articolo", idArticolo, "e cliente", idCliente);

        $.ajax({
            url: '/utente/ajax/get_prezzo_articolo_simple',
            type: 'GET',
            dataType: 'json',
            data: {
                id_articolo: idArticolo,
                id_cliente: idCliente
            },
            success: function(data) {
                console.log("Risposta AJAX ricevuta:", data);

                if (data.success && data.prezzo) {
                    console.log("Prezzo ricevuto:", data.prezzo);

                    // Imposta il prezzo dal listino
                    campoPrezzoUnitario.value = data.prezzo.toFixed(2);

                    // Cambia lo sfondo per indicare che il prezzo viene da un listino
                    if (data.da_listino) {
                        campoPrezzoUnitario.style.backgroundColor = '#e8f5e9';

                        // Mostra un messaggio di alert
                        alert("Prezzo applicato dal listino: " + data.messaggio);
                    }

                    // Aggiorna i calcoli dei totali in modo sicuro
                    try {
                        updatePrice(index);
                    } catch (e) {
                        console.error("Errore nell'aggiornamento del prezzo:", e);
                    }
                } else {
                    console.log("Nessun prezzo ricevuto o errore");
                }
            },
            error: function(xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                console.log("Risposta:", xhr.responseText);
            }
        });
    }


    /*scan del barcode*/

    // ... existing code ...
    function updateProductNameField(index, selectElement) {
        try {
            console.log("updateProductNameField chiamata per indice:", index);

            if (!selectElement) {
                console.log("Select element non valido");
                return;
            }

            var selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption) {
                console.log("Nessuna opzione selezionata");
                return;
            }

            var productId = selectedOption.value;
            console.log("ID prodotto selezionato:", productId);

            var descrizioneField = document.getElementById('descrizione_' + index);
            if (!descrizioneField) {
                console.log("Campo descrizione non trovato");
                return;
            }

            if (productId != 0) {
                // Nascondi il campo descrizione quando viene selezionato un articolo
                descrizioneField.style.display = 'none';

                // Imposta la descrizione
                var descrizione = selectedOption.getAttribute('data-descrizione');
                descrizioneField.value = descrizione || '';

                // Imposta il prezzo
                var prezzo = selectedOption.getAttribute('data-prezzo');
                var prezzoField = document.getElementById('productRate-' + index);
                if (prezzoField) {
                    prezzoField.value = prezzo || '';
                }

                // Imposta l'unità di misura nel campo di testo
                var um = selectedOption.getAttribute('data-um');
                var umInput = document.getElementById('productUm-' + index);
                if (umInput) {
                    umInput.value = um || '';
                }

                // Applica il prezzo da listino se disponibile
                console.log("Chiamo applicaPrezzoListino");
                setTimeout(function() {
                    applicaPrezzoListino(index, productId);
                }, 100); // Piccolo ritardo per garantire che il DOM sia pronto

            } else {
                // Mostra il campo descrizione quando non viene selezionato nessun articolo
                descrizioneField.style.display = 'block';
                descrizioneField.value = ''; // Pulisci il campo

                // Resetta gli altri campi
                var prezzoField = document.getElementById('productRate-' + index);
                if (prezzoField) {
                    prezzoField.value = '';
                }

                var umInput = document.getElementById('productUm-' + index);
                if (umInput) {
                    umInput.value = '';
                }
            }

            // Aggiorna i calcoli in modo sicuro
            try {
                updatePrice(index);
            } catch (e) {
                console.error("Errore nell'aggiornamento del prezzo:", e);
            }
        } catch (e) {
            console.error("Errore in updateProductNameField:", e);
        }
    }

    // ... existing code ...



    function populateProductDetails(product) {
        let existingEmptyProduct = findEmptyProductRow();

        if (!existingEmptyProduct) {
            new_link(); // Crea una nuova riga vuota se non ne esistono
            existingEmptyProduct = findEmptyProductRow(); // Trova la nuova riga creata
        }

        if (existingEmptyProduct) {
            fillProductRow(existingEmptyProduct, product);
            updateTotals(); // Aggiorna i totali dopo aver inserito i dati del prodotto
        }
    }

    function findEmptyProductRow() {
        // Trova la prima riga di prodotto vuota nel documento
        const products = document.querySelectorAll('.product');
        return Array.from(products).find(row => !row.querySelector('input[name*="[nome_prodotto]"]').value);
    }

    function fillProductRow(row, product) {
        row.querySelector('input[name*="[nome_prodotto]"]').value = product.nome_prodotto;
        row.querySelector('input[name*="[prezzo_unitario]"]').value = product.prezzo_unitario;
        row.querySelector('input[name*="[qta]"]').value = product.qta;
        row.querySelector('input[name*="[prezzo_totale]"]').value = product.prezzo_totale;

        // Chiama updatePrice per la riga specifica se necessario
        // Puoi aggiungere un ID o un altro identificatore unico alla riga per facilitare questo aggiornamento
    }



    /*scan del barcode*/


    function compilaCampi(value) {
        var tipoAnagrafica = '<?php echo in_array($cd_do, ["RDO", "ORDF"]) ? "fornitore" : "cliente"; ?>';
        if(typeof aggiornaSediConsegna === 'function') aggiornaSediConsegna(value);
        jQuery.ajax({
            url: "<?php echo URL::asset('ajax/getClienteForOrdine') ?>/",
            type:'GET',
            data:{id:value, tipo: tipoAnagrafica},
            success: function(result){
                console.log(result)
                document.getElementById('companyAddress').innerHTML = result.indirizzo;
                document.getElementById('cap').value = result.cap;
                document.getElementById('comune').value = result.comune;
                document.getElementById('indirizzoFatturazione').innerHTML = result.indirizzo;
                document.getElementById('ragioneSocialeFatturazione').value = result.ragione_sociale;
                document.getElementById('partitaIvaFatturazione').value = result.piva;
                document.getElementById('sdi').value = result.sdi;
                document.getElementById('pec').value = result.pec;
                document.getElementById('comune_fatturazione').value = result.comune;
                document.getElementById('provincia_fatturazione').value = result.provincia;
                <?php if($modalita == 'crea' && ($cd_do == 'FTV' || $cd_do == 'NC') || $modalita != 'crea' && ($dotes->cd_do == 'FTV' || $dotes->cd_do == 'NC')){ ?>
                document.getElementById('esigibilita_iva').value = result.esigibilita_iva;
                <?php } ?>


            }});


    }


    /*parte dell'aggiunta degli articoli*/

    // Variabile globale per contare gli articoli
    let productCount = document.querySelectorAll('.product').length; // Conta i prodotti già presenti

    // Funzione per aggiungere un nuovo articolo
    function new_link() {
        const index = productCount;
        productCount++;
        const newRow = `
        <tr id="${productCount}" class="product">
            <th scope="row" class="product-id">${productCount}</th>

            <input type="hidden" name="products[${index}][tipologia]" value="insert">

            <td class="text-start">
                <div class="mb-2">
                    <select class="form-control bg-light border-0 select2" id="productSelect-${productCount}" name="products[${index}][id_articolo]" onchange="updateProductNameField(${productCount},this)" style="width: 100%;">
                        <option value="0"><?= in_array($cd_do, ['RDO', 'ORDF']) ? '-- Campo libero / Descrizione manuale --' : 'Seleziona i tuoi prodotti' ?></option>
                        <?php if(!in_array($cd_do, ['RDO', 'ORDF'])): ?>
                        <optgroup label="Prodotti Finiti">
                        <?php foreach($prodotti_finiti as $prodotto): ?>
        <option value="<?= $prodotto->id ?>" data-prezzo="<?= $prodotto->prezzo ?>" data-descrizione="<?= htmlspecialchars($prodotto->descrizione) ?>" data-um="<?= $prodotto->um ?>">
            <?= htmlspecialchars($prodotto->codice_articolo) ?> - <?= htmlspecialchars($prodotto->descrizione) ?>
        </option>
        <?php endforeach; ?>
        </optgroup>
        <?php endif; ?>
        <optgroup label="Materie Prime">
        <?php foreach($materie_prime as $mp): ?>
        <option value="<?= $mp->id ?>" data-prezzo="<?= $mp->prezzo ?>" data-descrizione="<?= htmlspecialchars($mp->descrizione) ?>" data-um="<?= $mp->um ?>">
            <?= htmlspecialchars($mp->codice_articolo) ?> - <?= htmlspecialchars($mp->descrizione) ?>
        </option>
        <?php endforeach; ?>
        </optgroup>
        <optgroup label="Commerciali / Ricambi">
        <?php foreach($commerciali as $cm): ?>
        <option value="<?= $cm->id ?>" data-prezzo="<?= $cm->prezzo ?>" data-descrizione="<?= htmlspecialchars($cm->descrizione) ?>" data-um="<?= $cm->um ?>">
            <?= htmlspecialchars($cm->codice_articolo) ?> - <?= htmlspecialchars($cm->descrizione) ?>
        </option>
        <?php endforeach; ?>
        </optgroup>
    </select>

    <textarea style="field-sizing: content" class="form-control bg-light border-0" name="products[${index}][descrizione]" id="descrizione_${productCount}" rows="1" placeholder="Descrizione Articolo"></textarea>
                </div>
            </td>
            <td>
                <input type="number" class="form-control product-price bg-light border-0" name="products[${index}][prezzo_unitario]" id="productRate-${productCount}" step="0.01" placeholder="0.00" <?= !in_array($cd_do, ['RDO', 'ORDF']) ? 'required' : '' ?> />
                <?= !in_array($cd_do, ['RDO', 'ORDF']) ? '<div class="invalid-feedback">Please enter a rate</div>' : '' ?>
            </td>
            <td>
                <input type="number" class="form-control product-discount bg-light border-0" name="products[${index}][sconto_perc]" id="productDiscount-${productCount}" min="0" max="100" step="0.01" value="0" placeholder="0" onchange="updatePrice(${productCount})" onkeyup="updatePrice(${productCount})" />
            </td>

            <td>
                <div class="row">
        <div class="col-md-8">
            <select class="select2" name="products[${index}][natura]" id="productNature-${productCount}" onchange="updateNatureFields(${productCount})">
                                <option value="">Seleziona Natura IVA</option>
                                @foreach(DB::table('ft_nature')->orderBy('preferito', 'desc')->get() as $natura)
        <option value="{{ $natura->id }}" data-aliquota="{{ $natura->aliquota }}" data-descrizione="{{ $natura->descrizione }}" data-descrizione-pdf="{{ $natura->descrizione_pdf }}" data-iva="{{ $natura->aliquota }}"  {{ $azienda->natura == $natura->id ? 'selected' : '' }}>{{ $natura->aliquota }}% - {{ $natura->descrizione }}</option>
                                @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <input type="number" style="width: 50px" class="product-vat form-control bg-light border-0" name="products[${index}][iva]" id="productIva-${productCount}" value="22" step="0.01" placeholder="0%" required />
                        </div>
                        <div class="col-md-12">
                            <input type="text" readonly placeholder="Rif. Normativo" class="form-control" name="products[${index}][rif_normativo]" id="rif_normativo_${productCount}">
                            <input type="text" readonly placeholder="Rif. Normativo nel PDF" class="form-control" name="products[${index}][rif_normativo_pdf]" id="rif_normativo_pdf_${productCount}">
                        </div>
        </div>
    </td>

    <td>
        <div class="row">
            <div class="col-md-6">
                <input type="number" class="form-control bg-light border-0"
                    name="products[${index}][lotto]"
                            id="productLotto-${productCount}"
                            placeholder="Lotto"
                            oninput="toggleScadenzaLotto(this.id)" />
                    </div>
                    <div class="col-md-6" style="display: none;">
                        <input type="date"
                            class="form-control bg-light border-0"
                            name="products[${index}][scadenza_lotto]"
                            id="productScadenzaLotto-${productCount}"
                            placeholder="Scadenza" />
                    </div>
                </div>
            </td>
            <td>
                <input type="number" class="form-control bg-light border-0 product-quantity" name="products[${index}][qta]" id="product-qty-${productCount}" value="1" min="0" step="1" oninput="updatePrice(${productCount})">
            </td>
            <td>
                <input type="text" style="width: 50px" class="form-control bg-light border-0" name="products[${index}][um]" id="productUm-${productCount}" placeholder="UM">
            </td>
            <td class="text-end">
                <div>
                    <input type="text" class="form-control bg-light border-0 product-line-price" name="products[${index}][imponibile]" id="productPrice-${productCount}" placeholder="€0.00" readonly />
                </div>
            </td>
            <td class="text-end">
                <div>
                    <input type="text" class="form-control bg-light border-0 product-vat-price" name="products[${index}][imposta]" id="vatPrice-${productCount}" placeholder="€0.00" readonly />
                </div>
            </td>
            <td class="product-removal">
                <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct(${productCount})">Elimina</a>
            </td>
        </tr>
    `;

        // Aggiungi la nuova riga al tbody
        document.getElementById('newlink').insertAdjacentHTML('beforeend', newRow);

        // Inizializza select2 per la nuova riga
        $(`#productSelect-${productCount}`).select2();
        $(`#productNature-${productCount}`).select2();

        // Aggiungi la gestione del lotto
        document.getElementById(`productLotto-${productCount}`).addEventListener('input', function() {
            toggleScadenzaLotto(this.id);
        });

        // Nascondi inizialmente il campo scadenza
        document.getElementById(`productScadenzaLotto-${productCount}`).closest('.col-md-6').style.display = 'none';

        // Aggiorna i totali
        updatePrice(productCount);
    }

    function add_nota() {
        const index = productCount;
        productCount++;
        const newRow = `
        <tr id="${productCount}" class="product nota-separatore" style="background-color: #fff3cd;">
            <th scope="row" class="product-id"></th>
            <input type="hidden" name="products[${index}][tipologia]" value="insert">
            <input type="hidden" name="products[${index}][is_nota]" value="1">
            <input type="hidden" name="products[${index}][id_articolo]" value="0">
            <input type="hidden" name="products[${index}][prezzo_unitario]" value="0">
            <input type="hidden" name="products[${index}][iva]" value="0">
            <input type="hidden" name="products[${index}][qta]" value="0">
            <input type="hidden" name="products[${index}][um]" value="">
            <input type="hidden" name="products[${index}][imponibile]" value="0">
            <input type="hidden" name="products[${index}][imposta]" value="0">
            <input type="hidden" name="products[${index}][lotto]" value="">
            <td colspan="9" class="text-start">
                <div class="d-flex align-items-center">
                    <i class="ri-sticky-note-line me-2 text-warning fs-20"></i>
                    <textarea class="form-control fw-bold" name="products[${index}][descrizione]" rows="1" style="field-sizing: content; font-weight: bold;" placeholder="Scrivi una nota separatrice..."></textarea>
                </div>
            </td>
            <td class="product-removal">
                <a href="javascript:void(0)" class="btn btn-danger" onclick="removeProduct(${productCount})">
                    <i class="ri-delete-bin-line"></i>
                </a>
            </td>
        </tr>
        `;
        document.getElementById('newlink').insertAdjacentHTML('beforeend', newRow);
    }


    function updateNatureFields(id) {
        const select = document.getElementById(`productNature-${id}`);
        const selectedOption = select.options[select.selectedIndex];


        document.getElementById(`rif_normativo_${id}`).value = selectedOption.dataset.descrizione || '';
        document.getElementById(`rif_normativo_pdf_${id}`).value = selectedOption.dataset.descrizionePdf || '';

        // Aggiorna il calcolo dei prezzi usando l'aliquota della natura
        const iva = parseFloat(selectedOption.dataset.iva) || 0;
        document.getElementById(`productIva-${id}`).value = iva;
        updatePrice(id);
    }

    function changeQuantity(id, change) {
        const qtyInput = document.getElementById(`product-qty-${id}`);
        let quantity = parseInt(qtyInput.value);
        quantity = Math.max(0, quantity + change); // Impedisce valori negativi
        qtyInput.value = quantity;
        updatePrice(id); // Aggiorna il prezzo della riga
    }

    // Funzione per aggiornare il prezzo dell'articolo
    function updatePrice(id) {
        try {
            console.log("updatePrice chiamata per id:", id);

            var rateElement = document.getElementById(`productRate-${id}`);
            var quantityElement = document.getElementById(`product-qty-${id}`);
            var ivaElement = document.getElementById(`productIva-${id}`);

            if (!rateElement || !quantityElement || !ivaElement) {
                console.log("Elementi mancanti per updatePrice");
                return;
            }

            const rate = parseFloat(rateElement.value) || 0;
            const quantity = parseFloat(quantityElement.value) || 0;
            const iva = parseFloat(ivaElement.value) || 0;
            var discountElement = document.getElementById(`productDiscount-${id}`);
            const sconto = discountElement ? (parseFloat(discountElement.value) || 0) : 0;

            console.log("Valori:", rate, quantity, iva, "sconto:", sconto);

            // Calcola il prezzo base con sconto
            const linePrice = rate * quantity * (1 - sconto/100);

            // Aggiungi IVA al prezzo base
            const linePriceWithIva = (linePrice / 100) * iva;

            var priceElement = document.getElementById(`productPrice-${id}`);
            var vatElement = document.getElementById(`vatPrice-${id}`);

            if (priceElement) {
                priceElement.value = `€${linePrice.toFixed(2)}`;
            }

            if (vatElement) {
                vatElement.value = `€${linePriceWithIva.toFixed(2)}`;
            }

            updateTotals();
        } catch (e) {
            console.error("Errore in updatePrice:", e);
        }
    }


    function updateTotals() {
        try {
            console.log("updateTotals chiamata");

            let subtotal = 0;
            let tax = 0;

            // Calcola il subtotale in modo sicuro
            document.querySelectorAll('.product-line-price').forEach(function(priceField) {
                if (priceField && priceField.value) {
                    const price = parseFloat(priceField.value.replace('€', '')) || 0;
                    subtotal += price;
                }
            });

            document.querySelectorAll('.product-vat-price').forEach(function(priceField) {
                if (priceField && priceField.value) {
                    const price = parseFloat(priceField.value.replace('€', '')) || 0;
                    tax += price;
                }
            });

            let total = subtotal + tax;

            var subtotalElement = document.getElementById('cart-subtotal');
            var taxElement = document.getElementById('cart-tax');
            var totalElement = document.getElementById('cart-total');

            if (subtotalElement) {
                subtotalElement.value = `€${subtotal.toFixed(2)}`;
            }

            if (taxElement) {
                taxElement.value = `€${tax.toFixed(2)}`;
            }

            if (totalElement) {
                totalElement.value = `€${total.toFixed(2)}`;
            }

            var totalImportoScadenza = document.getElementById('totale_importo_scadenza');
            if (totalImportoScadenza) {
                totalImportoScadenza.value = `${total.toFixed(2)}`;
            }

            let totalScadenziario = 0;
            document.querySelectorAll('.importo').forEach(function(input) {
                if (input && input.value) {
                    totalScadenziario += parseFloat(input.value) || 0;
                }
            });

            const submitButton = document.getElementById('modifica_dotes');
            const warningDiv = document.getElementById('warning-scadenze');

            if (submitButton && !warningDiv) {
                const newWarningDiv = document.createElement('div');
                newWarningDiv.id = 'warning-scadenze';
                newWarningDiv.className = 'alert alert-danger mt-3';
                if (submitButton.parentNode) {
                    submitButton.parentNode.insertBefore(newWarningDiv, submitButton);
                }
            }

            if (totalElement) {
                var totalRounded = Math.round(total * 100) / 100;

                var esigibilitaIva = document.getElementById('esigibilita_iva');
                if (esigibilitaIva && esigibilitaIva.value == 'S') {
                    totalRounded = Math.round(subtotal * 100) / 100;
                }

                var scadenzaRounded = Math.round(totalScadenziario * 100) / 100;

                // Logica per verificare se è una fattura
                var isFattura = false;
                var documentType = '<?php echo ($modalita == "crea") ? $cd_do : (isset($dotes) ? $dotes->cd_do : ""); ?>';
                if (documentType !== 'FTV') {
                    scadenzaRounded = totalRounded;
                }

                if (submitButton && warningDiv) {
                    if (totalRounded !== scadenzaRounded) {
                        submitButton.disabled = true;
                        warningDiv.innerHTML =
                            `Attenzione: Il totale delle scadenze (${scadenzaRounded.toFixed(2)}€) non corrisponde al totale del documento (${totalRounded.toFixed(2)}€). ` +
                            `La differenza è di ${(totalRounded - scadenzaRounded).toFixed(2)}€`;
                        warningDiv.style.display = 'block';
                    } else {
                        submitButton.disabled = false;
                        warningDiv.style.display = 'none';
                    }
                }
            }
        } catch (e) {
            console.error("Errore in updateTotals:", e);
        }
    }

    // Assicurati di inizializzare correttamente all'avvio della pagina
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM completamente caricato");

        try {
            // Aggiunta di un callback globale per gli eventi Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $(document).on('select2:select', 'select[id^="productSelect-"]', function(e) {
                    var selectId = $(this).attr('id');
                    var index = selectId.split('-')[1];
                    console.log("Evento select2:select rilevato per", selectId, "indice:", index);

                    // Recupera l'ID dell'articolo selezionato
                    var idArticolo = e.params.data.id;
                    console.log("Articolo selezionato:", idArticolo);

                    setTimeout(function() {
                        applicaPrezzoListino(index, idArticolo);
                    }, 200);
                });
            }

            // Inizializza gli eventi e i calcoli
            updateTotals();
        } catch (e) {
            console.error("Errore nell'inizializzazione:", e);
        }
    });


    let deletedRows = []; // Array per tenere traccia delle righe eliminate

    function removeProduct(id) {

        deletedRows.push(id);
        document.getElementById('deleted_rows').value = deletedRows.join(',');

        const row = document.getElementById(id);
        if (row) {
            row.remove();
            updateTotals();
        }
    }

    // Inizializza gli eventi sugli elementi esistenti
    function initializeEvents() {
        // Associa l'evento oninput per aggiornare il prezzo sui campi esistenti
        document.querySelectorAll('.product-price').forEach((input, index) => {
            input.addEventListener('input', () => updatePrice(index + 1));
        });

        document.querySelectorAll('.product-vat').forEach((input, index) => {
            input.addEventListener('input', () => updatePrice(index + 1));
        });

        // Associa l'evento click per le quantità su campi esistenti
        document.querySelectorAll('.minus').forEach((button, index) => {
            button.addEventListener('click', () => changeQuantity(index + 1, -1));
        });

        document.querySelectorAll('.plus').forEach((button, index) => {
            button.addEventListener('click', () => changeQuantity(index + 1, 1));
        });
    }

    // Inizializza i calcoli e gli eventi quando la pagina viene caricata
    document.addEventListener('DOMContentLoaded', () => {
        initializeEvents();
        updateTotals();
        <?php if($modalita == 'crea' && $cd_do == 'FTV' || $modalita != 'crea' && $dotes->cd_do == 'FTV'){ ?>
        updateNatureFields(1);
        <?php } ?>
    });

</script>

<script>

    function updateEventListeners() {
        // Rimuovi riga
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.riga').length > 1) {
                    this.closest('.riga').remove();
                }
            };
        });

        // Aggiorna scadenza in base ai termini
        document.querySelectorAll('.termini').forEach(select => {
            select.onchange = function() {
                const scadenzaInput = this.closest('.riga').querySelector('.scadenza');
                const oggi = new Date();
                let scadenza = new Date();

                switch(this.value) {
                    case 'immediato':
                        scadenza = oggi;
                        break;
                    case '30gg':
                        scadenza.setDate(oggi.getDate() + 30);
                        break;
                    case '60gg':
                        scadenza.setDate(oggi.getDate() + 60);
                        break;
                    case '90gg':
                        scadenza.setDate(oggi.getDate() + 90);
                        break;
                    case '120gg':
                        scadenza.setDate(oggi.getDate() + 120);
                        break;
                }

                scadenzaInput.value = scadenza.toISOString().split('T')[0];
            };
        });

        // Calcola totali quando cambiano gli importi
        document.querySelectorAll('.importo').forEach(input => {
            input.onchange = function() {
                calcolaTotali();
            };
        });
    }

    function calcolaTotali() {
        let totalePagamenti = 0;
        const importoDocumento = parseFloat(document.getElementById('cart-total').value.replace('€', '')) || 0;

        // Calcola il totale attuale dei pagamenti
        document.querySelectorAll('.importo').forEach(input => {
            totalePagamenti += parseFloat(input.value || 0);
        });

        // Verifica se il totale supera l'importo del documento
        if (totalePagamenti > importoDocumento) {
            // Reset dell'ultimo valore inserito
            event.target.value = 0;

            // Ricalcola il totale
            totalePagamenti = 0;
            document.querySelectorAll('.importo').forEach(input => {
                totalePagamenti += parseFloat(input.value || 0);
            });
        }

        // Mostra l'importo rimanente da assegnare
        const rimanente = importoDocumento - totalePagamenti;
        if(rimanente > 0) {
            // Se c'è un importo rimanente, mostralo
            document.querySelectorAll('.payment-row:last-child .importo').forEach(input => {
                if(input.value === '0' || input.value === '') {
                    input.value = rimanente.toFixed(2);
                }
            });
        }

        updateTotals();
    }


    // Aggiungi la validazione al form prima dell'invio (solo per documenti con scadenziario)
    document.querySelector('form').addEventListener('submit', function(e) {
        // Esegui la validazione solo se esistono righe di pagamento (FTV/NC)
        if (document.querySelectorAll('.payment-row').length === 0) return;

        const importoDocumento = parseFloat(document.getElementById('cart-total').value.replace('€', '')) || 0;
        let totalePagamenti = 0;

        document.querySelectorAll('.importo').forEach(input => {
            totalePagamenti += parseFloat(input.value || 0);
        });

        if (Math.abs(totalePagamenti - importoDocumento) > 0.01) {
            e.preventDefault();
            alert('Il totale dei pagamenti non corrisponde al totale del documento.');
            return false;
        }
    });

    function validaImportoTotale() {
        let totalePagamenti = 0;
        document.querySelectorAll('.importo').forEach(input => {
            totalePagamenti += parseFloat(input.value || 0);
        });

        const importoDocumento = parseFloat(document.getElementById('cart-total').value);
        if (totalePagamenti !== importoDocumento) {
            return false;
        }
        return true;
    }

    // Inizializza gli event listeners per le righe esistenti
    updateEventListeners();

    let rowCount = {{ 0 }};


    // Funzione per aggiungere nuova riga

    <?php if($modalita == 'crea' && $cd_do == 'FTV' || $modalita != 'crea' && $dotes->cd_do == 'FTV'){ ?>


    document.getElementById('addPaymentRow').addEventListener('click', function() {
        rowCount++;
        const template = document.querySelector('.payment-row').cloneNode(true);



        // Ottieni la data di oggi nel formato YYYY-mm-dd
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];

        // Reset valori
        template.querySelectorAll('input').forEach(input => {
            // Se è un campo data, imposta la data di oggi
            if(input.type === 'date') {
                input.value = formattedDate;
            } else {
                input.value = '';
            }
            // Aggiorna i nomi dei campi con il nuovo indice
            if(input.name && input.name.includes('scadenziario[')) {
                input.name = input.name.replace(/scadenziario\[\d+\]/, `scadenziario[${rowCount}]`);
            }
        });


        template.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
            // Aggiorna i nomi dei campi select con il nuovo indice
            if(select.name && select.name.includes('scadenziario[')) {
                select.name = select.name.replace(/scadenziario\[\d+\]/, `scadenziario[${rowCount}]`);
            }
        });

        // Aggiorna eventuali altri campi nella sezione avanzata
        template.querySelectorAll('.card-body input, .card-body select').forEach(field => {
            if(field.name && field.name.includes('scadenziario[')) {
                field.name = field.name.replace(/scadenziario\[\d+\]/, `scadenziario[${rowCount}]`);
            }
        });

        document.getElementById('payment-rows').appendChild(template);

        // Aggiorna event listeners
        updateEventListeners();
        calcolaTotali();

    });

    <?php } ?>

    $('.select2').select2();


</script>

<style>

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 35px;
        user-select: none;
        -webkit-user-select: none;
    }

    .payment-row {
        position: relative;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }

    .btn-remove {
        opacity: 0.8;
    }

    .btn-remove:hover {
        opacity: 1;
    }

    .btn-advanced {
        opacity: 0.8;
    }

    .btn-advanced:hover {
        opacity: 1;
    }
</style>



<?php if($modalita == 'crea'){ ?>

<script type="text/javascript">
    if (document.querySelector('select[name="modalita_pagamento"]')) {
        document.querySelector('select[name="modalita_pagamento"]').value = '<?php echo $azienda->modalita_pagamento ?>';
    }

    if (document.querySelector('select[name="condizioni_pagamento"]')) {
        document.querySelector('select[name="condizioni_pagamento"]').value = '<?php echo $azienda->condizioni_pagamento ?>'; // Default a pagamento completo
    }

    if (document.querySelector('input[name="iban"]')) {
        document.querySelector('input[name="iban"]').value = '<?php echo $azienda->iban ?>';
    }

    if (document.querySelector('input[name="istituto_finanziario"]')) {
        document.querySelector('input[name="istituto_finanziario"]').value = '<?php echo $azienda->istituto_finanziario ?>';
    }

    $('#modalita_pagamento').select2()


</script>

<?php } ?>


<?php if($modalita != 'crea'){ ?>
<script type="text/javascript">

    function evadiDocumento() {
        $('#modal_evadi').modal('show');
    }

    function duplicaDocumento() {
        if(confirm('Vuoi davvero duplicare questo documento?')) {
            $.post('/utente/duplica_documento/{{ $dotes->id }}', function(response) {
                if(response.success) {
                    window.location.href = '/utente/modifica_documento/' + response.new_id;
                }
            });
        }
    }

    function inviaEmail() {
        $('#modalInviaEmail').modal('show');
    }

    function inviaFE() {
        $('#modalInviaFE').modal('show');
    }

    function cosaDevo() {
        $('#modalCosaFare').modal('show');
    }

    function visualizzaFE() {
        window.open('/utente/visualizza_fe/{{ $dotes->id }}', '_blank');
    }


    function esportaXML() {
        window.location.href = '/utente/esporta_xml/{{ $dotes->id }}';
    }


</script>

<?php } ?>

<script>
    // Aggiungi questa funzione dopo le altre funzioni JavaScript esistenti

    function toggleScadenzaLotto(lottoId) {
        // Estrai il numero dell'indice dal lottoId
        const index = lottoId.split('-')[1];
        const scadenzaInput = document.getElementById(`productScadenzaLotto-${index}`);
        const lottoInput = document.getElementById(lottoId);

        // Mostra/nascondi il campo scadenza in base al valore del lotto
        if (lottoInput.value && lottoInput.value !== '0') {
            scadenzaInput.closest('.col-md-6').style.display = 'block';
        } else {
            scadenzaInput.closest('.col-md-6').style.display = 'none';
            scadenzaInput.value = ''; // Reset del valore della scadenza
        }
    }


    // Aggiungi questo al DOMContentLoaded per inizializzare i campi esistenti
    document.addEventListener('DOMContentLoaded', function() {
        // ... existing code ...

        // Inizializza i campi esistenti
        document.querySelectorAll('input[id^="productLotto-"]').forEach(lottoInput => {
            // Aggiungi l'evento input
            lottoInput.addEventListener('input', function() {
                toggleScadenzaLotto(this.id);
            });

            // Imposta lo stato iniziale
            toggleScadenzaLotto(lottoInput.id);
        });
    });



//codice javascript per listini



</script>
<script>
    function compilaCampi(value) {
        var tipoAnagrafica = '<?php echo in_array($cd_do, ["RDO", "ORDF"]) ? "fornitore" : "cliente"; ?>';
        if(typeof aggiornaSediConsegna === 'function') aggiornaSediConsegna(value);
        jQuery.ajax({
            url: "<?php echo URL::asset('ajax/getClienteForOrdine') ?>/",
            type:'GET',
            data:{id:value, tipo: tipoAnagrafica},
            success: function(result){
                console.log(result);
                document.getElementById('companyAddress').innerHTML = result.indirizzo;
                document.getElementById('cap').value = result.cap;
                document.getElementById('comune').value = result.comune;
                document.getElementById('indirizzoFatturazione').innerHTML = result.indirizzo;
                document.getElementById('ragioneSocialeFatturazione').value = result.ragione_sociale;
                document.getElementById('partitaIvaFatturazione').value = result.piva;
                document.getElementById('sdi').value = result.sdi;
                document.getElementById('pec').value = result.pec;
                document.getElementById('comune_fatturazione').value = result.comune;
                document.getElementById('provincia_fatturazione').value = result.provincia;
                <?php if($modalita == 'crea' && ($cd_do == 'FTV' || $cd_do == 'NC') || $modalita != 'crea' && ($dotes->cd_do == 'FTV' || $dotes->cd_do == 'NC')){ ?>
                document.getElementById('esigibilita_iva').value = result.esigibilita_iva;
                <?php } ?>

                // Verifica se il cliente ha un agente associato direttamente
                // nella tabella clienti tramite il campo id_agente
                const cliente = <?php echo json_encode($clienti); ?>;
                const clienteSelezionato = cliente.find(c => c.id == value);

                if (clienteSelezionato && clienteSelezionato.id_agente) {
                    // Imposta l'agente nel select
                    document.getElementById('id_agente').value = clienteSelezionato.id_agente;

                    // Trova il nome dell'agente per il messaggio
                    const agenti = <?php echo json_encode($agenti); ?>;
                    const agente = agenti.find(a => a.id == clienteSelezionato.id_agente);
                    let nomeAgente = agente ? (agente.nome + ' ' + agente.cognome) : 'sconosciuto';

                    // Mostra un popup per avvisare l'utente
                    Swal.fire({
                        title: 'Agente associato',
                        html: `Il cliente selezionato ha già un agente associato:<br><strong>${nomeAgente}</strong>`,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });

                    // Se utilizzi select2, aggiorna anche l'interfaccia visuale
                    if ($.fn.select2) {
                        $('#id_agente').trigger('change');
                    }
                }
            }
        });
    }

    // Assicurati che SweetAlert2 sia incluso nella pagina
    document.addEventListener('DOMContentLoaded', function() {
        // Verifica se SweetAlert2 non è già caricato
        if (typeof Swal === 'undefined') {
            // Carica dinamicamente SweetAlert2 se non è presente
            const sweetAlertScript = document.createElement('script');
            sweetAlertScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(sweetAlertScript);
        }
    });

    // Gestione della navigazione tramite Tab e creazione automatica di nuove righe
    document.addEventListener('DOMContentLoaded', function() {
        setupTabNavigation();
    });

    function setupTabNavigation() {
        // Trova tutte le righe prodotto nel documento
        const productRows = document.querySelectorAll('tr.product');

        // Per ogni riga, trova l'ultimo campo di input e aggiungi l'evento keydown
        productRows.forEach(row => {
            setupRowTabNavigation(row);
        });
    }

    function setupRowTabNavigation(row) {
        // Trova tutti gli input nella riga
        const inputs = row.querySelectorAll('input:not([readonly]), select, textarea');

        if (inputs.length === 0) return;

        // Ottieni l'ultimo input della riga
        const lastInput = inputs[inputs.length - 1];

        // Aggiungi event listener per il tasto Tab sull'ultimo input
        lastInput.addEventListener('keydown', function(e) {
            // Verifica se è stato premuto il tasto Tab e non è stato premuto Shift
            if (e.key === 'Tab' && !e.shiftKey) {
                // Trova tutte le righe prodotto
                const allRows = document.querySelectorAll('tr.product');
                // Controlla se questa è l'ultima riga
                if (row === allRows[allRows.length - 1]) {
                    // Previeni il comportamento predefinito del tasto Tab
                    e.preventDefault();
                    // Crea una nuova riga
                    new_link();

                    // Dopo che la nuova riga è stata creata, ottieni il primo input e imposta il focus
                    setTimeout(() => {
                        const newRow = document.querySelectorAll('tr.product')[allRows.length];
                        if (newRow) {
                            const firstInput = newRow.querySelector('input:not([readonly]), select, textarea');
                            if (firstInput) {
                                firstInput.focus();

                                // Se è un select2, apri il dropdown
                                if (firstInput.tagName === 'SELECT' && $(firstInput).hasClass('select2')) {
                                    $(firstInput).select2('open');
                                }
                            }
                        }
                    }, 100); // Piccolo ritardo per garantire che la nuova riga sia completamente creata
                }
            }
        });
    }

    // Estendi la funzione new_link per aggiungere la navigazione tab alla nuova riga
    const originalNewLink = window.new_link;
    window.new_link = function() {
        // Chiama la funzione originale
        originalNewLink();

        // Trova tutte le righe prodotto
        const allRows = document.querySelectorAll('tr.product');
        // Imposta la navigazione tab per l'ultima riga aggiunta
        if (allRows.length > 0) {
            setupRowTabNavigation(allRows[allRows.length - 1]);
        }
    };

    // Funzione per gestire anche la navigazione all'interno della riga
    function setupTabOrder() {
        // Trova tutte le righe prodotto
        const productRows = document.querySelectorAll('tr.product');

        productRows.forEach(row => {
            const inputs = Array.from(row.querySelectorAll('input:not([readonly]), select, textarea'));

            inputs.forEach((input, index) => {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab') {
                        if (!e.shiftKey && index < inputs.length - 1) {
                            // Tab in avanti all'interno della riga
                            e.preventDefault();
                            const nextInput = inputs[index + 1];
                            nextInput.focus();

                            // Se è un select2, apri il dropdown
                            if (nextInput.tagName === 'SELECT' && $(nextInput).hasClass('select2')) {
                                $(nextInput).select2('open');
                            }
                        } else if (e.shiftKey && index > 0) {
                            // Tab indietro all'interno della riga
                            e.preventDefault();
                            const prevInput = inputs[index - 1];
                            prevInput.focus();

                            // Se è un select2, apri il dropdown
                            if (prevInput.tagName === 'SELECT' && $(prevInput).hasClass('select2')) {
                                $(prevInput).select2('open');
                            }
                        }
                    }
                });
            });
        });
    }

    // Estendiamo la funzione di aggiornamento degli eventi
    const originalUpdateEventListeners = window.updateEventListeners || function() {};
    window.updateEventListeners = function() {
        // Chiama la funzione originale se esiste
        if (typeof originalUpdateEventListeners === 'function') {
            originalUpdateEventListeners();
        }

        // Aggiorna l'ordine di navigazione Tab
        setupTabNavigation();
        setupTabOrder();
    };

    // Aggiungi un MutationObserver per rilevare quando vengono aggiunte nuove righe
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                const addedNodes = Array.from(mutation.addedNodes);
                const addedRows = addedNodes.filter(node => {
                    return node.nodeType === 1 && node.classList && node.classList.contains('product');
                });

                if (addedRows.length) {
                    addedRows.forEach(setupRowTabNavigation);
                }
            }
        });
    });

    // Osserva la tabella per l'aggiunta di nuove righe
    const table = document.querySelector('.invoice-table tbody#newlink');
    if (table) {
        observer.observe(table, { childList: true });
    }

    // Inizializza la gestione della navigazione Tab anche per le scadenze di pagamento
    function setupPaymentRowsTabNavigation() {
        const paymentRows = document.querySelectorAll('.payment-row');

        paymentRows.forEach(row => {
            const inputs = Array.from(row.querySelectorAll('input:not([readonly]), select'));

            if (inputs.length === 0) return;

            const lastInput = inputs[inputs.length - 1];

            lastInput.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && !e.shiftKey) {
                    const allRows = document.querySelectorAll('.payment-row');
                    if (row === allRows[allRows.length - 1]) {
                        e.preventDefault();

                        // Simula il click sul pulsante "Aggiungi scadenza di pagamento"
                        const addButton = document.getElementById('addPaymentRow');
                        if (addButton) {
                            addButton.click();

                            // Focus sul primo input della nuova riga di pagamento
                            setTimeout(() => {
                                const newRow = document.querySelectorAll('.payment-row')[allRows.length];
                                if (newRow) {
                                    const firstInput = newRow.querySelector('input:not([readonly]), select');
                                    if (firstInput) {
                                        firstInput.focus();
                                    }
                                }
                            }, 100);
                        }
                    }
                }
            });
        });
    }

    // Aggiungi l'inizializzazione per le scadenze di pagamento
    document.addEventListener('DOMContentLoaded', function() {
        setupPaymentRowsTabNavigation();

        // Aggiorna quando viene aggiunta una nuova riga di pagamento
        const addPaymentRowButton = document.getElementById('addPaymentRow');
        if (addPaymentRowButton) {
            addPaymentRowButton.addEventListener('click', function() {
                setTimeout(setupPaymentRowsTabNavigation, 100);
            });
        }
    });

    // Gestisci la navigazione anche nei campi select2
    $(document).on('select2:select', 'select[id^="productSelect-"]', function() {
        // Passa al campo successivo dopo la selezione
        const rowId = this.id.split('-')[1];
        const nextField = document.getElementById(`productRate-${rowId}`);
        if (nextField) {
            setTimeout(() => {
                nextField.focus();
            }, 100);
        }
    });

    // Gestisci la navigazione anche nei campi natura IVA
    $(document).on('select2:select', 'select[id^="productNature-"]', function() {
        // Passa al campo successivo dopo la selezione
        const rowId = this.id.split('-')[1];
        const nextField = document.getElementById(`productIva-${rowId}`);
        if (nextField) {
            setTimeout(() => {
                nextField.focus();
            }, 100);
        }
    });
</script>


<!-- Modifica lo stile CSS -->
<style>
    /* ... existing styles ... */

    .col-md-6 {
        transition: all 0.3s ease;
    }

    .table>:not(caption)>*>* {
        padding: 5px;
    }
</style>