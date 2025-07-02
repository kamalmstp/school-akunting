<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Transaction;
use App\Models\FixAsset;
use App\Models\Student;
use App\Models\StudentReceivables;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:SuperAdmin,AdminMonitor']);
    }

    /**
     * Display a listing of the schools.
     */
    public function index(Request $request)
    {
        $schoolId = $request->get('school_id');
        $status = $request->get('status');
        $allSchools = School::pluck('name', 'id');
        $schools = School::when($schoolId, function ($q) use ($schoolId) {
                $q->where('id', $schoolId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status == "1" ? true : false);
            })
            ->paginate(10)->withQueryString();
        return view('schools.index', compact('schools', 'status', 'allSchools', 'schoolId'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools',
            'phone' => 'required|max:13',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Nama sekolah wajib diisi',
            'email.required' => 'Email sekolah wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sekolah sudah digunakan',
            'phone.required' => 'Telepon sekolah wajib diisi',
            'phone.max' => 'Telepon sekolah maksimal 13 angka'
        ]);

        School::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools',
            'phone' => 'required|max:13',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Nama sekolah wajib diisi',
            'email.required' => 'Email sekolah wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sekolah sudah digunakan',
            'phone.required' => 'Telepon sekolah wajib diisi',
        ]);

        $school->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school)
    {
        Transaction::where('school_id', $school->id)->update(['deleted_at', now()]);
        FixAsset::where('school_id', $school->id)->update(['deleted_at', now()]);
        Student::where('school_id', $school->id)->update(['deleted_at', now()]);
        StudentReceivables::where('school_id', $school->id)->update(['deleted_at', now()]);
        $school->update(['deleted_at', now()]);
        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil dihapus.');
    }
}