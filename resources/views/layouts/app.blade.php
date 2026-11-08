<!DOCTYPE html>
<html lang="id">
<head>
@php
    /* ---------- SEO: judul & deskripsi per halaman ---------- */
    $lnTitle     = trim($__env->yieldContent('title')) ?: ($title ?? null);
    $lnTitleFull = $lnTitle ? $lnTitle . ' — LedgerNusa' : 'LedgerNusa — Aplikasi Akuntansi Online';
    $lnDesc      = trim($__env->yieldContent('meta_description'))
        ?: 'LedgerNusa adalah aplikasi akuntansi online untuk jurnal, buku besar, kas & bank, penjualan, pembelian, persediaan, dan laporan keuangan usaha Anda.';
    $lnRobots    = trim($__env->yieldContent('robots')) ?: 'noindex, nofollow';

    /* ---------- Menu utama (dock bawah) ---------- */
    $navMain = [
        ['Dashboard', 'dashboard',          'dashboard',      '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9"/>'],
        ['Kas',       'cash.index',         'cash.*',         '<rect x="2.5" y="6.5" width="19" height="12" rx="2"/><circle cx="12" cy="12.5" r="2.4"/>'],
        ['Penjualan', 'sales.index',        'sales.*',        '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>'],
        ['Langganan', 'subscription.index', 'subscription.*', '<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19"/>'],
        ['Jurnal',    'journals.index',     'journals.*',     '<path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7"/>'],
        ['Laporan',   'reports.index',      'reports.*',      '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4M12 15V9M16 15v-6"/>'],
    ];

    /* ---------- Menu "Lainnya" (teks saja) ---------- */
    $lnMore = [
        'Kas & Bank' => [
            ['Kas & Bank',      'cash.index',                    'cash.*'],
            ['Rekonsiliasi',    'bank-rec.index',                'bank-rec.*'],
            ['Transfer',        'transfers.index',               'transfers.*'],
        ],
        'Penjualan' => [
            ['Sales Order',     'sales-orders.index',            'sales-orders.*'],
            ['Delivery',        'delivery-orders.index',         'delivery-orders.*'],
            ['Penjualan',       'sales.index',                   'sales.*'],
            ['Customer',        'customers.index',               'customers.*'],
        ],
        'Pembelian' => [
            ['Purchase Order',  'purchase-orders.index',         'purchase-orders.*'],
            ['Penerimaan',      'goods-receipts.index',          'goods-receipts.*'],
            ['Pembelian',       'purchases.index',               'purchases.*'],
            ['Supplier',        'suppliers.index',               'suppliers.*'],
        ],
        'Persediaan' => [
            ['Produk',          'products.index',                'products.*'],
            ['Mutasi Stok',     'stock.index',                   'stock.*'],
            ['Gudang',          'warehouses.index',              'warehouses.*'],
            ['Kategori',        'product-categories.index',      'product-categories.*'],
        ],
        'Akuntansi' => [
            ['Akun (COA)',      'accounts.index',                'accounts.*'],
            ['Aset Tetap',      'fixed-assets.index',            'fixed-assets.*'],
            ['Pajak',           'tax.index',                     'tax.*'],
            ['Kontak',          'contacts.index',                'contacts.*'],
            ['Karyawan',        'employees.index',               'employees.*'],
            ['Payroll',         'payrolls.index',                'payrolls.*'],
        ],
        'Sistem' => [
            ['Undang User',     'invitations.index',             'invitations.*'],
            ['Setting',         'companies.index',               'companies.*'],
            ['Profil',          'profile.edit',                  'profile.*'],
            ['Currencies',      'settings.currencies.index',     'settings.currencies.*'],
            ['Kurs',            'settings.exchange-rates.index', 'settings.exchange-rates.*'],
        ],
    ];

    $isOn          = fn($m) => request()->routeIs(...(array) $m);
    $mainOnAll     = collect($navMain)->contains(fn($n) => $isOn($n[2]));
    $moreOn        = collect($lnMore)->flatten(1)->contains(fn($i) => $isOn($i[2]));
    $moreActive    = $moreOn && ! $mainOnAll;
    $activeCompany = auth()->user()->activeCompany();
@endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">

    <title>{{ $lnTitleFull }}</title>
    <meta name="description" content="{{ $lnDesc }}">
    <meta name="robots" content="{{ $lnRobots }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="LedgerNusa">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $lnTitleFull }}">
    <meta property="og:description" content="{{ $lnDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#F2F2F5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="LedgerNusa">
    <link rel="icon" type="image/png" href="/images/icon-192.png">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #1D1D1F; --soft: #515154; --mute: #6E6E73; --faint: #86868B;
            --hair: rgba(0,0,0,.08); --hair-2: rgba(0,0,0,.14);
            --glass: rgba(255,255,255,.78);
            --shadow-s: 0 1px 2px rgba(0,0,0,.04), 0 6px 20px -8px rgba(0,0,0,.10);
            --shadow-m: 0 2px 6px rgba(0,0,0,.05), 0 18px 44px -16px rgba(0,0,0,.18);
            --shadow-l: 0 24px 70px -20px rgba(0,0,0,.38);
            --ok: #2E9E6B; --warn: #C98A1B; --bad: #D04A4A;   /* hanya titik status kecil */
            --dock-h: 62px; --dock-gap: 14px;

            /* nama lama, supaya halaman yang belum diredesain tetap benar */
            --ln-navy: #1D1D1F; --ln-navy-deep: #000; --ln-cyan: #2C2C2E; --ln-cyan-bright: #6E6E73; --ln-brass: #8E8E93;
            --ln-paper: #ECECEF; --ln-line: var(--hair); --ln-ink-soft: var(--mute); --ln-ink-faint: var(--faint);
            --ln-shadow-sm: var(--shadow-s); --ln-shadow-md: var(--shadow-m); --ln-shadow-lg: var(--shadow-l);
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; scroll-padding-top: 90px; }
        html.ln-lock { overflow: hidden; }
        body {
            margin: 0; min-height: 100vh; color: var(--ink); background: #ECECEF;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Inter', 'Segoe UI', system-ui, sans-serif;
            letter-spacing: -.003em; -webkit-font-smoothing: antialiased; font-feature-settings: "tnum";
        }
        body::before {
            content: ""; position: fixed; inset: 0; z-index: -1; pointer-events: none;
            background:
                radial-gradient(720px 520px at 88% -8%, #fff 0%, rgba(255,255,255,0) 70%),
                radial-gradient(640px 520px at 4% 96%, #D6D6DC 0%, rgba(214,214,220,0) 70%),
                linear-gradient(180deg, #F3F3F6, #E8E8EC);
        }
        a { color: inherit; -webkit-tap-highlight-color: transparent; }
        :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; border-radius: 8px; }
        .ln-skip { position: absolute; left: 12px; top: -60px; z-index: 200; padding: 10px 16px; border-radius: 12px; background: var(--ink); color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: top .15s; }
        .ln-skip:focus { top: 12px; }

        /* ===== Glass ===== */
        .glass {
            background: var(--glass);
            -webkit-backdrop-filter: saturate(180%) blur(24px); backdrop-filter: saturate(180%) blur(24px);
            border: 1px solid rgba(255,255,255,.78);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-m);
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .glass, .ln-stat, .ln-table-card, .ln-form-card, .ln-alert, .ln-sheet { background: rgba(250,250,252,.97); }
        }

        /* ===== Header: logo + perusahaan + akun ===== */
        .ln-header { position: sticky; top: 0; z-index: 40; padding: calc(10px + env(safe-area-inset-top, 0px)) 12px 0; }
        .ln-bar { max-width: 1152px; margin: 0 auto; min-height: 56px; padding: 8px 12px 8px 18px; border-radius: 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .ln-brand { display: flex; align-items: center; flex: none; text-decoration: none; }
        .ln-brand img { height: 26px; width: auto; display: block; }
        .ln-right { display: flex; align-items: center; justify-content: flex-end; gap: 6px; min-width: 0; }
        .ln-right form { margin: 0; }
        .ln-company {
            display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; overflow-wrap: anywhere;
            max-width: min(46vw, 320px); padding: 6px 14px; border-radius: 12px; text-align: center; line-height: 1.25;
            font-size: 12.5px; font-weight: 500; color: var(--soft); text-decoration: none;
            background: rgba(255,255,255,.7); border: 1px solid var(--hair); transition: .16s;
        }
        .ln-company:hover { background: #fff; color: var(--ink); }
        .ln-hlink, .ln-hlogout {
            padding: 7px 11px; border-radius: 10px; border: 0; background: transparent; cursor: pointer; font-family: inherit;
            font-size: 13px; font-weight: 500; color: var(--mute); text-decoration: none; transition: .16s; white-space: nowrap;
        }
        .ln-hlink:hover { color: var(--ink); background: rgba(0,0,0,.05); }
        .ln-hlogout:hover { color: var(--bad); background: rgba(208,74,74,.08); }

        /* ===== Trial ===== */
        .ln-trial { max-width: 1152px; margin: 12px auto 0; padding: 0 16px; }
        .ln-trial > div { display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap; text-align: center; padding: 11px 16px; border-radius: 14px; font-size: 13px; color: var(--soft); }
        .ln-trial strong { color: var(--ink); }
        .ln-trial a { color: var(--ink); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }

        /* ===== Alerts & main ===== */
        .ln-alert { display: flex; gap: 11px; align-items: flex-start; padding: 12px 16px; border-radius: 14px; font-size: 13.5px; color: var(--ink);
            background: var(--glass); -webkit-backdrop-filter: blur(20px) saturate(170%); backdrop-filter: blur(20px) saturate(170%);
            border: 1px solid rgba(255,255,255,.78); box-shadow: 0 0 0 .5px var(--hair), var(--shadow-s); }
        .ln-alert::before { content: ""; flex: none; width: 8px; height: 8px; margin-top: 6px; border-radius: 50%; background: var(--dot, var(--faint)); }
        .ln-alert-success { --dot: var(--ok); }
        .ln-alert-error { --dot: var(--bad); }
        .ln-alert ul { margin: 0; padding-left: 16px; }

        main { animation: ln-rise .35s ease both; }
        @keyframes ln-rise { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        main.ln-main { padding-bottom: calc(var(--dock-h) + var(--dock-gap) + 3.5rem + env(safe-area-inset-bottom, 0px)); }

        /* ===== DOCK BAWAH =====
           Mobile : ikon tipis, hanya menu aktif yang menampilkan nama
           Desktop: teks saja, tanpa ikon */
        .ln-tabbar {
            position: fixed; z-index: 100; left: 12px; right: 12px;
            bottom: calc(var(--dock-gap) + env(safe-area-inset-bottom, 0px));
            height: var(--dock-h); padding: 6px; border-radius: 26px; display: flex; gap: 2px;
        }
        .ln-ti {
            position: relative; flex: 1 1 0; min-width: 0; display: flex; align-items: center; justify-content: center;
            border: 0; border-radius: 20px; background: transparent; cursor: pointer; overflow: hidden; white-space: nowrap;
            font-family: inherit; font-size: 12px; font-weight: 600; color: var(--faint); text-decoration: none;
            transition: flex-grow .3s cubic-bezier(.2,.8,.2,1), background .2s, color .2s;
        }
        .ln-ti svg { flex: none; width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; }
        .ln-ti .lbl { max-width: 0; margin-left: 0; opacity: 0; overflow: hidden; transition: max-width .3s cubic-bezier(.2,.8,.2,1), margin .3s, opacity .2s; }
        .ln-ti:hover { color: var(--ink); }
        .ln-ti.active, .ln-ti.open { flex-grow: 2.7; color: var(--ink); background: rgba(0,0,0,.07); }
        .ln-ti.active .lbl, .ln-ti.open .lbl { max-width: 84px; margin-left: 6px; opacity: 1; }
        /* saat menu "Lainnya" terbuka, hanya "Lainnya" yang aktif */
        .ln-tabbar.more-open .ln-ti.active:not(.open) { flex-grow: 1; color: var(--faint); background: transparent; }
        .ln-tabbar.more-open .ln-ti.active:not(.open) .lbl { max-width: 0; margin-left: 0; opacity: 0; }

        @media (min-width: 640px) {
            :root { --dock-h: 54px; --dock-gap: 22px; }
            .ln-tabbar { left: 50%; right: auto; width: min(680px, calc(100% - 32px)); transform: translateX(-50%); border-radius: 22px; }
            .ln-ti svg { display: none; }
            .ln-ti .lbl, .ln-ti.active .lbl, .ln-ti.open .lbl { max-width: none; margin: 0; opacity: 1; }
            .ln-ti, .ln-ti.active, .ln-ti.open { flex-grow: 1; font-size: 13px; font-weight: 500; border-radius: 16px; }
            .ln-ti.active, .ln-ti.open { font-weight: 600; background: rgba(255,255,255,.95); box-shadow: 0 1px 3px rgba(0,0,0,.1), 0 0 0 .5px var(--hair); }
            .ln-ti:not(.active):not(.open):hover { background: rgba(0,0,0,.05); }
            .ln-tabbar.more-open .ln-ti.active:not(.open) { color: var(--faint); background: transparent; box-shadow: none; }
            .ln-tabbar.more-open .ln-ti.active:not(.open) .lbl { max-width: none; opacity: 1; }
            .ln-company { max-width: 320px; }
        }

        /* ===== Menu "Lainnya" ===== */
        .ln-bd { position: fixed; inset: 0; z-index: 98; background: rgba(0,0,0,.2); -webkit-backdrop-filter: blur(3px); backdrop-filter: blur(3px); opacity: 0; pointer-events: none; transition: opacity .2s; }
        .ln-bd.open { opacity: 1; pointer-events: auto; }
        .ln-sheet [hidden] { display: none !important; }

        .ln-sheet {
            position: fixed; z-index: 99; left: 10px; right: 10px;
            bottom: calc(var(--dock-h) + var(--dock-gap) + 12px + env(safe-area-inset-bottom, 0px));
            display: flex; flex-direction: column; overflow: hidden; outline: none;
            max-height: calc(100vh - 190px); max-height: calc(100dvh - 190px);
            border-radius: 28px;
            background: rgba(255,255,255,.9);
            -webkit-backdrop-filter: saturate(180%) blur(30px); backdrop-filter: saturate(180%) blur(30px);
            border: 1px solid rgba(255,255,255,.8);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-l);
            opacity: 0; visibility: hidden; pointer-events: none; transform: translateY(14px) scale(.985);
            transition: opacity .2s, transform .26s cubic-bezier(.2,.8,.2,1), visibility 0s linear .26s;
        }
        .ln-sheet.open { opacity: 1; visibility: visible; pointer-events: auto; transform: none; transition-delay: 0s; }
        .ln-grab { flex: none; width: 38px; height: 4px; border-radius: 4px; background: var(--hair-2); margin: 9px auto 0; }

        /* kepala + pencarian (tetap di atas saat scroll) */
        .ln-sh-head { flex: none; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px 6px 20px; }
        .ln-sh-title { font-size: 17px; font-weight: 700; letter-spacing: -.02em; color: var(--ink); }
        .ln-sh-close { width: 30px; height: 30px; border-radius: 50%; border: 0; cursor: pointer; font-size: 18px; line-height: 1; color: var(--soft); background: rgba(0,0,0,.06); transition: .15s; }
        .ln-sh-close:hover { background: rgba(0,0,0,.12); color: var(--ink); }
        .ln-sh-search { flex: none; padding: 6px 16px 10px; }
        .ln-sh-search input {
            width: 100%; height: 42px; padding: 0 14px; border: 0; border-radius: 13px; outline: none;
            font-family: inherit; font-size: 16px; color: var(--ink); background: rgba(0,0,0,.055); transition: .16s;
        }
        .ln-sh-search input::placeholder { color: var(--faint); }
        .ln-sh-search input:focus { background: #fff; box-shadow: 0 0 0 .5px var(--hair-2), 0 0 0 4px rgba(0,0,0,.06); }

        /* daftar menu (area yang scroll) */
        .ln-sh-scroll { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; padding: 4px 16px 12px; }
        .ln-sg-wrap { display: grid; grid-template-columns: 1fr; gap: 18px; }
        .ln-sg-t { margin: 0 0 8px; padding: 0 4px; font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--faint); }
        .ln-sg ul { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .ln-sl {
            display: block; padding: 12px 14px; border-radius: 14px; font-size: 14px; font-weight: 500; line-height: 1.25;
            color: var(--ink); text-decoration: none; background: rgba(0,0,0,.045); transition: background .14s, color .14s;
        }
        .ln-sl:hover { background: rgba(0,0,0,.09); }
        /* PERUBAHAN: Blok hitam jadi biru */
        .ln-sl.active { color: #fff; font-weight: 600; background: linear-gradient(180deg, #2563EB, #1E3A8A); }
        .ln-sh-empty { margin: 0; padding: 36px 20px 44px; text-align: center; font-size: 13.5px; color: var(--mute); }

        /* kaki: akun + logout */
        .ln-sheet-foot { flex: none; display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-top: 1px solid var(--hair); }
        .ln-av { flex: none; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-size: 14px; font-weight: 700; color: var(--ink); background: rgba(0,0,0,.07); }
        .ln-sheet-foot .who { flex: 1; min-width: 0; }
        .ln-sheet-foot .who b { display: block; font-size: 13.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ln-sheet-foot .who span { display: block; font-size: 12px; color: var(--mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ln-sheet-foot form { margin: 0; flex: none; }
        .ln-sheet-foot .ln-btn-outline { height: 36px; padding: 0 14px; font-size: 13px; }

        @media (min-width: 640px) {
            .ln-sheet {
                left: 50%; right: auto; width: min(840px, calc(100vw - 32px));
                max-height: calc(100vh - 170px); max-height: calc(100dvh - 170px);
                transform: translate(-50%, 10px) scale(.985);
            }
            .ln-sheet.open { transform: translate(-50%, 0); }
            .ln-grab { display: none; }
            .ln-sh-head { padding: 18px 18px 4px 26px; }
            .ln-sh-search { padding: 8px 24px 14px; }
            .ln-sh-search input { font-size: 14px; }
            .ln-sh-scroll { padding: 4px 24px 18px; }
            .ln-sg-wrap { grid-template-columns: repeat(3, 1fr); gap: 14px; align-items: start; }
            .ln-sg { padding: 10px 8px 8px; border-radius: 20px; background: rgba(255,255,255,.55); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair); }
            .ln-sg-t { padding: 2px 10px 8px; }
            .ln-sg ul { grid-template-columns: 1fr; gap: 1px; }
            .ln-sl { padding: 9px 10px; border-radius: 11px; background: transparent; }
            .ln-sl:hover { background: rgba(0,0,0,.06); }
            /* PERUBAHAN: Blok hitam jadi biru */
            .ln-sl.active { background: linear-gradient(180deg, #2563EB, #1E3A8A); }
            .ln-sheet-foot { padding: 14px 24px; }
        }

        /* ===== Komponen konten (dipakai halaman lama) ===== */
        .ln-page-head { display: flex; flex-wrap: wrap; gap: 10px 16px; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; }
        .ln-page-title { margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -.022em; color: var(--ink); }
        .ln-page-sub { font-size: 13.5px; color: var(--mute); margin-top: 4px; }
        .ln-section-title { font-size: 16px; font-weight: 700; letter-spacing: -.01em; color: var(--ink); margin: 0 0 12px; }

        .ln-stat, .ln-table-card, .ln-form-card {
            background: var(--glass); -webkit-backdrop-filter: saturate(180%) blur(20px); backdrop-filter: saturate(180%) blur(20px);
            border: 1px solid rgba(255,255,255,.78); border-radius: 20px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-s);
        }
        .ln-stat { padding: 18px 20px; transition: transform .18s, box-shadow .18s; }
        .ln-stat:hover { transform: translateY(-2px); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-m); }
        .ln-stat .top { display: flex; align-items: flex-start; justify-content: space-between; }
        .ln-stat .lbl { font-size: 12.5px; font-weight: 600; color: var(--mute); }
        .ln-stat .ico { width: 28px; height: 28px; border-radius: 9px; display: grid; place-items: center; background: rgba(0,0,0,.05); }
        .ln-stat .ico svg { width: 15px; height: 15px; stroke: var(--soft); }
        .ln-stat .val { margin-top: 10px; font-size: 22px; font-weight: 700; letter-spacing: -.02em; color: var(--ink); word-break: break-word; }

        .ln-btn-primary, .ln-btn-outline, .ln-btn-cancel {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 18px; border-radius: 12px;
            font-family: inherit; font-size: 13.5px; font-weight: 600; text-decoration: none; cursor: pointer; transition: .16s;
        }
        .ln-btn-primary { color: #fff; border: 0; background: linear-gradient(180deg, #3A3A3C, #1D1D1F); box-shadow: 0 8px 18px -8px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.18); }
        .ln-btn-primary:hover { background: linear-gradient(180deg, #2C2C2E, #000); transform: translateY(-1px); }
        .ln-btn-primary:active { transform: scale(.98); }
        .ln-btn-outline, .ln-btn-cancel { color: var(--ink); background: rgba(255,255,255,.75); border: 1px solid var(--hair); }
        .ln-btn-outline:hover, .ln-btn-cancel:hover { background: #fff; border-color: var(--hair-2); }
        .ln-form-actions { display: flex; gap: 10px; padding-top: 6px; }

        .ln-table-card { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .ln-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .ln-table thead th { text-align: left; padding: 13px 18px; font-size: 11px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--mute); background: rgba(0,0,0,.025); border-bottom: 1px solid var(--hair); white-space: nowrap; }
        .ln-table thead th.num { text-align: right; }
        .ln-table thead th.center { text-align: center; }
        .ln-table tbody td { padding: 14px 18px; border-bottom: 1px solid var(--hair); color: var(--ink); }
        .ln-table tbody tr { transition: background .14s; }
        .ln-table tbody tr:hover { background: rgba(0,0,0,.03); }
        .ln-table tbody tr:last-child td { border-bottom: 0; }
        .ln-table tbody td.num { text-align: right; font-weight: 600; white-space: nowrap; }
        .ln-table tbody td.center { text-align: center; }
        .ln-table a.ref { font-weight: 600; color: var(--ink); text-decoration: underline; text-decoration-color: var(--hair-2); text-underline-offset: 3px; }
        .ln-table a.ref:hover { text-decoration-color: var(--ink); }
        .ln-table td.empty { text-align: center; padding: 48px 16px; color: var(--mute); }

        .ln-badge, .ln-type { display: inline-flex; align-items: center; gap: 7px; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; color: var(--soft); background: rgba(0,0,0,.05); }
        .ln-badge::before, .ln-type::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--dot, var(--faint)); }
        .ln-badge.posted, .ln-badge.in, .ln-badge.paid, .ln-badge.active { --dot: var(--ok); }
        .ln-badge.draft, .ln-badge.partial { --dot: var(--warn); }
        .ln-badge.out, .ln-badge.unpaid, .ln-badge.expired { --dot: var(--bad); }
        .ln-type { font-weight: 500; }
        .ln-type.aset, .ln-type.asset { --dot: #8E8E93; }
        .ln-type.kewajiban, .ln-type.liabilitas, .ln-type.liability { --dot: var(--bad); }
        .ln-type.ekuitas, .ln-type.equity { --dot: var(--warn); }
        .ln-type.pendapatan, .ln-type.revenue { --dot: var(--ok); }
        .ln-type.beban, .ln-type.expense { --dot: #3A3A3C; }
        .ln-code { font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 12.5px; color: var(--soft); }

        .ln-action { background: none; border: 0; padding: 0; cursor: pointer; font-family: inherit; font-size: 12.5px; font-weight: 600; color: var(--ink); text-decoration: none; opacity: .85; transition: .15s; }
        .ln-action:hover { opacity: 1; text-decoration: underline; text-underline-offset: 3px; }
        .ln-action.green { color: #1E7A4C; }
        .ln-action.red { color: #B5362F; }
        .ln-action-sep { color: var(--hair-2); margin: 0 6px; }
        .ln-pagination { margin-top: 18px; }
        .ln-pagination nav { font-size: 13px; }

        .ln-form-card { padding: 24px; }
        .ln-label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--mute); }
        .ln-input, .ln-textarea, .ln-select {
            width: 100%; border: 1px solid var(--hair); border-radius: 12px; padding: 10px 14px; font-family: inherit; font-size: 13.5px; color: var(--ink);
            background: rgba(255,255,255,.8); transition: .16s;
        }
        .ln-input::placeholder, .ln-textarea::placeholder { color: #B0B0B6; }
        .ln-input:focus, .ln-textarea:focus, .ln-select:focus { outline: none; border-color: rgba(0,0,0,.4); box-shadow: 0 0 0 4px rgba(0,0,0,.06); background: #fff; }
        .ln-input-sm { padding: 8px 10px; font-size: 13px; border-radius: 10px; }
        .ln-checkbox { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--soft); }
        .ln-checkbox input { width: 16px; height: 16px; accent-color: var(--ink); }

        .ln-entries-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .ln-entries-table thead th { text-align: left; padding: 12px 10px; font-size: 11px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--mute); background: rgba(0,0,0,.03); border-bottom: 1px solid var(--hair); }
        .ln-entries-table thead th.num { text-align: right; }
        .ln-entries-table tbody td { padding: 8px 10px; border-bottom: 1px solid var(--hair); }
        .ln-entries-table tfoot td { padding: 12px 10px; font-weight: 700; background: rgba(0,0,0,.03); }
        .ln-remove-btn { background: none; border: 0; cursor: pointer; font-size: 13px; color: var(--faint); transition: .15s; }
        .ln-remove-btn:hover { color: var(--bad); }
        .ln-add-row { display: inline-flex; align-items: center; height: 38px; padding: 0 16px; border-radius: 12px; font-family: inherit; font-size: 13px; font-weight: 600; color: var(--soft); background: rgba(255,255,255,.6); border: 1px dashed var(--hair-2); cursor: pointer; transition: .16s; }
        .ln-add-row:hover { color: var(--ink); background: #fff; border-style: solid; }
        .ln-balance-ok { font-size: 13px; font-weight: 600; color: #1E7A4C; }
        .ln-balance-bad { font-size: 13px; font-weight: 600; color: #B5362F; }

        /* ===== Asisten AI ===== */
        .ln-ai-fab {
            position: fixed; z-index: 90; right: 14px;
            bottom: calc(var(--dock-h) + var(--dock-gap) + 14px + env(safe-area-inset-bottom, 0px));
            width: 46px; height: 46px; border-radius: 50%; display: grid; place-items: center; border: 0; cursor: pointer; color: #fff;
            background: linear-gradient(180deg, #3A3A3C, #1D1D1F); box-shadow: 0 12px 26px -8px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.2);
            transition: transform .18s;
        }
        .ln-ai-fab:hover { transform: translateY(-2px) scale(1.04); }
        .ln-ai-fab:active { transform: scale(.96); }
        .ln-ai-fab svg { width: 20px; height: 20px; }

        .ln-ai-panel {
            position: fixed; z-index: 89; right: 14px;
            bottom: calc(var(--dock-h) + var(--dock-gap) + 70px + env(safe-area-inset-bottom, 0px));
            width: 380px; max-width: calc(100vw - 28px); height: 540px; max-height: calc(100vh - 260px); max-height: calc(100dvh - 260px);
            display: none; flex-direction: column; overflow: hidden; border-radius: 24px;
            background: rgba(255,255,255,.94); -webkit-backdrop-filter: saturate(180%) blur(28px); backdrop-filter: saturate(180%) blur(28px);
            border: 1px solid rgba(255,255,255,.8); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-l);
        }
        .ln-ai-panel.open { display: flex; animation: ln-pop .24s ease; }
        @keyframes ln-pop { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
        .ln-ai-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--hair); }
        .ln-ai-head-title { font-size: 15px; font-weight: 700; letter-spacing: -.01em; }
        .ln-ai-head-sub { margin-top: 2px; font-size: 12px; color: var(--mute); }
        .ln-ai-close { width: 30px; height: 30px; border-radius: 50%; border: 0; cursor: pointer; font-size: 18px; line-height: 1; color: var(--soft); background: rgba(0,0,0,.06); transition: .15s; }
        .ln-ai-close:hover { background: rgba(0,0,0,.12); color: var(--ink); }
        .ln-ai-messages { flex: 1; overflow-y: auto; padding: 16px; font-size: 13.5px; line-height: 1.55; -webkit-overflow-scrolling: touch; }
        .ln-ai-msg { max-width: 88%; margin-bottom: 10px; padding: 10px 14px; border-radius: 18px; word-wrap: break-word; white-space: pre-line; animation: ln-msg .2s ease; }
        @keyframes ln-msg { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
        .ln-ai-msg.user { margin-left: auto; color: #fff; background: #1D1D1F; border-bottom-right-radius: 6px; }
        .ln-ai-msg.assistant { background: #fff; border: 1px solid var(--hair); border-bottom-left-radius: 6px; }
        .ln-ai-msg .action { margin-top: 10px; }
        .ln-ai-msg .action a { display: inline-flex; padding: 7px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 600; color: #fff; text-decoration: none; background: #1D1D1F; transition: .15s; }
        .ln-ai-msg .action a:hover { background: #000; }
        .ln-ai-typing { display: inline-flex; gap: 4px; padding: 4px 0; }
        .ln-ai-typing span { width: 6px; height: 6px; border-radius: 50%; background: var(--faint); animation: ln-dot 1.2s infinite; }
        .ln-ai-typing span:nth-child(2) { animation-delay: .15s; }
        .ln-ai-typing span:nth-child(3) { animation-delay: .3s; }
        @keyframes ln-dot { 0%, 60%, 100% { opacity: .3; transform: none; } 30% { opacity: 1; transform: translateY(-2px); } }
        .ln-ai-chips { display: flex; gap: 6px; padding: 10px 14px; overflow-x: auto; border-top: 1px solid var(--hair); scrollbar-width: none; }
        .ln-ai-chips::-webkit-scrollbar { display: none; }
        .ln-ai-chips button { flex: none; padding: 6px 13px; border-radius: 999px; font-family: inherit; font-size: 12px; font-weight: 500; color: var(--soft); background: rgba(0,0,0,.05); border: 0; cursor: pointer; transition: .15s; white-space: nowrap; }
        .ln-ai-chips button:hover { color: var(--ink); background: rgba(0,0,0,.1); }
        .ln-ai-inputbar { display: flex; gap: 8px; padding: 10px 12px 12px; border-top: 1px solid var(--hair); }
        .ln-ai-inputbar input { flex: 1; min-width: 0; height: 42px; padding: 0 14px; border-radius: 12px; border: 1px solid var(--hair); background: rgba(255,255,255,.85); font-family: inherit; font-size: 13.5px; outline: none; transition: .16s; }
        .ln-ai-inputbar input:focus { border-color: rgba(0,0,0,.4); box-shadow: 0 0 0 4px rgba(0,0,0,.06); background: #fff; }
        .ln-ai-send { width: 46px; height: 42px; display: grid; place-items: center; border: 0; border-radius: 12px; cursor: pointer; color: #fff; background: #1D1D1F; transition: .15s; }
        .ln-ai-send:hover { background: #000; }
        .ln-ai-send:active { transform: scale(.96); }
        .ln-ai-send:disabled { opacity: .45; cursor: not-allowed; }

        @media (min-width: 640px) {
            .ln-ai-fab { right: 24px; }
            .ln-ai-panel { right: 24px; }
        }
        @media (max-width: 639px) {
            .ln-input, .ln-textarea, .ln-select { font-size: 16px; }
            .ln-input-sm { font-size: 15px; }
            .ln-page-title { font-size: 23px; }
            .ln-form-card { padding: 18px; }
            .ln-ai-inputbar input { font-size: 16px; }
            .ln-bar { padding-left: 14px; }
            .ln-company { font-size: 11.5px; padding: 5px 10px; }
            .ln-hlogout { display: none; }   /* logout ada di menu "Lainnya" */
        }
        @media (prefers-reduced-motion: reduce) {
            main, .ln-ai-panel, .ln-ai-msg { animation: none; }
            * { transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

<a href="#konten" class="ln-skip">Lewati ke konten</a>

<header class="ln-header">
    <div class="ln-bar glass">
        <a href="{{ route('dashboard') }}" class="ln-brand" aria-label="LedgerNusa — ke Dashboard">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa" height="26" decoding="async" fetchpriority="high">
        </a>

        <div class="ln-right">
            @if($activeCompany)
                <a href="{{ route('companies.index') }}" class="ln-company" title="Kelola company">{{ $activeCompany->name }}</a>
            @endif
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="ln-hlink">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="ln-hlogout">Logout</button>
            </form>
        </div>
    </div>
</header>

@if(session('trial_days') !== null && session('trial_days') <= 5)
    <div class="ln-trial">
        <div class="glass">
            <span>Trial Anda tersisa <strong>{{ session('trial_days') }} hari</strong>.</span>
            <a href="{{ route('subscription.index') }}">Perpanjang sekarang</a>
        </div>
    </div>
@endif

<main id="konten" class="ln-main max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="ln-alert ln-alert-success mb-4" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ln-alert ln-alert-error mb-4" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="ln-alert ln-alert-error mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

{{-- DOCK BAWAH --}}
<nav class="ln-tabbar glass" id="lnDock" aria-label="Menu utama">
    @foreach($navMain as [$label, $route, $match, $icon])
        @php $on = $isOn($match); @endphp
        <a href="{{ route($route) }}" class="ln-ti {{ $on ? 'active' : '' }}" title="{{ $label }}" @if($on) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true">{!! $icon !!}</svg>
            <span class="lbl">{{ $label }}</span>
        </a>
    @endforeach
    <button type="button" class="ln-ti js-more {{ $moreActive ? 'active' : '' }}" title="Lainnya" aria-haspopup="dialog" aria-expanded="false" aria-controls="lnDdMenu">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5.5" cy="12" r="1.2"/><circle cx="12" cy="12" r="1.2"/><circle cx="18.5" cy="12" r="1.2"/></svg>
        <span class="lbl">Lainnya</span>
    </button>
</nav>

{{-- Menu "Lainnya" --}}
<div class="ln-bd" id="lnDdBackdrop"></div>
<div class="ln-sheet" id="lnDdMenu" role="dialog" aria-modal="true" aria-labelledby="lnShTitle" tabindex="-1" inert>
    <div class="ln-grab" aria-hidden="true"></div>

    <div class="ln-sh-head">
        <div class="ln-sh-title" id="lnShTitle">Menu</div>
        <button type="button" class="ln-sh-close" id="lnShClose" aria-label="Tutup menu">×</button>
    </div>

    <div class="ln-sh-search">
        <input type="search" id="lnMenuSearch" placeholder="Cari menu…" autocomplete="off" aria-label="Cari menu" aria-controls="lnMenuList">
    </div>

    <div class="ln-sh-scroll">
        <div class="ln-sg-wrap" id="lnMenuList">
            @foreach($lnMore as $group => $items)
                @php $visible = collect($items)->filter(fn($i) => Route::has($i[1])); @endphp
                @if($visible->isNotEmpty())
                    <section class="ln-sg" data-group="{{ $group }}">
                        <p class="ln-sg-t">{{ $group }}</p>
                        <ul>
                            @foreach($visible as [$label, $route, $match])
                                @php $on = $isOn($match); @endphp
                                <li><a href="{{ route($route) }}" class="ln-sl {{ $on ? 'active' : '' }}" @if($on) aria-current="page" @endif>{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            @endforeach
        </div>
        <p class="ln-sh-empty" id="lnMenuEmpty" role="status" hidden>Menu tidak ditemukan.</p>
    </div>

    <div class="ln-sheet-foot">
        <span class="ln-av" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
        <div class="who">
            <b>{{ auth()->user()->name }}</b>
            <span>{{ auth()->user()->email }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ln-btn-outline">Logout</button>
        </form>
    </div>
</div>

{{-- ==================== ASISTEN AI ==================== --}}
<button type="button" class="ln-ai-fab" onclick="lnAiToggle()" title="Tanya Asisten LedgerNusa" aria-label="Buka Asisten AI">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
    </svg>
</button>

<div class="ln-ai-panel" id="lnAiPanel" role="dialog" aria-label="Asisten LedgerNusa">
    <div class="ln-ai-head">
        <div>
            <div class="ln-ai-head-title">Asisten LedgerNusa</div>
            <div class="ln-ai-head-sub">Tanya apa saja soal akuntansi</div>
        </div>
        <button type="button" class="ln-ai-close" onclick="lnAiToggle()" aria-label="Tutup">×</button>
    </div>

    <div class="ln-ai-messages" id="lnAiMessages" aria-live="polite"></div>

    <div class="ln-ai-chips" id="lnAiChips">
        <button type="button" onclick="lnAiQuick('Cara input jurnal?')">Cara input jurnal?</button>
        <button type="button" onclick="lnAiQuick('Apa itu COA?')">Apa itu COA?</button>
        <button type="button" onclick="lnAiQuick('Cara lihat laba rugi?')">Laba rugi</button>
        <button type="button" onclick="lnAiQuick('Cara install ke HP?')">Install HP</button>
        <button type="button" onclick="lnAiQuick('Berapa lama trial?')">Trial</button>
    </div>

    <div class="ln-ai-inputbar">
        <input type="text" id="lnAiInput" placeholder="Ketik pertanyaan..." autocomplete="off" aria-label="Pertanyaan"
               onkeypress="if(event.key==='Enter'){event.preventDefault();lnAiSend();}">
        <button type="button" class="ln-ai-send" id="lnAiSend" onclick="lnAiSend()" aria-label="Kirim">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
        </button>
    </div>
</div>

<script>
// ===== Menu "Lainnya" =====
(function () {
    const menu = document.getElementById('lnDdMenu');
    const bd = document.getElementById('lnDdBackdrop');
    const dock = document.getElementById('lnDock');
    const btn = document.querySelector('.js-more');
    const search = document.getElementById('lnMenuSearch');
    const empty = document.getElementById('lnMenuEmpty');
    const closeBtn = document.getElementById('lnShClose');
    const groups = [...document.querySelectorAll('#lnMenuList .ln-sg')];
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    function filter() {
        const q = search.value.trim().toLowerCase();
        let total = 0;
        groups.forEach(g => {
            const inGroup = g.dataset.group.toLowerCase().includes(q);
            let shown = 0;
            g.querySelectorAll('li').forEach(li => {
                const ok = !q || inGroup || li.textContent.toLowerCase().includes(q);
                li.hidden = !ok;
                if (ok) shown++;
            });
            g.hidden = shown === 0;
            total += shown;
        });
        empty.hidden = total !== 0;
    }

    function setOpen(open) {
        menu.classList.toggle('open', open);
        bd.classList.toggle('open', open);
        dock.classList.toggle('more-open', open);
        btn.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.documentElement.classList.toggle('ln-lock', open);
        if (open) {
            menu.removeAttribute('inert');
            // desktop: langsung bisa mengetik; mobile: jangan memunculkan keyboard
            setTimeout(() => (finePointer ? search : menu).focus({ preventScroll: true }), 60);
        } else {
            menu.setAttribute('inert', '');
            search.value = '';
            filter();
        }
    }

    window.lnToggleMore = function () { setOpen(!menu.classList.contains('open')); };
    btn.addEventListener('click', window.lnToggleMore);
    bd.addEventListener('click', () => setOpen(false));
    closeBtn.addEventListener('click', () => { setOpen(false); btn.focus(); });
    search.addEventListener('input', filter);
    search.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            const first = menu.querySelector('.ln-sg:not([hidden]) li:not([hidden]) a');
            if (first) { e.preventDefault(); first.click(); }
        }
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && menu.classList.contains('open')) { setOpen(false); btn.focus(); }
    });
})();

// ===== Asisten AI =====
(function () {
    let convId = null;
    let greeted = false;
    const ENDPOINT = @json(route('ai.chat'));
    const CSRF = @json(csrf_token());

    window.lnAiToggle = function () {
        const panel = document.getElementById('lnAiPanel');
        if (panel.classList.contains('open')) {
            panel.classList.remove('open');
        } else {
            panel.classList.add('open');
            if (!greeted) {
                greeted = true;
                lnAiAddMsg('Halo! Saya asisten LedgerNusa.<br>Ada yang bisa saya bantu?', 'assistant');
            }
            setTimeout(() => document.getElementById('lnAiInput').focus(), 150);
        }
    };

    function lnAiAddMsg(html, role, actionUrl, actionLabel) {
        const box = document.getElementById('lnAiMessages');
        const div = document.createElement('div');
        div.className = 'ln-ai-msg ' + role;
        div.innerHTML = html;
        if (actionUrl && actionLabel) {
            div.innerHTML += '<div class="action"><a href="' + actionUrl + '">' + actionLabel + ' →</a></div>';
        }
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
        return div;
    }
    window.lnAiAddMsg = lnAiAddMsg;

    window.lnAiQuick = function (text) {
        document.getElementById('lnAiInput').value = text;
        lnAiSend();
    };

    window.lnAiSend = async function () {
        const input = document.getElementById('lnAiInput');
        const btn = document.getElementById('lnAiSend');
        const msg = input.value.trim();
        if (!msg || btn.disabled) return;

        input.value = '';
        input.disabled = true;
        btn.disabled = true;

        lnAiAddMsg(msg.replace(/</g, '&lt;'), 'user');
        const typing = lnAiAddMsg('<span class="ln-ai-typing"><span></span><span></span><span></span></span>', 'assistant');

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: msg, conversation_id: convId })
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();

            typing.remove();
            convId = data.conversation_id;
            const answerHtml = (data.answer || '').replace(/</g, '&lt;').replace(/\n/g, '<br>');
            lnAiAddMsg(answerHtml, 'assistant', data.action_url, data.action_label);
        } catch (e) {
            typing.remove();
            lnAiAddMsg('Maaf, terjadi kesalahan. Silakan coba lagi.', 'assistant');
        } finally {
            input.disabled = false;
            btn.disabled = false;
            input.focus();
        }
    };
})();

// PWA Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
</script>

</body>
</html>