@extends('layouts.app')
@section('title', 'Company Baru')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:260px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-form{max-width:640px;padding:26px;display:flex;flex-direction:column;gap:18px}
.dz-row{display:grid;grid-template-columns:1fr 160px;gap:16px}
.dz-field{display:flex;flex-direction:column;gap:7px}
.dz-lbl{font-size:12px;font-weight:600;color:var(--mute);letter-spacing:.02em}

.dz-in,.dz-ta{width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-in{height:42px}
.dz-ta{padding:11px 14px;resize:vertical;min-height:96px}
.dz-in:focus,.dz-ta:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-in.cur{text-transform:uppercase;font-weight:600;letter-spacing:.04em}

.dz-actions{padding-top:18px;border-top:1px solid var(--line)}
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 24px;border-radius:12px;
    font-size:13px;font-weight:600;border:0;cursor:pointer;transition:.18s;color:#fff;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn:hover{transform:translateY(-1px);filter:brightness(1.05)}

@media(max-width:560px){.dz-title{font-size:23px}.dz-row{grid-template-columns:1fr}.dz-form{padding:18px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Pengaturan</span>
    <h1 class="dz-title">Company Baru</h1>
    <p class="dz-sub">Tambahkan perusahaan baru untuk dikelola pembukuannya</p>

    <form method="POST" action="{{ route('companies.store') }}" class="dz-glass dz-form">
        @csrf
        <div class="dz-row">
            <div class="dz-field">
                <label class="dz-lbl">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="dz-in">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', 'IDR') }}" required class="dz-in cur">
            </div>
        </div>
        <div class="dz-field">
            <label class="dz-lbl">Alamat</label>
            <textarea name="address" class="dz-ta">{{ old('address') }}</textarea>
        </div>
        <div class="dz-actions">
            <button class="dz-btn">Simpan</button>
        </div>
    </form>

</div>

@endsection