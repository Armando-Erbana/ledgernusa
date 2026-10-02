@extends('layouts.app')
@section('title', 'Customer')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Customer</h1>
        <p class="ln-page-sub">{{ $customers->count() }} customer terdaftar</p>
    </div>
    <a href="{{ route('customers.create') }}" class="ln-btn-primary">+ Customer</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Telepon</th>
                <th>Email</th>
                <th class="num">Limit Kredit</th>
                <th class="center">Term</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $c)
            <tr>
                <td><span class="ln-code">{{ $c->code ?: '-' }}</span></td>
                <td><strong>{{ $c->name }}</strong></td>
                <td>{{ $c->phone ?: '-' }}</td>
                <td>{{ $c->email ?: '-' }}</td>
                <td class="num">Rp {{ number_format($c->credit_limit, 0, ',', '.') }}</td>
                <td class="center">{{ $c->payment_term_days }} hari</td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('customers.edit', $c) }}" class="ln-action">Edit</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus customer ini?')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada customer</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection