@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:260px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-rpt{display:flex;align-items:center;gap:16px;padding:22px;text-decoration:none;color:var(--ink);transition:.2s}
.dz-rpt:hover{transform:translateY(-3px);border-color:rgba(29,78,216,.25);
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 18px 38px -14px rgba(29,78,216,.28),0 0 0 1px rgba(29,78,216,.14)}
.dz-ico{flex:none;width:48px;height:48px;border-radius:15px;display:grid;place-items:center;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-ico svg{width:22px;height:22px}
.dz-txt{flex:1;min-width:0}
.dz-name{font-size:15.5px;font-weight:700;letter-spacing:-.01em}
.dz-desc{margin-top:3px;font-size:12.5px;color:var(--mute)}
.dz-arrow{flex:none;width:32px;height:32px;border-radius:10px;display:grid;place-items:center;color:var(--mute);
    background:rgba(15,30,61,.05);transition:.2s}
.dz-arrow svg{width:16px;height:16px}
.dz-rpt:hover .dz-arrow{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));transform:translateX(2px)}

@media(max-width:640px){.dz-grid{grid-template-columns:1fr}.dz-title{font-size:23px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Akuntansi</span>
    <h1 class="dz-title">Laporan Keuangan</h1>
    <p class="dz-sub">Pilih laporan yang ingin dilihat</p>

    <div class="dz-grid">

        <a href="{{ route('reports.ledger') }}" class="dz-glass dz-rpt">
            <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7M9 16h4"/></svg></span>
            <span class="dz-txt">
                <div class="dz-name">Buku Besar</div>
                <div class="dz-desc">Mutasi &amp; saldo per akun</div>
            </span>
            <span class="dz-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('reports.trial-balance') }}" class="dz-glass dz-rpt">
            <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16M6 20h12M12 7 5 9l-2 6a3.5 3.5 0 0 0 4 0l-2-6 7-2M12 7l7 2 2 6a3.5 3.5 0 0 1-4 0l2-6"/></svg></span>
            <span class="dz-txt">
                <div class="dz-name">Neraca Saldo</div>
                <div class="dz-desc">Trial balance per periode</div>
            </span>
            <span class="dz-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('reports.income-statement') }}" class="dz-glass dz-rpt">
            <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/></svg></span>
            <span class="dz-txt">
                <div class="dz-name">Laba Rugi</div>
                <div class="dz-desc">Pendapatan &amp; beban</div>
            </span>
            <span class="dz-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('reports.balance-sheet') }}" class="dz-glass dz-rpt">
            <span class="dz-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M12 4v16M3 10h18"/></svg></span>
            <span class="dz-txt">
                <div class="dz-name">Neraca</div>
                <div class="dz-desc">Aset, kewajiban, ekuitas</div>
            </span>
            <span class="dz-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('reports.aging-piutang') }}" class="ln-form-card hover:border-cyan-400 transition">
    <div style="font-size:24px;margin-bottom:6px;">📅</div>
    <div class="ln-section-title">Aging Piutang</div>
    <p class="text-gray-500" style="font-size:12.5px;">Umur piutang customer</p>
</a>
<a href="{{ route('reports.aging-hutang') }}" class="ln-form-card hover:border-cyan-400 transition">
    <div style="font-size:24px;margin-bottom:6px;">📆</div>
    <div class="ln-section-title">Aging Hutang</div>
    <p class="text-gray-500" style="font-size:12.5px;">Umur hutang ke supplier</p>
</a>

    </div>

</div>

@endsection