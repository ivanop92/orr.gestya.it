@include('admin.common.header')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <h4 class="fs-16 mb-1">Benvenuto {{ $utente->nome }} {{ $utente->cognome }}</h4>
                        </div>
                    </div>

                </div> <!-- end .h-100-->
            </div> <!-- end col -->
        </div>


    </div>
</div>

@include('admin.common.footer')
