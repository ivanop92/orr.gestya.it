<?php foreach($articoli as $a){ ?>
<form method="post" enctype="multipart/form-data">
        <div class="modal fade modal-lg" id="modal_mgmov_<?php echo $a->id ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Movimenti Magazzino <?php echo $a->descrizione ?></h4>
                    </div>
                    <div class="modal-body row">

                        <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:200px;">Data</th>
                                <th>Causale</th>
                                <th style="width:150px">Qta</th>
                                <th style="width:50px">Lotto</th>
                                <th style="width:150px">Scadenza</th>
                            </tr>
                            </thead>

                            <tbody>
                                <?php foreach($a->mgmov as $mgm){ ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i:s',strtotime($mgm->datamov)) ?></td>
                                <td><?php echo $mgm->causale ?></td>
                                <td><?php echo $mgm->qta ?></td>
                                <td><?php echo $mgm->lotto ?></td>
                                <td><?php echo ($mgm->scadenza_lotto != '')?date('d/m/Y',strtotime($mgm->scadenza_lotto)):'' ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td style="font-size:22px;"><b>Totale</b></td>
                                <td style="font-size:20px;"><b><?php echo number_format($a->giacenza,2,'.','') ?> <?php echo $a->um ?></b></td>
                                <td></td>
                                <td></td>

                            </tr>
                            </tfoot>
                        </table>

                        <h2>Giacenze Per Lotti</h2>
                        <table id="scroll-horizontal" class="table table-bordered table-hover datatable" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width:200px;">Lotto</th>
                                <th>Scadenza</th>
                                <th style="width:150px">Qta</th>
                            </tr>
                            </thead>

                            <tbody>
                                <?php foreach($a->giacenze_lotti as $glot){ ?>
                            <tr>
                                <td><?php echo $glot->lotto ?></td>
                                <td><?php echo ($glot->scadenza_lotto != '')?date('d/m/Y',strtotime($glot->scadenza_lotto)):'' ?></td>
                                <td><?php echo $glot->giacenza ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>


                        <div class="clearfix"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

<?php } ?>
