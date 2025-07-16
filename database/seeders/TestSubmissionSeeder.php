<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\User;
use App\Models\Notification;

class TestSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get employee users
        $employees = User::where('role', 'karyawan')->take(2)->get();
        
        if ($employees->isEmpty()) {
            $this->command->info('No employee users found. Please create employee users first.');
            return;
        }
        
        foreach ($employees as $employee) {
            // Create a leave request
            $leave = Leave::create([
                'user_id' => $employee->id,
                'start_date' => now()->addDays(5)->format('Y-m-d'),
                'end_date' => now()->addDays(7)->format('Y-m-d'),
                'reason' => 'Liburan keluarga',
                'status' => 'pending',
                'duration' => 3,
            ]);
            
            // Create notification for this leave
            Notification::createLeaveNotification($leave);
            
            // Create a permission request
            $permission = Permission::create([
                'user_id' => $employee->id,
                'permission_date' => now()->addDays(3)->format('Y-m-d'),
                'reason' => 'Urusan keluarga',
                'status' => 'pending',
            ]);
            
            // Create notification for this permission
            Notification::createPermissionNotification($permission);
        }
        
        $this->command->info('Test submissions and notifications created successfully!');
    }
}
