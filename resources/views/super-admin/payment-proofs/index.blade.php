@extends('super-admin.layout')
@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="max-w-6xl mx-auto py-4">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Verifikasi Pembayaran Manual</h1>
        <p class="text-sm text-gray-500 mt-1">Bukti transfer dari perusahaan untuk perpanjangan langganan.</p>
    </div>

    {{-- Tab Filter --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
            @php
                // Cek apakah tab ini yang sedang aktif
                $isActive = ($status == $key);
            @endphp
            <a href="{{ route('super-admin.payment-proofs.index', ['status' => $key]) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200
               {{ $isActive 
                    ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600 ring-offset-2' 
                    : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-gray-900' }}">
                {{ $label }}
                @if($key !== 'all' && isset($counts[$key]))
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $isActive ? 'bg-blue-700 text-blue-100' : 'bg-gray-100 text-gray-600' }}">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs font-semibold text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Perusahaan</th>
                        <th scope="col" class="px-6 py-4">Paket</th>
                        <th scope="col" class="px-6 py-4 text-right">Nominal</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proofs as $proof)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $proof->company->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $proof->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $proof->plan->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $proof->plan->duration_days ?? 30 }} hari</div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($proof->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $proof->transfer_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($proof->isPending())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif($proof->isApproved())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('super-admin.payment-proofs.show', $proof) }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    Detail <span class="ml-1">&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Tidak ada data bukti pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $proofs->links() }}
    </div>
</div>
@endsection