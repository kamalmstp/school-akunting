<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Account;
use App\Models\School;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the schedules.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $status = $request->get('status');
        $payType = $request->get('pay_type');
        $userType = $request->get('user_type');
        $accountId = $request->get('account');
        $accounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();
        if (auth()->user()->role != 'SchoolAdmin') {
            $schools = School::pluck('name', 'id');
            $school = $request->get('school');
            $schedules = Schedule::with(['school', 'account', 'income_account'])
                ->when($school, function ($q) use ($school) {
                    $q->where('school_id', $school);
                })
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($payType, function ($q) use ($payType) {
                    $q->where('schedule_type', $payType);
                })
                ->when($userType, function ($q) use ($userType) {
                    $q->where('user_type', $userType);
                })
                ->when($accountId, function ($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10)->withQueryString();
            
            return view('settings.index', compact('schedules', 'schools', 'status', 'school', 'payType', 'userType', 'accounts', 'accountId'));
        }

        // SchoolAdmin atau SuperAdmin dengan sekolah tertentu
        $school = $school ?? $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schedules = Schedule::with(['school', 'account', 'income_account'])
            ->where('school_id', $school->id)
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($payType, function ($q) use ($payType) {
                $q->where('schedule_type', $payType);
            })
            ->when($userType, function ($q) use ($userType) {
                $q->where('user_type', $userType);
            })
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('account_id', $accountId);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('settings.index', compact('schedules', 'school', 'status', 'payType', 'userType', 'accounts', 'accountId'));
    }

    /**
     * Show the form for creating a new receivable.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }
        $schoolId = $school->id;
        $accounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();

        return view('settings.create', compact('school', 'accounts'));
    }

    /**
     * Store a newly created receivable in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'account_id' => 'required',
            'income_account_id' => 'required',
            'user_type' => 'required',
            'amount' => 'required',
            'schedule_type' => 'required'
        ];

        $messages = [
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'user_type.required' => 'Pilih tipe user',
            'amount.required' => 'Jumlah wajib diisi',
            'schedule_type.required' => 'Pilih tipe pembayaran'
        ];

        if (auth()->user()->role == 'SuperAdmin') {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih salah satu sekolah';
        }

        $request->validate($rules, $messages);

        $scheduleExist = Schedule::where([
            ['account_id', '=', $request->account_id],
            ['status', '=', true],
            ['user_type', '=', $request->user_type]
        ])->exists();

        if ($scheduleExist) {
            return back()->withErrors(['invalid' => 'Pemabayaran untuk akun piutang sudah ada.']);
        }

        $schoolId = auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id;
        Schedule::create([
            'school_id' => $schoolId,
            'account_id' => $request->account_id,
            'income_account_id' => $request->income_account_id,
            'user_type' => $request->user_type,
            'amount' => (float)str_replace('.', '', $request->amount),
            'description' => $request->description,
            'status' => $request->status,
            'schedule_type' => $request->schedule_type
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('schedules.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-schedules.index', $school);
        }

        return $route->with('success', 'Data pembayaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified receivable.
     */
    public function edit(School $school, Schedule $schedule)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::where('account_type', 'Aset Lancar')
            ->where('code', 'like', '1-12%')
            ->get();
        
        return view('settings.edit', compact('schedule', 'school', 'accounts'));
    }

    /**
     * Update the specified receivable in storage.
     */
    public function update(Request $request, School $school, Schedule $schedule)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'account_id' => 'required',
            'income_account_id' => 'required',
            'user_type' => 'required',
            'amount' => 'required',
            'schedule_type' => 'required'
        ], [
            'account_id.required' => 'Pilih akun piutang',
            'income_account_id.required' => 'Pilih akun pendapatan',
            'user_type.required' => 'Pilih tipe user',
            'amount.required' => 'Jumlah wajib diisi',
            'schedule_type.required' => 'Pilih tipe pembayaran'
        ]);

        $schedule->update([
            'account_id' => $request->account_id,
            'income_account_id' => $request->income_account_id,
            'user_type' => $request->user_type,
            'amount' => (float)str_replace('.', '', $request->amount),
            'description' => $request->description,
            'status' => $request->status,
            'schedule_type' => $request->schedule_type
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('schedules.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-schedules.index', $school);
        }

        return $route->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified receivable from storage.
     */
    public function destroy(School $school, Schedule $schedule)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schedule->delete();

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('schedules.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-schedules.index', $school);
        }

        return $route->with('success', 'Data pembayaran berhasil dihapus.');
    }
}