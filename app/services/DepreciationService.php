<?php

namespace App\Services;

use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    public function runMonthly(int $companyId, ?Carbon $periodDate = null): array
    {
        $periodDate = $periodDate ?? now()->endOfMonth();

        $assets = FixedAsset::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $processed = 0;
        $skipped = 0;
        $total = 0;

        foreach ($assets as $asset) {
            if ($asset->depreciations()->where('period_date', $periodDate->format('Y-m-d'))->exists()) {
                $skipped++;
                continue;
            }

            if ($asset->isFullyDepreciated()) {
                $asset->update(['status' => 'fully_depreciated']);
                $skipped++;
                continue;
            }

            $amount = $this->calculateDepreciation($asset);

            $depreciable = (float) $asset->purchase_cost - (float) $asset->residual_value;
            $remaining = $depreciable - (float) $asset->accumulated_depreciation;
            $amount = min($amount, $remaining);

            if ($amount <= 0) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($asset, $periodDate, $amount) {
                $newAccum = (float) $asset->accumulated_depreciation + $amount;
                $newBookValue = (float) $asset->purchase_cost - $newAccum;

                $journal = $asset->generateDepreciationJournal($periodDate, $amount);

                AssetDepreciation::create([
                    'company_id' => $asset->company_id,
                    'fixed_asset_id' => $asset->id,
                    'journal_id' => $journal->id,
                    'period_date' => $periodDate,
                    'amount' => $amount,
                    'accumulated_after' => $newAccum,
                    'book_value_after' => $newBookValue,
                    'created_by' => auth()->id(),
                ]);

                $asset->update([
                    'accumulated_depreciation' => $newAccum,
                    'book_value' => $newBookValue,
                    'last_depreciation_date' => $periodDate,
                ]);

                if ($asset->fresh()->isFullyDepreciated()) {
                    $asset->update(['status' => 'fully_depreciated']);
                }
            });

            $processed++;
            $total += $amount;
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'total' => $total,
        ];
    }

    protected function calculateDepreciation(FixedAsset $asset): float
    {
        return match ($asset->depreciation_method) {
            'straight_line' => $asset->monthlyDepreciation(),
            'double_declining' => $this->doubleDeclining($asset),
            'sum_of_years' => $this->sumOfYears($asset),
            default => $asset->monthlyDepreciation(),
        };
    }

    protected function doubleDeclining(FixedAsset $asset): float
    {
        $years = $asset->useful_life_months / 12;
        if ($years <= 0) return 0;
        $rate = (2 / $years) / 12;
        $bookValue = (float) $asset->book_value;
        return round($bookValue * $rate, 2);
    }

    protected function sumOfYears(FixedAsset $asset): float
    {
        $n = $asset->useful_life_months;
        if ($n <= 0) return 0;
        $depreciable = (float) $asset->purchase_cost - (float) $asset->residual_value;
        $sumOfYears = $n * ($n + 1) / 2;
        $monthNumber = $asset->monthsDepreciated() + 1;
        $remainingMonths = $n - $monthNumber + 1;
        return round(($remainingMonths / $sumOfYears) * $depreciable, 2);
    }
}