@extends('layouts.app')
@section('title', 'Setup Awal')
@section('content')

<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-8">
        <h1 class="ln-page-title">Pilih Jenis Bisnis</h1>
        <p class="ln-page-sub mt-2">
            Pilih tipe bisnis <strong>{{ $company->name }}</strong> agar COA dan menu disesuaikan.
        </p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
        @csrf
        <input type="hidden" name="business_type" id="selectedType" required>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- CARD JASA --}}
            <div class="ln-form-card flex flex-col">
                <div class="text-4xl mb-3">💼</div>
                <h2 class="ln-section-title mb-2">Bisnis Jasa</h2>
                <p class="text-sm text-[var(--mute)] leading-relaxed mb-3">
                    Konsultan, agensi, freelancer, jasa profesional.
                </p>
                <ul class="text-xs text-[var(--mute)] space-y-1 mb-4 flex-grow">
                    <li>✓ COA tanpa Persediaan &amp; HPP</li>
                    <li>✓ Fokus Pendapatan Jasa &amp; Beban</li>
                    <li>✓ Menu Persediaan disembunyikan</li>
                </ul>
                <div class="flex gap-2 mt-auto">
                    <button type="button" onclick="openDetail('jasa')" class="ln-btn-outline flex-1 text-sm">Lihat Selengkapnya</button>
                    <button type="button" onclick="selectType('jasa')" class="ln-btn-primary flex-1 text-sm">Pilih Jasa</button>
                </div>
            </div>

            {{-- CARD DAGANG --}}
            <div class="ln-form-card flex flex-col">
                <div class="text-4xl mb-3">🏪</div>
                <h2 class="ln-section-title mb-2">Bisnis Dagang</h2>
                <p class="text-sm text-[var(--mute)] leading-relaxed mb-3">
                    Toko, distributor, grosir, retail, toko online.
                </p>
                <ul class="text-xs text-[var(--mute)] space-y-1 mb-4 flex-grow">
                    <li>✓ COA lengkap dengan Persediaan</li>
                    <li>✓ HPP &amp; Pembelian aktif</li>
                    <li>✓ Menu Persediaan tersedia</li>
                </ul>
                <div class="flex gap-2 mt-auto">
                    <button type="button" onclick="openDetail('dagang')" class="ln-btn-outline flex-1 text-sm">Lihat Selengkapnya</button>
                    <button type="button" onclick="selectType('dagang')" class="ln-btn-primary flex-1 text-sm">Pilih Dagang</button>
                </div>
            </div>
        </div>

        @error('business_type')
            <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="mt-6 text-center text-xs text-[var(--mute)]">
            Ingin punya <strong>kedua tipe</strong> (jasa + dagang)? Upgrade ke <strong>Pro/Enterprise</strong> untuk multi-company.
        </div>
    </form>
</div>

{{-- MODAL JASA --}}
<div id="modal-jasa" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,.4); backdrop-filter: blur(4px);" onclick="if(event.target===this) closeDetail()">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-white border-b border-[var(--hair)] px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">💼 Bisnis Jasa</h2>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-xl">×</button>
        </div>
        <div class="px-6 py-5 space-y-4 text-sm">
            <div>
                <h3 class="font-bold text-base mb-2">Cocok untuk:</h3>
                <p class="text-[var(--mute)]">Konsultan, agensi digital, freelancer, akuntan, notaris, dokter, klinik, les privat, salon, bengkel, jasa kebersihan, event organizer.</p>
            </div>
            <div>
                <h3 class="font-bold text-base mb-2">Fitur yang didapat:</h3>
                <ul class="space-y-1 text-[var(--mute)]">
                    <li>✓ COA khusus jasa — tanpa Persediaan &amp; HPP</li>
                    <li>✓ Pendapatan Jasa, Konsultasi, Lain-lain</li>
                    <li>✓ Beban operasional lengkap</li>
                    <li>✓ Menu Persediaan <strong>disembunyikan</strong></li>
                    <li>✓ Fokus invoice jasa &amp; laporan keuangan</li>
                </ul>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs">
                <strong>Catatan:</strong> Jika bisnis Anda juga jualan barang, pilih <strong>Dagang</strong>.
            </div>
        </div>
        <div class="sticky bottom-0 bg-white border-t border-[var(--hair)] px-6 py-4 flex gap-2">
            <button onclick="closeDetail()" class="ln-btn-outline flex-1">Tutup</button>
            <button onclick="selectType('jasa')" class="ln-btn-primary flex-1">Pilih Jasa</button>
        </div>
    </div>
</div>

{{-- MODAL DAGANG --}}
<div id="modal-dagang" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,.4); backdrop-filter: blur(4px);" onclick="if(event.target===this) closeDetail()">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-white border-b border-[var(--hair)] px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">🏪 Bisnis Dagang</h2>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-xl">×</button>
        </div>
        <div class="px-6 py-5 space-y-4 text-sm">
            <div>
                <h3 class="font-bold text-base mb-2">Cocok untuk:</h3>
                <p class="text-[var(--mute)]">Toko retail, minimarket, distributor, grosir, toko online, toko bangunan, apotek, toko pakaian.</p>
            </div>
            <div>
                <h3 class="font-bold text-base mb-2">Fitur yang didapat:</h3>
                <ul class="space-y-1 text-[var(--mute)]">
                    <li>✓ COA lengkap dengan Persediaan</li>
                    <li>✓ Akun HPP &amp; Pembelian</li>
                    <li>✓ Menu Persediaan <strong>aktif</strong></li>
                    <li>✓ Retur &amp; Diskon Penjualan</li>
                </ul>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs">
                <strong>Catatan:</strong> Jika tidak menjual barang fisik, pilih <strong>Jasa</strong>.
            </div>
        </div>
        <div class="sticky bottom-0 bg-white border-t border-[var(--hair)] px-6 py-4 flex gap-2">
            <button onclick="closeDetail()" class="ln-btn-outline flex-1">Tutup</button>
            <button onclick="selectType('dagang')" class="ln-btn-primary flex-1">Pilih Dagang</button>
        </div>
    </div>
</div>

<script>
function openDetail(type) {
    const m = document.getElementById('modal-' + type);
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeDetail() {
    document.querySelectorAll('[id^="modal-"]').forEach(m => {
        m.classList.add('hidden');
        m.classList.remove('flex');
    });
    document.body.style.overflow = '';
}
function selectType(type) {
    document.getElementById('selectedType').value = type;
    document.getElementById('onboardingForm').submit();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>

@endsection