<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\StudentReceivables;
use App\Models\TeacherReceivable;
use App\Models\EmployeeReceivable;
use App\Models\BeginningBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Artisan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access')->only(['dashboard', 'beginningBalance', 'generalJournal', 'ledger', 'profitLoss', 'balanceSheet']);
    }

    /**
     * Export Beginning Balance to Excel
     */
    protected function exportBeginningBalance($transactions, $school, $date, $totalDebit, $totalCredit, $transactionsBySchool = null)
    {
        Log::info('Exporting Beginning Balance', ['school_id' => $school?->id, 'date' => $date->format('Y-m-d'), 'transaction_count' => $transactions->count()]);
        $schoolName = $school ? str_replace(' ', '_', $school->name) : 'Semua_Sekolah';
        $fileName = "Saldo_Awal_{$date->format('Y-m-d')}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($transactions, $school, $totalDebit, $totalCredit, $transactionsBySchool) implements FromCollection, WithHeadings, WithTitle {
                protected $transactions;
                protected $school;
                protected $totalDebit;
                protected $totalCredit;
                protected $transactionsBySchool;

                public function __construct($transactions, $school, $totalDebit, $totalCredit, $transactionsBySchool = null)
                {
                    $this->transactions = $transactions;
                    $this->school = $school;
                    $this->totalDebit = $totalDebit;
                    $this->totalCredit = $totalCredit;
                    $this->transactionsBySchool = $transactionsBySchool;
                }

                public function collection()
                {
                    $data = collect();

                    if ($this->school) {
                        // Sekolah spesifik
                        foreach ($this->transactions as $transaction) {
                            $data->push([
                                \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y'),
                                $transaction['account'],
                                $transaction['description'],
                                $transaction['debit'],
                                $transaction['credit'],
                            ]);
                        }
                        $data->push(['', '', 'Total', $this->totalDebit, $this->totalCredit]);
                    } else {
                        // Tanpa filter sekolah
                        foreach ($this->transactionsBySchool as $schoolData) {
                            $data->push([$schoolData['school']->name]);
                            foreach ($schoolData['transactions'] as $transaction) {
                                $data->push([
                                    \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y'),
                                    $transaction['school']->name,
                                    $transaction['account'],
                                    $transaction['description'],
                                    $transaction['debit'],
                                    $transaction['credit'],
                                ]);
                            }
                            $data->push(['', '', '', 'Total', $schoolData['total_debit'], $schoolData['total_credit']]);
                            $data->push([]); // Baris kosong antar sekolah
                        }
                        $data->push(['', '', '', 'Grand Total', $this->totalDebit, $this->totalCredit]);
                    }

                    return $data;
                }

                public function headings(): array
                {
                    return $this->school
                        ? ['Tanggal', 'Akun', 'Deskripsi', 'Pemasukan', 'Pengeluaran']
                        : ['Tanggal', 'Sekolah', 'Akun', 'Deskripsi', 'Pemasukan', 'Pengeluaran'];
                }

                public function title(): string
                {
                    return 'Saldo Awal';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export Beginning Balance', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Saldo Awal
     */
    public function beginningBalance(Request $request, School $school = null)
    {
        Log::info('Accessing Beginning Balance', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? $school->id : null;
        $perPage = 10;
        $currentPage = $request->input('page', 1);

        $date = Carbon::parse($request->input('date', now()->startOfMonth()))->startOfMonth();
        $endOfPreviousMonth = $date->subMonth()->endOfMonth();

        if ($schoolId) {
            // Sekolah spesifik
            $transactions = Transaction::join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->where('transactions.school_id', $schoolId)
                ->where('transactions.date', '<=', $endOfPreviousMonth)
                ->select(
                    'transactions.id',
                    'transactions.date',
                    'accounts.code as account_code',
                    'accounts.name as account_name',
                    'transactions.description',
                    'transactions.debit',
                    'transactions.credit',
                    'transactions.school_id'
                )
                ->orderBy('transactions.date')
                ->get()
                ->map(function ($transaction) use ($school) {
                    return [
                        'id' => $transaction->id,
                        'date' => $transaction->date,
                        'account' => $transaction->account_code . ' - ' . $transaction->account_name,
                        'description' => $transaction->description,
                        'debit' => $transaction->debit,
                        'credit' => $transaction->credit,
                        'school' => $school,
                        'school_id' => $transaction->school_id,
                    ];
                });

            $totalDebit = $transactions->sum('debit');
            $totalCredit = $transactions->sum('credit');

            if ($request->has('export') && $request->input('export') === 'excel') {
                return $this->exportBeginningBalance($transactions, $school, $date, $totalDebit, $totalCredit);
            }

            return view('reports.beginning-balance', compact('school', 'schools', 'date', 'transactions', 'totalDebit', 'totalCredit'));
        } else {
            // Tanpa filter sekolah
            $transactionsBySchool = [];
            $allTransactions = collect();

            foreach ($schoolIds as $sid) {
                $schoolData = School::find($sid);
                $schoolTransactions = Transaction::join('accounts', 'transactions.account_id', '=', 'accounts.id')
                    ->where('transactions.school_id', $sid)
                    ->where('transactions.date', '<=', $endOfPreviousMonth)
                    ->select(
                        'transactions.id',
                        'transactions.date',
                        'accounts.code as account_code',
                        'accounts.name as account_name',
                        'transactions.description',
                        'transactions.debit',
                        'transactions.credit',
                        'transactions.school_id'
                    )
                    ->orderBy('transactions.date')
                    ->get()
                    ->map(function ($transaction) use ($schoolData) {
                        return [
                            'id' => $transaction->id,
                            'date' => $transaction->date,
                            'account' => $transaction->account_code . ' - ' . $transaction->account_name,
                            'description' => $transaction->description,
                            'debit' => $transaction->debit,
                            'credit' => $transaction->credit,
                            'school' => $schoolData,
                            'school_id' => $transaction->school_id,
                        ];
                    });

                if ($schoolTransactions->isNotEmpty()) {
                    $transactionsBySchool[$sid] = [
                        'school' => $schoolData,
                        'transactions' => $schoolTransactions,
                        'total_debit' => $schoolTransactions->sum('debit'),
                        'total_credit' => $schoolTransactions->sum('credit'),
                    ];
                    $allTransactions = $allTransactions->merge($schoolTransactions);
                }
            }

            $totalDebit = $allTransactions->sum('debit');
            $totalCredit = $allTransactions->sum('credit');

            if ($request->has('export') && $request->input('export') === 'excel') {
                return $this->exportBeginningBalance($allTransactions, null, $date, $totalDebit, $totalCredit, $transactionsBySchool);
            }

            return view('reports.beginning-balance', compact('school', 'schools', 'date', 'transactions', 'transactionsBySchool', 'totalDebit', 'totalCredit'));
        }
    }

    /**
     * Export General Journal to Excel
     */
    protected function exportGeneralJournal($transactions, $school, $startDate, $endDate)
    {
        Log::info('Exporting General Journal', ['school_id' => $school?->id, 'start_date' => $startDate, 'end_date' => $endDate]);
        $schoolName = $school ? str_replace(' ', '_', $school->name) : 'Semua_Sekolah';
        $fileName = "Jurnal_Umum_{$startDate}_to_{$endDate}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($transactions, $school, $schoolName) implements FromCollection, WithHeadings, WithTitle {
                protected $transactions;
                protected $school;
                protected $schoolName;

                public function __construct($transactions, $school, $schoolName)
                {
                    $this->transactions = $transactions;
                    $this->school = $school;
                    $this->schoolName = $schoolName;
                }

                public function collection()
                {
                    $data = collect();
                    foreach ($this->transactions as $schoolId => $schoolTransactions) {
                        foreach ($schoolTransactions as $transaction) {
                            $data->push([
                                str_replace('_', ' ', $this->schoolName),
                                $transaction->date,
                                $transaction->description,
                                $transaction->account->code . ' - ' . $transaction->account->name,
                                $transaction->debit,
                                $transaction->credit,
                            ]);
                        }
                        $data->push([]); // Empty row for separation
                    }
                    return $data;
                }

                public function headings(): array
                {
                    return ['Sekolah', 'Tanggal', 'Deskripsi', 'Akun', 'Pemasukan', 'Pengeluaran'];
                }

                public function title(): string
                {
                    return 'Jurnal Umum';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export General Journal', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Jurnal Umum
     */
    public function generalJournal(Request $request, School $school = null)
    {
        Log::info('Accessing General Journal', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? collect([$school]) : collect([$user->school]);

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $schoolIds = $schools->pluck('id');

        $transactions = Transaction::whereIn('school_id', $schoolIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['school', 'account'])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy('school_id');

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportGeneralJournal($transactions, $school, $startDate, $endDate);
        }

        return view('reports.general-journal', compact('school', 'schools', 'transactions', 'startDate', 'endDate'));
    }

    /**
     * Export Ledger to Excel
     */
    protected function exportLedger($accounts, $school, $startDate, $endDate)
    {
        Log::info('Exporting Ledger', ['school_id' => $school?->id, 'start_date' => $startDate, 'end_date' => $endDate]);
        $schoolName = $school ? str_replace(' ', '_', $school->name) : 'Semua_Sekolah';
        $fileName = "Buku_Besar_{$startDate}_to_{$endDate}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($accounts, $school, $schoolName) implements FromCollection, WithHeadings, WithTitle {
                protected $accounts;
                protected $school;
                protected $schoolName;

                public function __construct($accounts, $school, $schoolName)
                {
                    $this->accounts = $accounts;
                    $this->school = $school;
                    $this->schoolName = $schoolName;
                }

                public function collection()
                {
                    $data = collect();
                    foreach ($this->accounts as $schoolId => $schoolAccounts) {
                        $schoolName = School::find($schoolId)->name ?? 'Sekolah Tidak Diketahui';
                        foreach ($schoolAccounts as $item) {
                            $data->push(["Akun: {$item['account']->code} - {$item['account']->name}"]);
                            $data->push(['Saldo Awal', '', '', '', $item['opening_balance'], '', '', '', '', '']);
                            $runningBalance = $item['opening_balance'];
                            foreach ($item['transactions'] as $transaction) {
                                $runningBalance += $transaction['balance'];
                                $studentReceivable = $transaction['student_receivable'];
                                $teacherReceivable = $transaction['teacher_receivable'];
                                $employeeReceivable = $transaction['employee_receivable'];
                                if ($studentReceivable && $transaction['transaction']->credit > 0 && Str::startsWith($transaction['transaction']->account->code, '1-12')) {
                                    foreach ($studentReceivable->student_receivable_details as $detail) {
                                        $data->push([
                                            str_replace('_', ' ', $this->schoolName),
                                            $transaction['transaction']->date,
                                            $transaction['transaction']->description,
                                            $transaction['transaction']->debit,
                                            $transaction['transaction']->credit,
                                            $runningBalance,
                                            $studentReceivable->student->name,
                                            $detail->description,
                                            $detail->amount,
                                            Carbon::parse($detail->created_at)->format('Y-m-d'),
                                        ]);
                                    }
                                } elseif ($teacherReceivable && $transaction['transaction']->credit > 0 && Str::startsWith($transaction['transaction']->account->code, '1-12')) {
                                    foreach ($teacherReceivable->teacher_receivable_details as $detail) {
                                        $data->push([
                                            str_replace('_', ' ', $this->schoolName),
                                            $transaction['transaction']->date,
                                            $transaction['transaction']->description,
                                            $transaction['transaction']->debit,
                                            $transaction['transaction']->credit,
                                            $runningBalance,
                                            $teacherReceivable->teacher->name,
                                            $detail->description,
                                            $detail->amount,
                                            Carbon::parse($detail->created_at)->format('Y-m-d'),
                                        ]);
                                    }
                                } elseif ($employeeReceivable && $transaction['transaction']->credit > 0 && Str::startsWith($transaction['transaction']->account->code, '1-12')) {
                                    foreach ($employeeReceivable->employee_receivable_details as $detail) {
                                        $data->push([
                                            str_replace('_', ' ', $this->schoolName),
                                            $transaction['transaction']->date,
                                            $transaction['transaction']->description,
                                            $transaction['transaction']->debit,
                                            $transaction['transaction']->credit,
                                            $runningBalance,
                                            $employeeReceivable->employee->name,
                                            $detail->description,
                                            $detail->amount,
                                            Carbon::parse($detail->created_at)->format('Y-m-d'),
                                        ]);
                                    }
                                } else {
                                    $data->push([
                                        str_replace('_', ' ', $this->schoolName),
                                        $transaction['transaction']->date,
                                        $transaction['transaction']->description,
                                        $transaction['transaction']->debit,
                                        $transaction['transaction']->credit,
                                        $runningBalance,
                                        '',
                                        '',
                                        '',
                                        '',
                                    ]);
                                }
                            }
                            $data->push(['Saldo Akhir', '', '', '', $item['closing_balance'], '', '', '', '', '']);
                            $data->push([]); // Empty row for separation
                        }
                    }
                    return $data;
                }

                public function headings(): array
                {
                    return ['Sekolah', 'Tanggal', 'Deskripsi', 'Pemasukan', 'Pengeluaran', 'Saldo', 'Nama', 'Deskripsi', 'Jumlah Pembayaran', 'Tanggal Pembayaran'];
                }

                public function title(): string
                {
                    return 'Buku Besar';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export Ledger', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Buku Besar
     */
    public function ledger(Request $request, School $school = null)
    {
        Log::info('Accessing Ledger', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? collect([$school]) : collect([$user->school]);

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $account = $request->get('account');
        $accountType = $request->get('account_type');
        $singleAccount = Account::find($account);
        $schoolIds = $schools->pluck('id');

        $paymentDetails = StudentReceivables::whereIn('school_id', $schoolIds)
            ->with(['student', 'student_receivable_details' => fn($q) => $q->orderBy('created_at')])
            ->get()
            ->keyBy('id');
        
        $paymentTeacherDetails = TeacherReceivable::whereIn('school_id', $schoolIds)
            ->with(['teacher', 'teacher_receivable_details' => fn($q) => $q->orderBy('created_at')])
            ->get()
            ->keyBy('id');
        
        $paymentEmployeeDetails = EmployeeReceivable::whereIn('school_id', $schoolIds)
            ->with(['employee', 'employee_receivable_details' => fn($q) => $q->orderBy('created_at')])
            ->get()
            ->keyBy('id');

        $accounts = Account::select('*')
            ->with(['transactions' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])
                ->whereIn('school_id', $schoolIds)
                ->with('account')])
            ->when($accountType, function ($q) use ($accountType) {
                $q->where('account_type', $accountType);
            })
            ->when($account, function ($q) use ($account) {
                $q->where('id', $account);
            })
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(code, '-', 1) AS INTEGER),
                LENGTH(SUBSTRING_INDEX(code, '-', 2)),
                CAST(SUBSTRING_INDEX(code, '-', 2) AS INTEGER),
                CAST(COALESCE(SUBSTRING_INDEX(code, '-', 3), '0') AS INTEGER)
            ")
            ->get()
            ->map(function ($account) use ($startDate, $schoolIds, $paymentDetails, $paymentTeacherDetails, $paymentEmployeeDetails) {
                $openingBalance = Transaction::where('account_id', $account->id)
                    ->where('date', '<', $startDate)
                    ->whereIn('school_id', $schoolIds)
                    ->sum('debit') - Transaction::where('account_id', $account->id)
                    ->where('date', '<', $startDate)
                    ->whereIn('school_id', $schoolIds)
                    ->sum('credit');
                $openingBalance = $account->normal_balance === 'Debit' ? $openingBalance : -$openingBalance;

                $transactions = $account->transactions->map(function ($transaction) use ($account, $paymentDetails, $paymentTeacherDetails, $paymentEmployeeDetails) {
                    $balance = $account->normal_balance === 'Debit'
                        ? $transaction->debit - $transaction->credit
                        : $transaction->credit - $transaction->debit;

                    $studentReceivable = null;
                    if ($transaction->reference_type === 'App\Models\StudentReceivables' && $transaction->reference_id) {
                        $studentReceivable = $paymentDetails->get($transaction->reference_id);
                    }
                    $teacherReceivable = null;
                    if ($transaction->reference_type === 'App\Models\TeacherReceivable' && $transaction->reference_id) {
                        $teacherReceivable = $paymentTeacherDetails->get($transaction->reference_id);
                    }
                    $employeeReceivable = null;
                    if ($transaction->reference_type === 'App\Models\EmployeeReceivable' && $transaction->reference_id) {
                        $employeeReceivable = $paymentEmployeeDetails->get($transaction->reference_id);
                    }

                    return [
                        'transaction' => $transaction,
                        'balance' => $balance,
                        'student_receivable' => $studentReceivable,
                        'teacher_receivable' => $teacherReceivable,
                        'employee_receivable' => $employeeReceivable
                    ];
                });

                $closingBalance = $openingBalance + $transactions->sum('balance');

                return [
                    'account' => $account,
                    'opening_balance' => $openingBalance,
                    'transactions' => $transactions,
                    'closing_balance' => $closingBalance,
                ];
            })->filter(fn($item) => $item['transactions']->isNotEmpty() || $item['opening_balance'] != 0 || $item['closing_balance'] != 0)
            ->groupBy(fn($item) => $item['account']->transactions->first()->school_id ?? 0);

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportLedger($accounts, $school, $startDate, $endDate);
        }

        return view('reports.ledger', compact('school', 'schools', 'accounts', 'startDate', 'endDate', 'accountType', 'account', 'singleAccount'));
    }

    /**
     * Neraca Saldo Awal (Belum Disesuaikan)
     */
    public function trialBalanceBefore(Request $request, School $school = null)
    {
        Log::info('Accessing Trial Balance Before', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolIds = $school ? [$school->id] : $schools->pluck('id');

        $date = Carbon::parse($request->input('date', now()->endOfMonth()))->toDateString();

        $trialBalance = $this->calculateTrialBalance($schoolIds, $date, false);

        if ($request->has('export') && $request->input('export') === 'excel') {
            return $this->exportTrialBalance($trialBalance, $school, $date, 'Before');
        }

        return view('reports.trial-balance-before', compact('school', 'schools', 'date', 'trialBalance'));
    }

    /**
     * Jurnal Penyesuaian
     */
    public function adjustingEntries(Request $request, School $school = null)
    {
        Log::info('Accessing Adjusting Entries', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);

        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->toDateString();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->toDateString();
        $schoolId = $request->input('school');

        $query = Transaction::with(['school', 'account'])
            ->where('type', 'adjustment')
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->when($user->role === 'SchoolAdmin', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            })
            ->whereBetween('date', [$startDate, $endDate]);

        $transactions = $query->orderBy('date')->paginate(10);

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportAdjustingEntries($transactions, $school, $startDate, $endDate);
        }

        return view('reports.adjusting-entries', compact('school', 'schools', 'startDate', 'endDate', 'transactions'));
    }

    /**
     * Export Jurnal Penyesuaian
     */
    protected function exportAdjustingEntries($transactions, $school, $startDate, $endDate)
    {
        $schoolName = $school ? Str::slug($school->name) : 'Semua_Sekolah';
        $fileName = "Jurnal_Penyesuaian_{$startDate}_{$endDate}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($transactions) implements FromCollection, WithHeadings, WithTitle {
                protected $transactions;

                public function __construct($transactions)
                {
                    $this->transactions = $transactions;
                }

                public function collection()
                {
                    return $this->transactions->map(function ($transaction) {
                        return [
                            Carbon::parse($transaction->date)->format('d-m-Y'),
                            $transaction->school->name,
                            $transaction->account->code . ' - ' . $transaction->account->name,
                            $transaction->description ?? '-',
                            $transaction->debit,
                            $transaction->credit,
                        ];
                    })->prepend(['Tanggal', 'Sekolah', 'Akun', 'Deskripsi', 'Pemasukan', 'Pengeluaran']);
                }

                public function headings(): array
                {
                    return [];
                }

                public function title(): string
                {
                    return 'Jurnal Penyesuaian';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export Adjusting Entries', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Neraca Saldo Akhir (Sudah Disesuaikan)
     */
    public function trialBalanceAfter(Request $request, School $school = null)
    {
        Log::info('Accessing Trial Balance After', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolIds = $school ? [$school->id] : $schools->pluck('id');

        $date = Carbon::parse($request->input('date', now()->endOfMonth()))->toDateString();

        $trialBalance = $this->calculateTrialBalance($schoolIds, $date, true);

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportTrialBalance($trialBalance, $school, $date, 'After');
        }

        return view('reports.trial-balance-after', compact('school', 'schools', 'date', 'trialBalance'));
    }

    /**
     * Calculate Trial Balance
     */
    protected function calculateTrialBalance($schoolIds, $date, $includeAdjustments)
    {
        $transactions = Transaction::with(['school', 'account'])
            ->whereIn('school_id', $schoolIds)
            ->where('date', '<=', $date)
            ->when(!$includeAdjustments, function ($q) {
                $q->where('type', '!=', 'adjustment');
            })
            ->get()
            ->groupBy('school_id');

        $trialBalance = collect();

        foreach ($schoolIds as $schoolId) {
            $school = School::find($schoolId);
            $accounts = [];

            $schoolTransactions = $transactions->get($schoolId, collect());
            foreach ($schoolTransactions as $transaction) {
                $account = $transaction->account;
                if (!isset($accounts[$account->id])) {
                    $accounts[$account->id] = [
                        'school' => $school,
                        'account' => $account,
                        'debit' => 0,
                        'credit' => 0,
                    ];
                }
                $accounts[$account->id]['debit'] += $transaction->debit;
                $accounts[$account->id]['credit'] += $transaction->credit;
            }

            // Filter akun dengan debit atau kredit tidak nol, lalu konversi ke koleksi
            $schoolAccounts = collect($accounts)
                ->filter(function ($item) {
                    return $item['debit'] != 0 || $item['credit'] != 0;
                })
                ->values();

            if ($schoolAccounts->isNotEmpty()) {
                $trialBalance->push($schoolAccounts);
            }
        }

        return $trialBalance;
    }

    /**
     * Export Neraca Saldo
     */
    protected function exportTrialBalance($trialBalance, $school, $date, $type)
    {
        $type = $type == 'Before' ? 'Awal' : 'Akhir';
        $schoolName = $school ? Str::slug($school->name) : 'Semua_Sekolah';
        $fileName = "Neraca_Saldo_{$type}_{$date}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($trialBalance, $schoolName, $type) implements FromCollection, WithHeadings, WithTitle {
                protected $trialBalance;
                protected $schoolName;
                protected $type;

                public function __construct($trialBalance, $schoolName, $type)
                {
                    $this->trialBalance = $trialBalance;
                    $this->schoolName = $schoolName;
                    $this->type = $type;
                }

                public function collection()
                {
                    $data = collect();
                    foreach ($this->trialBalance as $schoolAccounts) {
                        foreach ($schoolAccounts as $item) {
                            $data->push([
                                $this->schoolName,
                                $item['account']->code . '-' . $item['account']->name,
                                $item['debit'],
                                $item['credit'],
                            ]);
                        }
                        $data->push([]);
                    }
                    return $data;
                }

                public function headings(): array
                {
                    return ['Sekolah', 'Akun', 'Pemasukan', 'Pengeluaran'];
                }

                public function title(): string
                {
                    return "Neraca Saldo " . $this->type;
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error("Failed to export Trial Balance {$type}", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Keuangan (Laba Rugi dan Neraca)
     */
    public function financialStatements(Request $request, School $school = null)
    {
        Log::info('Accessing Financial Statements', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolIds = $school ? [$school->id] : $schools->pluck('id');

        $date = Carbon::parse($request->input('date', now()->endOfMonth()))->toDateString();

        $profitLoss = $this->calculateProfitLoss($schoolIds, $date);
        $balanceSheet = $this->calculateBalanceSheet($schoolIds, $date);

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportFinancialStatements($profitLoss, $balanceSheet, $school, $date);
        }

        return view('reports.financial-statements', compact('school', 'schools', 'date', 'profitLoss', 'balanceSheet'));
    }

    /**
     * Calculate Laba Rugi
     */
    protected function calculateProfitLoss($schoolIds, $date)
    {
        $transactions = Transaction::with(['school', 'account'])
            ->whereIn('school_id', $schoolIds)
            ->where('date', '<=', $date)
            ->get()
            ->groupBy('school_id');

        $profitLoss = collect();

        foreach ($schoolIds as $schoolId) {
            $school = School::find($schoolId);
            $revenues = collect();
            $expenses = collect();

            $schoolTransactions = $transactions->get($schoolId, collect());
            foreach ($schoolTransactions as $transaction) {
                $account = $transaction->account;
                if ($account->account_type === 'Pendapatan') {
                    $revenues[$account->id] = ($revenues[$account->id] ?? 0) + ($transaction->credit - $transaction->debit);
                } elseif ($account->account_type === 'Biaya') {
                    $expenses[$account->id] = ($expenses[$account->id] ?? 0) + ($transaction->debit - $transaction->credit);
                }
            }

            $profitLoss->push([
                'school' => $school,
                'revenues' => $revenues->map(function ($amount, $accountId) {
                    $account = Account::find($accountId);
                    return ['account' => $account, 'amount' => $amount];
                })->values(),
                'expenses' => $expenses->map(function ($amount, $accountId) {
                    $account = Account::find($accountId);
                    return ['account' => $account, 'amount' => $amount];
                })->values(),
            ]);
        }

        return $profitLoss;
    }

    /**
     * Calculate Neraca
     */
    protected function calculateBalanceSheet($schoolIds, $date)
    {
        $trialBalance = $this->calculateTrialBalance($schoolIds, $date, true);

        $balanceSheet = collect();

        foreach ($trialBalance as $schoolAccounts) {
            $school = $schoolAccounts->first()['school'];
            $currentAssets = collect();
            $fixAssets = collect();
            $investments = collect();
            $liabilities = collect();
            $equity = collect();

            foreach ($schoolAccounts as $item) {
                $account = $item['account'];
                $balance = ($account->normal_balance === 'Debit')
                    ? ($item['debit'] - $item['credit'])
                    : ($item['credit'] - $item['debit']);

                if ($account->account_type === 'Aset Lancar') {
                    $currentAssets->push(['account' => $account, 'balance' => $balance]);
                } else if ($account->account_type === 'Aset Tetap') {
                    $fixAssets->push(['account' => $account, 'balance' => $balance]);
                } else if ($account->account_type === 'Investasi') {
                    $investments->push(['account' => $account, 'balance' => $balance]);
                } elseif ($account->account_type === 'Kewajiban') {
                    $liabilities->push(['account' => $account, 'balance' => $balance]);
                } elseif ($account->account_type === 'Aset Neto') {
                    $equity->push(['account' => $account, 'balance' => $balance]);
                }
            }

            $balanceSheet->push([
                'school' => $school,
                'currentAssets' => $currentAssets,
                'fixAssets' => $fixAssets,
                'investments' => $investments,
                'liabilities' => $liabilities,
                'equity' => $equity,
            ]);
        }

        return $balanceSheet;
    }

    /**
     * Export Laporan Keuangan
     */
    protected function exportFinancialStatements($profitLoss, $balanceSheet, $school, $date)
    {
        $schoolName = $school ? Str::slug($school->name) : 'Semua_Sekolah';
        $fileName = "Laporan_Keuangan_{$date}_{$schoolName}.xlsx";

        try {
            return Excel::download(new class($profitLoss, $balanceSheet, $schoolName) implements FromCollection, WithHeadings, WithTitle {
                protected $profitLoss;
                protected $balanceSheet;
                protected $schoolName;

                public function __construct($profitLoss, $balanceSheet, $schoolName)
                {
                    $this->profitLoss = $profitLoss;
                    $this->balanceSheet = $balanceSheet;
                    $this->schoolName = $schoolName;
                }

                public function collection()
                {
                    $data = collect();

                    $data->push(['Laporan Laba Rugi']);
                    $data->push(['Sekolah', 'Akun', 'Jenis', 'Jumlah']);
                    foreach ($this->profitLoss as $item) {
                        foreach ($item['revenues'] as $revenue) {
                            $data->push([
                                $this->schoolName,
                                "{$revenue['account']->code} - {$revenue['account']->name}",
                                'Pendapatan',
                                $revenue['amount'],
                            ]);
                        }
                        foreach ($item['expenses'] as $expense) {
                            $data->push([
                                $this->schoolName,
                                "{$expense['account']->code} - {$expense['account']->name}",
                                'Biaya',
                                $expense['amount'],
                            ]);
                        }
                        $data->push([]);
                    }

                    $data->push(['Neraca']);
                    $data->push(['Sekolah', 'Akun', 'Jenis', 'Jumlah']);
                    foreach ($this->balanceSheet as $item) {
                        foreach ($item['currentAssets'] as $asset) {
                            $data->push([
                                $this->schoolName,
                                "{$asset['account']->code} - {$asset['account']->name}",
                                'Aset Lancar',
                                $asset['balance'],
                            ]);
                        }
                        foreach ($item['fixAssets'] as $asset) {
                            $data->push([
                                $this->schoolName,
                                "{$asset['account']->code} - {$asset['account']->name}",
                                'Aset Tetap',
                                $asset['balance'],
                            ]);
                        }
                        foreach ($item['investments'] as $asset) {
                            $data->push([
                                $this->schoolName,
                                "{$asset['account']->code} - {$asset['account']->name}",
                                'Investasi',
                                $asset['balance'],
                            ]);
                        }
                        foreach ($item['liabilities'] as $liability) {
                            $data->push([
                                $this->schoolName,
                                "{$liability['account']->code} - {$liability['account']->name}",
                                'Kewajiban',
                                $liability['balance'],
                            ]);
                        }
                        foreach ($item['equity'] as $equity) {
                            $data->push([
                                $this->schoolName,
                                "{$equity['account']->code} - {$equity['account']->name}",
                                'Aset Neto',
                                $equity['balance'],
                            ]);
                        }
                        $data->push([]);
                    }

                    return $data;
                }

                public function headings(): array
                {
                    return [];
                }

                public function title(): string
                {
                    return 'Laporan Keuangan';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export Financial Statements', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk resolve sekolah berdasarkan role pengguna
     */
    private function resolveSchool($user, $school)
    {
        Log::info('Resolving School', ['user_id' => $user->id, 'role' => $user->role, 'school_id' => $school?->id, 'request_school_id' => request()->get('school_id')]);
        if ($user->role !== 'SchoolAdmin') {
            if (!$school) {
                $reqSchool = request()->input('school');
                $school = School::when($reqSchool, fn($q) => $q->where('id', $reqSchool))->first();
            }
            return $school;
        }
        $school = $school ?: $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }
        return $school;
    }
}