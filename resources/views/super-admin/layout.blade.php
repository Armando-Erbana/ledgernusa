<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Super Admin — LedgerNusa' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sa-navy: #0F1729;
            --sa-navy-2: #1A2540;
            --sa-cyan: #1C8FC4;
            --sa-line: #2A3654;
            --sa-ink: #E8ECF4;
            --sa-ink-soft: #96A1BC;
            --sa-paper: #F5F6FA;
            --sa-green: #10B981;
            --sa-yellow: #F59E0B;
            --sa-red: #EF4444;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--sa-paper); color: #1A2540; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, .display { font-family: 'Outfit', sans-serif; letter-spacing: -0.01em; }

        /* Sidebar (desktop) */
        .sa-layout { display: flex; min-height: 100vh; }
        .sa-sidebar {
            width: 240px; background: var(--sa-navy); color: var(--sa-ink);
            padding: 24px 0; position: fixed; top: 0; bottom: 0; left: 0;
            display: flex; flex-direction: column;
        }
        .sa-brand {
            padding: 0 24px 24px; border-bottom: 1px solid var(--sa-line);
            font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 17px;
        }
        .sa-brand small { display: block; font-size: 11px; color: var(--sa-cyan); font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 3px; }
        .sa-nav { padding: 20px 12px; flex: 1; }
        .sa-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; color: var(--sa-ink-soft); font-size: 13.5px;
            text-decoration: none; border-radius: 8px; transition: all 0.15s ease; margin-bottom: 2px;
        }
        .sa-nav a:hover { background: var(--sa-navy-2); color: var(--sa-ink); }
        .sa-nav a.active { background: var(--sa-cyan); color: #fff; }
        .sa-nav a svg { width: 16px; height: 16px; stroke: currentColor; flex-shrink: 0; }
        .sa-sidebar-footer {
            padding: 16px 24px; border-top: 1px solid var(--sa-line);
            font-size: 12px; color: var(--sa-ink-soft);
        }
        .sa-sidebar-footer a { color: var(--sa-ink-soft); text-decoration: none; display: block; margin-top: 6px; }
        .sa-sidebar-footer a:hover { color: var(--sa-ink); }

        .sa-main { flex: 1; margin-left: 240px; padding: 32px 40px; max-width: 1300px; }

        /* Mobile top bar */
        .sa-mobile-nav {
            display: none;
            background: var(--sa-navy); color: var(--sa-ink);
            padding: 12px 16px; justify-content: space-between; align-items: center;
        }
        .sa-mobile-nav .brand { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 15px; }

        /* Cards */
        .sa-head { margin-bottom: 28px; }
        .sa-title { font-size: 26px; font-weight: 600; color: var(--sa-navy); }
        .sa-sub { font-size: 13.5px; color: #6B7694; margin-top: 4px; }

        .sa-grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .sa-stat { background: #fff; border: 1px solid #E1E4EC; border-radius: 12px; padding: 20px; position: relative; overflow: hidden; }
        .sa-stat::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--sa-accent, var(--sa-cyan)); }
        .sa-stat .lbl { font-size: 12px; color: #7A84A0; }
        .sa-stat .val { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 600; margin-top: 8px; color: var(--sa-navy); }
        .sa-stat .sub { font-size: 11.5px; color: #96A1BC; margin-top: 4px; }
        .sa-stat.cyan { --sa-accent: var(--sa-cyan); }
        .sa-stat.green { --sa-accent: var(--sa-green); }
        .sa-stat.yellow { --sa-accent: var(--sa-yellow); }
        .sa-stat.red { --sa-accent: var(--sa-red); }
        .sa-stat.navy { --sa-accent: var(--sa-navy); }

        .sa-card { background: #fff; border: 1px solid #E1E4EC; border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .sa-card-head { padding: 18px 22px; border-bottom: 1px solid #EEF0F5; display: flex; justify-content: space-between; align-items: center; }
        .sa-card-title { font-family: 'Outfit', sans-serif; font-size: 15.5px; font-weight: 600; color: var(--sa-navy); }
        .sa-card-body { padding: 18px 22px; }

        table.sa-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        table.sa-table th { text-align: left; padding: 12px 22px; font-size: 11px; letter-spacing: 0.05em; color: #7A84A0; font-weight: 500; background: #FAFBFD; border-bottom: 1px solid #EEF0F5; text-transform: uppercase; }
        table.sa-table td { padding: 14px 22px; border-bottom: 1px solid #F1F3F8; color: #24304D; }
        table.sa-table tr:last-child td { border-bottom: none; }
        table.sa-table tr:hover { background: #FAFBFD; }
        table.sa-table a { color: var(--sa-cyan); text-decoration: none; font-weight: 500; }
        table.sa-table a:hover { color: var(--sa-navy); }

        .sa-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 500; }
        .sa-badge::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .sa-badge.trial { background: rgba(245, 158, 11, 0.12); color: #B45309; }
        .sa-badge.active { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .sa-badge.expired { background: rgba(239, 68, 68, 0.12); color: #B91C1C; }
        .sa-badge.cancelled { background: rgba(107, 114, 128, 0.12); color: #4B5563; }
        .sa-badge.past_due { background: rgba(239, 68, 68, 0.12); color: #B91C1C; }
        .sa-badge.paid { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .sa-badge.unpaid { background: rgba(245, 158, 11, 0.12); color: #B45309; }

        .sa-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 500; border-radius: 8px; border: none; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
        .sa-btn-primary { background: var(--sa-navy); color: #fff; }
        .sa-btn-primary:hover { background: var(--sa-cyan); }
        .sa-btn-cyan { background: var(--sa-cyan); color: #fff; }
        .sa-btn-cyan:hover { background: #1573a0; }
        .sa-btn-outline { background: #fff; color: var(--sa-navy); border: 1px solid #E1E4EC; }
        .sa-btn-outline:hover { border-color: var(--sa-navy); }
        .sa-btn-danger { background: var(--sa-red); color: #fff; }
        .sa-btn-danger:hover { background: #dc2626; }
        .sa-btn-sm { padding: 6px 10px; font-size: 12px; }

        .sa-input, .sa-select {
            width: 100%; border: 1px solid #E1E4EC; border-radius: 8px;
            padding: 9px 12px; font-size: 13.5px; color: #1A2540;
            background: #fff; font-family: inherit;
        }
        .sa-input:focus, .sa-select:focus { outline: none; border-color: var(--sa-cyan); }
        .sa-label { display: block; font-size: 12px; color: #6B7694; margin-bottom: 5px; }

        .sa-alert { border-radius: 8px; padding: 12px 16px; font-size: 13.5px; margin-bottom: 20px; border-left: 3px solid; }
        .sa-alert-success { background: #ECFDF5; border-color: var(--sa-green); color: #065F46; }
        .sa-alert-error { background: #FEF2F2; border-color: var(--sa-red); color: #991B1B; }

        @media (max-width: 900px) {
            .sa-sidebar { display: none; }
            .sa-main { margin-left: 0; padding: 20px 16px; }
            .sa-mobile-nav { display: flex; }
            .sa-title { font-size: 20px; }
            .sa-stat .val { font-size: 22px; }
            table.sa-table th, table.sa-table td { padding: 10px 14px; }
        }
    </style>
</head>
<body>

<div class="sa-mobile-nav">
    <div class="brand">LedgerNusa · Super Admin</div>
    <a href="{{ route('dashboard') }}" class="sa-btn sa-btn-sm sa-btn-outline">← App</a>
</div>

<div class="sa-layout">
    <aside class="sa-sidebar">
        <div class="sa-brand">
            LedgerNusa
            <small>Super Admin</small>
        </div>

        <nav class="sa-nav">
            <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('super-admin.tenants.index') }}" class="{{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                Tenants
            </a>
            <a href="{{ route('dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9"/></svg>
                App Utama
            </a>
        </nav>

        <div class="sa-sidebar-footer">
            Login sebagai
            <strong style="color:#E8ECF4; display:block; margin-top:2px;">{{ auth()->user()->name }}</strong>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; color: inherit; cursor: pointer; padding: 0; font-size: 12px; margin-top: 8px;">Logout →</button>
            </form>
        </div>
    </aside>

    <main class="sa-main">
        @if(session('success'))
            <div class="sa-alert sa-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="sa-alert sa-alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>