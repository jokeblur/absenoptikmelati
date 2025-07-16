<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Kantor Pusat Jakarta',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'latitude' => -6.2087634,
            'longitude' => 106.845599,
        ]);

        Branch::create([
            'name' => 'Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 45, Bandung',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
        ]);
    }
}