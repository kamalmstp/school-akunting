<?php

namespace App\Http\Controllers;

use App\Models\CashManagement;
use App\Models\School;
use App\Models\Account;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

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
        $accounts = Account::where('school_id', $school->id)
                    ->where('account_type', 'Aset Lancar')
                    ->where('code', 'like', '1-11%')
                    ->get();

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
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'name'                => 'required|string|max:100',
            'account_id'          => 'required|exists:accounts,id',
        ]);

        $period = FinancialPeriod::where('school_id', $school->id)
            ->where('is_active', true)
            ->firstOrFail();

        CashManagement::create([
            'school_id'           => $school->id,
            'account_id'          => $request->account_id,
            'name'                => $request->name,
            'financial_period_id' => $period->id,
        ]);

        $route = back();
        if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-cash-managements.index', $school);
        }

        return $route->with('success', 'Kas berhasil ditambahkan.');
    }

    /**
     * Form edit kas.
     */
    public function edit(School $school, CashManagement $cash_management)
    {
        $accounts = Account::where('school_id', $school->id)
                    ->where('account_type', 'Aset Lancar')
                    ->where('code', 'like', '1-11%')
                    ->get();

        $periods = FinancialPeriod::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        return view('cash-management.edit', compact('school', 'cash_management', 'accounts', 'periods'));
    }

    /**
     * Update data kas.
     */
    public function update(Request $request, School $school, CashManagement $cash_management)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'account_id'          => 'required|exists:accounts,id',
        ]);

        $period = FinancialPeriod::where('school_id', $school->id)
            ->where('is_active', true)
            ->firstOrFail();

        $cash_management->update([
            'account_id'          => $request->account_id,
            'name'                => $request->name,
            'financial_period_id' => $period->id,
        ]);

        return redirect()->route('school-cash-managements.index', $school)
            ->with('success', 'Kas berhasil diperbarui.');
    }

    /**
     * Hapus kas.
     */
    public function destroy(School $school, CashManagement $cash_management)
    {
        $cash_management->delete();

        return redirect()->route('school-cash-managements.index', $school)
            ->with('success', 'Kas berhasil dihapus.');
    }
}