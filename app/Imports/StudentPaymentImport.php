<?php

namespace App\Imports;

use App\Models\StudentReceivables;
use App\Models\StudentReceivableDetail;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StudentPaymentImport implements ToCollection
{
    private $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(Collection $rows)
    {
        $errors = [];
        $successCount = 0;

        foreach ($rows as $index => $row) {
            // Skip header jika ada
            if ($index === 0 && !is_numeric($row[0] ?? null)) {
                continue;
            }

            // Skip baris kosong
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            $studentIdNumber = trim($row[0] ?? '');
            $amount = (int) ($row[1] ?? 0);
            $paymentDate = trim($row[2] ?? '');
            $description = trim($row[3] ?? '') ?: 'Import dari Excel';

            // Validasi data
            if (empty($studentIdNumber) || $amount <= 0) {
                $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap atau amount tidak valid";
                continue;
            }

            // Cari siswa berdasarkan nomor induk
            $student = Student::where('student_id_number', $studentIdNumber)
                ->where('school_id', $this->schoolId)
                ->first();

            if (!$student) {
                $errors[] = "Baris " . ($index + 1) . ": Siswa dengan nomor induk '$studentIdNumber' tidak ditemukan";
                continue;
            }

            // Validasi tanggal
            try {
                $period = Carbon::parse($paymentDate);
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": Format tanggal tidak valid";
                continue;
            }

            // Cari piutang siswa yang belum lunas
            $receivable = StudentReceivables::where('school_id', $this->schoolId)
                ->where('student_id', $student->id)
                ->where('status', '!=', 'Paid')
                ->first();

            if (!$receivable) {
                $errors[] = "Baris " . ($index + 1) . ": Tidak ada piutang yang harus dibayar untuk siswa ini";
                continue;
            }

            try {
                // Tambah detail pembayaran
                StudentReceivableDetail::create([
                    'student_receivable_id' => $receivable->id,
                    'amount' => $amount,
                    'period' => $period->format('Y-m-d'),
                    'description' => $description,
                    'reason' => 'Import dari Excel'
                ]);

                // Update paid_amount dan status piutang
                $newPaidAmount = $receivable->paid_amount + $amount;
                $status = $newPaidAmount >= $receivable->total_payable ? 'Paid' : 'Partial';

                $receivable->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $status
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errors
        ];
    }
}
