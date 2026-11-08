<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange-rates:sync
                            {--company= : ID company (kosong = semua company)}
                            {--from=USD : Mata uang asal}
                            {--to=IDR : Mata uang tujuan}';

    protected $description = 'Sinkronisasi kurs dari Frankfurter API per company';

    public function handle(ExchangeRateService $service): int
    {
        $from = strtoupper($this->option('from'));
        $to = strtoupper($this->option('to'));
        $companyId = $this->option('company');

        $companies = $companyId
            ? Company::where('id', $companyId)->get()
            : Company::all();

        if ($companies->isEmpty()) {
            $this->error('Tidak ada company untuk disinkronkan.');
            return self::FAILURE;
        }

        $this->info("Mengambil kurs {$from} → {$to} dari Frankfurter...");

        $rate = $service->fetchLatest($from, $to);

        if ($rate === null) {
            $this->error("Gagal mengambil kurs {$from} → {$to}.");
            return self::FAILURE;
        }

        $count = 0;
        foreach ($companies as $company) {
            ExchangeRate::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'from_currency' => $from,
                    'to_currency' => $to,
                    'effective_date' => now()->toDateString(),
                ],
                [
                    'rate' => $rate,
                    'source' => 'Frankfurter (ECB)',
                ]
            );
            $count++;
        }

        $this->info("Berhasil: 1 {$from} = {$rate} {$to} untuk {$count} company.");
        return self::SUCCESS;
    }
}