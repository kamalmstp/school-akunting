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
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the student receivables.
     */
    public function index(Request $request, School $school = null)
{
    $user      = auth()->user();
    $account   = $request->get('account');
    $rawDate   = $request->get('date');
    $dueDate   = $rawDate ? Carbon::parse($rawDate)->format('Y-m-d') : null;
    $status    = $request->get('status');
    $studentId = $request->get('student_id');

    if ($user->role !== 'SchoolAdmin') {
        $schools   = School::pluck('name', 'id');
        $schoolId  = $request->get('school');
        $schoolVar = $schoolId ? School::find($schoolId) : null;

        $receivables = StudentReceivables::with(['school', 'student', 'account', 'student_receivable_details'])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($account, fn($q) => $q->where('account_id', $account))
            ->when($dueDate, fn($q) => $q->whereDate('due_date', $dueDate))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('student-receivables.index', [
            'receivables' => $receivables,
            'schools'     => $schools,
            'school'      => $schoolVar,
            'schoolId'    => $schoolId,
            'account'     => $account,
            'dueDate'     => $dueDate,
            'status'      => $status,
            'studentId'   => $studentId,
        ]);
    }

    $school = $school ?? $user->school;
    if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
        abort(403, 'Unauthorized access to this school.');
    }

    $receivables = StudentReceivables::with(['student', 'account', 'student_receivable_details'])
        ->where('school_id', $school->id)
        ->when($studentId, fn($q) => $q->where('student_id', $studentId))
        ->when($account, fn($q) => $q->where('account_id', $account))
        ->when($dueDate, fn($q) => $q->whereDate('due_date', $dueDate))
        ->when($status, fn($q) => $q->where('status', $status))
        ->orderByDesc('updated_at')
        ->paginate(10)
        ->withQueryString();

    return view('student-receivables.index', compact(
        'receivables', 'school', 'account', 'dueDate', 'status', 'studentId'
    ));
}


    public function getStudent(Request $request)
    {
        $students = Student::where('school_id', $request->school)->get();
        return response()->json($students, 200);
    }

    public function getReceivableDetail($receivableId)
    {
        $details = StudentReceivableDetail::with('student_receivable.student')->where('student_receivable_id', $receivableId)->get();
        $totalReceivable = StudentReceivables::find($receivableId);
        return view('partials.student-receivable-modal', compact('details', 'totalReceivable'));
    }

    public function getPaymentHistory(Request $request)
    {
        $studentId = $request->input('student_id');
        $accountId = $request->input('account_id');
        $schoolId  = $request->input('school_id');
        
        if (!$studentId || !$accountId || !$schoolId)
             return response()->json([], 200);

        // Validasi (opsional tapi disarankan)
        $request->validate([
            'student_id' => 'required',
            'account_id' => 'required',
            'school_id' => 'required',
        ]);

        // Query menggunakan scope yang sudah dibuat sebelumnya
        $details = StudentReceivableDetail::filterByStudentAccountSchool($studentId, $accountId, $schoolId)->get();

        return response()->json($details, 200);
    }

    /**
     * Show the form for creating a new receivable.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }
        $schoolId = $school->id;
        $students = Student::when($schoolId, function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%') // Piutang (1-12)
            ->get();
        return view('student-receivables.create', compact('school', 'students', 'accounts'));
    }

    /**
     * Store a newly created receivable in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'student_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'required|date',
        ];

        $messages = [
            'student_id.required' => 'Pilih salah satu siswa',
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'amount.required' => 'Jumlah wajib diisi',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi'
        ];

        if (auth()->user()->role == 'SuperAdmin') {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih salah satu sekolah';
        }

        $request->validate($rules, $messages);
        $schoolId = auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id;
        $amount = (float) str_replace('.', '', $request->final_amount);

        // Process discounts
        $labels = $request->input('discount_label', []);
        $percents = $request->input('discount_percent', []);
        $discounts = [];
        $totalPotongan = 0;

        //Infaq
        $infaq = 0.04166667;

        foreach ($labels as $i => $label) {
            $label = trim($label);
            $percent = isset($percents[$i]) ? (int)$percents[$i] : 0;

            if ($label && $percent > 0) {
                $nominal = intval(round(($percent / 100) * $amount));
                $discounts[] = [
                    'label' => $label,
                    'percent' => $percent,
                    'nominal' => $nominal,
                ];
                $totalPotongan += $nominal;
            }
        }

        $totalBayar = max($amount - $totalPotongan, 0);
        $totalInfaqAwal = max($totalBayar * $infaq, 0);
        $totalInfaq = ceil($totalInfaqAwal / 1000) * 1000;
        $totalBayarInfaq = max($totalBayar + $totalInfaq, 0);

        //Cek SPP atau bukan
        $akun = Account::find($request->account_id)->code;
        $piutangInfaq = Account::where('code', '=', '1-120002')->where('school_id', '=', $schoolId)->first();
        $pendapatanInfaq = Account::where('code', '=', '4-120002')->where('school_id', '=', $schoolId)->first();

        if ($akun == '1-120001-3') {
            $receivableInfaq = StudentReceivables::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'account_id' => $piutangInfaq->id,
                'amount' => $totalInfaq,
                'paid_amount' => 0,
                'due_date' => $request->due_date,
                'status' => 'Unpaid',
                'total_discount' => 0,
                'total_payable' => $totalInfaq,
            ]);
        }

        // Save student receivable
        $receivable = StudentReceivables::create([
            'school_id' => $schoolId,
            'student_id' => $request->student_id,
            'account_id' => $request->account_id,
            'amount' => $amount,
            'paid_amount' => 0,
            'due_date' => $request->due_date,
            'status' => 'Unpaid',
            'total_discount' => $totalPotongan,
            'total_payable' => $totalBayar,
        ]);

        // Save related discounts
        foreach ($discounts as $discount) {
            $receivable->discounts()->create($discount);
        }

        $description = Account::find($request->account_id)->name . ' siswa: ' . Student::find($request->student_id)->name;
        $descriptionInfaq = 'Piutang Internal siswa: ' . Student::find($request->student_id)->name;
        // Catat transaksi piutang (Debit pada akun piutang)
        if ($akun == '1-120001-3') {
            Transaction::create([
                'school_id' => $schoolId,
                'account_id' => $piutangInfaq->id,
                'date' => now(),
                'description' => $descriptionInfaq,
                'debit' => $totalInfaq,
                'credit' => 0,
                'reference_id' => $receivableInfaq->id,
                'reference_type' => StudentReceivables::class,
            ]);

            Transaction::create([
                'school_id' => $schoolId,
                'account_id' => $pendapatanInfaq->id,
                'date' => now(),
                'description' => $descriptionInfaq,
                'debit' => 0,
                'credit' => $totalInfaq,
                'reference_id' => $receivableInfaq->id,
                'reference_type' => StudentReceivables::class,
            ]);
        }

        Transaction::create([
            'school_id' => $schoolId,
            'account_id' => $request->account_id,
            'date' => now(),
            'description' => $description,
            'debit' => $totalBayar,
            'credit' => 0,
            'reference_id' => $receivable->id,
            'reference_type' => StudentReceivables::class,
        ]);

        // Catat transaksi piutang (Kredit pada akun pendapatan)
        Transaction::create([
            'school_id' => $schoolId,
            'account_id' => $request->income_account_id,
            'date' => now(),
            'description' => $description,
            'debit' => 0,
            'credit' => $totalBayar,
            'reference_id' => $receivable->id,
            'reference_type' => StudentReceivables::class,
        ]);

        // update jumlah dana untuk akun terkait
        FundManagement::where('school_id', $schoolId)
            ->where('account_id', $request->income_account_id)
            ->increment('amount', $totalBayar);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('student-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-student-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified receivable.
     */
    public function edit(School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schoolId = $school->id;
        $students = Student::where('school_id', $schoolId)->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();
        $transaction = Transaction::where([
            ['reference_id', '=', $student_receivable->id],
            ['reference_type', '=', 'App\Models\StudentReceivables'],
            ['account_id', '!=', $student_receivable->account_id],
        ])->whereRaw('CAST(credit AS SIGNED) = ?', intval($student_receivable->amount))->first();

        $discounts = $student_receivable->discounts()->get();
        
        $payment_histories = $student_receivable->student_receivable_details()->get();

        return view('student-receivables.edit', compact(
            'payment_histories',
            'student_receivable',
            'school',
            'students',
            'accounts',
            'transaction',
            'discounts'
        ));
    }

    /**
     * Update the specified receivable in storage.
     */
    public function update(Request $request, School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();

        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        // Validate input
        $request->validate([
            'student_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'nullable|date',
        ], [
            'student_id.required' => 'Pilih salah satu siswa',
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'amount.required' => 'Jumlah wajib diisi',
        ]);

        // Calculate amount
        $amount = (float) str_replace('.', '', $request->final_amount);

        // Handle discount recalculation
        $labels = $request->input('discount_label', []);
        $percents = $request->input('discount_percent', []);
        $discounts = [];
        $totalPotongan = 0;

        foreach ($labels as $i => $label) {
            $label = trim($label);
            $percent = isset($percents[$i]) ? (int)$percents[$i] : 0;

            if ($label && $percent > 0) {
                $nominal = intval(round(($percent / 100) * $amount));
                $discounts[] = [
                    'label' => $label,
                    'percent' => $percent,
                    'nominal' => $nominal,
                ];
                $totalPotongan += $nominal;
            }
        }

        $totalBayar = max($amount - $totalPotongan, 0);

        // Update main receivable
        $student_receivable->update([
            'student_id' => $request->student_id,
            'account_id' => $request->account_id,
            'amount' => $amount,
            'due_date' => $request->due_date,
            'total_discount' => $totalPotongan,
            'total_payable' => $totalBayar,
        ]);

        // Clear old discounts and re-insert
        $student_receivable->discounts()->delete();
        foreach ($discounts as $discount) {
            $student_receivable->discounts()->create($discount);
        }

        // Update transactions
        $student = Student::find($request->student_id);
        $desc = Account::find($request->account_id)->name . ' siswa: ' . $student->name;

        // Update debit transaction (piutang)
        Transaction::where('reference_id', $student_receivable->id)
            ->where('reference_type', StudentReceivables::class)
            ->where('debit', '>', 0)
            ->update([
                'school_id' => $school->id,
                'account_id' => $request->account_id,
                'description' => $desc,
                'debit' => $totalBayar,
                'credit' => 0,
            ]);

        // Update credit transaction (pendapatan)
        $descIncome = Account::find($request->income_account_id)->name . ' siswa: ' . $student->name;

        Transaction::where('reference_id', $student_receivable->id)
            ->where('reference_type', StudentReceivables::class)
            ->where('credit', '>', 0)
            ->update([
                'school_id' => $school->id,
                'account_id' => $request->income_account_id,
                'description' => $descIncome,
                'debit' => 0,
                'credit' => $totalBayar,
            ]);

        // update jumlah dana untuk akun terkait
        $sum_amount = StudentReceivables::where('account_id', $request->account_id)
            ->where('status', 'Paid')
            ->sum('total_payable');
        FundManagement::where('school_id', $school->id)
            ->where('account_id', $request->account_id)
            ->update([
                'amount' => $sum_amount,
            ]);

        // Redirect
        $route = back();
        if ($user->role === 'SuperAdmin') {
            $route = redirect()->route('student-receivables.index');
        } elseif ($user->role === 'SchoolAdmin') {
            $route = redirect()->route('school-student-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil diperbarui.');
    }


    /**
     * Remove the specified receivable from storage.
     */
    public function destroy(School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::where([
            ['reference_id', '=', $student_receivable->id],
            ['reference_type', '=', 'App\Models\StudentReceivables']
        ])->update(['deleted_at' => now()]);
        $student_receivable->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('student-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-student-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil dihapus.');
    }

    /**
     * Show the form for paying a receivable.
     */
    public function payForm(School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();
        $receivable = $student_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }
        $schoolId = $school->id;
        $cashAccounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-11%') // Kas Setara Kas (1-11)
            ->get();
        return view('student-receivables.pay', compact('receivable', 'school', 'cashAccounts'));
    }

    /**
     * Process payment for a receivable.
     */
    public function pay(Request $request, School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();
        $receivable = $student_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'amount' => 'required',
            'cash_account_id' => [
                function ($attribute, $value, $fail) use ($receivable) {
                    if ($receivable->student_receivable_details->isEmpty() && empty($value)) {
                        $fail('Pilih akun kas');
                    }
                }
            ],
            'description' => 'required',
            'date' => 'required|date',
        ], [
            'amount.required' => 'Jumlah wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'date.required' => 'Tanggal pembayaran wajib diisi'
        ]);

        if ($receivable->total_payable - $receivable->paid_amount < (float)str_replace('.', '', $request->amount)) {
            return back()->withErrors(['amount' => 'Pembayaran tidak dapat melebihi sisa piutang']);
        }

        $receivable->paid_amount += (float)str_replace('.', '', $request->amount);
        $receivable->status = $receivable->paid_amount >= $receivable->total_payable ? 'Paid' :
            ($receivable->paid_amount > 0 ? 'Partial' : 'Unpaid');
        if($receivable->save()) {
            $existReceivableDetail = StudentReceivableDetail::where('student_receivable_id', $receivable->id)
                ->latest()->first()->period ?? '';
            if ($existReceivableDetail) {
                // Update transaksi pembayaran
                // Debit: Kas (cash_account_id)
                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\StudentReceivables')
                    ->where('debit', '>', 0)
                    ->update([
                        'date' => $request->date,
                        'debit' => $receivable->paid_amount,
                    ]);

                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\StudentReceivables')
                    ->where('account_id', $receivable->account_id)
                    ->where('credit', '>', 0)
                    ->update([
                        'date' => $request->date,
                        'credit' => $receivable->paid_amount,
                    ]);
            } else {
                // Catat transaksi pembayaran
                // Debit: Kas (cash_account_id)
                Transaction::create([
                    'school_id' => $receivable->school_id,
                    'account_id' => $request->cash_account_id,
                    'date' => $request->date,
                    'description' => 'Pembayaran piutang: ' . $receivable->student->name,
                    'debit' => (float)str_replace('.', '', $request->amount),
                    'credit' => 0,
                    'reference_id' => $receivable->id,
                    'reference_type' => StudentReceivables::class,
                ]);

                // Kredit: Piutang (receivable->account_id)
                Transaction::create([
                    'school_id' => $receivable->school_id,
                    'account_id' => $receivable->account_id,
                    'date' => $request->date,
                    'description' => 'Pembayaran piutang: ' . $receivable->student->name,
                    'debit' => 0,
                    'credit' => (float)str_replace('.', '', $request->amount),
                    'reference_id' => $receivable->id,
                    'reference_type' => StudentReceivables::class,
                ]);
            }
            StudentReceivableDetail::create([
                'student_receivable_id' => $receivable->id,
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date
            ]);
        }

        //hitung SPP dan DPP
        foreach (['SPP', 'DPP'] as $toCount) {
            $countStats = StudentReceivables::getPaidAmountCounter($school->id, $toCount);
            foreach ($countStats ?? [] as $stats) {
                FundManagement::where('school_id', $stats->school_id)
                    ->where('name', 'like', '%' . $toCount . '%')
                    ->update([
                        'amount' => $stats->total_paid_amount,
                        'updated_at' => now(),
                    ]);
            }
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('student-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-student-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil dicatat.');
    }

    /**
     * Show the form for edit paying a receivable.
     */
    public function editPayForm(School $school, StudentReceivableDetail $student_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('student-receivables.edit-pay', compact('student_receivable_detail', 'school'));
    }

    /**
     * Process edit payment for a receivable.
     */
    public function editPay(Request $request, School $school, StudentReceivableDetail $student_receivable_detail)
    {
        $user = auth()->user();
        $receivable_detail = $student_receivable_detail;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'amount' => 'required',
            'date' => 'required|date',
            'reason' => 'required'
        ], [
            'amount.required' => 'Jumlah wajib diisi',
            'date.required' => 'Tanggal pembayaran wajib diisi',
            'reason.required' => 'Alasan wajib diisi'
        ]);

        if ($receivable_detail->student_receivable->amount - $receivable_detail->student_receivable->paid_amount < (float)str_replace('.', '', $request->amount)) {
            return back()->withErrors(['amount' => 'Pembayaran tidak dapat melebihi sisa piutang']);
        }

        if ($receivable_detail->amount < (float)str_replace('.', '', $request->amount)) {
            $diffAmount = (float)str_replace('.', '', $request->amount) - $receivable_detail->amount;
            $receivable_detail->student_receivable->paid_amount += $diffAmount;
        } else if ($receivable_detail->amount > (float)str_replace('.', '', $request->amount)) {
            $diffAmount = $receivable_detail->amount - (float)str_replace('.', '', $request->amount);
            $receivable_detail->student_receivable->paid_amount -= $diffAmount;
        }
        $receivable_detail->student_receivable->status = $receivable_detail->student_receivable->paid_amount >= $receivable_detail->student_receivable->amount ? 'Paid' :
            ($receivable_detail->student_receivable->paid_amount > 0 ? 'Partial' : 'Unpaid');
        if($receivable_detail->student_receivable->save()) {
            $receivable_detail->update([
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date,
                'reason' => $request->reason
            ]);

            // Update transaksi pembayaran
            Transaction::where('reference_id', $receivable_detail->student_receivable->id)
                ->where('reference_type', 'App\Models\StudentReceivables')
                ->where('date', Carbon::parse($student_receivable_detail->period)->format('Y-m-d'))
                ->where('debit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => $receivable_detail->student_receivable->paid_amount,
                    'credit' => 0,
                ]);
            Transaction::where('reference_id', $receivable_detail->student_receivable->id)
                ->where('reference_type', 'App\Models\StudentReceivables')
                ->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                ->where('account_id', $receivable_detail->student_receivable->account_id)
                ->where('credit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => 0,
                    'credit' => $receivable_detail->student_receivable->paid_amount,
                ]);
        }

        //hitung SPP dan DPP
        foreach (['SPP', 'DPP'] as $toCount) {
            $countStats = StudentReceivables::getPaidAmountCounter($school->id, $toCount);
            foreach ($countStats ?? [] as $stats) {
                FundManagement::where('school_id', $stats->school_id)
                    ->where('name', 'like', '%' . $toCount . '%')
                    ->update([
                        'amount' => $stats->total_paid_amount,
                        'updated_at' => now(),
                    ]);
            }
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('student-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-student-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    /**
     * Download receipt
     */
    public function receipt(School $school, StudentReceivableDetail $student_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $receivable_detail = $student_receivable_detail;
        $receivables = StudentReceivables::where('id', $receivable_detail->student_receivable_id)->first();
        $students = Student::where('id', $receivables->student_id)->first();

        $year = \Carbon\Carbon::parse($receivable_detail->period);
        $idFormatted = str_pad($receivable_detail->id, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $year->format('Y') . '/' . $idFormatted;

        $terbilang = new \App\Services\TerbilangService();

        $uniqueCode = now()->timestamp . $students->id;

        $receipt = Receipt::create([
            'school_id'   => $school->id,
            'student_id'  => $students->id,
            'invoice_no'  => $invoiceNo,
            'date'        => $receivable_detail->period,
            'token'       => $uniqueCode,
            'total_amount' => $receivable_detail->amount,
        ]);

        $verifyUrl = route('receipts.verify', ['code' => $receipt->token]);
        $pathQrCode = 'images/qrcode/'.$uniqueCode.'.svg';

        $qrCode = QrCode::size(100)->generate($verifyUrl, public_path($pathQrCode));

        $data = [
            'invoice_no' => $invoiceNo,
            'date' => $year->format('M d, Y'),
            'from' => $students->name,
            'amount' => $receivable_detail->amount,
            'amount_words' => trim($terbilang->convert($receivable_detail->amount)).' Rupiah',
            'payment_note' => $receivable_detail->description,
            'company' => [
                'name' => $school->name,
                'telp' => $school->phone,
                'email' => $school->email,
                'logo' => $school->logo
            ],
            'verifyUrl'    => $verifyUrl,
            'qrCode'       => $pathQrCode,
        ];

        $pdf = PDF::loadView('student-receivables.receipt', $data);
        return $pdf->download('kwitansi.pdf');
    }

    public function receiptAll(School $school, StudentReceivables $student_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $receivable = $student_receivable;
        $student = Student::findOrFail($receivable->student_id);

        $details = $receivable->student_receivable_details()->orderBy('id', 'asc')->get();

        $totalAmount = $details->sum('amount');

        $year = \Carbon\Carbon::now();
        $idFormatted = str_pad($receivable->id, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $year->format('Y') . '/' . $idFormatted;

        $terbilang = new \App\Services\TerbilangService();

        $uniqueCode = now()->timestamp . $student->id;

        $receipt = Receipt::create([
            'school_id'   => $school->id,
            'student_id'  => $student->id,
            'invoice_no'  => $invoiceNo,
            'date'        => $year,
            'token'       => $uniqueCode,
            'total_amount' => $totalAmount,
        ]);

        $verifyUrl = route('receipts.verify', ['code' => $receipt->token]);
        $pathQrCode = 'images/qrcode/'.$uniqueCode.'.svg';

        $qrCode = QrCode::size(100)->generate($verifyUrl, public_path($pathQrCode));

        $data = [
            'invoice_no'   => $invoiceNo,
            'date'         => $year->format('d/m/Y'),
            'from'         => $student->name,
            'amount'       => $totalAmount,
            'amount_words' => trim($terbilang->convert($totalAmount)) . ' Rupiah',
            'details'      => $details,
            'company'      => [
                'name'  => $school->name,
                'telp'  => $school->phone,
                'email' => $school->email,
                'logo'  => $school->logo
            ],
            'verifyUrl'    => $verifyUrl,
            'qrCode'       => $pathQrCode,
        ];

        $pdf = \PDF::loadView('student-receivables.receipt-all', $data);
        return $pdf->download('kwitansi.pdf');
    }
}