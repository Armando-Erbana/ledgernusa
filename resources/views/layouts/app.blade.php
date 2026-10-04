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
            --ln-shadow-lg: 0 20px 60px rgba(16, 26, 48, 0.22);
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
            z-index: 50;
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
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
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
        .ln-navitem.has-dropdown.active::before { display: none; }
        .ln-navitem.has-dropdown.open { color: var(--ln-navy); font-weight: 600; }
        .ln-navitem.has-dropdown.open svg { stroke: var(--ln-cyan); transform: rotate(180deg); }

        @media (min-width: 768px) {
            nav.ln-navbar {
                left: 50%; right: auto;
                width: min(620px, calc(100% - 32px));
                bottom: 16px;
                transform: translateX(-50%);
                border: 1px solid var(--ln-line);
                border-radius: 20px;
                overflow: visible;
                box-shadow: var(--ln-shadow-md);
            }
            .ln-navitem { padding-bottom: 9px; font-size: 10.5px; }
            .ln-navitem:hover { background: rgba(20, 33, 58, 0.03); }
        }

        /* ---- Dropdown menu "Lainnya" ---- */
        .ln-dropdown-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(16, 26, 48, 0.4);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 48;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .ln-dropdown-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        .ln-dropdown {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: calc(78px + env(safe-area-inset-bottom, 0px));
            z-index: 49;
            background: #fff;
            border: 1px solid var(--ln-line);
            border-radius: 20px;
            box-shadow: var(--ln-shadow-lg);
            padding: 8px;
            opacity: 0;
            transform: translateY(16px) scale(0.98);
            pointer-events: none;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 480px;
            margin: 0 auto;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }
        .ln-dropdown.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        @media (min-width: 768px) {
            .ln-dropdown { bottom: 96px; }
        }

        .ln-dropdown-header {
            padding: 12px 14px 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--ln-ink-faint);
        }
        .ln-dropdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            padding: 4px;
        }
        @media (max-width: 380px) {
            .ln-dropdown-grid { grid-template-columns: repeat(3, 1fr); }
        }
        .ln-dropdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 4px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--ln-navy);
            font-size: 11px;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            transition: background 0.15s ease;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .ln-dropdown-item:hover { background: rgba(28, 143, 196, 0.07); }
        .ln-dropdown-item.active { background: rgba(28, 143, 196, 0.12); color: var(--ln-cyan); }
        .ln-dropdown-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 10px -4px rgba(16, 26, 48, 0.3), inset 0 1px 0 rgba(255,255,255,0.25);
        }
        .ln-dropdown-icon svg { width: 20px; height: 20px; stroke: currentColor; }

        .ln-dropdown-divider {
            height: 1px;
            background: var(--ln-line);
            margin: 6px 12px;
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
        .ln-stat.in { --ln-stat-accent: #1E7A4C; --ln-stat-tint: rgba(30,122,76,0.1); }
        .ln-stat.out { --ln-stat-accent: #C0392B; --ln-stat-tint: rgba(192,57,43,0.1); }

        .ln-btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 16px;
            background: linear-gradient(180deg, #1B2C4B 0%, var(--ln-navy) 100%);
            color: #fff; font-size: 13.5px; font-weight: 500;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(16, 26, 48, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .ln-btn-primary:hover { background: linear-gradient(180deg, #2AA0D6 0%, var(--ln-cyan) 100%); box-shadow: 0 6px 16px rgba(28, 143, 196, 0.3); }
        .ln-btn-primary:active { transform: scale(0.98); }
        .ln-btn-outline {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 16px; background: #fff; color: var(--ln-navy);
            font-size: 13.5px; font-weight: 500;
            border: 1px solid var(--ln-line); border-radius: 10px;
            transition: all 0.15s ease;
            text-decoration: none;
            cursor: pointer;
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
        .ln-badge.posted, .ln-badge.in, .ln-badge.paid, .ln-badge.active { background: rgba(30, 122, 76, 0.1); color: #1E7A4C; }
        .ln-badge.draft, .ln-badge.partial { background: rgba(180, 145, 91, 0.14); color: #8A6B3B; }
        .ln-badge.out, .ln-badge.unpaid, .ln-badge.expired { background: rgba(192, 57, 43, 0.1); color: #A93226; }

        .ln-action { font-size: 12px; color: var(--ln-cyan); font-weight: 500; transition: color 0.15s ease; background: none; border: none; cursor: pointer; padding: 0; font-family: inherit; text-decoration: none; }
        .ln-action:hover { color: var(--ln-navy); }
        .ln-action.green { color: #1E7A4C; }
        .ln-action.green:hover { color: #14532D; }
        .ln-action.red { color: #C0392B; }
        .ln-action.red:hover { color: #8A2B22; }
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

        .ln-remove-btn { color: #C0392B; opacity: 0.75; font-size: 13px; background: none; border: none; transition: opacity 0.15s ease; cursor: pointer; }
        .ln-remove-btn:hover { opacity: 1; }
        .ln-add-row { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #fff; border: 1px dashed #CBD2DE; border-radius: 10px; font-size: 13px; color: var(--ln-ink-soft); transition: all 0.15s ease; cursor: pointer; font-family: inherit; }
        .ln-add-row:hover { border-color: var(--ln-cyan); color: var(--ln-cyan); background: rgba(28, 143, 196, 0.03); }

        .ln-balance-ok { color: #1E7A4C; font-size: 13px; font-weight: 500; }
        .ln-balance-bad { color: #C0392B; font-size: 13px; font-weight: 500; }

        .ln-form-actions { padding-top: 6px; display: flex; gap: 10px; }
        .ln-btn-cancel { padding: 9px 16px; background: #fff; border: 1px solid var(--ln-line); border-radius: 10px; font-size: 13.5px; color: var(--ln-ink-soft); transition: all 0.15s ease; text-decoration: none; display: inline-flex; align-items: center; cursor: pointer; font-family: inherit; }
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

        .ln-checkbox { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--ln-ink-soft); }
        .ln-checkbox input { width: 15px; height: 15px; border-radius: 4px; border: 1.5px solid var(--ln-line); accent-color: var(--ln-navy); }

        /* ---- AI Assistant ---- */
        .ln-ai-fab {
            position: fixed;
            right: 16px;
            bottom: calc(90px + env(safe-area-inset-bottom, 0px));
            z-index: 45;
            width: 54px; height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--ln-navy) 0%, var(--ln-cyan) 100%);
            color: #fff;
            border: none;
            box-shadow: 0 10px 28px rgba(20, 33, 58, 0.35), 0 2px 6px rgba(20, 33, 58, 0.2);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .ln-ai-fab:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 14px 32px rgba(28, 143, 196, 0.45); }
        .ln-ai-fab:active { transform: scale(0.96); }
        .ln-ai-fab svg { width: 24px; height: 24px; }
        .ln-ai-fab::after {
            content: "";
            position: absolute;
            top: 4px; right: 4px;
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #10B981;
            border: 2px solid #fff;
            animation: ln-pulse 2s ease-in-out infinite;
        }
        @keyframes ln-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        @media (min-width: 768px) {
            .ln-ai-fab { bottom: 96px; right: 24px; }
        }

        .ln-ai-panel {
            position: fixed;
            right: 16px;
            bottom: calc(150px + env(safe-area-inset-bottom, 0px));
            z-index: 44;
            width: 380px;
            max-width: calc(100vw - 32px);
            height: 540px;
            max-height: calc(100vh - 200px);
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--ln-shadow-lg);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--ln-line);
            animation: ln-slide-up 0.25s ease;
        }
        .ln-ai-panel.open { display: flex; }
        @keyframes ln-slide-up {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to   { opacity: 1; transform: none; }
        }
        @media (min-width: 768px) {
            .ln-ai-panel { bottom: 156px; right: 24px; }
        }

        .ln-ai-head {
            background: linear-gradient(135deg, var(--ln-navy) 0%, var(--ln-cyan) 100%);
            color: #fff;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ln-ai-head-title { font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 15px; }
        .ln-ai-head-sub { font-size: 11px; opacity: 0.8; margin-top: 2px; }
        .ln-ai-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            width: 28px; height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.15s ease;
        }
        .ln-ai-close:hover { background: rgba(255,255,255,0.28); }

        .ln-ai-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #F7F7F5;
            font-size: 13.5px;
            line-height: 1.55;
            -webkit-overflow-scrolling: touch;
        }
        .ln-ai-msg {
            margin-bottom: 12px;
            padding: 10px 14px;
            border-radius: 14px;
            max-width: 88%;
            word-wrap: break-word;
            white-space: pre-line;
            animation: ln-msg-in 0.2s ease;
        }
        @keyframes ln-msg-in {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: none; }
        }
        .ln-ai-msg.user {
            background: var(--ln-navy);
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .ln-ai-msg.assistant {
            background: #fff;
            border: 1px solid var(--ln-line);
            border-bottom-left-radius: 4px;
        }
        .ln-ai-msg .action { margin-top: 10px; }
        .ln-ai-msg .action a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--ln-cyan);
            color: #fff;
            padding: 7px 13px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: background 0.15s ease;
        }
        .ln-ai-msg .action a:hover { background: var(--ln-navy); }
        .ln-ai-typing { display: inline-flex; gap: 4px; padding: 4px 0; }
        .ln-ai-typing span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--ln-ink-faint);
            animation: ln-dot 1.2s infinite;
        }
        .ln-ai-typing span:nth-child(2) { animation-delay: 0.15s; }
        .ln-ai-typing span:nth-child(3) { animation-delay: 0.3s; }
        @keyframes ln-dot {
            0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
            30% { opacity: 1; transform: translateY(-2px); }
        }

        .ln-ai-chips {
            padding: 8px 12px;
            background: #fff;
            border-top: 1px solid var(--ln-line);
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .ln-ai-chips::-webkit-scrollbar { display: none; }
        .ln-ai-chips button {
            white-space: nowrap;
            background: #F7F7F5;
            border: 1px solid var(--ln-line);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 11.5px;
            color: var(--ln-ink-soft);
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s ease;
        }
        .ln-ai-chips button:hover { background: rgba(28, 143, 196, 0.08); border-color: var(--ln-cyan); color: var(--ln-cyan); }

        .ln-ai-inputbar {
            padding: 10px 12px calc(10px + env(safe-area-inset-bottom, 0px));
            border-top: 1px solid var(--ln-line);
            display: flex;
            gap: 8px;
            background: #fff;
        }
        .ln-ai-inputbar input {
            flex: 1;
            border: 1px solid var(--ln-line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ln-ai-inputbar input:focus { border-color: var(--ln-cyan); box-shadow: 0 0 0 4px rgba(28, 143, 196, 0.13); }
        .ln-ai-send {
            background: var(--ln-cyan);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0 18px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .ln-ai-send:hover { background: var(--ln-navy); }
        .ln-ai-send:active { transform: scale(0.96); }
        .ln-ai-send:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ---- Mobile ---- */
        @media (max-width: 640px) {
            .ln-input, .ln-textarea, .ln-select { font-size: 16px; }
            .ln-input-sm { font-size: 15px; }
            .ln-page-title { font-size: 21px; }
            .ln-form-card { padding: 18px; }
            .ln-company { max-width: 120px; }
            .ln-ai-inputbar input { font-size: 16px; }
            .ln-navitem { font-size: 9.5px; }
            .ln-navitem svg { width: 19px; height: 19px; }
        }

        @media (prefers-reduced-motion: reduce) {
            main, .ln-ai-panel, .ln-ai-msg { animation: none; }
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
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="ln-link" style="color:#B45309;">⚙ Admin</a>
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

{{-- Backdrop dropdown --}}
<div class="ln-dropdown-backdrop" id="lnDdBackdrop" onclick="lnToggleMore()"></div>

{{-- Dropdown "Lainnya" --}}
<div class="ln-dropdown" id="lnDdMenu" role="menu" aria-label="Menu Lainnya">

    {{-- GRUP 1: TRANSAKSI --}}
    <div class="ln-dropdown-header">Transaksi</div>
    <div class="ln-dropdown-grid">
        <a href="{{ route('cash.index') }}" class="ln-dropdown-item {{ request()->routeIs('cash.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#1E7A4C,#2FA36B);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6.5" width="19" height="12" rx="2"/><circle cx="12" cy="12.5" r="2.4"/><path d="M6 6.5V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1.5"/></svg>
            </span>
            Kas & Bank
        </a>
        <a href="{{ route('bank-rec.index') }}" class="ln-dropdown-item {{ request()->routeIs('bank-rec.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0E7490,#06B6D4);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c1.5 0 2.91.37 4.16 1.03"/><path d="M16 5l5-3-1 5"/></svg>
            </span>
            Rekonsiliasi
        </a>
        <a href="{{ route('transfers.index') }}" class="ln-dropdown-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#1C8FC4,#4FC3EC);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16"/><path d="m14 6 6 6-6 6"/><path d="M10 6 4 12l6 6"/></svg>
            </span>
            Transfer
        </a>
        <a href="{{ route('sales-orders.index') }}" class="ln-dropdown-item {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#7C3AED,#A855F7);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15l2 2 4-4"/></svg>
            </span>
            Sales Order
        </a>
        <a href="{{ route('delivery-orders.index') }}" class="ln-dropdown-item {{ request()->routeIs('delivery-orders.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0891B2,#22D3EE);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </span>
            Delivery
        </a>
        <a href="{{ route('sales.index') }}" class="ln-dropdown-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#B4915B,#D4A870);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </span>
            Penjualan
        </a>
        <a href="{{ route('purchase-orders.index') }}" class="ln-dropdown-item {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#BE123C,#FB7185);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15l2 2 4-4"/></svg>
            </span>
            Purchase Order
        </a>
        <a href="{{ route('goods-receipts.index') }}" class="ln-dropdown-item {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#16A34A,#4ADE80);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </span>
            Penerimaan
        </a>
        <a href="{{ route('purchases.index') }}" class="ln-dropdown-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#DC2626,#F87171);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </span>
            Pembelian
        </a>
        <a href="{{ route('customers.index') }}" class="ln-dropdown-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#7C3AED,#A855F7);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c.8-3 2.8-4.6 5.5-4.6s4.7 1.6 5.5 4.6"/></svg>
            </span>
            Customer
        </a>
        <a href="{{ route('suppliers.index') }}" class="ln-dropdown-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0891B2,#22D3EE);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            </span>
            Supplier
        </a>
    </div>

    <div class="ln-dropdown-divider"></div>

    {{-- GRUP 2: PERSEDIAAN --}}
    <div class="ln-dropdown-header">Persediaan</div>
    <div class="ln-dropdown-grid">
        <a href="{{ route('products.index') }}" class="ln-dropdown-item {{ request()->routeIs('products.*','product-categories.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#F59E0B,#FBBF24);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </span>
            Produk
        </a>
        <a href="{{ route('stock.index') }}" class="ln-dropdown-item {{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#059669,#10B981);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 4-6"/></svg>
            </span>
            Mutasi Stok
        </a>
        <a href="{{ route('warehouses.index') }}" class="ln-dropdown-item {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0284C7,#38BDF8);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
            </span>
            Gudang
        </a>
        <a href="{{ route('product-categories.index') }}" class="ln-dropdown-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#9333EA,#C084FC);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </span>
            Kategori
        </a>
    </div>

    <div class="ln-dropdown-divider"></div>

    {{-- GRUP 3: AKUNTANSI --}}
    <div class="ln-dropdown-header">Akuntansi</div>
    <div class="ln-dropdown-grid">
        <a href="{{ route('fixed-assets.index') }}" class="ln-dropdown-item {{ request()->routeIs('fixed-assets.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0F766E,#14B8A6);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
            </span>
            Aset Tetap
        </a>
        <a href="{{ route('tax.index') }}" class="ln-dropdown-item {{ request()->routeIs('tax.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#7C2D12,#EA580C);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11H5a2 2 0 0 1 0-4h4"/><path d="M9 11v2"/><path d="M15 7h4a2 2 0 0 1 0 4h-4"/><path d="M15 7v10"/><path d="M9 13v4"/><rect x="4" y="17" width="16" height="4" rx="1"/></svg>
            </span>
            Pajak
        </a>
        <a href="{{ route('employees.index') }}" class="ln-dropdown-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#4338CA,#6366F1);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            Karyawan
        </a>
        <a href="{{ route('payrolls.index') }}" class="ln-dropdown-item {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#C026D3,#E879F9);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
            </span>
            Payroll
        </a>
        <a href="{{ route('contacts.index') }}" class="ln-dropdown-item {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#0EA5E9,#38BDF8);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/></svg>
            </span>
            Kontak
        </a>
        <a href="{{ route('subscription.index') }}" class="ln-dropdown-item {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#14213A,#1B2C4B);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19"/></svg>
            </span>
            Langganan
        </a>
    </div>

    <div class="ln-dropdown-divider"></div>

    {{-- GRUP 4: SISTEM --}}
    <div class="ln-dropdown-header">Sistem</div>
    <div class="ln-dropdown-grid">
        <a href="{{ route('companies.index') }}" class="ln-dropdown-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#5B6577,#8B96A9);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.6"/><path d="M12 3.5v2.3M12 18.2v2.3M20.5 12h-2.3M5.8 12H3.5M17.8 6.2l-1.6 1.6M7.8 16.2l-1.6 1.6M17.8 17.8l-1.6-1.6M7.8 7.8 6.2 6.2"/></svg>
            </span>
            Setting
        </a>
        <a href="{{ route('profile.edit') }}" class="ln-dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#EC4899,#F472B6);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4.5 4-6.5 8-6.5s7 2 8 6.5"/></svg>
            </span>
            Profil
        </a>
        <form method="POST" action="{{ route('logout') }}" style="display:contents;">
            @csrf
            <button type="submit" class="ln-dropdown-item" style="color:#C0392B;">
                <span class="ln-dropdown-icon" style="background:linear-gradient(135deg,#DC2626,#EF4444);">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                </span>
                Logout
            </button>
        </form>
    </div>
</div>

<nav class="ln-navbar">
    <a href="{{ route('dashboard') }}" class="ln-navitem {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9"/></svg>
        Dashboard
    </a>
    <a href="{{ route('cash.index') }}" class="ln-navitem {{ request()->routeIs('cash.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6.5" width="19" height="12" rx="2"/><circle cx="12" cy="12.5" r="2.4"/><path d="M6 6.5V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1.5"/></svg>
        Kas
    </a>
    <a href="{{ route('sales.index') }}" class="ln-navitem {{ request()->routeIs('sales.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Penjualan
    </a>
    <a href="{{ route('journals.index') }}" class="ln-navitem {{ request()->routeIs('journals.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M5 17.5V7.5a3 3 0 0 1 3-3"/><path d="M9 9h7M9 12.5h7"/></svg>
        Jurnal
    </a>
    <a href="{{ route('reports.index') }}" class="ln-navitem {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4M12 15V9M16 15v-6"/></svg>
        Laporan
    </a>
    <button type="button" class="ln-navitem has-dropdown {{ request()->routeIs('accounts.*','transfers.*','customers.*','suppliers.*','purchases.*','contacts.*','subscription.*','companies.*','profile.*','fixed-assets.*','tax.*','products.*','product-categories.*','warehouses.*','stock.*','bank-rec.*','sales-orders.*','delivery-orders.*','purchase-orders.*','goods-receipts.*','employees.*','payrolls.*') ? 'active' : '' }}" onclick="lnToggleMore()" id="lnMoreBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
        Lainnya
    </button>
</nav>

{{-- ==================== AI ASSISTANT ==================== --}}
<button type="button" class="ln-ai-fab" onclick="lnAiToggle()" title="Tanya Asisten LedgerNusa" aria-label="Buka Asisten AI">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
    </svg>
</button>

<div class="ln-ai-panel" id="lnAiPanel" role="dialog" aria-label="Asisten LedgerNusa">
    <div class="ln-ai-head">
        <div>
            <div class="ln-ai-head-title">Asisten LedgerNusa</div>
            <div class="ln-ai-head-sub">Tanya apa saja soal akuntansi</div>
        </div>
        <button type="button" class="ln-ai-close" onclick="lnAiToggle()" aria-label="Tutup">✕</button>
    </div>

    <div class="ln-ai-messages" id="lnAiMessages"></div>

    <div class="ln-ai-chips" id="lnAiChips">
        <button type="button" onclick="lnAiQuick('Cara input jurnal?')">Cara input jurnal?</button>
        <button type="button" onclick="lnAiQuick('Apa itu COA?')">Apa itu COA?</button>
        <button type="button" onclick="lnAiQuick('Cara lihat laba rugi?')">Laba rugi</button>
        <button type="button" onclick="lnAiQuick('Cara install ke HP?')">Install HP</button>
        <button type="button" onclick="lnAiQuick('Berapa lama trial?')">Trial</button>
    </div>

    <div class="ln-ai-inputbar">
        <input type="text" id="lnAiInput" placeholder="Ketik pertanyaan..." autocomplete="off"
               onkeypress="if(event.key==='Enter'){event.preventDefault();lnAiSend();}">
        <button type="button" class="ln-ai-send" id="lnAiSend" onclick="lnAiSend()" aria-label="Kirim">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
        </button>
    </div>
</div>

<script>
// ===== Dropdown "Lainnya" =====
window.lnToggleMore = function() {
    const menu = document.getElementById('lnDdMenu');
    const backdrop = document.getElementById('lnDdBackdrop');
    const btn = document.getElementById('lnMoreBtn');
    const isOpen = menu.classList.contains('open');
    if (isOpen) {
        menu.classList.remove('open');
        backdrop.classList.remove('open');
        btn.classList.remove('open');
    } else {
        menu.classList.add('open');
        backdrop.classList.add('open');
        btn.classList.add('open');
    }
};

// Tutup dropdown kalau tekan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const menu = document.getElementById('lnDdMenu');
        if (menu && menu.classList.contains('open')) lnToggleMore();
    }
});

// ===== AI Assistant =====
(function() {
    let convId = null;
    let greeted = false;
    const ENDPOINT = @json(route('ai.chat'));
    const CSRF = @json(csrf_token());

    window.lnAiToggle = function() {
        const panel = document.getElementById('lnAiPanel');
        const isOpen = panel.classList.contains('open');
        if (isOpen) {
            panel.classList.remove('open');
        } else {
            panel.classList.add('open');
            if (!greeted) {
                greeted = true;
                lnAiAddMsg('Halo! 👋 Saya asisten LedgerNusa.<br>Ada yang bisa saya bantu?', 'assistant');
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

    window.lnAiQuick = function(text) {
        document.getElementById('lnAiInput').value = text;
        lnAiSend();
    };

    window.lnAiSend = async function() {
        const input = document.getElementById('lnAiInput');
        const btn = document.getElementById('lnAiSend');
        const msg = input.value.trim();
        if (!msg || btn.disabled) return;

        input.value = '';
        input.disabled = true;
        btn.disabled = true;

        lnAiAddMsg(msg.replace(/</g, '&lt;'), 'user');

        const typing = lnAiAddMsg(
            '<span class="ln-ai-typing"><span></span><span></span><span></span></span>',
            'assistant'
        );

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