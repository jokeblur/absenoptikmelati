<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah kolom 'role' menjadi VARCHAR yang lebih panjang
            // Misalnya, jika sebelumnya VARCHAR(20) atau ENUM, ubah ke VARCHAR(50)
            $table->string('role', 50)->change(); // Mengubah panjang kolom

            // ATAU, jika Anda ingin menggunakan ENUM dan menambahkan 'karyawan'
            // Pastikan untuk mencantumkan semua nilai yang valid
            // $table->enum('role', ['admin', 'karyawan', 'manajer'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika ingin mengembalikan ke tipe data sebelumnya
            // Perlu diingat bahwa jika ada data yang lebih panjang, akan terpotong
            $table->string('role', 20)->change(); // Contoh mengembalikan ke 20
            // ATAU:
            // $table->enum('role', ['admin'])->change();
        });
    }
};