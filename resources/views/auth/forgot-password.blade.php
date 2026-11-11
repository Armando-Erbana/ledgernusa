<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password — LedgerNusa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
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

        .ln-brand-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .ln-mark {
            display: inline-flex;
            align-items: center;
            background: #F7F7F5;
            padding: 10px 16px;
            border-radius: 10px;
        }
        .ln-mark img { height: 26px; width: auto; display: block; }
        
        .ln-by {
            font-size: 12px;
            font-weight: 500;
            color: #8B96A9;
            letter-spacing: 0.02em;
            padding-left: 4px;
        }
        .ln-by b { color: #EDEFF4; font-weight: 600; }

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

        /* Fitur Teks Saja (Tanpa Ikon) */
        .ln-features {
            position: relative;
            z-index: 1;
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .ln-feature { position: relative; padding-left: 16px; }
        .ln-feature::before {
            content: "";
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: rgba(79, 195, 236, 0.4);
            border-radius: 2px;
        }
        .ln-feature .txt b { display: block; font-size: 14.5px; color: #EDEFF4; font-weight: 500; margin-bottom: 3px; }
        .ln-feature .txt span { font-size: 13px; color: #8B96A9; line-height: 1.4; }

        .ln-foot { position: relative; z-index: 1; font-size: 12px; color: #6E7A8E; }

        /* ---- Right panel ---- */
        .ln-form-col {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            width: 100%;
        }
        @media (max-width: 1023px) {
            .ln-form-col {
                align-items: flex-start;
                padding-top: 60px;
                padding-bottom: 60px;
            }
        }

        .ln-form-inner { width: 100%; max-width: 380px; }

        /* Branding Mobile */
        .ln-mobile-mark {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            gap: 8px;
        }
        .ln-mobile-mark img { height: 30px; }
        .ln-mobile-by {
            font-size: 11px;
            font-weight: 500;
            color: #8B96A9;
            letter-spacing: 0.02em;
        }
        .ln-mobile-by b { color: #14213A; font-weight: 600; }
        @media (min-width: 1024px) { .ln-mobile-mark { display: none; } }

        .ln-eyebrow { font-size: 13px; color: #8B96A9; margin-bottom: 6px; }
        .ln-h1 { font-size: 26px; letter-spacing: -0.01em; color: #14213A; }
        .ln-intro { margin-top: 14px; font-size: 13.5px; line-height: 1.6; color: #5B6577; }

        .ln-field { margin-top: 22px; }
        .ln-field label { display: block; font-size: 12.5px; color: #5B6577; margin-bottom: 8px; }
        
        .ln-field input {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid #D6DAE2;
            border-radius: 0;
            background: transparent;
            padding: 8px 2px 10px;
            font-size: 15px;
            color: #14213A;
            transition: border-color 0.15s ease;
        }
        .ln-field input:focus { outline: none; border-bottom-color: #1C8FC4; box-shadow: none; }

        .ln-submit {
            width: 100%;
            margin-top: 30px;
            padding: 13px 20px;
            background: #14213A;
            color: #F7F7F5;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.01em;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .ln-submit:hover { background: #1C8FC4; }
        .ln-submit:focus-visible { outline: 2px solid #1C8FC4; outline-offset: 2px; }

        .ln-status { font-size: 13.5px; color: #1E5B3D; background: #F2FAF6; border-left: 3px solid #2FA36B; padding: 8px 12px; border-radius: 6px; margin-top: 18px; }
        .error-text { color: #D04A4A; font-size: 12px; margin-top: 4px; display: block; }
    </style>
</head>
<body>
    <div class="ln-shell">
        {{-- Left: brand panel (Desktop) --}}
        <div class="ln-panel">
            <div>
                <div class="ln-brand-wrap">
                    <div class="ln-mark">
                        <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
                    </div>
                    <div class="ln-by">by <b>Mangata Nusantara Teknologi</b></div>
                </div>
                <p class="ln-display ln-tagline">
                    Lupa password bukan<br>
                    akhir dari <b>pembukuan rapi</b>.
                </p>
            </div>

            <div class="ln-features">
                <div class="ln-feature">
                    <div class="txt">
                        <b>Link dikirim ke email Anda</b>
                        <span>Berlaku sementara untuk keamanan akun</span>
                    </div>
                </div>
                <div class="ln-feature">
                    <div class="txt">
                        <b>Data tetap utuh</b>
                        <span>Jurnal dan laporan Anda tidak berubah</span>
                    </div>
                </div>
            </div>

            <div class="ln-foot">© {{ date('Y') }} Mangata Nusantara Teknologi. All rights reserved.</div>
        </div>

        {{-- Right: form (Mobile & Desktop) --}}
        <div class="ln-form-col">
            <div class="ln-form-inner">
                {{-- Mobile Branding (Logo + Text) --}}
                <div class="ln-mobile-mark">
                    <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
                    <div class="ln-mobile-by">by <b>Mangata Nusantara Teknologi</b></div>
                </div>

                <p class="ln-eyebrow">Pemulihan akun</p>
                <h1 class="ln-display ln-h1">Lupa password?</h1>
                <p class="ln-intro">Masukkan email Anda, kami akan kirim tautan untuk membuat password baru.</p>

                @if (session('status'))
                    <div class="ln-status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="ln-field">
                        <label for="email">Email</label>
                        <!-- Menggunakan input biasa agar rapi di HP -->
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                        @if($errors->has('email'))
                            <span class="error-text">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <button type="submit" class="ln-submit">Kirim link reset password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>