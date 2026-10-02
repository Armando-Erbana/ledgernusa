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

.dz-form{max-width:680px;padding:26px;display:flex;flex-direction:column;gap:18px}
.dz-row{display:grid;grid-template-columns:1fr 160px;gap:16px}
.dz-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.dz-field{display:flex;flex-direction:column;gap:7px}
.dz-lbl{font-size:12px;font-weight:600;color:var(--mute);letter-spacing:.02em}
.dz-hint{font-size:11px;color:var(--mute);margin-top:2px}

.dz-in,.dz-ta,.dz-sel{width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-in,.dz-sel{height:42px}
.dz-ta{padding:11px 14px;resize:vertical;min-height:96px}
.dz-in:focus,.dz-ta:focus,.dz-sel:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-in.cur{text-transform:uppercase;font-weight:600;letter-spacing:.04em}

.dz-section{display:flex;align-items:center;gap:10px;margin-top:6px}
.dz-section::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}
.dz-section-lbl{font-size:13px;font-weight:700;color:var(--ink)}
.dz-section-sub{font-size:11.5px;color:var(--mute);margin-left:auto}

.dz-check{display:flex;align-items:center;gap:10px;padding:12px 14px;background:rgba(255,255,255,.6);border:1px solid var(--line);border-radius:12px;cursor:pointer;transition:.18s}
.dz-check:hover{background:#fff;border-color:rgba(29,78,216,.3)}
.dz-check input[type=checkbox]{width:16px;height:16px;accent-color:var(--b1);cursor:pointer;flex-shrink:0}
.dz-check-txt{display:flex;flex-direction:column;gap:2px}
.dz-check-title{font-size:13px;font-weight:600;color:var(--ink)}
.dz-check-desc{font-size:11.5px;color:var(--mute)}

.dz-tax-fields{display:none;flex-direction:column;gap:16px;padding:16px;background:rgba(29,78,216,.04);border:1px solid rgba(29,78,216,.12);border-radius:14px;margin-top:4px}
.dz-tax-fields.show{display:flex}

.dz-actions{padding-top:18px;border-top:1px solid var(--line);display:flex;gap:10px}
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 24px;border-radius:12px;
    font-size:13px;font-weight:600;border:0;cursor:pointer;transition:.18s;color:#fff;text-decoration:none;
    background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn-ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line);box-shadow:none}
.dz-btn-ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1);filter:none}

@media(max-width:560px){
    .dz-title{font-size:23px}
    .dz-row,.dz-row-2{grid-template-columns:1fr}
    .dz-form{padding:18px}
}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Pengaturan</span>
    <h1 class="dz-title">Company Baru</h1>
    <p class="dz-sub">Tambahkan perusahaan baru untuk dikelola pembukuannya</p>

    <form method="POST" action="{{ route('companies.store') }}" class="dz-glass dz-form">
        @csrf

        {{-- ============ IDENTITAS ============ --}}
        <div class="dz-row">
            <div class="dz-field">
                <label class="dz-lbl">Nama Perusahaan *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="dz-in" placeholder="PT Maju Jaya">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">Currency *</label>
                <input type="text" name="currency" value="{{ old('currency', 'IDR') }}" required class="dz-in cur" maxlength="8">
            </div>
        </div>

        <div class="dz-field">
            <label class="dz-lbl">Alamat</label>
            <textarea name="address" class="dz-ta" placeholder="Jl. Sudirman No. 1, Jakarta">{{ old('address') }}</textarea>
        </div>

        {{-- ============ PAJAK ============ --}}
        <div class="dz-section">
            <span class="dz-section-lbl">Pengaturan Pajak</span>
            <span class="dz-section-sub">Opsional</span>
        </div>

        <div class="dz-row-2">
            <div class="dz-field">
                <label class="dz-lbl">NPWP</label>
                <input type="text" name="npwp" value="{{ old('npwp') }}" class="dz-in" placeholder="00.000.000.0-000.000">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">e-FIN</label>
                <input type="text" name="efin" value="{{ old('efin') }}" class="dz-in" placeholder="Nomor e-FIN">
            </div>
        </div>

        <label class="dz-check" for="is_pkp">
            <input type="checkbox" name="is_pkp" id="is_pkp" value="1" @checked(old('is_pkp')) onchange="toggleTaxFields()">
            <span class="dz-check-txt">
                <span class="dz-check-title">Perusahaan ini PKP</span>
                <span class="dz-check-desc">Pengusaha Kena Pajak — akan otomatis menghitung PPN pada invoice penjualan</span>
            </span>
        </label>

        <div class="dz-tax-fields" id="taxFields">
            <div class="dz-row-2">
                <div class="dz-field">
                    <label class="dz-lbl">Tarif PPN (%)</label>
                    <input type="number" name="ppn_rate" value="{{ old('ppn_rate', 11) }}" min="0" max="100" step="0.01" class="dz-in">
                    <span class="dz-hint">Sesuai aturan DJP: 11% atau 12%</span>
                </div>
                <div class="dz-field">
                    <label class="dz-lbl">KPP</label>
                    <input type="text" name="tax_office" value="{{ old('tax_office') }}" class="dz-in" placeholder="KPP Jakarta Selatan">
                </div>
            </div>

            <label class="dz-check" for="ppn_included">
                <input type="checkbox" name="ppn_included" id="ppn_included" value="1" @checked(old('ppn_included'))>
                <span class="dz-check-txt">
                    <span class="dz-check-title">Harga sudah termasuk PPN</span>
                    <span class="dz-check-desc">Jika dicentang, PPN akan dihitung mundur dari total harga</span>
                </span>
            </label>
        </div>

        {{-- ============ ACTIONS ============ --}}
        <div class="dz-actions">
            <button type="submit" class="dz-btn">Simpan Company</button>
            <a href="{{ route('companies.index') }}" class="dz-btn dz-btn-ghost">Batal</a>
        </div>
    </form>

</div>

<script>
function toggleTaxFields() {
    const cb = document.getElementById('is_pkp');
    const fields = document.getElementById('taxFields');
    if (cb.checked) {
        fields.classList.add('show');
    } else {
        fields.classList.remove('show');
    }
}
// Init saat load (kalau old value ada yang checked)
document.addEventListener('DOMContentLoaded', toggleTaxFields);
</script>

@endsection