@extends('layouts.app')
@section('title', 'Dashboard')
@section('meta_description', 'Ringkasan keuangan perusahaan Anda: saldo kas dan bank, jumlah akun, jurnal terbaru, dan akses cepat ke fitur LedgerNusa.')

@section('content')

<style>
    /* Banner Konsolidasi */
    .db-banner {
        margin-bottom: 24px; padding: 14px 18px; border-radius: 14px;
        background-color: #EFF6FF; border: 1px solid #BFDBFE; 
        font-size: 13.5px; color: #1E3A8A; line-height: 1.5;
        display: flex; align-items: center; gap: 10px;
    }
    
    .db-eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 11.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--mute, #6E6E73); margin-bottom: 8px; }
    .db-eyebrow i { width: 18px; height: 2px; border-radius: 2px; background: #2563EB; }
    .db-title { margin: 0 0 6px; font-size: 28px; font-weight: 700; letter-spacing: -.022em; color: #1E3A8A; }
    .db-sub { margin: 0 0 28px; font-size: 14px; color: var(--mute, #6E6E73); }

    .db-glass {
        background: var(--glass, rgba(255,255,255,.78));
        -webkit-backdrop-filter: saturate(180%) blur(20px); backdrop-filter: saturate(180%) blur(20px);
        border: 1px solid rgba(255,255,255,.78); border-radius: 22px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair, rgba(0,0,0,.08)), var(--shadow-s, 0 6px 20px -8px rgba(0,0,0,.1));
    }

    /* ===== Grid Ringkasan (Kartu) ===== */
    .db-stats {
        display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 24px;
    }
    /* Tablet: 2 kolom, kartu utama penuh */
    @media(min-width: 640px) {
        .db-stats { grid-template-columns: 1fr 1fr; }
        .db-hero { grid-column: span 2; }
    }
    /* Desktop: 3 kolom (kartu utama lebih lebar) */
    @media(min-width: 1024px) {
        .db-stats { grid-template-columns: 2fr 1fr 1fr; }
        .db-hero { grid-column: auto; }
    }
    
    .db-stat { padding: 24px; display: flex; flex-direction: column; gap: 10px; }
    .db-lbl { font-size: 13px; font-weight: 600; color: var(--mute, #6E6E73); }
    .db-val { font-size: 26px; font-weight: 700; letter-spacing: -.02em; line-height: 1.1; color: #1E3A8A; word-break: break-word; }
    .db-val small { margin-right: 6px; font-size: 15px; font-weight: 600; color: var(--mute, #6E6E73); }
    .db-note { font-size: 12.5px; color: var(--mute, #6E6E73); }

    /* Kartu Utama (Biru) */
    .db-hero { position: relative; overflow: hidden; gap: 14px; color: #fff;
        background: linear-gradient(145deg, #2563EB, #1E3A8A); border-color: rgba(255,255,255,.12);
        box-shadow: 0 18px 40px -16px rgba(37,99,235,.55), inset 0 1px 0 rgba(255,255,255,.18);
    }
    .db-hero::before { content: ""; position: absolute; inset: 0; pointer-events: none; opacity: .6;
        background: repeating-linear-gradient(to bottom, transparent 0 27px, rgba(255,255,255,.05) 27px 28px);
    }
    .db-hero > * { position: relative; }
    .db-hero .db-lbl { color: rgba(255,255,255,.75); }
    .db-hero .db-val { font-size: 34px; color: #fff; }
    .db-hero .db-val small { color: rgba(255,255,255,.65); }
    
    .db-split { display: flex; height: 6px; border-radius: 6px; overflow: hidden; background: rgba(255,255,255,.14); margin: 4px 0; }
    .db-split span:first-child { background: #fff; }
    .db-split span:last-child { background: rgba(255,255,255,.4); }
    
    .db-parts { display: flex; gap: 30px; flex-wrap: wrap; margin-top: auto; padding-top: 10px; }
    .db-part small { display: flex; align-items: center; gap: 7px; font-size: 12px; color: rgba(255,255,255,.8); margin-bottom: 4px; }
    .db-part small::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: #fff; }
    .db-part.b small::before { background: rgba(255,255,255,.4); }
    .db-part b { display: block; font-size: 16px; font-weight: 600; }

    /* ===== Grafik ===== */
    .db-chart-card { padding: 24px; margin-bottom: 24px; }
    .db-chart-head { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px; }
    .db-chart-wrap { display: flex; align-items: flex-end; gap: 8px; height: 140px; margin-top: 20px; padding-bottom: 24px; position: relative; }
    .db-chart-wrap::after { content: ""; position: absolute; bottom: 24px; left: 0; right: 0; border-bottom: 1px dashed rgba(0,0,0,.1); z-index: 0; }
    .db-bar-col { flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; height: 100%; position: relative; z-index: 1; }
    .db-bar { width: 100%; max-width: 40px; background: linear-gradient(180deg, #3B82F6, #1E3A8A); border-radius: 6px 6px 0 0; transition: height 0.4s ease; position: relative; min-height: 4px; }
    .db-bar:hover { filter: brightness(1.1); }
    .db-bar-label { position: absolute; bottom: -24px; font-size: 10.5px; color: var(--mute, #6E6E73); white-space: nowrap; }
    .db-empty-chart { padding: 40px; text-align: center; color: var(--mute, #6E6E73); font-size: 13.5px; }

    /* ===== Tombol Aksi ===== */
    .db-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 32px; }
    .db-btn { height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 20px; border-radius: 12px; font-family: inherit; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all .2s; border: none; }
    .db-btn.pri { color: #fff; background: linear-gradient(180deg, #2563EB, #1E3A8A); box-shadow: 0 4px 12px rgba(37,99,235,.3); }
    .db-btn.ghost { color: #1E3A8A; background: #fff; border: 1px solid var(--hair); }
    .db-btn.pri:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37,99,235,.4); }
    .db-btn.ghost:hover { background: #f8f9fa; border-color: var(--hair-2); }
    
    /* Mobile: Tombol Grid 2x2 */
    @media(max-width: 639px) {
        .db-actions { display: grid; grid-template-columns: 1fr 1fr; }
        .db-btn.pri { grid-column: 1 / -1; }
        .db-btn { width: 100%; padding: 0 12px; }
    }

    /* ===== Section & Menu Cepat ===== */
    .db-sec { margin: 0 0 16px; font-size: 16px; font-weight: 700; letter-spacing: -.01em; color: #1E3A8A; }
    .db-quick { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; margin-bottom: 32px; }
    .db-q { display: flex; align-items: center; justify-content: space-between; padding: 18px; text-decoration: none; color: #1E3A8A; border-radius: 18px; transition: transform .18s, box-shadow .18s; }
    .db-q:hover { transform: translateY(-3px); box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 0 0 .5px var(--hair-2), var(--shadow-m); }
    .db-q b { display: block; font-size: 14.5px; font-weight: 600; letter-spacing: -.005em; }
    .db-q small { display: block; margin-top: 4px; font-size: 12px; color: var(--mute); line-height: 1.3; }
    .db-q .go { flex: none; font-size: 16px; color: var(--faint); transition: transform .18s, color .18s; }
    .db-q:hover .go { transform: translateX(4px); color: #1E3A8A; }

    /* ===== Tabel ===== */
    .db-card { overflow: hidden; }
    .db-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }
    .db-table { width: 100%; border-collapse: collapse; min-width: 520px; }
    .db-table th { padding: 14px 20px; text-align: left; font-size: 11.5px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--mute); background: rgba(0,0,0,.025); border-bottom: 1px solid var(--hair); white-space: nowrap; }
    .db-table th.num { text-align: right; }
    .db-table th.center { text-align: center; }
    .db-table td { padding: 16px 20px; font-size: 13.5px; color: #1E3A8A; border-bottom: 1px solid var(--hair); }
    .db-table tbody tr { transition: background .14s; }
    .db-table tbody tr:hover { background: rgba(37,99,235,.03); }
    .db-table tbody tr:last-child td { border-bottom: 0; }
    .db-table td.num { text-align: right; font-weight: 600; white-space: nowrap; }
    .db-table td.center { text-align: center; }
    .db-table td.date { color: var(--mute); white-space: nowrap; }
    .db-ref { font-weight: 600; color: #1E3A8A; text-decoration: underline; text-decoration-color: rgba(37,99,235,.35); text-underline-offset: 3px; }
    .db-ref:hover { text-decoration-color: #1E3A8A; }
    .db-empty { text-align: center; padding: 60px 20px !important; color: var(--mute); font-size: 14px; }

    .db-badge { display: inline-flex; align-items: center; gap: 7px; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; color: var(--soft); background: rgba(0,0,0,.05); }
    .db-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--dot, var(--faint)); }
    .db-badge.posted { --dot: var(--ok); }
    .db-badge.draft { --dot: var(--warn); }
</style>

<div class="db">

    @if($isConsolidated ?? false)
        <div class="db-banner">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span><strong>Mode Konsolidasi</strong> — angka di bawah adalah total dari <strong>seluruh cabang</strong>.</span>
        </div>
    @endif

    @php
        $likuid = $totalKas + $totalBank;
        $kasPct = $likuid > 0 ? max(0, min(100, round($totalKas / $likuid * 100))) : 50;
    @endphp

    <span class="db-eyebrow"><i></i>Ringkasan</span>
    <h1 class="db-title">Dashboard</h1>
    <p class="db-sub">Ringkasan keuangan {{ auth()->user()->activeCompany()->name ?? 'perusahaan Anda' }}</p>

    {{-- Ringkasan (Kartu) --}}
    <div class="db-stats">
        <div class="db-glass db-stat db-hero">
            <span class="db-lbl">Total Kas &amp; Bank</span>
            <div class="db-val"><small>Rp</small>{{ number_format($likuid, 0, ',', '.') }}</div>
            <div class="db-split" role="img" aria-label="Kas {{ $kasPct }} persen, bank {{ 100 - $kasPct }} persen">
                <span style="width:{{ $kasPct }}%"></span><span style="width:{{ 100 - $kasPct }}%"></span>
            </div>
            <div class="db-parts">
                <div class="db-part">
                    <small>Saldo Kas</small>
                    <b>Rp {{ number_format($totalKas, 0, ',', '.') }}</b>
                </div>
                <div class="db-part b">
                    <small>Saldo Bank</small>
                    <b>Rp {{ number_format($totalBank, 0, ',', '.') }}</b>
                </div>
            </div>
        </div>

        <div class="db-glass db-stat">
            <span class="db-lbl">Total Akun</span>
            <div class="db-val">{{ number_format($totalAkun, 0, ',', '.') }}</div>
            <span class="db-note">Chart of Accounts</span>
        </div>

        <div class="db-glass db-stat">
            <span class="db-lbl">Total Jurnal</span>
            <div class="db-val">{{ number_format($totalJournal, 0, ',', '.') }}</div>
            <span class="db-note">Semua transaksi tercatat</span>
        </div>
    </div>

    {{-- Grafik Penjualan 7 Hari --}}
    <div class="db-glass db-chart-card">
        <div class="db-chart-head">
            <span class="db-lbl" style="font-size:14px; color:#1E3A8A;">Grafik Penjualan (7 Hari Terakhir)</span>
            @if(($chartData ?? collect())->count() > 0)
                <span class="db-note">Total: Rp {{ number_format($chartData->sum('total'), 0, ',', '.') }}</span>
            @endif
        </div>
        
        @if(($chartData ?? collect())->count() > 0)
            <div class="db-chart-wrap">
                @php $max = $chartData->max('total') ?: 1; @endphp
                @foreach($chartData as $row)
                    <div class="db-bar-col">
                        <div class="db-bar" style="height: {{ max(5, ($row->total / $max) * 100) }}%" title="Rp {{ number_format($row->total, 0, ',', '.') }}"></div>
                        <span class="db-bar-label">{{ \Carbon\Carbon::parse($row->d)->format('d/m') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="db-empty-chart">
                Belum ada data penjualan untuk ditampilkan minggu ini.
            </div>
        @endif
    </div>

    {{-- Aksi cepat --}}
    <div class="db-actions">
        <a href="{{ route('journals.create') }}" class="db-btn pri">+ Jurnal Baru</a>
        <a href="{{ route('cash.create', ['type' => 'in']) }}" class="db-btn ghost">+ Kas Masuk</a>
        <a href="{{ route('cash.create', ['type' => 'out']) }}" class="db-btn ghost">− Kas Keluar</a>
        <a href="{{ route('accounts.create') }}" class="db-btn ghost">+ Akun COA</a>
        <a href="{{ route('contacts.create') }}" class="db-btn ghost">+ Kontak</a>
    </div>

    {{-- Menu cepat --}}
    <h2 class="db-sec">Menu Cepat</h2>
    <div class="db-quick">
        <a href="{{ route('cash.index') }}" class="db-glass db-q">
            <span><b>Kas &amp; Bank</b><small>Catat transaksi kas</small></span>
            <span class="go" aria-hidden="true">→</span>
        </a>
        <a href="{{ route('contacts.index') }}" class="db-glass db-q">
            <span><b>Kontak</b><small>Customer &amp; supplier</small></span>
            <span class="go" aria-hidden="true">→</span>
        </a>
        <a href="{{ route('reports.index') }}" class="db-glass db-q">
            <span><b>Laporan</b><small>Keuangan lengkap</small></span>
            <span class="go" aria-hidden="true">→</span>
        </a>
        <a href="{{ route('subscription.index') }}" class="db-glass db-q">
            <span><b>Langganan</b><small>Paket &amp; tagihan</small></span>
            <span class="go" aria-hidden="true">→</span>
        </a>
        <a href="{{ route('companies.index') }}" class="db-glass db-q">
            <span><b>Setting</b><small>Company &amp; profil</small></span>
            <span class="go" aria-hidden="true">→</span>
        </a>
    </div>

    {{-- Jurnal terbaru --}}
    <h2 class="db-sec">Jurnal Terbaru</h2>
    <div class="db-glass db-card">
        <div class="db-scroll">
            <table class="db-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Referensi</th>
                        <th class="num">Total</th>
                        <th class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJournals as $j)
                        <tr>
                            <td class="date">{{ $j->date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('journals.show', $j) }}" class="db-ref">{{ $j->reference ?: '#' . $j->id }}</a>
                            </td>
                            <td class="num">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                            <td class="center">
                                <span class="db-badge {{ $j->status === 'posted' ? 'posted' : 'draft' }}">{{ $j->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="db-empty">Belum ada jurnal yang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection