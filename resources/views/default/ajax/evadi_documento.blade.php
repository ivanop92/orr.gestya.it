<?php foreach ($dotes as $d) { ?>
<div class="modal modal-xl fade" id="modal_evadi_<?php echo $d->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Evadi Documento <?php echo $d->cd_do ?></h5>
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $d->numero_doc ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form id="form_evadi_<?php echo $d->id; ?>" class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post" action="{{ route('evadi.quantita') }}">                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Iterazione sui 'dorig' associati -->
                            <?php foreach ($dorig as $item) { ?>
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column">
                                <!-- Dettagli Prodotto -->
                                <div class="mb-2">
                                    <strong>Prodotto:</strong> <?php echo $item->nome_prodotto; ?><br>
                                    <small><?php echo $item->dettagli_prodotto; ?></small>
                                </div>

                                <!-- Select Quantità -->
                                <div class="mb-2">
                                    <strong>Quantità:</strong>
                                    <select name="quantita_evasa[<?php echo $item->id; ?>]" class="form-select">
                                            <?php for ($i = 0; $i <= $item->qta_evadibile; $i++) { ?>
                                        <option value="<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Quantità Evadibile -->
                                <div class="mb-2">
                                    <strong>Quantità Evadibile:</strong>
                                    <span><?php echo $item->qta_evadibile; ?></span>
                                </div>

                                <!-- Quantità Evasa -->
                                <div class="mb-2">
                                    <strong>Quantità Evasa:</strong>
                                    <span><?php echo $item->qta_evasa ?: 0; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                                <!-- Select per la scelta del documento da creare -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="documento">Seleziona Documento da Creare</label>
                                <select name="documento_creato" class="form-select" id="documentoSelect" required onchange="toggleFatturaFields()">
                                    <option value="">Seleziona un documento</option>
                                        <?php foreach ($documenti as $doc) { ?>
                                    <option value="<?php echo $doc->cd_do; ?>" data-ordine="<?php echo $doc->ordine; ?>">
                                            <?php echo $doc->cd_do . ' - ' . $doc->descrizione; ?>
                                    </option>
                                    <?php } ?>
                                    <option value="fattura">Fattura</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sezione Fattura che si attiva dinamicamente -->
                        <div id="fatturaFields" class="row g-3 mt-3" style="display:none;">
                            <!-- Iterazione sui 'dorig' associati per aggiungere i campi Codice IVA e Rif. Normativo -->
                                <?php foreach ($dorig as $item) { ?>
                            <div class="col-12 mb-3">
                                <div class="d-flex flex-column">
                                    <!-- Dettagli Prodotto -->
                                    <div class="mb-2">
                                        <strong>Prodotto:</strong> <?php echo $item->nome_prodotto; ?><br>
                                        <small><?php echo $item->dettagli_prodotto; ?></small>
                                    </div>

                                    <!-- Codice IVA -->
                                    <div class="col-sm-12 mb-2">
                                        <label>Codice IVA <b style="color:red">*</b></label>
                                        <select id="codice_iva_<?php echo $item->id; ?>" name="codice_iva[<?php echo $item->id; ?>]" class="form-control" style="width:100%" onchange="cambia_rif(<?php echo $item->id; ?>)">
                                            <option value="">Nessuna Esenzione</option>
                                            <option value="N1">N1. operazioni escluse ex articolo 15</option>
                                            <option value="N2">N2. operazioni non soggette</option>
                                            <option value="N4">N4. operazioni esenti articolo 10</option>
                                            <option value="N5">N5. Regime al margine</option>
                                            <option value="N6">N6. operazioni in “Reverse Charge” articolo 17 c.6 lett. a-ter</option>
                                            <option value="N7">N7. iva assolta in altro stato dell’Unione Europea</option>
                                        </select>
                                    </div>

                                    <!-- Rif. Normativo -->
                                    <div class="col-sm-12 mb-2">
                                        <label>Rif. Normativo</label>
                                        <input type="text" name="rif_normativo[<?php echo $item->id; ?>]" class="form-control" id="rif_normativo_<?php echo $item->id; ?>" placeholder="rif_normativo">
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                                    <!-- Altri campi fattura (uguale a prima, nessuna modifica fatta qui) -->
                            <div class="col-sm-6">
                                <label>Tipologia <b style="color:red">*</b></label>
                                <select name="tipologia_documento" class="form-control select2" style="width:100%">
                                    <option value="TD24" selected>Fattura Differita</option>
                                    <option value="TD02">Acconto/Anticipo su fattura</option>
                                    <option value="TD03">Acconto/Anticipo su parcella</option>
                                    <option value="TD04">Nota di Credito</option>
                                    <option value="TD05">Nota di Debito</option>
                                    <option value="TD06">Parcella</option>
                                    <option value="TD07">Fattura semplificata</option>
                                    <option value="TD08">Nota di credito semplificata</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label>Numero <b style="color:red">*</b></label>
                                <input type="text" name="numero" class="form-control" id="numero" placeholder="Numero" value="<?php echo $num_fattura ?>/<?php echo date('Y') ?>">
                            </div>

                            <div class="col-sm-3">
                                <label>Data <b style="color:red">*</b></label>
                                <input type="text" name="data" class="form-control date-picker" id="data" placeholder="data" value="<?php echo date('d/m/Y') ?>" required>
                            </div>

                            <div class="col-sm-12">
                                <label>Esigibilità IVA <b style="color:red">*</b></label>
                                <select name="esigibilita_iva" class="form-control select2" style="width:100%">
                                    <option value="I" selected>IVA ad esigibilità immediata</option>
                                    <option value="D">IVA ad esigibilità differita</option>
                                    <option value="S">Scissione dei pagamenti</option>
                                </select>
                            </div>

                            <!-- Precompila i campi con i valori del documento dotes che stai evadendo -->
                            <div class="col-sm-6">
                                <label>Nominativo <b style="color:red">*</b></label>
                                <input type="text" name="nominativo" class="form-control" id="nominativo" placeholder="Nominativo" value="<?php echo $d->ragione_sociale; ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label>CF</label>
                                <input type="text" name="cf" class="form-control" id="cf" placeholder="CF" value="<?php echo $d->partita_iva; ?>">
                            </div>

                            <div class="col-sm-6">
                                <label>P.IVA</label>
                                <input type="text" name="piva" class="form-control" id="piva" placeholder="P.IVA" value="<?php echo $d->partita_iva; ?>">
                            </div>

                            <div class="col-sm-6">
                                <label>Indirizzo <b style="color:red">*</b></label>
                                <input type="text" name="indirizzo" class="form-control" id="indirizzo" placeholder="Indirizzo" value="<?php echo $d->indirizzo; ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label>CAP <b style="color:red">*</b></label>
                                <input type="text" name="cap" class="form-control" id="cap" placeholder="CAP" value="<?php echo $d->cap; ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label>Città <b style="color:red">*</b></label>
                                <input type="text" name="citta" class="form-control" id="citta" placeholder="Città" value="<?php echo $d->comune; ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label>Provincia <b style="color:red">*</b></label>
                                <input type="text" name="provincia" class="form-control" id="provincia" placeholder="Provincia" value="<?php echo $d->provincia; ?>" required maxlength="2">
                            </div>

                            <div class="col-sm-6">
                                <label>Nazione <b style="color:red">*</b></label>
                                <input type="text" name="nazione" class="form-control" id="nazione" placeholder="Nazione" value="IT" required>
                            </div>

                            <div class="col-sm-6">
                                <label>SDI <b style="color:red">*</b></label>
                                <input type="text" name="sdi" class="form-control" id="sdi" placeholder="SDI" value="<?php echo $d->sdi; ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label>PEC</label>
                                <input type="email" name="pec" class="form-control" id="pec" placeholder="PEC" value="<?php echo $d->pec; ?>">
                            </div>

                            <div class="col-sm-6">
                                <label>Condizioni Pagamento <b style="color:red">*</b></label>
                                <select name="condizioni_pagamento" class="form-control select2" required style="width:100%">
                                    <option value="TP01">Pagamento a rate (TP01)</option>
                                    <option value="TP02" selected>Pagamento Completo (TP02)</option>
                                    <option value="TP03">Anticipo (TP03)</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label>Tipologia Pagamento <b style="color:red">*</b></label>
                                <select name="tipologia_pagamento" class="form-control select2" required style="width:100%">
                                    <option value="MP01">Contanti</option>
                                    <option value="MP02">Assegno</option>
                                    <option value="MP03">Assegno Circolare</option>
                                    <option value="MP04">Contanti Presso Tesoreria</option>
                                    <option value="MP05" selected>Bonifico</option>
                                    <option value="MP06">Vaglia Cambiario</option>
                                    <option value="MP07">Bollettino Bancario</option>
                                    <option value="MP08">Carta di Pagamento</option>
                                    <option value="MP09">RID</option>
                                    <option value="MP10">RID utente</option>
                                    <option value="MP11">RID veloce</option>
                                    <option value="MP12">RIBA</option>
                                    <option value="MP13">MAV</option>
                                    <option value="MP14">Quietanza Erario</option>
                                    <option value="MP15">Giroconto su conti di contabilità speciale</option>
                                    <option value="MP16">Domiciliazione Bancaria</option>
                                    <option value="MP17">Domiciliazione postale</option>
                                    <option value="MP18">Bollettino di c/c postale</option>
                                    <option value="MP19">SEPA Direct Debit</option>
                                    <option value="MP20">SEPA Direct Debit CORE</option>
                                    <option value="MP21">SEPA Direct Debit B2B</option>
                                    <option value="MP22">Trattenuta su somme già riscosse</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label>Stato <b style="color:red">*</b></label>
                                <select name="stato" class="form-control select2" required style="width:100%">
                                    <option value="0">Da Inviare</option>
                                    <option value="1">Inviato</option>
                                    <option value="2">Scartato</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="ordine_flag" id="ordineFlag">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="location.reload();">Chiudi</button>
                    <input type="hidden" name="id_dotes_originale" value="<?php echo $d->id; ?>">
                    <input type="submit" class="btn btn-success" value="Evadi Quantità">
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<script>
    // Funzione JavaScript per aggiornare il flag del documento e mostrare/nascondere campi specifici per il tipo di documento scelto
    function toggleFatturaFields() {
        const select = document.getElementById('documentoSelect');
        const selectedValue = select.value;
        const fatturaFields = document.getElementById('fatturaFields');
        const provincia = document.getElementById('provincia');
        const selectedOption = select.options[select.selectedIndex];
        const ordine = selectedOption.getAttribute('data-ordine');

        // Usa una selezione più specifica per ottenere il form corrente
        const form = document.querySelector('#form_evadi_' + select.closest('.modal').id.split('_')[2]);

        if (selectedValue === 'fattura') {
            console.log('entra in fattura');
            fatturaFields.style.display = 'flex'; // Mostra i campi per la fattura
            form.setAttribute('action', "{{ route('creafatturaevasa') }}"); // Imposta la nuova azione del form
        } else {
            fatturaFields.style.display = 'none'; // Nascondi i campi per la fattura
            document.getElementById('ordineFlag').value = ordine;
            form.setAttribute('action', "{{ route('evadi.quantita') }}"); // Ripristina l'azione del form originale

            // Rimuovi tutti gli input di fattura per evitare che diano fastidio
            const fatturaInputs = fatturaFields.querySelectorAll('input, select, textarea');
            fatturaInputs.forEach(input => {
                input.remove();
            });
        }
    }


    /*function updateOrdineInput() {
        const select = document.getElementById('documentoSelect');
        const selectedOption = select.options[select.selectedIndex];
        const ordine = selectedOption.getAttribute('data-ordine');
        document.getElementById('ordineFlag').value = ordine;
    }
*/
    function cambia_rif(itemId) {
        const codiceIvaSelect = document.getElementById(`codice_iva_${itemId}`);
        const rifNormativoInput = document.getElementById(`rif_normativo_${itemId}`);

        // Logica di esempio: se cambia il codice IVA, cambia il valore del campo Rif. Normativo
        if (codiceIvaSelect.value === 'N1') {
            rifNormativoInput.value = 'Escluso Articolo 15';
        } else if (codiceIvaSelect.value === 'N4') {
            rifNormativoInput.value = 'Esente Articolo 10';
        } else {
            rifNormativoInput.value = ''; // Reset se non è N1 o N4
        }
    }
</script>
