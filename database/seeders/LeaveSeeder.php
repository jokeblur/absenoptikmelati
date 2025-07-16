<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leave;
use App\Models\User;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();

        if ($employees->count() > 0) {
            $employee1 = $employees->first();
            
            // Pengajuan cuti yang sudah disetujui
            Leave::create([
                'user_id' => $employee1->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(7)->toDateString(),
                'reason' => 'Cuti tahunan untuk liburan keluarga.',
                'status' => 'approved',
                'admin_notes' => 'Disetujui, harap siapkan handover sebelum cuti.',
                'duration' => 3,
            ]);

            // Pengajuan cuti yang pending
            Leave::create([
                'user_id' => $employee1->id,
                'start_date' => now()->addDays(15)->toDateString(),
                'end_date' => now()->addDays(16)->toDateString(),
                'reason' => 'Ada acara keluarga yang penting.',
                'status' => 'pending',
                'duration' => 2,
            ]);

            // Pengajuan cuti yang ditolak
            Leave::create([
                'user_id' => $employee1->id,
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->subDays(8)->toDateString(),
                'reason' => 'Cuti mendadak karena sakit.',
                'status' => 'rejected',
                'admin_notes' => 'Ditolak karena tidak ada pemberitahuan sebelumnya.',
                'duration' => 3,
            ]);

            // Jika ada employee kedua, buat pengajuan untuk mereka
            if ($employees->count() > 1) {
                $employee2 = $employees->skip(1)->first();
                
                Leave::create([
                    'user_id' => $employee2->id,
                    'start_date' => now()->addDays(20)->toDateString(),
                    'end_date' => now()->addDays(22)->toDateString(),
                    'reason' => 'Mengikuti workshop pelatihan.',
                    'status' => 'pending',
                    'duration' => 3,
                ]);
            }
        }
    }
}
