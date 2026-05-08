@include('default.common.header')

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

                    <!-- Sezione card documenti -->
                    <div class="row">
                        @foreach ($documenti_data as $documento)
                            <div class="col-xl-4 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">{{ $documento->descrizione }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $documento->totale }}</h4>
                                                <a href="{{ $documento->link }}" class="text-decoration-underline">Visualizza dettagli</a>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-soft-primary rounded fs-3">
                                                    <i class="bx bx-file text-primary"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Sezione grafico -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header border-0 align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Conteggio Documenti per Mese</h4>
                                </div>
                                <div class="card-body p-0 pb-2">
                                    <div class="w-100">
                                        <div id="documenti_mensili_chart" data-colors='["#556ee6", "#34c38f", "#f46a6a", "#50a5f1", "#f1b44c"]' class="apex-charts" dir="ltr"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div> <!-- end .h-100-->
            </div> <!-- end col -->
        </div>


    </div>
</div>
<script src="/default/assets/libs/apexcharts/apexcharts.min.js"></script>

<!-- Script per il grafico documenti mensili -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [
                    @foreach($grafico_data as $cd_do => $dati_mensili)
                    @php
                        $documento = $documenti_data->firstWhere('cd_do', $cd_do);
                    @endphp
                    @if($documento)
                {
                    name: '{{ $documento->descrizione }}',
                    data: @json($dati_mensili)
                },
                @endif
                @endforeach
            ],
            chart: {
                type: 'line',
                height: 350
            },
            stroke: {
                width: [3, 3, 3, 3, 3]
            },
            xaxis: {
                categories: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic']
            },
            colors: ['#556ee6', '#34c38f', '#f46a6a', '#50a5f1', '#f1b44c'],
            markers: {
                size: 4
            }
        };

        var chart = new ApexCharts(document.querySelector("#documenti_mensili_chart"), options);
        chart.render();
    });
</script>

@include('default.common.footer')
