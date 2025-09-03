<?php

namespace App\Http\Controllers;

use App\Models\FundManagement;
use App\Models\School;
use App\Models\Account;
use App\Models\StudentReceivables;
// use App\Models\TeacherReceivable;
// use App\Models\EmployeeReceivable;
use Illuminate\Http\Request;

class FundManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the fund managements.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $name = $request->get('name');

        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        if (auth()->user()->role != 'SchoolAdmin') {
            $schoolId = $request->get('school');
            $funds = FundManagement::when($name, function ($q) use ($name) {
                    $q->where('name', 'like', '%' . $name . '%');
                })
                ->when($schoolId, function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->paginate(10)->withQueryString();
            return view('fund-managements.index', compact('funds', 'name', 'schoolId','school','schoolId'));
        }

        $schoolId = $school->id;
        $funds = FundManagement::where('school_id', $schoolId)
                ->when($name, function ($q) use ($name) {
                    $q->where('name', 'like', '%' . $name . '%');
                })
                ->paginate(10)->withQueryString();

        return view('fund-managements.index', compact('funds', 'name', 'schoolId','school'));
    }

    /**
     * Show the form for creating a new fund management.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::where('school_id', $school->id)->get();
        return view('fund-managements.create', compact('school', 'accounts'));
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'name.required' => 'Nama jurusan wajib diisi',
        ];

        if (auth()->user()->role == 'SuperAdmin' && !isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih sekolah';
        }

        $request->validate($rules, $messages);

        if ($request->account_id) {
            $sum_amount = StudentReceivables::where('account_id', $request->account_id)
                ->where('status', 'Paid')
                ->sum('total_payable');
        } else {
            $sum_amount = (float) str_replace('.', '', $request->amount);
        }
        FundManagement::create([
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
            'account_id' => $request->account_id,
            'name' => $request->name,
            'amount' => $sum_amount,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fund-managements.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fund-managements.index', $school);
        }

        return $route->with('success', 'Jurusan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school, FundManagement $fund_management)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $fund_management->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::where('school_id', $school->id)->get();
        return view('fund-managements.edit', compact('fund_management', 'school', 'accounts'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school, FundManagement $fund_management)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $fund_management->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama jurusan wajib diisi',
        ]);

        if ($request->account_id) {
            $sum_amount = StudentReceivables::where('account_id', $request->account_id)
                ->where('status', 'Paid')
                ->sum('total_payable');
        } else {
            $sum_amount = (float) str_replace('.', '', $request->amount);
        }
        $fund_management->update([
            'account_id' => $request->account_id,
            'name' => $request->name,
            'amount' => $sum_amount,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fund-managements.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fund-managements.index', $school);
        }

        return $route->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school, FundManagement $fund_management)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $fund_management->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $fund_management->delete();

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fund-managements.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fund-managements.index', $school);
        }
        return $route->with('success', 'Jurusan berhasil dihapus.');
    }
}