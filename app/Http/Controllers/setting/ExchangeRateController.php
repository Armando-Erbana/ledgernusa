<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function __construct(private CurrencyService $currencyService)
    {
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->activeCompany()->id ?? session('company_id');

        $rates = ExchangeRate::where('company_id', $companyId)
            ->when($request->from, fn ($q, $v) => $q->where('from_currency', $v))
            ->orderByDesc('effective_date')
            ->paginate(30);

        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('settings.exchange-rates.index', compact('rates', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_currency' => 'required|size:3',
            'to_currency' => 'required|size:3|different:from_currency',
            'rate' => 'required|numeric|min:0.00000001',
            'effective_date' => 'required|date',
        ]);

        $companyId = session('company_id');

        ExchangeRate::updateOrCreate(
            [
                'company_id' => $companyId,
                'from_currency' => strtoupper($data['from_currency']),
                'to_currency' => strtoupper($data['to_currency']),
                'effective_date' => $data['effective_date'],
            ],
            [
                'rate' => $data['rate'],
                'source' => 'manual',
            ]
        );

        return back()->with('success', 'Kurs berhasil disimpan.');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        $exchangeRate->delete();
        return back()->with('success', 'Kurs berhasil dihapus.');
    }

    public function fetchRate(Request $request)
    {
        $request->validate([
            'from' => 'required|size:3',
            'to' => 'required|size:3',
            'date' => 'nullable|date',
        ]);

        try {
            $rate = $this->currencyService->getRate(
                session('company_id'),
                strtoupper($request->from),
                strtoupper($request->to),
                $request->date
            );

            return response()->json(['rate' => $rate]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}