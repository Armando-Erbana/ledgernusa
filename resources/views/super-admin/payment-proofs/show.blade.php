@extends('super-admin.layout')
@section('title', 'Detail Bukti Pembayaran')

@section('content')

<style>
.tn{--b1:#1D4ED8;--b2:#06B6D4;--navy:#0B1B3F;--ink:#0F1E3D;--mute:#6B7A99;--line:rgba(15,30,61,.08);
    max-width:1024px;margin:0 auto;font-feature-settings:"tnum"}

.tn-back{display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;font-size:13px;font-weight:600;color:var(--mute);text-decoration:none;transition:.18s}
.tn-back:hover{color:var(--b1)}

.tn-glass{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);
    border:1px solid rgba(255,255,255,.75);border-radius:20px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 12px 32px -14px rgba(15,30,61,.18),0 0 0 1px var(--line)}

/* header */
.tn-head{padding:24px 26px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
.tn-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--b1)}
.tn-eyebrow i{width:18px;height:2px;border-radius:2px;background:linear-gradient(90deg,var(--b1),var(--b2))}
.tn-title{margin:6px 0 4px;font-size:24px;font-weight:700;letter-spacing:-.02em;color:var(--ink)}
.tn-sub{margin:0;font-size:13px;color:var(--mute)}
.tn-sub b{color:var(--ink);font-weight:600}

