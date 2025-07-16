<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance; // Pastikan ini mengacu pada model Attendance Anda
use App\Models\User;      // Pastikan ini mengacu pada model User/Employee Anda
use App\Models\Branch;    // Pastikan ini mengacu pada model Branch Anda
use Faker\Factory as Faker;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID'); // Gunakan locale Indonesia jika diinginkan

        // Ambil semua user (karyawan) dan branches yang ada
        $users = User::all();
        $branches = Branch::all();

        // Pastikan ada user dan branches sebelum melanjutkan
        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }
        if ($branches->isEmpty()) {
            $this->command->info('Tidak ada branch ditemukan. Jalankan BranchSeeder terlebih dahulu.');
            return;
        }

        $numAttendances = 200; // Jumlah data absensi yang ingin Anda buat

        for ($i = 0; $i < $numAttendances; $i++) {
            $randomUser = $users->random();
            $randomBranch = $branches->random();

            // Tentukan tanggal absensi dalam rentang waktu tertentu (misal: 30 hari terakhir)
            $date = Carbon::now()->subDays(rand(0, 29));

            // Tentukan waktu check-in dan check-out
            $checkInTime = $date->copy()->setTime(rand(7, 9), rand(0, 59), rand(0, 59)); // Antara jam 07:00 - 09:59
            $checkOutTime = $date->copy()->setTime(rand(16, 18), rand(0, 59), rand(0, 59)); // Antara jam 16:00 - 18:59

            // Tentukan status dan tipe absensi (sederhana)
            $type = $faker->randomElement(['check_in', 'check_out']);
            $status = 'on_time';
            if ($type == 'check_in' && $checkInTime->hour >= 9) { // Jika check-in setelah jam 9
                $status = 'late';
            } elseif ($type == 'check_out' && $checkOutTime->hour < 16) { // Jika check-out sebelum jam 4 sore
                $status = 'early_out';
            }

            // Buat data check_in
            Attendance::create([
                'user_id'    => $randomUser->id,
                'branch_id'  => $randomBranch->id,
                'timestamp'  => $checkInTime,
                'type'       => 'check_in',
                'status'     => ($checkInTime->hour >= 9) ? 'late' : 'on_time', // Status lebih akurat
                'latitude'   => $faker->latitude,
                'longitude'  => $faker->longitude,
                'notes'      => $faker->sentence(3),
                'created_at' => $checkInTime, // Sesuaikan dengan nama kolom timestamp Anda
                'updated_at' => $checkInTime, // Sesuaikan dengan nama kolom timestamp Anda
            ]);

            // Jika tipe absensi adalah check_in, kita juga bisa tambahkan check_out untuk hari yang sama
            // Ini untuk mensimulasikan absensi lengkap (masuk dan pulang)
            if ($type == 'check_in' && $faker->boolean(80)) { // 80% kemungkinan ada check-out
                Attendance::create([
                    'user_id'    => $randomUser->id,
                    'branch_id'  => $randomBranch->id,
                    'timestamp'  => $checkOutTime,
                    'type'       => 'check_out',
                    'status'     => ($checkOutTime->hour < 16) ? 'early_out' : 'on_time', // Status lebih akurat
                    'latitude'   => $faker->latitude,
                    'longitude'  => $faker->longitude,
                    'notes'      => $faker->sentence(3),
                    'created_at' => $checkOutTime, // Sesuaikan dengan nama kolom timestamp Anda
                    'updated_at' => $checkOutTime, // Sesuaikan dengan nama kolom timestamp Anda
                ]);
            }
        }
        $this->command->info('Seeder Absensi berhasil dijalankan. Ditambahkan ' . $numAttendances . ' atau lebih record.');
    }
}