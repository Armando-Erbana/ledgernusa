@extends('layouts.app')
@section('title', 'Dashboard')
@section('meta_description', 'Ringkasan keuangan perusahaan Anda: saldo kas dan bank, jumlah akun, jurnal terbaru, dan akses cepat ke fitur LedgerNusa.')

@section('content')

@php
    $likuid = $totalKas + $totalBank;
    $kasPct = $likuid > 0 ? max(0, min(100, round($totalKas / $likuid * 100))) : 50;
    $totalAkun  = \App\Models\Account::where('company_id', session('company_id'))->count();
    $totalJurnal = \App\Models\Journal::where('company_id', session('company_id'))->count();
@endphp

<style>
.db-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--mute,#6E6E73)}
.db-eyebrow i{width:18px;height:2px;border-radius:2px;background:#2563EB}
.db-title{margin:8px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.022em;color:#1E3A8A}
.db-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute,#6E6E73)}

.db-glass{
    background:var(--glass,rgba(255,255,255,.78));
    -webkit-backdrop-filter:saturate(180%) blur(20px);backdrop-filter:saturate(180%) blur(20px);
    border:1px solid rgba(255,255,255,.78);border-radius:22px;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 0 0 .5px var(--hair,rgba(0,0,0,.08)),var(--shadow-s,0 6px 20px -8px rgba(0,0,0,.1));
}

/* ===== Ringkasan ===== */
.db-stats{display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px;margin-bottom:22px}
.db-stat{padding:22px;display:flex;flex-direction:column;gap:10px}
.db-lbl{font-size:12.5px;font-weight:600;color:var(--mute,#6E6E73)}
.db-val{font-size:26px;font-weight:700;letter-spacing:-.02em;line-height:1.1;color:#1E3A8A}
.db-val small{margin-right:4px;font-size:15px;font-weight:600;color:var(--mute,#6E6E73)}
.db-note{font-size:12px;color:var(--mute,#6E6E73)}

.db-hero{position:relative;overflow:hidden;gap:14px;color:#fff;
    background:linear-gradient(145deg,#2563EB,#1E3A8A);border-color:rgba(255,255,255,.12);
    box-shadow:0 18px 40px -16px rgba(37,99,235,.55),inset 0 1px 0 rgba(255,255,255,.18)}
.db-hero::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.6;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.05) 27px 28px)}
.db-hero > *{position:relative}
.db-hero .db-lbl{color:rgba(255,255,255,.7)}
.db-hero .db-val{font-size:32px;color:#fff}
.db-hero .db-val small{color:rgba(255,255,255,.65)}
.db-split{display:flex;height:6px;border-radius:6px;overflow:hidden;background:rgba(255,255,255,.14)}
.db-split span:first-child{background:#fff}
.db-split span:last-child{background:rgba(255,255,255,.4)}
.db-parts{display:flex;gap:30px;flex-wrap:wrap}
.db-part small{display:flex;align-items:center;gap:7px;font-size:11.5px;color:rgba(255,255,255,.7)}
.db-part small::before{content:"";width:7px;height:7px;border-radius:50%;background:#fff}
.db-part.b small::before{background:rgba(255,255,255,.4)}
.db-part b{display:block;margin-top:4px;font-size:15px;font-weight:600}

/* ===== Aksi cepat ===== */
.db-actions{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:30px}
.db-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:13px;
    font-family:inherit;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer;transition:.16s}
.db-btn.pri{color:#fff;border:0;background:linear-gradient(180deg,#2563EB,#1E3A8A);box-shadow:0 8px 18px -8px rgba(37,99,235,.5),inset 0 1px 0 rgba(255,255,255,.18)}
.db-btn.pri:hover{background:linear-gradient(180deg,#1E3A8A,#1E3A8A);transform:translateY(-1px)}
.db-btn.ghost{color:#1E3A8A;background:rgba(255,255,255,.75);border:1px solid var(--hair,rgba(0,0,0,.08))}
.db-btn.ghost:hover{background:#fff;border-color:rgba(37,99,235,.35)}
.db-btn:active{transform:scale(.98)}

/* ===== Section ===== */
.db-sec{margin:0 0 12px;font-size:16px;font-weight:700;letter-spacing:-.01em;color:#1E3A8A}

/* ===== Menu cepat (teks saja) ===== */
.db-quick{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:30px}
.db-q{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;text-decoration:none;color:#1E3A8A;
    border-radius:18px;transition:transform .18s,box-shadow .18s}
.db-q:hover{transform:translateY(-2px);box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 0 0 .5px var(--hair-2,rgba(0,0,0,.14)),var(--shadow-m,0 18px 44px -16px rgba(0,0,0,.18))}
.db-q b{display:block;font-size:14px;font-weight:600;letter-spacing:-.005em}
.db-q small{display:block;margin-top:3px;font-size:12px;color:var(--mute,#6E6E73)}
.db-q .go{flex:none;font-size:15px;color:var(--faint,#86868B);transition:transform .18s,color .18s}
.db-q:hover .go{transform:translateX(3px);color:#1E3A8A}

/* ===== Tabel ===== */
.db-card{overflow:hidden}
.db-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.db-table{width:100%;border-collapse:collapse;min-width:520px}
.db-table th{padding:13px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute,#6E6E73);background:rgba(0,0,0,.025);border-bottom:1px solid var(--hair,rgba(0,0,0,.08));white-space:nowrap}
.db-table th.num{text-align:right}
.db-table th.center{text-align:center}
.db-table td{padding:15px 20px;font-size:13.5px;color:#1E3A8A;border-bottom:1px solid var(--hair,rgba(0,0,0,.08))}
.db-table tbody tr{transition:background .14s}
.db-table tbody tr:hover{background:rgba(37,99,235,.04)}
.db-table tbody tr:last-child td{border-bottom:0}
.db-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.db-table td.center{text-align:center}
.db-table td.date{color:var(--mute,#6E6E73);white-space:nowrap}
.db-ref{font-weight:600;color:#1E3A8A;text-decoration:underline;text-decoration-color:rgba(37,99,235,.35);text-underline-offset:3px}
.db-ref:hover{text-decoration-color:#1E3A8A}
.db-empty{text-align:center;padding:48px 20px !important;color:var(--mute,#6E6E73)}

.db-badge{display:inline-flex;align-items:center;gap:7px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize;
    color:var(--soft,#515154);background:rgba(0,0,0,.05)}
.db-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--dot,#86868B)}
.db-badge.posted{--dot:var(--ok,#2E9E6B)}
.db-badge.draft{--dot:var(--warn,#C98A1B)}

@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))){
    .db-glass{background:rgba(250,250,252,.97)}
}
@media(max-width:900px){
    .db-stats{grid-template-columns:1fr 1fr}
    .db-hero{grid-column:span 2}
}
@media(max-width:520px){
    .db-title{font-size:23px}
    .db-val{font-size:22px}
    .db-hero .db-val{font-size:27px}
    .db-stat{padding:18px}
    .db-actions .db-btn{flex:1 1 calc(50% - 5px);padding:0 12px}
    .db-actions .db-btn.pri{flex-basis:100%}
}
</style>

<div class="db">

    <span class="db-eyebrow"><i></i>Ringkasan</span>
    <h1 class="db-title">Dashboard</h1>
    <p class="db-sub">Ringkasan keuangan {{ auth()->user()->activeCompany()->name ?? 'perusahaan Anda' }}</p>

    {{-- Ringkasan --}}
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
            <div class="db-val">{{ number_format($totalJurnal, 0, ',', '.') }}</div>
            <span class="db-note">Semua transaksi tercatat</span>
        </div>
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
                        <tr><td colspan="4" class="db-empty">Belum ada jurnal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection