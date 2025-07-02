<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::create([
            'school_id' => 1,
            'name' => 'Ahmad Yani',
            'student_id_number' => 'S001',
            'class' => '5A',
        ]);
        Student::create([
            'school_id' => 1,
            'name' => 'Siti Aminah',
            'student_id_number' => 'S002',
            'class' => '5B',
        ]);
    }
}