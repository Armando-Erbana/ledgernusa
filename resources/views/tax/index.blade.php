@extends('layouts.app')
@section('title', 'Pajak')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:240px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-card{position:relative;display:flex;flex-direction:column;min-height:190px;padding:24px;text-decoration:none;color:var(--ink);overflow:hidden;transition:.2s}
.dz-card::after{content:"";position:absolute;left:0;right:0;bottom:0;height:3px;background:linear-gradient(90deg,var(--b1),var(--b2));
    transform:scaleX(0);transform-origin:left;transition:transform .3s ease}
.dz-card:hover{transform:translateY(-3px);box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 18px 38px -14px rgba(29,78,216,.28),0 0 0 1px rgba(29,78,216,.16)}
.dz-card:hover::after{transform:scaleX(1)}

.dz-no{font-size:34px;font-weight:700;letter-spacing:-.03em;line-height:1;
    background:linear-gradient(135deg,var(--b1),var(--b2));-webkit-background-clip:text;background-clip:text;color:transparent}
.dz-name{margin-top:auto;font-size:18px;font-weight:700;letter-spacing:-.01em}
.dz-desc{margin:6px 0 0;font-size:12.5px;line-height:1.5;color:var(--mute)}
.dz-open{margin-top:16px;padding-top:14px;border-top:1px solid var(--line);font-size:12px;font-weight:600;letter-spacing:.04em;
    text-transform:uppercase;color:var(--mute);transition:color .2s}
.dz-card:hover .dz-open{color:var(--b1)}

@media(max-width:860px){.dz-grid{grid-template-columns:1fr}.dz-card{min-height:0}.dz-name{margin-top:18px}}
@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Akuntansi</span>
    <h1 class="dz-title">Pajak</h1>
    <p class="dz-sub">Laporan &amp; rekap pajak perusahaan</p>

    <div class="dz-grid">

        <a href="{{ route('tax.ppn') }}" class="dz-glass dz-card">
            <div class="dz-no">01</div>
            <div class="dz-name">PPN</div>
            <p class="dz-desc">PPN Masukan vs Keluaran per periode</p>
            <div class="dz-open">Buka laporan</div>
        </a>

        <a href="{{ route('tax.pph23') }}" class="dz-glass dz-card">
            <div class="dz-no">02</div>
            <div class="dz-name">PPh 23</div>
            <p class="dz-desc">Pajak atas jasa (2% / 4%)</p>
            <div class="dz-open">Buka laporan</div>
        </a>

        <a href="{{ route('tax.summary') }}" class="dz-glass dz-card">
            <div class="dz-no">03</div>
            <div class="dz-name">Rekap Tahunan</div>
            <p class="dz-desc">Ringkasan pajak 12 bulan</p>
            <div class="dz-open">Buka laporan</div>
        </a>

    </div>

</div>

@endsection