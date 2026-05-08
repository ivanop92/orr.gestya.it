<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Invio Dati | {{$utente->ragione_sociale}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Dashboard CRM Ingenia SRL" name="description" />
    <meta content="Themesbrand" name="author" /><link rel="shortcut icon" href="/icona.png">
    <link href="/default/assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="/default/assets/js/layout.js"></script>
    <link href="/default/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>
<body>
    <div class="container-fluid px-5">
        <div style="display: flex; justify-content: center; margin-bottom: 50px; margin-top: 40px">
            <?php if($bando->immagine_bando != null){?>
                <img style="width:200px;" src="{{asset($bando->immagine_bando)}}">
            <?php }else{ ?>
                <img style="width:200px;" src="https://crm.ingenia.cloud/logo.png">
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-12 d-md-flex justify-content-md-between" >
                <h1 class="mb-md-0 mb-3 text-md-start text-center">Benvenuto {{$utente->ragione_sociale}}</h1>
                <h4 style="margin: 0; display: flex; flex-direction: column; align-items: center">
                    <p style="margin-bottom: 5px"><i class="ri-arrow-down-line"></i> Decreto <i class="ri-arrow-down-line"></i></p>
                    <a class="btn btn-primary" target="_blank" href="{{asset($bando->decreto)}}">
                        {{$bando->titolo}}
                    </a>
                </h4>
            </div>
        </div>
        <h3 style="margin-top: 3rem">Inserisci i seguenti documenti...</h3>
        <?php foreach ($bandi_allegati as $bd){ ?>
            <?php $file_caricato = DB::table('bandi_allegati_utenti')->where('id_bando', $bando->id)->where('id_allegato', $bd->id)->where('id_cliente', $utente->id)->first();  ?>
            <div class="mt-3 mb-3" <?php echo ($file_caricato != null) ? 'style="position: relative; padding: 15px; border: 1px solid black; border-radius: 10px;"' : ' ' ?> >
                <?php if($file_caricato != null){ ?>
                    <i style="font-size: 30px; position: absolute; transform: translate(20px, -25px); top: 0; right: 0; color: darkgreen" class="ri-checkbox-circle-fill"></i>
                <?php } ?>
                <form id="invio_dati" method="post" style="margin-top: 30px" enctype="multipart/form-data">
                    <div class="row d-flex justify-content-between">
                        <div class="<?php echo ($file_caricato != null) ? 'col-md-7' : 'col-md-9' ?> d-md-flex">
                            <div style="width: 90%; margin-right: 10px">
                                <label style="font-size: 16px; margin: 0; font-weight: bold" >{{$bd->descrizione}} <b style="color:red">*</b><span style="font-size: 12px; font-weight: normal">(In formato: {{$bd->formati}})</span></label>
                                <input type="file" class="form-control" name="path_allegato" accept="{{$bd->formati}}" <?php if($file_caricato === null){ ?> required <?php } ?>>
                            </div>
                            <?php if($bd->valore_si_no > 0){ ?>
                                <div style="width: 25%">
                                    <label style="font-size: 16px; margin: 0; font-weight: bold" >Valore <b style="color:red">*</b></label>
                                    <input type="number" step="0.01" class="form-control" name="valore" required <?php echo ($file_caricato != null) ? 'value="'.$file_caricato->valore.'"' : '' ?>>
                                    <?php if($file_caricato != null){ ?>
                                        <input class="btn btn-primary mt-2" type="submit" name="modifica_valore_allegato" value="Modifica">
                                        <input class="btn btn-primary mt-2" type="hidden" name="id_modifica_valore" value="{{$file_caricato->id}}">
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <input type="hidden" name="id_allegato" value="{{$bd->id}}">
                        </div>
                        <?php if($file_caricato != null){ ?>
                            <div class="col-md-3 d-none d-md-block">
                                <iframe src="{{asset($file_caricato->path_allegato)}}" style="border: none; width: 100%"></iframe>
                            </div>
                        <?php } ?>
                        <div class="col-md-2 mt-3 mt-md-0 d-md-flex align-items-md-center justify-content-md-end">
                            <?php if($file_caricato != null){ ?>
                                <a target="_blank" class="btn btn-primary me-2" href="{{asset($file_caricato->path_allegato)}}"><i class="ri-article-line"></i></a>
                            <?php } ?>

                            <input <?php if($file_caricato != null){ ?> readonly disabled <?php } ?> class="btn btn-success" type="submit" name="invio_dati_effettuato" value="Aggiungi">
                        </div>
                    </div>

                </form>
                <?php if($file_caricato != null){ ?>
                    <form method="post" style="text-align: right; margin: 0;">
                        <input type="hidden" name="id_eliminazione_allegato" value="{{$file_caricato->id}}">
                        <input class="btn btn-danger me-2" type="submit" name="elimina_file_caricato" value="Elimina">
                    </form>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if($bando->n_preventivi != null){ ?>
            <h3 style="margin-top: 75px">Preventivi</h3>
            <div class="row g-3">
                <?php
                // Supponiamo che $b->n_preventivi contenga la stringa "1,2,3,4,5,6,7,8,9,10"
                $n_preventivi = $bando->n_preventivi;

                // Dividiamo la stringa in un array utilizzando la virgola come delimitatore
                $numbersArray = explode(',', $n_preventivi);

                // Prendiamo l'ultimo elemento dell'array
                $lastNumber = end($numbersArray);
                ?>
                <?php for($i = 1; $i <= $lastNumber; $i++ ){ ?>
                    <div class="col-md-4" style="border: 1px solid lightgrey; padding: 20px">
                        <form method="post" enctype="multipart/form-data">
                            <?php $preventivi_caricati = DB::table('bandi_allegati_utenti')->where('id_bando', $bando->id)->where('indice_preventivo', $i)->where('id_cliente', $utente->id)->first();  ?>

                            <div>
                                <label style="font-size: 16px; margin: 0; font-weight: bold;" >Preventivo <?php echo $i ?> <b style="color:red">*</b></label>
                                <input style="margin-bottom: 6px;" type="file" class="form-control mx-1" name="preventivo" <?php if($preventivi_caricati && $preventivi_caricati->indice_preventivo != $i){ ?> required <?php } ?> >
                                <label style="font-size: 16px; margin: 0; font-weight: bold;" >Valore <?php echo $i ?> <b style="color:red">*</b></label>
                                <input type="number" class="form-control mx-1" name="valore" required step="0.01" <?php echo ($preventivi_caricati && $preventivi_caricati->indice_preventivo === $i) ? 'value="'.$preventivi_caricati->valore.'"' : '' ?>>
                                <input type="hidden" name="indice_preventivo" value="{{$i}}">
                            </div>
                            <?php if($preventivi_caricati &&$preventivi_caricati->indice_preventivo === $i){ ?>
                                <a target="_blank" class="btn btn-primary me-2 mt-3" href="{{asset($preventivi_caricati->path_allegato)}}"><i class="ri-article-line"></i> Preventivo Caricato</a>
                            <?php } ?>
                            <div class="mt-2 d-flex justify-content-between">
                                <input <?php if($preventivi_caricati && $preventivi_caricati->indice_preventivo === $i){ ?> readonly disabled <?php } ?> style="width: 90px" class="btn btn-success me-2" type="submit" name="invio_preventivo">
                                <div>
                                    <?php if($preventivi_caricati && $preventivi_caricati->indice_preventivo === $i){ ?>
                                        <input class="btn btn-primary" type="submit" name="modifica_valore_preventivo" value="Modifica">
                                        <input class="btn btn-primary" type="hidden" name="id_modifica_valore_preventivo" value="{{$preventivi_caricati->id}}">
                                    <?php } ?>
                                    <?php if($preventivi_caricati && $preventivi_caricati->indice_preventivo === $i){ ?>
                                        <form method="post" style="text-align: right; margin: 0;">
                                            <input type="hidden" name="id_eliminazione_preventivo" value="{{$preventivi_caricati->id}}">
                                            <input class="btn btn-danger me-2" type="submit" name="elimina_preventivo_caricato" value="Elimina">
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if($bando->descrizione != null || $bando->descrizione != ''){ ?>
            <h3 style="margin-top: 4rem">Descrizione</h3>
            <div class="row">
                <div class="col-12">
                    {{$bando->descrizione}}
                </div>
            </div>
        <?php } ?>
    </div>
    <footer class="footer mt-5" style="position: relative!important; left: 0!important;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>document.write(new Date().getFullYear())</script> © Ingenia SRL.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Design & Develop by Ingenia SRL
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

<script src="/default/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/default/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/default/assets/libs/node-waves/waves.min.js"></script>
<script src="/default/assets/libs/feather-icons/feather.min.js"></script>
<script src="/default/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="/default/assets/js/plugins.js"></script>

<!-- apexcharts -->
<script src="/default/assets/libs/apexcharts/apexcharts.min.js"></script>

<!-- Vector map-->
<script src="/default/assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
<script src="/default/assets/libs/jsvectormap/maps/world-merc.js"></script>

<!--Swiper slider js-->
<script src="/default/assets/libs/swiper/swiper-bundle.min.js"></script>

<!-- Dashboard init -->
<script src="/default/assets/js/pages/dashboard-ecommerce.init.js"></script>

<!-- App js -->
<script src="/default/assets/js/app.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/default/assets/js/pages/datatables.init.js"></script>

</body>

</html>

<script type="text/javascript">



    $('.datatable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": true,
        "responsive": true,
        "scrollX": true,
        "stateSave": true,

        "oLanguage": {
            "sLengthMenu": "<span> Risultati :</span> _MENU_",
            "oPaginate": { "sFirst": "Primo", "sLast": "Ultimo", "sNext": ">", "sPrevious": "<" }
        },

        "columnDefs": [
            { targets: 'no-sort', orderable: false }
        ]
    });



</script>
</html>