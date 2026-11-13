@extends('super-admin.layout') 
@section('title', 'Balas Tiket')
@section('content')

<div class="ln-page-head">
    <div>
        <a href="{{ route('super-admin.support-tickets.index') }}" class="text-sm text-blue-600 hover:underline mb-2 inline-block">&larr; Kembali ke Daftar</a>
        <h1 class="ln-page-title">Tiket: {{ $ticket->subject }}</h1>
        <p class="ln-page-sub">Dari {{ $ticket->user->name }} ({{ $ticket->company->name ?? '-' }})</p>
    </div>
</div>

<div class="max-w-3xl mx-auto">
    <div class="ln-form-card mb-6">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[var(--hair)]">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-[var(--ink)]">{{ $ticket->user->name }}</div>
                <div class="text-sm text-[var(--mute)]">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-[var(--ink)] leading-relaxed">
            {{ $ticket->message }}
        </div>
    </div>

    @if($ticket->reply)
        <div class="ln-form-card bg-blue-50 border-blue-200 mb-6">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-blue-200">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">
                    A
                </div>
                <div>
                    <div class="font-bold text-blue-900">Admin / CS LedgerNusa</div>
                    <div class="text-sm text-blue-700">Balasan Anda</div>
                </div>
            </div>
            <div class="text-sm text-blue-900 leading-relaxed whitespace-pre-line">
                {{ $ticket->reply }}
            </div>
        </div>
    @endif

    <div class="ln-form-card">
        <h2 class="ln-section-title mb-4">Kirim Balasan</h2>
        <form method="POST" action="{{ route('super-admin.support-tickets.reply', $ticket->id) }}">
            @csrf
            <textarea name="reply" rows="5" class="ln-textarea mb-4" placeholder="Ketik balasan untuk user disini..." required>{{ $ticket->reply }}</textarea>
            <button type="submit" class="ln-btn-primary w-full md:w-auto">Kirim Balasan</button>
        </form>
    </div>
</div>

@endsection