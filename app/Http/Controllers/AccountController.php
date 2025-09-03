<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AccountsImport;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(['role:SuperAdmin,AdminMonitor']);
        $this->middleware('school.access');
    }

    /**
     * Display a listing of accounts.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $account = $request->get('account');
        $type = $request->get('type');

        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        if (auth()->user()->role != 'SchoolAdmin') {
            $schoolId = $request->get('school');
            $accounts = Account::with('parent','school')
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->when($account, function ($q) use ($account) {
                    $q->where('account_type', $account);
                })
                ->when($type, function ($q) use ($type) {
                    $q->where('normal_balance', $type);
                })
                ->paginate(10)->withQueryString();
            return view('accounts.index', compact('accounts', 'school', 'account', 'type', 'schoolId'));
        }

        $accounts = Account::with('parent','school')
            ->where('school_id', $school->id)
            ->when($account, function ($q) use ($account) {
                $q->where('account_type', $account);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('normal_balance', $type);
            })
            ->paginate(10)->withQueryString();
        return view('accounts.index', compact('accounts', 'school', 'account', 'type'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $schools = School::all();
        $accounts = $this->getAccounts($school->id);

        return view('accounts.create', compact('school', 'schools', 'accounts'));
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'code' => 'required|string|max:20|unique:accounts',
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:Aset Lancar,Aset Tetap,Kewajiban,Aset Neto,Pendapatan,Biaya,Investasi',
            'normal_balance' => 'required|in:Debit,Kredit',
            'parent_id' => 'nullable|exists:accounts,id',
        ];

        // Tambahkan validasi kode berdasarkan tipe akun
        $codeRules = $this->getCodePatternRules();
        if (isset($codeRules[$request->account_type])) {
            $rules['code'] .= '|' . 'regex:' . $codeRules[$request->account_type]['regex'];
        }

        $messages = [
            'code.required' => 'Kode akun wajib diisi',
            'code.max' => 'Kode akun maksimal 20 digit',
            'code.unique' => 'Kode akun sudah digunakan',
            'code.regex' => $codeRules[$request->account_type]['message'] ?? 'Format kode akun tidak valid.',
            'name.required' => 'Nama akun wajib diisi',
            'account_type.required' => 'Pilih salah satu tipe akun',
            'normal_balance.required' => 'Pilih salah satu saldo normal',
        ];

        if (auth()->user()->role == 'SuperAdmin' && !isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih sekolah';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi parent_id: kode harus konsisten dan sekolah sama
        if ($request->parent_id) {
            $parent = Account::find($request->parent_id);
            if (!str_starts_with($request->code, $parent->code)) {
                return redirect()->back()->withErrors(['code' => 'Kode akun anak harus dimulai dengan kode akun induk (' . $parent->code . ').'])->withInput();
            }
        }

        Account::create([
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
            'code' => $request->code,
            'name' => $request->name,
            'account_type' => $request->account_type,
            'normal_balance' => $request->normal_balance,
            'parent_id' => $request->parent_id,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('accounts.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-accounts.index', $school);
        }

        return $route->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(School $school, Account $account)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $account->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or account.');
        }

        $schools = School::all();
        $accounts = $this->getAccounts($school->id);

        return view('accounts.edit', compact('school', 'account', 'schools', 'accounts'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, School $school, Account $account)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $account->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or account.');
        }

        $rules = [
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:Aset Lancar,Aset Tetap,Kewajiban,Aset Neto,Pendapatan,Biaya,Investasi',
            'normal_balance' => 'required|in:Debit,Kredit',
            'parent_id' => 'nullable|exists:accounts,id',
        ];

        // Tambahkan validasi kode berdasarkan tipe akun
        $codeRules = $this->getCodePatternRules();
        if (isset($codeRules[$request->account_type])) {
            $rules['code'] .= '|' . 'regex:' . $codeRules[$request->account_type]['regex'];
        }

        $messages = [
            'code.required' => 'Kode akun wajib diisi',
            'code.max' => 'Kode akun maksimal 20 digit',
            'code.unique' => 'Kode akun sudah digunakan',
            'code.regex' => $codeRules[$request->account_type]['message'] ?? 'Format kode akun tidak valid.',
            'name.required' => 'Nama akun wajib diisi',
            'account_type.required' => 'Pilih salah satu tipe akun',
            'normal_balance.required' => 'Pilih salah satu saldo normal',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi parent_id: kode harus konsisten dan sekolah sama
        if ($request->parent_id) {
            $parent = Account::find($request->parent_id);
            if (!str_starts_with($request->code, $parent->code)) {
                return redirect()->back()->withErrors(['code' => 'Kode akun anak harus dimulai dengan kode akun induk (' . $parent->code . ').'])->withInput();
            }
            if ($request->parent_id == $account->id) {
                return redirect()->back()->withErrors(['parent_id' => 'Akun tidak dapat menjadi induk dari dirinya sendiri.'])->withInput();
            }
        }

        $account->update([
            'code' => $request->code,
            'name' => $request->name,
            'account_type' => $request->account_type,
            'normal_balance' => $request->normal_balance,
            'parent_id' => $request->parent_id,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('accounts.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-accounts.index', $school);
        }

        return $route->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy(School $school, Account $account)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $account->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or account.');
        }

        if ($account->transactions()->exists()) {
            return redirect()->route('accounts.index')->with('error', 'Akun tidak dapat dihapus karena memiliki transaksi terkait.');
        }

        if ($account->children()->exists()) {
            return redirect()->route('accounts.index')->with('error', 'Akun tidak dapat dihapus karena memiliki akun anak.');
        }

        $account->delete();

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('accounts.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-accounts.index', $school);
        }
        return $route->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Show the import form.
     */
    public function importForm(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('accounts.import', compact('school'));
    }

    /**
     * Handle the import of accounts from Excel.
     */
    public function import(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }
        $rules = ['file' => 'required|mimes:xlsx,xls|max:2048'];
        $messages = [
            'file.required' => 'File wajib diupload',
            'file.mimes' => 'Tipe file tidak valid',
            'file.max' => 'File maksimal 2MB'
        ];

        if (auth()->user()->role == 'SuperAdmin') {
            $rules['school'] = 'required';
            $messages['school.required'] = 'Pilih salah satu sekolah';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $schoolId = auth()->user()->role == 'SuperAdmin' ? $request->school : $school->id;
            Excel::import(new AccountsImport($schoolId), $request->file('file'));

            $route = back();
            if (auth()->user()->role == 'SuperAdmin') {
                $route = redirect()->route('accounts.index');
            } else if (auth()->user()->role == 'SchoolAdmin') {
                $route = redirect()->route('school-accounts.index', $school);
            }
            return $route->with('success', 'Data akun berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Download the account template excel
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/accounts_template.xlsx'));
    }

    /**
     * Get code pattern rules based on account type.
     */
    protected function getCodePatternRules()
    {
        return [
            'Aset Lancar' => ['regex' => '/^1-1[0-9]{0,5}(-[0-9]+)*$/', 'message' => 'Kode Aset Lancar harus dimulai dengan 1-1 dan opsional -[angka].'],
            'Aset Tetap' => ['regex' => '/^1-2[0-9]{0,5}(-[0-9]+)*$/', 'message' => 'Kode Aset Tetap harus dimulai dengan 1-2 dan opsional -[angka].'],
            'Kewajiban' => ['regex' => '/^2-[0-9]{0,6}(-[0-9]+)*$/', 'message' => 'Kode Kewajiban harus dimulai dengan 2- dan opsional -[angka].'],
            'Aset Neto' => ['regex' => '/^3-[0-9]{0,6}(-[0-9]+)*$/', 'message' => 'Kode Aset Neto harus dimulai dengan 3- dan opsional -[angka].'],
            'Pendapatan' => ['regex' => '/^4-[0-9]{0,6}(-[0-9]+)*$/', 'message' => 'Kode Pendapatan harus dimulai dengan 4- dan opsional -[angka].'],
            'Biaya' => ['regex' => '/^6-[0-9]{0,6}(-[0-9]+)*$/', 'message' => 'Kode Biaya harus dimulai dengan 6- opsional -[angka].'],
            'Investasi' => ['regex' => '/^7-[0-9]{0,6}(-[0-9]+)*$/', 'message' => 'Kode Investasi harus dimulai dengan 7- dan opsional -[angka].'],
        ];
    }

    protected function getAccounts($school_id)
    {
        $accounts = Account::where('school_id', $school_id)->get();
        return $accounts;
    }
}