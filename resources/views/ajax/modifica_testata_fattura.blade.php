<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_modifica_testata">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modifica Fattura <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-6">
                        <label>Tipologia <b style="color:red">*</b></label>
                        <select name="tipologia_documento" class="form-control" style="width:100%">
                            <option value="TD01" <?php echo ($f->tipologia_documento == 'TD01')?'selected':'' ?>>Fattura</option>
                            <option value="TD24" <?php echo ($f->tipologia_documento == 'TD24')?'selected':'' ?>>Fattura Differita</option>
                            <option value="TD02" <?php echo ($f->tipologia_documento == 'TD02')?'selected':'' ?>>Acconto/Anticipo su fattura</option>
                            <option value="TD03" <?php echo ($f->tipologia_documento == 'TD03')?'selected':'' ?>>Acconto/Anticipo su parcella</option>
                            <option value="TD04" <?php echo ($f->tipologia_documento == 'TD04')?'selected':'' ?>>Nota di Credito</option>
                            <option value="TD05" <?php echo ($f->tipologia_documento == 'TD05')?'selected':'' ?>>Nota di Debito</option>
                            <option value="TD06" <?php echo ($f->tipologia_documento == 'TD06')?'selected':'' ?>>Parcella</option>
                            <option value="TD07" <?php echo ($f->tipologia_documento == 'TD07')?'selected':'' ?> >Fattura semplificata</option>
                            <option value="TD08" <?php echo ($f->tipologia_documento == 'TD08')?'selected':'' ?>>Nota di credito semplificata</option>
                        </select>
                    </div>


                    <div class="col-sm-3">
                        <label>Numero <b style="color:red">*</b></label>
                        <input type="text" name="numero" class="form-control" id="numero" placeholder="Numero" value="<?php echo $f->numero ?>/<?php echo $f->anno ?>">
                    </div>

                    <div class="col-sm-3">
                        <label>Data <b style="color:red">*</b></label>
                        <input type="text" name="data" class="form-control date-picker" id="data" placeholder="data" value="<?php echo date('d/m/Y',strtotime($f->data)) ?>" required>
                    </div>


                    <div class="col-sm-12">
                        <label>Esigibilità IVA <b style="color:red">*</b></label>
                        <select name="esigibilita_iva" class="form-control" style="width:100%">
                            <option value="I" <?php echo ($f->esigibilita_iva == 'I')?'selected':'' ?>>IVA ad esigibilità immediata</option>
                            <option value="D" <?php echo ($f->esigibilita_iva == 'D')?'selected':'' ?>>IVA ad esigibilità differita</option>
                            <option value="S" <?php echo ($f->esigibilita_iva == 'S')?'selected':'' ?>>scissione dei pagamenti<option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label>Nominativo <b style="color:red">*</b></label>
                        <input type="text" name="nominativo" class="form-control" id="nominativo" placeholder="Nominativo" value="<?php echo $f->nominativo ?>" required>
                    </div>


                    <div class="col-sm-6">
                        <label>CF</label>
                        <input type="text" name="cf" class="form-control" id="cf" placeholder="CF" value="<?php echo $f->cf ?>">
                    </div>

                    <div class="col-sm-6">
                        <label>P.IVA</label>
                        <input type="text" name="piva" class="form-control" id="piva" placeholder="P.IVA" value="<?php echo $f->piva ?>">
                    </div>

                    <div class="col-sm-6">
                        <label>Indirizzo <b style="color:red">*</b></label>
                        <input type="text" name="indirizzo" class="form-control" id="indirizzo" placeholder="Indirizzo" value="<?php echo $f->indirizzo ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label>CAP <b style="color:red">*</b></label>
                        <input type="text" name="cap" class="form-control" id="cap" placeholder="CAP" value="<?php echo $f->cap ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label>Città <b style="color:red">*</b></label>
                        <input type="text" name="citta" class="form-control" id="citta" placeholder="citta" value="<?php echo $f->citta ?>" required>
                    </div>


                    <div class="col-sm-6">
                        <label>Provincia <b style="color:red">*</b></label>
                        <input style="text-transform: uppercase" type="text" name="provincia" class="form-control" id="provincia" placeholder="Provincia" value="<?php echo $f->provincia ?>" required maxlength="2" >
                    </div>

                    <div class="col-sm-6">
                        <label>Nazione <b style="color:red">*</b></label>
                        <input type="text" name="nazione" class="form-control" id="nazione" placeholder="Nazione" value="<?php echo $f->nazione ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label>SDI <b style="color:red">*</b></label>
                        <input type="text" name="sdi" class="form-control" id="sdi" placeholder="SDI" value="<?php echo $f->sdi ?>" required>
                    </div>

                    <div class="col-sm-6">
                        <label>PEC</label>
                        <input type="email" name="pec" class="form-control" id="pec" placeholder="PEC" value="<?php echo $f->pec ?>">
                    </div>

                    <div class="col-sm-6">
                        <label>Condizioni Pagamento <b style="color:red">*</b></label>
                        <select name="condizioni_pagamento" class="form-control" required style="width:100%">
                            <option value="TP01" <?php echo ($f->condizioni_pagamento == 'TP01')?'selected':'' ?>>Pagamento a rate (TO01)</option>
                            <option value="TP02" <?php echo ($f->condizioni_pagamento == 'TP02')?'selected':'' ?>>Pagamento Completo (TP02)</option>
                            <option value="TP03" <?php echo ($f->condizioni_pagamento == 'TP03')?'selected':'' ?>>Anticipo (TP03)</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-sm-6">
                        <label>Tipologia Pagamento <b style="color:red">*</b></label>
                        <select name="tipologia_pagamento" class="form-control" required style="width:100%">
                            <option value="MP01" <?php echo ($f->tipologia_pagamento == 'MP01')?'selected':'' ?>>Contanti</option>
                            <option value="MP02" <?php echo ($f->tipologia_pagamento == 'MP02')?'selected':'' ?>>Assegno</option>
                            <option value="MP03" <?php echo ($f->tipologia_pagamento == 'MP03')?'selected':'' ?>>Assegno Circolare</option>
                            <option value="MP04" <?php echo ($f->tipologia_pagamento == 'MP04')?'selected':'' ?>>Contanti Presso Tesoreria</option>
                            <option value="MP05" <?php echo ($f->tipologia_pagamento == 'MP05')?'selected':'' ?>>Bonifico</option>
                            <option value="MP06" <?php echo ($f->tipologia_pagamento == 'MP06')?'selected':'' ?>>Vaglia Cambiario</option>
                            <option value="MP07" <?php echo ($f->tipologia_pagamento == 'MP07')?'selected':'' ?>>Bollettino Bancario</option>
                            <option value="MP08" <?php echo ($f->tipologia_pagamento == 'MP08')?'selected':'' ?>>Carta di Pagamento</option>
                            <option value="MP09" <?php echo ($f->tipologia_pagamento == 'MP09')?'selected':'' ?>>RID</option>
                            <option value="MP10" <?php echo ($f->tipologia_pagamento == 'MP10')?'selected':'' ?>>RID utente</option>
                            <option value="MP11" <?php echo ($f->tipologia_pagamento == 'MP11')?'selected':'' ?>>RID veloce</option>
                            <option value="MP12" <?php echo ($f->tipologia_pagamento == 'MP12')?'selected':'' ?>>RIBA</option>
                            <option value="MP13" <?php echo ($f->tipologia_pagamento == 'MP13')?'selected':'' ?>>MAV</option>
                            <option value="MP14" <?php echo ($f->tipologia_pagamento == 'MP14')?'selected':'' ?>>Quietanza Erario</option>
                            <option value="MP15" <?php echo ($f->tipologia_pagamento == 'MP15')?'selected':'' ?>>Giroconto su conti di contabilità speciale</option>
                            <option value="MP16" <?php echo ($f->tipologia_pagamento == 'MP16')?'selected':'' ?>>Domiciliazione Bancaria</option>
                            <option value="MP17" <?php echo ($f->tipologia_pagamento == 'MP17')?'selected':'' ?>>Domiciliazione postale</option>
                            <option value="MP18" <?php echo ($f->tipologia_pagamento == 'MP18')?'selected':'' ?>>bollettino di c/c postale</option>
                            <option value="MP19" <?php echo ($f->tipologia_pagamento == 'MP19')?'selected':'' ?>>SEPA Direct Debit</option>
                            <option value="MP20" <?php echo ($f->tipologia_pagamento == 'MP20')?'selected':'' ?>>SEPA Direct Debit CORE</option>
                            <option value="MP21" <?php echo ($f->tipologia_pagamento == 'MP21')?'selected':'' ?>>SEPA Direct Debit B2B</option>
                            <option value="MP22" <?php echo ($f->tipologia_pagamento == 'MP22')?'selected':'' ?>>Trattenuta su somme già riscosse</option>

                        </select>
                    </div>


                    <div class="col-sm-6">
                        <label>Split Payment<b style="color:red">*</b></label>
                        <select name="split_payment" class="form-control" required style="width:100%">
                            <option value="0" <?php echo ($f->split_payment == 0)?'selected':'' ?>>NO</option>
                            <option value="1" <?php echo ($f->split_payment == 1)?'selected':'' ?>>SI</option>
                        </select>
                    </div>



                    <div class="col-sm-6">
                        <label>Inviato<b style="color:red">*</b></label>
                        <select name="stato" class="form-control" required style="width:100%">
                            <option value="0" <?php echo ($f->stato == 0)?'selected':'' ?>>Da Inviare</option>
                            <option value="1" <?php echo ($f->stato == 1)?'selected':'' ?>>Inviato</option>
                            <option value="2" <?php echo ($f->stato == 2)?'selected':'' ?>>Scartato</option>
                        </select>
                    </div>


                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?php echo $f->id ?>">
                    <input type="submit" class="btn btn-primary" name="modifica_testata" value="Modifica Testata">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_nota_credito">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Crea Nota Credito <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-12">
                        <label>Descrizione</label>
                        <input type="text" name="descrizione" class="form-control" id="descrizione" placeholder="Descrizione" value="Nota Credito per Errata Imputazione IVA Fattura <?php echo $f->numero.'/'.$f->anno ?>">
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?php echo $f->id ?>">
                    <input type="submit" class="btn btn-primary" name="crea_nota_credito" value="Crea Nota Credito">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>


