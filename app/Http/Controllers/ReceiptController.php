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
            'date'        => $dateObj,
            'token'       => $uniqueCode,
            'total_amount' => $total,
        ]);

        $verifyUrl = route('receipts.verify', ['code' => $receipt->token]);
        $pathQrCode = 'images/qrcode/'.$uniqueCode.'.svg';

        //$qrCode = base64_encode(QrCode::format('png')->size(200)->generate($verifyUrl));
        $qrCode = QrCode::size(100)->generate($verifyUrl, public_path($pathQrCode));

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
            'verifyUrl'    => $verifyUrl,
            'qrCode'       => $pathQrCode,
        ];

        $pdf = \PDF::loadView('receipts.print-by-date', $data);
        return $pdf->download("kwitansi-{$student->id}-{$date}.pdf");
    }

    public function filterByDate(Request $request, School $school, Student $student)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $receivables = \App\Models\StudentReceivables::where('school_id', $school->id)
            ->where('student_id', $student->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn($r) => \Carbon\Carbon::parse($r->created_at)->format('Y-m-d'));

        if ($receivables->isEmpty()) {
            return '<div class="text-center text-muted py-3">Tidak ada data pada rentang tanggal tersebut.</div>';
        }

        $html = '';
        foreach ($receivables as $date => $items) {
            $html .= '
                <h5 class="mt-3">Tanggal Pembayaran: ' . $date . '</h5>
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Jenis Tagihan</th><th>Nominal</th></tr></thead>
                    <tbody>';
            foreach ($items as $item) {
                $html .= '
                    <tr>
                        <td>' . $item->account->code . ' - ' . $item->account->name . '</td>
                        <td>Rp ' . number_format($item->amount, 2, ',', '.') . '</td>
                    </tr>';
            }
            $html .= '
                    <tr class="table-light">
                        <td><strong>Total</strong></td>
                        <td><strong>Rp ' . number_format($items->sum('amount'), 2, ',', '.') . '</strong></td>
                    </tr>
                    </tbody>
                </table>
                <a href="' . route('school-student-receipts.printByStudentAndDate', [$school, $student, $date]) . '" target="_blank" class="btn btn-success btn-sm">
                    Cetak Kwitansi
                </a>
                <hr>';
        }

        return $html;
    }
}
