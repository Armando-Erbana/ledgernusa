<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LedgerNusa' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

<header class="sticky top-0 z-30 bg-white border-b shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="font-bold text-lg text-indigo-700">LedgerNusa</a>
        <div class="flex items-center gap-3 text-sm">
            @php $activeCompany = auth()->user()->activeCompany(); @endphp
            @if($activeCompany)
                <span class="hidden sm:inline text-gray-600">{{ $activeCompany->name }}</span>
            @endif
            <a href="{{ route('companies.index') }}" class="text-indigo-600 hover:underline">Company</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-500 hover:text-red-600 text-xs">Logout</button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-5 pb-24">
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<nav class="fixed bottom-0 left-0 right-0 bg-white border-t grid grid-cols-5 text-xs shadow-lg">
    <a href="{{ route('dashboard') }}" class="p-3 text-center {{ request()->routeIs('dashboard') ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
        <div class="text-lg">🏠</div>Dashboard
    </a>
    <a href="{{ route('journals.index') }}" class="p-3 text-center {{ request()->routeIs('journals.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
        <div class="text-lg">📒</div>Jurnal
    </a>
    <a href="{{ route('accounts.index') }}" class="p-3 text-center {{ request()->routeIs('accounts.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
        <div class="text-lg">📊</div>COA
    </a>
    <a href="{{ route('contacts.index') }}" class="p-3 text-center {{ request()->routeIs('contacts.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
        <div class="text-lg">👥</div>Kontak
    </a>
    <a href="{{ route('companies.index') }}" class="p-3 text-center {{ request()->routeIs('companies.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
        <div class="text-lg">⚙️</div>Setting
    </a>
</nav>

</body>
</html>