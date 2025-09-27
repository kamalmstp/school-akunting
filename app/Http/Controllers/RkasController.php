<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\School;
use App\Models\FinancialPeriod;
use App\Models\CashManagement;
use App\Models\Transaction;

class RkasController extends Controller
{
    protected function getActivePeriod(School $school): ?FinancialPeriod
    {
        return FinancialPeriod::where('school_id', $school->id)
                                ->where('is_active', true)
                                ->first();
    }

    public static function getCashSourcesForMenu(School $school): array
    {
        // Panggil fungsi non-statis untuk mendapatkan periode aktif
        $instance = new static(); 
        $activePeriod = $instance->getActivePeriod($school);

        if (!$activePeriod) {
            return [];
        }
        
        // Mengambil semua nama akun kas yang unik (distinct name) 
        // untuk sekolah dan periode aktif.
        $sources = CashManagement::where('school_id', $school->id)
                                 ->where('financial_period_id', $activePeriod->id)
                                 ->distinct()
                                 ->pluck('name')
                                 ->toArray();
        
        return $sources;
    }

    public function global(Request $request, School $school = null)
    {
        Log::info('Accessing RKAS Global Report');

        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? $school->id : null;

        $activePeriod = $schoolId ? FinancialPeriod::where('school_id', $schoolId)->where('is_active', true)->first() : null;

        if (!$activePeriod) {
            return view('reports.rkas.global', [
                'school' => $school,
                'schools' => $schools,
                'rkasData' => [],
                'totalIncome' => 0,
                'totalExpense' => 0,
                'balance' => 0,
                'startDate' => null,
                'endDate' => null,
                'message' => 'Tidak ada periode keuangan aktif yang ditemukan.'
            ]);
        }
        
        $startDate = $request->input('start_date', $activePeriod->start_date);
        $endDate = $request->input('end_date', $activePeriod->end_date);

        $cashManagements = CashManagement::where('school_id', $schoolId)
            ->where('financial_period_id', $activePeriod->id)
            ->with('account')
            ->get();

        $rkasData = [];
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($cashManagements as $cashManagement) {
            $report = $this->getReportForCashManagement($cashManagement, $startDate, $endDate);
            $rkasData[] = $report;
            $totalIncome += $report['income'];
            $totalExpense += $report['expense'];
        }

        $balance = $totalIncome - $totalExpense;

        return view('reports.rkas.global', compact('school', 'schools', 'rkasData', 'totalIncome', 'totalExpense', 'balance', 'startDate', 'endDate', 'activePeriod'));
    }

    /**
     * Menampilkan laporan RKAS untuk sumber dana tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\School|null  $school
     * @param  string $source
     * @return \Illuminate\View\View
     */
    public function detail(Request $request, School $school, $source)
    {
        Log::info("Accessing RKAS Report for source: $source");

        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? $school->id : null;

        $activePeriod = $schoolId ? FinancialPeriod::where('school_id', $schoolId)->where('is_active', true)->first() : null;

        if (!$activePeriod) {
            return view('reports.rkas.detail', [
                'school' => $school,
                'schools' => $schools,
                'reportData' => ['items' => []],
                'source' => $source,
                'startDate' => null,
                'endDate' => null,
                'message' => 'Tidak ada periode keuangan aktif yang ditemukan.'
            ]);
        }

        $cashManagement = CashManagement::where('school_id', $schoolId)
            ->where('financial_period_id', $activePeriod->id)
            ->where('name', $source)
            ->with('account')
            ->firstOrFail();

        $startDate = $request->input('start_date', $activePeriod->start_date);
        $endDate = $request->input('end_date', $activePeriod->end_date);

        $reportData = $this->getReportForCashManagement($cashManagement, $startDate, $endDate);

        return view('reports.rkas.detail', compact('school', 'schools', 'reportData', 'source', 'startDate', 'endDate'));
    }

    /**
     * Resolve the school context based on user role and request.
     *
     * @param \App\Models\User $user
     * @param \App\Models\School|null $school
     * @return \App\Models\School|null
     */
    protected function resolveSchool($user, $school)
    {
        if ($user->role === 'SchoolAdmin' && !$school) {
            return $user->school;
        }

        if ($school) {
            return $school;
        }

        return School::first();
    }

    /**
     * Ambil data pendapatan dan pengeluaran untuk satu entri CashManagement.
     *
     * @param  \App\Models\CashManagement  $cashManagement
     * @param  string $startDate
     * @param  string $endDate
     * @return array
     */
    protected function getReportForCashManagement(CashManagement $cashManagement, $startDate, $endDate)
    {
        $transactions = Transaction::where('account_id', $cashManagement->account_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $income = $transactions->sum('debit');
        $expense = $transactions->sum('credit');

        $items = $transactions->map(function ($transaction) {
            return [
                'description' => $transaction->description,
                'type' => $transaction->credit > 0 ? 'Pendapatan' : 'Pengeluaran',
                'amount' => $transaction->credit > 0 ? $transaction->credit : $transaction->debit,
            ];
        })->toArray();
        
        $initialBalance = $cashManagement->initial_balance_amount;
        $currentBalance = $initialBalance + $income - $expense;

        return [
            'cashManagementId' => $cashManagement->id,
            'name' => $cashManagement->name,
            'initial_balance' => $initialBalance,
            'income' => $income,
            'expense' => $expense,
            'balance' => $currentBalance,
            'items' => $items,
        ];
    }
}
