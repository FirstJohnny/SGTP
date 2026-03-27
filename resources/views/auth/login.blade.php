<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>SGTP — Acesso ao Sistema</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ═══════════════════════════════════════════════════
           TOKENS
        ═══════════════════════════════════════════════════ */
        :root {
            --navy:        #0d2e5e;
            --navy-deep:   #07203f;
            --navy-mid:    #1a4d8c;
            --amber:       #f39c12;
            --amber-dim:   rgba(243,156,18,.14);
            --amber-glow:  rgba(243,156,18,.22);
            --white:       #ffffff;
            --off:         #f4f6fb;
            --slate:       #64748b;
            --slate-lt:    #94a3b8;
            --border:      rgba(203,213,225,.7);
            --err:         #dc2626;
            --err-bg:      #fff1f1;
            --ok:          #16a34a;

            --r-input:     14px;
            --r-btn:       14px;

            --ease:        cubic-bezier(.4,0,.2,1);
            --ease-out:    cubic-bezier(0,.55,.45,1);

            --t-fast:  .18s;
            --t-mid:   .28s;
            --t-slow:  .52s;
        }

        /* ═══════════════════════════════════════════════════
           RESET
        ═══════════════════════════════════════════════════ */
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        html {
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100%;
            min-height: 100dvh;
            display: flex;
            background: var(--navy-deep);
            overflow: hidden;
        }

        /* ═══════════════════════════════════════════════════
           SPLIT LAYOUT
        ═══════════════════════════════════════════════════ */
        .split {
            display: flex;
            width: 100%;
            min-height: 100dvh;
        }

        /* ════════════════════════════════
           LEFT — BRAND PANEL
        ════════════════════════════════ */
        .panel-brand {
            flex: 0 0 46%;
            position: relative;
            background: var(--navy-deep);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(36px,5vw,64px) clamp(36px,5vw,68px);
            overflow: hidden;
        }

        /* geometric ring overlays */
        .panel-brand::before {
            content: '';
            position: absolute;
            top: -20%; right: -12%;
            width: 72%; height: 140%;
            border: 1px solid rgba(243,156,18,.07);
            border-radius: 50%;
            pointer-events: none;
        }

        .panel-brand::after {
            content: '';
            position: absolute;
            top: 12%; right: -28%;
            width: 72%; height: 80%;
            border: 1px solid rgba(26,77,140,.28);
            border-radius: 50%;
            pointer-events: none;
        }

        /* mesh */
        .bg-mesh {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 5%  85%, rgba(26,77,140,.55)   0%, transparent 60%),
                radial-gradient(ellipse 55% 55% at 90% 8%,  rgba(243,156,18,.06)  0%, transparent 55%),
                radial-gradient(ellipse 65% 70% at 50% 50%, rgba(7,32,63,.85)     0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* dot grid */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.022) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.022) 1px, transparent 1px);
            background-size: 44px 44px;
            z-index: 0;
            pointer-events: none;
        }

        /* amber bottom gradient strip */
        .bg-strip {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--amber), transparent);
            opacity: .4;
            z-index: 1;
        }

        .brand-inner {
            position: relative;
            z-index: 2;
        }

        /* ── mark ── */
        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            animation: slideFromLeft var(--t-slow) var(--ease-out) .08s both;
        }

        .mark-badge {
            width: 48px; height: 48px;
            border-radius: 13px;
            background: var(--amber);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(243,156,18,.32);
        }

        .mark-badge i {
            font-size: 1.25rem;
            color: var(--navy-deep);
        }

        .mark-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.85rem;
            letter-spacing: 2.5px;
            color: var(--white);
            line-height: 1;
        }

        .mark-name em {
            font-style: normal;
            color: var(--amber);
        }

        /* ── hero ── */
        .brand-hero {
            margin-top: clamp(52px,9vh,100px);
            animation: slideFromLeft var(--t-slow) var(--ease-out) .22s both;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--amber);
            margin-bottom: 18px;
        }

        .eyebrow::before {
            content: '';
            display: block;
            width: 26px; height: 2px;
            background: var(--amber);
            border-radius: 2px;
        }

        .brand-hero h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(3rem,5.2vw,4.8rem);
            line-height: .93;
            letter-spacing: .5px;
            color: var(--white);
        }

        .brand-hero h2 .outline {
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(255,255,255,.28);
        }

        .brand-hero p {
            margin-top: 22px;
            font-size: clamp(.8rem,1.05vw,.9rem);
            font-weight: 400;
            color: rgba(255,255,255,.42);
            line-height: 1.75;
            max-width: 300px;
        }

        /* ── pills ── */
        .brand-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: clamp(28px,5vh,52px);
            animation: slideFromLeft var(--t-slow) var(--ease-out) .38s both;
        }

        .pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 100px;
            padding: 8px 16px;
        }

        .pill i   { font-size: .75rem; color: var(--amber); }
        .pill span { font-size: .74rem; font-weight: 600; color: rgba(255,255,255,.55); white-space: nowrap; }

        /* ── brand footer ── */
        .brand-foot {
            position: relative;
            z-index: 2;
            font-size: .7rem;
            color: rgba(255,255,255,.18);
            animation: slideFromLeft var(--t-slow) var(--ease-out) .48s both;
        }

        @keyframes slideFromLeft {
            from { opacity:0; transform:translateX(-22px); }
            to   { opacity:1; transform:translateX(0); }
        }

        /* ════════════════════════════════
           RIGHT — FORM PANEL
        ════════════════════════════════ */
        .panel-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            padding: clamp(28px,5vw,56px) clamp(28px,6.5vw,80px);
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* soft radial tint */
        .panel-form::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(243,156,18,.035) 0%, transparent 70%);
            pointer-events: none;
        }

        .form-shell {
            width: 100%;
            max-width: 388px;
            animation: slideFromRight var(--t-slow) var(--ease-out) .18s both;
        }

        @keyframes slideFromRight {
            from { opacity:0; transform:translateX(22px); }
            to   { opacity:1; transform:translateX(0); }
        }

        /* ── form heading ── */
        .form-head {
            margin-bottom: 34px;
        }

        .form-head .label-area {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--amber);
            margin-bottom: 10px;
        }

        .form-head h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(2rem,3.8vw,2.9rem);
            letter-spacing: .4px;
            color: var(--navy);
            line-height: 1;
            margin-bottom: 8px;
        }

        .form-head p {
            font-size: .84rem;
            color: var(--slate);
            font-weight: 400;
        }

        /* ── server error ── */
        .alert-err {
            display: none;
            align-items: flex-start;
            gap: 10px;
            background: var(--err-bg);
            border-left: 3px solid var(--err);
            color: var(--err);
            padding: 12px 14px;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 500;
            margin-bottom: 22px;
            animation: shake .45s var(--ease) both;
        }

        .alert-err.show { display: flex; }
        .alert-err i    { margin-top: 1px; flex-shrink: 0; }

        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%     { transform:translateX(-5px); }
            40%     { transform:translateX(5px); }
            60%     { transform:translateX(-3px); }
            80%     { transform:translateX(3px); }
        }

        /* ── field ── */
        .field { margin-bottom: 18px; }

        .field > label {
            display: block;
            font-size: .71rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--slate);
            margin-bottom: 7px;
        }

        .inp-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .inp-wrap .ico-l {
            position: absolute;
            left: 15px;
            color: var(--slate-lt);
            font-size: .88rem;
            pointer-events: none;
            transition: color var(--t-fast) var(--ease);
            z-index: 1;
        }

        .inp-wrap input {
            width: 100%;
            height: 52px;
            padding: 0 50px 0 42px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-input);
            background: var(--off);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .93rem;
            color: var(--navy);
            -webkit-appearance: none;
            transition:
                border-color var(--t-mid) var(--ease),
                background   var(--t-mid) var(--ease),
                box-shadow   var(--t-mid) var(--ease);
        }

        .inp-wrap input::placeholder {
            color: var(--slate-lt);
            font-weight: 300;
        }

        .inp-wrap input:focus {
            outline: none;
            border-color: var(--navy-mid);
            background: var(--white);
            box-shadow: 0 0 0 3.5px rgba(26,77,140,.1);
        }

        .inp-wrap:focus-within .ico-l { color: var(--navy-mid); }

        /* validation states */
        .inp-wrap input.is-invalid {
            border-color: var(--err);
            background: var(--err-bg);
            box-shadow: 0 0 0 3px rgba(220,38,38,.08);
        }

        .inp-wrap input.is-valid {
            border-color: var(--ok);
            box-shadow: 0 0 0 3px rgba(22,163,74,.08);
        }

        /* inline status icon */
        .st-ico {
            position: absolute;
            right: 46px;
            font-size: .8rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity var(--t-fast);
        }

        .st-ico.ok  { color: var(--ok); }
        .st-ico.err { color: var(--err); }
        .st-ico.on  { opacity: 1; }

        /* push status icon left on pw field (room for eye button) */
        .pw-wrap .st-ico { right: 50px; }

        /* field message */
        .f-msg {
            font-size: .72rem;
            font-weight: 600;
            color: var(--err);
            margin-top: 5px;
            padding-left: 2px;
            display: none;
        }

        .f-msg.on { display: block; }

        /* eye toggle */
        .btn-eye {
            position: absolute;
            right: 10px;
            width: 32px; height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            color: var(--slate-lt);
            font-size: .88rem;
            transition: color var(--t-fast), background var(--t-fast);
        }

        .btn-eye:hover       { color: var(--navy); background: rgba(0,0,0,.04); }
        .btn-eye:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 1px;
        }

        /* ── options row ── */
        .opts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 26px;
        }

        .chk {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
        }

        .chk input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px; height: 18px;
            border: 1.5px solid var(--border);
            border-radius: 5px;
            background: var(--off);
            cursor: pointer;
            flex-shrink: 0;
            position: relative;
            transition: border-color var(--t-fast), background var(--t-fast);
        }

        .chk input[type="checkbox"]:checked {
            background: var(--navy-mid);
            border-color: var(--navy-mid);
        }

        .chk input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            top: 2px; left: 5px;
            width: 5px; height: 9px;
            border: 2px solid white;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .chk input[type="checkbox"]:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
        }

        .chk span {
            font-size: .84rem;
            font-weight: 500;
            color: var(--slate);
        }

        .link-forgot {
            font-size: .84rem;
            font-weight: 600;
            color: var(--navy-mid);
            text-decoration: none;
            position: relative;
        }

        .link-forgot::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 1.5px;
            background: var(--amber);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--t-mid) var(--ease);
        }

        .link-forgot:hover { color: var(--amber); }
        .link-forgot:hover::after { transform: scaleX(1); }

        /* ── submit button ── */
        .btn-submit {
            width: 100%;
            height: 54px;
            border: none;
            border-radius: var(--r-btn);
            background: var(--navy-deep);
            color: var(--white);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .04em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition:
                transform  var(--t-mid) var(--ease),
                box-shadow var(--t-mid) var(--ease);
        }

        /* amber top-stroke on hover */
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--amber);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--t-mid) var(--ease);
        }

        /* shimmer sweep */
        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg,
                transparent 35%,
                rgba(255,255,255,.06) 50%,
                transparent 65%);
            transform: translateX(-100%);
            transition: transform .55s var(--ease);
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(7,32,63,.38);
        }

        .btn-submit:hover:not(:disabled)::before { transform: scaleX(1); }
        .btn-submit:hover:not(:disabled)::after  { transform: translateX(100%); }
        .btn-submit:active:not(:disabled)        { transform: translateY(0); box-shadow: none; }

        .btn-submit:disabled { opacity: .65; cursor: not-allowed; }

        /* ripple */
        .btn-submit .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.16);
            transform: scale(0);
            animation: ripple .55s linear forwards;
            pointer-events: none;
        }

        @keyframes ripple { to { transform:scale(4); opacity:0; } }

        /* spinner */
        .spin {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,.25);
            border-top-color: white;
            border-radius: 50%;
            animation: rot .6s linear infinite;
            display: none; flex-shrink: 0;
        }

        @keyframes rot { to { transform: rotate(360deg); } }

        .btn-submit.loading .spin    { display: block; }
        .btn-submit.loading .btn-ico { display: none; }

        /* ── form footer ── */
        .form-foot {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid var(--border);
            font-size: .72rem;
            color: var(--slate-lt);
            text-align: center;
            line-height: 1.65;
        }

        .form-foot a {
            color: var(--slate);
            text-underline-offset: 2px;
        }

        .form-foot a:hover { color: var(--navy-mid); }

        /* ═══════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════ */
        @media (max-width: 860px) {
            body   { overflow: auto; }
            .split { min-height: 100dvh; }

            .panel-brand { display: none; }

            .panel-form {
                flex: 1;
                padding: 48px 28px 52px;
                overflow: visible;
            }

            .panel-form::before { display: none; }
            .form-shell         { max-width: 100%; }
        }

        @media (max-width: 420px) {
            .panel-brand { padding: 26px 20px 30px; }
            .panel-form  { padding: 32px 20px 38px; }
            .opts        { flex-direction: column; align-items: flex-start; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>

<div class="split">

    <!-- ════════════════ BRAND ════════════════ -->
    <aside class="panel-brand">
        <div class="bg-mesh"  aria-hidden="true"></div>
        <div class="bg-grid"  aria-hidden="true"></div>
        <div class="bg-strip" aria-hidden="true"></div>

        <div class="brand-inner">
            <!-- Mark -->
            <div class="brand-mark" aria-label="SGTP">
                <div class="mark-badge" aria-hidden="true">
                    <i class="fas fa-bus"></i>
                </div>
                <span class="mark-name">SG<em>T</em>P</span>
            </div>

            <!-- Hero -->
            <div class="brand-hero">
                <p class="eyebrow">Plataforma Operacional</p>
                <h2>
                    Gestão<br>
                    de <span class="outline">Trans</span><br>
                    portes
                </h2>
                <p>Centralize rotas, frotas e operações num único ecossistema inteligente de mobilidade urbana.</p>
            </div>

            <!-- Pills -->
            <div class="brand-pills">
                <div class="pill">
                    <i class="fas fa-route"          aria-hidden="true"></i>
                    <span>Rotas em tempo real</span>
                </div>
                <div class="pill">
                    <i class="fas fa-shield-halved"  aria-hidden="true"></i>
                    <span>Acesso seguro</span>
                </div>
                <div class="pill">
                    <i class="fas fa-chart-line"     aria-hidden="true"></i>
                    <span>Relatórios avançados</span>
                </div>
            </div>
        </div>

        <p class="brand-foot">© {{ date('Y') }} SGTP &middot; Todos os direitos reservados</p>
    </aside>

    <!-- ════════════════ FORM ════════════════ -->
    <main class="panel-form" role="main">
        <div class="form-shell">

            <!-- Heading -->
            <div class="form-head">
                <p class="label-area">Área Restrita</p>
                <h3>Bem&#8209;vindo de volta</h3>
                <p>Insira as suas credenciais institucionais para aceder ao sistema.</p>
            </div>

            <!-- Server errors (Laravel) -->
            @if($errors->any())
            <div class="alert-err show" role="alert" aria-live="assertive">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf

                <!-- E-mail -->
                <div class="field">
                    <label for="email">E-mail institucional</label>
                    <div class="inp-wrap">
                        <i class="fas fa-at ico-l" aria-hidden="true"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="utilizador@dominio.ao"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            inputmode="email"
                            aria-describedby="email-msg"
                        >
                        <i class="fas fa-circle-check st-ico ok"  id="e-ok"  aria-hidden="true"></i>
                        <i class="fas fa-circle-xmark st-ico err" id="e-err" aria-hidden="true"></i>
                    </div>
                    <p class="f-msg" id="email-msg" role="alert"></p>
                </div>

                <!-- Senha -->
                <div class="field">
                    <label for="password">Senha</label>
                    <div class="inp-wrap pw-wrap">
                        <i class="fas fa-lock ico-l" aria-hidden="true"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••••"
                            required
                            autocomplete="current-password"
                            aria-describedby="pw-msg"
                        >
                        <i class="fas fa-circle-check st-ico ok"  id="pw-ok"  aria-hidden="true"></i>
                        <i class="fas fa-circle-xmark st-ico err" id="pw-err" aria-hidden="true"></i>
                        <button
                            type="button"
                            class="btn-eye"
                            id="eyeBtn"
                            aria-label="Mostrar senha"
                        >
                            <i class="fas fa-eye" id="eyeIco"></i>
                        </button>
                    </div>
                    <p class="f-msg" id="pw-msg" role="alert"></p>
                </div>

                <!-- Options -->
                <div class="opts">
                    <label class="chk">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Lembrar-me</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-forgot">Esqueceu a senha?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="loginBtn">
                    <div class="spin" aria-hidden="true"></div>
                    <i class="fas fa-arrow-right-to-bracket btn-ico" aria-hidden="true"></i>
                    <span id="btnTxt">Entrar no Sistema</span>
                </button>

            </form>

            <!-- Footer -->
            <footer class="form-foot">
                Problemas de acesso?
                Contacte o <a href="mailto:suporte@sgtp.ao">suporte técnico</a>.
            </footer>

        </div>
    </main>

</div><!-- /.split -->

<script>
(() => {
    'use strict';

    /* ── EMAIL REGEX (RFC-5322, robusto) ─────────── */
    const EMAIL_RE = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/;

    const form     = document.getElementById('loginForm');
    const emailEl  = document.getElementById('email');
    const pwEl     = document.getElementById('password');
    const eyeBtn   = document.getElementById('eyeBtn');
    const eyeIco   = document.getElementById('eyeIco');
    const loginBtn = document.getElementById('loginBtn');
    const btnTxt   = document.getElementById('btnTxt');

    /* ── toggle password visibility ── */
    eyeBtn.addEventListener('click', () => {
        const hidden     = pwEl.type === 'password';
        pwEl.type        = hidden ? 'text' : 'password';
        eyeIco.className = hidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        eyeBtn.setAttribute('aria-label', hidden ? 'Ocultar senha' : 'Mostrar senha');
        pwEl.focus();
    });

    /* ── set field state ── */
    function setState(input, okId, errId, msgId, msg) {
        const hasErr = !!msg;
        const hasVal = input.value.trim().length > 0;
        const hasOk  = !hasErr && hasVal;

        input.classList.toggle('is-invalid', hasErr);
        input.classList.toggle('is-valid',   hasOk);

        document.getElementById(okId) .classList.toggle('on', hasOk);
        document.getElementById(errId).classList.toggle('on', hasErr);

        const msgEl = document.getElementById(msgId);
        msgEl.textContent = msg || '';
        msgEl.classList.toggle('on', hasErr);
    }

    /* ── validate email ── */
    function checkEmail(force = false) {
        const val = emailEl.value.trim();
        if (!val && !force) { setState(emailEl, 'e-ok', 'e-err', 'email-msg', ''); return true; }
        if (!val)           { setState(emailEl, 'e-ok', 'e-err', 'email-msg', 'O e-mail é obrigatório.'); return false; }
        if (!EMAIL_RE.test(val)) {
            setState(emailEl, 'e-ok', 'e-err', 'email-msg', 'Endereço de e-mail inválido.');
            return false;
        }
        setState(emailEl, 'e-ok', 'e-err', 'email-msg', '');
        return true;
    }

    /* ── validate password ── */
    function checkPw(force = false) {
        const val = pwEl.value;
        if (!val && !force) { setState(pwEl, 'pw-ok', 'pw-err', 'pw-msg', ''); return true; }
        if (!val)           { setState(pwEl, 'pw-ok', 'pw-err', 'pw-msg', 'A senha é obrigatória.'); return false; }
        if (val.length < 6) { setState(pwEl, 'pw-ok', 'pw-err', 'pw-msg', 'Mínimo de 6 caracteres.'); return false; }
        setState(pwEl, 'pw-ok', 'pw-err', 'pw-msg', '');
        return true;
    }

    /* ── live events ── */
    emailEl.addEventListener('blur',  () => checkEmail(true));
    emailEl.addEventListener('input', () => checkEmail(false));
    pwEl.addEventListener('blur',  () => checkPw(true));
    pwEl.addEventListener('input', () => checkPw(false));

    /* ── submit ── */
    form.addEventListener('submit', e => {
        const okE = checkEmail(true);
        const okP = checkPw(true);
        if (!okE || !okP) { e.preventDefault(); return; }

        loginBtn.disabled = true;
        loginBtn.classList.add('loading');
        btnTxt.textContent = 'Autenticando…';
    });

    /* ── ripple ── */
    loginBtn.addEventListener('pointerdown', function(e) {
        if (this.disabled) return;
        const r    = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const s    = Math.max(rect.width, rect.height);
        r.className = 'ripple';
        Object.assign(r.style, {
            width:  s + 'px',
            height: s + 'px',
            left:   (e.clientX - rect.left  - s / 2) + 'px',
            top:    (e.clientY - rect.top   - s / 2) + 'px',
        });
        this.appendChild(r);
        r.addEventListener('animationend', () => r.remove(), { once: true });
    });

})();
</script>
</body>
</html>