<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentReceivables;
use App\Models\StudentReceivableDetail;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\FundManagement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    public function filterForm(School $school)
    {
        $students = Student::where('school_id', $school->id)->get();
        
        return view('receipts.filter', compact('school', 'students'));
    }

    public function previewByStudent(School $school, Student $student)
    {
        // Group by created_at (tanggal input pembayaran)
        $receivables = StudentReceivables::where('school_id', $school->id)
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($r) {
                return \Carbon\Carbon::parse($r->created_at)->format('Y-m-d');
            });

        return view('receipts.preview-by-student', compact('school', 'student', 'receivables'));
    }

    public function printByStudentAndDate(School $school, Student $student, $date)
    {
        $receivables = StudentReceivables::where('school_id', $school->id)
            ->where('student_id', $student->id)
            ->whereDate('created_at', $date)
            ->get();

        if ($receivables->isEmpty()) {
            return back()->with('error', 'Tidak ada pembayaran pada tanggal tersebut.');
        }

        $terbilang = new \App\Services\TerbilangService();
        $total = $receivables->sum('amount');

        $dateObj = \Carbon\Carbon::parse($date);
        $idFormatted = str_pad($student->id, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $dateObj->format('Y') . '/' . $idFormatted;

        $data = [
            'invoice_no'   => $invoiceNo,
            'date'         => $dateObj->format('M d, Y'),
            'from'         => $student->name,
            'amount'       => $total,
            'amount_words' => trim($terbilang->convert($total)).' Rupiah',
            'receivables'  => $receivables,
            'company'      => [
                'name'  => $school->name,
                'telp'  => $school->phone,
                'email' => $school->email,
                'logo'  => $school->logo
            ]
        ];

        $pdf = \PDF::loadView('receipts.print-by-date', $data);
        return $pdf->download("kwitansi-{$student->id}-{$date}.pdf");
    }

    public function printByDate(Request $request, School $school)
    {
        $studentId = $request->student_id;
        $date = $request->date;

        // ambil data dari student_receivables
        $receivables = StudentReceivables::where('student_id', $studentId)
            ->where('school_id', $school->id)
            ->whereDate('created_at', $date)
            ->get();

        if ($receivables->isEmpty()) {
            return back()->with('error', 'Tidak ada tagihan pada tanggal tersebut.');
        }

        $student = Student::find($studentId);
        $terbilang = new \App\Services\TerbilangService();

        $total = $receivables->sum('amount');
        $year = \Carbon\Carbon::parse($date);
        $idFormatted = str_pad($studentId, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $year->format('Y') . '/' . $idFormatted;

        $data = [
            'invoice_no'   => $invoiceNo,
            'date'         => $year->format('M d, Y'),
            'from'         => $student->name,
            'amount'       => $total,
            'amount_words' => trim($terbilang->convert($total)) . ' Rupiah',
            'receivables'  => $receivables, // loop di blade
            'company'      => [
                'name'  => $school->name,
                'telp'  => $school->phone,
                'email' => $school->email,
                'logo'  => $school->logo
            ]
        ];

        $pdf = \PDF::loadView('receipts.print-by-date', $data);
        return $pdf->download('kwitansi.pdf');
    }
}
