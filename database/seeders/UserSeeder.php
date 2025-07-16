<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Karyawan Contoh',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan', // <-- Tambahkan ini
            'branch_id' => 1, // Pastikan nilai branch_id ini valid atau null jika belum diisi
        ]);
        // ... tambahkan user lain jika ada
    }
}