<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}" type="image/png">
    <title>{{ $title ?? 'Iniciar sesión — Nexora' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-cyan: #22d3ee;
            --brand-blue: #2563eb;
            --brand-indigo: #4338ca;
            --brand-purple: #a855f7;
            --accent: #1D4ED8;
            --ink: #111827;
            --muted: #6B7280;
            --line: #EAECF0;
            --bg: #F5F6FA;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --danger-border: #FECACA;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--ink);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            width: 100%;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════
           PANEL IZQUIERDO — MARCA / ANIMACIÓN
        ══════════════════════════════════════════ */
        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 64px;
            overflow: hidden;
            color: #fff;
            background:
                radial-gradient(120% 140% at 15% 10%, #0ea5e9 0%, transparent 55%),
                radial-gradient(120% 140% at 85% 90%, #a855f7 0%, transparent 55%),
                linear-gradient(135deg, #0b1a4d 0%, #16227a 45%, #3312a3 100%);
            background-size: 200% 200%, 200% 200%, 100% 100%;
            animation: brandDrift 18s ease-in-out infinite;
        }

        @keyframes brandDrift {
            0%   { background-position: 0% 0%, 100% 100%, 0 0; }
            50%  { background-position: 40% 30%, 60% 70%, 0 0; }
            100% { background-position: 0% 0%, 100% 100%, 0 0; }
        }

        .brand-panel canvas#net {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: .55;
        }

        .brand-panel .glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            opacity: .5;
        }
        .glow-a { width: 340px; height: 340px; background: #22d3ee; top: -80px; left: -60px; animation: floatY 9s ease-in-out infinite; }
        .glow-b { width: 380px; height: 380px; background: #a855f7; bottom: -100px; right: -80px; animation: floatY 11s ease-in-out infinite reverse; }

        @keyframes floatY {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(18px, -22px); }
        }

        .brand-top, .brand-bottom { position: relative; z-index: 2; }

        .brand-logo-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 56px;
        }

        .brand-logo-row img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(0,0,0,.35));
        }

        .brand-logo-row span {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .brand-heading {
            font-size: 38px;
            line-height: 1.18;
            font-weight: 800;
            max-width: 460px;
            margin: 0 0 18px;
            opacity: 0;
            animation: riseIn .7s .1s cubic-bezier(.16,1,.3,1) forwards;
        }

        .brand-heading .grad {
            background: linear-gradient(90deg, #67e8f9, #c4b5fd);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .brand-sub {
            font-size: 15.5px;
            color: rgba(255,255,255,.78);
            max-width: 420px;
            line-height: 1.6;
            opacity: 0;
            animation: riseIn .7s .25s cubic-bezier(.16,1,.3,1) forwards;
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .brand-features {
            list-style: none;
            margin: 34px 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14.5px;
            color: rgba(255,255,255,.9);
            opacity: 0;
            animation: riseIn .6s cubic-bezier(.16,1,.3,1) forwards;
        }
        .brand-features li:nth-child(1) { animation-delay: .38s; }
        .brand-features li:nth-child(2) { animation-delay: .48s; }
        .brand-features li:nth-child(3) { animation-delay: .58s; }

        .brand-features .ico {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }

        .brand-quote {
            font-size: 13.5px;
            color: rgba(255,255,255,.6);
            border-left: 2px solid rgba(255,255,255,.35);
            padding-left: 14px;
            max-width: 400px;
        }

        /* ══════════════════════════════════════════
           PANEL DERECHO — FORMULARIO
        ══════════════════════════════════════════ */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: var(--bg);
        }

        .form-card {
            width: 100%;
            max-width: 396px;
            opacity: 0;
            animation: riseIn .6s .1s cubic-bezier(.16,1,.3,1) forwards;
        }

        .form-card.shake { animation: riseIn .6s cubic-bezier(.16,1,.3,1) forwards, shake .5s .55s cubic-bezier(.36,.07,.19,.97); }

        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-5px); }
            40%, 60% { transform: translateX(5px); }
        }

        .mobile-logo {
            display: none;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .mobile-logo img { width: 34px; height: 34px; object-fit: contain; }
        .mobile-logo span { font-weight: 800; font-size: 17px; color: var(--ink); }

        .form-header { margin-bottom: 28px; }
        .form-header h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 6px;
            color: var(--ink);
        }
        .form-header p {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .alert-error {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: #991B1B;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .alert-error svg { flex-shrink: 0; margin-top: 1px; }
        .alert-error ul { margin: 0; padding-left: 16px; }

        form { display: flex; flex-direction: column; gap: 18px; }

        .field { position: relative; }

        .field input {
            width: 100%;
            height: 52px;
            padding: 22px 14px 8px;
            font-size: 14.5px;
            font-family: inherit;
            color: var(--ink);
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 11px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .field input.has-icon { padding-right: 44px; }

        .field label {
            position: absolute;
            left: 14px;
            top: 16px;
            font-size: 14.5px;
            color: var(--muted);
            pointer-events: none;
            transform-origin: left top;
            transition: transform .16s ease, color .16s ease, top .16s ease;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(29,78,216,.12);
        }

        .field input:focus + label,
        .field input:not(:placeholder-shown) + label {
            top: 7px;
            transform: scale(.78);
            color: var(--accent);
        }

        .field input.field-error { border-color: var(--danger); }
        .field input.field-error:focus { box-shadow: 0 0 0 4px rgba(220,38,38,.12); }

        .toggle-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            border-radius: 6px;
        }
        .toggle-eye:hover { color: var(--ink); background: #F3F4F6; }

        .capslock-hint {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #B45309;
            margin-top: -8px;
        }
        .capslock-hint.show { display: flex; }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: -4px;
        }

        .switch-remember {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
        }
        .switch-remember input { position: absolute; opacity: 0; width: 0; height: 0; }
        .switch-track {
            width: 34px;
            height: 19px;
            border-radius: 20px;
            background: #D1D5DB;
            position: relative;
            transition: background .2s ease;
            flex-shrink: 0;
        }
        .switch-track::after {
            content: '';
            position: absolute;
            top: 2px; left: 2px;
            width: 15px; height: 15px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.25);
            transition: transform .2s ease;
        }
        .switch-remember input:checked + .switch-track { background: var(--accent); }
        .switch-remember input:checked + .switch-track::after { transform: translateX(15px); }
        .switch-remember span.lbl { font-size: 13.5px; color: var(--muted); }

        .btn-submit {
            position: relative;
            height: 50px;
            border: none;
            border-radius: 11px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .2px;
            cursor: pointer;
            overflow: hidden;
            background: linear-gradient(90deg, #2563eb, #4338ca 55%, #a855f7);
            background-size: 200% 100%;
            background-position: 0% 0%;
            transition: background-position .45s ease, transform .15s ease, box-shadow .2s ease;
            box-shadow: 0 8px 20px -6px rgba(37,99,235,.55);
            margin-top: 4px;
        }
        .btn-submit:hover { background-position: 100% 0%; box-shadow: 0 10px 24px -6px rgba(88,28,235,.6); }
        .btn-submit:active { transform: translateY(1px); }

        .btn-submit .btn-label {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -60%;
            width: 40%; height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg);
            transition: left .6s ease;
        }
        .btn-submit:hover::before { left: 130%; }

        .btn-submit.loading { pointer-events: none; }
        .btn-submit.loading .btn-label { opacity: 0; }
        .spinner {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .btn-submit.loading .spinner { display: flex; }
        .spinner span {
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .form-footer {
            margin-top: 26px;
            text-align: center;
            font-size: 12.5px;
            color: #9CA3AF;
        }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 940px) {
            .layout { grid-template-columns: 1fr; }
            .brand-panel { display: none; }
            .mobile-logo { display: flex; }
            .form-panel { padding: 32px 20px; align-items: flex-start; padding-top: 64px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; }
        }

        /* ══════════════════════════════════════════
           CRÉDITO DEL DESARROLLADOR
        ══════════════════════════════════════════ */
        .dev-credit {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 50;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px 9px 12px;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(10px);
            border: 1px solid var(--line);
            border-radius: 999px;
            box-shadow: 0 6px 18px -6px rgba(17,24,39,.15);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            white-space: nowrap;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .dev-credit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -6px rgba(37,99,235,.28);
            border-color: #C7D2FE;
        }
        .dev-credit .wa-ico {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #25D366;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            animation: waPulse 2.4s ease-in-out infinite;
        }
        @keyframes waPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37,211,102,.45); }
            50% { box-shadow: 0 0 0 5px rgba(37,211,102,0); }
        }
        .dev-credit .dev-muted { color: var(--muted); font-weight: 500; }
        .dev-credit .dev-handle {
            background: linear-gradient(90deg, #2563eb, #a855f7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 800;
        }

        @media (max-width: 940px) {
            .dev-credit { right: 50%; transform: translateX(50%); bottom: 14px; }
            .dev-credit:hover { transform: translateX(50%) translateY(-2px); }
        }

        @media (max-width: 420px) {
            .dev-credit { font-size: 11.5px; padding: 8px 13px 8px 10px; }
            .dev-credit .dev-muted { display: none; }
        }
    </style>
</head>

<body>
    <div class="layout">
        {{-- ── PANEL IZQUIERDO ── --}}
        <div class="brand-panel">
            <canvas id="net"></canvas>
            <div class="glow glow-a"></div>
            <div class="glow glow-b"></div>

            <div class="brand-top">
                <div class="brand-logo-row">
                    <img src="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}" alt="Nexora">
                    <span>NEXORA</span>
                </div>

                <h1 class="brand-heading">Gestiona tu negocio con <span class="grad">total claridad</span></h1>
                <p class="brand-sub">Contabilidad, inventario, facturación y punto de venta en una sola plataforma, pensada para que tomes mejores decisiones cada día.</p>

                <ul class="brand-features">
                    <li>
                        <span class="ico">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </span>
                        Contabilidad y comprobantes en tiempo real
                    </li>
                    <li>
                        <span class="ico">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/></svg>
                        </span>
                        Inventario y kardex siempre bajo control
                    </li>
                    <li>
                        <span class="ico">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/></svg>
                        </span>
                        Acceso seguro por roles y auditoría
                    </li>
                </ul>
            </div>

            <div class="brand-bottom">
                <p class="brand-quote">"Todo tu negocio, un solo panel de control."</p>
            </div>
        </div>

        {{-- ── PANEL DERECHO — FORMULARIO ── --}}
        <div class="form-panel">
            <div class="form-card {{ $errors->any() ? 'shake' : '' }}">
                <div class="mobile-logo">
                    <img src="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}" alt="Nexora">
                    <span>NEXORA</span>
                </div>

                <div class="form-header">
                    <h1>Bienvenido de nuevo</h1>
                    <p>Inicia sesión para continuar en tu panel</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST" id="login-form" novalidate>
                    @csrf
                    <div class="field">
                        <input type="text" name="username" id="username" placeholder=" "
                            value="{{ old('username') }}"
                            class="{{ $errors->has('username') ? 'field-error' : '' }}"
                            autocomplete="username" required autofocus>
                        <label for="username">Usuario o correo electrónico</label>
                    </div>

                    <div>
                        <div class="field">
                            <input type="password" name="password" id="password" placeholder=" "
                                class="has-icon {{ $errors->has('password') ? 'field-error' : '' }}"
                                autocomplete="current-password" required>
                            <label for="password">Contraseña</label>
                            <button type="button" class="toggle-eye" id="toggle-password" aria-label="Mostrar contraseña" tabindex="-1">
                                <svg id="eye-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="capslock-hint" id="capslock-hint">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2 3 12h5v9h8v-9h5L12 2Z"/></svg>
                            Bloq Mayús está activado
                        </div>
                    </div>

                    <div class="row-between">
                        <label class="switch-remember">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="switch-track"></span>
                            <span class="lbl">Recuérdame</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit" id="btn-submit">
                        <span class="btn-label">
                            Iniciar sesión
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </span>
                        <span class="spinner"><span></span></span>
                    </button>
                </form>

                <p class="form-footer">© {{ date('Y') }} Nexora. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>

    <a href="https://wa.me/573157444356" target="_blank" rel="noopener" class="dev-credit">
        <span class="wa-ico">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="#fff"><path d="M17.5 14.4c-.3-.1-1.6-.8-1.9-.9-.3-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.1.2-.3.2-.6.1-.3-.2-1.2-.5-2.3-1.5-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.4.1-.2 0-.4 0-.5 0-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.1 0 1.2.9 2.4 1 2.6.1.2 1.8 2.8 4.4 3.9.6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.6-.6 1.8-1.3.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.4.8 3.1 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.2.9.9-3.1-.2-.3C4.2 14.8 3.8 13.4 3.8 12c0-4.5 3.7-8.2 8.2-8.2s8.2 3.7 8.2 8.2-3.7 8.2-8.2 8.2z"/></svg>
        </span>
        <span class="dev-muted">Desarrollado por</span>&nbsp;<span class="dev-handle">@Andrexito.vip</span>
    </a>

    <script>
        // ── Mostrar / ocultar contraseña ──
        (function () {
            var btn = document.getElementById('toggle-password');
            var input = document.getElementById('password');
            var icon = document.getElementById('eye-icon');
            var eyeOpen = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/>';
            var eyeClosed = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.44M9.9 4.24A9.6 9.6 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.9 4.13M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>';
            btn.addEventListener('click', function () {
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                icon.innerHTML = showing ? eyeOpen : eyeClosed;
                btn.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
            });
        })();

        // ── Aviso de Bloq Mayús ──
        (function () {
            var input = document.getElementById('password');
            var hint = document.getElementById('capslock-hint');
            function check(e) {
                if (typeof e.getModifierState === 'function') {
                    hint.classList.toggle('show', e.getModifierState('CapsLock'));
                }
            }
            input.addEventListener('keyup', check);
            input.addEventListener('keydown', check);
            input.addEventListener('blur', function () { hint.classList.remove('show'); });
        })();

        // ── Estado de carga al enviar ──
        (function () {
            var form = document.getElementById('login-form');
            var btn = document.getElementById('btn-submit');
            form.addEventListener('submit', function () {
                if (form.checkValidity()) {
                    btn.classList.add('loading');
                }
            });
        })();

        // ── Fondo: red de partículas discreta ──
        (function () {
            var canvas = document.getElementById('net');
            if (!canvas || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            var ctx = canvas.getContext('2d');
            var panel = canvas.parentElement;
            var particles = [];
            var W, H;

            function resize() {
                W = canvas.width = panel.offsetWidth;
                H = canvas.height = panel.offsetHeight;
            }

            function init() {
                resize();
                var count = Math.max(24, Math.floor((W * H) / 26000));
                particles = [];
                for (var i = 0; i < count; i++) {
                    particles.push({
                        x: Math.random() * W,
                        y: Math.random() * H,
                        vx: (Math.random() - 0.5) * 0.35,
                        vy: (Math.random() - 0.5) * 0.35,
                        r: Math.random() * 1.6 + 0.8
                    });
                }
            }

            function step() {
                ctx.clearRect(0, 0, W, H);
                for (var i = 0; i < particles.length; i++) {
                    var p = particles[i];
                    p.x += p.vx; p.y += p.vy;
                    if (p.x < 0 || p.x > W) p.vx *= -1;
                    if (p.y < 0 || p.y > H) p.vy *= -1;
                }
                for (var i = 0; i < particles.length; i++) {
                    for (var j = i + 1; j < particles.length; j++) {
                        var a = particles[i], b = particles[j];
                        var dx = a.x - b.x, dy = a.y - b.y;
                        var dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 130) {
                            ctx.strokeStyle = 'rgba(255,255,255,' + (0.14 * (1 - dist / 130)) + ')';
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(a.x, a.y);
                            ctx.lineTo(b.x, b.y);
                            ctx.stroke();
                        }
                    }
                    ctx.fillStyle = 'rgba(255,255,255,.55)';
                    ctx.beginPath();
                    ctx.arc(particles[i].x, particles[i].y, particles[i].r, 0, Math.PI * 2);
                    ctx.fill();
                }
                requestAnimationFrame(step);
            }

            window.addEventListener('resize', init);
            init();
            requestAnimationFrame(step);
        })();
    </script>
</body>

</html>
