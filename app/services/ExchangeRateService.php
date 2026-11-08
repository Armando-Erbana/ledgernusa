<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected string $baseUrl = 'https://api.frankfurter.dev/v1';

    /**
     * Ambil kurs terbaru dari Frankfurter (sumber: ECB).
     * Return null jika gagal.
     */
    public function fetchLatest(string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/latest", [
                'base' => $from,
                'symbols' => $to,
            ]);

            if ($response->successful()) {
                $rate = $response->json("rates.{$to}");
                return $rate !== null ? (float) $rate : null;
            }

            Log::warning('Frankfurter gagal mengambil kurs', [
                'from' => $from,
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Frankfurter exception: ' . $e->getMessage(), [
                'from' => $from,
                'to' => $to,
            ]);
            return null;
        }
    }

    /**
     * Ambil daftar mata uang yang didukung Frankfurter.
     * Format: ['USD' => 'United States Dollar', ...]
     */
    public function currencies(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/currencies");

            return $response->successful() ? $response->json() : [];
        } catch (\Throwable $e) {
            Log::error('Frankfurter currencies gagal: ' . $e->getMessage());
            return [];
        }
    }
}