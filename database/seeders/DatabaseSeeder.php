<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\School;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'Super Admin',
        //     'email' => 'superadmin@example.com',
        //     'password' => Hash::make('password123'),
        //     'role' => 'SuperAdmin',
        //     'school_id' => null,
        // ]);

        // User::create([
        //     'name' => 'Admin Monitor',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password123'),
        //     'role' => 'AdminMonitor',
        //     'school_id' => null,
        // ]);

        // // SchoolAdmin untuk Sekolah 1
        // $school1 = School::where('id', 1)->first();
        // User::create([
        //     'name' => 'Admin Sekolah 1',
        //     'email' => 'schooladmin1@example.com',
        //     'password' => Hash::make('password123'),
        //     'role' => 'SchoolAdmin',
        //     'school_id' => $school1->id,
        // ]);
        $schools = School::all();
        $kasBank = Account::where('code', '1-110002')->first(); // Kas Bank
        $modalSekolah = Account::where('code', '3-110001')->first(); // Modal Sekolah
        $investasi = Account::where('code', '7-110001')->first(); // Investasi

        foreach ($schools as $school) {
            // Transaksi untuk Sekolah ID 1
            if ($school->id === 1) {
                // Setoran Kas Bank (1 Jan 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $kasBank->id,
                    'date' => Carbon::create(2025, 1, 1),
                    'description' => 'Setoran awal kas bank',
                    'debit' => 100000000, // Rp100.000.000
                    'credit' => 0,
                ]);

                // Modal Sekolah (1 Jan 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $modalSekolah->id,
                    'date' => Carbon::create(2025, 1, 1),
                    'description' => 'Setoran modal awal',
                    'debit' => 0,
                    'credit' => 100000000, // Rp100.000.000
                ]);

                // Investasi (1 Feb 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $investasi->id,
                    'date' => Carbon::create(2025, 2, 1),
                    'description' => 'Investasi awal',
                    'debit' => 50000000, // Rp50.000.000
                    'credit' => 0,
                ]);

                // Pengeluaran dari Kas Bank untuk Investasi (1 Feb 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $kasBank->id,
                    'date' => Carbon::create(2025, 2, 1),
                    'description' => 'Pengeluaran untuk investasi',
                    'debit' => 0,
                    'credit' => 50000000, // Rp50.000.000
                ]);
            }

            // Transaksi untuk Sekolah ID 2
            if ($school->id === 2) {
                // Setoran Kas Bank (1 Mar 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $kasBank->id,
                    'date' => Carbon::create(2025, 3, 1),
                    'description' => 'Setoran awal kas bank',
                    'debit' => 80000000, // Rp80.000.000
                    'credit' => 0,
                ]);

                // Modal Sekolah (1 Mar 2025)
                Transaction::create([
                    'school_id' => $school->id,
                    'account_id' => $modalSekolah->id,
                    'date' => Carbon::create(2025, 3, 1),
                    'description' => 'Setoran modal awal',
                    'debit' => 0,
                    'credit' => 80000000, // Rp80.000.000
                ]);
            }
        }
    }
}
