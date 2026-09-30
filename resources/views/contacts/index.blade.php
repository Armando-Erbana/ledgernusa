@extends('layouts.app')
@section('title', 'Kontak')
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

.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:620px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.center{text-align:center}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.center{text-align:center;white-space:nowrap}

.dz-co{display:flex;align-items:center;gap:12px}
.dz-av{flex:none;width:38px;height:38px;border-radius:12px;display:grid;place-items:center;font-size:13px;font-weight:700;color:var(--b1);
    background:linear-gradient(135deg,rgba(29,78,216,.12),rgba(6,182,212,.18));border:1px solid rgba(29,78,216,.14)}
.dz-co strong{font-weight:600}
.dz-muted{color:#A3AEC6}
.dz-tel{color:var(--ink)}

.dz-type{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize;
    color:var(--mute);background:rgba(15,30,61,.06)}
.dz-type::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-type.customer,.dz-type.pelanggan{color:#1D4ED8;background:rgba(29,78,216,.09)}
.dz-type.vendor,.dz-type.supplier{color:#0E7490;background:rgba(6,182,212,.14)}

.dz-acts{display:inline-flex;align-items:center;gap:6px}
.dz-acts form{display:inline;margin:0}
.dz-act{height:32px;display:inline-flex;align-items:center;padding:0 13px;border-radius:9px;font-size:12.5px;font-weight:600;
    color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);text-decoration:none;cursor:pointer;transition:.18s}
.dz-act:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-act.del{color:#B4233A}
.dz-act.del:hover{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.35);color:#B4233A}

.dz-empty{text-align:center;padding:56px 20px !important;color:var(--mute)}
.dz-empty svg{width:40px;height:40px;margin:0 auto 10px;display:block;color:#B9C4DC}

@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Master Data</span>
            <h1 class="dz-title">Kontak</h1>
            <p class="dz-sub">Pelanggan dan pemasok perusahaan Anda</p>
        </div>
        <div class="dz-head-r">
            <span class="dz-count">{{ number_format(method_exists($contacts, 'total') ? $contacts->total() : $contacts->count()) }} kontak</span>
            <a href="{{ route('contacts.create') }}" class="dz-btn pri">+ Kontak</a>
        </div>
    </div>

    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Telepon</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($contacts as $c)
                    <tr>
                        <td>
                            <div class="dz-co">
                                <span class="dz-av">{{ strtoupper(mb_substr($c->name, 0, 2)) }}</span>
                                <strong>{{ $c->name }}</strong>
                            </div>
                        </td>
                        <td><span class="dz-type {{ $c->type }}">{{ $c->type }}</span></td>
                        <td class="dz-tel">{!! $c->phone ? e($c->phone) : '<span class="dz-muted">—</span>' !!}</td>
                        <td class="center">
                            <div class="dz-acts">
                                <a href="{{ route('contacts.edit', $c) }}" class="dz-act">Edit</a>
                                <form method="POST" action="{{ route('contacts.destroy', $c) }}" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="dz-act del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="dz-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Belum ada kontak
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection