<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Scadenze Aperte</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Scadenze Aperte</h1>
        <p>Data: {{ date('d/m/Y') }}</p>
    </div>

    <div class="section-title">Scadenze in Entrata</div>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Importo Totale</th>
                <th>Importo Pagato</th>
                <th>Residuo</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scadenze_entrata as $s)
            <tr>
                <td>{{ date('d/m/Y', strtotime($s->data_scadenza)) }}</td>
                <td>{{ $s->cliente }}</td>
                <td>€ {{ number_format($s->importo, 2, ',', '.') }}</td>
                <td>€ {{ number_format($s->importo_pagato, 2, ',', '.') }}</td>
                <td>€ {{ number_format($s->importo - $s->importo_pagato, 2, ',', '.') }}</td>
                <td>{{ $s->note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Scadenze in Uscita</div>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Importo Totale</th>
                <th>Importo Pagato</th>
                <th>Residuo</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scadenze_uscita as $s)
            <tr>
                <td>{{ date('d/m/Y', strtotime($s->data_scadenza)) }}</td>
                <td>{{ $s->cliente }}</td>
                <td>€ {{ number_format($s->importo, 2, ',', '.') }}</td>
                <td>€ {{ number_format($s->importo_pagato, 2, ',', '.') }}</td>
                <td>€ {{ number_format($s->importo - $s->importo_pagato, 2, ',', '.') }}</td>
                <td>{{ $s->note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p class="total-row">Totale Scadenze in Entrata: € {{ number_format($totale_entrate, 2, ',', '.') }}</p>
        <p class="total-row">Totale Scadenze in Uscita: € {{ number_format($totale_uscite, 2, ',', '.') }}</p>
        <p class="total-row">Cashflow: € {{ number_format($totale_entrate - $totale_uscite, 2, ',', '.') }}</p>
    </div>
</body>
</html> 