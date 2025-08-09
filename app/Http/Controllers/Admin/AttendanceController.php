<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Branch; // Jika menggunakan branch_id
use App\Models\WorkSchedule;
use App\Models\Leave;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AttendanceController extends Controller
{
    /**
     * Helper function untuk format tanggal dalam bahasa Indonesia
     */
    private function formatIndonesianDate($date)
    {
        $dayNames = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        
        $carbon = Carbon::parse($date)->setTimezone('Asia/Jakarta');
        $dayName = $dayNames[$carbon->format('l')] ?? $carbon->format('l');
        
        return $carbon->format('d/m/Y') . ' (' . $dayName . ')';
    }

    /**
     * Helper function untuk format waktu dalam timezone Indonesia
     */
    private function formatIndonesianTime($time)
    {
        if (!$time) return '-';
        return Carbon::parse($time)->setTimezone('Asia/Jakarta')->format('H:i:s');
    }

    /**
     * Helper function untuk format tanggal tanpa hari dalam bahasa Indonesia
     */
    private function formatIndonesianDateOnly($date)
    {
        return Carbon::parse($date)->setTimezone('Asia/Jakarta')->format('d/m/Y');
    }

    /**
     * Menampilkan daftar absensi yang dikelompokkan per hari per karyawan.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Attendance::with('user', 'branch')->select('attendances.*');

            // Filter berdasarkan tanggal
            if ($request->has('start_date') && $request->start_date != '') {
                $start_date = Carbon::parse($request->start_date)->startOfDay();
                $query->where('date', '>=', $start_date->format('Y-m-d'));
            }
            if ($request->has('end_date') && $request->end_date != '') {
                $end_date = Carbon::parse($request->end_date)->endOfDay();
                $query->where('date', '<=', $end_date->format('Y-m-d'));
            }

            // Filter berdasarkan karyawan
            if ($request->has('user_id') && $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }

            // Filter berdasarkan cabang
            if ($request->has('branch_id') && $request->branch_id != '') {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            // Filter berdasarkan status keterlambatan
            if ($request->has('late_status') && $request->late_status != '') {
                if ($request->late_status === 'late') {
                    $query->where('late_minutes', '>', 0);
                } elseif ($request->late_status === 'on_time') {
                    $query->where(function($q) {
                        $q->where('late_minutes', 0)->orWhereNull('late_minutes');
                    });
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_name', function(Attendance $attendance) {
                    return $attendance->user->name ?? 'N/A';
                })
                ->addColumn('employee_email', function(Attendance $attendance) {
                    return $attendance->user->email ?? 'N/A';
                })
                ->addColumn('branch_name', function(Attendance $attendance) {
                    return $attendance->user->branch->name ?? 'N/A';
                })
                ->addColumn('date', function(Attendance $attendance) {
                    return Carbon::parse($attendance->date)->format('d/m/Y'); // Ubah format ke dd/mm/yyyy
                })
                ->addColumn('check_out_time', function(Attendance $attendance) {
                    return $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i:s') : '-';
                })
                ->addColumn('break_start_time', function(Attendance $attendance) {
                    return $attendance->break_start ? Carbon::parse($attendance->break_start)->format('H:i:s') : '-';
                })
                ->addColumn('break_end_time', function(Attendance $attendance) {
                    return $attendance->break_end ? Carbon::parse($attendance->break_end)->format('H:i:s') : '-';
                })
                ->addColumn('time', function(Attendance $attendance) {
                    if ($attendance->check_in) {
                        return Carbon::parse($attendance->check_in)->format('H:i:s');
                    } elseif ($attendance->check_out) {
                        return Carbon::parse($attendance->check_out)->format('H:i:s');
                    }
                    return '-';
                })
                ->addColumn('type_badge', function(Attendance $attendance) {
                    if ($attendance->check_in && !$attendance->check_out) {
                        return '<span class="badge badge-primary">Check-in</span>';
                    } elseif ($attendance->check_out) {
                        return '<span class="badge badge-secondary">Check-out</span>';
                    }
                    return '<span class="badge badge-info">Both</span>';
                })
                 ->addColumn('status_badge', function(Attendance $attendance) {
                    $status = $attendance->status_in ?? $attendance->status_out ?? 'on_time';
                    $badgeClass = '';
                    switch ($status) {
                        case 'on_time': $badgeClass = 'badge-success'; break;
                        case 'late': $badgeClass = 'badge-warning'; break;
                        case 'early_out': $badgeClass = 'badge-info'; break;
                        case 'no_check_out': $badgeClass = 'badge-danger'; break;
                        default: $badgeClass = 'badge-secondary'; break;
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
                })
                ->addColumn('late_badge', function(Attendance $attendance) {
                    $lateMinutes = $attendance->late_minutes;
                    if ($lateMinutes && $lateMinutes > 0) {
                        $hours = floor($lateMinutes / 60);
                        $minutes = $lateMinutes % 60;
                        $timeString = '';
                        if ($hours > 0) {
                            $timeString .= $hours . 'j ';
                        }
                        $timeString .= $minutes . 'm';
                        return '<span class="badge badge-warning">' . $timeString . '</span>';
                    }
                    return '<span class="badge badge-success">Tepat Waktu</span>';
                })
                ->addColumn('location', function(Attendance $attendance) {
                    $lat = $attendance->latitude_in ?? $attendance->latitude_out;
                    $lng = $attendance->longitude_in ?? $attendance->longitude_out;
                    if ($lat && $lng) {
                        return $lat . ', ' . $lng;
                    }
                    return '-';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="btn btn-info btn-sm editAttendance"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteAttendance"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['type_badge', 'status_badge', 'late_badge', 'action'])
                ->make(true);
        }

        $employees = User::where('role', 'karyawan')->get(['id', 'name']);
        $branches = Branch::all(['id', 'name']);

        return view('admin.attendances.index', compact('employees', 'branches'));
    }

    /**
     * Menampilkan laporan absensi
     */
    public function report(Request $request)
    {
        // Handle date filtering
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now()->endOfMonth();
        
        // Validate dates
        if ($startDate->gt($endDate)) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        
        // Build query for attendance data
        $attendanceQuery = Attendance::whereBetween('date', [
            $startDate->format('Y-m-d'), 
            $endDate->format('Y-m-d')
        ]);
        
        // Apply user filter
        if ($request->filled('user_id')) {
            $attendanceQuery->where('user_id', $request->user_id);
        }
        
        // Apply branch filter
        if ($request->filled('branch_id')) {
            $attendanceQuery->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
        
        // Statistik umum
        $totalEmployees = User::where('role', 'karyawan')->count();
        
        // Dapatkan semua user yang sudah absen dalam periode tersebut
        $attendedUsers = (clone $attendanceQuery)->pluck('user_id')->unique();
        
        // Hitung user yang tidak hadir (tidak absen sama sekali dalam periode)
        $absentUsers = User::where('role', 'karyawan')
                          ->whereNotIn('id', $attendedUsers)
                          ->get();
        
        $totalAttendance = $attendedUsers->count();
        $absentCount = $absentUsers->count();
        
        $lateCount = (clone $attendanceQuery)
            ->where('late_minutes', '>', 0)->count();
        
        // Data untuk grafik - daily stats dengan perhitungan yang benar
        $dailyStats = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = strtolower($currentDate->format('l')); // monday, tuesday, etc.
            
            // Hitung absensi untuk tanggal ini
            $dayAttendance = (clone $attendanceQuery)
                ->whereDate('date', $dateStr)
                ->get();
            
            $dayAttendedUsers = $dayAttendance->pluck('user_id')->unique();
            $dayLateCount = $dayAttendance->where('late_minutes', '>', 0)->count();
            
            // Cek apakah hari ini adalah hari kerja (bukan Minggu dan bukan hari libur)
            $isWorkingDay = true;
            
            // Hari Minggu selalu libur
            if ($dayOfWeek === 'sunday') {
                $isWorkingDay = false;
            } else {
                // Cek jadwal kerja untuk memastikan hari ini bukan hari libur
                $workSchedules = WorkSchedule::where('is_holiday', true)
                    ->where('day', $dayOfWeek)
                    ->get();
                
                // Jika ada jadwal yang menandakan hari ini libur, maka bukan hari kerja
                if ($workSchedules->count() > 0) {
                    $isWorkingDay = false;
                }
            }
            
            // Hitung user yang tidak hadir untuk tanggal ini (hanya jika hari kerja)
            $dayAbsentUsers = 0;
            if ($isWorkingDay) {
                // Dapatkan user yang cuti pada tanggal ini
                $dayLeaveUsers = Leave::where('status', 'approved')
                                    ->where(function($q) use ($dateStr) {
                                        $q->where('start_date', '<=', $dateStr)
                                          ->where('end_date', '>=', $dateStr);
                                    })
                                    ->pluck('user_id')
                                    ->unique();

                // Dapatkan user yang izin pada tanggal ini
                $dayPermissionUsers = Permission::where('status', 'approved')
                                             ->whereDate('permission_date', $dateStr)
                                             ->pluck('user_id')
                                             ->unique();

                // Gabungkan semua user yang tidak seharusnya dihitung sebagai tidak hadir
                $dayExcludedUsers = $dayAttendedUsers->merge($dayLeaveUsers)->merge($dayPermissionUsers)->unique();

                $dayAbsentUsers = User::where('role', 'karyawan')
                                     ->whereNotIn('id', $dayExcludedUsers)
                                     ->count();
            }
            
            $dailyStats[] = (object) [
                'date' => $dateStr,
                'total' => $dayAttendedUsers->count(),
                'late_count' => $dayLateCount,
                'absent_count' => $dayAbsentUsers,
                'is_working_day' => $isWorkingDay,
                'day_name' => $currentDate->format('l') // Nama hari dalam bahasa Inggris
            ];
            
            $currentDate->addDay();
        }
        
        // Recalculate total absent count excluding non-working days
        $totalAbsentCount = 0;
        foreach ($dailyStats as $stat) {
            if ($stat->is_working_day) {
                $totalAbsentCount += $stat->absent_count;
            }
        }
        
        $employees = User::where('role', 'karyawan')->get(['id', 'name']);
        $branches = Branch::all(['id', 'name']);
        
        return view('admin.attendances.report', compact(
            'totalEmployees', 'totalAttendance', 'lateCount', 'totalAbsentCount',
            'dailyStats', 'employees', 'branches', 'startDate', 'endDate'
        ));
    }

    /**
     * Mendapatkan daftar user yang tidak hadir pada tanggal tertentu
     */
    public function getAbsentUsers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $date = $request->date;
        $dateObj = Carbon::parse($date);
        $dayOfWeek = strtolower($dateObj->format('l')); // monday, tuesday, etc.
        
        // Cek apakah hari ini adalah hari kerja (bukan Minggu dan bukan hari libur)
        $isWorkingDay = true;
        
        // Hari Minggu selalu libur
        if ($dayOfWeek === 'sunday') {
            $isWorkingDay = false;
        } else {
            // Cek jadwal kerja untuk memastikan hari ini bukan hari libur
            $workSchedules = WorkSchedule::where('is_holiday', true)
                ->where('day', $dayOfWeek)
                ->get();
            
            // Jika ada jadwal yang menandakan hari ini libur, maka bukan hari kerja
            if ($workSchedules->count() > 0) {
                $isWorkingDay = false;
            }
        }
        
        // Jika bukan hari kerja, kembalikan response kosong
        if (!$isWorkingDay) {
            return response()->json([
                'success' => true,
                'data' => [],
                'date' => $dateObj->format('d/m/Y'),
                'total' => 0,
                'message' => 'Hari ini adalah hari libur'
            ]);
        }
        
        // Dapatkan user yang sudah absen pada tanggal tersebut
        $attendedUsers = Attendance::whereDate('date', $date)
                                  ->pluck('user_id')
                                  ->unique();

        // Dapatkan user yang cuti pada tanggal tersebut
        $leaveUsers = Leave::where('status', 'approved')
                          ->where(function($q) use ($date) {
                              $q->where('start_date', '<=', $date)
                                ->where('end_date', '>=', $date);
                          })
                          ->pluck('user_id')
                          ->unique();

        // Dapatkan user yang izin pada tanggal tersebut
        $permissionUsers = Permission::where('status', 'approved')
                                   ->whereDate('permission_date', $date)
                                   ->pluck('user_id')
                                   ->unique();

        // Gabungkan semua user yang tidak seharusnya dihitung sebagai tidak hadir
        $excludedUsers = $attendedUsers->merge($leaveUsers)->merge($permissionUsers)->unique();

        // Query untuk user yang tidak hadir
        $absentUsersQuery = User::where('role', 'karyawan')
                               ->whereNotIn('id', $excludedUsers);

        // Apply filters
        if ($request->filled('user_id')) {
            $absentUsersQuery->where('id', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $absentUsersQuery->where('branch_id', $request->branch_id);
        }

        $absentUsers = $absentUsersQuery->with('branch')->get();

        return response()->json([
            'success' => true,
            'data' => $absentUsers,
            'date' => $dateObj->format('d/m/Y'),
            'total' => $absentUsers->count()
        ]);
    }

    /**
     * Mendapatkan data user yang terlambat per tanggal
     */
    public function getLateUsers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $date = $request->date;
        $dateObj = Carbon::parse($date);
        
        // Query untuk attendance yang terlambat pada tanggal tersebut
        $lateUsersQuery = Attendance::with(['user', 'user.branch'])
                                   ->whereDate('date', $date)
                                   ->where('late_minutes', '>', 0);

        // Apply filters
        if ($request->filled('user_id')) {
            $lateUsersQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $lateUsersQuery->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $lateUsers = $lateUsersQuery->get();

        return response()->json([
            'success' => true,
            'data' => $lateUsers,
            'date' => $dateObj->format('d/m/Y'),
            'total' => $lateUsers->count()
        ]);
    }

    /**
     * Laporan keterlambatan bulanan per karyawan
     */
    public function monthlyLateReport(Request $request)
    {
        // Default ke bulan dan tahun saat ini jika tidak ada filter
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Parse bulan dan tahun
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Ambil semua karyawan
        $employees = User::where('role', 'karyawan')
                        ->with('branch')
                        ->orderBy('name')
                        ->get();
        
        // Ambil data keterlambatan untuk periode tersebut
        $lateData = Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                             ->where('late_minutes', '>', 0)
                             ->with(['user', 'user.branch'])
                             ->get();
        
        // Hitung total keterlambatan per karyawan
        $employeeLateStats = [];
        
        foreach ($employees as $employee) {
            $employeeLates = $lateData->where('user_id', $employee->id);
            
            $totalLateMinutes = $employeeLates->sum('late_minutes');
            $totalLateHours = round($totalLateMinutes / 60, 2);
            $lateDays = $employeeLates->count();
            
            // Ambil data tidak hadir untuk karyawan ini
            $absentDetails = $this->getAbsentDaysForEmployee($employee->id, $startDate, $endDate);
            
            $employeeLateStats[] = [
                'employee' => $employee,
                'total_late_minutes' => $totalLateMinutes,
                'total_late_hours' => $totalLateHours,
                'late_days' => $lateDays,
                'average_late_minutes' => $lateDays > 0 ? round($totalLateMinutes / $lateDays, 1) : 0,
                'late_details' => $employeeLates->map(function($late) {
                    return [
                        'date' => Carbon::parse($late->date)->format('d/m/Y'),
                        'late_minutes' => $late->late_minutes,
                        'check_in_time' => Carbon::parse($late->check_in)->format('H:i:s')
                    ];
                }),
                'absent_details' => $absentDetails
            ];
        }
        
        // Urutkan berdasarkan total menit terlambat (descending)
        usort($employeeLateStats, function($a, $b) {
            return $b['total_late_minutes'] <=> $a['total_late_minutes'];
        });
        
        // Hitung statistik keseluruhan
        $totalLateMinutes = collect($employeeLateStats)->sum('total_late_minutes');
        $totalLateHours = round($totalLateMinutes / 60, 2);
        $totalLateDays = collect($employeeLateStats)->sum('late_days');
        $totalAbsentDays = collect($employeeLateStats)->sum(function($stat) {
            return isset($stat['absent_details']) ? count($stat['absent_details']) : 0;
        });
        
        // Data untuk dropdown bulan dan tahun
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate($year, $i, 1)->format('F');
        }
        
        $years = [];
        $currentYear = now()->year;
        for ($i = $currentYear - 2; $i <= $currentYear; $i++) {
            $years[$i] = $i;
        }
        
        return view('admin.attendances.monthly_late_report', compact(
            'employeeLateStats',
            'totalLateMinutes',
            'totalLateHours',
            'totalLateDays',
            'totalAbsentDays',
            'months',
            'years',
            'month',
            'year',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Helper method untuk mendapatkan hari tidak hadir karyawan
     */
    private function getAbsentDaysForEmployee($userId, $startDate, $endDate)
    {
        $absentDays = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = strtolower($currentDate->format('l')); // monday, tuesday, etc.
            
            // Cek apakah hari ini adalah hari kerja (bukan Minggu dan bukan hari libur)
            $isWorkingDay = true;
            
            // Cek apakah hari Minggu
            if ($dayOfWeek === 'sunday') {
                $isWorkingDay = false;
            }
            
            // Cek apakah hari libur berdasarkan work schedule
            $workSchedule = WorkSchedule::where('user_id', $userId)
                                      ->where('day', $dayOfWeek)
                                      ->first();
            if ($workSchedule && $workSchedule->is_holiday) {
                $isWorkingDay = false;
            }
            
            if ($isWorkingDay) {
                // Cek apakah ada attendance record
                $attendance = Attendance::where('user_id', $userId)
                                      ->where('date', $dateStr)
                                      ->first();
                
                // Cek apakah ada izin yang disetujui
                $approvedLeave = Leave::where('user_id', $userId)
                                    ->where('status', 'approved')
                                    ->where(function($query) use ($dateStr) {
                                        $query->where('start_date', '<=', $dateStr)
                                              ->where('end_date', '>=', $dateStr);
                                    })
                                    ->first();
                
                // Cek apakah ada permission yang disetujui
                $approvedPermission = Permission::where('user_id', $userId)
                                              ->where('status', 'approved')
                                              ->where('permission_date', $dateStr)
                                              ->first();
                
                // Jika tidak ada attendance dan tidak ada izin/permission yang disetujui, maka tidak hadir
                if (!$attendance && !$approvedLeave && !$approvedPermission) {
                    $absentDays[] = [
                        'date' => $currentDate->format('d/m/Y'),
                        'reason' => 'Tidak ada data kehadiran'
                    ];
                }
            }
            
            $currentDate->addDay();
        }
        
        return $absentDays;
    }

    /**
     * Mendapatkan data user yang tidak hadir untuk export
     */
    private function getAbsentUsersForExport(Request $request)
    {
        $absentData = collect();
        $currentDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = strtolower($currentDate->format('l')); // monday, tuesday, etc.
            
            // Cek apakah hari ini adalah hari kerja (bukan Minggu dan bukan hari libur)
            $isWorkingDay = true;
            
            // Hari Minggu selalu libur
            if ($dayOfWeek === 'sunday') {
                $isWorkingDay = false;
            } else {
                // Cek jadwal kerja untuk memastikan hari ini bukan hari libur
                $workSchedules = WorkSchedule::where('is_holiday', true)
                    ->where('day', $dayOfWeek)
                    ->get();
                
                // Jika ada jadwal yang menandakan hari ini libur, maka bukan hari kerja
                if ($workSchedules->count() > 0) {
                    $isWorkingDay = false;
                }
            }
            
            // Hanya proses jika hari kerja
            if ($isWorkingDay) {
                // Dapatkan user yang sudah absen pada tanggal ini
                $attendedUsers = Attendance::whereDate('date', $dateStr)
                                          ->pluck('user_id')
                                          ->unique();

                // Dapatkan user yang cuti pada tanggal ini
                $leaveUsers = Leave::where('status', 'approved')
                                  ->where(function($q) use ($dateStr) {
                                      $q->where('start_date', '<=', $dateStr)
                                        ->where('end_date', '>=', $dateStr);
                                  })
                                  ->pluck('user_id')
                                  ->unique();

                // Dapatkan user yang izin pada tanggal ini
                $permissionUsers = Permission::where('status', 'approved')
                                           ->whereDate('permission_date', $dateStr)
                                           ->pluck('user_id')
                                           ->unique();

                // Gabungkan semua user yang tidak seharusnya dihitung sebagai tidak hadir
                $excludedUsers = $attendedUsers->merge($leaveUsers)->merge($permissionUsers)->unique();

                // Query untuk user yang tidak hadir
                $absentUsersQuery = User::where('role', 'karyawan')
                                       ->whereNotIn('id', $excludedUsers);

                // Apply filters
                if ($request->filled('user_id')) {
                    $absentUsersQuery->where('id', $request->user_id);
                }

                if ($request->filled('branch_id')) {
                    $absentUsersQuery->where('branch_id', $request->branch_id);
                }

                $absentUsers = $absentUsersQuery->with('branch')->get();

                // Buat data untuk export dengan struktur yang sesuai
                foreach ($absentUsers as $user) {
                    // Safe access to user data
                    $userName = $user->name ?? '-';
                    $userEmail = $user->email ?? '-';
                    $branchName = $user->branch ? $user->branch->name : '-';
                    
                    $absentData->push([
                        'user' => [
                            'name' => $userName,
                            'email' => $userEmail,
                            'branch' => [
                                'name' => $branchName
                            ]
                        ],
                        'date' => $dateStr,
                        'check_in' => null,
                        'check_out' => null,
                        'status_in' => 'Tidak Hadir',
                        'status_out' => 'Tidak Hadir',
                        'late_minutes' => 0,
                        'latitude_in' => null,
                        'longitude_in' => null,
                        'latitude_out' => null,
                        'longitude_out' => null,
                        'notes' => 'Tidak ada data kehadiran',
                        'is_absent' => true
                    ]);
                }
            }
            
            $currentDate->addDay();
        }

        return $absentData;
    }

    /**
     * Mendapatkan data cuti untuk export
     */
    private function getLeaveDataForExport(Request $request)
    {
        $leaveData = collect();
        
        // Query untuk data cuti
        $leaveQuery = Leave::with(['user', 'user.branch'])
                          ->where(function($q) use ($request) {
                              // Cari cuti yang overlap dengan periode yang dipilih
                              $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                                ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                                ->orWhere(function($subQ) use ($request) {
                                    $subQ->where('start_date', '<=', $request->start_date)
                                         ->where('end_date', '>=', $request->end_date);
                                });
                          });

        // Apply filters
        if ($request->filled('user_id')) {
            $leaveQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $leaveQuery->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $leaves = $leaveQuery->get();

        foreach ($leaves as $leave) {
            // Generate data untuk setiap hari dalam periode cuti
            $currentDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Hanya tambahkan jika tanggal dalam range yang diminta
                if ($dateStr >= $request->start_date && $dateStr <= $request->end_date) {
                    // Safe access to user data
                    $userName = $leave->user ? $leave->user->name : '-';
                    $userEmail = $leave->user ? $leave->user->email : '-';
                    $branchName = ($leave->user && $leave->user->branch) ? $leave->user->branch->name : '-';
                    
                    $leaveData->push([
                        'user' => [
                            'name' => $userName,
                            'email' => $userEmail,
                            'branch' => [
                                'name' => $branchName
                            ]
                        ],
                        'date' => $dateStr,
                        'check_in' => null,
                        'check_out' => null,
                        'status_in' => 'Cuti',
                        'status_out' => 'Cuti',
                        'late_minutes' => 0,
                        'latitude_in' => null,
                        'longitude_in' => null,
                        'latitude_out' => null,
                        'longitude_out' => null,
                        'notes' => 'Cuti: ' . $leave->reason . ' (Status: ' . ucfirst($leave->status) . ')',
                        'is_leave' => true,
                        'leave_id' => $leave->id,
                        'leave_status' => $leave->status
                    ]);
                }
                
                $currentDate->addDay();
            }
        }

        return $leaveData;
    }

    /**
     * Mendapatkan data izin untuk export
     */
    private function getPermissionDataForExport(Request $request)
    {
        $permissionData = collect();
        
        // Query untuk data izin
        $permissionQuery = Permission::with(['user', 'user.branch'])
                                   ->whereBetween('permission_date', [$request->start_date, $request->end_date]);

        // Apply filters
        if ($request->filled('user_id')) {
            $permissionQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $permissionQuery->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $permissions = $permissionQuery->get();

        foreach ($permissions as $permission) {
            // Safe access to user data
            $userName = $permission->user ? $permission->user->name : '-';
            $userEmail = $permission->user ? $permission->user->email : '-';
            $branchName = ($permission->user && $permission->user->branch) ? $permission->user->branch->name : '-';
            
            $permissionData->push([
                'user' => [
                    'name' => $userName,
                    'email' => $userEmail,
                    'branch' => [
                        'name' => $branchName
                    ]
                ],
                'date' => $permission->permission_date->format('Y-m-d'),
                'check_in' => null,
                'check_out' => null,
                'status_in' => 'Izin',
                'status_out' => 'Izin',
                'late_minutes' => 0,
                'latitude_in' => null,
                'longitude_in' => null,
                'latitude_out' => null,
                'longitude_out' => null,
                'notes' => 'Izin: ' . $permission->reason . ' (Status: ' . ucfirst($permission->status) . ')',
                'is_permission' => true,
                'permission_id' => $permission->id,
                'permission_status' => $permission->status
            ]);
        }

        return $permissionData;
    }

    /**
     * Menampilkan pengaturan absensi
     */
    public function settings()
    {
        // Ambil pengaturan dari database atau config
        $settings = [
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'late_threshold' => '15', // menit
            'early_leave_threshold' => '30', // menit
            'attendance_radius' => '100', // meter
        ];
        
        return view('admin.attendances.settings', compact('settings'));
    }

    /**
     * Menampilkan halaman export data
     */
    public function export()
    {
        $employees = User::where('role', 'karyawan')->get(['id', 'name']);
        $branches = Branch::all(['id', 'name']);
        
        return view('admin.attendances.export', compact('employees', 'branches'));
    }

    /**
     * Preview data absensi
     */
    public function preview(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'nullable|in:all,check_in,check_out,late,absent',
            'include_absent' => 'nullable|boolean',
            'include_leave' => 'nullable|boolean',
            'include_permission' => 'nullable|boolean',
        ]);

        $query = Attendance::with(['user', 'user.branch'])
                          ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->branch_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->type && $request->type !== 'all') {
            if ($request->type === 'late') {
                $query->where('late_minutes', '>', 0);
            } elseif ($request->type === 'absent') {
                // Untuk absent, kita akan handle secara terpisah
                $data = collect();
            } else {
                if ($request->type === 'check_in') {
                    $query->whereNotNull('check_in');
                } else {
                    $query->whereNotNull('check_out');
                }
            }
        }

        $data = $query->orderBy('date', 'desc')->limit(50)->get();

        // Jika type adalah absent atau include_absent diaktifkan
        if ($request->type === 'absent' || $request->boolean('include_absent')) {
            $absentData = $this->getAbsentUsersForExport($request);
            if ($request->type === 'absent') {
                // Jika hanya ingin data absent, gunakan hanya absent data
                $data = $absentData->take(50);
            } else {
                // Jika include_absent, merge dengan data existing
                $data = collect($data->toArray())->merge($absentData->take(50)->toArray());
            }
        }

        // Tambahkan data cuti jika diminta
        if ($request->boolean('include_leave')) {
            $leaveData = $this->getLeaveDataForExport($request);
            $data = collect($data->toArray())->merge($leaveData->take(50)->toArray());
        }

        // Tambahkan data izin jika diminta
        if ($request->boolean('include_permission')) {
            $permissionData = $this->getPermissionDataForExport($request);
            $data = collect($data->toArray())->merge($permissionData->take(50)->toArray());
        }

        return view('admin.attendances.preview', compact('data'));
    }

    /**
     * Menyimpan pengaturan absensi
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'type' => 'required|in:work_time,tolerance,location,general,work_days',
        ]);

        $type = $request->type;
        $settings = [];

        switch ($type) {
            case 'work_time':
                $request->validate([
                    'work_start_time' => 'required|date_format:H:i:s',
                    'work_end_time' => 'required|date_format:H:i:s|after:work_start_time',
                ]);
                $settings = [
                    'work_start_time' => $request->work_start_time,
                    'work_end_time' => $request->work_end_time,
                ];
                break;

            case 'tolerance':
                $request->validate([
                    'late_threshold' => 'required|integer|min:0|max:60',
                    'early_leave_threshold' => 'required|integer|min:0|max:120',
                ]);
                $settings = [
                    'late_threshold' => $request->late_threshold,
                    'early_leave_threshold' => $request->early_leave_threshold,
                ];
                break;

            case 'location':
                $request->validate([
                    'attendance_radius' => 'required|integer|min:10|max:1000',
                    'office_latitude' => 'nullable|numeric|between:-90,90',
                    'office_longitude' => 'nullable|numeric|between:-180,180',
                ]);
                $settings = [
                    'attendance_radius' => $request->attendance_radius,
                    'office_latitude' => $request->office_latitude,
                    'office_longitude' => $request->office_longitude,
                ];
                break;

            case 'general':
                $settings = [
                    'enable_location' => $request->has('enable_location'),
                    'enable_photo' => $request->has('enable_photo'),
                    'enable_notification' => $request->has('enable_notification'),
                ];
                break;

            case 'work_days':
                $request->validate([
                    'work_days' => 'required|array|min:1',
                    'work_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                ]);
                $settings = [
                    'work_days' => $request->work_days,
                ];
                break;
        }

        // Simpan ke database atau config
        // Untuk demo, kita akan menggunakan session
        session(['attendance_settings_' . $type => $settings]);

        return response()->json(['success' => 'Pengaturan berhasil disimpan.']);
    }

    /**
     * Export data absensi
     */
    public function exportData(Request $request)
    {
        // Set unlimited time limit for export
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,csv,pdf',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'include_absent' => 'nullable|boolean',
            'include_leave' => 'nullable|boolean',
            'include_permission' => 'nullable|boolean',
            'type' => 'nullable|in:all,check_in,check_out,late,absent',
        ]);

        $query = Attendance::with(['user', 'user.branch'])
                          ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->branch_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Filter berdasarkan type
        if ($request->filled('type') && $request->type !== 'all') {
            switch ($request->type) {
                case 'check_in':
                    $query->whereNotNull('check_in');
                    break;
                case 'check_out':
                    $query->whereNotNull('check_out');
                    break;
                case 'late':
                    $query->where('late_minutes', '>', 0);
                    break;
                case 'absent':
                    // Untuk absent, kita akan handle secara terpisah
                    $data = collect();
                    break;
            }
        }

        $data = $query->orderBy('date', 'desc')->get();

        // Jika type adalah absent atau include_absent diaktifkan
        if ($request->type === 'absent' || $request->boolean('include_absent')) {
            $absentData = $this->getAbsentUsersForExport($request);
            if ($request->type === 'absent') {
                // Jika hanya ingin data absent, gunakan hanya absent data
                $data = $absentData;
            } else {
                // Jika include_absent, merge dengan data existing
                $data = collect($data->toArray())->merge($absentData->toArray());
            }
        }

        // Tambahkan data cuti jika diminta
        if ($request->boolean('include_leave')) {
            $leaveData = $this->getLeaveDataForExport($request);
            $data = collect($data->toArray())->merge($leaveData->toArray());
        }

        // Tambahkan data izin jika diminta
        if ($request->boolean('include_permission')) {
            $permissionData = $this->getPermissionDataForExport($request);
            $data = collect($data->toArray())->merge($permissionData->toArray());
        }

        // Gabungkan data per tanggal dan urutkan
        $data = $this->mergeDataByDate($data);

        // Periksa apakah data kosong
        if ($data->isEmpty()) {
            return response()->json([
                'error' => 'Tidak ada data absensi untuk periode yang dipilih.'
            ], 404);
        }

        $filename = 'attendance_report_' . date('Y-m-d_H-i-s');

        try {
            switch ($request->format) {
                case 'excel':
                    return $this->exportToExcel($data, $filename);
                case 'csv':
                    return $this->exportToCsv($data, $filename);
                case 'pdf':
                    return $this->exportToPdf($data, $filename);
                default:
                    return response()->json(['error' => 'Format tidak valid'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk menggabungkan data per tanggal
     */
    private function mergeDataByDate($data)
    {
        $mergedData = collect();
        $groupedByDate = $data->groupBy('date');

        foreach ($groupedByDate as $date => $records) {
            // Urutkan records berdasarkan nama karyawan untuk konsistensi
            $sortedRecords = $records->sortBy('user.name');
            
            foreach ($sortedRecords as $record) {
                // Ganti N/A dengan -
                $record = $this->replaceNAWithDash($record);
                $mergedData->push($record);
            }
        }

        // Urutkan berdasarkan tanggal (descending)
        return $mergedData->sortByDesc('date')->values();
    }

    /**
     * Helper method untuk mengganti N/A dengan -
     */
    private function replaceNAWithDash($record)
    {
        if (is_array($record)) {
            foreach ($record as $key => $value) {
                if (is_array($value)) {
                    $record[$key] = $this->replaceNAWithDash($value);
                } else {
                    // Jangan ubah nilai null untuk field waktu yang akan diproses oleh formatIndonesianTime
                    if ($value === 'N/A') {
                        $record[$key] = '-';
                    }
                    // Biarkan null tetap null untuk field waktu
                }
            }
        }
        return $record;
    }

    /**
     * Export ke Excel
     */
    private function exportToExcel($data, $filename)
    {
        // Set unlimited time limit for Excel export
        set_time_limit(300);
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi Optik Melati')
            ->setLastModifiedBy('Sistem Absensi')
            ->setTitle('Laporan Absensi')
            ->setSubject('Laporan Absensi Karyawan')
            ->setDescription('Laporan absensi karyawan periode tertentu');
        
        // Add logo as background (only if file exists and not too many records)
        $logoPath = public_path('image/optik-melati.png');
        if (file_exists($logoPath) && $data->count() < 1000) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Optik Melati');
            $drawing->setPath($logoPath);
            $drawing->setHeight(100);
            $drawing->setWidth(100);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            $drawing->setResizeProportional(true);
            $drawing->setRotation(0);
            $drawing->getShadow()->setVisible(true);
            $drawing->getShadow()->setDirection(45);
            $drawing->getShadow()->setDistance(10);
            $drawing->setWorksheet($sheet);
            
            // Set logo opacity by adjusting the worksheet background
            $sheet->getStyle('A1:Z1000')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFFFF');
        }
        
        // Define headers
        $headers = [
            'No',
            'Tanggal',
            'Nama Karyawan',
            'Email',
            'Cabang',
            'Status Masuk',
            'Waktu Masuk',
            'Status Keluar',
            'Waktu Keluar',
            'Keterlambatan (Menit)',
            'Keterangan'
        ];
        
        // Set headers in first row
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Add data rows
        $rowNumber = 2;
        $currentDate = null;
        $batchSize = 100; // Process in batches
        $batchCount = 0;
        
        foreach ($data as $attendance) {
            $date = $this->formatIndonesianDateOnly($attendance['date']);
            
            // Add date separator if date changes
            if ($currentDate !== $attendance['date']) {
                $currentDate = $attendance['date'];
                $dayName = Carbon::parse($attendance['date'])->locale('id')->isoFormat('dddd');
                
                // Add empty row for visual separation
                $sheet->insertNewRowBefore($rowNumber, 1);
                $rowNumber++;
                
                // Add date header with new icon and black text
                $sheet->setCellValue("A{$rowNumber}", "📊 {$date} ({$dayName})");
                $sheet->mergeCells("A{$rowNumber}:K{$rowNumber}");
                
                // Style date header with black text and light background
                $dateHeaderStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '000000'], // Black text
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'], // Light gray background
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ];
                $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->applyFromArray($dateHeaderStyle);
                $rowNumber++;
            }
            
            // Add data row
            $row = [
                $rowNumber - 2, // No
                $date, // Use the same date from header
                isset($attendance['user']['name']) ? $attendance['user']['name'] : '-',
                isset($attendance['user']['email']) ? $attendance['user']['email'] : '-',
                isset($attendance['user']['branch']['name']) ? $attendance['user']['branch']['name'] : '-',
                $attendance['status_in'] ?? '-',
                $attendance['check_in'] ? $this->formatIndonesianTime($attendance['check_in']) : '-',
                $attendance['status_out'] ?? '-',
                $attendance['check_out'] ? $this->formatIndonesianTime($attendance['check_out']) : '-',
                $attendance['late_minutes'] ?? '0',
                $attendance['notes'] ?? '-'
            ];
            
            $sheet->fromArray($row, NULL, "A{$rowNumber}");
            
            // Style data row based on status (only for smaller datasets)
            if ($data->count() < 1000) {
                $rowStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ];
                
                // Add background color based on status
                $statusIn = strtolower($attendance['status_in'] ?? '');
                if ($statusIn === 'absent' || $statusIn === 'tidak hadir') {
                    $rowStyle['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6'], // Light red
                    ];
                } elseif ($statusIn === 'izin' || $statusIn === 'cuti') {
                    $rowStyle['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6F3FF'], // Light blue
                    ];
                } elseif ($attendance['late_minutes'] > 0) {
                    $rowStyle['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2E6'], // Light yellow
                    ];
                }
                
                $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")->applyFromArray($rowStyle);
            }
            
            $rowNumber++;
            $batchCount++;
            
            // Process in batches to avoid memory issues
            if ($batchCount % $batchSize === 0) {
                // Force garbage collection
                gc_collect_cycles();
            }
        }
        
        // Add legend at the end
        $legendRow = $rowNumber + 1;
        $sheet->setCellValue("A{$legendRow}", "KETERANGAN:");
        $sheet->mergeCells("A{$legendRow}:K{$legendRow}");
        $sheet->getStyle("A{$legendRow}:K{$legendRow}")->getFont()->setBold(true);
        
        $legendRow++;
        $sheet->setCellValue("A{$legendRow}", "• Status Masuk: Hadir, Terlambat, Izin, Cuti, Tidak Hadir");
        $sheet->mergeCells("A{$legendRow}:K{$legendRow}");
        
        $legendRow++;
        $sheet->setCellValue("A{$legendRow}", "• Status Keluar: Hadir, Izin, Cuti, Tidak Hadir");
        $sheet->mergeCells("A{$legendRow}:K{$legendRow}");
        
        $legendRow++;
        $sheet->setCellValue("A{$legendRow}", "• Keterlambatan: Jumlah menit terlambat");
        $sheet->mergeCells("A{$legendRow}:K{$legendRow}");
        
        $legendRow++;
        $sheet->setCellValue("A{$legendRow}", "• Keterangan: Alasan izin/cuti atau catatan lainnya");
        $sheet->mergeCells("A{$legendRow}:K{$legendRow}");
        
        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);
        
        // Return file response
        return response()->download($tempFile, $filename . '.xlsx', $headers)->deleteFileAfterSend();
    }

    /**
     * Export ke CSV
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Email', 'Cabang', 'Tanggal', 'Waktu Check-in', 'Waktu Check-out', 'Status', 'Keterlambatan', 'Lokasi', 'Catatan']);

            foreach ($data as $row) {
                // Handle array data (absent users)
                if (is_array($row)) {
                    $checkIn = $this->formatIndonesianTime($row['check_in']);
                    $checkOut = $this->formatIndonesianTime($row['check_out']);
                    $status = isset($row['is_absent']) && $row['is_absent'] ? 'Tidak Hadir' : 
                             (isset($row['is_leave']) && $row['is_leave'] ? 'Cuti' :
                             (isset($row['is_permission']) && $row['is_permission'] ? 'Izin' : 
                             ($row['status_in'] ?? $row['status_out'] ?? 'on_time')));
                    $location = ($row['latitude_in'] || $row['longitude_in']) ? ($row['latitude_in'] . ', ' . $row['longitude_in']) : '-';
                    
                    // Safe access to user data
                    $userName = isset($row['user']['name']) ? $row['user']['name'] : 'N/A';
                    $userEmail = isset($row['user']['email']) ? $row['user']['email'] : 'N/A';
                    $branchName = isset($row['user']['branch']['name']) ? $row['user']['branch']['name'] : 'N/A';
                    
                    fputcsv($file, [
                        $userName,
                        $userEmail,
                        $branchName,
                        $this->formatIndonesianDateOnly($row['date']),
                        $checkIn,
                        $checkOut,
                        $status,
                        $row['late_minutes'] ?? 0,
                        $location,
                        isset($row['is_absent']) && $row['is_absent'] ? 'Tidak Hadir' : 
                        (isset($row['is_leave']) && $row['is_leave'] ? 'Cuti' :
                        (isset($row['is_permission']) && $row['is_permission'] ? 'Izin' : 
                        ($row['notes'] ?? '-')))
                    ]);
                } else {
                    // Handle object data (regular attendance)
                    $checkIn = $this->formatIndonesianTime($row->check_in);
                    $checkOut = $this->formatIndonesianTime($row->check_out);
                    $status = $row->status_in ?? $row->status_out ?? 'on_time';
                    $location = ($row->latitude_in && $row->longitude_in) ? ($row->latitude_in . ', ' . $row->longitude_in) : '-';
                    
                    // Safe access to user data
                    $userName = $row->user ? $row->user->name : 'N/A';
                    $userEmail = $row->user ? $row->user->email : 'N/A';
                    $branchName = ($row->user && $row->user->branch) ? $row->user->branch->name : 'N/A';
                    
                    fputcsv($file, [
                        $userName,
                        $userEmail,
                        $branchName,
                        $this->formatIndonesianDateOnly($row->date),
                        $checkIn,
                        $checkOut,
                        $status,
                        $row->late_minutes ?? 0,
                        $location,
                        $row->notes ?? '-'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export ke PDF
     */
    private function exportToPdf($data, $filename)
    {
        // Simple HTML to PDF conversion
        $html = '<html><head><title>Laporan Absensi</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            h1 { color: #333; text-align: center; }
        </style></head><body>';
        
        $html .= '<h1>Laporan Absensi Karyawan</h1>';
        $html .= '<table>';
        $html .= '<tr>
            <th>No</th>
            <th>Nama Karyawan</th>
            <th>Email</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>Waktu Check-in</th>
            <th>Waktu Check-out</th>
            <th>Status</th>
        </tr>';
        
        $no = 1;
        foreach ($data as $attendance) {
            $html .= '<tr>';
            $html .= '<td>' . $no . '</td>';
            
            // Handle array data (absent users)
            if (is_array($attendance)) {
                $checkIn = $this->formatIndonesianTime($attendance['check_in']);
                $checkOut = $this->formatIndonesianTime($attendance['check_out']);
                $status = isset($attendance['is_absent']) && $attendance['is_absent'] ? 'Tidak Hadir' : 
                         (isset($attendance['is_leave']) && $attendance['is_leave'] ? 'Cuti' :
                         (isset($attendance['is_permission']) && $attendance['is_permission'] ? 'Izin' : 
                         ($attendance['status_in'] ?? $attendance['status_out'] ?? 'on_time')));
                
                // Safe access to user data
                $userName = isset($attendance['user']['name']) ? $attendance['user']['name'] : 'N/A';
                $userEmail = isset($attendance['user']['email']) ? $attendance['user']['email'] : 'N/A';
                $branchName = isset($attendance['user']['branch']['name']) ? $attendance['user']['branch']['name'] : 'N/A';
                
                $html .= '<td>' . $userName . '</td>';
                $html .= '<td>' . $userEmail . '</td>';
                $html .= '<td>' . $branchName . '</td>';
                $html .= '<td>' . $this->formatIndonesianDateOnly($attendance['date']) . '</td>';
                $html .= '<td>' . $checkIn . '</td>';
                $html .= '<td>' . $checkOut . '</td>';
                $html .= '<td>' . $status . '</td>';
            } else {
                // Handle object data (regular attendance)
                $checkIn = $this->formatIndonesianTime($attendance->check_in);
                $checkOut = $this->formatIndonesianTime($attendance->check_out);
                $status = $attendance->status_in ?? $attendance->status_out ?? 'on_time';
                
                // Safe access to user data
                $userName = $attendance->user ? $attendance->user->name : 'N/A';
                $userEmail = $attendance->user ? $attendance->user->email : 'N/A';
                $branchName = ($attendance->user && $attendance->user->branch) ? $attendance->user->branch->name : 'N/A';
                
                $html .= '<td>' . $userName . '</td>';
                $html .= '<td>' . $userEmail . '</td>';
                $html .= '<td>' . $branchName . '</td>';
                $html .= '<td>' . $this->formatIndonesianDateOnly($attendance->date) . '</td>';
                $html .= '<td>' . $checkIn . '</td>';
                $html .= '<td>' . $checkOut . '</td>';
                $html .= '<td>' . $status . '</td>';
            }
            
            $html .= '</tr>';
            $no++;
        }
        
        $html .= '</table></body></html>';
        
        // For now, return HTML content that can be saved as PDF
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.html"',
        ];
        
        return response($html, 200, $headers);
    }

    /**
     * Menyimpan catatan absensi baru (biasanya manual oleh admin atau dari device).
     * Logika di sini akan lebih sederhana karena satu record = satu event (check_in/check_out).
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:check_in,check_out',
            'timestamp' => 'required|date_format:Y-m-d H:i:s', // Format datetime
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:on_time,late,early_out,no_check_out,absent', // Tambahkan absent untuk kasus tertentu
            'late_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = Carbon::parse($request->timestamp)->format('Y-m-d');
        $time = Carbon::parse($request->timestamp)->format('H:i:s');

        $attendanceData = [
            'user_id' => $request->user_id,
            'date' => $date,
        ];

        if ($request->type === 'check_in') {
            $attendanceData['check_in'] = $time;
            $attendanceData['status_in'] = $request->status;
            $attendanceData['latitude_in'] = $request->latitude;
            $attendanceData['longitude_in'] = $request->longitude;
            $attendanceData['late_minutes'] = $request->late_minutes ?? 0;
        } else {
            $attendanceData['check_out'] = $time;
            $attendanceData['status_out'] = $request->status;
            $attendanceData['latitude_out'] = $request->latitude;
            $attendanceData['longitude_out'] = $request->longitude;
        }

        Attendance::create($attendanceData);

        return response()->json(['success' => 'Catatan absensi berhasil ditambahkan.']);
    }

    /**
     * Menampilkan detail catatan absensi untuk diedit.
     */
    public function show($id)
    {
        $attendance = Attendance::with('user')->find($id);
        if (!$attendance) {
            return response()->json(['error' => 'Catatan absensi tidak ditemukan.'], 404);
        }
        
        // Convert to expected format for frontend
        // Since check_in and check_out are cast as datetime in the model,
        // they are already Carbon instances
        $timestamp = null;
        
        if ($attendance->check_in) {
            $timestamp = $attendance->check_in->format('d/m/Y H:i:s');
        } elseif ($attendance->check_out) {
            $timestamp = $attendance->check_out->format('d/m/Y H:i:s');
        } else {
            // No check_in or check_out, use date only
            $timestamp = $attendance->date->format('d/m/Y') . ' 00:00:00';
        }
        
        $data = [
            'id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'branch_id' => $attendance->user->branch_id ?? null,
            'timestamp' => $timestamp,
            'type' => $attendance->check_in ? 'check_in' : 'check_out',
            'status' => $attendance->status_in ?? $attendance->status_out ?? 'on_time',
            'latitude' => $attendance->latitude_in ?? $attendance->latitude_out,
            'longitude' => $attendance->longitude_in ?? $attendance->longitude_out,
            'late_minutes' => $attendance->late_minutes ?? 0,
            'notes' => null, // Not available in current structure
        ];
        
        return response()->json($data);
    }

    /**
     * Memperbarui catatan absensi.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'type' => 'required|in:check_in,check_out',
            'timestamp' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:on_time,late,early_out,no_check_out,absent',
            'late_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['error' => 'Catatan absensi tidak ditemukan.'], 404);
        }

        // Parse timestamp dari format d/m/Y H:i:s ke Y-m-d H:i:s
        try {
            $dt = \DateTime::createFromFormat('d/m/Y H:i:s', $request->timestamp);
            if (!$dt) {
                throw new \Exception('Format tanggal & waktu tidak valid. Gunakan format dd/mm/yyyy HH:mm:ss');
            }
            $date = $dt->format('Y-m-d');
            $time = $dt->format('H:i:s');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $updateData = [
            'user_id' => $request->user_id,
            'date' => $date,
        ];

        if ($request->type === 'check_in') {
            $updateData['check_in'] = $time;
            $updateData['status_in'] = $request->status;
            $updateData['latitude_in'] = $request->latitude;
            $updateData['longitude_in'] = $request->longitude;
            $updateData['late_minutes'] = $request->late_minutes ?? 0;
        } else {
            $updateData['check_out'] = $time;
            $updateData['status_out'] = $request->status;
            $updateData['latitude_out'] = $request->latitude;
            $updateData['longitude_out'] = $request->longitude;
        }

        $attendance->update($updateData);

        return response()->json(['success' => 'Catatan absensi berhasil diperbarui.']);
    }

    /**
     * Menghapus catatan absensi.
     */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['error' => 'Catatan absensi tidak ditemukan.'], 404);
        }
        $attendance->delete();

        return response()->json(['success' => 'Catatan absensi berhasil dihapus.']);
    }
}