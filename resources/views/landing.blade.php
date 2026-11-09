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
    <meta name="theme-color" content="#EEF3FB">

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script type="application/ld+json">{!! json_encode($ldApp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($ldOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <style>
        /* Hanya dua warna: biru + cyan. Semua gradasi memakai keduanya. */
        :root {
            --b1: #2563EB; --b2: #06B6D4;
            --grad: linear-gradient(135deg, var(--b1), var(--b2));
            --ink: #0B1220; --soft: #3B4558; --mute: #5F6B80; --faint: #8893A6;
            --hair: rgba(11,18,32,.08);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; scroll-padding-top: 96px; }
        body {
            margin: 0; min-height: 100vh; color: var(--ink); background: #F3F6FC;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Segoe UI', system-ui, sans-serif;
            line-height: 1.5; letter-spacing: -.005em; -webkit-font-smoothing: antialiased; overflow-x: hidden;
        }
        a { color: inherit; -webkit-tap-highlight-color: transparent; }
        h1, h2, h3, p, ul { margin: 0; }
        ul { padding: 0; list-style: none; }
        :focus-visible { outline: 2px solid var(--b1); outline-offset: 3px; border-radius: 10px; }
        .skip { position: absolute; left: 12px; top: -60px; z-index: 200; padding: 10px 16px; border-radius: 12px; background: var(--ink); color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: top .15s; }
        .skip:focus { top: 12px; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        .grad { background: var(--grad); -webkit-background-clip: text; background-clip: text; color: transparent; -webkit-text-fill-color: transparent; }

        /* ===== Latar aurora: bahan yang "dibiaskan" kaca ===== */
        .aurora { position: fixed; inset: 0; z-index: -1; overflow: hidden; background: linear-gradient(180deg, #F6F9FF, #EAF0FA); pointer-events: none; }
        .aurora i { position: absolute; border-radius: 50%; filter: blur(80px); will-change: transform; }
        .aurora i:nth-child(1) { width: 620px; height: 620px; left: -160px; top: -200px; background: var(--b1); opacity: .42; animation: drift1 26s ease-in-out infinite alternate; }
        .aurora i:nth-child(2) { width: 560px; height: 560px; right: -180px; top: 80px; background: var(--b2); opacity: .45; animation: drift2 30s ease-in-out infinite alternate; }
        .aurora i:nth-child(3) { width: 520px; height: 520px; left: 28%; bottom: -300px; background: var(--b1); opacity: .26; animation: drift1 34s ease-in-out infinite alternate-reverse; }
        @keyframes drift1 { to { transform: translate(90px, 70px) scale(1.08); } }
        @keyframes drift2 { to { transform: translate(-80px, 60px) scale(1.1); } }

        /* ===== Kaca (liquid glass): blur, saturasi, tepi spekular ===== */
        .glass {
            position: relative; border-radius: 28px;
            background: linear-gradient(180deg, rgba(255,255,255,.64), rgba(255,255,255,.34));
            -webkit-backdrop-filter: blur(28px) saturate(190%); backdrop-filter: blur(28px) saturate(190%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.85),
                inset 0 -1px 0 rgba(255,255,255,.18),
                0 28px 60px -28px rgba(20,50,120,.34),
                0 2px 8px rgba(20,50,120,.05);
        }
        .glass::before {
            content: ""; position: absolute; inset: 0; border-radius: inherit; padding: 1px; pointer-events: none;
            background: linear-gradient(140deg, rgba(255,255,255,.95), rgba(255,255,255,.12) 34%, rgba(255,255,255,.06) 66%, rgba(255,255,255,.75));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor;
            mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) { .glass { background: rgba(250,252,255,.95); } }

        /* ===== Tombol ===== */
        .btn { position: relative; display: inline-flex; align-items: center; justify-content: center; height: 48px; padding: 0 26px; border: 0; border-radius: 999px; cursor: pointer;
            font-family: inherit; font-size: 15px; font-weight: 600; text-decoration: none; white-space: nowrap; transition: transform .18s, box-shadow .18s, background .18s; }
        .btn-grad { color: #fff; background: var(--grad); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 14px 28px -12px rgba(37,99,235,.65); }
        .btn-grad:hover { transform: translateY(-2px); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 18px 32px -12px rgba(37,99,235,.75); }
        .btn-glass { color: var(--ink); background: rgba(255,255,255,.55); -webkit-backdrop-filter: blur(18px) saturate(180%); backdrop-filter: blur(18px) saturate(180%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 1px rgba(255,255,255,.6), 0 10px 24px -14px rgba(20,50,120,.35); }
        .btn-glass:hover { background: rgba(255,255,255,.8); transform: translateY(-2px); }
        .btn-white { color: var(--b1); background: #fff; box-shadow: 0 16px 30px -14px rgba(10,30,90,.55); }
        .btn-white:hover { transform: translateY(-2px); }
        .btn:active { transform: scale(.98); }
        .btn-sm { height: 40px; padding: 0 20px; font-size: 14px; }

        /* ===== Navbar ===== */
        .nav { position: fixed; z-index: 60; top: 0; left: 0; right: 0; padding: calc(12px + env(safe-area-inset-top, 0px)) 14px 0; }
        .bar { max-width: 1040px; margin: 0 auto; min-height: 60px; padding: 8px 10px 8px 22px; border-radius: 999px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; flex: none; }
        .brand img { height: 28px; width: auto; display: block; }
        .brand span { padding-left: 12px; border-left: 1px solid var(--hair); font-size: 11.5px; font-weight: 500; line-height: 1.25; color: var(--faint); white-space: nowrap; }
        .links { display: flex; align-items: center; gap: 2px; margin: 0 auto; }
        .links a { padding: 8px 15px; border-radius: 999px; font-size: 14px; font-weight: 500; color: var(--mute); text-decoration: none; transition: .16s; }
        .links a:hover { color: var(--ink); background: rgba(255,255,255,.7); }
        .cta { display: flex; align-items: center; gap: 4px; flex: none; }
        .login { padding: 8px 16px; border-radius: 999px; font-size: 14px; font-weight: 600; color: var(--soft); text-decoration: none; transition: .16s; }
        .login:hover { color: var(--ink); background: rgba(255,255,255,.7); }

        /* ===== Hero ===== */
        .hero { padding: 168px 0 0; text-align: center; }
        .eyebrow { display: inline-block; padding: 7px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; color: var(--soft);
            background: rgba(255,255,255,.55); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 1px rgba(255,255,255,.55); -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); }
        .h1 { margin: 26px auto 0; max-width: 880px; font-size: clamp(42px, 8vw, 84px); font-weight: 700; line-height: 1.03; letter-spacing: -.045em; }
        .lead { margin: 26px auto 0; max-width: 580px; font-size: clamp(17px, 2.3vw, 20px); color: var(--mute); line-height: 1.55; letter-spacing: -.01em; }
        .acts { display: flex; justify-content: center; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 36px; }
        .fine { margin-top: 18px; font-size: 13px; color: var(--faint); }

        /* ===== Jendela aplikasi ===== */
        .win { margin: 68px auto 0; max-width: 960px; padding: 14px; border-radius: 34px; text-align: left; }
        .win-bar { display: flex; align-items: center; gap: 7px; padding: 4px 10px 14px; }
        .win-bar i { width: 11px; height: 11px; border-radius: 50%; background: rgba(11,18,32,.14); }
        .win-bar span { margin-left: 10px; font-size: 12px; font-weight: 600; color: var(--faint); }
        .win-body { display: grid; grid-template-columns: 1.15fr 1fr; gap: 14px; }
        .w-hero { position: relative; overflow: hidden; padding: 24px; border-radius: 24px; color: #fff; background: var(--grad);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 22px 40px -22px rgba(37,99,235,.7); }
        .w-hero::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(420px 220px at 12% -10%, rgba(255,255,255,.4), transparent 70%); }
        .w-hero > * { position: relative; z-index: 1; }
        .w-hero small { display: block; font-size: 12.5px; font-weight: 600; opacity: .85; }
        .w-hero b { display: block; margin-top: 8px; font-size: clamp(26px, 4vw, 36px); font-weight: 700; letter-spacing: -.03em; }
        .spark { display: flex; align-items: flex-end; gap: 6px; height: 74px; margin-top: 26px; }
        .spark span { flex: 1; height: var(--h); border-radius: 6px 6px 3px 3px; background: rgba(255,255,255,.42); }
        .spark span:nth-last-child(-n+3) { background: rgba(255,255,255,.9); }
        .w-list { padding: 8px 6px; border-radius: 24px; background: rgba(255,255,255,.55); box-shadow: inset 0 0 0 1px rgba(255,255,255,.7); }
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
        .kicker { font-size: 14px; font-weight: 600; color: var(--b1); }
        .h2 { margin-top: 12px; max-width: 700px; font-size: clamp(32px, 5.2vw, 54px); font-weight: 700; line-height: 1.06; letter-spacing: -.04em; }
        .sub { margin-top: 16px; max-width: 540px; font-size: 17px; color: var(--mute); line-height: 1.55; }
        .center { text-align: center; }
        .center .h2, .center .sub { margin-left: auto; margin-right: auto; }

        /* ===== Bento fitur ===== */
        .bento { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-top: 56px; }
        .tile { padding: 30px; display: flex; flex-direction: column; min-height: 270px; transition: transform .25s; }
        .tile:hover { transform: translateY(-4px); }
        .tile h3 { font-size: 22px; font-weight: 700; letter-spacing: -.025em; }
        .tile p { margin-top: 8px; max-width: 440px; font-size: 15px; color: var(--mute); line-height: 1.55; }
        .tile .vis { margin-top: auto; padding-top: 26px; }
        .t-a { grid-column: span 4; } .t-b { grid-column: span 2; } .t-c { grid-column: span 2; }
        .t-d { grid-column: span 4; } .t-e { grid-column: span 3; } .t-f { grid-column: span 3; }

        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { display: inline-flex; align-items: center; padding: 8px 15px; border-radius: 999px; font-size: 13px; font-weight: 600; color: var(--soft);
            background: rgba(255,255,255,.7); box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 0 0 1px rgba(11,18,32,.06); }
        .chip.on { color: #fff; background: var(--grad); box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 10px 20px -10px rgba(37,99,235,.6); }
        .steps { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .steps + .steps { margin-top: 12px; }
        .steps b { width: 22px; height: 2px; border-radius: 2px; background: var(--grad); opacity: .7; }
        .bal { position: relative; height: 34px; border-radius: 12px; background: rgba(255,255,255,.55); overflow: hidden; box-shadow: inset 0 0 0 1px rgba(11,18,32,.05); }
        .bal + .bal { margin-top: 10px; }
        .bal span { position: absolute; inset: 0 auto 0 0; width: var(--w); background: var(--grad); opacity: .85; border-radius: 12px; }
        .bal em { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-style: normal; font-size: 12.5px; font-weight: 700; color: #fff; }
        .stock .bal span { opacity: .9; }
        .stock .bal em { color: var(--ink); }
        .stock .bal:nth-child(1) span { --w: 82%; } .stock .bal:nth-child(2) span { --w: 46%; opacity: .55; }
        .stock .bal em { mix-blend-mode: normal; }

        /* ===== Harga ===== */
        .plans { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 56px; align-items: stretch; }
        .plan { display: flex; flex-direction: column; padding: 34px 30px 30px; border-radius: 32px; transition: transform .25s; }
        .plan.rec { transform: translateY(-14px); background: linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.5));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 0 0 1.5px rgba(37,99,235,.35), 0 40px 80px -30px rgba(37,99,235,.5); }
        .plan .rec-tag { margin-bottom: 8px; font-size: 12.5px; font-weight: 700; }
        .pn { font-size: 19px; font-weight: 700; letter-spacing: -.02em; }
        .price { margin-top: 14px; font-size: 44px; font-weight: 700; letter-spacing: -.045em; line-height: 1; }
        .price small { font-size: 14px; font-weight: 500; letter-spacing: 0; color: var(--mute); }
        .per { min-height: 22px; margin-top: 8px; font-size: 13px; color: var(--mute); }
        .lim { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--hair); display: grid; gap: 8px; font-size: 14.5px; font-weight: 600; }
        .inc { margin: 18px 0 28px; display: grid; gap: 11px; flex: 1; align-content: start; font-size: 14px; color: var(--soft); }
        .inc li { position: relative; padding-left: 18px; }
        .inc li::before { content: ""; position: absolute; left: 0; top: 8px; width: 6px; height: 6px; border-radius: 50%; background: var(--grad); }
        .plan .btn { width: 100%; }
        .note { margin-top: 28px; font-size: 13px; color: var(--faint); text-align: center; }

        /* ===== FAQ ===== */
        .faq { max-width: 780px; margin: 48px auto 0; padding: 8px 30px; border-radius: 30px; }
        .faq details { border-top: 1px solid var(--hair); }
        .faq details:first-child { border-top: 0; }
        .faq summary { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 22px 0; cursor: pointer; list-style: none; font-size: 16.5px; font-weight: 600; letter-spacing: -.015em; }
        .faq summary::-webkit-details-marker { display: none; }
        .faq summary::after { content: "+"; flex: none; width: 28px; height: 28px; display: grid; place-items: center; border-radius: 50%; font-size: 18px; font-weight: 500; color: var(--b1); background: rgba(255,255,255,.8); box-shadow: 0 0 0 1px rgba(11,18,32,.06); transition: transform .25s; }
        .faq details[open] summary::after { transform: rotate(45deg); }
        .faq details p { padding: 0 44px 24px 0; font-size: 15px; color: var(--mute); line-height: 1.65; }

        /* ===== CTA akhir ===== */
        .final { position: relative; overflow: hidden; margin-top: 132px; padding: 84px 28px; border-radius: 40px; text-align: center; color: #fff; background: var(--grad);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.5), 0 50px 100px -40px rgba(37,99,235,.75); }
        .final::before { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(640px 320px at 15% -20%, rgba(255,255,255,.45), transparent 70%), radial-gradient(520px 300px at 100% 120%, rgba(255,255,255,.25), transparent 70%); }
        .final > * { position: relative; }
        .final h2 { max-width: 640px; margin: 0 auto; font-size: clamp(30px, 5vw, 50px); font-weight: 700; line-height: 1.06; letter-spacing: -.04em; }
        .final p { max-width: 480px; margin: 16px auto 0; font-size: 17px; color: rgba(255,255,255,.85); line-height: 1.55; }
        .final .btn { margin-top: 32px; }
        .final small { display: block; margin-top: 16px; font-size: 13px; color: rgba(255,255,255,.75); }

        /* ===== Footer ===== */
        .foot-w { margin-top: 90px; padding: 30px 0 calc(34px + env(safe-area-inset-bottom, 0px)); border-top: 1px solid var(--hair); }
        .foot { display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; font-size: 13px; color: var(--mute); }
        .foot .l { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .foot img { height: 22px; width: auto; opacity: .85; }
        .foot nav { display: flex; gap: 24px; flex-wrap: wrap; }
        .foot a { text-decoration: none; transition: color .15s; }
        .foot a:hover { color: var(--b1); }

        /* ===== Responsif ===== */
        @media (max-width: 960px) {
            .t-a, .t-d { grid-column: span 6; } .t-b, .t-c { grid-column: span 3; } .t-e, .t-f { grid-column: span 3; }
            .plans { grid-template-columns: 1fr; max-width: 520px; margin-left: auto; margin-right: auto; }
            .plan.rec { transform: none; order: -1; }
            .links { display: none; }
            .cta { margin-left: auto; }
            .win-body { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .hero { padding-top: 132px; }
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
            .aurora i:nth-child(1) { width: 380px; height: 380px; }
            .aurora i:nth-child(2) { width: 340px; height: 340px; }
            .aurora i:nth-child(3) { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } .aurora i { animation: none; } * { transition-duration: .01ms !important; } }
    </style>
</head>
<body>

<div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
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
            <span class="eyebrow">Aplikasi akuntansi online</span>
            <h1 class="h1">Pembukuan bisnis, <span class="grad">tanpa ribet.</span></h1>
            <p class="lead">LedgerNusa mencatat, menyeimbangkan, dan melaporkan keuangan bisnis Anda secara otomatis, dari HP maupun komputer.</p>

            <div class="acts">
                <a href="{{ route('register') }}" class="btn btn-grad">Mulai Trial 7 Hari</a>
                <a href="#fitur" class="btn btn-glass">Lihat Fitur</a>
            </div>
            <p class="fine">Tanpa kartu kredit · Batal kapan saja</p>

            <div class="win glass" role="img" aria-label="Ilustrasi dashboard LedgerNusa: total kas dan bank, grafik, dan daftar jurnal terbaru">
                <div class="win-bar" aria-hidden="true"><i></i><i></i><i></i><span>LedgerNusa · Dashboard</span></div>
                <div class="win-body" aria-hidden="true">
                    <div class="w-hero">
                        <small>Total Kas &amp; Bank</small>
                        <b>Rp 184.250.000</b>
                        <div class="spark">
                            @foreach([34,48,40,58,52,66,60,74,68,82,76,90,84,100] as $h)<span style="--h:{{ $h }}%"></span>@endforeach
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
            <div class="center">
                <span class="kicker">Fitur</span>
                <h2 class="h2" id="h-fitur">Semua kebutuhan pembukuan, satu tempat.</h2>
                <p class="sub">Dari pencatatan harian sampai laporan akhir bulan, tanpa berpindah-pindah aplikasi.</p>
            </div>

            <div class="bento">
                <article class="tile glass t-a">
                    <h3>Jurnal otomatis</h3>
                    <p>Transaksi kas, penjualan, dan pembelian langsung masuk ke Buku Besar tanpa input ulang.</p>
                    <div class="vis">
                        <div class="chips"><span class="chip">Kas masuk</span><span class="chip">Invoice penjualan</span><span class="chip">Pembelian</span><span class="chip on">Buku Besar</span></div>
                    </div>
                </article>

                <article class="tile glass t-b">
                    <h3>Selalu balance</h3>
                    <p>Debit dan kredit dijaga seimbang di setiap jurnal.</p>
                    <div class="vis">
                        <div class="bal"><span style="--w:100%"></span><em>Debit</em></div>
                        <div class="bal"><span style="--w:100%"></span><em>Kredit</em></div>
                    </div>
                </article>

                <article class="tile glass t-c">
                    <h3>Kas &amp; Bank</h3>
                    <p>Catat kas masuk dan keluar, transfer antar rekening, lalu cocokkan dengan mutasi bank.</p>
                    <div class="vis"><div class="chips"><span class="chip on">Kas</span><span class="chip">Bank</span><span class="chip">Rekonsiliasi</span></div></div>
                </article>

                <article class="tile glass t-d">
                    <h3>Penjualan &amp; pembelian</h3>
                    <p>Dari pesanan sampai tagihan, setiap tahap tercatat dan saling terhubung.</p>
                    <div class="vis">
                        <div class="steps"><span class="chip on">Sales Order</span><b></b><span class="chip">Delivery</span><b></b><span class="chip">Invoice</span></div>
                        <div class="steps"><span class="chip on">Purchase Order</span><b></b><span class="chip">Penerimaan</span><b></b><span class="chip">Pembelian</span></div>
                    </div>
                </article>

                <article class="tile glass t-e">
                    <h3>Persediaan</h3>
                    <p>Pantau stok tiap gudang dan mutasi barang dengan rapi.</p>
                    <div class="vis stock">
                        <div class="bal"><span></span><em>Gudang utama</em></div>
                        <div class="bal"><span></span><em>Gudang cabang</em></div>
                    </div>
                </article>

                <article class="tile glass t-f">
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
            <div class="center">
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
                    <article class="plan glass {{ $popular ? 'rec' : '' }}">
                        @if($popular)
                            <span class="rec-tag grad">Disarankan</span>
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
            <div class="center">
                <span class="kicker">FAQ</span>
                <h2 class="h2" id="h-faq">Pertanyaan yang sering diajukan.</h2>
            </div>
            <div class="faq glass">
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
        <div class="final">
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

</body>
</html>