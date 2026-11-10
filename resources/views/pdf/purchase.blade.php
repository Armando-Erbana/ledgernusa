<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian {{ $purchase->bill_number }}</title>
    <style>
        @page { margin: 40px 50px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #64748B; }
        .text-primary { color: #2563EB; }
        
        .header-table td { vertical-align: top; padding-bottom: 20px; }
        .invoice-title { font-size: 32px; font-weight: 800; color: #1E293B; letter-spacing: -1px; margin: 0; line-height: 1; }
        .company-name { font-size: 18px; font-weight: bold; color: #1E293B; margin: 0 0 6px 0; }
        .company-info { font-size: 11.5px; color: #64748B; line-height: 1.4; }
        
        .info-wrapper { margin-top: 10px; margin-bottom: 30px; }
        .info-wrapper td { vertical-align: top; }
        .info-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.5px; margin-bottom: 8px; }
        .info-content { font-size: 13px; font-weight: bold; color: #1E293B; margin-bottom: 4px; }
        .info-sub { font-size: 12px; color: #64748B; }
        
        .items-table { margin-bottom: 30px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
        .items-table th { background-color: #F8FAFC; color: #475569; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 12px; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .items-table td { padding: 14px 12px; border-bottom: 1px solid #F1F5F9; font-size: 12.5px; color: #334155; }
        .items-table tbody tr:nth-child(even) { background-color: #F8FAFC; }
        .items-table tbody tr:last-child td { border-bottom: none; }
        
        .totals-wrapper { width: 100%; margin-bottom: 40px; }
        .totals-table { width: 45%; float: right; }
        .totals-table td { padding: 8px 12px; font-size: 13px; }
        .totals-table .label { color: #64748B; text-align: right; }
        .totals-table .value { color: #1E293B; font-weight: bold; text-align: right; }
        .totals-table .grand-total-label { font-size: 14px; font-weight: 800; color: #0F172A; border-top: 2px solid #E2E8F0; padding-top: 16px; }
        .totals-table .grand-total-value { font-size: 18px; font-weight: 800; color: #2563EB; border-top: 2px solid #E2E8F0; padding-top: 16px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #E2E8F0; padding-top: 15px; margin-top: 30px; }
        .footer td { vertical-align: middle; }
        .footer-text { font-size: 11px; color: #94A3B8; }
        .clearfix { clear: both; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="55%">
                <div class="company-name">{{ $purchase->company->name ?? 'Nama Perusahaan Anda' }}</div>
                <div class="company-info">
                    {{ $purchase->company->address ?? 'Alamat Perusahaan' }}<br>
                    Telp: {{ $purchase->company->phone ?? '-' }}<br>
                    Email: {{ $purchase->company->email ?? '-' }}
                </div>
            </td>
            <td width="45%" class="text-right">
                <h1 class="invoice-title">FAKTUR PEMBELIAN</h1>
                <div style="margin-top: 10px;">
                    <div class="font-bold text-primary" style="font-size: 14px;">#{{ $purchase->bill_number }}</div>
                    <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                        Tanggal: <span class="font-bold" style="color: #334155;">{{ $purchase->date->format('d M Y') }}</span><br>
                        Jatuh Tempo: <span class="font-bold" style="color: #334155;">{{ $purchase->due_date ? $purchase->due_date->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div style="height: 1px; background-color: #E2E8F0; margin-bottom: 30px;"></div>

    <table class="info-wrapper">
        <tr>
            <td width="55%">
                <div class="info-title">DITERBITKAN OLEH (SUPPLIER):</div>
                <div class="info-content">{{ $purchase->supplier->name ?? 'Supplier Umum' }}</div>
                <div class="info-sub">
                    {{ $purchase->supplier->address ?? 'Alamat tidak diisi' }}<br>
                    {{ $purchase->supplier->phone ? 'Telp: ' . $purchase->supplier->phone : '' }}
                </div>
            </td>
            <td width="45%" class="text-right">
                <div class="info-title">STATUS PEMBAYARAN:</div>
                <div class="text-right">
                    <span style="background: {{ $purchase->status === 'paid' ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $purchase->status === 'paid' ? '#065F46' : '#991B1B' }}; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; text-transform: uppercase;">
                        {{ $purchase->status }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="40%">Deskripsi Barang/Jasa</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Harga Beli</th>
                <th width="10%" class="text-right">Diskon</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $index => $item)
            <tr>
                <td class="text-center text-muted">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold" style="color: #1E293B;">{{ $item->description }}</div>
                </td>
                <td class="text-center">{{ number_format($item->qty, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #1E293B;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($purchase->discount > 0)
            <tr>
                <td class="label">Diskon:</td>
                <td class="value" style="color: #DC2626;">- Rp {{ number_format($purchase->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($purchase->tax > 0)
            <tr>
                <td class="label">Pajak (PPN):</td>
                <td class="value">Rp {{ number_format($purchase->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="grand-total-label text-right">Total Tagihan:</td>
                <td class="grand-total-value text-right">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>

    {{-- FOOTER BRANDING --}}
    @php
        $logoPath = public_path('images/logo_akutansi_ledger_nusa-removebg-preview.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <div class="footer">
        <table>
            <tr>
                <td width="50%" style="text-align: left;">
                    <div class="footer-text" style="font-weight: bold; color: #64748B; margin-bottom: 2px;">Dokumen Internal Perusahaan.</div>
                    <div class="footer-text">Dihasilkan oleh sistem LedgerNusa.</div>
                </td>
                <td width="50%" style="text-align: right;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="LedgerNusa" style="height: 24px; margin-bottom: 4px;">
                    @endif
                    <div class="footer-text" style="font-size: 10px; text-transform: uppercase;">By Mangata Nusantara Teknologi</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>