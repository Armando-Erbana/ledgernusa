<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    public function index()
    {
        return view('tax.index');
    }

    public function ppn(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->format('Y-m-d'));
        $companyId = session('company_id');

        $ppnKeluaran = Sale::where('company_id', $companyId)
            ->whereBetween('date', [$from, $to])
            ->whereNotIn('status', ['cancelled'])
            ->with('customer')
            ->orderBy('date')
            ->get();

        $ppnMasukanAccount = \App\Models\Account::where('company_id', $companyId)
            ->where('code', '212')->first();

        $ppnMasukan = collect();
        if ($ppnMasukanAccount) {
            $ppnMasukan = JournalEntry::where('account_id', $ppnMasukanAccount->id)
                ->whereHas('journal', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')->whereBetween('date', [$from, $to]);
                })
                ->with('journal')
                ->get();
        }

        $totalKeluaran = $ppnKeluaran->sum('tax');
        $totalMasukan = $ppnMasukan->sum('debit');
        $selisih = $totalKeluaran - $totalMasukan;
        $status = $selisih > 0 ? 'kurang_bayar' : ($selisih < 0 ? 'lebih_bayar' : 'nihil');

        return view('tax.ppn', compact(
            'ppnKeluaran', 'ppnMasukan',
            'totalKeluaran', 'totalMasukan', 'selisih', 'status',
            'from', 'to'
        ));
    }

    public function pph23(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->format('Y-m-d'));
        $companyId = session('company_id');

        $pphAccount = \App\Models\Account::where('company_id', $companyId)
            ->where('code', '222')->first();

        $pphList = collect();
        if ($pphAccount) {
            $pphList = JournalEntry::where('account_id', $pphAccount->id)
                ->whereHas('journal', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')->whereBetween('date', [$from, $to]);
                })
                ->with('journal')
                ->get();
        }

        $totalPph = $pphList->sum('credit');

        return view('tax.pph23', compact('pphList', 'totalPph', 'from', 'to'));
    }

    public function summary(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $companyId = session('company_id');

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $keluaran = Sale::where('company_id', $companyId)
                ->whereBetween('date', [$start, $end])
                ->whereNotIn('status', ['cancelled'])
                ->sum('tax');

            $masukan = 0;
            $ppnIn = \App\Models\Account::where('company_id', $companyId)->where('code', '212')->first();
            if ($ppnIn) {
                $masukan = JournalEntry::where('account_id', $ppnIn->id)
                    ->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$start, $end]))
                    ->sum('debit');
            }

            $data[] = [
                'month' => $start->format('F'),
                'keluaran' => $keluaran,
                'masukan' => $masukan,
                'selisih' => $keluaran - $masukan,
            ];
        }

        $yearly = [
            'keluaran' => collect($data)->sum('keluaran'),
            'masukan' => collect($data)->sum('masukan'),
            'selisih' => collect($data)->sum('selisih'),
        ];

        return view('tax.summary', compact('data', 'yearly', 'year'));
    }
}