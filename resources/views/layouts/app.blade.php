<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LedgerNusa' }}</title>
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
            --ln-paper: #F7F7F5;
            --ln-line: #E4E7ED;
            --ln-ink-soft: #5B6577;
            --ln-ink-faint: #8B96A9;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: var(--ln-paper); color: var(--ln-navy); -webkit-font-smoothing: antialiased; }
        .ln-display { font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif; }

        /* ---- Header ---- */
        .ln-header { background: #FFFFFF; border-bottom: 1px solid var(--ln-line); }
        .ln-brand { display: flex; align-items: center; }
        .ln-brand img { height: 26px; width: auto; display: block; }

        .ln-company { font-size: 12.5px; color: var(--ln-ink-soft); padding: 5px 12px; border: 1px solid var(--ln-line); border-radius: 999px; }
        .ln-link { font-size: 13px; color: var(--ln-cyan); font-weight: 500; }
        .ln-link:hover { color: var(--ln-navy); }
        .ln-logout { font-size: 12.5px; color: var(--ln-ink-faint); }
        .ln-logout:hover { color: #C0392B; }

        /* ---- Alerts ---- */
        .ln-alert { border-radius: 8px; padding: 10px 14px; font-size: 13.5px; border-left: 3px solid; }
        .ln-alert-success { background: #F2FAF6; border-color: #2FA36B; color: #1E5B3D; }
        .ln-alert-error { background: #FDF3F2; border-color: #C0392B; color: #8A2B22; }
        .ln-alert-error ul { margin-top: 2px; }

        /* ---- Bottom nav ---- */
        .ln-navbar { background: #FFFFFF; border-top: 1px solid var(--ln-line); box-shadow: 0 -8px 20px rgba(16, 26, 48, 0.06); }
        .ln-navitem { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 10px 0 calc(10px + env(safe-area-inset-bottom, 0px)); font-size: 10.5px; color: var(--ln-ink-faint); transition: color 0.15s ease; }
        .ln-navitem svg { width: 20px; height: 20px; stroke: var(--ln-ink-faint); transition: stroke 0.15s ease; }
        .ln-navitem.active { color: var(--ln-navy); font-weight: 600; }
        .ln-navitem.active svg { stroke: var(--ln-cyan); }

        /* ---- Shared content components ---- */
        .ln-page-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 22px; }
        .ln-page-title { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 23px; font-weight: 600; letter-spacing: -0.01em; color: var(--ln-navy); }
        .ln-page-sub { font-size: 13px; color: var(--ln-ink-faint); margin-top: 3px; }

        .ln-stat { position: relative; background: #FFFFFF; border: 1px solid var(--ln-line); border-radius: 12px; padding: 16px 18px 17px; overflow: hidden; }
        .ln-stat::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 2.5px; background: var(--ln-stat-accent, var(--ln-line)); }
        .ln-stat .top { display: flex; align-items: flex-start; justify-content: space-between; }
        .ln-stat .lbl { font-size: 12px; color: var(--ln-ink-faint); }
        .ln-stat .ico { width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; background: var(--ln-stat-tint, #F1F3F6); }
        .ln-stat .ico svg { width: 14px; height: 14px; stroke: var(--ln-stat-accent, var(--ln-ink-faint)); }
        .ln-stat .val { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 20px; font-weight: 600; margin-top: 10px; color: var(--ln-navy); font-variant-numeric: tabular-nums; }
        .ln-stat.kas { --ln-stat-accent: #1E7A4C; --ln-stat-tint: rgba(30,122,76,0.1); }
        .ln-stat.bank { --ln-stat-accent: #1C8FC4; --ln-stat-tint: rgba(28,143,196,0.1); }
        .ln-stat.akun { --ln-stat-accent: #B4915B; --ln-stat-tint: rgba(180,145,91,0.12); }
        .ln-stat.jurnal { --ln-stat-accent: #14213A; --ln-stat-tint: rgba(20,33,58,0.07); }

        .ln-btn-primary { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; background: var(--ln-navy); color: #fff; font-size: 13.5px; font-weight: 500; border-radius: 8px; transition: background 0.15s ease; }
        .ln-btn-primary:hover { background: var(--ln-cyan); }
        .ln-btn-outline { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; background: #fff; color: var(--ln-navy); font-size: 13.5px; font-weight: 500; border: 1px solid var(--ln-line); border-radius: 8px; transition: all 0.15s ease; }
        .ln-btn-outline:hover { border-color: var(--ln-cyan); color: var(--ln-cyan); }

        .ln-section-title { font-family: 'Outfit', ui-sans-serif, sans-serif; font-size: 15px; font-weight: 600; color: var(--ln-navy); margin-bottom: 12px; }

        .ln-table-card { background: #fff; border: 1px solid var(--ln-line); border-radius: 12px; overflow: hidden; }
        .ln-table { width: 100%; font-size: 13.5px; border-collapse: collapse; }
        .ln-table thead th { text-align: left; font-size: 11px; letter-spacing: 0.03em; color: var(--ln-ink-faint); font-weight: 500; padding: 12px 16px; border-bottom: 1px solid var(--ln-line); background: #FBFBFA; }
        .ln-table thead th.num { text-align: right; }
        .ln-table thead th.center { text-align: center; }
        .ln-table tbody tr { transition: background 0.12s ease; }
        .ln-table tbody tr:hover { background: #FAFBFC; }
        .ln-table tbody td { padding: 13px 16px; border-bottom: 1px solid var(--ln-line); color: var(--ln-navy); }
        .ln-table tbody tr:last-child td { border-bottom: none; }
        .ln-table tbody td.num { text-align: right; font-variant-numeric: tabular-nums; font-weight: 500; }
        .ln-table tbody td.center { text-align: center; }
        .ln-table a.ref { color: var(--ln-cyan); font-weight: 500; }
        .ln-table a.ref:hover { color: var(--ln-navy); }
        .ln-table td.empty { text-align: center; padding: 40px 14px; color: var(--ln-ink-faint); font-size: 13px; }

        .ln-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 500; }
        .ln-badge::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .ln-badge.posted { background: rgba(30, 122, 76, 0.1); color: #1E7A4C; }
        .ln-badge.draft { background: rgba(180, 145, 91, 0.14); color: #8A6B3B; }

        .ln-action { font-size: 12px; color: var(--ln-cyan); font-weight: 500; }
        .ln-action:hover { color: var(--ln-navy); }
        .ln-action.green { color: #1E7A4C; background: none; border: none; }
        .ln-action.green:hover { color: #14532D; }
        .ln-action-sep { color: var(--ln-line); margin: 0 6px; }

        .ln-pagination { margin-top: 18px; }
        .ln-pagination nav { font-size: 13px; }

        /* ---- Forms ---- */
        .ln-form-card { background: #fff; border: 1px solid var(--ln-line); border-radius: 12px; padding: 22px; }
        .ln-label { display: block; font-size: 12.5px; color: var(--ln-ink-soft); margin-bottom: 6px; }
        .ln-input, .ln-textarea, .ln-select {
            width: 100%; border: 1px solid var(--ln-line); border-radius: 8px;
            padding: 9px 11px; font-size: 13.5px; color: var(--ln-navy); background: #fff;
            transition: border-color 0.15s ease;
        }
        .ln-input:focus, .ln-textarea:focus, .ln-select:focus { outline: none; border-color: var(--ln-cyan); }
        .ln-input-sm { padding: 7px 9px; font-size: 13px; }

        .ln-entries-table { width: 100%; font-size: 13.5px; border-collapse: collapse; }
        .ln-entries-table thead th { text-align: left; font-size: 11px; letter-spacing: 0.03em; color: var(--ln-ink-faint); font-weight: 500; padding: 10px 8px; border-bottom: 1px solid var(--ln-line); background: #FBFBFA; }
        .ln-entries-table thead th.num { text-align: right; }
        .ln-entries-table tbody td { padding: 8px; border-bottom: 1px solid var(--ln-line); }
        .ln-entries-table tfoot td { padding: 10px 8px; font-weight: 600; color: var(--ln-navy); background: #FBFBFA; font-variant-numeric: tabular-nums; }

        .ln-remove-btn { color: #C0392B; opacity: 0.75; font-size: 13px; background: none; border: none; }
        .ln-remove-btn:hover { opacity: 1; }
        .ln-add-row { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #fff; border: 1px dashed var(--ln-line); border-radius: 8px; font-size: 13px; color: var(--ln-ink-soft); }
        .ln-add-row:hover { border-color: var(--ln-cyan); color: var(--ln-cyan); }

        .ln-balance-ok { color: #1E7A4C; font-size: 13px; font-weight: 500; }
        .ln-balance-bad { color: #C0392B; font-size: 13px; font-weight: 500; }

        .ln-form-actions { padding-top: 6px; display: flex; gap: 10px; }
        .ln-btn-cancel { padding: 9px 16px; background: #fff; border: 1px solid var(--ln-line); border-radius: 8px; font-size: 13.5px; color: var(--ln-ink-soft); }
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
    </style>
</head>
<body class="min-h-screen">

<header class="ln-header sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-3.5 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="ln-brand">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
        </a>
        <div class="flex items-center gap-3">
            @php $activeCompany = auth()->user()->activeCompany(); @endphp
            @if($activeCompany)
                <span class="hidden sm:inline ln-company">{{ $activeCompany->name }}</span>
            @endif
            <a href="{{ route('companies.index') }}" class="ln-link">Company</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="ln-logout">Logout</button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 pb-24">
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

<nav class="ln-navbar fixed bottom-0 left-0 right-0 grid grid-cols-5">
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
    <a href="{{ route('contacts.index') }}" class="ln-navitem {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c.8-3 2.8-4.6 5.5-4.6s4.7 1.6 5.5 4.6"/><path d="M15.5 8.3a3 3 0 1 1 3.6 2.9"/><path d="M16 14.6c2.2.3 3.8 1.8 4.5 4.4"/></svg>
        Kontak
    </a>
    <a href="{{ route('companies.index') }}" class="ln-navitem {{ request()->routeIs('companies.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.6"/><path d="M12 3.5v2.3M12 18.2v2.3M20.5 12h-2.3M5.8 12H3.5M17.8 6.2l-1.6 1.6M7.8 16.2l-1.6 1.6M17.8 17.8l-1.6-1.6M7.8 7.8 6.2 6.2"/></svg>
        Setting
    </a>
</nav>

</body>
</html>