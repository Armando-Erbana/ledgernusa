@extends('layouts.app')
@section('title', 'Setup Awal')

<style>
    /* CSS Murni untuk menjamin tampilan rapi di semua perangkat */
    .ob-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px 0;
    }
    .ob-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .ob-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 8px 0;
    }
    .ob-sub {
        font-size: 14px;
        color: var(--mute);
        margin: 0;
    }
    .ob-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    @media (min-width: 768px) {
        .ob-grid { grid-template-columns: 1fr 1fr; }
    }
    .ob-card {
        background: #fff;
        border: 1px solid var(--hair);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .ob-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 10px 0;
    }
    .ob-card-desc {
        font-size: 13px;
        color: var(--mute);
        margin: 0 0 16px 0;
        line-height: 1.5;
    }
    .ob-card-list {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
        flex-grow: 1;
    }
    .ob-card-list li {
        font-size: 13px;
        color: var(--soft);
        margin-bottom: 8px;
        padding-left: 16px;
        position: relative;
    }
    .ob-card-list li::before {
        content: "-";
        position: absolute;
        left: 0;
        color: var(--faint);
    }
    .ob-actions {
        display: flex;
        flex-direction: column; /* Tumpuk vertikal di HP */
        gap: 10px;
        margin-top: auto;
    }
    @media (min-width: 480px) {
        .ob-actions { flex-direction: row; } /* Sejajar di layar lebar */
    }
    .ob-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.2s;
    }
    .ob-btn-outline {
        background: transparent;
        border: 1px solid var(--hair-2);
        color: var(--ink);
    }
    .ob-btn-outline:hover { background: rgba(0,0,0,.03); }
    .ob-btn-primary {
        background: #14213A;
        color: #fff;
    }
    .ob-btn-primary:hover { background: #1C8FC4; }

    /* Modal Styles */
    .ob-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    .ob-modal.open { display: flex; }
    .ob-modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 550px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .ob-modal-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--hair);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .ob-modal-title { font-size: 18px; font-weight: 700; margin: 0; color: var(--ink); }
    .ob-modal-close {
        background: #f0f0f0;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--soft);
    }
    .ob-modal-body {
        padding: 20px;
        overflow-y: auto;
        font-size: 13.5px;
        color: var(--soft);
        line-height: 1.6;
    }
    .ob-modal-body h4 { margin: 16px 0 8px 0; font-size: 14px; color: var(--ink); }
    .ob-modal-body ul { padding-left: 20px; margin: 0 0 16px 0; }
    .ob-modal-body li { margin-bottom: 6px; }
    .ob-modal-foot {
        padding: 16px 20px;
        border-top: 1px solid var(--hair);
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex-shrink: 0;
    }
    @media (min-width: 480px) {
        .ob-modal-foot { flex-direction: row-reverse; }
    }
</style>

@section('content')
<div class="ob-wrap">
    <div class="ob-header">
        <h1 class="ob-title">Pilih Jenis Bisnis</h1>
        <p class="ob-sub">Pilih tipe bisnis <strong>{{ $company->name }}</strong> agar COA dan menu disesuaikan.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
        @csrf
        <input type="hidden" name="business_type" id="selectedType" required>

        <div class="ob-grid">
            {{-- CARD JASA --}}
            <div class="ob-card">
                <h2 class="ob-card-title">Bisnis Jasa</h2>
                <p class="ob-card-desc">Konsultan, agensi, freelancer, jasa profesional.</p>
                <ul class="ob-card-list">
                    <li>COA tanpa Persediaan & HPP</li>
                    <li>Fokus Pendapatan Jasa & Beban</li>
                    <li>Menu Persediaan disembunyikan</li>
                </ul>
                <div class="ob-actions">
                    <button type="button" onclick="openDetail('jasa')" class="ob-btn ob-btn-outline">Lihat Detail</button>
                    <button type="button" onclick="selectType('jasa')" class="ob-btn ob-btn-primary">Pilih Jasa</button>
                </div>
            </div>

            {{-- CARD DAGANG --}}
            <div class="ob-card">
                <h2 class="ob-card-title">Bisnis Dagang</h2>
                <p class="ob-card-desc">Toko, distributor, grosir, retail, toko online.</p>
                <ul class="ob-card-list">
                    <li>COA lengkap dengan Persediaan</li>
                    <li>HPP & Pembelian aktif</li>
                    <li>Menu Persediaan tersedia</li>
                </ul>
                <div class="ob-actions">
                    <button type="button" onclick="openDetail('dagang')" class="ob-btn ob-btn-outline">Lihat Detail</button>
                    <button type="button" onclick="selectType('dagang')" class="ob-btn ob-btn-primary">Pilih Dagang</button>
                </div>
            </div>
        </div>

        @error('business_type')
            <p style="margin-top: 16px; color: #D04A4A; font-size: 13px;">{{ $message }}</p>
        @enderror

        <div style="margin-top: 24px; text-align: center; font-size: 12px; color: var(--mute);">
            Ingin punya <strong>kedua tipe</strong> (jasa + dagang)? Upgrade ke <strong>Pro/Enterprise</strong> untuk multi-company.
        </div>
    </form>
