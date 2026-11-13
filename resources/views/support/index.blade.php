@extends('layouts.app')
@section('title', 'Pusat Bantuan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Pusat Bantuan</h1>
        <p class="ln-page-sub">Kirim pertanyaan atau kendala Anda ke tim LedgerNusa</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl">
    <div class="md:col-span-1">
        <div class="ln-form-card space-y-4">
            <h2 class="ln-section-title">Kirim Pesan Baru</h2>
            <form method="POST" action="{{ route('support.store') }}">
                @csrf
                <div>
                    <label class="ln-label">Subjek *</label>
                    <input type="text" name="subject" class="ln-input" placeholder="Contoh: Error saat input jurnal" required>
                </div>
                <div>
                    <label class="ln-label">Pesan *</label>
                    <textarea name="message" class="ln-textarea" rows="4" placeholder="Jelaskan kendala Anda secara detail..." required></textarea>
                </div>
                <button type="submit" class="ln-btn-primary w-full">Kirim Tiket</button>
            </form>
        </div>
    </div>

    <div class="md:col-span-2">
        <h2 class="ln-section-title">Riwayat Tiket Anda</h2>
        <div class="space-y-4">
            @forelse($tickets as $t)
                <div class="ln-form-card">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-bold text-[var(--ink)]">{{ $t->subject }}</div>
                        <span class="ln-badge {{ $t->status === 'open' ? 'draft' : 'posted' }}">{{ $t->status }}</span>
                    </div>
                    <div class="text-sm text-[var(--mute)] mb-2">{{ $t->created_at->format('d M Y, H:i') }}</div>
                    <div class="text-sm text-[var(--soft)] bg-gray-50 p-3 rounded-lg border">{{ $t->message }}</div>
                    
                    @if($t->reply)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <strong class="text-blue-800 text-sm block mb-1">Balasan dari Tim LedgerNusa:</strong>
                            <div class="text-sm text-blue-900">{{ $t->reply }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="ln-form-card text-center py-10 text-[var(--mute)]">
                    Belum ada tiket bantuan.
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection