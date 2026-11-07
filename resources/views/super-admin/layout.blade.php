<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') · Super Admin @else{{ $title ?? 'Super Admin — LedgerNusa' }}@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @php
        /*
         * Daftar menu sidebar. Item hanya tampil kalau route-nya ada (Route::has),
         * jadi aman: tambah route baru -> menu otomatis muncul, tanpa error kalau belum dibuat.
         * 'match' = pola route untuk status aktif.
         */
        $saMenu = [
            'Utama' => [
                ['Dashboard',       'super-admin.dashboard',              'super-admin.dashboard',        '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
                ['Tenants',         'super-admin.tenants.index',          'super-admin.tenants.*',        '<path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1M9 13h1M9 17h1"/>'],
            ],
            'Penagihan' => [
                ['Bukti Transfer',  'super-admin.payment-proofs.index',   'super-admin.payment-proofs.*', '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18"/><path d="M7 15h4"/>'],
                ['Paket',           'super-admin.plans.index',            'super-admin.plans.*',          '<path d="M12 3 3 7.5 12 12l9-4.5L12 3z"/><path d="m3 12 9 4.5 9-4.5"/><path d="m3 16.5 9 4.5 9-4.5"/>'],
                ['Invoice',         'super-admin.invoices.index',         'super-admin.invoices.*',       '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6"/>'],
            ],
            'Sistem' => [
                ['Pengguna',        'super-admin.users.index',            'super-admin.users.*',          '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.2a6.5 6.5 0 0 1 3.5 5.8"/>'],
                ['Log Aktivitas',   'super-admin.activity-logs.index',    'super-admin.activity-logs.*',  '<path d="M3 12h4l3-8 4 16 3-8h4"/>'],
                ['Pengaturan',      'super-admin.settings.index',         'super-admin.settings.*',       '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>'],
            ],
        ];
        $saUser = auth()->user();
        $saInitial = strtoupper(mb_substr($saUser->name ?? 'SA', 0, 2));
    @endphp

    <style>
        :root {
            --b1: #1D4ED8; --b2: #06B6D4; --navy: #0B1B3F; --ink: #0F1E3D; --mute: #6B7A99;
            --line: rgba(15,30,61,.08);
            --sa-navy: var(--navy); --sa-cyan: var(--b1); --sa-green: #10B981; --sa-yellow: #F59E0B; --sa-red: #EF4444;
            --side-w: 252px;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Inter', sans-serif; margin: 0; color: var(--ink); -webkit-font-smoothing: antialiased;
            background: #F4F7FC;
            background-image:
                radial-gradient(520px 420px at 92% -6%, rgba(6,182,212,.20), transparent 70%),
                radial-gradient(480px 420px at 6% 78%, rgba(29,78,216,.13), transparent 70%);
            background-attachment: fixed;
        }
        h1, h2, h3, .display { font-family: 'Outfit', sans-serif; letter-spacing: -0.01em; }
        a { -webkit-tap-highlight-color: transparent; }

        /* ============ SIDEBAR ============ */
        .sa-layout { display: flex; min-height: 100vh; }
        .sa-sidebar {
            position: fixed; z-index: 50; top: 12px; left: 12px; bottom: 12px; width: var(--side-w);
            display: flex; flex-direction: column; padding: 18px 12px 12px;
            background: rgba(255,255,255,.66);
            backdrop-filter: blur(20px) saturate(170%); -webkit-backdrop-filter: blur(20px) saturate(170%);
            border: 1px solid rgba(255,255,255,.8); border-radius: 22px;
            box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 18px 40px -18px rgba(15,30,61,.22), 0 0 0 1px var(--line);
            transition: transform .28s cubic-bezier(.2,.8,.2,1);
        }
        .sa-brand { display: flex; align-items: center; gap: 12px; padding: 4px 10px 18px; margin-bottom: 6px; border-bottom: 1px solid var(--line); text-decoration: none; }
        .sa-logo {
            flex: none; width: 36px; height: 36px; border-radius: 11px; display: grid; place-items: center;
            font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 17px; color: #fff;
            background: linear-gradient(135deg, var(--b1), var(--b2));
            box-shadow: 0 8px 16px -8px rgba(29,78,216,.65), inset 0 1px 0 rgba(255,255,255,.3);
        }
        .sa-brand-t { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 16.5px; color: var(--ink); line-height: 1.1; }
        .sa-brand-t small { display: block; margin-top: 4px; font-family: 'Inter', sans-serif; font-size: 10.5px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--b1); }

        .sa-nav { flex: 1; overflow-y: auto; padding: 8px 0; scrollbar-width: none; }
        .sa-nav::-webkit-scrollbar { display: none; }
        .sa-group { margin-bottom: 14px; }
        .sa-group-t { padding: 8px 12px 6px; font-size: 10.5px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: #9AA6C0; }
        .sa-nav a {
            position: relative; display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; margin-bottom: 2px; border-radius: 12px;
            font-size: 13.5px; font-weight: 500; color: #4A5878; text-decoration: none; transition: .18s;
        }
        .sa-nav a svg { flex: none; width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; opacity: .85; }
        .sa-nav a:hover { background: rgba(29,78,216,.07); color: var(--b1); }
        .sa-nav a.active {
            color: #fff; font-weight: 600;
            background: linear-gradient(135deg, var(--b1), var(--b2));
            box-shadow: 0 10px 20px -10px rgba(29,78,216,.7), inset 0 1px 0 rgba(255,255,255,.28);
        }
        .sa-nav a.active svg { opacity: 1; }

        .sa-side-foot { padding-top: 12px; border-top: 1px solid var(--line); display: flex; flex-direction: column; gap: 8px; }
        .sa-app-link {
            display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 12px;
            font-size: 12.5px; font-weight: 600; color: var(--ink); text-decoration: none;
            background: rgba(255,255,255,.7); border: 1px solid var(--line); transition: .18s;
        }
        .sa-app-link:hover { background: #fff; border-color: rgba(29,78,216,.35); color: var(--b1); }
        .sa-me { display: flex; align-items: center; gap: 11px; padding: 8px 6px 4px; }
        .sa-me-av {
            flex: none; width: 36px; height: 36px; border-radius: 11px; display: grid; place-items: center; font-size: 12.5px; font-weight: 700; color: var(--b1);
            background: linear-gradient(135deg, rgba(29,78,216,.12), rgba(6,182,212,.18)); border: 1px solid rgba(29,78,216,.14);
        }
        .sa-me-t { flex: 1; min-width: 0; }
        .sa-me-t b { display: block; font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sa-me-t span { font-size: 11.5px; color: var(--mute); }
        .sa-logout { flex: none; width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; border: 0; background: transparent; color: #9AA6C0; cursor: pointer; transition: .18s; }
        .sa-logout:hover { color: #B4233A; background: rgba(239,68,68,.1); }
        .sa-logout svg { width: 17px; height: 17px; stroke: currentColor; fill: none; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }

        .sa-main { flex: 1; min-width: 0; margin-left: calc(var(--side-w) + 24px); padding: 32px 36px 48px; max-width: 1360px; }

        /* ============ MOBILE ============ */
        .sa-mobile-nav {
            display: none; position: sticky; top: 0; z-index: 40; align-items: center; justify-content: space-between; gap: 12px;
            padding: calc(10px + env(safe-area-inset-top, 0px)) 14px 10px;
            background: rgba(255,255,255,.72); backdrop-filter: blur(18px) saturate(160%); -webkit-backdrop-filter: blur(18px) saturate(160%);
            border-bottom: 1px solid var(--line);
        }
        .sa-mobile-nav .m-brand { display: flex; align-items: center; gap: 10px; font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 15px; color: var(--ink); }
        .sa-mobile-nav .sa-logo { width: 30px; height: 30px; font-size: 14px; border-radius: 9px; }
        .sa-burger { width: 40px; height: 40px; display: grid; place-items: center; border-radius: 12px; border: 1px solid var(--line); background: rgba(255,255,255,.8); color: var(--ink); cursor: pointer; }
        .sa-burger svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; }
        .sa-overlay { display: none; position: fixed; inset: 0; z-index: 45; background: rgba(11,27,63,.38); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); }

        @media (max-width: 900px) {
            .sa-mobile-nav { display: flex; }
            .sa-sidebar { top: 0; left: 0; bottom: 0; border-radius: 0 22px 22px 0; padding-top: calc(18px + env(safe-area-inset-top, 0px)); transform: translateX(-110%); background: rgba(255,255,255,.92); }
            body.nav-open .sa-sidebar { transform: none; }
            body.nav-open .sa-overlay { display: block; }
            body.nav-open { overflow: hidden; }
            .sa-main { margin-left: 0; padding: 20px 16px 40px; }
        }

        /* ============ ALERT ============ */
        .sa-alert { display: flex; gap: 10px; align-items: flex-start; border-radius: 14px; padding: 13px 16px; font-size: 13.5px; margin-bottom: 20px; border: 1px solid; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .sa-alert::before { content: ""; flex: none; width: 7px; height: 7px; margin-top: 7px; border-radius: 50%; background: currentColor; }
        .sa-alert ul { margin: 0; padding-left: 16px; }
        .sa-alert-success { background: rgba(16,185,129,.10); border-color: rgba(16,185,129,.28); color: #0B6B50; }
        .sa-alert-error { background: rgba(239,68,68,.09); border-color: rgba(239,68,68,.28); color: #A0233A; }

        /* ============ KOMPONEN sa-* (kompatibel dgn halaman lama) ============ */
        .sa-head { margin-bottom: 26px; }
        .sa-title { font-size: 28px; font-weight: 700; letter-spacing: -.02em; color: var(--ink); margin: 0; }
        .sa-sub { font-size: 13.5px; color: var(--mute); margin: 4px 0 0; }

        .sa-grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .sa-stat, .sa-card {
            background: rgba(255,255,255,.62); backdrop-filter: blur(18px) saturate(160%); -webkit-backdrop-filter: blur(18px) saturate(160%);
            border: 1px solid rgba(255,255,255,.75); border-radius: 20px;
            box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 12px 32px -14px rgba(15,30,61,.18), 0 0 0 1px var(--line);
        }
        .sa-stat { padding: 20px; }
        .sa-stat .lbl { font-size: 12.5px; font-weight: 600; color: var(--mute); }
        .sa-stat .val { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 700; margin-top: 10px; color: var(--ink); letter-spacing: -.02em; }
        .sa-stat .sub { font-size: 12px; color: var(--mute); margin-top: 6px; }

        .sa-card { overflow: hidden; margin-bottom: 22px; }
        .sa-card-head { padding: 18px 22px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .sa-card-title { font-family: 'Outfit', sans-serif; font-size: 15.5px; font-weight: 700; color: var(--ink); }
        .sa-card-body { padding: 18px 22px; }

        table.sa-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        table.sa-table th { text-align: left; padding: 13px 20px; font-size: 11px; font-weight: 600; letter-spacing: .07em; color: var(--mute); background: rgba(15,30,61,.025); border-bottom: 1px solid var(--line); text-transform: uppercase; white-space: nowrap; }
        table.sa-table td { padding: 14px 20px; border-bottom: 1px solid var(--line); color: var(--ink); vertical-align: middle; }
        table.sa-table tr:last-child td { border-bottom: 0; }
        table.sa-table tbody tr { transition: background .15s; }
        table.sa-table tbody tr:hover { background: rgba(29,78,216,.045); }
        table.sa-table a { color: var(--b1); text-decoration: none; font-weight: 600; }
        table.sa-table a.sa-btn { text-decoration: none; }

        .sa-badge { display: inline-flex; align-items: center; gap: 7px; padding: 5px 11px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; color: var(--mute); background: rgba(15,30,61,.06); }
        .sa-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .sa-badge.trial, .sa-badge.unpaid, .sa-badge.pending { background: rgba(245,158,11,.14); color: #B45309; }
        .sa-badge.active, .sa-badge.paid, .sa-badge.approved { background: rgba(16,185,129,.12); color: #0E8F6B; }
        .sa-badge.expired, .sa-badge.past_due, .sa-badge.rejected, .sa-badge.suspended { background: rgba(239,68,68,.10); color: #B4233A; }
        .sa-badge.cancelled { background: rgba(15,30,61,.06); color: var(--mute); }

        .sa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 18px; font-size: 13px; font-weight: 600; border-radius: 12px; border: 0; cursor: pointer; text-decoration: none; transition: .18s; font-family: inherit; }
        .sa-btn-primary, .sa-btn-cyan { color: #fff; background: linear-gradient(135deg, var(--b1), var(--b2)); box-shadow: 0 8px 18px -8px rgba(29,78,216,.6), inset 0 1px 0 rgba(255,255,255,.3); }
        .sa-btn-primary:hover, .sa-btn-cyan:hover { transform: translateY(-1px); filter: brightness(1.05); }
        .sa-btn-outline { color: var(--ink); background: rgba(255,255,255,.75); border: 1px solid var(--line); }
        .sa-btn-outline:hover { background: #fff; border-color: rgba(29,78,216,.35); color: var(--b1); }
        .sa-btn-danger { color: #B4233A; background: rgba(255,255,255,.8); border: 1px solid rgba(239,68,68,.35); }
        .sa-btn-danger:hover { color: #fff; background: #C8283F; border-color: #C8283F; }
        .sa-btn-sm { height: 32px; padding: 0 13px; font-size: 12.5px; border-radius: 9px; }

        .sa-input, .sa-select { width: 100%; height: 42px; border: 1px solid var(--line); border-radius: 12px; padding: 0 14px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,.8); font-family: inherit; transition: .18s; }
        .sa-input:focus, .sa-select:focus { outline: none; border-color: var(--b1); box-shadow: 0 0 0 4px rgba(29,78,216,.12); background: #fff; }
        .sa-label { display: block; font-size: 12px; font-weight: 600; color: var(--mute); margin-bottom: 6px; }

        @media (max-width: 640px) {
            .sa-title { font-size: 23px; }
            .sa-stat .val { font-size: 22px; }
            table.sa-table th, table.sa-table td { padding: 12px 14px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Top bar mobile --}}
<div class="sa-mobile-nav">
    <div class="m-brand"><span class="sa-logo">N</span>Super Admin</div>
    <button type="button" class="sa-burger" onclick="saNav(true)" aria-label="Buka menu">
        <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
</div>
<div class="sa-overlay" onclick="saNav(false)"></div>

<div class="sa-layout">

    <aside class="sa-sidebar" id="saSidebar">
        <a href="{{ route('super-admin.dashboard') }}" class="sa-brand">
            <span class="sa-logo">N</span>
            <span class="sa-brand-t">LedgerNusa<small>Super Admin</small></span>
        </a>

        <nav class="sa-nav">
            @foreach($saMenu as $group => $items)
                @php $visible = collect($items)->filter(fn($i) => Route::has($i[1])); @endphp
                @if($visible->isNotEmpty())
                    <div class="sa-group">
                        <div class="sa-group-t">{{ $group }}</div>
                        @foreach($visible as [$label, $routeName, $match, $icon])
                            <a href="{{ route($routeName) }}" class="{{ request()->routeIs($match) ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="sa-side-foot">
            @if(Route::has('dashboard'))
                <a href="{{ route('dashboard') }}" class="sa-app-link">Buka Aplikasi <span>→</span></a>
            @endif
            <div class="sa-me">
                <span class="sa-me-av">{{ $saInitial }}</span>
                <div class="sa-me-t">
                    <b>{{ $saUser->name }}</b>
                    <span>Super Admin</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sa-logout" title="Logout" aria-label="Logout">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="sa-main">
        @if(session('success'))
            <div class="sa-alert sa-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="sa-alert sa-alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="sa-alert sa-alert-error">
                <ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
function saNav(open) { document.body.classList.toggle('nav-open', open); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') saNav(false); });
document.querySelectorAll('#saSidebar .sa-nav a').forEach(a => a.addEventListener('click', () => saNav(false)));
</script>
@stack('scripts')
</body>
</html>