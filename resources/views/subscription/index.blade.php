@extends('layouts.app')
@section('title', 'Langganan')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:420px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* current plan */
.dz-cur{position:relative;overflow:hidden;padding:26px;margin-bottom:28px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;
    background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.dz-cur::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.5;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.06) 27px 28px)}
.dz-cur > *{position:relative}
.dz-cur .k{font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.7)}
.dz-cur .plan{margin:6px 0 10px;font-size:30px;font-weight:700;letter-spacing:-.02em}
.dz-cur .st{display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.75)}
.dz-right{text-align:right;padding:14px 20px;border-radius:16px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.2);min-width:160px}
.dz-right .k{font-size:12px;color:rgba(255,255,255,.72)}
.dz-right .big{margin-top:4px;font-size:28px;font-weight:700;letter-spacing:-.02em;line-height:1.1}
.dz-right .mid{margin-top:4px;font-size:18px;font-weight:600}

/* badges */
.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.posted{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}
.dz-cur .dz-badge.posted{color:#7CF0C8;background:rgba(255,255,255,.14)}
.dz-cur .dz-badge.draft{color:#FCD98A;background:rgba(255,255,255,.14)}

/* section */
.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}

/* plans */
.dz-plans{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:30px}
.dz-plan{position:relative;padding:24px;display:flex;flex-direction:column;transition:.2s}
.dz-plan:hover{transform:translateY(-3px)}
.dz-plan.on{border-color:transparent;box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 18px 38px -14px rgba(29,78,216,.32),0 0 0 2px var(--b1)}
.dz-tag{position:absolute;top:16px;right:16px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2))}
.dz-pn{font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--b1)}
.dz-price{margin:8px 0 18px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-price small{font-size:13px;font-weight:500;color:var(--mute)}
.dz-feat{list-style:none;margin:0 0 22px;padding:16px 0 0;border-top:1px solid var(--line);display:flex;flex-direction:column;gap:10px;flex:1}
.dz-feat li{display:flex;align-items:center;gap:10px;font-size:13px;color:var(--ink)}
.dz-feat svg{flex:none;width:16px;height:16px;padding:3px;border-radius:50%;color:var(--b1);background:rgba(29,78,216,.1)}

.dz-btn{width:100%;height:42px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;
    font-size:13px;font-weight:600;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

/* invoices */
.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:520px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table th.center{text-align:center}
.dz-table td{padding:14px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line)}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.dz-table td.center{text-align:center}
.dz-code{display:inline-block;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.dz-date{color:var(--mute);font-size:13px}

@media(max-width:860px){.dz-plans{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}.dz-right{text-align:left;width:100%}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Akun</span>
    <h1 class="dz-title">Langganan</h1>
    <p class="dz-sub">Kelola paket &amp; tagihan Anda</p>

    @if($subscription)
        <div class="dz-glass dz-cur">
            <div>
                <div class="k">Paket Saat Ini</div>
                <div class="plan">{{ $subscription->plan->name ?? '-' }}</div>
                <div class="st">
                    Status:
                    <span class="dz-badge {{ $subscription->status === 'active' ? 'posted' : 'draft' }}">
                        {{ $subscription->status }}
                    </span>
                </div>
            </div>
            <div class="dz-right">
                @if($subscription->status === 'trial')
                    <div class="k">Sisa trial</div>
                    <div class="big">{{ $subscription->daysRemaining() }} hari</div>
                @else
                    <div class="k">Berakhir</div>
                    <div class="mid">{{ $subscription->ends_at?->format('d M Y') }}</div>
                @endif
            </div>
        </div>
    @endif

    <h2 class="dz-sec">Paket Tersedia</h2>
    <div class="dz-plans">
        @foreach($plans as $plan)
            @php $isCurrent = $subscription && $subscription->plan_id === $plan->id; @endphp
            <div class="dz-glass dz-plan {{ $isCurrent ? 'on' : '' }}">
                @if($isCurrent)<span class="dz-tag">Aktif</span>@endif
                <div class="dz-pn">{{ $plan->name }}</div>
                <div class="dz-price">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}<small>/bulan</small></div>
                <ul class="dz-feat">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>{{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} user</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>{{ $plan->max_journals_per_month == -1 ? 'Unlimited' : number_format($plan->max_journals_per_month) }} jurnal/bulan</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>{{ $plan->max_companies == -1 ? 'Unlimited' : $plan->max_companies }} company</li>
                </ul>
                <button class="dz-btn {{ $isCurrent ? 'ghost' : 'pri' }}" onclick="alert('Fitur upgrade akan segera hadir. Hubungi admin untuk upgrade manual.')">
                    {{ $isCurrent ? 'Paket Aktif' : 'Pilih Paket' }}
                </button>
            </div>
        @endforeach
    </div>

    @if($invoices->count() > 0)
        <h2 class="dz-sec">Riwayat Tagihan</h2>
        <div class="dz-glass dz-card">
            <div class="dz-scroll">
                <table class="dz-table">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th class="num">Jumlah</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                            <tr>
                                <td><span class="dz-code">{{ $inv->invoice_number }}</span></td>
                                <td class="dz-date">{{ $inv->created_at->format('d/m/Y') }}</td>
                                <td class="num">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                <td class="center">
                                    <span class="dz-badge {{ $inv->status === 'paid' ? 'posted' : 'draft' }}">{{ $inv->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection