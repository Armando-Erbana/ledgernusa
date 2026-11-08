@extends('layouts.app')
@section('title', 'Laporan')
@section('meta_description', 'Laporan keuangan LedgerNusa: buku besar, neraca saldo, laba rugi, neraca, serta aging piutang dan hutang.')

@section('content')

@php
    $reportGroups = [
        'Laporan Keuangan' => [
            ['Buku Besar',   'Mutasi & saldo per akun',     'reports.ledger'],
            ['Neraca Saldo', 'Trial balance per periode',   'reports.trial-balance'],
            ['Laba Rugi',    'Pendapatan & beban',          'reports.income-statement'],
            ['Neraca',       'Aset, kewajiban, ekuitas',    'reports.balance-sheet'],
        ],
        'Umur Piutang & Hutang' => [
            ['Aging Piutang', 'Umur piutang customer',      'reports.aging-piutang'],
            ['Aging Hutang',  'Umur hutang ke supplier',    'reports.aging-hutang'],
        ],
    ];
@endphp

<style>
.rp-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--mute,#6E6E73)}
.rp-eyebrow i{width:18px;height:2px;border-radius:2px;background:var(--ink,#1D1D1F)}
.rp-title{margin:8px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.022em;color:var(--ink,#1D1D1F)}
.rp-sub{margin:0 0 28px;font-size:13.5px;color:var(--mute,#6E6E73)}

.rp-group{margin-bottom:30px}
.rp-group:last-child{margin-bottom:0}
.rp-gt{margin:0 0 12px;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--faint,#86868B)}
.rp-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}

.rp-card{
    display:flex;flex-direction:column;min-height:158px;padding:22px;text-decoration:none;color:var(--ink,#1D1D1F);
    background:var(--glass,rgba(255,255,255,.78));
    -webkit-backdrop-filter:saturate(180%) blur(20px);backdrop-filter:saturate(180%) blur(20px);
    border:1px solid rgba(255,255,255,.78);border-radius:22px;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 0 0 .5px var(--hair,rgba(0,0,0,.08)),var(--shadow-s,0 6px 20px -8px rgba(0,0,0,.1));
    transition:transform .2s,box-shadow .2s;
}
.rp-card:hover{
    transform:translateY(-3px);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 0 0 .5px var(--hair-2,rgba(0,0,0,.14)),var(--shadow-m,0 18px 44px -16px rgba(0,0,0,.18));
}
.rp-card:active{transform:scale(.99)}
.rp-no{font-size:13px;font-weight:600;letter-spacing:.04em;color:var(--faint,#86868B)}
.rp-name{margin-top:auto;padding-top:22px;font-size:18px;font-weight:700;letter-spacing:-.015em}
.rp-desc{margin:4px 0 0;font-size:13px;line-height:1.45;color:var(--mute,#6E6E73)}
.rp-go{margin-top:16px;padding-top:14px;border-top:1px solid var(--hair,rgba(0,0,0,.08));display:flex;justify-content:space-between;align-items:center;
    font-size:12.5px;font-weight:600;color:var(--mute,#6E6E73);transition:color .2s}
.rp-go span{display:inline-block;transition:transform .2s}
.rp-card:hover .rp-go{color:var(--ink,#1D1D1F)}
.rp-card:hover .rp-go span{transform:translateX(4px)}

@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))){
    .rp-card{background:rgba(250,250,252,.97)}
}
@media(max-width:640px){
    .rp-grid{grid-template-columns:1fr}
    .rp-title{font-size:23px}
    .rp-card{min-height:0}
    .rp-name{padding-top:14px}
}
</style>

<div class="rp">

    <span class="rp-eyebrow"><i></i>Akuntansi</span>
    <h1 class="rp-title">Laporan Keuangan</h1>
    <p class="rp-sub">Pilih laporan yang ingin dilihat</p>

    @foreach($reportGroups as $group => $reports)
        <section class="rp-group" aria-labelledby="rp-g-{{ $loop->index }}">
            <h2 class="rp-gt" id="rp-g-{{ $loop->index }}">{{ $group }}</h2>
            <div class="rp-grid">
                @foreach($reports as [$name, $desc, $route])
                    <a href="{{ route($route) }}" class="rp-card">
                        <div class="rp-name">{{ $name }}</div>
                        <p class="rp-desc">{{ $desc }}</p>
                        <div class="rp-go">Buka laporan <span aria-hidden="true">→</span></div>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach

</div>

@endsection