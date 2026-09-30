@extends('layouts.app')
@section('title', 'Jurnal Umum')
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
.dz-table{width:100%;border-collapse:collapse;min-width:760px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table td{padding:15px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table .num,.dz-table th.num{text-align:right}
.dz-table .num{font-weight:600}
.dz-table .center,.dz-table th.center{text-align:center}
.dz-nowrap{white-space:nowrap}
.dz-date{color:var(--mute);font-size:13px}
.dz-ref{display:inline-block;padding:4px 10px;border-radius:8px;font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}
.dz-desc{color:var(--mute)}
.dz-muted{color:#A3AEC6}

.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.posted{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.draft{color:#B45309;background:rgba(245,158,11,.14)}

.dz-acts{display:inline-flex;align-items:center;gap:6px}
.dz-acts form{display:inline;margin:0}
.dz-act{height:32px;display:inline-flex;align-items:center;padding:0 12px;border-radius:9px;font-size:12.5px;font-weight:600;
    color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);text-decoration:none;cursor:pointer;transition:.18s}
.dz-act:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-act.post{color:#fff;border:0;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 6px 14px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-act.post:hover{color:#fff;filter:brightness(1.07)}

.dz-empty{text-align:center;padding:56px 20px !important;color:var(--mute)}
.dz-empty svg{width:40px;height:40px;margin:0 auto 10px;display:block;color:#B9C4DC}
.dz-pager{margin-top:18px}

@media(max-width:560px){.dz-title{font-size:23px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Akuntansi</span>
            <h1 class="dz-title">Jurnal Umum</h1>
            <p class="dz-sub">Semua transaksi jurnal yang tercatat</p>
        </div>
        <div class="dz-head-r">
            @if(method_exists($journals, 'total'))
                <span class="dz-count">{{ number_format($journals->total()) }} jurnal</span>
            @endif
            <a href="{{ route('journals.create') }}" class="dz-btn pri">+ Jurnal</a>
        </div>
    </div>

    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Ref</th>
                        <th>Deskripsi</th>
                        <th class="num">Total</th>
                        <th class="center">Status</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($journals as $j)
                    <tr>
                        <td class="dz-nowrap dz-date">{{ $j->date->format('d/m/Y') }}</td>
                        <td>
                            @if($j->reference)
                                <span class="dz-ref">{{ $j->reference }}</span>
                            @else
                                <span class="dz-muted">—</span>
                            @endif
                        </td>
                        <td class="dz-desc">{{ \Illuminate\Support\Str::limit($j->description, 40) }}</td>
                        <td class="num dz-nowrap">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                        <td class="center">
                            <span class="dz-badge {{ $j->status === 'posted' ? 'posted' : 'draft' }}">{{ $j->status }}</span>
                        </td>
                        <td class="center dz-nowrap">
                            <div class="dz-acts">
                                <a href="{{ route('journals.show', $j) }}" class="dz-act">Lihat</a>
                                @if($j->status !== 'posted')
                                    <a href="{{ route('journals.edit', $j) }}" class="dz-act">Edit</a>
                                    <form method="POST" action="{{ route('journals.post', $j) }}">
                                        @csrf
                                        <button class="dz-act post">Posting</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="dz-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7"/></svg>
                            Belum ada jurnal
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dz-pager">{{ $journals->links() }}</div>

</div>

@endsection