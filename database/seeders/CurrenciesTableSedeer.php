<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrenciesTableSedeer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            'usd', 'eur', 'gbp', 'jpy',
        ];

        foreach ($currencies as $curency) {
            Currency::create([
                'iso' => $curency,
            ]);
        }
    }
}
