@extends('layouts.app')
@section('title', 'Detail PO')
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
.dz-v{font-size:15px;font-weight:600;color:var(--ink);line-height:1.4}
.dz-v.big{font-size:22px;font-weight:700;letter-spacing:-.02em;color:var(--b1)}
.dz-muted{color:#A3AEC6}

/* badges */
.dz-badge{align-self:flex-start;display:inline-flex;align-items:center;gap:7px;padding:5px 12px;border-radius:999px;font-size:12.5px;font-weight:600;text-transform:capitalize;
    color:var(--mute);background:rgba(15,30,61,.06)}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}
.dz-badge.confirmed{color:#1D4ED8;background:rgba(29,78,216,.09)}
.dz-badge.partial_received{color:#0E7490;background:rgba(6,182,212,.14)}
.dz-badge.received,.dz-badge.billed{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.cancelled{color:#B4233A;background:rgba(239,68,68,.1)}
.dz-table .dz-badge{align-self:auto;font-size:12px;padding:5px 11px}

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

/* sections + tables */
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-card{overflow:hidden;margin-bottom:26px}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:640px}
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
.dz-item small{display:block;margin-top:3px;font-size:12px;color:var(--mute)}

/* footer ringkasan */
.dz-table tfoot td{padding:12px 20px;font-size:13.5px;border-bottom:0}
.dz-table tfoot tr:first-child td{border-top:1px solid var(--line)}
.dz-table tfoot .lbl-t{text-align:right;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.dz-table tfoot .val-t{text-align:right;font-weight:600;white-space:nowrap}
.dz-table tfoot tr.total td{padding-top:16px;padding-bottom:16px;background:rgba(29,78,216,.06);border-top:1px solid var(--line)}
.dz-table tfoot tr.total .lbl-t{color:var(--ink)}
.dz-table tfoot tr.total .val-t{font-size:16px;font-weight:700;color:var(--b1)}

.dz-no{display:inline-block;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.dz-date{color:var(--mute);font-size:13px}
.dz-act{height:32px;display:inline-flex;align-items:center;padding:0 13px;border-radius:9px;font-size:12.5px;font-weight:600;
    color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);text-decoration:none;transition:.18s}
.dz-act:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

@media(max-width:760px){.dz-info{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Purchase Order</span>
            <h1 class="dz-title">{{ $po->po_number }}</h1>
            <p class="dz-sub">{{ $po->supplier->name ?? '-' }} · {{ $po->date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="dz-btn ghost sm">← Kembali</a>
    </div>

    <div class="dz-info">
        <div class="dz-glass dz-box">
            <div class="dz-k">Status</div>
            <span class="dz-badge {{ $po->status }}">{{ str_replace('_', ' ', $po->status) }}</span>
        </div>
        <div class="dz-glass dz-box">
            <div class="dz-k">Total</div>
            <div class="dz-v big">Rp {{ number_format($po->total, 0, ',', '.') }}</div>
        </div>
        <div class="dz-glass dz-box">
            <div class="dz-k">Expected Date</div>
            @if($po->expected_date)
                <div class="dz-v">{{ $po->expected_date->format('d M Y') }}</div>
            @else
                <div class="dz-v dz-muted">-</div>
            @endif
        </div>
    </div>

    @if($po->status === 'draft')
        <div class="dz-act-bar">
            <form method="POST" action="{{ route('purchase-orders.confirm', $po) }}">
                @csrf
                <button class="dz-btn pri" onclick="return confirm('Konfirmasi PO ini?')">Konfirmasi PO</button>
            </form>
        </div>
    @endif

    <h2 class="dz-sec">Item Pesanan</h2>
    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Produk/Deskripsi</th>
                        <th class="num">Qty</th>
                        <th class="num">Diterima</th>
                        <th class="num">Harga</th>
                        <th class="num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($po->items as $i)
                    <tr>
                        <td class="dz-item">{{ $i->description }}<small>{{ $i->product->name ?? '-' }}</small></td>
                        <td class="num">{{ number_format($i->qty, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format($i->received_qty, 2, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($i->price, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($i->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="lbl-t">Subtotal</td>
                        <td class="val-t">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($po->discount > 0)
                        <tr>
                            <td colspan="4" class="lbl-t">Diskon</td>
                            <td class="val-t">- Rp {{ number_format($po->discount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($po->tax > 0)
                        <tr>
                            <td colspan="4" class="lbl-t">PPN Masukan</td>
                            <td class="val-t">Rp {{ number_format($po->tax, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td colspan="4" class="lbl-t">Total</td>
                        <td class="val-t">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($po->goodsReceipts->count() > 0)
        <h2 class="dz-sec">Penerimaan Terkait</h2>
        <div class="dz-glass dz-card">
            <div class="dz-scroll">
                <table class="dz-table" style="min-width:520px">
                    <thead>
                        <tr>
                            <th>No. GR</th>
                            <th>Tanggal</th>
                            <th class="center">Status</th>
                            <th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($po->goodsReceipts as $gr)
                        <tr>
                            <td><span class="dz-no">{{ $gr->gr_number }}</span></td>
                            <td class="dz-date">{{ $gr->date->format('d/m/Y') }}</td>
                            <td class="center"><span class="dz-badge {{ $gr->status === 'received' ? 'received' : 'draft' }}">{{ $gr->status }}</span></td>
                            <td class="center"><a href="{{ route('goods-receipts.show', $gr) }}" class="dz-act">Lihat</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
        <div class="ln-right">

            @if($activeCompany && $allCompanies->count() > 0)
                <div class="ln-company-wrap" id="companyDropdown">
                    <button type="button" class="ln-company" onclick="document.getElementById('companyDropdown').classList.toggle('is-open')" title="Ganti perusahaan">
                        <span>{{ $activeCompany->name }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="ln-company-menu">
                        @foreach($allCompanies as $co)
                            @php $isLocked = in_array($co->id, $lockedIds, true); @endphp
                            @if($co->id == $activeCompany->id)
                                <div class="ln-company-item active">
                                    <span>{{ $co->name }}</span>
                                    <span style="font-size:10px;color:var(--ok);font-weight:700;">Aktif</span>
                                </div>
                            @elseif($isLocked)
                                <a href="{{ route('subscription.index') }}" class="ln-company-item locked" title="Upgrade ke Pro untuk mengakses company ini">
                                    <span>{{ $co->name }}</span>
                                    <span style="font-size:11px;">🔒</span>
                                </a>
                            @else
                                <a href="{{ route('companies.switch', $co->id) }}" class="ln-company-item">
                                    <span>{{ $co->name }}</span>
                                </a>
                            @endif
                        @endforeach
                        <div class="ln-company-divider"></div>
                        <a href="{{ route('companies.index') }}" class="ln-company-item" style="font-size:12px;color:var(--mute);">
                            <span>Kelola Company</span>
                        </a>
                    </div>
                </div>
            @elseif($activeCompany)
                <a href="{{ route('companies.index') }}" class="ln-company" title="Kelola company">
                    <span>{{ $activeCompany->name }}</span>
                </a>
            @endif

            <div class="relative" id="notifWrapper">
                <button class="ln-hlink relative" onclick="toggleNotif()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="notifBadge" class="absolute top-0 right-0 hidden bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">0</span>
                </button>
                <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                    <div class="p-4 border-b font-bold text-sm">Notifikasi</div>
                    <div id="notifList" class="max-h-64 overflow-y-auto p-2 text-sm text-gray-600">
                    </div>
                </div>
            </div>

            @if($showBranchDropdown)
                <div class="ln-branch-wrap" id="branchDropdown">
                    <button type="button" class="ln-branch-btn" onclick="document.getElementById('branchDropdown').classList.toggle('is-open')">
                        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $activeBranch ? Str::limit($activeBranch->name, 15) : 'Konsolidasi' }}
                    </button>
                    <div class="ln-branch-menu">
                        @if(!$isStaff)
                            <a href="{{ route('branches.switch', 0) }}" class="ln-branch-item {{ !session('branch_id') ? 'active' : '' }}">
                                <span>Semua Cabang (Konsolidasi)</span>
                            </a>
                            <div class="ln-branch-divider"></div>
                        @endif

                        @foreach($branches as $b)
                            <a href="{{ route('branches.switch', $b) }}" class="ln-branch-item {{ $activeBranch && $activeBranch->id == $b->id ? 'active' : '' }}">
                                <span>{{ $b->name }}</span>
                                @if($b->is_main)
                                    <span style="font-size: 10px; opacity: .6;">Pusat</span>
                                @endif
                            </a>
                        @endforeach

                        @if(!$isStaff)
                            <div class="ln-branch-divider"></div>
                            <a href="{{ route('branches.index') }}" class="ln-branch-item" style="color: var(--mute); font-size: 12px;">
                                Kelola Cabang...
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($isPro && $branches->count() === 1)
                <div class="ln-branch-btn" style="cursor: default; background: transparent; border: none; color: var(--soft);">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $branches->first()->name }}
                </div>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="ln-hlink">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ln-hlogout">Logout</button>
            </form>
        </div>
</div>

@endsection