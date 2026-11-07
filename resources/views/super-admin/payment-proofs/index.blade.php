@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <h1 class="ln-page-title">Verifikasi Pembayaran Manual</h1>
        <p class="text-sm text-gray-500 mt-1">Bukti transfer dari perusahaan untuk perpanjangan langganan.</p>
    </div>

    {{-- Tab Filter --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
            @php
                $isActive = $status === $key || ($key === 'all' && !in_array($status, ['pending','approved','rejected']));
            @endphp
            <a href="{{ route('super-admin.payment-proofs.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition
               {{ $isActive ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
                @if($key !== 'all' && isset($counts[$key]))
                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $isActive ? 'bg-blue-500' : 'bg-gray-200' }}">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="ln-table">
            <thead>
                <tr>
                    <th>Perusahaan</th>
                    <th>Paket</th>
                    <th class="num">Nominal</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proofs as $proof)
                    <tr>
                        <td>
                            <div class="font-semibold">{{ $proof->company->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $proof->user->name ?? '-' }}</div>
                        </td>
                        <td>
                            {{ $proof->plan->name ?? '-' }}
                            <div class="text-xs text-gray-500">{{ $proof->plan->duration_days ?? 30 }} hari</div>
                        </td>
                        <td class="num">Rp {{ number_format($proof->amount, 0, ',', '.') }}</td>
                        <td>{{ $proof->transfer_date->format('d M Y') }}</td>
                        <td>
                            @if($proof->isPending())
                                <span class="ln-badge draft">Menunggu</span>
                            @elseif($proof->isApproved())
                                <span class="ln-badge posted">Disetujui</span>
                            @else
                                <span class="ln-badge out">Ditolak</span>
                            @endif
                        </td>
                        <td class="center">
                            <a href="{{ route('super-admin.payment-proofs.show', $proof) }}" class="ln-action">
                                Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada bukti pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $proofs->links() }}</div>
</div>
@endsection