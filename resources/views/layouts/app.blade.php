<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SGTP') — Sistema de Gestão de Transportes Públicos</title>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts — mesmas do login -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300&family=Bebas+Neue&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════
           DESIGN TOKENS — alinhados 100% com o login
        ═══════════════════════════════════════════════════ */
        :root {
            /* brand */
            --navy:        #0d2e5e;
            --navy-deep:   #07203f;
            --navy-mid:    #1a4d8c;
            --navy-light:  #eef4ff;
            --amber:       #f39c12;
            --amber-dark:  #e67e22;
            --amber-dim:   rgba(243,156,18,.14);
            --amber-glow:  rgba(243,156,18,.22);

            /* semantic (compat with existing blade views) */
            --primary:       #1a4d8c;
            --primary-dark:  #07203f;
            --primary-light: #eef4ff;
            --accent:        #f39c12;
            --accent-dark:   #e67e22;
            --accent-light:  #fff3e0;

            /* grays */
            --gray-50:  #f8fafc;
            --gray-100: #f4f6fb;
            --gray-200: #eef2f6;
            --gray-300: #e2e8f0;
            --gray-400: #cbd5e1;
            --gray-500: #94a3b8;
            --gray-600: #64748b;
            --gray-700: #475569;
            --gray-800: #1e293b;
            --gray-900: #0f172a;

            /* status */
            --success: #10b981;
            --danger:  #ef4444;
            --warning: #f59e0b;
            --info:    #3b82f6;

            /* layout */
            --sidebar-w:      268px;
            --topbar-h:       64px;
            --sidebar-radius: 0px;

            /* motion */
            --ease:     cubic-bezier(.4,0,.2,1);
            --ease-out: cubic-bezier(0,.55,.45,1);
            --t-fast:   .16s;
            --t-mid:    .26s;
        }

        /* ═══════════════════════════════════════════════════
           RESET
        ═══════════════════════════════════════════════════ */
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        html {
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            overflow-x: hidden;
        }

        /* ═══════════════════════════════════════════════════
           APP SHELL
        ═══════════════════════════════════════════════════ */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--navy-deep);
            color: white;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform var(--t-mid) var(--ease);
            /* subtle internal border */
            box-shadow: 1px 0 0 rgba(255,255,255,.04), 4px 0 24px rgba(0,0,0,.18);
        }

        /* grid texture — mesmo do painel brand do login */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.018) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* mesh gradient overlay */
        .sidebar::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 10% 90%, rgba(26,77,140,.45) 0%, transparent 65%),
                radial-gradient(ellipse 50% 40% at 80% 5%,  rgba(243,156,18,.05) 0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        /* amber bottom accent stripe */
        .sidebar-stripe {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber), transparent);
            opacity: .45;
            z-index: 2;
        }

        /* ── sidebar header ── */
        .sidebar-header {
            position: relative;
            z-index: 1;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
        }

        .logo-badge {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--amber);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(243,156,18,.3);
        }

        .logo-badge i {
            font-size: 1.2rem;
            color: var(--navy-deep);
        }

        .logo-text {
            line-height: 1;
        }

        .logo-text h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: 2px;
            color: white;
        }

        .logo-text h2 em {
            font-style: normal;
            color: var(--amber);
        }

        .logo-text p {
            font-size: .67rem;
            font-weight: 500;
            color: rgba(255,255,255,.45);
            letter-spacing: .04em;
            margin-top: 3px;
        }

        /* ── nav ── */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 14px 12px;
            position: relative;
            z-index: 1;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .nav-menu { list-style: none; }

        .nav-section-label {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(255,255,255,.28);
            padding: 16px 10px 6px;
        }

        .nav-item { margin-bottom: 2px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 11px 14px;
            border-radius: 12px;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .87rem;
            font-weight: 500;
            transition:
                background var(--t-fast) var(--ease),
                color     var(--t-fast) var(--ease),
                transform var(--t-fast) var(--ease);
            position: relative;
            overflow: hidden;
        }

        .nav-link .nav-ico {
            width: 20px;
            font-size: 1rem;
            flex-shrink: 0;
            text-align: center;
        }

        /* shimmer on hover */
        .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.06);
            opacity: 0;
            transition: opacity var(--t-fast);
        }

        .nav-link:hover { color: white; transform: translateX(3px); }
        .nav-link:hover::before { opacity: 1; }

        .nav-link.active {
            background: var(--amber);
            color: var(--navy-deep);
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(243,156,18,.28);
            transform: none;
        }

        .nav-link.active::before { display: none; }

        .nav-link.active .nav-ico { color: var(--navy-deep); }

        /* ── sidebar footer (user mini) ── */
        .sidebar-foot {
            position: relative;
            z-index: 1;
            border-top: 1px solid rgba(255,255,255,.07);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 11px;
            flex-shrink: 0;
        }

        .sf-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(243,156,18,.18);
            border: 1.5px solid rgba(243,156,18,.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--amber);
            font-size: .9rem;
            flex-shrink: 0;
        }

        .sf-info { flex: 1; min-width: 0; }

        .sf-name {
            font-size: .8rem;
            font-weight: 600;
            color: rgba(255,255,255,.85);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sf-role {
            font-size: .68rem;
            color: rgba(255,255,255,.35);
            margin-top: 1px;
        }

        .sf-logout {
            background: none;
            border: none;
            padding: 6px;
            border-radius: 8px;
            color: rgba(255,255,255,.35);
            cursor: pointer;
            font-size: .85rem;
            transition: color var(--t-fast), background var(--t-fast);
            display: flex;
            align-items: center;
        }

        .sf-logout:hover {
            color: var(--amber);
            background: rgba(243,156,18,.1);
            transform: none;
        }

        /* ═══════════════════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════════════════ */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left var(--t-mid) var(--ease);
        }

        /* ── topbar ── */
        .topbar {
            height: var(--topbar-h);
            background: white;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 1px 0 var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        /* hamburger */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            width: 36px; height: 36px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--gray-600);
            cursor: pointer;
            flex-shrink: 0;
            transition: background var(--t-fast), color var(--t-fast);
        }

        .menu-toggle:hover { background: var(--gray-100); color: var(--navy); transform: none; }

        /* breadcrumb / page title */
        .page-title h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.55rem;
            letter-spacing: .5px;
            color: var(--navy);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* topbar right */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        /* icon buttons */
        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 11px;
            border: none;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            font-size: .95rem;
            cursor: pointer;
            position: relative;
            transition: background var(--t-fast), color var(--t-fast);
        }

        .topbar-btn:hover { background: var(--gray-100); color: var(--navy); transform: none; }

        /* notification badge */
        .notif-badge {
            position: absolute;
            top: 4px; right: 4px;
            min-width: 16px; height: 16px;
            background: var(--amber);
            color: var(--navy-deep);
            font-size: .6rem;
            font-weight: 800;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            line-height: 1;
        }

        /* divider */
        .topbar-sep {
            width: 1px;
            height: 28px;
            background: var(--gray-200);
            margin: 0 4px;
        }

        /* user avatar button */
        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: var(--navy-light);
            border: 1.5px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-mid);
            font-size: .95rem;
            cursor: pointer;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
        }

        .user-avatar:hover {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px var(--amber-dim);
            transform: none;
        }

        /* dropdown */
        .dropdown-wrap {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,.12), 0 0 0 1px rgba(0,0,0,.03);
            min-width: 230px;
            display: none;
            z-index: 1000;
            overflow: hidden;
            animation: dropIn .18s var(--ease-out) both;
        }

        @keyframes dropIn {
            from { opacity:0; transform:translateY(-6px) scale(.97); }
            to   { opacity:1; transform:translateY(0)    scale(1);   }
        }

        .dropdown-menu.show { display: block; }

        .dropdown-header {
            padding: 14px 18px 10px;
        }

        .dropdown-user-name {
            font-size: .88rem;
            font-weight: 700;
            color: var(--navy);
        }

        .dropdown-user-email {
            font-size: .75rem;
            color: var(--gray-500);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 4px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            font-size: .85rem;
            font-weight: 500;
            color: var(--gray-700);
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            transition: background var(--t-fast), color var(--t-fast);
        }

        .dropdown-item:hover { background: var(--gray-50); color: var(--navy); }

        .dropdown-item i {
            width: 18px;
            font-size: .85rem;
            color: var(--amber);
            flex-shrink: 0;
        }

        .dropdown-item.logout { color: var(--danger); }
        .dropdown-item.logout i { color: var(--danger); }
        .dropdown-item.logout:hover { background: #fff1f1; }

        /* ═══════════════════════════════════════════════════
           CONTENT AREA
        ═══════════════════════════════════════════════════ */
        .dashboard-container {
            flex: 1;
            padding: clamp(20px, 3vw, 32px) clamp(16px, 3.5vw, 36px);
        }

        /* ── alerts ── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 13px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: .87rem;
            font-weight: 500;
            animation: fadeIn .35s var(--ease-out) both;
        }

        .alert i { margin-top: 1px; flex-shrink: 0; }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #ffedd5;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        /* ── stats grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 22px 20px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            transition: transform var(--t-mid) var(--ease), box-shadow var(--t-mid) var(--ease);
            animation: fadeIn .4s var(--ease-out) both;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
        }

        .stat-title {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--gray-500);
        }

        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.4rem;
            letter-spacing: .5px;
            color: var(--navy);
            margin: 10px 0 2px;
            line-height: 1;
        }

        .stat-icon {
            float: right;
            font-size: 2.4rem;
            color: var(--navy-light);
        }

        /* ── card ── */
        .row-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 22px 22px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            animation: fadeIn .4s var(--ease-out) both;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1.5px solid var(--gray-100);
        }

        .card-header-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.15rem;
            letter-spacing: .5px;
            color: var(--navy);
        }

        /* legacy .card-header used in existing blades */
        .card-header {
            font-weight: 600;
            color: var(--gray-700);
        }

        /* ── tables ── */
        .table-responsive { overflow-x: auto; }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 11px 10px;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
            font-size: .87rem;
        }

        .data-table th {
            font-size: .71rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--gray-500);
        }

        .data-table tr:last-child td { border-bottom: none; }

        .data-table tbody tr {
            transition: background var(--t-fast);
        }

        .data-table tbody tr:hover { background: var(--gray-50); }

        /* ── status badges ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 100px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .status-ativo,   .status-success { background: #d1fae5; color: #065f46; }
        .status-em-andamento, .status-warning { background: #fef3c7; color: #92400e; }
        .status-pendente, .status-info   { background: #dbeafe; color: #1e40af; }
        .status-inativo, .status-danger  { background: #fee2e2; color: #991b1b; }

        /* ── buttons ── */
        button, .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border: none;
            border-radius: 100px;
            background: var(--navy-mid);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .86rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition:
                background var(--t-fast) var(--ease),
                transform  var(--t-fast) var(--ease),
                box-shadow var(--t-fast) var(--ease);
        }

        button:hover, .btn:hover {
            background: var(--navy);
            box-shadow: 0 6px 16px rgba(13,46,94,.25);
            transform: translateY(-1px);
            color: white;
        }

        button:active, .btn:active { transform: translateY(0); box-shadow: none; }

        .btn-accent {
            background: var(--amber);
            color: var(--navy-deep);
        }

        .btn-accent:hover {
            background: var(--amber-dark);
            color: var(--navy-deep);
            box-shadow: 0 6px 16px rgba(243,156,18,.3);
        }

        .btn-danger { background: var(--danger); }
        .btn-danger:hover { background: #b91c1c; }

        .btn-ghost {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .btn-ghost:hover {
            background: var(--gray-200);
            color: var(--gray-900);
            box-shadow: none;
        }

        .btn-sm {
            padding: 5px 13px;
            font-size: .78rem;
        }

        /* ── forms ── */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            margin-bottom: 7px;
            font-size: .79rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--gray-600);
        }

        .form-control {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1.5px solid var(--gray-300);
            border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .9rem;
            color: var(--navy);
            background: var(--gray-50);
            -webkit-appearance: none;
            transition:
                border-color var(--t-mid) var(--ease),
                box-shadow   var(--t-mid) var(--ease),
                background   var(--t-mid) var(--ease);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--amber);
            background: white;
            box-shadow: 0 0 0 3.5px var(--amber-dim);
        }

        textarea.form-control {
            height: auto;
            padding: 12px 14px;
            resize: vertical;
        }

        select.form-control { cursor: pointer; }

        /* ── pagination ── */
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin-top: 22px;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px; height: 36px;
            padding: 0 10px;
            border-radius: 10px;
            text-decoration: none;
            font-size: .84rem;
            font-weight: 600;
            color: var(--gray-600);
            background: white;
            border: 1px solid var(--gray-200);
            transition: background var(--t-fast), color var(--t-fast), border-color var(--t-fast);
        }

        .pagination a:hover {
            background: var(--navy-light);
            border-color: var(--navy-mid);
            color: var(--navy);
        }

        .pagination .active span {
            background: var(--amber);
            border-color: var(--amber);
            color: var(--navy-deep);
        }

        /* ── overlay (mobile sidebar backdrop) ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(7,32,63,.55);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.show { display: block; }

        /* ═══════════════════════════════════════════════════
           UTILITIES
        ═══════════════════════════════════════════════════ */
        @keyframes fadeIn {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0);   }
        }

        .fade-in { animation: fadeIn .4s var(--ease-out) both; }

        /* ═══════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }

            .sidebar.open { transform: translateX(0); }

            .main-content { margin-left: 0; }

            .menu-toggle { display: flex; }
        }

        @media (max-width: 768px) {
            .topbar { padding: 0 16px; }

            .page-title h1 { font-size: 1.25rem; }

            .dashboard-container { padding: 16px; }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .row-cards { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-container">

    <!-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-stripe" aria-hidden="true"></div>

        <!-- logo -->
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo-area" style="text-decoration:none;">
                <div class="logo-badge">
                    <i class="fas fa-bus" aria-hidden="true"></i>
                </div>
                <div class="logo-text">
                    <h2>SG<em>T</em>P</h2>
                    <p>Transportes Públicos</p>
                </div>
            </a>
        </div>

        <!-- nav -->
        <nav class="sidebar-nav" aria-label="Navegação principal">
            <ul class="nav-menu">

                @can('access-dashboard')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="fas fa-gauge-high nav-ico" aria-hidden="true"></i>
                        Dashboard
                    </a>
                </li>
                @endcan

                @can('access-frota')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frota.*') ? 'active' : '' }}"
                       href="{{ route('frota.index') }}">
                        <i class="fas fa-truck nav-ico" aria-hidden="true"></i>
                        Gestão de Frota
                    </a>
                </li>
                @endcan

                @can('access-colaboradores')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('colaboradores.*') ? 'active' : '' }}"
                       href="{{ route('colaboradores.index') }}">
                        <i class="fas fa-users nav-ico" aria-hidden="true"></i>
                        Colaboradores
                    </a>
                </li>
                @endcan

                @can('access-operacoes')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rotas.*') || request()->routeIs('horarios.*') || request()->routeIs('escalas.*') ? 'active' : '' }}"
                       href="{{ route('rotas.index') }}">
                        <i class="fas fa-map-location-dot nav-ico" aria-hidden="true"></i>
                        Rotas &amp; Horários
                    </a>
                </li>
                @endcan

                @can('access-bilhetica')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bilhetica.*') ? 'active' : '' }}"
                       href="{{ route('tarifas.index') }}">
                        <i class="fas fa-ticket nav-ico" aria-hidden="true"></i>
                        Bilhética
                    </a>
                </li>
                @endcan

                @can('access-ocorrencias')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ocorrencias.*') ? 'active' : '' }}"
                       href="{{ route('ocorrencias.index') }}">
                        <i class="fas fa-triangle-exclamation nav-ico" aria-hidden="true"></i>
                        Ocorrências
                    </a>
                </li>
                @endcan

                @can('access-manutencoes')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manutencoes.*') ? 'active' : '' }}"
                       href="{{ route('manutencoes.index') }}">
                        <i class="fas fa-wrench nav-ico" aria-hidden="true"></i>
                        Manutenções
                    </a>
                </li>
                @endcan

                @can('access-financeiro')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('financeiro.*') ? 'active' : '' }}"
                       href="{{ route('receitas.index') }}">
                        <i class="fas fa-chart-line nav-ico" aria-hidden="true"></i>
                        Financeiro
                    </a>
                </li>
                @endcan

                @can('access-relatorios')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('relatorios.*') ? 'active' : '' }}"
                       href="{{ route('relatorios.cumprimento-horarios') }}">
                        <i class="fas fa-file-lines nav-ico" aria-hidden="true"></i>
                        Relatórios
                    </a>
                </li>
                @endcan

                @can('gerir_usuarios')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                       href="{{ route('admin.usuarios.index') }}">
                        <i class="fas fa-shield-halved nav-ico" aria-hidden="true"></i>
                        Administração
                    </a>
                </li>
                @endcan

            </ul>
        </nav>

        <!-- sidebar user footer -->
        <div class="sidebar-foot">
            <div class="sf-avatar" aria-hidden="true">
                <i class="fas fa-user"></i>
            </div>
            <div class="sf-info">
                <div class="sf-name">{{ Auth::user()->name }}</div>
                <div class="sf-role">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm">
                @csrf
                <button type="submit" class="sf-logout" title="Sair do sistema" aria-label="Sair do sistema">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- ══════════════════════════════════════
         MAIN
    ══════════════════════════════════════ -->
    <div class="main-content">

        <!-- topbar -->
        <header class="topbar" role="banner">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
                <div class="page-title">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Notifications -->
                <div class="dropdown-wrap">
                    <button class="topbar-btn" id="notificationBell" aria-label="Notificações">
                        <i class="fas fa-bell" aria-hidden="true"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="notif-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                        @endif
                    </button>
                </div>

                <div class="topbar-sep" aria-hidden="true"></div>

                <!-- User dropdown -->
                <div class="dropdown-wrap" id="userDropdownWrap">
                    <button class="user-avatar" id="userAvatar" aria-label="Menu do utilizador" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user" aria-hidden="true"></i>
                    </button>

                    <div class="dropdown-menu" id="userDropdown" role="menu">
                        <div class="dropdown-header">
                            <div class="dropdown-user-name">{{ Auth::user()->name }}</div>
                            <div class="dropdown-user-email">{{ Auth::user()->email }}</div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('profile') }}" class="dropdown-item" id="profileBtn" role="menuitem">
                            <i class="fas fa-id-card" aria-hidden="true"></i>
                            Meu Perfil
                        </a>

                        <a href="{{ route('admin.configuracoes.index') }}" class="dropdown-item" id="settingsBtn" role="menuitem">
                            <i class="fas fa-gear" aria-hidden="true"></i>
                            Configurações
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                            @csrf
                            <button type="submit" class="dropdown-item logout" role="menuitem">
                                <i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                Sair do Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- page content -->
        <main class="dashboard-container" id="mainContent">

            @if(session('success'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                {{ session('error') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                {{ session('warning') }}
            </div>
            @endif

            @yield('content')

        </main>
    </div><!-- /.main-content -->

</div><!-- /.app-container -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ── sidebar toggle (mobile) ── */
    const sidebar         = document.getElementById('sidebar');
    const menuToggle      = document.getElementById('menuToggle');
    const sidebarOverlay  = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        sidebarOverlay.classList.add('show');
        menuToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('show');
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
    }

    sidebarOverlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
    });

    /* ── user dropdown ── */
    const userAvatar   = document.getElementById('userAvatar');
    const userDropdown = document.getElementById('userDropdown');

    if (userAvatar && userDropdown) {
        userAvatar.addEventListener('click', e => {
            e.stopPropagation();
            const open = userDropdown.classList.toggle('show');
            userAvatar.setAttribute('aria-expanded', open);
        });

        document.addEventListener('click', () => {
            if (userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
                userAvatar.setAttribute('aria-expanded', 'false');
            }
        });

        userDropdown.addEventListener('click', e => e.stopPropagation());
    }

    /* ── notifications ── */
    const notificationBell = document.getElementById('notificationBell');
    if (notificationBell) {
        notificationBell.addEventListener('click', () => {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                });
        });
    }

    /* ── auto-dismiss alerts ── */
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .4s, transform .4s';
            alert.style.opacity    = '0';
            alert.style.transform  = 'translateY(-6px)';
            setTimeout(() => alert.remove(), 420);
        }, 5000);
    });
});
</script>

@stack('scripts')
</body>
</html>