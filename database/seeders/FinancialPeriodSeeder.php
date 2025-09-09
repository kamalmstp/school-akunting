<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;

class FinancialPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari semua sekolah yang ada
        $schools = School::all();

        // Jika tidak ada sekolah, berikan pesan kesalahan
        if ($schools->isEmpty()) {
            echo "Error: School not found. Please run SchoolSeeder first.\n";
            return;
        }

        $periods = [];
        $currentDate = now();

        foreach ($schools as $school) {
            // Data dummy untuk periode keuangan
            $periods[] = [
                'school_id' => $school->id,
                'name' => 'Tahun Ajaran ' . ($currentDate->year - 1) . '/' . $currentDate->year,
                'start_date' => ($currentDate->year - 1) . '-07-01',
                'end_date' => $currentDate->year . '-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $periods[] = [
                'school_id' => $school->id,
                'name' => 'Tahun Ajaran ' . $currentDate->year . '/' . ($currentDate->year + 1),
                'start_date' => $currentDate->year . '-07-01',
                'end_date' => ($currentDate->year + 1) . '-06-30',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Tambahkan beberapa periode tahun-tahun sebelumnya untuk riwayat
            $periods[] = [
                'school_id' => $school->id,
                'name' => 'Tahun Ajaran ' . ($currentDate->year - 2) . '/' . ($currentDate->year - 1),
                'start_date' => ($currentDate->year - 2) . '-07-01',
                'end_date' => ($currentDate->year - 1) . '-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $periods[] = [
                'school_id' => $school->id,
                'name' => 'Tahun Ajaran ' . ($currentDate->year - 3) . '/' . ($currentDate->year - 2),
                'start_date' => ($currentDate->year - 3) . '-07-01',
                'end_date' => ($currentDate->year - 2) . '-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Masukkan data ke tabel financial_periods
        DB::table('financial_periods')->insert($periods);
    }
}