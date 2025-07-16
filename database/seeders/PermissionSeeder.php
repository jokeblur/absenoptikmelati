<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User; // Perlu untuk mendapatkan user_id

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawan1 = User::where('role', 'karyawan')->first();
        $karyawan2 = User::where('role', 'karyawan')->skip(1)->first(); // Ambil karyawan kedua jika ada

        if ($karyawan1) {
            Permission::create([
                'user_id' => $karyawan1->id,
                'permission_date' => now()->addDays(rand(1, 10))->toDateString(),
                'reason' => 'Ada keperluan keluarga mendadak.',
                'status' => 'pending',
            ]);
            Permission::create([
                'user_id' => $karyawan1->id,
                'permission_date' => now()->subDays(5)->toDateString(),
                'reason' => 'Kunjungan dokter gigi.',
                'status' => 'approved',
                'admin_notes' => 'Disetujui, harap membawa surat keterangan.',
            ]);
        }

        if ($karyawan2) {
            Permission::create([
                'user_id' => $karyawan2->id,
                'permission_date' => now()->addDays(rand(11, 20))->toDateString(),
                'reason' => 'Mengikuti workshop online.',
                'status' => 'pending',
            ]);
        }
    }
}