<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin
     */
    public function index()
    {
        $totalEmployees = User::where('role', 'karyawan')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalAttendance = \App\Models\Attendance::whereDate('date', now())->count();
        $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count();
        
        return view('admin.dashboard', compact('totalEmployees', 'totalAdmins', 'totalAttendance', 'pendingLeaves'));
    }

    /**
     * Menampilkan daftar admin
     */
    public function indexAdmins()
    {
        $admins = User::where('role', 'admin')->with('branch')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Menampilkan form tambah admin
     */
    public function createAdmin()
    {
        $branches = Branch::all();
        return view('admin.admins.create', compact('branches'));
    }

    /**
     * Menyimpan admin baru
     */
    public function storeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'branch_id.exists' => 'Cabang tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'branch_id' => $request->branch_id,
            ]);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating admin: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan admin.')
                ->withInput();
        }
    }

    /**
     * Menampilkan form edit admin
     */
    public function editAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $branches = Branch::all();
        return view('admin.admins.edit', compact('admin', 'branches'));
    }

    /**
     * Update admin
     */
    public function updateAdmin(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'branch_id' => 'nullable|exists:branches,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'branch_id.exists' => 'Cabang tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'branch_id' => $request->branch_id,
            ]);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Data admin berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating admin: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui admin.')
                ->withInput();
        }
    }

    /**
     * Hapus admin
     */
    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        
        // Mencegah admin menghapus dirinya sendiri
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            $admin->delete();
            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting admin: ' . $e->getMessage());
            return redirect()->route('admin.admins.index')
                ->with('error', 'Terjadi kesalahan saat menghapus admin.');
        }
    }

    /**
     * Menampilkan halaman profil admin
     */
    public function profile()
    {
        $admin = Auth::user();
        $branches = Branch::all();
        
        return view('admin.profile', compact('admin', 'branches'));
    }

    /**
     * Update profil admin
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'branch_id' => 'nullable|exists:branches,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'branch_id.exists' => 'Cabang tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'branch_id' => $request->branch_id,
            ]);

            return redirect()->route('admin.profile')
                ->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating admin profile: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil.')
                ->withInput();
        }
    }

    /**
     * Update password admin
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek password saat ini
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak benar'])
                ->withInput();
        }

        try {
            $admin->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('admin.profile')
                ->with('success', 'Password berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating admin password: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui password.')
                ->withInput();
        }
    }
} 