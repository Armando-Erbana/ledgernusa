@extends('layouts.app')
@section('title', 'Detail DO')
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

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* info cards */
.dz-info{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:22px}
.dz-box{padding:18px 20px;display:flex;flex-direction:column;gap:10px}
.dz-k{font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.dz-v{font-size:15px;font-weight:600;color:var(--ink);line-height:1.4;word-break:break-word}
.dz-mono{display:inline-block;align-self:flex-start;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:13px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.dz-muted{color:#A3AEC6}

.dz-badge{align-self:flex-start;display:inline-flex;align-items:center;gap:7px;padding:5px 12px;border-radius:999px;font-size:12.5px;font-weight:600;text-transform:capitalize}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.posted{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}

/* actions */
.dz-act-bar{margin-bottom:26px}
.dz-act-bar form{margin:0}
.dz-btn{height:44px;display:inline-flex;align-items:center;justify-content:center;padding:0 24px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-btn.sm{height:38px;padding:0 16px;font-size:12.5px;border-radius:10px}

/* items */
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:560px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}

@media(max-width:760px){.dz-info{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Delivery Order</span>
            <h1 class="dz-title">{{ $do->do_number }}</h1>
            <p class="dz-sub">{{ $do->customer->name ?? '-' }} · {{ $do->date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('delivery-orders.index') }}" class="dz-btn ghost sm">← Kembali</a>
    </div>

    <div class="dz-info">
        <div class="dz-glass dz-box">
            <div class="dz-k">Status</div>
            <span class="dz-badge {{ $do->status === 'delivered' ? 'posted' : 'draft' }}">{{ $do->status }}</span>
        </div>
        <div class="dz-glass dz-box">
            <div class="dz-k">SO</div>
            @if($do->salesOrder?->so_number)
                <span class="dz-mono">{{ $do->salesOrder->so_number }}</span>
            @else
                <div class="dz-v dz-muted">-</div>
            @endif
        </div>
        <div class="dz-glass dz-box">
            <div class="dz-k">Alamat Kirim</div>
            <div class="dz-v">{{ $do->shipping_address ?: '-' }}</div>
        </div>
    </div>

    @if($do->status === 'draft')
        <div class="dz-act-bar">
            <form method="POST" action="{{ route('delivery-orders.deliver', $do) }}">
                @csrf
                <button class="dz-btn pri" onclick="return confirm('Tandai DO sudah dikirim? Stok akan berkurang.')">Tandai Terkirim</button>
            </form>
        </div>
    @elseif($do->status === 'delivered' && !$do->invoice_id)
        <div class="dz-act-bar">
            <form method="POST" action="{{ route('delivery-orders.create-invoice', $do) }}">
                @csrf
                <button class="dz-btn pri">Buat Invoice</button>
            </form>
        </div>
    @endif

    <h2 class="dz-sec">Item Dikirim</h2>
    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Produk</th>
                        <th>Gudang</th>
                        <th class="num">Qty</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($do->items as $i)
                    <tr>
                        <td>{{ $i->description }}</td>
                        <td>{{ $i->product->name ?? '-' }}</td>
                        <td>{{ $i->warehouse->name ?? '-' }}</td>
                        <td class="num">{{ number_format($i->qty, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection