@include('default.common.header')

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Riepilogo Fatturato Anno {{ $anno }}</h2>
        </div>
    </div>

    <!-- Dati di Riepilogo -->
    <div class="row mt-4 text-center">
        <div class="col-md-4">
            <div class="circle shadow mx-auto">
                <h4>Fatturato Totale</h4>
                <p class="circle-value">€ {{ number_format($fatturato_totale, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="circle shadow mx-auto">
                <h4>Entrate</h4>
                <p class="circle-value text-success">€ {{ number_format($totale_entrate, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="circle shadow mx-auto">
                <h4>Uscite</h4>
                <p class="circle-value text-danger">€ {{ number_format($totale_uscite, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Tabella delle Fatture -->
    <div class="row mt-5">
        <div class="col-md-12">
            <table id="fattureTable" class="table table-bordered table-striped w-100">
                <thead>
                <tr>
                    <th>Numero</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Totale</th>
                    <th>Pagato</th>
                    <th>Rate</th>
                    <th>Stato</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fatture as $fattura)
                    @php
                        $importoRate = json_decode($fattura->importo_rate, true) ?? [];
                        $statusRate = json_decode($fattura->status_pagamento, true) ?? [];
                        $totalePagato = 0;
                        $ratePagate = 0;

                        foreach ($statusRate as $index => $status) {
                            if ($status === 'saldato') {
                                $totalePagato += $importoRate[$index] ?? 0;
                                $ratePagate++;
                            }
                        }

                        $rateTotali = count($importoRate);
                    @endphp
                    <tr>
                        <td>{{ $fattura->numero }}</td>
                        <td>{{ date('d/m/Y', strtotime($fattura->data_doc)) }}</td>
                        <td>{{ $fattura->cliente }}</td>
                        <td>€ {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($totalePagato, 2, ',', '.') }} di € {{ number_format($fattura->totale, 2, ',', '.') }}</td>
                        <td>{{ $ratePagate }} di {{ $rateTotali }}</td>
                        <td>
                            @if($fattura->saldata)
                                <span class="badge bg-success">Saldata</span>
                            @else
                                <span class="badge bg-warning">
                                        Mancano € {{ number_format($fattura->totale - $totalePagato, 2, ',', '.') }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            border: 2px solid #e0e0e0;
        }

        .circle h4 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .circle-value {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }
    </style>
</div>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#fattureTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Italian.json"
            }
        });
    });
</script>

@include('default.common.footer')
