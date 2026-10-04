@extends('layouts.app')
@section('title', 'Delivery Order')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:300px;left:-90px;background:var(--b1);opacity:.14}

.dz-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0;font-size:13.5px;color:var(--mute)}
.dz-head-r{display:flex;align-items:center;gap:10px}
.dz-count{padding:9px 14px;border-radius:12px;font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn:hover{transform:translateY(-1px);filter:brightness(1.05)}

.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:720px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.center{text-align:center}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.center{text-align:center;white-space:nowrap}
.dz-nowrap{white-space:nowrap}
.dz-date{color:var(--mute);font-size:13px}
.dz-muted{color:#A3AEC6}

.dz-no{display:inline-block;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08);text-decoration:none;transition:.18s}
.dz-no:hover{background:rgba(29,78,216,.16)}
.dz-so{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12.5px;color:var(--mute)}

.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.posted{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}

.dz-act{height:32px;display:inline-flex;align-items:center;padding:0 13px;border-radius:9px;font-size:12.5px;font-weight:600;
    color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);text-decoration:none;transition:.18s}
.dz-act:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

.dz-empty{text-align:center;padding:56px 20px !important;color:var(--mute)}
.dz-pager{margin-top:18px}

@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Penjualan</span>
            <h1 class="dz-title">Delivery Order</h1>
            <p class="dz-sub">Surat jalan pengiriman barang</p>
        </div>
        <div class="dz-head-r">
            @if(method_exists($dos, 'total'))
                <span class="dz-count">{{ number_format($dos->total()) }} DO</span>
            @endif
            <a href="{{ route('delivery-orders.create') }}" class="dz-btn">+ DO Baru</a>
        </div>
    </div>

    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>No. DO</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>SO</th>
                        <th class="center">Status</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($dos as $d)
                    <tr>
                        <td><a href="{{ route('delivery-orders.show', $d) }}" class="dz-no">{{ $d->do_number }}</a></td>
                        <td class="dz-nowrap dz-date">{{ $d->date->format('d/m/Y') }}</td>
                        <td>{{ $d->customer->name ?? '-' }}</td>
                        <td>
                            @if($d->salesOrder?->so_number)
                                <span class="dz-so">{{ $d->salesOrder->so_number }}</span>
                            @else
                                <span class="dz-muted">-</span>
                            @endif
                        </td>
                        <td class="center"><span class="dz-badge {{ $d->status === 'delivered' ? 'posted' : 'draft' }}">{{ $d->status }}</span></td>
                        <td class="center"><a href="{{ route('delivery-orders.show', $d) }}" class="dz-act">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="dz-empty">Belum ada DO</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dz-pager">{{ $dos->links() }}</div>

</div>

@endsection