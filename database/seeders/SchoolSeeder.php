<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::create([
            'name' => 'SDN 1 Jakarta',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
        ]);
        School::create([
            'name' => 'SMPN 2 Bandung',
            'address' => 'Jl. Sudirman No. 10, Bandung',
        ]);
    }
}