<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentReceivables;
use App\Models\StudentReceivableDetail;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\FundManagement;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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

        $uniqueCode = now()->timestamp . $student->id;

        $receipt = Receipt::create([
            'school_id'   => $school->id,
            'student_id'  => $student->id,
            'invoice_no'  => $invoiceNo,
            'amount'      => $total,
            'date'        => $dateObj,
            'token'       => $uniqueCode,
            'total_amount' => $total,
        ]);

        $verifyUrl = route('receipts.verify', ['code' => $receipt->token]);

        $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($verifyUrl));
        //$qrCode = QrCode::size(200)->generate($verifyUrl);

        $data = [
            'invoice_no'   => $invoiceNo,
            'date'         => $dateObj->format('M d, Y'),
            'from'         => $student->name,
            'amount'       => $total,
            'amount_words' => trim($terbilang->convert($total)) . ' Rupiah',
            'receivables'  => $receivables,
            'company'      => [
                'name'  => $school->name,
                'telp'  => $school->phone,
                'email' => $school->email,
                'logo'  => $school->logo,
            ],
            'qrCode'       => $qrCode,
            'verifyUrl'    => $verifyUrl,
        ];

        $pdf = \PDF::loadView('receipts.print-by-date', $data);
        return $pdf->download("kwitansi-{$student->id}-{$date}.pdf");
    }

    public function verify($code)
    {
        $receipt = Receipt::with(['student', 'school'])
            ->where('token', $code)
            ->first();

        if (!$receipt) {
            return view('receipts.verify', [
                'status' => 'error',
                'message' => 'Kwitansi tidak ditemukan atau kode salah.'
            ]);
        }

        return view('receipts.verify', [
            'status' => 'success',
            'receipt' => $receipt,
        ]);
    }
}
