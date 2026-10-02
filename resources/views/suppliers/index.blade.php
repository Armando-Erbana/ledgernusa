@extends('layouts.app')
@section('title', 'Supplier')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Supplier</h1>
        <p class="ln-page-sub">{{ $suppliers->count() }} supplier terdaftar</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="ln-btn-primary">+ Supplier</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>Kode</th><th>Nama</th><th>Telepon</th><th>Email</th><th class="center">Term</th><th class="center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($suppliers as $s)
            <tr>
                <td><span class="ln-code">{{ $s->code ?: '-' }}</span></td>
                <td><strong>{{ $s->name }}</strong></td>
                <td>{{ $s->phone ?: '-' }}</td>
                <td>{{ $s->email ?: '-' }}</td>
                <td class="center">{{ $s->payment_term_days }} hari</td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('suppliers.edit', $s) }}" class="ln-action">Edit</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus supplier?')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada supplier</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection