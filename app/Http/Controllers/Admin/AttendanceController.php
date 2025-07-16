<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Branch; // Jika menggunakan branch_id
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
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
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteAttendance"><i class="fas fa-trash"></i></a>';
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
        $totalAttendance = $attendanceQuery->count();
        $lateCount = (clone $attendanceQuery)
            ->where('late_minutes', '>', 0)->count();
        $absentCount = (clone $attendanceQuery)
            ->where(function($q) {
                $q->where('status_in', 'absent')->orWhere('status_out', 'absent');
            })->count();
        
        // Data untuk grafik - daily stats
        $dailyStats = (clone $attendanceQuery)
            ->selectRaw('date, COUNT(*) as total, 
                        SUM(CASE WHEN late_minutes > 0 THEN 1 ELSE 0 END) as late_count,
                        SUM(CASE WHEN status_in = "absent" OR status_out = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $employees = User::where('role', 'karyawan')->get(['id', 'name']);
        $branches = Branch::all(['id', 'name']);
        
        return view('admin.attendances.report', compact(
            'totalEmployees', 'totalAttendance', 'lateCount', 'absentCount',
            'dailyStats', 'employees', 'branches', 'startDate', 'endDate'
        ));
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
                $query->where(function($q) {
                    $q->where('status_in', 'late')->orWhere('status_out', 'late');
                });
            } elseif ($request->type === 'absent') {
                $query->where(function($q) {
                    $q->where('status_in', 'absent')->orWhere('status_out', 'absent');
                });
            } else {
                if ($request->type === 'check_in') {
                    $query->whereNotNull('check_in');
                } else {
                    $query->whereNotNull('check_out');
                }
            }
        }

        $data = $query->orderBy('date', 'desc')->limit(50)->get();

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
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,csv,pdf',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
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

        $data = $query->orderBy('date', 'desc')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'error' => 'Tidak ada data absensi untuk periode yang dipilih.'
            ], 404);
        }

        $filename = 'attendance_report_' . date('Y-m-d_H-i-s');

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
    }

    /**
     * Export ke Excel
     */
    private function exportToExcel($data, $filename)
    {
        // Create a proper Excel file using HTML table format
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Absensi</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #f2f2f2; font-weight: bold; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-right { text-align: right; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th style="text-align: center;">No</th>';
        $html .= '<th style="text-align: center;">Nama Karyawan</th>';
        $html .= '<th style="text-align: center;">Email</th>';
        $html .= '<th style="text-align: center;">Cabang</th>';
        $html .= '<th style="text-align: center;">Tanggal</th>';
        $html .= '<th style="text-align: center;">Waktu Check-in</th>';
        $html .= '<th style="text-align: center;">Waktu Check-out</th>';
        $html .= '<th style="text-align: center;">Status Check-in</th>';
        $html .= '<th style="text-align: center;">Status Check-out</th>';
        $html .= '<th style="text-align: center;">Keterlambatan (menit)</th>';
        $html .= '<th style="text-align: center;">Lokasi Check-in</th>';
        $html .= '<th style="text-align: center;">Lokasi Check-out</th>';
        $html .= '<th style="text-align: center;">Catatan</th>';
        $html .= '</tr>';
        
        $no = 1;
        foreach ($data as $attendance) {
            $html .= '<tr>';
            $html .= '<td style="text-align: center;">' . $no . '</td>';
            $html .= '<td>' . ($attendance->user->name ?? 'N/A') . '</td>';
            $html .= '<td>' . ($attendance->user->email ?? 'N/A') . '</td>';
            $html .= '<td>' . ($attendance->user->branch->name ?? 'N/A') . '</td>';
            $html .= '<td style="text-align: center;">' . Carbon::parse($attendance->date)->format('Y-m-d') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i:s') : '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i:s') : '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->status_in ?? '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->status_out ?? '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->late_minutes ?? 0) . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->latitude_in && $attendance->longitude_in ? 
                $attendance->latitude_in . ', ' . $attendance->longitude_in : '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($attendance->latitude_out && $attendance->longitude_out ? 
                $attendance->latitude_out . ', ' . $attendance->longitude_out : '-') . '</td>';
            $html .= '<td>' . ($attendance->notes ?? '-') . '</td>';
            $html .= '</tr>';
            $no++;
        }
        
        $html .= '</table>';
        $html .= '</body></html>';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
            'Cache-Control' => 'max-age=0',
        ];
        
        return response($html, 200, $headers);
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
            fputcsv($file, ['Nama', 'Email', 'Cabang', 'Tanggal', 'Waktu', 'Tipe', 'Status', 'Lokasi', 'Catatan']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->user->name ?? 'N/A',
                    $row->user->email ?? 'N/A',
                    $row->user->branch->name ?? 'N/A',
                    Carbon::parse($row->date)->format('Y-m-d'),
                    Carbon::parse($row->check_in)->format('H:i:s'),
                    $row->check_in ? 'Check-in' : 'Check-out',
                    $row->status_in ?? $row->status_out ?? 'on_time',
                    $row->latitude_in && $row->longitude_in ? $row->latitude_in . ', ' . $row->longitude_in : '-',
                    $row->notes ?? '-'
                ]);
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
            $html .= '<td>' . ($attendance->user->name ?? 'N/A') . '</td>';
            $html .= '<td>' . ($attendance->user->email ?? 'N/A') . '</td>';
            $html .= '<td>' . ($attendance->user->branch->name ?? 'N/A') . '</td>';
            $html .= '<td>' . Carbon::parse($attendance->date)->format('Y-m-d') . '</td>';
            $html .= '<td>' . ($attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i:s') : '-') . '</td>';
            $html .= '<td>' . ($attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i:s') : '-') . '</td>';
            $html .= '<td>' . ($attendance->status_in ?? $attendance->status_out ?? 'on_time') . '</td>';
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
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['error' => 'Catatan absensi tidak ditemukan.'], 404);
        }
        
        // Convert to expected format for frontend
        $data = [
            'id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'timestamp' => $attendance->check_in ? 
                Carbon::parse($attendance->date . ' ' . $attendance->check_in)->format('Y-m-d H:i:s') :
                Carbon::parse($attendance->date . ' ' . $attendance->check_out)->format('Y-m-d H:i:s'),
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
            'timestamp' => 'required|date_format:Y-m-d H:i:s',
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

        $date = Carbon::parse($request->timestamp)->format('Y-m-d');
        $time = Carbon::parse($request->timestamp)->format('H:i:s');

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