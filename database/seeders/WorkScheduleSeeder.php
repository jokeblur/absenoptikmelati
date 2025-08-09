<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;
use App\Models\User;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees
        $employees = User::where('role', 'karyawan')->get();
        
        foreach ($employees as $employee) {
            // Set Sunday as holiday for all employees
            WorkSchedule::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'day' => 'sunday'
                ],
                [
                    'clock_in' => null,
                    'clock_out' => null,
                    'is_holiday' => true
                ]
            );
            
            // Set working days (Monday to Saturday) as non-holiday
            $workingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            
            foreach ($workingDays as $day) {
                WorkSchedule::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'day' => $day
                    ],
                    [
                        'clock_in' => '08:00:00',
                        'clock_out' => '17:00:00',
                        'is_holiday' => false
                    ]
                );
            }
        }
    }
} 