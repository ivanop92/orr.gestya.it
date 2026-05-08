<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8" />
    <title>Accesso Produzione | {{ $azienda->ragione_sociale }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Login Operatori Produzione" />

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/default/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/default/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: #f1f4f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-user-select: none;
            user-select: none;
        }
        .kiosk-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .kiosk-header {
            background: linear-gradient(135deg, #405189, #2c3a66);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
        }
        .kiosk-header img {
            max-height: 50px;
            background: #fff;
            border-radius: 6px;
            padding: 4px 8px;
            margin-right: 16px;
        }
        .kiosk-header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }
        .kiosk-header small {
            display: block;
            opacity: .8;
            font-size: 13px;
            font-weight: 400;
        }
        .kiosk-body {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }

        /* Step 1: griglia operatori */
        .operatori-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .op-card {
            background: #fff;
            border: 2px solid #e3e8f0;
            border-radius: 14px;
            padding: 22px 12px;
            text-align: center;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .op-card:active {
            transform: scale(0.97);
            border-color: #405189;
            box-shadow: 0 4px 12px rgba(64,81,137,.18);
        }
        .op-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #405189;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 600;
            margin: 0 auto 12px auto;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        .op-name {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
            margin: 0;
            line-height: 1.3;
        }
        .op-no-pin {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            color: #d63031;
            font-weight: 500;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 60px;
            opacity: .4;
            display: block;
            margin-bottom: 12px;
        }

        /* Step 2: tastierino PIN */
        .pin-overlay {
            position: fixed;
            inset: 0;
            background: rgba(20, 25, 45, .92);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .pin-overlay.show { display: flex; }
        .pin-box {
            background: #fff;
            border-radius: 18px;
            padding: 28px 24px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
        }
        .pin-back {
            position: absolute;
            top: 18px;
            left: 18px;
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 0;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .pin-back:active { background: rgba(255,255,255,.25); }
        .pin-greeting {
            text-align: center;
            margin-bottom: 18px;
        }
        .pin-greeting .op-avatar {
            width: 72px;
            height: 72px;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .pin-greeting h3 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #212529;
        }
        .pin-greeting p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin: 20px 0 24px 0;
        }
        .pin-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #cbd3e0;
            background: #fff;
            transition: all .12s ease;
        }
        .pin-dot.filled {
            background: #405189;
            border-color: #405189;
        }
        .pin-error {
            color: #d63031;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            margin: -10px 0 14px 0;
            min-height: 18px;
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .key {
            background: #f1f4f8;
            border: 0;
            border-radius: 12px;
            padding: 18px 0;
            font-size: 24px;
            font-weight: 600;
            color: #212529;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: transform .08s ease, background .12s ease;
        }
        .key:active {
            transform: scale(0.95);
            background: #dde3ee;
        }
        .key-action {
            background: #405189;
            color: #fff;
        }
        .key-action:active { background: #2c3a66; }
        .key-danger {
            background: #fee;
            color: #d63031;
        }
        .key-danger:active { background: #fcc; }

        /* Login fallback (email/password) */
        .fallback-toggle {
            text-align: center;
            margin-top: 28px;
        }
        .fallback-toggle button {
            background: transparent;
            border: 0;
            color: #6c757d;
            font-size: 13px;
            text-decoration: underline;
        }
        .fallback-form {
            max-width: 400px;
            margin: 24px auto 0 auto;
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            display: none;
        }
        .fallback-form.show { display: block; }
        .fallback-form .form-control { padding: 12px; font-size: 16px; }
        .fallback-form .btn { padding: 12px; font-size: 16px; }

        @media (max-width: 480px) {
            .operatori-grid { grid-template-columns: repeat(2, 1fr); }
            .op-avatar { width: 70px; height: 70px; font-size: 26px; }
        }
    </style>
</head>
<body>
<div class="kiosk-wrapper">
    <div class="kiosk-header">
        @if(!empty($azienda->immagine))
            <img src="{{ URL::asset($azienda->immagine) }}" alt="{{ $azienda->ragione_sociale }}">
        @endif
        <div>
            <h1>Accesso Produzione</h1>
            <small>{{ $azienda->ragione_sociale }}</small>
        </div>
    </div>

    <div class="kiosk-body">
        @if(empty($operatori))
            <div class="empty-state">
                <i class="ri-user-search-line"></i>
                <p>Nessun operatore configurato per questa azienda.</p>
                <p class="text-muted">Vai in Anagrafica Dipendenti per aggiungere un operatore e impostare il suo PIN.</p>
            </div>
        @else
            <div class="operatori-grid" id="operatoriGrid">
                @foreach($operatori as $op)
                    @php
                        $iniziali = strtoupper(mb_substr($op->nome ?? '', 0, 1) . mb_substr($op->cognome ?? '', 0, 1));
                        $img = $op->immagine ?? '';
                        $hasImg = $img && $img !== '/default/assets/images/users/user-dummy-img.jpg' && file_exists(public_path($img));
                    @endphp
                    <div class="op-card"
                         data-id="{{ $op->id }}"
                         data-nome="{{ $op->nome }} {{ $op->cognome }}"
                         data-iniziali="{{ $iniziali }}"
                         data-has-pin="{{ !empty($op->pin) ? '1' : '0' }}"
                         @if($hasImg) data-img="/{{ ltrim($img, '/') }}" @endif>
                        <div class="op-avatar"
                             @if($hasImg) style="background-image:url('/{{ ltrim($img, '/') }}'); color:transparent;" @endif>
                            {{ $iniziali ?: '?' }}
                        </div>
                        <p class="op-name">{{ $op->nome }} {{ $op->cognome }}</p>
                        @if(empty($op->pin))
                            <span class="op-no-pin"><i class="ri-error-warning-line"></i> PIN non impostato</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="fallback-toggle">
            <button type="button" id="toggleFallback">Accedi con email e password &rsaquo;</button>
        </div>

        <div class="fallback-form" id="fallbackForm">
            <form method="post">
                @if($error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endif
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button class="btn btn-primary w-100" name="login" type="submit" value="Accedi">Accedi</button>
            </form>
        </div>
    </div>
</div>

<!-- Overlay tastierino PIN -->
<div class="pin-overlay" id="pinOverlay">
    <button type="button" class="pin-back" id="pinBack"><i class="ri-arrow-left-line"></i> Indietro</button>
    <div class="pin-box">
        <div class="pin-greeting">
            <div class="op-avatar" id="pinAvatar"></div>
            <h3 id="pinNome"></h3>
            <p>Inserisci il tuo PIN</p>
        </div>

        <div class="pin-dots" id="pinDots">
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
        </div>

        <div class="pin-error" id="pinError">
            @if($error === 'PIN errato') {{ $error }} @endif
        </div>

        <form method="post" id="pinForm">
            <input type="hidden" name="id_operatore" id="pinIdOperatore">
            <input type="hidden" name="pin" id="pinValue">
            <input type="hidden" name="login_pin" value="1">
            <div class="keypad">
                <button type="button" class="key" data-digit="1">1</button>
                <button type="button" class="key" data-digit="2">2</button>
                <button type="button" class="key" data-digit="3">3</button>
                <button type="button" class="key" data-digit="4">4</button>
                <button type="button" class="key" data-digit="5">5</button>
                <button type="button" class="key" data-digit="6">6</button>
                <button type="button" class="key" data-digit="7">7</button>
                <button type="button" class="key" data-digit="8">8</button>
                <button type="button" class="key" data-digit="9">9</button>
                <button type="button" class="key key-danger" id="keyClear"><i class="ri-close-line"></i></button>
                <button type="button" class="key" data-digit="0">0</button>
                <button type="button" class="key key-action" id="keyOk"><i class="ri-check-line"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('pinOverlay');
    var pinDigits = '';
    var pinLength = 4;
    var dots = document.querySelectorAll('#pinDots .pin-dot');
    var pinValue = document.getElementById('pinValue');
    var pinIdOperatore = document.getElementById('pinIdOperatore');
    var pinAvatar = document.getElementById('pinAvatar');
    var pinNome = document.getElementById('pinNome');
    var pinForm = document.getElementById('pinForm');
    var pinError = document.getElementById('pinError');

    function renderDots() {
        dots.forEach(function (d, i) {
            if (i < pinDigits.length) d.classList.add('filled');
            else d.classList.remove('filled');
        });
    }

    function clearPin() {
        pinDigits = '';
        renderDots();
        pinError.textContent = '';
    }

    function openOverlay(card) {
        if (card.dataset.hasPin === '0') {
            alert('Questo operatore non ha ancora un PIN impostato. Chiedi all\'amministratore di configurarlo.');
            return;
        }
        clearPin();
        pinIdOperatore.value = card.dataset.id;
        pinNome.textContent = card.dataset.nome;
        pinAvatar.textContent = card.dataset.iniziali || '?';
        pinAvatar.style.backgroundImage = '';
        pinAvatar.style.color = '';
        if (card.dataset.img) {
            pinAvatar.style.backgroundImage = "url('" + card.dataset.img + "')";
            pinAvatar.style.color = 'transparent';
        }
        overlay.classList.add('show');
    }

    function closeOverlay() {
        overlay.classList.remove('show');
        clearPin();
    }

    document.querySelectorAll('.op-card').forEach(function (card) {
        card.addEventListener('click', function () { openOverlay(card); });
    });

    document.getElementById('pinBack').addEventListener('click', closeOverlay);

    document.querySelectorAll('.key[data-digit]').forEach(function (key) {
        key.addEventListener('click', function () {
            if (pinDigits.length >= pinLength) return;
            pinDigits += key.dataset.digit;
            renderDots();
            if (pinDigits.length === pinLength) {
                setTimeout(function () { submitPin(); }, 150);
            }
        });
    });

    document.getElementById('keyClear').addEventListener('click', function () {
        clearPin();
    });

    document.getElementById('keyOk').addEventListener('click', function () {
        if (pinDigits.length >= pinLength) submitPin();
    });

    function submitPin() {
        pinValue.value = pinDigits;
        pinForm.submit();
    }

    // Toggle fallback email/password
    var toggle = document.getElementById('toggleFallback');
    var fallback = document.getElementById('fallbackForm');
    if (toggle) {
        toggle.addEventListener('click', function () {
            fallback.classList.toggle('show');
        });
    }

    // Se ho un errore PIN, riapri l'overlay automaticamente
    @if($error === 'PIN errato')
        // niente: l'utente vede l'errore quando riclicca sull'operatore
    @endif

    // Mostra fallback se l'errore viene dal form email/password
    @if($error && $error !== 'PIN errato')
        fallback.classList.add('show');
    @endif
})();
</script>
</body>
</html>
