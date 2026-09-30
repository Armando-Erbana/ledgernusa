@extends('layouts.app')
@section('title', 'Company')
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
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-btn.sm{height:36px;padding:0 16px;font-size:12.5px;border-radius:10px}

/* list */
.dz-list{display:grid;gap:14px}
.dz-co{display:flex;align-items:center;gap:16px;padding:18px 20px;transition:.2s}
.dz-co:hover{transform:translateY(-2px);box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 16px 34px -14px rgba(29,78,216,.25),0 0 0 1px rgba(29,78,216,.14)}
.dz-co.on{box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 16px 34px -14px rgba(29,78,216,.3),0 0 0 2px var(--b1)}
.dz-av{flex:none;width:48px;height:48px;border-radius:15px;display:grid;place-items:center;font-size:15px;font-weight:700;color:var(--b1);
    background:linear-gradient(135deg,rgba(29,78,216,.12),rgba(6,182,212,.18));border:1px solid rgba(29,78,216,.14)}
.dz-co.on .dz-av{color:#fff;border:0;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-info{flex:1;min-width:0}
.dz-name{font-size:15.5px;font-weight:700;letter-spacing:-.01em;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.dz-meta{display:flex;align-items:center;gap:8px;margin-top:6px;flex-wrap:wrap}
.dz-chip{padding:3px 9px;border-radius:7px;font-size:11.5px;font-weight:600;color:var(--mute);background:rgba(15,30,61,.06)}
.dz-chip.role{color:var(--b1);background:rgba(29,78,216,.08);text-transform:capitalize}

.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:999px;font-size:12px;font-weight:600;color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}

@media(max-width:560px){.dz-title{font-size:23px}.dz-co{padding:16px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Pengaturan</span>
            <h1 class="dz-title">Daftar Company</h1>
            <p class="dz-sub">Pilih perusahaan yang ingin dikelola</p>
        </div>
        <div class="dz-head-r">
            <span class="dz-count">{{ number_format($companies->count()) }} company</span>
            <a href="{{ route('companies.create') }}" class="dz-btn pri">+ Baru</a>
        </div>
    </div>

    <div class="dz-list">
        @foreach($companies as $c)
            @php $isActive = session('company_id') == $c->id; @endphp
            <div class="dz-glass dz-co {{ $isActive ? 'on' : '' }}">
                <span class="dz-av">{{ strtoupper(mb_substr($c->name, 0, 2)) }}</span>
                <div class="dz-info">
                    <div class="dz-name">{{ $c->name }}</div>
                    <div class="dz-meta">
                        <span class="dz-chip">{{ $c->currency }}</span>
                        <span class="dz-chip role">Role: {{ $c->pivot->role }}</span>
                    </div>
                </div>
                @if($isActive)
                    <span class="dz-badge">Aktif</span>
                @else
                    <a href="{{ route('companies.switch', $c->id) }}" class="dz-btn ghost sm">Pilih</a>
                @endif
            </div>
        @endforeach
    </div>

</div>

@endsection