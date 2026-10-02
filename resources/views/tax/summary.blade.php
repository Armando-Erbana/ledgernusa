@extends('layouts.app')
@section('title', 'Rekap Pajak Tahunan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Rekap Pajak {{ $year }}</h1>
        <p class="ln-page-sub">Ringkasan PPN 12 bulan</p>
    </div>
    <a href="{{ route('tax.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="ln-label">Tahun</label>
        <select name="year" class="ln-select">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="flex items-end md:col-span-2"><button class="ln-btn-primary">Tampilkan</button></div>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    <div class="ln-stat bank"><div class="lbl">Total PPN Keluaran</div><div class="val">Rp {{ number_format($yearly['keluaran'], 0, ',', '.') }}</div></div>
    <div class="ln-stat kas"><div class="lbl">Total PPN Masukan</div><div class="val">Rp {{ number_format($yearly['masukan'], 0, ',', '.') }}</div></div>
    <div class="ln-stat {{ $yearly['selisih'] > 0 ? 'out' : 'in' }}"><div class="lbl">Selisih</div><div class="val">Rp {{ number_format(abs($yearly['selisih']), 0, ',', '.') }}</div></div>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="num">PPN Keluaran</th>
                <th class="num">PPN Masukan</th>
                <th class="num">Selisih</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td class="num">Rp {{ number_format($row['keluaran'], 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($row['masukan'], 0, ',', '.') }}</td>
                <td class="num" style="color:{{ $row['selisih'] > 0 ? '#C0392B' : '#1E7A4C' }}">
                    Rp {{ number_format(abs($row['selisih']), 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection