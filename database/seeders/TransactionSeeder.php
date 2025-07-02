<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Transaction::create([
            'school_id' => 1,
            'account_id' => 8, // Piutang PPDB (1-120001-1)
            'date' => '2025-06-01',
            'description' => 'Piutang PPDB siswa baru',
            'debit' => 1000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => 28, // Pendapatan PPDB (4-110001)
            'date' => '2025-06-01',
            'description' => 'Pembayaran PPDB',
            'debit' => 0,
            'credit' => 1000000,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => 32, // Biaya Administrasi Kelas (6-110001-1)
            'date' => '2025-06-01',
            'description' => 'Biaya administrasi kelas',
            'debit' => 500000,
            'credit' => 0,
        ]);
    }
}