@extends('layouts.app')
@section('title', 'Langganan')

@section('content')

<style>
.dz{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    position:relative;isolation:isolate;max-width:1024px;margin:0 auto;font-feature-settings:"tnum"}
.dz::before,.dz::after{content:"";position:absolute;z-index:-1;border-radius:50%;filter:blur(70px);pointer-events:none}
.dz::before{width:320px;height:320px;top:-80px;right:-60px;background:var(--b2);opacity:.2}
.dz::after{width:280px;height:280px;top:420px;left:-90px;background:var(--b1);opacity:.14}

.dz-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.dz-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.dz-title{margin:6px 0 4px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.dz-sub{margin:0 0 24px;font-size:13.5px;color:var(--mute)}

.dz-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

.dz-sec{display:flex;align-items:center;gap:10px;margin:0 0 14px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-sec::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}

/* status */
.dz-status{position:relative;overflow:hidden;padding:24px 26px;margin-bottom:18px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;
    background:linear-gradient(135deg,var(--navy),var(--b1));border-color:rgba(255,255,255,.15);
    box-shadow:0 14px 34px -12px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.22)}
.dz-status::before{content:"";position:absolute;inset:0;pointer-events:none;opacity:.5;
    background:repeating-linear-gradient(to bottom,transparent 0 27px,rgba(255,255,255,.06) 27px 28px)}
.dz-status > *{position:relative}
.dz-status .k{font-size:11.5px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:rgba(255,255,255,.7)}
.dz-status .st{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:8px}
.dz-pill{display:inline-flex;align-items:center;gap:7px;padding:5px 13px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(255,255,255,.14)}
.dz-pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-pill.on{color:#7CF0C8}
.dz-pill.off{color:#FFB4BE}
.dz-pill.none{color:rgba(255,255,255,.8)}
.dz-status .until{font-size:13.5px;color:rgba(255,255,255,.8)}
.dz-status .co{text-align:right}
.dz-status .co b{display:block;margin-top:6px;font-size:15px;font-weight:600}

/* payment info */
.dz-pay{padding:22px 24px;margin-bottom:30px}
.dz-pay h3{margin:0 0 4px;font-size:15px;font-weight:700;color:var(--ink)}
.dz-pay p.note{margin:0 0 16px;font-size:13px;color:var(--mute)}
.dz-banks{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.dz-bank{padding:14px 16px;border-radius:14px;background:rgba(255,255,255,.7);border:1px solid var(--line);display:flex;flex-direction:column;gap:6px}
.dz-bank .nm{font-size:11px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--b1)}
.dz-bank .no{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:16px;font-weight:600;color:var(--ink);letter-spacing:.02em}
.dz-bank .an{font-size:12px;color:var(--mute)}
.dz-copy{align-self:flex-start;margin-top:4px;height:28px;padding:0 11px;border-radius:8px;font-size:12px;font-weight:600;color:var(--b1);
    background:rgba(29,78,216,.08);border:0;cursor:pointer;transition:.18s}
.dz-copy:hover{background:rgba(29,78,216,.16)}
.dz-pay .foot{margin:16px 0 0;font-size:12.5px;color:var(--mute)}

/* ====== PILIH PAKET (Animasi Hover Diperbarui) ====== */
.dz-plans{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:30px;align-items:stretch}
.dz-plan{
    padding:24px;display:flex;flex-direction:column;position:relative;
    border:2px solid transparent; /* Border transparan agar tidak ada pergeseran layout */
    transition:all .3s cubic-bezier(.25,.8,.25,1);
    background:rgba(255,255,255,.62);
}
/* Efek Hover: Stroke Biru & Naik Sedikit */
.dz-plan:hover{
    transform:translateY(-4px);
    border-color:var(--b1);
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 18px 38px -14px rgba(29,78,216,.25), 0 0 0 1px var(--b1);
}
/* Kartu Professional (Populer) Statis Biru */
.dz-plan.popular{
    border-color:var(--b1);
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 32px -14px rgba(29,78,216,.2);
}
.dz-plan.popular:hover{
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 18px 38px -14px rgba(29,78,216,.4), 0 0 0 1px var(--b1);
}
/* Kartu yang Sedang Dipilih */
.dz-plan.on{
    border-color:var(--b1);
    background:rgba(29,78,216,.03);
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 18px 38px -14px rgba(29,78,216,.32), 0 0 0 2px var(--b1);
}
.dz-plan.on:hover{ transform:translateY(-2px); }

/* Label Paling Populer */
.dz-badge-pop{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--b1);color:#fff;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:4px 12px;border-radius:999px;white-space:nowrap;box-shadow:0 4px 10px -2px rgba(29,78,216,.4);}

.dz-pn{font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--b1)}
.dz-pd{margin:6px 0 0;font-size:12.5px;color:var(--mute);line-height:1.5;min-height:36px}
.dz-price{margin:12px 0 20px;font-size:28px;font-weight:700;letter-spacing:-.02em;color:var(--ink);border-bottom:1px solid var(--line);padding-bottom:20px}
.dz-price small{font-size:13px;font-weight:500;color:var(--mute)}

/* Fitur List */
.dz-features{list-style:none;padding:0;margin:0 0 24px 0;flex:1}
.dz-features li{display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--ink);margin-bottom:12px;line-height:1.4}
.dz-features li:last-child{margin-bottom:0}
.dz-features li svg{flex:none;width:16px;height:16px;color:var(--b1);margin-top:1px}
.dz-features li.muted{color:var(--mute);opacity:.6}
.dz-features li.muted svg{color:var(--mute)}
.dz-features strong{font-weight:700}

