@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Evadi Documento</h4>

                    <div class="page-title-right">
                        <a href="{{ url('documenti/' . ($documento->attivo == 1 ? 'ca' : 'cp') . '/' . $dotes->cd_do) }}" class="btn btn-primary">Torna a Documenti</a>
                    </div>

                </div>
            </div>
        </div>
       

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
@include('default.common.footer')


<script>








</script>