</div>

{{-- MODAL JASA --}}
<div id="modal-jasa" class="ob-modal" onclick="if(event.target===this) closeDetail()">
    <div class="ob-modal-box">
        <div class="ob-modal-head">
            <h2 class="ob-modal-title">Bisnis Jasa</h2>
            <button onclick="closeDetail()" class="ob-modal-close">&times;</button>
        </div>
        <div class="ob-modal-body">
            <h4>Cocok untuk:</h4>
            <p style="margin: 0 0 16px 0;">Konsultan, agensi digital, freelancer, akuntan, notaris, dokter, klinik, les privat, salon, bengkel, jasa kebersihan, event organizer.</p>
            
            <h4>Fitur yang didapat:</h4>
            <ul>
                <li>COA khusus jasa tanpa Persediaan & HPP</li>
                <li>Pendapatan Jasa, Konsultasi, Lain-lain</li>
                <li>Beban operasional lengkap</li>
                <li>Menu Persediaan disembunyikan</li>
                <li>Fokus invoice jasa & laporan keuangan</li>
            </ul>

            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; font-size: 12px; border: 1px solid #eee;">
                <strong>Catatan:</strong> Jika bisnis Anda juga jualan barang, pilih Dagang.
            </div>
        </div>
        <div class="ob-modal-foot">
            <button onclick="closeDetail()" class="ob-btn ob-btn-outline" style="flex: 1;">Tutup</button>
            <button onclick="selectType('jasa')" class="ob-btn ob-btn-primary" style="flex: 1;">Pilih Jasa</button>
        </div>
    </div>
</div>

{{-- MODAL DAGANG --}}
<div id="modal-dagang" class="ob-modal" onclick="if(event.target===this) closeDetail()">
    <div class="ob-modal-box">
        <div class="ob-modal-head">
            <h2 class="ob-modal-title">Bisnis Dagang</h2>
            <button onclick="closeDetail()" class="ob-modal-close">&times;</button>
        </div>
        <div class="ob-modal-body">
            <h4>Cocok untuk:</h4>
            <p style="margin: 0 0 16px 0;">Toko retail, minimarket, distributor, grosir, toko online, toko bangunan, apotek, toko pakaian.</p>
            
            <h4>Fitur yang didapat:</h4>
            <ul>
                <li>COA lengkap dengan Persediaan</li>
                <li>Akun HPP & Pembelian</li>
                <li>Menu Persediaan aktif</li>
                <li>Retur & Diskon Penjualan</li>
            </ul>

            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; font-size: 12px; border: 1px solid #eee;">
                <strong>Catatan:</strong> Jika tidak menjual barang fisik, pilih Jasa.
            </div>
        </div>
        <div class="ob-modal-foot">
            <button onclick="closeDetail()" class="ob-btn ob-btn-outline" style="flex: 1;">Tutup</button>
            <button onclick="selectType('dagang')" class="ob-btn ob-btn-primary" style="flex: 1;">Pilih Dagang</button>
        </div>
    </div>
</div>

<script>
function openDetail(type) {
    document.getElementById('modal-' + type).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDetail() {
    document.querySelectorAll('.ob-modal').forEach(m => m.classList.remove('open'));
    document.body.style.overflow = '';
}
function selectType(type) {
    document.getElementById('selectedType').value = type;
    document.getElementById('onboardingForm').submit();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
@endsection