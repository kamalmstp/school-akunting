<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\TeacherReceivable;
use App\Models\TeacherReceivableDetail;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the teacher receivables.
     */
    public function index(Request $request, School $school = null)
{
    $user     = auth()->user();
    $account  = $request->get('account');
    $dueDate  = $request->get('date') ? Carbon::parse($request->get('date'))->format('Y-m-d') : null;
    $status   = $request->get('status');
    $teacherId = $request->get('teacher_id');

    // SuperAdmin: bisa lihat semua sekolah
    if ($user->role !== 'SchoolAdmin') {
        $schools  = School::pluck('name', 'id');
        $schoolId = $request->get('school');

        $receivables = TeacherReceivable::with(['school', 'teacher', 'account'])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
            ->when($account, fn($q) => $q->where('account_id', $account))
            ->when($dueDate, fn($q) => $q->where('due_date', $dueDate))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('teacher-receivables.index', [
            'receivables' => $receivables,
            'schools'     => $schools,
            'schoolId'    => $schoolId,
            'school'      => $school,
            'account'     => $account,
            'dueDate'     => $dueDate,
            'status'      => $status,
            'teacherId'   => $teacherId,
        ]);
    }

    $school   = $school ?? $user->school;
    $schoolId = $school->id;

    if ($user->school_id !== $schoolId) {
        abort(403, 'Unauthorized access to this school.');
    }

    $receivables = TeacherReceivable::where('school_id', $schoolId)
        ->with(['teacher', 'account'])
        ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
        ->when($account, fn($q) => $q->where('account_id', $account))
        ->when($dueDate, fn($q) => $q->where('due_date', $dueDate))
        ->when($status, fn($q) => $q->where('status', $status))
        ->orderByDesc('updated_at')
        ->paginate(10)
        ->withQueryString();

    return view('teacher-receivables.index', [
        'receivables' => $receivables,
        'school'      => $school,
        'schoolId'    => $schoolId,
        'account'     => $account,
        'dueDate'     => $dueDate,
        'status'      => $status,
        'teacherId'   => $teacherId,
    ]);
}


    public function getTeacher(Request $request)
    {
        $teachers = Teacher::where('school_id', $request->school)->get();
        return response()->json($teachers, 200);
    }

    public function getReceivableDetail($receivableId)
    {
        $details = TeacherReceivableDetail::with('teacher_receivable.teacher')->where('teacher_receivable_id', $receivableId)->get();
        $totalReceivable = TeacherReceivable::find($receivableId);
        return view('partials.teacher-receivable-modal', compact('details', 'totalReceivable'));
    }

    public function getPaymentHistory(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $accountId = $request->input('account_id');
        $schoolId  = $request->input('school_id');
        
        if (!$teacherId || !$accountId || !$schoolId)
             return response()->json([], 200);

        // Validasi (opsional tapi disarankan)
        $request->validate([
            'teacher_id' => 'required',
            'account_id' => 'required',
            'school_id' => 'required',
        ]);

        // Query menggunakan scope yang sudah dibuat sebelumnya
        $details = TeacherReceivableDetail::filterByTeacherAccountSchool($teacherId, $accountId, $schoolId)->get();

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
        $teachers = Teacher::when($schoolId, function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%') // Piutang (1-12)
            ->get();
        return view('teacher-receivables.create', compact('school', 'teachers', 'accounts'));
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
            'teacher_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'nullable|date',
        ];

        $messages = [
            'teacher_id.required' => 'Pilih salah satu guru',
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'amount.required' => 'Jumlah wajib diisi'
        ];

        if (auth()->user()->role == 'SuperAdmin') {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih salah satu sekolah';
        }

        $request->validate($rules, $messages);

        $amount = (float) str_replace('.', '', $request->final_amount);

        $schoolId = auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id;
        TeacherReceivable::create([
            'school_id' => $schoolId,
            'teacher_id' => $request->teacher_id,
            'account_id' => $request->account_id,
            'amount' => $amount,
            'paid_amount' => 0,
            'due_date' => $request->due_date,
            'status' => 'Unpaid',
        ]);

        // Catat transaksi piutang (Debit pada akun piutang)
        Transaction::create([
            'school_id' => $schoolId,
            'account_id' => $request->account_id,
            'date' => now(),
            'description' => Account::find($request->account_id)->name . ' guru: ' . Teacher::find($request->teacher_id)->name,
            'debit' => $amount,
            'credit' => 0,
            'reference_id' => TeacherReceivable::latest()->first()->id,
            'reference_type' => TeacherReceivable::class,
        ]);

        // Catat transaksi piutang (Kredit pada akun pendapatan)
        Transaction::create([
            'school_id' => $schoolId,
            'account_id' => $request->income_account_id,
            'date' => now(),
            'description' => Account::find($request->account_id)->name . ' guru: ' . Teacher::find($request->teacher_id)->name,
            'debit' => 0,
            'credit' => $amount,
            'reference_id' => TeacherReceivable::latest()->first()->id,
            'reference_type' => TeacherReceivable::class,
        ]);

        // update jumlah dana untuk akun terkait
        FundManagement::where('school_id', $schoolId)
            ->where('account_id', $request->income_account_id)
            ->increment('amount', $amount);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teacher-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teacher-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified receivable.
     */
    public function edit(School $school, TeacherReceivable $teacher_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schoolId = $school->id;
        $teachers = Teacher::where('school_id', $schoolId)->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();
        $transaction = Transaction::where([
            ['reference_id', '=', $teacher_receivable->id],
            ['reference_type', '=', 'App\Models\TeacherReceivable'],
            ['account_id', '!=', $teacher_receivable->account_id],
        ])->whereRaw('CAST(credit AS int) = ?', intval($teacher_receivable->amount))->first();
        
        return view('teacher-receivables.edit', compact('teacher_receivable', 'school', 'teachers', 'accounts', 'transaction'));
    }

    /**
     * Update the specified receivable in storage.
     */
    public function update(Request $request, School $school, TeacherReceivable $teacher_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'teacher_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'nullable|date',
        ], [
            'teacher_id.required' => 'Pilih salah satu guru',
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'amount.required' => 'Jumlah wajib diisi'
        ]);

        $amount = (float) str_replace('.', '', $request->final_amount);

        $teacher_receivable->update([
            'teacher_id' => $request->teacher_id,
            'account_id' => $request->account_id,
            'amount' => $amount,
            'due_date' => $request->due_date,
        ]);

        Transaction::where([
            ['reference_id', '=', $teacher_receivable->id],
            ['reference_type', '=', 'App\Models\TeacherReceivable'],
        ])->where('account_id', $request->account_id)->update([
            'school_id' => $school->id,
            'account_id' => $request->account_id,
            'description' => Account::find($request->account_id)->name . ' guru: ' . $teacher_receivable->teacher->name,
            'debit' => $amount,
            'credit' => 0,
            'reference_id' => $teacher_receivable->id,
            'reference_type' => TeacherReceivable::class,
        ]);

        Transaction::where([
            ['reference_id', '=', $teacher_receivable->id],
            ['reference_type', '=', 'App\Models\TeacherReceivable'],
        ])->where('account_id', $request->income_account_id)->update([
            'school_id' => $school->id,
            'account_id' => $request->income_account_id,
            'description' => Account::find($request->income_account_id)->name . ' guru: ' . $teacher_receivable->teacher->name,
            'debit' => 0,
            'credit' => $amount,
            'reference_id' => $teacher_receivable->id,
            'reference_type' => TeacherReceivable::class,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teacher-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teacher-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil diperbarui.');
    }

    /**
     * Remove the specified receivable from storage.
     */
    public function destroy(School $school, TeacherReceivable $teacher_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::where([
            ['reference_id', '=', $teacher_receivable->id],
            ['reference_type', '=', 'App\Models\TeacherReceivable']
        ])->update(['deleted_at' => now()]);
        $teacher_receivable->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teacher-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teacher-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil dihapus.');
    }

    /**
     * Show the form for paying a receivable.
     */
    public function payForm(School $school, TeacherReceivable $teacher_receivable)
    {
        $user = auth()->user();
        $receivable = $teacher_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $cashAccounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-11%') // Kas Setara Kas (1-11)
            ->get();
        return view('teacher-receivables.pay', compact('receivable', 'school', 'cashAccounts'));
    }

    /**
     * Process payment for a receivable.
     */
    public function pay(Request $request, School $school, TeacherReceivable $teacher_receivable)
    {
        $user = auth()->user();
        $receivable = $teacher_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'amount' => 'required',
            'cash_account_id' => [
                function ($attribute, $value, $fail) use ($receivable) {
                    if ($receivable->teacher_receivable_details->isEmpty() && empty($value)) {
                        $fail('Pilih akun kas');
                    }
                }
            ],
            'date' => 'required|date',
        ], [
            'amount.required' => 'Jumlah wajib diisi',
            'date.required' => 'Tanggal pembayaran wajib diisi'
        ]);

        if ($receivable->amount - $receivable->paid_amount < (float)str_replace('.', '', $request->amount)) {
            return back()->withErrors(['amount' => 'Pembayaran tidak dapat melebihi sisa piutang']);
        }

        $receivable->paid_amount += (float)str_replace('.', '', $request->amount);
        $receivable->status = $receivable->paid_amount >= $receivable->amount ? 'Paid' :
            ($receivable->paid_amount > 0 ? 'Partial' : 'Unpaid');
        if ($receivable->save()) {
            $existReceivableDetail = TeacherReceivableDetail::where('teacher_receivable_id', $receivable->id)
                ->latest()->first()->period ?? '';
            if ($existReceivableDetail) {
                // Update transaksi pembayaran
                // Debit: Kas (cash_account_id)
                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\TeacherReceivable')
                    ->where('debit', '>', 0)
                    ->update([
                        'date' => $request->date,
                        'debit' => $receivable->paid_amount,
                    ]);

                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\TeacherReceivable')
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
                    'description' => 'Pembayaran piutang: ' . $receivable->teacher->name,
                    'debit' => (float)str_replace('.', '', $request->amount),
                    'credit' => 0,
                    'reference_id' => $receivable->id,
                    'reference_type' => TeacherReceivable::class,
                ]);

                // Kredit: Piutang (receivable->account_id)
                Transaction::create([
                    'school_id' => $receivable->school_id,
                    'account_id' => $receivable->account_id,
                    'date' => $request->date,
                    'description' => 'Pembayaran piutang: ' . $receivable->teacher->name,
                    'debit' => 0,
                    'credit' => (float)str_replace('.', '', $request->amount),
                    'reference_id' => $receivable->id,
                    'reference_type' => TeacherReceivable::class,
                ]);
            }
            TeacherReceivableDetail::create([
                'teacher_receivable_id' => $receivable->id,
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date
            ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teacher-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teacher-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil dicatat.');
    }

    /**
     * Show the form for edit paying a receivable.
     */
    public function editPayForm(School $school, TeacherReceivableDetail $teacher_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('teacher-receivables.edit-pay', compact('teacher_receivable_detail', 'school'));
    }

    /**
     * Process edit payment for a receivable.
     */
    public function editPay(Request $request, School $school, TeacherReceivableDetail $teacher_receivable_detail)
    {
        $user = auth()->user();
        $receivable_detail = $teacher_receivable_detail;
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

        if ($receivable_detail->teacher_receivable->amount - $receivable_detail->teacher_receivable->paid_amount < (float)str_replace('.', '', $request->amount)) {
            return back()->withErrors(['amount' => 'Pembayaran tidak dapat melebihi sisa piutang']);
        }

        if ($receivable_detail->amount < (float)str_replace('.', '', $request->amount)) {
            $diffAmount = (float)str_replace('.', '', $request->amount) - $receivable_detail->amount;
            $receivable_detail->teacher_receivable->paid_amount += $diffAmount;
        } else if ($receivable_detail->amount > (float)str_replace('.', '', $request->amount)) {
            $diffAmount = $receivable_detail->amount - (float)str_replace('.', '', $request->amount);
            $receivable_detail->teacher_receivable->paid_amount -= $diffAmount;
        }
        $receivable_detail->teacher_receivable->status = $receivable_detail->teacher_receivable->paid_amount >= $receivable_detail->teacher_receivable->amount ? 'Paid' :
            ($receivable_detail->teacher_receivable->paid_amount > 0 ? 'Partial' : 'Unpaid');
        if($receivable_detail->teacher_receivable->save()) {
            $receivable_detail->update([
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date,
                'reason' => $request->reason
            ]);

            // Update transaksi pembayaran
            Transaction::where('reference_id', $receivable_detail->teacher_receivable->id)
                ->where('reference_type', 'App\Models\TeacherReceivable')
                ->where('date', Carbon::parse($teacher_receivable_detail->period)->format('Y-m-d'))
                ->where('debit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => $receivable_detail->teacher_receivable->paid_amount,
                    'credit' => 0,
                ]);
            Transaction::where('reference_id', $receivable_detail->teacher_receivable->id)
                ->where('reference_type', 'App\Models\TeacherReceivable')
                ->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                ->where('account_id', $receivable_detail->teacher_receivable->account_id)
                ->where('credit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => 0,
                    'credit' => $receivable_detail->teacher_receivable->paid_amount,
                ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teacher-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teacher-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    /**
     * Download receipt
     */
    public function receipt(School $school, TeacherReceivableDetail $teacher_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $receivable_detail = $teacher_receivable_detail;
        $receivables = TeacherReceivable::where('id', $receivable_detail->teacher_receivable_id)->first();
        $teachers = Teacher::where('id', $receivables->teacher_id)->first();
        
        $year = \Carbon\Carbon::parse($receivable_detail->period);
        $idFormatted = str_pad($receivable_detail->id, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $year->format('Y') . '/' . $idFormatted;

        $terbilang = new \App\Services\TerbilangService();

        $data = [
            'invoice_no' => $invoiceNo,
            'date' => $year->format('M d, Y'),
            'from' => $teachers->name,
            'amount' => $receivable_detail->amount,
            'amount_words' => trim($terbilang->convert($receivable_detail->amount)).' Rupiah',
            'payment_note' => $receivable_detail->description,
            'company' => [
                'name' => $school->name,
                'telp' => $school->phone,
                'email' => $school->email,
                'logo' => $school->logo
            ]
        ];

        $pdf = PDF::loadView('teacher-receivables.receipt', $data);
        return $pdf->download('kwitansi.pdf');
    }
}
