<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        /* Pengaturan Dasar */
        @page { margin: 40px 50px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.6; }
        
        /* Reset & Utility */
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #64748B; }
        .text-primary { color: #2563EB; }
        
        /* Header Layout */
        .header-table td { vertical-align: top; padding-bottom: 20px; }
        .invoice-title { font-size: 32px; font-weight: 800; color: #1E293B; letter-spacing: -1px; margin: 0; line-height: 1; }
        .company-name { font-size: 18px; font-weight: bold; color: #1E293B; margin: 0 0 6px 0; }
        .company-info { font-size: 11.5px; color: #64748B; line-height: 1.4; }
        
        /* Info Box (Customer & Detail) */
        .info-wrapper { margin-top: 10px; margin-bottom: 30px; }
        .info-wrapper td { vertical-align: top; }
        .info-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.5px; margin-bottom: 8px; }
        .info-content { font-size: 13px; font-weight: bold; color: #1E293B; margin-bottom: 4px; }
        .info-sub { font-size: 12px; color: #64748B; }
        
        /* Tabel Item */
        .items-table { margin-bottom: 30px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
        .items-table th { background-color: #F8FAFC; color: #475569; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 12px; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .items-table td { padding: 14px 12px; border-bottom: 1px solid #F1F5F9; font-size: 12.5px; color: #334155; }
        .items-table tbody tr:nth-child(even) { background-color: #F8FAFC; }
        .items-table tbody tr:last-child td { border-bottom: none; }
        
        /* Ringkasan Total */
        .totals-wrapper { width: 100%; margin-bottom: 40px; }
        .totals-table { width: 45%; float: right; }
        .totals-table td { padding: 8px 12px; font-size: 13px; }
        .totals-table .label { color: #64748B; text-align: right; }
        .totals-table .value { color: #1E293B; font-weight: bold; text-align: right; }
        .totals-table .grand-total-label { font-size: 14px; font-weight: 800; color: #0F172A; border-top: 2px solid #E2E8F0; padding-top: 16px; }
        .totals-table .grand-total-value { font-size: 18px; font-weight: 800; color: #2563EB; border-top: 2px solid #E2E8F0; padding-top: 16px; }
        
        /* Tanda Tangan */
        .signature-box { width: 250px; float: right; text-align: center; margin-top: 20px; }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; border-top: 1px solid #94A3B8; padding-top: 8px; margin-top: 10px; color: #1E293B; }
        
        /* Footer Branding */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #E2E8F0; padding-top: 15px; margin-top: 30px; }
        .footer td { vertical-align: middle; }
        .footer-text { font-size: 11px; color: #94A3B8; }
        
        .clearfix { clear: both; }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <table class="header-table">
        <tr>
            <td width="55%">
                {{-- Logo Perusahaan (Opsional, jika ada) --}}
                <div class="company-name">{{ $sale->company->name ?? 'Nama Perusahaan Anda' }}</div>
                <div class="company-info">
                    {{ $sale->company->address ?? 'Alamat Perusahaan Belum Diatur' }}<br>
                    Telp: {{ $sale->company->phone ?? '-' }}<br>
                    Email: {{ $sale->company->email ?? '-' }}
                </div>
            </td>
            <td width="45%" class="text-right">
                <h1 class="invoice-title">INVOICE</h1>
                <div style="margin-top: 10px;">
                    <div class="font-bold text-primary" style="font-size: 14px;">#{{ $sale->invoice_number }}</div>
                    <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                        Tanggal: <span class="font-bold" style="color: #334155;">{{ $sale->date->format('d M Y') }}</span><br>
                        Jatuh Tempo: <span class="font-bold" style="color: #334155;">{{ $sale->due_date ? $sale->due_date->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Garis Pembatas Header --}}
    <div style="height: 1px; background-color: #E2E8F0; margin-bottom: 30px;"></div>

    {{-- ===== INFO CUSTOMER & PEMBAYARAN ===== --}}
    <table class="info-wrapper">
        <tr>
            <td width="55%">
                <div class="info-title">DITAGIHKAN KEPADA:</div>
                <div class="info-content">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</div>
                <div class="info-sub">
                    {{ $sale->customer->address ?? 'Alamat tidak diisi' }}<br>
                    {{ $sale->customer->phone ? 'Telp: ' . $sale->customer->phone : '' }}
                </div>
            </td>
            <td width="45%" class="text-right">
                <div class="info-title">DETAIL PEMBAYARAN:</div>
                <table style="width: 100%; font-size: 12px;">
                    <tr>
                        <td class="text-muted text-right" style="padding-bottom: 4px; width: 50%;">Tipe Pembayaran:</td>
                        <td class="font-bold text-right" style="padding-bottom: 4px; color: #1E293B;">{{ $sale->type === 'cash' ? 'Tunai (Cash)' : 'Kredit (Transfer)' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted text-right" style="padding-bottom: 4px;">Status:</td>
                        <td class="text-right" style="padding-bottom: 4px;">
                            <span style="background: {{ $sale->status === 'paid' ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $sale->status === 'paid' ? '#065F46' : '#991B1B' }}; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; text-transform: uppercase;">
                                {{ $sale->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== TABEL ITEM ===== --}}
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="35%">Deskripsi Barang/Jasa</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Harga Satuan</th>
                <th width="15%" class="text-right">Diskon</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td class="text-center text-muted">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold" style="color: #1E293B;">{{ $item->description }}</div>
                    @if($item->product)
                        <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">SKU: {{ $item->product->code }}</div>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->qty, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #1E293B;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== RINGKASAN TOTAL ===== --}}
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr>
                <td class="label">Diskon Invoice:</td>
                <td class="value" style="color: #DC2626;">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($sale->tax > 0)
            <tr>
                <td class="label">Pajak (PPN):</td>
                <td class="value">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="grand-total-label text-right">Total Tagihan:</td>
                <td class="grand-total-value text-right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="signature-box">
        <div class="text-muted" style="font-size: 12px;">Hormat Kami,</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ $sale->creator->name ?? 'Admin' }}</div>
        <div class="text-muted" style="font-size: 11px; margin-top: 2px;">Authorized Signature</div>
    </div>
    <div class="clearfix"></div>

    {{-- ===== FOOTER (Branding LedgerNusa) ===== --}}
    {{-- Gunakan public_path() agar DomPDF bisa membaca gambar lokal dengan aman --}}
    @php
        $logoPath = public_path('images/logo_akutansi_ledger_nusa-removebg-preview.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <div class="footer">
        <table>
            <tr>
                <td width="50%" style="text-align: left;">
                    <div class="footer-text" style="font-weight: bold; color: #64748B; margin-bottom: 2px;">
                        Terima kasih atas kepercayaan Anda.
                    </div>
                    <div class="footer-text">
                        Dokumen ini dihasilkan secara otomatis oleh sistem.
                    </div>
                </td>
                <td width="50%" style="text-align: right;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="LedgerNusa" style="height: 24px; margin-bottom: 4px;">
                    @else
                        <div class="font-bold" style="color: #1E3A8A; font-size: 14px; margin-bottom: 4px;">LedgerNusa</div>
                    @endif
                    <div class="footer-text" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        By Mangata Nusantara Teknologi
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>