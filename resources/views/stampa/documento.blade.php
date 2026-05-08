<style>

    @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');


    @page {
        margin-bottom: 120px; /* Per il footer */
    }

    body {
        font-family: "Alata", serif;
        font-weight: 400;
        font-style: normal;
        font-size:14px;
    }


    .header-section {
        width: 100%;
        margin-bottom: 20px;
    }

    .company-info {
        width: 50%;
        float: left;
    }

    .document-info {
        width: 50%;
        float: right;
        text-align: right;
    }

    .clear {
        clear: both;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .products-table th {
        background-color: #f0f3f1;
        padding: 8px;
        text-align: left;
    }

    .products-table td {
        padding: 8px;
        border-bottom: 1px solid #f0f3f1;
    }

    .totals-section {
        margin-top: 20px;
        text-align: right;
    }


    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        font-size: 10px;
        color: #555555;
        border-top: 1px solid #f0f3f1;
        padding-top: 10px;
    }
</style>

<div class="header-section">
    <div class="company-info">
        <div class="logo">
            <img src="{{ public_path('logo_gestya.jpg') }}" style="max-width:500px; max-height:120px;">
        </div>
        <h2>{{ $azienda->ragione_sociale }}</h2>
        <div>Capitale Sociale : {{ number_format($azienda->capitale_sociale, 2, ',', '.') }} i.v.</div>
        <div>Nr. Rea : {{ $azienda->nr_rea }}</div>
        <div>{{ $azienda->indirizzo }}<br>{{ $azienda->cap }} {{ $azienda->comune }} ({{ $azienda->provincia }}) {{ $azienda->nazione }}</div>
        <div>P.IVA: {{ $azienda->partita_iva }}</div>
        <div>MAIL: {{ $azienda->email_ricezione_fatture }}</div>
    </div>

    <div class="document-info">
        <h1>{{ $do->descrizione }}</h1>
        <br>
        <div><b>{{ $do->descrizione }} N. {{ $dotes->numero_doc }}</b></div>
        <div>Data: {{ date('d/m/Y', strtotime($dotes->data_doc)) }}</div>
        <br>
        <div>All'attenzione di</div>
        <div><b>{{ $dotes->ragione_sociale_fatturazione }}</b></div>
        <div>{{ $dotes->indirizzo }}<br>{{ $dotes->cap }} {{ $dotes->comune_fatturazione }} ({{ $dotes->provincia_fatturazione }})</div>
        <div>{{ $dotes->nazione }}</div>
        <div>P.IVA: {{ $dotes->partita_iva_fatturazione }}</div>
        @php
            $sede_consegna = null;
            if(!empty($dotes->id_sede_consegna)) {
                $sede_consegna = DB::table('sedi')->where('id', $dotes->id_sede_consegna)->first();
            }
        @endphp
        @if($sede_consegna)
            <br>
            <div><b>Indirizzo di consegna:</b> {{ $sede_consegna->nome }}</div>
            <div>{{ $sede_consegna->indirizzo }}<br>{{ $sede_consegna->cap }} {{ $sede_consegna->comune }} @if($sede_consegna->provincia)({{ $sede_consegna->provincia }})@endif</div>
        @endif
    </div>
    <div class="clear"></div>
</div>
<?php if($dotes->oggetto_visibile != ''){ ?>
    <div style="margin-top: 20px; font-size: 12px;font-weight: bold;">
        <?php echo nl2br($dotes->oggetto_visibile) ?>
    </div>
<?php } ?>

@php $is_ordine_passivo = in_array($dotes->cd_do, ['RDO', 'ORDF']); @endphp
<table class="products-table">
    <thead>
    <tr style="background-color: #f0f3f1;">
        <th style="padding: 8px; text-align: left;">Descrizione</th>
        @if(!$is_ordine_passivo)
        <th style="padding: 8px; text-align: right;">Prezzo</th>
        @endif
        <th style="padding: 8px; text-align: right;">Quantità</th>
        @if(!$is_ordine_passivo)
        <th style="padding: 8px; text-align: right;">Importo netto</th>
        <th style="padding: 8px; text-align: right;">IVA {{ $dorig[0]->iva }}%</th>
        <th style="padding: 8px; text-align: right;">Importo totale</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($dorig as $riga)
        @if($riga->nota_riga === 'NOTA_SEPARATORE')
        <tr style="border-bottom: 1px solid #f0f3f1; background-color: #fff9e6;">
            <td colspan="{{ $is_ordine_passivo ? 2 : 6 }}" style="padding: 8px;">
                <strong><?php echo nl2br($riga->descrizione) ?></strong>
            </td>
        </tr>
        @else
        <tr style="border-bottom: 1px solid #f0f3f1;">
            <td style="padding: 8px;width: 300px;">
                <?php echo nl2br($riga->descrizione) ?>
            </td>
            @if(!$is_ordine_passivo)
            <td style="padding: 8px; text-align: right;">
                € {{ number_format($riga->prezzo_unitario, 2, ',', '.') }}
                @if(!empty($riga->sconto_perc) && $riga->sconto_perc > 0)
                    <br><small style="color:#888;">Sc. {{ rtrim(rtrim(number_format($riga->sconto_perc, 2, ',', '.'), '0'), ',') }}%</small>
                @endif
            </td>
            @endif
            <td style="padding: 8px; text-align: right;">
                {{ number_format($riga->qta, 2, ',', '.') }}
            </td>
            @if(!$is_ordine_passivo)
            <td style="padding: 8px; text-align: right;">
                € {{ number_format($riga->imponibile, 2, ',', '.') }}
            </td>
            <td style="padding: 8px; text-align: right;">
                € {{ number_format($riga->imposta, 2, ',', '.') }}
            </td>
            <td style="padding: 8px; text-align: right;">
                € {{ number_format($riga->prezzo_totale, 2, ',', '.') }}
            </td>
            @endif
        </tr>
        @endif
    @endforeach
    </tbody>
