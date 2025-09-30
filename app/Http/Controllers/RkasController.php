<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\School;
use App\Models\FinancialPeriod;
use App\Models\CashManagement;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
        // Baris ini menangani AdminMonitor/SuperAdmin atau SchoolAdmin
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? $school->id : null;
        $activePeriod = $school ? $this->getActivePeriod($school) : null;

        $rkasData = [];
        $totalIncome = 0;
        $totalExpense = 0;

        if ($activePeriod) {
            $startDate = $request->input('start_date', $activePeriod->start_date);
            $endDate = $request->input('end_date', $activePeriod->end_date);

            $cashManagements = CashManagement::where('school_id', $schoolId)
                ->where('financial_period_id', $activePeriod->id)
                ->with('account')
                ->get();

            foreach ($cashManagements as $cashManagement) {
                $report = $this->getReportForCashManagement($cashManagement, $startDate, $endDate);
                $rkasData[] = $report;
                $totalIncome += $report['income'];
                $totalExpense += $report['expense'];
            }
        }

        $balance = $totalIncome - $totalExpense;
        
        return view('reports.rkas.global', compact(
            'school',
            'schools',
            'rkasData',
            'totalIncome',
            'totalExpense',
            'balance',
            'activePeriod'
        ));
    }

    public function detail(Request $request, School $school, CashManagement $cashManagement)
    {
        Log::info("Accessing RKAS Detail Report for CashManagement ID: {$cashManagement->id}");

        $activePeriod = $this->getActivePeriod($school);

        if (!$activePeriod || $cashManagement->financial_period_id !== $activePeriod->id) {
            return redirect()->route('school-rkas.global', $school)->with('error', 'Periode keuangan tidak aktif atau tidak cocok.');
        }

        $report = $this->getReportForCashManagement(
            $cashManagement, 
            $activePeriod->start_date, 
            $activePeriod->end_date
        );
        
        $title = "Laporan Jurnal Kas: " . $cashManagement->name;
        $initialBalance = $report['initial_balance'];
        $transactions = collect($report['items']);
        $totalDebit = $report['income'];
        $totalCredit = $report['expense'];
        $finalBalance = $report['balance'];


        return view('reports.rkas.detail', compact(
            'school', 
            'activePeriod',
            'title',
            'initialBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'finalBalance'
        ));
    }

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

    protected function getReportForCashManagement_old(CashManagement $cashManagement, $startDate, $endDate)
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

    protected function getReportForCashManagement(CashManagement $cashManagement, $startDate, $endDate): array
    {
        $transactions = Transaction::where('cash_management_id', $cashManagement->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['account', 'subAccount'])
            ->orderBy('date')
            ->get();
        
        $initialBalance = Transaction::where('cash_management_id', $cashManagement->id)
            ->where('date', '<', $startDate)
            ->sum('debit') - Transaction::where('cash_management_id', $cashManagement->id)
            ->where('date', '<', $startDate)
            ->sum('credit');

        $income = $transactions->sum('debit');
        $expense = $transactions->sum('credit');
        $finalBalance = $initialBalance + $income - $expense;

        $items = $transactions->map(function ($transaction) {
            return [
                'date' => $transaction->date,
                'description' => $transaction->description,
                'code' => $transaction->subAccount ? $transaction->subAccount->code : 'N/A',
                'account_name' => $transaction->account->name,
                'debit' => $transaction->debit,
                'credit' => $transaction->credit,
            ];
        })->toArray();

        return [
            'cashManagementId' => $cashManagement->id,
            'name' => $cashManagement->name,
            'account' => $cashManagement->account->name,
            'initial_balance' => $initialBalance,
            'income' => $income,
            'expense' => $expense,
            'balance' => $finalBalance,
            'items' => $items,
        ];
    }

    public function printGlobalPdf(Request $request, School $school = null)
    {
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schoolId = $school ? $school->id : null;

        $activePeriod = $schoolId ? FinancialPeriod::where('school_id', $schoolId)->where('is_active', true)->first() : null;

        $rkasData = [];
        $totalIncome = 0;
        $totalExpense = 0;
        $balance = 0;
        
        if ($activePeriod) {
            $startDate = $activePeriod->start_date;
            $endDate = $activePeriod->end_date;

            $cashManagements = CashManagement::where('school_id', $schoolId)
                ->where('financial_period_id', $activePeriod->id)
                ->with('account')
                ->get();

            foreach ($cashManagements as $cashManagement) {
                $report = $this->getReportForCashManagement($cashManagement, $startDate, $endDate);
                $rkasData[] = $report;
                $totalIncome += $report['income'];
                $totalExpense += $report['expense'];
            }

            $balance = $totalIncome - $totalExpense;
        }
        
        $data = compact('school', 'rkasData', 'totalIncome', 'totalExpense', 'balance', 'activePeriod');

        $pdf = Pdf::loadView('reports.rkas.pdf.global', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = "RKAS-Global-" . Str::slug($school->name ?? 'Sekolah') . "-" . date('Ymd') . ".pdf";
        return $pdf->download($filename);
    }

    public function printDetailPdf(School $school, CashManagement $cashManagement)
    {
        $activePeriod = $this->getActivePeriod($school);

        if (!$activePeriod || $cashManagement->financial_period_id !== $activePeriod->id) {
             return redirect()->back()->with('error', 'Periode keuangan tidak aktif atau tidak cocok.');
        }

        $report = $this->getReportForCashManagement(
            $cashManagement, 
            $activePeriod->start_date, 
            $activePeriod->end_date
        );
        
        $title = "Laporan Jurnal Kas: " . $cashManagement->name;
        $initialBalance = $report['initial_balance'];
        $transactions = collect($report['items']); 
        $totalDebit = $report['income'];
        $totalCredit = $report['expense'];
        $finalBalance = $report['balance'];

        $data = compact(
            'school', 
            'activePeriod',
            'title',
            'initialBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'finalBalance'
        );

        $pdf = Pdf::loadView('reports.rkas.pdf.detail', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = "RKAS-Detail-" . Str::slug($cashManagement->name) . "-" . date('Ymd') . ".pdf";
        return $pdf->download($filename);
    }
}
