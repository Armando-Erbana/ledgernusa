<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Company;
use App\Models\Journal;
use App\Services\CurrencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevalueForeignAccounts extends Command
{
    protected $signature = 'currency:revalue {--company=} {--date=}';
    protected $description = 'Revaluasi akun valas ke kurs terkini';

    public function handle(CurrencyService $currencyService): int
    {
        $companyId = $this->option('company') ?? Company::first()?->id;
        $company = Company::findOrFail($companyId);
        $companyCurrency = $company->currency ?? 'IDR';
        $revalDate = $this->option('date') ?? now()->toDateString();

        $accounts = Account::where('company_id', $companyId)
            ->where('is_multi_currency', true)
            ->where('currency', '!=', $companyCurrency)
            ->get();

        if ($accounts->isEmpty()) {
            $this->warn('Tidak ada akun multi-currency.');
            return self::SUCCESS;
        }

        $gainLoss = Account::firstOrCreate(
            ['company_id' => $companyId, 'code' => '7-1000'],
            [
                'name' => 'Unrealized Exchange Gain/Loss',
                'type' => 'revenue',
                'normal_balance' => 'credit',
                'currency' => $companyCurrency,
            ]
        );

        DB::transaction(function () use (
            $accounts, $currencyService, $companyId,
            $companyCurrency, $revalDate, $gainLoss
        ) {
            foreach ($accounts as $account) {
                $foreign = (float) DB::table('journal_entries as je')
                    ->join('journals as j', 'j.id', '=', 'je.journal_id')
                    ->where('je.account_id', $account->id)
                    ->where('j.company_id', $companyId)
                    ->where('j.date', '<=', $revalDate)
                    ->selectRaw('SUM(COALESCE(je.foreign_debit,0) - COALESCE(je.foreign_credit,0)) as bal')
                    ->value('bal');

                if (abs($foreign) < 0.01) continue;

                $base = (float) DB::table('journal_entries as je')
                    ->join('journals as j', 'j.id', '=', 'je.journal_id')
                    ->where('je.account_id', $account->id)
                    ->where('j.company_id', $companyId)
                    ->where('j.date', '<=', $revalDate)
                    ->selectRaw('SUM(COALESCE(je.debit,0) - COALESCE(je.credit,0)) as bal')
                    ->value('bal');

                $newRate = $currencyService->getRate(
                    $companyId, $account->currency, $companyCurrency, $revalDate
                );

                $diff = round($foreign * $newRate - $base, 2);
                if (abs($diff) < 0.01) continue;

                $journal = Journal::create([
                    'company_id' => $companyId,
                    'date' => $revalDate,
                    'currency' => $companyCurrency,
                    'exchange_rate' => 1,
                    'reference' => 'REVAL-' . $account->code,
                    'description' => "Revaluasi {$account->currency} @ {$newRate}",
                ]);

                $abs = abs($diff);

                if ($diff > 0) {
                    $journal->entries()->createMany([
                        ['account_id' => $account->id,  'debit' => $abs, 'credit' => 0,    'foreign_debit' => 0, 'foreign_credit' => 0],
                        ['account_id' => $gainLoss->id, 'debit' => 0,    'credit' => $abs, 'foreign_debit' => 0, 'foreign_credit' => 0],
                    ]);
                } else {
                    $journal->entries()->createMany([
                        ['account_id' => $account->id,  'debit' => 0,    'credit' => $abs, 'foreign_debit' => 0, 'foreign_credit' => 0],
                        ['account_id' => $gainLoss->id, 'debit' => $abs, 'credit' => 0,    'foreign_debit' => 0, 'foreign_credit' => 0],
                    ]);
                }

                $this->info("  {$account->code}: revaluasi " . number_format($diff, 2));
            }
        });

        $this->info('Revaluasi selesai.');
        return self::SUCCESS;
    }
}