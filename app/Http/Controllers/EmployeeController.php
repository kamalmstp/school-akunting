<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\School;
use App\Models\EmployeeReceivable;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $employeeNumber = $request->get('nik');
        $employeeName = $request->get('name');

        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        if (auth()->user()->role != 'SchoolAdmin') {
            $schoolId = $request->get('school');
            $employees = Employee::with('school')
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->when($employeeNumber, function ($q) use ($employeeNumber) {
                    $q->where('employee_id_number', 'like', $employeeNumber . '%');
                })
                ->when($employeeName, function ($q) use ($employeeName) {
                    $q->where('name', 'like', '%' . $employeeName . '%');
                })
                ->paginate(10)->withQueryString();
            return view('employees.index', compact('employees', 'school', 'employeeNumber', 'employeeName', 'schoolId'));
        }

        $employees = Employee::where('school_id', $school->id)
            ->when($employeeNumber, function ($q) use ($employeeNumber) {
                $q->where('employee_id_number', 'like', $employeeNumber . '%');
            })
            ->when($employeeName, function ($q) use ($employeeName) {
                $q->where('name', 'like', '%' . $employeeName . '%');
            })
            ->paginate(10)->withQueryString();
        return view('employees.index', compact('employees', 'school', 'employeeNumber', 'employeeName'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('employees.create', compact('school'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'employee_id_number' => 'required|string|max:20|unique:employees',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|max:13',
            'is_active' => 'boolean',
        ];

        $messages = [
            'employee_id_number.required' => 'NIK wajib diisi',
            'employee_id_number.max' => 'NIK maksimal 20 digit',
            'employee_id_number.unique' => 'NIK sudah digunakan',
            'name.required' => 'Nama karyawan wajib diisi',
            'email.required' => 'Email karyawan wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Telepon karyawan wajib diisi',
            'phone.max' => 'Telepon karyawan maksimal 13 angka',
        ];

        if (auth()->user()->role == 'SuperAdmin' && isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.reuired'] = 'Pilih sekolah';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Employee::create([
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
            'employee_id_number' => $request->employee_id_number,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employees.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employees.index', $school);
        }

        return $route->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(School $school, Employee $employee)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $employee->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('employees.edit', compact('school', 'employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, School $school, Employee $employee)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $employee->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id_number' => 'required|string|max:20|unique:employees,employee_id_number,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|max:13',
            'is_active' => 'boolean',
        ], [
            'employee_id_number.required' => 'NIK wajib diisi',
            'employee_id_number.max' => 'NIK maksimal 20 digit',
            'employee_id_number.unique' => 'NIK sudah digunakan',
            'name.required' => 'Nama karyawan wajib diisi',
            'email.required' => 'Email karyawan wajib diisi',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Telepon karyawan wajib diisi',
            'phone.max' => 'Telepon karyawan maksimal 13 angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee->update([
            'employee_id_number' => $request->employee_id_number,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employees.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employees.index', $school);
        }

        return $route->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(School $school, Employee $employee)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $employee->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::WhereIn('reference_id', EmployeeReceivable::where('employee_id', $employee->id)->pluck('id'))
            ->where('reference_type', 'App\Models\EmployeeReceivable')
            ->update(['deleted_at' => now()]);
        EmployeeReceivable::where('employee_id', $employee->id)->update(['deleted_at' => now()]);
        $employee->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('employees.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-employees.index', $school);
        }
        return $route->with('success', 'Karyawan berhasil dihapus.');
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

        return view('employees.import', compact('school'));
    }

    /**
     * Handle the import of employees from Excel.
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
            Excel::import(new EmployeesImport($schoolId), $request->file('file'));

            $route = back();
            if (auth()->user()->role == 'SuperAdmin') {
                $route = redirect()->route('employees.index');
            } else if (auth()->user()->role == 'SchoolAdmin') {
                $route = redirect()->route('school-employees.index', $school);
            }
            return $route->with('success', 'Data karyawan berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template for employees.
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/employees_template.xlsx'));
    }
}