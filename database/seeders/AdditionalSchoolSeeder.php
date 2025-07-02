<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class AdditionalSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Tambah sekolah kedua
        $school = School::where('name', 'SD Negeri 2')->firstOrCreate();

        // Tambah akun untuk sekolah kedua (contoh)
        $account = Account::create([
            'code' => '1-110001-2',
            'name' => 'Kas Operasional',
            'account_type' => 'Aset Lancar',
            'normal_balance' => 'Debit',
            'parent_id' => 3,
        ]);

        // Tambah transaksi
        Transaction::create([
            'school_id' => $school->id,
            'account_id' => $account->id,
            'date' => '2025-06-01',
            'description' => 'Setoran awal',
            'debit' => 20000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => $school->id,
            'account_id' => Account::create([
                'code' => '3-110001-2',
                'name' => 'Modal Sekolah',
                'account_type' => 'Aset Neto',
                'normal_balance' => 'Kredit',
                'parent_id' => 25,
            ])->id,
            'date' => '2025-06-01',
            'description' => 'Setoran modal',
            'debit' => 0,
            'credit' => 20000000,
        ]);
    }
}