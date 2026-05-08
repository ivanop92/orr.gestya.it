@include('utente.common.header')


<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestione Distinte Basi</h4>

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

        <div class="row">
            <div class="col-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Articoli</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover datatable" style="width: 100%">
                            <thead>
                            <tr>
                                <th>Descrizione</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($articoli as $a)
                                <tr onclick="distinta_base(<?php echo $a->id ?>)">
                                    <td>{{ $a->titolo }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-10" id="dettaglio_db">

            </div>
        </div>
    </div>
    @include('utente.common.footer')


    <script type="text/javascript">

        function distinta_base(id){
            $.ajax({
                url: "<?php echo URL::asset('utente/distinta_base/dettaglio') ?>/"+id,
                type:'GET',
                success: function(result){
                    $('#dettaglio_db').html(result);
                }
            });
        }

    </script>