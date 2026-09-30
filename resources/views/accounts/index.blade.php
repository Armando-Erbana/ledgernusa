@extends('layouts.app')
@section('title', 'COA')
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
.dz-head-r{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
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

.dz-card{overflow:hidden}
.dz-scroll{overflow-x:auto}
.dz-table{width:100%;border-collapse:collapse;min-width:680px}
.dz-table th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.025);border-bottom:1px solid var(--line);white-space:nowrap}
.dz-table th.num{text-align:right}
.dz-table th.center{text-align:center}
.dz-table td{padding:14px 20px;font-size:13.5px;color:var(--ink);border-bottom:1px solid var(--line);vertical-align:middle}
.dz-table tbody tr{transition:background .15s}
.dz-table tbody tr:hover{background:rgba(29,78,216,.045)}
.dz-table tbody tr:last-child td{border-bottom:0}
.dz-table td.num{text-align:right;font-weight:600;white-space:nowrap}
.dz-table td.center{text-align:center;white-space:nowrap}
.dz-neg{color:#B4233A}

.dz-code{display:inline-block;padding:4px 10px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:12.5px;font-weight:600;color:var(--b1);background:rgba(29,78,216,.08)}

/* tipe akun */
.dz-type{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize;
    color:var(--mute);background:rgba(15,30,61,.06)}
.dz-type::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-type.asset,.dz-type.aset{color:#1D4ED8;background:rgba(29,78,216,.09)}
.dz-type.liability,.dz-type.kewajiban,.dz-type.utang{color:#B45309;background:rgba(245,158,11,.14)}
.dz-type.equity,.dz-type.modal,.dz-type.ekuitas{color:#5B47D6;background:rgba(91,71,214,.1)}
.dz-type.revenue,.dz-type.income,.dz-type.pendapatan{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-type.expense,.dz-type.beban,.dz-type.biaya{color:#B4233A;background:rgba(239,68,68,.1)}

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
            <span class="dz-eyebrow"><i></i>Akuntansi</span>
            <h1 class="dz-title">Chart of Accounts</h1>
            <p class="dz-sub">Daftar akun dan saldo berjalan</p>
        </div>
        <div class="dz-head-r">
            <span class="dz-count">{{ number_format(method_exists($accounts, 'total') ? $accounts->total() : $accounts->count()) }} akun</span>
            <a href="{{ route('accounts.import') }}" class="dz-btn ghost">Import CSV</a>
            <a href="{{ route('accounts.create') }}" class="dz-btn pri">+ Akun</a>
        </div>
    </div>

    <div class="dz-glass dz-card">
        <div class="dz-scroll">
            <table class="dz-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th class="num">Saldo</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $a)
                        <tr>
                            <td><span class="dz-code">{{ $a->code }}</span></td>
                            <td>{{ $a->name }}</td>
                            <td><span class="dz-type {{ strtolower($a->type) }}">{{ $a->type }}</span></td>
                            <td class="num {{ $a->balance < 0 ? 'dz-neg' : '' }}">Rp {{ number_format($a->balance, 0, ',', '.') }}</td>
                            <td class="center">
                                <div class="dz-acts">
                                    <a href="{{ route('accounts.edit', $a) }}" class="dz-act">Edit</a>
                                    <form method="POST" action="{{ route('accounts.destroy', $a) }}" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="dz-act del">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="dz-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20V10M12 20V4M19 20v-7"/></svg>
                                Belum ada akun
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection