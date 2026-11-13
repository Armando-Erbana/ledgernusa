@extends('layouts.app')
@section('title', 'Struktur Organisasi & Persetujuan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Struktur Organisasi</h1>
        <p class="ln-page-sub">Atur Divisi, Jabatan, dan Aturan Persetujuan (Approval)</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- FORM DIVISI -->
    <div class="ln-form-card">
        <h2 class="ln-section-title">1. Tambah Divisi</h2>
        <form method="POST" action="{{ route('organization.division') }}" class="flex gap-2">
            @csrf
            <input type="text" name="name" required placeholder="Misal: Pembelian, Keuangan" class="ln-input">
            <button class="ln-btn-primary">Tambah</button>
        </form>
        <ul class="mt-4 space-y-2">
            @foreach($divisions as $div)
                <li class="flex justify-between items-center bg-gray-50 p-2 rounded">
                    <span><strong>{{ $div->name }}</strong></span>
                    <form method="POST" action="{{ route('organization.division.destroy', $div) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-500 text-xs font-bold">Hapus</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- FORM JABATAN -->
    <div class="ln-form-card">
        <h2 class="ln-section-title">2. Tambah Jabatan</h2>
        <form method="POST" action="{{ route('organization.position') }}" class="space-y-3">
            @csrf
            <select name="division_id" required class="ln-select">
                <option value="">— Pilih Divisi —</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <input type="text" name="name" required placeholder="Misal: Manager, Officer" class="ln-input">
                <button class="ln-btn-primary">Tambah</button>
            </div>
        </form>
        <ul class="mt-4 space-y-2">
            @foreach($positions as $pos)
                <li class="flex justify-between items-center bg-gray-50 p-2 rounded">
                    <span>{{ $pos->division->name }} - <strong>{{ $pos->name }}</strong></span>
                    <form method="POST" action="{{ route('organization.position.destroy', $pos) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-500 text-xs font-bold">Hapus</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- FORM ATURAN APPROVAL -->
<div class="ln-form-card">
    <h2 class="ln-section-title">3. Aturan Persetujuan (Approval Matrix)</h2>
    <form method="POST" action="{{ route('organization.rule') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end mb-6">
        @csrf
        <div class="md:col-span-1">
            <label class="ln-label">Dokumen</label>
            <select name="document_type" class="ln-select">
                <option value="purchase_order">Purchase Order</option>
                <option value="sales_order">Sales Order</option>
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="ln-label">Min (Rp)</label>
            <input type="number" name="min_amount" value="0" required class="ln-input">
        </div>
        <div class="md:col-span-1">
            <label class="ln-label">Max (Rp)</label>
            <input type="number" name="max_amount" placeholder="Kosongkan jika tanpa batas" class="ln-input">
        </div>
        <div class="md:col-span-1">
            <label class="ln-label">Jabatan (ACC)</label>
            <select name="position_id" required class="ln-select">
                <option value="">— Pilih —</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}">{{ $pos->division->name }} - {{ $pos->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="ln-label">Level (1,2,dst)</label>
            <input type="number" name="level" value="1" required class="ln-input">
        </div>
        <div class="md:col-span-1">
            <button class="ln-btn-primary w-full">Simpan Aturan</button>
        </div>
    </form>

    <div class="ln-table-card">
        <table class="ln-table">
            <thead>
                <tr><th>Dokumen</th><th>Nominal</th><th>Level</th><th>Jabatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($rules as $rule)
                <tr>
                    <td>{{ $rule->document_type === 'purchase_order' ? 'Purchase Order' : 'Sales Order' }}</td>
                    <td>Rp {{ number_format($rule->min_amount, 0, ',', '.') }} - {{ $rule->max_amount ? 'Rp '.number_format($rule->max_amount, 0, ',', '.') : '∞' }}</td>
                    <td>Level {{ $rule->level }}</td>
                    <td>{{ $rule->position->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('organization.rule.destroy', $rule) }}">
                            @csrf @method('DELETE')
                            <button class="ln-action red">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection