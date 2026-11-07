@extends('super-admin.layout')
@section('title', 'Detail Tenant')

@section('content')

@php
    $sub = $tenant->subscription ?? null;
    $isActive = $sub && $sub->ends_at && $sub->ends_at->isFuture() && in_array($sub->status, ['active', 'trial']);
    $tenantStatus = $tenant->status ?? 'active';
@endphp

<style>
.tn{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;max-width:1024px;margin:0 auto;font-feature-settings:"tnum"}
.tn::before,.tn::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.tn::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.tn::after{width:280px;height:280px;top:420px;left:-90px;background:var(--b1);opacity:.14}

.tn-back{display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;font-size:13px;font-weight:600;color:var(--mute);text-decoration:none;transition:.18s}
.tn-back:hover{color:var(--b1)}

.tn-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* header */
.tn-head{padding:26px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap}
.tn-id{display:flex;align-items:center;gap:16px;min-width:0}
.tn-av{flex:none;width:56px;height:56px;border-radius:17px;display:grid;place-items:center;font-size:18px;font-weight:700;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 10px 22px -10px rgba(29,78,216,.65),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-name{margin:0;font-size:24px;font-weight:700;letter-spacing:-.02em;color:var(--ink);line-height:1.2}
.tn-meta{display:flex;flex-wrap:wrap;gap:6px 18px;margin-top:8px;font-size:13px;color:var(--mute)}
.tn-meta b{color:var(--ink);font-weight:600}

/* badges */
.tn-badge{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:999px;font-size:12.5px;font-weight:600;text-transform:capitalize;
    color:var(--mute);background:rgba(15,30,61,.06)}
.tn-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.tn-badge.active{color:#0E8F6B;background:rgba(16,185,129,.12)}
.tn-badge.trial{color:#0E7490;background:rgba(6,182,212,.14)}
.tn-badge.expired,.tn-badge.suspended,.tn-badge.cancelled{color:#B4233A;background:rgba(239,68,68,.1)}
.tn-badge.none{color:#B45309;background:rgba(245,158,11,.14)}
.tn-badge.role{padding:4px 10px;font-size:12px;color:var(--b1);background:rgba(29,78,216,.08)}
.tn-badge.role::before{display:none}

/* cards */
.tn-card{padding:24px;margin-bottom:18px}
.tn-ct{display:flex;align-items:center;gap:10px;margin:0 0 18px;font-size:16px;font-weight:700;color:var(--ink)}
.tn-ct::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.tn-ct small{margin-left:auto;font-size:12.5px;font-weight:600;color:var(--mute)}

.tn-kv{display:grid;grid-template-columns:repeat(4,1fr);gap:0}
.tn-kv > div{padding:2px 20px;border-left:1px solid var(--line)}
.tn-kv > div:first-child{padding-left:0;border-left:0}
.tn-k{font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.tn-v{margin-top:6px;font-size:15px;font-weight:600;color:var(--ink)}
.tn-v small{display:block;margin-top:2px;font-size:12px;font-weight:500;color:var(--mute)}
.tn-v .dash{color:#A3AEC6}

/* actions */
.tn-acts{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:22px;padding-top:22px;border-top:1px solid var(--line)}
.tn-act{padding:18px;border-radius:16px;background:rgba(255,255,255,.55);border:1px solid var(--line)}
.tn-act h3{margin:0 0 4px;font-size:14px;font-weight:700;color:var(--ink)}
.tn-act p{margin:0 0 14px;font-size:12.5px;color:var(--mute)}
.tn-row{display:flex;gap:10px}
.tn-in,.tn-sel{flex:1;min-width:0;height:42px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.85);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.tn-in:focus,.tn-sel:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.tn-in{max-width:120px;text-align:right;font-weight:600}
.tn-lbl{display:block;margin-bottom:6px;font-size:11.5px;font-weight:600;color:var(--mute)}

.tn-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;white-space:nowrap;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.tn-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.tn-btn.ghost{color:var(--ink);background:rgba(255,255,255,.8);border:1px solid var(--line)}
.tn-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.tn-btn.ok{color:#fff;background:#0E8F6B;box-shadow:0 8px 18px -8px rgba(14,143,107,.6)}
.tn-btn.ok:hover{filter:brightness(1.07)}
.tn-btn.danger{color:#B4233A;background:rgba(255,255,255,.8);border:1px solid rgba(239,68,68,.35)}
.tn-btn.danger:hover{color:#fff;background:#C8283F;border-color:#C8283F}

/* zona akses */
.tn-zone{margin-top:22px;padding-top:22px;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap}
.tn-zone h3{margin:0 0 3px;font-size:14px;font-weight:700;color:var(--ink)}
.tn-zone p{margin:0;font-size:12.5px;color:var(--mute);max-width:520px}
.tn-zone form{margin:0}

/* users */
.tn-scroll{overflow-x:auto;margin:0 -24px;padding:0 24px}
.tn-table{width:100%;border-collapse:collapse;min-width:560px}
.tn-table th{padding:12px 14px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);border-bottom:1px solid var(--line);white-space:nowrap}
.tn-table td{padding:14px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.tn-table tbody tr{transition:background .15s}
.tn-table tbody tr:hover{background:rgba(29,78,216,.045)}
.tn-table tbody tr:last-child td{border-bottom:0}
.tn-u{display:flex;align-items:center;gap:12px}
.tn-ua{flex:none;width:34px;height:34px;border-radius:11px;display:grid;place-items:center;font-size:12px;font-weight:700;color:var(--b1);
    background:linear-gradient(135deg,rgba(29,78,216,.12),rgba(6,182,212,.18));border:1px solid rgba(29,78,216,.14)}
.tn-u strong{font-weight:600}
.tn-mail{color:var(--mute)}
.tn-date{color:var(--mute);font-size:13px;white-space:nowrap}
.tn-empty{padding:28px 0 6px;text-align:center;font-size:13.5px;color:var(--mute)}

@media(max-width:860px){.tn-kv{grid-template-columns:repeat(2,1fr);row-gap:18px}.tn-kv > div:nth-child(3){padding-left:0;border-left:0}.tn-acts{grid-template-columns:1fr}}
@media(max-width:560px){.tn-head,.tn-card{padding:20px}.tn-name{font-size:20px}.tn-row{flex-direction:column}.tn-in{max-width:none;text-align:left}.tn-btn{width:100%}.tn-scroll{margin:0 -20px;padding:0 20px}}
</style>

<div class="tn">

    <a href="{{ route('super-admin.tenants.index') }}" class="tn-back">← Kembali ke daftar tenant</a>

    {{-- HEADER PERUSAHAAN --}}
    <div class="tn-glass tn-head">
        <div class="tn-id">
            <span class="tn-av">{{ strtoupper(mb_substr($tenant->name, 0, 2)) }}</span>
            <div>
                <h1 class="tn-name">{{ $tenant->name }}</h1>
                <div class="tn-meta">
                    <span>NPWP <b>{{ $tenant->npwp ?? '-' }}</b></span>
                    <span>Currency <b>{{ $tenant->currency ?? 'IDR' }}</b></span>
                    <span>Status <b>{{ strtoupper($tenantStatus) }}</b></span>
                </div>
            </div>
        </div>
        <div>
            @if($isActive)
                <span class="tn-badge active">Langganan Aktif</span>
            @elseif($sub)
                <span class="tn-badge expired">Expired</span>
            @else
                <span class="tn-badge none">Belum Langganan</span>
            @endif
        </div>
    </div>

    {{-- STATUS LANGGANAN --}}
    <div class="tn-glass tn-card">
        <h2 class="tn-ct">Status Langganan</h2>

        <div class="tn-kv">
            <div>
                <div class="tn-k">Paket</div>
                <div class="tn-v">{{ $sub?->plan?->name ?? '-' }}</div>
            </div>
            <div>
                <div class="tn-k">Status</div>
                <div class="tn-v">
                    @if($sub?->status)
                        <span class="tn-badge {{ $sub->status }}">{{ $sub->status }}</span>
                    @else
                        <span class="dash">-</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="tn-k">Mulai</div>
                <div class="tn-v">{{ $sub?->starts_at?->format('d M Y') ?? '-' }}</div>
            </div>
            <div>
                <div class="tn-k">Berakhir</div>
                <div class="tn-v">
                    {{ $sub?->ends_at?->format('d M Y') ?? '-' }}
                    @if($isActive)
                        <small>{{ $sub->daysRemaining() }} hari lagi</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- EXTEND + CHANGE PLAN --}}
        <div class="tn-acts">
            <div class="tn-act">
                <h3>Perpanjang Manual</h3>
                <p>Tambahkan masa aktif langganan.</p>
                <form method="POST" action="{{ route('super-admin.tenants.extend', $tenant) }}">
                    @csrf
                    <label class="tn-lbl">Tambah Hari</label>
                    <div class="tn-row">
                        <input type="number" name="days" value="30" min="1" max="3650" required class="tn-in">
                        <button class="tn-btn pri">Perpanjang</button>
                    </div>
                </form>
            </div>

            <div class="tn-act">
                <h3>Ganti Paket</h3>
                <p>Pindahkan tenant ke paket lain.</p>
                <form method="POST" action="{{ route('super-admin.tenants.change-plan', $tenant) }}">
                    @csrf
                    <label class="tn-lbl">Pilih Paket Baru</label>
                    <div class="tn-row">
                        <select name="plan_id" required class="tn-sel">
                            @foreach(\App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get() as $plan)
                                <option value="{{ $plan->id }}" @selected($sub?->plan_id == $plan->id)>
                                    {{ $plan->name }} — Rp {{ number_format($plan->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <button class="tn-btn ghost">Ganti Paket</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SUSPEND / REACTIVATE --}}
        <div class="tn-zone">
            @if($tenant->status === 'suspended')
                <div>
                    <h3>Tenant sedang disuspend</h3>
                    <p>User perusahaan ini tidak bisa mengakses aplikasi sampai diaktifkan kembali.</p>
                </div>
                <form method="POST" action="{{ route('super-admin.tenants.reactivate', $tenant) }}">
                    @csrf
                    <button class="tn-btn ok">Aktifkan Kembali</button>
                </form>
            @else
                <div>
                    <h3>Suspend Tenant</h3>
                    <p>Hentikan akses semua user perusahaan ini. Data tetap aman dan bisa diaktifkan lagi kapan saja.</p>
                </div>
                <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant) }}"
                      onsubmit="return confirm('Suspend tenant ini? User tidak akan bisa akses.')">
                    @csrf
                    <button class="tn-btn danger">Suspend Tenant</button>
                </form>
            @endif
        </div>
    </div>

    {{-- DAFTAR USER --}}
    <div class="tn-glass tn-card">
        <h2 class="tn-ct">Daftar User <small>{{ $tenant->users?->count() ?? 0 }} user</small></h2>

        @if($tenant->users && $tenant->users->count() > 0)
            <div class="tn-scroll">
                <table class="tn-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenant->users as $user)
                            <tr>
                                <td>
                                    <div class="tn-u">
                                        <span class="tn-ua">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td class="tn-mail">{{ $user->email }}</td>
                                <td>
                                    @if($user->pivot->role ?? null)
                                        <span class="tn-badge role">{{ $user->pivot->role }}</span>
                                    @else
                                        <span class="dash" style="color:#A3AEC6">-</span>
                                    @endif
                                </td>
                                <td class="tn-date">{{ $user->created_at?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="tn-empty">Belum ada user terdaftar di perusahaan ini.</p>
        @endif
    </div>

</div>

@endsection