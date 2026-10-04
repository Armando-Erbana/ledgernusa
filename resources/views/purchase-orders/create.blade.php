@extends('layouts.app')
@section('title', 'PO Baru')
@section('content')

@php
    $productsJson = \App\Models\Product::where('company_id', session('company_id'))
        ->where('is_active', true)->orderBy('name')
        ->get(['id','code','name','cost_price'])
        ->map(fn($p) => ['id' => $p->id, 'code' => $p->code, 'name' => $p->name, 'cost_price' => (float) $p->cost_price]);
@endphp

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:320px;left:-90px;background:var(--b1);opacity:.14}

.dz-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-form{padding:24px;display:flex;flex-direction:column;gap:20px}
.dz-grid2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.dz-grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.dz-field{display:flex;flex-direction:column;gap:7px}
.dz-lbl{font-size:12px;font-weight:600;color:var(--mute);letter-spacing:.02em}

.dz-in,.dz-sel,.dz-ta{width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-in,.dz-sel{height:42px}
.dz-ta{padding:11px 14px;resize:vertical;min-height:72px}
.dz-in:focus,.dz-sel:focus,.dz-ta:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-in.sm,.dz-sel.sm{height:38px;border-radius:10px;font-size:13px}
.dz-in.r{text-align:right;font-weight:600}
/* hilangkan spinner angka supaya teks benar-benar rata kanan */
.dz-in[type=number]{-moz-appearance:textfield;appearance:textfield}
.dz-in[type=number]::-webkit-outer-spin-button,
.dz-in[type=number]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}

.dz-sec{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700;color:var(--ink);margin-bottom:12px}
.dz-sec::before{content:"";width:4px;height:14px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}

/* items */
.dz-entries{border-radius:16px;border:1px solid var(--line);overflow:hidden;background:rgba(255,255,255,.5)}
.dz-scroll{overflow-x:auto}
.dz-et{width:100%;border-collapse:collapse;min-width:900px}
.dz-et th{padding:12px 14px;text-align:left;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;
    color:var(--mute);background:rgba(15,30,61,.03);border-bottom:1px solid var(--line)}
.dz-et td{padding:10px 14px;border-bottom:1px solid var(--line);vertical-align:middle}
.dz-et tbody tr:last-child td{border-bottom:0}

/* angka rata kanan: 14px padding cell + 14px padding input = 28px,
   supaya header, subtotal, footer, dan teks di input satu garis */
.dz-et th.num{text-align:right;padding-right:28px}
.dz-et td.subtotal{text-align:right;font-weight:600;white-space:nowrap;padding-right:28px}

