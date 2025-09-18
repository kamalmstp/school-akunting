<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Account;
use App\Models\StudentReceivables;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request, School $school = null)
    {
        $user = auth()->user();
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->startOfMonth()->startOfDay();

        // menentukan id sekolah dari parameter
        $schoolId = $user->role !== 'SchoolAdmin'
            ? $request->get('school')
            : ($school?->id ?? $user->school_id);

        // membatasi akses 
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $schoolId) {
            abort(403, 'Unauthorized access to this school.');
        }

        // Ambil semua data sekolah jika role != SchoolAdmin
        $schools = $user->role !== 'SchoolAdmin' ? School::all() : null;

        // ==== Hitung Total Aset ====
        $assetAccounts = Account::whereIn('account_type', ['Aset Lancar', 'Aset Tetap'])
            ->get(['id', 'normal_balance']);

        $balances = Transaction::selectRaw('account_id, SUM(debit - credit) as balance')
            ->when($schoolId && $schoolId !== 'semua', fn($q) => $q->where('school_id', $schoolId))
            ->where('date', '<=', $endDate)
            ->whereIn('account_id', $assetAccounts->pluck('id'))
            ->groupBy('account_id')
            ->pluck('balance', 'account_id');

        $totalAssets = $assetAccounts->sum(function ($account) use ($balances) {
            $balance = $balances[$account->id] ?? 0;
            return $account->normal_balance === 'Kredit' ? -$balance : $balance;
        });

        // laba bersih
        $revenueIds = Account::where('account_type', 'Pendapatan')->pluck('id');
        $expenseIds = Account::where('account_type', 'Biaya')->pluck('id');

        $revenue = Transaction::whereIn('account_id', $revenueIds)
            ->when($schoolId && $schoolId !== 'semua', fn($q) => $q->where('school_id', $schoolId))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(credit - debit) as balance')
            ->value('balance') ?? 0;

        $expense = Transaction::whereIn('account_id', $expenseIds)
            ->when($schoolId && $schoolId !== 'semua', fn($q) => $q->where('school_id', $schoolId))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance') ?? 0;

        $netIncome = $revenue - $expense;

        // arus kas bersih
        $cashAccounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-11%')
            ->pluck('id');

        $cashFlow = Transaction::whereIn('account_id', $cashAccounts)
            ->when($schoolId && $schoolId !== 'semua', fn($q) => $q->where('school_id', $schoolId))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance') ?? 0;

        // grafik
        $chartData = $this->getChartData($startDate->copy()->subMonths(5), $endDate, $schoolId);
        $chartStudentData = $this->getStudentChartData($startDate->copy()->subMonths(5), $endDate, $schoolId);

        return view('dashboard.index', compact(
            'schoolId',
            'school',
            'schools',
            'totalAssets',
            'netIncome',
            'cashFlow',
            'chartData',
            'chartStudentData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get chart data for revenue and expenses.
     */
    protected function getChartData($startDate, $endDate, $schoolId = null)
{
    $revenueIds = Account::where('account_type', 'Pendapatan')->pluck('id');
    $expenseIds = Account::where('account_type', 'Biaya')->pluck('id');

    $rows = Transaction::selectRaw("
            YEAR(date) as year, MONTH(date) as month,
            SUM(IF(account_id IN (" . $revenueIds->implode(',') . "), credit - debit, 0)) as revenue,
            SUM(IF(account_id IN (" . $expenseIds->implode(',') . "), debit - credit, 0)) as expense
        ")
        ->when($schoolId && $schoolId !== 'semua', fn($query) => $query->where('school_id', $schoolId))
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
        ->orderBy(DB::raw('YEAR(date), MONTH(date)'))
        ->get();

    $labels = [];
    $revenues = [];
    $expenses = [];

    $currentDate = $startDate->copy()->startOfMonth();
    while ($currentDate <= $endDate) {
        $labels[] = $currentDate->format('M Y');
        $row = $rows->first(fn($r) => $r->year == $currentDate->year && $r->month == $currentDate->month);

        $revenues[] = $row->revenue ?? 0;
        $expenses[] = $row->expense ?? 0;

        $currentDate->addMonth();
    }

    return compact('labels', 'revenues', 'expenses');
}


    /**
     * Get chart data for student receivables.
     */
    protected function getStudentChartData($startDate, $endDate, $schoolId = null)
{
    $rows = StudentReceivables::selectRaw("
            YEAR(updated_at) as year, MONTH(updated_at) as month,
            SUM(amount - paid_amount) as balance
        ")
        ->whereIn('status', ['Unpaid', 'Partial'])
        ->when($schoolId && $schoolId !== 'semua', fn($query) => $query->where('school_id', $schoolId))
        ->whereBetween('updated_at', [$startDate, $endDate])
        ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
        ->orderBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
        ->get();

    // mapping tahun dan bulan
    $map = [];
    foreach ($rows as $row) {
        $map[$row->year][$row->month] = $row->balance;
    }

    $labels = [];
    $receivables = [];

    $currentDate = $startDate->copy()->startOfMonth();
    while ($currentDate <= $endDate) {
        $labels[] = $currentDate->format('M Y');
        $balance = $map[$currentDate->year][$currentDate->month] ?? 0;
        $receivables[] = $balance;
        $currentDate->addMonth();
    }

    return compact('labels', 'receivables');
}

}
