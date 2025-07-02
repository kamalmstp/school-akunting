<?php

namespace Database\Seeders;

use App\Models\FixAsset;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class FixedAssetSeeder extends Seeder
{
    public function run(): void
    {
        $fixedAsset = FixAsset::create([
            'school_id' => 1,
            'account_id' => 18, // Peralatan Kantor (1-210001)
            'name' => 'Komputer Kantor',
            'acquisition_date' => '2025-01-01',
            'acquisition_cost' => 10000000,
            'useful_life' => 5,
            'accumulated_depriciation' => 0,
        ]);

        Transaction::create([
            'school_id' => 1,
            'account_id' => 18,
            'date' => '2025-01-01',
            'description' => 'Pembelian Komputer Kantor',
            'debit' => 10000000,
            'credit' => 0,
            'reference_id' => $fixedAsset->id,
            'reference_type' => FixAsset::class,
        ]);
    }
}