<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email — LedgerNusa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .ln-display { font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif; }

        .ln-shell {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
            background: #F7F7F5;
        }
        @media (min-width: 1024px) {
            .ln-shell { grid-template-columns: 0.92fr 1fr; }
        }

        /* ---- Left panel ---- */
        .ln-panel {
            position: relative;
            display: none;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 52px;
            background: #101A30;
            color: #EDEFF4;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .ln-panel { display: flex; } }

        .ln-panel::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 195, 236, 0.16) 0%, transparent 70%);
            pointer-events: none;
        }

        .ln-mark {
            display: inline-flex;
            align-items: center;
            position: relative;
            z-index: 1;
            background: #F7F7F5;
            padding: 10px 16px;
            border-radius: 10px;
        }
        .ln-mark img { height: 26px; width: auto; display: block; }

        .ln-tagline {
            position: relative;
            z-index: 1;
            max-width: 340px;
            font-size: 26px;
            line-height: 1.35;
            letter-spacing: -0.01em;
            color: #F3F5F9;
            margin-top: 40px;
        }
        .ln-tagline b { color: #4FC3EC; font-weight: 600; }

        .ln-features {
            position: relative;
            z-index: 1;
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .ln-feature { display: flex; align-items: flex-start; gap: 14px; }
        .ln-feature .ico {
            flex: none;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(79, 195, 236, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ln-feature .ico svg { width: 17px; height: 17px; stroke: #4FC3EC; }
        .ln-feature .txt b { display: block; font-size: 14px; color: #EDEFF4; font-weight: 500; }
        .ln-feature .txt span { font-size: 12.5px; color: #8B96A9; }

        .ln-foot { position: relative; z-index: 1; font-size: 12px; color: #6E7A8E; }

        /* ---- Right panel ---- */
        .ln-form-col {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }
        .ln-form-inner { width: 100%; max-width: 380px; }

        .ln-mobile-mark { display: flex; justify-content: center; margin-bottom: 28px; }
        .ln-mobile-mark img { height: 30px; }
        @media (min-width: 1024px) { .ln-mobile-mark { display: none; } }

        .ln-eyebrow { font-size: 13px; color: #8B96A9; margin-bottom: 6px; }
        .ln-h1 { font-size: 26px; letter-spacing: -0.01em; color: #14213A; }
        .ln-intro { margin-top: 14px; font-size: 13.5px; line-height: 1.65; color: #5B6577; }

        .ln-status { font-size: 13.5px; color: #1E5B3D; background: #F2FAF6; border-left: 3px solid #2FA36B; padding: 8px 12px; border-radius: 6px; margin-top: 18px; }

        .ln-actions { display: flex; align-items: center; justify-content: space-between; margin-top: 28px; }
        .ln-submit {
            padding: 12px 22px;
            background: #14213A;
            color: #F7F7F5;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.01em;
            border-radius: 8px;
            border: none;
            transition: background 0.15s ease;
        }
        .ln-submit:hover { background: #1C8FC4; }
        .ln-submit:focus-visible { outline: 2px solid #1C8FC4; outline-offset: 2px; }

        .ln-logout { background: none; border: none; font-size: 13px; color: #8B96A9; }
        .ln-logout:hover { color: #14213A; }
    </style>
</head>
<body>
    <div class="ln-shell">
        {{-- Left: brand panel --}}
        <div class="ln-panel">
            <div>
                <div class="ln-mark">
                    <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
                </div>
                <p class="ln-display ln-tagline">
                    Satu klik lagi sebelum<br>
                    <b>mulai membukukan</b>.
                </p>
            </div>

            <div class="ln-features">
                <div class="ln-feature">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="6" width="16" height="12" rx="2"/><path d="M4 7.5l8 6 8-6"/></svg></div>
                    <div class="txt"><b>Cek email Anda</b><span>Link verifikasi sudah kami kirimkan</span></div>
                </div>
                <div class="ln-feature">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg></div>
                    <div class="txt"><b>Akun langsung aktif</b><span>Setelah diverifikasi, semua fitur terbuka</span></div>
                </div>
            </div>

            <div class="ln-foot">© {{ date('Y') }} LedgerNusa. Sistem akuntansi untuk bisnis modern.</div>
        </div>

        {{-- Right: content --}}
        <div class="ln-form-col">
            <div class="ln-form-inner">
                <div class="ln-mobile-mark">
                    <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
                </div>

                <p class="ln-eyebrow">Satu langkah terakhir</p>
                <h1 class="ln-display ln-h1">Verifikasi email Anda</h1>
                <p class="ln-intro">Terima kasih sudah mendaftar. Sebelum mulai, konfirmasi alamat email Anda lewat tautan yang baru saja kami kirim. Belum dapat emailnya? Kami kirimkan lagi.</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="ln-status">Tautan verifikasi baru sudah dikirim ke email yang Anda daftarkan.</div>
                @endif

                <div class="ln-actions">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="ln-submit">Kirim ulang email verifikasi</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ln-logout">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>