</table>

@if(!$is_ordine_passivo)
<div class="totals-section">
    <table width="100%">
        <tr>
            <td width="80%" align="right">Imponibile</td>
            <td width="20%" align="right">€ {{ number_format($dotes->imponibile, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td align="right">IVA {{ $dorig[0]->iva }}% su € {{ number_format($dotes->imponibile, 2, ',', '.') }}<br>{{ $dorig[0]->rif_normativo_pdf }}</td>
            <td align="right">€ {{ number_format($dotes->imposta, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td align="right"><b>Totale dovuto</b></td>
            <td align="right"><b>€ {{ number_format($dotes->totale, 2, ',', '.') }}</b></td>
        </tr>
    </table>
</div>
@endif


@php
    $modalita_map = [
        'MP01' => 'Contanti',
        'MP02' => 'Assegno',
        'MP03' => 'Assegno circolare',
        'MP05' => 'Bonifico bancario',
        'MP08' => 'Carta di pagamento',
        'MP19' => 'SEPA Direct Debit',
        'MP21' => 'RIBA',
    ];
    $mod_pagamento = $dotes->modalita_pagamento ?? $azienda->modalita_pagamento ?? 'MP05';
    $nome_modalita = $modalita_map[$mod_pagamento] ?? $mod_pagamento;

    $condizioni_map = [
        'TP01' => 'Pagamento a rate',
        'TP02' => 'Pagamento completo',
        'TP03' => 'Anticipo',
    ];
    $cond_pagamento = $dotes->condizioni_pagamento ?? $azienda->condizioni_pagamento ?? '';
    $nome_condizioni = $condizioni_map[$cond_pagamento] ?? $cond_pagamento;
@endphp

<div style="margin-top: 20px; font-size: 11px;">
    <b>Modalità di Pagamento:</b> {{ $nome_modalita }}
    @if($nome_condizioni) — {{ $nome_condizioni }} @endif
    <br>
    @if(in_array($mod_pagamento, ['MP05', 'MP19', 'MP21']))
        <b>IBAN:</b> {{ $dotes->iban ?? $azienda->iban }}<br>
        @if($azienda->istituto_finanziario)
            <b>BANCA:</b> {{ $azienda->istituto_finanziario }}<br>
        @endif
        <b>INTESTAZIONE:</b> {{ $azienda->ragione_sociale }}<br>
    @endif

    @if(count($scadenze) > 0)
        <br>
        @if(count($scadenze) == 1)
            <b>SCADENZA:</b>
            {{ number_format($scadenze[0]->importo, 2, ',', '.') }} €
            @if($scadenze[0]->stato == 'pagato')
                — saldato in data {{ date('d/m/Y', strtotime($scadenze[0]->data_pagamento)) }}
            @else
                — entro il {{ date('d/m/Y', strtotime($scadenze[0]->data_scadenza)) }}
            @endif
            <br>
        @else
            <b>PAGABILE IN {{ count($scadenze) }} RATE:</b><br>
            @foreach($scadenze as $scadenza)
                &bull; <b>{{ number_format($scadenza->importo, 2, ',', '.') }} €</b>
                @if($scadenza->stato == 'pagato')
                    — saldato in data {{ date('d/m/Y', strtotime($scadenza->data_pagamento)) }}
                @else
                    — entro il {{ date('d/m/Y', strtotime($scadenza->data_scadenza)) }}
                @endif
                <br>
            @endforeach
        @endif
    @endif
</div>

@if($dotes->cd_do == 'FTV' || $dotes->cd_do == 'NC')
<div style="margin-top: 20px; font-size: 11px; color: #666;">
    Documento privo di valenza fiscale ai sensi dell'art. 21 Dpr 633/72. L'originale è disponibile all'indirizzo telematico da Lei fornito oppure nella Sua area riservata dell'Agenzia delle Entrate.
</div>
@endif

<div class="footer">
    <table width="100%">
        <tr>
            <td width="50%">
                {{ $azienda->ragione_sociale }} P.IVA {{ $azienda->partita_iva }}
            </td>
            <td width="50%" style="text-align: right">
                {{ $do->descrizione }} N. {{ $dotes->numero_doc }} del {{ date('d/m/Y', strtotime($dotes->data_doc)) }}
            </td>
        </tr>
    </table>
</div>