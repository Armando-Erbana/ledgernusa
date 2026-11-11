<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f4f4f5; padding: 20px; color: #333; margin: 0; }
        .card { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo img { height: 35px; }
        .btn { display: inline-block; padding: 14px 30px; background: #14213A; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; font-size: 15px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; line-height: 1.5; }
        .divider { height: 1px; background: #eee; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <img src="{{ asset('images/logo_akutansi_ledger_nusa-removebg-preview.png') }}" alt="LedgerNusa">
        </div>
        
        <h2 style="color: #14213A; margin-top: 0;">Halo, {{ $user->name }}!</h2>
        <p style="line-height: 1.6; color: #555;">Kami menerima permintaan untuk mereset password akun LedgerNusa Anda. Jika Anda merasa tidak melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <div style="text-align: center;">
            <a href="{{ $url }}" class="btn">Reset Password Sekarang</a>
        </div>
        
        <p style="line-height: 1.6; color: #555; margin-top: 30px; font-size: 13px;">
            Tautan ini akan kedaluwarsa dalam <strong>60 menit</strong>.
        </p>
        
        <div class="divider"></div>
        
        <p style="font-size: 12px; color: #888;">Jika Anda mengalami kesulitan mengklik tombol di atas, salin dan tempel URL berikut ke browser Anda:</p>
        <p style="font-size: 12px; color: #1C8FC4; word-break: break-all;">{{ $url }}</p>
        
        <p style="margin-top: 30px;">Salam hangat,<br><strong>Mangata Nusantara Teknologi</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Mangata Nusantara Teknologi. All rights reserved.
    </div>
</body>
</html>