<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.work-schedules.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used as we are editing schedules per user
        return redirect()->route('admin.work-schedules.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not used
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $days = WorkSchedule::getWorkDays();
        $schedules = WorkSchedule::where('user_id', $user->id)->get()->keyBy('day');
        return view('admin.work-schedules.edit', compact('user', 'days', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $days = WorkSchedule::getWorkDays();
        $rules = [];
        $messages = [];

        foreach ($days as $day => $name) {
            $isHoliday = $request->has($day . '_is_holiday');

            $rules[$day . '_clock_in'] = $isHoliday ? 'nullable' : 'required|date_format:H:i';
            $rules[$day . '_clock_out'] = $isHoliday ? 'nullable' : 'required|date_format:H:i|after:' . $day . '_clock_in';
            $rules[$day . '_break_start_time'] = $isHoliday ? 'nullable' : 'required|date_format:H:i|after:' . $day . '_clock_in';
            $rules[$day . '_break_end_time'] = $isHoliday ? 'nullable' : 'required|date_format:H:i|after:' . $day . '_break_start_time';
            
            // Custom validation logic to ensure clock_out is after break_end_time
            if (!$isHoliday) {
                $rules[$day . '_clock_out'] .= '|after:' . $day . '_break_end_time';
            }


            $messages[$day . '_clock_out.after'] = "Jam pulang hari {$name} harus setelah jam masuk.";
            $messages[$day . '_break_start_time.after'] = "Jam mulai istirahat hari {$name} harus setelah jam masuk.";
            $messages[$day . '_break_end_time.after'] = "Jam selesai istirahat hari {$name} harus setelah jam mulai istirahat.";
        }

        $request->validate($rules, $messages);


        foreach ($days as $day => $name) {
            WorkSchedule::updateOrCreate(
                ['user_id' => $user->id, 'day' => $day],
                [
                    'clock_in' => $request->input($day . '_clock_in'),
                    'clock_out' => $request->input($day . '_clock_out'),
                    'break_start_time' => $request->input($day . '_break_start_time'),
                    'break_end_time' => $request->input($day . '_break_end_time'),
                    'is_holiday' => $request->has($day . '_is_holiday')
                ]
            );
        }

        return redirect()->route('admin.employees.index')->with('success', 'Jadwal kerja untuk ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