<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi_allegato">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Allegato <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-6">
                        <label>Allegato</label>
                        <input type="file" name="allegato" class="form-control" id="allegato" placeholder="Allegato" required>
                    </div>


                    <div class="col-sm-6">
                        <label>Nome Allegato</label>
                        <input type="text" name="nome_allegato" class="form-control" id="nome_allegato" placeholder="Nome Allegato" value="<?php echo $f->nome_allegato ?>">
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?php echo $f->id ?>">
                    <input type="submit" class="btn btn-primary" name="aggiungi_allegato" value="Aggiungi Allegato">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi_allegato2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Allegato <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-6">
                        <label>Allegato</label>
                        <input type="file" name="allegato2" class="form-control" id="allegato" placeholder="Allegato" required>
                    </div>


                    <div class="col-sm-6">
                        <label>Nome Allegato</label>
                        <input type="text" name="nome_allegato2" class="form-control" id="nome_allegato" placeholder="Nome Allegato" value="<?php echo $f->nome_allegato2 ?>">
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" value="<?php echo $f->id ?>">
                    <input type="submit" class="btn btn-primary" name="aggiungi_allegato2" value="Aggiungi Allegato">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi_riga">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Riga <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-12">
                        <label>Descrizione <b style="color:red">*</b></label>
                        <input type="text" name="descrizione" class="form-control" id="descrizione" placeholder="Inserisci una Descrizione" value="" required>
                    </div>

                    <div class="col-sm-4">
                        <label>Qta <b style="color:red">*</b></label>
                        <input type="number" min="0" step="1" name="qta" class="form-control" id="qta" placeholder="qta" value="1" required onkeyup="cambia_totale()">
                    </div>

                    <div class="col-sm-4">
                        <label>UM <b style="color:red">*</b></label>
                        <input type="text" name="um" class="form-control" id="um" placeholder="um" value="NR" required>
                    </div>

                    <div class="col-sm-4">
                        <label>Prezzo Unitario <b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="pu" class="form-control" id="pu" placeholder="Prezzo Unitario" value="0" required onkeyup="cambia_totale()">
                    </div>


                    <div class="col-sm-6">
                        <label>Prezzo Totale <b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="pt" class="form-control" id="pt" placeholder="Prezzo Totale" value="0" required readonly>
                    </div>

                    <div class="col-sm-6">
                        <label>IVA %<b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="iva" class="form-control" id="iva" placeholder="IVA" value="22" required>
                    </div>

                    <div class="col-sm-12">
                        <label>Codice IVA<b style="color:red">*</b></label>
                        <select id="codice_iva" name="codice_iva" class="form-control" style="width:100%" onchange="cambia_rif(0)">
                            <option value="">Nessuna Esenzione</option>
                            <option value="N1">N1. operazioni escluse ex articolo 15</option>
                            <option value="N2">N2. operazioni non soggette</option>
                            <option value="N4">N4. operazioni esenti articolo 10</option>
                            <option value="N5">N5. Regime al margine</option>
                            <option value="N6">N6. operazioni in “Reverse Charge” articolo 17 c.6 lett. a-ter</option>
                            <option value="N7">N7. iva assolta in altro stato dell’Unione Europea</option>
                        </select>
                    </div>


                    <div class="col-sm-12">
                        <label>Rif. Normativo</label>
                        <input type="text" name="rif_normativo" class="form-control" id="rif_normativo" placeholder="rif_normativo">
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id_testata" value="<?php echo $f->id ?>">
                    <input type="submit" class="btn btn-primary" name="aggiungi_riga" value="Aggiungi Riga">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_mostra_righe">
        <div class="modal-dialog modal-lg" style="max-width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Righe Fattura <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <table class="table table-bordered table-hover dataTable" style="width:100%">
                        <thead>

                        <tr>
                            <th class="no-sort" style="width:600px;">Descrizione</th>
                            <th class="no-sort" style="width:100px;">Dati</th>
                            <th class="no-sort" style="width:150px;">Totale</th>
                            <th class="no-sort" style="width:150px;"></th>
                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach($f->righe as $r){ ?>

                        <tr style="width:100%" class="riga riga_<?php echo $f->id ?>">
                            <td>
                                <?php echo $r->descrizione ?>
                                <?php if($r->rif_normativo != ''){ ?>
                                <br>Rif: <b><?php echo $r->rif_normativo; ?></b>
                                <?php } ?>
                                <?php if($r->id_pratica != 0){ ?>
                                <br>Pratica: <a target="_blank" href="<?php echo URL::asset('modifica_pratica/'.$r->id_pratica) ?>"><?php echo $r->id_pratica ?></a>
                                <?php } ?>
                            </td>
                            <td>&euro;<?php echo $r->imponibile ?> </td>
                            <td>IVA: &euro;<?php echo $r->imposta ?> (<?php echo $r->iva ?>%) <?php echo ($r->codice_iva != '')?'('.$r->codice_iva.')':'' ?></td>
                            <td>
                                <a style="float:left;" onclick="modifica_riga(<?php echo $r->id ?>)"  class="btn btn-primary btn-sm">MODIFICA</a>
                                <form method="post" onsubmit="return confirm('vuoi eliminare questa riga ?')" style="float:left;margin-left:5px;">
                                    <input type="hidden" name="id" value="<?php echo $r->id ?>">
                                    <input type="hidden" name="id_testata" value="<?php echo $f->id ?>">
                                    <input type="submit" name="elimina_riga" value="ELIMINA" style="float:left;margin-left:5px;" class="btn btn-danger btn-sm">
                                </form>
                            </td>
                        </tr>

                        <?php } ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
