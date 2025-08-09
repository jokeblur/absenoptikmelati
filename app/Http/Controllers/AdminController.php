<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Branch;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Statistik untuk dashboard
        $totalEmployees = User::where('role', 'karyawan')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        // Absensi hari ini
        $today = Carbon::today();
        
        // Dapatkan semua karyawan yang sudah absen hari ini
        $attendedUsers = Attendance::whereDate('date', $today)
                                  ->pluck('user_id')
                                  ->unique();
        
        // Hitung karyawan yang tidak hadir (tidak absen sama sekali)
        $absentUsers = User::where('role', 'karyawan')
                          ->whereNotIn('id', $attendedUsers)
                          ->get();
        
        $absentCount = $absentUsers->count();
        $totalAttendance = $attendedUsers->count();
        
        // Hitung karyawan terlambat hari ini
        $todayLateCount = Attendance::whereDate('date', $today)
                                   ->where(function($q) {
                                       $q->where('status_in', 'late')->orWhere('status_out', 'late');
                                   })->count();
        
        // Persentase kehadiran hari ini (termasuk yang tidak hadir)
        $attendancePercentage = $totalEmployees > 0 ? 
            round(($totalAttendance / $totalEmployees) * 100, 1) : 0;
        
        // Karyawan terlambat hari ini
        $lateEmployees = Attendance::whereDate('date', $today)
                                  ->where(function($q) {
                                      $q->where('status_in', 'late')->orWhere('status_out', 'late');
                                  })
                                  ->with('user')
                                  ->get()
                                  ->unique('user_id')
                                  ->count();
        
        // Total keterlambatan hari ini (dalam menit)
        $totalLateMinutes = Attendance::whereDate('date', $today)
                                     ->where('late_minutes', '>', 0)
                                     ->sum('late_minutes');
        
        // Rata-rata keterlambatan hari ini
        $avgLateMinutes = $lateEmployees > 0 ? round($totalLateMinutes / $lateEmployees, 1) : 0;
        
        // Pengajuan cuti pending
        $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count();

        // Pengajuan izin pending
        $pendingPermissions = \App\Models\Permission::where('status', 'pending')->count();
        
        // Data untuk grafik bulanan
        $monthlyData = Attendance::whereMonth('date', Carbon::now()->month)
                                ->whereYear('date', Carbon::now()->year)
                                ->selectRaw('date, COUNT(*) as total')
                                ->groupBy('date')
                                ->orderBy('date')
                                ->get();
        
        $chartLabels = $monthlyData->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('d/m');
        });
        $chartData = $monthlyData->pluck('total');

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalAdmins',
            'totalAttendance',
            'absentCount',
            'absentUsers',
            'todayLateCount',
            'attendancePercentage',
            'lateEmployees',
            'totalLateMinutes',
            'avgLateMinutes',
            'pendingLeaves',
            'pendingPermissions',
            'chartLabels',
            'chartData'
        ));
    }
}