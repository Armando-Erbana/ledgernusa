@php
    // Cek apakah user berhak atas fitur Pro
    $planCode = strtolower(auth()->user()->activeCompany()->subscription?->plan?->code ?? 'starter');
    $isPro = in_array($planCode, ['pro', 'enterprise']);
    
    // Ambil daftar cabang jika Pro
    $branches = $isPro ? \App\Models\Branch::where('company_id', session('company_id'))->get() : collect();
    
    // Tentukan cabang yang sedang dipilih (dari URL atau Session)
    $currentBranch = request('branch_id', session('branch_id') ?? 'all');
@endphp

@if($isPro && $branches->count() > 0)
    <form method="GET" action="" class="mb-6 flex items-center justify-end">
        <div class="flex items-center gap-2 bg-white/60 backdrop-blur-md border border-gray-200 rounded-xl px-3 py-1.5 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tampilkan:</span>
            <select name="branch_id" onchange="this.form.submit()" class="bg-transparent border-none text-sm font-semibold text-blue-700 focus:ring-0 cursor-pointer pr-8">
                <option value="all" {{ $currentBranch === 'all' ? 'selected' : '' }}>Konsolidasi (Semua Cabang)</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $currentBranch == $b->id ? 'selected' : '' }}>
                        Cabang {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@endif