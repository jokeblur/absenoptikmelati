<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkScheduleController extends Controller
{
    /**
     * Display a listing of work schedules.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')
                        ->with('workSchedules')
                        ->get();
        
        return view('admin.work-schedules.index', compact('employees'));
    }

    /**
     * Show the form for creating a new work schedule.
     */
    public function create()
    {
        $employees = User::where('role', 'employee')->get();
        $workDays = WorkSchedule::getWorkDays();
        
        return view('admin.work-schedules.create', compact('employees', 'workDays'));
    }

    /**
     * Store a newly created work schedule in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|string',
            'schedules.*.clock_in' => 'required|date_format:H:i',
            'schedules.*.clock_out' => 'required|date_format:H:i|after:schedules.*.clock_in',
            'schedules.*.break_start_time' => 'nullable|date_format:H:i|after:schedules.*.clock_in',
            'schedules.*.break_end_time' => 'nullable|date_format:H:i|after:schedules.*.break_start_time|before:schedules.*.clock_out',
            'schedules.*.is_holiday' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Delete existing schedules for this user
            WorkSchedule::where('user_id', $request->user_id)->delete();

            // Create new schedules
            foreach ($request->schedules as $schedule) {
                WorkSchedule::create([
                    'user_id' => $request->user_id,
                    'day' => $schedule['day'],
                    'clock_in' => $schedule['clock_in'],
                    'clock_out' => $schedule['clock_out'],
                    'break_start_time' => $schedule['break_start_time'] ?? null,
                    'break_end_time' => $schedule['break_end_time'] ?? null,
                    'is_holiday' => $schedule['is_holiday'] ?? false
                ]);
            }

            DB::commit();

            return redirect()->route('admin.work-schedules.index')
                           ->with('success', 'Jadwal kerja berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Show the form for editing the specified work schedule.
     */
    public function edit($userId)
    {
        $employee = User::where('role', 'employee')
                       ->with('workSchedules')
                       ->findOrFail($userId);
        
        $workDays = WorkSchedule::getWorkDays();
        
        // Create a schedule array indexed by day
        $schedules = [];
        foreach ($employee->workSchedules as $schedule) {
            $schedules[$schedule->day] = $schedule;
        }
        
        return view('admin.work-schedules.edit', compact('employee', 'workDays', 'schedules'));
    }

    /**
     * Update the specified work schedule in storage.
     */
    public function update(Request $request, $userId)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|string',
            'schedules.*.clock_in' => 'required|date_format:H:i',
            'schedules.*.clock_out' => 'required|date_format:H:i|after:schedules.*.clock_in',
            'schedules.*.break_start_time' => 'nullable|date_format:H:i|after:schedules.*.clock_in',
            'schedules.*.break_end_time' => 'nullable|date_format:H:i|after:schedules.*.break_start_time|before:schedules.*.clock_out',
            'schedules.*.is_holiday' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Delete existing schedules for this user
            WorkSchedule::where('user_id', $userId)->delete();

            // Create new schedules
            foreach ($request->schedules as $schedule) {
                WorkSchedule::create([
                    'user_id' => $userId,
                    'day' => $schedule['day'],
                    'clock_in' => $schedule['clock_in'],
                    'clock_out' => $schedule['clock_out'],
                    'break_start_time' => $schedule['break_start_time'] ?? null,
                    'break_end_time' => $schedule['break_end_time'] ?? null,
                    'is_holiday' => $schedule['is_holiday'] ?? false
                ]);
            }

            DB::commit();

            return redirect()->route('admin.work-schedules.index')
                           ->with('success', 'Jadwal kerja berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified work schedule from storage.
     */
    public function destroy($userId)
    {
        try {
            WorkSchedule::where('user_id', $userId)->delete();
            
            return redirect()->route('admin.work-schedules.index')
                           ->with('success', 'Jadwal kerja berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show work schedule for a specific employee
     */
    public function show($userId)
    {
        $employee = User::where('role', 'employee')
                       ->with('workSchedules')
                       ->findOrFail($userId);
        
        $workDays = WorkSchedule::getWorkDays();
        
        // Create a schedule array indexed by day
        $schedules = [];
        foreach ($employee->workSchedules as $schedule) {
            $schedules[$schedule->day] = $schedule;
        }
        
        return view('admin.work-schedules.show', compact('employee', 'workDays', 'schedules'));
    }
}