.tn-badge{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:999px;font-size:12.5px;font-weight:600}
.tn-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.tn-badge.pending{color:#B45309;background:rgba(245,158,11,.14)}
.tn-badge.ok{color:#0E8F6B;background:rgba(16,185,129,.12)}
.tn-badge.no{color:#B4233A;background:rgba(239,68,68,.1)}

/* body */
.tn-body{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}
.tn-card{padding:24px}
.tn-ct{display:flex;align-items:center;gap:10px;margin:0 0 6px;font-size:15px;font-weight:700;color:var(--ink)}
.tn-ct::before{content:"";width:4px;height:16px;border-radius:4px;background:linear-gradient(180deg,var(--b1),var(--b2))}

.tn-dl{margin:0}
.tn-row{display:flex;justify-content:space-between;align-items:baseline;gap:20px;padding:14px 0;border-bottom:1px solid var(--line)}
.tn-row:last-child{border-bottom:0;padding-bottom:0}
.tn-k{flex:none;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}
.tn-v{margin:0;font-size:14px;font-weight:600;color:var(--ink);text-align:right;word-break:break-word}
.tn-v small{display:block;margin-top:2px;font-size:12px;font-weight:500;color:var(--mute)}
.tn-v.amount{font-size:22px;font-weight:700;letter-spacing:-.02em;color:var(--b1)}
.tn-note{margin-top:14px;padding:12px 14px;border-radius:12px;font-size:13px;line-height:1.55;color:var(--ink);background:rgba(29,78,216,.05)}
.tn-note span{display:block;margin-bottom:4px;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--mute)}

/* proof */
.tn-proof{display:block;margin-top:14px;border-radius:16px;overflow:hidden;border:1px solid var(--line);background:rgba(15,30,61,.03);transition:.2s}
.tn-proof:hover{border-color:rgba(29,78,216,.35);box-shadow:0 14px 30px -16px rgba(29,78,216,.35)}
.tn-proof img{display:block;width:100%;height:auto;max-height:380px;object-fit:contain}
.tn-proof-foot{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px}
.tn-hint{font-size:12px;color:var(--mute)}

/* buttons */
.tn-btn{height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 20px;border-radius:12px;white-space:nowrap;
    font-size:13px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:.18s;font-family:inherit}
.tn-btn.block{width:100%}
.tn-btn.sm{height:36px;padding:0 15px;font-size:12.5px;border-radius:10px}
.tn-btn.ghost{color:var(--ink);background:rgba(255,255,255,.8);border:1px solid var(--line)}
.tn-btn.ghost:hover{background:#fff;border-color:rgba(29,78,216,.35);color:var(--b1)}
.tn-btn.ok{color:#fff;background:linear-gradient(135deg,var(--b1),var(--b2));box-shadow:0 8px 18px -8px rgba(29,78,216,.6),inset 0 1px 0 rgba(255,255,255,.3)}
.tn-btn.ok:hover{transform:translateY(-1px);filter:brightness(1.05)}
.tn-btn.danger{color:#B4233A;background:rgba(255,255,255,.8);border:1px solid rgba(239,68,68,.35)}
.tn-btn.danger:hover{color:#fff;background:#C8283F;border-color:#C8283F}

/* actions */
.tn-acts{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.tn-act{padding:22px;display:flex;flex-direction:column}
.tn-act h3{margin:0 0 4px;font-size:15px;font-weight:700;color:var(--ink)}
.tn-act p{margin:0 0 16px;font-size:13px;line-height:1.55;color:var(--mute)}
.tn-act p b{color:var(--ink);font-weight:600}
.tn-act form{margin:0;margin-top:auto}
.tn-ta{width:100%;min-height:76px;margin-bottom:12px;padding:11px 14px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.85);
    font-size:13.5px;color:var(--ink);font-family:inherit;resize:vertical;outline:none;transition:.18s}
.tn-ta:focus{border-color:var(--b1);box-shadow:0 0 0 4px rgba(29,78,216,.12);background:#fff}

/* verified */
.tn-done{padding:22px 26px;font-size:13.5px;line-height:1.7;color:var(--mute)}
.tn-done b{color:var(--ink);font-weight:600}
.tn-reason{margin-top:10px;padding:10px 14px;border-radius:12px;font-size:13px;color:#B4233A;background:rgba(239,68,68,.08)}

@media(max-width:820px){.tn-body,.tn-acts{grid-template-columns:1fr}}
@media(max-width:560px){.tn-head,.tn-card,.tn-act,.tn-done{padding:20px}.tn-title{font-size:20px}.tn-row{flex-direction:column;gap:4px}.tn-v{text-align:left}}
</style>

<div class="tn">

    <a href="{{ route('super-admin.payment-proofs.index') }}" class="tn-back">← Kembali ke daftar</a>

    {{-- HEADER --}}
    <div class="tn-glass tn-head">
        <div>
            <span class="tn-eyebrow"><i></i>Bukti Pembayaran</span>
            <h1 class="tn-title">#{{ $proof->id }} · {{ $proof->company->name ?? '-' }}</h1>
            <p class="tn-sub">Diupload {{ $proof->created_at->format('d M Y H:i') }} oleh <b>{{ $proof->user->name ?? '-' }}</b></p>
        </div>
        <div>
            @if($proof->isPending())
                <span class="tn-badge pending">Menunggu Verifikasi</span>
            @elseif($proof->isApproved())
                <span class="tn-badge ok">Disetujui</span>
            @else
                <span class="tn-badge no">Ditolak</span>
            @endif
        </div>
    </div>

    {{-- DETAIL + BUKTI --}}
    <div class="tn-body">
        <div class="tn-glass tn-card">
            <h2 class="tn-ct">Detail Transfer</h2>
            <dl class="tn-dl">
                <div class="tn-row">
                    <dt class="tn-k">Perusahaan</dt>
                    <dd class="tn-v">{{ $proof->company->name ?? '-' }}</dd>
                </div>
                <div class="tn-row">
                    <dt class="tn-k">Paket</dt>
                    <dd class="tn-v">{{ $proof->plan->name ?? '-' }}<small>{{ $proof->plan->duration_days ?? 30 }} hari</small></dd>
                </div>
                <div class="tn-row">
                    <dt class="tn-k">Nominal</dt>
                    <dd class="tn-v amount">Rp {{ number_format($proof->amount, 0, ',', '.') }}</dd>
                </div>
                <div class="tn-row">
                    <dt class="tn-k">Tanggal Transfer</dt>
                    <dd class="tn-v">{{ $proof->transfer_date->format('d M Y') }}</dd>
                </div>
                <div class="tn-row">
                    <dt class="tn-k">Bank Pengirim</dt>
                    <dd class="tn-v">{{ $proof->bank_from }}<small>a/n {{ $proof->account_name }}</small></dd>
                </div>
            </dl>
            @if($proof->notes)
                <div class="tn-note">
                    <span>Catatan User</span>
                    {{ $proof->notes }}
                </div>
            @endif
        </div>

        <div class="tn-glass tn-card">
            <h2 class="tn-ct">Bukti Transfer</h2>
            <a href="{{ asset('storage/' . $proof->proof_file) }}" target="_blank" class="tn-proof">
                <img src="{{ asset('storage/' . $proof->proof_file) }}" alt="Bukti Transfer">
            </a>
            <div class="tn-proof-foot">
                <span class="tn-hint">Klik gambar untuk memperbesar</span>
                <a href="{{ route('super-admin.payment-proofs.download', $proof) }}" class="tn-btn ghost sm">Download</a>
            </div>
        </div>
    </div>

    {{-- VERIFIKASI --}}
    @if($proof->isPending())
        <div class="tn-acts">
            {{-- APPROVE --}}
            <div class="tn-glass tn-act">
                <h3>Setujui Pembayaran</h3>
                <p>
                    Langganan <b>{{ $proof->company->name }}</b> akan diperpanjang
                    <b>{{ $proof->plan->duration_days ?? 30 }} hari</b>.
                </p>
                <form method="POST" action="{{ route('super-admin.payment-proofs.approve', $proof) }}"
                      onsubmit="return confirm('Setujui pembayaran ini dan perpanjang langganan?')">
                    @csrf
                    <button class="tn-btn ok block">Setujui &amp; Perpanjang</button>
                </form>
            </div>

            {{-- REJECT --}}
            <div class="tn-glass tn-act">
                <h3>Tolak Bukti</h3>
                <p>Alasan akan ditampilkan ke user di halaman langganan mereka.</p>
                <form method="POST" action="{{ route('super-admin.payment-proofs.reject', $proof) }}">
                    @csrf
                    <textarea name="rejection_reason" rows="2" required
                              placeholder="Alasan penolakan (wajib)"
                              class="tn-ta"></textarea>
                    <button class="tn-btn danger block">Tolak Bukti</button>
                </form>
            </div>
        </div>
    @else
        <div class="tn-glass tn-done">
            Diverifikasi oleh <b>{{ $proof->verifier->name ?? '-' }}</b>
            pada {{ $proof->verified_at?->format('d M Y H:i') }}.
            @if($proof->isApproved() && $proof->subscription)
                <br>Langganan aktif s/d <b>{{ $proof->subscription->ends_at->format('d M Y') }}</b>.
            @endif
            @if($proof->isRejected() && $proof->rejection_reason)
                <div class="tn-reason">Alasan: {{ $proof->rejection_reason }}</div>
            @endif
        </div>
    @endif

</div>

@endsection