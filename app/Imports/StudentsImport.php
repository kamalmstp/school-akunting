<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    /**
     * Transform Excel row to Student model or update existing.
     */
    public function model(array $row)
    {
        // Cari siswa berdasarkan NIS dan school_id
        $student = Student::where('student_id_number', $row['nisn'])
            ->where('school_id', $this->schoolId)
            ->first();

        $data = [
            'school_id' => $this->schoolId,
            'student_id_number' => $row['nisn'],
            'name' => $row['nama'],
            'class' => $row['kelas'],
            'is_active' => $row['status_aktif'] == 1,
        ];

        if ($student) {
            // Update jika siswa sudah ada
            $student->update($data);
            return null; // Kembalikan null agar tidak membuat record baru
        }

        // Buat siswa baru jika tidak ada
        return new Student($data);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'nisn' => 'required|string|max:20', // Unik dihapus karena akan dihandle oleh model
            'nama' => 'required|string|max:255',
            'telepon' => 'required|max:13',
            'kelas' => 'required|string|max:50',
            'status_aktif' => 'required|in:0,1',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'NIS wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi',
            'kelas.required' => 'Kelas wajib diisi.',
            'status_aktif.required' => 'Status Aktif wajib diisi (1 untuk aktif, 0 untuk tidak aktif).',
        ];
    }

    /**
     * Prepare data for validation (check NISN uniqueness manually).
     */
    public function prepareForValidation($data, $index)
    {
        // Validasi NISN unik hanya jika siswa baru
        $existingStudent = Student::where('student_id_number', $data['nisn'])
            ->where('school_id', $this->schoolId)
            ->exists();

        if ($existingStudent && $data['nisn']) {
            // Jika NISN ada, skip validasi unik karena akan diupdate
            return $data;
        }

        // Tambahkan validasi unik untuk NISN baru
        validator($data, [
            'nisn' => ['required', Rule::unique('students', 'student_id_number')->where('school_id', $this->schoolId)],
        ])->validate();

        return $data;
    }
}