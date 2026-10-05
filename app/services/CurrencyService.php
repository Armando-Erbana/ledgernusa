<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class CurrencyService
{
    public function getRate(int $companyId, string $from, string $to, $date = null): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $date = $date ? Carbon::parse($date) : now();
        $key = "rate:{$companyId}:{$from}:{$to}:" . $date->toDateString();

        return Cache::remember($key, 3600, function () use ($companyId, $from, $to, $date) {
            $rate = ExchangeRate::forPair($companyId, $from, $to)
                ->where('effective_date', '<=', $date)
                ->orderByDesc('effective_date')
                ->first();

            if ($rate) {
                return (float) $rate->rate;
            }

            $inverse = ExchangeRate::forPair($companyId, $to, $from)
                ->where('effective_date', '<=', $date)
                ->orderByDesc('effective_date')
                ->first();

            if ($inverse && (float) $inverse->rate > 0) {
                return 1 / (float) $inverse->rate;
            }

            throw new RuntimeException(
                "Kurs {$from}→{$to} tidak tersedia untuk {$date->toDateString()}"
            );
        });
    }

    public function convert(float $amount, int $companyId, string $from, string $to, $date = null): float
    {
        return round($amount * $this->getRate($companyId, $from, $to, $date), 2);
    }

    public function format(float $amount, string $code): string
    {
        $currency = Currency::where('code', $code)->first();
        $decimals = $currency?->decimal_places ?? 2;
        $symbol = $currency?->symbol ?? $code;

        return $symbol . ' ' . number_format($amount, $decimals, ',', '.');
    }
}