</form>

<?php foreach($f->righe as $r){ ?>
<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_modifica_riga_<?php echo $r->id ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modifica Riga N. <?php echo $r->id ?> - <?php echo $f->numero.'/'.$f->anno ?></h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-12">
                        <label>Descrizione <b style="color:red">*</b></label>
                        <input type="text" name="descrizione" class="form-control" id="descrizione_<?php echo $r->id ?>" placeholder="Inserisci una Descrizione" value="<?php echo $r->descrizione ?>" required>
                    </div>

                    <div class="col-sm-4">
                        <label>Qta <b style="color:red">*</b></label>
                        <input type="number" min="0" step="1" name="qta" class="form-control" id="qta_<?php echo $r->id ?>" placeholder="qta" value="<?php echo $r->qta ?>" required onkeyup="modifica_totale(<?php echo $r->id ?>)">
                    </div>

                    <div class="col-sm-4">
                        <label>UM <b style="color:red">*</b></label>
                        <input type="text" name="um" class="form-control" id="um_<?php echo $r->id ?>" placeholder="um" value="<?php echo $r->um ?>" required>
                    </div>

                    <div class="col-sm-4">
                        <label>Prezzo Unitario <b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="pu" class="form-control" id="pu_<?php echo $r->id ?>" placeholder="Prezzo Unitario" value="<?php echo $r->pu ?>" required onkeyup="modifica_totale(<?php echo $r->id ?>)">
                    </div>

                    <div class="col-sm-6">
                        <label>Prezzo Totale <b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="pt" class="form-control" id="pt_<?php echo $r->id ?>" placeholder="Prezzo Totale" value="<?php echo $r->pt ?>" required readonly>
                    </div>

                    <div class="col-sm-6">
                        <label>IVA %<b style="color:red">*</b></label>
                        <input type="number" min="0" step="0.01" name="iva" class="form-control" id="iva_<?php echo $r->id ?>" placeholder="IVA" value="<?php echo $r->iva ?>" required>
                    </div>

                    <div class="col-sm-12">
                        <label>Codice IVA<b style="color:red">*</b></label>
                        <select id="codice_iva_<?php echo $r->id ?>" name="codice_iva" class="form-control" style="width:100%" onchange="cambia_rif(<?php echo $r->id ?>)">
                            <option value="">Nessuna Esenzione</option>
                            <option value="N1" <?php echo ($r->codice_iva == 'N1')?'selected':'' ?>>N1. operazioni escluse ex articolo 15</option>
                            <option value="N2" <?php echo ($r->codice_iva == 'N2')?'selected':'' ?>>N2. operazioni non soggette</option>
                            <option value="N3" <?php echo ($r->codice_iva == 'N3')?'selected':'' ?>>N3. operazioni non imponibili</option>
                            <option value="N4" <?php echo ($r->codice_iva == 'N4')?'selected':'' ?>>N4. operazioni esenti articolo 10</option>
                            <option value="N5" <?php echo ($r->codice_iva == 'N5')?'selected':'' ?>>N5. operazioni nel regime del margine</option>
                            <option value="N6" <?php echo ($r->codice_iva == 'N6')?'selected':'' ?>>N6. operazioni in “Reverse Charge” articolo 17 c.6 lett. a-ter</option>
                            <option value="N7" <?php echo ($r->codice_iva == 'N7')?'selected':'' ?>>N7. iva assolta in altro stato dell’Unione Europea</option>
                        </select>
                    </div>


                    <div class="col-sm-12">
                        <label>Rif. Normativo</label>
                        <input type="text" name="rif_normativo" class="form-control" id="rif_normativo_<?php echo $r->id ?>" placeholder="rif_normativo_<?php echo $r->id ?>" value="<?php echo $r->rif_normativo ?>">
                    </div>


                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id_testata" value="<?php echo $f->id ?>">
                    <input type="hidden" name="id" value="<?php echo $r->id ?>">
                    <input type="submit" class="btn btn-primary" name="modifica_riga" value="Modifica Riga">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>
<?php } ?>


<script type="text/javascript">

    $(function () {

        $('.datatable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "scrollX": true,

            "oLanguage": {
                "sLengthMenu": "<span> Risultati :</span> _MENU_",
                "oPaginate": { "sFirst": "Primo", "sLast": "Ultimo", "sNext": ">", "sPrevious": "<" }
            }
        });


        $('.select2').select2();

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $('.date-picker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'it'
        });

    });

</script>
