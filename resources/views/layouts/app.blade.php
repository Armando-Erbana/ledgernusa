<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LedgerNusa' }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#14213A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="LedgerNusa">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ln-navy: #14213A;
            --ln-navy-deep: #101A30;
            --ln-cyan: #1C8FC4;
            --ln-cyan-bright: #4FC3EC;
            --ln-brass: #B4915B;
            --ln-paper: #F6F6F3;
            --ln-line: #E4E7ED;
            --ln-ink-soft: #5B6577;
            --ln-ink-faint: #8B96A9;
            --ln-shadow-sm: 0 1px 2px rgba(16, 26, 48, 0.04), 0 4px 14px rgba(16, 26, 48, 0.04);
            --ln-shadow-md: 0 2px 4px rgba(16, 26, 48, 0.04), 0 12px 28px rgba(16, 26, 48, 0.08);
        }
        * { box-sizing: border-box; }
        html { scroll-padding-top: 72px; }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: var(--ln-navy);
            -webkit-font-smoothing: antialiased;
            background-color: var(--ln-paper);
            background-image:
                radial-gradient(900px 380px at 100% -80px, rgba(79, 195, 236, 0.13), transparent 70%),
                radial-gradient(700px 320px at -10% -60px, rgba(180, 145, 91, 0.08), transparent 70%);
            background-repeat: no-repeat;
        }
        .ln-display { font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif; }
        :focus-visible { outline: 2px solid var(--ln-cyan); outline-offset: 2px; border-radius: 6px; }

        /* ---- Header ---- */
        .ln-header {
            position: sticky;
            background: rgba(255, 255, 255, 0.82);
            -webkit-backdrop-filter: saturate(180%) blur(14px);
            backdrop-filter: saturate(180%) blur(14px);
            border-bottom: 1px solid var(--ln-line);
            padding-top: env(safe-area-inset-top, 0px);
        }
        .ln-header::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 1.5px;
            background: linear-gradient(90deg, var(--ln-navy) 0%, var(--ln-cyan) 45%, var(--ln-brass) 100%);
            opacity: 0.55;
        }
        .ln-brand { display: flex; align-items: center; }
        .ln-brand img { height: 28px; width: auto; display: block; }

        .ln-company {
            display: inline-block;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ln-ink-soft);
            padding: 5px 12px;
            background: #fff;
            border: 1px solid var(--ln-line);
            border-radius: 999px;
        }
        .ln-link {
            font-size: 13px;
            color: var(--ln-cyan);
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .ln-link:hover { color: var(--ln-navy); background: rgba(20, 33, 58, 0.05); }
        .ln-logout {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ln-ink-faint);
            padding: 6px 10px;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .ln-logout:hover { color: #C0392B; background: rgba(192, 57, 43, 0.07); }

        /* ---- Trial banner ---- */
        .ln-trial {
            background: linear-gradient(90deg, #FEF3C7 0%, #FDE68A 100%);
            border-bottom: 1px solid #F59E0B;
            padding: 10px 16px;
            text-align: center;
            font-size: 13px;
            color: #78350F;
        }
        .ln-trial a { color: #78350F; text-decoration: underline; font-weight: 600; }

        /* ---- Alerts ---- */
        .ln-alert {
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 13.5px;
            border-left: 3px solid;
            box-shadow: var(--ln-shadow-sm);
        }
        .ln-alert-success { background: #F2FAF6; border-color: #2FA36B; color: #1E5B3D; }
        .ln-alert-error { background: #FDF3F2; border-color: #C0392B; color: #8A2B22; }
        .ln-alert-error ul { margin-top: 2px; }

        /* ---- Main ---- */
        main { animation: ln-rise 0.35s ease both; }
        main.ln-main { padding-bottom: calc(7rem + env(safe-area-inset-bottom, 0px)); }
        @keyframes ln-rise {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: none; }
        }

        /* ---- Bottom nav ---- */
        nav.ln-navbar {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            z-index: 40;
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            align-items: stretch;
            background: rgba(255, 255, 255, 0.88);
            -webkit-backdrop-filter: saturate(180%) blur(16px);
            backdrop-filter: saturate(180%) blur(16px);
            border-top: 1px solid var(--ln-line);
            box-shadow: 0 -8px 24px rgba(16, 26, 48, 0.06);
        }
        .ln-navitem {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 9px 2px calc(9px + env(safe-area-inset-bottom, 0px));
            font-size: 10px;
            color: var(--ln-ink-faint);
            transition: color 0.15s ease;
            text-align: center;
            line-height: 1.1;
        }
        .ln-navitem svg {
            width: 21px; height: 21px;
            stroke: var(--ln-ink-faint);
            transition: stroke 0.15s ease, transform 0.15s ease;
            flex-shrink: 0;
        }
        .ln-navitem:hover svg { transform: translateY(-1px); }
        .ln-navitem.active { color: var(--ln-navy); font-weight: 600; }
        .ln-navitem.active svg { stroke: var(--ln-cyan); }
        .ln-navitem.active::before {
            content: "";
            position: absolute;
            top: 0; left: 50%;
            width: 26px; height: 3px;
            transform: translateX(-50%);
            border-radius: 0 0 3px 3px;
            background: linear-gradient(90deg, var(--ln-cyan), var(--ln-cyan-bright));
        }

        @media (min-width: 768px) {
            nav.ln-navbar {
                left: 50%; right: auto;
                width: min(560px, calc(100% - 32px));
                bottom: 16px;
                transform: translateX(-50%);
                border: 1px solid var(--ln-line);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: var(--ln-shadow-md);
            }
            .ln-navitem { padding-bottom: 9px; font-size: 10.5px; }
            .ln-navitem:hover { background: rgba(20, 33, 58, 0.03); }
        }

        /* ---- Shared content components ---- */
        .ln-page-head { display: flex; flex-wrap: wrap; gap: 10px 16px; align-items: baseline; justify-content: space-between; margin-bottom: 22px; }
        .ln-page-title { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 24px; font-weight: 600; letter-spacing: -0.015em; color: var(--ln-navy); }
        .ln-page-sub { font-size: 13px; color: var(--ln-ink-faint); margin-top: 3px; }

        .ln-stat {
            position: relative;
            background: #FFFFFF;
            border: 1px solid var(--ln-line);
            border-radius: 14px;
            padding: 16px 18px 17px;
            overflow: hidden;
            box-shadow: var(--ln-shadow-sm);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .ln-stat:hover { transform: translateY(-2px); box-shadow: var(--ln-shadow-md); }
        .ln-stat::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--ln-stat-accent, var(--ln-line)); }
        .ln-stat .top { display: flex; align-items: flex-start; justify-content: space-between; }
        .ln-stat .lbl { font-size: 12px; font-weight: 500; color: var(--ln-ink-faint); }
        .ln-stat .ico { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--ln-stat-tint, #F1F3F6); }
        .ln-stat .ico svg { width: 15px; height: 15px; stroke: var(--ln-stat-accent, var(--ln-ink-faint)); }
        .ln-stat .val { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 21px; font-weight: 600; letter-spacing: -0.01em; margin-top: 10px; color: var(--ln-navy); font-variant-numeric: tabular-nums; word-break: break-word; }
        .ln-stat.kas { --ln-stat-accent: #1E7A4C; --ln-stat-tint: rgba(30,122,76,0.1); }
        .ln-stat.bank { --ln-stat-accent: #1C8FC4; --ln-stat-tint: rgba(28,143,196,0.1); }
        .ln-stat.akun { --ln-stat-accent: #B4915B; --ln-stat-tint: rgba(180,145,91,0.12); }
        .ln-stat.jurnal { --ln-stat-accent: #14213A; --ln-stat-tint: rgba(20,33,58,0.07); }

        .ln-btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 16px;
            background: linear-gradient(180deg, #1B2C4B 0%, var(--ln-navy) 100%);
            color: #fff; font-size: 13.5px; font-weight: 500;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(16, 26, 48, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
        }
        .ln-btn-primary:hover { background: linear-gradient(180deg, #2AA0D6 0%, var(--ln-cyan) 100%); box-shadow: 0 6px 16px rgba(28, 143, 196, 0.3); }
        .ln-btn-primary:active { transform: scale(0.98); }
        .ln-btn-outline {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 16px; background: #fff; color: var(--ln-navy);
            font-size: 13.5px; font-weight: 500;
            border: 1px solid var(--ln-line); border-radius: 10px;
            transition: all 0.15s ease;
        }
        .ln-btn-outline:hover { border-color: var(--ln-cyan); color: var(--ln-cyan); background: rgba(28, 143, 196, 0.03); }
        .ln-btn-outline:active { transform: scale(0.98); }

        .ln-section-title { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 15px; font-weight: 600; color: var(--ln-navy); margin-bottom: 12px; }

        .ln-table-card {
            background: #fff;
            border: 1px solid var(--ln-line);
            border-radius: 14px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: var(--ln-shadow-sm);
        }
        .ln-table { width: 100%; font-size: 13.5px; border-collapse: collapse; }
        .ln-table thead th { text-align: left; font-size: 11px; letter-spacing: 0.04em; color: var(--ln-ink-faint); font-weight: 500; padding: 12px 16px; border-bottom: 1px solid var(--ln-line); background: #FBFBFA; white-space: nowrap; }
        .ln-table thead th.num { text-align: right; }
        .ln-table thead th.center { text-align: center; }
        .ln-table tbody tr { transition: background 0.12s ease; }
        .ln-table tbody tr:hover { background: #F8FAFC; }
        .ln-table tbody td { padding: 13px 16px; border-bottom: 1px solid var(--ln-line); color: var(--ln-navy); }
        .ln-table tbody tr:last-child td { border-bottom: none; }
        .ln-table tbody td.num { text-align: right; font-variant-numeric: tabular-nums; font-weight: 500; white-space: nowrap; }
        .ln-table tbody td.center { text-align: center; }
        .ln-table a.ref { color: var(--ln-cyan); font-weight: 500; }
        .ln-table a.ref:hover { color: var(--ln-navy); }
        .ln-table td.empty { text-align: center; padding: 44px 14px; color: var(--ln-ink-faint); font-size: 13px; }

        .ln-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 500; }
        .ln-badge::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .ln-badge.posted { background: rgba(30, 122, 76, 0.1); color: #1E7A4C; }
        .ln-badge.draft { background: rgba(180, 145, 91, 0.14); color: #8A6B3B; }

        .ln-action { font-size: 12px; color: var(--ln-cyan); font-weight: 500; transition: color 0.15s ease; }
        .ln-action:hover { color: var(--ln-navy); }
        .ln-action.green { color: #1E7A4C; background: none; border: none; }
        .ln-action.green:hover { color: #14532D; }
        .ln-action-sep { color: var(--ln-line); margin: 0 6px; }

        .ln-pagination { margin-top: 18px; }
        .ln-pagination nav { font-size: 13px; }

        /* ---- Forms ---- */
        .ln-form-card { background: #fff; border: 1px solid var(--ln-line); border-radius: 14px; padding: 22px; box-shadow: var(--ln-shadow-sm); }
        .ln-label { display: block; font-size: 12.5px; font-weight: 500; color: var(--ln-ink-soft); margin-bottom: 6px; }
        .ln-input, .ln-textarea, .ln-select {
            width: 100%; border: 1px solid var(--ln-line); border-radius: 10px;
            padding: 9px 12px; font-size: 13.5px; color: var(--ln-navy); background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ln-input::placeholder, .ln-textarea::placeholder { color: #B3BBC9; }
        .ln-input:focus, .ln-textarea:focus, .ln-select:focus {
            outline: none;
            border-color: var(--ln-cyan);
            box-shadow: 0 0 0 4px rgba(28, 143, 196, 0.13);
        }
        .ln-input-sm { padding: 7px 9px; font-size: 13px; }

        .ln-entries-table { width: 100%; font-size: 13.5px; border-collapse: collapse; }
        .ln-entries-table thead th { text-align: left; font-size: 11px; letter-spacing: 0.04em; color: var(--ln-ink-faint); font-weight: 500; padding: 10px 8px; border-bottom: 1px solid var(--ln-line); background: #FBFBFA; }
        .ln-entries-table thead th.num { text-align: right; }
        .ln-entries-table tbody td { padding: 8px; border-bottom: 1px solid var(--ln-line); }
        .ln-entries-table tfoot td { padding: 10px 8px; font-weight: 600; color: var(--ln-navy); background: #FBFBFA; font-variant-numeric: tabular-nums; }

        .ln-remove-btn { color: #C0392B; opacity: 0.75; font-size: 13px; background: none; border: none; transition: opacity 0.15s ease; }
        .ln-remove-btn:hover { opacity: 1; }
        .ln-add-row { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #fff; border: 1px dashed #CBD2DE; border-radius: 10px; font-size: 13px; color: var(--ln-ink-soft); transition: all 0.15s ease; }
        .ln-add-row:hover { border-color: var(--ln-cyan); color: var(--ln-cyan); background: rgba(28, 143, 196, 0.03); }

        .ln-balance-ok { color: #1E7A4C; font-size: 13px; font-weight: 500; }
        .ln-balance-bad { color: #C0392B; font-size: 13px; font-weight: 500; }

        .ln-form-actions { padding-top: 6px; display: flex; gap: 10px; }
        .ln-btn-cancel { padding: 9px 16px; background: #fff; border: 1px solid var(--ln-line); border-radius: 10px; font-size: 13.5px; color: var(--ln-ink-soft); transition: all 0.15s ease; }
        .ln-btn-cancel:hover { color: var(--ln-navy); border-color: var(--ln-navy); }

        /* ---- Account type badges (COA) ---- */
        .ln-code { font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 12.5px; color: var(--ln-ink-soft); }
        .ln-type { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 500; padding: 2px 9px; border-radius: 999px; text-transform: capitalize; }
        .ln-type::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .ln-type.aset, .ln-type.asset { background: rgba(28,143,196,0.1); color: #1C8FC4; }
        .ln-type.kewajiban, .ln-type.liabilitas, .ln-type.liability { background: rgba(192,57,43,0.1); color: #A93226; }
        .ln-type.ekuitas, .ln-type.equity { background: rgba(180,145,91,0.14); color: #8A6B3B; }
        .ln-type.pendapatan, .ln-type.revenue { background: rgba(30,122,76,0.1); color: #1E7A4C; }
        .ln-type.beban, .ln-type.expense { background: rgba(20,33,58,0.08); color: #14213A; }

        .ln-action.red { color: #C0392B; background: none; border: none; }
        .ln-action.red:hover { color: #8A2B22; }

        .ln-checkbox { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--ln-ink-soft); }
        .ln-checkbox input { width: 15px; height: 15px; border-radius: 4px; border: 1.5px solid var(--ln-line); accent-color: var(--ln-navy); }

        /* ---- Mobile ---- */
        @media (max-width: 640px) {
            /* 16px mencegah iOS zoom otomatis saat input difokuskan */
            .ln-input, .ln-textarea, .ln-select { font-size: 16px; }
            .ln-input-sm { font-size: 15px; }
            .ln-page-title { font-size: 21px; }
            .ln-form-card { padding: 18px; }
            .ln-company { max-width: 120px; }
        }

        @media (prefers-reduced-motion: reduce) {
            main { animation: none; }
            * { transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body class="min-h-screen">

<header class="ln-header sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-3.5 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="ln-brand">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
        </a>
        <div class="flex items-center gap-1.5 sm:gap-3">
            @php $activeCompany = auth()->user()->activeCompany(); @endphp
            @if($activeCompany)
                <span class="ln-company">{{ $activeCompany->name }}</span>
            @endif
            <a href="{{ route('companies.index') }}" class="ln-link">Company</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="ln-logout">Logout</button>
            </form>
        </div>
    </div>
</header>

@if(session('trial_days') !== null && session('trial_days') <= 5)
    <div class="ln-trial">
        ⏰ Trial Anda tersisa <strong>{{ session('trial_days') }} hari</strong>.
        <a href="{{ route('subscription.index') }}">Perpanjang sekarang</a>
    </div>
@endif

<main class="ln-main max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="ln-alert ln-alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ln-alert ln-alert-error mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="ln-alert ln-alert-error mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<nav class="ln-navbar fixed bottom-0 left-0 right-0 grid grid-cols-6">
    <a href="{{ route('dashboard') }}" class="ln-navitem {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9"/></svg>
        Dashboard
    </a>
    <a href="{{ route('journals.index') }}" class="ln-navitem {{ request()->routeIs('journals.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M5 17.5V7.5a3 3 0 0 1 3-3"/><path d="M9 9h7M9 12.5h7"/></svg>
        Jurnal
    </a>
    <a href="{{ route('accounts.index') }}" class="ln-navitem {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20V10M12 20V4M19 20v-7"/></svg>
        COA
    </a>
    <a href="{{ route('reports.index') }}" class="ln-navitem {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4M12 15V9M16 15v-6"/></svg>
        Laporan
    </a>
    <a href="{{ route('subscription.index') }}" class="ln-navitem {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19"/></svg>
        Langganan
    </a>
    <a href="{{ route('companies.index') }}" class="ln-navitem {{ request()->routeIs('companies.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.6"/><path d="M12 3.5v2.3M12 18.2v2.3M20.5 12h-2.3M5.8 12H3.5M17.8 6.2l-1.6 1.6M7.8 16.2l-1.6 1.6M17.8 17.8l-1.6-1.6M7.8 7.8 6.2 6.2"/></svg>
        Setting
    </a>
</nav>

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
</script>

</body>
</html>