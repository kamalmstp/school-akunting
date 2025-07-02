<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\School;
use App\Models\TeacherReceivable;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of teachers.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $teacherNumber = $request->get('nik');
        $teacherName = $request->get('name');

        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        if (auth()->user()->role != 'SchoolAdmin') {
            $schoolId = $request->get('school');
            $teachers = Teacher::with('school')
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->when($teacherNumber, function ($q) use ($teacherNumber) {
                    $q->where('teacher_id_number', 'like', $teacherNumber . '%');
                })
                ->when($teacherName, function ($q) use ($teacherName) {
                    $q->where('name', 'like', '%' . $teacherName . '%');
                })
                ->paginate(10)->withQueryString();
            return view('teachers.index', compact('teachers', 'school', 'teacherNumber', 'teacherName', 'schoolId'));
        }

        $teachers = Teacher::where('school_id', $school->id)
            ->when($teacherNumber, function ($q) use ($teacherNumber) {
                $q->where('teacher_id_number', 'like', $teacherNumber . '%');
            })
            ->when($teacherName, function ($q) use ($teacherName) {
                $q->where('name', 'like', '%' . $teacherName . '%');
            })
            ->paginate(10)->withQueryString();
        return view('teachers.index', compact('teachers', 'school', 'teacherNumber', 'teacherName'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('teachers.create', compact('school'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'teacher_id_number' => 'required|string|max:20|unique:teachers',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|max:13',
            'is_active' => 'boolean',
        ];

        $messages = [
            'teacher_id_number.required' => 'NIK wajib diisi',
            'teacher_id_number.max' => 'NIK maksimal 20 digit',
            'teacher_id_number.unique' => 'NIK sudah digunakan',
            'name.required' => 'Nama guru wajib diisi',
            'email.required' => 'Email guru wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'Telepon guru wajib diisi',
            'phone.max' => 'Telepon guru maksimal 13 angka',
        ];

        if (auth()->user()->role == 'SuperAdmin' && isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.reuired'] = 'Pilih sekolah';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Teacher::create([
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
            'teacher_id_number' => $request->teacher_id_number,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teachers.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teachers.index', $school);
        }

        return $route->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(School $school, Teacher $teacher)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $teacher->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('teachers.edit', compact('school', 'teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, School $school, Teacher $teacher)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $teacher->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $validator = Validator::make($request->all(), [
            'teacher_id_number' => 'required|string|max:20|unique:teachers,teacher_id_number,' . $teacher->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|max:13',
            'is_active' => 'boolean',
        ], [
            'teacher_id_number.required' => 'NIK wajib diisi',
            'teacher_id_number.max' => 'NIK maksimal 20 digit',
            'teacher_id_number.unique' => 'NIK sudah digunakan',
            'name.required' => 'Nama guru wajib diisi',
            'email.required' => 'Email guru wajib diisi',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Telepon guru wajib diisi',
            'phone.max' => 'Telepon guru maksimal 13 angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $teacher->update([
            'teacher_id_number' => $request->teacher_id_number,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teachers.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teachers.index', $school);
        }

        return $route->with('success', 'Guru berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(School $school, Teacher $teacher)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $teacher->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        Transaction::WhereIn('reference_id', TeacherReceivable::where('teacher_id', $teacher->id)->pluck('id'))
            ->where('reference_type', 'App\Models\TeacherReceivable')
            ->update(['deleted_at' => now()]);
        TeacherReceivable::where('teacher_id', $teacher->id)->update(['deleted_at' => now()]);
        $teacher->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('teachers.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-teachers.index', $school);
        }
        return $route->with('success', 'Guru berhasil dihapus.');
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

        return view('teachers.import', compact('school'));
    }

    /**
     * Handle the import of teachers from Excel.
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
            Excel::import(new TeachersImport($schoolId), $request->file('file'));

            $route = back();
            if (auth()->user()->role == 'SuperAdmin') {
                $route = redirect()->route('teachers.index');
            } else if (auth()->user()->role == 'SchoolAdmin') {
                $route = redirect()->route('school-teachers.index', $school);
            }
            return $route->with('success', 'Data guru berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template for teachers.
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/teachers_template.xlsx'));
    }
}