<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Compilazione Questionario FRC</title>
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
        <div style="display: flex; justify-content: center; margin-bottom: 50px; margin-top: 40px"></div>
        <div class="row">
            <div class="col-12 d-md-flex justify-content-md-between" >
                <h1 class="mb-md-0 mb-3 text-md-start text-center">Compilazione Questionario FRC</h1>

            </div>
        </div>

        <form method="post">

            <div class="row">
                <div class="col-md-12">
                    <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                        <div class="modal-body">
                            <div class="row g-3">


                                <div class="col-md-12">
                                    <div>
                                        <label for="company_name-field" class="form-label">A) IMPIANTI, MACCHINARI, MACCHINE ELETTRONICHE, DOTAZIONI HARDWARE E ATTREZZATURE</label>
                                        <input type="number" name="valore_macchinari" class="form-control" placeholder="Valore Macchinari" required />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div>
                                        <label  class="form-label">Email <b style="color:red">*</b></label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Telefono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Telefono" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">Indirizzo</label>
                                        <input type="text" name="indirizzo" class="form-control" placeholder="Indirizzo" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">CAP</label>
                                        <input type="text" name="cap" class="form-control" placeholder="Comune" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Comune</label>
                                        <input type="text" name="comune" class="form-control" placeholder="Comune" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Provincia</label>
                                        <input type="text" name="provincia" class="form-control" placeholder="Provincia" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Regione</label>
                                        <input type="text" name="regione" class="form-control" placeholder="Provincia" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Mail Fatture</label>
                                        <input type="text" name="mail_recapito" class="form-control" placeholder="Mail Fatture" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Mail Leads</label>
                                        <input type="text" name="mail_leads" class="form-control" placeholder="Mail Leads" />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Codice Fiscale</label>
                                        <input type="text" name="cf" class="form-control" placeholder="CF" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Partita IVA</label>
                                        <input type="text" name="piva" class="form-control" placeholder="P.IVA" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">Codice SDI</label>
                                        <input type="text" name="sdi" class="form-control" placeholder="P.IVA" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label class="form-label">PEC</label>
                                        <input type="text" name="pec" class="form-control" placeholder="pec" />
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                                <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                                <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </form>

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