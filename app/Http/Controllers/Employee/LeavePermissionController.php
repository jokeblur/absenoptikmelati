<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\Notification;

class LeavePermissionController extends Controller
{
    public function index()
    {
        return view('employee.submission');
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ], [
            'start_date.required' => 'Tanggal mulai cuti wajib diisi',
            'start_date.after_or_equal' => 'Tanggal mulai cuti harus hari ini atau setelahnya',
            'end_date.required' => 'Tanggal selesai cuti wajib diisi',
            'end_date.after_or_equal' => 'Tanggal selesai cuti harus sama dengan atau setelah tanggal mulai',
            'reason.required' => 'Alasan cuti wajib diisi',
            'reason.max' => 'Alasan cuti maksimal 500 karakter',
        ]);

        // Hitung durasi cuti
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $duration = $startDate->diffInDays($endDate) + 1;

        $leave = Leave::create([
            'user_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending', // Default status
            'duration' => $duration,
        ]);

        // Buat notifikasi untuk admin
        Notification::createLeaveNotification($leave);

        return response()->json(['success' => true, 'message' => 'Pengajuan cuti berhasil dikirim!']);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'permission_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:500',
        ], [
            'permission_date.required' => 'Tanggal izin wajib diisi',
            'permission_date.after_or_equal' => 'Tanggal izin harus hari ini atau setelahnya',
            'reason.required' => 'Alasan izin wajib diisi',
            'reason.max' => 'Alasan izin maksimal 500 karakter',
        ]);

        // Cek apakah sudah ada pengajuan izin untuk tanggal yang sama
        $existingPermission = Permission::where('user_id', Auth::id())
            ->where('permission_date', $request->permission_date)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingPermission) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah mengajukan izin untuk tanggal tersebut.'
            ], 400);
        }

        $permission = Permission::create([
            'user_id' => Auth::id(),
            'permission_date' => $request->permission_date,
            'reason' => $request->reason,
            'status' => 'pending', // Default status
        ]);

        // Buat notifikasi untuk admin
        Notification::createPermissionNotification($permission);

        return response()->json(['success' => true, 'message' => 'Pengajuan izin berhasil dikirim!']);
    }
}