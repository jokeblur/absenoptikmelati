<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\Branch;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now();
        $todayString = $today->toDateString();

        $attendanceToday = Attendance::where('user_id', $user->id)
            ->whereDate('date', $todayString)
            ->first();

        $hasClockedIn = false;
        $hasClockedOut = false;

        if ($attendanceToday) {
            $hasClockedIn = !is_null($attendanceToday->check_in);
            $hasClockedOut = !is_null($attendanceToday->check_out);
        }

        $workSchedules = WorkSchedule::where('user_id', $user->id)
            ->get()
            ->keyBy('day');

        // Get current day's schedule and day name
        $dayOfWeek = strtolower($today->format('l'));
        $todaySchedule = $workSchedules->get($dayOfWeek);
        $dayName = WorkSchedule::getWorkDays()[$dayOfWeek] ?? ucfirst($dayOfWeek);
        if ($dayOfWeek === 'sunday') {
            $dayName = 'Minggu';
        }


        return view('employee.dashboard', compact(
            'hasClockedIn', 'hasClockedOut', 'attendanceToday', 'workSchedules',
            'dayName', 'todaySchedule'
        ));
    }

    /**
     * Menampilkan halaman profil karyawan
     */
    public function profile()
    {
        $user = Auth::user();
        $branches = Branch::all();
        
        return view('employee.profile', compact('user', 'branches'));
    }

    /**
     * Update profil karyawan
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'branch_id' => 'nullable|exists:branches,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'branch_id.exists' => 'Cabang tidak valid',
            'profile_photo.image' => 'File harus berupa gambar',
            'profile_photo.mimes' => 'Format foto harus jpg, jpeg, atau png',
            'profile_photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
        ]);

        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');
            // Hapus foto lama jika ada
            if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                \Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $path;
        }

        $user->save();

        return redirect()->route('employee.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password karyawan
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
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
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak benar'])
                ->withInput();
        }

        // Update password
        $user->fill([
            'password' => Hash::make($request->new_password)
        ]);
        $user->save();

        return redirect()->route('employee.profile')
            ->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Menampilkan riwayat absensi karyawan
     */
    public function history()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        // Ambil riwayat pengajuan cuti dan izin
        $leaves = Leave::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $permissions = Permission::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('employee.history', compact('attendances', 'leaves', 'permissions'));
    }
   
}