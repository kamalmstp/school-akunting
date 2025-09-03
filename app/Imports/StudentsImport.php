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
        $student = Student::where('student_id_number', $row['nis'])
            ->where('school_id', $this->schoolId)
            ->first();

        $data = [
            'school_id' => $this->schoolId,
            'student_id_number' => $row['nis'],
            'national_student_number' => $row['nisn'],
            'year' => $row['tahun'],
            'parent_name' => $row['nama_orang_tua'],
            'parent_phone' => $row['telepon_orang_tua'],
            'parent_mail' => $row['email_orang_tua'],
            'parent_job' => $row['pekerjaan_orang_tua'],
            'name' => $row['nama'],
            'class' => $row['kelas'],
            'phone' => $row['telepon'],
            'address' => $row['alamat'],
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
            'nis' => 'required|max:20', // Unik dihapus karena akan dihandle oleh model
            'nisn' => 'required|max:20',
            'tahun' => 'required|integer',
            'nama_orang_tua' => 'required|string|max:255',
            'telepon_orang_tua' => 'required|max:13',
            'email_orang_tua' => 'required|email',
            'pekerjaan_orang_tua' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'telepon' => 'required|max:13',
            'status_aktif' => 'required|in:0,1',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS wajib diisi.',
            'nisn.required' => 'NISN wajib diisi',
            'tahun.required' => 'Tahun Masuk wajib diisi',
            'nama_orang_tua.required' => 'Nama Orang Tua wajib diisi',
            'telepon_orang_tua.required' => 'Telepon Orang Tua wajib diisi',
            'email_orang_tua.required' => 'Email Orang Tua wajib diisi',
            'pekerjaan_orang_tua.required' => 'Pekerjaan Orang Tua wajib diisi',
            'nama.required' => 'Nama wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi',
            'status_aktif.required' => 'Status Aktif wajib diisi (1 untuk aktif, 0 untuk tidak aktif).',
        ];
    }

    /**
     * Prepare data for validation (check NIS uniqueness manually).
     */
    public function prepareForValidation($data, $index)
    {
        // Validasi NIS unik hanya jika siswa baru
        $existingStudent = Student::where('student_id_number', $data['nis'])
            ->where('school_id', $this->schoolId)
            ->exists();

        if ($existingStudent && $data['nis']) {
            // Jika NIS ada, skip validasi unik karena akan diupdate
            return $data;
        }

        // Tambahkan validasi unik untuk NIS baru
        validator($data, [
            'nis' => ['required', Rule::unique('students', 'student_id_number')->where('school_id', $this->schoolId)],
        ])->validate();

        return $data;
    }
}