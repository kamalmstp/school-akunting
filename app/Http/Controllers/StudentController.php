<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use App\Models\StudentReceivables;
use App\Models\Transaction;
use App\Models\SchoolMajor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request, School $school)
    {
        $user = auth()->user();
        $studentNumber = $request->get('nis');
        $studentName = $request->get('name');

        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        if (auth()->user()->role != 'SchoolAdmin') {
            $schoolId = $request->get('school');
            $students = Student::with('school')
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->when($studentNumber, function ($q) use ($studentNumber) {
                    $q->where('student_id_number', 'like', $studentNumber . '%');
                })
                ->when($studentName, function ($q) use ($studentName) {
                    $q->where('name', 'like', '%' . $studentName . '%');
                })
                ->paginate(10)->withQueryString();
            return view('students.index', compact('students', 'school', 'studentNumber', 'studentName', 'schoolId'));
        }

        $students = Student::where('school_id', $school->id)
            ->when($studentNumber, function ($q) use ($studentNumber) {
                $q->where('student_id_number', 'like', $studentNumber . '%');
            })
            ->when($studentName, function ($q) use ($studentName) {
                $q->where('name', 'like', '%' . $studentName . '%');
            })
            ->paginate(10)->withQueryString();
        return view('students.index', compact('students', 'school', 'studentNumber', 'studentName'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $classes = $this->getClass();

        return view('students.create', compact('school', 'classes'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request, School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $rules = [
            'student_id_number' => 'required|string|max:20|unique:students',
            'national_student_number' => 'required|string|max:20|unique:students',
            'year' => 'required|integer',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|max:13',
            'parent_mail' => 'required|email',
            'parent_job' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|max:13',
            'class' => 'required|string|max:50',
            'is_active' => 'boolean',
        ];

        $messages = [
            'student_id_number.required' => 'NIS wajib diisi',
            'student_id_number.max' => 'NIS maksimal 20 digit',
            'student_id_number.unique' => 'NIS sudah digunakan',
            'national_student_number.required' => 'NISN wajib diisi',
            'national_student_number.max' => 'NISN maksimal 20 digit',
            'national_student_number.unique' => 'NISN sudah digunakan',
            'year.required' => 'Tahun Masuk wajib diisi',
            'parent_name.required' => 'Nama Orang Tua wajib diisi',
            'parent_phone.required' => 'Telepon Orang Tua wajib diisi',
            'parent_phone.max' => 'Telepon Orang Tua maksimal 13 angka',
            'parent_mail.required' => 'Email Orang Tua wajib diisi',
            'parent_mail.email' => 'Format email tidak valid',
            'parent_job.required' => 'Pekerjaan Orang Tua wajib diisi',
            'name.required' => 'Nama siswa wajib diisi',
            'phone.required' => 'Telepon siswa wajib diisi',
            'phone.max' => 'Telepon siswa maksimal 13 angka',
            'class.required' => 'Kelas siswa wajib diisi'
        ];

        if (auth()->user()->role == 'SuperAdmin' && !isset($request->school_id)) {
            $rules['school_id'] = 'required';
            $messages['school_id.required'] = 'Pilih sekolah';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Student::create([
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
            'student_id_number' => $request->student_id_number,
            'national_student_number' => $request->national_student_number,
            'year' => $request->year,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'parent_mail' => $request->parent_mail,
            'parent_job' => $request->parent_job,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'class' => $request->class,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('students.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-students.index', $school);
        }

        return $route->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(School $school, Student $student)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $student->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or student.');
        }

        $classes = $this->getClass();

        return view('students.edit', compact('school', 'student', 'classes'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, School $school, Student $student)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $student->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or student.');
        }

        $validator = Validator::make($request->all(), [
            'student_id_number' => 'required|string|max:20|unique:students,student_id_number,' . $student->id,
            'national_student_number' => 'required|string|max:20|unique:students,national_student_number,' . $student->id,
            'year' => 'required|integer',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|max:13',
            'parent_mail' => 'required|email',
            'parent_job' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|max:13',
            'class' => 'required|string|max:50',
            'is_active' => 'boolean',
        ], [
            'student_id_number.required' => 'NIS wajib diisi',
            'student_id_number.max' => 'NIS maksimal 20 digit',
            'student_id_number.unique' => 'NIS sudah digunakan',
            'national_student_number.required' => 'NISN wajib diisi',
            'national_student_number.max' => 'NISN maksimal 20 digit',
            'national_student_number.unique' => 'NISN sudah digunakan',
            'year.required' => 'Tahun Masuk wajib diisi',
            'parent_name.required' => 'Nama Orang Tua wajib diisi',
            'parent_phone.required' => 'Telepon Orang Tua wajib diisi',
            'parent_phone.max' => 'Telepon Orang Tua maksimal 13 angka',
            'parent_mail.required' => 'Email Orang Tua wajib diisi',
            'parent_mail.email' => 'Format email tidak valid',
            'parent_job.required' => 'Pekerjaan Orang Tua wajib diisi',
            'phone.required' => 'Telepon siswa wajib diisi',
            'phone.max' => 'Telepon siswa maksimal 13 angka',
            'name.required' => 'Nama siswa wajib diisi',
            'class.required' => 'Kelas siswa wajib diisi'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $student->update([
            'student_id_number' => $request->student_id_number,
            'national_student_number' => $request->national_student_number,
            'year' => $request->year,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'parent_mail' => $request->parent_mail,
            'parent_job' => $request->parent_job,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'class' => $request->class,
            'is_active' => $request->is_active ?? true,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('students.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-students.index', $school);
        }

        return $route->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(School $school, Student $student)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $student->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or student.');
        }

        Transaction::WhereIn('reference_id', StudentReceivables::where('student_id', $student->id)->pluck('id'))
            ->where('reference_type', 'App\Models\StudentReceivables')
            ->update(['deleted_at' => now()]);
        StudentReceivables::where('student_id', $student->id)->update(['deleted_at' => now()]);
        $student->update(['deleted_at' => now()]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('students.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-students.index', $school);
        }
        return $route->with('success', 'Siswa berhasil dihapus.');
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

        return view('students.import', compact('school'));
    }

    /**
     * Handle the import of students from Excel.
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
            Excel::import(new StudentsImport($schoolId), $request->file('file'));

            $route = back();
            if (auth()->user()->role == 'SuperAdmin') {
                $route = redirect()->route('students.index');
            } else if (auth()->user()->role == 'SchoolAdmin') {
                $route = redirect()->route('school-students.index', $school);
            }
            return $route->with('success', 'Data siswa berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template for students.
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/students_template.xlsx'));
    }

    protected function getClass()
    {
        $nums = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'];
        // $otherNums = ['X', 'XI', 'XII'];
        $majors = SchoolMajor::pluck('name');
        $newNums = [];
        // foreach ($otherNums as $value) {
        //     foreach ($majors as $major) {
        //         $newNums[] = $value . ' ' . $major;
        //     }
        // }
        foreach ($majors as $major) {
                $newNums[] = $major;
            }
        return array_merge($nums, $newNums);
    }
}