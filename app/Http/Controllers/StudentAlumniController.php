<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentReceivables;
use App\Models\StudentReceivableDetail;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class StudentAlumniController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the student receivables.
     */
    public function index(Request $request, School $school = null)
    {
        $user = auth()->user();
        $account = $request->get('account');
        $dueDate = $request->get('date');
        $status = $request->get('status');
        $year = $request->get('year');
        $studentId = $request->get('student_id');

        // Jika bukan SchoolAdmin (SuperAdmin)
        if ($user->role !== 'SchoolAdmin') {
            $schools = School::pluck('name', 'id');
            $school = $request->get('school');

            $students = Student::with(['receivables' => function ($q) use ($account, $dueDate, $status, $year) {
                    $q->when($account, fn($q) => $q->where('account_id', $account))
                      ->when($dueDate, fn($q) => $q->where('due_date', Carbon::parse($dueDate)->format('Y-m-d')))
                      ->when($status, fn($q) => $q->where('status', $status));
                }])
                ->when($studentId, fn($q) => $q->where('id', $studentId))
                ->when($year, fn($q) => $q->where('year', $year))
                ->when($school, fn($q) => $q->where('school_id', $school))
                ->where('is_alumni', 1)
                ->whereNotNull('year')
                ->withSum('receivables', 'total_payable')
                ->withSum('receivables', 'paid_amount')
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->withQueryString();

            return view('student-alumni.index', compact('students', 'schools', 'school', 'account', 'dueDate', 'status', 'year', 'studentId'));
        }

        // Jika SchoolAdmin
        $school = $school ?? $user->school;
        if (!$school || $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $students = Student::with(['receivables' => function ($q) use ($account, $dueDate, $status, $year) {
                $q->when($account, fn($q) => $q->where('account_id', $account))
                  ->when($dueDate, fn($q) => $q->where('due_date', Carbon::parse($dueDate)->format('Y-m-d')))
                  ->when($status, fn($q) => $q->where('status', $status));
            }])
            ->where('school_id', $school->id)
            ->when($studentId, fn($q) => $q->where('id', $studentId))
            ->when($year, fn($q) => $q->where('year', $year))
            ->where('is_alumni', 1)
            ->whereNotNull('year')
            ->withSum('receivables', 'total_payable')
            ->withSum('receivables', 'paid_amount')
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('student-alumni.index', compact('students', 'school', 'account', 'dueDate', 'status', 'year', 'studentId'));
    }

    public function getStudent(Request $request)
    {
        $students = Student::where('school_id', $request->school)->where('is_alumni', 1)->whereNotNull('year')->get();
        return response()->json($students, 200);
    }

    public function getYear(Request $request)
    {
        $students = Student::select('year')->where('school_id', $request->school)->where('is_alumni', 1)->whereNotNull('year')->groupBy('year')->orderBy('year', 'asc')->get();
        return response()->json($students, 200);
    }

    public function updateCertificateStatus(Request $request, School $school, Student $student)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (($user->role !== 'SuperAdmin' && $user->school_id !== $school->id) || $student->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school or student.');
        }

        // Validasi input
        $request->validate([
            'certificate_status' => 'required|in:taken,not_taken'
        ]);

        // Update status
        $student->update([
            'certificate_status' => $request->certificate_status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status ijazah berhasil diperbarui',
            'certificate_status' => $student->certificate_status
        ]);
    }
}