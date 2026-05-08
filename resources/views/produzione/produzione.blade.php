@extends('produzione.layout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard Produzione</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ordini di Lavoro Attivi</h5>
                        </div>
                        <div class="card-body">
                            <table id="odlTable" class="table table-bordered table-hover dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Data</th>
                                    <th>Articolo</th>
                                    <th>Quantità</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($odl_attivi as $odl)
                                    <tr>
                                        <td>{{ $odl->numero }}</td>
                                        <td>{{ date('d/m/Y', strtotime($odl->data)) }}</td>
                                        <td>{{ $odl->articolo }}</td>
                                        <td>{{ $odl->qta }}</td>
                                        <td>
                                            @if($odl->stato == 0)
                                                <span class="badge bg-warning">In Attesa</span>
                                            @elseif($odl->stato == 1)
                                                <span class="badge bg-info">In Produzione</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('produzione/dettaglio_odl/'.$odl->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ri-eye-line"></i> Dettaglio
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#odlTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Italian.json'
                },
                responsive: true
            });
        });
    </script>
@endsection