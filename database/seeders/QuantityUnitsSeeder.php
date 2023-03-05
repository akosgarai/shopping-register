<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\QuantityUnit;

class QuantityUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (QuantityUnit::UNITS as $unit) {
            (new QuantityUnit())->firstOrCreate(['name' => $unit]);
        }
    }
}
