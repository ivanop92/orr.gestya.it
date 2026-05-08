@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Canoni di Manutenzione</h4>

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
                        <h5 class="card-title mb-0">Canoni</h5>
                    </div>
                    <div class="card-body">
                        <table class="table nowrap align-middle" style="width:100%">
                            <thead>
                            <tr>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Descrizione</th>
                                <th>Allegato</th>
                                <th>Canone</th>
                                <th>Data Canone</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $totale_canone = 0; ?>
                            <?php foreach($preventivi as $p){ $totale_canone += $p->canone;   ?>
                            <tr>

                                <td>
                                    <?php echo date('d/m/Y',strtotime($p->data)) ?>
                                </td>
                                <td><a href="<?php echo URL::asset('admin/dettaglio_utente/'.$p->id_utente) ?>" target="_blank"><?php echo $p->ragione_sociale ?></a></td>
                                <td><?php echo $p->descrizione ?></td>
                                <td>
                                    <?php if($p->allegato != ''){ ?>
                                    <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo URL::asset($p->allegato) ?>">Allegato</a>
                                    <?php } ?>
                                </td>
                                <td>&euro;<?php echo number_format($p->canone,2,'.','') ?></td>
                                <td><?php echo date('d/m/Y',strtotime($p->data_canone)) ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><h5>&euro;<?php echo number_format($totale_canone,2,'.','') ?></h5></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->

        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>


@include('default.common.footer')
