<?php

namespace App\Http\Controllers;

use App\Models\CashManagement;
use App\Models\School;
use App\Models\Account;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    /**
     * Tampilkan daftar kas.
     */
    public function index(School $school)
    {
        $cashes = CashManagement::where('school_id', $school->id)
            ->with(['account', 'financialPeriod'])
            ->get();

        return view('cash-management.index', compact('school', 'cashes'));
    }

    /**
     * Form tambah kas.
     */
    public function create(School $school)
    {
        $accounts = Account::where('school_id', $school->id)->get();

        $periods = FinancialPeriod::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        return view('cash-management.create', compact('school', 'accounts', 'periods'));
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request, School $school)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'account_id'          => 'required|exists:accounts,id',
            'financial_period_id' => 'required|exists:financial_periods,id',
        ]);

        $period = FinancialPeriod::where('id', $request->financial_period_id)
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->firstOrFail();

        CashManagement::create([
            'school_id'           => $school->id,
            'account_id'          => $request->account_id,
            'name'                => $request->name,
            'financial_period_id' => $period->id,
        ]);

        return redirect()->route('school-cash.index', $school)
            ->with('success', 'Kas baru berhasil ditambahkan.');
    }

    /**
     * Form edit kas.
     */
    public function edit(School $school, CashManagement $cash)
    {
        $accounts = Account::where('school_id', $school->id)->get();

        $periods = FinancialPeriod::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        return view('cash-management.edit', compact('school', 'cash', 'accounts', 'periods'));
    }

    /**
     * Update data kas.
     */
    public function update(Request $request, School $school, CashManagement $cash)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'account_id'          => 'required|exists:accounts,id',
            'financial_period_id' => 'required|exists:financial_periods,id',
        ]);

        $period = FinancialPeriod::where('id', $request->financial_period_id)
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->firstOrFail();

        $cash->update([
            'account_id'          => $request->account_id,
            'name'                => $request->name,
            'financial_period_id' => $period->id,
        ]);

        return redirect()->route('school-cash.index', $school)
            ->with('success', 'Kas berhasil diperbarui.');
    }

    /**
     * Hapus kas.
     */
    public function destroy(School $school, CashManagement $cash)
    {
        $cash->delete();

        return redirect()->route('school-cash.index', $school)
            ->with('success', 'Kas berhasil dihapus.');
    }
}