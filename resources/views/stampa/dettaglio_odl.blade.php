<style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
    h1 { font-size: 18px; margin: 0 0 5px 0; }
    h2 { font-size: 14px; margin: 15px 0 8px 0; color: #405189; border-bottom: 2px solid #405189; padding-bottom: 3px; }
    h3 { font-size: 12px; margin: 10px 0 5px 0; color: #666; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { padding: 5px 8px; border: 1px solid #ddd; text-align: left; font-size: 10px; }
    th { background-color: #f0f3f7; font-weight: bold; }
    .header-table { border: none; margin-bottom: 15px; }
    .header-table td { border: none; padding: 2px 5px; vertical-align: top; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
    .bg-success { background-color: #0ab39c; }
    .bg-primary { background-color: #405189; }
    .bg-danger { background-color: #f06548; }
    .bg-warning { background-color: #f7b84b; color: #333; }
    .bg-secondary { background-color: #878a99; }
    .bg-info { background-color: #299cdb; }
    .fase-card { border: 1px solid #ddd; border-radius: 5px; margin-bottom: 12px; page-break-inside: avoid; }
    .fase-header { padding: 8px 12px; color: #fff; border-radius: 5px 5px 0 0; }
    .fase-header-success { background-color: #0ab39c; }
    .fase-header-primary { background-color: #405189; }
    .fase-header-danger { background-color: #f06548; }
    .fase-body { padding: 10px 12px; }
    .info-row { margin-bottom: 3px; }
    .info-label { color: #888; font-size: 9px; }
    .info-value { font-weight: bold; }
    .semi-card { border: 1px solid #ddd; border-radius: 4px; padding: 6px 8px; margin-bottom: 5px; }
    .semi-completato { border-color: #0ab39c; background-color: #f0faf8; }
    .semi-da-fare { border-color: #f7b84b; background-color: #fffbf0; }
</style>

<!-- Intestazione -->
<table class="header-table" width="100%">
    <tr>
        <td width="60%">
            <img src="{{ public_path('logo_gestya.jpg') }}" style="max-height: 60px; margin-bottom: 5px;"><br>
            <h1>{{ $azienda->ragione_sociale ?? '' }}</h1>
            <span style="font-size: 10px; color: #888;">{{ $azienda->indirizzo ?? '' }} - {{ $azienda->cap ?? '' }} {{ $azienda->comune ?? '' }} ({{ $azienda->provincia ?? '' }})</span>
        </td>
        <td width="40%" style="text-align: right;">
            <span style="font-size: 9px; color: #888;">Stampato il {{ date('d/m/Y H:i') }}</span>
        </td>
    </tr>
</table>

<!-- Info ODL -->
<table class="header-table" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
    <tr>
        <td width="50%">
            <div style="font-size: 16px; font-weight: bold; color: #405189;">ODL N. {{ $odl->numero }}</div>
            <div style="margin-top: 3px;">Data: <strong>{{ date('d/m/Y', strtotime($odl->data)) }}</strong></div>
            <div>Quantità: <strong>{{ $odl->qta }}</strong></div>
        </td>
        <td width="50%">
            <div>Codice Articolo: <strong>{{ $odl->codice_articolo }}</strong></div>
            <div>Prodotto: <strong>{{ $odl->articolo }}</strong></div>
            @if(!empty($odl->cliente_ragione_sociale))
                <div>Cliente: <strong>{{ $odl->cliente_ragione_sociale }}</strong></div>
            @endif
            <div>Stato:
                @if($odl->stato == 0)
                    <span class="badge bg-danger">In Attesa</span>
                @elseif($odl->stato == 1)
                    <span class="badge bg-primary">In Produzione</span>
                @else
                    <span class="badge bg-success">Completato</span>
                @endif
            </div>
        </td>
    </tr>
</table>

<!-- Fasi di Lavorazione -->
<h2>Fasi di Lavorazione</h2>

@php $i = 1; @endphp
@foreach($odl_righe as $or)
    @php
        $header_class = 'fase-header-danger';
        $stato_label = 'In attesa';
        if($or->inizio != '' && $or->fine == '' && $or->completato == 0) {
            $header_class = 'fase-header-primary';
            $stato_label = 'In corso';
        }
        if($or->fine != '' && $or->completato == 1) {
            $header_class = 'fase-header-success';
            $stato_label = 'Completato';
        }
        $durata_str = '-';
        if($or->inizio != '' && $or->fine != '') {
            $diff = strtotime($or->fine) - strtotime($or->inizio);
            $ore = floor($diff / 3600);
            $minuti = floor(($diff % 3600) / 60);
            $durata_str = $ore.'h '.$minuti.'m';
        }
        $op_nome = '-';
        if(!empty($or->id_operatore_assegnato)) {
            foreach($operatori as $o) {
                if($o->id == $or->id_operatore_assegnato) $op_nome = $o->nome . ' ' . ($o->cognome ?? '');
            }
        }
        $fornitore_nome = '';
        if(!empty($or->id_fornitore) && isset($fornitori_odl)) {
            foreach($fornitori_odl as $fo) {
                if($fo->id == $or->id_fornitore) $fornitore_nome = $fo->ragione_sociale;
            }
        }
    @endphp

    <div class="fase-card">
        <div class="fase-header {{ $header_class }}">
            <strong>Fase {{ $i }} — {{ $or->nome_fase }}</strong>
            <span style="float: right;">{{ $stato_label }}</span>
        </div>
        <div class="fase-body">
            <table class="header-table">
                <tr>
                    <td width="25%">
                        <span class="info-label">Inizio</span><br>
                        <span class="info-value">{{ $or->inizio ? date('d/m/Y H:i', strtotime($or->inizio)) : '-' }}</span>
                    </td>
                    <td width="25%">
                        <span class="info-label">Fine</span><br>
                        <span class="info-value">{{ $or->fine ? date('d/m/Y H:i', strtotime($or->fine)) : '-' }}</span>
                    </td>
                    <td width="15%">
                        <span class="info-label">Durata</span><br>
                        <span class="info-value">{{ $durata_str }}</span>
                    </td>
                    <td width="15%">
                        <span class="info-label">Qta</span><br>
                        <span class="info-value">{{ $or->qta_fatta }}/{{ $or->qta }}</span>
                    </td>
                    <td width="20%">
                        @if($fornitore_nome)
                            <span class="info-label">Lav. Esterna</span><br>
                            <span class="info-value" style="color: #f7b84b;">{{ $fornitore_nome }}</span>
                        @else
                            <span class="info-label">Operatore</span><br>
                            <span class="info-value">{{ $op_nome }}</span>
                        @endif
                    </td>
                </tr>
            </table>

            @if(!empty($or->note))
                <div style="margin-top: 5px; padding: 4px 8px; background: #fff9e6; border-left: 3px solid #f7b84b; font-size: 10px;">
                    <strong>Note:</strong> {{ $or->note }}
                </div>
            @endif

            <!-- Materiali -->
            @if(count($or->materiali) > 0)
                <h3>Materiali da scaricare</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Materiale</th>
                            <th style="width: 60px;">Tipo</th>
                            <th style="width: 90px;">Qta/unità</th>
                            <th style="width: 90px;">Qta totale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($or->materiali as $m)
                        <tr>
                            <td>{{ $m->titolo }}</td>
                            <td>
                                @if($m->tipologia == 3)
                                    <span class="badge bg-warning">SEMI</span>
                                @elseif($m->tipologia == 1)
                                    <span class="badge bg-info">MP</span>
                                @else
                                    <span class="badge bg-secondary">COMM</span>
                                @endif
                            </td>
                            <td>{{ number_format($m->qta, 4, ',', '.') }} {{ $m->um ?? '' }}</td>
                            <td><strong>{{ number_format($m->qta * $odl->qta, 4, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Semilavorati -->
            @php
                $semi_fase = array_filter($odl_semilavorati, function($s) use ($or) {
                    return $s->id_fase == $or->id_fase;
                });
            @endphp
            @if(count($semi_fase) > 0)
                <h3>Semilavorati da produrre</h3>
                @foreach($semi_fase as $semi)
                    <div class="semi-card {{ $semi->stato == 1 ? 'semi-completato' : 'semi-da-fare' }}">
                        <strong>{{ $semi->codice_articolo }}</strong> — {{ $semi->titolo }}
                        <span style="float: right;">
                            @if($semi->stato == 1)
                                <span class="badge bg-success">Completato</span>
                            @else
                                <span class="badge bg-warning">Da fare</span>
                            @endif
                        </span>
                        <br>
                        <span style="font-size: 9px; color: #888;">Qta: {{ $semi->qta }}</span>
                        @if(!empty($semi->fornitore_nome))
                            <span style="font-size: 9px; color: #f7b84b; margin-left: 10px; font-weight: bold;">Lav. esterna: {{ $semi->fornitore_nome }}</span>
                        @endif
                        @if(isset($semi->materiali) && count($semi->materiali) > 0)
                            <div style="font-size: 9px; color: #555; margin-top: 4px;">
                                <strong>Materiali necessari:</strong>
                                <ul style="margin: 2px 0 0 15px; padding: 0;">
                                    @foreach($semi->materiali as $mat)
                                        <li>
                                            @if($mat->tipologia == 1)<span style="background:#299cdb;color:#fff;padding:1px 4px;border-radius:2px;">MP</span>
                                            @elseif($mat->tipologia == 2)<span style="background:#878a99;color:#fff;padding:1px 4px;border-radius:2px;">COMM</span>
                                            @elseif($mat->tipologia == 3)<span style="background:#f7b84b;color:#333;padding:1px 4px;border-radius:2px;">SEMI</span>
                                            @endif
                                            {{ $mat->codice_articolo }} - {{ $mat->titolo }} ({{ number_format($mat->qta * $semi->qta, 4, ',', '.') }} {{ $mat->um }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($semi->completato_at)
                            <span style="font-size: 9px; color: #0ab39c; margin-left: 10px;">
                                Completato il {{ date('d/m/Y H:i', strtotime($semi->completato_at)) }}
                                @if($semi->operatore_nome) — {{ $semi->operatore_nome }} {{ $semi->operatore_cognome }} @endif
                            </span>
                        @endif
                    </div>
                @endforeach
            @endif

            <!-- Allegati -->
            @if(isset($or->allegati) && count($or->allegati) > 0)
                <h3>Allegati</h3>
                <table>
                    <tr>
                        <th>File</th>
                        <th>Descrizione</th>
                        <th style="width: 100px;">Data</th>
                    </tr>
                    @foreach($or->allegati as $allegato)
                    <tr>
                        <td>{{ $allegato->nome_originale }}</td>
                        <td>{{ $allegato->descrizione ?? '-' }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($allegato->created_at)) }}</td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>

    @php $i++; @endphp
@endforeach

<!-- Riepilogo -->
<h2>Riepilogo</h2>
<table>
    <tr>
        <th>Fasi Totali</th>
        <th>Fasi Completate</th>
        <th>Fasi In Corso</th>
        <th>Fasi In Attesa</th>
    </tr>
    <tr>
        <td style="text-align:center;">{{ count($odl_righe) }}</td>
        <td style="text-align:center;">
            @php $completate = collect($odl_righe)->where('completato', 1)->count(); @endphp
            {{ $completate }}
        </td>
        <td style="text-align:center;">
            @php $in_corso = collect($odl_righe)->filter(function($r){ return $r->inizio != '' && $r->fine == '' && $r->completato == 0; })->count(); @endphp
            {{ $in_corso }}
        </td>
        <td style="text-align:center;">
            {{ count($odl_righe) - $completate - $in_corso }}
        </td>
    </tr>
</table>

@if(count($odl_semilavorati) > 0)
<table>
    <tr>
        <th>Semilavorati Totali</th>
        <th>Completati</th>
        <th>Da Fare</th>
    </tr>
    <tr>
        <td style="text-align:center;">{{ count($odl_semilavorati) }}</td>
        <td style="text-align:center;">{{ collect($odl_semilavorati)->where('stato', 1)->count() }}</td>
        <td style="text-align:center;">{{ collect($odl_semilavorati)->where('stato', 0)->count() }}</td>
    </tr>
</table>
@endif
