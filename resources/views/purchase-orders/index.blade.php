@extends('layouts.app')
@section('title', 'Purchase Order')
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

/* buttons */
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

/* filter */
.dz-filter{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:14px;margin-bottom:18px}
.dz-field{display:flex;flex-direction:column;gap:6px;min-width:200px}
.dz-lbl{font-size:11.5px;font-weight:600;color:var(--mute);letter-spacing:.02em}
.dz-sel{height:42px;width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s}
.dz-sel:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}

/* table */
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:760px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table th.center{text-align:center}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.dz-table td.center{text-align:center;white-space:nowrap}
.dz-nowrap{white-space:nowrap}
.dz-date{color:var(--mute);font-size:13px}

.dz-no{display:inline-block;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08);text-decoration:none;transition:.18s}
.dz-no:hover{background:rgba(29,78,216,.16)}

/* status */
.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize;
    color:var(--mute);background:rgba(15,30,61,.06)}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}
.dz-badge.confirmed{color:#1D4ED8;background:rgba(29,78,216,.09)}
.dz-badge.partial_received{color:#0E7490;background:rgba(6,182,212,.14)}
.dz-badge.received,.dz-badge.billed{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.cancelled{color:#B4233A;background:rgba(239,68,68,.1)}

/* actions */
.dz-acts{display:inline-flex;align-items:center;gap:6px}
.dz-act{height:32px;display:inline-flex;align-items:center;padding:0 13px;border-radius:9px;font-size:12.5px;font-weight:600;
    color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);text-decoration:none;transition:.18s}
.dz-act:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-act.go{color:#fff;border:0;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 6px 14px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-act.go:hover{color:#fff;filter:brightness(1.07)}

.dz-empty{text-align:center;padding:56px 20px !important;color:var(--mute)}
.dz-pager{margin-top:18px}

@media(max-width:560px){.dz-title{font-size:23px}.dz-field{min-width:100%}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Pembelian</span>
            <h1 class="dz-title">Purchase Order</h1>
            <p class="dz-sub">Pesanan ke supplier</p>
        </div>
        <div class="dz-head-r">
            @if(method_exists($orders, 'total'))
                <span class="dz-count">{{ number_format($orders->total()) }} PO</span>
            @endif
            <a href="{{ route('purchase-orders.create') }}" class="dz-btn pri">+ PO Baru</a>
        </div>
    </div>

    <form method="GET" class="dz-glass dz-filter">
        <div class="dz-field">
            <label class="dz-lbl">Status</label>
            <select name="status" class="dz-sel">
                <option value="">Semua</option>
                @foreach(['draft'=>'Draft','confirmed'=>'Confirmed','partial_received'=>'Sebagian Diterima','received'=>'Diterima','billed'=>'Billed'] as $k=>$v)
                    <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <button class="dz-btn pri">Filter</button>
        @if(request()->filled('status'))
            <a href="{{ route('purchase-orders.index') }}" class="dz-btn ghost">Reset</a>
        @endif
    </form>

    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th class="num">Total</th>
                        <th class="center">Status</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td><a href="{{ route('purchase-orders.show', $o) }}" class="dz-no">{{ $o->po_number }}</a></td>
                        <td class="dz-nowrap dz-date">{{ $o->date->format('d/m/Y') }}</td>
                        <td>{{ $o->supplier->name ?? '-' }}</td>
                        <td class="num">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                        <td class="center"><span class="dz-badge {{ $o->status }}">{{ str_replace('_', ' ', $o->status) }}</span></td>
                        <td class="center">
                            <div class="dz-acts">
                                <a href="{{ route('purchase-orders.show', $o) }}" class="dz-act">Lihat</a>
                                @if(in_array($o->status, ['confirmed', 'partial_received']))
                                    <a href="{{ route('goods-receipts.create', ['po' => $o->id]) }}" class="dz-act go">Buat GR</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="dz-empty">Belum ada PO</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dz-pager">{{ $orders->links() }}</div>

</div>

@endsection