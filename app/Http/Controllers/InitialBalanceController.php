<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\FinancialPeriod;
use App\Models\InitialBalance;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitialBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }

        $accounts = Account::where('school_id', $school->id)->with(['initialBalances' => function ($query) use ($financialPeriod) {
            $query->where('financial_period_id', $financialPeriod->id);
        }])->get();

        return view('initial-balances.index', compact('accounts', 'financialPeriod', 'school'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school, FinancialPeriod $financialPeriod)
    {
        if ($school->id !== $financialPeriod->school_id) {
            abort(403);
        }

        $accounts = Account::where('school_id', $school->id)->with(['initialBalances' => function ($query) use ($financialPeriod) {
            $query->where('financial_period_id', $financialPeriod->id);
        }])->get();

        return view('initial-balances.edit', compact('accounts', 'financialPeriod', 'school'));
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
            'balances' => 'required|array',
            'balances.*' => 'numeric|nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->balances as $accountId => $amount) {
                if (is_null($amount)) {
                    // Hapus saldo yang dikosongkan
                    InitialBalance::where('school_id', $school->id)
                        ->where('financial_period_id', $financialPeriod->id)
                        ->where('account_id', $accountId)
                        ->delete();
                } else {
                    InitialBalance::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'financial_period_id' => $financialPeriod->id,
                            'account_id' => $accountId,
                        ],
                        [
                            'amount' => $amount,
                        ]
                    );
                }
            }
            DB::commit();
            return redirect()->route('school-initial-balances.index', [$school, $financialPeriod])->with('success', 'Saldo awal berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan saldo awal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan saldo awal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}