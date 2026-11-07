@extends('layouts.app')

@section('title', 'Langganan Berakhir')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- HEADER: Peringatan --}}
    <div class="bg-white border border-red-200 rounded-2xl p-6 mb-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-orange-400"></div>

        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>

            <div class="flex-1">
                <h1 class="ln-page-title text-red-700">Masa Langganan Berakhir</h1>
                <p class="text-sm text-gray-600 mt-2">
                    Akses ke fitur LedgerNusa untuk
                    <strong>{{ $company->name ?? 'perusahaan Anda' }}</strong>
                    @if($subscription && $subscription->ends_at)
                        telah berakhir pada
                        <strong>{{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M Y') }}</strong>.
                    @else
                        telah berakhir.
                    @endif
                    Pilih paket di bawah untuk melanjutkan.
                </p>
            </div>
        </div>
    </div>

    {{-- INFO LANGGANAN SEBELUMNYA --}}
    @if($subscription)
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Paket Terakhir</p>
                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $subscription->plan->name ?? 'Tidak diketahui' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Status</p>
                    <p class="font-semibold text-red-600 mt-1 uppercase">
                        {{ $subscription->status ?? 'expired' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- DAFTAR PAKET --}}
    <div class="mb-4">
        <h2 class="ln-section-title text-lg">Pilih Paket Baru</h2>
        <p class="text-sm text-gray-500">Semua data dan transaksi Anda tetap tersimpan dan bisa diakses kembali setelah perpanjangan.</p>
    </div>

    @if($plans->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500">
            Belum ada paket tersedia. Hubungi admin untuk info lebih lanjut.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($plans as $plan)
                <div class="bg-white border rounded-2xl p-5 shadow-sm hover:shadow-md transition {{ $loop->index === 1 ? 'border-blue-500 ring-1 ring-blue-200' : 'border-gray-200' }}">
                    @if($loop->index === 1)
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full mb-2">POPULER</span>
                    @endif

                    <h3 class="font-bold text-lg text-gray-800">{{ $plan->name }}</h3>
                    @if($plan->description ?? null)
                        <p class="text-xs text-gray-500 mt-1">{{ $plan->description }}</p>
                    @endif

                    <div class="mt-4 mb-4">
                        <span class="text-2xl font-bold text-gray-800">
                            Rp {{ number_format($plan->price ?? 0, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-gray-500">
                            / {{ $plan->duration_days ?? 30 }} hari
                        </span>
                    </div>

                    @if(isset($plan->features) && is_array($plan->features))
                        <ul class="space-y-1.5 mb-5 text-sm text-gray-600">
                            @foreach($plan->features as $feature)
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <a href="{{ route('subscription.index') }}?plan={{ $plan->id }}"
                       class="block text-center w-full bg-gradient-to-b from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-medium py-2.5 rounded-xl transition">
                        Pilih Paket Ini
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- FOOTER ACTIONS --}}
    <div class="flex flex-wrap gap-3 justify-center pt-4 border-t border-gray-200">
        <a href="{{ route('subscription.index') }}" class="ln-btn-outline">
            Kembali ke Halaman Langganan
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ln-btn-cancel">Logout</button>
        </form>
    </div>

</div>
@endsection