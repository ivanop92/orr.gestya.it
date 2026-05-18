<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f766e">
    <title>Storico interventi · Manutenzione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body { background: #f6f7fa; }
        .navbar-manut { background: #0f766e; color: #fff; padding: 12px 16px; position: sticky; top: 0; z-index: 100; }
        .intervento-card {
            background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            padding: 12px 14px; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;
        }
        .intervento-card:hover { color: inherit; box-shadow: 0 3px 12px rgba(0,0,0,0.12); }
        .empty-state { text-align: center; padding: 60px 20px; color: #6b7280; }
        .empty-state i { font-size: 4rem; color: #d1d5db; }
    </style>
</head>
<body>

<div class="navbar-manut">
    <div class="d-flex align-items-center">
        <a href="/manutentore/dashboard" class="text-white text-decoration-none me-3"><i class="ri-arrow-left-line" style="font-size: 1.4rem;"></i></a>
        <div class="flex-grow-1">
            <div class="fw-semibold"><i class="ri-history-line me-1"></i>Storico</div>
            <div class="small" style="opacity: 0.85;">I miei interventi chiusi</div>
        </div>
    </div>
</div>

<div class="container py-3" style="max-width: 720px;">

    @if(count($interventi) === 0)
        <div class="empty-state">
            <i class="ri-history-line"></i>
            <p class="mt-3 mb-0"><strong>Nessun intervento nello storico</strong></p>
            <small>Quando completerai un report, lo troverai qui.</small>
        </div>
    @else
        <div class="text-muted small mb-2">{{ $interventi->total() }} interventi nello storico</div>
        @foreach($interventi as $i)
            <a href="/manutentore/intervento/{{ $i->id }}" class="intervento-card">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <strong>#{{ $i->id }}</strong>
                            @if($i->stato === 'completato')
                                <span class="badge bg-success">Completato</span>
                            @else
                                <span class="badge bg-info">Step {{ $i->step_corrente }}/6</span>
                            @endif
                            @if($i->step_3_completato_il)
                                <small class="text-muted ms-auto">Report il {{ \Carbon\Carbon::parse($i->step_3_completato_il)->format('d/m/Y') }}</small>
                            @endif
                        </div>
                        <div class="fw-medium">{{ $i->cliente_ragione_sociale ?: '—' }}</div>
                        <div class="text-muted small">
                            <i class="ri-train-line"></i> {{ $i->vagone_codice ?: $i->automezzo ?: '—' }}
                        </div>
                        @if($i->reason_intake)
                            <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($i->reason_intake, 80) }}</div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach

        @if($interventi->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {!! $interventi->links('utente.common._pagination') !!}
            </div>
        @endif
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
