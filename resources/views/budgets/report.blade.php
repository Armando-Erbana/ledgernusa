@extends('layouts.app')
@section('title', 'Laporan Budget vs Realisasi')

@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Budget vs Realisasi</h1>
        <p class="ln-page-sub">Bandingkan rencana anggaran dengan pengeluaran aktual</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('budgets.index', ['month' => $month, 'year' => $year]) }}" class="ln-btn-outline">Input Budget</a>
    </div>
</div>

@if(!$branchId)
    <div class="ln-alert ln-alert-error mb-4" role="alert">
        Anda sedang berada di mode Konsolidasi. Silakan pilih cabang spesifik di pojok kanan atas untuk melihat laporan Budget.
    </div>
@endif

<form method="GET" class="ln-form-card mb-6 flex items-end gap-3">
    <div>
        <label class="ln-label">Bulan</label>
        <select name="month" class="ln-select" onchange="this.form.submit()">
            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}" @selected($month == $i)>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
            @endfor
        </select>
    </div>
    <div>
        <label class="ln-label">Tahun</label>
        <input type="number" name="year" value="{{ $year }}" class="ln-input" onchange="this.form.submit()">
    </div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Akun</th>
                <th class="num">Budget (Rp)</th>
                <th class="num">Realisasi (Rp)</th>
                <th class="num">Selisih (Rp)</th>
                <th class="center" style="width: 30%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $data)
                @php
                    $isOver = $data->actual > $data->amount;
                    // Jika tipe akun adalah pendapatan, realisasi yang lebih kecil dari budget dianggap kurang baik
                    $isBad = $data->account->type === 'expense' ? $isOver : $data->actual < $data->amount;
                    
                    $pct = min(100, $data->percentage); // Batasi progress bar maksimal 100%
                    $barColor = $isBad ? '#B5362F' : '#1E7A4C'; // Merah jika Over, Hijau jika Aman
                @endphp
                <tr>
                    <td>
                        <div class="font-semibold text-[var(--ink)]">{{ $data->account->name }}</div>
                        <div class="ln-code">{{ $data->account->code }} ({{ ucfirst($data->account->type) }})</div>
                    </td>
                    <td class="num">{{ number_format($data->amount, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($data->actual, 0, ',', '.') }}</td>
                    <td class="num font-bold {{ $isBad ? 'text-[#B5362F]' : 'text-[#1E7A4C]' }}">
                        {{ number_format($data->difference, 0, ',', '.') }}
                    </td>
                    <td class="center">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $barColor }};"></div>
                            </div>
                            <span class="text-xs font-bold {{ $isBad ? 'text-[#B5362F]' : 'text-[#1E7A4C]' }}">
                                {{ round($data->percentage, 1) }}%
                            </span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">
                        Belum ada data budget untuk periode ini. Silakan <a href="{{ route('budgets.index', ['month' => $month, 'year' => $year]) }}" class="text-blue-600 underline">Input Budget</a> terlebih dahulu.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection