<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fattura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            background-color: #f4f4f4;
        }
        .header-table {
            border: none;
        }
        .header-table td {
            border: none;
            padding: 5px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<table class="header-table">
    <tr>
        <td>
            <strong>Cedente/prestatore (fornitore)</strong><br>
            Identificativo fiscale: IT{{ $azienda->partita_iva }}<br>
            Codice fiscale: {{ $azienda->partita_iva }}<br>
            Denominazione: {{ $azienda->ragione_sociale }}<br>
            Regime fiscale: {{ $azienda->regime_fiscale }}<br>
            Indirizzo: {{ $azienda->indirizzo }}<br>
            Comune: {{ $azienda->comune }} Provincia: {{ $azienda->provincia }}<br>
            Cap: {{ $azienda->cap }} Nazione: IT
        </td>
        <td>
            <strong>Cessionario/committente (cliente)</strong><br>
            Identificativo fiscale: IT{{ $testata->piva }}<br>
            Codice fiscale: {{ $testata->cf }}<br>
            Denominazione: {{ $testata->nominativo }}<br>
            Indirizzo: {{ $testata->indirizzo }}<br>
            Comune: {{ $testata->citta }} Provincia: {{ $testata->provincia }}<br>
            Cap: {{ $testata->cap }} Nazione: IT<br>
            Pec: {{ $testata->pec }}
        </td>
    </tr>
</table>

<table>
    <tr>
        <th>Tipologia documento</th>
        <th>Art. 73</th>
        <th>Numero documento</th>
        <th>Data documento</th>
        <th>Codice destinatario</th>
    </tr>
    <tr>
        <td>{{ $testata->tipologia_documento }}</td>
        <td>fattura</td>
        <td>{{ $testata->numero }}</td>
        <td>{{ $testata->data }}</td>
        <td>Indica PEC</td>
    </tr>
</table>

<table>
    <tr>
        <th>Cod. articolo</th>
        <th>Descrizione</th>
        <th>Quantità</th>
        <th>UM</th>
        <th>Prezzo unitario</th>
        <th>Sconto o magg.</th>
        <th>%IVA</th>
        <th>Prezzo totale</th>
    </tr>
    @foreach($righe as $riga)
        <tr>
            <td>contratto con allegati</td>
            <td>{{ $riga->descrizione }}</td>
            <td>{{ number_format($riga->qta, 2, ',', '.') }}</td>
            <td>{{ $riga->um }}</td>
            <td>{{ number_format($riga->pu / (1 + $riga->iva / 100), 2, ',', '.') }}</td>
            <td></td>
            <td>{{ number_format($riga->iva, 2, ',', '.') }}</td>
            <td>{{ number_format($riga->pt / (1 + $riga->iva / 100), 2, ',', '.') }}</td>
        </tr>
    @endforeach
</table>

<table>
    <tr>
        <th>Esigibilità IVA</th>
        <th>%IVA</th>
        <th>Totale imponibile</th>
        <th>Totale imposta</th>
    </tr>
    @foreach($dati_riepilogo as $riepilogo)
        <tr>
            <td>S (scissione dei pagamenti)</td>
            <td>{{ number_format($riepilogo->iva, 2, ',', '.') }}</td>
            <td>{{ number_format($riepilogo->imponibile, 2, ',', '.') }}</td>
            <td>{{ number_format($riepilogo->imposta, 2, ',', '.') }}</td>
        </tr>
    @endforeach
</table>

<table>
    <tr>
        <th>Modalità pagamento</th>
        <th>Dettagli</th>
        <th>Importo</th>
    </tr>
    <tr>
        <td>{{ $testata->tipologia_pagamento }}</td>
        <td>Bonifico</td>
        <td>{{ number_format($testata->totale, 2, ',', '.') }}</td>
    </tr>
</table>
</body>
</html>
