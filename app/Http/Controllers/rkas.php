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
                // Check if a school filter ID is provided in the query string
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
        // Mendapatkan parameter 'type' dari URL (cth: .../global?type=pdf)
        $type = $request->input('type', 'view'); // Default ke 'view'

        Log::info("Accessing RKAS Global Report as $type");

        $user = auth()->user();

        // 1. Resolve school
        $school = $this->resolveSchool($user, $school);
        $schools = in_array($user->role, ['SuperAdmin', 'AdminMonitor']) ? School::all() : collect([$user->school]);
        
        // Tentukan ID sekolah yang digunakan (Array of IDs)
        $schoolIds = $school ? [$school->id] : $schools->pluck('id')->toArray();
        
        // MENDAPATKAN SEMUA PERIODE AKTIF YANG RELEVAN
        $activePeriods = FinancialPeriod::whereIn('school_id', $schoolIds)
                                      ->where('is_active', true)
                                      ->get();

        // Gunakan periode aktif pertama yang ditemukan untuk konsistensi default tanggal
        $firstActivePeriod = $activePeriods->first();

        // --------------------------------------------------------------------------------
        // INI ADALAH KASUS DIMANA TIDAK ADA PERIODE AKTIF SAMA SEKALI
        // --------------------------------------------------------------------------------
        if (!$firstActivePeriod) {
            $data = [
                'school' => $school,
                'schools' => $schools,
                'rkasData' => [],
                'totalIncome' => 0,
                'totalExpense' => 0,
                'balance' => 0,
                'startDate' => null,
                'endDate' => null,
                'activePeriod' => null,
            ];

            if ($type === 'pdf') {
                 // Jika tidak ada data aktif, kembalikan PDF kosong atau error
                $pdf = Pdf::loadView('reports.rkas.pdf.global', $data);
                $pdf->setPaper('a4', 'landscape');
                $filename = "RKAS-Global-Kosong-" . date('Ymd') . ".pdf";
                return $pdf->download($filename);
            }
            
            $data['message'] = 'Tidak ada periode keuangan aktif yang ditemukan.';
            return view('reports.rkas.global', $data);
        }
        
        // --------------------------------------------------------------------------------
        // LOGIKA PENGUMPULAN DATA RKAS
        // --------------------------------------------------------------------------------
        $startDate = $request->input('start_date', $firstActivePeriod->start_date);
        $endDate = $request->input('end_date', $firstActivePeriod->end_date);
        
        // Ambil ID dari semua periode aktif yang ditemukan
        $activePeriodIds = $activePeriods->pluck('id')->toArray();

        // Mengambil CashManagement yang relevan
        $cashManagements = CashManagement::whereIn('school_id', $schoolIds)
            ->whereIn('financial_period_id', $activePeriodIds)
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
        
        // Compact semua variabel yang dibutuhkan
        $activePeriod = $firstActivePeriod; 
        $data = compact('school', 'schools', 'rkasData', 'totalIncome', 'totalExpense', 'balance', 'startDate', 'endDate', 'activePeriod');

        // --------------------------------------------------------------------------------
        // KEMBALIKAN OUTPUT (VIEW atau PDF)
        // --------------------------------------------------------------------------------
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('reports.rkas.pdf.global', $data);
            $pdf->setPaper('a4', 'landscape');

            // Filename menggunakan nama sekolah yang difilter, atau 'Semua-Sekolah'
            $filename = "RKAS-Global-" . Str::slug($school->name ?? 'Semua-Sekolah') . "-" . date('Ymd') . ".pdf";
            return $pdf->download($filename);
        }

        return view('reports.rkas.global', $data);
    }

    // MEMODIFIKASI METHOD DETAIL UNTUK MENGHANDLE VIEW DAN PDF
    public function detail(Request $request, School $school, CashManagement $cashManagement)
    {
        $type = $request->input('type', 'view'); // Default ke 'view'
        Log::info("Accessing RKAS Detail Report for CashManagement ID: {$cashManagement->id} as $type");

        $user = auth()->user();
        // 1. Resolve school untuk otorisasi
        $school = $this->resolveSchool($user, $school); 

        // Ambil periode keuangan terkait (bukan hanya yang aktif)
        $activePeriod = FinancialPeriod::where('id', $cashManagement->financial_period_id)->first();

        if (!$activePeriod) {
             return redirect()->back()->with('error', 'Periode keuangan yang terkait dengan Cash Management ini tidak ditemukan.');
        }

        // 2. Kumpulkan data laporan
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

        // 3. Output berdasarkan tipe
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('reports.rkas.pdf.detail', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = "RKAS-Detail-" . Str::slug($cashManagement->name) . "-" . date('Ymd') . ".pdf";
            return $pdf->download($filename);
        }

        // Default: Kembali ke view
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

    // METODE printDetailPdf DIHAPUS KARENA SUDAH DIGABUNGKAN KE DETAIL
}
