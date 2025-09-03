<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Employee;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeReceivableDetail;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the employee receivables.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $account = $request->get('account');
        $dueDate = is_null($request->get('date')) ? '' : $request->get('date');
        $status = $request->get('status');
        $employeeId = $request->get('employee_id');
        if (auth()->user()->role != 'SchoolAdmin') {
            // SuperAdmin: Semua piutang
            $schools = School::pluck('name', 'id');
            $school = $request->get('school');
            $receivables = EmployeeReceivable::with(['school', 'employee', 'account'])
                ->when($school, function ($q) use ($school) {
                    $q->where('school_id', $school);
                })
                ->when($employeeId, function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                })
                ->when($account, function ($q) use ($account) {
                    $q->where('account_id', $account);
                })
                ->when($dueDate, function ($q) use ($dueDate) {
                    $q->where('due_date', Carbon::parse($dueDate)->format('Y-m-d'));
                })
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10)->withQueryString();
            
            return view('employee-receivables.index', compact('receivables', 'schools', 'school', 'account', 'dueDate', 'status', 'employeeId'));
        }

        // SchoolAdmin atau SuperAdmin dengan sekolah tertentu
        $school = $school ?? $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }

        $receivables = EmployeeReceivable::where('school_id', $school->id)
            ->with(['employee', 'account'])
            ->when($employeeId, function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                })
            ->when($account, function ($q) use ($account) {
                $q->where('account_id', $account);
            })
            ->when($dueDate, function ($q) use ($dueDate) {
                $q->where('due_date', Carbon::parse($dueDate)->format('Y-m-d'));
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)->withQueryString();
        return view('employee-receivables.index', compact('receivables', 'school', 'account', 'dueDate', 'status', 'employeeId'));
    }

    public function getEmployee(Request $request)
    {
        $employees = Employee::where('school_id', $request->school)->get();
        return response()->json($employees, 200);
    }

    public function getReceivableDetail($receivableId)
    {
        $details = EmployeeReceivableDetail::with('employee_receivable.employee')->where('employee_receivable_id', $receivableId)->get();
        $totalReceivable = EmployeeReceivable::find($receivableId);
        return view('partials.employee-receivable-modal', compact('details', 'totalReceivable'));
    }

    public function getPaymentHistory(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $accountId = $request->input('account_id');
        $schoolId  = $request->input('school_id');
        
        if (!$employeeId || !$accountId || !$schoolId)
             return response()->json([], 200);

        // Validasi (opsional tapi disarankan)
        $request->validate([
            'employee_id' => 'required',
            'account_id' => 'required',
            'school_id' => 'required',
        ]);

        // Query menggunakan scope yang sudah dibuat sebelumnya
        $details = EmployeeReceivableDetail::filterByEmployeeAccountSchool($employeeId, $accountId, $schoolId)->get();

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
        $employees = Employee::when($schoolId, function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%') // Piutang (1-12)
            ->get();
        return view('employee-receivables.create', compact('school', 'employees', 'accounts'));
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
            'employee_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'nullable|date',
        ];

        $messages = [
            'employee_id.required' => 'Pilih salah satu karyawan',
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
        EmployeeReceivable::create([
            'school_id' => $schoolId,
            'employee_id' => $request->employee_id,
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
            'description' => Account::find($request->account_id)->name . ' karyawan: ' . Employee::find($request->employee_id)->name,
            'debit' => $amount,
            'credit' => 0,
            'reference_id' => EmployeeReceivable::latest()->first()->id,
            'reference_type' => EmployeeReceivable::class,
        ]);

        // Catat transaksi piutang (Kredit pada akun pendapatan)
        Transaction::create([
            'school_id' => $schoolId,
            'account_id' => $request->income_account_id,
            'date' => now(),
            'description' => Account::find($request->account_id)->name . ' karyawan: ' . Employee::find($request->employee_id)->name,
            'debit' => 0,
            'credit' => $amount,
            'reference_id' => EmployeeReceivable::latest()->first()->id,
            'reference_type' => EmployeeReceivable::class,
        ]);

        // update jumlah dana untuk akun terkait
        FundManagement::where('school_id', $schoolId)
            ->where('account_id', $request->income_account_id)
            ->increment('amount', $amount);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employee-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employee-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified receivable.
     */
    public function edit(School $school, EmployeeReceivable $employee_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schoolId = $school->id;
        $employees = Employee::where('school_id', $schoolId)->get();
        $accounts = Account::when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();
        $transaction = Transaction::where([
            ['reference_id', '=', $employee_receivable->id],
            ['reference_type', '=', 'App\Models\EmployeeReceivable'],
            ['account_id', '!=', $employee_receivable->account_id],
        ])->whereRaw('CAST(credit AS int) = ?', intval($employee_receivable->amount))->first();
        
        return view('employee-receivables.edit', compact('employee_receivable', 'school', 'employees', 'accounts', 'transaction'));
    }

    /**
     * Update the specified receivable in storage.
     */
    public function update(Request $request, School $school, EmployeeReceivable $employee_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'employee_id' => 'required',
            'account_id' => 'required',
            'income_account_id' => 'required',
            'amount' => 'required',
            'due_date' => 'nullable|date',
        ], [
            'employee_id.required' => 'Pilih salah satu karyawan',
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id' => 'Pilih akun pendapatan',
            'amount.required' => 'Jumlah wajib diisi'
        ]);

        $amount = (float) str_replace('.', '', $request->final_amount);

        $employee_receivable->update([
            'employee_id' => $request->employee_id,
            'account_id' => $request->account_id,
            'amount' => $amount,
            'due_date' => $request->due_date,
        ]);

        $transaction  = Transaction::where([
            ['reference_id', '=', $employee_receivable->id],
            ['reference_type', '=', 'App\Models\EmployeeReceivable'],
        ]);

        Transaction::where([
            ['reference_id', '=', $employee_receivable->id],
            ['reference_type', '=', 'App\Models\EmployeeReceivable'],
        ])->where('account_id', $request->account_id)->update([
            'school_id' => $school->id,
            'account_id' => $request->account_id,
            'description' => Account::find($request->account_id)->name . ' karyawan: ' . $employee_receivable->employee->name,
            'debit' => $amount,
            'credit' => 0,
        ]);

        Transaction::where([
            ['reference_id', '=', $employee_receivable->id],
            ['reference_type', '=', 'App\Models\EmployeeReceivable'],
        ])->where('account_id', $request->income_account_id)->update([
            'school_id' => $school->id,
            'account_id' => $request->income_account_id,
            'description' => Account::find($request->income_account_id)->name . ' karyawan: ' . $employee_receivable->employee->name,
            'debit' => 0,
            'credit' => $amount,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employee-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employee-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil diperbarui.');
    }

    /**
     * Remove the specified receivable from storage.
     */
    public function destroy(School $school, EmployeeReceivable $employee_receivable)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::where([
            ['reference_id', '=', $employee_receivable->id],
            ['reference_type', '=', 'App\Models\EmployeeReceivable']
        ])->update(['deleted_at' => now()]);
        $employee_receivable->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employee-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employee-receivables.index', $school);
        }

        return $route->with('success', 'Piutang berhasil dihapus.');
    }

    /**
     * Show the form for paying a receivable.
     */
    public function payForm(School $school, EmployeeReceivable $employee_receivable)
    {
        $user = auth()->user();
        $receivable = $employee_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $cashAccounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-11%') // Kas Setara Kas (1-11)
            ->get();
        return view('employee-receivables.pay', compact('receivable', 'school', 'cashAccounts'));
    }

    /**
     * Process payment for a receivable.
     */
    public function pay(Request $request, School $school, EmployeeReceivable $employee_receivable)
    {
        $user = auth()->user();
        $receivable = $employee_receivable;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'amount' => 'required',
            'cash_account_id' => [
                function ($attribute, $value, $fail) use ($receivable) {
                    if ($receivable->employee_receivable_details->isEmpty() && empty($value)) {
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
            $existReceivableDetail = EmployeeReceivableDetail::where('employee_receivable_id', $receivable->id)
                ->latest()->first()->period ?? '';
            if ($existReceivableDetail) {
                // Update transaksi pembayaran
                // Debit: Kas (cash_account_id)
                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\EmployeeReceivable')
                    ->where('debit', '>', 0)
                    ->update([
                        'date' => $request->date,
                        'debit' => $receivable->paid_amount,
                    ]);

                Transaction::where('date', Carbon::parse($existReceivableDetail)->format('Y-m-d'))
                    ->where('reference_id', $receivable->id)
                    ->where('reference_type', 'App\Models\EmployeeReceivable')
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
                    'description' => 'Pembayaran piutang: ' . $receivable->employee->name,
                    'debit' => (float)str_replace('.', '', $request->amount),
                    'credit' => 0,
                    'reference_id' => $receivable->id,
                    'reference_type' => EmployeeReceivable::class,
                ]);

                // Kredit: Piutang (receivable->account_id)
                Transaction::create([
                    'school_id' => $receivable->school_id,
                    'account_id' => $receivable->account_id,
                    'date' => $request->date,
                    'description' => 'Pembayaran piutang: ' . $receivable->employee->name,
                    'debit' => 0,
                    'credit' => (float)str_replace('.', '', $request->amount),
                    'reference_id' => $receivable->id,
                    'reference_type' => EmployeeReceivable::class,
                ]);
            }
            EmployeeReceivableDetail::create([
                'employee_receivable_id' => $receivable->id,
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date
            ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employee-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employee-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil dicatat.');
    }

    /**
     * Show the form for edit paying a receivable.
     */
    public function editPayForm(School $school, EmployeeReceivableDetail $employee_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('employee-receivables.edit-pay', compact('employee_receivable_detail', 'school'));
    }

    /**
     * Process edit payment for a receivable.
     */
    public function editPay(Request $request, School $school, EmployeeReceivableDetail $employee_receivable_detail)
    {
        $user = auth()->user();
        $receivable_detail = $employee_receivable_detail;
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

        if ($receivable_detail->employee_receivable->amount - $receivable_detail->employee_receivable->paid_amount < (float)str_replace('.', '', $request->amount)) {
            return back()->withErrors(['amount' => 'Pembayaran tidak dapat melebihi sisa piutang']);
        }

        if ($receivable_detail->amount < (float)str_replace('.', '', $request->amount)) {
            $diffAmount = (float)str_replace('.', '', $request->amount) - $receivable_detail->amount;
            $receivable_detail->employee_receivable->paid_amount += $diffAmount;
        } else if ($receivable_detail->amount > (float)str_replace('.', '', $request->amount)) {
            $diffAmount = $receivable_detail->amount - (float)str_replace('.', '', $request->amount);
            $receivable_detail->employee_receivable->paid_amount -= $diffAmount;
        }
        $receivable_detail->employee_receivable->status = $receivable_detail->employee_receivable->paid_amount >= $receivable_detail->employee_receivable->amount ? 'Paid' :
            ($receivable_detail->employee_receivable->paid_amount > 0 ? 'Partial' : 'Unpaid');
        if($receivable_detail->employee_receivable->save()) {
            $receivable_detail->update([
                'description' => $request->description,
                'amount' => (float)str_replace('.', '', $request->amount),
                'period' => $request->date,
                'reason' => $request->reason
            ]);

            // Update transaksi pembayaran
            Transaction::where('reference_id', $receivable_detail->employee_receivable->id)
                ->where('reference_type', 'App\Models\EmployeeReceivable')
                ->where('date', Carbon::parse($employee_receivable_detail->period)->format('Y-m-d'))
                ->where('debit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => $receivable_detail->employee_receivable->paid_amount,
                    'credit' => 0,
                ]);
            Transaction::where('reference_id', $receivable_detail->employee_receivable->id)
                ->where('reference_type', 'App\Models\EmployeeReceivable')
                ->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                ->where('account_id', $receivable_detail->employee_receivable->account_id)
                ->where('credit', '>', 0)
                ->update([
                    'date' => $request->date,
                    'debit' => 0,
                    'credit' => $receivable_detail->employee_receivable->paid_amount,
                ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employee-receivables.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employee-receivables.index', $school);
        }

        return $route->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    /**
     * Download receipt
     */
    public function receipt(School $school, EmployeeReceivableDetail $employee_receivable_detail)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $receivable_detail = $employee_receivable_detail;
        $receivables = EmployeeReceivable::where('id', $receivable_detail->employee_receivable_id)->first();
        $employees = Employee::where('id', $receivables->employee_id)->first();
        
        $year = \Carbon\Carbon::parse($receivable_detail->period);
        $idFormatted = str_pad($receivable_detail->id, 4, '0', STR_PAD_LEFT);
        $invoiceNo = 'INV/' . $year->format('Y') . '/' . $idFormatted;

        $terbilang = new \App\Services\TerbilangService();

        $data = [
            'invoice_no' => $invoiceNo,
            'date' => $year->format('M d, Y'),
            'from' => $employees->name,
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

        $pdf = PDF::loadView('employee-receivables.receipt', $data);
        return $pdf->download('kwitansi.pdf');
    }
}
