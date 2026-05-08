<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        margin: 0;
        padding: 0;
    }
    .title {
        font-size: 14pt;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .info {
        font-size: 11pt;
        margin-bottom: 5px;
        text-align: left;
        padding-left: 10px;
    }
    .label {
        font-weight: bold;
    }
    .barcode-container {
        margin-top: 15px;
        text-align: center;
        padding: 5px;
        border: 1px solid #eee;
        border-radius: 4px;
    }
    .barcode {
        display: inline-block;
    }
    .barcode-number {
        font-family: monospace;
        font-size: 10pt;
        margin-top: 5px;
        letter-spacing: 1px;
    }
</style>

<div class="title">ORDINE DI LAVORO</div>

<div class="info">
    <span class="label">Numero ODL:</span> {{ $odl->numero }}
</div>

<div class="info">
    <span class="label">Articolo:</span> {{ $odl->codice_articolo }}
</div>

<div class="info" style="font-size: 10pt;">
    {{ $odl->articolo }}
</div>

<div class="info">
    <span class="label">Quantità:</span> {{ $odl->qta }}
</div>

<div class="info">
    <span class="label">Lotto:</span> {{ $odl->lotto_produzione }}
</div>

<div class="barcode-container">
    <div class="barcode">
        <img src="data:image/png;base64,{{ $barcode }}" style="width: 80mm; height: 20mm">
    </div>
    <div class="barcode-number">
        @if(isset($movimento) && !empty($movimento->barcode))
            {{ $movimento->barcode }}
        @else
            {{ $odl->lotto_produzione }}
        @endif
    </div>
</div>