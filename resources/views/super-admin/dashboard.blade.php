@extends('super-admin.layout')
@section('title', 'Super Admin Dashboard')

@section('content')

<style>
.tn{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.tn::before,.tn::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.tn::before{width:340px;height:340px;top:-90px;right:-60px;background:var(--b2);opacity:.22}
.tn::after{width:300px;height:300px;top:360px;left:-100px;background:var(--b1);opacity:.16}

.tn-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.tn-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.tn-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.tn-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.tn-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* stats */
.tn-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.tn-stat{padding:20px;display:flex;flex-direction:column;gap:14px;transition:.2s}
.tn-stat:hover{transform:translateY(-2px)}
.tn-stat-top{display:flex;justify-content:space-between;align-items:center}
.tn-stat .lbl{font-size:12.5px;font-weight:600;color:var(--mute)}
.tn-ico{width:36px;height:36px;border-radius:11px;display:grid;place-items:center;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 6px 14px -6px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-ico svg{width:18px;height:18px}
.tn-stat .val{font-size:27px;font-weight:700;letter-spacing:-.02em;color:var(--ink);line-height:1}
.tn-stat .sub{font-size:12px;color:var(--mute)}
.tn-bar{height:5px;border-radius:5px;background:rgba(15,30,61,.07);overflow:hidden}
.tn-bar span{display:block;height:100%;border-radius:5px;background:linear-gradient(90deg,var(--b1),var(--b2))}

.tn-stat.hero{color:#fff;background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.tn-stat.hero .lbl,.tn-stat.hero .sub{color:rgba(255,255,255,.72)}
.tn-stat.hero .val{color:#fff}
.tn-stat.hero .tn-ico{background:rgba(255,255,255,.16);box-shadow:inset 0 1px 0 rgba(255,255,255,.3)}

/* cards */
.tn-card{overflow:hidden;margin-bottom:22px}
.tn-card-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:18px 20px;border-bottom:1px solid var(--line)}
.tn-card-title{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:var(--ink)}
.tn-card-title svg{width:18px;height:18px;color:var(--b1)}
.tn-scroll{overflow-x:auto}
.tn-table{width:100%;border-collapse:collapse;min-width:720px}
.tn-table th{padding:13px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.tn-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.tn-table tbody tr{transition:background .15s}
.tn-table tbody tr:hover{background:rgba(29,78,216,.045)}
.tn-table tbody tr:last-child td{border-bottom:0}

.tn-co{display:flex;align-items:center;gap:12px}
.tn-av{flex:none;width:38px;height:38px;border-radius:12px;display:grid;place-items:center;font-size:13px;font-weight:700;color:var(--b1);
    background:linear-gradient(135deg,rgba(29,78,216,.12),rgba(6,182,212,.18));border:1px solid rgba(29,78,216,.14)}
.tn-co strong{font-weight:600}
.tn-mail{display:block;font-size:11.5px;color:var(--mute);margin-top:2px}
.tn-plan{display:inline-block;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.tn-muted{color:#A3AEC6}
.tn-date{font-size:12.5px;color:var(--mute)}

.tn-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.tn-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.tn-badge.active{color:#0E8F6B;background:rgba(16,185,129,.12)}
.tn-badge.trial{color:#0E7490;background:rgba(6,182,212,.14)}
.tn-badge.expired{color:#B45309;background:rgba(245,158,11,.14)}
.tn-badge.cancelled{color:#B4233A;background:rgba(239,68,68,.11)}
.tn-count{padding:5px 12px;border-radius:999px;font-size:12px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.tn-left{display:inline-block;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:600;color:var(--ink);background:rgba(15,30,61,.06)}
.tn-left.urgent{color:#B4233A;background:rgba(239,68,68,.11)}

.tn-btn{height:34px;display:inline-flex;align-items:center;justify-content:center;padding:0 14px;border-radius:10px;
    font-size:12.5px;font-weight:600;cursor:pointer;text-decoration:none;border:0;transition:.18s}
.tn-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.tn-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.tn-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

.tn-empty{text-align:center;padding:52px 20px !important;color:var(--mute)}
.tn-empty svg{width:40px;height:40px;margin:0 auto 10px;display:block;color:#B9C4DC}

@media(max-width:1024px){.tn-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.tn-stats{grid-template-columns:1fr}.tn-title{font-size:23px}}
</style>

<div class="tn">

    <span class="tn-eyebrow"><i></i>Super Admin</span>
    <h1 class="tn-title">Dashboard</h1>
    <p class="tn-sub">Ringkasan bisnis LedgerNusa</p>

    {{-- Stat cards --}}
    @php $activePct = $totalCompanies > 0 ? min(100, round($activeCount / $totalCompanies * 100)) : 0; @endphp
    <div class="tn-stats">
        <div class="tn-glass tn-stat">
            <div class="tn-stat-top">
                <span class="lbl">Total Tenant</span>
                <span class="tn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h6"/></svg></span>
            </div>
            <div class="val">{{ number_format($totalCompanies) }}</div>
            <div class="sub">Perusahaan terdaftar</div>
        </div>

        <div class="tn-glass tn-stat">
            <div class="tn-stat-top">
                <span class="lbl">Total User</span>
                <span class="tn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            </div>
            <div class="val">{{ number_format($totalUsers) }}</div>
            <div class="sub">Exclude super admin</div>
        </div>

        <div class="tn-glass tn-stat">
            <div class="tn-stat-top">
                <span class="lbl">Langganan Aktif</span>
                <span class="tn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
            </div>
            <div class="val">{{ $activeCount }}</div>
            <div class="tn-bar"><span style="width:{{ $activePct }}%"></span></div>
            <div class="sub">{{ $trialCount }} trial · {{ $expiredCount }} expired</div>
        </div>

        <div class="tn-glass tn-stat hero">
            <div class="tn-stat-top">
                <span class="lbl">Estimasi MRR</span>
                <span class="tn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 17 6-6 4 4 8-8M15 7h6v6"/></svg></span>
            </div>
            <div class="val">Rp {{ number_format($mrr, 0, ',', '.') }}</div>
            <div class="sub">Dari langganan aktif</div>
        </div>
    </div>

    {{-- Expiring soon --}}
    @if($expiringSoon->count() > 0)
        <div class="tn-glass tn-card">
            <div class="tn-card-head">
                <div class="tn-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    Akan Berakhir 7 Hari ke Depan
                </div>
                <span class="tn-count">{{ $expiringSoon->count() }} tenant</span>
            </div>
            <div class="tn-scroll">
                <table class="tn-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Berakhir</th>
                            <th>Sisa</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoon as $sub)
                            @php $left = $sub->daysRemaining(); @endphp
                            <tr>
                                <td>
                                    <div class="tn-co">
                                        <span class="tn-av">{{ strtoupper(mb_substr($sub->company->name ?? '-', 0, 2)) }}</span>
                                        <strong>{{ $sub->company->name ?? '-' }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($sub->plan?->name)
                                        <span class="tn-plan">{{ $sub->plan->name }}</span>
                                    @else
                                        <span class="tn-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="tn-badge {{ $sub->status }}">{{ $sub->status }}</span></td>
                                <td class="tn-date">{{ $sub->ends_at?->format('d M Y') }}</td>
                                <td><span class="tn-left {{ $left <= 3 ? 'urgent' : '' }}">{{ $left }} hari</span></td>
                                <td style="text-align:right;">
                                    @if($sub->company)
                                        <a href="{{ route('super-admin.tenants.show', $sub->company) }}" class="tn-btn pri">Kelola</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Recent tenants --}}
    <div class="tn-glass tn-card">
        <div class="tn-card-head">
            <div class="tn-card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h6"/></svg>
                Tenant Terbaru
            </div>
            <a href="{{ route('super-admin.tenants.index') }}" class="tn-btn ghost">Lihat Semua →</a>
        </div>
        <div class="tn-scroll">
            <table class="tn-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Owner</th>
                        <th>Paket</th>
                        <th>Status</th>
                        <th>Daftar</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCompanies as $company)
                        @php
                            $owner = $company->users->where('pivot.role', 'owner')->first();
                            $sub = $company->subscription;
                        @endphp
                        <tr>
                            <td>
                                <div class="tn-co">
                                    <span class="tn-av">{{ strtoupper(mb_substr($company->name, 0, 2)) }}</span>
                                    <strong>{{ $company->name }}</strong>
                                </div>
                            </td>
                            <td>
                                {{ $owner->name ?? '-' }}
                                <span class="tn-mail">{{ $owner->email ?? '' }}</span>
                            </td>
                            <td>
                                @if($sub?->plan?->name)
                                    <span class="tn-plan">{{ $sub->plan->name }}</span>
                                @else
                                    <span class="tn-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($sub)
                                    <span class="tn-badge {{ $sub->status }}">{{ $sub->status }}</span>
                                @else
                                    <span class="tn-muted">—</span>
                                @endif
                            </td>
                            <td class="tn-date">{{ $company->created_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('super-admin.tenants.show', $company) }}" class="tn-btn ghost">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="tn-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h6"/></svg>
                                Belum ada tenant
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection