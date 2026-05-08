@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bandi Archiviati</h4>

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
                            <h5 class="card-title mb-0">Bandi Archiviati</h5>
                        </div>
                        <div class="card-body">
                            <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Descrizione</th>
                                    <th style="max-width: 50px; min-width: 50px">Allegati</th>
                                    <th style="max-width: 300px; min-width: 300px">Allegati Richiesti</th>
                                    <th>Clienti</th>
                                    <th style="width:120px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($bandi_archiviati as $ba){ ?>
                                <tr>
                                    <td><?php echo $ba->titolo ?></td>
                                    <td><p class="btn btn-light btn-sm m-0" onclick="show_descrizione('<?php echo $ba->descrizione ?>')" >Vai alla Descrzione del Bando</p></td>
                                    <td>
                                        <?php if($ba->allegati != NULL){ ?>
                                        <a class="btn btn-danger btn-sm" target="_blank" href="<?php echo URL::asset($ba->allegati) ?>">Allegato</a>
                                        <?php }else{ ?>
                                            <p style="text-align: center; font-weight: bold">-</p>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php $array_allegati = (explode(',', $ba->id_allegati)) ?>
                                        <?php $allegati = DB::table('bandi_allegati')->whereIn('id', $array_allegati)->get() ?>
                                        <div style="display: flex; flex-wrap: wrap">
                                            <?php foreach ($allegati as $all){ ?>
                                                <p style="margin: 0; margin-right: 10px; margin-bottom: 7px" class="btn btn-primary btn-sm"  href="#">{{$all->descrizione}}</p>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                            <?php $array_utenti = (explode(',', $ba->id_clienti)) ?>
                                            <?php $utenti_selezionati = DB::table('utenti')->whereIn('id', $array_utenti)->get() ?>
                                        <div style="display: flex; flex-wrap: wrap">
                                                <?php foreach ($utenti_selezionati as $ute){ ?>
                                            <a target="_blank" href="{{asset('bandi/' . $ute->token_utente_per_bando . '/' . $ba->token_bando)}}" style="display: block; margin: 0; margin-right: 10px; margin-bottom: 7px" class="btn btn-primary btn-sm"  href="#">{{$ute->ragione_sociale}}</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div style="display: flex; justify-content: space-between">
                                                <form method="post" onsubmit="return confirm('Vuoi Estrarre dal archivio questo bando ?')">
                                                    <input type="hidden" name="id" value="<?php echo $ba->id ?>">
                                                    <input type="hidden" name="estrai" value="<?php echo $ba->id ?>">
                                                    <button style="margin-left:5px;" type="submit" class="btn btn-sm btn-success"><i class="ri-inbox-unarchive-line"></i></button>
                                                </form>
                                            </div>
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

@include('default.common.footer')
