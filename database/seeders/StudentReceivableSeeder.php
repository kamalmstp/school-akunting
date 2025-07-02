<?php

namespace Database\Seeders;

use App\Models\StudentReceivables;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class StudentReceivableSeeder extends Seeder
{
    public function run(): void
    {
        $receivable = StudentReceivables::create([
            'school_id' => 1,
            'student_id' => 1,
            'account_id' => 8, // Piutang PPDB (1-120001-1)
            'amount' => 1000000,
            'paid_amount' => 0,
            'due_date' => '2025-06-30',
            'status' => 'Unpaid',
        ]);

        Transaction::create([
            'school_id' => 1,
            'account_id' => 8,
            'date' => '2025-06-01',
            'description' => 'Piutang PPDB: Ahmad Yani',
            'debit' => 1000000,
            'credit' => 0,
            'reference_id' => $receivable->id,
            'reference_type' => StudentReceivables::class,
        ]);
    }
}