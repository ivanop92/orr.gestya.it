<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f766e">
    <title>Manutenzione · I miei interventi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body { background: #f6f7fa; }
        .navbar-manut { background: #0f766e; color: #fff; padding: 12px 16px; position: sticky; top: 0; z-index: 100; }
        .navbar-manut .nome { font-weight: 600; font-size: 1rem; }
        .navbar-manut .small { opacity: 0.85; }
        .intervento-card {
            background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            padding: 14px; margin-bottom: 12px; text-decoration: none; color: inherit; display: block;
        }
        .intervento-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.12); color: inherit; }
        .badge-prio-alta { background: #dc2626; }
        .badge-prio-media { background: #f59e0b; }
        .badge-prio-bassa { background: #6b7280; }
        .step-pill { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.78rem; background: #e0f2fe; color: #075985; font-weight: 600; }
        .empty-state { text-align: center; padding: 60px 20px; color: #6b7280; }
        .empty-state i { font-size: 4rem; color: #d1d5db; }
    </style>
</head>
<body>

<div class="navbar-manut">
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <div class="nome"><i class="ri-tools-line me-1"></i>I miei interventi</div>
            <div class="small">{{ $utente->nome }} {{ $utente->cognome }}</div>
        </div>
        <a href="/manutentore/logout" class="text-white text-decoration-none ms-2" title="Esci">
            <i class="ri-logout-box-r-line" style="font-size: 1.4rem;"></i>
        </a>
    </div>
</div>

<div class="container py-3" style="max-width: 720px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            <i class="ri-check-line me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($interventi) === 0)
        <div class="empty-state">
            <i class="ri-inbox-line"></i>
            <p class="mt-3 mb-0"><strong>Nessun intervento assegnato</strong></p>
            <small>Quando l'ufficio ti assegnerà un intervento, lo vedrai qui.</small>
        </div>
    @else
        <div class="text-muted small mb-2">{{ count($interventi) }} interventi assegnati a te</div>
        @foreach($interventi as $i)
            <a href="/manutentore/intervento/{{ $i->id }}" class="intervento-card">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <strong>#{{ $i->id }}</strong>
                            <span class="badge bg-{{ $i->priorita === 'alta' ? 'danger' : ($i->priorita === 'bassa' ? 'secondary' : 'warning') }}">
                                {{ strtoupper($i->priorita) }}
                            </span>
                            <span class="step-pill">Step {{ $i->step_corrente }}/6</span>
                        </div>
                        <div class="fw-medium">{{ $i->cliente_ragione_sociale ?: '—' }}</div>
                        <div class="text-muted small">
                            <i class="ri-train-line"></i> {{ $i->vagone_codice ?: $i->automezzo ?: 'vagone non specificato' }}
                            @if($i->vagone_tipo) · {{ $i->vagone_tipo }}@endif
                        </div>
                        @if($i->reason_intake)
                            <div class="small mt-1"><i class="ri-error-warning-line text-danger me-1"></i>{{ \Illuminate\Support\Str::limit($i->reason_intake, 80) }}</div>
                        @endif
                        @if($i->localita)
                            <div class="text-muted small"><i class="ri-map-pin-line"></i> {{ $i->localita }}</div>
                        @endif
                    </div>
                    <i class="ri-arrow-right-s-line text-muted" style="font-size: 1.5rem;"></i>
                </div>
            </a>
        @endforeach
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
