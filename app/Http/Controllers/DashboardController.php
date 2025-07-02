<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Account;
use App\Models\StudentReceivables;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access')->only('index');
    }

    /**
     * Display the dashboard.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->startOfMonth()->startOfDay();

        if ($user->role != 'SchoolAdmin') {
            // Konsolidasi semua sekolah
            $schools = School::all();
            $schoolId = $request->get('school');

            // Total Aset
            $totalAssets = [];
            $accountAssets = Account::whereIn('account_type', ['Aset Lancar', 'Aset Tetap'])->select('id', 'normal_balance')->get();
            foreach ($accountAssets as $account) {
                $balance = Transaction::where('account_id', $account->id)
                    ->where('date', '<=', $endDate);
                if ($schoolId && $schoolId != 'semua') {
                    $balance = $balance->where('school_id', $schoolId);
                }
                $balance = $balance->selectRaw('SUM(debit - credit) as balance')->first()->balance ?? 0;
                if ($account->normal_balance === 'Kredit') {
                    $balance = -$balance;
                }
                $totalAssets[] = $balance;
            }
            $totalAssets = array_sum($totalAssets);

            // Laba Bersih
            $revenue = Transaction::whereIn('account_id', Account::where('account_type', 'Pendapatan')->pluck('id'))
                ->whereBetween('date', [$startDate, $endDate]);
            if ($schoolId && $schoolId != 'semua') {
                $revenue = $revenue->where('school_id', $schoolId);
            }
            $revenue = $revenue->selectRaw('SUM(credit - debit) as balance')->first()->balance ?? 0;
            $expense = Transaction::whereIn('account_id', Account::where('account_type', 'Biaya')->pluck('id'))
                ->whereBetween('date', [$startDate, $endDate]);
            if ($schoolId && $schoolId != 'semua') {
                $expense = $expense->where('school_id', $schoolId);
            }
            $expense = $expense->selectRaw('SUM(credit - debit) as balance')->first()->balance ?? 0;
            $netIncome = $revenue - abs($expense);

            // Arus Kas Bersih
            $cashAccounts = Account::whereIn('account_type', ['Aset Lancar'])->where('code', 'like', '1-11%')->pluck('id');
            $cashFlow = Transaction::whereIn('account_id', $cashAccounts)
                ->whereBetween('date', [$startDate, $endDate]);                
            if ($schoolId && $schoolId != 'semua') {
                $cashFlow = $cashFlow->where('school_id', $schoolId);
            }
            $cashFlow = $cashFlow->selectRaw('SUM(credit - debit) as balance')->first()->balance ?? 0;

            // Data untuk Grafik (Pendapatan vs Biaya per Bulan)
            $chartData = $this->getChartData($startDate->copy()->subMonths(5), $endDate, $schoolId);
            $chartStudentData = $this->getStudentChartData($startDate->copy()->subMonth(5), $endDate, $schoolId);

            return view('dashboard.index', compact('schoolId', 'schools', 'totalAssets', 'netIncome', 'cashFlow', 'chartData', 'chartStudentData', 'startDate', 'endDate'));
        }

        // SchoolAdmin atau SuperAdmin untuk sekolah tertentu
        $school = $school ?? $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }

        // Total Aset
        $totalAssets = [];
        $accountAssets = Account::whereIn('account_type', ['Aset Lancar', 'Aset Tetap'])->select('id', 'normal_balance')->get();
        foreach ($accountAssets as $account) {
            $balance = Transaction::where('account_id', $account->id)
                ->where('date', '<=', $endDate)
                ->where('school_id', $school->id)
                ->selectRaw('SUM(debit - credit) as balance')->first()->balance ?? 0;
            if ($account->normal_balance === 'Kredit') {
                $balance = -$balance;
            }
            $totalAssets[] = $balance;
        }
        $totalAssets = array_sum($totalAssets);

        // Laba Bersih
        $revenue = Transaction::whereIn('account_id', Account::where('account_type', 'Pendapatan')->pluck('id'))
            ->where('school_id', $school->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(credit - debit) as balance')
            ->first()->balance ?? 0;
        $expense = Transaction::whereIn('account_id', Account::where('account_type', 'Biaya')->pluck('id'))
            ->where('school_id', $school->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(debit - credit) as balance')
            ->first()->balance ?? 0;
        $netIncome = $revenue - $expense;

        // Arus Kas Bersih
        $cashAccounts = Account::whereIn('account_type', ['Aset Lancar'])->where('code', 'like', '1-11%')->pluck('id');
        $cashFlow = Transaction::whereIn('account_id', $cashAccounts)
            ->where('school_id', $school->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(debit - credit) as balance')
            ->first()->balance ?? 0;

        // Data untuk Grafik
        $chartData = $this->getChartData($startDate->copy()->subMonths(5), $endDate, $school->id);
        $chartStudentData = $this->getStudentChartData($startDate->copy()->subMonth(5), $endDate, $school->id);

        return view('dashboard.index', compact('school', 'totalAssets', 'netIncome', 'cashFlow', 'chartData', 'chartStudentData', 'startDate', 'endDate'));
    }

    /**
     * Get chart data for revenue and expenses.
     */
    protected function getChartData($startDate, $endDate, $schoolId = null)
    {
        $labels = [];
        $revenues = [];
        $expenses = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            $labels[] = $monthStart->format('M Y');

            $revenueQuery = Transaction::whereIn('account_id', Account::where('account_type', 'Pendapatan')->pluck('id'))
                ->whereBetween('date', [$monthStart, $monthEnd]);
            $expenseQuery = Transaction::whereIn('account_id', Account::where('account_type', 'Biaya')->pluck('id'))
                ->whereBetween('date', [$monthStart, $monthEnd]);

            if ($schoolId && $schoolId != 'semua') {
                $revenueQuery->where('school_id', $schoolId);
                $expenseQuery->where('school_id', $schoolId);
            }

            $revenues[] = $revenueQuery->selectRaw('SUM(credit - debit) as balance')->first()->balance ?? 0;
            $expenses[] = $expenseQuery->selectRaw('SUM(debit - credit) as balance')->first()->balance ?? 0;

            $currentDate->addMonth();
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    /**
     * Get chart data for student receivables.
     */
    protected function getStudentChartData($startDate, $endDate, $schoolId = null)
    {
        $labels = [];
        $receivables = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            $labels[] = $monthStart->format('M Y');

            $receivableQuery = StudentReceivables::whereIn('status', ['Unpaid', 'Partial'])
                ->whereBetween('updated_at', [$monthStart, $monthEnd]);

            if ($schoolId && $schoolId != 'semua') {
                $receivableQuery->where('school_id', $schoolId);
            }

            $receivables[] = $receivableQuery->selectRaw('SUM(amount - paid_amount) as balance')->first()->balance ?? 0;

            $currentDate->addMonth();
        }

        return [
            'labels' => $labels,
            'receivables' => $receivables,
        ];
    }
}