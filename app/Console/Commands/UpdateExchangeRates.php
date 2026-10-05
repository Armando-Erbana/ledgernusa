<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update-rates {--base=USD} {--company=}';
    protected $description = 'Update kurs harian dari exchangerate.host';

    public function handle(): int
    {
        $base = strtoupper($this->option('base'));
        $companyId = $this->option('company') ?? Company::first()?->id;

        if (!$companyId) {
            $this->error('Tidak ada company.');
            return self::FAILURE;
        }

        $response = Http::timeout(15)->get('https://api.exchangerate.host/latest', [
            'base' => $base,
            'symbols' => 'IDR,EUR,SGD,JPY,MYR,GBP',
        ]);

        if (!$response->successful()) {
            $this->error('Gagal fetch: HTTP ' . $response->status());
            return self::FAILURE;
        }

        $rates = $response->json('rates', []);
        $count = 0;

        foreach ($rates as $code => $rate) {
            ExchangeRate::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'from_currency' => $base,
                    'to_currency' => $code,
                    'effective_date' => now()->toDateString(),
                ],
                [
                    'rate' => $rate,
                    'source' => 'exchangerate.host',
                ]
            );
            $count++;
        }

        $this->info("Berhasil update {$count} kurs (base {$base}).");
        return self::SUCCESS;
    }
}