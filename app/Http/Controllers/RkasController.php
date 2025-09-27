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

    protected function resolveSchool($user, ?School $school): ?School
    {
        if (isset($school)) {
            return $school;
        }

        if ($user && $user->school_id) {
            $school = School::find($user->school_id);
        }

        if ($school) {
            return $school;
        }

        return School::first();
    }

    protected function getActivePeriod(School $school): ?FinancialPeriod
    {
        return FinancialPeriod::where('school_id', $school->id)
                                ->where('is_active', true)
                                ->first();
    }

    public static function getCashSourcesForMenu(School $school): array
    {
        $instance = new static(); 
        $activePeriod = $instance->getActivePeriod($school);

        if (!$activePeriod) {
            return [];
        }
        
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
        $activePeriod = $this->getActivePeriod($school);

        if (!$activePeriod) {
            return view('rkas.global')->with('error', 'Tidak ada periode finansial aktif.');
        }

        $cashManagements = CashManagement::with('account')
            ->where('school_id', $school->id)
            ->where('financial_period_id', $activePeriod->id)
            ->get();
            
        $reportData = $cashManagements->map(function ($cashManagement) {
            return [
                'name' => $cashManagement->name,
                'initial_balance' => $cashManagement->initial_balance_amount, 
                'balance' => $cashManagement->balance, 
                'account_name' => $cashManagement->account->name ?? 'N/A',
            ];
        })->values()->groupBy('name');
        
        $data = [
            'school' => $school,
            'period' => $activePeriod,
            'reports' => $reportData,
            'cashManagements' => $cashManagements, 
        ];

        return view('reports.rkas.global', $data);
    }
    
    public function detail(Request $request, School $school, CashManagement $cashManagement)
    {
        $activePeriod = $this->getActivePeriod($school);

        if (!$activePeriod || 
            $cashManagement->financial_period_id !== $activePeriod->id ||
            $cashManagement->school_id !== $school->id) 
        {
             abort(403, 'Akses ditolak atau data tidak ditemukan dalam periode aktif.');
        }
        
        $accountId = $cashManagement->account_id;
        
        $transactions = Transaction::with(['account', 'budget']) 
            ->where('account_id', $accountId)
            ->where('financial_period_id', $activePeriod->id)
            ->orderBy('date', 'asc') 
            ->get();

        $initialBalance = $cashManagement->initial_balance_amount; 
        $currentBalance = $cashManagement->balance;

        $data = [
            'school' => $school,
            'period' => $activePeriod,
            'sourceName' => $cashManagement->name,
            'cashManagement' => $cashManagement,
            'transactions' => $transactions,
            'initialBalance' => $initialBalance,
            'currentBalance' => $currentBalance,
            'title' => "Laporan RKAS Detail: {$cashManagement->name} ({$cashManagement->account->name})",
        ];
    
        return view('reports.rkas.detail', $data);
    }
    
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
            'name' => $cashManagement->name,
            'initial_balance' => $initialBalance,
            'income' => $income,
            'expense' => $expense,
            'balance' => $currentBalance,
            'items' => $items,
        ];
    }
}
