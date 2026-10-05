<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah',   'symbol' => 'Rp', 'decimal_places' => 0],
            ['code' => 'USD', 'name' => 'US Dollar',           'symbol' => '$',  'decimal_places' => 2],
            ['code' => 'EUR', 'name' => 'Euro',                'symbol' => '€',  'decimal_places' => 2],
            ['code' => 'SGD', 'name' => 'Singapore Dollar',    'symbol' => 'S$', 'decimal_places' => 2],
            ['code' => 'JPY', 'name' => 'Japanese Yen',        'symbol' => '¥',  'decimal_places' => 0],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit',   'symbol' => 'RM', 'decimal_places' => 2],
            ['code' => 'GBP', 'name' => 'British Pound',       'symbol' => '£',  'decimal_places' => 2],
        ];

        foreach ($currencies as $c) {
            Currency::updateOrCreate(
                ['code' => $c['code']],
                $c + ['is_active' => true]
            );
        }
    }
}