.dz-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 22px;border-radius:12px;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s}
.dz-btn.block{width:100%}
.dz-btn.pri{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.dz-btn.pri:hover{transform:translateY(-1px);filter:brightness(1.05)}
.dz-btn.ghost{color:var(--ink);background:rgba(255,255,255,.7);border:1px solid var(--line)}
.dz-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.dz-btn.chosen{color:var(--b1);background:rgba(29,78,216,.08);cursor:default;border:1px solid transparent}

.dz-empty{padding:40px 20px;text-align:center;color:var(--mute);margin-bottom:30px}

/* form */
.dz-form{padding:26px;margin-bottom:30px}
.dz-form h2{margin:0 0 20px;font-size:16px;font-weight:700;color:var(--ink)}
.dz-form h2 span{color:var(--b1)}
.dz-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.dz-full{grid-column:1 / -1}
.dz-field{display:flex;flex-direction:column;gap:7px}
.dz-lbl{font-size:12px;font-weight:600;color:var(--mute);letter-spacing:.02em}
.dz-hint{margin:0;font-size:12px;color:var(--mute)}
.dz-in,.dz-sel,.dz-ta{width:100%;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.8);
    padding:0 14px;font-size:13.5px;color:var(--ink);outline:none;transition:.18s;font-family:inherit}
.dz-in,.dz-sel{height:42px}
.dz-ta{padding:11px 14px;resize:vertical;min-height:72px}
.dz-in:focus,.dz-sel:focus,.dz-ta:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}
.dz-in[type=file]{padding:0;line-height:40px;cursor:pointer;color:var(--mute)}
.dz-in[type=file]::file-selector-button{height:40px;margin-right:14px;padding:0 16px;border:0;border-right:1px solid var(--line);
    background:rgba(29,78,216,.08);color:var(--b1);font-weight:600;font-size:13px;cursor:pointer;transition:.18s}
.dz-in[type=file]::file-selector-button:hover{background:rgba(29,78,216,.16)}
.dz-actions{display:flex;gap:10px;margin-top:22px;padding-top:18px;border-top:1px solid var(--line)}

