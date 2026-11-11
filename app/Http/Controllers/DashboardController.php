<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Sale;
use App\Services\BranchScope;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(private readonly BranchScope $branchScope) {}

    public function index()
    {
        $companyId = session('company_id');
        $isConsolidated = $this->branchScope->isConsolidated();
        $branchKey = $isConsolidated ? 'all' : (string) session('branch_id');

        if (! $companyId) {
            return view('dashboard', [
                'totalKas' => 0, 'totalBank' => 0, 'totalJournal' => 0, 'totalAkun' => 0,
                'recentJournals' => collect(), 'chartData' => collect(),
                'isConsolidated' => false,
            ]);
        }

        // Simpan ke Cache selama 3 menit
        $cacheKey = "dashboard.v4.{$companyId}.{$branchKey}";

        $data = Cache::remember($cacheKey, now()->addMinutes(3), function () use ($companyId) {
            return [
                'totalKas' => $this->getBalance($companyId, 'is_cash'),
                'totalBank' => $this->getBalance($companyId, 'is_bank'),
                'totalJournal' => $this->branchScope->apply(
                    Journal::where('company_id', $companyId)->where('status', 'posted')
                )->count(),
                'totalAkun' => Account::where('company_id', $companyId)->count(),
                'chartData' => $this->branchScope->apply(
                    Sale::where('company_id', $companyId)->where('date', '>=', now()->subDays(6)->startOfDay())
                )->selectRaw('DATE(date) as d, SUM(total) as total')->groupBy('d')->orderBy('d')->get(),
            ];
        });

        // Jurnal terbaru tidak di-cache
        $recentJournals = $this->branchScope->apply(Journal::where('company_id', $companyId))
            ->with('creator')->latest('date')->latest('id')->limit(5)->get();

        return view('dashboard', array_merge($data, [
            'recentJournals' => $recentJournals,
            'isConsolidated' => $isConsolidated,
        ]));
    }

    private function getBalance(int $companyId, string $flag): float
    {
        $accountIds = Account::where('company_id', $companyId)->where($flag, true)->pluck('id');
        if ($accountIds->isEmpty()) return 0.0;

        $query = JournalEntry::query()
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
            ->whereIn('journal_entries.account_id', $accountIds)
            ->where('journals.company_id', $companyId)
            ->where('journals.status', 'posted');

        $query = $this->branchScope->apply($query, 'journals.branch_id');

        return (float) ($query->selectRaw('SUM(CASE WHEN accounts.normal_balance = "debit" THEN journal_entries.debit - journal_entries.credit ELSE journal_entries.credit - journal_entries.debit END) AS bal')->value('bal') ?? 0.0);
    }
}