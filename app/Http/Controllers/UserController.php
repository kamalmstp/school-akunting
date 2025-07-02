<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:SuperAdmin,AdminMonitor,SchoolAdmin')->only(['profile', 'resetPassword']);
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $school = $request->get('school');
        $role = $request->get('role');
        $users = User::with('school')
            ->when($school, function ($q) use ($school) {
                $q->where('school_id', $school);
            })
            ->when($role, function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->paginate(10)->withQueryString();
        return view('users.index', compact('users', 'role', 'school'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $schools = School::all();
        return view('users.create', compact('schools'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|max:13',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:SuperAdmin,AdminMonitor,SchoolAdmin',
            'school_id' => 'required_if:role,SchoolAdmin|exists:schools,id|nullable',
        ], [
            'name.required' => 'Nama pengguna wajib diisi',
            'email.required' => 'Email pengguna wajib diisi',
            'email.email' => 'Format email pengguna tidak valid',
            'email.unique' => 'Email pengguna sudah digunakan',
            'phone.required' => 'Telepon pengguna wajib diisi',
            'phone.max' => 'Telepon pengguna maksimal 13 angka',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'role.required' => 'Pilih salah satu role',
            'school_id.required_if' => 'Pilih salah satu sekolah',
            'school_id.exists' => 'Sekolah sudah memiliki admin'
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'school_id' => $request->role === 'SchoolAdmin' ? $request->school_id : null,
            'status' => $request->status
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $schools = School::all();
        return view('users.edit', compact('user', 'schools'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id . '|nullable',
            'phone' => 'required|max:13',
            'role' => 'required|in:SuperAdmin,AdminMonitor,SchoolAdmin',
            'school_id' => 'required_if:role,SchoolAdmin|exists:schools,id|nullable',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama pengguna wajib diisi',
            'email.required' => 'Email pengguna wajib diisi',
            'email.email' => 'Format email pengguna tidak valid',
            'email.unique' => 'Email pengguna sudah digunakan',
            'phone.required' => 'Telepon pengguna wajib diisi',
            'phone.max' => 'Telepon pengguna maksimal 13 angka',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'role.required' => 'Pilih salah satu role',
            'school_id.required_if' => 'Pilih salah satu sekolah',
            'school_id.exists' => 'Sekolah sudah memiliki admin'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'school_id' => $request->role === 'SchoolAdmin' ? $request->school_id : null,
            'status' => $request->status
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus pengguna yang sedang login.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Reset the specified user's password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'password.confirmed' => 'Kata sandi tidak sama'
        ]);

        $newPassword = $request->password;
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('users.index')->with('success', "Kata sandi {$user->name} telah direset ke: {$newPassword}");
    }

    public function profile()
    {
        return view('users.profile');
    }

    public function edit_profile(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id . '|nullable',
            'phone' => 'required|max:13',
        ], [
            'name.required' => 'Nama pengguna wajib diisi',
            'email.required' => 'Email pengguna wajib diisi',
            'email.email' => 'Format email pengguna tidak valid',
            'email.unique' => 'Email pengguna sudah digunakan',
            'phone.required' => 'Telepon wajib diisi',
            'phone.max' => 'Telepon pengguna maksimal 13 angka',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}