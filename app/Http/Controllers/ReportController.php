<?php

namespace App\Http\Controllers;

use Artisan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    School,
    Account,
    Transaction,
    StudentReceivables,
    TeacherReceivable,
    EmployeeReceivable,
    BeginningBalance,
    FinancialPeriod,
    InitialBalance,
    CashManagement
};
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithEvents,
    WithStyles
};
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access')->only(['dashboard', 'beginningBalance', 'generalJournal', 'ledger', 'profitLoss', 'balanceSheet']);
    }

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
                                Carbon::parse($transaction['date'])->format('d/m/Y'),
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
                                    Carbon::parse($transaction['date'])->format('d/m/Y'),
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
                        $data->push([]);
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

    public function generalJournal_old(Request $request, School $school = null)
    {
        Log::info('Accessing General Journal', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);

        $activePeriod = null;
        if ($school) {
            $activePeriod = FinancialPeriod::where('school_id', $school->id)
                ->where('is_active', true)
                ->first();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate && $activePeriod) {
            $startDate = $activePeriod->start_date->toDateString();
        }
        if (!$endDate && $activePeriod) {
            $endDate = $activePeriod->end_date->toDateString();
        }

        $schoolIds = $schools->pluck('id');

        $transactions = Transaction::whereIn('school_id', $schoolIds)
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->with(['school', 'account'])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy('school_id');

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportGeneralJournal($transactions, $school, $startDate, $endDate);
        }

        return view('reports.general-journal', compact('school', 'schools', 'transactions', 'startDate', 'endDate', 'activePeriod'));
    }

    protected function printGeneralJournalPdf(Collection $transactionsBySchool, ?School $school, ?string $startDate, ?string $endDate, ?FinancialPeriod $activePeriod)
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($transactionsBySchool as $schoolId => $transactions) {
            foreach ($transactions as $transaction) {
                $totalDebit += $transaction->debit;
                $totalCredit += $transaction->credit;
            }
        }
        
        $data = compact('school', 'startDate', 'endDate', 'transactionsBySchool', 'totalDebit', 'totalCredit', 'activePeriod');
        
        $pdf = Pdf::loadView('reports.pdf.general-journal-pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $schoolName = $school ? \Str::slug($school->name) : 'Semua-Sekolah';
        $fileName = "Jurnal-Umum-{$schoolName}-" . date('Ymd') . ".pdf";
        
        return $pdf->download($fileName);
    }

    public function generalJournal(Request $request, School $school = null)
    {
        Log::info('Accessing General Journal', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);

        $activePeriod = null;
        if ($school) {
            $activePeriod = FinancialPeriod::where('school_id', $school->id)
                ->where('is_active', true)
                ->first();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate && $activePeriod) {
            $startDate = $activePeriod->start_date->toDateString();
        }
        if (!$endDate && $activePeriod) {
            $endDate = $activePeriod->end_date->toDateString();
        }

        if ($school) {
            $schoolIds = [$school->id];
        } else {
            $schoolIds = $schools->pluck('id');
        }

        $transactions = Transaction::whereIn('school_id', $schoolIds)
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->with(['school', 'account'])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy('school_id');

        if ($request->has('export')) {
            if ($request->export === 'excel') {
                return $this->exportGeneralJournal($transactions, $school, $startDate, $endDate);
            } elseif ($request->export === 'pdf') {
                return $this->printGeneralJournalPdf($transactions, $school, $startDate, $endDate, $activePeriod);
            }
        }
        
        return view('reports.general-journal', compact('school', 'schools', 'transactions', 'startDate', 'endDate', 'activePeriod'));
    }

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
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);

        $activePeriod = null;
        if ($school) {
            $activePeriod = FinancialPeriod::where('school_id', $school->id)
                ->where('is_active', true)
                ->first();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate && $activePeriod) {
            $startDate = $activePeriod->start_date->toDateString();
        }
        if (!$endDate && $activePeriod) {
            $endDate = $activePeriod->end_date->toDateString();
        }

        $account = $request->get('account');
        $accountType = $request->get('account_type');
        $singleAccount = Account::find($account);
        $schoolIds = $schools->pluck('id');

        $initialBalances = collect();
        if ($activePeriod) {
            $initialBalances = InitialBalance::where('school_id', $school->id)
                ->where('financial_period_id', $activePeriod->id)
                ->get()
                ->keyBy('account_id');
        }

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
            ->map(function ($account) use ($startDate, $schoolIds, $paymentDetails, $paymentTeacherDetails, $paymentEmployeeDetails, $activePeriod, $initialBalances) {
                
                $initialAmount = optional($initialBalances->get($account->id))->amount ?? 0;
                
                $transactionsBeforeReportStart = Transaction::where('account_id', $account->id)
                    ->whereIn('school_id', $schoolIds)
                    ->whereBetween('date', [
                        $activePeriod->start_date->toDateString(),
                        Carbon::parse($startDate)->subDay()->toDateString()
                    ])
                    ->sum(DB::raw('debit - credit'));

                $openingBalance = $initialAmount + $transactionsBeforeReportStart;
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

    public function trialBalance(Request $request, School $school = null)
    {
        Log::info('Accessing Trial Balance', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? $school->id : null;

        $activePeriod = null;
        if ($schoolId) {
            $activePeriod = FinancialPeriod::where('school_id', $schoolId)->where('is_active', true)->first();
        }

        $startDate = $request->input('start_date', optional($activePeriod)->start_date ?? now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', optional($activePeriod)->end_date ?? now()->endOfMonth()->toDateString());

        $initialBalances = InitialBalance::where('school_id', $schoolId)
            ->where('financial_period_id', optional($activePeriod)->id)
            ->get()
            ->keyBy('account_id');

        $transactions = Transaction::where('school_id', $schoolId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('account_id');

        $trialBalance = collect();
        $accounts = Account::orderBy('code')->get();

        foreach ($accounts as $account) {
            $initialBalance = optional($initialBalances->get($account->id))->amount ?? 0;
            $accountTransactions = $transactions->get($account->id) ?? collect();

            $debit = $accountTransactions->sum('debit');
            $credit = $accountTransactions->sum('credit');

            $totalDebit = $initialBalance + $debit;
            $totalCredit = $credit;

            if ($account->normal_balance === 'Kredit') {
                $totalDebit = $debit;
                $totalCredit = $initialBalance + $credit;
            }

            if ($totalDebit > 0 || $totalCredit > 0) {
                $trialBalance->push([
                    'code' => $account->code,
                    'name' => $account->name,
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                ]);
            }
        }

        $totalDebit = $trialBalance->sum('debit');
        $totalCredit = $trialBalance->sum('credit');

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportTrialBalance($trialBalance, $school, $startDate, $endDate);
        }

        return view('reports.trial-balance', compact('school', 'schools', 'trialBalance', 'startDate', 'endDate', 'totalDebit', 'totalCredit'));
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

        $activePeriod = null;
        if ($school) {
            $activePeriod = FinancialPeriod::where('school_id', $school->id)
                ->where('is_active', true)
                ->first();
        }

        $date = Carbon::parse($request->input('date', optional($activePeriod)->end_date ?? now()))->toDateString();

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
     * Laporan Keluar Masuk Keuangan
     */
    public function cashReports(Request $request, School $school = null)
    {
        Log::info('Accessing Cash Reports', ['request' => $request->all()]);
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);

        $activePeriod = null;
        if ($school) {
            $activePeriod = FinancialPeriod::where('school_id', $school->id)
                ->where('is_active', true)
                ->first();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate && $activePeriod) {
            $startDate = $activePeriod->start_date->toDateString();
        }
        if (!$endDate && $activePeriod) {
            $endDate = $activePeriod->end_date->toDateString();
        }

        $accountType = $request->input('account');
        $isMasuk = $accountType === 'masuk';

        $transactions = Transaction::with('account')
            ->where('school_id', $school->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->when($isMasuk, function ($query) {
                return $query->where('credit', '>', 0);
            }, function ($query) {
                return $query->where('debit', '>', 0)
                             ->where('description', 'not like', '%piutang%');
            })
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $items = [
            'data' => [],
            'totals' => [
                'ppdb' => 0, 'dpp' => 0, 'spp' => 0, 'uks' => 0, 'uis' => 0, 'uig' => 0, 'uik' => 0,
                'unit_usaha' => 0, 'pemerintah' => 0, 'swasta' => 0, 'lain_lain' => 0, 'grand_total' => 0,
            ],
        ];

        foreach ($transactions->groupBy(fn($item) => $item->id) as $id => $transactionGroup) {
            $transaction = $transactionGroup->first();
            $amount = $isMasuk ? $transaction->credit : $transaction->debit;
            $category = 'lain_lain';

            if (str_contains($transaction->account->name, 'ppdb')) {
                $category = 'ppdb';
            } elseif (str_contains($transaction->account->name, 'dpp')) {
                $category = 'dpp';
            } elseif (str_contains($transaction->account->name, 'spp')) {
                $category = 'spp';
            } elseif (str_contains($transaction->account->name, 'uks') || str_contains($transaction->account->name, 'biaya') || str_contains($transaction->account->name, 'beli')) {
                $category = 'uks';
            } elseif (str_contains($transaction->account->name, 'uis')) {
                $category = 'uis';
            } elseif (str_contains($transaction->account->name, 'uig')) {
                $category = 'uig';
            } elseif (str_contains($transaction->account->name, 'uik')) {
                $category = 'uik';
            } elseif (str_contains($transaction->account->name, 'unit usaha')) {
                $category = 'unit_usaha';
            } elseif (str_contains($transaction->account->name, 'pemerintah') || $transaction->account->account_type === 'Aset Tetap') {
                $category = 'pemerintah';
            } elseif (str_contains($transaction->account->name, 'swasta')) {
                $category = 'swasta';
            }

            $item = [
                'no' => count($items['data']) + 1,
                'code' => $transaction->account->code,
                'date' => $transaction->date,
                'description' => $transaction->description,
                'doc_number' => $transaction->doc_number,
                'ppdb' => $category === 'ppdb' ? $amount : 0,
                'dpp' => $category === 'dpp' ? $amount : 0,
                'spp' => $category === 'spp' ? $amount : 0,
                'uks' => $category === 'uks' ? $amount : 0,
                'uis' => $category === 'uis' ? $amount : 0,
                'uig' => $category === 'uig' ? $amount : 0,
                'uik' => $category === 'uik' ? $amount : 0,
                'unit_usaha' => $category === 'unit_usaha' ? $amount : 0,
                'pemerintah' => $category === 'pemerintah' ? $amount : 0,
                'swasta' => $category === 'swasta' ? $amount : 0,
                'lain_lain' => $category === 'lain_lain' ? $amount : 0,
            ];
            $items['data'][] = $item;
            $items['totals'][$category] += $amount;
        }

        $items['totals']['grand_total'] = array_sum($items['totals']);

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportCashReports($items, $school, $accountType, $startDate, $endDate);
        }

        return view('reports.cash-reports', compact('items', 'school', 'schools', 'accountType', 'startDate', 'endDate'));
    }

    /**
     * Export Laporan Keluar Masuk Keuangan
     */
    protected function exportCashReports($items, $school, $account, $startDate, $endDate)
    {
        $headType = ucwords($account);
        $schoolName = $school ? Str::slug($school->name) : 'Semua_Sekolah';
        $fileName = "Laporan_Kas_{$headType}_{$startDate}_to_{$endDate}_{$schoolName}.xlsx";

        $item_data = $items['data'];
        $item_total = $items['totals'];

        try {
            return Excel::download(new class($item_data, $item_total, $schoolName, $school, $headType, $startDate, $endDate) implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents {
                protected $item_data;
                protected $item_total;
                protected $schoolName;
                protected $school;
                protected $headType;
                protected $startDate;
                protected $endDate;

                public function __construct($item_data, $item_total, $schoolName, $school, $headType, $startDate, $endDate)
                {
                    $this->item_data = $item_data;
                    $this->item_total = $item_total;
                    $this->schoolName = $schoolName;
                    $this->school = $school;
                    $this->headType = $headType;
                    $this->startDate = $startDate;
                    $this->endDate = $endDate;
                }

                public function collection()
                {
                    $data = collect();

                    // Header teks
                    $data->push(['Lampiran '.($this->headType=='Masuk'?2:3).'. Catatan Uang '.ucwords($this->headType)]);
                    $data->push(['Nama Sekolah : '.$this->school->name]);
                    $data->push(['Alamat Sekolah : '.$this->school->address]);
                    $data->push(['']);
                    $startDate = Carbon::parse($this->startDate)->format('d-m-Y');
                    $endDate = Carbon::parse($this->endDate)->format('d-m-Y');
                    $data->push(['CATATAN UANG '.strtoupper($this->headType).' ('.$startDate.' - '.$endDate.')']);
                    $data->push(['']);

                    // Header tabel
                    $data->push([
                        'NO', 'TGL', 'Uraian', 'No. Bukti', 'PPDB', 'DPP', 'SPP', 'UKS', 'UIS', 'UIG', 'UIK', 'UNIT USAHA', 'PEMERINTAH', 'SWASTA', 'LAIN-LAIN'
                    ]);

                    // Data item_data
                    foreach ($this->item_data as $item) {
                        $data->push([ $item['no'], Carbon::parse($item['date'])->format('d-m-Y'), $item['description'], $item['doc_number'], $item['ppdb'], $item['dpp'], $item['spp'], $item['uks'], $item['uis'], $item['uig'], $item['uik'], $item['unit_usaha'], $item['pemerintah'], $item['swasta'], $item['lain_lain'] ]);
                    }
                    $this->lastRow = $data->count();

                    // Footer
                    $data->push(['Jumlah Uang '.($this->headType), '', '', '', $this->item_total['ppdb'], $this->item_total['dpp'], $this->item_total['spp'], $this->item_total['uks'], $this->item_total['uis'], $this->item_total['uig'], $this->item_total['uik'], $this->item_total['unit_usaha'], $this->item_total['pemerintah'], $this->item_total['swasta'], $this->item_total['lain_lain']]);
                    $data->push(['','','','',$this->item_total['grand_total']]);

                    $data->push(['Nama Kota, '.date('d-m-Y')]);
                    $data->push(['']);
                    $data->push(['Mengetahui', '', '', '', '', '', '', '', '', '', '', '', '']);
                    $data->push(['Kepala Sekolah', '', '', '', '', '', '', '', '', '', '', '', 'Bendahara']);
                    $data->push(['']);
                    $data->push(['']);
                    $data->push(['']);
                    $data->push(['(..............................)', '', '', '', '', '', '', '', '', '', '', '', '(..............................)']);

                    return $data;
                }

                public function headings(): array
                {
                    return [];
                }

                public function title(): string
                {
                    return 'Laporan Kas';
                }

                public function styles(Worksheet $sheet)
                {
                    return [
                        1 => ['font' => ['bold' => true]], // Lampiran
                        5 => ['font' => ['bold' => true]], // CATATAN UANG KELUAR
                        7 => [
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                            'borders' => [
                                'outline' => ['borderStyle' => 'thin'],
                                'inside' => ['borderStyle' => 'thin'],
                            ]
                        ]
                    ];
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet;

                            // Merge cell untuk header
                            $sheet->mergeCells('A1:O1'); // Lampiran
                            $sheet->mergeCells('A2:O2'); // Nama Sekolah
                            $sheet->mergeCells('A3:O3'); // Alamat
                            $sheet->mergeCells('A5:O5'); // CATATAN UANG KELUAR

                            $sheet->getStyle('D7:O7')->getAlignment()->setTextRotation(90);

                            // Style untuk header
                            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                            $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
                            $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');

                            // Table range (mulai baris 7)
                            $lastRow = 2 + $this->lastRow; // jumlah baris
                            $tableRange = "A7:O{$lastRow}";

                            // Border tabel
                            $sheet->getStyle($tableRange)->applyFromArray([
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    ]
                                ]
                            ]);

                            // Align kolom NO, TGL, No. Bukti ke center
                            $sheet->getStyle("A8:A{$lastRow}")->getAlignment()->setHorizontal('center');
                            $sheet->getStyle("B8:B{$lastRow}")->getAlignment()->setHorizontal('center');
                            $sheet->getStyle("D8:D{$lastRow}")->getAlignment()->setHorizontal('center');

                            // Kolom width
                            $sheet->getColumnDimension('A')->setWidth(5);
                            $sheet->getColumnDimension('B')->setWidth(11);
                            $sheet->getColumnDimension('C')->setWidth(45);
                            $sheet->getColumnDimension('D')->setWidth(12);
                            foreach (range('E', 'N') as $col) {
                                $sheet->getColumnDimension($col)->setWidth(10);
                            }

                            // Format angka (E sampai N)
                            $sheet->getStyle("E8:O{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

                            // Footer style
                            $tblSummary = $lastRow - 1;
                            $footerStart = $lastRow + 1;

                            $sheet->mergeCells("A{$tblSummary}:D{$lastRow}");
                            $sheet->mergeCells("E{$lastRow}:O{$lastRow}");
                            $sheet->getStyle("E{$lastRow}")->getAlignment()->setHorizontal('center');

                            $sheet->mergeCells("A{$footerStart}:O{$footerStart}"); // Jumlah uang keluar
                            $sheet->getStyle("A{$tblSummary}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                            $sheet->getStyle("A{$footerStart}")->getAlignment()->setHorizontal('right');
                        }
                    ];
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export Cash Reports', ['error' => $e->getMessage()]);
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