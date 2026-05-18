<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f766e">
    <title>Intervento #{{ $intervento->id }} · Manutenzione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body { background: #f6f7fa; }
        .navbar-manut { background: #0f766e; color: #fff; padding: 12px 16px; position: sticky; top: 0; z-index: 100; }
        .card-info { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 12px; }
        .card-info .head { padding: 12px 16px; border-bottom: 1px solid #eef0f4; font-weight: 600; }
        .card-info .body { padding: 12px 16px; }
        .info-row { padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 0.95rem; }
        .info-row:last-child { border: none; }
        .info-row .label { color: #6b7280; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-row .val { font-weight: 500; }
        .step-info { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; padding: 12px 14px; border-radius: 10px; font-size: 0.9rem; }
        .step-info-done { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
        textarea { font-size: 16px; }
        .btn-sticky {
            position: sticky; bottom: 0; background: #f6f7fa; padding: 12px 0; margin: 0 -12px -12px;
            padding-left: 12px; padding-right: 12px;
        }
    </style>
</head>
<body>

<div class="navbar-manut">
    <div class="d-flex align-items-center">
        <a href="/manutentore/dashboard" class="text-white text-decoration-none me-3"><i class="ri-arrow-left-line" style="font-size: 1.4rem;"></i></a>
        <div class="flex-grow-1">
            <div class="fw-semibold">Intervento #{{ $intervento->id }}</div>
            <div class="small" style="opacity: 0.85;">{{ $intervento->cliente_ragione_sociale ?: '—' }}</div>
        </div>
        <span class="badge bg-{{ $intervento->priorita === 'alta' ? 'danger' : ($intervento->priorita === 'bassa' ? 'secondary' : 'warning') }}">
            {{ strtoupper($intervento->priorita) }}
        </span>
    </div>
</div>

<div class="container py-3" style="max-width: 720px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small">
            <i class="ri-check-line me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show small">
            <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Dati intervento --}}
    <div class="card-info">
        <div class="head"><i class="ri-train-line me-1"></i>Dati intervento</div>
        <div class="body">
            <div class="info-row">
                <div class="label">Cliente</div>
                <div class="val">{{ $intervento->cliente_ragione_sociale ?: '—' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Vagone</div>
                <div class="val">{{ $intervento->vagone_codice ?: $intervento->automezzo ?: '—' }}@if($intervento->vagone_tipo) <small class="text-muted">({{ $intervento->vagone_tipo }})</small>@endif</div>
            </div>
            @if($intervento->data_apertura)
                <div class="info-row">
                    <div class="label">Apertura</div>
                    <div class="val">{{ \Carbon\Carbon::parse($intervento->data_apertura)->format('d/m/Y') }}</div>
                </div>
            @endif
            @if($intervento->localita)
                <div class="info-row">
                    <div class="label">Località</div>
                    <div class="val"><i class="ri-map-pin-line text-muted"></i> {{ $intervento->localita }}</div>
                </div>
            @endif
            @if($intervento->reason_intake)
                <div class="info-row">
                    <div class="label">Motivo rientro (sintomo)</div>
                    <div class="val">{{ $intervento->reason_intake }}</div>
                </div>
            @endif
            @if($intervento->note)
                <div class="info-row">
                    <div class="label">Note ufficio</div>
                    <div class="val">{{ $intervento->note }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Step info --}}
    @if($intervento->step_corrente == 3)
        <div class="step-info mb-3">
            <strong><i class="ri-pencil-line me-1"></i>Tocca a te</strong><br>
            Valuta il vagone e scrivi il <strong>report danni</strong> qui sotto. Quando confermi, il documento passa all'ufficio per l'emissione del preventivo.
        </div>

        <form method="post" action="/manutentore/intervento/{{ $intervento->id }}/invia_report">
            @csrf
            <div class="card-info">
                <div class="head"><i class="ri-tools-line me-1"></i>Report danni</div>
                <div class="body">
                    <textarea name="report_danni" class="form-control" rows="8" required placeholder="Descrivi i danni rilevati, le parti da sostituire, le lavorazioni necessarie...">{{ $intervento->report_danni }}</textarea>
                </div>
            </div>

            <div class="btn-sticky">
                <button type="submit" class="btn btn-success w-100 btn-lg" onclick="return confirm('Inviare il report all\'ufficio?');">
                    <i class="ri-send-plane-line me-1"></i> Invia Report all'Ufficio
                </button>
            </div>
        </form>

    @elseif($intervento->step_corrente < 3)
        <div class="step-info mb-3">
            <strong><i class="ri-time-line me-1"></i>In attesa</strong><br>
            L'intervento è ancora in fase di apertura/assegnazione presso l'ufficio. Non c'è nulla da fare ora.
        </div>

    @elseif($intervento->step_corrente > 3)
        <div class="step-info step-info-done mb-3">
            <strong><i class="ri-check-double-line me-1"></i>Report già inviato</strong><br>
            Il tuo report è in carico all'ufficio. Lo step corrente è {{ $intervento->step_corrente }}/6.
        </div>

        @if($intervento->report_danni)
            <div class="card-info">
                <div class="head"><i class="ri-tools-line me-1"></i>Il tuo report</div>
                <div class="body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $intervento->report_danni }}</p>
                </div>
            </div>
        @endif
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
