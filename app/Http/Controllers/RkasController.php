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

    public function global(Request $request, School $school = null)
    {
        $type = $request->input('type', 'view');

        Log::info('Accessing RKAS Global Report');

        $user = auth()->user();

        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor', 'Pengawas']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? [$school->id] : $schools->pluck('id');

        $activePeriod = $schoolId ? FinancialPeriod::where('school_id', $schoolId)->where('is_active', true)->first() : null;

        if (!$activePeriod) {

            $data = [
                'school' => $school,
                'schools' => $schools,
                'rkasData' => [],
                'totalIncome' => 0,
                'totalExpense' => 0,
                'balance' => 0,
                'startDate' => null,
                'endDate' => null,
            ];

            $data['message'] = 'Tidak ada periode keuangan aktif yang ditemukan.';
            return view('reports.rkas.global', $data);
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

        $data = compact('school', 'schools', 'rkasData', 'totalIncome', 'totalExpense', 'balance', 'startDate', 'endDate', 'activePeriod');

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('reports.rkas.pdf.global', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = "RKAS-Global-" . Str::slug($school->name ?? 'Sekolah') . "-" . date('Ymd') . ".pdf";
            return $pdf->download($filename);
        }

        return view('reports.rkas.global', $data);
    }

    public function printGlobalPdf(Request $request, School $school = null)
    {
        $user = auth()->user();
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor', 'Pengawas']) ? School::all() : collect([$user->school]);
        $schoolId = $school ? [$school->id] : $schools->pluck('id');

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

    public function detail(Request $request, School $school, CashManagement $cashManagement)
    {
        $type = $request->input('type', 'view');
        Log::info("Accessing RKAS Detail Report for CashManagement ID: {$cashManagement->id}");

        $activePeriod = FinancialPeriod::where('id', $cashManagement->financial_period_id)->first();
        $school_data = School::where('id', $cashManagement->school_id)->first();

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
            'school_data',
            'activePeriod',
            'cashManagement',
            'title',
            'initialBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'finalBalance'
        );

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('reports.rkas.pdf.detail', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = "RKAS-Detail-" . Str::slug($cashManagement->name) . "-" . date('Ymd') . ".pdf";
            return $pdf->download($filename);
        }

        return view('reports.rkas.detail', $data);
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

    protected function getReportForCashManagement(CashManagement $cashManagement, $startDate, $endDate)
    {
        $transactions = Transaction::where('account_id', $cashManagement->account_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $income = $transactions->sum('debit');
        $expense = $transactions->sum('credit');

        $items = $transactions->map(function ($transaction) {
            return [
                'date' => $transaction->date,
                'description' => $transaction->description,
                'debit' => $transaction->debit,
                'credit' => $transaction->credit,
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
