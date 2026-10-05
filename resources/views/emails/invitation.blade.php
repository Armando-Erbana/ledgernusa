<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #F6F6F3; padding: 20px; }
        .card { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 30px; }
        h1 { color: #14213A; margin: 0 0 12px; font-size: 20px; }
        p { color: #5B6577; line-height: 1.6; font-size: 14px; }
        .btn { display: inline-block; background: #1C8FC4; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; }
        .footer { margin-top: 24px; font-size: 12px; color: #8B96A9; }
        .logo { font-size: 18px; font-weight: bold; color: #14213A; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">LedgerNusa</div>
        <h1>Undangan Bergabung</h1>
        <p>Halo,</p>
        <p><strong>{{ $invitation->inviter->name ?? 'Admin' }}</strong> mengundang Anda untuk bergabung ke perusahaan <strong>{{ $invitation->company->name }}</strong> sebagai <strong>{{ ucfirst($invitation->role) }}</strong>.</p>
        <p>Klik tombol di bawah untuk menerima undangan. Link berlaku sampai <strong>{{ $invitation->expires_at->format('d M Y H:i') }}</strong>.</p>
        <a href="{{ $acceptUrl }}" class="btn">Terima Undangan</a>
        <p style="margin-top: 20px; font-size: 12px;">Atau salin link: <br>{{ $acceptUrl }}</p>
        <div class="footer">
            Email ini dikirim otomatis dari LedgerNusa. Jika Anda tidak merasa diundang, abaikan email ini.
        </div>
    </div>
</body>
</html>