/* history */
.dz-hist{padding:8px 24px}
.dz-row{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:18px 0;border-bottom:1px solid var(--line)}
.dz-row:last-child{border-bottom:0}
.dz-row .t{font-size:14px;font-weight:600;color:var(--ink)}
.dz-row .m{margin-top:4px;font-size:12.5px;color:var(--mute)}
.dz-row .d{margin-top:3px;font-size:12px;color:#A3AEC6}
.dz-reject{margin-top:8px;padding:8px 12px;border-radius:10px;font-size:12.5px;color:#B4233A;background:rgba(239,68,68,.08)}
.dz-side{flex:none;text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px}
.dz-side form{margin:0}
.dz-del{border:0;background:transparent;font-size:12.5px;font-weight:600;color:#B4233A;cursor:pointer;padding:0}
.dz-del:hover{text-decoration:underline}

.dz-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600}
.dz-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.dz-badge.pending{color:#B45309;background:rgba(245,158,11,.14)}
.dz-badge.ok{color:#0E8F6B;background:rgba(16,185,129,.12)}
.dz-badge.no{color:#B4233A;background:rgba(239,68,68,.1)}

@media(max-width:860px){.dz-plans,.dz-banks{grid-template-columns:1fr}}
@media(max-width:640px){.dz-grid{grid-template-columns:1fr}.dz-title{font-size:23px}.dz-status .co{text-align:left}.dz-form{padding:20px}.dz-hist{padding:4px 18px}}
</style>

<div class="dz">

    <span class="dz-eyebrow"><i></i>Akun</span>
    <h1 class="dz-title">Langganan</h1>
    <p class="dz-sub">Pilih paket, transfer, lalu upload bukti pembayaran</p>

    {{-- STATUS --}}
    <div class="dz-glass dz-status">
        <div>
            <div class="k">Status Langganan</div>
            <div class="st">
                @if($subscription && $subscription->isActive())
                    <span class="dz-pill on">Aktif</span>
                    <span class="until">s/d {{ $subscription->ends_at->format('d M Y') }} · {{ $subscription->daysRemaining() }} hari lagi</span>
                @elseif($subscription)
                    <span class="dz-pill off">Berakhir</span>
                    <span class="until">{{ $subscription->ends_at?->format('d M Y') }}</span>
                @else
                    <span class="dz-pill none">Belum Berlangganan</span>
                @endif
            </div>
        </div>
        <div class="co">
            <div class="k">Perusahaan</div>
            <b>{{ $company->name ?? '-' }}</b>
        </div>
    </div>

    {{-- INFO TRANSFER --}}
    <div class="dz-glass dz-pay">
        <h3>Instruksi Pembayaran Manual</h3>
        <p class="note">Transfer ke salah satu rekening berikut:</p>
        <div class="dz-banks">
            <div class="dz-bank">
                <span class="nm">BCA</span>
                <span class="no">1234567890</span>
                <span class="an">a/n PT LedgerNusa Digital</span>
                <button type="button" class="dz-copy" onclick="copyNo(this, '1234567890')">Salin</button>
            </div>
            <div class="dz-bank">
                <span class="nm">Mandiri</span>
                <span class="no">9876543210</span>
                <span class="an">a/n PT LedgerNusa Digital</span>
                <button type="button" class="dz-copy" onclick="copyNo(this, '9876543210')">Salin</button>
            </div>
            <div class="dz-bank">
                <span class="nm">BRI</span>
                <span class="no">5555666677</span>
                <span class="an">a/n PT LedgerNusa Digital</span>
                <button type="button" class="dz-copy" onclick="copyNo(this, '5555666677')">Salin</button>
            </div>
        </div>
        <p class="foot">Setelah transfer, upload bukti di form di bawah. Verifikasi maksimal 1x24 jam.</p>
    </div>

    {{-- PILIH PAKET --}}
    <h2 class="dz-sec">Pilih Paket</h2>
    @if($plans->isEmpty())
        <div class="dz-glass dz-empty">Belum ada paket tersedia.</div>
    @else
        <div class="dz-plans">
            @foreach($plans as $plan)
                @php 
                    $isSelected = $selectedPlanId == $plan->id; 
                    $planCode = strtolower($plan->code ?? $plan->name); // Deteksi paket
                    $isPro = str_contains($planCode, 'pro');
                @endphp
                
                <div class="dz-plan {{ $isSelected ? 'on' : '' }} {{ $isPro ? 'popular' : '' }}">
                    @if($isPro)
                        <div class="dz-badge-pop">Paling Populer</div>
                    @endif

                    <div class="dz-pn">{{ $plan->name }}</div>
                    @if($plan->description ?? null)
                        <p class="dz-pd">{{ $plan->description }}</p>
                    @endif
                    <div class="dz-price">
                        Rp {{ number_format($plan->price, 0, ',', '.') }}
                        <small> / {{ $plan->duration_days ?? 30 }} hari</small>
                    </div>

                    {{-- Daftar Fitur Dinamis --}}
                    <ul class="dz-features">
                        @if(str_contains($planCode, 'starter'))
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> 1 Pengguna (Owner)</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Jurnal & Buku Besar</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Kas & Bank, Penjualan, Pembelian</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Laporan Keuangan Dasar</li>
                            <li class="muted"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Multi-Cabang</li>
                        @elseif($isPro)
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <strong>Semua fitur Starter</strong></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Hingga 5 Pengguna</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <strong>Multi-Cabang</strong> & Rekonsiliasi</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Sales Order, PO, Penerimaan</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Payroll, Aset Tetap, Pajak</li>
                        @else
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <strong>Semua fitur Professional</strong></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <strong>Unlimited</strong> Pengguna & Cabang</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Masa aktif hingga 60 hari</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Prioritas Dukungan (Support)</li>
                        @endif
                    </ul>

                    @if($isSelected)
                        <span class="dz-btn block chosen">Paket Dipilih</span>
                    @else
                        <a href="{{ route('subscription.index', ['plan' => $plan->id]) }}" class="dz-btn block {{ $isPro ? 'pri' : 'ghost' }}">Pilih Paket</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- FORM UPLOAD BUKTI --}}
    @php $selectedPlan = $selectedPlanId ? $plans->firstWhere('id', $selectedPlanId) : null; @endphp
    @if($selectedPlan)
        <div class="dz-glass dz-form">
            <h2>Upload Bukti Transfer — <span>{{ $selectedPlan->name }}</span></h2>

            <form method="POST" action="{{ route('subscription.upload-proof') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">

                <div class="dz-grid">
                    <div class="dz-field">
                        <label class="dz-lbl">Nominal Transfer (Rp) *</label>
                        <input type="number" name="amount" value="{{ old('amount', $selectedPlan->price) }}"
                               required min="{{ $selectedPlan->price }}" step="1000" class="dz-in">
                        <p class="dz-hint">Minimal Rp {{ number_format($selectedPlan->price, 0, ',', '.') }}</p>
                    </div>

                    <div class="dz-field">
                        <label class="dz-lbl">Tanggal Transfer *</label>
                        <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}"
                               required max="{{ date('Y-m-d') }}" class="dz-in">
                    </div>

                    <div class="dz-field">
                        <label class="dz-lbl">Bank Pengirim *</label>
                        <select name="bank_from" required class="dz-sel">
                            <option value="">— Pilih Bank —</option>
                            @foreach(['BCA','Mandiri','BRI','BNI','BSI','CIMB','Permata','BTN','Bank Lain'] as $b)
                                <option value="{{ $b }}" @selected(old('bank_from') === $b)>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dz-field">
                        <label class="dz-lbl">Nama Pemilik Rekening Pengirim *</label>
                        <input type="text" name="account_name" value="{{ old('account_name') }}"
                               required class="dz-in" placeholder="Sesuai rekening">
                    </div>

                    <div class="dz-field dz-full">
                        <label class="dz-lbl">Foto Bukti Transfer * (JPG/PNG, max 5MB)</label>
                        <input type="file" name="proof_file" accept="image/*" required class="dz-in">
                    </div>

                    <div class="dz-field dz-full">
                        <label class="dz-lbl">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" class="dz-ta" placeholder="Contoh: transfer dari rekening istri">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="dz-actions">
                    <button type="submit" class="dz-btn pri">Upload Bukti Transfer</button>
                    <a href="{{ route('subscription.index') }}" class="dz-btn ghost">Batal</a>
                </div>
            </form>
        </div>
    @endif

    {{-- RIWAYAT BUKTI --}}
    @if($proofs->isNotEmpty())
        <h2 class="dz-sec">Riwayat Upload Bukti</h2>
        <div class="dz-glass dz-hist">
            @foreach($proofs as $proof)
                <div class="dz-row">
                    <div>
                        <div class="t">{{ $proof->plan->name ?? 'Paket' }} — Rp {{ number_format($proof->amount, 0, ',', '.') }}</div>
                        <div class="m">{{ $proof->transfer_date->format('d M Y') }} via {{ $proof->bank_from }} — a/n {{ $proof->account_name }}</div>
                        <div class="d">Diupload {{ $proof->created_at->diffForHumans() }}</div>
                        @if($proof->isRejected() && $proof->rejection_reason)
                            <div class="dz-reject">Ditolak: {{ $proof->rejection_reason }}</div>
                        @endif
                    </div>
                    <div class="dz-side">
                        @if($proof->isPending())
                            <span class="dz-badge pending">Menunggu</span>
                            <form method="POST" action="{{ route('subscription.delete-proof', $proof) }}"
                                  onsubmit="return confirm('Hapus bukti ini?')">
                                @csrf @method('DELETE')
                                <button class="dz-del">Hapus</button>
                            </form>
                        @elseif($proof->isApproved())
                            <span class="dz-badge ok">Disetujui</span>
                        @else
                            <span class="dz-badge no">Ditolak</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
function copyNo(btn, no) {
    const done = () => { btn.textContent = 'Tersalin'; setTimeout(() => btn.textContent = 'Salin', 1500); };
    if (navigator.clipboard) navigator.clipboard.writeText(no).then(done);
}
</script>

@endsection