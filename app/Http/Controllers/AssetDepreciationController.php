<?php

namespace App\Http\Controllers;

use App\Models\AssetDepreciation;
use App\Models\FixedAsset;
use App\Services\DepreciationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetDepreciationController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetDepreciation::with(['fixedAsset', 'journal'])->latest('period_date');

        if ($request->filled('from')) $query->where('period_date', '>=', $request->get('from'));
        if ($request->filled('to')) $query->where('period_date', '<=', $request->get('to'));
        if ($request->filled('asset_id')) $query->where('fixed_asset_id', $request->get('asset_id'));

        $depreciations = $query->paginate(30)->withQueryString();
        $assets = FixedAsset::orderBy('name')->get();

        return view('fixed-assets.depreciations', compact('depreciations', 'assets'));
    }

    public function form()
    {
        $period = now()->endOfMonth()->format('Y-m-d');
        return view('fixed-assets.depreciation-run', compact('period'));
    }

    public function run(Request $request)
    {
        $data = $request->validate([
            'period' => 'required|date',
        ]);

        $periodDate = Carbon::parse($data['period'])->endOfMonth();
        $service = new DepreciationService();
        $result = $service->runMonthly(session('company_id'), $periodDate);

        $msg = "Depresiasi selesai: {$result['processed']} aset diproses, {$result['skipped']} dilewati. ";
        $msg .= "Total depresiasi: Rp " . number_format($result['total'], 0, ',', '.') . ".";

        return redirect()->route('fixed-assets.depreciations')->with('success', $msg);
    }
}