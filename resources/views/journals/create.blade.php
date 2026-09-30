@extends('layouts.app')
@section('title', 'Jurnal Baru')
@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:320px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-form{padding:24px;display:flex;flex-direction:column;gap:20px}
.dz-row2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.dz-field{display:flex;flex-direction:column;gap:7px}
.dz-lbl{font-size:12px;font-weight:600;color:var(--mute);letter-spacing:.02em}

.dz-in,.dz-sel,.dz-ta{width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-in,.dz-sel{height:42px}
.dz-ta{padding:11px 14px;resize:vertical;min-height:72px}
.dz-in:focus,.dz-sel:focus,.dz-ta:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-in.sm,.dz-sel.sm{height:38px;border-radius:10px;font-size:13px}
.dz-in.r{text-align:right;font-weight:600}

/* entries */
.dz-entries{border-radius:16px;border:1px solid var(--line);overflow:hidden;background:rgba(255,255,255,.5)}
.dz-scroll{overflow-x:auto}
.dz-et{width:100%;border-collapse:collapse;min-width:560px}
.dz-et th{padding:12px 14px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.03);border-bottom:1px solid var(--line)}
.dz-et th.num{text-align:right}
.dz-et td{padding:10px 14px;border-bottom:1px solid var(--line)}
.dz-et tbody tr:last-child td{border-bottom:0}
.dz-et tfoot td{padding:14px;font-size:14px;font-weight:700;color:var(--ink);background:rgba(29,78,216,.05);border-top:1px solid var(--line)}
.dz-et tfoot td.lbl-t{font-size:11px;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.dz-rm{width:32px;height:32px;display:inline-grid;place-items:center;border-radius:9px;border:1px solid transparent;
    background:transparent;color:#A3AEC6;cursor:pointer;font-size:13px;transition:.18s}
.dz-rm:hover{color:#B4233A;background:rgba(239,68,68,.1)}

/* bar bawah tabel */
.dz-tools{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.dz-add{height:38px;display:inline-flex;align-items:center;padding:0 16px;border-radius:10px;font-size:13px;font-weight:600;
    color:var(--b1);background:rgba(29,78,216,.07);border:1px dashed rgba(29,78,216,.35);cursor:pointer;transition:.18s}
.dz-add:hover{background:rgba(29,78,216,.12);border-style:solid}
.dz-bal{display:inline-flex;align-items:center;gap:7px;padding:7px 13px;border-radius:999px;font-size:12.5px;font-weight:600}
.dz-bal.ok{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-bal.bad{color:#B4233A;background:rgba(239,68,68,.1)}

/* actions */
.dz-actions{display:flex;align-items:center;gap:10px;padding-top:18px;border-top:1px solid var(--line)}
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 22px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}

@media(max-width:560px){.dz-title{font-size:23px}.dz-row2{grid-template-columns:1fr}.dz-form{padding:18px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Akuntansi</span>
    <h1 class="dz-title">Jurnal Baru</h1>
    <p class="dz-sub">Catat transaksi debit dan kredit yang saling seimbang</p>

    <form method="POST" action="{{ route('journals.store') }}" class="dz-glass dz-form">
        @csrf
        <div class="dz-row2">
            <div class="dz-field">
                <label class="dz-lbl">Tanggal</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="dz-in">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">Referensi</label>
                <input type="text" name="reference" value="{{ old('reference') }}" class="dz-in">
            </div>
        </div>

        <div class="dz-field">
            <label class="dz-lbl">Deskripsi</label>
            <textarea name="description" rows="2" class="dz-ta">{{ old('description') }}</textarea>
        </div>

        <div class="dz-entries">
            <div class="dz-scroll">
                <table class="dz-et">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="num" style="width: 9rem;">Debit</th>
                            <th class="num" style="width: 9rem;">Kredit</th>
                            <th style="width: 2.5rem;"></th>
                        </tr>
                    </thead>
                    <tbody id="entriesBody"></tbody>
                    <tfoot>
                        <tr>
                            <td class="lbl-t" style="text-align:right;">Total</td>
                            <td style="text-align:right;" id="totalDebit">0</td>
                            <td style="text-align:right;" id="totalCredit">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="dz-tools">
            <button type="button" onclick="addRow()" class="dz-add">+ Baris</button>
            <div id="balanceMsg"></div>
        </div>

        <div class="dz-actions">
            <button class="dz-btn pri">Simpan Draft</button>
            <a href="{{ route('journals.index') }}" class="dz-btn ghost">Batal</a>
        </div>
    </form>

</div>

<script>
const accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const body = document.getElementById('entriesBody');

function addRow() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="entries[][account_id]" class="dz-sel sm" required>
                <option value="">- Pilih Akun -</option>
                ${accounts.map(a => `<option value="${a.id}">${a.label}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" step="0.01" name="entries[][debit]" value="0" class="dz-in sm r debit" oninput="calc()"></td>
        <td><input type="number" step="0.01" name="entries[][credit]" value="0" class="dz-in sm r credit" oninput="calc()"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove(); calc()" class="dz-rm">✕</button></td>
    `;
    body.appendChild(tr);
    calc();
}

function calc() {
    let d = 0, c = 0;
    document.querySelectorAll('.debit').forEach(el => d += parseFloat(el.value) || 0);
    document.querySelectorAll('.credit').forEach(el => c += parseFloat(el.value) || 0);
    document.getElementById('totalDebit').textContent = d.toLocaleString('id-ID');
    document.getElementById('totalCredit').textContent = c.toLocaleString('id-ID');
    const msg = document.getElementById('balanceMsg');
    if (Math.abs(d - c) < 0.01 && d > 0) msg.innerHTML = '<span class="dz-bal ok">✓ Balance</span>';
    else msg.innerHTML = '<span class="dz-bal bad">✗ Belum balance · selisih ' + Math.abs(d - c).toLocaleString('id-ID') + '</span>';
}

addRow(); addRow();
</script>

@endsection