<!DOCTYPE html>
<html lang="id">
<head>
@php
    $lpTitle = 'LedgerNusa — Aplikasi Akuntansi Online untuk UMKM & Bisnis Berkembang';
    $lpDesc  = 'Aplikasi akuntansi online: jurnal otomatis, kas & bank, penjualan, pembelian, persediaan, pajak, dan laporan keuangan yang selalu balance. Coba gratis 7 hari tanpa kartu kredit.';
    $lpUrl   = url('/');

    $plans = [
        ['Starter',      175000, 30, '3 user',  '300 jurnal / bulan',   '1 company', false, ['Jurnal, kas & bank, COA', 'Laporan keuangan lengkap', 'Penjualan & pembelian dasar']],
        ['Professional', 375000, 30, '10 user', '2.000 jurnal / bulan', '3 company', true,  ['Semua fitur Starter', 'Persediaan & gudang', 'Sales Order, Purchase Order, DO & GR', 'Pajak: PPN, PPh 23, rekap tahunan']],
        ['Enterprise',   600000, 60, '10 user', '2.000 jurnal / bulan', '3 company', false, ['Fitur sama dengan Professional', 'Bayar 2 bulan sekaligus', 'Lebih hemat per bulan']],
    ];

    $faq = [
        ['Apakah ada masa percobaan gratis?', 'Ya. Anda bisa mencoba LedgerNusa gratis selama 7 hari tanpa kartu kredit, dan tidak perlu menghubungi siapa pun untuk memulai.'],
        ['Apakah bisa dipakai lewat HP?', 'Bisa. LedgerNusa berjalan di browser dan dapat dipasang ke layar utama ponsel seperti aplikasi, jadi Anda bisa mencatat transaksi dari mana saja.'],
        ['Bagaimana cara pembayarannya?', 'Pembayaran lewat transfer bank. Setelah transfer, unggah bukti di halaman Langganan, dan verifikasi selesai paling lama 1x24 jam.'],
        ['Berapa banyak perusahaan dan user yang bisa dikelola?', 'Paket Starter untuk 1 company dan 3 user. Paket Professional dan Enterprise untuk 3 company dan 10 user, dengan data tiap company terpisah.'],
        ['Apa bedanya paket Professional dan Enterprise?', 'Fitur dan batasannya sama. Bedanya hanya durasi: Professional berlaku 30 hari, Enterprise berlaku 60 hari dengan harga per bulan yang lebih hemat.'],
        ['Apakah ada fitur pajak?', 'Ada. Paket Professional dan Enterprise menyediakan laporan PPN, PPh 23, dan rekap pajak tahunan.'],
    ];

    $ldApp = [
        '@context' => 'https://schema.org', '@type' => 'SoftwareApplication', 'name' => 'LedgerNusa',
        'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web, Android, iOS',
        'description' => $lpDesc, 'url' => $lpUrl, 'inLanguage' => 'id-ID',
        'offers' => collect($plans)->map(fn($p) => [
            '@type' => 'Offer', 'name' => $p[0], 'price' => (string) $p[1], 'priceCurrency' => 'IDR',
            'description' => 'Berlaku ' . $p[2] . ' hari',
        ])->values()->all(),
    ];
    $ldOrg = [
        '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Mangata Nusantara Teknologi',
        'url' => $lpUrl, 'logo' => asset('images/logo_akutansi_ledger_nusa-removebg-preview.png'),
    ];
    $ldFaq = [
        '@context' => 'https://schema.org', '@type' => 'FAQPage',
        'mainEntity' => collect($faq)->map(fn($f) => [
            '@type' => 'Question', 'name' => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ])->all(),
    ];
@endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#EDF0F6">

    <title>{{ $lpTitle }}</title>
    <meta name="description" content="{{ $lpDesc }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $lpUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="LedgerNusa">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $lpTitle }}">
    <meta property="og:description" content="{{ $lpDesc }}">
    <meta property="og:url" content="{{ $lpUrl }}">
    <meta property="og:image" content="{{ asset('images/og-cover.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $lpTitle }}">
    <meta name="twitter:description" content="{{ $lpDesc }}">
    <meta name="twitter:image" content="{{ asset('images/og-cover.png') }}">

    <link rel="icon" type="image/png" href="/images/icon-192.png">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
    <link rel="manifest" href="/manifest.json">

    {{-- Tandai JS aktif lebih awal: animasi reveal hanya aktif kalau JS jalan, jadi tanpa JS konten tetap tampil --}}
    <script>document.documentElement.classList.add('js');</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script type="application/ld+json">{!! json_encode($ldApp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($ldOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <style>
        :root {
            --b2: #3E73F5;           
            --b1: #142B78;           
            --accent: #2A55D6;       
            --grad: linear-gradient(145deg, var(--b2), var(--b1));
            --grad-btn: linear-gradient(180deg, #4B7AF7, #2F5BE0);
            --ink: #0C1530; --soft: #36415C; --mute: #5B6680; --faint: #8A93A8;
            --hair: rgba(12,21,48,.09);
            --ease: cubic-bezier(.2,.7,.2,1);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; scroll-padding-top: 96px; }
        body {
            margin: 0; min-height: 100vh; color: var(--ink); background: #EDF0F6;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Segoe UI', system-ui, sans-serif;
            line-height: 1.5; letter-spacing: -.005em; -webkit-font-smoothing: antialiased; overflow-x: hidden;
        }
        /* butiran halus supaya kaca terasa seperti material, bukan blok warna rata */
        body::after {
            content: ""; position: fixed; inset: 0; z-index: 150; pointer-events: none; opacity: .045;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 .6 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }
        a { color: inherit; -webkit-tap-highlight-color: transparent; }
        h1, h2, h3, p, ul { margin: 0; }
        h1, h2 { text-wrap: balance; }
        ul { padding: 0; list-style: none; }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; border-radius: 10px; }
        .skip { position: absolute; left: 12px; top: -60px; z-index: 200; padding: 10px 16px; border-radius: 12px; background: var(--ink); color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: top .15s; }
        .skip:focus { top: 12px; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }

        .bg { position: fixed; inset: 0; z-index: -1; overflow: hidden; background: linear-gradient(180deg, #F2F5FB, #E6EBF4); pointer-events: none; }
        .bg i { position: absolute; border-radius: 50%; filter: blur(90px); will-change: transform; }
        .bg i:nth-child(1) { width: 580px; height: 580px; left: -150px; top: -170px; background: var(--b2); opacity: .30; animation: drift1 44s ease-in-out infinite alternate; }
        .bg i:nth-child(2) { width: 540px; height: 540px; right: -170px; top: 240px; background: var(--b1); opacity: .15; animation: drift2 52s ease-in-out infinite alternate; }
        .bg i:nth-child(3) { width: 500px; height: 500px; left: 30%; bottom: -280px; background: var(--b2); opacity: .22; animation: drift1 60s ease-in-out infinite alternate-reverse; }
        @keyframes drift1 { to { transform: translate(70px, 50px) scale(1.06); } }
        @keyframes drift2 { to { transform: translate(-60px, 40px) scale(1.08); } }

        .glass {
            position: relative; border-radius: 28px;
            background: linear-gradient(180deg, rgba(255,255,255,.68), rgba(255,255,255,.38));
            -webkit-backdrop-filter: blur(28px) saturate(170%); backdrop-filter: blur(28px) saturate(170%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.9),
                inset 0 -1px 0 rgba(255,255,255,.2),
                0 26px 56px -28px rgba(18,36,92,.32),
                0 2px 8px rgba(18,36,92,.05);
            transition: box-shadow .4s var(--ease), background .4s var(--ease);
        }
        .glass::before {
            content: ""; position: absolute; inset: 0; border-radius: inherit; padding: 1px; pointer-events: none;
            background: linear-gradient(140deg, rgba(255,255,255,.95), rgba(255,255,255,.1) 34%, rgba(255,255,255,.05) 66%, rgba(255,255,255,.75));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor;
            mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) { .glass { background: rgba(250,252,255,.96); } }

        .glow > * { position: relative; z-index: 1; }
        .glow::after {
            content: ""; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; opacity: 0; transition: opacity .4s var(--ease);
            background: radial-gradient(280px circle at var(--mx, 50%) var(--my, 0%), rgba(255,255,255,.75), transparent 65%);
        }
        .glow:hover::after { opacity: 1; }

        /* ===== Tombol ===== */
        .btn { position: relative; display: inline-flex; align-items: center; justify-content: center; height: 48px; padding: 0 26px; border: 0; border-radius: 999px; cursor: pointer;
            font-family: inherit; font-size: 15px; font-weight: 600; text-decoration: none; white-space: nowrap; transition: transform .25s var(--ease), box-shadow .25s var(--ease), background .25s; }
        .btn-grad { color: #fff; background: var(--grad-btn); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 12px 24px -12px rgba(30,70,200,.7); }
        .btn-grad:hover { transform: translateY(-2px); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 18px 30px -12px rgba(30,70,200,.75); }
        .btn-glass { color: var(--ink); background: rgba(255,255,255,.6); -webkit-backdrop-filter: blur(18px) saturate(170%); backdrop-filter: blur(18px) saturate(170%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 0 0 1px rgba(255,255,255,.6), 0 10px 22px -14px rgba(18,36,92,.35); }
        .btn-glass:hover { background: rgba(255,255,255,.85); transform: translateY(-2px); }
        .btn-white { color: var(--b1); background: #fff; box-shadow: 0 16px 30px -14px rgba(6,18,70,.6); }
        .btn-white:hover { transform: translateY(-2px); }
        .btn:active { transform: scale(.98); }
        .btn-sm { height: 40px; padding: 0 20px; font-size: 14px; }

        /* ===== Navbar ===== */
        .nav { position: fixed; z-index: 60; top: 0; left: 0; right: 0; padding: calc(12px + env(safe-area-inset-top, 0px)) 14px 0; }
        .bar { max-width: 1040px; margin: 0 auto; min-height: 60px; padding: 8px 10px 8px 22px; border-radius: 999px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .bar.scrolled { background: linear-gradient(180deg, rgba(255,255,255,.84), rgba(255,255,255,.58));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 18px 40px -20px rgba(18,36,92,.45), 0 2px 8px rgba(18,36,92,.06); }
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; flex: none; }
        .brand img { height: 28px; width: auto; display: block; }
        .brand span { padding-left: 12px; border-left: 1px solid var(--hair); font-size: 11.5px; font-weight: 500; line-height: 1.25; color: var(--faint); white-space: nowrap; }
        .links { display: flex; align-items: center; gap: 2px; margin: 0 auto; }
        .links a { padding: 8px 15px; border-radius: 999px; font-size: 14px; font-weight: 500; color: var(--mute); text-decoration: none; transition: .2s; }
        .links a:hover { color: var(--ink); background: rgba(255,255,255,.75); }
        .cta { display: flex; align-items: center; gap: 4px; flex: none; }
        .login { padding: 8px 16px; border-radius: 999px; font-size: 14px; font-weight: 600; color: var(--soft); text-decoration: none; transition: .2s; }
        .login:hover { color: var(--ink); background: rgba(255,255,255,.75); }

        /* ===== Hero ===== */
        .hero { padding: 170px 0 0; text-align: center; }
        .kick { display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: var(--mute); }
        .kick i { width: 22px; height: 1.5px; background: var(--accent); }
        .h1 { margin: 22px auto 0; max-width: 860px; font-size: clamp(40px, 7.4vw, 78px); font-weight: 700; line-height: 1.04; letter-spacing: -.045em; }
        .h1 em { font-style: normal; color: var(--accent); }
        .lead { margin: 26px auto 0; max-width: 560px; font-size: clamp(17px, 2.3vw, 19.5px); color: var(--mute); line-height: 1.58; letter-spacing: -.01em; }
        .acts { display: flex; justify-content: center; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 36px; }
        .fine { margin-top: 18px; font-size: 13px; color: var(--faint); }

        /* masuk bertahap saat halaman dibuka */
        .up { animation: rise .9s var(--ease) both; animation-delay: var(--d, 0s); }
        @keyframes rise { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }

        /* ===== Jendela aplikasi ===== */
        .win { margin: 68px auto 0; max-width: 960px; padding: 14px; border-radius: 34px; text-align: left; }
        .win-bar { display: flex; align-items: center; gap: 7px; padding: 4px 10px 14px; }
        .win-bar i { width: 11px; height: 11px; border-radius: 50%; background: rgba(12,21,48,.14); }
        .win-bar span { margin-left: 10px; font-size: 12px; font-weight: 600; color: var(--faint); }
        .win-body { display: grid; grid-template-columns: 1.15fr 1fr; gap: 14px; }
        .w-hero { position: relative; overflow: hidden; padding: 24px; border-radius: 24px; color: #fff; background: var(--grad);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 22px 40px -22px rgba(20,43,120,.8); }
        .w-hero::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(420px 220px at 12% -10%, rgba(255,255,255,.32), transparent 70%); }
        .w-hero > * { position: relative; z-index: 1; }
        .w-hero small { display: block; font-size: 12.5px; font-weight: 600; opacity: .8; }
        .w-hero b { display: block; margin-top: 8px; font-size: clamp(26px, 4vw, 36px); font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
        .spark { display: flex; align-items: flex-end; gap: 6px; height: 74px; margin-top: 26px; }
        .spark span { flex: 1; height: var(--h); border-radius: 6px 6px 3px 3px; background: rgba(255,255,255,.4); transform-origin: bottom; }
        .spark span:nth-last-child(-n+3) { background: rgba(255,255,255,.88); }
        .w-list { padding: 8px 6px; border-radius: 24px; background: rgba(255,255,255,.6); box-shadow: inset 0 0 0 1px rgba(255,255,255,.7); }
        .w-head { padding: 12px 16px 6px; font-size: 12px; font-weight: 600; color: var(--faint); }
        .w-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 16px; border-top: 1px solid var(--hair); font-size: 13.5px; }
        .w-row:first-of-type { border-top: 0; }
        .w-row div { min-width: 0; }
        .w-row strong { display: block; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .w-row small { display: flex; align-items: center; gap: 6px; margin-top: 3px; font-size: 11.5px; color: var(--mute); }
        .w-row small::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--dot, var(--b2)); }
        .w-row em { font-style: normal; font-weight: 600; white-space: nowrap; }
        .cap { margin-top: 16px; font-size: 12.5px; color: var(--faint); }

        /* ===== Section ===== */
        .sec { padding: 132px 0 0; }
        .kicker { font-size: 14px; font-weight: 600; color: var(--accent); }
        .h2 { margin-top: 12px; max-width: 700px; font-size: clamp(31px, 5vw, 52px); font-weight: 700; line-height: 1.07; letter-spacing: -.04em; }
        .sub { margin-top: 16px; max-width: 540px; font-size: 17px; color: var(--mute); line-height: 1.58; }
        .center { text-align: center; }
        .center .h2, .center .sub { margin-left: auto; margin-right: auto; }

        /* ===== Bento fitur ===== */
        .bento { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-top: 56px; }
        .tile { padding: 30px; display: flex; flex-direction: column; min-height: 270px; transition: translate .4s var(--ease), box-shadow .4s var(--ease); }
        .tile:hover { translate: 0 -4px; box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 34px 60px -28px rgba(18,36,92,.4), 0 2px 8px rgba(18,36,92,.06); }
        .tile h3 { font-size: 21px; font-weight: 700; letter-spacing: -.025em; }
        .tile p { margin-top: 8px; max-width: 440px; font-size: 15px; color: var(--mute); line-height: 1.58; }
        .tile .vis { margin-top: auto; padding-top: 26px; }
        .t-a { grid-column: span 4; } .t-b { grid-column: span 2; } .t-c { grid-column: span 2; }
        .t-d { grid-column: span 4; } .t-e { grid-column: span 3; } .t-f { grid-column: span 3; }

        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { display: inline-flex; align-items: center; padding: 8px 15px; border-radius: 999px; font-size: 13px; font-weight: 600; color: var(--soft);
            background: rgba(255,255,255,.72); box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 0 0 1px rgba(12,21,48,.06); }
        .chip.on { color: #fff; background: var(--grad-btn); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 10px 20px -10px rgba(30,70,200,.6); }
        .steps { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .steps + .steps { margin-top: 12px; }
        .steps b { width: 22px; height: 2px; border-radius: 2px; background: var(--b2); opacity: .55; }
        .bal { position: relative; height: 34px; border-radius: 12px; background: rgba(255,255,255,.6); overflow: hidden; box-shadow: inset 0 0 0 1px rgba(12,21,48,.05); }
        .bal + .bal { margin-top: 10px; }
        .bal span { position: absolute; inset: 0 auto 0 0; width: var(--w); background: var(--grad); opacity: .88; border-radius: 12px; transform-origin: left; }
        .bal em { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-style: normal; font-size: 12.5px; font-weight: 700; color: #fff; }
        .stock .bal em { color: var(--ink); }
        .stock .bal:nth-child(1) span { --w: 82%; opacity: .9; }
        .stock .bal:nth-child(2) span { --w: 46%; opacity: .5; }

        /* ===== Harga ===== */
        .plans { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 56px; align-items: stretch; }
        .plan { display: flex; flex-direction: column; padding: 34px 30px 30px; border-radius: 32px; transition: translate .4s var(--ease), box-shadow .4s var(--ease); }
        .plan:hover { translate: 0 -4px; }
        .plan.rec { translate: 0 -14px; background: linear-gradient(180deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 0 0 1.5px rgba(62,115,245,.4), 0 40px 80px -30px rgba(30,70,200,.5); }
        .plan.rec:hover { translate: 0 -18px; }
        .rec-tag { margin-bottom: 8px; font-size: 12.5px; font-weight: 700; color: var(--accent); }
        .pn { font-size: 19px; font-weight: 700; letter-spacing: -.02em; }
        .price { margin-top: 14px; font-size: 44px; font-weight: 700; letter-spacing: -.045em; line-height: 1; font-variant-numeric: tabular-nums; }
        .price small { font-size: 14px; font-weight: 500; letter-spacing: 0; color: var(--mute); }
        .per { min-height: 22px; margin-top: 8px; font-size: 13px; color: var(--mute); }
        .lim { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--hair); display: grid; gap: 8px; font-size: 14.5px; font-weight: 600; }
        .inc { margin: 18px 0 28px; display: grid; gap: 11px; flex: 1; align-content: start; font-size: 14px; color: var(--soft); }
        .inc li { position: relative; padding-left: 18px; }
        .inc li::before { content: ""; position: absolute; left: 0; top: 8px; width: 6px; height: 6px; border-radius: 50%; background: var(--b2); }
        .plan .btn { width: 100%; }
        .note { margin-top: 28px; font-size: 13px; color: var(--faint); text-align: center; }

        /* ===== FAQ ===== */
        .faq { max-width: 780px; margin: 48px auto 0; padding: 8px 30px; border-radius: 30px; }
        .faq details { border-top: 1px solid var(--hair); }
        .faq details:first-child { border-top: 0; }
        .faq summary { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 22px 0; cursor: pointer; list-style: none; font-size: 16.5px; font-weight: 600; letter-spacing: -.015em; transition: color .2s; }
        .faq summary:hover { color: var(--accent); }
        .faq summary::-webkit-details-marker { display: none; }
        .faq summary::after { content: "+"; flex: none; width: 28px; height: 28px; display: grid; place-items: center; border-radius: 50%; font-size: 18px; font-weight: 500; color: var(--accent); background: rgba(255,255,255,.85); box-shadow: 0 0 0 1px rgba(12,21,48,.06); transition: transform .35s var(--ease); }
        .faq details[open] summary::after { transform: rotate(135deg); }
        .faq details p { padding: 0 44px 24px 0; font-size: 15px; color: var(--mute); line-height: 1.65; }
        .faq details[open] p { animation: fade .45s var(--ease) both; }
        @keyframes fade { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }

        /* ===== CTA akhir ===== */
        .final { position: relative; overflow: hidden; margin-top: 132px; padding: 84px 28px; border-radius: 40px; text-align: center; color: #fff; background: var(--grad);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 50px 100px -40px rgba(20,43,120,.85); }
        .final::before { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(640px 320px at 15% -20%, rgba(255,255,255,.34), transparent 70%); }
        .final > * { position: relative; }
        .final h2 { max-width: 640px; margin: 0 auto; font-size: clamp(30px, 5vw, 48px); font-weight: 700; line-height: 1.07; letter-spacing: -.04em; }
        .final p { max-width: 480px; margin: 16px auto 0; font-size: 17px; color: rgba(255,255,255,.82); line-height: 1.58; }
        .final .btn { margin-top: 32px; }
        .final small { display: block; margin-top: 16px; font-size: 13px; color: rgba(255,255,255,.7); }

        /* ===== Footer ===== */
        .foot-w { margin-top: 90px; padding: 30px 0 calc(34px + env(safe-area-inset-bottom, 0px)); border-top: 1px solid var(--hair); }
        .foot { display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; font-size: 13px; color: var(--mute); }
        .foot .l { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .foot img { height: 22px; width: auto; opacity: .85; }
        .foot nav { display: flex; gap: 24px; flex-wrap: wrap; }
        .foot a { text-decoration: none; transition: color .2s; }
        .foot a:hover { color: var(--accent); }

        /* ===== Reveal saat scroll (aktif hanya jika JS jalan) ===== */
        .js .rv { opacity: 0; }
        .js .rv.in { animation: rise .9s var(--ease) both; animation-delay: var(--d, 0s); }

        /* isi bar bertumbuh sekali saat terlihat */
        .js .spark span { transform: scaleY(.04); transition: transform 1s var(--ease); transition-delay: calc(var(--i) * 45ms + .35s); }
        .js .win.in .spark span { transform: none; }
        .js .bal span { transform: scaleX(0); transition: transform 1.1s var(--ease); transition-delay: .35s; }
        .js .tile.in .bal span { transform: none; }
        .js .tile.in .bal:nth-child(2) span { transition-delay: .5s; }

        /* ===== Responsif ===== */
        @media (max-width: 960px) {
            .t-a, .t-d { grid-column: span 6; } .t-b, .t-c { grid-column: span 3; } .t-e, .t-f { grid-column: span 3; }
            .plans { grid-template-columns: 1fr; max-width: 520px; margin-left: auto; margin-right: auto; }
            .plan.rec { translate: none; order: -1; }
            .plan.rec:hover { translate: 0 -4px; }
            .links { display: none; }
            .cta { margin-left: auto; }
            .win-body { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .hero { padding-top: 134px; }
            .sec { padding-top: 92px; }
            .bar { padding: 8px 8px 8px 18px; border-radius: 28px; }
            .brand { flex-direction: column; align-items: flex-start; gap: 3px; }
            .brand span { padding: 0; border: 0; font-size: 10px; }
            .login { display: none; }
            .bento { grid-template-columns: 1fr; gap: 14px; }
            .t-a, .t-b, .t-c, .t-d, .t-e, .t-f { grid-column: auto; }
            .tile { min-height: 0; padding: 26px; }
            .win { padding: 10px; border-radius: 28px; margin-top: 48px; }
            .w-hero { padding: 20px; }
            .faq { padding: 4px 22px; }
            .final { margin-top: 92px; padding: 60px 22px; border-radius: 32px; }
            .bg i:nth-child(1) { width: 380px; height: 380px; }
            .bg i:nth-child(2) { width: 340px; height: 340px; }
            .bg i:nth-child(3) { display: none; }
        }

        /* hormati pengguna yang mematikan animasi */
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .bg i, .up, .js .rv.in, .faq details[open] p { animation: none !important; }
            .js .rv { opacity: 1; }
            .js .spark span, .js .bal span { transform: none; transition: none; }
            * { transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

<div class="bg" aria-hidden="true"><i></i><i></i><i></i></div>
<a href="#konten" class="skip">Lewati ke konten</a>

{{-- NAVBAR --}}
<header class="nav">
    <div class="bar glass">
        <a href="{{ url('/') }}" class="brand" aria-label="LedgerNusa — beranda">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa" height="28" decoding="async" fetchpriority="high">
            <span>by Mangata Nusantara Teknologi</span>
        </a>
        <nav class="links" aria-label="Navigasi halaman">
            <a href="#fitur">Fitur</a>
            <a href="#harga">Harga</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="cta">
            <a href="{{ route('login') }}" class="login">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-grad btn-sm">Coba Gratis</a>
        </div>
    </div>
</header>

<main id="konten">

    {{-- HERO --}}
    <section class="hero">
        <div class="wrap">
            <span class="kick up" style="--d:.05s"><i></i>Aplikasi akuntansi online</span>
            <h1 class="h1 up" style="--d:.12s">Pembukuan bisnis yang rapi, <em>tanpa input ganda.</em></h1>
            <p class="lead up" style="--d:.2s">LedgerNusa mencatat transaksi, menjaga debit dan kredit tetap seimbang, lalu menyusun laporan keuangan, dari HP maupun komputer.</p>

            <div class="acts up" style="--d:.28s">
                <a href="{{ route('register') }}" class="btn btn-grad">Mulai Trial 7 Hari</a>
                <a href="#fitur" class="btn btn-glass">Lihat Fitur</a>
            </div>
            <p class="fine up" style="--d:.34s">Tanpa kartu kredit · Batal kapan saja</p>

            <div class="win glass rv" role="img" aria-label="Ilustrasi dashboard LedgerNusa: total kas dan bank, grafik, dan daftar jurnal terbaru">
                <div class="win-bar" aria-hidden="true"><i></i><i></i><i></i><span>LedgerNusa · Dashboard</span></div>
                <div class="win-body" aria-hidden="true">
                    <div class="w-hero">
                        <small>Total Kas &amp; Bank</small>
                        <b data-count="184250000">Rp 184.250.000</b>
                        <div class="spark">
                            @foreach([34,48,40,58,52,66,60,74,68,82,76,90,84,100] as $h)
                                <span style="--h:{{ $h }}%;--i:{{ $loop->index }}"></span>
                            @endforeach
                        </div>
                    </div>
                    <div class="w-list">
                        <div class="w-head">Jurnal terbaru</div>
                        <div class="w-row"><div><strong>Penjualan — Invoice</strong><small>Posted</small></div><em>Rp 12.500.000</em></div>
                        <div class="w-row"><div><strong>Pembelian bahan</strong><small>Posted</small></div><em>Rp 3.275.000</em></div>
                        <div class="w-row"><div><strong>Beban listrik</strong><small style="--dot:var(--faint)">Draft</small></div><em>Rp 890.000</em></div>
                    </div>
                </div>
            </div>
            <p class="cap">Ilustrasi tampilan aplikasi. Angka hanya contoh.</p>
        </div>
    </section>

    {{-- FITUR --}}
    <section class="sec" id="fitur" aria-labelledby="h-fitur">
        <div class="wrap">
            <div class="center rv">
                <span class="kicker">Fitur</span>
                <h2 class="h2" id="h-fitur">Semua kebutuhan pembukuan, satu tempat.</h2>
                <p class="sub">Dari pencatatan harian sampai laporan akhir bulan, tanpa berpindah-pindah aplikasi.</p>
            </div>

            <div class="bento">
                <article class="tile glass glow rv t-a">
                    <h3>Jurnal otomatis</h3>
                    <p>Transaksi kas, penjualan, dan pembelian langsung masuk ke Buku Besar tanpa input ulang.</p>
                    <div class="vis">
                        <div class="chips"><span class="chip">Kas masuk</span><span class="chip">Invoice penjualan</span><span class="chip">Pembelian</span><span class="chip on">Buku Besar</span></div>
                    </div>
                </article>

                <article class="tile glass glow rv t-b">
                    <h3>Selalu balance</h3>
                    <p>Debit dan kredit dijaga seimbang di setiap jurnal.</p>
                    <div class="vis">
                        <div class="bal"><span style="--w:100%"></span><em>Debit</em></div>
                        <div class="bal"><span style="--w:100%"></span><em>Kredit</em></div>
                    </div>
                </article>

                <article class="tile glass glow rv t-c">
                    <h3>Kas &amp; Bank</h3>
                    <p>Catat kas masuk dan keluar, transfer antar rekening, lalu cocokkan dengan mutasi bank.</p>
                    <div class="vis"><div class="chips"><span class="chip on">Kas</span><span class="chip">Bank</span><span class="chip">Rekonsiliasi</span></div></div>
                </article>

                <article class="tile glass glow rv t-d">
                    <h3>Penjualan &amp; pembelian</h3>
                    <p>Dari pesanan sampai tagihan, setiap tahap tercatat dan saling terhubung.</p>
                    <div class="vis">
                        <div class="steps"><span class="chip on">Sales Order</span><b></b><span class="chip">Delivery</span><b></b><span class="chip">Invoice</span></div>
                        <div class="steps"><span class="chip on">Purchase Order</span><b></b><span class="chip">Penerimaan</span><b></b><span class="chip">Pembelian</span></div>
                    </div>
                </article>

                <article class="tile glass glow rv t-e">
                    <h3>Persediaan</h3>
                    <p>Pantau stok tiap gudang dan mutasi barang dengan rapi.</p>
                    <div class="vis stock">
                        <div class="bal"><span></span><em>Gudang utama</em></div>
                        <div class="bal"><span></span><em>Gudang cabang</em></div>
                    </div>
                </article>

                <article class="tile glass glow rv t-f">
                    <h3>Multi-company &amp; pajak</h3>
                    <p>Kelola beberapa perusahaan dalam satu akun, lengkap dengan laporan PPN dan PPh 23.</p>
                    <div class="vis"><div class="chips"><span class="chip on">PPN</span><span class="chip">PPh 23</span><span class="chip">Rekap tahunan</span></div></div>
                </article>
            </div>
        </div>
    </section>

    {{-- HARGA --}}
    <section class="sec" id="harga" aria-labelledby="h-harga">
        <div class="wrap">
            <div class="center rv">
                <span class="kicker">Harga</span>
                <h2 class="h2" id="h-harga">Pilih paket sesuai ukuran bisnis Anda.</h2>
                <p class="sub">Semua paket diawali trial 7 hari. Pembayaran lewat transfer bank.</p>
            </div>

            <div class="plans">
                @foreach($plans as $plan)
                    @php
                        $name = $plan[0];
                        $price = $plan[1];
                        $days = $plan[2];
                        $users = $plan[3];
                        $journals = $plan[4];
                        $companies = $plan[5];
                        $popular = $plan[6];
                        $includes = $plan[7];
                    @endphp
                    <article class="plan glass glow rv {{ $popular ? 'rec' : '' }}">
                        @if($popular)
                            <span class="rec-tag">Disarankan</span>
                        @endif
                        <h3 class="pn">{{ $name }}</h3>
                        <div class="price">Rp {{ number_format($price, 0, ',', '.') }}<small> / {{ $days }} hari</small></div>
                        <p class="per">
                            @if($days > 30)
                                Setara Rp {{ number_format($price / $days * 30, 0, ',', '.') }} per 30 hari
                            @endif
                        </p>
                        <ul class="lim">
                            <li>{{ $users }}</li>
                            <li>{{ $journals }}</li>
                            <li>{{ $companies }}</li>
                        </ul>
                        <ul class="inc">
                            @foreach($includes as $inc)
                                <li>{{ $inc }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn {{ $popular ? 'btn-grad' : 'btn-glass' }}">Coba Gratis 7 Hari</a>
                    </article>
                @endforeach
            </div>
            <p class="note">Harga belum termasuk pajak apabila berlaku.</p>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="sec" id="faq" aria-labelledby="h-faq">
        <div class="wrap">
            <div class="center rv">
                <span class="kicker">FAQ</span>
                <h2 class="h2" id="h-faq">Pertanyaan yang sering diajukan.</h2>
            </div>
            <div class="faq glass rv">
                @foreach($faq as $f)
                    <details>
                        <summary>{{ $f[0] }}</summary>
                        <p>{{ $f[1] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="wrap" aria-labelledby="h-cta">
        <div class="final rv">
            <h2 id="h-cta">Siap merapikan pembukuan bisnis Anda?</h2>
            <p>Coba semua fitur selama 7 hari. Tidak perlu kartu kredit, dan Anda bisa berhenti kapan saja.</p>
            <a href="{{ route('register') }}" class="btn btn-white">Daftar Sekarang — Gratis 7 Hari</a>
            <small>Tanpa kartu kredit · Batal kapan saja</small>
        </div>
    </section>

</main>

<footer class="foot-w">
    <div class="wrap foot">
        <div class="l">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa" height="22" loading="lazy" decoding="async">
            <span>&copy; {{ date('Y') }} Mangata Nusantara Teknologi.</span>
        </div>
        <nav aria-label="Tautan footer">
            <a href="#">Syarat &amp; Ketentuan</a>
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Bantuan</a>
        </nav>
    </div>
</footer>

<script>
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // 1. Navbar: kaca lebih padat setelah di-scroll
    var bar = document.querySelector('.bar');
    function onScroll() { bar.classList.toggle('scrolled', window.scrollY > 12); }
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    // 2. Angka kas naik sekali saat jendela aplikasi terlihat
    function countUp() {
        var el = document.querySelector('[data-count]');
        if (!el || reduce) return;
        var end = +el.dataset.count, fmt = new Intl.NumberFormat('id-ID'), dur = 1500;
        setTimeout(function () {
            var t0 = performance.now();
            (function f(t) {
                var p = Math.min((t - t0) / dur, 1);
                el.textContent = 'Rp ' + fmt.format(Math.round(end * (1 - Math.pow(1 - p, 3))));
                if (p < 1) requestAnimationFrame(f);
            })(t0);
        }, 350);
    }

    // 3. Reveal saat scroll, bertahap per kelompok
    var els = [].slice.call(document.querySelectorAll('.rv'));
    if (!('IntersectionObserver' in window) || reduce) {
        els.forEach(function (e) { e.classList.add('in'); });
        return;
    }
    els.forEach(function (el) {
        var sibs = [].filter.call(el.parentElement.children, function (c) { return c.classList.contains('rv'); });
        el.style.setProperty('--d', Math.min(sibs.indexOf(el), 5) * 70 + 'ms');
    });
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (!e.isIntersecting) return;
            e.target.classList.add('in');
            if (e.target.classList.contains('win')) countUp();
            io.unobserve(e.target);
        });
    }, { threshold: .12, rootMargin: '0px 0px -6% 0px' });
    els.forEach(function (e) { io.observe(e); });

    // 4. Kilau kaca mengikuti kursor (hanya perangkat dengan mouse)
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        [].forEach.call(document.querySelectorAll('.glow'), function (el) {
            el.addEventListener('pointermove', function (e) {
                var r = el.getBoundingClientRect();
                el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                el.style.setProperty('--my', (e.clientY - r.top) + 'px');
            });
        });
    }
})();
</script>

</body>
</html>