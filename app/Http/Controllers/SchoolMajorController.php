<?php

namespace App\Http\Controllers;

use App\Models\SchoolMajor;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolMajorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.access');
    }

    /**
     * Display a listing of the school majors.
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
            $majors = SchoolMajor::when($name, function ($q) use ($name) {
                    $q->where('name', 'like', '%' . $name . '%');
                })
                ->when($schoolId, function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->paginate(10)->withQueryString();
            return view('school-majors.index', compact('majors', 'name', 'schoolId','school','schoolId'));
        }

        $schoolId = $school->id;
        $majors = SchoolMajor::where('school_id', $schoolId)
                ->when($name, function ($q) use ($name) {
                    $q->where('name', 'like', '%' . $name . '%');
                })
                ->paginate(10)->withQueryString();

        return view('school-majors.index', compact('majors', 'name', 'schoolId','school'));
    }

    /**
     * Show the form for creating a new school major.
     */
    public function create(School $school)
    {
        $user = auth()->user();
        if ($user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('school-majors.create', compact('school'));
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

        SchoolMajor::create([
            'name' => $request->name,
            'school_id' => auth()->user()->role == 'SuperAdmin' ? $request->school_id : $school->id,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('school-majors.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-school-majors.index', $school);
        }

        return $route->with('success', 'Jurusan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school, SchoolMajor $school_major)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $school_major->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return view('school-majors.edit', compact('school_major', 'school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school, SchoolMajor $school_major)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $school_major->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama jurusan wajib diisi',
        ]);

        $school_major->update([
            'name' => $request->name,
        ]);

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('school-majors.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-school-majors.index', $school);
        }

        return $route->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school, SchoolMajor $school_major)
    {
        $user = auth()->user();
        if (($user->role != 'SuperAdmin' && $user->school_id !== $school->id) || $school_major->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        $school_major->delete();

        $route = back();
        if (auth()->user()->role == 'SuperAdmin') {
            $route = redirect()->route('school-majors.index');
        } else if (auth()->user()->role == 'SchoolAdmin') {
            $route = redirect()->route('school-school-majors.index', $school);
        }
        return $route->with('success', 'Jurusan berhasil dihapus.');
    }
}