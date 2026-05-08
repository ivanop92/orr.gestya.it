<!-- resources/views/utente/commesse/gantt_pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .progress-bar {
            background-color: #eee;
            width: 100%;
            height: 20px;
            position: relative;
        }
        .progress-fill {
            background-color: #4CAF50;
            height: 100%;
            position: absolute;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
    </style>
</head>
<body>
<div style="margin-bottom: 20px;">
    <h3>Dettagli Commessa</h3>
    <p><strong>Codice:</strong> {{ $commessa->codice_commessa }}</p>
    <p><strong>Descrizione:</strong> {{ $commessa->descrizione }}</p>
    <p><strong>Budget:</strong> € {{ number_format($commessa->budget, 2, ',', '.') }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>Attività</th>
        <th>Responsabile</th>
        <th>Inizio</th>
        <th>Fine</th>
        <th>Stato</th>
        <th>Completamento</th>
    </tr>
    </thead>
    <tbody>
    @foreach($attivita as $a)
        <tr>
            <td>{{ $a->titolo }}</td>
            <td>{{ $a->responsabile }}</td>
            <td>{{ \Carbon\Carbon::parse($a->data_inizio)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($a->data_fine)->format('d/m/Y') }}</td>
            <td>
                @if($a->stato == 'da_iniziare')
                    <span class="badge badge-warning">Da Iniziare</span>
                @elseif($a->stato == 'in_corso')
                    <span class="badge badge-info">In Corso</span>
                @elseif($a->stato == 'completata')
                    <span class="badge badge-success">Completata</span>
                @else
                    <span class="badge badge-danger">In Ritardo</span>
                @endif
            </td>
            <td>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $a->completamento }}%"></div>
                    <div style="position: absolute; width: 100%; text-align: center; color: black;">
                        {{ $a->completamento }}%
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top: 20px;">
    <p><small>Documento generato il {{ $data_stampa }} da {{ $utente->nome }} {{ $utente->cognome }}</small></p>
</div>
</body>
</html>