<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('code')->paginate(20);
        return view('settings.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('settings.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:50',
            'symbol' => 'nullable|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
        ]);

        $data['code'] = strtoupper($data['code']);
        Currency::create($data + ['is_active' => true]);

        return redirect()->route('settings.currencies.index')
            ->with('success', 'Currency berhasil ditambahkan.');
    }

    public function edit(Currency $currency)
    {
        return view('settings.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'nullable|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'is_active' => 'boolean',
        ]);

        $currency->update($data);

        return redirect()->route('settings.currencies.index')
            ->with('success', 'Currency berhasil diupdate.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->code === 'IDR') {
            return back()->with('error', 'IDR tidak dapat dihapus.');
        }

        $currency->delete();
        return back()->with('success', 'Currency berhasil dihapus.');
    }
}