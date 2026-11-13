@extends('super-admin.layout')
@section('title', 'Pusat Bantuan (CS)')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Pusat Bantuan (CS)</h1>
        <p class="ln-page-sub">Daftar tiket dan keluhan dari pengguna</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse($tickets as $ticket)
        <a href="{{ route('super-admin.support-tickets.show', $ticket->id) }}" class="ln-form-card block hover:ring-2 hover:ring-blue-500 transition-all cursor-pointer">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-[var(--ink)]">{{ $ticket->user->name }}</div>
                        <div class="text-xs text-[var(--mute)]">{{ $ticket->company->name ?? '-' }}</div>
                    </div>
                </div>
                <span class="ln-badge {{ $ticket->status === 'open' ? 'draft' : 'posted' }}">{{ $ticket->status }}</span>
            </div>
            
            <h3 class="font-semibold text-[var(--ink)] mb-1 truncate">{{ $ticket->subject }}</h3>
            <p class="text-sm text-[var(--mute)] line-clamp-2">{{ $ticket->message }}</p>
            
            <div class="mt-4 text-xs text-[var(--faint)] text-right">
                {{ $ticket->created_at->diffForHumans() }}
            </div>
        </a>
    @empty
        <div class="col-span-3 text-center py-10 text-[var(--mute)]">
            Belum ada tiket bantuan yang masuk.
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $tickets->links() }}
</div>

@endsection