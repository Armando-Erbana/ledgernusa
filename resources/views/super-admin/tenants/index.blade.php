@extends('super-admin.layout')
@section('title', 'Daftar Tenant')

@section('content')

<style>
.tn{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.tn::before,.tn::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.tn::before{width:340px;height:340px;top:-90px;right:-60px;background:var(--b2);opacity:.22}
.tn::after{width:300px;height:300px;top:220px;left:-100px;background:var(--b1);opacity:.16}

/* header */
.tn-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.tn-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.tn-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.tn-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.tn-sub{margin:0;font-size:13.5px;color:var(--mute)}
.tn-total{display:flex;align-items:center;gap:12px;padding:12px 18px;border-radius:16px;color:#fff;
    background:linear-gradient(135deg,var(--navy),var(--b1));
    box-shadow:0 10px 30px -10px rgba(29,78,216,.55),inset 0 1px 0 rgba(255,255,255,.22)}
.tn-total b{font-size:26px;line-height:1;font-weight:700;letter-spacing:-.02em}
.tn-total span{font-size:11.5px;line-height:1.3;opacity:.75}

/* glass */
.tn-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* filter */
.tn-filter{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:14px;margin-bottom:18px}
.tn-field{display:flex;flex-direction:column;gap:6px}
.tn-field.grow{flex:1;min-width:220px}
.tn-field.sel{min-width:170px}
.tn-lbl{font-size:11.5px;font-weight:600;color:var(--mute);letter-spacing:.02em}
.tn-ctl{position:relative}
.tn-ctl svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--mute);pointer-events:none}
.tn-in,.tn-sel{width:100%;height:42px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s}
.tn-in{padding-left:38px}
.tn-in:focus,.tn-sel:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.tn-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;
    font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;border:0;transition:.18s}
.tn-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.tn-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.tn-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.tn-btn.sm{height:34px;padding:0 14px;font-size:12.5px;border-radius:10px}

/* table */
.tn-card{overflow:hidden}
.tn-scroll{overflow-x:auto}
.tn-table{width:100%;border-collapse:collapse;min-width:820px}
.tn-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.tn-table td{padding:16px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
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
.tn-users{display:inline-flex;align-items:center;gap:6px;font-weight:600}
.tn-users svg{width:15px;height:15px;color:var(--mute)}

/* status badge */
.tn-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.tn-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.tn-badge.active{color:#0E8F6B;background:rgba(16,185,129,.12)}
.tn-badge.trial{color:#0E7490;background:rgba(6,182,212,.14)}
.tn-badge.expired{color:#B45309;background:rgba(245,158,11,.14)}
.tn-badge.cancelled{color:#B4233A;background:rgba(239,68,68,.11)}

/* empty + pager */
.tn-empty{text-align:center;padding:56px 20px !important;color:var(--mute)}
.tn-empty svg{width:40px;height:40px;margin:0 auto 10px;display:block;color:#B9C4DC}
.tn-pager{margin-top:18px}

@media(max-width:640px){.tn-title{font-size:23px}.tn-total{width:100%}}
</style>

<div class="tn">

    <div class="tn-head">
        <div>
            <span class="tn-eyebrow"><i></i>Super Admin</span>
            <h1 class="tn-title">Tenants</h1>
            <p class="tn-sub">Semua perusahaan yang menggunakan LedgerNusa</p>
        </div>
        <div class="tn-total">
            <b>{{ number_format($tenants->total()) }}</b>
            <span>Total<br>tenant</span>
        </div>
    </div>

    <form method="GET" class="tn-glass tn-filter">
        <div class="tn-field grow">
            <label class="tn-lbl">Cari</label>
            <div class="tn-ctl">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" class="tn-in" placeholder="Nama company...">
            </div>
        </div>
        <div class="tn-field sel">
            <label class="tn-lbl">Status</label>
            <select name="status" class="tn-sel">
                <option value="">Semua</option>
                @foreach(['trial','active','expired','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button class="tn-btn pri">Filter</button>
        @if(request()->hasAny(['q','status']))
            <a href="{{ route('super-admin.tenants.index') }}" class="tn-btn ghost">Reset</a>
        @endif
    </form>

    <div class="tn-glass tn-card">
        <div class="tn-scroll">
            <table class="tn-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Owner</th>
                        <th>Paket</th>
                        <th>Status</th>
                        <th>Berakhir</th>
                        <th>User</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                        @php
                            $owner = $tenant->users->where('pivot.role', 'owner')->first();
                            $sub = $tenant->subscription;
                        @endphp
                        <tr>
                            <td>
                                <div class="tn-co">
                                    <span class="tn-av">{{ strtoupper(mb_substr($tenant->name, 0, 2)) }}</span>
                                    <strong>{{ $tenant->name }}</strong>
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
                            <td style="font-size:12.5px;">{{ $sub?->ends_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <span class="tn-users">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $tenant->users->count() }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="tn-btn ghost sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="tn-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h6"/></svg>
                                Tidak ada tenant
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="tn-pager">{{ $tenants->withQueryString()->links() }}</div>

</div>

@endsection