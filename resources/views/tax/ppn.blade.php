@extends('layouts.app')
@section('title', 'Laporan PPN')

@section('content')

@php
    $statusKey   = $status === 'kurang_bayar' ? 'kurang' : ($status === 'lebih_bayar' ? 'lebih' : 'nihil');
    $statusLabel = $status === 'kurang_bayar' ? 'Kurang Bayar' : ($status === 'lebih_bayar' ? 'Lebih Bayar' : 'Nihil');
    $statusNote  = $status === 'kurang_bayar'
        ? 'PPN Keluaran lebih besar dari Masukan'
        : ($status === 'lebih_bayar' ? 'PPN Masukan lebih besar dari Keluaran' : 'PPN Keluaran dan Masukan seimbang');
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

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* filter periode */
.dz-filter{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:8px}
.dz-filter .sep{font-size:12.5px;font-weight:600;color:var(--mute)}
.dz-date{height:40px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.85);padding:0 12px;
    font-size:13px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-date:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-btn{height:40px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;border:0;cursor:pointer;transition:.18s;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn:hover{transform:translateY(-1px);filter:brightness(1.05)}

/* summary */
.dz-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:26px}
.dz-stat{padding:22px;display:flex;flex-direction:column;gap:10px}
.dz-lbl{font-size:12.5px;font-weight:600;color:var(--mute)}
.dz-val{font-size:26px;font-weight:700;letter-spacing:-.02em;color:var(--ink);line-height:1.1}
.dz-val small{font-size:15px;font-weight:600;color:var(--mute);margin-right:4px}
.dz-stat .note{font-size:12px;color:var(--mute)}

.dz-stat.hero{position:relative;overflow:hidden;color:#fff;background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.dz-stat.hero::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.5;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.06) 27px 28px)}
.dz-stat.hero > *{position:relative}
.dz-stat.hero .dz-lbl,.dz-stat.hero .note{color:rgba(255,255,255,.72)}
.dz-stat.hero .dz-val{color:#fff}
.dz-pill{align-self:flex-start;display:inline-flex;align-items:center;gap:7px;padding:5px 13px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(255,255,255,.14)}
.dz-pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-pill.kurang{color:#FFB4BE}
.dz-pill.lebih{color:#7CF0C8}
.dz-pill.nihil{color:rgba(255,255,255,.85)}

/* tables */
.dz-cols{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;align-items:start}
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-sec small{margin-left:auto;font-size:12.5px;font-weight:600;color:var(--mute)}
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:420px}
.dz-table th{padding:13px 18px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table td{padding:14px 18px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:top}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.dz-table td.date{color:var(--mute);font-size:13px;white-space:nowrap}
.dz-ref{display:inline-block;padding:3px 9px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.dz-desc{margin-top:5px;font-size:12px;color:var(--mute);line-height:1.45}
.dz-empty{text-align:center;padding:44px 20px !important;color:var(--mute);font-size:13.5px}

.dz-table tfoot td{padding:15px 18px;background:rgba(29,78,216,.06);border-top:1px solid var(--line);vertical-align:middle}
.dz-table tfoot .lbl-t{text-align:right;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--ink)}
.dz-table tfoot .val-t{text-align:right;font-size:15px;font-weight:700;color:var(--b1);white-space:nowrap}

@media(max-width:1100px){.dz-cols{grid-template-columns:1fr}}
@media(max-width:860px){.dz-stats{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}.dz-val{font-size:22px}.dz-filter{width:100%}.dz-date{flex:1;min-width:0}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Pajak</span>
            <h1 class="dz-title">Laporan PPN</h1>
            <p class="dz-sub">Rekapitulasi Pajak Pertambahan Nilai (PPN) Keluaran dan Masukan</p>
        </div>
        <form method="GET" class="dz-glass dz-filter">
            <input type="date" name="from" value="{{ $from }}" class="dz-date" required>
            <span class="sep">s/d</span>
            <input type="date" name="to" value="{{ $to }}" class="dz-date" required>
            <button type="submit" class="dz-btn">Filter</button>
        </form>
    </div>

    {{-- SUMMARY --}}
    <div class="dz-stats">
        <div class="dz-glass dz-stat">
            <span class="dz-lbl">PPN Keluaran (Penjualan)</span>
            <div class="dz-val"><small>Rp</small>{{ number_format($totalKeluaran, 2, ',', '.') }}</div>
            <span class="note">{{ count($ppnKeluaran) }} transaksi</span>
        </div>

        <div class="dz-glass dz-stat">
            <span class="dz-lbl">PPN Masukan (Pembelian)</span>
            <div class="dz-val"><small>Rp</small>{{ number_format($totalMasukan, 2, ',', '.') }}</div>
            <span class="note">{{ count($ppnMasukan) }} transaksi</span>
        </div>

        <div class="dz-glass dz-stat hero">
            <span class="dz-lbl">Status PPN</span>
            <span class="dz-pill {{ $statusKey }}">{{ $statusLabel }}</span>
            <div class="dz-val"><small style="color:rgba(255,255,255,.7)">Rp</small>{{ number_format(abs($selisih), 2, ',', '.') }}</div>
            <span class="note">{{ $statusNote }}</span>
        </div>
    </div>

    <div class="dz-cols">

        {{-- PPN KELUARAN --}}
        <div>
            <h2 class="dz-sec">Rincian PPN Keluaran <small>{{ count($ppnKeluaran) }} data</small></h2>
            <div class="dz-glass dz-card">
                <div class="dz-scroll">
                    <table class="dz-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Referensi</th>
                                <th class="num">Nominal PPN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ppnKeluaran as $item)
                                <tr>
                                    <td class="date">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                    <td><span class="dz-ref">{{ $item->invoice_number ?? $item->reference ?? '-' }}</span></td>
                                    <td class="num">Rp {{ number_format($item->tax, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="dz-empty">Tidak ada data PPN Keluaran pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($ppnKeluaran) > 0)
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="lbl-t">Total PPN Keluaran</td>
                                    <td class="val-t">Rp {{ number_format($totalKeluaran, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- PPN MASUKAN --}}
        <div>
            <h2 class="dz-sec">Rincian PPN Masukan <small>{{ count($ppnMasukan) }} data</small></h2>
            <div class="dz-glass dz-card">
                <div class="dz-scroll">
                    <table class="dz-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Referensi Jurnal</th>
                                <th class="num">Nominal PPN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ppnMasukan as $entry)
                                <tr>
                                    <td class="date">{{ \Carbon\Carbon::parse($entry->journal->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="dz-ref">{{ $entry->journal->reference ?? 'JRN-'.$entry->journal_id }}</span>
                                        @if($entry->journal->description)
                                            <div class="dz-desc">{{ $entry->journal->description }}</div>
                                        @endif
                                    </td>
                                    <td class="num">Rp {{ number_format($entry->debit, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="dz-empty">Tidak ada data PPN Masukan pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($ppnMasukan) > 0)
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="lbl-t">Total PPN Masukan</td>
                                    <td class="val-t">Rp {{ number_format($totalMasukan, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection