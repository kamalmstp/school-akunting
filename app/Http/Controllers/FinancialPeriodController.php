<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialPeriod;
use App\Models\InitialBalance;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(School $school)
    {
        $periods = FinancialPeriod::where('school_id', $school->id)->orderBy('start_date', 'desc')->get();
        return view('financial-periods.index', compact('periods', 'school'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(School $school)
    {
        return view('financial-periods.create', compact('school'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();
        try {
            if ($request->is_active) {
                FinancialPeriod::where('school_id', $school->id)->update(['is_active' => false]);
            }

            $period = FinancialPeriod::create([
                'school_id' => $school->id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->is_active ?? false,
            ]);

            DB::commit();
            return redirect()->route('school-financial-periods.index', $school)->with('success', 'Periode keuangan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat periode: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat periode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }
        return view('financial-periods.edit', compact('financialPeriod', 'school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();
        try {
            if ($request->is_active) {
                FinancialPeriod::where('school_id', $school->id)->update(['is_active' => false]);
            }

            $financialPeriod->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->is_active ?? false,
            ]);

            DB::commit();
            return redirect()->route('school-financial-periods.index', $school)->with('success', 'Periode keuangan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui periode: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui periode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }
        
        // Cek apakah ada saldo awal atau transaksi yang terhubung
        if ($financialPeriod->initialBalances()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus periode yang memiliki saldo awal.');
        }

        DB::beginTransaction();
        try {
            $financialPeriod->delete();
            DB::commit();
            return redirect()->route('school-financial-periods.index', $school)->with('success', 'Periode keuangan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus periode: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus periode: ' . $e->getMessage());
        }
    }

    public function copyBalances(School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }

        $previousPeriod = FinancialPeriod::where('school_id', $school->id)
            ->where('end_date', '<', $financialPeriod->start_date)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$previousPeriod) {
            return back()->with('error', 'Tidak ditemukan periode sebelumnya untuk disalin.');
        }

        DB::beginTransaction();
        try {
            // Hapus saldo awal yang sudah ada untuk periode ini
            InitialBalance::where('school_id', $school->id)
                ->where('financial_period_id', $financialPeriod->id)
                ->delete();

            // Dapatkan saldo akhir dari periode sebelumnya
            $endBalances = Transaction::where('school_id', $school->id)
                ->whereBetween('date', [$previousPeriod->start_date, $previousPeriod->end_date])
                ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
                ->groupBy('account_id')
                ->get();

            $newInitialBalances = [];
            foreach ($endBalances as $balance) {
                $finalBalance = $balance->total_debit - $balance->total_credit;
                // Hanya salin saldo yang tidak nol
                if ($finalBalance != 0) {
                    $newInitialBalances[] = [
                        'school_id' => $school->id,
                        'account_id' => $balance->account_id,
                        'financial_period_id' => $financialPeriod->id,
                        'amount' => $finalBalance,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            InitialBalance::insert($newInitialBalances);

            DB::commit();
            return back()->with('success', 'Saldo awal berhasil disalin dari periode sebelumnya.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyalin saldo awal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyalin saldo awal: ' . $e->getMessage());
        }
    }
}
