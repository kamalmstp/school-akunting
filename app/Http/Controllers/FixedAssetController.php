<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\FixAsset;
use App\Models\Account;
use App\Models\Depreciation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FixedAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the fixed assets.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $account = $request->get('account');
        $acqDate = is_null($request->get('date')) ? '' : $request->get('date');
        $assetName = $request->get('name');
        if (auth()->user()->role != 'SchoolAdmin') {
            // SuperAdmin: Semua aset tetap
            $schools = School::pluck('name', 'id');
            $school = $request->get('school');
            $fixedAssets = FixAsset::with(['school', 'account'])
                ->when($school, function ($q) use ($school) {
                    $q->where('school_id', $school);
                })
                ->when($account, function ($q) use ($account) {
                    $q->where('account_id', $account);
                })
                ->when($acqDate, function ($q) use ($acqDate) {
                    $q->where('acquisition_date', Carbon::parse($acqDate)->format('Y-m-d'));
                })
                ->when($assetName, function ($q) use ($assetName) {
                    $q->where('name', 'like', '%' . $assetName . '%');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10)->withQueryString();
            
            return view('fixed-assets.index', compact('fixedAssets', 'schools', 'school', 'account', 'acqDate', 'assetName'));
        }

        // SchoolAdmin atau SuperAdmin dengan sekolah tertentu
        $school = $school ?? $user->school;
        if (!$school || ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id)) {
            abort(403, 'Unauthorized access to this school.');
        }

        $fixedAssets = FixAsset::where('school_id', $school->id)
            ->with('account')
            ->when($account, function ($q) use ($account) {
                $q->where('account_id', $account);
            })
            ->when($acqDate, function ($q) use ($acqDate) {
                $q->where('acquisition_date', Carbon::parse($acqDate)->format('Y-m-d'));
            })
            ->when($assetName, function ($q) use ($assetName) {
                $q->where('name', 'like', '%' . $assetName . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('fixed-assets.index', compact('fixedAssets', 'school', 'account', 'acqDate', 'assetName'));
    }

    /**
     * Show the form for creating a new fixed asset.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $accounts = Account::get();
        return view('fixed-assets.create', compact('school', 'accounts'));
    }

    /**
     * Store a newly created fixed asset in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'account_id.*' => 'required',
            'acquisition_date' => 'required|date',
            'useful_life' => 'required|integer|min:1',
        ];

        $messages = [
            'name.required' => 'Nama aset wajib diisi',
            'account_id.*.required' => 'Pilih akun',
            'acquisition_date.required' => 'Tanggal perolehan wajib diisi',
            'useful_life' => 'Umur manfaat wajib diisi'
        ];

        if (auth()->user()->role == 'SuperAdmin') {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih salah satu sekolah';
        }

        $request->validate($rules, $messages);

        $debit = [];
        $credit = [];
        foreach ($request->debit as $index => $value) {
            $debit[] = $value ? (float)str_replace('.', '', $value) : 0;
            $credit[] = $request->credit[$index] ? (float)str_replace('.', '', $request->credit[$index]) : 0;
        }
        $totalDebit = array_sum($debit);
        $totalCredit = array_sum($credit);

        if ($totalDebit != $totalCredit) {
            return back()->withErrors(['balance' => 'Pastikan pemasukan dan pengeluaran seimbang']);
        }

        if ($debit[0] == 0) {
            return back()->withErrors(['balance' => 'Pastikan akun pemasukan diinput terlebih dulu']);
        }

        $schoolId = auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id;
        $percentageValue = 1 / $request->useful_life * 100;
        $fixedAsset = FixAsset::create([
            'school_id' => $schoolId,
            'account_id' => $request->account_id[0],
            'name' => $request->name,
            'acquisition_date' => $request->acquisition_date,
            'acquisition_cost' => $totalDebit,
            'useful_life' => $request->useful_life,
            'accumulated_depriciation' => 0,
            'depreciation_percentage' => $percentageValue
        ]);

        foreach ($request->account_id as $index => $value) {
            // Catat transaksi pembelian aset tetap (Debit: Aset Tetap)
            Transaction::create([
                'school_id' => $schoolId,
                'account_id' => $value,
                'date' => $request->acquisition_date,
                'description' => Account::find($value)->name . ' : ' . $request->name,
                'debit' => (float)str_replace('.', '', $request->debit[$index]) ?? 0,
                'credit' => (float)str_replace('.', '', $request->credit[$index]) ?? 0,
                'reference_id' => $fixedAsset->id,
                'reference_type' => FixAsset::class,
            ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fixed-assets.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fixed-assets.index', $school);
        }

        return $route->with('success', 'Aset tetap berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified fixed asset.
     */
    public function edit(School $school, FixAsset $fixed_asset)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $fixedAsset = $fixed_asset;
        $accounts = Account::get();
        $transactions = Transaction::where([
            ['reference_id', '=', $fixedAsset->id],
            ['reference_type', '=', 'App\Models\FixAsset']
        ])->get();
        return view('fixed-assets.edit', compact('fixedAsset', 'school', 'accounts', 'transactions'));
    }

    /**
     * Update the specified fixed asset in storage.
     */
    public function update(Request $request, School $school, FixAsset $fixed_asset)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'account_id.*' => 'required',
            'acquisition_date' => 'required|date',
            'useful_life' => 'required|integer|min:1',
            'depreciation_percentage' => 'required|max:100'
        ], [
            'name.required' => 'Nama aset wajib diisi',
            'account_id.*.required' => 'Pilih akun',
            'acquisition_date.required' => 'Tanggal perolehan wajib diisi',
            'useful_life' => 'Umur manfaat wajib diisi',
            'depreciation_percentage.required' => 'Persentase wajib diisi',
            'depreciation_percentage.max' => 'Persentase maksimal 100%'
        ]);

        $debit = [];
        $credit = [];
        foreach ($request->debit as $index => $value) {
            $debit[] = $value ? (float)str_replace('.', '', $value) : 0;
            $credit[] = $request->credit[$index] ? (float)str_replace('.', '', $request->credit[$index]) : 0;
        }
        $totalDebit = array_sum($debit);
        $totalCredit = array_sum($credit);

        if ($totalDebit != $totalCredit) {
            return back()->withErrors(['balance' => 'Pastikan pemasukan dan pengeluaran seimbang']);
        }

        if ($debit[0] == 0) {
            return back()->withErrors(['balance' => 'Pastikan akun pemasukan diinput terlebih dulu']);
        }

        $fixed_asset->update([
            'name' => $request->name,
            'account_id' => $request->account_id[0],
            'acquisition_date' => $request->acquisition_date,
            'acquisition_cost' => $totalDebit,
            'useful_life' => $request->useful_life,
            'depreciation_percentage' => $request->depreciation_percentage
        ]);

        Transaction::where([
            ['reference_id', '=', $fixed_asset->id],
            ['reference_type', '=', 'App\Models\FixAsset']
        ])->delete();

        foreach ($request->account_id as $index => $value) {
            // Catat transaksi pembelian aset tetap (Debit Kredit: Aset Tetap)
            Transaction::create([
                'school_id' => $school->id,
                'account_id' => $value,
                'date' => $request->acquisition_date,
                'description' => Account::find($value)->name . ' : ' . $request->name,
                'debit' => (float)str_replace('.', '', $request->debit[$index]) ?? 0,
                'credit' => (float)str_replace('.', '', $request->credit[$index]) ?? 0,
                'reference_id' => $fixed_asset->id,
                'reference_type' => FixAsset::class,
            ]);
        }

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fixed-assets.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fixed-assets.index', $school);
        }

        return $route->with('success', 'Aset tetap berhasil diperbarui.');
    }

    /**
     * Remove the specified fixed asset from storage.
     */
    public function destroy(School $school, FixAsset $fixed_asset)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $fixedAsset = $fixed_asset;
        Transaction::where([
            ['reference_id', '=', $fixedAsset->id],
            ['reference_type', '=', 'App\Models\FixAsset']
        ])->update(['deleted_at' => now()]);
        Depreciation::where('fix_asset_id', $fixedAsset->id)->pluck('id');
        Transaction::whereIn([
            ['reference_id', '=', $depreciation],
            ['reference_type', '=', 'App\Models\Depreciation']
        ])->update(['deleted_at' => now()]);
        $fixedAsset->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fixed-assets.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fixed-assets.index', $school);
        }

        return $route->with('success', 'Aset tetap berhasil dihapus.');
    }

    /**
     * Show the form for recording depreciation.
     */
    public function depreciateForm(School $school, FixAsset $fixed_asset)
    {
        $user = auth()->user();
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $fixedAsset = $fixed_asset;
        $accounts = Account::where('account_type', 'Biaya')
            ->where('code', 'like', '6-12%') // Biaya Penyusutan (1-11)
            ->get();
        return view('fixed-assets.depreciate', compact('fixedAsset', 'school', 'accounts'));
    }

    /**
     * Process depreciation for a fixed asset.
     */
    public function depreciate(Request $request, School $school, FixAsset $fixed_asset)
    {
        $user = auth()->user();
        $fixedAsset = $fixed_asset;
        if ($user->role === 'SchoolAdmin' && $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'account_id' => [
                function ($attribute, $value, $fail) use ($fixedAsset) {
                    if ($fixedAsset->depreciations->isEmpty() && empty($value)) {
                        $fail('Pilih akun biaya penyusutan');
                    }
                }
            ],
            'date' => 'required|date',
            'description' => 'required'
        ], [
            'date.required' => 'Tanggal penyusutan wajib diisi',
            'description.required' => 'Deskripsi penyusutan wajib diisi',
        ]);

        $existDepreciation = Depreciation::where('fix_asset_id', $fixedAsset->id)->latest()->first();

        $accountId = $existDepreciation ? $existDepreciation->account_id : $request->account_id;
        $accountName = Account::find($accountId)->name;

        $accumulateAccount = Account::where([
            ['account_type', '=', 'Aset Tetap'],
            ['code', 'like', '1-25%'],
            ['name', '=', str_replace('Biaya', 'Akumulasi', $accountName)]
        ])->first();

        if (!$accumulateAccount) {
            return back()->withErrors(['amount' => 'Akun ' . str_replace('Biaya', 'Akumulasi', $accountName) . ' tidak ditemukan.']);
        }
        // Tambah penyusutan
        $depreciateAmount = ($fixedAsset->acquisition_cost - $fixedAsset->accumulated_depriciation) * $fixedAsset->depreciation_percentage / 100;
        $balance = $existDepreciation ? $existDepreciation->balance - $depreciateAmount : $fixedAsset->acquisition_cost - $depreciateAmount;
        $fixedAsset->accumulated_depriciation += $depreciateAmount;
        $fixedAsset->update(['accumulated_depriciation' => $fixedAsset->accumulated_depriciation]);

        $depreciation = Depreciation::create([
            'fix_asset_id' => $fixedAsset->id,
            'account_id' => $accountId,
            'date' => $request->date,
            'description' => $request->description,
            'amount' => $depreciateAmount,
            'balance' => $balance
        ]);

        // Catat transaksi penyusutan
        Transaction::create([
            'school_id' => $fixedAsset->school_id,
            'account_id' => $accountId,
            'date' => $request->date,
            'description' => 'Penyusutan aset: ' . $fixedAsset->name,
            'debit' => $depreciateAmount,
            'credit' => 0,
            'reference_id' => $depreciation->id,
            'reference_type' => Depreciation::class,
        ]);

        Transaction::create([
            'school_id' => $fixedAsset->school_id,
            'account_id' => $accumulateAccount->id,
            'date' => $request->date,
            'description' => 'Penyusutan aset: ' . $fixedAsset->name,
            'debit' => 0,
            'credit' => $depreciateAmount,
            'reference_id' => $depreciation->id,
            'reference_type' => Depreciation::class,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('fixed-assets.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-fixed-assets.index', $school);
        }

        return $route->with('success', 'Penyusutan berhasil dicatat.');
    }
}