.dz-et tfoot td{padding:10px 14px;font-size:13.5px;color:var(--ink);border-bottom:0;vertical-align:middle}
.dz-et tfoot tr:first-child td{border-top:1px solid var(--line)}
.dz-et tfoot .lbl-t{text-align:right;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.dz-et tfoot .val-t{text-align:right;font-weight:600;white-space:nowrap;padding-right:28px}
.dz-et tfoot td.inp{padding-right:14px}
.dz-et tfoot tr.total td{padding-top:15px;padding-bottom:15px;background:rgba(29,78,216,.06);border-top:1px solid var(--line)}
.dz-et tfoot tr.total .lbl-t{color:var(--ink)}
.dz-et tfoot tr.total .val-t{font-size:16px;font-weight:700;color:var(--b1)}

.dz-rm{width:32px;height:32px;display:inline-grid;place-items:center;border-radius:9px;border:1px solid transparent;
    background:transparent;color:#A3AEC6;cursor:pointer;font-size:13px;transition:.18s}
.dz-rm:hover{color:#B4233A;background:rgba(239,68,68,.1)}
.dz-add{margin-top:12px;height:38px;display:inline-flex;align-items:center;padding:0 16px;border-radius:10px;font-size:13px;font-weight:600;
    color:var(--b1);background:rgba(29,78,216,.07);border:1px dashed rgba(29,78,216,.35);cursor:pointer;transition:.18s}
.dz-add:hover{background:rgba(29,78,216,.12);border-style:solid}

/* actions */
.dz-actions{display:flex;align-items:center;gap:10px;padding-top:18px;border-top:1px solid var(--line)}
.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 22px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-btn.sm{height:38px;padding:0 16px;font-size:12.5px;border-radius:10px}

@media(max-width:760px){.dz-grid2,.dz-grid3{grid-template-columns:1fr}}
@media(max-width:560px){.dz-title{font-size:23px}.dz-form{padding:18px}}
</style>

<div class="dz">

    <div class="dz-head">
        <div>
            <span class="dz-eyebrow"><i></i>Pembelian</span>
            <h1 class="dz-title">Purchase Order Baru</h1>
            <p class="dz-sub">Catat pesanan ke supplier</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="dz-btn ghost sm">← Kembali</a>
    </div>

    <form method="POST" action="{{ route('purchase-orders.store') }}" class="dz-glass dz-form">
        @csrf

        <div class="dz-grid3">
            <div class="dz-field">
                <label class="dz-lbl">No. PO *</label>
                <input type="text" name="po_number" value="{{ old('po_number', $nextNumber) }}" required class="dz-in">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">Tanggal *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="dz-in">
            </div>
            <div class="dz-field">
                <label class="dz-lbl">Expected Date</label>
                <input type="date" name="expected_date" class="dz-in">
            </div>
        </div>

        <div class="dz-grid2">
            <div class="dz-field">
                <label class="dz-lbl">Supplier</label>
                <select name="supplier_id" class="dz-sel">
                    <option value="">- Tanpa Supplier -</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <div class="dz-sec">Item Pesanan</div>
            <div class="dz-entries">
                <div class="dz-scroll">
                    <table class="dz-et">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Deskripsi</th>
                                <th class="num" style="width:90px">Qty</th>
                                <th class="num" style="width:140px">Harga</th>
                                <th class="num" style="width:110px">Diskon</th>
                                <th class="num" style="width:140px">Subtotal</th>
                                <th style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="itemBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="lbl-t">Subtotal</td>
                                <td class="val-t" id="fSubtotal">0</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="lbl-t">Diskon</td>
                                <td class="inp"><input type="number" name="discount" id="fDiscount" value="0" min="0" class="dz-in sm r" oninput="calc()"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="lbl-t">Pajak</td>
                                <td class="inp"><input type="number" name="tax" id="fTax" value="0" min="0" class="dz-in sm r" oninput="calc()"></td>
                                <td></td>
                            </tr>
                            <tr class="total">
                                <td colspan="5" class="lbl-t">Total</td>
                                <td class="val-t" id="fTotal">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <button type="button" onclick="addRow()" class="dz-add">+ Baris</button>
        </div>

        <div class="dz-field">
            <label class="dz-lbl">Catatan</label>
            <textarea name="notes" rows="2" class="dz-ta"></textarea>
        </div>

        <div class="dz-actions">
            <button class="dz-btn pri">Simpan PO</button>
            <a href="{{ route('purchase-orders.index') }}" class="dz-btn ghost">Batal</a>
        </div>
    </form>

</div>

<script>
const products = @json($productsJson);
const body = document.getElementById('itemBody');

function addRow() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="items[][product_id]" class="dz-sel sm" onchange="onProd(this)">
                <option value="">- Non Produk -</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.cost_price}">${p.code} - ${p.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="text" name="items[][description]" required class="dz-in sm"></td>
        <td><input type="number" name="items[][qty]" value="1" min="0.01" step="0.01" class="dz-in sm r qty" oninput="calc()"></td>
        <td><input type="number" name="items[][price]" value="0" min="0" step="0.01" class="dz-in sm r price" oninput="calc()"></td>
        <td><input type="number" name="items[][discount]" value="0" min="0" step="0.01" class="dz-in sm r disc" oninput="calc()"></td>
        <td class="subtotal">0</td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove(); calc()" class="dz-rm">✕</button></td>
    `;
    body.appendChild(tr);
    calc();
}

function onProd(sel) {
    const tr = sel.closest('tr');
    const opt = sel.options[sel.selectedIndex];
    if (opt.dataset.price) {
        tr.querySelector('.price').value = opt.dataset.price;
        const desc = tr.querySelector('input[name="items[][description]"]');
        if (!desc.value) desc.value = opt.text.split(' - ').slice(1).join(' - ');
        calc();
    }
}

function fmt(n) { return n.toLocaleString('id-ID'); }

function calc() {
    let sub = 0;
    body.querySelectorAll('tr').forEach(tr => {
        const q = parseFloat(tr.querySelector('.qty').value) || 0;
        const p = parseFloat(tr.querySelector('.price').value) || 0;
        const d = parseFloat(tr.querySelector('.disc').value) || 0;
        const s = q * p - d;
        tr.querySelector('.subtotal').textContent = fmt(s);
        sub += s;
    });
    const disc = parseFloat(document.getElementById('fDiscount').value) || 0;
    const tax = parseFloat(document.getElementById('fTax').value) || 0;
    document.getElementById('fSubtotal').textContent = fmt(sub);
    document.getElementById('fTotal').textContent = fmt(sub - disc + tax);
}

addRow();
</script>

@endsection