@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:320px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* stats */
.dz-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.dz-stat{padding:20px;display:flex;flex-direction:column;gap:12px;transition:.2s}
.dz-stat:hover{transform:translateY(-2px)}
.dz-top{display:flex;justify-content:space-between;align-items:center}
.dz-lbl{font-size:12.5px;font-weight:600;color:var(--mute)}
.dz-ico{width:36px;height:36px;border-radius:11px;display:grid;place-items:center;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 6px 14px -6px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-ico svg{width:18px;height:18px;stroke:currentColor}
.dz-val{font-size:24px;font-weight:700;letter-spacing:-.02em;color:var(--ink);line-height:1.1}

/* hero: kas + bank */
.dz-hero{grid-column:span 2;color:#fff;position:relative;overflow:hidden;
    background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.dz-hero::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.5;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.06) 27px 28px)}
.dz-hero > *{position:relative}
.dz-hero .dz-lbl{color:rgba(255,255,255,.72)}
.dz-hero .dz-val{color:#fff;font-size:30px}
.dz-hero .dz-ico{background:rgba(255,255,255,.16);box-shadow:inset 0 1px 0 rgba(255,255,255,.3)}
.dz-split{display:flex;height:6px;border-radius:6px;overflow:hidden;background:rgba(255,255,255,.14)}
.dz-split span:first-child{background:#fff}
.dz-split span:last-child{background:var(--b2)}
.dz-parts{display:flex;gap:28px;flex-wrap:wrap}
.dz-part small{display:flex;align-items:center;gap:7px;font-size:11.5px;color:rgba(255,255,255,.7)}
.dz-part small::before{content:"";width:7px;height:7px;border-radius:50%;background:#fff}
.dz-part.b small::before{background:var(--b2)}
.dz-part b{display:block;margin-top:3px;font-size:15px;font-weight:600}

/* actions */
.dz-actions{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:30px}
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);backdrop-filter:blur(10px)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

/* quick menu */
.dz-quick{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:30px}
.dz-quick-item{display:flex;align-items:center;gap:12px;padding:14px 16px;text-decoration:none;border-radius:14px;
    background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 8px 22px -14px rgba(15,30,61,.18),0 0 0 1px var(--line);
    transition:.18s}
.dz-quick-item:hover{transform:translateY(-2px);box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 14px 28px -14px rgba(29,78,216,.28),0 0 0 1px rgba(29,78,216,.25)}
.dz-quick-ico{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;color:#fff;flex-shrink:0;
    box-shadow:0 6px 14px -6px rgba(15,30,61,.4),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-quick-ico svg{width:20px;height:20px;stroke:currentColor}
.dz-quick-item b{display:block;font-size:13.5px;font-weight:600;color:var(--ink);margin-bottom:2px}
.dz-quick-item small{font-size:11.5px;color:var(--mute)}

/* table */
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 12px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:520px}
.dz-table th{padding:13px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line)}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table .num{text-align:right;font-weight:600}
.dz-table th.num{text-align:right}
.dz-table .center{text-align:center}
.dz-ref{font-weight:600;color:var(--b1);text-decoration:none}
.dz-ref:hover{text-decoration:underline}
.dz-empty{text-align:center;padding:48px 20px !important;color:var(--mute)}

.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.posted{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}

@media(max-width:820px){.dz-stats{grid-template-columns:repeat(2,1fr)}.dz-hero{grid-column:span 2}}
@media(max-width:480px){.dz-title{font-size:23px}.dz-val{font-size:20px}.dz-hero .dz-val{font-size:25px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Ringkasan</span>
    <h1 class="dz-title">Dashboard</h1>
    <p class="dz-sub">Ringkasan keuangan {{ auth()->user()->activeCompany()->name ?? 'perusahaan Anda' }}</p>

    @php
        $likuid = $totalKas + $totalBank;
        $kasPct = $likuid > 0 ? max(0, min(100, round($totalKas / $likuid * 100))) : 50;
    @endphp

    <div class="dz-stats">
        {{-- Kas + Bank --}}
        <div class="dz-glass dz-stat dz-hero">
            <div class="dz-top">
                <span class="dz-lbl">Total Kas &amp; Bank</span>
                <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6.5" width="18" height="11" rx="2"/><circle cx="12" cy="12" r="2.4"/></svg></span>
            </div>
            <div class="dz-val">Rp {{ number_format($likuid, 0, ',', '.') }}</div>
            <div class="dz-split"><span style="width:{{ $kasPct }}%"></span><span style="width:{{ 100 - $kasPct }}%"></span></div>
            <div class="dz-parts">
                <div class="dz-part">
                    <small>Saldo Kas</small>
                    <b>Rp {{ number_format($totalKas, 0, ',', '.') }}</b>
                </div>
                <div class="dz-part b">
                    <small>Saldo Bank</small>
                    <b>Rp {{ number_format($totalBank, 0, ',', '.') }}</b>
                </div>
            </div>
        </div>

        <div class="dz-glass dz-stat">
            <div class="dz-top">
                <span class="dz-lbl">Total Akun</span>
                <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20V10M12 20V4M19 20v-7"/></svg></span>
            </div>
            <div class="dz-val">{{ \App\Models\Account::where('company_id', session('company_id'))->count() }}</div>
        </div>

        <div class="dz-glass dz-stat">
            <div class="dz-top">
                <span class="dz-lbl">Total Jurnal</span>
                <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7"/></svg></span>
            </div>
            <div class="dz-val">{{ \App\Models\Journal::where('company_id', session('company_id'))->count() }}</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="dz-actions">
        <a href="{{ route('journals.create') }}" class="dz-btn pri">+ Jurnal Baru</a>
        <a href="{{ route('cash.create', ['type' => 'in']) }}" class="dz-btn ghost">+ Kas Masuk</a>
        <a href="{{ route('cash.create', ['type' => 'out']) }}" class="dz-btn ghost">− Kas Keluar</a>
        <a href="{{ route('accounts.create') }}" class="dz-btn ghost">+ Akun COA</a>
        <a href="{{ route('contacts.create') }}" class="dz-btn ghost">+ Kontak</a>
    </div>

    {{-- Menu Cepat --}}
    <h2 class="dz-sec">Menu Cepat</h2>
    <div class="dz-quick">
        <a href="{{ route('cash.index') }}" class="dz-quick-item">
            <span class="dz-quick-ico" style="background:linear-gradient(135deg,#1E7A4C,#2FA36B);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6.5" width="19" height="12" rx="2"/><circle cx="12" cy="12.5" r="2.4"/><path d="M6 6.5V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1.5"/></svg>
            </span>
            <span>
                <b>Kas &amp; Bank</b>
                <small>Catat transaksi kas</small>
            </span>
        </a>
        <a href="{{ route('contacts.index') }}" class="dz-quick-item">
            <span class="dz-quick-ico" style="background:linear-gradient(135deg,#B4915B,#D4A870);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c.8-3 2.8-4.6 5.5-4.6s4.7 1.6 5.5 4.6"/></svg>
            </span>
            <span>
                <b>Kontak</b>
                <small>Customer &amp; supplier</small>
            </span>
        </a>
        <a href="{{ route('reports.index') }}" class="dz-quick-item">
            <span class="dz-quick-ico" style="background:linear-gradient(135deg,#1C8FC4,#4FC3EC);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4M12 15V9M16 15v-6"/></svg>
            </span>
            <span>
                <b>Laporan</b>
                <small>Keuangan lengkap</small>
            </span>
        </a>
        <a href="{{ route('subscription.index') }}" class="dz-quick-item">
            <span class="dz-quick-ico" style="background:linear-gradient(135deg,#14213A,#1B2C4B);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19"/></svg>
            </span>
            <span>
                <b>Langganan</b>
                <small>Paket &amp; tagihan</small>
            </span>
        </a>
        <a href="{{ route('companies.index') }}" class="dz-quick-item">
            <span class="dz-quick-ico" style="background:linear-gradient(135deg,#5B6577,#8B96A9);">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.6"/><path d="M12 3.5v2.3M12 18.2v2.3M20.5 12h-2.3M5.8 12H3.5M17.8 6.2l-1.6 1.6M7.8 16.2l-1.6 1.6M17.8 17.8l-1.6-1.6M7.8 7.8 6.2 6.2"/></svg>
            </span>
            <span>
                <b>Setting</b>
                <small>Company &amp; profil</small>
            </span>
        </a>
    </div>

    {{-- Jurnal Terbaru --}}
    <h2 class="dz-sec">Jurnal Terbaru</h2>
    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
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
                            <td>{{ $j->date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('journals.show', $j) }}" class="dz-ref">
                                    {{ $j->reference ?: '#' . $j->id }}
                                </a>
                            </td>
                            <td class="num">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                            <td class="center">
                                <span class="dz-badge {{ $j->status === 'posted' ? 'posted' : 'draft' }}">
                                    {{ $j->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="dz-empty">Belum ada jurnal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection