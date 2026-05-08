@include('default.common.header')

<div class="page-content">
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?php $anno = intval(Date('Y')); ?>
                        <h1 style="float:left">Lista Fatture <?php echo $anno ?></h1>

                        <?php $anno_attuale = intval(Date('Y')); ?>

                        <?php if($anno == $anno_attuale){ ?>
                            <a style="float:right;" class="btn btn-success" onclick="aggiungi()">Crea Fattura</a>
                            <a style="float:right;margin-right:20px;" class="btn btn-success" onclick="esporta_fatture()">Esporta Fatture</a>
                        <a href="{{ route('importa.fattura.xml') }}" class="btn btn-primary">Scarica Fattura in Ingresso</a>

                        <?php } ?>

                        <div class="clearfix"></div>

                        <div class="col-md-2" style="padding:0;float:left;margin-bottom:20px;">
                            <label>Filtro Tipologie di documenti</label>
                            <select id="td_filter" class="form-control select2" onchange="oTable.fnDraw();">
                                <option value="TD24">Fattura Differita</option>
                                <option value="TD04">Nota di Credito</option>
                                <option value="TD07">Fattura Semplificata</option>
                                <option value="TD01">Fattura Ordinaria</option>


                            </select>
                        </div>

                        <div class="clearfix"></div>



                        <table id="lista_fatture" class="table table-bordered table-hover" style="width:100%">
                            <thead>

                            <tr>
                                <th class="no-sort" style="width:100px;">Numero Documento</th>
                                <th class="no-sort">Data Documento</th>
                                <th class="no-sort" style="width: 250px">Recapiti</th>
                                <th class="no-sort">Dati</th>
                                <th class="no-sort" style="width:150px;">Totale</th>
                                <th class="no-sort" style="width:450px;"></th>
                            </tr>

                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
</div>

<div id="ajax_modifica_testata">

</div>

<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_aggiungi">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Crea Fattura</h4>
                </div>
                <div class="modal-body row">
                    <select class="form-control mb-2"  name="cd_cf" id="selectCliente" onchange="compilaCampi(this.value)">
                        <option value="0" disabled selected>-- Seleziona un Cliente --</option>

                        <?php foreach($clienti as $c) {?>
                        <option value="{{ $c->cd_cf }}">{{ $c->ragione_sociale }}</option>
                        <?php } ?>
                    </select>


                    <div class="col-sm-6">
                        <label>Tipologia <b style="color:red">*</b></label>
                        <select name="tipologia_documento" class="form-control select2" style="width:100%">
                            <option value="TD24" selected>Fattura Differita</option>
                            <option value="TD02" >Acconto/Anticipo su fattura</option>
                            <option value="TD03" >Acconto/Anticipo su parcella</option>
                            <option value="TD04" >Nota di Credito</option>
                            <option value="TD05" >Nota di Debito</option>
                            <option value="TD06" >Parcella</option>
                            <option value="TD07" >Fattura semplificata</option>
                            <option value="TD08" >Nota di credito semplificata</option>
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
                            <option value="S">scissione dei pagamenti<option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label>Nominativo <b style="color:red">*</b></label>
                        <input type="text" name="nominativo" class="form-control" id="nominativo" placeholder="Nominativo" value="" required>
                    </div>

                    <div class="col-sm-6">
                        <label>CF</label>
                        <input type="text" name="cf" class="form-control" id="cf" placeholder="CF">
                    </div>

                    <div class="col-sm-6">
                        <label>P.IVA</label>
                        <input type="text" name="piva" class="form-control" id="piva" placeholder="P.IVA">
                    </div>

                    <div class="col-sm-6">
                        <label>Indirizzo <b style="color:red">*</b></label>
                        <input type="text" name="indirizzo" class="form-control" id="indirizzo" placeholder="Indirizzo" value="" required>
                    </div>

                    <div class="col-sm-6">
                        <label>CAP <b style="color:red">*</b></label>
                        <input type="text" name="cap" class="form-control" id="cap" placeholder="CAP" value="" required>
                    </div>

                    <div class="col-sm-6">
                        <label>Città <b style="color:red">*</b></label>
                        <input type="text" name="citta" class="form-control" id="citta" placeholder="Città" value="" required>
                    </div>


                    <div class="col-sm-6">
                        <label>Provincia <b style="color:red">*</b></label>
                        <input style="text-transform: uppercase" type="text" name="provincia" class="form-control" id="provincia" placeholder="Provincia" value="" required maxlength="2">
                    </div>

                    <div class="col-sm-6">
                        <label>Nazione <b style="color:red">*</b></label>
                        <input type="text" name="nazione" class="form-control" id="nazione" placeholder="Nazione" value="IT" required>
                    </div>

                    <div class="col-sm-6">
                        <label>SDI <b style="color:red">*</b></label>
                        <input type="text" name="sdi" class="form-control" id="sdi" placeholder="SDI" value="0000000" required>
                    </div>

                    <div class="col-sm-6">
                        <label>PEC</label>
                        <input type="email" name="pec" class="form-control" id="pec" placeholder="PEC" value="">
                    </div>

                    <div class="col-sm-6">
                        <label>Condizioni Pagamento <b style="color:red">*</b></label>
                        <select name="condizioni_pagamento" class="form-control select2" required style="width:100%">
                            <option value="TP01">Pagamento a rate (TO01)</option>
                            <option value="TP02" selected>Pagamento Completo (TP02)</option>
                            <option value="TP03">Anticipo (TP03)</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>

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
                            <option value="MP18">bollettino di c/c postale</option>
                            <option value="MP19">SEPA Direct Debit</option>
                            <option value="MP20">SEPA Direct Debit CORE</option>
                            <option value="MP21">SEPA Direct Debit B2B</option>
                            <option value="MP22">Trattenuta su somme già riscosse</option>

                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label>Stato<b style="color:red">*</b></label>
                        <select name="stato" class="form-control select2" required style="width:100%">
                            <option value="0">Da Inviare</option>
                            <option value="1">Inviato</option>
                            <option value="2">Scartato</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" name="aggiungi_testata" value="Aggiungi Testata">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>


<form method="post" enctype="multipart/form-data">
    <div class="modal fade" id="modal_esporta_fatture">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Esporta Fatture XML</h4>
                </div>
                <div class="modal-body row">

                    <div class="col-sm-6">
                        <label>Inizio <b style="color:red">*</b></label>
                        <input type="text" name="data_inizio" class="form-control date-picker" id="data" placeholder="data" value="<?php echo date('01/m/Y') ?>" required>
                    </div>


                    <div class="col-sm-6">
                        <label>Fine <b style="color:red">*</b></label>
                        <input type="text" name="data_fine" class="form-control date-picker" id="data" placeholder="data" value="<?php echo date('30/m/Y') ?>" required>
                    </div>


                    <div class="clearfix" style="margin-bottom:20px;"></div>

                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" name="esporta_fatture" value="Esporta Fatture">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</form>

<script type="text/javascript">

    function compilaCampi(value) {
        jQuery.ajax({
            url: "<?php echo URL::asset('ajax/getClienteForOrdine') ?>/",
            type:'GET',
            data:{cd_cf:value},
            success: function(result){
                console.log(result)
                document.getElementById('indirizzo').value = result.indirizzo;
                document.getElementById('piva').value = result.piva;
                document.getElementById('cap').value = result.cap;
                document.getElementById('pec').value = result.pec;
                document.getElementById('nominativo').value = result.ragione_sociale;
                document.getElementById('citta').value = result.comune;
                document.getElementById('provincia').value = result.provincia;
                document.getElementById('nazione').value = result.nazione;
                document.getElementById('sdi').value = result.sdi;




            }});


    }


    function aggiungi(){
        $('#modal_aggiungi').modal('show');
    }

    function aggiungi_fattura_da_pratiche(){
        $('#modal_aggiungi_da_pratiche').modal('show');
        datatable.resize();
    }
    function pratiche_nascoste(){
        $('#modal_pratiche_nascoste').modal('show');
        datatable2.resize();
    }

    function esporta_fatture(){
        $('#modal_esporta_fatture').modal('show');
    }


    function modifica(id){

        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_modifica_testata').modal('show');
        });

    }

    function nota_credito(id){

        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_nota_credito').modal('show');
        });

    }

    function aggiungi_allegato(id){

        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_aggiungi_allegato').modal('show');
        });

    }
    function aggiungi_allegato2(id){

        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_aggiungi_allegato2').modal('show');
        });

    }

    function aggiungi_riga(id){
        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_aggiungi_riga').modal('show');
        });

    }

    function mostra_righe(id){

        $.get("<?php echo URL::ASSET('admin/ajax/modifica_testata_fattura') ?>/"+id, function( data ) {
            $("#ajax_modifica_testata" ).html( data );
            $('#modal_mostra_righe').modal('show');
        });

    }

    function modifica_riga(id){
        $('#modal_modifica_riga_'+id).modal('show');
    }

    function cambia_totale(){

        pu = parseFloat($('#pu').val()).toFixed(2);
        qta = $('#qta').val();
        pt = pu*qta
        $('#pt').val(parseFloat(pt).toFixed(2));
    }

    function modifica_totale(id){

        pu = parseFloat($('#pu_'+id).val()).toFixed(2);
        qta = $('#qta_'+id).val();
        pt = pu*qta
        $('#pt_'+id).val(parseFloat(pt).toFixed(2));
    }


    <?php if(isset($_GET['id_pratica'])){ ?>

    aggiungi_fattura_da_pratiche();

    <?php } ?>



    function cambia_rif(id){


        testo_N1 = 'Anticipazione su Servizi';
        testo_N2 = '';
        testo_N4 = 'Provvigioni per serv. assicurativi- esente (art.10 dpr n.633/72)';
        testo_N5 = 'Art. 74ter';

        if(id == 0){

            if($('#codice_iva').val() == 'N1') $('#rif_normativo').val(testo_N1);
            if($('#codice_iva').val() == 'N2') $('#rif_normativo').val(testo_N2);
            if($('#codice_iva').val() == 'N4') $('#rif_normativo').val(testo_N4);
            if($('#codice_iva').val() == 'N5') $('#rif_normativo').val(testo_N5);

        } else {

            if($('#codice_iva_'+id).val() == 'N1') $('#rif_normativo_'+id).val(testo_N1);
            if($('#codice_iva_'+id).val() == 'N2') $('#rif_normativo_'+id).val(testo_N2);
            if($('#codice_iva_'+id).val() == 'N4') $('#rif_normativo_'+id).val(testo_N4);
            if($('#codice_iva_'+id).val() == 'N5') $('#rif_normativo_'+id).val(testo_N5);

        }
    }

    function impostazioni_fattura(id,tipologia_testata,tipologia_righe,id_struttura,nome_struttura){

        $('#modal_impostazioni_fattura').modal('show');
        $('#if_id_pratica').html(id+' - '+nome_struttura);
        $('#if_id_pratica_val').val(id);
        $('#if_tipologia_intestazione').val(tipologia_testata);
        $('#if_tipologia_intestazione').trigger('change');
        $('#if_tipologia_righe').val(tipologia_righe);
        $('#if_tipologia_righe').trigger('change');


        /*
        $('#if_tipologia_intestazione option[value="2"]').text(nome_struttura);
        $('#if_tipologia_intestazione').trigger('change');
        */

        $("#if_altre_pratiche option").each(function()
        {
            $(this).removeAttr('selected');
            $(this).attr('disabled',true);

            if($(this).val() != id) {
                if ($(this).attr('struttura') == id_struttura) {
                    $(this).removeAttr('disabled');
                }
            }

            // Add $(this).val() to your list
        });

        $('#if_altre_pratiche').val(null).trigger('change');
    }


</script>



<script type="text/javascript">


    function aggiungi(){

        $('#modal_aggiungi').modal('show');
    }


    document.addEventListener("DOMContentLoaded", function(event) {


        var input = document.getElementById('td_filter');
        if (localStorage['filtro']) { // if job is set
            input.value = localStorage['filtro']; // set the value
        }

        oTable = $('#lista_fatture').dataTable({
            "bJQueryUI": false,
            "bAutoWidth": true,
            "sAjaxSource": '<?php echo URL::asset('json/lista_fatture') ?>/<?php echo $anno ?>',
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "td", "value": $('#td_filter').val() } );
            },

            "sPaginationType": "full_numbers",
            "aaSorting": [[ 0, "desc" ]],
            "bStateSave": true,
            "bServerSide" : true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem( 'DataTables_'+location.hash, JSON.stringify(oData) );
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse( localStorage.getItem('DataTables_'+location.hash) );
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td', nRow).css('background',aData[6]);
            },
            "aoColumns": [
                null,
                null,
                null,
                null,
                null,
                null,
                {"visible": false},
            ],

            "scrollX": true,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "Tutti"]],
            columnDefs: [
                { targets: 'no-sort', orderable: false,"order": [] }
            ],
            "oLanguage": {
                "sLengthMenu": "<span> Risultati :</span> _MENU_",
                "oPaginate": { "sFirst": "Primo", "sLast": "Ultimo", "sNext": ">", "sPrevious": "<" }
            }
        });



        input.onchange = function () {
            localStorage['filtro'] = this.value; // change localStorage on change
            oTable.fnDraw();
        }

    });

</script>


<style>
    body{font-size:12px}
</style>

@include('default.common.footer')