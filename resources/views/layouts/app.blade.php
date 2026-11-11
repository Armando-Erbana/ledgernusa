<!DOCTYPE html>
<html lang="id">
<head>
@php
    /* ---------- 1. SEO: judul & deskripsi per halaman ---------- */
    $lnTitle     = trim($__env->yieldContent('title')) ?: ($title ?? null);
    $lnTitleFull = $lnTitle ? $lnTitle . ' — LedgerNusa' : 'LedgerNusa — Aplikasi Akuntansi Online';
    $lnDesc      = trim($__env->yieldContent('meta_description'))
        ?: 'LedgerNusa adalah aplikasi akuntansi online untuk jurnal, buku besar, kas & bank, penjualan, pembelian, persediaan, dan laporan keuangan usaha Anda.';
    $lnRobots    = trim($__env->yieldContent('robots')) ?: 'noindex, nofollow';

    /* ---------- 2. Cek Status Langganan & Role User ---------- */
    $activeCompany = auth()->user()->activeCompany();
    $companyId = session('company_id');
    $userRole = auth()->user()->roleInActiveCompany();
    $businessType = $activeCompany->business_type ?? null;
    $isJasa = $businessType === 'jasa';

    $activeSub = \App\Models\Subscription::where('company_id', $companyId)
        ->where(function($q) {
            $q->where(function($sub1) {
                $sub1->where('status', 'active')->where('ends_at', '>=', now());
            })->orWhere(function($sub2) {
                $sub2->where('trial_ends_at', '>=', now());
            });
        })
        ->latest('id')
        ->first();

    $isExpired = !$activeSub;
    $isTrial = false;
    $trialDaysLeft = 0;
    $currentPlanCode = 'starter';

    if ($activeSub) {
    if ($activeSub->status === 'trial' && $activeSub->trial_ends_at && $activeSub->trial_ends_at >= now()) {
            $isTrial = true;
            $trialDaysLeft = (int) ceil(now()->diffInDays($activeSub->trial_ends_at, false));
        }
        $plan = \App\Models\Plan::find($activeSub->plan_id);
        if ($plan) {
            $currentPlanCode = strtolower($plan->code);
        }
    }

    $isPro = $isTrial || in_array($currentPlanCode, ['pro', 'enterprise']);

    // LOGIKA MULTI-CABANG & ROLE
    $isStaff = $userRole === 'staff';
    $isOwner = $userRole === 'owner';
    $isAdmin = $userRole === 'admin';

    if ($isPro) {
        if ($isStaff) {
            $branches = auth()->user()->branches()->where('company_id', $companyId)->get();
            if ($branches->count() > 0 && !session('branch_id')) {
                session(['branch_id' => $branches->first()->id]);
            }
        } else {
            $branches = \App\Models\Branch::where('company_id', $companyId)->get();
        }
    } else {
        $branches = collect();
    }

    $activeBranch = $branches->firstWhere('id', session('branch_id')) ?? $branches->firstWhere('is_main', true) ?? $branches->first();
    $showBranchDropdown = $isPro && ($branches->count() > 1 || !$isStaff);

    /* ---------- 2b. Multi-Company ---------- */
    $allCompanies = auth()->user()->companies()->orderBy('company_users.created_at')->get();
    $accessService = app(\App\Services\CompanyAccessService::class);
    $lockedIds = $accessService->lockedCompanyIds(auth()->user());

    /* ---------- 3. Menu utama (dock bawah) ---------- */
    $navMain = array_filter([
        ['Dashboard', 'dashboard',          'dashboard',      '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9"/>'],
        ['Kas',       'cash.index',         'cash.*',         '<rect x="2.5" y="6.5" width="19" height="12" rx="2"/><circle cx="12" cy="12.5" r="2.4"/>'],
        ['Penjualan', 'sales.index',        'sales.*',        '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>'],
        $isOwner ? ['Langganan', 'subscription.index', 'subscription.*', '<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19"/>'] : null,
        !$isStaff ? ['Jurnal',    'journals.index',     'journals.*',     '<path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7"/>'] : null,
        !$isStaff ? ['Laporan',   'reports.index',      'reports.*',      '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4M12 15V9M16 15v-6"/>'] : null,
    ]);

    /* ---------- 4. Menu "Lainnya" ---------- */
    $lnMore = [
        'Kas & Bank' => array_filter([
            ['Kas & Bank',      'cash.index',                    'cash.*'],
            $isPro && !$isStaff ? ['Rekonsiliasi', 'bank-rec.index', 'bank-rec.*'] : null,
            ['Transfer',        'transfers.index',               'transfers.*'],
        ]),
        'Penjualan' => array_filter([
            $isPro && !$isStaff ? ['Sales Order', 'sales-orders.index', 'sales-orders.*'] : null,
            $isPro && !$isStaff ? ['Delivery',    'delivery-orders.index', 'delivery-orders.*'] : null,
            ['Penjualan',       'sales.index',                   'sales.*'],
            ['Customer',        'customers.index',               'customers.*'],
        ]),
        'Pembelian' => array_filter([
            $isPro && !$isStaff ? ['Purchase Order', 'purchase-orders.index', 'purchase-orders.*'] : null,
            $isPro && !$isStaff ? ['Penerimaan',     'goods-receipts.index',  'goods-receipts.*'] : null,
            ['Pembelian',       'purchases.index',               'purchases.*'],
            ['Supplier',        'suppliers.index',               'suppliers.*'],
        ]),
        'Persediaan' => $isJasa ? [] : array_filter([
            ['Produk',          'products.index',                'products.*'],
            $isPro && !$isStaff ? ['Mutasi Stok', 'stock.index', 'stock.*'] : null,
            $isPro && !$isStaff ? ['Gudang',      'warehouses.index', 'warehouses.*'] : null,
            ['Kategori',        'product-categories.index',      'product-categories.*'],
        ]),
        'Akuntansi' => array_filter([
            !$isStaff ? ['Akun (COA)',  'accounts.index',        'accounts.*'] : null,
            $isPro && !$isStaff ? ['Aset Tetap', 'fixed-assets.index', 'fixed-assets.*'] : null,
            $isPro && !$isStaff ? ['Pajak',      'tax.index',    'tax.*'] : null,
            ['Kontak',          'contacts.index',                'contacts.*'],
            $isPro && !$isStaff ? ['Karyawan',   'employees.index', 'employees.*'] : null,
            $isPro && !$isStaff ? ['Payroll',    'payrolls.index', 'payrolls.*'] : null,
            $isPro && !$isStaff ? ['Budgeting',  'budgets.report', 'budgets.*'] : null,
        ]),
        'Sistem' => array_filter([
            ($isOwner || $isAdmin) ? ['Kelola User', 'users.index', 'users.*'] : null,
            ($isOwner || $isAdmin) ? ['Setting',     'companies.index',   'companies.*'] : null,
            ['Panduan',         'help.index',                    'help.*'],
            ['Profil',          'profile.edit',                  'profile.*'],
            $isPro && !$isStaff ? ['Currencies', 'settings.currencies.index', 'settings.currencies.*'] : null,
            $isPro && !$isStaff ? ['Kurs',       'settings.exchange-rates.index', 'settings.exchange-rates.*'] : null,
            $isPro && !$isStaff ? ['Multi-Cabang', 'branches.index', 'branches.*'] : null,
        ]),
    ];

    $filterMenu = function (array $items): array {
        return array_values(array_filter(
            $items,
            fn ($item) => $item && \Illuminate\Support\Facades\Route::has($item[1])
        ));
    };
    $navMain = $filterMenu($navMain);
    $lnMore  = array_map($filterMenu, $lnMore);
    $lnMore  = array_filter($lnMore, fn ($items) => count($items) > 0);

    $isOn          = fn($m) => request()->routeIs(...(array) $m);
    $mainOnAll     = collect($navMain)->contains(fn($n) => $isOn($n[2]));
    $moreOn        = collect($lnMore)->flatten(1)->contains(fn($i) => $isOn($i[2]));
    $moreActive    = $moreOn && ! $mainOnAll;
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
            --ok: #2E9E6B; --warn: #C98A1B; --bad: #D04A4A;
            --dock-h: 62px; --dock-gap: 14px;
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

        .glass {
            background: var(--glass);
            -webkit-backdrop-filter: saturate(180%) blur(24px); backdrop-filter: saturate(180%) blur(24px);
            border: 1px solid rgba(255,255,255,.78);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-m);
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .glass, .ln-stat, .ln-table-card, .ln-form-card, .ln-alert, .ln-sheet { background: rgba(250,250,252,.97); }
        }

        .ln-header { position: sticky; top: 0; z-index: 40; padding: calc(10px + env(safe-area-inset-top, 0px)) 12px 0; }
        .ln-bar { max-width: 1152px; margin: 0 auto; min-height: 56px; padding: 8px 12px 8px 18px; border-radius: 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }

        .ln-brand-group { display: flex; align-items: center; gap: 12px; }
        .ln-brand { display: flex; align-items: center; flex: none; text-decoration: none; }
        .ln-brand img { height: 26px; width: auto; display: block; }
        .ln-brand-divider { width: 1px; height: 20px; background: var(--hair-2); }
        .ln-brand-mangata { display: flex; flex-direction: column; justify-content: center; }
        .ln-brand-mangata span { font-size: 10px; font-weight: 500; color: var(--mute); line-height: 1.2; text-transform: uppercase; letter-spacing: .05em; }
        .ln-brand-mangata b { font-size: 12px; font-weight: 700; color: var(--ink); }

        .ln-right { display: flex; align-items: center; justify-content: flex-end; gap: 8px; min-width: 0; }
        .ln-right form { margin: 0; }

        /* ═══════════════════════════════════════════════════════════
           COMPANY DROPDOWN — CSS yang sebelumnya HILANG
           ═══════════════════════════════════════════════════════════ */
        .ln-company-wrap { position: relative; }
        .ln-company {
            display: inline-flex; align-items: center; justify-content: center;
            max-width: min(46vw, 320px);
            padding: 6px 14px; border-radius: 12px;
            font-size: 12.5px; font-weight: 500; color: var(--soft);
            line-height: 1.25; text-align: center;
            background: rgba(255,255,255,.7); border: 1px solid var(--hair);
            cursor: pointer; font-family: inherit; transition: .16s;
        }
        .ln-company > span {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            max-width: 240px;
        }
        .ln-company:hover { background: #fff; color: var(--ink); }
        .ln-company svg { flex: none; width: 10px; height: 10px; margin-left: 6px; }
        .ln-company-menu {
            position: absolute; top: calc(100% + 6px); right: 0; width: 240px;
            background: rgba(255,255,255,.97);
            -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px);
            border: 1px solid var(--hair); border-radius: 16px; box-shadow: var(--shadow-m);
            opacity: 0; visibility: hidden; transform: translateY(-8px); transition: .2s;
            z-index: 100; overflow: hidden; padding: 6px;
        }
        .ln-company-wrap.is-open .ln-company-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        @media (min-width: 768px) {
            .ln-company-wrap:hover .ln-company-menu {
                opacity: 1; visibility: visible; transform: translateY(0);
            }
        }
        .ln-company-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500; color: var(--soft);
            text-decoration: none; transition: .15s; gap: 8px;
        }
        .ln-company-item > span:first-child {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;
        }
        .ln-company-item:hover { background: rgba(0,0,0,.04); color: var(--ink); }
        .ln-company-item.active {
            background: rgba(37,99,235,0.08); color: #2563EB; font-weight: 600;
        }
        .ln-company-item.locked {
            opacity: .7; color: var(--mute);
        }
        .ln-company-item.locked:hover {
            background: rgba(208,74,74,.08); color: var(--bad); opacity: 1;
        }
        .ln-company-divider { height: 1px; background: var(--hair); margin: 4px 8px; }

        /* ═══════════════════════════════════════════════════════════
           BRANCH DROPDOWN (existing)
           ═══════════════════════════════════════════════════════════ */
        .ln-branch-wrap { position: relative; }
        .ln-branch-btn {
            display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 12px; cursor: pointer;
            font-size: 12px; font-weight: 600; color: #2563EB; background: rgba(37,99,235,0.08);
            border: 1px solid rgba(37,99,235,0.2); transition: .2s;
        }
        .ln-branch-btn:hover { background: rgba(37,99,235,0.15); }
        .ln-branch-btn svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2; }
        .ln-branch-menu {
            position: absolute; top: calc(100% + 6px); right: 0; width: 220px;
            background: rgba(255,255,255,.95); -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px);
            border: 1px solid var(--hair); border-radius: 16px; box-shadow: var(--shadow-m);
            opacity: 0; visibility: hidden; transform: translateY(-8px); transition: .2s; z-index: 100;
            overflow: hidden; padding: 6px;
        }
        .ln-branch-wrap.is-open .ln-branch-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        @media (min-width: 768px) {
            .ln-branch-wrap:hover .ln-branch-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        }
        .ln-branch-item {
            display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500; color: var(--soft); text-decoration: none; transition: .15s;
        }
        .ln-branch-item:hover { background: rgba(0,0,0,.04); color: var(--ink); }
        .ln-branch-item.active { background: rgba(37,99,235,0.08); color: #2563EB; font-weight: 600; }
        .ln-branch-divider { height: 1px; background: var(--hair); margin: 4px 8px; }

        /* ═══════════════════════════════════════════════════════════
           HEADER LINKS
           ═══════════════════════════════════════════════════════════ */
        .ln-hlink, .ln-hlogout {
            padding: 7px 11px; border-radius: 10px; border: 0; background: transparent; cursor: pointer; font-family: inherit;
            font-size: 13px; font-weight: 500; color: var(--mute); text-decoration: none; transition: .16s; white-space: nowrap;
        }
        .ln-hlink:hover { color: var(--ink); background: rgba(0,0,0,.05); }
        .ln-hlogout:hover { color: var(--bad); background: rgba(208,74,74,.08); }

        /* ═══════════════════════════════════════════════════════════
           BANNERS
           ═══════════════════════════════════════════════════════════ */
        .ln-banner-expired { max-width: 1152px; margin: 12px auto 0; padding: 0 16px; }
        .ln-banner-expired > div { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; padding: 12px 16px; border-radius: 14px; font-size: 13.5px; background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; box-shadow: 0 4px 12px -4px rgba(185, 28, 28, 0.15); }
        .ln-banner-expired a { font-weight: 600; text-decoration: underline; color: #991B1B; }
        .ln-banner-expired a:hover { color: #7F1D1D; }

        .ln-banner-trial { max-width: 1152px; margin: 12px auto 0; padding: 0 16px; }
        .ln-banner-trial > div { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; padding: 12px 16px; border-radius: 14px; font-size: 13.5px; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E3A8A; box-shadow: 0 4px 12px -4px rgba(30, 58, 138, 0.15); }
        .ln-banner-trial a { font-weight: 600; text-decoration: underline; color: #1D4ED8; }
        .ln-banner-trial a:hover { color: #1E3A8A; }

        .ln-alert { display: flex; gap: 11px; align-items: flex-start; padding: 12px 16px; border-radius: 14px; font-size: 13.5px; color: var(--ink); background: var(--glass); -webkit-backdrop-filter: blur(20px) saturate(170%); backdrop-filter: blur(20px) saturate(170%); border: 1px solid rgba(255,255,255,.78); box-shadow: 0 0 0 .5px var(--hair), var(--shadow-s); }
        .ln-alert::before { content: ""; flex: none; width: 8px; height: 8px; margin-top: 6px; border-radius: 50%; background: var(--dot, var(--faint)); }
        .ln-alert-success { --dot: var(--ok); }
        .ln-alert-error { --dot: var(--bad); }
        .ln-alert ul { margin: 0; padding-left: 16px; }

        /* ═══════════════════════════════════════════════════════════
           POPUPS
           ═══════════════════════════════════════════════════════════ */
        .ln-popup-container { position: fixed; top: 24px; left: 50%; transform: translateX(-50%); z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .ln-popup { min-width: 320px; max-width: 90vw; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; color: #fff; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 12px; pointer-events: auto; animation: popupSlideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .ln-popup-error { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .ln-popup-success { background: linear-gradient(135deg, #10b981, #047857); }
        @keyframes popupSlideDown { from { opacity: 0; transform: translateY(-40px); } to { opacity: 1; transform: translateY(0); } }

        /* ═══════════════════════════════════════════════════════════
           MAIN + TABBAR
           ═══════════════════════════════════════════════════════════ */
        main { animation: ln-rise .35s ease both; }
        @keyframes ln-rise { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        main.ln-main { padding-bottom: calc(var(--dock-h) + var(--dock-gap) + 3.5rem + env(safe-area-inset-bottom, 0px)); }

        .ln-tabbar { position: fixed; z-index: 100; left: 12px; right: 12px; bottom: calc(var(--dock-gap) + env(safe-area-inset-bottom, 0px)); height: var(--dock-h); padding: 6px; border-radius: 26px; display: flex; gap: 2px; }
        .ln-ti { position: relative; flex: 1 1 0; min-width: 0; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 20px; background: transparent; cursor: pointer; overflow: hidden; white-space: nowrap; font-family: inherit; font-size: 12px; font-weight: 600; color: var(--faint); text-decoration: none; transition: flex-grow .3s cubic-bezier(.2,.8,.2,1), background .2s, color .2s; }
        .ln-ti svg { flex: none; width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; }
        .ln-ti .lbl { max-width: 0; margin-left: 0; opacity: 0; overflow: hidden; transition: max-width .3s cubic-bezier(.2,.8,.2,1), margin .3s, opacity .2s; }
        .ln-ti:hover { color: var(--ink); }
        .ln-ti.active, .ln-ti.open { flex-grow: 2.7; color: var(--ink); background: rgba(0,0,0,.07); }
        .ln-ti.active .lbl, .ln-ti.open .lbl { max-width: 84px; margin-left: 6px; opacity: 1; }
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

        /* ═══════════════════════════════════════════════════════════
           SHEET ("Lainnya")
           ═══════════════════════════════════════════════════════════ */
        .ln-bd { position: fixed; inset: 0; z-index: 98; background: rgba(0,0,0,.2); -webkit-backdrop-filter: blur(3px); backdrop-filter: blur(3px); opacity: 0; pointer-events: none; transition: opacity .2s; }
        .ln-bd.open { opacity: 1; pointer-events: auto; }
        .ln-sheet [hidden] { display: none !important; }

        .ln-sheet { position: fixed; z-index: 99; left: 10px; right: 10px; bottom: calc(var(--dock-h) + var(--dock-gap) + 12px + env(safe-area-inset-bottom, 0px)); display: flex; flex-direction: column; overflow: hidden; outline: none; max-height: calc(100vh - 190px); max-height: calc(100dvh - 190px); border-radius: 28px; background: rgba(255,255,255,.9); -webkit-backdrop-filter: saturate(180%) blur(30px); backdrop-filter: saturate(180%) blur(30px); border: 1px solid rgba(255,255,255,.8); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-l); opacity: 0; visibility: hidden; pointer-events: none; transform: translateY(14px) scale(.985); transition: opacity .2s, transform .26s cubic-bezier(.2,.8,.2,1), visibility 0s linear .26s; }
        .ln-sheet.open { opacity: 1; visibility: visible; pointer-events: auto; transform: none; transition-delay: 0s; }
        .ln-grab { flex: none; width: 38px; height: 4px; border-radius: 4px; background: var(--hair-2); margin: 9px auto 0; }
        .ln-sh-head { flex: none; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px 6px 20px; }
        .ln-sh-title { font-size: 17px; font-weight: 700; letter-spacing: -.02em; color: var(--ink); }
        .ln-sh-close { width: 30px; height: 30px; border-radius: 50%; border: 0; cursor: pointer; font-size: 18px; line-height: 1; color: var(--soft); background: rgba(0,0,0,.06); transition: .15s; }
        .ln-sh-close:hover { background: rgba(0,0,0,.12); color: var(--ink); }
        .ln-sh-search { flex: none; padding: 6px 16px 10px; }
        .ln-sh-search input { width: 100%; height: 42px; padding: 0 14px; border: 0; border-radius: 13px; outline: none; font-family: inherit; font-size: 16px; color: var(--ink); background: rgba(0,0,0,.055); transition: .16s; }
        .ln-sh-search input::placeholder { color: var(--faint); }
        .ln-sh-search input:focus { background: #fff; box-shadow: 0 0 0 .5px var(--hair-2), 0 0 0 4px rgba(0,0,0,.06); }
        .ln-sh-scroll { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; padding: 4px 16px 12px; }
        .ln-sg-wrap { display: grid; grid-template-columns: 1fr; gap: 18px; }
        .ln-sg-t { margin: 0 0 8px; padding: 0 4px; font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--faint); }
        .ln-sg ul { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .ln-sl { display: block; padding: 12px 14px; border-radius: 14px; font-size: 14px; font-weight: 500; line-height: 1.25; color: var(--ink); text-decoration: none; background: rgba(0,0,0,.045); transition: background .14s, color .14s; }
        .ln-sl:hover { background: rgba(0,0,0,.09); }
        .ln-sl.active { color: #fff; font-weight: 600; background: linear-gradient(180deg, #2563EB, #1E3A8A); }
        .ln-sh-empty { margin: 0; padding: 36px 20px 44px; text-align: center; font-size: 13.5px; color: var(--mute); }
        .ln-sheet-foot { flex: none; display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-top: 1px solid var(--hair); }
        .ln-av { flex: none; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-size: 14px; font-weight: 700; color: var(--ink); background: rgba(0,0,0,.07); }
        .ln-sheet-foot .who { flex: 1; min-width: 0; }
        .ln-sheet-foot .who b { display: block; font-size: 13.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ln-sheet-foot .who span { display: block; font-size: 12px; color: var(--mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ln-sheet-foot form { margin: 0; flex: none; }
        .ln-sheet-foot .ln-btn-outline { height: 36px; padding: 0 14px; font-size: 13px; }

        @media (min-width: 640px) {
            .ln-sheet { left: 50%; right: auto; width: min(840px, calc(100vw - 32px)); max-height: calc(100vh - 170px); max-height: calc(100dvh - 170px); transform: translate(-50%, 10px) scale(.985); }
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
            .ln-sl.active { background: linear-gradient(180deg, #2563EB, #1E3A8A); }
            .ln-sheet-foot { padding: 14px 24px; }
        }

        /* ═══════════════════════════════════════════════════════════
           PAGE + CARDS + FORMS
           ═══════════════════════════════════════════════════════════ */
        .ln-page-head { display: flex; flex-wrap: wrap; gap: 10px 16px; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; }
        .ln-page-title { margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -.022em; color: var(--ink); }
        .ln-page-sub { font-size: 13.5px; color: var(--mute); margin-top: 4px; }
        .ln-section-title { font-size: 16px; font-weight: 700; letter-spacing: -.01em; color: var(--ink); margin: 0 0 12px; }

        .ln-stat, .ln-table-card, .ln-form-card { background: var(--glass); -webkit-backdrop-filter: saturate(180%) blur(20px); backdrop-filter: saturate(180%) blur(20px); border: 1px solid rgba(255,255,255,.78); border-radius: 20px; box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-s); }
        .ln-stat { padding: 18px 20px; transition: transform .18s, box-shadow .18s; }
        .ln-stat:hover { transform: translateY(-2px); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-m); }
        .ln-stat .top { display: flex; align-items: flex-start; justify-content: space-between; }
        .ln-stat .lbl { font-size: 12.5px; font-weight: 600; color: var(--mute); }
        .ln-stat .ico { width: 28px; height: 28px; border-radius: 9px; display: grid; place-items: center; background: rgba(0,0,0,.05); }
        .ln-stat .ico svg { width: 15px; height: 15px; stroke: var(--soft); }
        .ln-stat .val { margin-top: 10px; font-size: 22px; font-weight: 700; letter-spacing: -.02em; color: var(--ink); word-break: break-word; }

        .ln-btn-primary, .ln-btn-outline, .ln-btn-cancel { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; padding: 0 18px; border-radius: 12px; font-family: inherit; font-size: 13.5px; font-weight: 600; text-decoration: none; cursor: pointer; transition: .16s; }
        .ln-btn-primary { color: #fff; border: 0; background: linear-gradient(180deg, #2563EB, #1E3A8A); box-shadow: 0 8px 18px -8px rgba(37,99,235,.5), inset 0 1px 0 rgba(255,255,255,.18); }
        .ln-btn-primary:hover { background: linear-gradient(180deg, #1E3A8A, #1E3A8A); transform: translateY(-1px); }
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
        .ln-input, .ln-textarea, .ln-select { width: 100%; border: 1px solid var(--hair); border-radius: 12px; padding: 10px 14px; font-family: inherit; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,.8); transition: .16s; }
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

        /* ═══════════════════════════════════════════════════════════
           AI ASSISTANT
           ═══════════════════════════════════════════════════════════ */
        .ln-ai-fab { position: fixed; z-index: 90; right: 14px; bottom: calc(var(--dock-h) + var(--dock-gap) + 14px + env(safe-area-inset-bottom, 0px)); width: 46px; height: 46px; border-radius: 50%; display: grid; place-items: center; border: 0; cursor: pointer; color: #fff; background: linear-gradient(180deg, #2563EB, #1E3A8A); box-shadow: 0 12px 26px -8px rgba(37,99,235,.5), inset 0 1px 0 rgba(255,255,255,.2); transition: transform .18s; }
        .ln-ai-fab:hover { transform: translateY(-2px) scale(1.04); }
        .ln-ai-fab:active { transform: scale(.96); }
        .ln-ai-fab svg { width: 20px; height: 20px; }
        .ln-ai-panel { position: fixed; z-index: 89; right: 14px; bottom: calc(var(--dock-h) + var(--dock-gap) + 70px + env(safe-area-inset-bottom, 0px)); width: 380px; max-width: calc(100vw - 28px); height: 540px; max-height: calc(100vh - 260px); max-height: calc(100dvh - 260px); display: none; flex-direction: column; overflow: hidden; border-radius: 24px; background: rgba(255,255,255,.94); -webkit-backdrop-filter: saturate(180%) blur(28px); backdrop-filter: saturate(180%) blur(28px); border: 1px solid rgba(255,255,255,.8); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair), var(--shadow-l); }
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
        .ln-ai-send { width: 46px; height: 42px; display: grid; place-items: center; border: 0; border-radius: 12px; cursor: pointer; color: #fff; background: linear-gradient(180deg, #2563EB, #1E3A8A); transition: .15s; }
        .ln-ai-send:hover { background: #1E3A8A; }
        .ln-ai-send:active { transform: scale(.96); }
        .ln-ai-send:disabled { opacity: .45; cursor: not-allowed; }

        @media (min-width: 640px) {
            .ln-ai-fab { right: 24px; }
            .ln-ai-panel { right: 24px; }
        }

        /* ═══════════════════════════════════════════════════════════
           MOBILE
           ═══════════════════════════════════════════════════════════ */
        @media (max-width: 639px) {
            .ln-input, .ln-textarea, .ln-select { font-size: 16px; }
            .ln-input-sm { font-size: 15px; }
            .ln-page-title { font-size: 23px; }
            .ln-form-card { padding: 18px; }
            .ln-ai-inputbar input { font-size: 16px; }
            .ln-bar { padding-left: 14px; }
            .ln-company { font-size: 11.5px; padding: 5px 10px; }
            .ln-company > span { max-width: 100px; }
            .ln-hlogout { display: none; }
            .ln-branch-btn { padding: 5px 8px; font-size: 11px; }
            .ln-banner-expired > div, .ln-banner-trial > div { flex-direction: column; text-align: center; gap: 6px; }
            .ln-brand-divider, .ln-brand-mangata { display: none; }
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

        <div class="ln-brand-group">
            <a href="{{ route('dashboard') }}" class="ln-brand" aria-label="LedgerNusa — ke Dashboard">
                <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa" height="26" decoding="async" fetchpriority="high">
            </a>
            <div class="ln-brand-divider"></div>
            <div class="ln-brand-mangata">
                <span>by</span>
                <b>Mangata Nusantara Teknologi</b>
            </div>
        </div>

        <div class="ln-right">

            {{-- ══════════ COMPANY DROPDOWN ══════════ --}}
            @if($activeCompany && $allCompanies->count() > 0)
                <div class="ln-company-wrap" id="companyDropdown">
                    <button type="button" class="ln-company" onclick="document.getElementById('companyDropdown').classList.toggle('is-open')" title="Ganti perusahaan">
                        <span>{{ $activeCompany->name }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="ln-company-menu">
                        @foreach($allCompanies as $co)
                            @php $isLocked = in_array($co->id, $lockedIds, true); @endphp
                            @if($co->id == $activeCompany->id)
                                <div class="ln-company-item active">
                                    <span>{{ $co->name }}</span>
                                    <span style="font-size:10px;color:var(--ok);font-weight:700;">Aktif</span>
                                </div>
                            @elseif($isLocked)
                                <a href="{{ route('subscription.index') }}" class="ln-company-item locked" title="Upgrade ke Pro untuk mengakses company ini">
                                    <span>{{ $co->name }}</span>
                                    <span style="font-size:11px;">🔒</span>
                                </a>
                            @else
                                <a href="{{ route('companies.switch', $co->id) }}" class="ln-company-item">
                                    <span>{{ $co->name }}</span>
                                </a>
                            @endif
                        @endforeach
                        <div class="ln-company-divider"></div>
                        <a href="{{ route('companies.index') }}" class="ln-company-item" style="font-size:12px;color:var(--mute);">
                            <span>Kelola Company</span>
                        </a>
                    </div>
                </div>
            @elseif($activeCompany)
                <a href="{{ route('companies.index') }}" class="ln-company" title="Kelola company">
                    <span>{{ $activeCompany->name }}</span>
                </a>
            @endif

            {{-- ══════════ BRANCH DROPDOWN ══════════ --}}
            @if($showBranchDropdown)
                <div class="ln-branch-wrap" id="branchDropdown">
                    <button type="button" class="ln-branch-btn" onclick="document.getElementById('branchDropdown').classList.toggle('is-open')">
                        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $activeBranch ? Str::limit($activeBranch->name, 15) : 'Konsolidasi' }}
                    </button>
                    <div class="ln-branch-menu">
                        @if(!$isStaff)
                            <a href="{{ route('branches.switch', 0) }}" class="ln-branch-item {{ !session('branch_id') ? 'active' : '' }}">
                                <span>Semua Cabang (Konsolidasi)</span>
                            </a>
                            <div class="ln-branch-divider"></div>
                        @endif

                        @foreach($branches as $b)
                            <a href="{{ route('branches.switch', $b) }}" class="ln-branch-item {{ $activeBranch && $activeBranch->id == $b->id ? 'active' : '' }}">
                                <span>{{ $b->name }}</span>
                                @if($b->is_main)
                                    <span style="font-size: 10px; opacity: .6;">Pusat</span>
                                @endif
                            </a>
                        @endforeach

                        @if(!$isStaff)
                            <div class="ln-branch-divider"></div>
                            <a href="{{ route('branches.index') }}" class="ln-branch-item" style="color: var(--mute); font-size: 12px;">
                                Kelola Cabang...
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($isPro && $branches->count() === 1)
                <div class="ln-branch-btn" style="cursor: default; background: transparent; border: none; color: var(--soft);">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $branches->first()->name }}
                </div>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="ln-hlink">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ln-hlogout">Logout</button>
            </form>
        </div>
    </div>
</header>

{{-- Banner Trial / Expired --}}
@if($isTrial)
    <div class="ln-banner-trial">
        <div>
            <span><strong>Masa Trial Pro.</strong> Anda sedang mencoba fitur Pro/Enterprise. Sisa waktu: <strong>{{ $trialDaysLeft }} hari</strong>.</span>
            <a href="{{ route('subscription.index') }}">Berlangganan Sekarang &rarr;</a>
        </div>
    </div>
@elseif($isExpired)
    <div class="ln-banner-expired">
        <div>
            <span><strong>Masa aktif habis.</strong> Anda hanya dapat melihat data. Silakan berlangganan untuk melakukan transaksi baru.</span>
            <a href="{{ route('subscription.index') }}">Berlangganan Sekarang &rarr;</a>
        </div>
    </div>
@endif

{{-- POPUP ALERT --}}
<div class="ln-popup-container" id="popupContainer">
    @if(session('success'))
        <div class="ln-popup ln-popup-success">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="ln-popup ln-popup-error">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="ln-popup ln-popup-error" style="align-items: flex-start;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <strong style="display: block; margin-bottom: 4px;">Terjadi Kesalahan:</strong>
                <ul style="margin: 0; padding-left: 16px; font-size: 13px; font-weight: normal;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>

<main id="konten" class="ln-main max-w-6xl mx-auto px-4 py-6">
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
                @if(count($items) > 0)
                    <section class="ln-sg" data-group="{{ $group }}">
                        <p class="ln-sg-t">{{ $group }}</p>
                        <ul>
                            @foreach($items as [$label, $route, $match])
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
        <button type="button" onclick="lnAiQuick('Cara lihat laba rugi?')">Laba Rugi</button>
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
// ═══════════════════════════════════════════════════════
// Menu "Lainnya"
// ═══════════════════════════════════════════════════════
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

// ═══════════════════════════════════════════════════════
// Close dropdowns saat klik di luar
// ═══════════════════════════════════════════════════════
document.addEventListener('click', function (e) {
    const companyWrap = document.getElementById('companyDropdown');
    if (companyWrap && !companyWrap.contains(e.target)) {
        companyWrap.classList.remove('is-open');
    }
    const branchWrap = document.getElementById('branchDropdown');
    if (branchWrap && !branchWrap.contains(e.target)) {
        branchWrap.classList.remove('is-open');
    }
});

// Close on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('companyDropdown')?.classList.remove('is-open');
        document.getElementById('branchDropdown')?.classList.remove('is-open');
    }
});

// ═══════════════════════════════════════════════════════
// Asisten AI
// ═══════════════════════════════════════════════════════
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
            let formattedText = (data.answer || '')
    .replace(/</g, '&lt;')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') 
    .replace(/\*(.*?)\*/g, '<em>$1</em>')             
    .replace(/\n/g, '<br>');                          

    lnAiAddMsg(formattedText, 'assistant', data.action_url, data.action_label);
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

// Auto hide popup alert after 5 seconds
setTimeout(() => {
    const popups = document.querySelectorAll('.ln-popup');
    popups.forEach(popup => {
        popup.style.transition = 'all 0.4s ease';
        popup.style.opacity = '0';
        popup.style.transform = 'translateY(-20px)';
        setTimeout(() => popup.remove(), 400);
    });
}, 5000);
</script>

</body>
</html>