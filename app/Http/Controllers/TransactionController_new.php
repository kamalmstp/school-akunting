<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\FundManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request, School $school)
    {
        // now()->format('YmdHi')
        $user = auth()->user();
        $account = $request->get('account');
        $startDate = is_null($request->get('start_date')) ? '' : $request->get('start_date');
        $endDate = is_null($request->get('end_date')) ? '' : $request->get('end_date');
        $accountType = $request->get('account_type');
        $singleAccount = Account::find($account);
        $referenceStudent = 'App\Models\StudentReceivables';
        $referenceTeacher = 'App\Models\TeacherReceivable';
        $referenceNull = NULL;
        if (auth()->user()->role != 'SchoolAdmin') {
            // SuperAdmin: Semua transaksi
            $schools = School::pluck('name', 'id');
            $schoolId = $request->get('school_id');
            $school = School::find($schoolId);
            $transactions = Transaction::with(['school', 'account'])
                ->when($schoolId, function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->when($account, function ($q) use ($account) {
                    $q->where('account_id', $account);
                })
                ->when($startDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')]);
                })
                ->whereHas('account', function($q) use ($accountType) {
                    $q->when($accountType, fn($q) => $q->where('account_type', $accountType));
                })
                ->orderBy('date', 'desc')
                ->paginate(10)->withQueryString();

            return view('transactions.index', compact('transactions', 'schools', 'school', 'account', 'startDate', 'endDate', 'accountType', 'singleAccount', 'schoolId'));
        }

        // SchoolAdmin atau SuperAdmin dengan sekolah tertentu
        $school = $school ?? $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }

        $transactions = Transaction::where('school_id', $school->id)
            ->with('account')
            ->when($account, function ($q) use ($account) {
                $q->where('account_id', $account);
            })
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [
                    Carbon::parse($startDate)->format('Y-m-d'),
                    Carbon::parse($endDate)->format('Y-m-d')
                ]);
            })
            ->where(function ($q) use ($referenceStudent, $referenceTeacher) {
                $q->whereNull('reference_type')
                ->orWhereNotIn('reference_type', [$referenceStudent, $referenceTeacher]);
            })
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

            // $transDesc = Transaction::where('school_id', $school->id)->pluck('description');
            // $transIds = [];
            // foreach ($transDesc->duplicates()->unique()->all() as $value) {
            //     $sameTrans = Transaction::where('description', $value);
            //     $totalDebit = $sameTrans->sum('debit');
            //     $totalCredit = $sameTrans->sum('credit');
            //     if ($totalDebit != $totalCredit) {
            //         $transIds[] = $sameTrans->pluck('id');
            //     }
            // }

        return view('transactions.index', compact('transactions', 'school', 'account', 'startDate', 'endDate', 'accountType', 'singleAccount'));
    }

    public function getAccountParent(Request $request)
    {
        $accounts = Account::where('account_type', $request->accountType)
            ->whereNotNull('parent_id')
            ->get();
        return response()->json($accounts, 200);
    }

    public function getFundSource(Request $request)
    {
        $funds = FundManagement::where('school_id', $request->school_id)
            ->get();
        return response()->json($funds, 200);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(School $school)
    {
        $user = auth()->user();

        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::where('school_id', $school->id)->get();
        return view('transactions.create', compact('school', 'accounts'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'account_id.*' => 'required',
            'doc_number' => 'required',
            'date' => 'required|date',
            'description' => 'required|string',
        ];

        $messages = [
            'account_id.*.required' => 'Pilih akun',
            'doc_number.required' => 'Nomor Dokumen wajib diisi',
            'date.required' => 'Tanggal transaksi wajib diisi'
        ];

        if (auth()->user()->role == 'SuperAdmin' && !isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih sekolah';
        }

        $request->validate($rules, $messages);

        $debit = [];
        $credit = [];
        foreach ($request->debit as $index => $value) {
            $debit[] = $value ? (float)str_replace('.', '', $value) : 0;
            $credit[] = $request->credit[$index] ? (float)str_replace('.', '', $request->credit[$index]) : 0;
        }
        $totalDebit = array_sum($debit);
        $totalCredit = array_sum($credit);

        if ($totalDebit != $totalCredit && is_null($request->type)) {
            return back()->withErrors(['balance' => 'Pastikan pemasukan dan pengeluaran seimbang']);
        }

        foreach ($request->account_id as $index => $account) {
            $total_debit = (float)str_replace('.', '', $request->debit[$index]) ?? 0;
            $total_credit = (float)str_replace('.', '', $request->credit[$index]) ?? 0;
            Transaction::create([
                'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
                'account_id' => $account,
                'fund_management_id' => $request->fund_management_id[$index],
                'doc_number' => $request->doc_number,
                'date' => $request->date,
                'description' => $request->description,
                'debit' => $total_debit,
                'credit' => $total_credit,
                'type' => $request->type == 'true' ? 'adjustment' : 'general'
            ]);
            FundManagement::where([
                ['school_id', '=', auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id],
                ['id', '=', $request->fund_management_id[$index]]
            ])->update(['amount' => DB::raw('amount - '.$total_credit),'updated_at' => now()]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('transactions.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-transactions.index', $school);
        }

        return $route->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(School $school, Transaction $transaction)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::where('school_id', $school->id)->get();
        $funds = FundManagement::where('school_id', $school->id)->get();
        $otherTrans = Transaction::where([
            ['description', '=', $transaction->description],
            ['id', '!=', $transaction->id]
        ])->get();
        return view('transactions.edit', compact('transaction', 'school', 'accounts', 'funds', 'otherTrans'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, School $school, Transaction $transaction)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'account_id' => 'required',
            'fund_management_id' => 'required',
            'doc_number' => 'required',
            'date' => 'required|date',
            'description' => 'required|string',
        ], [
            'account_id.required' => 'Pilih akun',
            'fund_management_id.required' => 'Pilih sumber dana',
            'doc_number.required' => 'Nomor Dokumen wajib diisi',
            'date.required' => 'Tanggal transaksi wajib diisi'
        ]);

        $debit = (float)str_replace('.', '', $request->debit) ?? 0;
        $credit = (float)str_replace('.', '', $request->credit) ?? 0;

        if (($transaction->credit  > 0 && $debit > 0) || ($transaction->debit > 0 && $credit > 0)) {
            return back()->withErrors(['balance' => 'Pastikan transaksi sesuai penginputan awal']);
        }

        if ($transaction->description != $request->description) {
            Transaction::where([
                ['description', '=', $transaction->decription],
                ['id', '!=', $transaction->id]
            ])->update(['description' => $request->description]);
        }

        $tes = $transaction->update([
            'account_id' => $request->account_id,
            'fund_management_id' => $request->fund_management_id,
            'doc_number' => $request->doc_number,
            'date' => $request->date,
            'description' => $request->description,
            'debit' => (float)str_replace('.', '', $request->debit) ?? 0,
            'credit' => (float)str_replace('.', '', $request->credit) ?? 0,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('transactions.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-transactions.index', $transaction->school);
        }

        return $route->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(School $school, Transaction $transaction)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::where([
            ['description', '=', $transaction->decription],
            ['id', '!=', $transaction->id]
        ])->update(['deleted_at' => now()]);

        $transaction->update(['deleted_at' => now()]);
        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('transactions.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-transactions.index', $school);
        }
        return $route->with('success', 'Transaksi berhasil dihapus.');
    }

    public function exportTransaction(Request $request)
    {
        Log::info('Exporting Transactions', ['request' => $request->all()]);
        $user = auth()->user();
        $schoolId = $request->input('school');
        $school = $schoolId && $schoolId !== 'semua' ? School::find($schoolId) : null;
        $accountType = $request->input('account_type');
        $accountId = $request->input('account');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Query transactions
        $query = Transaction::query()
            ->with(['school', 'account'])
            ->when($schoolId && $schoolId !== 'semua', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->when($schoolId !== 'semua' && !$schoolId && $user->role === 'SchoolAdmin', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            })
            ->when($accountType, function ($q) use ($accountType) {
                $q->whereHas('account', function ($q) use ($accountType) {
                    $q->where('name', $accountType)->whereNull('parent_id');
                });
            })
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('account_id', $accountId);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('date', '<=', $endDate);
            });

        $transactions = $query->orderBy('date', 'desc')->get();

        $fileName = 'Transaksi_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . ($school ? '_' . str_replace(' ', '_', $school->name) : '') . '.xlsx';

        try {
            return Excel::download(new class($transactions, $school) implements FromCollection, WithHeadings, WithTitle {
                protected $transactions;
                protected $school;

                public function __construct($transactions, $school)
                {
                    $this->transactions = $transactions;
                    $this->school = $school;
                }

                public function collection()
                {
                    $data = collect();
                    foreach ($this->transactions as $index => $transaction) {
                        $data->push([
                            $index + 1,
                            Carbon::parse($transaction->date)->format('d-m-Y'),
                            $transaction->school->name,
                            $transaction->account->code,
                            $transaction->account->name,
                            $transaction->description ?? '-',
                            $transaction->debit,
                            $transaction->credit,
                        ]);
                    }
                    return $data;
                }

                public function headings(): array
                {
                    return ['No', 'Tanggal', 'Sekolah', 'Kode Akun', 'Nama Akun', 'Deskripsi', 'Pemasukan', 'Pengeluaran'];
                }

                public function title(): string
                {
                    return 'Transaksi';
                }
            }, $fileName);
        } catch (\Exception $e) {
            Log::error('Failed to export transactions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor ke Excel: ' . $e->getMessage());
        }
    }
}