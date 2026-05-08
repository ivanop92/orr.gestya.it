<?php foreach($articoli as $a){ ?>
<form method="post" enctype="multipart/form-data">
        <div class="modal fade" id="modal_carica_<?php echo $a->id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Carica Materiale <?php echo $a->descrizione ?></h4>
                    </div>
                    <div class="modal-body row">


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Causale<b style="color:red">*</b></label>
                                <input type="text" class="form-control" name="causale" value="Carico di Magazzino" placeholder="Causale" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Magazzino<b style="color:red">*</b></label>
                                <select name="id_mg" class="form-control" required>
                                    <?php foreach($mg as $m){ ?>
                                        <option value="<?php echo $m->id ?>"><?php echo $m->codice_magazzino.' '.$m->descrizione ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Quantità<b style="color:red">*</b></label>
                                <input type="number" class="form-control" name="qta" value="0" placeholder="Quantità" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lotto</label>
                                <input type="text" class="form-control" name="lotto" value="" placeholder="Inserisci un lotto se previsto">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Scadenza</label>
                                <input type="date" class="form-control" name="scadenza_lotto" value="" placeholder="Inserisci un scadenza">
                            </div>
                        </div>


                        <div class="clearfix"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_articolo" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-primary pull-right" name="carica_materiale" value="Carica" style="margin-right:5px;">
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{--<form method="post" enctype="multipart/form-data">
        <div class="modal fade" id="modal_scarica_<?php echo $a->id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Scarica Materiale <?php echo $a->descrizione ?></h4>
                    </div>
                    <div class="modal-body row">


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Causale<b style="color:red">*</b></label>
                                <input type="text" class="form-control" name="causale" value="Scarico di Magazzino" placeholder="Causale" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Quantità<b style="color:red">*</b></label>
                                <input type="number" class="form-control" name="qta" value="0" placeholder="Quantità" required>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_articolo" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-primary pull-right" name="scarica_materiale" value="Carica" style="margin-right:5px;">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form method="post" enctype="multipart/form-data">
        <div class="modal fade" id="modal_rettifica_<?php echo $a->id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Rettifica Materiale <?php echo $a->descrizione ?></h4>
                    </div>
                    <div class="modal-body row">


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Causale<b style="color:red">*</b></label>
                                <input type="text" class="form-control" name="causale" value="Rettifica di Magazzino" placeholder="Causale" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Quantità<b style="color:red">*</b></label>
                                <input type="number" class="form-control" name="qta" value="0" placeholder="Quantità" required>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id_articolo" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-primary pull-right" name="rettifica_materiale" value="Rettifica" style="margin-right:5px;">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form method="post" enctype="multipart/form-data">
        <div class="modal fade" id="modal_movimenti_<?php echo $a->id ?>">
            <div class="modal-dialog" style="min-width:50%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Movimenti Magazzino Materiale <?php echo $a->descrizione ?></h4>
                    </div>
                    <div class="modal-body">

                        <table class="table table-bordered table-hover" border="0" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:200px;">Data</th>
                                <th>Causale</th>
                                <th style="width:50px">Qta</th>
                                <th style="width:50px">CAR</th>
                                <th style="width:50px">SCA</th>
                                <th style="width:50px">RET</th>
                            </tr>
                            </thead>

                            <tbody>
                                <?php foreach($a->mgmov as $mgm){ ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i:s',strtotime($mgm->datamov)) ?></td>
                                <td><?php echo $mgm->causale ?></td>
                                <td><?php echo $mgm->qta ?></td>
                                <td><?php echo $mgm->car ?></td>
                                <td><?php echo $mgm->sca ?></td>
                                <td><?php echo $mgm->ret ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td style="font-size:22px;"><b>Totale</b></td>
                                <td style="font-size:22px;"><b><?php echo $a->giacenza ?></b></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            </tfoot>
                        </table>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    </form>--}}

<?php } ?>
