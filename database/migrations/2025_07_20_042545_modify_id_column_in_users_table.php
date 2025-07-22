<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan kolom id adalah BIGINT, UNSIGNED, dan NOT NULL sebelum mengubahnya
        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk rollback, kita hanya akan mengubahnya kembali ke tipe data awal
            // Perhatian: Ini tidak akan menghapus properti auto-increment
            $table->bigInteger('id')->change();
        });
    }
};
