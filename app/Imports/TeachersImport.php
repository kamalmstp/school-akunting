<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    /**
     * Transform Excel row to Teacher model or update existing.
     */
    public function model(array $row)
    {
        // Cari guru berdasarkan NIK dan school_id
        $teacher = Teacher::where('teacher_id_number', $row['nik'])
            ->where('school_id', $this->schoolId)
            ->first();

        $data = [
            'school_id' => $this->schoolId,
            'teacher_id_number' => $row['nik'],
            'nik' => $row['nik_ktp'],
            'education' => $row['pendidikan_terakhir'],
            'tmt' => $row['tmt'],
            'work_period' => $row['masa_kerja'],
            'certification' => $row['sertifikasi'],
            'employment_status' => $row['status_kepegawaian'],
            'name' => $row['nama'],
            'email' => $row['email'],
            'phone' => $row['telepon'],
            'address' => $row['alamat'],
            'is_active' => $row['status_aktif'] == 1,
        ];

        if ($teacher) {
            // Update jika guru sudah ada
            $teacher->update($data);
            return null; // Kembalikan null agar tidak membuat record baru
        }

        // Buat guru baru jika tidak ada
        return new Teacher($data);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'nik' => 'required|string|max:20', // Unik dihapus karena akan dihandle oleh model
            'nik_ktp' => 'required|integer|digits:16',
            'pendidikan_terakhir' => 'required|string',
            'tmt' => 'required|integer',
            'masa_kerja' => 'required|string',
            'sertifikasi' => 'required|string',
            'status_kepegawaian' => 'required|string',
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
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
            'nik.required' => 'NIK wajib diisi.',
            'nik_ktp.required' => 'NIK KTP wajib diisi.',
            'nik_ktp.integer' => 'NIK KTP harus berupa angka',
            'nik_ktp.digits' => 'NIK KTP terdiri dari 16 angka',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
            'tmt.required' => 'TMT wajib diisi.',
            'tmt.integer' => 'TMT harus berupa angka',
            'masa_kerja.required' => 'Masa kerja wajib diisi.',
            'sertifikasi.required' => 'Sertifikasi wajib diisi.',
            'status_kepegawaian.required' => 'Status kepegawaian wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi',
            'status_aktif.required' => 'Status Aktif wajib diisi (1 untuk aktif, 0 untuk tidak aktif).',
        ];
    }

    /**
     * Prepare data for validation (check NIK uniqueness manually).
     */
    public function prepareForValidation($data, $index)
    {
        // Validasi NIK unik hanya jika guru baru
        $existingTeacher = Teacher::where('teacher_id_number', $data['nik'])
            ->where('school_id', $this->schoolId)
            ->exists();

        if ($existingTeacher && $data['nik']) {
            // Jika NIK ada, skip validasi unik karena akan diupdate
            return $data;
        }

        // Tambahkan validasi unik untuk NIK baru
        validator($data, [
            'nik' => ['required', Rule::unique('teachers', 'teacher_id_number')->where('school_id', $this->schoolId)],
            'email' => ['required', Rule::unique('teachers', 'email')->where('school_id', $this->schoolId)],
        ])->validate();

        return $data;
    }
}