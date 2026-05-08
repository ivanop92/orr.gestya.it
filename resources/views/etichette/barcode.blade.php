<!-- resources/views/etichette/barcode.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Etichetta Barcode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .etichetta {
            width: 100%;
            height: 60mm;
            padding: 5mm;
            margin-bottom: 0;
            page-break-after: always;
            box-sizing: border-box;
        }
        .etichetta:last-child {
            page-break-after: avoid;
        }
        .content {
            text-align: center;
        }
        .titolo {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .dettagli {
            margin-bottom: 5px;
        }
        .barcode-img {
            max-width: 90%;
            height: auto;
            margin: 5px auto;
        }
        .barcode-text {
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
@for ($i = 0; $i < $num_copie; $i++)
    <div class="etichetta">
        <div class="content">
            <div class="titolo">{{ $articolo->titolo }}</div>
            <div class="dettagli">
                <span>Codice: {{ $articolo->codice_articolo }}</span>
                <span style="margin-left: 10px;">Lotto: {{ $lotto }}</span>
            </div>
            <div>
                <img src="{{ $barcode_url }}" alt="Barcode {{ $barcode }}" class="barcode-img">
            </div>
            <div class="barcode-text">{{ $barcode }}</div>
        </div>
    </div>
@endfor
</body>
</html>