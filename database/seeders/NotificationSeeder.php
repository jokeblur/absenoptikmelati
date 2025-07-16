<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Leave;
use App\Models\Permission;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->command->info('No admin users found. Please create admin users first.');
            return;
        }
        
        // Get some employees for test data
        $employees = User::where('role', 'karyawan')->take(3)->get();
        
        if ($employees->isEmpty()) {
            $this->command->info('No employee users found. Please create employee users first.');
            return;
        }
        
        // Create test notifications for each admin
        foreach ($admins as $admin) {
            // Create some leave request notifications
            foreach ($employees as $employee) {
                Notification::create([
                    'type' => 'leave_request',
                    'title' => 'Pengajuan Cuti Baru',
                    'message' => "Karyawan {$employee->name} mengajukan cuti dari 2024-01-15 sampai 2024-01-17",
                    'user_id' => $admin->id,
                    'related_id' => 1, // Dummy ID
                    'related_type' => Leave::class,
                    'is_read' => false,
                    'created_at' => now()->subHours(rand(1, 24))
                ]);
                
                Notification::create([
                    'type' => 'permission_request',
                    'title' => 'Pengajuan Izin Baru',
                    'message' => "Karyawan {$employee->name} mengajukan izin pada 2024-01-20",
                    'user_id' => $admin->id,
                    'related_id' => 1, // Dummy ID
                    'related_type' => Permission::class,
                    'is_read' => false,
                    'created_at' => now()->subHours(rand(1, 48))
                ]);
            }
            
            // Create some read notifications
            Notification::create([
                'type' => 'leave_request',
                'title' => 'Pengajuan Cuti Baru',
                'message' => "Karyawan Test Employee mengajukan cuti dari 2024-01-10 sampai 2024-01-12",
                'user_id' => $admin->id,
                'related_id' => 1,
                'related_type' => Leave::class,
                'is_read' => true,
                'created_at' => now()->subDays(2)
            ]);
        }
        
        $this->command->info('Test notifications created successfully!');
    }
}
