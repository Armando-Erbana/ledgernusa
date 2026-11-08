<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index(Request $request, ExchangeRateService $service)
    {
        $companyId = (int) session('company_id');

        $query = ExchangeRate::where('company_id', $companyId);

        if ($request->filled('from_currency')) {
            $query->where('from_currency', strtoupper($request->string('from_currency')));
        }
        if ($request->filled('to_currency')) {
            $query->where('to_currency', strtoupper($request->string('to_currency')));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('effective_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('effective_date', '<=', $request->date('date_to'));
        }
        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->string('source') . '%');
        }

        $rates = $query->latest('effective_date')->paginate(20)->withQueryString();

        $fromCurrencies = ExchangeRate::where('company_id', $companyId)
            ->distinct()->pluck('from_currency')->sort()->values();

        $toCurrencies = ExchangeRate::where('company_id', $companyId)
            ->distinct()->pluck('to_currency')->sort()->values();

        $availableCurrencies = $service->currencies() ?: [
            'IDR' => 'Indonesian Rupiah',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'SGD' => 'Singapore Dollar',
            'JPY' => 'Japanese Yen',
            'GBP' => 'British Pound',
        ];

        // PERUBAHAN ADA DI SINI: Menambahkan .index
        return view('settings.exchange-rates.index', compact(
            'rates', 'fromCurrencies', 'toCurrencies', 'availableCurrencies'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        ExchangeRate::updateOrCreate(
            [
                'company_id' => (int) session('company_id'),
                'from_currency' => strtoupper($data['from_currency']),
                'to_currency' => strtoupper($data['to_currency']),
                'effective_date' => $data['effective_date'],
            ],
            [
                'rate' => $data['rate'],
                'source' => 'Manual',
            ]
        );

        return back()->with('success', 'Kurs manual berhasil disimpan.');
    }

    public function sync(Request $request, ExchangeRateService $service)
    {
        $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3|different:from_currency',
        ]);

        $from = strtoupper($request->input('from_currency'));
        $to = strtoupper($request->input('to_currency'));
        $companyId = (int) session('company_id');

        $rate = $service->fetchLatest($from, $to);

        if ($rate === null) {
            return back()->with('error', "Gagal mengambil kurs {$from} → {$to} dari Frankfurter.");
        }

        ExchangeRate::updateOrCreate(
            [
                'company_id' => $companyId,
                'from_currency' => $from,
                'to_currency' => $to,
                'effective_date' => now()->toDateString(),
            ],
            [
                'rate' => $rate,
                'source' => 'Frankfurter (ECB)',
            ]
        );

        return back()->with('success', "Kurs {$from} → {$to} berhasil disinkronkan: {$rate}");
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        abort_if($exchangeRate->company_id !== (int) session('company_id'), 403);
        $exchangeRate->delete();

        return back()->with('success', 'Kurs berhasil dihapus.');
    }
}