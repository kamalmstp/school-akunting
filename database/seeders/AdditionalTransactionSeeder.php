<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AdditionalTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Transaksi untuk Sekolah 1
        $kas = Account::where('code', '1-110001')->first();
        $pendapatan = Account::where('code', '4-110003')->first();
        $biaya = Account::where('code', '6-110001-1')->first();
        $piutang = Account::where('code', '1-120001-1')->first();
        $asetTetap = Account::where('code', '1-210001')->first();
        $kewajiban = Account::where('code', '2-110001')->first();
        $investasi = Account::where('code', '7-110001')->first();

        // Penerimaan SPP
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan SPP',
            'debit' => 5000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $pendapatan->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan SPP',
            'debit' => 0,
            'credit' => 5000000,
        ]);

        // Biaya Administrasi
        Transaction::create([
            'school_id' => 1,
            'account_id' => $biaya->id,
            'date' => '2025-06-01',
            'description' => 'Biaya administrasi',
            'debit' => 2000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Biaya administrasi',
            'debit' => 0,
            'credit' => 2000000,
        ]);

        // Penerimaan Piutang PPDB
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan piutang PPDB',
            'debit' => 3000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $piutang->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan piutang PPDB',
            'debit' => 0,
            'credit' => 3000000,
        ]);

        // Pembelian Aset Tetap
        Transaction::create([
            'school_id' => 1,
            'account_id' => $asetTetap->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian peralatan',
            'debit' => 5000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian peralatan',
            'debit' => 0,
            'credit' => 5000000,
        ]);

        // Pembayaran Utang
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kewajiban->id,
            'date' => '2025-06-01',
            'description' => 'Pembayaran utang',
            'debit' => 1000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Pembayaran utang',
            'debit' => 0,
            'credit' => 1000000,
        ]);

        // Pembelian Investasi Peralatan Kantor
        Transaction::create([
            'school_id' => 1,
            'account_id' => $investasi->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian investasi peralatan kantor',
            'debit' => 10000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 1,
            'account_id' => $kas->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian investasi peralatan kantor',
            'debit' => 0,
            'credit' => 10000000,
        ]);

        // Transaksi untuk Sekolah 2
        $kas2 = Account::where('code', '1-110001')->first();
        $pendapatan2 = Account::where('code', '4-110003')->first();
        $biaya2 = Account::where('code', '6-110001-1')->first();
        $investasi2 = Account::where('code', '7-110001')->first();

        // Penerimaan SPP
        Transaction::create([
            'school_id' => 2,
            'account_id' => $kas2->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan SPP',
            'debit' => 4000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 2,
            'account_id' => $pendapatan2->id,
            'date' => '2025-06-01',
            'description' => 'Penerimaan SPP',
            'debit' => 0,
            'credit' => 4000000,
        ]);

        // Biaya Administrasi
        Transaction::create([
            'school_id' => 2,
            'account_id' => $biaya2->id,
            'date' => '2025-06-01',
            'description' => 'Biaya administrasi',
            'debit' => 1500000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 2,
            'account_id' => $kas2->id,
            'date' => '2025-06-01',
            'description' => 'Biaya administrasi',
            'debit' => 0,
            'credit' => 1500000,
        ]);

        // Pembelian Investasi Peralatan Kantor
        Transaction::create([
            'school_id' => 2,
            'account_id' => $investasi2->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian investasi peralatan kantor',
            'debit' => 8000000,
            'credit' => 0,
        ]);
        Transaction::create([
            'school_id' => 2,
            'account_id' => $kas2->id,
            'date' => '2025-06-01',
            'description' => 'Pembelian investasi peralatan kantor',
            'debit' => 0,
            'credit' => 8000000,
        ]);
    }
}