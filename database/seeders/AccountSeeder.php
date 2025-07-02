<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Aset Lancar
            ['code' => '1-1', 'name' => 'Aset Lancar', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => null],
            ['code' => '1-11', 'name' => 'Kas Setara Kas', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 1],
            ['code' => '1-110001', 'name' => 'Kas Tangan', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 2],
            ['code' => '1-110002', 'name' => 'Kas Bank', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 2],
            ['code' => '1-110002-1', 'name' => 'Kas Bank Tabungan', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 4],
            ['code' => '1-12', 'name' => 'Piutang', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 1],
            ['code' => '1-120001', 'name' => 'Piutang Pembayaran Siswa', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 6],
            ['code' => '1-120001-1', 'name' => 'Piutang PPDB', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 7],
            ['code' => '1-120001-2', 'name' => 'Piutang DPP', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 7],
            ['code' => '1-120001-3', 'name' => 'Piutang SPP', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 7],
            ['code' => '1-120001-4', 'name' => 'Piutang UKS', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 7],
            ['code' => '1-120002', 'name' => 'Piutang Internal', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 6],
            ['code' => '1-120003', 'name' => 'Piutang Eksternal', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 6],
            ['code' => '1-13', 'name' => 'Bangunan Dalam Proses', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 1],
            ['code' => '1-130001', 'name' => 'Bangunan Dalam Proses', 'account_type' => 'Aset Lancar', 'normal_balance' => 'Debit', 'parent_id' => 14],

            // Aset Tetap
            ['code' => '1-2', 'name' => 'Aset Tetap', 'account_type' => 'Aset Tetap', 'normal_balance' => 'Debit', 'parent_id' => null],
            ['code' => '1-21', 'name' => 'Peralatan', 'account_type' => 'Aset Tetap', 'normal_balance' => 'Debit', 'parent_id' => 16],
            ['code' => '1-210001', 'name' => 'Peralatan Kantor', 'account_type' => 'Aset Tetap', 'normal_balance' => 'Debit', 'parent_id' => 17],
            ['code' => '1-210002', 'name' => 'Peralatan Penunjang Pembelajaran', 'account_type' => 'Aset Tetap', 'normal_balance' => 'Debit', 'parent_id' => 17],
            ['code' => '1-210004', 'name' => 'Akumulasi Penyusutan Peralatan', 'account_type' => 'Aset Tetap', 'normal_balance' => 'Kredit', 'parent_id' => 17],
            // Tambahkan akun lain sesuai struktur (misalnya: 1-210003, dll.)

            // Kewajiban
            ['code' => '2-1', 'name' => 'Kewajiban', 'account_type' => 'Kewajiban', 'normal_balance' => 'Kredit', 'parent_id' => null],
            ['code' => '2-11', 'name' => 'Kewajiban Jangka Pendek', 'account_type' => 'Kewajiban', 'normal_balance' => 'Kredit', 'parent_id' => 20],
            ['code' => '2-110001', 'name' => 'Kewajiban Internal', 'account_type' => 'Kewajiban', 'normal_balance' => 'Kredit', 'parent_id' => 21],
            // Tambahkan akun lain sesuai struktur

            // Aset Neto
            ['code' => '3-1', 'name' => 'Aset Neto', 'account_type' => 'Aset Neto', 'normal_balance' => 'Kredit', 'parent_id' => null],
            ['code' => '3-11', 'name' => 'Aset Neto', 'account_type' => 'Aset Neto', 'normal_balance' => 'Kredit', 'parent_id' => 23],
            ['code' => '3-110001', 'name' => 'Aset Neto Tanpa Pembatas', 'account_type' => 'Aset Neto', 'normal_balance' => 'Kredit', 'parent_id' => 24],

            // Pendapatan
            ['code' => '4-1', 'name' => 'Pendapatan', 'account_type' => 'Pendapatan', 'normal_balance' => 'Kredit', 'parent_id' => null],
            ['code' => '4-11', 'name' => 'Pendapatan Pembayaran Siswa', 'account_type' => 'Pendapatan', 'normal_balance' => 'Kredit', 'parent_id' => 26],
            ['code' => '4-110001', 'name' => 'Pendapatan PPDB', 'account_type' => 'Pendapatan', 'normal_balance' => 'Kredit', 'parent_id' => 27],
            // Tambahkan akun lain sesuai struktur

            // Biaya
            ['code' => '6-1', 'name' => 'Biaya', 'account_type' => 'Biaya', 'normal_balance' => 'Debit', 'parent_id' => null],
            ['code' => '6-11', 'name' => 'Biaya Standart Nasional Pendidikan', 'account_type' => 'Biaya', 'normal_balance' => 'Debit', 'parent_id' => 29],
            ['code' => '6-110001', 'name' => 'Biaya Standart Proses', 'account_type' => 'Biaya', 'normal_balance' => 'Debit', 'parent_id' => 30],
            ['code' => '6-110001-1', 'name' => 'Administrasi Kelas', 'account_type' => 'Biaya', 'normal_balance' => 'Debit', 'parent_id' => 31],
            ['code' => '6-110002', 'name' => 'Biaya Penyusutan', 'account_type' => 'Biaya', 'normal_balance' => 'Debit', 'parent_id' => 30],

            // Investasi
            ['code' => '7-1', 'name' => 'Investasi', 'account_type' => 'Investasi', 'normal_balance' => 'Debit', 'parent_id' => null],
            ['code' => '7-11', 'name' => 'Investasi', 'account_type' => 'Investasi', 'normal_balance' => 'Debit', 'parent_id' => 33],
            ['code' => '7-110001', 'name' => 'Investasi Peralatan Kantor', 'account_type' => 'Investasi', 'normal_balance' => 'Debit', 'parent_id' => 34],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
