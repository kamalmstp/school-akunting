<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\FixAsset;
use App\Models\Student;
use App\Models\StudentReceivables;
use App\Services\AccountTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:SuperAdmin,AdminMonitor,Pengawas']);
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
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required' => 'Nama sekolah wajib diisi',
            'email.required' => 'Email sekolah wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sekolah sudah digunakan',
            'phone.required' => 'Telepon sekolah wajib diisi',
            'phone.max' => 'Telepon sekolah maksimal 13 angka',
            'logo.image' => 'Logo sekolah harus berupa gambar',
            'logo.max' => 'Logo sekolah maksimal 2 MB',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');

            // Generate random filename with original extension
            $filename = Str::random(40) . '.' . $logoFile->getClientOriginalExtension();

            // Move to public/images/schools/
            $logoFile->move(public_path('images/schools'), $filename);

            // Save path in DB
            $logoPath = 'images/schools/' . $filename;
        }

        $school = School::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        if (!empty($school->id))
            AccountTemplateService::createDefaultAccountsForSchool($school->id);

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
            'email' => 'required|email|unique:schools,email,'.$school->id,
            'phone' => 'required|max:13',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required' => 'Nama sekolah wajib diisi',
            'email.required' => 'Email sekolah wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sekolah sudah digunakan',
            'phone.required' => 'Telepon sekolah wajib diisi',
            'phone.max' => 'Telepon sekolah maksimal 13 angka',
            'logo.image' => 'Logo sekolah harus berupa gambar',
            'logo.max' => 'Logo sekolah maksimal 2 MB',
        ]);

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($school->logo && file_exists(public_path($school->logo))) {
                unlink(public_path($school->logo));
            }

            $logoFile = $request->file('logo');

            // Buat nama file random dengan ekstensi asli
            $filename = Str::random(40) . '.' . $logoFile->getClientOriginalExtension();

            // Simpan ke folder public/images/schools
            $logoFile->move(public_path('images/schools'), $filename);

            // Simpan path ke DB
            $logoPath = 'images/schools/' . $filename;
        } else {
            $logoPath = $school->logo;
        }

        $school->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school)
    {
        Account::where('school_id', $school->id)->update(['deleted_at' => now()]);
        Transaction::where('school_id', $school->id)->update(['deleted_at' => now()]);
        FixAsset::where('school_id', $school->id)->update(['deleted_at' => now()]);
        Student::where('school_id', $school->id)->update(['deleted_at' => now()]);
        StudentReceivables::where('school_id', $school->id)->update(['deleted_at' => now()]);
        $school->update(['deleted_at' => now()]);
        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil dihapus.');
    }
}