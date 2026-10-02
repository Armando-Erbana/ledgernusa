<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgingReportController extends Controller
{
    public function piutang(Request $request)
    {
        $asOf = $request->get('as_of', now()->format('Y-m-d'));
        $companyId = session('company_id');

        $sales = Sale::where('company_id', $companyId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('type', 'credit')
            ->whereDate('date', '<=', $asOf)
            ->with('customer')
            ->get();

        $buckets = [
            'current' => [],
            'd1_30' => [],
            'd31_60' => [],
            'd61_90' => [],
            'd90_plus' => [],
        ];

        $asOfDate = Carbon::parse($asOf);

        foreach ($sales as $sale) {
            $dueDate = $sale->due_date ? Carbon::parse($sale->due_date) : Carbon::parse($sale->date)->addDays(30);
            $daysOverdue = $asOfDate->diffInDays($dueDate, false);

            $balance = $sale->total - $sale->paid_amount;

            if ($daysOverdue >= 0) $buckets['current'][] = ['sale' => $sale, 'balance' => $balance, 'days' => 0];
            elseif ($daysOverdue >= -30) $buckets['d1_30'][] = ['sale' => $sale, 'balance' => $balance, 'days' => abs($daysOverdue)];
            elseif ($daysOverdue >= -60) $buckets['d31_60'][] = ['sale' => $sale, 'balance' => $balance, 'days' => abs($daysOverdue)];
            elseif ($daysOverdue >= -90) $buckets['d61_90'][] = ['sale' => $sale, 'balance' => $balance, 'days' => abs($daysOverdue)];
            else $buckets['d90_plus'][] = ['sale' => $sale, 'balance' => $balance, 'days' => abs($daysOverdue)];
        }

        $totals = [
            'current' => collect($buckets['current'])->sum('balance'),
            'd1_30' => collect($buckets['d1_30'])->sum('balance'),
            'd31_60' => collect($buckets['d31_60'])->sum('balance'),
            'd61_90' => collect($buckets['d61_90'])->sum('balance'),
            'd90_plus' => collect($buckets['d90_plus'])->sum('balance'),
        ];
        $totals['grand'] = array_sum($totals);

        return view('reports.aging-piutang', compact('buckets', 'totals', 'asOf'));
    }

    public function hutang(Request $request)
    {
        $asOf = $request->get('as_of', now()->format('Y-m-d'));
        $companyId = session('company_id');

        $purchases = Purchase::where('company_id', $companyId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('type', 'credit')
            ->whereDate('date', '<=', $asOf)
            ->with('supplier')
            ->get();

        $buckets = [
            'current' => [],
            'd1_30' => [],
            'd31_60' => [],
            'd61_90' => [],
            'd90_plus' => [],
        ];

        $asOfDate = Carbon::parse($asOf);

        foreach ($purchases as $purchase) {
            $dueDate = $purchase->due_date ? Carbon::parse($purchase->due_date) : Carbon::parse($purchase->date)->addDays(30);
            $daysOverdue = $asOfDate->diffInDays($dueDate, false);
            $balance = $purchase->total - $purchase->paid_amount;

            if ($daysOverdue >= 0) $buckets['current'][] = ['item' => $purchase, 'balance' => $balance, 'days' => 0];
            elseif ($daysOverdue >= -30) $buckets['d1_30'][] = ['item' => $purchase, 'balance' => $balance, 'days' => abs($daysOverdue)];
            elseif ($daysOverdue >= -60) $buckets['d31_60'][] = ['item' => $purchase, 'balance' => $balance, 'days' => abs($daysOverdue)];
            elseif ($daysOverdue >= -90) $buckets['d61_90'][] = ['item' => $purchase, 'balance' => $balance, 'days' => abs($daysOverdue)];
            else $buckets['d90_plus'][] = ['item' => $purchase, 'balance' => $balance, 'days' => abs($daysOverdue)];
        }

        $totals = [
            'current' => collect($buckets['current'])->sum('balance'),
            'd1_30' => collect($buckets['d1_30'])->sum('balance'),
            'd31_60' => collect($buckets['d31_60'])->sum('balance'),
            'd61_90' => collect($buckets['d61_90'])->sum('balance'),
            'd90_plus' => collect($buckets['d90_plus'])->sum('balance'),
        ];
        $totals['grand'] = array_sum($totals);

        return view('reports.aging-hutang', compact('buckets', 'totals', 'asOf'));
    }
}