@extends('layouts.app')
@section('title', 'Tutup Buku')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Tutup Buku Tahunan</h1>
        <p class="ln-page-sub">Pindahkan Laba/Rugi ke Laba Ditahan & kunci transaksi tahun lalu.</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="ln-form-card">
        <h2 class="ln-section-title text-lg mb-4">Proses Tutup Buku</h2>
        
        @if($company->closed_until)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm">
                Tahun buku terakhir yang sudah ditutup: <strong>{{ $company->closed_until->format('d M Y') }}</strong>.<br>
                Semua transaksi dengan tanggal sebelum atau sama dengan tanggal tersebut tidak dapat diubah/dihapus.
            </div>
        @endif

        <form method="POST" action="{{ route('closing.store') }}">
            @csrf
            
            <div class="mb-4">
                <label class="ln-label">Pilih Tahun yang Akan Ditutup *</label>
                <select name="year" class="ln-select" required>
                    @for($i = date('Y') - 5; $i <= date('Y'); $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <p class="text-xs text-[var(--mute)] mt-2">
                    Pastikan seluruh transaksi untuk tahun yang dipilih sudah diinput dan diposting. Proses ini akan membuat Jurnal Penutup secara otomatis dan mengunci tanggal transaksi.
                </p>
            </div>

            <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-6">
                <h3 class="text-sm font-bold text-red-800 mb-1">Peringatan!</h3>
                <p class="text-xs text-red-700">
                    Tindakan ini tidak dapat dibatalkan. Sistem akan memindahkan seluruh saldo Pendapatan dan Beban ke akun Laba Ditahan.
                </p>
            </div>

            <button type="submit" class="ln-btn-primary" onclick="return confirm('Apakah Anda yakin ingin menutup buku tahun ini? Transaksi lama tidak akan bisa diedit lagi.')">
                Proses Tutup Buku
            </button>
        </form>
    </div>
</div>

@endsection