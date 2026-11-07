@extends('layouts.app')
@section('title', 'Rekap Pajak Tahunan')
@section('content')

@php
    $kurang = $yearly['selisih'] > 0;
    $nihil  = $yearly['selisih'] == 0;
    $statusKey   = $nihil ? 'nihil' : ($kurang ? 'kurang' : 'lebih');
    $statusLabel = $nihil ? 'Nihil' : ($kurang ? 'Kurang Bayar' : 'Lebih Bayar');
@endphp

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:420px;left:-90px;background:var(--b1);opacity:.14}

.dz-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0;font-size:13.5px;color:var(--mute)}
.dz-head-r{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* filter tahun */
.dz-filter{display:flex;align-items:center;gap:10px;padding:8px}
.dz-sel{height:40px;min-width:110px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.85);padding:0 12px;
    font-size:13.5px;font-weight:600;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-sel:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-btn{height:40px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s;font-family:inherit}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

/* stats */
.dz-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:26px}
.dz-stat{padding:22px;display:flex;flex-direction:column;gap:10px}
.dz-lbl{font-size:12.5px;font-weight:600;color:var(--mute)}
.dz-val{font-size:26px;font-weight:700;letter-spacing:-.02em;color:var(--ink);line-height:1.1}
.dz-val small{font-size:15px;font-weight:600;color:var(--mute);margin-right:4px}
.dz-stat.hero{position:relative;overflow:hidden;color:#fff;background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.dz-stat.hero::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.5;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.06) 27px 28px)}
.dz-stat.hero > *{position:relative}
.dz-stat.hero .dz-lbl{color:rgba(255,255,255,.72)}
.dz-stat.hero .dz-val{color:#fff}
.dz-stat.hero .dz-val small{color:rgba(255,255,255,.7)}
.dz-pill{align-self:flex-start;display:inline-flex;align-items:center;gap:7px;padding:5px 13px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(255,255,255,.14)}
.dz-pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-pill.kurang{color:#FFB4BE}
.dz-pill.lebih{color:#7CF0C8}
.dz-pill.nihil{color:rgba(255,255,255,.85)}

/* table */
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:560px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table td{padding:14px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.dz-table td.mo{font-weight:600}
.dz-table tr.zero td{color:#A3AEC6}
.dz-table tr.zero td.mo{font-weight:500}
.dz-neg{color:#B4233A}
.dz-pos{color:#0E8F6B}
.dz-table tr.zero .dz-neg,.dz-table tr.zero .dz-pos{color:#A3AEC6}

.dz-table tfoot td{padding:16px 20px;background:rgba(29,78,216,.06);border-top:1px solid var(--line);font-weight:700;white-space:nowrap}
.dz-table tfoot td:first-child{font-size:11px;letter-spacing:.07em;text-transform:uppercase}
.dz-table tfoot td.num{font-size:14.5px}

@media(max-width:860px){.dz-stats{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}.dz-val{font-size:22px}.dz-head-r{width:100%}.dz-filter{flex:1}.dz-sel{flex:1}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Pajak</span>
            <h1 class="dz-title">Rekap Pajak {{ $year }}</h1>
            <p class="dz-sub">Ringkasan PPN 12 bulan</p>
        </div>
        <div class="dz-head-r">
            <form method="GET" class="dz-glass dz-filter">
                <select name="year" class="dz-sel">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endfor
                </select>
                <button class="dz-btn pri">Tampilkan</button>
            </form>
            <a href="{{ route('tax.index') }}" class="dz-btn ghost">← Kembali</a>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="dz-stats">
        <div class="dz-glass dz-stat">
            <span class="dz-lbl">Total PPN Keluaran</span>
            <div class="dz-val"><small>Rp</small>{{ number_format($yearly['keluaran'], 0, ',', '.') }}</div>
        </div>
        <div class="dz-glass dz-stat">
            <span class="dz-lbl">Total PPN Masukan</span>
            <div class="dz-val"><small>Rp</small>{{ number_format($yearly['masukan'], 0, ',', '.') }}</div>
        </div>
        <div class="dz-glass dz-stat hero">
            <span class="dz-lbl">Selisih</span>
            <span class="dz-pill {{ $statusKey }}">{{ $statusLabel }}</span>
            <div class="dz-val"><small>Rp</small>{{ number_format(abs($yearly['selisih']), 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- TABEL BULANAN --}}
    <h2 class="dz-sec">Rincian per Bulan</h2>
    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="num">PPN Keluaran</th>
                        <th class="num">PPN Masukan</th>
                        <th class="num">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $row)
                    <tr class="{{ ($row['keluaran'] == 0 && $row['masukan'] == 0) ? 'zero' : '' }}">
                        <td class="mo">{{ $row['month'] }}</td>
                        <td class="num">Rp {{ number_format($row['keluaran'], 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($row['masukan'], 0, ',', '.') }}</td>
                        <td class="num {{ $row['selisih'] > 0 ? 'dz-neg' : 'dz-pos' }}">
                            Rp {{ number_format(abs($row['selisih']), 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total {{ $year }}</td>
                        <td class="num">Rp {{ number_format($yearly['keluaran'], 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($yearly['masukan'], 0, ',', '.') }}</td>
                        <td class="num {{ $kurang ? 'dz-neg' : 'dz-pos' }}">Rp {{ number_format(abs($yearly['selisih']), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

@endsection